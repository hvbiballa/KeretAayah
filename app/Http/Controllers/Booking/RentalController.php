<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Mail\AdminBookingNotification;
use App\Mail\BookConfirmMail;
use App\Models\Car;
use App\Models\Rental;
use App\Models\User;
use App\Services\Booking\BookingScheduleService;
use App\Services\Booking\RentalLifecycleService;
use App\Services\Catalog\RentalAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Throwable;

class RentalController extends Controller
{
    public function __construct(
        private RentalAvailabilityService $availability,
        private BookingScheduleService $bookingSchedule,
        private RentalLifecycleService $rentalLifecycle,
    )
    {
    }

    function index()
    {
        abort_unless(auth()->user()?->isCustomer(), 403);

        $rentals = Rental::where('user_id', auth()->id())
            ->with(['car.images', 'payment'])
            ->latest('pickup_at')
            ->get();

        $this->rentalLifecycle->syncMany($rentals);

        return Inertia::render('Booking/MyBookingsPage', [
            'rentals' => $rentals,
        ]);
    }

    public function confirm(Request $request)
    {
        abort_unless($request->user()?->isCustomer(), 403);

        $validated = $this->bookingSchedule->validate($request->only([
            'rental_method',
            'pickup_date',
            'pickup_time',
            'return_date',
            'return_time',
        ]));

        $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
        ]);

        $car = Car::with('images')->findOrFail($request->integer('car_id'));
        $schedule = $this->bookingSchedule->resolve($validated);
        $quote = $this->bookingSchedule->quote($car, $schedule);
        $alreadyBooked = $this->availability->hasOverlap(
            $car,
            $schedule['reservation_start'],
            $schedule['reservation_end'],
        );
        $pendingPaymentLimitReached = $this->rentalLifecycle->hasReachedPendingPaymentLimit(
            $request->user(),
        );
        $bookingBlockReason = $pendingPaymentLimitReached
            ? __('booking.flash.pending_payment_limit', [
                'count' => Rental::MAX_PENDING_PAYMENT_BOOKINGS,
            ])
            : ($car->status !== Car::STATUS_AVAILABLE
                ? __('booking.flash.car_maintenance')
                : ($alreadyBooked ? __('booking.flash.already_booked') : null));

        return Inertia::render('Booking/BookingConfirmPage', [
            'car' => $car,
            'schedule' => [
                'rental_method' => $schedule['rental_method'],
                'pickup_date' => $validated['pickup_date'],
                'pickup_time' => $validated['pickup_time'],
                'return_date' => $validated['return_date'],
                'return_time' => $validated['return_time'],
                'duration_units' => number_format($schedule['duration_units'], 2, '.', ''),
            ],
            'quote' => [
                'applied_rate' => number_format($quote['applied_rate'], 2, '.', ''),
                'total_cost' => number_format($quote['total_cost'], 2, '.', ''),
            ],
            'bookingBlocked' => $pendingPaymentLimitReached
                || $car->status !== Car::STATUS_AVAILABLE
                || $alreadyBooked,
            'bookingBlockReason' => $bookingBlockReason,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->isCustomer(), 403);

        $validated = $request->validate([
            'car_id' => 'required',
            'rental_method' => 'required',
            'pickup_date' => 'required',
            'pickup_time' => 'required',
            'return_date' => 'required',
            'return_time' => 'required',
        ]);

        $car = Car::findOrFail($request->car_id);
        $schedule = $this->bookingSchedule->resolve(
            $this->bookingSchedule->validate($validated),
        );

        if ($this->rentalLifecycle->hasReachedPendingPaymentLimit($request->user())) {
            return back()->with([
                'error' => __('booking.flash.pending_payment_limit', [
                    'count' => Rental::MAX_PENDING_PAYMENT_BOOKINGS,
                ]),
            ]);
        }

        if ($car->status !== Car::STATUS_AVAILABLE) {
            return back()->with([
                'error' => __('booking.flash.car_maintenance')
            ]);
        }
        $quote = $this->bookingSchedule->quote($car, $schedule);

        $alreadyBooked = $this->availability->hasOverlap(
            $car,
            $schedule['reservation_start'],
            $schedule['reservation_end'],
        );

        if ($alreadyBooked) {
            return back()->with([
                'error' => __('booking.flash.already_booked')
            ]);
        }

        $booking = DB::transaction(function () use ($car, $schedule, $quote) {
            $booking = Rental::create([
                'user_id' => auth()->id(),
                'car_id' => $car->id,
                'rental_method' => $schedule['rental_method'],
                'pickup_at' => $schedule['pickup_at'],
                'return_at' => $schedule['return_at'],
                'applied_rate' => $quote['applied_rate'],
                'duration_units' => $schedule['duration_units'],
                'total_cost' => $quote['total_cost'],
                'status' => Rental::STATUS_CONFIRMED,
            ]);

            $booking->payment()->create([
                'amount_due' => $quote['total_cost'],
                'amount_paid' => 0,
                'status' => 'pending',
            ]);

            return $booking;
        });

        try {
            Mail::mailer('noreply_mailer')
                ->to(auth()->user()->email)
                ->send(new BookConfirmMail($booking));

            $admin = User::where('role', 'admin')->first();

            if ($admin) {
                Mail::mailer('noreply_mailer')
                    ->to($admin->email)
                    ->send(new AdminBookingNotification($booking));
            }
        } catch (Throwable) {
            // Email failures must not block a completed booking.
        }

        session()->flash('success', __('booking.flash.created'));
        return redirect()->route('bookings.payment', $booking);
    }

    function show(Request $request, $id)
    {
        abort_unless($request->user()?->isCustomer(), 403);

        $rental = Rental::where('user_id', auth()->id())
            ->with(['car.images', 'payment'])
            ->findOrFail($id);

        $this->rentalLifecycle->sync($rental);

        $displayIndex = Rental::where('user_id', auth()->id())
            ->orderBy('pickup_at')
            ->pluck('id')
            ->search($rental->id);

        return Inertia::render('Booking/BookingDetailPage', [
            'rental' => $rental,
            'displayReference' => $displayIndex === false ? null : $displayIndex + 1,
        ]);
    }

    public function payment(Request $request, Rental $rental)
    {
        abort_unless($request->user()?->isCustomer(), 403);
        abort_unless((int) $rental->user_id === (int) $request->user()?->id, 403);

        $rental->load(['car.images', 'payment']);
        $this->rentalLifecycle->sync($rental);

        $displayIndex = Rental::where('user_id', auth()->id())
            ->orderBy('pickup_at')
            ->pluck('id')
            ->search($rental->id);

        return Inertia::render('Booking/BookingPaymentPage', [
            'rental' => $rental,
            'displayReference' => $displayIndex === false ? null : $displayIndex + 1,
        ]);
    }

    function cancel(Request $request, Rental $rental)
    {
        $user = $request->user();

        abort_unless(
            $user?->isCustomer() && (int) $rental->user_id === (int) $user->id,
            403,
        );

        if (now()->gte($rental->pickup_at)) {
            return back()->with('error', __('booking.flash.already_started'));
        }

        $rental->update([
            'status' => Rental::STATUS_CANCELLED,
        ]);

        return back()->with(['success' => __('booking.flash.cancelled')]);
    }

}

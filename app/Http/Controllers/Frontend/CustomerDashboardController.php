<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Services\BookingScheduleService;
use App\Services\Booking\RentalLifecycleService;
use App\Services\Catalog\RentalAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CustomerDashboardController extends Controller
{
    public function __construct(
        private BookingScheduleService $bookingSchedule,
        private RentalAvailabilityService $availability,
        private RentalLifecycleService $rentalLifecycle,
    ) {
    }

    public function index(Request $request): Response
    {
        abort_unless($request->user()?->isCustomer(), 403);

        $scheduleInput = $request->only([
            'rental_method',
            'pickup_date',
            'pickup_time',
            'return_date',
            'return_time',
        ]);

        $hasSchedule = collect($scheduleInput)->contains(
            fn ($value) => $value !== null && $value !== '',
        );

        $resolvedSchedule = $hasSchedule
            ? $this->bookingSchedule->resolve($this->bookingSchedule->validate($scheduleInput))
            : null;

        $cars = Car::with('images')
            ->orderBy('name')
            ->get()
            ->map(function (Car $car) use ($resolvedSchedule): array {
                $quote = $resolvedSchedule
                    ? $this->bookingSchedule->quote($car, $resolvedSchedule)
                    : null;

                $matchesSchedule = ! $resolvedSchedule
                    ? $car->status === Car::STATUS_AVAILABLE
                    : $car->status === Car::STATUS_AVAILABLE
                        && ! $this->availability->hasOverlap(
                            $car,
                            $resolvedSchedule['reservation_start'],
                            $resolvedSchedule['reservation_end'],
                        );

                return [
                    ...$car->toArray(),
                    'matches_schedule' => $matchesSchedule,
                    'schedule_quote' => $quote ? [
                        'applied_rate' => number_format($quote['applied_rate'], 2, '.', ''),
                        'total_cost' => number_format($quote['total_cost'], 2, '.', ''),
                        'duration_units' => number_format($resolvedSchedule['duration_units'], 2, '.', ''),
                    ] : null,
                ];
            })
            ->sortByDesc(fn (array $car): int => $car['matches_schedule'] ? 1 : 0)
            ->values();

        $rentals = $request->user()
            ->rentals()
            ->with(['car.images', 'payment'])
            ->latest('pickup_at')
            ->get();

        $this->rentalLifecycle->syncMany($rentals);

        return Inertia::render('Frontend/CustomerDashboardPage', [
            'schedule' => $scheduleInput,
            'hasSchedule' => $hasSchedule,
            'cars' => $cars,
            'rentals' => $rentals,
        ]);
    }

    public function availability(Request $request)
    {
        abort_unless($request->user()?->isCustomer(), 403);

        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
            'rental_method' => ['required', Rule::in(['hourly', 'daily'])],
        ]);

        $month = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();

        if ($month->lt(today()->subYears(2)->startOfMonth())
            || $month->gt(today()->addYears(2)->startOfMonth())) {
            return response()->json([
                'message' => 'Availability can be viewed up to two years before or after the current month.',
            ], 422);
        }

        return response()->json(
            $this->availability->fleetMonth($month, $validated['rental_method']),
        );
    }
}

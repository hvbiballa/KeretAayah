<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Services\Catalog\RentalAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CarController extends Controller
{
    public function __construct(private RentalAvailabilityService $availability)
    {
    }

    function index(Request $request)
    {
        $cars = Car::with('images');

        /*
         * Catalogue filters and sorting are intentionally disabled while the
         * filter experience is being redesigned.
         */

        return Inertia::render('Catalog/CarsPage', [
            'cars' => $cars->get(),
        ]);
    }

    function show(Request $request, string $id)
    {
        $car = Car::with('images')->findOrFail($id);
        return Inertia::render('Catalog/CarDetailsPage', [
            'car' => $car,
        ]);
    }

    function availability(Request $request, Car $car)
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $month = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();

        if ($month->lt(today()->subYears(2)->startOfMonth())
            || $month->gt(today()->addYears(2)->startOfMonth())) {
            throw ValidationException::withMessages([
                'month' => 'Availability can be viewed up to two years before or after the current month.',
            ]);
        }

        return response()->json($this->availability->month($car, $month));
    }

    function book(Request $request, $id)
    {
        Car::query()->findOrFail($id);

        if (! $request->user()?->isCustomer()) {
            return redirect()->route('home');
        }

        return redirect()
            ->route('customer.dashboard')
            ->with('success', __('booking.flash.start_with_schedule'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Car;
use App\Models\Rental;
use App\Models\User;
use App\Services\Booking\RentalLifecycleService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private RentalLifecycleService $rentalLifecycle,
    ) {
    }

    function index(Request $request)
    {
        $cars = Car::count();
        $availableCars = Car::where('status', Car::STATUS_AVAILABLE)->count();
        $customers = User::where('role', 'customer')->count();
        $rentals = Rental::with(['car', 'user', 'payment'])->latest()->get();
        $this->rentalLifecycle->syncMany($rentals);
        $earnings = Rental::sum('total_cost');

        return Inertia::render('Admin/Dashboard', [
            'cars' => $cars,
            'availableCars' => $availableCars,
            'customers' => $customers,
            'rentals' => $rentals,
            'earnings' => $earnings,
        ]);
    }
}

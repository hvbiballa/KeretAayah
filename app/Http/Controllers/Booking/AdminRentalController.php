<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Services\Booking\RentalLifecycleService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminRentalController extends Controller
{
    public function __construct(
        private RentalLifecycleService $rentalLifecycle,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rentals = Rental::with(['car', 'user', 'payment'])->latest()->get();
        $this->rentalLifecycle->syncMany($rentals);

        return Inertia::render('Booking/RentalsPage', [
            'rentals' => $rentals
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rental $rental)
    {
        $rental->load(['user', 'car', 'payment']);
        $this->rentalLifecycle->sync($rental);

        return Inertia::render('Booking/RentalEdit', [
            'rental' => $rental,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rental $rental)
    {
        $request->validate([
            'status' => 'required|in:confirmed,ongoing,pending,completed,cancelled'
        ]);

        $rental->update([
            'status' => $request->status
        ]);

        return redirect()
            ->route('rentals.index')
            ->with('success', __('admin.flash.rental_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rental $rental)
    {
        $rental->delete();

        return back()->with('success', __('admin.flash.rental_deleted'));
    }
}

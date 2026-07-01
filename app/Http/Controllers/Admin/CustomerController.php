<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Services\RentalLifecycleService;
use App\Support\VerificationDocumentData;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
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
        $customers = User::where('role', 'customer')->with('rentals')->latest()->get();

        return Inertia::render('Admin/CustomersPage', [
            'customers' => $customers
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
        $customer = User::where('role', 'customer')
            ->with([
                'verification',
                'rentals.car',
                'rentals.payment.receiver',
            ])
            ->findOrFail($id);

        $payments = Payment::with(['rental.car', 'receiver'])
            ->whereHas('rental', fn ($query) => $query->where('user_id', $customer->id))
            ->latest()
            ->get();

        $this->rentalLifecycle->syncMany($customer->rentals);

        $customerData = $customer->toArray();

        if (auth()->user()->isAdmin() && $customer->verification) {
            $customerData['verification'] = VerificationDocumentData::verification($customer->verification);
        } else {
            unset($customerData['verification']);
        }

        return Inertia::render('Admin/CustomerDetails', [
            'customer' => $customerData,
            'payments' => $payments,
            'canViewVerification' => auth()->user()->isAdmin(),
            'canManageVerification' => auth()->user()->isAdmin(),
            'canDeleteCustomer' => auth()->user()->isAdmin(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);

        if ($customer->rentals()->exists()) {
            return redirect()->route('customers.index')->with([
                'error' => __('admin.flash.customer_delete_blocked'),
            ]);
        }

        $customer->delete();

        return redirect()->route('customers.index')->with([
            'success' => __('admin.flash.customer_deleted'),
        ]);
    }
}

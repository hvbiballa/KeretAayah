<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Booking\RentalLifecycleService;
use App\Notifications\Payment\PaymentStatusUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Throwable;

class AdminPaymentController extends Controller
{
    public function __construct(
        private RentalLifecycleService $rentalLifecycle,
    ) {
    }

    public function index()
    {
        return Inertia::render('Payment/PaymentsPage', [
            'payments' => Payment::with(['rental.user', 'rental.car', 'receiver'])
                ->latest()
                ->get(),
        ]);
    }

    public function edit(Payment $payment)
    {
        return Inertia::render('Payment/PaymentEdit', [
            'payment' => $payment->load(['rental.user', 'rental.car', 'receiver']),
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => ['nullable', Rule::in([
                Payment::METHOD_CASH,
                Payment::METHOD_BANK_TRANSFER,
                Payment::METHOD_DUITNOW_QR,
                Payment::METHOD_OTHER,
            ])],
            'payment_date' => 'nullable|date',
            'status' => ['required', Rule::in([
                Payment::STATUS_PENDING,
                Payment::STATUS_PROOF_SUBMITTED,
                Payment::STATUS_PARTIALLY_PAID,
                Payment::STATUS_PAID,
                Payment::STATUS_REFUNDED,
            ])],
            'notes' => 'nullable|string|max:5000',
        ]);

        $payment->update([
            ...$validated,
            'received_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $payment->refresh()->load(['rental.user', 'rental.car']);
        $this->rentalLifecycle->sync($payment->rental);

        try {
            $payment->rental->user->notify(new PaymentStatusUpdatedNotification($payment));
        } catch (Throwable) {
            // Notification failures must not block a completed payment update.
        }

        return redirect()
            ->route('payments.index')
            ->with('success', __('payment.flash.updated'));
    }
}

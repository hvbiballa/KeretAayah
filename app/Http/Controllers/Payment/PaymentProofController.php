<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\User;
use App\Notifications\Payment\PaymentProofSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class PaymentProofController extends Controller
{
    public function store(Request $request, Rental $rental)
    {
        $user = $request->user();

        abort_unless(
            $user && (int) $rental->user_id === (int) $user->id,
            403,
        );

        $payment = $rental->payment()->firstOrFail();

        if (! $payment->can_upload_proof) {
            throw ValidationException::withMessages([
                'proof' => __('payment.proof_locked'),
            ]);
        }

        $validated = $request->validate([
            'proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $proofPath = $validated['proof']->store(
            "payment-proofs/{$user->id}/{$rental->id}",
            'local',
        );

        Storage::disk('local')->delete(array_filter([$payment->proof_path]));

        $payment->update([
            'proof_path' => $proofPath,
            'status' => Payment::STATUS_PROOF_SUBMITTED,
        ]);

        $payment->refresh()->load(['rental.user', 'rental.car']);

        try {
            User::whereIn('role', ['admin', 'staff'])
                ->get()
                ->each(fn (User $reviewer) => $reviewer->notify(
                    new PaymentProofSubmittedNotification($payment),
                ));
        } catch (Throwable) {
            // Notification failures must not block a completed proof upload.
        }

        return back()->with('success', __('payment.flash.proof_submitted'));
    }
}

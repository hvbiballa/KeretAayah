<?php

namespace App\Http\Controllers\Verification;

use App\Http\Controllers\Controller;
use App\Models\CustomerVerification;
use App\Models\User;
use App\Notifications\Verification\VerificationStatusUpdatedNotification;
use App\Support\VerificationDocumentData;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Throwable;

class AdminVerificationController extends Controller
{
    public function index()
    {
        return Inertia::render('Verification/VerificationsPage', [
            'customers' => User::where('role', 'customer')
                ->with('verification')
                ->latest()
                ->get()
                ->map(function (User $customer) {
                    $data = $customer->toArray();
                    $verification = $customer->verification;

                    $data['verification'] = $verification
                        ? VerificationDocumentData::verification($verification)
                        : null;

                    return $data;
                }),
        ]);
    }

    public function approve(Request $request, User $customer)
    {
        $verification = $this->customerVerification($customer);

        if ($verification->status === CustomerVerification::STATUS_SUSPENDED) {
            throw ValidationException::withMessages([
                'status' => __('verification.flash.lift_before_review'),
            ]);
        }

        if (! $verification->government_id_path || ! $verification->driving_license_path) {
            throw ValidationException::withMessages([
                'documents' => __('verification.flash.documents_required'),
            ]);
        }

        $validated = $request->validate([
            'id_expiry_date' => 'required|date|after_or_equal:today',
            'driving_license_expiry_date' => 'required|date|after_or_equal:today',
        ]);

        $verification->update([
            ...$validated,
            'status' => CustomerVerification::STATUS_APPROVED,
            'verified_at' => now(),
            'review_note' => null,
        ]);

        try {
            $customer->notify(new VerificationStatusUpdatedNotification(
                $verification->fresh(),
                trans('email.verification_subjects.approved', [], 'ms'),
                trans('email.verification_messages.approved', [], 'ms'),
                trans('email.verification_subjects.approved', [], 'en'),
                trans('email.verification_messages.approved', [], 'en'),
            ));
        } catch (Throwable) {
            // Notification failures must not block a completed verification approval.
        }

        return back()->with('success', __('verification.flash.approved'));
    }

    public function updateStatus(Request $request, User $customer)
    {
        $verification = $this->customerVerification($customer);

        $validated = $request->validate([
            'action' => ['required', Rule::in([
                'reject',
                'suspend',
                'request_documents',
                'lift_suspension',
            ])],
            'review_note' => [
                Rule::requiredIf(in_array($request->input('action'), [
                    'reject',
                    'suspend',
                    'request_documents',
                ])),
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        if ($verification->status === CustomerVerification::STATUS_SUSPENDED
            && $validated['action'] !== 'lift_suspension') {
            throw ValidationException::withMessages([
                'status' => __('verification.flash.lift_before_change'),
            ]);
        }

        if ($verification->status !== CustomerVerification::STATUS_SUSPENDED
            && $validated['action'] === 'lift_suspension') {
            throw ValidationException::withMessages([
                'status' => __('verification.flash.not_suspended'),
            ]);
        }

        $attributes = match ($validated['action']) {
            'reject' => [
                'status' => CustomerVerification::STATUS_REJECTED,
                'verified_at' => null,
                'review_note' => $validated['review_note'],
            ],
            'suspend' => [
                'status' => CustomerVerification::STATUS_SUSPENDED,
                'review_note' => $validated['review_note'],
            ],
            'request_documents' => [
                'status' => CustomerVerification::STATUS_PENDING,
                'verified_at' => null,
                'review_note' => $validated['review_note'],
            ],
            'lift_suspension' => [
                'status' => CustomerVerification::STATUS_PENDING,
                'verified_at' => null,
                'review_note' => null,
            ],
        };

        $verification->update($attributes);

        $notification = match ($validated['action']) {
            'reject' => new VerificationStatusUpdatedNotification(
                $verification->fresh(),
                trans('email.verification_subjects.rejected', [], 'ms'),
                trans('email.verification_messages.rejected', [], 'ms'),
                trans('email.verification_subjects.rejected', [], 'en'),
                trans('email.verification_messages.rejected', [], 'en'),
            ),
            'suspend' => new VerificationStatusUpdatedNotification(
                $verification->fresh(),
                trans('email.verification_subjects.suspended', [], 'ms'),
                trans('email.verification_messages.suspended', [], 'ms'),
                trans('email.verification_subjects.suspended', [], 'en'),
                trans('email.verification_messages.suspended', [], 'en'),
            ),
            'request_documents' => new VerificationStatusUpdatedNotification(
                $verification->fresh(),
                trans('email.verification_subjects.update_requested', [], 'ms'),
                trans('email.verification_messages.update_requested', [], 'ms'),
                trans('email.verification_subjects.update_requested', [], 'en'),
                trans('email.verification_messages.update_requested', [], 'en'),
            ),
            default => null,
        };

        if ($notification) {
            try {
                $customer->notify($notification);
            } catch (Throwable) {
                // Notification failures must not block a completed verification update.
            }
        }

        return back()->with('success', __('verification.flash.updated'));
    }

    private function customerVerification(User $customer): CustomerVerification
    {
        abort_unless($customer->isCustomer(), 404);

        return $customer->verification()->firstOrCreate();
    }
}

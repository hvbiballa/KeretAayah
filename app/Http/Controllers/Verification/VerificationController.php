<?php

namespace App\Http\Controllers\Verification;

use App\Http\Controllers\Controller;
use App\Models\CustomerVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isCustomer(), 403);

        return redirect()->to(route('profile.edit').'#verification');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isCustomer(), 403);

        $verification = $request->user()->verification()->firstOrCreate();

        if ($verification->status === CustomerVerification::STATUS_SUSPENDED) {
            return back()->with('error', __('verification.flash.suspended_no_submit'));
        }

        if (! $verification->can_submit_documents) {
            return back()->with('error', __('verification.flash.already_submitted'));
        }

        $validated = $request->validate([
            'government_id' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'driving_license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $governmentIdPath = $validated['government_id']->store(
            "customer-verifications/{$request->user()->id}",
            'local',
        );
        $drivingLicensePath = $validated['driving_license']->store(
            "customer-verifications/{$request->user()->id}",
            'local',
        );

        Storage::disk('local')->delete(array_filter([
            $verification->government_id_path,
            $verification->driving_license_path,
        ]));

        $verification->update([
            'status' => CustomerVerification::STATUS_PENDING,
            'government_id_path' => $governmentIdPath,
            'driving_license_path' => $drivingLicensePath,
            'id_expiry_date' => null,
            'driving_license_expiry_date' => null,
            'verified_at' => null,
            'review_note' => null,
        ]);

        return back()->with('success', __('verification.flash.submitted'));
    }
}

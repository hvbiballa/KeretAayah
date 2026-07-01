<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($this->verifiedRedirectPath($request).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended($this->verifiedRedirectPath($request).'?verified=1');
    }

    private function verifiedRedirectPath(EmailVerificationRequest $request): string
    {
        return match ($request->user()->role) {
            'admin' => route('dashboard', absolute: false),
            'staff' => route('payments.index', absolute: false),
            default => route('customer.dashboard', absolute: false),
        };
    }
}

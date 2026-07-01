<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($this->verifiedRedirectPath($request));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    private function verifiedRedirectPath(Request $request): string
    {
        return match ($request->user()->role) {
            'admin' => route('dashboard', absolute: false),
            'staff' => route('payments.index', absolute: false),
            default => route('customer.dashboard', absolute: false),
        };
    }
}

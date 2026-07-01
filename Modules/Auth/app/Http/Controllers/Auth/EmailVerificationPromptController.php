<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended($this->verifiedRedirectPath($request))
                    : Inertia::render('Auth/VerifyEmail', ['status' => session('status')]);
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

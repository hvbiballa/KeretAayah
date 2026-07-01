<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->isCustomer() && ! $user->hasVerifiedEmail()) {
            return redirect()
                ->route('verification.notice')
                ->with('error', __('verification.restriction.email_required'));
        }

        return $next($request);
    }
}

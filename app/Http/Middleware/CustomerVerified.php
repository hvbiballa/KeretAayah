<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->canCreateBookings()) {
            return redirect()
                ->route('customer.verification.index')
                ->with('error', $user?->bookingRestrictionMessage()
                    ?? __('verification.restriction.signin_required'));
        }

        return $next($request);
    }
}

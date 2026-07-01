<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentReviewer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->canReviewPayments()) {
            $user = $request->user();
            $target = $user?->isCustomer()
                ? route('customer.dashboard')
                : ($user?->isAdmin() ? route('dashboard') : '/');

            return redirect($target)->with('error', __('common.errors.payment_reviewer_only'));
        }

        return $next($request);
    }
}

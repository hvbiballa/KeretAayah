<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect('/login');
        }

        if (! auth()->user()->isAdmin()) {
            $user = auth()->user();
            $target = $user->canReviewPayments()
                ? route('payments.index')
                : ($user->isCustomer() ? route('customer.dashboard') : '/');

            return redirect($target)->with('error', __('common.errors.admin_only'));
        }

        return $next($request);
    }
}

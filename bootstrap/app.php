<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'customer.email.verified' => \App\Http\Middleware\CustomerEmailVerified::class,
            'customer.verified' => \App\Http\Middleware\CustomerVerified::class,
            'payment.reviewer' => \App\Http\Middleware\PaymentReviewer::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (QueryException $exception, Request $request) {
            $message = $request->routeIs('profile.destroy')
                ? __('profile.flash.delete_blocked')
                : ($exception->getCode() === '23000'
                    ? __('common.errors.action_blocked')
                    : __('common.errors.action_failed'));

            if (! $request->isMethod('GET')) {
                return redirect()->back()->with('error', $message);
            }

            return Inertia::render('ErrorPage', [
                'status' => 500,
                'title' => __('common.errors.server_title'),
                'message' => $message,
            ])->toResponse($request)->setStatusCode(500);
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            $status = $exception->getStatusCode();

            if (! in_array($status, [403, 404, 419, 500, 503], true)) {
                return null;
            }

            if ($status === 419 && ! $request->isMethod('GET')) {
                return redirect()->back()->with('error', __('common.errors.session_expired'));
            }

            [$title, $message] = match ($status) {
                403 => [__('common.errors.forbidden_title'), __('common.errors.forbidden_message')],
                404 => [__('common.errors.not_found_title'), __('common.errors.not_found_message')],
                419 => [__('common.errors.server_title'), __('common.errors.session_expired')],
                default => [__('common.errors.server_title'), __('common.errors.server_message')],
            };

            return Inertia::render('ErrorPage', [
                'status' => $status,
                'title' => $title,
                'message' => $message,
            ])->toResponse($request)->setStatusCode($status);
        });
    })->create();

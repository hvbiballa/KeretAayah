<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Middleware;
use App\Support\VerificationDocumentData;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'isAdmin' => $request->user()?->isAdmin(),
                'isStaff' => $request->user()?->isStaff(),
                'canReviewPayments' => $request->user()?->canReviewPayments(),
                'verification' => $request->user()?->isCustomer() && $request->user()->verification
                    ? VerificationDocumentData::verification($request->user()->verification)
                    : null,
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'i18n' => [
                'locale' => fn() => App::getLocale(),
                'fallbackLocale' => fn() => config('app.fallback_locale', 'en'),
                'supportedLocales' => fn() => config('app.supported_locales', ['ms', 'en']),
                'translations' => fn() => $this->frontendTranslations(),
            ],
        ];
    }

    private function frontendTranslations(): array
    {
        return collect(config('app.frontend_translation_groups', []))
            ->mapWithKeys(fn(string $group) => [$group => trans($group)])
            ->all();
    }
}

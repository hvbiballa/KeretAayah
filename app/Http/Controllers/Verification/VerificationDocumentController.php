<?php

namespace App\Http\Controllers\Verification;

use App\Http\Controllers\Controller;

use App\Models\CustomerVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationDocumentController extends Controller
{
    public function __invoke(Request $request, CustomerVerification $verification, string $document)
    {
        $user = $request->user();

        abort_unless(
            $user && (
                $user->isAdmin()
                || (int) $verification->user_id === (int) $user->id
            ),
            403,
        );

        $path = match ($document) {
            'government-id' => $verification->government_id_path,
            'driving-license' => $verification->driving_license_path,
            default => null,
        };

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }
}

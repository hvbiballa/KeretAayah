<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentProofController extends Controller
{
    public function __invoke(Request $request, Payment $payment)
    {
        $user = $request->user();

        abort_unless(
            $user && (
                $user->canReviewPayments()
                || (int) $payment->rental->user_id === (int) $user->id
            ),
            403,
        );

        abort_unless($payment->proof_path && Storage::disk('local')->exists($payment->proof_path), 404);

        return Storage::disk('local')->response($payment->proof_path);
    }
}

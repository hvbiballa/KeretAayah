<?php

namespace App\Services\Booking;

use App\Models\Payment;
use App\Models\Rental;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class RentalLifecycleService
{
    public function sync(Rental $rental, ?CarbonInterface $now = null): Rental
    {
        $rental->loadMissing('payment');

        $status = $this->determineStatus($rental, $now);

        if ($rental->getRawOriginal('status') !== $status) {
            $rental->forceFill([
                'status' => $status,
            ])->saveQuietly();
        }

        $rental->setAttribute('status', $status);

        return $rental;
    }

    public function syncMany(iterable $rentals, ?CarbonInterface $now = null): iterable
    {
        if ($rentals instanceof EloquentCollection) {
            $rentals->loadMissing('payment');
        }

        foreach ($rentals as $rental) {
            if ($rental instanceof Rental) {
                $this->sync($rental, $now);
            }
        }

        return $rentals;
    }

    public function determineStatus(Rental $rental, ?CarbonInterface $now = null): string
    {
        $now ??= now();

        if ($rental->getRawOriginal('status') === Rental::STATUS_CANCELLED) {
            return Rental::STATUS_CANCELLED;
        }

        if (! $rental->pickup_at || ! $rental->return_at) {
            return Rental::STATUS_CONFIRMED;
        }

        if ($now->lt($rental->pickup_at)) {
            return Rental::STATUS_CONFIRMED;
        }

        if ($now->lt($rental->return_at)) {
            return Rental::STATUS_ONGOING;
        }

        return $this->paymentSettled($rental)
            ? Rental::STATUS_COMPLETED
            : Rental::STATUS_PENDING;
    }

    public function hasReachedPendingPaymentLimit(User $user): bool
    {
        return $this->pendingPaymentCountForUser($user) >= Rental::MAX_PENDING_PAYMENT_BOOKINGS;
    }

    public function pendingPaymentCountForUser(User $user): int
    {
        return Rental::query()
            ->where('user_id', $user->id)
            ->where('return_at', '<=', now())
            ->where('status', '!=', Rental::STATUS_CANCELLED)
            ->where(function ($query) {
                $query->whereDoesntHave('payment')
                    ->orWhereHas('payment', function ($paymentQuery) {
                        $paymentQuery->where('status', '!=', Payment::STATUS_PAID);
                    });
            })
            ->count();
    }

    protected function paymentSettled(Rental $rental): bool
    {
        $payment = $rental->relationLoaded('payment')
            ? $rental->payment
            : $rental->payment()->first();

        return $payment?->status === Payment::STATUS_PAID;
    }
}

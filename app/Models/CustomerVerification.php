<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerVerification extends Model
{
    public const STATUS_PENDING = 'Pending';

    public const STATUS_APPROVED = 'Approved';

    public const STATUS_REJECTED = 'Rejected';

    public const STATUS_SUSPENDED = 'Suspended';

    protected $fillable = [
        'status',
        'government_id_path',
        'driving_license_path',
        'id_expiry_date',
        'driving_license_expiry_date',
        'verified_at',
        'review_note',
    ];

    protected $appends = [
        'is_expired',
        'can_book',
        'display_status',
        'booking_restriction_message',
        'can_submit_documents',
    ];

    protected function casts(): array
    {
        return [
            'id_expiry_date' => 'date',
            'driving_license_expiry_date' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return ($this->id_expiry_date && $this->id_expiry_date->isBefore(today()))
            || ($this->driving_license_expiry_date
                && $this->driving_license_expiry_date->isBefore(today()));
    }

    public function getCanBookAttribute(): bool
    {
        return $this->status === self::STATUS_APPROVED
            && $this->id_expiry_date
            && $this->driving_license_expiry_date
            && ! $this->is_expired;
    }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === self::STATUS_APPROVED && $this->is_expired) {
            return 'Expired';
        }

        return $this->status ?? self::STATUS_PENDING;
    }

    public function getBookingRestrictionMessageAttribute(): string
    {
        if ($this->status === self::STATUS_SUSPENDED) {
            return __('verification.restriction.suspended');
        }

        if ($this->is_expired) {
            return __('verification.restriction.expired');
        }

        if ($this->status === self::STATUS_REJECTED) {
            return __('verification.restriction.rejected');
        }

        if ($this->status !== self::STATUS_APPROVED) {
            return __('verification.restriction.pending');
        }

        return __('verification.restriction.review_required');
    }

    public function getCanSubmitDocumentsAttribute(): bool
    {
        if ($this->status === self::STATUS_SUSPENDED) {
            return false;
        }

        if (! $this->government_id_path || ! $this->driving_license_path) {
            return true;
        }

        return $this->status === self::STATUS_REJECTED
            || $this->is_expired
            || ($this->status === self::STATUS_PENDING && (bool) $this->review_note);
    }
}

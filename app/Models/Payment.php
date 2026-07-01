<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROOF_SUBMITTED = 'proof_submitted';

    public const STATUS_PARTIALLY_PAID = 'partially_paid';

    public const STATUS_PAID = 'paid';

    public const STATUS_REFUNDED = 'refunded';

    public const METHOD_CASH = 'cash';

    public const METHOD_BANK_TRANSFER = 'bank_transfer';

    public const METHOD_DUITNOW_QR = 'duitnow_qr';

    public const METHOD_OTHER = 'other';

    protected $fillable = [
        'amount_due',
        'amount_paid',
        'status',
        'payment_method',
        'payment_date',
        'proof_path',
        'notes',
        'received_by',
        'reviewed_at',
    ];

    protected $appends = [
        'can_upload_proof',
    ];

    protected function casts(): array
    {
        return [
            'amount_due' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'payment_date' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getCanUploadProofAttribute(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_PROOF_SUBMITTED,
        ], true);
    }
}

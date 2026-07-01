<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Car;
use App\Models\User;

class Rental extends Model
{
    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_ONGOING = 'ongoing';

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const MAX_PENDING_PAYMENT_BOOKINGS = 2;

    protected $fillable = [
        'user_id',
        'car_id',
        'rental_method',
        'pickup_at',
        'return_at',
        'applied_rate',
        'duration_units',
        'total_cost',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'pickup_at' => 'datetime',
            'return_at' => 'datetime',
            'applied_rate' => 'decimal:2',
            'duration_units' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}

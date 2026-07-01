<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    public const STATUS_AVAILABLE = 'Available';

    public const STATUS_UNDER_MAINTENANCE = 'Under Maintenance';

    protected $fillable = [
        'name',
        'number_plate',
        'brand',
        'model',
        'year',
        'car_type',
        'daily_rent_price',
        'hourly_rent_price',
        'status',
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function images()
    {
        return $this->hasMany(CarImage::class)
            ->orderByDesc('is_primary')
            ->orderBy('position')
            ->orderBy('id');
    }
}

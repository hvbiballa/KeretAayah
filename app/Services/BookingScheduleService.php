<?php

namespace App\Services;

use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingScheduleService
{
    public function validate(array $input): array
    {
        return Validator::make(
            $input,
            [
                'rental_method' => ['required', Rule::in(['hourly', 'daily'])],
                'pickup_date' => ['required', 'date'],
                'pickup_time' => ['required', 'date_format:H:i'],
                'return_date' => ['required', 'date'],
                'return_time' => ['required', 'date_format:H:i'],
            ],
            [
                'rental_method.required' => __('booking.flash.schedule_required'),
                'pickup_date.required' => __('booking.flash.schedule_required'),
                'pickup_time.required' => __('booking.flash.schedule_required'),
                'return_date.required' => __('booking.flash.schedule_required'),
                'return_time.required' => __('booking.flash.schedule_required'),
            ],
        )->validate();
    }

    public function resolve(array $validated): array
    {
        $pickupAt = Carbon::parse("{$validated['pickup_date']} {$validated['pickup_time']}");
        $returnAt = Carbon::parse("{$validated['return_date']} {$validated['return_time']}");

        if ($returnAt->lte($pickupAt)) {
            throw ValidationException::withMessages([
                'return_date' => __('booking.flash.return_after_pickup'),
            ]);
        }

        if ($validated['rental_method'] === 'hourly') {
            $durationUnits = round($pickupAt->diffInMinutes($returnAt) / 60, 2);

            if ($durationUnits < 2 || $durationUnits > 20) {
                throw ValidationException::withMessages([
                    'return_time' => __('booking.flash.hourly_range'),
                ]);
            }

            $reservationStart = $pickupAt->copy();
            $reservationEnd = $returnAt->copy();
        } else {
            $durationUnits = $pickupAt->copy()->startOfDay()->diffInDays(
                $returnAt->copy()->startOfDay(),
            );

            if ($durationUnits < 1 || $durationUnits > 7) {
                throw ValidationException::withMessages([
                    'return_date' => __('booking.flash.daily_range'),
                ]);
            }

            $reservationStart = $pickupAt->copy()->startOfDay();
            $reservationEnd = $returnAt->copy()->addDay()->startOfDay();
        }

        return [
            'rental_method' => $validated['rental_method'],
            'pickup_at' => $pickupAt,
            'return_at' => $returnAt,
            'duration_units' => $durationUnits,
            'reservation_start' => $reservationStart,
            'reservation_end' => $reservationEnd,
        ];
    }

    public function quote(Car $car, array $schedule): array
    {
        $appliedRate = $schedule['rental_method'] === 'hourly'
            ? $car->hourly_rent_price
            : $car->daily_rent_price;

        return [
            'applied_rate' => round((float) $appliedRate, 2),
            'total_cost' => round($schedule['duration_units'] * $appliedRate, 2),
        ];
    }
}

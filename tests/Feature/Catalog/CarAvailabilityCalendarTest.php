<?php

use App\Models\Car;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->car = Car::create([
        'name' => 'Calendar Car',
        'brand' => 'Test Brand',
        'model' => 'Test Model',
        'year' => 2025,
        'car_type' => 'sedan',
        'daily_rent_price' => 120,
        'hourly_rent_price' => 15,
        'status' => Car::STATUS_AVAILABLE,
    ]);
});

function availabilityUrl(Car $car, Carbon $date): string
{
    return route('cars.availability', [
        'car' => $car,
        'month' => $date->format('Y-m'),
    ]);
}

function createCalendarRental(Car $car, User $user, array $attributes): Rental
{
    return Rental::create([
        'user_id' => $user->id,
        'car_id' => $car->id,
        'rental_method' => 'hourly',
        'pickup_at' => '2030-06-10 09:00:00',
        'return_at' => '2030-06-10 12:00:00',
        'applied_rate' => 15,
        'duration_units' => 3,
        'total_cost' => 45,
        'status' => 'ongoing',
        ...$attributes,
    ]);
}

it('marks every inclusive daily rental date as booked without preferred times', function () {
    $date = today()->addMonths(2)->startOfMonth()->addDays(9);

    createCalendarRental($this->car, $this->user, [
        'rental_method' => 'daily',
        'pickup_at' => $date->copy()->setTime(10, 0),
        'return_at' => $date->copy()->addDays(2)->setTime(16, 0),
        'applied_rate' => 120,
        'duration_units' => 2,
        'total_cost' => 240,
    ]);

    $days = $this->getJson(availabilityUrl($this->car, $date))
        ->assertOk()
        ->json('days');

    foreach (range(0, 2) as $offset) {
        $day = collect($days)->firstWhere('date', $date->copy()->addDays($offset)->toDateString());

        expect($day['status'])->toBe('booked')
            ->and($day['available_ranges'])->toBe([]);
    }
});

it('marks hourly rental dates as partially booked with cooldown-aware available ranges only', function () {
    $date = today()->addMonths(2)->startOfMonth()->addDays(9);

    createCalendarRental($this->car, $this->user, [
        'pickup_at' => $date->copy()->setTime(9, 0),
        'return_at' => $date->copy()->setTime(12, 30),
    ]);

    $day = collect($this->getJson(availabilityUrl($this->car, $date))
        ->assertOk()
        ->json('days'))
        ->firstWhere('date', $date->toDateString());

    expect($day)->toBe([
        'date' => $date->toDateString(),
        'status' => 'partially_booked',
        'available_ranges' => [[
            'label' => '12:00 AM - 9:00 AM',
        ], [
            'label' => '1:00 PM - 11:59 PM',
        ]],
    ]);
});

it('merges cooldown-connected bookings before calculating available ranges', function () {
    $date = today()->addMonths(2)->startOfMonth()->addDays(9);

    createCalendarRental($this->car, $this->user, [
        'pickup_at' => $date->copy()->setTime(9, 0),
        'return_at' => $date->copy()->setTime(11, 0),
    ]);
    createCalendarRental($this->car, $this->user, [
        'pickup_at' => $date->copy()->setTime(11, 30),
        'return_at' => $date->copy()->setTime(14, 0),
    ]);

    $day = collect($this->getJson(availabilityUrl($this->car, $date))
        ->assertOk()
        ->json('days'))
        ->firstWhere('date', $date->toDateString());

    expect($day['status'])->toBe('partially_booked')
        ->and($day['available_ranges'])->toBe([[
            'label' => '12:00 AM - 9:00 AM',
        ], [
            'label' => '2:30 PM - 11:59 PM',
        ]]);
});

it('marks a date as booked when no two hour hourly slot remains', function () {
    $date = today()->addMonths(2)->startOfMonth()->addDays(9);

    createCalendarRental($this->car, $this->user, [
        'pickup_at' => $date->copy()->startOfDay(),
        'return_at' => $date->copy()->setTime(11, 0),
    ]);
    createCalendarRental($this->car, $this->user, [
        'pickup_at' => $date->copy()->setTime(13, 29),
        'return_at' => $date->copy()->endOfDay(),
    ]);

    $day = collect($this->getJson(availabilityUrl($this->car, $date))
        ->assertOk()
        ->json('days'))
        ->firstWhere('date', $date->toDateString());

    expect($day['status'])->toBe('booked')
        ->and($day['available_ranges'])->toBe([]);
});

it('never blocks availability with cancelled rentals', function () {
    $date = today()->addMonths(2)->startOfMonth()->addDays(9);

    createCalendarRental($this->car, $this->user, [
        'pickup_at' => $date->copy()->setTime(9, 0),
        'return_at' => $date->copy()->setTime(12, 0),
        'status' => 'cancelled',
    ]);

    $day = collect($this->getJson(availabilityUrl($this->car, $date))
        ->assertOk()
        ->json('days'))
        ->firstWhere('date', $date->toDateString());

    expect($day['status'])->toBe('available')
        ->and($day['available_ranges'])->toBe([[
            'label' => '12:00 AM - 11:59 PM',
        ]]);
});

it('marks every visible date unavailable when the car is under maintenance', function () {
    $date = today()->addMonths(2)->startOfMonth();
    $this->car->update(['status' => Car::STATUS_UNDER_MAINTENANCE]);

    $days = $this->getJson(availabilityUrl($this->car, $date))
        ->assertOk()
        ->json('days');

    expect(collect($days)->pluck('status')->unique()->values()->all())
        ->toBe(['maintenance'])
        ->and(collect($days)->pluck('available_ranges')->unique()->values()->all())
        ->toBe([[]]);
});

it('shows historical dates as past with reconstructed available ranges only', function () {
    $date = today()->subMonth()->startOfMonth()->addDays(9);

    createCalendarRental($this->car, $this->user, [
        'pickup_at' => $date->copy()->setTime(9, 0),
        'return_at' => $date->copy()->setTime(12, 0),
        'status' => 'completed',
    ]);

    $day = collect($this->getJson(availabilityUrl($this->car, $date))
        ->assertOk()
        ->json('days'))
        ->firstWhere('date', $date->toDateString());

    expect($day['status'])->toBe('past')
        ->and($day['available_ranges'])->toBe([[
            'label' => '12:00 AM - 9:00 AM',
        ], [
            'label' => '12:30 PM - 11:59 PM',
        ]]);
});

it('applies daily rental cooldown spillover to the following date', function () {
    $date = today()->addMonths(2)->startOfMonth()->addDays(9);

    createCalendarRental($this->car, $this->user, [
        'rental_method' => 'daily',
        'pickup_at' => $date->copy()->setTime(10, 0),
        'return_at' => $date->copy()->addDay()->setTime(16, 0),
        'applied_rate' => 120,
        'duration_units' => 1,
        'total_cost' => 120,
    ]);

    $day = collect($this->getJson(availabilityUrl($this->car, $date))
        ->assertOk()
        ->json('days'))
        ->firstWhere('date', $date->copy()->addDays(2)->toDateString());

    expect($day['status'])->toBe('partially_booked')
        ->and($day['available_ranges'])->toBe([[
            'label' => '12:30 AM - 11:59 PM',
        ]]);
});

it('limits public availability requests to a bounded month range', function () {
    $date = today()->addYears(3);

    $this->getJson(availabilityUrl($this->car, $date))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('month');
});

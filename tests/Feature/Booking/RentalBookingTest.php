<?php

use App\Models\Car;
use App\Models\CustomerVerification;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();

    User::factory()->create([
        'role' => 'admin',
    ]);

    $this->user = User::factory()->create();
    $this->user->verification()->create([
        'status' => CustomerVerification::STATUS_APPROVED,
        'government_id_path' => 'test/government-id.pdf',
        'driving_license_path' => 'test/driving-license.pdf',
        'id_expiry_date' => '2031-12-31',
        'driving_license_expiry_date' => '2031-12-31',
        'verified_at' => now(),
    ]);

    $this->car = Car::create([
        'name' => 'Test Car',
        'brand' => 'Test Brand',
        'model' => 'Test Model',
        'year' => 2025,
        'car_type' => 'sedan',
        'daily_rent_price' => 120,
        'hourly_rent_price' => 15,
        'status' => Car::STATUS_AVAILABLE,
    ]);
});

it('creates an hourly rental using the hourly rate', function () {
    $response = $this->actingAs($this->user)
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'hourly',
            'pickup_date' => '2030-06-10',
            'pickup_time' => '09:00',
            'return_date' => '2030-06-10',
            'return_time' => '12:30',
        ]);

    $rental = Rental::firstOrFail();

    $response->assertRedirect(route('bookings.payment', $rental));

    expect($rental->rental_method)->toBe('hourly')
        ->and($rental->duration_units)->toBe('3.50')
        ->and($rental->applied_rate)->toBe('15.00')
        ->and($rental->total_cost)->toBe('52.50');
});

it('rejects hourly rentals shorter than two hours', function () {
    $this->actingAs($this->user)
        ->from('/cars/'.$this->car->id.'/book')
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'hourly',
            'pickup_date' => '2030-06-10',
            'pickup_time' => '09:00',
            'return_date' => '2030-06-10',
            'return_time' => '10:30',
        ])
        ->assertRedirect('/cars/'.$this->car->id.'/book')
        ->assertSessionHasErrors('return_time');

    expect(Rental::count())->toBe(0);
});

it('rejects hourly rentals longer than twenty hours', function () {
    $this->actingAs($this->user)
        ->from('/cars/'.$this->car->id.'/book')
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'hourly',
            'pickup_date' => '2030-06-10',
            'pickup_time' => '09:00',
            'return_date' => '2030-06-11',
            'return_time' => '06:00',
        ])
        ->assertRedirect('/cars/'.$this->car->id.'/book')
        ->assertSessionHasErrors('return_time');

    expect(Rental::count())->toBe(0);
});

it('creates a daily rental using the daily rate', function () {
    $response = $this->actingAs($this->user)
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'daily',
            'pickup_date' => '2030-06-10',
            'pickup_time' => '10:00',
            'return_date' => '2030-06-12',
            'return_time' => '16:00',
        ]);

    $rental = Rental::firstOrFail();

    $response->assertRedirect(route('bookings.payment', $rental));

    expect($rental->rental_method)->toBe('daily')
        ->and($rental->duration_units)->toBe('2.00')
        ->and($rental->applied_rate)->toBe('120.00')
        ->and($rental->total_cost)->toBe('240.00');
});

it('rejects daily rentals longer than seven days', function () {
    $this->actingAs($this->user)
        ->from('/cars/'.$this->car->id.'/book')
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'daily',
            'pickup_date' => '2030-06-10',
            'pickup_time' => '10:00',
            'return_date' => '2030-06-18',
            'return_time' => '16:00',
        ])
        ->assertRedirect('/cars/'.$this->car->id.'/book')
        ->assertSessionHasErrors('return_date');

    expect(Rental::count())->toBe(0);
});

it('blocks an hourly rental that overlaps a reserved daily rental date', function () {
    Rental::create([
        'user_id' => $this->user->id,
        'car_id' => $this->car->id,
        'rental_method' => 'daily',
        'pickup_at' => '2030-06-10 10:00:00',
        'return_at' => '2030-06-11 10:00:00',
        'applied_rate' => 120,
        'duration_units' => 1,
        'total_cost' => 120,
        'status' => 'ongoing',
    ]);

    $this->actingAs($this->user)
        ->from('/cars/'.$this->car->id.'/book')
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'hourly',
            'pickup_date' => '2030-06-11',
            'pickup_time' => '18:00',
            'return_date' => '2030-06-11',
            'return_time' => '20:00',
        ])
        ->assertRedirect('/cars/'.$this->car->id.'/book')
        ->assertSessionHas('error');

    expect(Rental::count())->toBe(1);
});

it('blocks an hourly pickup before the previous booking cooldown ends', function () {
    Rental::create([
        'user_id' => $this->user->id,
        'car_id' => $this->car->id,
        'rental_method' => 'hourly',
        'pickup_at' => '2030-06-10 09:00:00',
        'return_at' => '2030-06-10 12:00:00',
        'applied_rate' => 15,
        'duration_units' => 3,
        'total_cost' => 45,
        'status' => 'ongoing',
    ]);

    $this->actingAs($this->user)
        ->from('/cars/'.$this->car->id.'/book')
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'hourly',
            'pickup_date' => '2030-06-10',
            'pickup_time' => '12:29',
            'return_date' => '2030-06-10',
            'return_time' => '14:29',
        ])
        ->assertRedirect('/cars/'.$this->car->id.'/book')
        ->assertSessionHas('error');

    expect(Rental::count())->toBe(1);
});

it('allows an hourly pickup exactly when the previous booking cooldown ends', function () {
    Rental::create([
        'user_id' => $this->user->id,
        'car_id' => $this->car->id,
        'rental_method' => 'hourly',
        'pickup_at' => '2030-06-10 09:00:00',
        'return_at' => '2030-06-10 12:00:00',
        'applied_rate' => 15,
        'duration_units' => 3,
        'total_cost' => 45,
        'status' => 'ongoing',
    ]);

    $response = $this->actingAs($this->user)
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'hourly',
            'pickup_date' => '2030-06-10',
            'pickup_time' => '12:30',
            'return_date' => '2030-06-10',
            'return_time' => '14:30',
        ]);

    $response->assertRedirect(route('bookings.payment', Rental::latest('id')->firstOrFail()));
    expect(Rental::count())->toBe(2);
});

it('blocks a booking when its own cooldown overlaps a later booking', function () {
    Rental::create([
        'user_id' => $this->user->id,
        'car_id' => $this->car->id,
        'rental_method' => 'hourly',
        'pickup_at' => '2030-06-10 15:00:00',
        'return_at' => '2030-06-10 17:00:00',
        'applied_rate' => 15,
        'duration_units' => 2,
        'total_cost' => 30,
        'status' => 'ongoing',
    ]);

    $this->actingAs($this->user)
        ->from('/cars/'.$this->car->id.'/book')
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'hourly',
            'pickup_date' => '2030-06-10',
            'pickup_time' => '12:30',
            'return_date' => '2030-06-10',
            'return_time' => '14:45',
        ])
        ->assertRedirect('/cars/'.$this->car->id.'/book')
        ->assertSessionHas('error');

    expect(Rental::count())->toBe(1);
});

it('blocks a pickup during daily rental cooldown spillover', function () {
    Rental::create([
        'user_id' => $this->user->id,
        'car_id' => $this->car->id,
        'rental_method' => 'daily',
        'pickup_at' => '2030-06-10 10:00:00',
        'return_at' => '2030-06-11 16:00:00',
        'applied_rate' => 120,
        'duration_units' => 1,
        'total_cost' => 120,
        'status' => 'ongoing',
    ]);

    $this->actingAs($this->user)
        ->from('/cars/'.$this->car->id.'/book')
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'hourly',
            'pickup_date' => '2030-06-12',
            'pickup_time' => '00:15',
            'return_date' => '2030-06-12',
            'return_time' => '02:15',
        ])
        ->assertRedirect('/cars/'.$this->car->id.'/book')
        ->assertSessionHas('error');

    expect(Rental::count())->toBe(1);
});

it('allows a pickup exactly when daily rental cooldown spillover ends', function () {
    Rental::create([
        'user_id' => $this->user->id,
        'car_id' => $this->car->id,
        'rental_method' => 'daily',
        'pickup_at' => '2030-06-10 10:00:00',
        'return_at' => '2030-06-11 16:00:00',
        'applied_rate' => 120,
        'duration_units' => 1,
        'total_cost' => 120,
        'status' => 'ongoing',
    ]);

    $response = $this->actingAs($this->user)
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'hourly',
            'pickup_date' => '2030-06-12',
            'pickup_time' => '00:30',
            'return_date' => '2030-06-12',
            'return_time' => '02:30',
        ]);

    $response->assertRedirect(route('bookings.payment', Rental::latest('id')->firstOrFail()));
    expect(Rental::count())->toBe(2);
});

<?php

use App\Models\Car;
use App\Models\CustomerVerification;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->verification = $this->user->verification()->create();

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

it('blocks pending customers from booking on the server', function () {
    $this->actingAs($this->user)
        ->get("/cars/{$this->car->id}/book")
        ->assertRedirect(route('customer.verification.index'))
        ->assertSessionHas('error');

    $this->actingAs($this->user)
        ->post('/rentals', [
            'car_id' => $this->car->id,
            'rental_method' => 'hourly',
            'pickup_date' => '2030-06-10',
            'pickup_time' => '09:00',
            'return_date' => '2030-06-10',
            'return_time' => '12:00',
        ])
        ->assertRedirect(route('customer.verification.index'));

    expect(Rental::count())->toBe(0);
});

it('blocks approved customers when a document has expired', function () {
    $this->verification->update([
        'status' => CustomerVerification::STATUS_APPROVED,
        'id_expiry_date' => now()->subDay()->toDateString(),
        'driving_license_expiry_date' => now()->addYear()->toDateString(),
        'verified_at' => now(),
    ]);

    $this->actingAs($this->user)
        ->get("/cars/{$this->car->id}/book")
        ->assertRedirect(route('customer.verification.index'))
        ->assertSessionHas('error');
});

it('stores replacement documents privately and returns the status to pending', function () {
    Storage::fake('local');

    $this->verification->update([
        'status' => CustomerVerification::STATUS_APPROVED,
        'id_expiry_date' => now()->subDay()->toDateString(),
        'driving_license_expiry_date' => now()->addYear()->toDateString(),
        'verified_at' => now(),
    ]);

    $this->actingAs($this->user)
        ->post(route('customer.verification.store'), [
            'government_id' => UploadedFile::fake()->create('government-id.pdf', 100, 'application/pdf'),
            'driving_license' => UploadedFile::fake()->create('driving-license.pdf', 100, 'application/pdf'),
        ])
        ->assertSessionHas('success');

    $this->verification->refresh();

    expect($this->verification->status)->toBe(CustomerVerification::STATUS_PENDING)
        ->and($this->verification->id_expiry_date)->toBeNull()
        ->and($this->verification->driving_license_expiry_date)->toBeNull()
        ->and($this->verification->verified_at)->toBeNull();

    Storage::disk('local')->assertExists($this->verification->government_id_path);
    Storage::disk('local')->assertExists($this->verification->driving_license_path);
});

it('prevents suspended customers from submitting replacement documents', function () {
    Storage::fake('local');
    $this->verification->update([
        'status' => CustomerVerification::STATUS_SUSPENDED,
    ]);

    $this->actingAs($this->user)
        ->post(route('customer.verification.store'), [
            'government_id' => UploadedFile::fake()->create('government-id.pdf', 100, 'application/pdf'),
            'driving_license' => UploadedFile::fake()->create('driving-license.pdf', 100, 'application/pdf'),
        ])
        ->assertSessionHas('error');

    expect($this->verification->fresh()->government_id_path)->toBeNull();
});

it('prevents customers from replacing documents while admin review is pending', function () {
    Storage::fake('local');
    Storage::disk('local')->put('customer-verifications/1/government-id.pdf', 'original-id');
    Storage::disk('local')->put('customer-verifications/1/driving-license.pdf', 'original-license');

    $this->verification->update([
        'status' => CustomerVerification::STATUS_PENDING,
        'government_id_path' => 'customer-verifications/1/government-id.pdf',
        'driving_license_path' => 'customer-verifications/1/driving-license.pdf',
    ]);

    $this->actingAs($this->user)
        ->post(route('customer.verification.store'), [
            'government_id' => UploadedFile::fake()->create('replacement-id.pdf', 100, 'application/pdf'),
            'driving_license' => UploadedFile::fake()->create('replacement-license.pdf', 100, 'application/pdf'),
        ])
        ->assertSessionHas('error');

    $this->verification->refresh();

    expect($this->verification->government_id_path)->toBe('customer-verifications/1/government-id.pdf')
        ->and($this->verification->driving_license_path)->toBe('customer-verifications/1/driving-license.pdf');
});

it('allows only the owner and admins to view private documents', function () {
    Storage::fake('local');
    Storage::disk('local')->put('customer-verifications/1/government-id.pdf', 'document');

    $this->verification->update([
        'government_id_path' => 'customer-verifications/1/government-id.pdf',
    ]);

    $otherCustomer = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $url = route('customer.verification.document', [
        $this->verification,
        'government-id',
    ]);

    $this->actingAs($this->user)->get($url)->assertOk();
    $this->actingAs($admin)->get($url)->assertOk();
    $this->actingAs($otherCustomer)->get($url)->assertForbidden();
});

it('creates a pending verification record during customer registration', function () {
    $this->post('/register', [
        'name' => 'New Customer',
        'email' => 'new-customer@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $customer = User::where('email', 'new-customer@example.com')->firstOrFail();

    expect($customer->verification->status)->toBe(CustomerVerification::STATUS_PENDING);
});

it('allows admins to approve and request replacement documents', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->verification->update([
        'government_id_path' => 'customer-verifications/1/government-id.pdf',
        'driving_license_path' => 'customer-verifications/1/driving-license.pdf',
    ]);

    $this->actingAs($admin)
        ->post(route('verifications.approve', $this->user), [
            'id_expiry_date' => now()->addYear()->toDateString(),
            'driving_license_expiry_date' => now()->addYear()->toDateString(),
        ])
        ->assertSessionHas('success');

    expect($this->verification->fresh()->can_book)->toBeTrue();

    $this->actingAs($admin)
        ->post(route('verifications.status', $this->user), [
            'action' => 'request_documents',
            'review_note' => 'Please submit updated documents.',
        ])
        ->assertSessionHas('success');

    $this->verification->refresh();

    expect($this->verification->status)->toBe(CustomerVerification::STATUS_PENDING)
        ->and($this->verification->review_note)->toBe('Please submit updated documents.')
        ->and($this->verification->can_book)->toBeFalse();
});

it('requires admins to explicitly lift a suspension before review continues', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->verification->update([
        'status' => CustomerVerification::STATUS_SUSPENDED,
        'government_id_path' => 'customer-verifications/1/government-id.pdf',
        'driving_license_path' => 'customer-verifications/1/driving-license.pdf',
    ]);

    $this->actingAs($admin)
        ->post(route('verifications.approve', $this->user), [
            'id_expiry_date' => now()->addYear()->toDateString(),
            'driving_license_expiry_date' => now()->addYear()->toDateString(),
        ])
        ->assertSessionHasErrors('status');

    $this->actingAs($admin)
        ->post(route('verifications.status', $this->user), [
            'action' => 'lift_suspension',
        ])
        ->assertSessionHas('success');

    expect($this->verification->fresh()->status)->toBe(CustomerVerification::STATUS_PENDING);
});

it('renders the customer and admin verification pages', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($this->user)
        ->get(route('customer.verification.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Frontend/VerificationPage')
            ->has('verification'));

    $this->actingAs($admin)
        ->get(route('verifications.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/VerificationsPage')
            ->has('customers'));
});

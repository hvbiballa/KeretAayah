<?php

namespace App\Models;

use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\Auth\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    function isAdmin()
    {
        return $this->getAttribute('role') == 'admin';
    }

    function isCustomer()
    {
        return $this->getAttribute('role') == 'customer';
    }

    function isStaff()
    {
        return $this->getAttribute('role') == 'staff';
    }

    function canReviewPayments(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    function verification()
    {
        return $this->hasOne(CustomerVerification::class);
    }

    function receivedPayments()
    {
        return $this->hasMany(Payment::class, 'received_by');
    }

    function canCreateBookings(): bool
    {
        return $this->isCustomer() && (bool) $this->verification?->can_book;
    }

    function bookingRestrictionMessage(): string
    {
        return $this->verification?->booking_restriction_message
            ?? __('verification.restriction.submit_documents');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

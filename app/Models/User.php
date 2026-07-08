<?php

namespace App\Models;

use App\Models\Concerns\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRolesAndPermissions, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'picture_profile_url',
        'social_provider',
        'enable_core_notification',
        'enable_commercial_notification',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function managedVenues(): BelongsToMany
    {
        return $this->belongsToMany(Venue::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function joinedBookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_guests', 'user_id', 'booking_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps();
    }

    public function notificationTokens(): HasMany
    {
        return $this->hasMany(UserNotificationToken::class);
    }
}

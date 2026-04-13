<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Required for Flutter Auth

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Role Constants for easy reference throughout the app
     */
    const ROLE_CUSTOMER = 'customer';
    const ROLE_HOTEL = 'hotel';
    const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',     // Added for Delivery API
        'role',      // Added for Delivery API
        'is_active', // Added for Delivery API
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'is_active' => 'boolean',
        ];
    }

    // --- Relationships ---

    /**
     * Link to the Customer Profile
     */
    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    /**
     * Link to the Hotel Profile
     */
    public function hotelProfile()
    {
        return $this->hasOne(HotelProfile::class);
    }

    /**
     * Link to the Admin Profile
     */
    public function adminProfile()
    {
        return $this->hasOne(AdminProfile::class);
    }

    // --- Helpers ---

    /**
     * Check if the user is a hotel
     */
    public function isHotel(): bool
    {
        return $this->role === self::ROLE_HOTEL;
    }

    /**
     * Check if the user is a customer
     */
    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /**
     * Check if the user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
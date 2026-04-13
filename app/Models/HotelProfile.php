<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelProfile extends Model
{
    protected $fillable = [
        'user_id',
        'hotel_name',
        'description',
        'address',
        'lat',
        'long',
        'opens_at',
        'closes_at',
        'is_available'
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
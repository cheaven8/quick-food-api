<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'hotel_id',
        'status',
        'subtotal',
        'delivery_fee',
        'total',
        'delivery_address',
        'customer_phone',
        'special_instructions',
        'delivered_at',
    ];

    // 👤 Customer (User)
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // 🏨 Hotel (HotelProfile)
    public function hotel()
    {
        return $this->belongsTo(HotelProfile::class, 'hotel_id');
    }

    // 🍲 Items in order
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // 🔥 Scopes (optional but useful)
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }
}
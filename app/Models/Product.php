<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'hotel_id',
        'name',
        'description',
        'price',
        'category',
        'preparation_time',
        'image',
        'is_available',
        'is_featured',
        'ingredients',
        'calories',
    ];

    protected $casts = [
        'price' => 'float',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'calories' => 'integer',
        'preparation_time' => 'integer',
    ];



    public function hotel()
    {
        return $this->belongsTo(HotelProfile::class, 'hotel_id');
    }

   

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
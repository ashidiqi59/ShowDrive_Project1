<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'vin',
        'year',
        'price',
        'status',
        'engine',
        'transmission',
        'color',
        'image',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}

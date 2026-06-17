<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'customer_name',
        'phone',
        'car_id',
        'date',
        'status',
        'payment_status',
        'payment_type',
        'paid_amount',
        'payment_proof',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}

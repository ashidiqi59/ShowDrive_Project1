<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tax_id', 'address', 'phone', 'bank_name', 'bank_account', 'bank_account_holder', 'qris_image'];

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function cashiers(): HasMany
    {
        return $this->hasMany(Cashier::class);
    }
}

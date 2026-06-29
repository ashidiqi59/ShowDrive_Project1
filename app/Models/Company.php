<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tax_id'];

    // Relasi One-to-Many: Satu perusahaan punya banyak gudang
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    // Relasi One-to-Many: Satu perusahaan punya banyak kasir/admin
    public function cashiers()
    {
        return $this->hasMany(Cashier::class);
    }
}

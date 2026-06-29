<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name', 'location'];

    // Relasi Belongs-To: Gudang ini milik satu perusahaan
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Relasi One-to-Many: Gudang ini menyimpan banyak mobil (items)
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}

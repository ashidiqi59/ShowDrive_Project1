<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id', 
        'brand', 
        'model', 
        'vin', 
        'year', 
        'price', 
        'status', 
        'engine', 
        'transmission', 
        'color', 
        'image_url'
    ];

    // Accessor untuk properti 'image' agar merujuk ke 'image_url' di database
    public function getImageAttribute()
    {
        if (!$this->image_url) {
            return 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=600&q=80';
        }
        if (filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            return $this->image_url;
        }
        return asset('storage/' . $this->image_url);
    }

    // Accessor untuk memetakan status 'Invoiced' di database ke 'Booked' di UI
    public function getStatusAttribute($value)
    {
        if ($value === 'Invoiced') {
            return 'Booked';
        }
        return $value;
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Relasi One-to-Many ke ItemImage (Galeri Foto Pendukung)
    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    // Relasi One-to-Many: Satu mobil bisa memiliki riwayat invoice (walau biasanya 1 mobil = 1 invoice lunas)
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}

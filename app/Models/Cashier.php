<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Penting untuk fitur Login
use Illuminate\Notifications\Notifiable;

class Cashier extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'company_id', 
        'name', 
        'username', 
        'password', 
        'role'
    ];

    protected $hidden = [
        'password', // Sembunyikan password saat data dipanggil
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Relasi One-to-Many: Satu kasir bisa mengesahkan banyak invoice
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}

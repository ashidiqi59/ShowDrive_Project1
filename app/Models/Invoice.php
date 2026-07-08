<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_code',
        'customer_id',
        'item_id',
        'cashier_id',
        'date',
        'total_amount',
        'paid_amount',
        'payment_type',
        'payment_status',
        'status',
        'authentic_receipt'
    ];

    /**
     * Cast atribut ke tipe data yang tepat.
     * Memastikan type safety dan konsistensi saat data diakses dari model.
     */
    protected $casts = [
        'date'         => 'date',          // Otomatis menjadi Carbon instance
        'total_amount' => 'decimal:2',     // Selalu 2 angka desimal
        'paid_amount'  => 'decimal:2',     // Selalu 2 angka desimal
    ];

    // Relasi Belongs-To ke 3 Entitas Sekaligus
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function cashier()
    {
        return $this->belongsTo(Cashier::class);
    }

    // Alias relasi 'item' ke 'car' agar kompatibel dengan view Blade
    public function car()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    // Accessor untuk nama pelanggan
    public function getCustomerNameAttribute()
    {
        return $this->customer->name ?? '';
    }

    // Accessor untuk nomor telepon pelanggan
    public function getPhoneAttribute()
    {
        return $this->customer->phone ?? '';
    }

    // Accessor untuk bukti pembayaran
    public function getPaymentProofAttribute()
    {
        return $this->authentic_receipt;
    }
}

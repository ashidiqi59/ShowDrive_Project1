<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_code',
        'customer_id',
        'item_id',
        'cashier_id',
        'date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'payment_type',
        'payment_status',
        'status',
        'authentic_receipt',
        'rejection_note',
        'cancellation_note',
        'handed_over_at',
    ];

    protected $casts = [
        'date'           => 'date',
        'subtotal'       => 'decimal:2',
        'tax_rate'       => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'total_amount'   => 'decimal:2',
        'paid_amount'    => 'decimal:2',
        'handed_over_at' => 'datetime',
    ];

    // ---- Query Scopes ----

    /**
     * Scope: invoice yang masih bisa dibatalkan oleh pelanggan.
     * Hanya boleh cancel jika belum ada pembayaran masuk sama sekali.
     */
    public function scopeCancellable($query)
    {
        return $query->where('payment_status', 'Unpaid')
                     ->where('status', 'Pending');
    }

    // ---- Static Helpers ----

    /**
     * Generate a sequential, collision-safe invoice code.
     * Format: SD/YYYY/NNNN  (e.g. SD/2026/0001)
     *
     * Must be called INSIDE a DB transaction with a preceding lockForUpdate()
     * on the parent row to guarantee uniqueness under concurrent requests.
     */
    public static function generateCode(): string
{
    $year   = now()->format('Y');
    $prefix = 'SD/' . $year . '/';

    // lockForUpdate() memastikan tidak ada dua request konkuren
    // yang mendapatkan sequence number yang sama sebelum salah satu commit
    $last = self::where('invoice_code', 'like', $prefix . '%')
        ->lockForUpdate()
        ->orderByDesc('id')
        ->value('invoice_code');

    $nextSeq = $last
        ? ((int) substr($last, strlen($prefix))) + 1
        : 1;

    return $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
}


    // ---- Relationships ----

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(Cashier::class);
    }
}

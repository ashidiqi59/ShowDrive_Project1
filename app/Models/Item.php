<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'dp_percentage',
        'status',
        'engine',
        'transmission',
        'color',
        'image_url',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'year'          => 'integer',
        'dp_percentage' => 'integer',
    ];

    // ---- Accessors ----

    /**
     * Resolve the primary display image, falling back to a default placeholder.
     * Does NOT rewrite the 'status' value — use getRawOriginal('status') for DB comparisons.
     */
    public function getImageAttribute(): string
    {
        if (! $this->image_url) {
            return 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=600&q=80';
        }

        if (filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            return $this->image_url;
        }

        return asset('storage/' . $this->image_url);
    }

    /**
     * Map internal 'Invoiced' DB enum to 'Booked' for UI display.
     * IMPORTANT: always use getRawOriginal('status') when making DB-level status checks.
     */
    public function getStatusAttribute(string $value): string
    {
        return $value === 'Invoiced' ? 'Booked' : $value;
    }

    // ---- Query Scopes ----

    /** Scope to filter only units currently available for booking. */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'Available');
    }

    // ---- Relationships ----

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ItemImage::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}

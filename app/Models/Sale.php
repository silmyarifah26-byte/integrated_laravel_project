<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'customer_name',
        'sale_number',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'notes',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'sold_at' => 'datetime',
        ];
    }

    /**
     * Staff/cashier who created the sale transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Customer user account (if registered).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Sale detail items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Alias for items relation.
     */
    public function saleItems(): HasMany
    {
        return $this->items();
    }
}

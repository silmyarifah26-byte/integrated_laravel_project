<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'user_id',
        'purchase_number',
        'total',
        'status',
        'notes',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'purchased_at' => 'datetime',
        ];
    }

    /**
     * Supplier from whom products were purchased.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * User / staff who recorded the purchase.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Detail items in this purchase.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Alias for items relation.
     */
    public function purchaseItems(): HasMany
    {
        return $this->items();
    }
}

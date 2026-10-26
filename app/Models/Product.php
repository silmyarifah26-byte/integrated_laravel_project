<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'size',
        'material',
        'image_url',
        'purchase_price',
        'selling_price',
        'stock',
        'minimum_stock',
    ];

    protected $appends = [
        'image_asset_url',
    ];

    /**
     * Get the verified asset URL for the product image.
     */
    public function getImageAssetUrlAttribute(): string
    {
        $path = $this->image_url ? ltrim($this->image_url, '/') : 'images/plushies/teddy-bear-pita.jpg';
        return asset($path);
    }

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock' => 'integer',
            'minimum_stock' => 'integer',
        ];
    }

    /**
     * Category that this product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Purchase item records for this product.
     */
    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Sale item records for this product.
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}

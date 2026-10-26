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
        'image_path',
    ];

    /**
     * The "booted" method of the model.
     * Automatically assigns an aesthetic placeholder image if none is provided.
     */
    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->image_url)) {
                $product->image_url = $product->getDefaultPlaceholderImage();
            }
        });
    }

    /**
     * Determine an aesthetic dummy/placeholder image based on product category or name.
     */
    public function getDefaultPlaceholderImage(): string
    {
        $categoryName = strtolower($this->category->name ?? '');
        $productName = strtolower($this->name ?? '');

        $filename = 'pashmina-ceruty';

        // 1. Pashmina Series
        if (str_contains($categoryName, 'pashmina') || str_contains($productName, 'pashmina')) {
            if (str_contains($productName, 'silk') || str_contains($productName, 'cradenza') || str_contains($productName, 'shimmer')) {
                $filename = 'pashmina-silk';
            } elseif (str_contains($productName, 'rayon') || str_contains($productName, 'kaos')) {
                $filename = 'pashmina-rayon';
            } else {
                $filename = 'pashmina-ceruty';
            }
        }
        // 2. Segi Empat (Square) Series
        elseif (str_contains($categoryName, 'segi empat') || str_contains($categoryName, 'square') || 
            str_contains($productName, 'segi empat') || str_contains($productName, 'square') || str_contains($productName, 'voal')) {
            if (str_contains($productName, 'paris')) {
                $filename = 'segiempat-paris';
            } elseif (str_contains($productName, 'motif') || str_contains($productName, 'printing')) {
                $filename = 'segiempat-motif';
            } else {
                $filename = 'segiempat-voal';
            }
        }
        // 3. Bergo & Hijab Instan
        elseif (str_contains($categoryName, 'bergo') || str_contains($categoryName, 'instan') || 
            str_contains($productName, 'bergo') || str_contains($productName, 'sport')) {
            if (str_contains($productName, 'sport') || str_contains($productName, 'aerobic') || str_contains($productName, 'dry-fit')) {
                $filename = 'bergo-sport';
            } else {
                $filename = 'bergo-hamidah';
            }
        }
        // 4. Khimar & Syar'i Series
        elseif (str_contains($categoryName, 'khimar') || str_contains($categoryName, 'syar') || 
            str_contains($productName, 'khimar') || str_contains($productName, 'french')) {
            $filename = 'khimar-french';
        }
        // 5. Inner & Ciput
        elseif (str_contains($categoryName, 'inner') || str_contains($categoryName, 'aksesoris') || 
            str_contains($productName, 'ciput') || str_contains($productName, 'inner')) {
            $filename = 'inner-ciput';
        }

        // Return real JPG boutique photo if available, fallback to SVG
        if (file_exists(public_path("images/hijab/{$filename}.jpg"))) {
            return "images/hijab/{$filename}.jpg";
        }

        return "images/hijab/{$filename}.svg";
    }

    /**
     * Get the relative image path (clean of leading slashes) with automatic fallback.
     */
    public function getImagePathAttribute(): string
    {
        if (!empty($this->image_url)) {
            $cleaned = ltrim($this->image_url, '/');
            if (file_exists(public_path($cleaned))) {
                return $cleaned;
            }
            // Check if substituting extension (.svg to .jpg) exists
            $altJpg = preg_replace('/\.svg$/i', '.jpg', $cleaned);
            if (file_exists(public_path($altJpg))) {
                return $altJpg;
            }
        }

        return $this->getDefaultPlaceholderImage();
    }

    /**
     * Get the verified asset URL for the product image rendered with asset() helper.
     */
    public function getImageAssetUrlAttribute(): string
    {
        return asset($this->image_path);
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

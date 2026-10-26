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
        'available_colors',
        'available_sizes',
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

    /**
     * Get available color variations for the hijab item.
     */
    public function getAvailableColorsAttribute(): array
    {
        $name = strtolower($this->name ?? '');
        $cat = strtolower($this->category->name ?? '');

        if (str_contains($name, 'silk') || str_contains($name, 'cradenza') || str_contains($name, 'shimmer')) {
            return ['Champagne Gold', 'Dusty Mauve', 'Rose Gold', 'Silver Grey', 'Emerald Lux'];
        }

        if (str_contains($name, 'rayon') || str_contains($name, 'kaos')) {
            return ['Soft Beige', 'Charcoal Grey', 'Walnut Brown', 'Nude Sand', 'Denim Blue'];
        }

        if (str_contains($name, 'paris')) {
            return ['Lavender', 'Vanilla Cream', 'Taupe Brown', 'Soft Rose', 'Hitam'];
        }

        if (str_contains($name, 'motif') || str_contains($name, 'printing')) {
            return ['Garden Rose', 'Pastel Blossom', 'Turkish Floral', 'Emerald Elegance'];
        }

        if (str_contains($name, 'sport') || str_contains($cat, 'sport')) {
            return ['Hitam Jetblack', 'Abu Misty', 'Navy Sport', 'Marun Red'];
        }

        if (str_contains($name, 'bergo') || str_contains($cat, 'bergo')) {
            return ['Coffee Brown', 'Caramel', 'Khaki', 'Denim Blue', 'Dusty Mauve'];
        }

        if (str_contains($name, 'french') || str_contains($name, 'khimar') || str_contains($cat, 'khimar')) {
            return ['Jetblack Syar\'i', 'Dark Emerald', 'Deep Plum', 'Dusty Rose'];
        }

        if (str_contains($name, 'ciput') || str_contains($name, 'inner') || str_contains($cat, 'inner')) {
            return ['Nude Cream', 'Hitam Jetblack', 'Broken White', 'Abu-Abu'];
        }

        if (str_contains($name, 'voal') || str_contains($cat, 'segi empat')) {
            return ['Sage Green', 'Dusty Pink', 'Terracotta', 'Midnight Navy', 'Warm Mustard'];
        }

        // Pashmina Ceruty / Default Hijab
        return ['Rose Dust', 'Mocca', 'Sage Green', 'Broken White', 'Hitam Jetblack', 'Dusty Lavender'];
    }

    /**
     * Get available size variations for the hijab item.
     */
    public function getAvailableSizesAttribute(): array
    {
        $name = strtolower($this->name ?? '');
        $cat = strtolower($this->category->name ?? '');
        $currentSize = $this->size ? trim($this->size) : null;

        if (str_contains($name, 'bergo') || str_contains($cat, 'bergo')) {
            return ['Size M (Standar)', 'Size L (Menutup Dada)', 'Size XL (Syar\'i)'];
        }

        if (str_contains($name, 'khimar') || str_contains($cat, 'khimar')) {
            return ['Size L (Depan 100cm / Belakang 120cm)', 'Size XL Syar\'i (Depan 115cm / Belakang 135cm)'];
        }

        if (str_contains($name, 'ciput') || str_contains($name, 'inner') || str_contains($cat, 'inner')) {
            return ['All Size Elastis'];
        }

        if (str_contains($name, 'pashmina') || str_contains($cat, 'pashmina')) {
            if ($currentSize) {
                return ["Standar ({$currentSize})", 'Jumbo (200 x 75 cm)'];
            }
            return ['Standar (175 x 75 cm)', 'Jumbo (200 x 75 cm)'];
        }

        if (str_contains($name, 'segi empat') || str_contains($cat, 'segi empat') || str_contains($name, 'voal') || str_contains($name, 'paris')) {
            if ($currentSize) {
                return ["Standar ({$currentSize})", 'Syar\'i (130 x 130 cm)'];
            }
            return ['Standar (115 x 115 cm)', 'Syar\'i (130 x 130 cm)'];
        }

        if ($currentSize) {
            return [$currentSize];
        }

        return ['All Size Standar'];
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

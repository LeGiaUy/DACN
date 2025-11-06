<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'img_url',
        'category_id',
        'brand_id',
        'colors',
        'sizes',
        'quantity',
        'is_featured',
    ];
    

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    protected $casts = [
        'sizes' => 'array', // Tự động convert JSON thành array và ngược lại
        'colors' => 'array', // Tự động convert JSON thành array và ngược lại
        'quantity' => 'integer',
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'total_quantity',
        'stock_status',
    ];

    /**
     * Get sizes as array (helper method)
     */
    public function getSizesArrayAttribute()
    {
        return $this->sizes ?? [];
    }

    /**
     * Set sizes from array (helper method)
     */
    public function setSizesArrayAttribute($value)
    {
        $this->sizes = $value;
    }

    /**
     * Get colors as array (helper method)
     */
    public function getColorsArrayAttribute()
    {
        return $this->colors ?? [];
    }

    /**
     * Set colors from array (helper method)
     */
    public function setColorsArrayAttribute($value)
    {
        $this->colors = $value;
    }

    /**
     * Safely check if variants table exists and has data
     */
    protected function hasVariants()
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->isNotEmpty();
        }
        
        try {
            return Schema::hasTable('product_variants') 
                && $this->variants()->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if product is in stock
     */
    public function isInStock()
    {
        if ($this->hasVariants()) {
            try {
                return (int) $this->variants()->sum('quantity') > 0;
            } catch (\Exception $e) {
                return (int) $this->quantity > 0;
            }
        }
        return (int) $this->quantity > 0;
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute()
    {
        $total = 0;
        if ($this->hasVariants()) {
            try {
                $total = (int) $this->variants()->sum('quantity');
            } catch (\Exception $e) {
                $total = (int) $this->quantity;
            }
        } else {
            $total = (int) $this->quantity;
        }

        if ($total > 10) {
            return 'Còn hàng';
        } elseif ($total > 0) {
            return 'Sắp hết hàng';
        }
        return 'Hết hàng';
    }

    public function getTotalQuantityAttribute()
    {
        if ($this->hasVariants()) {
            try {
                return (int) $this->variants()->sum('quantity');
            } catch (\Exception $e) {
                return (int) $this->quantity;
            }
        }
        return (int) $this->quantity;
    }

}

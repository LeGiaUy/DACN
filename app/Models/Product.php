<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    protected $casts = [
        'sizes' => 'array', // Tự động convert JSON thành array và ngược lại
        'colors' => 'array', // Tự động convert JSON thành array và ngược lại
        'quantity' => 'integer',
        'is_featured' => 'boolean',
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
     * Check if product is in stock
     */
    public function isInStock()
    {
        return $this->quantity > 0;
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute()
    {
        if ($this->quantity > 10) {
            return 'Còn hàng';
        } elseif ($this->quantity > 0) {
            return 'Sắp hết hàng';
        } else {
            return 'Hết hàng';
        }
    }

}

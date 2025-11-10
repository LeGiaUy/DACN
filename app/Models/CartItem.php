<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'color',
        'size',
        'quantity',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    protected $appends = [
        'subtotal',
        'actual_price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the actual price (from cart item or product)
     */
    public function getActualPriceAttribute()
    {
        // If price is 0 or null, get from product
        if (!$this->price || $this->price == 0) {
            $this->loadMissing('product');
            if ($this->product && $this->product->price) {
                return $this->product->price;
            }
            return 0;
        }
        return $this->price;
    }

    /**
     * Get subtotal for this cart item
     */
    public function getSubtotalAttribute()
    {
        $price = $this->actual_price;
        return $price * $this->quantity;
    }
}


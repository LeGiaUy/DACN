<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_email',
        'shipping_method',
        'notes',
        'subtotal',
        'shipping_fee',
        'total',
        'payment_transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayName(): string
    {
        return match($this->status) {
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đã gửi hàng',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy',
            default => 'Không xác định'
        };
    }

    /**
     * Get payment status display name
     */
    public function getPaymentStatusDisplayName(): string
    {
        return match($this->payment_status) {
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'refunded' => 'Đã hoàn tiền',
            default => 'Không xác định'
        };
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodDisplayName(): string
    {
        return match($this->payment_method) {
            'cod' => 'Thanh toán khi nhận hàng',
            'bank_card' => 'Thẻ ngân hàng',
            'qr_code' => 'QR Code',
            default => 'Không xác định'
        };
    }

    /**
     * Get shipping method display name
     */
    public function getShippingMethodDisplayName(): string
    {
        return match($this->shipping_method ?? 'standard') {
            'standard' => 'Giao hàng tiết kiệm',
            'fast' => 'Giao hàng nhanh',
            'express' => 'Giao hàng hỏa tốc',
            default => 'Không xác định'
        };
    }
}

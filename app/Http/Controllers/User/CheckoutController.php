<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Calculate shipping fee based on method
     */
    private function calculateShippingFee($shippingMethod, $subtotal)
    {
        return match($shippingMethod) {
            'standard' => 20000, // 20,000 VND - Tiết kiệm
            'fast' => 35000,     // 35,000 VND - Nhanh
            'express' => 50000,   // 50,000 VND - Hỏa tốc
            default => 20000
        };
    }

    /**
     * Show checkout page
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $cartItems = CartItem::with(['product.category', 'product.brand'])
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.cart')->with('error', 'Giỏ hàng của bạn đang trống');
        }

        // Fix prices for items with 0 or null price
        $needsRefresh = false;
        foreach ($cartItems as $item) {
            if (!$item->price || $item->price == 0) {
                if ($item->product && $item->product->price) {
                    $item->price = $item->product->price;
                    $item->save();
                    $needsRefresh = true;
                }
            }
        }

        // Refresh collection if prices were updated
        if ($needsRefresh) {
            $cartItems = CartItem::with(['product.category', 'product.brand'])
                ->where('user_id', $user->id)
                ->get();
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->subtotal;
        });

        // Default shipping method
        $defaultShippingMethod = 'standard';
        $shippingFee = $this->calculateShippingFee($defaultShippingMethod, $subtotal);
        $total = $subtotal + $shippingFee;

        return Inertia::render('User/Checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
            'total' => $total,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
            ],
        ]);
    }

    /**
     * Process checkout and create order
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_email' => 'nullable|email|max:255',
            'payment_method' => 'required|in:cod,bank_card,qr_code',
            'shipping_method' => 'required|in:standard,fast,express',
            'notes' => 'nullable|string',
        ]);

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.cart')->with('error', 'Giỏ hàng của bạn đang trống');
        }

        // Fix prices for items with 0 or null price
        $needsRefresh = false;
        foreach ($cartItems as $item) {
            if (!$item->price || $item->price == 0) {
                if ($item->product && $item->product->price) {
                    $item->price = $item->product->price;
                    $item->save();
                    $needsRefresh = true;
                }
            }
        }

        // Refresh collection if prices were updated
        if ($needsRefresh) {
            $cartItems = CartItem::with('product')
                ->where('user_id', $user->id)
                ->get();
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->subtotal;
        });

        // Calculate shipping fee based on selected method
        $shippingFee = $this->calculateShippingFee($validated['shipping_method'], $subtotal);
        $total = $subtotal + $shippingFee;

        // For COD, payment status is pending (will be paid on delivery)
        // For online payment, payment status is pending until payment is processed
        $paymentStatus = $validated['payment_method'] === 'cod' ? 'pending' : 'pending';

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'shipping_name' => $validated['shipping_name'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_email' => $validated['shipping_email'] ?? $user->email,
                'shipping_method' => $validated['shipping_method'],
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $total,
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'color' => $cartItem->color,
                    'size' => $cartItem->size,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->subtotal,
                ]);
            }

            DB::commit();

            // If COD, redirect to success page (no online payment needed)
            if ($validated['payment_method'] === 'cod') {
                // Clear cart after creating order
                CartItem::where('user_id', $user->id)->delete();
                return redirect()->route('user.checkout.success', $order->id)
                    ->with('success', 'Đơn hàng đã được tạo thành công. Bạn sẽ thanh toán khi nhận hàng.');
            }

            // Redirect to payment page for online payment
            return redirect()->route('user.checkout.payment', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Show payment page (fake Vietcombank)
     */
    public function payment($orderId)
    {
        $user = Auth::user();
        
        $order = Order::with('items.product')
            ->where('id', $orderId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // If COD, redirect to success
        if ($order->payment_method === 'cod') {
            return redirect()->route('user.checkout.success', $order->id);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('user.checkout.success', $order->id);
        }

        return Inertia::render('User/Payment', [
            'order' => $order,
        ]);
    }

    /**
     * Process payment (fake)
     */
    public function processPayment(Request $request, $orderId)
    {
        $user = Auth::user();
        
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return redirect()->route('user.checkout.success', $order->id);
        }

        // Validate based on payment type
        $paymentType = $request->input('payment_type', $order->payment_method);
        
        if ($paymentType === 'bank_card') {
            $validated = $request->validate([
                'card_number' => 'required|string|min:16|max:19',
                'card_name' => 'required|string|max:255',
                'expiry_month' => 'required|integer|between:1,12',
                'expiry_year' => 'required|integer|min:' . date('Y'),
                'cvv' => 'required|string|min:3|max:4',
            ]);
        } else {
            // For QR Code, no validation needed
            $validated = [];
        }

        // Simulate payment processing delay
        sleep(2);

        // Simulate payment success (90% success rate for demo)
        $paymentSuccess = rand(1, 10) <= 9;

        try {
            DB::beginTransaction();

            if ($paymentSuccess) {
                // Generate transaction ID based on payment type
                $transactionPrefix = match($paymentType) {
                    'qr_code' => 'QR-',
                    'bank_card' => 'CARD-',
                    default => 'VCB-'
                };
                
                $order->update([
                    'payment_status' => 'paid',
                    'payment_transaction_id' => $transactionPrefix . time() . '-' . strtoupper(substr(uniqid(), -8)),
                    'paid_at' => now(),
                ]);

                // Clear cart after successful payment
                CartItem::where('user_id', $user->id)->delete();

                DB::commit();

                return redirect()->route('user.checkout.success', $order->id);
            } else {
                $order->update([
                    'payment_status' => 'failed',
                ]);

                DB::commit();

                return redirect()->route('user.checkout.failed', $order->id)
                    ->with('error', 'Thanh toán thất bại. Vui lòng thử lại.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Show payment success page
     */
    public function success($orderId)
    {
        $user = Auth::user();
        
        $order = Order::with('items.product')
            ->where('id', $orderId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return Inertia::render('User/PaymentSuccess', [
            'order' => $order,
        ]);
    }

    /**
     * Show payment failed page
     */
    public function failed($orderId)
    {
        $user = Auth::user();
        
        $order = Order::with('items.product')
            ->where('id', $orderId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return Inertia::render('User/PaymentFailed', [
            'order' => $order,
        ]);
    }
}

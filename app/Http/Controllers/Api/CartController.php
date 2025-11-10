<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CartController extends Controller
{
    /**
     * Get user's cart items
     */
    public function index()
    {
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $cartItems = CartItem::with(['product.category', 'product.brand'])
            ->where('user_id', $user->id)
            ->get();

        // Fix prices for items with 0 or null price
        foreach ($cartItems as $item) {
            if (!$item->price || $item->price == 0) {
                if ($item->product && $item->product->price) {
                    $item->price = $item->product->price;
                    $item->save();
                }
            }
        }

        $total = $cartItems->sum(function ($item) {
            return $item->subtotal;
        });

        return response()->json([
            'items' => $cartItems,
            'total' => $total,
            'count' => $cartItems->sum('quantity'),
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color' => 'nullable|string|max:50',
            'size' => 'nullable|string|max:20',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Validate stock availability
        $requestedQuantity = (int) $request->quantity;
        $color = $request->input('color');
        $size = $request->input('size');

        // Check variant stock if variants exist
        if (Schema::hasTable('product_variants') && $product->variants()->exists()) {
            $variant = $product->variants()
                ->where('color', $color ?? null)
                ->where('size', $size ?? null)
                ->first();

            if (!$variant) {
                return response()->json([
                    'message' => 'Biến thể sản phẩm không tồn tại'
                ], 404);
            }

            if ($variant->quantity < $requestedQuantity) {
                return response()->json([
                    'message' => 'Số lượng sản phẩm không đủ. Còn lại: ' . $variant->quantity
                ], 400);
            }
        } else {
            // Check product stock
            $availableQuantity = (int) $product->quantity;
            if ($availableQuantity < $requestedQuantity) {
                return response()->json([
                    'message' => 'Số lượng sản phẩm không đủ. Còn lại: ' . $availableQuantity
                ], 400);
            }
        }

        // Check if item already exists in cart
        $existingItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('color', $color ?? null)
            ->where('size', $size ?? null)
            ->first();

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->quantity + $requestedQuantity;
            
            // Re-validate stock
            if (Schema::hasTable('product_variants') && $product->variants()->exists()) {
                $variant = $product->variants()
                    ->where('color', $color ?? null)
                    ->where('size', $size ?? null)
                    ->first();
                if ($variant && $variant->quantity < $newQuantity) {
                    return response()->json([
                        'message' => 'Số lượng sản phẩm không đủ. Còn lại: ' . $variant->quantity
                    ], 400);
                }
            } else {
                if ($product->quantity < $newQuantity) {
                    return response()->json([
                        'message' => 'Số lượng sản phẩm không đủ. Còn lại: ' . $product->quantity
                    ], 400);
                }
            }

            // Update price if it's 0 or null (fix for existing items with wrong price)
            if (!$existingItem->price || $existingItem->price == 0) {
                $existingItem->price = $product->price;
            }

            $existingItem->quantity = $newQuantity;
            $existingItem->save();

            return response()->json([
                'message' => 'Đã cập nhật giỏ hàng',
                'item' => $existingItem->load(['product.category', 'product.brand'])
            ], 200);
        }

        // Get product price - ensure it's not null or 0
        $productPrice = $product->price ?? 0;
        if ($productPrice <= 0) {
            return response()->json([
                'message' => 'Sản phẩm không có giá. Vui lòng liên hệ quản trị viên.'
            ], 400);
        }

        // Create new cart item
        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'color' => $color,
            'size' => $size,
            'quantity' => $requestedQuantity,
            'price' => $productPrice,
        ]);

        return response()->json([
            'message' => 'Đã thêm vào giỏ hàng',
            'item' => $cartItem->load(['product.category', 'product.brand'])
        ], 201);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $newQuantity = (int) $request->quantity;
        $product = $cartItem->product;

        // Validate stock
        if (Schema::hasTable('product_variants') && $product->variants()->exists()) {
            $variant = $product->variants()
                ->where('color', $cartItem->color ?? null)
                ->where('size', $cartItem->size ?? null)
                ->first();

            if ($variant && $variant->quantity < $newQuantity) {
                return response()->json([
                    'message' => 'Số lượng sản phẩm không đủ. Còn lại: ' . $variant->quantity
                ], 400);
            }
        } else {
            if ($product->quantity < $newQuantity) {
                return response()->json([
                    'message' => 'Số lượng sản phẩm không đủ. Còn lại: ' . $product->quantity
                ], 400);
            }
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        return response()->json([
            'message' => 'Đã cập nhật số lượng',
            'item' => $cartItem->load(['product.category', 'product.brand'])
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy($id)
    {
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $cartItem = CartItem::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'message' => 'Đã xóa khỏi giỏ hàng'
        ]);
    }

    /**
     * Clear all cart items
     */
    public function clear()
    {
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        CartItem::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Đã xóa tất cả sản phẩm khỏi giỏ hàng'
        ]);
    }
}


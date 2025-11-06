<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the cart page
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

        $total = $cartItems->sum(function ($item) {
            return $item->subtotal;
        });

        $count = $cartItems->sum('quantity');

        return Inertia::render('User/Cart', [
            'cartItems' => $cartItems,
            'total' => $total,
            'count' => $count,
        ]);
    }
}


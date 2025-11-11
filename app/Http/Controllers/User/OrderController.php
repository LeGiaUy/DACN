<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * List authenticated user's orders
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Order::withCount('items')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10)->withQueryString();

        return Inertia::render('User/Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'status' => $status,
            ],
        ]);
    }

    /**
     * Show an order detail for the authenticated user
     */
    public function show($id)
    {
        $user = Auth::user();

        $order = Order::with(['items.product'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return Inertia::render('User/Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Allow user to cancel order if still pending or processing
     */
    public function cancel($id)
    {
        $user = Auth::user();

        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại.');
        }

        $order->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('user.orders.show', $order->id)->with('success', 'Đã hủy đơn hàng.');
    }
}



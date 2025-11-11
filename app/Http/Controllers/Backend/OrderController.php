<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * List all orders with filters for admin
     */
    public function index(Request $request)
    {
        $query = Order::with(['user'])->withCount('items')->orderByDesc('created_at');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($paymentStatus = $request->get('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }
        if ($keyword = $request->get('q')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('order_number', 'like', "%{$keyword}%")
                  ->orWhere('shipping_name', 'like', "%{$keyword}%")
                  ->orWhere('shipping_phone', 'like', "%{$keyword}%")
                  ->orWhere('shipping_email', 'like', "%{$keyword}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'status' => $status,
                'payment_status' => $paymentStatus,
                'q' => $keyword,
            ],
        ]);
    }

    /**
     * Show order detail
     */
    public function show($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'status' => $request->input('status'),
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng.');
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'payment_status' => $request->input('payment_status'),
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái thanh toán.');
    }
}



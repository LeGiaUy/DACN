<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalRevenue = (float) Order::where('payment_status', 'paid')->sum('total');

        // Revenue by period
        $today = Carbon::today();
        $revenueToday = (float) Order::where('payment_status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('total');

        $revenueThisMonth = (float) Order::where('payment_status', 'paid')
            ->whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->sum('total');

        $revenueThisYear = (float) Order::where('payment_status', 'paid')
            ->whereYear('created_at', $today->year)
            ->sum('total');

        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $totalCategories = Category::count();
        $totalBrands = Brand::count();

        // Revenue charts (daily, monthly, yearly) - use PHP grouping for SQLite compatibility
        // Daily: last 7 days (including today)
        $dailyStart = Carbon::now()->subDays(6)->startOfDay();
        $ordersDaily = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $dailyStart)
            ->get(['created_at', 'total']);

        $rawDaily = $ordersDaily
            ->groupBy(function (Order $order) {
                return Carbon::parse($order->created_at)->format('Y-m-d');
            })
            ->map(function ($group) {
                return (object) [
                    'revenue' => $group->sum('total'),
                    'order_count' => $group->count(),
                ];
            });

        $dailyStats = [];
        $cursorDay = $dailyStart->copy();
        for ($i = 0; $i < 7; $i++) {
            $key = $cursorDay->format('Y-m-d');
            $label = $cursorDay->format('d/m');
            $row = $rawDaily->get($key);

            $dailyStats[] = [
                'key' => $key,
                'label' => $label,
                'revenue' => $row ? (float) $row->revenue : 0.0,
                'order_count' => $row ? (int) $row->order_count : 0,
            ];

            $cursorDay->addDay();
        }

        // Monthly: last 6 months (including current month)
        $monthlyStart = Carbon::now()->subMonths(5)->startOfMonth();
        $ordersMonthly = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $monthlyStart)
            ->get(['created_at', 'total']);

        $rawMonthly = $ordersMonthly
            ->groupBy(function (Order $order) {
                return Carbon::parse($order->created_at)->format('Y-m');
            })
            ->map(function ($group) {
                return (object) [
                    'revenue' => $group->sum('total'),
                    'order_count' => $group->count(),
                ];
            });

        $monthlyStats = [];
        $cursorMonth = $monthlyStart->copy();
        for ($i = 0; $i < 6; $i++) {
            $key = $cursorMonth->format('Y-m');
            $label = $cursorMonth->format('m/Y');
            $row = $rawMonthly->get($key);

            $monthlyStats[] = [
                'key' => $key,
                'label' => $label,
                'revenue' => $row ? (float) $row->revenue : 0.0,
                'order_count' => $row ? (int) $row->order_count : 0,
            ];

            $cursorMonth->addMonth();
        }

        // Yearly: last 5 years (including current year)
        $yearlyStart = Carbon::now()->subYears(4)->startOfYear();
        $ordersYearly = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $yearlyStart)
            ->get(['created_at', 'total']);

        $rawYearly = $ordersYearly
            ->groupBy(function (Order $order) {
                return Carbon::parse($order->created_at)->format('Y');
            })
            ->map(function ($group) {
                return (object) [
                    'revenue' => $group->sum('total'),
                    'order_count' => $group->count(),
                ];
            });

        $yearlyStats = [];
        $cursorYear = $yearlyStart->copy();
        for ($i = 0; $i < 5; $i++) {
            $key = $cursorYear->format('Y');
            $label = $cursorYear->format('Y');
            $row = $rawYearly->get($key);

            $yearlyStats[] = [
                'key' => $key,
                'label' => $label,
                'revenue' => $row ? (float) $row->revenue : 0.0,
                'order_count' => $row ? (int) $row->order_count : 0,
            ];

            $cursorYear->addYear();
        }

        $statusBreakdown = [
            'pending' => $pendingOrders,
            'processing' => $processingOrders,
            'delivered' => $deliveredOrders,
            'cancelled' => $cancelledOrders,
        ];

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_products' => $totalProducts,
                'total_orders' => $totalOrders,
                'total_users' => $totalUsers,
                'total_revenue' => $totalRevenue,
                'revenue_today' => $revenueToday,
                'revenue_this_month' => $revenueThisMonth,
                'revenue_this_year' => $revenueThisYear,
                'total_categories' => $totalCategories,
                'total_brands' => $totalBrands,
            ],
            'charts' => [
                'daily' => $dailyStats,
                'monthly' => $monthlyStats,
                'yearly' => $yearlyStats,
                'status_breakdown' => $statusBreakdown,
            ],
        ]);
    }

    public function exportPdf()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalRevenue = (float) Order::where('payment_status', 'paid')->sum('total');
        $totalCategories = Category::count();
        $totalBrands = Brand::count();

        $statusBreakdown = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        $data = [
            'generated_at' => now()->timezone('Asia/Ho_Chi_Minh'),
            'stats' => [
                'total_products' => $totalProducts,
                'total_orders' => $totalOrders,
                'total_users' => $totalUsers,
                'total_revenue' => $totalRevenue,
                'total_categories' => $totalCategories,
                'total_brands' => $totalBrands,
            ],
            'status_breakdown' => $statusBreakdown,
        ];

        $pdf = Pdf::loadView('admin.dashboard_pdf', $data)->setPaper('a4', 'portrait');

        return $pdf->download('dashboard-report-' . now()->format('Ymd_His') . '.pdf');
    }
}

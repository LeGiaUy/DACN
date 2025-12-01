<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy sản phẩm mới nhất (HÀNG MỚI VỀ)
        $newProducts = Product::with(['category', 'brand'])
            ->orderBy('created_at', 'desc')
            ->limit(16)
            ->get();
            
        // Lấy sản phẩm nổi bật
        $featuredProducts = Product::with(['category', 'brand'])
            ->where('is_featured', true)
            ->limit(8)
            ->get();
            
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();
        
        // Lấy banners đang hoạt động, sắp xếp theo order
        try {
            $banners = Banner::where('is_active', true)
                ->orderBy('order')
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            // Nếu bảng chưa tồn tại, trả về mảng rỗng
            $banners = collect([]);
        }

        return Inertia::render('User/Home', [
            'newProducts' => $newProducts,
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'brands' => $brands,
            'banners' => $banners,
        ]);
    }
}
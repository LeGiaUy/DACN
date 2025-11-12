<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
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

        return Inertia::render('User/Home', [
            'newProducts' => $newProducts,
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
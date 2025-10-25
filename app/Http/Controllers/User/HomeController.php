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
        $featuredProducts = Product::with(['category', 'brand'])
            ->where('is_featured', true)
            ->limit(8)
            ->get();
            
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();

        return Inertia::render('User/Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
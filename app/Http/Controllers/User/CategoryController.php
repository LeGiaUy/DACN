<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        
        return Inertia::render('User/Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function show(Category $category, Request $request)
    {
        $query = $category->products()->with(['brand']);

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        // Handle price_desc special case
        if ($sortBy === 'price_desc') {
            $sortBy = 'price';
            $sortOrder = 'desc';
        } elseif ($sortBy === 'price') {
            // Default to ascending for price if no order specified
            $sortOrder = $request->get('order', 'asc');
        }
        
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(12)->appends($request->query());
        $brands = $category->products()->with('brand')->get()->pluck('brand')->unique('id');

        return Inertia::render('User/Categories/Show', [
            'category' => $category,
            'products' => $products,
            'brands' => $brands,
            'filters' => $request->only(['brand', 'search', 'min_price', 'max_price', 'sort', 'order'])
        ]);
    }
}
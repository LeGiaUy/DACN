<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->get();
        
        return Inertia::render('User/Brands/Index', [
            'brands' => $brands
        ]);
    }

    public function show(Brand $brand, Request $request)
    {
        $query = $brand->products()->with(['category']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
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
        $categories = $brand->products()->with('category')->get()->pluck('category')->unique('id');

        return Inertia::render('User/Brands/Show', [
            'brand' => $brand,
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['category', 'search', 'min_price', 'max_price', 'sort', 'order'])
        ]);
    }
}
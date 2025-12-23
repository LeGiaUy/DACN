<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 6);
        $perPage = max(1, min($perPage, 50));
        
        $with = ['category', 'brand'];
        if (\Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
            $with[] = 'variants';
        }
        
        $products = Product::with($with)->paginate($perPage);
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'img_url' => 'nullable|string|url',
            'colors' => 'nullable|array',
            'colors.*' => 'string|max:50',
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:10',
            'quantity' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'variants' => 'nullable|array',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.size' => 'nullable|string|max:20',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.quantity' => 'required_with:variants|integer|min:0',
            'variants.*.img_url' => 'nullable|string|url|max:500',
        ]);
    
        $product = Product::create($request->only([
            'name', 'description', 'price', 'category_id', 'brand_id', 'img_url', 'colors', 'sizes', 'quantity', 'is_featured'
        ]));

        if ($request->filled('variants') && \Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
            $variants = collect($request->input('variants'))
                ->map(function ($v) use ($product) {
                    return [
                        'product_id' => $product->id,
                        'color' => $v['color'] ?? null,
                        'size' => $v['size'] ?? null,
                        'sku' => $v['sku'] ?? null,
                        'quantity' => (int) ($v['quantity'] ?? 0),
                        'img_url' => $v['img_url'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->all();
            if (!empty($variants)) {
                ProductVariant::insert($variants);
            }
        }
    
        $with = ['category', 'brand'];
        if (\Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
            $with[] = 'variants';
        }
        return response()->json($product->load($with), 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $with = ['category', 'brand'];
        if (\Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
            $with[] = 'variants';
        }
        
        $product = Product::with($with)->findOrFail($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'img_url' => 'nullable|string|url',
            'colors' => 'nullable|array',
            'colors.*' => 'string|max:50',
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:10',
            'quantity' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'variants' => 'nullable|array',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.size' => 'nullable|string|max:20',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.quantity' => 'required_with:variants|integer|min:0',
            'variants.*.img_url' => 'nullable|string|url|max:500',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->only([
            'name', 'description', 'price', 'category_id', 'brand_id', 'img_url', 'colors', 'sizes', 'quantity', 'is_featured'
        ]));

        if ($request->has('variants') && \Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
            $product->variants()->delete();
            $variants = collect($request->input('variants'))
                ->map(function ($v) use ($product) {
                    return [
                        'product_id' => $product->id,
                        'color' => $v['color'] ?? null,
                        'size' => $v['size'] ?? null,
                        'sku' => $v['sku'] ?? null,
                        'quantity' => (int) ($v['quantity'] ?? 0),
                        'img_url' => $v['img_url'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->all();
            if (!empty($variants)) {
                ProductVariant::insert($variants);
            }
        }

        $with = ['category', 'brand'];
        if (\Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
            $with[] = 'variants';
        }
        return response()->json($product->load($with));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->noContent();
    }

    /**
     * Xóa nhiều products cùng lúc
     */
    public function destroyMany(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:products,id',
        ]);

        $ids = $request->input('ids');
        $deleted = Product::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => "Đã xóa {$deleted} sản phẩm",
            'deleted' => $deleted,
        ]);
    }

    /**
     * Handle image upload for products.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $path = $request->file('image')->store('products', 'public');
        $url = asset('storage/' . $path);

        return response()->json([
            'url' => $url,
            'path' => $path,
        ], 201);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\Product;

class ProductVariantsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $perPage = max(1, min($perPage, 100));
        
        $query = ProductVariant::with(['product.category', 'product.brand']);
        
        // Filter by product_id
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        // Filter by color
        if ($request->filled('color')) {
            $query->where('color', 'like', '%' . $request->color . '%');
        }
        
        // Filter by size
        if ($request->filled('size')) {
            $query->where('size', 'like', '%' . $request->size . '%');
        }
        
        // Filter by SKU
        if ($request->filled('sku')) {
            $query->where('sku', 'like', '%' . $request->sku . '%');
        }
        
        // Filter by low stock (quantity < threshold)
        if ($request->filled('low_stock')) {
            $threshold = (int) $request->get('low_stock_threshold', 10);
            $query->where('quantity', '<', $threshold);
        }
        
        // Filter by out of stock
        if ($request->filled('out_of_stock')) {
            $query->where('quantity', '<=', 0);
        }
        
        // Search in product name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['id', 'product_id', 'color', 'size', 'sku', 'quantity', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $variants = $query->paginate($perPage);
        
        return response()->json($variants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color' => 'nullable|string|max:50',
            'size' => 'nullable|string|max:20',
            'sku' => 'nullable|string|max:100|unique:product_variants,sku',
            'quantity' => 'required|integer|min:0',
        ]);

        // Check for duplicate variant (product_id + color + size combination)
        $existing = ProductVariant::where('product_id', $request->product_id)
            ->where('color', $request->color ?? null)
            ->where('size', $request->size ?? null)
            ->first();
            
        if ($existing) {
            return response()->json([
                'message' => 'Biến thể này đã tồn tại cho sản phẩm này'
            ], 422);
        }

        $variant = ProductVariant::create($request->only([
            'product_id', 'color', 'size', 'sku', 'quantity'
        ]));

        return response()->json($variant->load(['product.category', 'product.brand']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $variant = ProductVariant::with(['product.category', 'product.brand'])->findOrFail($id);
        return response()->json($variant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $variant = ProductVariant::findOrFail($id);
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color' => 'nullable|string|max:50',
            'size' => 'nullable|string|max:20',
            'sku' => 'nullable|string|max:100|unique:product_variants,sku,' . $id,
            'quantity' => 'required|integer|min:0',
        ]);

        // Check for duplicate variant (excluding current variant)
        $existing = ProductVariant::where('product_id', $request->product_id)
            ->where('color', $request->color ?? null)
            ->where('size', $request->size ?? null)
            ->where('id', '!=', $id)
            ->first();
            
        if ($existing) {
            return response()->json([
                'message' => 'Biến thể này đã tồn tại cho sản phẩm này'
            ], 422);
        }

        $variant->update($request->only([
            'product_id', 'color', 'size', 'sku', 'quantity'
        ]));

        return response()->json($variant->load(['product.category', 'product.brand']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->delete();
        return response()->noContent();
    }

    /**
     * Bulk update quantities
     */
    public function bulkUpdateQuantity(Request $request)
    {
        $request->validate([
            'variants' => 'required|array',
            'variants.*.id' => 'required|exists:product_variants,id',
            'variants.*.quantity' => 'required|integer|min:0',
        ]);

        $updated = [];
        foreach ($request->variants as $item) {
            $variant = ProductVariant::find($item['id']);
            if ($variant) {
                $variant->quantity = $item['quantity'];
                $variant->save();
                $updated[] = $variant;
            }
        }

        return response()->json([
            'message' => 'Đã cập nhật ' . count($updated) . ' biến thể',
            'variants' => $updated
        ]);
    }

    /**
     * Get statistics
     */
    public function statistics()
    {
        $total = ProductVariant::count();
        $inStock = ProductVariant::where('quantity', '>', 0)->count();
        $lowStock = ProductVariant::where('quantity', '>', 0)
            ->where('quantity', '<', 10)
            ->count();
        $outOfStock = ProductVariant::where('quantity', '<=', 0)->count();
        $totalQuantity = ProductVariant::sum('quantity');

        return response()->json([
            'total' => $total,
            'in_stock' => $inStock,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'total_quantity' => $totalQuantity,
        ]);
    }
}


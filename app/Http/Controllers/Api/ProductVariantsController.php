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
        
        // Filter by category_id
        if ($request->filled('category_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        
        // Filter by brand_id
        if ($request->filled('brand_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('brand_id', $request->brand_id);
            });
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
            'img_url' => 'nullable|string|url|max:500',
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
            'product_id', 'color', 'size', 'sku', 'quantity', 'img_url'
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
            'img_url' => 'nullable|string|url|max:500',
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
            'product_id', 'color', 'size', 'sku', 'quantity', 'img_url'
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

    /**
     * Import product variants from CSV file (no external package).
     *
     * Expected header:
     * product_id,color,size,sku,quantity,img_url
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Không thể đọc file upload'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'File CSV rỗng hoặc không hợp lệ'], 422);
        }

        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $created = 0;
        $updated = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count(array_filter($row, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            $data = array_combine($header, $row);
            if ($data === false) {
                $errors[] = "Dòng {$rowNumber}: số cột không khớp header";
                continue;
            }

            // Chuẩn hóa encoding về UTF-8 để tránh lỗi tiếng Việt
            $data = array_map(function ($value) {
                return is_string($value)
                    ? mb_convert_encoding($value, 'UTF-8', 'UTF-8,ISO-8859-1,Windows-1258,Windows-1252')
                    : $value;
            }, $data);

            if (empty($data['product_id']) || $data['quantity'] === null || $data['quantity'] === '') {
                $errors[] = "Dòng {$rowNumber}: thiếu product_id hoặc quantity";
                continue;
            }

            try {
                $payload = [
                    'product_id' => (int) $data['product_id'],
                    'color'      => $data['color'] !== '' ? $data['color'] : null,
                    'size'       => $data['size'] !== '' ? $data['size'] : null,
                    'sku'        => $data['sku'] !== '' ? $data['sku'] : null,
                    'quantity'   => (int) $data['quantity'],
                    'img_url'    => $data['img_url'] !== '' ? $data['img_url'] : null,
                ];

                $variant = ProductVariant::where('product_id', $payload['product_id'])
                    ->where('color', $payload['color'])
                    ->where('size', $payload['size'])
                    ->first();

                if ($variant) {
                    $variant->update($payload);
                    $updated++;
                } else {
                    ProductVariant::create($payload);
                    $created++;
                }
            } catch (\Exception $e) {
                $errors[] = "Dòng {$rowNumber}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return response()->json([
            'message' => 'Import biến thể sản phẩm hoàn tất',
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
        ]);
    }
}


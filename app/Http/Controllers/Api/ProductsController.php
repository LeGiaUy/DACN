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
        $perPage = max(1, min($perPage, 10000));
        
        $with = ['category', 'brand'];
        if (\Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
            $with[] = 'variants';
        }
        
        $query = Product::with($with);
        
        // Filter by search (product name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'featured') {
                $query->where('is_featured', true);
            } elseif ($request->status === 'with_variants') {
                $query->whereHas('variants');
            } elseif ($request->status === 'no_variants') {
                $query->whereDoesntHave('variants');
            }
        }
        
        $products = $query->paginate($perPage);
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'img_url' => 'nullable|string',
                'colors' => 'nullable|array',
                'colors.*' => 'string|max:50',
                'sizes' => 'nullable|array',
                'sizes.*' => 'string|max:10',
                'quantity' => 'nullable|integer|min:0',
                'is_featured' => 'nullable|boolean',
                'variants' => 'nullable|array',
                'variants.*.color' => 'nullable|string|max:50',
                'variants.*.size' => 'nullable|string|max:20',
                'variants.*.sku' => 'nullable|string|max:100',
                'variants.*.quantity' => 'nullable|integer|min:0',
                'variants.*.img_url' => 'nullable|string|max:500',
            ]);
            
            // Chuẩn hóa dữ liệu
            $data = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'price' => $validated['price'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'],
                'img_url' => !empty($validated['img_url']) ? $validated['img_url'] : null,
                'colors' => $validated['colors'] ?? [],
                'sizes' => $validated['sizes'] ?? [],
                'quantity' => $validated['quantity'] ?? 0,
                'is_featured' => $validated['is_featured'] ?? false,
            ];
    
            $product = Product::create($data);

            if ($request->filled('variants') && \Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
                $variants = collect($request->input('variants'))
                    ->filter(function ($v) {
                        // Chỉ tạo variant nếu có ít nhất color hoặc size
                        return !empty($v['color']) || !empty($v['size']);
                    })
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating product: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * Import products from CSV file (no external package).
     *
     * Expected header:
     * name,description,price,category,brand,img_url,quantity,is_featured
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

            if (empty($data['name']) || empty($data['price']) || empty($data['category']) || empty($data['brand'])) {
                $errors[] = "Dòng {$rowNumber}: thiếu name/price/category/brand";
                continue;
            }

            try {
                $category = \App\Models\Category::firstOrCreate(
                    ['name' => $data['category']],
                    ['description' => '']
                );

                $brand = \App\Models\Brand::firstOrCreate(
                    ['name' => $data['brand']],
                    ['description' => '']
                );

                $payload = [
                    'name'        => $data['name'],
                    'description' => $data['description'] ?? '',
                    'price'       => (float) $data['price'],
                    'category_id' => $category->id,
                    'brand_id'    => $brand->id,
                    'img_url'     => $data['img_url'] ?? null,
                    'colors'      => [],
                    'sizes'       => [],
                    'quantity'    => isset($data['quantity']) && $data['quantity'] !== '' ? (int) $data['quantity'] : 0,
                    'is_featured' => isset($data['is_featured']) && $data['is_featured'] !== ''
                        ? filter_var($data['is_featured'], FILTER_VALIDATE_BOOLEAN)
                        : false,
                ];

                $product = Product::where('name', $payload['name'])
                    ->where('brand_id', $brand->id)
                    ->first();

                if ($product) {
                    $product->update($payload);
                    $updated++;
                } else {
                    Product::create($payload);
                    $created++;
                }
            } catch (\Exception $e) {
                $errors[] = "Dòng {$rowNumber}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return response()->json([
            'message' => 'Import sản phẩm hoàn tất',
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
        ]);
    }
}

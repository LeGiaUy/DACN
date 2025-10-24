<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::with(['category', 'brand'])->get();
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
            'quantity' => 'required|integer|min:0',
        ]);
    
        $product = Product::create($request->only([
            'name', 'description', 'price', 'category_id', 'brand_id', 'img_url', 'colors', 'sizes', 'quantity'
        ]));
    
        return response()->json($product->load(['category', 'brand']), 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'brand'])->findOrFail($id);
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
            'quantity' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->only([
            'name', 'description', 'price', 'category_id', 'brand_id', 'img_url', 'colors', 'sizes', 'quantity'
        ]));

        return response()->json($product->load(['category', 'brand']));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully.']);
    }
}

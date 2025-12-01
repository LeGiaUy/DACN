<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Brand::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Cột description trong DB đang là NOT NULL,
        // ConvertEmptyStringsToNull sẽ biến '' thành null,
        // nên cần ép null thành chuỗi rỗng để tránh lỗi.
        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description') ?? '',
        ];

        return Brand::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return $brand;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description') ?? '',
        ];

        $brand->update($data);

        return $brand;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return response()->noContent();
    }
}

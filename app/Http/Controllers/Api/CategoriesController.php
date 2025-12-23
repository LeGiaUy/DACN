<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Category::all();
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
        // nên cần ép null thành chuỗi rỗng bằng toán tử ??
        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description') ?? '',
        ];

        return Category::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $category;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255', 
            'description' => 'nullable|string'
        ]);

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description') ?? '',
        ];

        $category->update($data);

        return $category;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent();
    }

    /**
     * Xóa nhiều categories cùng lúc
     */
    public function destroyMany(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:categories,id',
        ]);

        $ids = $request->input('ids');
        $deleted = Category::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => "Đã xóa {$deleted} danh mục",
            'deleted' => $deleted,
        ]);
    }
}

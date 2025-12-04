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
     * Import categories from CSV file (no external package).
     *
     * Expected header: name,description
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

        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            $data = array_combine($header, $row);
            if ($data === false || empty($data['name'])) {
                continue;
            }

            // Chuẩn hóa encoding về UTF-8 để tránh lỗi tiếng Việt
            $data = array_map(function ($value) {
                return is_string($value)
                    ? mb_convert_encoding($value, 'UTF-8', 'UTF-8,ISO-8859-1,Windows-1258,Windows-1252')
                    : $value;
            }, $data);

            $payload = [
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
            ];

            $category = Category::where('name', $payload['name'])->first();
            if ($category) {
                $category->update($payload);
                $updated++;
            } else {
                Category::create($payload);
                $created++;
            }
        }

        fclose($handle);

        return response()->json([
            'message' => 'Import danh mục hoàn tất',
            'created' => $created,
            'updated' => $updated,
        ]);
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
}

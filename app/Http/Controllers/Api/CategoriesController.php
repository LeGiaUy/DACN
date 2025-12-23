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
        $tempPath = null;
        
        // Đọc file và xử lý BOM (Byte Order Mark) cho UTF-8
        $content = file_get_contents($path);
        $hasBom = substr($content, 0, 3) === "\xEF\xBB\xBF";
        
        // Loại bỏ BOM nếu có
        if ($hasBom) {
            $content = substr($content, 3);
        }
        
        // Đảm bảo content là UTF-8
        if (!mb_check_encoding($content, 'UTF-8')) {
            // Thử convert từ các encoding phổ biến
            $encodings = ['Windows-1258', 'ISO-8859-1', 'Windows-1252'];
            foreach ($encodings as $enc) {
                $converted = @mb_convert_encoding($content, 'UTF-8', $enc);
                if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                    $content = $converted;
                    break;
                }
            }
        }
        
        // Tạo temp file với content đã được xử lý
        $tempPath = sys_get_temp_dir() . '/' . uniqid('csv_') . '.csv';
        file_put_contents($tempPath, $content);
        $path = $tempPath;
        
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Không thể đọc file upload'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header || empty($header)) {
            fclose($handle);
            return response()->json(['message' => 'File CSV rỗng hoặc không hợp lệ'], 422);
        }

        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $created = 0;
        $updated = 0;
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            
            // Bỏ qua dòng trống
            if (count(array_filter($row, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            // Kiểm tra số cột có khớp với header không
            if (count($row) !== count($header)) {
                continue;
            }

            $data = array_combine($header, $row);
            if ($data === false || empty(trim($data['name'] ?? ''))) {
                continue;
            }

            // Chuẩn hóa encoding về UTF-8 để tránh lỗi tiếng Việt
            $data = array_map(function ($value) {
                if (!is_string($value) || empty($value)) {
                    return $value;
                }
                
                // Kiểm tra xem đã là UTF-8 hợp lệ chưa
                if (mb_check_encoding($value, 'UTF-8')) {
                    // Nếu đã là UTF-8, kiểm tra xem có phải double encoding không
                    // Nếu decode UTF-8 rồi encode lại vẫn giống nhau thì OK
                    $decoded = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    if ($decoded === $value) {
                        return $value;
                    }
                }
                
                // Thử detect encoding và convert
                $encodings = ['UTF-8', 'Windows-1258', 'ISO-8859-1', 'Windows-1252'];
                $detected = mb_detect_encoding($value, $encodings, true);
                
                if ($detected && $detected !== 'UTF-8') {
                    $converted = @mb_convert_encoding($value, 'UTF-8', $detected);
                    if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                        return $converted;
                    }
                }
                
                // Nếu không detect được hoặc convert thất bại, thử force convert từ Windows-1258
                // (encoding phổ biến cho tiếng Việt trên Windows)
                $converted = @mb_convert_encoding($value, 'UTF-8', 'Windows-1258');
                if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                    return $converted;
                }
                
                // Cuối cùng, trả về giá trị gốc
                return $value;
            }, $data);

            $payload = [
                'name' => trim($data['name']),
                'description' => trim($data['description'] ?? ''),
            ];

            if (empty($payload['name'])) {
                continue;
            }

            try {
                $category = Category::where('name', $payload['name'])->first();
                if ($category) {
                    $category->update($payload);
                    $updated++;
                } else {
                    Category::create($payload);
                    $created++;
                }
            } catch (\Exception $e) {
                // Log lỗi nhưng tiếp tục xử lý các dòng khác
                \Log::error("Lỗi import category dòng {$rowNumber}: " . $e->getMessage());
                continue;
            }
        }

        fclose($handle);
        
        // Xóa temp file luôn (vì đã tạo temp file mới)
        if ($tempPath !== null && file_exists($tempPath)) {
            @unlink($tempPath);
        }

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

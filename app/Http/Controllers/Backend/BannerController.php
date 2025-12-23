<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::orderBy('order')->orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/Banners/Index', [
            'banners' => $banners
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'image_url' => 'required|string|max:500',
            'alt_text' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $banner = Banner::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($banner, 201);
        }

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'image_url' => 'required|string|max:500',
            'alt_text' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $banner->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($banner);
        }

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Banner $banner)
    {
        $banner->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Banner đã được xóa thành công.'], 200);
        }

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được xóa thành công.');
    }

    /**
     * Import banners from CSV file (no external package).
     *
     * Expected header:
     * title,image_url,alt_text,link_url,order,is_active
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return back()->with('error', 'Không thể đọc file upload');
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'File CSV rỗng hoặc không hợp lệ');
        }

        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $created = 0;
        $updated = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            $data = array_combine($header, $row);
            if ($data === false || empty($data['image_url'])) {
                continue;
            }

            // Chuẩn hóa encoding về UTF-8 để tránh lỗi tiếng Việt
            $data = array_map(function ($value) {
                return is_string($value)
                    ? mb_convert_encoding($value, 'UTF-8', 'UTF-8,ISO-8859-1,Windows-1258,Windows-1252')
                    : $value;
            }, $data);

            $payload = [
                'title'     => $data['title'] ?? null,
                'image_url' => $data['image_url'],
                'alt_text'  => $data['alt_text'] ?? null,
                'link_url'  => $data['link_url'] ?? null,
                'order'     => isset($data['order']) && $data['order'] !== '' ? (int) $data['order'] : 0,
                'is_active' => isset($data['is_active']) && $data['is_active'] !== ''
                    ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN)
                    : true,
            ];

            $banner = Banner::where('image_url', $payload['image_url'])->first();
            if ($banner) {
                $banner->update($payload);
                $updated++;
            } else {
                Banner::create($payload);
                $created++;
            }
        }

        fclose($handle);

        $message = "Import banner hoàn tất. Tạo mới: {$created}, cập nhật: {$updated}";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $message], 200);
        }

        return redirect()->route('admin.banners.index')
            ->with('success', $message);
    }
}

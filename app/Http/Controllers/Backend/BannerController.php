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
}

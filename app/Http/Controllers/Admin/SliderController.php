<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sliders = Slider::ordered()->paginate(10);
        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['title', 'sort_order']);
        $data['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                // Simpan ke storage/app/public/sliders (untuk database)
                $path = $image->storeAs('public/sliders', $imageName);
                
                // Copy ke public/storage/sliders (untuk akses web)
                $publicPath = public_path('storage/sliders/' . $imageName);
                if (!file_exists(public_path('storage/sliders'))) {
                    mkdir(public_path('storage/sliders'), 0755, true);
                }
                copy($image->getRealPath(), $publicPath);
                
                $data['image'] = $imageName;
                Log::info('Slider: Image berhasil diupload', ['file' => $imageName, 'path' => $path, 'public_path' => $publicPath]);
            } catch (\Exception $e) {
                Log::error('Slider: Gagal upload image', ['error' => $e->getMessage()]);
                return back()->withErrors(['image' => 'Gagal mengupload gambar: ' . $e->getMessage()]);
            }
        }

        Slider::create($data);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Slider $slider)
    {
        return view('admin.sliders.show', compact('slider'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['title', 'sort_order']);
        $data['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                // Delete old image if exists
                if ($slider->image) {
                    Storage::delete('public/sliders/' . $slider->image);
                    $publicPath = public_path('storage/sliders/' . $slider->image);
                    if (file_exists($publicPath)) {
                        unlink($publicPath);
                    }
                }
                
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                // Simpan ke storage/app/public/sliders (untuk database)
                $path = $image->storeAs('public/sliders', $imageName);
                
                // Copy ke public/storage/sliders (untuk akses web)
                $publicPath = public_path('storage/sliders/' . $imageName);
                if (!file_exists(public_path('storage/sliders'))) {
                    mkdir(public_path('storage/sliders'), 0755, true);
                }
                copy($image->getRealPath(), $publicPath);
                
                $data['image'] = $imageName;
                Log::info('Slider: Image berhasil diupload (update)', ['file' => $imageName, 'path' => $path, 'public_path' => $publicPath]);
            } catch (\Exception $e) {
                Log::error('Slider: Gagal upload image (update)', ['error' => $e->getMessage()]);
                return back()->withErrors(['image' => 'Gagal mengupload gambar: ' . $e->getMessage()]);
            }
        }

        $slider->update($data);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider)
    {
        // Delete image if exists
        if ($slider->image) {
            Storage::delete('public/sliders/' . $slider->image);
            // Hapus juga dari public/storage/sliders
            $publicPath = public_path('storage/sliders/' . $slider->image);
            if (file_exists($publicPath)) {
                unlink($publicPath);
            }
        }

        $slider->delete();

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider berhasil dihapus!');
    }
}

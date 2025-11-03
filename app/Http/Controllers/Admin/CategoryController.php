<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::ordered()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        // Handle icon upload
        if ($request->hasFile('icon')) {
            try {
                $icon = $request->file('icon');
                $iconName = time() . '_' . $icon->getClientOriginalName();
                
                // Simpan ke storage/app/public/categories (untuk database)
                $path = $icon->storeAs('public/categories', $iconName);
                
                // Copy ke public/storage/categories (untuk akses web)
                $publicPath = public_path('storage/categories/' . $iconName);
                if (!file_exists(public_path('storage/categories'))) {
                    mkdir(public_path('storage/categories'), 0755, true);
                }
                copy($icon->getRealPath(), $publicPath);
                
                $data['icon'] = $iconName;
                Log::info('Kategori: Icon berhasil diupload', ['file' => $iconName, 'path' => $path, 'public_path' => $publicPath]);
            } catch (\Exception $e) {
                Log::error('Kategori: Gagal upload icon', ['error' => $e->getMessage()]);
            }
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        // Handle icon upload
        if ($request->hasFile('icon')) {
            try {
                // Delete old icon if exists
                if ($category->icon) {
                    Storage::delete('public/categories/' . $category->icon);
                }
                $icon = $request->file('icon');
                $iconName = time() . '_' . $icon->getClientOriginalName();
                
                // Simpan ke storage/app/public/categories (untuk database)
                $path = $icon->storeAs('public/categories', $iconName);
                
                // Copy ke public/storage/categories (untuk akses web)
                $publicPath = public_path('storage/categories/' . $iconName);
                if (!file_exists(public_path('storage/categories'))) {
                    mkdir(public_path('storage/categories'), 0755, true);
                }
                copy($icon->getRealPath(), $publicPath);
                
                $data['icon'] = $iconName;
                Log::info('Kategori: Icon berhasil diupload (update)', ['file' => $iconName, 'path' => $path, 'public_path' => $publicPath]);
            } catch (\Exception $e) {
                Log::error('Kategori: Gagal upload icon (update)', ['error' => $e->getMessage()]);
            }
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has campaigns
        if ($category->campaigns()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki campaign!');
        }

        // Delete icon if exists
        if ($category->icon) {
            Storage::delete('public/categories/' . $category->icon);
            // Hapus juga dari public/storage/categories
            $publicPath = public_path('storage/categories/' . $category->icon);
            if (file_exists($publicPath)) {
                unlink($publicPath);
            }
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}

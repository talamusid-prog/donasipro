@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('header-title', 'Tambah Kategori')
@section('header-subtitle', 'Buat kategori campaign baru')

@section('header-button')
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
        Kembali ke Daftar
    </a>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Form Tambah Kategori</h2>
    </div>
    
    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: Yatim & Dhuafa" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Warna *</label>
                <input type="color" name="color" id="color" value="{{ old('color', '#3B82F6') }}" 
                       class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                @error('color')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="0" min="0">
                @error('sort_order')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">Icon (Opsional)</label>
                <input type="file" name="icon" id="icon" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       accept="image/*">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF, SVG. Maksimal 2MB.</p>
                @error('icon')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" id="description" rows="4" 
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Deskripsi singkat tentang kategori ini...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Kategori Aktif</span>
                </label>
            </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('admin.categories.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Batal
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                Simpan Kategori
            </button>
        </div>
    </form>
</div>
@endsection 
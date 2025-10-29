@extends('layouts.admin')

@section('title', 'Edit Slider')
@section('header-title', 'Edit Slider')
@section('header-subtitle', 'Edit slider untuk halaman home')
@section('header-button')
    <a href="{{ route('admin.sliders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Kembali ke Daftar</a>
@endsection
@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Form Edit Slider</h2>
    </div>
    <form method="POST" action="{{ route('admin.sliders.update', $slider->id) }}" enctype="multipart/form-data" class="p-6">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $slider->title) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Gambar</label>
                <input type="file" name="image" id="image" class="w-full border border-gray-300 rounded-lg px-3 py-2" accept="image/*">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF, WEBP. Maksimal 2MB.</p>
                @if($slider->image)
                    <div class="mt-2">
                        <img src="{{ $slider->image_url }}" alt="Current image" class="w-32 h-20 object-cover rounded border">
                    </div>
                @endif
                @error('image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $slider->sort_order) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" min="0">
                @error('sort_order')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center mt-6">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $slider->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
            </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('admin.sliders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Batal
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                Update Slider
            </button>
        </div>
    </form>
</div>
@endsection 
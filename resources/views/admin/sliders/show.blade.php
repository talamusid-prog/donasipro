@extends('layouts.admin')

@section('title', 'Detail Slider: ' . $slider->title)
@section('header-title', 'Detail Slider')
@section('header-subtitle', $slider->title)
@section('header-button')
    <a href="{{ route('admin.sliders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
        Kembali ke Daftar
    </a>
    <a href="{{ route('admin.sliders.edit', $slider) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 ml-2">
        <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
        Edit Slider
    </a>
@endsection
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                @if($slider->image_url)
                    <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}" class="w-full h-48 object-cover rounded-lg mb-6">
                @endif
                <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $slider->title }}</h2>
            </div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <div class="mb-4">
                    <span class="text-sm font-medium text-gray-500">Status</span>
                    <p class="text-sm font-semibold {{ $slider->is_active ? 'text-green-600' : 'text-red-600' }}">{{ $slider->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                </div>
                <div class="mb-4">
                    <span class="text-sm font-medium text-gray-500">Urutan</span>
                    <p class="text-sm text-gray-900">{{ $slider->sort_order }}</p>
                </div>
                <div class="mb-4">
                    <span class="text-sm font-medium text-gray-500">Dibuat</span>
                    <p class="text-sm text-gray-900">{{ $slider->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="mb-4">
                    <span class="text-sm font-medium text-gray-500">Diupdate</span>
                    <p class="text-sm text-gray-900">{{ $slider->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
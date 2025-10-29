@extends('layouts.admin')

@section('title', 'Detail Kategori: ' . $category->name)

@section('header-title', 'Detail Kategori')
@section('header-subtitle', $category->name)

@section('header-button')
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
        Kembali ke Daftar
    </a>
    <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 ml-2">
        <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
        Edit Kategori
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Left Column: Category Details --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    @if($category->icon)
                        <img src="{{ $category->icon_url }}" alt="{{ $category->name }}" class="w-16 h-16 rounded-lg object-cover mr-4">
                    @else
                        <div class="w-16 h-16 rounded-lg mr-4 flex items-center justify-center" style="background-color: {{ $category->color }}">
                            <span class="text-white text-2xl font-bold">{{ strtoupper(substr($category->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $category->name }}</h2>
                        <p class="text-gray-500">Slug: {{ $category->slug }}</p>
                    </div>
                </div>

                @if($category->description)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Deskripsi</h3>
                        <p class="text-gray-700">{{ $category->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Warna</span>
                        <div class="flex items-center mt-1">
                            <div class="w-6 h-6 rounded mr-2" style="background-color: {{ $category->color }}"></div>
                            <span class="text-sm text-gray-900">{{ $category->color }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Urutan</span>
                        <p class="text-sm text-gray-900 mt-1">{{ $category->sort_order }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Status</span>
                        <p class="text-sm mt-1">
                            <span class="px-2 py-1 text-xs rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Dibuat</span>
                        <p class="text-sm text-gray-900 mt-1">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Campaigns --}}
    <div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Campaign dalam Kategori</h3>
            </div>
            <div class="p-6">
                @if($category->campaigns->count() > 0)
                    <div class="space-y-4">
                        @foreach($category->campaigns->take(10) as $campaign)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <img src="{{ $campaign->image_url ?? 'https://via.placeholder.com/40x40' }}" 
                                         alt="{{ $campaign->title }}" class="w-10 h-10 rounded object-cover mr-3">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $campaign->title }}</h4>
                                        <p class="text-xs text-gray-500">{{ $campaign->organization }}</p>
                                    </div>
                                </div>
                                <div class="mt-2 flex justify-between text-xs text-gray-500">
                                    <span>Target: Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</span>
                                    <span>{{ $campaign->status }}</span>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($category->campaigns->count() > 10)
                            <p class="text-center text-sm text-gray-500">
                                Dan {{ $category->campaigns->count() - 10 }} campaign lainnya...
                            </p>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Belum ada campaign dalam kategori ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 
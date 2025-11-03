@extends('layouts.admin')

@section('title', 'Kelola Kategori')

@section('header-title', 'Kelola Kategori')
@section('header-subtitle', 'Manajemen kategori campaign')

@section('header-button')
    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
        Tambah Kategori
    </a>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Kategori</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Campaign</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($categories as $category)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($category->icon)
                                <img src="{{ $category->icon_url }}" alt="{{ $category->name }}" class="w-8 h-8 rounded object-cover mr-3">
                            @else
                                <div class="w-8 h-8 rounded mr-3 flex items-center justify-center" style="background-color: {{ $category->color }}">
                                    <span class="text-white text-xs font-bold">{{ strtoupper(substr($category->name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                <div class="text-sm text-gray-500">{{ $category->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            {{ Str::limit($category->description, 50) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $category->campaigns_count }} campaign
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($category->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                            {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $category->sort_order }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('admin.categories.show', $category) }}" 
                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200 transition-colors duration-200">
                                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                Detail
                            </a>
                            <a href="{{ route('admin.categories.edit', $category) }}" 
                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-md hover:bg-indigo-200 transition-colors duration-200">
                                <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 transition-colors duration-200">
                                    <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $categories->links() }}
    </div>
</div>
@endsection 
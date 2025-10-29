@extends('layouts.admin')

@section('title', 'Kelola Slider')

@section('header-title', 'Kelola Slider')
@section('header-subtitle', 'Manajemen slider halaman home')

@section('header-button')
    <a href="{{ route('admin.sliders.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
        Tambah Slider
    </a>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Slider</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($sliders as $slider)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($slider->image_url)
                            <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}" class="w-20 h-12 object-cover rounded">
                        @else
                            <span class="text-xs text-gray-400">Tidak ada gambar</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $slider->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $slider->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $slider->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $slider->sort_order }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('admin.sliders.show', $slider) }}" 
                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200 transition-colors duration-200">
                                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                Detail
                            </a>
                            <a href="{{ route('admin.sliders.edit', $slider) }}" 
                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-md hover:bg-indigo-200 transition-colors duration-200">
                                <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus slider ini?')">
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
        {{ $sliders->links() }}
    </div>
</div>
@endsection

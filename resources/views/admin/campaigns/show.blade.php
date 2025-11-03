@extends('layouts.admin')

@section('title', 'Detail Campaign: ' . $campaign->title)

@section('header-title', 'Detail Campaign')
@section('header-subtitle', $campaign->title)

@section('header-button')
    <a href="{{ route('admin.campaigns.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
        Kembali ke Daftar
    </a>
    <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 ml-2">
        <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
        Edit Campaign
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Left Column: Campaign Details --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <img src="{{ $campaign->image_url ?? 'https://via.placeholder.com/800x400' }}" alt="{{ $campaign->title }}" class="w-full h-64 object-cover rounded-lg mb-6">
                
                <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $campaign->title }}</h2>
                <p class="text-sm text-gray-600 mb-4">Oleh: <span class="font-medium">{{ $campaign->organization }}</span></p>

                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($campaign->description)) !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Campaign Stats & Donations --}}
    <div>
        {{-- Stats Card --}}
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Dana</h3>
                
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">Terkumpul</span>
                        <span class="text-sm font-medium text-green-600">{{ number_format($campaign->progress_percentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $campaign->progress_percentage }}%"></div>
                    </div>
                </div>

                <div class="space-y-2 text-sm text-gray-800">
                    <div class="flex justify-between">
                        <span>Terkumpul:</span>
                        <span class="font-semibold">Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Target:</span>
                        <span class="font-semibold">Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Jumlah Donatur:</span>
                        <span class="font-semibold">{{ $campaign->donations->count() }} orang</span>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 grid grid-cols-2 gap-4 text-center">
                 <div>
                    <span class="text-xs text-gray-500">Status</span>
                    <p class="text-sm font-semibold {{ $campaign->status === 'active' ? 'text-green-600' : 'text-red-600' }}">{{ $campaign->status_label }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Verifikasi</span>
                    <p class="text-sm font-semibold {{ $campaign->is_verified ? 'text-green-600' : 'text-yellow-600' }}">{{ $campaign->is_verified ? 'Terverifikasi' : 'Pending' }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Kategori</span>
                    <p class="text-sm font-semibold">{{ $campaign->category_label }}</p>
                </div>
                 <div>
                    <span class="text-xs text-gray-500">Section</span>
                    <p class="text-sm font-semibold">{{ $campaign->section_label }}</p>
                </div>
            </div>
        </div>

        {{-- Recent Donations --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Donasi Terakhir</h3>
            </div>
            <div class="p-6">
                @if($campaign->donations->count() > 0)
                    <div class="space-y-4">
                        @foreach($campaign->donations->sortByDesc('created_at')->take(10) as $donation)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 text-xs font-medium">{{ strtoupper(substr($donation->donor_name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $donation->donor_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $donation->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-green-600">Rp {{ number_format($donation->amount, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Belum ada donasi.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 
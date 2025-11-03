@extends('layouts.app')

@section('title', 'Donasi Berhasil')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="card p-6">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="heart" class="w-10 h-10 text-green-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Terima Kasih!</h1>
            <p class="text-gray-600">Donasi Anda telah berhasil diproses</p>
        </div>

        <!-- Success Message -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mt-0.5"></i>
                <div>
                    <h3 class="font-medium text-green-900 mb-1">Donasi Berhasil</h3>
                    <p class="text-sm text-green-700">
                        Terima kasih telah berdonasi untuk kampanye "{{ $donation->campaign->title }}". 
                        Donasi Anda akan membantu banyak orang yang membutuhkan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Donation Summary -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">Ringkasan Donasi</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">ID Donasi</span>
                    <span class="font-medium">#{{ $donation->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tanggal</span>
                    <span class="font-medium">{{ $donation->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Kampanye</span>
                    <span class="font-medium">{{ $donation->campaign->title }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Jumlah Donasi</span>
                    <span class="font-medium text-lg text-brand-blue">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
                </div>
                @if($donation->message)
                    <div class="pt-2 border-t border-gray-200">
                        <span class="text-gray-600">Pesan:</span>
                        <p class="text-gray-900 mt-1">{{ $donation->message }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Next Steps -->
        <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">Langkah Selanjutnya</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 bg-brand-blue text-white rounded-full flex items-center justify-center text-sm font-medium mt-0.5">
                        1
                    </div>
                    <div>
                        <p class="text-sm text-gray-700">
                            Tim kami akan memverifikasi pembayaran Anda dalam 1x24 jam
                        </p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 bg-brand-blue text-white rounded-full flex items-center justify-center text-sm font-medium mt-0.5">
                        2
                    </div>
                    <div>
                        <p class="text-sm text-gray-700">
                            Anda akan menerima email konfirmasi setelah pembayaran diverifikasi
                        </p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 bg-brand-blue text-white rounded-full flex items-center justify-center text-sm font-medium mt-0.5">
                        3
                    </div>
                    <div>
                        <p class="text-sm text-gray-700">
                            Pantau perkembangan kampanye di halaman detail kampanye
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <a href="{{ route('home') }}" 
               class="flex-1 btn-secondary text-center">
                Beranda
            </a>
            <a href="{{ route('campaigns.show', $donation->campaign->slug) }}" 
               class="flex-1 btn-primary text-center">
                Lihat Kampanye
            </a>
        </div>

        <!-- Share Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-3 text-center">Bagikan Kampanye Ini</h3>
            <div class="flex justify-center space-x-4">
                <button class="p-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors">
                    <i data-lucide="facebook" class="w-5 h-5"></i>
                </button>
                <button class="p-2 bg-blue-400 text-white rounded-full hover:bg-blue-500 transition-colors">
                    <i data-lucide="twitter" class="w-5 h-5"></i>
                </button>
                <button class="p-2 bg-green-500 text-white rounded-full hover:bg-green-600 transition-colors">
                    <i data-lucide="message-circle" class="w-5 h-5"></i>
                </button>
                <button class="p-2 bg-gray-600 text-white rounded-full hover:bg-gray-700 transition-colors">
                    <i data-lucide="share" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
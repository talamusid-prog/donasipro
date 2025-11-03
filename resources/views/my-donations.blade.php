@extends('layouts.app')

@section('title', 'Donasi Saya - Donasi Apps')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Donasi Saya</h1>
        <p class="text-gray-600">Riwayat donasi yang telah Anda lakukan</p>
    </div>

    @if(auth()->user()->donations->count() > 0)
        <div class="space-y-4">
            @foreach(auth()->user()->donations as $donation)
                <div class="card p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-semibold text-lg">
                                    <a href="{{ route('campaigns.show', $donation->campaign->slug) }}" class="font-medium text-gray-900 hover:text-brand-blue">
                                        {{ $donation->campaign->title }}
                                    </a>
                                </h3>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($donation->payment_status === 'success') bg-green-100 text-green-800
                                    @elseif($donation->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    @if($donation->payment_status === 'success')
                                        Berhasil
                                    @elseif($donation->payment_status === 'pending')
                                        Menunggu
                                    @else
                                        Gagal
                                    @endif
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 mb-3">
                                <div>
                                    <span class="font-medium">Jumlah:</span>
                                    <span class="text-brand-blue font-semibold">
                                        Rp {{ number_format($donation->amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium">Tanggal:</span>
                                    {{ $donation->created_at->format('d M Y H:i') }}
                                </div>
                                <div>
                                    <span class="font-medium">Metode:</span>
                                    @switch($donation->payment_method)
                                        @case('bank_transfer')
                                            Transfer Bank
                                            @break
                                        @case('e_wallet')
                                            E-Wallet
                                            @break
                                        @case('qris')
                                            QRIS
                                            @break
                                    @endswitch
                                </div>
                            </div>
                            
                            @if($donation->message)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium">Pesan:</span> {{ $donation->message }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex flex-col items-end gap-2">
                            <a href="{{ route('campaigns.show', $donation->campaign->id) }}" 
                               class="text-sm text-brand-blue hover:underline">
                                Lihat Kampanye
                            </a>
                            @if($donation->payment_status === 'pending')
                                <a href="{{ route('donations.payment', $donation->id) }}" 
                                   class="text-sm text-brand-blue hover:underline">
                                    Lanjutkan Pembayaran
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="heart" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada donasi</h3>
            <p class="text-gray-600 mb-6">
                Anda belum melakukan donasi apapun. Mulai berdonasi sekarang untuk membantu sesama.
            </p>
            <a href="{{ route('home') }}" class="btn-primary">
                Lihat Program Donasi
            </a>
        </div>
    @endif
</div>
@endsection 
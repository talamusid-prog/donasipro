@extends('layouts.app')

@section('title', 'Bantuan - Donasi Apps')

@section('content')
<div class="px-4 py-6">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Pusat Bantuan</h1>
        <p class="text-sm text-gray-600">
            Temukan jawaban untuk pertanyaan Anda di sini.
        </p>
    </div>

    <div class="space-y-6">
        <!-- FAQ Section -->
        <div class="mobile-card p-4">
            <h2 class="text-lg font-semibold mb-4 px-2">Pertanyaan Umum</h2>
            
            <div x-data="{ openFaq: null }" class="space-y-2">
                @php
                    $faqs = [
                        ['q' => 'Bagaimana cara melakukan donasi?', 'a' => 'Pilih program donasi, klik tombol "Donasi Sekarang", isi form donasi, pilih metode pembayaran, dan selesaikan pembayaran.'],
                        ['q' => 'Metode pembayaran apa saja yang tersedia?', 'a' => 'Kami menyediakan berbagai metode pembayaran: transfer bank, e-wallet (GoPay, OVO, DANA), dan QRIS untuk kemudahan Anda.'],
                        ['q' => 'Apakah donasi saya aman?', 'a' => 'Ya, semua transaksi dilindungi sistem keamanan tinggi dan data pribadi Anda tidak akan dibagikan kepada pihak ketiga.'],
                        ['q' => 'Bagaimana saya bisa melacak donasi saya?', 'a' => 'Setelah login, Anda dapat melihat riwayat donasi di menu "Donasi Saya" dan melacak statusnya.'],
                        ['q' => 'Apakah saya bisa donasi secara anonim?', 'a' => 'Ya, Anda dapat memilih untuk donasi secara anonim dengan mencentang opsi "Sembunyikan Nama Saya" saat mengisi form donasi.'],
                        ['q' => 'Bagaimana jika pembayaran gagal?', 'a' => 'Jika pembayaran gagal, Anda akan menerima notifikasi dan dapat mencoba lagi. Jika masih mengalami masalah, hubungi tim support kami.'],
                    ];
                @endphp

                @foreach ($faqs as $index => $faq)
                <div class="border-b border-gray-100 last:border-b-0">
                    <button @click="openFaq = (openFaq === {{ $index }} ? null : {{ $index }})" class="w-full text-left flex justify-between items-center p-2">
                        <span class="font-medium text-sm text-gray-800">{{ $faq['q'] }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openFaq === {{ $index }} }"></i>
                    </button>
                    <div x-show="openFaq === {{ $index }}" x-collapse class="px-2 pb-3">
                        <p class="text-gray-600 text-xs">
                            {{ $faq['a'] }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Contact Info -->
        <div class="mobile-card p-4">
            <h3 class="font-semibold text-lg mb-3">Hubungi Kami</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <i data-lucide="mail" class="w-5 h-5 text-blue-600"></i>
                    <span class="text-sm text-gray-600">support@donasiapps.com</span>
                </div>
                <div class="flex items-center gap-3">
                    <i data-lucide="phone" class="w-5 h-5 text-blue-600"></i>
                    <span class="text-sm text-gray-600">+62 21 1234 5678</span>
                </div>
                <div class="flex items-center gap-3">
                    <i data-lucide="clock" class="w-5 h-5 text-blue-600"></i>
                    <span class="text-sm text-gray-600">Senin - Jumat, 09:00 - 17:00</span>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mobile-card p-4">
            <h3 class="font-semibold text-lg mb-3">Tautan Penting</h3>
            <div class="space-y-2">
                <a href="{{ route('campaigns') }}" class="block text-sm text-blue-600 hover:underline">
                    Program Donasi
                </a>
                <a href="{{ route('about') }}" class="block text-sm text-blue-600 hover:underline">
                    Tentang Kami
                </a>
                <a href="#" class="block text-sm text-blue-600 hover:underline">
                    Syarat & Ketentuan
                </a>
                <a href="#" class="block text-sm text-blue-600 hover:underline">
                    Kebijakan Privasi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 
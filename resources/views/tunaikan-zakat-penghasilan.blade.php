@extends('layouts.app')
@section('title', 'Tunaikan Zakat Penghasilan')
@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Tunaikan Zakat Penghasilan</h1>
            <p class="text-gray-600">Pilih campaign zakat yang ingin Anda tunaikan</p>
        </div>

        <!-- Campaign List -->
        <div class="max-w-4xl mx-auto">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Campaign 1 -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Zakat Penghasilan</h3>
                                <p class="text-sm text-gray-500">Program Zakat</p>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">Tunaikan zakat penghasilan Anda untuk membantu sesama yang membutuhkan.</p>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs text-gray-500">Target: Rp 100.000.000</span>
                            <span class="text-xs text-green-600 font-medium">Tersalurkan</span>
                        </div>
                        <a href="{{ route('campaigns.donate', 1) }}" class="btn-calculate-green w-full text-center">
                            Tunaikan Sekarang
                        </a>
                    </div>
                </div>

                <!-- Campaign 2 -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Zakat untuk Yatim</h3>
                                <p class="text-sm text-gray-500">Program Sosial</p>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">Bantu anak yatim dengan menyalurkan zakat penghasilan Anda.</p>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs text-gray-500">Target: Rp 50.000.000</span>
                            <span class="text-xs text-green-600 font-medium">Tersalurkan</span>
                        </div>
                        <a href="{{ route('campaigns.donate', 2) }}" class="btn-calculate-green w-full text-center">
                            Tunaikan Sekarang
                        </a>
                    </div>
                </div>

                <!-- Campaign 3 -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Zakat untuk Pendidikan</h3>
                                <p class="text-sm text-gray-500">Program Pendidikan</p>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">Dukung pendidikan anak-anak kurang mampu dengan zakat Anda.</p>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs text-gray-500">Target: Rp 75.000.000</span>
                            <span class="text-xs text-green-600 font-medium">Tersalurkan</span>
                        </div>
                        <a href="{{ route('campaigns.donate', 3) }}" class="btn-calculate-green w-full text-center">
                            Tunaikan Sekarang
                        </a>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-8">
                <a href="{{ route('zakat-calculator') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Kalkulator
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.btn-calculate-green {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    font-weight: 600;
    color: #fff;
    background: #f59e42;
    border-radius: 0.5rem;
    padding: 0.75rem 1.25rem;
    font-size: 0.875rem;
    box-shadow: 0 2px 8px 0 rgba(245,158,66,0.08);
    transition: background 0.2s, box-shadow 0.2s;
    text-align: center;
    min-height: 44px;
    gap: 0.5rem;
    border: none;
    text-decoration: none;
}
.btn-calculate-green:hover, .btn-calculate-green:focus {
    background: #ea580c;
    color: #fff;
}
</style>
@endsection 
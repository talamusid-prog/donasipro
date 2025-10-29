@extends('layouts.admin')

@section('title', 'Detail Donasi #'.$donation->id)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.donations.index') }}" class="text-gray-400 hover:text-brand-blue">
                    <i data-lucide="arrow-left" class="w-6 h-6"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Donasi</h1>
                    <p class="text-sm text-gray-500">ID: #{{ $donation->id }}</p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
            <!-- Status Badge -->
            @switch($donation->payment_status)
                @case('success')
                    <span class="badge-success">Berhasil</span>
                    @break
                @case('pending')
                    <span class="badge-warning">Menunggu Pembayaran</span>
                    @break
                @case('waiting_confirmation')
                    <span class="badge-info">Menunggu Konfirmasi</span>
                    @break
                @case('failed')
                    <span class="badge-danger">Gagal</span>
                    @break
                @default
                    <span class="badge-secondary">{{ $donation->payment_status }}</span>
            @endswitch
            
            <!-- Action Buttons -->
            <div class="flex gap-2">
                @if($donation->donor_email)
                <a href="mailto:{{ $donation->donor_email }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i data-lucide="mail" class="w-4 h-4 mr-1"></i>
                    Email
                </a>
                @endif
                
                @if($donation->donor_whatsapp)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $donation->donor_whatsapp) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <i data-lucide="message-circle" class="w-4 h-4 mr-1"></i>
                    WhatsApp
                </a>
                @endif
                
                <button onclick="window.print()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i data-lucide="printer" class="w-4 h-4 mr-1"></i>
                    Print
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Bukti Pembayaran -->
            <div class="card p-6">
                <h2 class="card-title">Bukti Pembayaran</h2>
                @if ($donation->payment_proof)
                    <div class="mt-4">
                        @php
                            // Gunakan storage URL langsung
                            $paymentProofUrl = asset('storage/' . $donation->payment_proof);
                        @endphp
                        <a href="{{ $paymentProofUrl }}" target="_blank">
                            <img src="{{ $paymentProofUrl }}" alt="Bukti Pembayaran" class="max-w-md mx-auto rounded-lg border border-gray-200 hover:opacity-90 transition-opacity shadow-sm">
                        </a>
                        <p class="text-xs text-gray-500 mt-2 text-center">Klik gambar untuk melihat ukuran penuh</p>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i data-lucide="file-x-2" class="w-16 h-16 mx-auto text-gray-300"></i>
                        <h3 class="mt-4 text-lg font-medium text-gray-800">Tidak Ada Bukti</h3>
                        <p class="text-gray-500 mt-1">Donatur belum mengupload bukti pembayaran.</p>
                    </div>
                @endif
            </div>

            <!-- Riwayat Donasi di Campaign Ini -->
            @if($donation->campaign)
            <div class="card p-6">
                <h2 class="card-title">Riwayat Donasi di Campaign Ini</h2>
                <div class="mt-4">
                    @php
                        $campaignDonations = $donation->campaign->donations()
                            ->where('donor_email', $donation->donor_email)
                            ->where('id', '!=', $donation->id)
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($campaignDonations->count() > 0)
                        <div class="space-y-3">
                            @foreach($campaignDonations as $prevDonation)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 text-xs font-medium">
                                                {{ strtoupper(substr($prevDonation->donor_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $prevDonation->donor_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $prevDonation->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">Rp {{ number_format($prevDonation->amount, 0, ',', '.') }}</p>
                                    <span class="inline-flex px-1.5 py-0.5 text-xs rounded-full
                                        @if($prevDonation->payment_status === 'success') bg-green-100 text-green-800
                                        @elseif($prevDonation->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($prevDonation->payment_status) }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.donations.index', ['campaign_id' => $donation->campaign->id, 'donor_email' => $donation->donor_email]) }}" class="text-sm text-blue-600 hover:underline">
                                Lihat semua donasi dari donatur ini
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i data-lucide="gift" class="w-12 h-12 mx-auto text-gray-300"></i>
                            <p class="text-gray-500 mt-2">Ini adalah donasi pertama dari donatur ini di campaign ini.</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Detail Campaign -->
            <div class="card p-6">
                <h2 class="card-title">Informasi Campaign</h2>
                @if($donation->campaign)
                    <div class="mt-4">
                        <!-- Campaign Header -->
                        <div class="flex items-center gap-4 mb-6">
                            <img src="{{ $donation->campaign->image_url ?? '/images/placeholder.jpg' }}" alt="{{ $donation->campaign->title }}" class="w-24 h-24 object-cover rounded-lg border">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-900">{{ $donation->campaign->title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $donation->campaign->organization }}</p>
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        @if($donation->campaign->status === 'active') bg-green-100 text-green-800
                                        @elseif($donation->campaign->status === 'completed') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($donation->campaign->status === 'active')
                                            <i data-lucide="play-circle" class="w-3 h-3 mr-1"></i>
                                            Aktif
                                        @elseif($donation->campaign->status === 'completed')
                                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                            Selesai
                                        @else
                                            <i data-lucide="pause-circle" class="w-3 h-3 mr-1"></i>
                                            {{ ucfirst($donation->campaign->status) }}
                                        @endif
                                    </span>
                                    @if($donation->campaign->category_id && $donation->campaign->category && is_object($donation->campaign->category))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i data-lucide="tag" class="w-3 h-3 mr-1"></i>
                                        {{ $donation->campaign->category->name }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Campaign Progress -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress Penggalangan Dana</span>
                                <span class="text-sm text-gray-500">
                                    {{ number_format(($donation->campaign->current_amount / $donation->campaign->target_amount) * 100, 1) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(($donation->campaign->current_amount / $donation->campaign->target_amount) * 100, 100) }}%"></div>
                            </div>
                        </div>

                        <!-- Campaign Stats -->
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-lg font-bold text-blue-600">
                                    Rp {{ number_format($donation->campaign->target_amount, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">Target</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-lg font-bold text-green-600">
                                    Rp {{ number_format($donation->campaign->current_amount, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">Terkumpul</div>
                            </div>
                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-lg font-bold text-purple-600">
                                    {{ $donation->campaign->donations()->count() }}
                                </div>
                                <div class="text-xs text-gray-500">Total Donatur</div>
                            </div>
                        </div>

                        <!-- Campaign Description -->
                        @if($donation->campaign->description)
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Deskripsi Campaign</h4>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ Str::limit($donation->campaign->description, 200) }}
                                @if(strlen($donation->campaign->description) > 200)
                                    <a href="{{ route('campaigns.show', $donation->campaign->slug) }}" target="_blank" class="text-blue-600 hover:underline">Baca selengkapnya</a>
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12">
                        <i data-lucide="alert-circle" class="w-16 h-16 mx-auto text-gray-300"></i>
                        <h3 class="mt-4 text-lg font-medium text-gray-800">Campaign Tidak Ditemukan</h3>
                        <p class="text-gray-500 mt-1">Campaign ini mungkin telah dihapus atau tidak tersedia.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Aksi -->
            @if ($donation->payment_status === 'waiting_confirmation')
                <div class="card p-6">
                    <h2 class="card-title">Tindakan</h2>
                    <p class="text-sm text-gray-500 mt-1 mb-4">Periksa bukti transfer dan konfirmasi pembayaran.</p>
                    <div class="space-y-3">
                        <form action="{{ route('admin.donations.update-status', $donation->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="payment_status" value="success">
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-3 font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-sm">
                                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                                Setujui Pembayaran
                            </button>
                        </form>
                        <form action="{{ route('admin.donations.update-status', $donation->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="payment_status" value="failed">
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-3 font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors duration-200 shadow-sm">
                                <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                                Tolak Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Detail Donasi -->
            <div class="card p-6">
                <h2 class="card-title">Detail Donasi</h2>
                <div class="mt-4 space-y-4">
                    <!-- Jumlah Donasi -->
                    <div class="text-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Jumlah Donasi</p>
                        <p class="text-2xl font-bold text-brand-blue">Rp {{ number_format($donation->amount, 0, ',', '.') }}</p>
                    </div>

                    <!-- Informasi Pembayaran -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Metode Pembayaran</p>
                            <p class="text-sm font-medium text-gray-900">
                                @switch($donation->payment_method)
                                    @case('bank_transfer')
                                        <span class="inline-flex items-center">
                                            <i data-lucide="building-2" class="w-4 h-4 mr-1 text-blue-600"></i>
                                            Transfer Bank
                                        </span>
                                        @break
                                    @case('tripay')
                                        <span class="inline-flex items-center">
                                            <i data-lucide="credit-card" class="w-4 h-4 mr-1 text-green-600"></i>
                                            Tripay
                                        </span>
                                        @break
                                    @default
                                        {{ ucfirst(str_replace('_', ' ', $donation->payment_method)) }}
                                @endswitch
                            </p>
                        </div>
                        
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Status Pembayaran</p>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                @if($donation->payment_status === 'success') bg-green-100 text-green-800
                                @elseif($donation->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($donation->payment_status === 'waiting_confirmation') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800 @endif">
                                @switch($donation->payment_status)
                                    @case('success')
                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                        Berhasil
                                        @break
                                    @case('pending')
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        Menunggu
                                        @break
                                    @case('waiting_confirmation')
                                        <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                        Menunggu Konfirmasi
                                        @break
                                    @case('failed')
                                        <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                        Gagal
                                        @break
                                    @default
                                        {{ ucfirst($donation->payment_status) }}
                                @endswitch
                            </span>
                        </div>
                    </div>

                    <!-- Informasi Waktu -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Tanggal Donasi</p>
                            <p class="text-sm font-medium text-gray-900">{{ $donation->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $donation->created_at->format('H:i') }} WIB</p>
                        </div>
                        
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Batas Waktu</p>
                            @if($donation->expired_at)
                                <p class="text-sm font-medium text-gray-900">{{ $donation->expired_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $donation->expired_at->format('H:i') }} WIB</p>
                            @else
                                <p class="text-sm text-gray-500">Tidak ada batas waktu</p>
                            @endif
                        </div>
                    </div>

                    <!-- Pesan Donatur -->
                    @if($donation->message)
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="message-square" class="w-4 h-4 text-yellow-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 mb-1">Pesan dari Donatur</p>
                                <p class="text-sm text-gray-700 italic">"{{ $donation->message }}"</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Informasi Tripay (jika menggunakan Tripay) -->
                    @if($donation->payment_method === 'tripay' && $donation->tripay_reference)
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="credit-card" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 mb-1">Informasi Tripay</p>
                                <div class="space-y-1 text-sm text-gray-700">
                                    <p><span class="font-medium">Reference ID:</span> {{ $donation->tripay_reference }}</p>
                                    @if($donation->tripay_merchant_ref)
                                    <p><span class="font-medium">Merchant Ref:</span> {{ $donation->tripay_merchant_ref }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

             <!-- Detail Donatur -->
            <div class="card p-6">
                <h2 class="card-title">Informasi Donatur</h2>
                <div class="mt-4">
                    <!-- Avatar dan Info Utama -->
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-xl font-bold">
                                {{ strtoupper(substr($donation->donor_name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $donation->donor_name }}</h3>
                            <p class="text-sm text-gray-500">
                                @if($donation->is_anonymous)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i data-lucide="eye-off" class="w-3 h-3 mr-1"></i>
                                        Donasi Anonim
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                        Donatur Terdaftar
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Informasi Kontak -->
                    <div class="space-y-4">
                        @if($donation->donor_email)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i data-lucide="mail" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm font-medium text-gray-900">{{ $donation->donor_email }}</p>
                            </div>
                            <a href="mailto:{{ $donation->donor_email }}" class="text-blue-600 hover:text-blue-800">
                                <i data-lucide="send" class="w-4 h-4"></i>
                            </a>
                        </div>
                        @endif

                        @if($donation->donor_whatsapp)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i data-lucide="phone" class="w-4 h-4 text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">WhatsApp</p>
                                <p class="text-sm font-medium text-gray-900">{{ $donation->donor_whatsapp }}</p>
                            </div>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $donation->donor_whatsapp) }}" target="_blank" class="text-green-600 hover:text-green-800">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Statistik Donatur -->
                    @if($donation->user)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Statistik Donatur</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-lg font-bold text-blue-600">
                                    {{ $donation->user->donations()->count() }}
                                </div>
                                <div class="text-xs text-gray-500">Total Donasi</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-lg font-bold text-green-600">
                                    Rp {{ number_format($donation->user->donations()->sum('amount'), 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">Total Nominal</div>
                            </div>
                        </div>
                        
                        <!-- Riwayat Donasi Terakhir -->
                        <div class="mt-4">
                            <h5 class="text-xs font-medium text-gray-600 mb-2">Riwayat Donasi Terakhir</h5>
                            <div class="space-y-2">
                                @foreach($donation->user->donations()->latest()->take(3)->get() as $prevDonation)
                                <div class="flex items-center justify-between p-2 bg-white rounded border">
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-gray-900">{{ $prevDonation->campaign->title ?? 'Campaign' }}</p>
                                        <p class="text-xs text-gray-500">{{ $prevDonation->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs font-medium text-gray-900">Rp {{ number_format($prevDonation->amount, 0, ',', '.') }}</p>
                                        <span class="inline-flex px-1.5 py-0.5 text-xs rounded-full
                                            @if($prevDonation->payment_status === 'success') bg-green-100 text-green-800
                                            @elseif($prevDonation->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($prevDonation->payment_status) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        @apply bg-white rounded-xl shadow-sm border border-gray-100;
    }
    .card-title {
        @apply text-lg font-semibold text-gray-800;
    }
    .badge-success { @apply inline-block px-3 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full; }
    .badge-info { @apply inline-block px-3 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded-full; }
    .badge-warning { @apply inline-block px-3 py-1 text-sm font-medium text-yellow-800 bg-yellow-100 rounded-full; }
    .badge-danger { @apply inline-block px-3 py-1 text-sm font-medium text-red-800 bg-red-100 rounded-full; }
    .badge-secondary { @apply inline-block px-3 py-1 text-sm font-medium text-gray-800 bg-gray-100 rounded-full; }
    .detail-list { @apply grid grid-cols-2 gap-x-4 gap-y-3 text-sm; }
    .detail-list li { @apply flex flex-col; }
    .detail-list span { @apply text-gray-500; }
    .detail-list strong { @apply text-gray-900 font-medium; }
    .btn-success { @apply flex items-center justify-center w-full px-4 py-2.5 font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors; }
    .btn-danger { @apply flex items-center justify-center w-full px-4 py-2.5 font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors; }
    
    /* Print Styles */
    @media print {
        body { 
            background: white !important; 
            color: black !important;
        }
        .card { 
            box-shadow: none !important; 
            border: 1px solid #ddd !important;
            break-inside: avoid;
        }
        .badge-success, .badge-info, .badge-warning, .badge-danger, .badge-secondary {
            background: #f3f4f6 !important;
            color: #374151 !important;
            border: 1px solid #d1d5db !important;
        }
        .bg-gradient-to-r, .bg-gray-50, .bg-blue-50, .bg-green-50, .bg-yellow-50, .bg-red-50 {
            background: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
        }
        .text-brand-blue { color: #1f2937 !important; }
        .text-blue-600 { color: #1f2937 !important; }
        .text-green-600 { color: #1f2937 !important; }
        .text-yellow-600 { color: #1f2937 !important; }
        .text-red-600 { color: #1f2937 !important; }
        
        /* Hide action buttons when printing */
        button, .flex.gap-2 { display: none !important; }
        
        /* Ensure proper page breaks */
        .grid { page-break-inside: avoid; }
        .space-y-8 > * { page-break-inside: avoid; }
    }
</style>
@endpush 
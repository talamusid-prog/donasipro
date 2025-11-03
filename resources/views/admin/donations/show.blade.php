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
                    <span class="badge-secondary">Unknown</span>
            @endswitch
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
                            // URL utama via Storage (gunakan disk 'public')
                            $storageUrl = \Storage::disk('public')->url($donation->payment_proof);
                            // Cek keberadaan file di disk publik
                            $fileExistsStorage = \Storage::disk('public')->exists($donation->payment_proof);
                            // Fallback ke serve-file.php untuk hosting yang tidak mendukung symlink
                            $fallbackUrl = url('/serve-file.php?file=' . ltrim($donation->payment_proof, '/'));
                        @endphp

                        @if($fileExistsStorage)
                            <a href="{{ $storageUrl }}" target="_blank">
                                <img src="{{ $storageUrl }}"
                                     alt="Bukti Pembayaran"
                                     class="max-w-md mx-auto rounded-lg border border-gray-200 hover:opacity-90 transition-opacity shadow-sm">
                            </a>
                            <p class="text-xs text-gray-500 mt-2 text-center">Klik gambar untuk melihat ukuran penuh</p>
                        @else
                            @php
                                // Coba deteksi keberadaan file di jalur alternatif untuk fallback
                                $altPathPublic = public_path('storage/' . ltrim($donation->payment_proof, '/'));
                                $altPathStorage = storage_path('app/public/' . ltrim($donation->payment_proof, '/'));
                                $altExists = file_exists($altPathPublic) || file_exists($altPathStorage);
                            @endphp
                            @if($altExists)
                                <a href="{{ $fallbackUrl }}" target="_blank">
                                    <img src="{{ $fallbackUrl }}"
                                         alt="Bukti Pembayaran"
                                         class="max-w-md mx-auto rounded-lg border border-gray-200 hover:opacity-90 transition-opacity shadow-sm">
                                </a>
                                <p class="text-xs text-gray-500 mt-2 text-center">Klik gambar untuk melihat ukuran penuh</p>
                            @else
                                <div class="text-center text-sm text-gray-500">
                                    Bukti pembayaran tidak ditemukan atau belum tersedia.
                                </div>
                            @endif
                        @endif
                    </div>
                @else
                    <p class="text-gray-500">Belum ada bukti pembayaran yang diunggah.</p>
                @endif
            </div>

            <!-- Detail Donasi -->
            <div class="card p-6">
                <h2 class="card-title">Detail Donasi</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Nama Donatur</div>
                        <div class="font-medium">{{ $donation->salutation }} {{ $donation->donor_name }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Email</div>
                        <div class="font-medium">{{ $donation->donor_email ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Nominal</div>
                        <div class="font-medium">Rp {{ number_format($donation->amount, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Metode Pembayaran</div>
                        <div class="font-medium">{{ $donation->payment_method }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-sm text-gray-500">Pesan</div>
                        <div class="font-medium">{{ $donation->message ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Campaign -->
            <div class="card p-6">
                <h2 class="card-title">Campaign</h2>
                @if ($donation->campaign)
                    <div class="flex items-center gap-4">
                        @if ($donation->campaign->image_url)
                            <img src="{{ $donation->campaign->image_url }}" alt="Campaign Image" class="w-24 h-24 object-cover rounded-lg">
                        @endif
                        <div>
                            <div class="font-bold">{{ $donation->campaign->title }}</div>
                            <div class="text-sm text-gray-500">Kategori: {{ $donation->campaign->category->name ?? '-' }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
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

            <!-- Info Donatur -->
            <div class="card p-6">
                <h2 class="card-title">Info Donatur</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Akun Pengguna</span>
                        <span class="font-medium">{{ $donation->user?->email ?? 'Guest' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Anonim</span>
                        <span class="font-medium">{{ $donation->is_anonymous ? 'Ya' : 'Tidak' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Donasi</span>
                        <span class="font-medium">{{ $donation->created_at->format('d M Y H:i') }}</span>
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
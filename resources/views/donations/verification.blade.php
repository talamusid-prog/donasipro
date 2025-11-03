@extends('layouts.app')

@section('title', 'Status Donasi')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    @if($donation->payment_status === 'success')
        <!-- Status Berhasil -->
        <div class="flex flex-col items-center mb-6">
            <div class="bg-green-100 rounded-full p-4 mb-3">
                <i data-lucide="check-circle" class="w-10 h-10 text-green-500"></i>
            </div>
            <h1 class="text-xl font-bold text-green-800 mb-1">Donasi Berhasil Diverifikasi!</h1>
            <p class="text-center text-gray-700">Selamat! Pembayaran Anda telah berhasil diverifikasi. Terima kasih atas donasi Anda.</p>
        </div>
    @elseif($donation->payment_status === 'failed')
        <!-- Status Ditolak -->
        <div class="flex flex-col items-center mb-6">
            <div class="bg-red-100 rounded-full p-4 mb-3">
                <i data-lucide="x-circle" class="w-10 h-10 text-red-500"></i>
            </div>
            <h1 class="text-xl font-bold text-red-800 mb-1">Donasi Ditolak</h1>
            <p class="text-center text-gray-700">Mohon maaf, bukti pembayaran Anda tidak dapat diverifikasi. Silakan coba lagi atau hubungi kami.</p>
        </div>
    @elseif($donation->payment_status === 'waiting_confirmation')
        <!-- Status Menunggu Konfirmasi -->
        <div class="flex flex-col items-center mb-6">
            <div class="bg-yellow-100 rounded-full p-4 mb-3">
                <i data-lucide="clock" class="w-10 h-10 text-yellow-500"></i>
            </div>
            <h1 class="text-xl font-bold text-yellow-800 mb-1">Donasi Dalam Proses Verifikasi</h1>
            <p class="text-center text-gray-700">Terima kasih telah melakukan pembayaran. Bukti pembayaran Anda telah kami terima dan sedang dalam proses verifikasi oleh tim kami.</p>
        </div>
    @else
        <!-- Status Pending -->
        <div class="flex flex-col items-center mb-6">
            <div class="bg-blue-100 rounded-full p-4 mb-3">
                <i data-lucide="alert-circle" class="w-10 h-10 text-blue-500"></i>
            </div>
            <h1 class="text-xl font-bold text-blue-800 mb-1">Menunggu Pembayaran</h1>
            <p class="text-center text-gray-700">Silakan selesaikan pembayaran Anda untuk melanjutkan proses donasi.</p>
        </div>
    @endif

    <!-- Ringkasan Donasi -->
    <div class="bg-white rounded-xl p-4 mb-6 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <img src="{{ $campaign->image_url ?? '/images/placeholder.jpg' }}" alt="{{ $campaign->title }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200" onerror="this.src='/images/placeholder.jpg'">
            <div>
                <div class="text-xs text-gray-500 mb-1">Program:</div>
                <div class="font-semibold text-gray-900 leading-tight">{{ $campaign->title }}</div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-y-2 text-sm">
            <div class="text-gray-500">Nama Donatur</div>
            <div class="font-semibold text-right">{{ $donation->salutation }} {{ $donation->donor_name }}</div>
            <div class="text-gray-500">Jumlah Donasi</div>
            <div class="font-semibold text-right text-brand-blue">Rp {{ number_format($donation->amount, 0, ',', '.') }}</div>
            <div class="text-gray-500">Metode Pembayaran</div>
            <div class="font-semibold text-right">{{ $donation->payment_method }}</div>
            <div class="text-gray-500">Status</div>
            <div class="font-semibold text-right">
                @if($donation->payment_status === 'success')
                    <span class="text-green-600">Berhasil</span>
                @elseif($donation->payment_status === 'failed')
                    <span class="text-red-600">Ditolak</span>
                @elseif($donation->payment_status === 'waiting_confirmation')
                    <span class="text-yellow-600">Menunggu Verifikasi</span>
                @else
                    <span class="text-blue-600">Menunggu Pembayaran</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Instruksi & Info berdasarkan status -->
    @if($donation->payment_status === 'success')
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-2 mb-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mt-0.5"></i>
                <span class="font-medium text-green-800">Donasi Berhasil!</span>
            </div>
            <ul class="text-sm text-green-700 space-y-1">
                <li>• Donasi Anda telah berhasil diverifikasi dan diterima.</li>
                <li>• Dana akan disalurkan sesuai dengan tujuan campaign.</li>
                <li>• Terima kasih atas kontribusi Anda untuk kebaikan.</li>
            </ul>
        </div>
    @elseif($donation->payment_status === 'failed')
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-2 mb-2">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mt-0.5"></i>
                <span class="font-medium text-red-800">Verifikasi Gagal</span>
            </div>
            <ul class="text-sm text-red-700 space-y-1">
                <li>• Bukti pembayaran tidak dapat diverifikasi.</li>
                <li>• Pastikan bukti pembayaran jelas dan lengkap.</li>
                <li>• Silakan hubungi kami untuk bantuan lebih lanjut.</li>
            </ul>
        </div>
    @elseif($donation->payment_status === 'waiting_confirmation')
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-2 mb-2">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                <span class="font-medium text-blue-800">Apa yang terjadi selanjutnya?</span>
            </div>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Tim kami akan memverifikasi bukti pembayaran Anda dalam waktu maksimal <b>1x24 jam</b>.</li>
                <li>• Status donasi akan diperbarui secara otomatis setelah verifikasi berhasil.</li>
                <li>• Anda akan menerima notifikasi melalui email/WhatsApp jika donasi sudah diverifikasi.</li>
            </ul>
        </div>
    @else
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-2 mb-2">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                <span class="font-medium text-blue-800">Langkah Selanjutnya</span>
            </div>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Silakan selesaikan pembayaran sesuai instruksi yang diberikan.</li>
                <li>• Upload bukti pembayaran setelah transfer selesai.</li>
                <li>• Tim kami akan memverifikasi pembayaran Anda.</li>
            </ul>
        </div>
    @endif

    <!-- Tombol Aksi -->
    <div class="flex flex-col gap-3">
        @if($donation->payment_status === 'success')
            <a href="{{ route('campaigns.show', $campaign->slug) }}" class="w-full bg-green-600 text-white py-3 px-4 rounded-lg text-center font-semibold hover:bg-green-700 transition-colors">
                Lihat Campaign
            </a>
        @elseif($donation->payment_status === 'failed')
            <a href="{{ route('campaigns.donate', $campaign->slug) }}" class="w-full bg-brand-blue text-white py-3 px-4 rounded-lg text-center font-semibold hover:bg-blue-600 transition-colors">
                Coba Lagi
            </a>
        @elseif($donation->payment_status === 'waiting_confirmation')
            <a href="{{ route('my-donations') }}" class="w-full bg-brand-blue text-white py-3 px-4 rounded-lg text-center font-semibold hover:bg-blue-600 transition-colors">
                Lihat Donasi Saya
            </a>
        @else
            <a href="{{ route('donations.payment-detail', $donation->id) }}" class="w-full bg-brand-blue text-white py-3 px-4 rounded-lg text-center font-semibold hover:bg-blue-600 transition-colors">
                Lanjutkan Pembayaran
            </a>
        @endif
        
        <a href="{{ route('home') }}" class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg text-center font-semibold hover:bg-gray-200 transition-colors">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection 
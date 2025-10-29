@extends('layouts.app')

@section('title', 'Upload Bukti Pembayaran')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-md">
    <!-- Header -->
    <div class="flex items-center mb-4">
        <a href="{{ url()->previous() }}" class="mr-2 text-gray-700 hover:text-brand-blue">
            <i data-lucide="chevron-left" class="w-6 h-6"></i>
        </a>
        <h1 class="text-lg font-semibold">Upload Bukti Pembayaran</h1>
    </div>

    <!-- Program Info -->
    <div class="flex items-center gap-3 bg-white rounded-xl p-4 mb-4 border border-gray-100">
        <img src="{{ $campaign->image_url ?? '/images/placeholder.jpg' }}" alt="{{ $campaign->title }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200" onerror="this.src='/images/placeholder.jpg'">
        <div class="flex-1">
            <div class="text-xs text-gray-500 mb-1">Program:</div>
            <div class="font-semibold text-gray-900 leading-tight">{{ $campaign->title }}</div>
        </div>
    </div>

    <!-- Status Pembayaran -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
        <div class="flex items-center gap-2 mb-2">
            <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
            <span class="font-semibold text-yellow-800">Menunggu Konfirmasi</span>
        </div>
        <p class="text-sm text-yellow-700">
            Pembayaran Anda sedang menunggu verifikasi. Upload bukti pembayaran untuk mempercepat proses verifikasi.
        </p>
        
        <!-- Countdown Timer -->
        @if($donation->expired_at && now() < $donation->expired_at)
        <div class="mt-3 p-3 bg-white rounded-lg border border-yellow-300">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="timer" class="w-4 h-4 text-yellow-600"></i>
                <span class="text-sm font-medium text-yellow-800">Batas Waktu Pembayaran:</span>
            </div>
            <div id="countdown-timer" class="text-lg font-mono font-bold text-yellow-700"></div>
        </div>
        @endif
    </div>

    <!-- Informasi Donasi -->
    <div class="bg-white rounded-xl p-4 mb-4 border border-gray-100">
        <div class="text-center font-semibold text-lg mb-4">Informasi Donasi</div>
        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <div class="text-gray-500 text-sm">Nama Donatur</div>
                <div class="font-semibold text-gray-900 text-sm text-right max-w-[60%] break-words">
                    {{ $donation->salutation }} {{ $donation->donor_name }}
                </div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <div class="text-gray-500 text-sm">Jumlah Donasi</div>
                <div class="font-semibold text-brand-blue text-sm">Rp {{ number_format($donation->amount, 0, ',', '.') }}</div>
            </div>
            <div class="flex justify-between items-start py-2 border-b border-gray-100">
                <div class="text-gray-500 text-sm">Email</div>
                <div class="font-semibold text-gray-900 text-sm text-right max-w-[60%] break-all">
                    {{ $donation->donor_email }}
                </div>
            </div>
            <div class="flex justify-between items-center py-2">
                <div class="text-gray-500 text-sm">No. Telepon</div>
                <div class="font-semibold text-gray-900 text-sm text-right">
                    {{ $donation->donor_whatsapp }}
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Bukti Pembayaran -->
    <div class="bg-white rounded-xl p-4 mb-4 border border-gray-100">
        <div class="text-center font-semibold text-lg mb-4">Upload Bukti Pembayaran</div>
        
        <!-- Instruksi -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <div class="flex items-start gap-2 mb-2">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                <span class="font-medium text-blue-800">Instruksi Upload</span>
            </div>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Pastikan bukti pembayaran jelas dan lengkap</li>
                <li>• Format file: JPG, PNG, atau PDF (max 2MB)</li>
                <li>• Bukti harus menampilkan nominal dan waktu transfer</li>
                <li>• Verifikasi akan dilakukan dalam 1x24 jam</li>
            </ul>
        </div>

        <!-- Form Upload -->
        <form action="{{ route('donations.upload-proof', $donation->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <!-- File Upload Area -->
                <div>
                    <label for="payment_proof_input" class="block border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-brand-blue transition">
                        <div class="mb-2">
                            <i data-lucide="upload" class="w-10 h-10 text-gray-400 mx-auto"></i>
                        </div>
                        <div id="file-upload-prompt">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium text-brand-blue">Klik untuk upload</span> atau drag & drop
                            </p>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, atau PDF (max 2MB)</p>
                        </div>
                        <div id="file-name-display" class="hidden text-sm font-medium text-gray-800 truncate"></div>
                    </label>
                    <input type="file" name="payment_proof" id="payment_proof_input" class="hidden" accept="image/*,.pdf" required onchange="displayFileName(this)">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full btn-primary py-3">
                    Upload Bukti Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Copy to clipboard function
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('Berhasil disalin!', 'success');
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showNotification('Berhasil disalin!', 'success');
        } else {
            showNotification('Gagal menyalin!', 'error');
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        showNotification('Gagal menyalin!', 'error');
    }
    
    document.body.removeChild(textArea);
}

function showNotification(message, type) {
    const bgColor = type === 'success' ? '#10b981' : '#ef4444';
    const messageEl = document.createElement('div');
    messageEl.textContent = message;
    messageEl.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(messageEl);
    
    setTimeout(() => {
        messageEl.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            if (messageEl.parentNode) {
                document.body.removeChild(messageEl);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Countdown Timer
const countdownEl = document.getElementById('countdown-timer');
if (countdownEl) {
    @if($donation->expired_at)
    const expiredAt = new Date("{{ \Carbon\Carbon::parse($donation->expired_at)->toIso8601String() }}").getTime();
    function updateCountdown() {
        const now = new Date().getTime();
        let distance = expiredAt - now;
        if (distance < 0) {
            countdownEl.textContent = 'Waktu pembayaran telah habis';
            countdownEl.classList.add('text-red-600');
            clearInterval(timerInterval);
            return;
        }
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        countdownEl.textContent = `${hours.toString().padStart(2, '0')}:${minutes
            .toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    const timerInterval = setInterval(updateCountdown, 1000);
    updateCountdown();
    @endif
}

function displayFileName(input) {
    const promptEl = document.getElementById('file-upload-prompt');
    const displayEl = document.getElementById('file-name-display');
    
    if (input.files && input.files.length > 0) {
        promptEl.classList.add('hidden');
        displayEl.textContent = input.files[0].name;
        displayEl.classList.remove('hidden');
    } else {
        promptEl.classList.remove('hidden');
        displayEl.classList.add('hidden');
    }
}
</script>

@endsection 
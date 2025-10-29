@extends('layouts.app')

@section('title', 'Pembayaran Donasi')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="card p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Donasi Berhasil Dibuat</h1>
            <p class="text-gray-600">Silakan selesaikan pembayaran untuk menyelesaikan donasi Anda</p>
        </div>

        <!-- Donation Details -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">Detail Donasi</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">ID Donasi</span>
                    <span class="font-medium">#{{ $donation->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Kampanye</span>
                    <span class="font-medium">{{ $donation->campaign->title }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Jumlah Donasi</span>
                    <span class="font-medium text-lg text-brand-blue">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode Pembayaran</span>
                    <span class="font-medium">
                        @if(str_starts_with($donation->payment_method, 'manual_'))
                            @php
                                $bankName = str_replace('manual_', '', $donation->payment_method);
                                $bankAccount = $bankAccounts->where('bank_name', ucfirst($bankName))->first();
                                if (!$bankAccount) {
                                    $bankAccount = $bankAccounts->where('bank_name', strtoupper($bankName))->first();
                                }
                                if (!$bankAccount) {
                                    $bankAccount = $bankAccounts->where('bank_name', strtolower($bankName))->first();
                                }
                                if (!$bankAccount) {
                                    $bankAccount = $bankAccounts->first(function($bank) use ($bankName) {
                                        return strtolower($bank->bank_name) === strtolower($bankName);
                                    });
                                }
                            @endphp
                            @if($bankAccount)
                                {{ $bankAccount->bank_name }} Transfer Manual
                            @else
                                {{ ucfirst($bankName) }} Transfer Manual
                            @endif
                        @elseif(str_starts_with($donation->payment_method, 'tripay_'))
                            @php
                                $vaCode = strtoupper(str_replace('tripay_', '', $donation->payment_method));
                                $tripayChannel = \App\Models\TripayChannel::where('code', $vaCode)->first();
                            @endphp
                            @if($tripayChannel)
                                {{ $tripayChannel->name }}
                            @else
                                {{ $vaCode }} Virtual Account
                            @endif
                        @else
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
                                @default
                                    {{ $donation->payment_method }}
                            @endswitch
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    <span class="font-medium">
                        @if($donation->payment_status === 'pending')
                            <span class="text-yellow-600">Menunggu Pembayaran</span>
                        @elseif($donation->payment_status === 'success')
                            <span class="text-green-600">Berhasil</span>
                        @else
                            <span class="text-red-600">Gagal</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Payment Instructions -->
        <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">Detail Pembayaran</h3>
            
            @if(str_starts_with($donation->payment_method, 'manual_'))
                @php
                    $bankName = str_replace('manual_', '', $donation->payment_method);
                    // Try different case variations to find the bank account
                    $bankAccount = $bankAccounts->where('bank_name', ucfirst($bankName))->first();
                    if (!$bankAccount) {
                        $bankAccount = $bankAccounts->where('bank_name', strtoupper($bankName))->first();
                    }
                    if (!$bankAccount) {
                        $bankAccount = $bankAccounts->where('bank_name', strtolower($bankName))->first();
                    }
                    if (!$bankAccount) {
                        // Try to find by partial match
                        $bankAccount = $bankAccounts->first(function($bank) use ($bankName) {
                            return strtolower($bank->bank_name) === strtolower($bankName);
                        });
                    }
                @endphp
                @if($bankAccount)
                    <div class="space-y-3">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Bank</div>
                            <div class="flex items-center gap-2">
                                @if($bankAccount->logo)
                                    <img src="{{ Storage::url($bankAccount->logo) }}" alt="{{ $bankAccount->bank_name }}" class="w-6 h-6 object-contain">
                                @else
                                    <i data-lucide="building-2" class="w-6 h-6 text-gray-400"></i>
                                @endif
                                <span class="font-medium">{{ $bankAccount->bank_name }}</span>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Nomor Rekening</div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-base select-all">{{ $bankAccount->account_number }}</span>
                                <button type="button" onclick="copyToClipboard('{{ $bankAccount->account_number }}')" class="p-2 rounded hover:bg-gray-200" title="Salin">
                                    <i data-lucide="copy" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Atas Nama</div>
                            <div class="font-medium">{{ $bankAccount->account_holder }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Jumlah yang Harus Dibayar</div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-lg">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
                                <button type="button" onclick="copyToClipboard('{{ number_format($donation->amount, 0, ',', '.') }}')" class="p-2 rounded hover:bg-gray-200" title="Salin nominal">
                                    <i data-lucide="copy" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Batas Waktu Pembayaran</div>
                            <div class="font-medium">
                                <span id="countdown-timer"></span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-red-800">Rekening {{ ucfirst($bankName) }} tidak tersedia saat ini.</p>
                    </div>
                @endif
            @elseif($donation->payment_method === 'bank_transfer')
                @php
                    $firstBankAccount = $bankAccounts->first();
                @endphp
                @if($firstBankAccount)
                    <div class="space-y-3">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Bank</div>
                            <div class="flex items-center gap-2">
                                @if($firstBankAccount->logo)
                                    <img src="{{ Storage::url($firstBankAccount->logo) }}" alt="{{ $firstBankAccount->bank_name }}" class="w-6 h-6 object-contain">
                                @else
                                    <i data-lucide="building-2" class="w-6 h-6 text-gray-400"></i>
                                @endif
                                <span class="font-medium">{{ $firstBankAccount->bank_name }}</span>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Nomor Rekening</div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-base select-all">{{ $firstBankAccount->account_number }}</span>
                                <button type="button" onclick="copyToClipboard('{{ $firstBankAccount->account_number }}')" class="p-2 rounded hover:bg-gray-200" title="Salin">
                                    <i data-lucide="copy" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Atas Nama</div>
                            <div class="font-medium">{{ $firstBankAccount->account_holder }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Jumlah yang Harus Dibayar</div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-lg">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
                                <button type="button" onclick="copyToClipboard('{{ number_format($donation->amount, 0, ',', '.') }}')" class="p-2 rounded hover:bg-gray-200" title="Salin nominal">
                                    <i data-lucide="copy" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Batas Waktu Pembayaran</div>
                            <div class="font-medium">
                                <span id="countdown-timer"></span>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif(str_starts_with($donation->payment_method, 'tripay_'))
                @php
                    $vaCode = strtoupper(str_replace('tripay_', '', $donation->payment_method));
                    $tripayInfo = $tripayData && isset($tripayData['data']) ? $tripayData['data'] : null;
                @endphp
                <div class="space-y-3">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="credit-card" class="w-5 h-5 text-blue-600"></i>
                            <span class="font-semibold text-blue-900">Virtual Account {{ $vaCode }}</span>
                        </div>
                        <p class="text-sm text-blue-700">Pembayaran otomatis melalui Virtual Account</p>
                    </div>
                    
                    @if($tripayInfo && isset($tripayInfo['pay_code']))
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Kode Virtual Account</div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-base font-semibold select-all">{{ $tripayInfo['pay_code'] }}</span>
                                <button type="button" onclick="copyToClipboard('{{ $tripayInfo['pay_code'] }}')" class="p-2 rounded hover:bg-gray-200" title="Salin kode VA">
                                    <i data-lucide="copy" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    @elseif($tripayData && isset($tripayData['data']['pay_code']))
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Kode Virtual Account</div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-base font-semibold select-all">{{ $tripayData['data']['pay_code'] }}</span>
                                <button type="button" onclick="copyToClipboard('{{ $tripayData['data']['pay_code'] }}')" class="p-2 rounded hover:bg-gray-200" title="Salin kode VA">
                                    <i data-lucide="copy" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start gap-2">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-yellow-600 mt-0.5"></i>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-medium">Kode Virtual Account sedang diproses...</p>
                                    <p class="text-xs mt-1">Silakan refresh halaman dalam beberapa saat.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-xs text-gray-500 mb-1">Jumlah yang Harus Dibayar</div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-semibold text-lg">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
                            <button type="button" onclick="copyToClipboard('{{ number_format($donation->amount, 0, ',', '.') }}')" class="p-2 rounded hover:bg-gray-200" title="Salin nominal">
                                <i data-lucide="copy" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                    
                    @if($tripayInfo && isset($tripayInfo['expired_time']))
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Batas Waktu Pembayaran</div>
                            <div class="font-medium">
                                {{ \Carbon\Carbon::createFromTimestamp($tripayInfo['expired_time'])->format('d M Y H:i') }}
                            </div>
                        </div>
                    @elseif($tripayData && isset($tripayData['data']['expired_time']))
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Batas Waktu Pembayaran</div>
                            <div class="font-medium">
                                {{ \Carbon\Carbon::createFromTimestamp($tripayData['data']['expired_time'])->format('d M Y H:i') }}
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 mb-1">Batas Waktu Pembayaran</div>
                            <div class="font-medium">
                                {{ $donation->expired_at ? $donation->expired_at->format('d M Y H:i') : '24 jam dari sekarang' }}
                            </div>
                        </div>
                    @endif
                    
                    @if($paymentInstructions && isset($paymentInstructions['data']) && !empty($paymentInstructions['data']))
                        <div class="space-y-3">
                            <h4 class="font-semibold text-gray-900 mb-3">Cara Pembayaran</h4>
                            @foreach($paymentInstructions['data'] as $index => $instruction)
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                    <button 
                                        class="w-full px-4 py-3 text-left bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex items-center justify-between"
                                        onclick="toggleInstruction({{ $index }})"
                                        type="button"
                                    >
                                        <div class="flex items-center gap-3">
                                            @if(str_contains(strtolower($instruction['title']), 'internet'))
                                                <i data-lucide="monitor" class="w-5 h-5 text-blue-600"></i>
                                            @elseif(str_contains(strtolower($instruction['title']), 'mobile') || str_contains(strtolower($instruction['title']), 'app'))
                                                <i data-lucide="smartphone" class="w-5 h-5 text-green-600"></i>
                                            @elseif(str_contains(strtolower($instruction['title']), 'atm'))
                                                <i data-lucide="credit-card" class="w-5 h-5 text-purple-600"></i>
                                            @else
                                                <i data-lucide="help-circle" class="w-5 h-5 text-gray-600"></i>
                                            @endif
                                            <span class="font-medium text-gray-900">{{ $instruction['title'] }}</span>
                                        </div>
                                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500 transition-transform duration-200" id="chevron-{{ $index }}"></i>
                                    </button>
                                    <div class="hidden px-4 py-3 border-t border-gray-200" id="instruction-{{ $index }}">
                                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                                            @foreach($instruction['steps'] as $step)
                                                <li class="leading-relaxed">
                                                    @php
                                                        // Prioritas: 1. tripayData, 2. tripayInfo, 3. N/A
                                                        $payCode = 'N/A';
                                                        if ($tripayData && isset($tripayData['data']['pay_code'])) {
                                                            $payCode = $tripayData['data']['pay_code'];
                                                        } elseif ($tripayInfo && isset($tripayInfo['pay_code'])) {
                                                            $payCode = $tripayInfo['pay_code'];
                                                        }
                                                    @endphp
                                                    {!! str_replace('{{pay_code}}', $payCode, $step) !!}
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif($tripayData && isset($tripayData['data']['instructions']) && !empty($tripayData['data']['instructions']))
                        <div class="space-y-4">
                            @foreach($tripayData['data']['instructions'] as $instruction)
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                        @if(str_contains(strtolower($instruction['title']), 'internet'))
                                            <i data-lucide="monitor" class="w-4 h-4 text-blue-600"></i>
                                        @elseif(str_contains(strtolower($instruction['title']), 'mobile') || str_contains(strtolower($instruction['title']), 'app'))
                                            <i data-lucide="smartphone" class="w-4 h-4 text-green-600"></i>
                                        @elseif(str_contains(strtolower($instruction['title']), 'atm'))
                                            <i data-lucide="credit-card" class="w-4 h-4 text-purple-600"></i>
                                        @else
                                            <i data-lucide="help-circle" class="w-4 h-4 text-gray-600"></i>
                                        @endif
                                        {{ $instruction['title'] }}
                                    </h4>
                                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                                        @foreach($instruction['steps'] as $step)
                                            <li class="leading-relaxed">
                                                @php
                                                    $payCode = $tripayData['data']['pay_code'] ?? 'N/A';
                                                @endphp
                                                {!! str_replace('{{pay_code}}', $payCode, $step) !!}
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start gap-2">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-yellow-600 mt-0.5"></i>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-medium mb-1">Cara Pembayaran:</p>
                                    @php
                                        // Prioritas: 1. tripayData, 2. tripayInfo, 3. N/A
                                        $payCode = 'N/A';
                                        if ($tripayData && isset($tripayData['data']['pay_code'])) {
                                            $payCode = $tripayData['data']['pay_code'];
                                        } elseif ($tripayInfo && isset($tripayInfo['pay_code'])) {
                                            $payCode = $tripayInfo['pay_code'];
                                        }
                                    @endphp
                                    <ol class="list-decimal list-inside space-y-1 text-xs">
                                        <li>Buka aplikasi m-banking atau internet banking {{ $vaCode }}</li>
                                        <li>Pilih menu "Transfer" atau "Pembayaran"</li>
                                        <li>Pilih "Virtual Account"</li>
                                        <li>Masukkan kode VA: <strong>{{ $payCode }}</strong></li>
                                        <li>Masukkan nominal: <strong>Rp {{ number_format($donation->amount, 0, ',', '.') }}</strong></li>
                                        <li>Konfirmasi dan selesaikan pembayaran</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-600">Metode pembayaran tidak dikenali.</p>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <a href="{{ route('campaigns.show', $donation->campaign->slug) }}" 
               class="flex-1 btn-secondary text-center">
                Kembali
            </a>
            <a href="{{ route('donations.payment-detail', $donation->id) }}" 
               class="flex-1 btn-primary text-center">
                Cek Status
            </a>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    // Try to use the modern Clipboard API
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showCopySuccess();
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
            fallbackCopyTextToClipboard(text);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    // Avoid scrolling to bottom
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
            showCopySuccess();
        } else {
            showCopyError();
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        showCopyError();
    }
    
    document.body.removeChild(textArea);
}

function showCopySuccess() {
    // Create a temporary success message
    const message = document.createElement('div');
    message.textContent = 'Berhasil disalin!';
    message.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(message);
    
    // Remove message after 2 seconds
    setTimeout(() => {
        message.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            if (message.parentNode) {
                document.body.removeChild(message);
            }
        }, 300);
    }, 2000);
}

function showCopyError() {
    // Create a temporary error message
    const message = document.createElement('div');
    message.textContent = 'Gagal menyalin!';
    message.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #ef4444;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(message);
    
    // Remove message after 2 seconds
    setTimeout(() => {
        message.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            if (message.parentNode) {
                document.body.removeChild(message);
            }
        }, 300);
    }, 2000);
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
}

function toggleInstruction(index) {
    const instructionDiv = document.getElementById(`instruction-${index}`);
    const chevron = document.getElementById(`chevron-${index}`);
    
    if (instructionDiv.classList.contains('hidden')) {
        instructionDiv.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
    } else {
        instructionDiv.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
    }
}

// Auto expand first instruction
document.addEventListener('DOMContentLoaded', function() {
    const firstInstruction = document.getElementById('instruction-0');
    const firstChevron = document.getElementById('chevron-0');
    if (firstInstruction) {
        firstInstruction.classList.remove('hidden');
        firstChevron.style.transform = 'rotate(180deg)';
    }
});
</script>

@endsection

@push('styles')
<style>
.card {
    @apply bg-white rounded-lg shadow-sm border border-gray-200;
}
.form-label {
    @apply block text-sm font-medium text-gray-700 mb-2;
}
.input-field {
    @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent;
}
.btn-primary {
    @apply bg-brand-blue hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
}
.btn-secondary {
    @apply bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
}
</style>
@endpush

@extends('layouts.app')

@section('title', 'Donasi Zakat Mal')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">
    <form method="POST" action="{{ route('campaigns.donate.store', 'zakat-mal') }}" id="donation-form">
        @csrf
        <input type="hidden" name="zakat_type" value="mal">

        <!-- Campaign Header -->
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-600">Anda akan berdonasi dalam program:</p>
                <h1 class="font-semibold text-gray-900">Zakat Mal</h1>
                <p class="text-xs text-gray-500">Disalurkan kepada mustahik yang berhak menerima zakat</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Error Display -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Ada kesalahan dalam form:
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Donation Amount -->
            <div>
                <label class="form-label text-center block mb-4">Jumlah Zakat Mal</label>
                <div>
                    <label for="amount" class="form-label">Masukkan jumlah zakat</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            Rp
                        </span>
                        <input
                            type="text"
                            id="amount"
                            name="amount"
                            class="input-field w-full pl-9 text-lg"
                            placeholder="0"
                            required
                            value="{{ $amount ? number_format($amount, 0, ',', '.') : '' }}"
                        >
                    </div>
                </div>
            </div>

            <!-- Salutation -->
            <div>
                <label class="form-label">Sapaan</label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" class="sapaan-btn" data-value="Bapak">Bapak</button>
                    <button type="button" class="sapaan-btn" data-value="Ibu">Ibu</button>
                    <button type="button" class="sapaan-btn" data-value="Saudara">Saudara</button>
                </div>
                <input type="hidden" name="salutation" id="salutation">
            </div>

            <!-- Donor Name -->
            <div>
                <label for="donor_name" class="form-label">Nama Lengkap</label>
                <input type="text" id="donor_name" name="donor_name" class="input-field" placeholder="Masukkan nama lengkap" value="{{ old('donor_name') }}">
                <div class="flex items-center mt-3">
                    <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1" class="rounded border-gray-300 text-brand-blue shadow-sm focus:border-brand-blue focus:ring focus:ring-brand-blue focus:ring-opacity-50" {{ old('is_anonymous') ? 'checked' : '' }}>
                    <label for="is_anonymous" class="ml-2 text-sm text-gray-700">Sembunyikan nama saya (Hamba Allah)</label>
                </div>
            </div>

            <!-- WhatsApp -->
            <div>
                <label for="donor_whatsapp" class="form-label">WhatsApp/No. HP</label>
                <input type="tel" id="donor_whatsapp" name="donor_whatsapp" class="input-field" placeholder="Contoh: 08123456789" value="{{ old('donor_whatsapp') }}">
            </div>

            <!-- Email -->
            <div>
                <label for="donor_email" class="form-label">Email (Opsional)</label>
                <input type="email" id="donor_email" name="donor_email" class="input-field" placeholder="Masukkan email" value="{{ old('donor_email') }}">
            </div>

            <!-- Message -->
            <div>
                <label for="message" class="form-label">Pesan/Doa (Opsional)</label>
                <textarea id="message" name="message" rows="3" class="input-field" placeholder="Tulis pesan atau doa">{{ old('message') }}</textarea>
            </div>

            <!-- Payment Method (Popup Manual Only) -->
            <div>
                <label class="form-label">Pilih Metode Pembayaran</label>
                <button type="button" id="open-payment-modal" class="w-full payment-method-trigger flex items-center justify-between border border-gray-300 rounded-lg px-4 py-3 bg-white text-base font-medium focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    <span id="selected-payment-method">
                        @if($bankAccounts->count() > 0)
                            @if($bankAccounts->first()->logo)
                                <img src="{{ Storage::url($bankAccounts->first()->logo) }}" alt="{{ $bankAccounts->first()->bank_name }}" class="inline w-5 h-5 mr-3 object-contain">
                            @else
                                <i data-lucide="building-2" class="inline w-5 h-5 mr-3 text-gray-400"></i>
                            @endif
                            {{ $bankAccounts->first()->bank_name }} Transfer Manual
                        @else
                            <i data-lucide="building-2" class="inline w-5 h-5 mr-3 text-gray-400"></i>
                            Pilih Bank
                        @endif
                    </span>
                    <i data-lucide="chevron-down" class="w-5 h-5 ml-2"></i>
                </button>
                <input type="hidden" name="payment_method" id="payment_method" value="{{ $bankAccounts->count() > 0 ? 'manual_' . strtolower($bankAccounts->first()->bank_name) : '' }}">
                @error('payment_method')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Modal Payment Method Manual Only -->
            <div id="payment-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6 relative max-h-[90vh] overflow-y-auto">
                    <button type="button" id="close-payment-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                    <div class="text-lg font-semibold mb-6">Pilih Metode Pembayaran</div>
                    <div class="mb-4">
                        <div class="font-semibold mb-4 text-gray-700">Transfer Manual</div>
                        <div class="space-y-3">
                            @forelse($bankAccounts as $bank)
                                <button type="button" 
                                        class="payment-method-card-modal flex items-center gap-4 p-4" 
                                        style="border-radius: 1rem;"
                                        data-value="manual_{{ strtolower($bank->bank_name) }}" 
                                        data-label="{{ $bank->bank_name }} Transfer Manual" 
                                        data-logo="{{ $bank->logo ? Storage::url($bank->logo) : '' }}"
                                        data-bank-name="{{ $bank->bank_name }}">
                                    @if($bank->logo)
                                        <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg border border-gray-100 flex items-center justify-center">
                                            <img src="{{ Storage::url($bank->logo) }}" alt="{{ $bank->bank_name }}" class="w-8 h-8 object-contain">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i data-lucide="building-2" class="w-5 h-5 text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1 text-left">
                                        <span class="font-medium text-gray-900">{{ $bank->bank_name }} Transfer Manual</span>
                                    </div>
                                </button>
                            @empty
                                <div class="text-center py-4 text-gray-500">
                                    <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2 text-gray-400"></i>
                                    <p>Tidak ada rekening bank yang tersedia</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="font-semibold mb-4 text-gray-700">Virtual Account (Otomatis)</div>
                        <div class="space-y-3">
                            @forelse($tripayChannels as $channel)
                                <button type="button"
                                        class="payment-method-card-modal flex items-center gap-4 p-4"
                                        style="border-radius: 1rem;"
                                        data-value="tripay_{{ strtolower($channel->code) }}"
                                        data-label="{{ $channel->name }}"
                                        data-logo="{{ $channel->icon_url }}"
                                        data-bank-name="{{ $channel->name }}">
                                    @if($channel->icon_url)
                                        <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg border border-gray-100 flex items-center justify-center">
                                            <img src="{{ $channel->icon_url }}" alt="{{ $channel->name }}" class="w-8 h-8 object-contain">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg border border-gray-100 flex items-center justify-center">
                                            <i data-lucide="credit-card" class="w-5 h-5 text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1 text-left">
                                        <span class="font-medium text-gray-900">{{ $channel->name }}</span>
                                    </div>
                                </button>
                            @empty
                                <div class="text-center py-4 text-gray-500">
                                    <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2 text-gray-400"></i>
                                    <p>Tidak ada channel Virtual Account yang tersedia</p>
                                    <p class="text-xs mt-1">Silakan aktifkan channel di admin panel</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-orange-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-orange-700 transition-colors">
                Lanjutkan ke Pembayaran
            </button>
        </div>
    </form>

    <!-- Back Button -->
    <div class="text-center mt-6">
        <a href="{{ route('zakat-calculator') }}" class="text-gray-600 hover:text-gray-800">
            Kembali ke Kalkulator
        </a>
    </div>
</div>

<style>
.form-label {
    @apply block text-sm font-medium text-gray-700 mb-2;
}
.input-field {
    @apply w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent;
}
.sapaan-btn {
    @apply flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl text-base font-medium bg-white cursor-pointer transition-all duration-200 shadow-sm;
    @apply hover:shadow-lg hover:border-brand-blue;
    min-height: 48px;
    border-width: 1.5px;
    border-style: solid;
    border-radius: 0.75rem !important;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
}
.sapaan-btn.active {
    background-color: #f0f6ff;
    border-color: #2563eb !important;
    color: #2563eb;
    box-shadow: 0 2px 8px 0 rgba(37,99,235,0.08);
}
.payment-method-trigger {
    transition: border-color 0.2s, box-shadow 0.2s;
}
.payment-method-trigger:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px #2563eb33;
}
.payment-method-card-modal {
    @apply bg-white border border-gray-200 cursor-pointer transition-all duration-200 shadow-sm w-full;
    @apply hover:shadow-md hover:border-brand-blue;
    border-width: 1.5px;
    border-style: solid;
    border-radius: 1rem !important;
    min-height: 64px;
    text-align: left;
}
.payment-method-card-modal.selected, .payment-method-card-modal:active {
    background-color: #f0f6ff;
    border-color: #2563eb !important;
    color: #2563eb;
    box-shadow: 0 2px 8px 0 rgba(37,99,235,0.08);
}
.payment-method-card-modal.selected span {
    color: #2563eb;
}
#payment-modal {
    animation: fadeIn 0.2s;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format number input
    document.getElementById('amount').addEventListener('input', function() {
        let value = this.value.replace(/[^\d]/g, '');
        if (value) {
            this.value = parseInt(value).toLocaleString('id-ID');
        }
    });

    // Salutation buttons
    document.querySelectorAll('.sapaan-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.sapaan-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('salutation').value = this.dataset.value;
        });
    });

    // Payment method popup logic
    const openModalBtn = document.getElementById('open-payment-modal');
    const closeModalBtn = document.getElementById('close-payment-modal');
    const paymentModal = document.getElementById('payment-modal');
    const paymentMethodInput = document.getElementById('payment_method');
    const selectedPaymentMethod = document.getElementById('selected-payment-method');
    const paymentMethodCards = document.querySelectorAll('.payment-method-card-modal');

    if (openModalBtn && paymentModal) {
        openModalBtn.addEventListener('click', function() {
            paymentModal.classList.remove('hidden');
        });
    }
    if (closeModalBtn && paymentModal) {
        closeModalBtn.addEventListener('click', function() {
            paymentModal.classList.add('hidden');
        });
    }
    paymentMethodCards.forEach(card => {
        card.addEventListener('click', function() {
            paymentMethodCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            paymentMethodInput.value = this.dataset.value;
            selectedPaymentMethod.innerHTML = (this.dataset.logo ? `<img src='${this.dataset.logo}' class='inline w-5 h-5 mr-3 object-contain'>` : '') + this.dataset.label;
            paymentModal.classList.add('hidden');
        });
    });

    // Form submit handler - convert formatted amount to integer
    document.getElementById('donation-form').addEventListener('submit', function(e) {
        const amountInput = document.getElementById('amount');
        if (amountInput.value) {
            // Remove all non-digit characters and convert to integer
            const cleanAmount = amountInput.value.replace(/[^\d]/g, '');
            amountInput.value = cleanAmount;
        }
    });
});
</script>
@endsection 
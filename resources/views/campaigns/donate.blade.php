@extends('layouts.app')

@section('title', 'Donasi - ' . $campaign->title)

@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">
    <!-- Header Sticky dengan Tombol Back -->
    <div class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200 -mx-4 px-4 py-3 mb-6" style="margin-top: -1.5rem;">
        <div class="flex items-center">
            <a href="{{ url()->previous() }}" class="back-button" title="Kembali" aria-label="Kembali">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
        </div>
    </div>
    <form method="POST" action="{{ route('campaigns.donate.store', $campaign->slug) }}" id="donation-form">
        @csrf

        <!-- Campaign Header -->
        <div class="flex items-center gap-4 mb-6">
            <img src="{{ $campaign->image_url ?? '/images/placeholder.jpg' }}" 
                 alt="{{ $campaign->title }}" 
                 class="w-16 h-16 object-cover rounded-lg"
                 onerror="this.src='/images/placeholder.jpg'">
            <div>
                <p class="text-sm text-gray-600">Anda akan berdonasi dalam program:</p>
                <h1 class="font-semibold text-gray-900">{{ $campaign->title }}</h1>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Donation Amount -->
            <div>
                <label class="form-label text-center block mb-4">Donasi Terbaik Anda</label>
                <div class="space-y-3 mb-4">
                    <button type="button" class="w-full flex items-center gap-3 bg-white rounded-xl shadow-sm px-4 py-3 border border-transparent hover:border-blue-400 transition group amount-card" data-amount="30000">
                        <span class="text-2xl">😊</span>
                        <div>
                            <div class="font-bold text-lg text-gray-900">Rp 30rb</div>
                        </div>
                    </button>
                    <button type="button" class="w-full flex items-center gap-3 bg-white rounded-xl shadow-sm px-4 py-3 border border-transparent hover:border-blue-400 transition group amount-card" data-amount="50000">
                        <span class="text-2xl">😊</span>
                        <div>
                            <div class="font-bold text-lg text-gray-900">Rp 50rb</div>
                            <div class="text-xs text-gray-400">sering dipilih</div>
                        </div>
                    </button>
                    <button type="button" class="w-full flex items-center gap-3 bg-white rounded-xl shadow-sm px-4 py-3 border border-transparent hover:border-blue-400 transition group amount-card" data-amount="100000">
                        <span class="text-2xl">😘</span>
                        <div>
                            <div class="font-bold text-lg text-gray-900">Rp 100rb</div>
                        </div>
                    </button>
                    <button type="button" class="w-full flex items-center gap-3 bg-white rounded-xl shadow-sm px-4 py-3 border border-transparent hover:border-blue-400 transition group amount-card" data-amount="200000">
                        <span class="text-2xl">😍</span>
                        <div>
                            <div class="font-bold text-lg text-gray-900">Rp 200rb</div>
                        </div>
                    </button>
                    <button type="button" class="w-full flex items-center gap-3 bg-white rounded-xl shadow-sm px-4 py-3 border border-transparent hover:border-blue-400 transition group amount-card" data-amount="other">
                        <span class="text-2xl">😊</span>
                        <div>
                            <div class="font-bold text-lg text-gray-900">Nominal</div>
                            <div class="text-xs text-gray-400">lainnya</div>
                        </div>
                    </button>
                </div>
                <div id="custom-amount-input" style="display: none;">
                    <label for="amount" class="form-label">Masukkan nominal lain</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            Rp
                        </span>
                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            class="input-field w-full pl-9 text-lg"
                            placeholder="0"
                            min="10000"
                            value="{{ old('amount') }}"
                        >
                    </div>
                </div>
                 @error('amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Salutation -->
            <div>
                <label class="form-label">Sapaan</label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" class="sapaan-btn" data-value="Bapak" style="border-radius: 0.5rem;">Bapak</button>
                    <button type="button" class="sapaan-btn" data-value="Ibu" style="border-radius: 0.5rem;">Ibu</button>
                    <button type="button" class="sapaan-btn" data-value="Saudara" style="border-radius: 0.5rem;">Saudara</button>
                </div>
                <input type="hidden" name="salutation" id="salutation">
            </div>

            <!-- Donor Name -->
            <div>
                <label for="donor_name" class="form-label">Nama Lengkap</label>
                <input type="text" id="donor_name" name="donor_name" class="input-field" placeholder="Masukkan nama lengkap" value="{{ old('donor_name') }}">
                @error('donor_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div class="flex items-center mt-3">
                    <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }} class="rounded border-gray-300 text-brand-blue shadow-sm focus:border-brand-blue focus:ring focus:ring-brand-blue focus:ring-opacity-50">
                    <label for="is_anonymous" class="ml-2 text-sm text-gray-700">Sembunyikan nama saya (Hamba Allah)</label>
                </div>
            </div>

            <!-- WhatsApp -->
            <div>
                <label for="donor_whatsapp" class="form-label">WhatsApp/No. HP</label>
                <input type="tel" id="donor_whatsapp" name="donor_whatsapp" class="input-field" placeholder="Contoh: 08123456789" value="{{ old('donor_whatsapp') }}">
                 @error('donor_whatsapp')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
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
        </div>

        <!-- Submit Button -->
        <div class="mt-8">
            <button type="submit" class="w-full btn-primary py-3">
                Lanjutkan Pembayaran
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-label {
    @apply block text-sm font-medium text-gray-700 mb-2;
}
.sapaan-btn {
    @apply flex items-center justify-center px-4 py-3 border border-gray-200 rounded-lg text-base font-medium bg-white cursor-pointer transition-all duration-200 shadow-sm;
    @apply hover:shadow-lg hover:border-brand-blue;
    min-height: 48px;
    border-width: 1.5px;
    border-style: solid;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
}
.sapaan-btn.active {
    background-color: #f0f6ff;
    border-color: #2563eb !important;
    color: #2563eb;
    box-shadow: 0 2px 8px 0 rgba(37,99,235,0.08);
}
.amount-card {
    @apply flex flex-col items-center justify-center px-3 py-2 border border-gray-200 rounded-xl text-sm font-medium bg-white cursor-pointer transition-all duration-200 shadow-sm;
    @apply hover:shadow-lg hover:border-brand-blue;
    min-height: 38px;
    border-width: 1.5px;
    border-style: solid;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
}
.amount-card.active {
    background-color: #f0f6ff;
    border-color: #2563eb !important;
}
.amount-label {
    @apply text-xs text-gray-500;
}
.amount-value {
    @apply text-base font-bold text-brand-blue;
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
.back-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #f8fafc;
    color: #475569;
    padding: 0.5rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    text-decoration: none;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    width: 2.5rem;
    height: 2.5rem;
}
.back-button:hover {
    background-color: #f1f5f9;
    color: #1e293b;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-color: #cbd5e1;
}
.back-button:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('donation-form');
    const amountInput = document.getElementById('amount');
    const amountCards = document.querySelectorAll('.amount-card');
    const donorNameInput = document.getElementById('donor_name');
    const donorWhatsappInput = document.getElementById('donor_whatsapp');
    const donorEmailInput = document.getElementById('donor_email');
    const salutationInput = document.getElementById('salutation');
    const paymentMethodInput = document.getElementById('payment_method');
    const customAmountInput = document.getElementById('custom-amount-input');
    const isAnonymousCheckbox = document.getElementById('is_anonymous');

    function clearActiveCards() {
        amountCards.forEach(card => card.classList.remove('active'));
    }

    amountCards.forEach(card => {
        card.addEventListener('click', function() {
            clearActiveCards();
            this.classList.add('active');
            amountInput.value = this.dataset.amount;
            if (this.dataset.amount === 'other') {
                customAmountInput.style.display = 'block';
                amountInput.setAttribute('required', 'required');
            } else {
                customAmountInput.style.display = 'none';
                amountInput.removeAttribute('required');
            }
        });
    });

    amountInput.addEventListener('input', function() {
        const currentValue = this.value;
        clearActiveCards();
        amountCards.forEach(card => {
            if (card.dataset.amount === currentValue) {
                card.classList.add('active');
            }
        });
    });

    // Initial state based on old input
    const initialValue = amountInput.value;
    if (initialValue) {
        amountCards.forEach(card => {
            if (card.dataset.amount === initialValue) {
                card.classList.add('active');
            }
        });
    }

    // Initialize anonymous checkbox state
    if (isAnonymousCheckbox.checked) {
        donorNameInput.value = 'Hamba Allah';
        donorNameInput.disabled = true;
        donorNameInput.classList.add('bg-gray-100', 'text-gray-500');
    }

    // Sapaan buttons
    const sapaanButtons = document.querySelectorAll('.sapaan-btn');
    const sapaanInput = document.getElementById('salutation');

    sapaanButtons.forEach(button => {
        button.addEventListener('click', function() {
            sapaanButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            sapaanInput.value = this.dataset.value;
        });
    });

    // Payment method modal logic
    const openModalBtn = document.getElementById('open-payment-modal');
    const closeModalBtn = document.getElementById('close-payment-modal');
    const paymentModal = document.getElementById('payment-modal');
    const selectedPaymentText = document.getElementById('selected-payment-method');
    const paymentCardsModal = document.querySelectorAll('.payment-method-card-modal');

    openModalBtn.addEventListener('click', function() {
        paymentModal.classList.remove('hidden');
    });
    closeModalBtn.addEventListener('click', function() {
        paymentModal.classList.add('hidden');
    });
    paymentModal.addEventListener('click', function(e) {
        if (e.target === paymentModal) paymentModal.classList.add('hidden');
    });
    paymentCardsModal.forEach(card => {
        card.addEventListener('click', function() {
            paymentCardsModal.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const value = this.getAttribute('data-value');
            const label = this.getAttribute('data-label');
            const logo = this.getAttribute('data-logo');
            paymentMethodInput.value = value;
            selectedPaymentText.innerHTML = `<img src="${logo}" alt="" class="inline w-5 h-5 mr-3 object-contain">${label}`;
            paymentModal.classList.add('hidden');
        });
    });

    // Anonymous checkbox functionality
    isAnonymousCheckbox.addEventListener('change', function() {
        if (this.checked) {
            donorNameInput.value = 'Hamba Allah';
            donorNameInput.disabled = true;
            donorNameInput.classList.add('bg-gray-100', 'text-gray-500');
        } else {
            donorNameInput.value = '';
            donorNameInput.disabled = false;
            donorNameInput.classList.remove('bg-gray-100', 'text-gray-500');
        }
    });

    // Form validation and submission
    form.addEventListener('submit', function(e) {
        // Validasi manual
        let isValid = true;
        const errors = [];

        // Check amount
        if (!amountInput.value || amountInput.value < 10000) {
            errors.push('Nominal donasi minimal Rp 10.000');
            isValid = false;
        }

        // Check donor name (skip if anonymous)
        if (!isAnonymousCheckbox.checked && !donorNameInput.value.trim()) {
            errors.push('Nama lengkap harus diisi');
            isValid = false;
        }

        // Check WhatsApp (optional)
        if (donorWhatsappInput.value.trim() && !isValidPhone(donorWhatsappInput.value)) {
            errors.push('Format nomor WhatsApp tidak valid');
            isValid = false;
        }

        // Check email (optional but if filled, must be valid)
        if (donorEmailInput.value.trim() && !isValidEmail(donorEmailInput.value)) {
            errors.push('Format email tidak valid');
            isValid = false;
        }

        // Check payment method
        if (!paymentMethodInput.value) {
            errors.push('Pilih metode pembayaran');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Mohon perbaiki kesalahan berikut:\n' + errors.join('\n'));
            return false;
        }
        // Jika valid, biarkan form submit normal (tanpa e.preventDefault)
    });

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function isValidPhone(phone) {
        // Remove all non-digit characters
        const cleanPhone = phone.replace(/\D/g, '');
        // Check if it's a valid Indonesian phone number (8-15 digits)
        return cleanPhone.length >= 8 && cleanPhone.length <= 15;
    }
});
</script>
@endpush 
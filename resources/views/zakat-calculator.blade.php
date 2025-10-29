@extends('layouts.app')

@section('title', 'Kalkulator Zakat')

@section('content')
<div class="min-h-screen bg-gray-50 font-sans">
    <div class="container mx-auto px-4 py-5">
        
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="text-2xl font-bold text-gray-800">Kalkulator Zakat</h1>
            <p class="text-sm text-gray-500">Hitung kewajiban zakat Anda</p>
        </div>

        <!-- Calculator Card -->
        <div class="bg-white rounded-2xl shadow-sm max-w-lg mx-auto">
            <!-- Tab Navigation -->
            <div>
                <div class="flex items-stretch justify-between p-4">
                    <button id="tab-mal" class="tab-btn-app active" data-tab="mal">
                        <span class="flex-shrink-0">Zakat Mal</span>
                    </button>
                    <button id="tab-fitrah" class="tab-btn-app" data-tab="fitrah">
                        <span class="flex-shrink-0">Zakat Penghasilan</span>
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-5">
                <!-- Zakat Mal Tab -->
                <div id="content-mal" class="tab-content">
                    <form id="zakat-mal-form" class="space-y-3" data-campaigns-url="{{ route('campaigns') }}">
                        <div class="input-group">
                            <div class="input-area">
                                <label for="emas" class="input-label">Emas (gram)</label>
                                <input type="text" id="emas" name="emas" min="0" placeholder="Contoh: 85" class="input-field">
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-area">
                                <label for="uang" class="input-label">Uang, Tabungan, Deposito (Rp)</label>
                                <input type="text" id="uang" name="uang" min="0" placeholder="50.000.000" class="input-field">
                            </div>
                        </div>
                        <button type="submit" class="btn-calculate-green">
                            <span>Hitung Zakat Mal</span>
                        </button>
                    </form>
                    <div id="zakat-mal-result" class="mt-5"></div>
                </div>

                <!-- Zakat Penghasilan Tab -->
                <div id="content-fitrah" class="tab-content hidden">
                    <form id="zakat-penghasilan-form" class="space-y-3" data-campaigns-url="{{ route('campaigns') }}">
                        <div class="input-group">
                            <div class="input-area">
                                <label for="penghasilan-bulanan" class="input-label">Penghasilan Bulanan (Rp)</label>
                                <input type="text" id="penghasilan-bulanan" name="penghasilan_bulanan" min="0" placeholder="5.000.000" class="input-field">
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-area">
                                <label for="pengeluaran-bulanan" class="input-label">Pengeluaran Bulanan (Rp)</label>
                                <input type="text" id="pengeluaran-bulanan" name="pengeluaran_bulanan" min="0" placeholder="3.000.000" class="input-field">
                            </div>
                        </div>
                        <button type="submit" class="btn-calculate-green">
                            <span>Hitung Zakat Penghasilan</span>
                        </button>
                    </form>
                    <div id="zakat-penghasilan-result" class="mt-5"></div>
                </div>
            </div>
        </div>
        
        <!-- Footer Note -->
        <div class="text-center mt-4 text-xs text-gray-400 max-w-sm mx-auto">
            <p>*Kalkulator ini adalah alat bantu, silakan konsultasi dengan ahli untuk kepastian.</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .tab-btn-app {
        @apply flex items-center justify-center text-sm font-semibold py-3 px-6 transition-all duration-200 relative rounded-t-xl shadow-none border-none outline-none;
        color: #6b7280;
        background: #f8fafc;
        margin-right: 0.5rem;
        margin-left: 0.5rem;
        min-width: 140px;
        min-height: 48px;
        box-shadow: none;
        border-bottom: none;
        border-radius: 1rem 1rem 0 0;
    }
    .tab-btn-app:last-child {
        margin-right: 0;
    }
    .tab-btn-app.active {
        color: #16a34a;
        background: #f0fdf4;
        border-bottom: none;
        box-shadow: 0 2px 8px rgba(22, 163, 74, 0.10);
        font-weight: 700;
        transform: translateY(-2px) scale(1.04);
        z-index: 1;
    }
    .tab-btn-app:hover:not(.active) {
        color: #374151;
        background: #f1f5f9;
        border-bottom: none;
        transform: translateY(-1px) scale(1.01);
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.08);
    }
    .tab-btn-app:focus {
        outline: 2px solid #16a34a;
        outline-offset: 2px;
    }
    @media (max-width: 640px) {
        .tab-btn-app {
            @apply text-xs py-2.5 px-3 rounded-t-lg;
            min-width: 110px;
            min-height: 40px;
            margin-right: 0.25rem;
            margin-left: 0.25rem;
        }
    }
    @media (max-width: 480px) {
        .tab-btn-app {
            @apply px-2 py-2 rounded-t-md;
            min-width: 90px;
            min-height: 36px;
        }
    }
    
    .input-group {
        @apply bg-gray-50 border border-gray-200 rounded-lg p-3 transition-all duration-300 focus-within:ring-2 focus-within:ring-green-400 focus-within:border-transparent;
    }
    .input-area {
        @apply w-full;
    }
    .input-label {
        @apply block text-xs font-medium text-gray-500;
    }
    .input-field {
        @apply w-full bg-transparent text-sm text-gray-800 font-semibold outline-none border-none p-0;
    }
    .input-field::placeholder {
        @apply font-normal text-gray-400;
    }
    .btn-calculate-green {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        font-weight: 600;
        color: #fff;
        background: #f59e42;
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        font-size: 1rem;
        box-shadow: 0 2px 8px 0 rgba(245,158,66,0.08);
        transition: background 0.2s, box-shadow 0.2s;
        margin-top: 1.5rem;
        margin-bottom: 0;
        text-align: center;
        min-height: 48px;
        gap: 0.5rem;
        border: none;
    }
    .btn-calculate-green:hover, .btn-calculate-green:focus {
        background: #ea580c;
        color: #fff;
    }
    .btn-calculate-green span {
        flex: 1;
        text-align: center;
        display: block;
    }
    .btn-calculate-green svg {
        width: 1.25rem;
        height: 1.25rem;
        margin-left: 0.5rem;
        flex-shrink: 0;
    }
    .result-card {
        @apply border-l-4 rounded-xl p-5 space-y-4 shadow bg-white;
        margin-top: 1rem;
    }
    .result-card .flex.items-start {
        @apply gap-4;
    }
    .result-card svg {
        @apply w-7 h-7 mt-1 mr-0;
        flex-shrink: 0;
    }
    .result-card h4 {
        @apply font-bold text-gray-800 text-base mb-2 leading-snug;
    }
    .result-card p {
        @apply text-sm text-gray-600 mb-1;
    }
    .result-card p.text-sm.font-semibold {
        @apply text-gray-700 mt-2 mb-0;
    }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('build/assets/zakat-calculator-DhSnedyf.js') }}" defer></script>
@endpush 
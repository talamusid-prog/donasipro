@extends('layouts.app')

@section('title', $campaign->title . ' - Donasi Apps')

@section('content')
<div class="container mx-auto py-6 max-w-3xl pb-40">
    <!-- Header dengan Back Button yang Lebih Jelas -->
    <div class="sticky-header">
        <div class="flex justify-between items-center px-4">
            <!-- Back Button yang Lebih Jelas -->
            <a href="{{ route('home') }}" class="back-button" title="Kembali ke Beranda" aria-label="Kembali ke Beranda">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            
            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button onclick="share()" class="action-button share" title="Bagikan">
                    <i data-lucide="share-2" class="w-4 h-4"></i>
                </button>
                <button class="action-button heart" title="Simpan">
                    <i data-lucide="heart" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Campaign Image -->
    <div class="relative w-full aspect-[3/2] rounded-xl overflow-hidden mb-6 px-4">
        <img src="{{ $campaign->image_url ?? '/images/placeholder.svg' }}" 
             alt="{{ $campaign->title }}"
             class="w-full h-full object-cover"
             onerror="this.src='/images/placeholder.svg'">
    </div>

    <!-- Campaign Content -->
    <div class="space-y-6 px-4">
        <!-- Title & Target -->
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-900 mb-2">{{ $campaign->title }}</h1>
            <div class="flex items-baseline justify-between">
                <span class="text-lg font-bold text-brand-blue">
                    Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}
                </span>
                <span class="text-xs text-gray-500">terkumpul dari <strong class="font-semibold text-gray-600">Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</strong></span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div>
            <div class="flex justify-between text-sm mb-2">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4 text-gray-500"></i>
                        <span>{{ $donations->count() }} Donatur</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-gray-500"></i>
                        @php
                            $daysLeft = ceil((strtotime($campaign->end_date) - time()) / (60 * 60 * 24));
                        @endphp
                        <span>{{ $daysLeft > 0 ? $daysLeft . ' Hari lagi' : 'Berakhir' }}</span>
                    </div>
                </div>
                <span class="font-medium">
                    @php
                        $percentage = $campaign->target_amount > 0 ? ($campaign->current_amount / $campaign->target_amount) * 100 : 0;
                    @endphp
                    {{ number_format($percentage, 1) }}%
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-brand-blue h-full rounded-full transition-all duration-500" 
                     style="width: {{ min($percentage, 100) }}%"></div>
            </div>
        </div>

        <!-- Organization -->
        <div class="flex items-center gap-3 py-4 border-t border-b border-gray-100">
            <div class="w-10 h-10 rounded-full bg-brand-blue/10 flex items-center justify-center">
                <i data-lucide="building-2" class="w-5 h-5 text-brand-blue"></i>
            </div>
            <div>
                <div class="font-medium text-gray-900">{{ $appSettings['organization_name'] ?? 'Yayasan Peduli Anak' }}</div>
                <div class="text-xs text-gray-500 flex items-center gap-1">
                    <span class="bg-gray-100 px-2 py-0.5 rounded">Organizer</span>
                    @if($campaign->is_verified)
                        <i data-lucide="check-circle" class="w-3 h-3 text-brand-blue"></i>
                    @endif
                </div>
            </div>
        </div>
        <!-- Tab Menu Section (font dan tinggi lebih kecil, tetap animasi) -->
        <style>
        .tab-animated {
            position: relative;
            overflow: hidden;
        }
        .tab-animated::after {
            content: '';
            position: absolute;
            left: 0; right: 0; bottom: 0;
            height: 100%;
            background: #03a9f4; /* brand-blue */
            border-radius: 0.5rem 0.5rem 0 0;
            z-index: 0;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(.4,0,.2,1);
        }
        .tab-animated.active::after {
            transform: translateY(0);
        }
        .tab-animated .tab-label, .tab-animated .tab-count {
            position: relative;
            z-index: 1;
        }
        </style>
        <div x-data="{ tab: 'keterangan' }" class="mt-6 mb-6">
            <div class="flex gap-3 border-b border-gray-200">
                <button @click="tab = 'keterangan'"
                    :class="[tab === 'keterangan' ? 'active text-white font-bold' : 'text-gray-800', 'tab-animated']"
                    class="transition-all duration-200 px-3 py-1.5 focus:outline-none text-sm -mb-px flex items-center gap-1 rounded-t-md"
                    style="min-width:80px;">
                    <span class="tab-label">Keterangan</span>
                </button>
                <button @click="tab = 'kabar'"
                    :class="[tab === 'kabar' ? 'active text-white font-bold' : 'text-gray-800', 'tab-animated']"
                    class="transition-all duration-200 px-3 py-1.5 focus:outline-none text-sm -mb-px flex items-center gap-1 rounded-t-md"
                    style="min-width:80px;">
                    <span class="tab-label">Kabar Terbaru</span>
                </button>
                <button @click="tab = 'donatur'"
                    :class="[tab === 'donatur' ? 'active text-white font-bold' : 'text-gray-800', 'tab-animated']"
                    class="transition-all duration-200 px-3 py-1.5 focus:outline-none text-sm -mb-px flex items-center gap-1 rounded-t-md"
                    style="min-width:80px;">
                    <span class="tab-label">Donatur</span>
                    <span class="tab-count" :class="tab === 'donatur' ? 'text-white' : 'text-gray-400'" class="text-xs ml-1">{{ $donations->count() }}</span>
                </button>
            </div>
            <!-- Section Content sesuai Tab -->
            <div class="mt-6">
                <div x-show="tab === 'keterangan'" x-data="{ expanded: false }">
                    <div class="prose max-w-none text-gray-600 transition-all duration-500 overflow-hidden" :class="expanded ? 'max-h-[9999px]' : 'max-h-48'">
                        <div class="p-4 bg-white rounded-lg shadow-sm">
                            {!! $campaign->description !!}
                        </div>
                    </div>
                    <div x-show="!expanded" class="absolute bottom-0 left-0 w-full h-20 bg-gradient-to-t from-gray-50 to-transparent pointer-events-none"></div>
                    <div class="mt-4 mb-8 text-center relative z-10" x-show="!expanded">
                        <button @click="expanded = true" class="px-6 py-2 bg-white/70 backdrop-blur-sm text-brand-blue rounded-full shadow-md hover:bg-white transition-all text-sm font-semibold">
                            <span>Selengkapnya</span>
                        </button>
                    </div>
                    <div class="mt-4 text-center" x-show="expanded" style="display: none;">
                        <button @click="expanded = false" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-all text-sm font-semibold">
                            <span>Tutup</span>
                        </button>
                    </div>
                </div>
                <div x-show="tab === 'kabar'" style="display: none;">
                    <div class="text-center text-gray-500 py-8 bg-gray-50 rounded-lg">
                        <i data-lucide='info' class='w-8 h-8 text-gray-400 mx-auto mb-2'></i>
                        <p>Belum ada kabar terbaru.</p>
                    </div>
                </div>
                <div x-show="tab === 'donatur'" x-data="{ showAllDonors: false }">
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="font-semibold text-lg">Donasi <span class="text-gray-500">({{ $donations->count() }})</span></h2>
                        </div>
                        @if($donations->count() > 0)
                            <div class="space-y-3">
                                @foreach($donations as $i => $donation)
                                    <template x-if="showAllDonors || {{ $i }} < 4">
                                    <div class="flex items-start gap-4 py-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                                            <span class="text-brand-blue font-bold text-lg">
                                                @if($donation->is_anonymous)
                                                    H
                                                @else
                                                    {{ strtoupper(substr($donation->donor_name ?? 'A', 0, 1)) }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800">
                                                @if($donation->is_anonymous)
                                                    Hamba Allah
                                                @else
                                                    {{ $donation->donor_name ?? 'Anonim' }}
                                                @endif
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                Berdonasi sebesar <strong class="font-bold text-gray-800">Rp {{ number_format($donation->amount, 0, ',', '.') }}</strong>
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $donation->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    </template>
                                @endforeach
                                @if($donations->count() > 4)
                                    <div class="pt-4" x-show="!showAllDonors">
                                        <button @click="showAllDonors = true" 
                                                class="px-6 py-2 bg-white/70 backdrop-blur-sm text-brand-blue rounded-full shadow-md hover:bg-white transition-all text-sm font-semibold w-full">
                                            <span>Lihat {{ $donations->count() - 4 }} Donatur Lainnya</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <i data-lucide='users' class='w-8 h-8 text-gray-400 mx-auto mb-2'></i>
                                <p class="text-gray-500 text-sm">Jadilah orang pertama yang berdonasi!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Donatur Section -->
        <!-- Bagian ini dihapus karena sudah ada di tab Donatur -->
        <!-- Doa & Komentar -->
        <div class="mb-40">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-lg">Doa-doa Baik <span class="text-gray-500">({{ $donations->where('message', '!=', '')->count() }})</span></h2>
                @if($donations->where('message', '!=', '')->count() > 3)
                    <a href="?show_all_prayers=1" class="text-sm font-medium text-brand-blue hover:underline">Lihat Semua</a>
                @endif
            </div>

            @php
                $prayers = $donations->where('message', '!=', '');
            @endphp

            @if($prayers->count() > 0)
                <div class="space-y-4">
                    @foreach($prayers->take($showAllPrayers ? $prayers->count() : 3) as $donation)
                        <div class="bg-white border border-gray-100 rounded-lg p-4">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                                    <span class="text-brand-blue font-bold text-lg">
                                        @if($donation->is_anonymous)
                                            H
                                        @else
                                            {{ strtoupper(substr($donation->donor_name ?? 'A', 0, 1)) }}
                                        @endif
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-gray-800">
                                                @if($donation->is_anonymous)
                                                    Hamba Allah
                                                @else
                                                    {{ $donation->donor_name ?? 'Anonim' }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-400">{{ $donation->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <!-- Amin Button -->
                                            <button 
                                                onclick="toggleAmin({{ $donation->id }})" 
                                                class="amin-btn flex items-center gap-1 px-2 py-1 rounded-full text-xs transition-colors duration-200"
                                                data-donation-id="{{ $donation->id }}"
                                                data-has-amin="{{ $donation->hasUserAmin(session()->getId()) ? 'true' : 'false' }}">
                                                <i data-lucide="heart" class="w-4 h-4 amin-icon"></i>
                                                <span class="amin-count">{{ $donation->amin_count }}</span>
                                            </button>
                                            <button class="text-gray-400 hover:text-gray-600">
                                                <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-3">{{ $donation->message }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if(!$showAllPrayers && $prayers->count() > 3)
                        <div class="pt-4">
                            <a href="{{ url()->current() }}?show_all_prayers=1#prayers" 
                               class="px-6 py-2 bg-white/70 backdrop-blur-sm text-brand-blue rounded-full shadow-md hover:bg-white transition-all text-sm font-semibold block text-center">
                                <span>Lihat {{ $prayers->count() - 3 }} Doa Lainnya</span>
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <i data-lucide="message-square" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                    <p class="text-gray-500 text-sm">Belum ada doa yang dituliskan.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Floating Donate Button -->
    <div class="fixed bottom-0 left-0 right-0 flex justify-center z-50">
        <div class="bg-white border-t border-gray-100 rounded-t-xl" style="max-width: 375px; width: 100%;">
            <div class="px-4 py-4">
                <div class="flex gap-4">
                    <button onclick="share()" class="flex-none w-12 h-12 flex items-center justify-center rounded-xl border border-gray-200 hover:border-brand-blue text-gray-600 hover:text-brand-blue transition">
                        <i data-lucide="share-2" class="w-5 h-5"></i>
                    </button>
                    <a href="{{ route('campaigns.donate', $campaign->slug) }}" 
                       class="flex-1 bg-brand-blue hover:bg-brand-blue/90 text-white font-semibold rounded-xl py-3 text-center transition">
                        Donasi Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Header styling */
.sticky-header {
    position: sticky;
    top: 0;
    z-index: 50;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(229, 231, 235, 0.6);
    margin: -1.5rem -1rem 1.5rem -1rem;
    padding: 0.875rem 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.sticky-header.scrolled {
    background: rgba(255, 255, 255, 0.99);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Back button styling */
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

/* Action button styling */
.action-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.75rem;
    height: 2.75rem;
    background-color: #f8fafc;
    color: #64748b;
    border-radius: 0.75rem;
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.action-button:hover {
    background-color: #f1f5f9;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-color: #cbd5e1;
}

.action-button:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.action-button.share:hover {
    color: #3b82f6;
    background-color: #eff6ff;
    border-color: #93c5fd;
}

.action-button.heart:hover {
    color: #ef4444;
    background-color: #fef2f2;
    border-color: #fca5a5;
}

/* Amin button styling */
.amin-btn {
    transition: all 0.2s ease;
}

.amin-btn:hover {
    transform: scale(1.05);
}

.amin-btn:active {
    transform: scale(0.95);
}

.amin-btn.text-red-500 {
    background-color: rgba(239, 68, 68, 0.1);
}

.amin-btn.text-gray-400:hover {
    background-color: rgba(156, 163, 175, 0.1);
}

.amin-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Heart icon animation */
.amin-btn .amin-icon {
    transition: all 0.2s ease;
}

.amin-btn.text-red-500 .amin-icon {
    animation: heartBeat 0.3s ease;
}

@keyframes heartBeat {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .sticky-header {
        margin: -1.5rem -1rem 1.5rem -1rem;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.99);
    }
    
    .back-button {
        width: 2.25rem;
        height: 2.25rem;
        padding: 0.5rem;
    }
    
    .action-button {
        width: 2.25rem;
        height: 2.25rem;
    }
}

@media (max-width: 480px) {
    .sticky-header {
        margin: -1.5rem -1rem 1.5rem -1rem;
        padding: 0.625rem 1rem;
    }
    
    .back-button {
        width: 2rem;
        height: 2rem;
        padding: 0.375rem;
    }
    
    .action-button {
        width: 2rem;
        height: 2rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function share() {
    if (navigator.share) {
        navigator.share({
            title: '{{ addslashes($campaign->title) }}',
            text: 'Dukung campaign ini di Donasi Apps!',
            url: window.location.href,
        })
        .then(() => console.log('Berhasil dibagikan'))
        .catch((error) => console.log('Gagal membagikan', error));
    } else {
        // Fallback for desktop or unsupported browsers
        if (navigator.clipboard) {
            // Modern approach for secure contexts (HTTPS)
            navigator.clipboard.writeText(window.location.href).then(function() {
                alert('Link campaign berhasil disalin ke clipboard!');
            }, function(err) {
                console.error('Gagal menyalin link: ', err);
                alert('Gagal menyalin link.');
            });
        } else {
            // Legacy approach for insecure contexts (HTTP) or old browsers
            const dummy = document.createElement('textarea');
            document.body.appendChild(dummy);
            dummy.value = window.location.href;
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);
            alert('Link campaign berhasil disalin ke clipboard!');
        }
    }
}

function showAllDonors() {
    // Reload page with show_all_donors parameter
    const url = new URL(window.location);
    url.searchParams.set('show_all_donors', '1');
    window.location.href = url.toString();
}

function showAllPrayers() {
    // Reload page with show_all_prayers parameter
    const url = new URL(window.location);
    url.searchParams.set('show_all_prayers', '1');
    window.location.href = url.toString();
}

// Amin functionality
function toggleAmin(donationId) {
    const button = document.querySelector(`[data-donation-id="${donationId}"]`);
    const icon = button.querySelector('.amin-icon');
    const countSpan = button.querySelector('.amin-count');
    
    // Disable button during request
    button.disabled = true;
    
    fetch('{{ route("amin.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            donation_id: donationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update count
            countSpan.textContent = data.amin_count;
            
            // Update button state
            if (data.action === 'added') {
                button.classList.add('text-red-500');
                button.classList.remove('text-gray-400');
                icon.style.fill = 'currentColor';
            } else {
                button.classList.remove('text-red-500');
                button.classList.add('text-gray-400');
                icon.style.fill = 'none';
            }
            
            // Update data attribute
            button.setAttribute('data-has-amin', data.has_amin);
            
            // Show notification
            showNotification(data.action === 'added' ? 'Aamiin! Doa telah diaminkan' : 'Amin telah dibatalkan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan, silakan coba lagi', 'error');
    })
    .finally(() => {
        button.disabled = false;
    });
}

// Initialize amin buttons on page load
document.addEventListener('DOMContentLoaded', function() {
    const aminButtons = document.querySelectorAll('.amin-btn');
    aminButtons.forEach(button => {
        const hasAmin = button.getAttribute('data-has-amin') === 'true';
        const icon = button.querySelector('.amin-icon');
        
        if (hasAmin) {
            button.classList.add('text-red-500');
            button.classList.remove('text-gray-400');
            icon.style.fill = 'currentColor';
        } else {
            button.classList.remove('text-red-500');
            button.classList.add('text-gray-400');
            icon.style.fill = 'none';
        }
    });

    // Header scroll effect
    const stickyHeader = document.querySelector('.sticky-header');
    if (stickyHeader) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                stickyHeader.classList.add('scrolled');
            } else {
                stickyHeader.classList.remove('scrolled');
            }
        });
    }
});

// Notification function
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg text-white text-sm font-medium transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endpush

@endsection 
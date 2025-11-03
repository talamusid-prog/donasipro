@extends('layouts.app')

@section('title', 'Beranda - Donasi Apps')

@section('content')
{{-- DEBUG - Featured Campaigns --}}
{{-- <div style="background: yellow; padding: 10px; margin: 10px; font-size: 12px;">
    <strong>DEBUG - Featured Campaigns:</strong><br>
    @foreach($featuredCampaigns as $c)
        {{ $c->title }}: {{ $c->image_url }}<br>
    @endforeach
</div> --}}

<!-- Hero Section -->
<div class="bg-blue-50/50 text-center pt-4 pb-8">
    <div class="px-4">
        <!-- Search Bar -->
        <!-- Dihilangkan sesuai permintaan -->
    </div>
    
    <div class="px-4 relative h-10 mb-4">
        <!-- Tag centered -->
        <div class="text-center">
            <div class="inline-block bg-white px-3 py-1 rounded-full text-xs text-brand-blue font-medium shadow-sm border border-gray-100">
                Berbagi #KuatkanIndonesia
            </div>
        </div>
        <!-- Heart Icon -->
        <div class="absolute right-4 top-0 bg-white rounded-xl p-2 shadow-sm border border-gray-100 animate-float">
            <i data-lucide="heart" class="w-6 h-6 text-gray-400"></i>
        </div>
    </div>
    
    <!-- Main Heading -->
    <h1 class="text-xl font-bold text-gray-800">
        Saling Jaga Se-Indonesia
    </h1>
    <h2 class="text-lg font-extrabold text-brand-blue mb-3">
        Bantu Sesama, Dibantu Bersama
    </h2>

    <!-- Sub Heading -->
    <div class="flex justify-center items-center gap-3 mb-6">
        <div class="bg-white rounded-lg p-2 shadow-sm border border-gray-100 animate-float" style="animation-delay: 0.2s;">
            <i data-lucide="gift" class="w-5 h-5 text-brand-blue"></i>
        </div>
        <p class="text-gray-600 text-sm text-center">Donasi, zakat, dan lindungi keluarga<br>bersama jutaan orang baik</p>
    </div>

    <!-- CTA Card -->
    <div class="px-4">
        <div class="bg-white rounded-2xl p-5 text-center shadow-md border border-gray-100">
            <h3 class="text-base font-semibold text-gray-800 mb-2">Gabung Donasi Komitmen,<br>Bersama Kita Berbagi</h3>
            <p class="text-gray-600 text-xs mb-3">Kita Dapat Lakukan Donasi Rutin dengan Fitur Donasi Komitmen</p>
            <a href="#" class="inline-flex items-center gap-2 justify-center w-full bg-brand-blue text-white font-semibold py-1.5 px-3 rounded-lg text-xs hover:bg-brand-blue/80 transition-colors">
                Donasi Sekarang
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="arrow-right" class="lucide lucide-arrow-right w-3 h-3"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </a>
        </div>
    </div>
</div>

<!-- Category Section -->
<div class="bg-white py-8">
    <div class="text-center mb-6 px-4">
        <p class="text-sm text-gray-500 mb-1">Kategori Program</p>
        <h2 class="text-xl font-bold text-gray-800">AyoBantu mereka yang membutuhkan</h2>
       
    </div>
    
    <div class="bg-gray-50/70 p-4 shadow-inner-sm">
        <div class="grid grid-cols-5 gap-0 text-center">
            @forelse($categories as $category)
            <a href="{{ route('campaigns', ['category' => $category->slug]) }}" class="flex flex-col items-center gap-2">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-md border border-gray-100">
                    @if($category->icon)
                        <img src="{{ $category->icon_url }}" alt="{{ $category->name }}" class="w-10 h-10 object-cover">
                    @else
                        <div class="w-10 h-10 rounded flex items-center justify-center" style="background-color: {{ $category->color }}">
                            <span class="text-white text-xs font-bold">{{ strtoupper(substr($category->name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
                <span class="text-[10px] font-medium text-gray-600">{{ $category->name }}</span>
            </a>
            @empty
            {{-- Fallback jika tidak ada kategori di database --}}
            @php
                $fallbackCategories = [
                    ['name' => 'Bantuan Sosial', 'icon' => 'hand-heart'],
                    ['name' => 'Bencana Alam', 'icon' => 'cloud-lightning'],
                    ['name' => 'Palestina', 'icon' => 'calendar-heart'],
                    ['name' => 'Pendidikan', 'icon' => 'book-open-check'],
                    ['name' => 'Zakat', 'icon' => 'sprout']
                ];
            @endphp
            @foreach($fallbackCategories as $category)
            <a href="#" class="flex flex-col items-center gap-2">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-md border border-gray-100">
                    <i data-lucide="{{ $category['icon'] }}" class="w-10 h-10 text-brand-blue"></i>
                </div>
                <span class="text-[10px] font-medium text-gray-600">{{ $category['name'] }}</span>
            </a>
            @endforeach
            @endforelse
        </div>
    </div>
</div>

<!-- Slider Section -->
<div class="px-4 mt-4">
    <div x-data="{
        active: 0,
        sliders: @js($sliders->map(fn($s) => [
            'image' => $s->image_url,
        ])),
    }" class="relative rounded-2xl overflow-hidden" style="height: 180px;">
        <!-- Slides -->
        <template x-for="(slide, idx) in sliders" :key="idx">
            <div x-show="active === idx" class="absolute inset-0 transition-all duration-500">
                <img :src="slide.image" alt="" class="absolute inset-0 w-full h-full object-cover">
            </div>
        </template>
        <!-- Nav Buttons -->
        <button @click="active = (active === 0 ? sliders.length - 1 : active - 1)" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/60 hover:bg-white text-brand-blue rounded-full w-8 h-8 flex items-center justify-center shadow-md backdrop-blur z-20">
            <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </button>
        <button @click="active = (active === sliders.length - 1 ? 0 : active + 1)" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/60 hover:bg-white text-brand-blue rounded-full w-8 h-8 flex items-center justify-center shadow-md backdrop-blur z-20">
            <i data-lucide="chevron-right" class="w-5 h-5"></i>
        </button>
        <!-- Dots -->
        <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2 z-20">
            <template x-for="(slide, idx) in sliders" :key="'dot'+idx">
                <div :class="{'bg-white': active === idx, 'bg-white/50': active !== idx }" class="w-2 h-2 rounded-full transition-all duration-300"></div>
            </template>
        </div>
    </div>
</div>

<!-- Featured Program Section -->
<div class="mt-8 mb-4">
    <div class="text-center mb-4">
        <h2 class="text-xl font-bold text-gray-800 mb-1">Program Unggulan</h2>
        <p class="text-gray-500 text-xs">Dukung program-program penting dan terpercaya di platform kami</p>
    </div>
    
    <div 
        x-data="{ startX: 0, scrollLeft: 0, isDown: false }"
        x-on:touchstart="isDown = true; startX = $event.touches[0].pageX - $el.offsetLeft; scrollLeft = $el.scrollLeft;"
        x-on:touchend="isDown = false"
        x-on:touchmove="if(isDown){ $el.scrollLeft = scrollLeft - ($event.touches[0].pageX - $el.offsetLeft - startX); }"
        class="overflow-x-auto scrollbar-hide"
    >
        <div class="flex gap-4 px-4" style="min-width: 400px;">
            @forelse($featuredCampaigns as $campaign)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 min-w-[260px] max-w-[260px] flex-shrink-0 flex flex-col overflow-hidden">
                <div class="bg-gray-100 flex items-center justify-center h-28 w-full">
                    @if($campaign->image_url)
                        <img src="{{ $campaign->image_url ?? '/images/placeholder.svg' }}" alt="{{ $campaign->title }}" class="w-full h-full object-cover" onerror="this.src='/images/placeholder.svg'">
                    @else
                        <img src="/images/placeholder.svg" alt="{{ $campaign->title }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="flex-1 flex flex-col p-3">
                    <div class="flex gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100/50 text-gray-700/80 backdrop-blur-sm">Aktif</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-brand-blue/50 text-white/90 backdrop-blur-sm">Unggulan</span>
                        @if($campaign->category_id && $campaign->category && is_object($campaign->category))
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold backdrop-blur-sm" style="background-color: {{ $campaign->category->color }}50; color: {{ $campaign->category->color }};">{{ $campaign->category->name }}</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-sm mb-2 leading-tight">{{ $campaign->title }}</h3>
                    <div class="w-full bg-gray-200 rounded-full h-1 mb-2">
                        <div class="bg-brand-blue h-1 rounded-full" style="width: {{ $campaign->progress_percentage }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-600 mb-2">
                        <span>Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</span>
                        <span>{{ $campaign->days_left }} hari tersisa</span>
                    </div>
                    <a href="{{ route('campaigns.show', $campaign->slug) }}" class="w-full bg-brand-blue text-white font-bold py-1.5 rounded-full text-xs mt-auto text-center">Donasi Sekarang</a>
                </div>
            </div>
            @empty
            {{-- Fallback jika tidak ada campaign unggulan --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 min-w-[260px] max-w-[260px] flex-shrink-0 flex flex-col overflow-hidden">
                <div class="bg-gray-100 flex items-center justify-center h-28 w-full">
                    <img src="/images/placeholder.svg" alt="Campaign placeholder" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 flex flex-col p-3">
                    <div class="flex gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100/50 text-gray-700/80 backdrop-blur-sm">Aktif</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-brand-blue/50 text-white/90 backdrop-blur-sm">Unggulan</span>
                    </div>
                    <h3 class="font-bold text-sm mb-2 leading-tight">Belum ada campaign unggulan</h3>
                    <div class="w-full bg-gray-200 rounded-full h-1 mb-2">
                        <div class="bg-brand-blue h-1 rounded-full" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-600 mb-2">
                        <span>Rp 0</span>
                        <span>0 hari tersisa</span>
                    </div>
                    <button class="w-full bg-gray-300 text-gray-500 font-bold py-2 rounded-full text-sm mt-auto text-center cursor-not-allowed" disabled>Belum Tersedia</button>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Latest Campaign Section -->
<div class="mt-8 mb-8">
    <div class="text-center mb-4">
        <h2 class="text-xl font-bold text-gray-800 mb-1">Campaign Terbaru</h2>
        <p class="text-gray-500 text-xs">Dukung aksi kebaikan terbaru di platform kami</p>
    </div>
    <div class="grid grid-cols-2 gap-4 px-2">
        @forelse($latestCampaigns as $campaign)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col relative overflow-hidden">
            <!-- Love Button -->
            <button class="absolute top-3 right-3 z-10 bg-white rounded-full p-1 shadow border border-gray-100">
                <i data-lucide="heart" class="w-5 h-5 text-brand-blue"></i>
            </button>
            <!-- Image -->
            <div class="bg-gradient-to-t from-gray-200 to-gray-100 flex items-center justify-center h-28 w-full">
                @if($campaign->image_url)
                    <img src="{{ $campaign->image_url ?? '/images/placeholder.svg' }}" alt="{{ $campaign->title }}" class="w-full h-full object-cover" onerror="this.src='/images/placeholder.svg'">
                @else
                    <img src="/images/placeholder.svg" alt="{{ $campaign->title }}" class="w-full h-full object-cover">
                @endif
            </div>
            <!-- Content -->
            <div class="flex-1 flex flex-col p-3">
                <h3 class="font-bold text-[15px] mb-2 leading-tight truncate">{{ $campaign->title }}</h3>
                <div class="w-full bg-gray-200 rounded-full h-1 mb-2">
                    <div class="bg-brand-blue h-1 rounded-full" style="width: {{ $campaign->progress_percentage }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-600 mb-2">
                    <div class="flex items-center gap-1"><i data-lucide="trending-up" class="w-4 h-4"></i> {{ number_format($campaign->progress_percentage, 1) }}%</div>
                    <span class="font-semibold text-brand-blue">Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-1 text-xs text-gray-500 mb-1">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    @if($campaign->donations_count > 0)
                        {{ $campaign->donations_count }} donatur
                    @else
                        Belum ada donatur
                    @endif
                </div>
                <a href="{{ route('campaigns.show', $campaign->slug) }}" class="w-full bg-brand-blue text-white font-bold py-1.5 rounded-full text-xs mt-auto text-center">Donasi Sekarang</a>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center py-8">
            <div class="text-gray-400 mb-4">
                <i data-lucide="campaign" class="w-16 h-16 mx-auto"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada campaign terbaru</h3>
            <p class="text-gray-600">Campaign akan muncul di sini setelah ditambahkan.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Program Lainnya Section -->
<div class="mb-8">
    <div class="text-center mb-4">
        <h2 class="text-xl font-bold text-gray-800 mb-1">Program Lainnya</h2>
        <p class="text-gray-500 text-xs">Jelajahi lebih banyak program kebaikan di platform kami</p>
    </div>
    
    @if($otherCampaigns->count() > 0)
        <div class="flex flex-col gap-5 px-2">
            @foreach($otherCampaigns as $campaign)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3 p-4">
                <!-- Image -->
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center">
                    @if($campaign->image_url)
                        <img src="{{ $campaign->image_url ?? '/images/placeholder.svg' }}" alt="{{ $campaign->title }}" class="w-12 h-12 object-cover rounded" onerror="this.src='/images/placeholder.svg'">
                    @else
                        <img src="/images/placeholder.svg" alt="{{ $campaign->title }}" class="w-12 h-12 object-cover rounded">
                    @endif
                </div>
                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm mb-1 truncate">{{ $campaign->title }}</h3>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                        <span>Progress</span>
                        <div class="w-24 h-1 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-1 bg-brand-blue rounded-full" style="width: {{ $campaign->progress_percentage }}%"></div>
                        </div>
                        <span class="text-gray-700 font-semibold">{{ number_format($campaign->progress_percentage, 1) }}%</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span>Terkumpul</span>
                        <span class="text-gray-700 font-semibold">Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                <!-- Action -->
                <div class="flex-shrink-0">
                    <a href="{{ route('campaigns.show', $campaign->slug) }}" class="bg-brand-blue text-white text-xs font-medium px-3 py-1 rounded-full hover:bg-brand-blue/80 transition-colors">
                        Donasi
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @if($otherCampaigns->count() > 4)
        <div class="flex justify-center mt-6">
            <a href="{{ route('campaigns') }}" class="bg-brand-blue text-white text-xs font-semibold px-6 py-2 rounded-full hover:bg-brand-blue/90 transition-colors shadow">
                Lihat Semua Program
            </a>
        </div>
        @endif
    @else
        <div class="text-center py-8 bg-gray-50 rounded-lg mx-2">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i data-lucide="campaign" class="w-6 h-6 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada program lainnya</h3>
            <p class="text-gray-600 text-sm">Program lainnya akan muncul di sini setelah ditambahkan.</p>
        </div>
    @endif
</div>

<!-- Doa-Doa Terbaik Section -->
<div class="mb-10">
    <div class="text-center mb-4">
        <h2 class="text-xl font-bold text-gray-800 mb-1">Doa-Doa Terbaik</h2>
        <p class="text-gray-500 text-xs">Doa dan harapan dari para donatur untuk sesama</p>
    </div>
    <div class="relative w-full overflow-hidden" id="prayers-container">
        <div class="flex gap-4 px-2" id="prayers-track" style="transform: translateX(0px);">
            @foreach($bestPrayers as $prayer)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center p-6 min-w-[220px] max-w-[260px] w-[70vw] flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mb-2">
                    <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                </div>
                <div class="font-bold text-gray-800 mb-1">{{ $prayer['donor_name'] }}</div>
                <div class="text-gray-600 text-sm text-center italic">"{{ $prayer['message'] }}"</div>
                <div class="text-xs text-gray-400 mt-2">{{ \Illuminate\Support\Carbon::parse($prayer['created_at'])->diffForHumans() }}</div>
            </div>
            @endforeach
            @foreach($bestPrayers as $prayer)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center p-6 min-w-[220px] max-w-[260px] w-[70vw] flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mb-2">
                    <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                </div>
                <div class="font-bold text-gray-800 mb-1">{{ $prayer['donor_name'] }}</div>
                <div class="text-gray-600 text-sm text-center italic">"{{ $prayer['message'] }}"</div>
                <div class="text-xs text-gray-400 mt-2">{{ \Illuminate\Support\Carbon::parse($prayer['created_at'])->diffForHumans() }}</div>
            </div>
            @endforeach
        </div>
    </div>
    
    <script>
        // Auto-scroll doa yang dioptimalkan dengan requestAnimationFrame
        function startPrayersScroll() {
            const track = document.getElementById('prayers-track');
            const container = document.getElementById('prayers-container');
            if (!track || !container) {
                return;
            }
            
            // Hentikan animasi yang sudah ada jika ada
            if (window.prayersAnimationId) {
                cancelAnimationFrame(window.prayersAnimationId);
            }
            
            let translateX = 0;
            let isPaused = false;
            let lastTime = 0;
            
            // Hitung width yang lebih akurat
            const firstCard = track.querySelector('.flex-shrink-0');
            const cardWidth = firstCard ? firstCard.offsetWidth + 16 : 236; // 220px + 16px gap
            const totalCards = {{ $bestPrayers->count() }};
            const maxTranslateX = -(cardWidth * totalCards);
            
            // Pause saat hover
            container.addEventListener('mouseenter', () => {
                isPaused = true;
            });
            
            container.addEventListener('mouseleave', () => {
                isPaused = false;
            });
            
            // Pause saat touch pada mobile
            container.addEventListener('touchstart', () => {
                isPaused = true;
            });
            
            container.addEventListener('touchend', () => {
                setTimeout(() => {
                    isPaused = false;
                }, 1000);
            });
            
            function animate(currentTime) {
                if (!isPaused) {
                    // Smooth animation dengan delta time
                    const deltaTime = currentTime - lastTime;
                    const speed = 0.02; // pixels per millisecond (lebih lambat untuk doa)
                    translateX -= speed * deltaTime;
                    
                    if (translateX <= maxTranslateX) {
                        translateX = 0; // Reset ke awal
                    }
                    
                    track.style.transform = `translateX(${translateX}px)`;
                }
                
                lastTime = currentTime;
                window.prayersAnimationId = requestAnimationFrame(animate);
            }
            
            // Mulai animasi
            window.prayersAnimationId = requestAnimationFrame(animate);
        }
        
        // Start scroll hanya sekali
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startPrayersScroll);
        } else {
            startPrayersScroll();
        }
    </script>
</div>

<!-- Metode Pembayaran Section -->
<div class="mb-10">
    <div class="text-center mb-4">
        <h2 class="text-xl font-bold text-gray-800 mb-1">Metode Pembayaran</h2>
        <p class="text-gray-500 text-xs">Berbagai metode pembayaran yang tersedia untuk memudahkan donasi Anda</p>
    </div>
    <div class="py-4 space-y-4 overflow-hidden">
        <!-- Baris Bergerak ke Kiri -->
        <div class="flex w-max animate-scroll-left">
            @php $logos1 = ['https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/BANK_BRI_logo.svg/640px-BANK_BRI_logo.svg.png', 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/1024px-Bank_Mandiri_logo_2016.svg.png', 'https://upload.wikimedia.org/wikipedia/commons/8/81/Muamalat_Logo.png', 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Gopay_logo.svg/1024px-Gopay_logo.svg.png']; @endphp
            @foreach (array_merge($logos1, $logos1) as $logo)
                <img src="{{ $logo }}" alt="" class="h-8 object-contain bg-white rounded shadow p-2 flex-shrink-0 mx-4">
            @endforeach
        </div>
        
        <!-- Baris Bergerak ke Kanan -->
        <div class="flex w-max animate-scroll-right">
            @php $logos2 = ['https://upload.wikimedia.org/wikipedia/commons/3/3e/Logo_flip.png', 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Logo_QRIS.svg/1024px-Logo_QRIS.svg.png', 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Logo_ovo_purple.svg/1024px-Logo_ovo_purple.svg.png', 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Shopee.svg/1024px-Shopee.svg.png']; @endphp
            @foreach (array_merge($logos2, $logos2) as $logo)
                <img src="{{ $logo }}" alt="" class="h-8 object-contain bg-white rounded shadow p-2 flex-shrink-0 mx-4">
            @endforeach
        </div>
    </div>
</div>

<!-- Footer Section -->
<div class="bg-gray-100 border-t border-gray-200 mt-10">
    <div class="container mx-auto px-6 py-8 text-center text-gray-600">
        <p class="text-xs leading-relaxed max-w-lg mx-auto">
            Berdiri sejak 2024, Donasi Apps memiliki izin Pengumpulan Uang dan Barang dari Kemensos. Donasi Apps rutin diaudit dengan status Wajar Tanpa Pengecualian (WTP).
        </p>
        <div class="border-t border-gray-200 my-6"></div>
        <div class="flex justify-center items-center space-x-2 sm:space-x-4 text-xs font-medium mb-6">
            <a href="{{ route('about') }}" class="hover:text-brand-blue transition-colors">Tentang Kami</a>
            <span class="text-gray-300">|</span>
            <a href="#" class="hover:text-brand-blue transition-colors">Syarat & Ketentuan</a>
            <span class="text-gray-300">|</span>
            <a href="{{ route('about') }}" class="hover:text-brand-blue transition-colors">Pusat Bantuan</a>
        </div>
        
        @if(($appSettings['show_social_media'] ?? '1') == '1')
        <div class="flex justify-center space-x-4 mb-6">
            @if($appSettings['social_facebook'] ?? false)
            <a href="{{ $appSettings['social_facebook'] }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-500 hover:text-brand-blue hover:bg-blue-50 transition-all duration-300 shadow-sm">
                <i data-lucide="facebook" class="w-5 h-5"></i>
            </a>
            @endif
            
            @if($appSettings['social_twitter'] ?? false)
            <a href="{{ $appSettings['social_twitter'] }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-500 hover:text-brand-blue hover:bg-blue-50 transition-all duration-300 shadow-sm">
                <i data-lucide="twitter" class="w-5 h-5"></i>
            </a>
            @endif
            
            @if($appSettings['social_instagram'] ?? false)
            <a href="{{ $appSettings['social_instagram'] }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-500 hover:text-brand-blue hover:bg-blue-50 transition-all duration-300 shadow-sm">
                <i data-lucide="instagram" class="w-5 h-5"></i>
            </a>
            @endif
            
            @if($appSettings['social_youtube'] ?? false)
            <a href="{{ $appSettings['social_youtube'] }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-500 hover:text-brand-blue hover:bg-blue-50 transition-all duration-300 shadow-sm">
                <i data-lucide="youtube" class="w-5 h-5"></i>
            </a>
            @endif
            
            @if($appSettings['social_linkedin'] ?? false)
            <a href="{{ $appSettings['social_linkedin'] }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-500 hover:text-brand-blue hover:bg-blue-50 transition-all duration-300 shadow-sm">
                <i data-lucide="linkedin" class="w-5 h-5"></i>
            </a>
            @endif
            
            @if($appSettings['social_telegram'] ?? false)
            <a href="{{ $appSettings['social_telegram'] }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-500 hover:text-brand-blue hover:bg-blue-50 transition-all duration-300 shadow-sm">
                <i data-lucide="send" class="w-5 h-5"></i>
            </a>
            @endif
        </div>
        @endif
        <p class="text-xs text-gray-400">
            Copyright © {{ date('Y') }} Donasi Apps. All Rights Reserved
        </p>
    </div>
</div>
@endsection
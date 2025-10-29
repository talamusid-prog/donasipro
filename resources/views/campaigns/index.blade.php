@extends('layouts.app')

@section('title', 'Program Donasi - Donasi Apps')

@section('content')
<div class="px-4 py-6">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">Program Donasi</h1>
        <p class="text-sm text-gray-600">Pilih program donasi yang ingin Anda bantu</p>
    </div>

    <!-- Search and Filter -->
    <div class="space-y-3 mb-6">
        <input
            type="text"
            placeholder="Cari program donasi..."
            class="mobile-input"
            id="searchInput"
        >
        <div class="flex gap-3">
            <select class="mobile-input text-sm" id="categoryFilter">
                <option value="">Semua Kategori</option>
                <option value="yatim-dhuafa">Yatim & Dhuafa</option>
                <option value="medical">Bantuan Medis</option>
                <option value="education">Pendidikan</option>
                <option value="mosque">Masjid</option>
            </select>
            <select class="mobile-input text-sm" id="statusFilter">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="completed">Selesai</option>
                <option value="expired">Berakhir</option>
            </select>
        </div>
    </div>

    <!-- Campaigns List -->
    <div class="space-y-4" id="campaignsContainer">
        @forelse($campaigns as $campaign)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3 p-4 campaign-card" 
                 data-category="{{ $campaign->category }}" 
                 data-status="{{ $campaign->status }}"
                 data-title="{{ strtolower($campaign->title) }}">
                
                <!-- Image -->
                <div class="flex-shrink-0 w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden">
                    @if($campaign->image_url)
                        <img src="{{ $campaign->image_url ?? '/images/placeholder.svg' }}" 
                             alt="{{ $campaign->title }}"
                             class="w-16 h-16 object-cover"
                             onerror="this.src='/images/placeholder.svg'">
                    @else
                        <img src="/images/placeholder.svg" 
                             alt="{{ $campaign->title }}"
                             class="w-16 h-16 object-cover">
                    @endif
                </div>
                
                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        @if($campaign->category_id && $campaign->category && is_object($campaign->category))
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold backdrop-blur-sm" 
                                  style="background-color: {{ $campaign->category->color }}50; color: {{ $campaign->category->color }};">
                                {{ $campaign->category->name }}
                            </span>
                        @endif
                    </div>
                    
                    <h3 class="font-bold text-sm mb-2 leading-tight truncate">
                        <a href="{{ route('campaigns.show', $campaign->slug) }}" class="hover:text-brand-blue">
                            {{ $campaign->title }}
                        </a>
                    </h3>
                    
                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                        <span>Progress</span>
                        <div class="w-20 h-1 bg-gray-200 rounded-full overflow-hidden">
                            @php
                                $percentage = $campaign->target_amount > 0 ? ($campaign->current_amount / $campaign->target_amount) * 100 : 0;
                            @endphp
                            <div class="h-1 bg-brand-blue rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                        <span class="text-gray-700 font-semibold">{{ number_format($percentage, 1) }}%</span>
                    </div>
                </div>
                
                <!-- Action -->
                <div class="flex-shrink-0">
                    <a href="{{ route('campaigns.show', $campaign->slug) }}" 
                       class="bg-brand-blue text-white text-xs font-medium px-4 py-2 rounded-full hover:bg-brand-blue/80 transition-colors">
                        Donasi
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-12" id="noResultsMessage" style="display: none;">
                <div class="text-gray-400 mb-4">
                    <i data-lucide="search" class="w-16 h-16 mx-auto"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Pencarian tidak ditemukan</h3>
                <p class="text-gray-600">Coba ubah kata kunci atau filter Anda.</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const campaignCards = document.querySelectorAll('.campaign-card');
    const noResultsMessage = document.getElementById('noResultsMessage');

    function filterCampaigns() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedStatus = statusFilter.value;
        let visibleCount = 0;

        campaignCards.forEach(card => {
            const title = card.dataset.title;
            const category = card.dataset.category;
            const status = card.dataset.status;

            const matchesSearch = title.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            const matchesStatus = !selectedStatus || status === selectedStatus;

            if (matchesSearch && matchesCategory && matchesStatus) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        if (visibleCount === 0) {
            noResultsMessage.style.display = 'block';
        } else {
            noResultsMessage.style.display = 'none';
        }
    }
    
    // Initial check in case there are no campaigns at all
    if (campaignCards.length === 0) {
        noResultsMessage.style.display = 'block';
        noResultsMessage.querySelector('h3').textContent = 'Belum ada program donasi';
        noResultsMessage.querySelector('p').textContent = 'Program donasi akan muncul di sini.';
    }

    searchInput.addEventListener('input', filterCampaigns);
    categoryFilter.addEventListener('change', filterCampaigns);
    statusFilter.addEventListener('change', filterCampaigns);
});
</script>
@endpush

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
@endsection 
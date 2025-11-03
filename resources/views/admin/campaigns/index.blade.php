@extends('layouts.admin')

@section('title', 'Kelola Campaign')

@section('header-title', 'Kelola Campaign')
@section('header-subtitle', 'Manajemen semua campaign donasi')

@section('header-button')
    <a href="{{ route('admin.campaigns.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
        Tambah Campaign
    </a>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Campaign</h2>
        <p class="text-gray-600 text-sm">Kelola semua campaign donasi</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terkumpul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verifikasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($campaigns as $campaign)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-lg object-cover" src="{{ $campaign->image_url ?? 'https://via.placeholder.com/40x40' }}" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $campaign->title }}</div>
                                <div class="text-sm text-gray-500">{{ $campaign->category_label }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($campaign->section === 'featured') bg-purple-100 text-purple-800
                            @elseif($campaign->section === 'urgent') bg-red-100 text-red-800
                            @elseif($campaign->section === 'popular') bg-orange-100 text-orange-800
                            @elseif($campaign->section === 'ending_soon') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ $campaign->section_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</div>
                        <div class="text-sm text-gray-500">{{ number_format($campaign->progress_percentage, 1) }}%</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($campaign->status === 'active') bg-green-100 text-green-800
                            @elseif($campaign->status === 'completed') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($campaign->is_verified) bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $campaign->is_verified ? 'Terverifikasi' : 'Belum Verifikasi' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('admin.campaigns.show', $campaign) }}" 
                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200 transition-colors duration-200">
                                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                Detail
                            </a>
                            <a href="{{ route('admin.campaigns.edit', $campaign) }}" 
                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-md hover:bg-indigo-200 transition-colors duration-200">
                                <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                                Edit
                            </a>
                            <button type="button" 
                                    onclick="openDeleteModal({{ $campaign->id }}, '{{ addslashes($campaign->title) }}')"
                                    class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 transition-colors duration-200">
                                <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                                Hapus
                            </button>
                            
                            <!-- Hidden form for delete -->
                            <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign) }}" class="hidden" id="delete-form-{{ $campaign->id }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $campaigns->links() }}
    </div>
</div>

<!-- Custom Delete Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            
            <!-- Header -->
            <div class="bg-red-50 px-4 py-3 sm:px-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-red-800">Konfirmasi Hapus</h3>
                        <p class="text-sm text-red-700">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-4 py-5 sm:p-6">
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-4">
                        Anda yakin ingin menghapus campaign:
                    </p>
                    <p class="text-lg font-semibold text-gray-900 mb-6" id="campaignTitle"></p>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Campaign yang dihapus tidak dapat dipulihkan kembali. Semua data terkait akan hilang permanen.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" 
                        id="confirmDeleteBtn"
                        class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors duration-200">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                    Ya, Hapus Campaign
                </button>
                <button type="button" 
                        onclick="closeDeleteModal()"
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors duration-200">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal(campaignId, campaignTitle) {
    document.getElementById('campaignTitle').innerText = campaignTitle;
    document.getElementById('confirmDeleteBtn').onclick = function() {
        document.getElementById(`delete-form-${campaignId}`).submit();
    };
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>

@endsection 
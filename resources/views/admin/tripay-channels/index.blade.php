@extends('layouts.admin')

@section('title', 'Manajemen Channel Tripay')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Channel Tripay</h1>
            <p class="text-gray-600 mt-1">Kelola channel pembayaran yang tersedia untuk donasi</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="testConnection()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                <i data-lucide="wifi" class="w-4 h-4 mr-2"></i>
                Test Koneksi
            </button>
            <form action="{{ route('admin.tripay-channels.sync') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Sync Channels
                </button>
            </form>
        </div>
    </div>

    <!-- Status Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tripay Settings -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Pengaturan Tripay</h3>
            <p class="text-sm text-gray-500">Konfigurasi API dan kredensial Tripay</p>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.tripay-settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- API Configuration -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900">Konfigurasi API</h4>
                        
                        <div>
                            <label for="api_key" class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                            <input type="password" 
                                   id="api_key" 
                                   name="api_key" 
                                   value="{{ old('api_key', $tripaySettings['api_key'] ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Masukkan API Key Tripay">
                            @error('api_key')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="private_key" class="block text-sm font-medium text-gray-700 mb-2">Private Key</label>
                            <input type="password" 
                                   id="private_key" 
                                   name="private_key" 
                                   value="{{ old('private_key', $tripaySettings['private_key'] ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Masukkan Private Key Tripay">
                            @error('private_key')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="merchant_code" class="block text-sm font-medium text-gray-700 mb-2">Merchant Code</label>
                            <input type="text" 
                                   id="merchant_code" 
                                   name="merchant_code" 
                                   value="{{ old('merchant_code', $tripaySettings['merchant_code'] ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Masukkan Merchant Code">
                            @error('merchant_code')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Environment Settings -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900">Pengaturan Environment</h4>
                        
                        <div>
                            <label for="environment" class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                            <select id="environment" 
                                    name="environment" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="sandbox" {{ (old('environment', $tripaySettings['environment'] ?? 'sandbox') == 'sandbox') ? 'selected' : '' }}>
                                    Sandbox (Testing)
                                </option>
                                <option value="production" {{ (old('environment', $tripaySettings['environment'] ?? 'sandbox') == 'production') ? 'selected' : '' }}>
                                    Production (Live)
                                </option>
                            </select>
                            @error('environment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="base_url" class="block text-sm font-medium text-gray-700 mb-2">Base URL</label>
                            <input type="text" 
                                   id="base_url" 
                                   name="base_url" 
                                   value="{{ old('base_url', $tripaySettings['base_url'] ?? 'https://tripay.co.id/api-sandbox') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://tripay.co.id/api-sandbox">
                            @error('base_url')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_production" 
                                   name="is_production" 
                                   value="1"
                                   {{ (old('is_production', $tripaySettings['is_production'] ?? '0') == '1') ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_production" class="ml-2 block text-sm text-gray-900">
                                Mode Production (Hati-hati! Akan menggunakan transaksi real)
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" 
                            onclick="testConnection()" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                        <i data-lucide="wifi" class="w-4 h-4 mr-2 inline"></i>
                        Test Koneksi
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <i data-lucide="save" class="w-4 h-4 mr-2 inline"></i>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Connection Status -->
    <div id="connection-status" class="hidden mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div id="status-icon" class="w-5 h-5 rounded-full mr-3"></div>
                <div>
                    <h4 id="status-title" class="text-sm font-medium"></h4>
                    <p id="status-message" class="text-sm text-gray-500"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-700">Bulk Actions:</span>
                <form action="{{ route('admin.tripay-channels.bulk-toggle') }}" method="POST" class="flex space-x-2">
                    @csrf
                    <input type="hidden" name="action" value="enable">
                    <input type="hidden" name="channels" id="bulk-channels-enable">
                    <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition-colors">
                        Aktifkan Selected
                    </button>
                </form>
                <form action="{{ route('admin.tripay-channels.bulk-toggle') }}" method="POST" class="flex space-x-2">
                    @csrf
                    <input type="hidden" name="action" value="disable">
                    <input type="hidden" name="channels" id="bulk-channels-disable">
                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition-colors">
                        Nonaktifkan Selected
                    </button>
                </form>
            </div>
            <div class="text-sm text-gray-500">
                <span id="selected-count">0</span> channel dipilih
            </div>
        </div>
    </div>

    <!-- Channels by Group -->
    @foreach($groups as $groupName => $groupChannels)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ $groupName }}</h3>
                <p class="text-sm text-gray-500">{{ $groupChannels->count() }} channel tersedia</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" class="group-checkbox rounded border-gray-300" data-group="{{ $groupName }}">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Channel
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Biaya
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Limit
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($groupChannels as $channel)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="channel-checkbox rounded border-gray-300" value="{{ $channel->id }}" data-group="{{ $groupName }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($channel->icon_url)
                                            <img src="{{ $channel->icon_url }}" alt="{{ $channel->name }}" class="w-8 h-8 rounded mr-3">
                                        @else
                                            <div class="w-8 h-8 bg-gray-100 rounded mr-3 flex items-center justify-center">
                                                <i data-lucide="credit-card" class="w-4 h-4 text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $channel->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $channel->code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $channel->formatted_fee }}</div>
                                    <div class="text-xs text-gray-500">
                                        Min: Rp {{ number_format($channel->minimum_fee, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        Rp {{ number_format($channel->minimum_amount, 0, ',', '.') }} - 
                                        Rp {{ number_format($channel->maximum_amount, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $channel->status_badge !!}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="toggleChannel({{ $channel->id }})" 
                                            class="toggle-btn text-blue-600 hover:text-blue-900 transition-colors"
                                            data-channel-id="{{ $channel->id }}">
                                        @if($channel->is_enabled)
                                            <i data-lucide="toggle-right" class="w-5 h-5"></i>
                                        @else
                                            <i data-lucide="toggle-left" class="w-5 h-5"></i>
                                        @endif
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    @if($channels->isEmpty())
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="credit-card" class="w-8 h-8 text-gray-400"></i>
            </div>
            <p class="text-gray-500 mb-4">Belum ada channel Tripay</p>
            <form action="{{ route('admin.tripay-channels.sync') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Sync Channels dari Tripay
                </button>
            </form>
        </div>
    @endif
</div>

@push('scripts')
<script>
function testConnection() {
    const statusDiv = document.getElementById('connection-status');
    const statusIcon = document.getElementById('status-icon');
    const statusTitle = document.getElementById('status-title');
    const statusMessage = document.getElementById('status-message');
    
    // Show loading state
    statusDiv.classList.remove('hidden');
    statusIcon.className = 'w-5 h-5 rounded-full mr-3 bg-yellow-400 animate-pulse';
    statusTitle.textContent = 'Menguji koneksi...';
    statusMessage.textContent = 'Sedang menghubungi Tripay API...';
    
    fetch('/admin/tripay-channels/test-connection', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusIcon.className = 'w-5 h-5 rounded-full mr-3 bg-green-400';
            statusTitle.textContent = 'Koneksi Berhasil';
            statusMessage.textContent = data.message;
        } else {
            statusIcon.className = 'w-5 h-5 rounded-full mr-3 bg-red-400';
            statusTitle.textContent = 'Koneksi Gagal';
            statusMessage.textContent = data.message;
        }
    })
    .catch(error => {
        statusIcon.className = 'w-5 h-5 rounded-full mr-3 bg-red-400';
        statusTitle.textContent = 'Error';
        statusMessage.textContent = 'Terjadi kesalahan saat menguji koneksi';
        console.error('Error:', error);
    });
}

function toggleChannel(channelId) {
    fetch(`/admin/tripay-channels/${channelId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to update UI
            location.reload();
        } else {
            alert('Gagal mengubah status channel');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

// Bulk selection
document.addEventListener('DOMContentLoaded', function() {
    const groupCheckboxes = document.querySelectorAll('.group-checkbox');
    const channelCheckboxes = document.querySelectorAll('.channel-checkbox');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkEnableInput = document.getElementById('bulk-channels-enable');
    const bulkDisableInput = document.getElementById('bulk-channels-disable');

    // Group checkbox functionality
    groupCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const group = this.dataset.group;
            const isChecked = this.checked;
            
            channelCheckboxes.forEach(channelCheckbox => {
                if (channelCheckbox.dataset.group === group) {
                    channelCheckbox.checked = isChecked;
                }
            });
            
            updateSelectedCount();
        });
    });

    // Individual checkbox functionality
    channelCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    function updateSelectedCount() {
        const selectedCheckboxes = document.querySelectorAll('.channel-checkbox:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
        
        selectedCountSpan.textContent = selectedIds.length;
        bulkEnableInput.value = JSON.stringify(selectedIds);
        bulkDisableInput.value = JSON.stringify(selectedIds);
    }

    // Environment change handler
    const environmentSelect = document.getElementById('environment');
    const baseUrlInput = document.getElementById('base_url');
    const isProductionCheckbox = document.getElementById('is_production');

    environmentSelect.addEventListener('change', function() {
        if (this.value === 'sandbox') {
            baseUrlInput.value = 'https://tripay.co.id/api-sandbox';
            isProductionCheckbox.checked = false;
        } else {
            baseUrlInput.value = 'https://tripay.co.id/api';
            isProductionCheckbox.checked = true;
        }
    });
});
</script>
@endpush
@endsection 
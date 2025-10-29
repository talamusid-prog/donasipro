@extends('layouts.admin')

@section('title', 'WA Blast API Settings')

@section('header-title', 'WA Blast API Settings')
@section('header-subtitle', 'Konfigurasi pengaturan WA Blast API')

@section('content')
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
        <div class="flex items-center">
            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
        <div class="flex items-center">
            <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
@endif

<div class="space-y-6">
    <!-- Konfigurasi Settings -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i data-lucide="settings" class="w-5 h-5 mr-2 text-blue-600"></i>
            Konfigurasi WA Blast API
        </h3>
        
        <form action="{{ route('admin.wa-blast.update-settings') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="base_url" class="block text-sm font-medium text-gray-700 mb-2">Base URL</label>
                    <input type="url" id="base_url" name="base_url" 
                           value="{{ \App\Models\AppSetting::where('key', 'wa_blast_base_url')->value('value') ?? config('whatsapp.wa_blast_api.base_url') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="https://api.wablast.com" required>
                    <p class="text-xs text-gray-500 mt-1">URL endpoint WA Blast API</p>
                </div>
                
                <div>
                    <label for="api_key" class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                    <input type="password" id="api_key" name="api_key" 
                           value="{{ \App\Models\AppSetting::where('key', 'wa_blast_api_key')->value('value') ?? config('whatsapp.wa_blast_api.api_key') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan API Key" required>
                    <p class="text-xs text-gray-500 mt-1">API Key dari WA Blast</p>
                </div>
                
                <div>
                    <label for="session_uuid" class="block text-sm font-medium text-gray-700 mb-2">UUID Session</label>
                    <input type="text" id="session_uuid" name="session_uuid" 
                           value="{{ \App\Models\AppSetting::where('key', 'wa_blast_session_uuid')->value('value') ?? '7d549d3d-a951-478e-b4d7-c90e465bd706' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                           pattern="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"
                           title="Format UUID: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                    <p class="text-xs text-gray-500 mt-1">UUID session WA Blast (format: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx)</p>
                </div>
                
                <div>
                    <label for="session_id" class="block text-sm font-medium text-gray-700 mb-2">Session ID (Legacy)</label>
                    <input type="number" id="session_id" name="session_id" 
                           value="{{ config('whatsapp.wa_blast_api.session_id', 1) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="1" min="1">
                    <p class="text-xs text-gray-500 mt-1">ID session WhatsApp (untuk kompatibilitas)</p>
                </div>
                
                <div>
                    <label for="enabled" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="enabled" name="enabled" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="1" {{ (\App\Models\AppSetting::where('key', 'wa_blast_enabled')->value('value') ?? config('whatsapp.wa_blast_api.enabled')) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !(\App\Models\AppSetting::where('key', 'wa_blast_enabled')->value('value') ?? config('whatsapp.wa_blast_api.enabled')) ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Aktifkan atau nonaktifkan WA Blast API</p>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.wa-blast.index') }}" 
                   class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 mr-2 inline"></i>
                    Simpan Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Test Koneksi -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i data-lucide="wifi" class="w-5 h-5 mr-2 text-blue-600"></i>
            Test Koneksi
        </h3>
        
        <div class="flex items-center space-x-4">
            <button onclick="testConnection()" class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                Test Koneksi
            </button>
            <div id="connection-result"></div>
        </div>
    </div>

    <!-- Informasi Konfigurasi -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i data-lucide="info" class="w-5 h-5 mr-2 text-blue-600"></i>
            Informasi Konfigurasi
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Base URL:</span>
                    <span class="text-sm font-medium text-gray-900">{{ \App\Models\AppSetting::where('key', 'wa_blast_base_url')->value('value') ?? config('whatsapp.wa_blast_api.base_url') ?: 'Belum dikonfigurasi' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">API Key:</span>
                    <span class="text-sm font-medium {{ \App\Models\AppSetting::where('key', 'wa_blast_api_key')->value('value') ?? config('whatsapp.wa_blast_api.api_key') ? 'text-green-600' : 'text-red-600' }}">
                        {{ \App\Models\AppSetting::where('key', 'wa_blast_api_key')->value('value') ?? config('whatsapp.wa_blast_api.api_key') ? '✓ Terkonfigurasi' : '✗ Belum dikonfigurasi' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">UUID Session:</span>
                    <span class="text-sm font-medium text-gray-900">{{ \App\Models\AppSetting::where('key', 'wa_blast_session_uuid')->value('value') ?? 'Belum dikonfigurasi' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Session ID (Legacy):</span>
                    <span class="text-sm font-medium text-gray-900">{{ config('whatsapp.wa_blast_api.session_id', 1) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Status:</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ (\App\Models\AppSetting::where('key', 'wa_blast_enabled')->value('value') ?? config('whatsapp.wa_blast_api.enabled')) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ (\App\Models\AppSetting::where('key', 'wa_blast_enabled')->value('value') ?? config('whatsapp.wa_blast_api.enabled')) ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-800 mb-2">Cara Konfigurasi</h4>
                <ol class="text-sm text-blue-700 space-y-1">
                    <li>1. Daftar akun di WA Blast API</li>
                    <li>2. Dapatkan API Key dari dashboard</li>
                    <li>3. Masukkan Base URL dan API Key</li>
                    <li>4. Set Session ID sesuai kebutuhan</li>
                    <li>5. Aktifkan status dan test koneksi</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i data-lucide="zap" class="w-5 h-5 mr-2 text-blue-600"></i>
            Quick Actions
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.wa-blast.index') }}" class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                <i data-lucide="home" class="w-6 h-6 text-blue-600 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-blue-800">Dashboard</h4>
                    <p class="text-sm text-blue-600">Kembali ke dashboard</p>
                </div>
            </a>
            
            <a href="{{ route('admin.wa-blast.templates') }}" class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                <i data-lucide="list" class="w-6 h-6 text-green-600 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-green-800">Template Messages</h4>
                    <p class="text-sm text-green-600">Kelola template pesan</p>
                </div>
            </a>
            
            <button onclick="testConnection()" class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                <i data-lucide="refresh-cw" class="w-6 h-6 text-yellow-600 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-yellow-800">Test Connection</h4>
                    <p class="text-sm text-yellow-600">Cek status koneksi</p>
                </div>
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function testConnection() {
    const resultDiv = document.getElementById('connection-result');
    resultDiv.innerHTML = `
        <div class="flex items-center">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-sm text-gray-600">Mengecek koneksi...</span>
        </div>
    `;

    fetch('{{ route("admin.wa-blast.test-connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="flex items-center text-green-600">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                    <span class="text-sm font-medium">Koneksi berhasil</span>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="flex items-center text-red-600">
                    <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                    <span class="text-sm font-medium">Koneksi gagal: ${data.message}</span>
                </div>
            `;
        }
        lucide.createIcons();
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="flex items-center text-red-600">
                <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                <span class="text-sm font-medium">Terjadi kesalahan</span>
            </div>
        `;
        lucide.createIcons();
    });
}
</script>
@endpush 
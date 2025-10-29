@extends('layouts.admin')

@section('title', 'WA Blast API Dashboard')

@section('header-title', 'WA Blast API Dashboard')
@section('header-subtitle', 'Kelola integrasi WA Blast API untuk notifikasi donasi otomatis')

@section('content')
<div class="space-y-6">
    <!-- Status dan Konfigurasi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Koneksi -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i data-lucide="wifi" class="w-5 h-5 mr-2 text-blue-600"></i>
                Status Koneksi
            </h3>
            
            <div id="connection-status">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="text-sm text-gray-600 mt-2">Mengecek status koneksi...</p>
                </div>
            </div>
            
            <div class="mt-4">
                <button onclick="testConnection()" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Test Koneksi
                </button>
            </div>
        </div>

        <!-- Konfigurasi -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i data-lucide="settings" class="w-5 h-5 mr-2 text-blue-600"></i>
                Konfigurasi
            </h3>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Base URL:</span>
                    <span class="text-sm font-medium text-gray-900">{{ config('whatsapp.wa_blast_api.base_url') ?: 'Belum dikonfigurasi' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">API Key:</span>
                    <span class="text-sm font-medium {{ config('whatsapp.wa_blast_api.api_key') ? 'text-green-600' : 'text-red-600' }}">
                        {{ config('whatsapp.wa_blast_api.api_key') ? '✓ Terkonfigurasi' : '✗ Belum dikonfigurasi' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Session ID:</span>
                    <span class="text-sm font-medium text-gray-900">{{ config('whatsapp.wa_blast_api.session_id', 1) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Status:</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ config('whatsapp.wa_blast_api.enabled') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ config('whatsapp.wa_blast_api.enabled') ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Method:</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $method === 'wa_blast_api' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ strtoupper(str_replace('_', ' ', $method)) }}
                    </span>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('admin.wa-blast.settings') }}" class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i data-lucide="settings" class="w-4 h-4 mr-2"></i>
                    Kelola Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Test Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Test Kirim Pesan -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i data-lucide="send" class="w-5 h-5 mr-2 text-blue-600"></i>
                Test Kirim Pesan
            </h3>
            
            <form id="test-message-form" class="space-y-4">
                <div>
                    <label for="test-phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="text" id="test-phone" name="phone" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="6281234567890" required>
                    <p class="text-xs text-gray-500 mt-1">Format: 6281234567890</p>
                </div>
                <div>
                    <label for="test-message" class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                    <textarea id="test-message" name="message" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Masukkan pesan test..." required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                        Kirim Test
                    </button>
                </div>
            </form>
            
            <div id="test-result" class="mt-4"></div>
        </div>

        <!-- Test Template Message -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i data-lucide="file-text" class="w-5 h-5 mr-2 text-blue-600"></i>
                Test Template Message
            </h3>
            
            <form id="template-test-form" class="space-y-4">
                <div>
                    <label for="template-phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="text" id="template-phone" name="phone" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="6281234567890" required>
                </div>
                <div>
                    <label for="template-content" class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                    <textarea id="template-content" name="template" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Halo {name}, ada promo menarik untuk Anda: {promo_message}" required></textarea>
                </div>
                <div>
                    <label for="template-variables" class="block text-sm font-medium text-gray-700 mb-2">Variables (JSON)</label>
                    <textarea id="template-variables" name="variables" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder='{"name": "John Doe", "promo_message": "Diskon 50%!"}'></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="flex items-center px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                        Kirim Template
                    </button>
                </div>
            </form>
            
            <div id="template-result" class="mt-4"></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i data-lucide="zap" class="w-5 h-5 mr-2 text-blue-600"></i>
            Quick Actions
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.wa-blast.templates') }}" class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                <i data-lucide="list" class="w-6 h-6 text-blue-600 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-blue-800">Template Messages</h4>
                    <p class="text-sm text-blue-600">Kelola dan test template pesan</p>
                </div>
            </a>
            
            <a href="{{ route('admin.wa-blast.settings') }}" class="flex items-center p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                <i data-lucide="settings" class="w-6 h-6 text-gray-600 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-gray-800">Settings</h4>
                    <p class="text-sm text-gray-600">Konfigurasi WA Blast API</p>
                </div>
            </a>
            
            <button onclick="testConnection()" class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                <i data-lucide="refresh-cw" class="w-6 h-6 text-green-600 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-green-800">Test Connection</h4>
                    <p class="text-sm text-green-600">Cek status koneksi API</p>
                </div>
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check connection status on page load
    testConnection();

    // Test message form
    document.getElementById('test-message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            phone: document.getElementById('test-phone').value,
            message: document.getElementById('test-message').value
        };

        fetch('{{ route("admin.wa-blast.send-test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('test-result').innerHTML = `
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                            ${data.message}
                        </div>
                    </div>
                `;
            } else {
                document.getElementById('test-result').innerHTML = `
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        <div class="flex items-center">
                            <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                            ${data.message}
                        </div>
                    </div>
                `;
            }
            lucide.createIcons();
        })
        .catch(error => {
            document.getElementById('test-result').innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <div class="flex items-center">
                        <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                        Terjadi kesalahan saat mengirim pesan
                    </div>
                </div>
            `;
            lucide.createIcons();
        });
    });

    // Template test form
    document.getElementById('template-test-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let variables = {};
        try {
            const variablesText = document.getElementById('template-variables').value;
            if (variablesText.trim()) {
                variables = JSON.parse(variablesText);
            }
        } catch (e) {
            document.getElementById('template-result').innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <div class="flex items-center">
                        <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                        Format JSON variables tidak valid
                    </div>
                </div>
            `;
            lucide.createIcons();
            return;
        }

        const formData = {
            phone: document.getElementById('template-phone').value,
            template: document.getElementById('template-content').value,
            variables: variables
        };

        fetch('{{ route("admin.wa-blast.send-template-test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('template-result').innerHTML = `
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                            ${data.message}
                        </div>
                    </div>
                `;
            } else {
                document.getElementById('template-result').innerHTML = `
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        <div class="flex items-center">
                            <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                            ${data.message}
                        </div>
                    </div>
                `;
            }
            lucide.createIcons();
        })
        .catch(error => {
            document.getElementById('template-result').innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <div class="flex items-center">
                        <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                        Terjadi kesalahan saat mengirim template
                    </div>
                </div>
            `;
            lucide.createIcons();
        });
    });
});

function testConnection() {
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
            document.getElementById('connection-status').innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-2"></i>
                        <div>
                            <h4 class="font-semibold text-green-800">Koneksi Berhasil</h4>
                            <p class="text-sm text-green-700">${data.message}</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('connection-status').innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i data-lucide="x-circle" class="w-5 h-5 text-red-600 mr-2"></i>
                        <div>
                            <h4 class="font-semibold text-red-800">Koneksi Gagal</h4>
                            <p class="text-sm text-red-700">${data.message}</p>
                        </div>
                    </div>
                </div>
            `;
        }
        lucide.createIcons();
    })
    .catch(error => {
        document.getElementById('connection-status').innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600 mr-2"></i>
                    <div>
                        <h4 class="font-semibold text-red-800">Koneksi Gagal</h4>
                        <p class="text-sm text-red-700">Gagal mengecek koneksi</p>
                    </div>
                </div>
            </div>
        `;
        lucide.createIcons();
    });
}
</script>
@endpush 
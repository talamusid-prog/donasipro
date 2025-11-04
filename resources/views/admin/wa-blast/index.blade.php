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
                    <span class="text-sm font-medium text-gray-900">{{ $config['base_url'] ?? 'Belum dikonfigurasi' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">API Key:</span>
                    <span class="text-sm font-medium {{ $config['api_key'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $config['api_key'] ? '✓ Terkonfigurasi' : '✗ Belum dikonfigurasi' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Session ID:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $config['session_id'] ?? 1 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Status:</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $config['enabled'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $config['enabled'] ? 'Aktif' : 'Nonaktif' }}
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
// Function to update CSRF token in meta tag
function updateCsrfToken() {
    // Get fresh token from a simple GET request
    return fetch('{{ route("admin.wa-blast.index") }}', {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.text())
    .then(html => {
        // Extract token from HTML response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const metaToken = doc.querySelector('meta[name="csrf-token"]');
        if (metaToken) {
            const newToken = metaToken.getAttribute('content');
            // Update meta tag in current page
            const currentMeta = document.querySelector('meta[name="csrf-token"]');
            if (currentMeta) {
                currentMeta.setAttribute('content', newToken);
            }
            return newToken;
        }
        return null;
    })
    .catch(() => {
        // Fallback: reload page if token update fails
        console.warn('Failed to update CSRF token, reloading page...');
        location.reload();
        return null;
    });
}

// Function to get current CSRF token
function getCsrfToken() {
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    return csrfTokenMeta 
        ? csrfTokenMeta.getAttribute('content') 
        : '{{ csrf_token() }}';
}

document.addEventListener('DOMContentLoaded', function() {
    // Check connection status on page load
    testConnection();

    // Test message form
    const testForm = document.getElementById('test-message-form');
    const testResult = document.getElementById('test-result');
    const testSubmitBtn = testForm.querySelector('button[type="submit"]');
    
    testForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable button and show loading
        testSubmitBtn.disabled = true;
        testSubmitBtn.innerHTML = `
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2 inline-block"></div>
            Mengirim...
        `;
        
        testResult.innerHTML = `
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-2"></div>
                    <span>Sedang menguji koneksi dan mengirim pesan...</span>
                </div>
            </div>
        `;
        
        const formData = {
            phone: document.getElementById('test-phone').value,
            message: document.getElementById('test-message').value
        };

        // Get CSRF token
        let csrfToken = getCsrfToken();
        
        if (!csrfToken) {
            testSubmitBtn.disabled = false;
            testSubmitBtn.innerHTML = `
                <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                Kirim Test
            `;
            testResult.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <div class="flex items-start">
                        <i data-lucide="x-circle" class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0"></i>
                        <div class="flex-1">
                            <div class="font-semibold">Error: CSRF token tidak ditemukan</div>
                            <div class="text-sm mt-1 opacity-75">Silakan refresh halaman dan coba lagi.</div>
                        </div>
                    </div>
                </div>
            `;
            lucide.createIcons();
            return;
        }
        
        // Function to send test message with retry
        const sendTestWithRetry = async (token, retryCount = 0) => {
            const response = await fetch('{{ route("admin.wa-blast.send-test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
            },
                credentials: 'same-origin',
            body: JSON.stringify(formData)
            });
            
            // Try to parse JSON response first
            let data;
            let responseText = '';
            try {
                responseText = await response.text();
                data = responseText ? JSON.parse(responseText) : {};
            } catch (e) {
                // If parsing fails, check if it's 419
                if (response.status === 419) {
                    return {
                        success: false,
                        message: 'CSRF token mismatch. Silakan refresh halaman dan coba lagi.',
                        error: 'CSRF token expired or invalid',
                        csrf_error: true,
                        http_status: 419
                    };
                }
                // If parsing fails, return error with raw response
                return {
                    success: false,
                    message: `Error ${response.status}: ${response.statusText}`,
                    error: 'Failed to parse JSON response: ' + e.message,
                    raw_response: responseText || 'Unable to read response',
                    http_status: response.status
                };
            }
            
            // Handle CSRF token mismatch (419) - try to update token and retry once
            if (response.status === 419 || data.error_code === 419 || data.http_status === 419) {
                // Try to update token and retry (max 1 retry)
                if (retryCount === 0) {
                    testResult.innerHTML = `
                        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                            <div class="flex items-center">
                                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-2"></div>
                                <span>CSRF token expired, memperbarui token...</span>
                            </div>
                        </div>
                    `;
                    
                    const newToken = await updateCsrfToken();
                    if (newToken) {
                        // Retry with new token
                        return sendTestWithRetry(newToken, 1);
                    }
                }
                
                return {
                    success: false,
                    message: data.message || 'CSRF token mismatch. Silakan refresh halaman dan coba lagi.',
                    error: 'CSRF token expired or invalid',
                    csrf_error: true,
                    http_status: 419,
                    details: data.details || null
                };
            }
            
            // Handle non-200 status codes
            if (!response.ok) {
                return {
                    success: false,
                    message: data.message || `Error ${response.status}: ${response.statusText}`,
                    errors: data.errors || null,
                    details: data.details || (Object.keys(data).length > 0 ? data : null),
                    error: data.error || null,
                    http_status: response.status,
                    response_data: data
                };
            }
            
            return data;
        };
        
        // Send request
        sendTestWithRetry(csrfToken)
        .then(data => {
            // Re-enable button
            testSubmitBtn.disabled = false;
            testSubmitBtn.innerHTML = `
                <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                Kirim Test
            `;
            
            if (data.success) {
                let detailsHtml = '';
                if (data.details) {
                    detailsHtml = `
                        <div class="mt-3 pt-3 border-t border-green-300">
                            <div class="text-xs space-y-1">
                                ${data.details.method ? `<div><strong>Method:</strong> ${data.details.method}</div>` : ''}
                                ${data.details.phone ? `<div><strong>Nomor:</strong> ${data.details.phone}</div>` : ''}
                                ${data.details.timestamp ? `<div><strong>Waktu:</strong> ${data.details.timestamp}</div>` : ''}
                            </div>
                        </div>
                    `;
                }
                
                testResult.innerHTML = `
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                        <div class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0"></i>
                            <div class="flex-1">
                                <div class="font-semibold">${data.message}</div>
                                ${detailsHtml}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                // Handle CSRF error specifically - check multiple conditions
                const isCsrfError = data.csrf_error || 
                                  data.http_status === 419 || 
                                  data.error_code === 419 || 
                                  (data.message && data.message.includes('CSRF')) ||
                                  (data.details && data.details.error_code === 419);
                
                if (isCsrfError) {
                    testResult.innerHTML = `
                        <div class="bg-yellow-50 border-2 border-yellow-300 text-yellow-800 px-4 py-4 rounded-lg">
                            <div class="flex items-start">
                                <i data-lucide="alert-triangle" class="w-6 h-6 mr-3 mt-0.5 flex-shrink-0 text-yellow-600"></i>
                                <div class="flex-1">
                                    <div class="font-bold text-lg mb-2">CSRF Token Mismatch</div>
                                    <div class="text-sm mb-3 opacity-90">
                                        ${data.message || 'CSRF token telah expired atau tidak valid. Silakan refresh halaman untuk mendapatkan token baru.'}
                                    </div>
                                    <div class="flex gap-2">
                                        <button onclick="location.reload()" class="px-4 py-2 bg-yellow-600 text-white rounded-md text-sm font-medium hover:bg-yellow-700 transition-colors shadow-sm">
                                            <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-1"></i>
                                            Refresh Halaman
                                        </button>
                                        <button onclick="testConnection()" class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-md text-sm font-medium hover:bg-yellow-200 transition-colors">
                                            <i data-lucide="wifi" class="w-4 h-4 inline mr-1"></i>
                                            Test Koneksi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    lucide.createIcons();
                    return;
                }
                
                let detailsHtml = '';
                let errorsHtml = '';
                
                // Handle validation errors
                if (data.errors) {
                    let errorsList = '';
                    Object.keys(data.errors).forEach(key => {
                        if (Array.isArray(data.errors[key])) {
                            data.errors[key].forEach(error => {
                                errorsList += `<div class="text-xs">• ${error}</div>`;
                            });
                        } else {
                            errorsList += `<div class="text-xs">• ${data.errors[key]}</div>`;
                        }
                    });
                    
                    if (errorsList) {
                        errorsHtml = `
                            <div class="mt-3 pt-3 border-t border-red-300">
                                <div class="text-xs font-semibold mb-1">Validation Errors:</div>
                                <div class="text-xs space-y-1">
                                    ${errorsList}
                                </div>
                            </div>
                        `;
                    }
                }
                
                // Handle details
                if (data.details) {
                    let detailsList = '';
                    if (typeof data.details === 'object') {
                        Object.keys(data.details).forEach(key => {
                            if (key !== 'message' && data.details[key] !== null && data.details[key] !== undefined) {
                                const value = typeof data.details[key] === 'object' 
                                    ? JSON.stringify(data.details[key], null, 2) 
                                    : String(data.details[key]);
                                detailsList += `<div class="text-xs"><strong>${key}:</strong> <span class="font-mono">${value}</span></div>`;
                            }
                        });
                    }
                    
                    if (detailsList) {
                        detailsHtml = `
                            <div class="mt-3 pt-3 border-t border-red-300">
                                <div class="text-xs font-semibold mb-1">Details:</div>
                                <div class="text-xs space-y-1">
                                    ${detailsList}
                                </div>
                            </div>
                        `;
                    }
                }
                
                // Handle response_data if available
                if (data.response_data && !detailsHtml) {
                    let responseList = '';
                    if (typeof data.response_data === 'object') {
                        Object.keys(data.response_data).forEach(key => {
                            if (key !== 'message' && data.response_data[key] !== null && data.response_data[key] !== undefined) {
                                const value = typeof data.response_data[key] === 'object' 
                                    ? JSON.stringify(data.response_data[key], null, 2) 
                                    : String(data.response_data[key]);
                                responseList += `<div class="text-xs"><strong>${key}:</strong> <span class="font-mono">${value}</span></div>`;
                            }
                        });
                    }
                    
                    if (responseList) {
                        detailsHtml = `
                            <div class="mt-3 pt-3 border-t border-red-300">
                                <div class="text-xs font-semibold mb-1">Response Data:</div>
                                <div class="text-xs space-y-1">
                                    ${responseList}
                                </div>
                            </div>
                        `;
                    }
                }
                
                // Show HTTP status if available
                if (data.http_status) {
                    if (!detailsHtml) detailsHtml = '';
                    detailsHtml += `
                        <div class="mt-3 pt-3 border-t border-red-300">
                            <div class="text-xs"><strong>HTTP Status:</strong> <span class="font-mono">${data.http_status}</span></div>
                        </div>
                    `;
                }
                
                // Build error message
                let errorMessage = data.message || 'Terjadi kesalahan';
                if (data.error && !data.message.includes(data.error)) {
                    errorMessage += ` (${data.error})`;
                }
                
                testResult.innerHTML = `
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        <div class="flex items-start">
                            <i data-lucide="x-circle" class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0"></i>
                            <div class="flex-1">
                                <div class="font-semibold">${errorMessage}</div>
                                ${errorsHtml}
                                ${detailsHtml}
                                ${data.raw_response ? `<div class="mt-3 pt-3 border-t border-red-300 text-xs"><strong>Raw Response:</strong> <pre class="mt-1 text-xs bg-red-100 p-2 rounded overflow-auto">${data.raw_response}</pre></div>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }
            lucide.createIcons();
        })
        .catch(error => {
            // Re-enable button
            testSubmitBtn.disabled = false;
            testSubmitBtn.innerHTML = `
                <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                Kirim Test
            `;
            
            let errorMessage = 'Network error atau server tidak merespon';
            if (error.message) {
                errorMessage = error.message;
            } else if (typeof error === 'string') {
                errorMessage = error;
            }
            
            testResult.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <div class="flex items-start">
                        <i data-lucide="x-circle" class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0"></i>
                        <div class="flex-1">
                            <div class="font-semibold">Terjadi kesalahan saat mengirim pesan</div>
                            <div class="text-sm mt-1 opacity-75">${errorMessage}</div>
                            <div class="text-xs mt-2 pt-2 border-t border-red-300 opacity-60">
                                Pastikan koneksi internet stabil dan server dapat diakses.
                            </div>
                        </div>
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

        // Get CSRF token with fallback
        const templateCsrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const templateCsrfToken = templateCsrfTokenMeta 
            ? templateCsrfTokenMeta.getAttribute('content') 
            : '{{ csrf_token() }}';

        fetch('{{ route("admin.wa-blast.send-template-test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': templateCsrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
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
    // Get CSRF token with fallback
    const connectionCsrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const connectionCsrfToken = connectionCsrfTokenMeta 
        ? connectionCsrfTokenMeta.getAttribute('content') 
        : '{{ csrf_token() }}';

    fetch('{{ route("admin.wa-blast.test-connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': connectionCsrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
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
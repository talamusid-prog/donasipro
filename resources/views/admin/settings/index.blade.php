@extends('layouts.admin')

@section('title', 'Pengaturan Aplikasi')

@section('header-title', 'Pengaturan Aplikasi')
@section('header-subtitle', 'Kelola pengaturan umum aplikasi')

@section('header-button')
<div class="flex gap-2">
    <form method="POST" action="{{ route('admin.settings.reset') }}" onsubmit="return confirm('Apakah Anda yakin ingin mereset semua pengaturan ke nilai default?')">
        @csrf
        <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
            <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-2"></i>
            Reset Default
        </button>
    </form>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Tabs -->
        <div x-data="{ activeTab: 'general' }" class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    @foreach($groups as $groupKey => $groupName)
                        <button type="button"
                                @click="activeTab = '{{ $groupKey }}'"
                                :class="activeTab === '{{ $groupKey }}' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            {{ $groupName }}
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="p-6">
                @foreach($groups as $groupKey => $groupName)
                    <div x-show="activeTab === '{{ $groupKey }}'" class="space-y-6">
                        @foreach($settings[$groupKey]['settings'] as $setting)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ $setting->label }}
                                    </label>
                                    @if($setting->description)
                                        <p class="text-xs text-gray-500">{{ $setting->description }}</p>
                                    @endif
                                </div>
                                <div class="md:col-span-2">
                                    @switch($setting->type)
                                        @case('textarea')
                                            <textarea 
                                                name="settings[{{ $setting->key }}]" 
                                                rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="Masukkan {{ strtolower($setting->label) }}">{{ old("settings.{$setting->key}", $setting->value) }}</textarea>
                                            @break
                                        
                                        @case('image')
                                            <div class="space-y-2">
                                                @if($setting->value)
                                                    <div class="flex items-center gap-4">
                                                        <img src="{{ Storage::url($setting->value) }}" 
                                                             alt="{{ $setting->label }}" 
                                                             class="w-20 h-20 object-cover rounded-lg border">
                                                        <div>
                                                            <p class="text-sm text-gray-600">File saat ini: {{ basename($setting->value) }}</p>
                                                            <label class="block text-sm font-medium text-gray-700">
                                                                Ganti {{ $setting->label }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @else
                                                    <label class="block text-sm font-medium text-gray-700">
                                                        Upload {{ $setting->label }}
                                                    </label>
                                                @endif
                                                <input 
                                                    type="file" 
                                                    name="settings[{{ $setting->key }}]" 
                                                    accept="image/*"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            </div>
                                            @break
                                        
                                        @case('email')
                                            <input 
                                                type="email" 
                                                name="settings[{{ $setting->key }}]" 
                                                value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="contoh@email.com">
                                            @break
                                        
                                        @case('phone')
                                            <input 
                                                type="tel" 
                                                name="settings[{{ $setting->key }}]" 
                                                value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="+62 812-3456-7890">
                                            @break
                                        
                                        @case('color')
                                            <div class="space-y-3">
                                                <div class="flex items-center gap-3">
                                                    <input 
                                                        type="color" 
                                                        name="settings[{{ $setting->key }}]" 
                                                        value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                                        class="w-16 h-10 border border-gray-300 rounded-md cursor-pointer"
                                                        onchange="updateColorText(this, '{{ $setting->key }}')"
                                                        oninput="updateColorText(this, '{{ $setting->key }}')">
                                                    <input 
                                                        type="text" 
                                                        id="text_{{ $setting->key }}"
                                                        value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                        placeholder="#000000"
                                                        onchange="updateColorPicker(this, '{{ $setting->key }}')"
                                                        oninput="updateColorPicker(this, '{{ $setting->key }}')">
                                                    <!-- Preview -->
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm text-gray-600">Preview:</span>
                                                        <div id="preview_{{ $setting->key }}" class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: {{ old("settings.{$setting->key}", $setting->value) }}"></div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Preset Colors -->
                                                <div class="flex flex-wrap gap-2">
                                                    <span class="text-sm text-gray-600 mr-2">Preset Warna:</span>
                                                    <button type="button" onclick="setPresetColor('{{ $setting->key }}', '#2563eb')" class="w-8 h-8 bg-blue-600 rounded border-2 border-white shadow-sm hover:scale-110 transition-transform" title="Biru"></button>
                                                    <button type="button" onclick="setPresetColor('{{ $setting->key }}', '#dc2626')" class="w-8 h-8 bg-red-600 rounded border-2 border-white shadow-sm hover:scale-110 transition-transform" title="Merah"></button>
                                                    <button type="button" onclick="setPresetColor('{{ $setting->key }}', '#059669')" class="w-8 h-8 bg-green-600 rounded border-2 border-white shadow-sm hover:scale-110 transition-transform" title="Hijau"></button>
                                                    <button type="button" onclick="setPresetColor('{{ $setting->key }}', '#d97706')" class="w-8 h-8 bg-orange-600 rounded border-2 border-white shadow-sm hover:scale-110 transition-transform" title="Oranye"></button>
                                                    <button type="button" onclick="setPresetColor('{{ $setting->key }}', '#7c3aed')" class="w-8 h-8 bg-purple-600 rounded border-2 border-white shadow-sm hover:scale-110 transition-transform" title="Ungu"></button>
                                                    <button type="button" onclick="setPresetColor('{{ $setting->key }}', '#0891b2')" class="w-8 h-8 bg-cyan-600 rounded border-2 border-white shadow-sm hover:scale-110 transition-transform" title="Cyan"></button>
                                                    <button type="button" onclick="setPresetColor('{{ $setting->key }}', '#be185d')" class="w-8 h-8 bg-pink-600 rounded border-2 border-white shadow-sm hover:scale-110 transition-transform" title="Pink"></button>
                                                    <button type="button" onclick="setPresetColor('{{ $setting->key }}', '#92400e')" class="w-8 h-8 bg-amber-700 rounded border-2 border-white shadow-sm hover:scale-110 transition-transform" title="Amber"></button>
                                                </div>
                                                
                                                <!-- Contoh Penggunaan -->
                                                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                                    <p class="text-sm text-gray-600 mb-3">Contoh penggunaan warna di aplikasi:</p>
                                                    <div class="flex flex-wrap gap-3">
                                                        <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm">
                                                            Tombol Utama
                                                        </button>
                                                        <span class="px-3 py-2 text-primary font-medium text-sm">
                                                            Teks Utama
                                                        </span>
                                                        <div class="px-3 py-2 border border-primary rounded-lg text-sm">
                                                            Border Utama
                                                        </div>
                                                        <div class="w-8 h-8 bg-primary rounded-full"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            @error("settings.{$setting->key}")
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                            <script>
                                                function updateColorText(colorPicker, key) {
                                                    const textInput = document.getElementById('text_' + key);
                                                    const preview = document.getElementById('preview_' + key);
                                                    textInput.value = colorPicker.value;
                                                    preview.style.backgroundColor = colorPicker.value;
                                                }
                                                
                                                function updateColorPicker(textInput, key) {
                                                    const colorPicker = textInput.previousElementSibling;
                                                    const preview = document.getElementById('preview_' + key);
                                                    if (textInput.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                                                        colorPicker.value = textInput.value;
                                                        preview.style.backgroundColor = textInput.value;
                                                    }
                                                }
                                                
                                                function setPresetColor(key, color) {
                                                    const colorPicker = document.querySelector(`input[name="settings[${key}]"]`);
                                                    const textInput = document.getElementById('text_' + key);
                                                    const preview = document.getElementById('preview_' + key);
                                                    
                                                    colorPicker.value = color;
                                                    textInput.value = color;
                                                    preview.style.backgroundColor = color;
                                                }
                                            </script>
                                            @break
                                        
                                        @case('toggle')
                                            <div class="flex items-center">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input 
                                                        type="checkbox" 
                                                        name="settings[{{ $setting->key }}]" 
                                                        value="1"
                                                        {{ old("settings.{$setting->key}", $setting->value) == '1' ? 'checked' : '' }}
                                                        class="sr-only peer">
                                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                    <span class="ml-3 text-sm font-medium text-gray-900">
                                                        {{ old("settings.{$setting->key}", $setting->value) == '1' ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                </label>
                                            </div>
                                            
                                            @if($setting->key === 'show_social_media')
                                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                                <p class="text-sm text-gray-600 mb-2">Preview Social Media:</p>
                                                <div class="flex space-x-2">
                                                    <div class="w-6 h-6 bg-blue-600 rounded flex items-center justify-center">
                                                        <i data-lucide="facebook" class="w-3 h-3 text-white"></i>
                                                    </div>
                                                    <div class="w-6 h-6 bg-blue-400 rounded flex items-center justify-center">
                                                        <i data-lucide="twitter" class="w-3 h-3 text-white"></i>
                                                    </div>
                                                    <div class="w-6 h-6 bg-pink-600 rounded flex items-center justify-center">
                                                        <i data-lucide="instagram" class="w-3 h-3 text-white"></i>
                                                    </div>
                                                    <div class="w-6 h-6 bg-red-600 rounded flex items-center justify-center">
                                                        <i data-lucide="youtube" class="w-3 h-3 text-white"></i>
                                                    </div>
                                                    <div class="w-6 h-6 bg-blue-700 rounded flex items-center justify-center">
                                                        <i data-lucide="linkedin" class="w-3 h-3 text-white"></i>
                                                    </div>
                                                    <div class="w-6 h-6 bg-blue-500 rounded flex items-center justify-center">
                                                        <i data-lucide="send" class="w-3 h-3 text-white"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            <script>
                                                document.querySelector('input[name="settings[{{ $setting->key }}]"]').addEventListener('change', function() {
                                                    const statusText = this.nextElementSibling.nextElementSibling;
                                                    statusText.textContent = this.checked ? 'Aktif' : 'Nonaktif';
                                                });
                                            </script>
                                            @break
                                        
                                        @case('whatsapp_uuid')
                                            <div class="space-y-3">
                                                <input 
                                                    type="text" 
                                                    name="settings[{{ $setting->key }}]" 
                                                    value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    placeholder="Masukkan UUID session WA Blast"
                                                    pattern="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"
                                                    title="Format UUID: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                                                
                                                <!-- Test Connection Button -->
                                                <button type="button" 
                                                        onclick="testWhatsAppConnection()"
                                                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm">
                                                    <i data-lucide="wifi" class="w-4 h-4 inline mr-2"></i>
                                                    Test Koneksi WhatsApp
                                                </button>
                                                
                                                <!-- Status Indicator -->
                                                <div id="whatsapp-status" class="hidden p-3 rounded-lg">
                                                    <div class="flex items-center gap-2">
                                                        <div id="status-icon" class="w-4 h-4 rounded-full"></div>
                                                        <span id="status-text" class="text-sm font-medium"></span>
                                                    </div>
                                                    <p id="status-message" class="text-xs mt-1"></p>
                                                </div>
                                                
                                                <!-- Help Text -->
                                                <div class="p-3 bg-blue-50 rounded-lg">
                                                    <p class="text-xs text-blue-700">
                                                        <strong>Panduan:</strong><br>
                                                        1. Buka dashboard WA Blast<br>
                                                        2. Salin UUID session yang aktif<br>
                                                        3. Paste di field di atas<br>
                                                        4. Klik "Test Koneksi" untuk memastikan terhubung
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <script>
                                                function testWhatsAppConnection() {
                                                    const statusDiv = document.getElementById('whatsapp-status');
                                                    const statusIcon = document.getElementById('status-icon');
                                                    const statusText = document.getElementById('status-text');
                                                    const statusMessage = document.getElementById('status-message');
                                                    
                                                    // Show loading
                                                    statusDiv.classList.remove('hidden');
                                                    statusDiv.className = 'p-3 bg-yellow-50 rounded-lg';
                                                    statusIcon.className = 'w-4 h-4 bg-yellow-500 rounded-full animate-pulse';
                                                    statusText.textContent = 'Mengecek koneksi...';
                                                    statusMessage.textContent = '';
                                                    
                                                    // Make AJAX request
                                                    fetch('/admin/wa-blast/test-connection', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                                        }
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            statusDiv.className = 'p-3 bg-green-50 rounded-lg';
                                                            statusIcon.className = 'w-4 h-4 bg-green-500 rounded-full';
                                                            statusText.textContent = 'Terhubung';
                                                            statusMessage.textContent = data.message || 'Koneksi WhatsApp berhasil!';
                                                        } else {
                                                            statusDiv.className = 'p-3 bg-red-50 rounded-lg';
                                                            statusIcon.className = 'w-4 h-4 bg-red-500 rounded-full';
                                                            statusText.textContent = 'Gagal';
                                                            statusMessage.textContent = data.message || 'Gagal terhubung ke WhatsApp';
                                                        }
                                                    })
                                                    .catch(error => {
                                                        statusDiv.className = 'p-3 bg-red-50 rounded-lg';
                                                        statusIcon.className = 'w-4 h-4 bg-red-500 rounded-full';
                                                        statusText.textContent = 'Error';
                                                        statusMessage.textContent = 'Terjadi kesalahan: ' + error.message;
                                                    });
                                                }
                                            </script>
                                            @break
                                        
                                        @default
                                            <input 
                                                type="text" 
                                                name="settings[{{ $setting->key }}]" 
                                                value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="Masukkan {{ strtolower($setting->label) }}">
                                    @endswitch
                                    
                                    @error("settings.{$setting->key}")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection 
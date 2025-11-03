@extends('layouts.admin')

@section('title', 'Tambah Campaign')
@section('header-title', 'Tambah Campaign')
@section('header-subtitle', 'Buat campaign donasi baru')
@section('header-button')
    <a href="{{ route('admin.campaigns.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
        Kembali
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Form Campaign</h2>
        </div>
        <form method="POST" action="{{ route('admin.campaigns.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            <!-- Basic Information -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Campaign *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan judul campaign" required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Category and Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select id="category" name="category" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Section *</label>
                    <div class="flex flex-col gap-2">
                        @foreach($sections as $key => $label)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="sections[]" value="{{ $key }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" {{ (collect(old('sections'))->contains($key)) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('sections')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi *</label>
                <textarea id="description" name="description" rows="4" 
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Masukkan deskripsi campaign" required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Images -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Gambar Campaign</label>
                <input type="file" name="image" id="image" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       accept="image/*">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF, WEBP. Maksimal 2MB.</p>
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Amount and Dates -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="target_amount" class="block text-sm font-medium text-gray-700 mb-2">Target Dana (Rp) *</label>
                    <input type="number" id="target_amount" name="target_amount" value="{{ old('target_amount') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="1000000" min="1000" required>
                    @error('target_amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai *</label>
                    <input type="datetime-local" id="start_date" name="start_date" value="{{ old('start_date') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berakhir *</label>
                    <input type="datetime-local" id="end_date" name="end_date" value="{{ old('end_date') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <!-- Status and Verification -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select id="status" name="status" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="is_verified" name="is_verified" value="1" 
                           {{ old('is_verified') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <label for="is_verified" class="ml-2 text-sm text-gray-700">
                        Campaign Terverifikasi
                    </label>
                </div>
            </div>

            <!-- Analytics & Tracking Section -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 mr-2 text-blue-600"></i>
                    Analytics & Tracking
                </h3>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-3 mt-0.5"></i>
                        <div>
                            <h4 class="font-medium text-blue-800 mb-2">Analytics Sudah Aktif Secara Global</h4>
                            <p class="text-sm text-blue-700 mb-3">
                                Google Analytics dan Facebook Pixel sudah terpasang secara global di aplikasi. 
                                Campaign ini akan otomatis di-track untuk:
                            </p>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• <strong>Campaign Views</strong> - Setiap kali campaign dilihat</li>
                                <li>• <strong>Donation Events</strong> - Setiap donasi untuk campaign ini</li>
                                <li>• <strong>Conversion Tracking</strong> - Konversi dari view ke donasi</li>
                                <li>• <strong>User Behavior</strong> - Perilaku pengguna di campaign ini</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- UTM Parameters -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="utm_source" class="block text-sm font-medium text-gray-700 mb-2">UTM Source</label>
                        <input type="text" id="utm_source" name="utm_source" value="{{ old('utm_source') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="facebook, google, email, etc.">
                        <p class="text-xs text-gray-500 mt-1">Sumber traffic (opsional)</p>
                    </div>
                    <div>
                        <label for="utm_medium" class="block text-sm font-medium text-gray-700 mb-2">UTM Medium</label>
                        <input type="text" id="utm_medium" name="utm_medium" value="{{ old('utm_medium') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="cpc, social, email, etc.">
                        <p class="text-xs text-gray-500 mt-1">Medium iklan (opsional)</p>
                    </div>
                    <div>
                        <label for="utm_campaign" class="block text-sm font-medium text-gray-700 mb-2">UTM Campaign</label>
                        <input type="text" id="utm_campaign" name="utm_campaign" value="{{ old('utm_campaign') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="ramadan2024, qurban2024, etc.">
                        <p class="text-xs text-gray-500 mt-1">Nama campaign (opsional)</p>
                    </div>
                </div>

                <!-- Campaign-Specific Analytics -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-3">Campaign-Specific Tracking</h4>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="checkbox" id="track_conversions" name="track_conversions" value="1" 
                                   {{ old('track_conversions', '1') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <label for="track_conversions" class="ml-2 text-sm text-gray-700">
                                Track konversi donasi untuk campaign ini
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="track_engagement" name="track_engagement" value="1" 
                                   {{ old('track_engagement', '1') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <label for="track_engagement" class="ml-2 text-sm text-gray-700">
                                Track engagement (scroll, time spent, etc.)
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="enhanced_ecommerce" name="enhanced_ecommerce" value="1" 
                                   {{ old('enhanced_ecommerce', '1') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <label for="enhanced_ecommerce" class="ml-2 text-sm text-gray-700">
                                Enhanced e-commerce tracking
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Analytics Preview -->
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-medium text-green-800 mb-2 flex items-center">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                        Analytics Preview
                    </h4>
                    <div class="text-sm text-green-700 space-y-1">
                        <p><strong>Google Analytics:</strong> 
                            @if(app(\App\Services\AnalyticsService::class)->isGoogleAnalyticsEnabled())
                                <span class="text-green-600">✅ Aktif</span>
                            @else
                                <span class="text-red-600">❌ Nonaktif</span>
                            @endif
                        </p>
                        <p><strong>Facebook Pixel:</strong> 
                            @if(app(\App\Services\AnalyticsService::class)->isFacebookPixelEnabled())
                                <span class="text-green-600">✅ Aktif</span>
                            @else
                                <span class="text-red-600">❌ Nonaktif</span>
                            @endif
                        </p>
                        <p class="text-xs mt-2">
                            Campaign ini akan otomatis di-track dengan event: <code class="bg-green-100 px-1 rounded">campaign_view</code> dan <code class="bg-green-100 px-1 rounded">donation_completed</code>
                        </p>
                    </div>
                </div>
            </div>
            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.campaigns.index') }}" 
                   class="inline-flex items-center px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Buat Campaign
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .ck-editor__editable {
        min-height: 200px;
        max-height: 400px;
    }
    .ck.ck-editor {
        width: 100%;
    }
    .ck.ck-editor__main > .ck-editor__editable {
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
    }
    .ck.ck-toolbar {
        border: 1px solid #d1d5db;
        border-bottom: none;
        border-radius: 0.5rem 0.5rem 0 0;
        background-color: #f9fafb;
    }
    /* Styling untuk gambar di CKEditor */
    .ck-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.375rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    .ck-content figure {
        margin: 1rem 0;
    }
    .ck-content figure img {
        display: block;
        margin: 0 auto;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    '|',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'link',
                    'blockQuote',
                    '|',
                    'undo',
                    'redo'
                ]
            },
            language: 'id',
            placeholder: 'Masukkan deskripsi campaign...'
        })
        .then(editor => {
            console.log('CKEditor berhasil dimuat');
            
            // Tambahkan tombol upload gambar ke toolbar secara manual
            const toolbar = editor.ui.view.toolbar;
            const toolbarElement = toolbar.element;
            
            // Buat separator
            const separator = document.createElement('span');
            separator.className = 'ck ck-toolbar__separator';
            
            // Buat tombol upload gambar
            const imageButton = document.createElement('button');
            imageButton.className = 'ck ck-button ck-off';
            imageButton.type = 'button';
            imageButton.title = 'Upload Gambar';
            imageButton.innerHTML = `
                <svg class="ck ck-icon ck-reset_all-excluded ck-icon_inherit-color ck-button__icon" viewBox="0 0 20 20">
                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="ck ck-button__label">Upload Gambar</span>
            `;
            
            // Event listener untuk upload gambar
            imageButton.addEventListener('click', function() {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';
                input.onchange = function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const formData = new FormData();
                        formData.append('upload', file);
                        
                        // Show loading state
                        imageButton.disabled = true;
                        imageButton.innerHTML = `
                            <svg class="ck ck-icon ck-reset_all-excluded ck-icon_inherit-color ck-button__icon animate-spin" viewBox="0 0 20 20">
                                <path d="M10 3.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H8a.5.5 0 0 1 0-1h1.5V4a.5.5 0 0 1 .5-.5zM10 16.5a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-1.5v1.5a.5.5 0 0 1-.5.5zM3.5 10a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1H4.5v1.5a.5.5 0 0 1-1 0V10.5a.5.5 0 0 1 .5-.5zM16.5 10a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 1 0v1.5a.5.5 0 0 1-.5.5z"></path>
                            </svg>
                            <span class="ck ck-button__label">Uploading...</span>
                        `;
                        
                        fetch('{{ route("admin.campaigns.upload-image") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.uploaded) {
                                // Insert image URL ke editor
                                const imageUrl = data.url;
                                const imageHtml = `<img src="${imageUrl}" alt="Uploaded image" style="max-width: 100%; height: auto; border-radius: 0.5rem; margin: 1rem 0;">`;
                                
                                // Insert HTML ke editor
                                const viewFragment = editor.data.processor.toView(imageHtml);
                                const modelFragment = editor.data.toModel(viewFragment);
                                editor.model.insertContent(modelFragment);
                                
                                // Reset input
                                input.value = '';
                            } else {
                                alert('Gagal upload gambar: ' + (data.error?.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Gagal upload gambar');
                        })
                        .finally(() => {
                            // Reset button state
                            imageButton.disabled = false;
                            imageButton.innerHTML = `
                                <svg class="ck ck-icon ck-reset_all-excluded ck-icon_inherit-color ck-button__icon" viewBox="0 0 20 20">
                                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="ck ck-button__label">Upload Gambar</span>
                            `;
                        });
                    }
                };
                input.click();
            });
            
            // Tambahkan separator dan tombol ke toolbar
            toolbarElement.appendChild(separator);
            toolbarElement.appendChild(imageButton);
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endpush 
@extends('layouts.admin')

@section('title', 'Edit Template WhatsApp')

@section('header-title', 'Edit Template WhatsApp')
@section('header-subtitle', 'Ubah template pesan WhatsApp')

@section('header-button')
    <a href="{{ route('admin.whatsapp-templates.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
        Kembali
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Form -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Form Edit Template WhatsApp</h2>
                <p class="text-gray-600 text-sm">Ubah template pesan WhatsApp untuk notifikasi otomatis</p>
            </div>
            
            <div class="p-6">
                <form action="{{ route('admin.whatsapp-templates.update', $template->id) }}" method="POST" id="editTemplateForm">
                    @csrf
                    @method('PUT')
                    <!-- Debug info -->
                    <input type="hidden" name="debug" value="1">
                    <input type="hidden" name="timestamp" value="{{ time() }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Template <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                                   id="name" name="name" value="{{ old('name', $template->name) }}" 
                                   placeholder="Contoh: Template Konfirmasi Donasi" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Template <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror" 
                                    id="type" name="type" required>
                                <option value="">Pilih tipe template</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('type', $template->type) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Template <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror" 
                               id="title" name="title" value="{{ old('title', $template->title) }}" 
                               placeholder="Contoh: 🎉 Terima Kasih Atas Donasi Anda!" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="template" class="block text-sm font-medium text-gray-700 mb-2">
                            Template Pesan <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('template') border-red-500 @enderror" 
                                  id="template" name="template" rows="15" 
                                  placeholder="Masukkan template pesan dengan variabel {variable_name}" required>{{ old('template', $template->template) }}</textarea>
                        @error('template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            Gunakan variabel dengan format {variable_name}. Contoh: {donor_name}, {campaign_title}, dll.
                        </p>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Deskripsi singkat tentang template ini">{{ old('description', $template->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                   id="is_active" name="is_active" 
                                   {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Aktifkan template ini</span>
                        </label>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200"
                                onclick="console.log('Button clicked');">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Update Template
                        </button>
                        <a href="{{ route('admin.whatsapp-templates.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors duration-200">
                            <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Variables Info -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Variabel yang Tersedia</h3>
            </div>
            <div class="p-6">
                <div id="variables-info">
                    <p class="text-gray-500">Pilih tipe template untuk melihat variabel yang tersedia.</p>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Tips Template</h3>
            </div>
            <div class="p-6">
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-sm text-gray-700">Gunakan emoji untuk membuat pesan lebih menarik</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-sm text-gray-700">Gunakan *teks* untuk bold di WhatsApp</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-sm text-gray-700">Gunakan _teks_ untuk italic di WhatsApp</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-sm text-gray-700">Gunakan `teks` untuk monospace di WhatsApp</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-sm text-gray-700">Variabel akan otomatis diganti dengan data yang sesuai</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
console.log('=== SCRIPT LOADED ===');
console.log('jQuery version:', typeof $ !== 'undefined' ? $.fn.jquery : 'jQuery not loaded');

$(document).ready(function() {
    console.log('=== DOCUMENT READY ===');
    // Debug form submission
    $('#editTemplateForm').on('submit', function(e) {
        console.log('=== FORM SUBMISSION DEBUG ===');
        console.log('Form submitted');
        console.log('Form action:', $(this).attr('action'));
        console.log('Form method:', $(this).attr('method'));
        console.log('CSRF token:', $('input[name="_token"]').val());
        console.log('Method override:', $('input[name="_method"]').val());
        console.log('Form data:', $(this).serialize());
        
        // Log individual field values
        console.log('Name:', $('#name').val());
        console.log('Type:', $('#type').val());
        console.log('Title:', $('#title').val());
        console.log('Template:', $('#template').val());
        console.log('Description:', $('#description').val());
        console.log('Is Active:', $('#is_active').is(':checked'));
        
        // Cek apakah ada field yang kosong
        if (!$('#name').val()) {
            console.error('Name field is empty!');
            e.preventDefault();
            return false;
        }
        if (!$('#type').val()) {
            console.error('Type field is empty!');
            e.preventDefault();
            return false;
        }
        if (!$('#title').val()) {
            console.error('Title field is empty!');
            e.preventDefault();
            return false;
        }
        if (!$('#template').val()) {
            console.error('Template field is empty!');
            e.preventDefault();
            return false;
        }
        
        console.log('=== FORM VALIDATION PASSED ===');
        
        // Tambahkan loading indicator
        $('button[type="submit"]').prop('disabled', true).html('<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Menyimpan...');
    });
    const variables = {
        'donation_confirmation': {
            'donor_name': 'Nama donatur',
            'campaign_title': 'Judul kampanye',
            'amount': 'Nominal donasi (format: 1.000.000)',
            'payment_method': 'Metode pembayaran',
            'payment_status': 'Status pembayaran',
            'donation_id': 'ID donasi',
            'expired_at': 'Batas waktu pembayaran',
            'payment_url': 'URL halaman pembayaran'
        },
        'payment_reminder': {
            'donor_name': 'Nama donatur',
            'campaign_title': 'Judul kampanye',
            'amount': 'Nominal donasi (format: 1.000.000)',
            'hours_left': 'Sisa waktu dalam jam',
            'donation_id': 'ID donasi',
            'payment_url': 'URL halaman pembayaran'
        },
        'payment_success': {
            'donor_name': 'Nama donatur',
            'campaign_title': 'Judul kampanye',
            'amount': 'Nominal donasi (format: 1.000.000)',
            'donation_id': 'ID donasi'
        }
    };

    $('#type').change(function() {
        const selectedType = $(this).val();
        let html = '';

        if (selectedType && variables[selectedType]) {
            html = '<ul class="list-unstyled">';
            Object.entries(variables[selectedType]).forEach(([key, description]) => {
                html += `<li class="mb-2">
                    <code class="text-primary">{${key}}</code>
                    <br><small class="text-muted">${description}</small>
                </li>`;
            });
            html += '</ul>';
        } else {
            html = '<p class="text-muted">Pilih tipe template untuk melihat variabel yang tersedia.</p>';
        }

        $('#variables-info').html(html);
    });

    // Trigger change event on page load
    $('#type').trigger('change');
});
</script>
@endpush 
@extends('layouts.admin')

@section('title', 'Detail Template WhatsApp')

@section('header-title', 'Detail Template WhatsApp')
@section('header-subtitle', 'Lihat detail template pesan WhatsApp')

@section('header-button')
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.whatsapp-templates.edit', $template->id) }}" 
           class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200">
            <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
            Edit
        </a>
        <a href="{{ route('admin.whatsapp-templates.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Template Info -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informasi Template</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Template</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $template->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipe Template</dt>
                                <dd class="mt-1">
                                    @switch($template->type)
                                        @case('donation_confirmation')
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                Konfirmasi Donasi
                                            </span>
                                            @break
                                        @case('payment_reminder')
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                                Reminder Pembayaran
                                            </span>
                                            @break
                                        @case('payment_success')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                Konfirmasi Pembayaran Berhasil
                                            </span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                {{ $template->type }}
                                            </span>
                                    @endswitch
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Judul</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $template->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($template->is_active) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                        {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $template->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Diupdate</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $template->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            @if($template->description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $template->description }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Message -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Template Pesan</h2>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <pre class="text-sm text-gray-800 whitespace-pre-wrap font-mono">{{ $template->template }}</pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Variables -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Variabel yang Tersedia</h3>
            </div>
            <div class="p-6">
                @if($template->variables && count($template->variables) > 0)
                    <ul class="space-y-3">
                        @foreach($template->variables as $key => $description)
                            <li>
                                <code class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">{{{ $key }}}</code>
                                <p class="mt-1 text-xs text-gray-600">{{ $description }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-sm">Tidak ada variabel yang tersedia.</p>
                @endif
            </div>
        </div>

        <!-- Preview -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Preview Pesan</h3>
            </div>
            <div class="p-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                    <div class="flex items-center">
                        <i data-lucide="message-circle" class="w-4 h-4 text-green-600 mr-2"></i>
                        <span class="text-sm font-medium text-green-800">WhatsApp Preview</span>
                    </div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 max-h-80 overflow-y-auto">
                    <div class="text-sm text-gray-800 whitespace-pre-wrap font-sans">
                        @php
                            // Sample data untuk preview
                            $sampleData = [
                                'donor_name' => 'Ahmad Rahman',
                                'campaign_title' => 'Bantu Korban Bencana Alam',
                                'amount' => '500.000',
                                'payment_method' => 'Transfer Bank',
                                'payment_status' => 'Menunggu Pembayaran',
                                'donation_id' => '12345',
                                'expired_at' => '25/12/2024 23:59',
                                'payment_url' => 'https://example.com/payment/12345',
                                'hours_left' => '12'
                            ];
                            
                            $previewMessage = $template->replaceVariables($sampleData);
                        @endphp
                        {{ $previewMessage }}
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs text-blue-700">
                            Preview menggunakan data contoh. Pesan asli akan menggunakan data donasi yang sebenarnya.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
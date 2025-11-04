@extends('layouts.admin')

@section('title', 'Manajemen Rekening Bank')

@section('header-title', 'Manajemen Rekening Bank')
@section('header-subtitle', 'Kelola rekening bank untuk donasi')

@section('header-button')
    <a href="{{ route('admin.bank-accounts.create') }}" class="inline-flex items-center px-2 py-1 md:px-3 md:py-1.5 text-xs md:text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
        <i data-lucide="plus" class="w-3 h-3 md:w-4 md:h-4 mr-1"></i>
        Tambah Rekening
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Integrasi Tripay</h2>
        </div>
        
        <div class="p-6">
            @php
                $tripay = new \App\Services\TripayService();
                $channels = $tripay->getVAPaymentMethods();
                $connectionStatus = $tripay->testConnection();
                $enabledChannels = \App\Models\TripayChannel::enabled()->active()->count();
                $totalChannels = \App\Models\TripayChannel::count();
            @endphp
            
            <div class="flex items-center gap-6 mb-6">
                <div class="flex-shrink-0">
                    <img src="https://tripay.co.id/images/logo-tripay.png" alt="Tripay" class="w-16 h-16 object-contain">
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-lg font-semibold text-gray-900">Status Tripay</span>
                        @if($connectionStatus['success'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                Terhubung
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                Gagal Terhubung
                            </span>
                        @endif
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Channel Aktif:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $enabledChannels }} / {{ $totalChannels }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Status API:</span>
                            <span class="text-sm font-medium {{ $connectionStatus['success'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $connectionStatus['success'] ? 'Berfungsi' : 'Gagal' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($channels && count($channels) > 0)
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Channel Virtual Account aktif:</h3>
                    <ul class="flex flex-wrap gap-3">
                        @foreach($channels as $ch)
                            <li class="flex items-center gap-2 bg-gray-50 rounded px-3 py-1.5 border border-gray-100">
                                <img src="{{ $ch['logo'] }}" alt="{{ $ch['name'] }}" class="w-5 h-5 object-contain">
                                <span class="text-sm font-medium">{{ $ch['name'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-sm text-red-600 bg-red-50 p-3 rounded-md mt-4">
                    Tidak dapat mengambil data channel Tripay. Periksa API Key dan koneksi internet.
                </div>
            @endif
            
            @if(!$connectionStatus['success'])
                <div class="text-xs text-red-600 bg-red-50 p-3 rounded-md mt-4">
                    {{ $connectionStatus['message'] }}
                </div>
            @endif
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200">
            <a href="{{ route('admin.tripay-channels.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                <i data-lucide="settings" class="w-4 h-4 mr-1"></i>
                Kelola Channel
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Rekening Bank</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Logo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bank
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nomor Rekening
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Atas Nama
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
                    @forelse($bankAccounts as $account)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($account->logo)
                                    <img src="{{ Storage::url($account->logo) }}" 
                                         alt="Logo Bank" 
                                         class="h-8 w-8 rounded shadow border inline-block"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';" />
                                    <span class="inline-block w-8 h-8 bg-gray-100 rounded" style="display: none;">
                                        <i data-lucide="image" class="w-4 h-4 text-gray-400 mx-auto mt-2"></i>
                                    </span>
                                @else
                                    <span class="inline-block w-8 h-8 bg-gray-100 rounded"></span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-brand-blue/10 rounded-lg flex items-center justify-center mr-3">
                                        <i data-lucide="building-2" class="w-4 h-4 text-brand-blue"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $account->bank_name }}</div>
                                        @if($account->description)
                                            <div class="text-xs text-gray-500">{{ Str::limit($account->description, 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">{{ $account->account_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $account->account_holder }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($account->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.bank-accounts.show', $account->id) }}" 
                                       class="text-brand-blue hover:text-brand-blue/80">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.bank-accounts.edit', $account->id) }}" 
                                       class="text-yellow-600 hover:text-yellow-800">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.bank-accounts.destroy', $account->id) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekening ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="building-2" class="w-8 h-8 text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 mb-4">Belum ada rekening bank</p>
                                <a href="{{ route('admin.bank-accounts.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                    Tambah Rekening Pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
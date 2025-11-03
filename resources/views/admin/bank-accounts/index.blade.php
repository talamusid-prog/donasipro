@extends('layouts.admin')

@section('title', 'Manajemen Rekening Bank')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Rekening Bank</h1>
        <a href="{{ route('admin.bank-accounts.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Tambah Rekening
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @php
        $tripay = new \App\Services\TripayService();
        $channels = $tripay->getVAPaymentMethods();
    @endphp
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex items-center gap-6">
            <div class="flex-shrink-0">
                <img src="https://tripay.co.id/images/logo-tripay.png" alt="Tripay" class="w-16 h-16 object-contain">
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-lg font-semibold text-gray-900">Integrasi Tripay</span>
                    @if($channels && count($channels) > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> Terhubung
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i> Gagal Terhubung
                        </span>
                    @endif
                </div>
                <div class="text-sm text-gray-700 mb-2">
                    @if($channels && count($channels) > 0)
                        <span>Channel Virtual Account aktif:</span>
                        <ul class="flex flex-wrap gap-4 mt-2">
                            @foreach($channels as $ch)
                                <li class="flex items-center gap-2 bg-gray-50 rounded px-3 py-1">
                                    <img src="{{ $ch['logo'] }}" alt="{{ $ch['name'] }}" class="w-6 h-6 object-contain">
                                    <span class="font-medium">{{ $ch['name'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-red-600">Tidak dapat mengambil data channel Tripay. Periksa API Key dan koneksi internet.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tripay Status -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <i data-lucide="credit-card" class="w-6 h-6 text-blue-600 mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">Status Tripay</h3>
            </div>
            @php
                $tripay = new \App\Services\TripayService();
                $connectionStatus = $tripay->testConnection();
                $enabledChannels = \App\Models\TripayChannel::enabled()->active()->count();
                $totalChannels = \App\Models\TripayChannel::count();
            @endphp
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
        <div class="space-y-3">
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
            @if(!$connectionStatus['success'])
                <div class="text-xs text-red-600 bg-red-50 p-2 rounded">
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

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
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
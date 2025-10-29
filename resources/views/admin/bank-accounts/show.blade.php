@extends('layouts.admin')

@section('title', 'Detail Rekening Bank')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <a href="{{ route('admin.bank-accounts.index') }}" class="text-gray-600 hover:text-brand-blue mr-4">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Rekening Bank</h1>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.bank-accounts.edit', $bankAccount->id) }}" 
               class="btn-secondary">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit
            </a>
            <form action="{{ route('admin.bank-accounts.destroy', $bankAccount->id) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekening ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="space-y-6">
            <!-- Bank Info -->
            <div class="flex items-center">
                <div class="w-16 h-16 bg-brand-blue/10 rounded-xl flex items-center justify-center mr-4">
                    <i data-lucide="building-2" class="w-8 h-8 text-brand-blue"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $bankAccount->bank_name }}</h2>
                    <p class="text-gray-500">Rekening Bank</p>
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <span class="text-sm font-medium text-gray-700 mr-2">Status:</span>
                @if($bankAccount->is_active)
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
            </div>

            <!-- Account Details -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                <h3 class="font-semibold text-gray-900">Informasi Rekening</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Rekening</label>
                        <p class="text-lg font-mono text-gray-900">{{ $bankAccount->account_number }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Atas Nama</label>
                        <p class="text-lg text-gray-900">{{ $bankAccount->account_holder }}</p>
                    </div>
                </div>

                @if($bankAccount->description)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                        <p class="text-gray-900">{{ $bankAccount->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Timestamps -->
            <div class="border-t border-gray-200 pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                    <div>
                        <span class="font-medium">Dibuat:</span> {{ $bankAccount->created_at->format('d M Y H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">Diperbarui:</span> {{ $bankAccount->updated_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
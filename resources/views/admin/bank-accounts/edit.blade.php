@extends('layouts.admin')

@section('title', 'Edit Rekening Bank')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.bank-accounts.index') }}" class="text-gray-600 hover:text-brand-blue mr-4">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Rekening Bank</h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.bank-accounts.update', $bankAccount->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Bank Name -->
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Bank <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="bank_name" 
                           name="bank_name" 
                           value="{{ old('bank_name', $bankAccount->bank_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent"
                           placeholder="Contoh: BCA, Mandiri, BNI">
                    @error('bank_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Number -->
                <div>
                    <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="account_number" 
                           name="account_number" 
                           value="{{ old('account_number', $bankAccount->account_number) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent font-mono"
                           placeholder="Contoh: 1234567890">
                    @error('account_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Holder -->
                <div>
                    <label for="account_holder" class="block text-sm font-medium text-gray-700 mb-2">
                        Atas Nama <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="account_holder" 
                           name="account_holder" 
                           value="{{ old('account_holder', $bankAccount->account_holder) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent"
                           placeholder="Nama pemilik rekening">
                    @error('account_holder')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent"
                              placeholder="Deskripsi tambahan tentang rekening ini">{{ old('description', $bankAccount->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $bankAccount->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-brand-blue shadow-sm focus:border-brand-blue focus:ring focus:ring-brand-blue focus:ring-opacity-50">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">
                            Rekening aktif (dapat digunakan untuk pembayaran)
                        </label>
                    </div>
                </div>

                <!-- Logo -->
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                        Logo Bank (Opsional)
                    </label>
                    @if($bankAccount->logo)
                        <div class="mb-2">
                            <img src="{{ Storage::url($bankAccount->logo) }}" alt="Logo Bank" class="h-12 inline-block rounded shadow border" />
                        </div>
                    @endif
                    <input type="file" id="logo" name="logo" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    @error('logo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.bank-accounts.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-brand-blue text-white rounded-lg hover:bg-brand-blue/90 transition">
                    Update Rekening
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 
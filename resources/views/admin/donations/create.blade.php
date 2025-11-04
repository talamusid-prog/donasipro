@extends('layouts.admin')

@section('title', 'Tambah Donasi')

@section('header-title', 'Tambah Donasi')
@section('header-subtitle', 'Tambahkan data donasi baru')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.donations.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="campaign_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Campaign</label>
                <select id="campaign_id" name="campaign_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">Pilih Campaign</option>
                    @foreach($campaigns as $campaign)
                        <option value="{{ $campaign->id }}" {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>
                            {{ $campaign->title }}
                        </option>
                    @endforeach
                </select>
                @error('campaign_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="donor_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Donatur</label>
                <input type="text" id="donor_name" name="donor_name" value="{{ old('donor_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                @error('donor_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="donor_email" class="block text-sm font-medium text-gray-700 mb-1">Email Donatur</label>
                <input type="email" id="donor_email" name="donor_email" value="{{ old('donor_email') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                @error('donor_email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="donor_phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon Donatur</label>
                <input type="text" id="donor_phone" name="donor_phone" value="{{ old('donor_phone') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                @error('donor_phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Donasi (Rp)</label>
                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" min="1000" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                @error('amount')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                <select id="payment_method" name="payment_method" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">Pilih Metode Pembayaran</option>
                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="ewallet" {{ old('payment_method') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                    <option value="manual" {{ old('payment_method') == 'manual' ? 'selected' : '' }}>Manual</option>
                </select>
                @error('payment_method')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                <select id="payment_status" name="payment_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ old('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="expired" {{ old('payment_status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
                @error('payment_status')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Pesan (Opsional)</label>
                <textarea id="message" name="message" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('message') }}</textarea>
            </div>
            
            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Donasi Anonim</span>
                </label>
            </div>
            
            <div class="flex items-center justify-end">
                <a href="{{ route('admin.donations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 mr-3">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
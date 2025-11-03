@extends('layouts.app')

@section('title', 'Daftar - Donasi Apps')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-brand-blue/10 via-white to-brand-blue/5">
    <div class="w-full max-w-sm">
        <div class="card border border-brand-blue/20 shadow-xl rounded-2xl bg-white">
            <div class="p-6 space-y-1 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-brand-blue/10">
                    <i data-lucide="user-plus" class="h-6 w-6 text-brand-blue"></i>
                </div>
                <h2 class="text-2xl font-bold text-brand-blue">Daftar</h2>
                <p class="text-gray-600 text-sm">
                    Buat akun baru untuk mulai berdonasi
                </p>
            </div>
            <form method="POST" action="{{ route('register') }}" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label for="name" class="text-sm font-semibold text-brand-blue">Nama Lengkap</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            class="input-field @error('name') border-red-500 @enderror bg-gray-50 focus:border-brand-blue focus:ring-brand-blue/30"
                            placeholder="Masukkan nama lengkap Anda"
                            required
                            autocomplete="name"
                        >
                        @error('name')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-semibold text-brand-blue">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            class="input-field @error('email') border-red-500 @enderror bg-gray-50 focus:border-brand-blue focus:ring-brand-blue/30"
                            placeholder="Masukkan email Anda"
                            required
                            autocomplete="email"
                        >
                        @error('email')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-semibold text-brand-blue">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="input-field @error('password') border-red-500 @enderror bg-gray-50 focus:border-brand-blue focus:ring-brand-blue/30"
                            placeholder="Masukkan password Anda"
                            required
                            autocomplete="new-password"
                        >
                        @error('password')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="password_confirmation" class="text-sm font-semibold text-brand-blue">Konfirmasi Password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            class="input-field bg-gray-50 focus:border-brand-blue focus:ring-brand-blue/30"
                            placeholder="Konfirmasi password Anda"
                            required
                            autocomplete="new-password"
                        >
                    </div>
                </div>
                <button type="submit" class="mt-6 w-full btn-primary bg-brand-blue hover:bg-brand-blue/90 text-white font-bold shadow">
                    Daftar
                </button>
            </form>
            <div class="p-6 pt-0">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Atau</span>
                    </div>
                </div>
                <div class="mt-6">
                    <p class="text-center text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="font-bold text-brand-blue hover:underline">
                            Masuk sekarang
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
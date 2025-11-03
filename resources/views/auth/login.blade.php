@extends('layouts.app')

@section('title', 'Login - Donasi Apps')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-brand-blue/10 via-white to-brand-blue/5">
    <div class="w-full max-w-sm">
        <div class="card border border-brand-blue/20 shadow-xl rounded-2xl bg-white">
            <div class="p-6 space-y-1 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-brand-blue/10">
                    <i data-lucide="log-in" class="h-6 w-6 text-brand-blue"></i>
                </div>
                <h2 class="text-2xl font-bold text-brand-blue">Login</h2>
                <p class="text-gray-600 text-sm">
                    Masuk menggunakan email dan password Anda
                </p>
            </div>
            <form method="POST" action="{{ route('login') }}" class="p-6">
                @csrf
                <div class="space-y-4">
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
                            autocomplete="current-password"
                        >
                        @error('password')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-blue shadow-sm focus:border-brand-blue focus:ring focus:ring-brand-blue focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-brand-blue hover:underline">
                                Lupa password?
                            </a>
                        @endif
                    </div>
                </div>
                <button type="submit" class="mt-6 w-full btn-primary bg-brand-blue hover:bg-brand-blue/90 text-white font-bold shadow">
                    @if(session('loading'))
                        <div class="flex items-center justify-center">
                            <i data-lucide="loader-2" class="mr-2 h-4 w-4 animate-spin"></i>
                            Memproses...
                        </div>
                    @else
                        Login
                    @endif
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
                        Belum punya akun? 
                        <a href="{{ route('register') }}" class="font-bold text-brand-blue hover:underline">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
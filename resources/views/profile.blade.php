@extends('layouts.app')

@section('title', 'Profil Saya - Donasi Apps')

@section('content')
<div class="px-4 py-6 max-w-lg mx-auto">
    <!-- Profile Header -->
    <div class="text-center mb-8">
        <div class="w-24 h-24 bg-brand-blue/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-brand-blue text-3xl font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h1>
        <p class="text-gray-600">{{ auth()->user()->email }}</p>
        <div class="mt-4 text-sm text-gray-500">
            Bergabung sejak {{ auth()->user()->created_at->format('d M Y') }}
        </div>
    </div>

    <!-- Notification -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Oops!</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <!-- Tabs -->
    <div x-data="{ tab: 'profile' }" class="w-full">
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button @click="tab = 'profile'" 
                        :class="{'border-brand-blue text-brand-blue': tab === 'profile', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'profile'}"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                    Informasi Akun
                </button>
                <button @click="tab = 'password'"
                        :class="{'border-brand-blue text-brand-blue': tab === 'password', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'password'}"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                    Ubah Password
                </button>
            </nav>
        </div>

        <!-- Profile Form -->
        <div x-show="tab === 'profile'" class="card p-6">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" class="input-field" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="input-field" required>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        Update Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Form -->
        <div x-show="tab === 'password'" class="card p-6">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password Saat Ini
                        </label>
                        <input type="password" id="current_password" name="current_password" class="input-field" required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password Baru
                        </label>
                        <input type="password" id="password" name="password" class="input-field" required>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Konfirmasi Password Baru
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="input-field" required>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
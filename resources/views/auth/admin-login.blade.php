@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] py-8">
    <div class="w-full max-w-xs bg-white rounded-xl shadow p-6 border border-gray-100">
        <h2 class="text-xl font-bold text-center mb-4">Login Admin</h2>
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input-field">
                @error('email')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium mb-1">Password</label>
                <input id="password" type="password" name="password" required class="input-field">
            </div>
            <button type="submit" class="btn-primary w-full">Login</button>
        </form>
    </div>
</div>
@endsection 
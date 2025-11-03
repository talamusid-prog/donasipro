@extends('layouts.admin')

@section('title', 'Detail User: ' . $user->name)

@section('header-title', 'Detail User')
@section('header-subtitle', $user->name)

@section('header-button')
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
        Kembali ke Daftar
    </a>
    <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 ml-2">
        <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
        Edit User
    </a>
    @if($user->id !== auth()->id())
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline ml-2" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                Hapus User
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Left Column: User Details --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-blue-600 text-2xl font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                        <p class="text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Role</span>
                        <p class="text-sm mt-1">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Bergabung</span>
                        <p class="text-sm text-gray-900 mt-1">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Total Donasi</span>
                        <p class="text-sm text-gray-900 mt-1">{{ $user->donations->count() }} kali</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Total Nominal</span>
                        <p class="text-sm text-gray-900 mt-1">Rp {{ number_format($user->donations->sum('amount'), 0, ',', '.') }}</p>
                    </div>
                </div>

                @if($user->role === 'user')
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ubah Role</h3>
                        <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="flex items-center space-x-3">
                            @csrf
                            @method('PATCH')
                            <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Update Role
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Recent Donations --}}
    <div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Donasi Terakhir</h3>
            </div>
            <div class="p-6">
                @if($user->donations->count() > 0)
                    <div class="space-y-4">
                        @foreach($user->donations->sortByDesc('created_at')->take(10) as $donation)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $donation->campaign->title ?? 'Campaign tidak ditemukan' }}</h4>
                                    <span class="text-sm font-medium text-green-600">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>{{ $donation->created_at->format('d/m/Y') }}</span>
                                    <span>{{ $donation->status ?? 'Pending' }}</span>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($user->donations->count() > 10)
                            <p class="text-center text-sm text-gray-500">
                                Dan {{ $user->donations->count() - 10 }} donasi lainnya...
                            </p>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Belum ada donasi.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
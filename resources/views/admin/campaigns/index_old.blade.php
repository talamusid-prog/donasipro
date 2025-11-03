<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Campaign - Admin Panel</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('build/assets/app-CfiNuBOo.css') }}">
    <script src="{{ asset('build/assets/app-DaBYqt0m.js') }}" defer></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg flex flex-col">
            <div class="h-20 flex items-center justify-center border-b">
                <span class="font-bold text-xl text-blue-700">Admin Panel</span>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 rounded hover:bg-blue-100">Dashboard</a>
                <a href="{{ route('admin.campaigns.index') }}" class="block py-2 px-4 rounded bg-blue-100 font-semibold text-blue-700">Kelola Campaign</a>
                <a href="{{ route('admin.donations.index') }}" class="block py-2 px-4 rounded hover:bg-blue-100">Data Donasi</a>
                <a href="{{ route('admin.users.index') }}" class="block py-2 px-4 rounded hover:bg-blue-100">Data User</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-8">
                    @csrf
                    <button type="submit" class="w-full py-2 px-4 rounded bg-red-100 text-red-700 hover:bg-red-200 font-semibold">Logout</button>
                </form>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="h-20 bg-white shadow flex items-center justify-between px-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Kelola Campaign</h1>
                    <p class="text-gray-500 text-sm">Manajemen campaign donasi</p>
                </div>
                <a href="{{ route('admin.campaigns.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Tambah Campaign
                </a>
            </header>
            
            <!-- Content -->
            <main class="flex-1 p-8">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Daftar Campaign</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organisasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terkumpul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verifikasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($campaigns as $campaign)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-lg object-cover" src="{{ $campaign->image_url ?? 'https://via.placeholder.com/40x40' }}" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $campaign->title }}</div>
                                                <div class="text-sm text-gray-500">{{ $campaign->category_label }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $campaign->organization }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                            {{ $campaign->category_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($campaign->section === 'featured') bg-purple-100 text-purple-800
                                            @elseif($campaign->section === 'urgent') bg-red-100 text-red-800
                                            @elseif($campaign->section === 'popular') bg-orange-100 text-orange-800
                                            @elseif($campaign->section === 'ending_soon') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ $campaign->section_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</div>
                                        <div class="text-sm text-gray-500">{{ number_format($campaign->progress_percentage, 1) }}%</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($campaign->status === 'active') bg-green-100 text-green-800
                                            @elseif($campaign->status === 'completed') bg-blue-100 text-blue-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($campaign->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($campaign->is_verified) bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $campaign->is_verified ? 'Terverifikasi' : 'Belum Verifikasi' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.campaigns.show', $campaign) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                                            <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus campaign ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $campaigns->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html> 
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Donasi Apps</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('build/assets/app-CfiNuBOo.css') }}">
    <script src="{{ asset('build/assets/app-DaBYqt0m.js') }}" defer></script>
    
    <!-- Custom CSS for Primary Color -->
    @if(isset($customCSS))
    <style>
        {!! $customCSS !!}
    </style>
    @endif
    
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg flex flex-col fixed inset-y-0 left-0 z-50">
            <div class="h-20 flex items-center justify-center border-b flex-shrink-0">
                <span class="font-bold text-xl text-blue-700">Admin Panel</span>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center py-2 px-4 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 font-semibold text-blue-700' : 'hover:bg-blue-100' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.campaigns.index') }}" class="flex items-center py-2 px-4 rounded {{ request()->routeIs('admin.campaigns.*') ? 'bg-blue-100 font-semibold text-blue-700' : 'hover:bg-blue-100' }}">
                    <i data-lucide="heart" class="w-5 h-5 mr-3"></i>
                    Kelola Campaign
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center py-2 px-4 rounded {{ request()->routeIs('admin.categories.*') ? 'bg-blue-100 font-semibold text-blue-700' : 'hover:bg-blue-100' }}">
                    <i data-lucide="tag" class="w-5 h-5 mr-3"></i>
                    Kelola Kategori
                </a>
                <a href="{{ route('admin.donations.index') }}" class="flex items-center py-2 px-4 rounded {{ request()->routeIs('admin.donations.*') ? 'bg-blue-100 font-semibold text-blue-700' : 'hover:bg-blue-100' }}">
                    <i data-lucide="gift" class="w-5 h-5 mr-3"></i>
                    Data Donasi
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center py-2 px-4 rounded {{ request()->routeIs('admin.users.*') ? 'bg-blue-100 font-semibold text-blue-700' : 'hover:bg-blue-100' }}">
                    <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                    Data User
                </a>
                <a href="{{ route('admin.sliders.index') }}" class="flex items-center py-2 px-4 rounded {{ request()->routeIs('admin.sliders.*') ? 'bg-blue-100 font-semibold text-blue-700' : 'hover:bg-blue-100' }}">
                    <i data-lucide="image" class="w-5 h-5 mr-3"></i>
                    Kelola Slider
                </a>
                <a href="{{ route('admin.bank-accounts.index') }}" class="flex items-center py-2 px-4 rounded {{ request()->routeIs('admin.bank-accounts.*') ? 'bg-blue-100 font-semibold text-blue-700' : 'hover:bg-blue-100' }}">
                    <i data-lucide="building-2" class="w-5 h-5 mr-3"></i>
                    Rekening Bank
                </a>
                <a href="{{ route('admin.tripay-channels.index') }}" class="flex items-center py-2 px-4 rounded {{ request()->routeIs('admin.tripay-channels.*') ? 'bg-blue-100 font-semibold text-blue-700' : 'hover:bg-blue-100' }}">
                    <i data-lucide="credit-card" class="w-5 h-5 mr-3"></i>
                    Channel Tripay
                </a>

                <a href="{{ route('admin.whatsapp-templates.index') }}" class="flex items-center py-2 px-4 rounded {{ request()->routeIs('admin.whatsapp-templates.*') ? 'bg-blue-100 font-semibold text-blue-700' : 'hover:bg-blue-100' }}">
                    <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                    Template WhatsApp
                </a>
                <a href="{{ route('admin.wa-blast.index') }}" class="flex items-center py-2 px-4 rounded {{ request()->routeIs('admin.wa-blast.*') ? 'bg-blue-100 font-semibold text-blue-700' : 'hover:bg-blue-100' }}">
                    <i data-lucide="message-square" class="w-5 h-5 mr-3"></i>
                    WA Blast API
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center py-2 px-4 rounded {{ request()->routeIs('admin.settings.*') ? 'bg-blue-100 font-semibold text-blue-700' : 'hover:bg-blue-100' }}">
                    <i data-lucide="settings" class="w-5 h-5 mr-3"></i>
                    Pengaturan Aplikasi
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-8 flex-shrink-0">
                    @csrf
                    <button type="submit" class="w-full flex items-center py-2 px-4 rounded bg-red-50 text-red-700 hover:bg-red-100 font-semibold">
                        <i data-lucide="log-out" class="w-5 h-5 mr-3"></i>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 ml-64">
            <!-- Header -->
            <header class="h-20 bg-white shadow flex items-center justify-between px-8 flex-shrink-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">@yield('header-title')</h1>
                    <p class="text-gray-500 text-sm">@yield('header-subtitle')</p>
                </div>
                <div>
                    @yield('header-button')
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 p-8 overflow-y-auto">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        lucide.createIcons();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html> 
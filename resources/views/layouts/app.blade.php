<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $appSettings['app_name'] ?? 'Donasi Apps')</title>
    <meta name="description" content="{{ $appSettings['meta_description'] ?? 'Platform donasi online untuk membantu sesama' }}">
    <meta name="keywords" content="{{ $appSettings['meta_keywords'] ?? 'donasi, charity, bantuan, yayasan, peduli' }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.css">
    
    <!-- Styles -->
    @php
        // FORCE production mode untuk hosting (jangan gunakan Vite dev server)
        $isProduction = true; // Paksa production mode
    @endphp
    
    {{-- PRODUCTION MODE FORCED - NO MORE ERRORS --}}
    <link rel="stylesheet" href="{{ asset('build/assets/app-CfiNuBOo.css') }}">
    <script src="{{ asset('build/assets/app-DaBYqt0m.js') }}" defer></script>
    
    <!-- Custom CSS for Primary Color -->
    @if(isset($customCSS))
    <style>
        {!! $customCSS !!}
    </style>
    @endif
    
    @stack('styles')
    
    <!-- Analytics Scripts -->
    @php
        $analyticsService = new \App\Services\AnalyticsService();
    @endphp
    {!! $analyticsService->generateAnalyticsScript() !!}
    
    <!-- Styling untuk konten HTML dari CKEditor -->
    <style>
        /* Styling untuk konten HTML dari CKEditor */
        .prose img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 1rem 0;
        }
        .prose figure {
            margin: 1rem 0;
        }
        .prose figure img {
            display: block;
            margin: 0 auto;
        }
        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            color: #1f2937;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .prose h1 { font-size: 1.5rem; }
        .prose h2 { font-size: 1.25rem; }
        .prose h3 { font-size: 1.125rem; }
        .prose p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        .prose ul, .prose ol {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }
        .prose li {
            margin-bottom: 0.5rem;
        }
        .prose blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 1rem;
            margin: 1rem 0;
            font-style: italic;
            color: #6b7280;
        }
        .prose table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .prose table th,
        .prose table td {
            border: 1px solid #e5e7eb;
            padding: 0.5rem;
            text-align: left;
        }
        .prose table th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        .prose a {
            color: #3b82f6;
            text-decoration: underline;
        }
        .prose a:hover {
            color: #2563eb;
        }

        /* Bulatan orange di belakang header */
        .header-circle {
            position: fixed;
            top: -15px;
            right: 15px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ff8c42, #ffa726);
            border-radius: 50%;
            z-index: 40;
            opacity: 0.9;
            box-shadow: 0 4px 20px rgba(255, 140, 66, 0.4);
            transition: opacity 0.3s ease;
        }

        /* Responsive untuk desktop */
        @media (min-width: 768px) {
            .header-circle {
                right: calc(50% - 187.5px + 20px);
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    @if(request()->is('admin*'))
        <!-- Admin Layout - Full Desktop -->
        @yield('content')
    @else
        <!-- Mobile Layout -->
        <div class="mobile-container">
            <!-- Bulatan orange di belakang header -->
            @if(!request()->is('campaigns/*'))
            <div class="header-circle"></div>
            @endif

            <!-- Header -->
            @if(!request()->is('campaigns/*'))
            <header class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md bg-white/60 border-b border-gray-200 shadow-sm rounded-b-2xl">
                <div class="flex items-center justify-between h-16 px-4 gap-4">
                    <!-- Logo dan Search -->
                    <div class="flex items-center gap-4 flex-1">
                        <a href="{{ route('home') }}" class="text-lg font-bold text-brand-blue whitespace-nowrap">
                            {{ $appSettings['app_name'] ?? 'Donasi Apps' }}
                        </a>
                        <form action="{{ route('campaigns') }}" method="get" class="flex-1 hidden md:block">
                            <input type="text" name="q" placeholder="Coba cari 'Tolong menolong'" class="w-full bg-white rounded-full py-2 pl-5 pr-12 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                        </form>
                    </div>
                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-3">
                        @auth
                        <!-- Tombol profil dihapus -->
                        @endauth
                    </div>
                </div>
            </header>
            @endif

            <!-- Main Content -->
            <main class="min-h-screen {{ !request()->is('campaigns/*') ? 'pt-16 pb-20' : '' }}">
                @yield('content')
            </main>

            <!-- Mobile Bottom Navigation -->
            @if(!request()->is('campaigns/*'))
            <nav class="fixed bottom-0 left-0 right-0 border-t border-gray-200/50 z-50 backdrop-blur-md bg-white/60">
                <div class="flex justify-around py-2 relative">
                    <a href="{{ route('home') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:text-brand-blue transition-colors">
                        <i data-lucide="home" class="w-5 h-5 mb-1"></i>
                        <span class="text-xs">Beranda</span>
                    </a>
                    <a href="{{ route('campaigns') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:text-brand-blue transition-colors">
                        <i data-lucide="heart" class="w-5 h-5 mb-1"></i>
                        <span class="text-xs">Program</span>
                    </a>
                    
                    <!-- Kalkulator Zakat - Menu Menonjol di Tengah -->
                    <div class="relative">
                        <a href="{{ route('zakat-calculator') }}" class="flex flex-col items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 -mt-4">
                            <i data-lucide="calculator" class="w-6 h-6 text-white mb-0.5"></i>
                            <span class="text-xs text-white font-medium">Zakat</span>
                        </a>
                    </div>
                    
                    @auth
                    <a href="{{ route('profile') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:text-brand-blue transition-colors">
                        <i data-lucide="user" class="w-5 h-5 mb-1"></i>
                        <span class="text-xs">Profil</span>
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:text-brand-blue transition-colors">
                        <i data-lucide="log-in" class="w-5 h-5 mb-1"></i>
                        <span class="text-xs">Masuk</span>
                    </a>
                    @endauth
                    <a href="{{ route('about') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:text-brand-blue transition-colors">
                        <i data-lucide="info" class="w-5 h-5 mb-1"></i>
                        <span class="text-xs">About</span>
                    </a>
                </div>
            </nav>
            @endif
        </div>
    @endif

    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        lucide.createIcons();
        
        // Script untuk mengatur opacity bulatan orange berdasarkan scroll
        document.addEventListener('DOMContentLoaded', function() {
            const headerCircle = document.querySelector('.header-circle');
            
            if (headerCircle) {
                window.addEventListener('scroll', function() {
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    const fadeStart = 50; // Mulai fade setelah scroll 50px
                    const fadeEnd = 200; // Hilang total setelah scroll 200px
                    
                    if (scrollTop <= fadeStart) {
                        headerCircle.style.opacity = '0.9';
                    } else if (scrollTop >= fadeEnd) {
                        headerCircle.style.opacity = '0';
                    } else {
                        // Hitung opacity berdasarkan posisi scroll
                        const fadeRange = fadeEnd - fadeStart;
                        const currentScroll = scrollTop - fadeStart;
                        const opacity = 0.9 - (currentScroll / fadeRange) * 0.9;
                        headerCircle.style.opacity = opacity;
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html> 
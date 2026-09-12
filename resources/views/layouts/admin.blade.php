<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f8f7f4]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }} — fifa</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Alex+Brush&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time() }}">
    @endif
    
    <script defer src="https://unpkg.com/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="min-h-full flex bg-[#f8f7f4] font-sans antialiased text-charcoal" 
      x-data="{ 
          sidebarOpen: false, 
          cmsOpen: {{ request()->routeIs('admin.hero-slides.*') || request()->routeIs('admin.pages.*') || request()->routeIs('admin.blog.*') || request()->routeIs('admin.stores.*') ? 'true' : 'false' }} 
      }">

    <!-- ========================================================================= -->
    <!-- DESKTOP SIDEBAR (Sticky Left, Standard 260px)                             -->
    <!-- ========================================================================= -->
    <aside class="hidden lg:flex flex-col w-64 bg-white border-r border-sand/80 h-screen sticky top-0 flex-shrink-0 z-40 justify-between">
        
        <!-- Sidebar Top Header / Brand -->
        <div>
            <div class="h-16 px-5 border-b border-sand/70 flex items-center justify-between bg-white">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/fifa-logo.svg') }}" alt="fifa" class="h-6 w-auto object-contain">
                    <span class="bg-charcoal text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md shadow-2xs">
                        ADMIN
                    </span>
                </a>
                <a href="{{ route('home') }}" target="_blank" title="Lihat Toko Publik" class="p-1.5 text-stone hover:text-charcoal rounded-md hover:bg-sand/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>

            <!-- Navigation Links Scrollable Area -->
            <nav class="p-3.5 space-y-6 overflow-y-auto max-h-[calc(100vh-8.5rem)] text-body-sm">
                
                <!-- Section: UTAMA -->
                <div class="space-y-1">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-widest text-stone/80 mb-2">
                        Menu Utama
                    </div>
                    
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-charcoal text-white font-semibold shadow-xs' : 'text-charcoal/80 hover:bg-sand/40 hover:text-black' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Pesanan -->
                    <a href="{{ route('admin.orders.index') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('admin.orders.*') ? 'bg-charcoal text-white font-semibold shadow-xs' : 'text-charcoal/80 hover:bg-sand/40 hover:text-black' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span>Pesanan</span>
                        </div>
                        @php
                            $pendingOrdersCount = \App\Models\Order::whereIn('status', ['paid', 'processing'])->count();
                        @endphp
                        @if ($pendingOrdersCount > 0)
                            <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full {{ request()->routeIs('admin.orders.*') ? 'bg-white text-charcoal' : 'bg-charcoal text-white' }}">
                                {{ $pendingOrdersCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Katalog Produk -->
                    <a href="{{ route('admin.products.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('admin.products.*') ? 'bg-charcoal text-white font-semibold shadow-xs' : 'text-charcoal/80 hover:bg-sand/40 hover:text-black' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>Katalog Produk</span>
                    </a>

                    <!-- Kategori -->
                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('admin.categories.*') ? 'bg-charcoal text-white font-semibold shadow-xs' : 'text-charcoal/80 hover:bg-sand/40 hover:text-black' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Kategori</span>
                    </a>

                    <!-- Koleksi -->
                    <a href="{{ route('admin.collections.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('admin.collections.*') ? 'bg-charcoal text-white font-semibold shadow-xs' : 'text-charcoal/80 hover:bg-sand/40 hover:text-black' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                        <span>Koleksi Toko</span>
                    </a>
                </div>

                <!-- Section: MARKETING & INTERAKSI -->
                <div class="space-y-1">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-widest text-stone/80 mb-2">
                        Pemasaran & Ulasan
                    </div>

                    <!-- Kupon Promo -->
                    <a href="{{ route('admin.coupons.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('admin.coupons.*') ? 'bg-charcoal text-white font-semibold shadow-xs' : 'text-charcoal/80 hover:bg-sand/40 hover:text-black' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                        <span>Kupon Diskon</span>
                    </a>

                    <!-- Ulasan & Rating -->
                    <a href="{{ route('admin.reviews.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('admin.reviews.*') ? 'bg-charcoal text-white font-semibold shadow-xs' : 'text-charcoal/80 hover:bg-sand/40 hover:text-black' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <span>Ulasan & Rating</span>
                    </a>

                    <!-- Subscribers -->
                    <a href="{{ route('admin.subscribers.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('admin.subscribers.*') ? 'bg-charcoal text-white font-semibold shadow-xs' : 'text-charcoal/80 hover:bg-sand/40 hover:text-black' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>Subscribers</span>
                    </a>
                </div>

                <!-- Section: KONTEN CMS -->
                <div class="space-y-1">
                    <button type="button" 
                            @click="cmsOpen = !cmsOpen" 
                            class="w-full flex items-center justify-between px-3 text-[10px] font-bold uppercase tracking-widest text-stone/80 mb-2 cursor-pointer">
                        <span>Konten Toko (CMS)</span>
                        <svg class="w-3.5 h-3.5 transform transition-transform" :class="cmsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="cmsOpen" x-collapse class="space-y-1 pl-1">
                        <!-- Hero Slides -->
                        <a href="{{ route('admin.hero-slides.index') }}" 
                           class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-body-sm transition {{ request()->routeIs('admin.hero-slides.*') ? 'bg-sand/60 text-charcoal font-bold' : 'text-charcoal/70 hover:bg-sand/30 hover:text-black' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.hero-slides.*') ? 'bg-charcoal' : 'bg-stone/60' }}"></span>
                            <span>Hero Banner</span>
                        </a>

                        <!-- Halaman Statis -->
                        <a href="{{ route('admin.pages.index') }}" 
                           class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-body-sm transition {{ request()->routeIs('admin.pages.*') ? 'bg-sand/60 text-charcoal font-bold' : 'text-charcoal/70 hover:bg-sand/30 hover:text-black' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.pages.*') ? 'bg-charcoal' : 'bg-stone/60' }}"></span>
                            <span>Halaman Statis</span>
                        </a>

                        <!-- Blog & Jurnal -->
                        <a href="{{ route('admin.blog.index') }}" 
                           class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-body-sm transition {{ request()->routeIs('admin.blog.*') ? 'bg-sand/60 text-charcoal font-bold' : 'text-charcoal/70 hover:bg-sand/30 hover:text-black' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.blog.*') ? 'bg-charcoal' : 'bg-stone/60' }}"></span>
                            <span>Blog & Jurnal</span>
                        </a>

                        <!-- Lokasi Toko Fisik -->
                        <a href="{{ route('admin.stores.index') }}" 
                           class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-body-sm transition {{ request()->routeIs('admin.stores.*') ? 'bg-sand/60 text-charcoal font-bold' : 'text-charcoal/70 hover:bg-sand/30 hover:text-black' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.stores.*') ? 'bg-charcoal' : 'bg-stone/60' }}"></span>
                            <span>Toko Fisik</span>
                        </a>
                    </div>
                </div>

                <!-- Section: SISTEM & LAPORAN -->
                <div class="space-y-1">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-widest text-stone/80 mb-2">
                        Sistem & Pengaturan
                    </div>

                    <!-- Laporan Penjualan -->
                    <a href="{{ route('admin.reports.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('admin.reports.*') ? 'bg-charcoal text-white font-semibold shadow-xs' : 'text-charcoal/80 hover:bg-sand/40 hover:text-black' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Laporan Penjualan</span>
                    </a>

                    <!-- Pengaturan Toko -->
                    <a href="{{ route('admin.settings.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('admin.settings.*') ? 'bg-charcoal text-white font-semibold shadow-xs' : 'text-charcoal/80 hover:bg-sand/40 hover:text-black' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Pengaturan Toko</span>
                    </a>
                </div>

            </nav>
        </div>

        <!-- Sidebar User Card & Logout Bottom Area -->
        <div class="p-3.5 border-t border-sand/70 bg-sand/10">
            <div class="flex items-center justify-between gap-3 p-2 rounded-xl bg-white border border-sand/60">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-charcoal text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <span class="text-caption font-bold text-charcoal block truncate leading-tight">{{ Auth::user()->name ?? 'Administrator' }}</span>
                        <span class="text-[10px] text-stone block truncate">{{ Auth::user()->role === 'super_admin' ? 'Super Admin' : 'Admin' }}</span>
                    </div>
                </div>

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            title="Keluar dari Panel Admin"
                            class="p-1.5 text-stone hover:text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- ========================================================================= -->
    <!-- MOBILE SIDEBAR DRAWER (Slide-in)                                          -->
    <!-- ========================================================================= -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 lg:hidden"
         style="display: none;">
    </div>

    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-out duration-250 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 max-w-[280px] w-full bg-white shadow-2xl z-50 flex flex-col justify-between overflow-y-auto lg:hidden"
         style="display: none;">
        
        <div>
            <!-- Mobile Header Top -->
            <div class="flex items-center justify-between p-4 border-b border-sand">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/fifa-logo.svg') }}" alt="fifa" class="h-6 w-auto object-contain">
                    <span class="bg-charcoal text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md">ADMIN</span>
                </a>
                <button type="button" @click="sidebarOpen = false" class="p-2 text-charcoal hover:text-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Nav Items -->
            <nav class="p-4 space-y-4 text-body-sm">
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false" class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-charcoal text-white font-bold' : 'text-charcoal hover:bg-sand/30' }}">Dashboard</a>
                    <a href="{{ route('admin.orders.index') }}" @click="sidebarOpen = false" class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-charcoal text-white font-bold' : 'text-charcoal hover:bg-sand/30' }}">Pesanan</a>
                    <a href="{{ route('admin.products.index') }}" @click="sidebarOpen = false" class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.products.*') ? 'bg-charcoal text-white font-bold' : 'text-charcoal hover:bg-sand/30' }}">Katalog Produk</a>
                    <a href="{{ route('admin.categories.index') }}" @click="sidebarOpen = false" class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-charcoal text-white font-bold' : 'text-charcoal hover:bg-sand/30' }}">Kategori</a>
                    <a href="{{ route('admin.collections.index') }}" @click="sidebarOpen = false" class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.collections.*') ? 'bg-charcoal text-white font-bold' : 'text-charcoal hover:bg-sand/30' }}">Koleksi</a>
                </div>

                <div class="pt-2 border-t border-sand space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-stone block px-3 mb-1">Pemasaran</span>
                    <a href="{{ route('admin.coupons.index') }}" @click="sidebarOpen = false" class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.coupons.*') ? 'bg-charcoal text-white font-bold' : 'text-charcoal hover:bg-sand/30' }}">Kupon Diskon</a>
                    <a href="{{ route('admin.reviews.index') }}" @click="sidebarOpen = false" class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.reviews.*') ? 'bg-charcoal text-white font-bold' : 'text-charcoal hover:bg-sand/30' }}">Ulasan</a>
                    <a href="{{ route('admin.subscribers.index') }}" @click="sidebarOpen = false" class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.subscribers.*') ? 'bg-charcoal text-white font-bold' : 'text-charcoal hover:bg-sand/30' }}">Subscribers</a>
                </div>

                <div class="pt-2 border-t border-sand space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-stone block px-3 mb-1">Konten & CMS</span>
                    <a href="{{ route('admin.hero-slides.index') }}" @click="sidebarOpen = false" class="block px-3 py-1.5 text-charcoal hover:bg-sand/30 rounded-lg">Hero Banner</a>
                    <a href="{{ route('admin.pages.index') }}" @click="sidebarOpen = false" class="block px-3 py-1.5 text-charcoal hover:bg-sand/30 rounded-lg">Halaman Statis</a>
                    <a href="{{ route('admin.blog.index') }}" @click="sidebarOpen = false" class="block px-3 py-1.5 text-charcoal hover:bg-sand/30 rounded-lg">Blog & Jurnal</a>
                    <a href="{{ route('admin.stores.index') }}" @click="sidebarOpen = false" class="block px-3 py-1.5 text-charcoal hover:bg-sand/30 rounded-lg">Toko Fisik</a>
                </div>

                <div class="pt-2 border-t border-sand space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-stone block px-3 mb-1">Sistem</span>
                    <a href="{{ route('admin.reports.index') }}" @click="sidebarOpen = false" class="block px-3 py-2 rounded-lg text-charcoal hover:bg-sand/30">Laporan Penjualan</a>
                    <a href="{{ route('admin.settings.index') }}" @click="sidebarOpen = false" class="block px-3 py-2 rounded-lg text-charcoal hover:bg-sand/30">Pengaturan Toko</a>
                </div>
            </nav>
        </div>

        <div class="p-4 border-t border-sand bg-sand/10 space-y-2">
            <a href="{{ route('home') }}" target="_blank" class="btn-pill-light block text-center w-full text-caption py-2">
                Lihat Toko Publik ↗
            </a>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-center text-caption uppercase tracking-wider text-red-600 font-semibold py-2">
                    Keluar
                </button>
            </form>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MAIN CONTENT CONTAINER (Right Side)                                       -->
    <!-- ========================================================================= -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        
        <!-- Top Navbar -->
        <header class="h-16 bg-white/90 backdrop-blur-md border-b border-sand/70 sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Mobile Hamburger Button -->
                <button type="button" 
                        @click="sidebarOpen = true" 
                        class="lg:hidden p-2 text-charcoal hover:bg-sand/40 rounded-xl min-w-[40px] min-h-[40px] flex items-center justify-center transition"
                        aria-label="Buka Menu Sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Breadcrumbs / Page Title Context -->
                <div class="flex items-center gap-2 text-caption">
                    <span class="text-stone">fifa Admin</span>
                    <span class="text-stone/60">/</span>
                    <span class="font-bold text-charcoal uppercase tracking-wider">{{ $title ?? 'Dashboard' }}</span>
                </div>
            </div>

            <!-- Topbar Right Actions -->
            <div class="flex items-center gap-3 sm:gap-4">
                <a href="{{ route('home') }}" target="_blank" class="hidden sm:flex items-center gap-1.5 text-caption font-bold uppercase tracking-wider text-charcoal hover:text-black px-3 py-1.5 rounded-full border border-sand/80 hover:bg-sand/30 transition">
                    <span>Lihat Toko</span>
                    <span>↗</span>
                </a>

                <div class="h-6 w-px bg-sand hidden sm:block"></div>

                <div class="flex items-center gap-2">
                    <span class="text-caption font-semibold text-charcoal hidden sm:inline-block">
                        {{ Auth::user()->name }}
                    </span>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-pill-light text-caption px-3.5 py-1.5 min-h-[34px] font-semibold">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        @if (Auth::check() && Auth::user()->isDemo())
            <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-orange-600 text-white px-4 py-2.5 sm:px-8 flex flex-wrap items-center justify-between gap-3 text-caption font-medium shadow-sm z-20">
                <div class="flex items-center gap-2">
                    <span class="bg-black/30 text-white px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-200 animate-pulse"></span>
                        Mode Demo • {{ Auth::user()->role_label }}
                    </span>
                    <span>Setiap konten atau data yang Anda buat/ubah di akun demo ini akan <strong>otomatis terhapus / direset kembali dalam 10 menit</strong>.</span>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('demo.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh data perubahan demo sekarang?')">
                        @csrf
                        <button type="submit" class="bg-white/20 hover:bg-white text-white hover:text-charcoal px-3 py-1 rounded-full text-[11px] font-bold transition flex items-center gap-1.5 backdrop-blur-xs cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Reset Data Demo Sekarang</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Main Content Area -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full max-w-7xl mx-auto">
            @if (session('success'))
                <div class="bg-charcoal text-canvas p-4 rounded-2xl mb-6 text-body-sm flex items-center gap-3 shadow-md border border-white/10">
                    <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs flex-shrink-0">✓</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-700 text-canvas p-4 rounded-2xl mb-6 text-body-sm flex items-center gap-3 shadow-md">
                    <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs flex-shrink-0">✕</span>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Admin Simple Footer -->
        <footer class="px-8 py-4 border-t border-sand/50 text-center sm:text-left text-caption text-stone flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} fifa Store. Seluruh hak cipta dilindungi.</span>
            <div class="flex items-center gap-4 text-[11px] font-medium text-stone">
                <span>Midtrans Snap Ready</span>
                <span>•</span>
                <span>Biteship Logistics Active</span>
            </div>
        </footer>

    </div>

    @stack('scripts')
</body>
</html>

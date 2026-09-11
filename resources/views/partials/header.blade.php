@php
    $menCategory = \App\Models\Category::where('gender', 'men')->whereNull('parent_id')->with('children.children')->first();
    $womenCategory = \App\Models\Category::where('gender', 'women')->whereNull('parent_id')->with('children.children')->first();
    $collections = \App\Models\Collection::where('is_active', true)->take(4)->get();
@endphp

<header x-data="{
        isScrolled: false,
        activeMenu: null,
        searchOpen: false,
        searchQuery: '',
        searchResults: [],
        searchLoading: false,
        mobileMenuOpen: false,
        mobileCategoryTab: 'men',
        mobileAccordion: {
            menJackets: true,
            menTops: false,
            womenJackets: true,
            womenTops: false
        },
        async doLiveSearch() {
            const q = this.searchQuery.trim();
            if (q.length < 2) {
                this.searchResults = [];
                this.searchLoading = false;
                return;
            }
            this.searchLoading = true;
            try {
                const res = await fetch(`{{ route('search.live') }}?q=${encodeURIComponent(q)}`);
                const data = await res.json();
                if (data.success) {
                    this.searchResults = data.products;
                }
            } catch (e) {
                console.error('Search error', e);
            } finally {
                this.searchLoading = false;
            }
        },
        selectTag(tag) {
            this.searchQuery = tag;
            this.doLiveSearch();
            this.$nextTick(() => { this.$refs.searchInput?.focus(); });
        }
    }" 
    @scroll.window="isScrolled = (window.pageYOffset > 10)"
    @keydown.window.escape="searchOpen = false"
    @keydown.window.ctrl.k.prevent="searchOpen = true; $nextTick(() => { $refs.searchInput?.focus(); })"
    @keydown.window.cmd.k.prevent="searchOpen = true; $nextTick(() => { $refs.searchInput?.focus(); })"
    @open-search.window="searchOpen = true; $nextTick(() => { $refs.searchInput?.focus(); })"
    class="sticky top-0 z-40 transition-all duration-200 px-3 sm:px-6 pt-2 pb-2 bg-transparent select-none">

    <div class="max-w-[1400px] mx-auto bg-white/95 backdrop-blur-md rounded-2xl border border-sand/70 shadow-xs px-4 sm:px-6 h-14 sm:h-16 flex items-center justify-between relative">
        
        <!-- Left: Mobile Hamburger & Brand Logo -->
        <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0 z-10">
            <!-- Mobile Hamburger Button -->
            <button type="button" 
                    @click="mobileMenuOpen = true"
                    class="lg:hidden p-2 -ml-2 text-charcoal hover:text-black focus:outline-none min-w-[44px] min-h-[44px] flex items-center justify-center cursor-pointer"
                    aria-label="Buka Menu Navigasi">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center text-charcoal hover:opacity-85 transition">
                <img src="{{ asset('images/fifa-logo.svg') }}" alt="fifa" class="h-6 sm:h-7 w-auto object-contain">
            </a>
        </div>

        <!-- Center: Desktop Navigation (Perfect Absolute Center) -->
        <nav class="hidden lg:flex items-center space-x-8 absolute left-1/2 -translate-x-1/2" @mouseleave="activeMenu = null">
            <!-- DROP TERBARU -->
            <div>
                <a href="{{ route('collections.show', 'new-arrivals') }}" 
                   class="nav-label py-2 inline-block font-bold tracking-widest text-[12px] uppercase {{ request()->is('*new-arrivals*') ? 'border-b-2 border-charcoal' : '' }}">
                    DROP TERBARU
                </a>
            </div>

            <!-- SEMUA ITEM -->
            <div>
                <a href="{{ route('search.index') }}" 
                   class="nav-label py-2 inline-block font-bold tracking-widest text-[12px] uppercase {{ request()->is('search*') ? 'border-b-2 border-charcoal' : '' }}">
                    SEMUA ITEM
                </a>
            </div>

            <!-- MEN Dropdown -->
            <div class="relative" @mouseenter="activeMenu = 'men'">
                <a href="{{ route('categories.men') }}" 
                   class="nav-label py-2 inline-block font-bold tracking-widest text-[12px] uppercase {{ request()->is('men*') ? 'border-b-2 border-charcoal' : '' }}">
                    PRIA
                </a>
            </div>

            <!-- WOMEN Dropdown -->
            <div class="relative" @mouseenter="activeMenu = 'women'">
                <a href="{{ route('categories.women') }}" 
                   class="nav-label py-2 inline-block font-bold tracking-widest text-[12px] uppercase {{ request()->is('women*') ? 'border-b-2 border-charcoal' : '' }}">
                    WANITA
                </a>
            </div>

            <!-- PALING DIBURU -->
            <div>
                <a href="{{ route('collections.show', 'best-sellers') }}" 
                   class="nav-label py-2 inline-block font-bold tracking-widest text-[12px] uppercase {{ request()->is('*best-sellers*') ? 'border-b-2 border-charcoal' : '' }}">
                    PALING DIBURU
                </a>
            </div>
        </nav>

        <!-- Right: Utility Icons & Actions -->
        <div class="flex items-center space-x-1 sm:space-x-2 z-10">
            <!-- Search Button (Opens Live Search Modal) -->
            <button type="button" 
                    @click="searchOpen = true; $nextTick(() => { $refs.searchInput?.focus(); })"
                    class="p-2 text-charcoal hover:text-black min-w-[40px] min-h-[40px] flex items-center justify-center transition cursor-pointer"
                    aria-label="Pencarian Produk">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>

            <!-- Account / Auth Link -->
            @auth
                <div class="relative" x-data="{ accountOpen: false }" @click.away="accountOpen = false">
                    <button @click="accountOpen = !accountOpen" 
                            class="p-2 text-charcoal hover:text-black min-w-[40px] min-h-[40px] flex items-center justify-center transition"
                            aria-label="Menu Akun">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </button>
                    <div x-show="accountOpen" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-canvas border border-sand rounded-card shadow-lg py-2 z-50">
                        <div class="px-4 py-2 border-b border-sand text-caption text-stone">
                            Masuk sebagai <span class="font-medium text-charcoal block truncate">{{ Auth::user()->name }}</span>
                        </div>
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-body-sm text-charcoal hover:bg-sand/30">
                                Panel Admin
                            </a>
                        @else
                            <a href="{{ route('account.dashboard') }}" class="block px-4 py-2 text-body-sm text-charcoal hover:bg-sand/30">
                                Akun Saya
                            </a>
                            <a href="{{ route('account.wishlist') }}" class="block px-4 py-2 text-body-sm text-charcoal hover:bg-sand/30">
                                Wishlist Saya
                            </a>
                            <a href="{{ route('account.orders') }}" class="block px-4 py-2 text-body-sm text-charcoal hover:bg-sand/30">
                                Pesanan Saya
                            </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-body-sm text-charcoal hover:bg-sand/30">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" 
                   class="p-2 text-charcoal hover:text-black min-w-[40px] min-h-[40px] flex items-center justify-center transition"
                   aria-label="Masuk ke Akun">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </a>
            @endauth

            <!-- Wishlist Button -->
            <a href="{{ route('account.wishlist') }}" 
               class="p-2 text-charcoal hover:text-black min-w-[40px] min-h-[40px] flex items-center justify-center transition relative"
               aria-label="Wishlist Produk">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <template x-if="$store.cart?.wishlistCount > 0">
                    <span class="absolute top-1 right-0.5 bg-terracotta text-white text-[10px] font-bold rounded-full min-w-[16px] h-4 px-1 flex items-center justify-center" 
                          x-text="$store.cart.wishlistCount"></span>
                </template>
            </a>

            <!-- Cart Button (Opens slide-in drawer) -->
            <button type="button" 
                    @click.prevent="$store.cart.openDrawer()" 
                    class="p-2 text-charcoal hover:text-black min-w-[40px] min-h-[40px] flex items-center justify-center relative transition cursor-pointer"
                    aria-label="Keranjang Belanja">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span x-show="$store.cart?.count > 0"
                      x-text="$store.cart?.count" 
                      class="absolute top-1 right-0.5 bg-charcoal text-canvas text-[10px] font-bold min-w-[16px] h-4 px-1 rounded-full flex items-center justify-center"
                      style="display: none;">
                </span>
            </button>
        </div>

    </div>

    <!-- Desktop Floating Mega Menus -->
    <div class="max-w-[1400px] mx-auto relative">
        <!-- Desktop Mega Menu: MEN -->
        <div x-show="activeMenu === 'men'" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @mouseenter="activeMenu = 'men'"
             @mouseleave="activeMenu = null"
             class="hidden lg:block absolute left-0 right-0 top-2 bg-white rounded-2xl border border-sand shadow-2xl z-50 p-8">
            <div class="grid grid-cols-4 gap-8">
                <div>
                    <h3 class="nav-label font-bold text-charcoal border-b border-sand pb-2 mb-4">Jaket & Outerwear</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('collections.show', 'men-workwear-jackets') }}" class="text-body-sm text-iron hover:text-charcoal transition">Jaket Workwear & Canvas</a></li>
                        <li><a href="{{ route('collections.show', 'men-varsity-bomber') }}" class="text-body-sm text-iron hover:text-charcoal transition">Varsity & Bomber 90s</a></li>
                        <li><a href="{{ route('collections.show', 'men-sweats-hoodies') }}" class="text-body-sm text-iron hover:text-charcoal transition">Hoodie & Crewneck Vintage</a></li>
                        <li><a href="{{ route('collections.show', 'men-tracktop-windbreaker') }}" class="text-body-sm text-iron hover:text-charcoal transition">Tracktop & Windbreaker</a></li>
                        <li><a href="{{ route('collections.show', 'men-leather-jackets') }}" class="text-body-sm text-iron hover:text-charcoal transition">Jaket Kulit & Moto Vintage</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="nav-label font-bold text-charcoal border-b border-sand pb-2 mb-4">Baju, Celana & Topi</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('collections.show', 'men-vintage-band-tees') }}" class="text-body-sm text-iron hover:text-charcoal transition">Vintage Band Tees Single Stitch</a></li>
                        <li><a href="{{ route('collections.show', 'men-graphic-tees') }}" class="text-body-sm text-iron hover:text-charcoal transition">Graphic Tees 90s & Y2K</a></li>
                        <li><a href="{{ route('collections.show', 'men-flannel-shirts') }}" class="text-body-sm text-iron hover:text-charcoal transition">Kemeja Flannel & Plaid</a></li>
                        <li><a href="{{ route('collections.show', 'men-vintage-denim') }}" class="text-body-sm text-iron hover:text-charcoal transition">Denim Levi's 501 USA</a></li>
                        <li><a href="{{ route('collections.show', 'men-cargo-pants') }}" class="text-body-sm text-iron hover:text-charcoal transition">Cargo Pants & Baggy</a></li>
                        <li><a href="{{ route('collections.show', 'men-vintage-snapback') }}" class="text-body-sm text-iron hover:text-charcoal transition">Snapback & Headwear 90s</a></li>
                    </ul>
                </div>

                <!-- Mega Menu Card 1: Carhartt Detroit -->
                <a href="{{ route('products.show', 'vintage-carhartt-detroit-j97-tan') }}" 
                   class="group relative rounded-[20px] bg-[#8b9aa4]/15 hover:bg-[#8b9aa4]/25 p-5 flex flex-col justify-between overflow-hidden min-h-[220px] border border-[#8b9aa4]/30 transition duration-300">
                    <div class="z-10">
                        <span class="inline-block bg-white text-charcoal rounded-full px-3 py-0.5 text-[10px] font-bold uppercase tracking-wider shadow-2xs">
                            Grail Item 1 of 1
                        </span>
                        <p class="text-[12px] text-charcoal/80 font-medium mt-1.5 leading-snug">Carhartt Detroit J97 Faded Tan (Made in USA).</p>
                    </div>
                    
                    <!-- Product Image -->
                    <div class="my-auto py-1 flex items-center justify-center">
                        <img src="{{ asset('images/products/vintage-carhartt-tan.png') }}" 
                             alt="Carhartt Detroit" 
                             class="h-24 w-auto object-contain drop-shadow-md group-hover:scale-110 transition-transform duration-300">
                    </div>

                    <span class="text-[11px] uppercase tracking-wider font-bold text-charcoal underline underline-offset-4 group-hover:opacity-80 transition inline-block">
                        Lihat Detail Jaket →
                    </span>
                </a>

                <!-- Mega Menu Card 2: Nirvana Band Tee -->
                <a href="{{ route('products.show', 'vintage-nirvana-in-utero-tee-1993') }}" 
                   class="group relative rounded-[20px] bg-[#8a7466]/15 hover:bg-[#8a7466]/25 p-5 flex flex-col justify-between overflow-hidden min-h-[220px] border border-[#8a7466]/30 transition duration-300">
                    <div class="z-10">
                        <span class="inline-block bg-white text-charcoal rounded-full px-3 py-0.5 text-[10px] font-bold uppercase tracking-wider shadow-2xs">
                            Single Stitch 1993
                        </span>
                        <p class="text-[12px] text-charcoal/80 font-medium mt-1.5 leading-snug">Nirvana In Utero Giant Tag USA.</p>
                    </div>

                    <!-- Product Image -->
                    <div class="my-auto py-1 flex items-center justify-center">
                        <img src="{{ asset('images/products/vintage-band-tee-nirvana.png') }}" 
                             alt="Nirvana Tee" 
                             class="h-24 w-auto object-contain drop-shadow-md group-hover:scale-110 transition-transform duration-300">
                    </div>

                    <span class="text-[11px] uppercase tracking-wider font-bold text-charcoal underline underline-offset-4 group-hover:opacity-80 transition inline-block">
                        Lihat Detail Kaos →
                    </span>
                </a>
            </div>
        </div>

        <!-- Desktop Mega Menu: WOMEN -->
        <div x-show="activeMenu === 'women'" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @mouseenter="activeMenu = 'women'"
             @mouseleave="activeMenu = null"
             class="hidden lg:block absolute left-0 right-0 top-2 bg-white rounded-2xl border border-sand shadow-2xl z-50 p-8">
            <div class="grid grid-cols-4 gap-8">
                <div>
                    <h3 class="nav-label font-bold text-charcoal border-b border-sand pb-2 mb-4">Jaket & Atasan</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('collections.show', 'women-oversized-bomber') }}" class="text-body-sm text-iron hover:text-charcoal transition">Oversized Bomber & Varsity</a></li>
                        <li><a href="{{ route('collections.show', 'women-denim-jackets') }}" class="text-body-sm text-iron hover:text-charcoal transition">Jaket Denim Vintage</a></li>
                        <li><a href="{{ route('collections.show', 'women-baby-tees') }}" class="text-body-sm text-iron hover:text-charcoal transition">Graphic Baby Tees Y2K</a></li>
                        <li><a href="{{ route('collections.show', 'women-oversized-tees') }}" class="text-body-sm text-iron hover:text-charcoal transition">Oversized Vintage Tees</a></li>
                        <li><a href="{{ route('collections.show', 'women-knit-sweaters') }}" class="text-body-sm text-iron hover:text-charcoal transition">Knit Sweater & Cardigan</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="nav-label font-bold text-charcoal border-b border-sand pb-2 mb-4">Celana, Topi & Aksesoris</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('collections.show', 'women-high-waist-denim') }}" class="text-body-sm text-iron hover:text-charcoal transition">High-Waist Mom Jeans</a></li>
                        <li><a href="{{ route('collections.show', 'women-cargo-skirts') }}" class="text-body-sm text-iron hover:text-charcoal transition">Cargo Skirt & Baggy Pants</a></li>
                        <li><a href="{{ route('collections.show', 'women-corduroy-pants') }}" class="text-body-sm text-iron hover:text-charcoal transition">Corduroy Trousers</a></li>
                        <li><a href="{{ route('collections.show', 'women-vintage-caps') }}" class="text-body-sm text-iron hover:text-charcoal transition">Vintage Caps & Headwear</a></li>
                        <li><a href="{{ route('collections.show', 'women-vintage-totes') }}" class="text-body-sm text-iron hover:text-charcoal transition">Vintage Tote & Tas Kulit</a></li>
                    </ul>
                </div>

                <!-- Mega Menu Card 1: Nike Center Swoosh -->
                <a href="{{ route('products.show', 'vintage-nike-center-swoosh-hoodie') }}" 
                   class="group relative rounded-[20px] bg-[#c4a4a4]/20 hover:bg-[#c4a4a4]/30 p-5 flex flex-col justify-between overflow-hidden min-h-[220px] border border-[#c4a4a4]/35 transition duration-300">
                    <div class="z-10">
                        <span class="inline-block bg-white text-charcoal rounded-full px-3 py-0.5 text-[10px] font-bold uppercase tracking-wider shadow-2xs">
                            Boxy Fit 90s
                        </span>
                        <p class="text-[12px] text-charcoal/80 font-medium mt-1.5 leading-snug">Nike Center Mini Swoosh Silver Tag.</p>
                    </div>

                    <!-- Product Image -->
                    <div class="my-auto py-1 flex items-center justify-center">
                        <img src="{{ asset('images/products/vintage-nike-hoodie-grey.png') }}" 
                             alt="Nike Center Swoosh" 
                             class="h-24 w-auto object-contain drop-shadow-md group-hover:scale-110 transition-transform duration-300">
                    </div>

                    <span class="text-[11px] uppercase tracking-wider font-bold text-charcoal underline underline-offset-4 group-hover:opacity-80 transition inline-block">
                        Lihat Detail Hoodie →
                    </span>
                </a>

                <!-- Mega Menu Card 2: Levi's 501 Stonewash -->
                <a href="{{ route('products.show', 'vintage-levis-501-usa-light-wash') }}" 
                   class="group relative rounded-[20px] bg-[#8a9a8c]/20 hover:bg-[#8a9a8c]/30 p-5 flex flex-col justify-between overflow-hidden min-h-[220px] border border-[#8a9a8c]/35 transition duration-300">
                    <div class="z-10">
                        <span class="inline-block bg-white text-charcoal rounded-full px-3 py-0.5 text-[10px] font-bold uppercase tracking-wider shadow-2xs">
                            Denim Ikonik
                        </span>
                        <p class="text-[12px] text-charcoal/80 font-medium mt-1.5 leading-snug">Levi's 501 Made in USA W32.</p>
                    </div>

                    <!-- Product Image -->
                    <div class="my-auto py-1 flex items-center justify-center">
                        <img src="{{ asset('images/products/vintage-levis-501-stonewash.png') }}" 
                             alt="Levi's 501" 
                             class="h-24 w-auto object-contain drop-shadow-md group-hover:scale-110 transition-transform duration-300">
                    </div>

                    <span class="text-[11px] uppercase tracking-wider font-bold text-charcoal underline underline-offset-4 group-hover:opacity-80 transition inline-block">
                        Lihat Detail Jeans →
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer (Full Height Slide-in) -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition-opacity ease-linear duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-50 lg:hidden"
         @click="mobileMenuOpen = false"
         style="display: none;">
    </div>

    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 max-w-[340px] w-full bg-canvas shadow-2xl z-50 flex flex-col justify-between overflow-y-auto lg:hidden"
         style="display: none;">
        
        <div>
            <!-- Mobile Header Top with Close Button -->
            <div class="flex items-center justify-between p-4 border-b border-sand">
                <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="flex items-center text-charcoal hover:opacity-85 transition">
                    <img src="{{ asset('images/fifa-logo.svg') }}" alt="fifa" class="h-6 w-auto object-contain">
                </a>
                <button type="button" 
                        @click="mobileMenuOpen = false" 
                        class="p-2 text-charcoal hover:text-black min-w-[44px] min-h-[44px] flex items-center justify-center cursor-pointer"
                        aria-label="Tutup Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Search Trigger in Drawer -->
            <div class="p-3 border-b border-sand">
                <button type="button" 
                        @click="mobileMenuOpen = false; searchOpen = true; $nextTick(() => { $refs.searchInput?.focus(); })"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2.5 bg-sand/30 hover:bg-sand/50 rounded-xl text-body-sm text-stone border border-sand/60 transition text-left cursor-pointer">
                    <svg class="w-4 h-4 text-stone flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="text-charcoal/70">Cari item vintage (misal: Carhartt, Band Tee, 501)...</span>
                </button>
            </div>

            <!-- Mobile Gender Segment Switcher -->
            <div class="grid grid-cols-2 border-b border-sand bg-oatMilk/30">
                <button type="button" 
                        @click="mobileCategoryTab = 'men'"
                        :class="mobileCategoryTab === 'men' ? 'border-b-2 border-charcoal font-bold text-charcoal bg-canvas' : 'text-iron'"
                        class="py-3 text-caption font-medium uppercase tracking-wide10 min-h-[44px] transition">
                    Pria
                </button>
                <button type="button" 
                        @click="mobileCategoryTab = 'women'"
                        :class="mobileCategoryTab === 'women' ? 'border-b-2 border-charcoal font-bold text-charcoal bg-canvas' : 'text-iron'"
                        class="py-3 text-caption font-medium uppercase tracking-wide10 min-h-[44px] transition">
                    Wanita
                </button>
            </div>

            <!-- Mobile Tab Content: MEN -->
            <div x-show="mobileCategoryTab === 'men'" class="p-4 space-y-4">
                <div class="border-b border-sand pb-3">
                    <a href="{{ route('collections.show', 'new-arrivals') }}" @click="mobileMenuOpen = false" class="block py-2 text-body-sm font-bold text-charcoal">
                        DROP TERBARU MINGGU INI →
                    </a>
                </div>

                <!-- Jackets Accordion -->
                <div class="border-b border-sand pb-3">
                    <button type="button" 
                            @click="mobileAccordion.menJackets = !mobileAccordion.menJackets" 
                            class="w-full flex items-center justify-between py-2 text-body-sm font-semibold text-charcoal min-h-[44px]">
                        <span>Jaket & Outerwear</span>
                        <svg class="w-4 h-4 transform transition-transform" :class="mobileAccordion.menJackets ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="mobileAccordion.menJackets" x-collapse class="pl-4 space-y-2.5 pt-2">
                        <a href="{{ route('collections.show', 'men-workwear-jackets') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Workwear & Canvas</a>
                        <a href="{{ route('collections.show', 'men-varsity-bomber') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Varsity & Bomber 90s</a>
                        <a href="{{ route('collections.show', 'men-sweats-hoodies') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Hoodie & Crewneck</a>
                        <a href="{{ route('collections.show', 'men-tracktop-windbreaker') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Tracktop & Windbreaker</a>
                        <a href="{{ route('collections.show', 'men-leather-jackets') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Jaket Kulit Motor</a>
                    </div>
                </div>

                <!-- Tops & Bottoms Accordion -->
                <div class="border-b border-sand pb-3">
                    <button type="button" 
                            @click="mobileAccordion.menTops = !mobileAccordion.menTops" 
                            class="w-full flex items-center justify-between py-2 text-body-sm font-semibold text-charcoal min-h-[44px]">
                        <span>Baju, Celana & Topi</span>
                        <svg class="w-4 h-4 transform transition-transform" :class="mobileAccordion.menTops ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="mobileAccordion.menTops" x-collapse class="pl-4 space-y-2.5 pt-2">
                        <a href="{{ route('collections.show', 'men-vintage-band-tees') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Vintage Band Tees</a>
                        <a href="{{ route('collections.show', 'men-graphic-tees') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Graphic Tees 90s</a>
                        <a href="{{ route('collections.show', 'men-vintage-denim') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Denim Levi's 501</a>
                        <a href="{{ route('collections.show', 'men-cargo-pants') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Cargo & Corduroy</a>
                        <a href="{{ route('collections.show', 'men-vintage-snapback') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Topi Snapback & Beanie</a>
                    </div>
                </div>

                <a href="{{ route('categories.men') }}" @click="mobileMenuOpen = false" class="block py-2 text-body-sm font-bold text-charcoal underline underline-offset-4">
                    Lihat Semua Koleksi Pria →
                </a>
            </div>

            <!-- Mobile Tab Content: WOMEN -->
            <div x-show="mobileCategoryTab === 'women'" class="p-4 space-y-4">
                <div class="border-b border-sand pb-3">
                    <a href="{{ route('collections.show', 'new-arrivals') }}" @click="mobileMenuOpen = false" class="block py-2 text-body-sm font-bold text-charcoal">
                        DROP TERBARU MINGGU INI →
                    </a>
                </div>

                <div class="border-b border-sand pb-3">
                    <button type="button" 
                            @click="mobileAccordion.womenJackets = !mobileAccordion.womenJackets" 
                            class="w-full flex items-center justify-between py-2 text-body-sm font-semibold text-charcoal min-h-[44px]">
                        <span>Jaket & Outerwear</span>
                        <svg class="w-4 h-4 transform transition-transform" :class="mobileAccordion.womenJackets ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="mobileAccordion.womenJackets" x-collapse class="pl-4 space-y-2.5 pt-2">
                        <a href="{{ route('collections.show', 'women-oversized-bomber') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Oversized Bomber</a>
                        <a href="{{ route('collections.show', 'women-denim-jackets') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Jaket Denim Vintage</a>
                        <a href="{{ route('collections.show', 'women-knit-sweaters') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Knit Sweater</a>
                    </div>
                </div>

                <div class="border-b border-sand pb-3">
                    <button type="button" 
                            @click="mobileAccordion.womenTops = !mobileAccordion.womenTops" 
                            class="w-full flex items-center justify-between py-2 text-body-sm font-semibold text-charcoal min-h-[44px]">
                        <span>Atasan & Celana</span>
                        <svg class="w-4 h-4 transform transition-transform" :class="mobileAccordion.womenTops ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="mobileAccordion.womenTops" x-collapse class="pl-4 space-y-2.5 pt-2">
                        <a href="{{ route('collections.show', 'women-baby-tees') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Baby Tees Y2K</a>
                        <a href="{{ route('collections.show', 'women-oversized-tees') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Oversized Graphic Tees</a>
                        <a href="{{ route('collections.show', 'women-high-waist-denim') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">High-Waist Mom Jeans</a>
                        <a href="{{ route('collections.show', 'women-cargo-skirts') }}" @click="mobileMenuOpen = false" class="block text-body-sm text-iron hover:text-charcoal py-1">Cargo Skirt</a>
                    </div>
                </div>

                <a href="{{ route('categories.women') }}" @click="mobileMenuOpen = false" class="block py-2 text-body-sm font-bold text-charcoal underline underline-offset-4">
                    Lihat Semua Koleksi Wanita →
                </a>
            </div>
        </div>

        <!-- Mobile Drawer Bottom Actions -->
        <div class="p-4 border-t border-sand bg-oatMilk/30 space-y-3">
            @auth
                <div class="flex items-center justify-between">
                    <span class="text-body-sm font-medium text-charcoal truncate">{{ Auth::user()->name }}</span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('account.wishlist') }}" @click="mobileMenuOpen = false" class="btn-pill-light text-caption px-3 py-1.5 flex items-center gap-1">
                            <span>♥</span> Wishlist
                        </a>
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn-pill-dark text-caption px-3 py-1.5">Admin</a>
                        @else
                            <a href="{{ route('account.dashboard') }}" class="btn-pill-light text-caption px-3 py-1.5">Akun</a>
                        @endif
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-center text-caption uppercase tracking-wide10 text-iron py-2">
                        Keluar
                    </button>
                </form>
            @else
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="btn-pill-dark text-center w-full">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="btn-pill-light text-center w-full">
                        Daftar
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Live Search Overlay / Modal Dialog -->
    <div x-show="searchOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-charcoal/50 backdrop-blur-xs p-3 sm:p-6 md:p-10 flex items-start justify-center"
         style="display: none;">
        
        <!-- Search Dialog Card -->
        <div @click.away="searchOpen = false" 
             x-show="searchOpen"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-98"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-4 scale-98"
             class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl border border-sand overflow-hidden relative mt-2 sm:mt-6">
            
            <!-- Search Form Header -->
            <form action="{{ route('search.index') }}" method="GET" class="relative border-b border-sand">
                <div class="flex items-center px-4 sm:px-6 py-4">
                    <svg class="w-6 h-6 text-stone flex-shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    
                    <input type="text" 
                           name="q" 
                           x-ref="searchInput"
                           x-model="searchQuery" 
                           @input.debounce.250ms="doLiveSearch()"
                           placeholder="Cari item vintage (contoh: Carhartt, Band Tee, 501, Varsity)..." 
                           class="w-full text-base sm:text-lg bg-transparent text-charcoal placeholder:text-stone/70 border-none outline-none focus:ring-0">
                    
                    <!-- Clear query button -->
                    <button type="button" 
                            x-show="searchQuery.length > 0" 
                            @click="searchQuery = ''; searchResults = []; $nextTick(() => { $refs.searchInput?.focus(); })"
                            class="p-1.5 text-stone hover:text-charcoal rounded-full hover:bg-sand/30 transition mr-2 cursor-pointer"
                            title="Hapus kata kunci">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    
                    <!-- Close modal button -->
                    <button type="button" 
                            @click="searchOpen = false" 
                            class="p-2 text-stone hover:text-charcoal rounded-full hover:bg-sand/30 transition text-caption font-bold cursor-pointer"
                            aria-label="Tutup Pencarian">
                        <span class="hidden sm:inline-block mr-1 text-[11px] uppercase tracking-wider text-stone font-semibold">ESC</span>
                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Search Modal Body -->
            <div class="p-4 sm:p-6 max-h-[65vh] overflow-y-auto space-y-6">
                
                <!-- Quick Search / Trending Tags -->
                <div x-show="searchQuery.length < 2">
                    <div class="text-[11px] font-bold uppercase tracking-widest text-stone mb-3">
                        Pencarian Populer
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Carhartt Detroit', 'Nirvana Band Tee', 'Nike Swoosh', "Levi's 501", 'Varsity 90s', 'Pria', 'Wanita', 'Vintage Beanie', 'Crossbody Bag'] as $tag)
                            <button type="button" 
                                    @click="selectTag('{{ $tag }}')"
                                    class="px-3.5 py-1.5 rounded-full bg-sand/30 hover:bg-sand text-charcoal text-body-sm font-medium transition cursor-pointer border border-sand/70">
                                {{ $tag }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Loading State -->
                <div x-show="searchLoading" class="py-8 text-center text-stone flex flex-col items-center justify-center gap-2">
                    <svg class="animate-spin h-6 w-6 text-charcoal" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span class="text-caption">Mencari produk vintage...</span>
                </div>

                <!-- Live Results -->
                <div x-show="!searchLoading && searchQuery.length >= 2 && searchResults.length > 0">
                    <div class="flex items-center justify-between mb-3 border-b border-sand pb-2">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-stone">
                            Hasil Produk (<span x-text="searchResults.length"></span>)
                        </span>
                        <a :href="'{{ route('search.index') }}?q=' + encodeURIComponent(searchQuery)" 
                           class="text-[12px] font-bold text-charcoal hover:underline">
                            Lihat Semua Hasil →
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <template x-for="item in searchResults" :key="item.id">
                            <a :href="item.url" 
                               @click="searchOpen = false"
                               class="group flex items-center gap-3.5 p-3 rounded-xl hover:bg-sand/25 border border-sand/50 transition">
                                <div class="w-16 h-16 bg-[#f5f4f0] rounded-lg flex items-center justify-center flex-shrink-0 p-1 overflow-hidden">
                                    <img :src="item.image" :alt="item.name" class="w-full h-full object-contain group-hover:scale-110 transition duration-300">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="text-[10px] uppercase tracking-wider text-stone block truncate" x-text="item.category"></span>
                                    <h4 class="text-body-sm font-semibold text-charcoal group-hover:text-black truncate" x-text="item.name"></h4>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-caption font-bold text-charcoal" x-text="item.price_formatted"></span>
                                        <template x-if="item.compare_at_price_formatted">
                                            <span class="text-[10px] text-stone line-through" x-text="item.compare_at_price_formatted"></span>
                                        </template>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>

                    <div class="mt-4 pt-4 border-t border-sand text-center">
                        <a :href="'{{ route('search.index') }}?q=' + encodeURIComponent(searchQuery)" 
                           class="btn-pill-dark inline-block px-6 py-2.5 text-center text-body-sm font-semibold">
                            Buka Semua Hasil di Halaman Katalog
                        </a>
                    </div>
                </div>

                <!-- Empty State -->
                <div x-show="!searchLoading && searchQuery.length >= 2 && searchResults.length === 0" 
                     class="py-8 text-center text-stone">
                    <p class="text-body-sm text-charcoal font-medium">Tidak ada produk yang cocok dengan "<span x-text="searchQuery"></span>".</p>
                    <p class="text-caption text-iron mt-1">Coba gunakan kata kunci lain seperti <em>Carhartt</em>, <em>Nirvana</em>, atau <em>501</em>.</p>
                </div>

            </div>

        </div>

    </div>

</header>

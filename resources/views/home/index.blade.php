@extends('layouts.app')

@section('title', 'fifa — Kurasi Vintage & Thrifting Otentik 1-of-1')

@section('content')
    <!-- 1. Hero Editorial Lifestyle Banner (Inset Card matching fifa design) -->
    <section class="max-w-[1400px] mx-auto px-3 sm:px-6 pt-1 pb-4">
        <div class="relative w-full h-[700px] min-h-[650px] sm:h-[640px] lg:h-[680px] rounded-[24px] sm:rounded-[32px] overflow-hidden bg-[#2d2926] shadow-sm select-none">
            <!-- Hero Photography: Responsive Picture for Perfect Mobile & Desktop Rendering -->
            <picture class="w-full h-full block">
                <source media="(max-width: 767px)" srcset="{{ asset('images/home/hero-dasher-mobile.jpg') }}">
                <img src="{{ asset('images/home/hero-dasher.jpg') }}" 
                     alt="Koleksi Vintage Thrifting fifa" 
                     class="w-full h-full object-cover object-center">
            </picture>

            <!-- Subtle Shadow Overlay for Text Readability -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 via-45% to-transparent pointer-events-none"></div>

            <!-- Vertical Ribbon Divider (FIFA THRIFT VINTAGE repeating) - Desktop Only -->
            <div class="absolute top-0 bottom-0 left-[35%] lg:left-[36%] hidden md:flex flex-col justify-between items-center bg-[#252220]/95 text-white/95 text-[11px] lg:text-[12px] font-bold tracking-[0.25em] z-10 w-9 sm:w-11 border-x border-white/10 select-none py-6">
                <span class="[writing-mode:vertical-rl] rotate-180 uppercase">
                    FIFA THRIFT &nbsp;&bull;&nbsp; 1 OF 1 VINTAGE &nbsp;&bull;&nbsp; AUTHENTIC DROPS &nbsp;&bull;&nbsp; SLOW FASHION &nbsp;&bull;&nbsp; HIGIENIS SIAP PAKAI
                </span>
            </div>

            <!-- Radiating Sketch Lines Doodle around Outfit -->
            <div class="absolute right-[4%] sm:right-[15%] md:right-[20%] top-[30%] sm:top-[20%] md:top-[22%] z-20 pointer-events-none select-none text-white/90 drop-shadow-md">
                <svg class="w-28 h-28 sm:w-36 sm:h-36 md:w-44 md:h-44 transform -rotate-12" viewBox="0 0 160 160" fill="none" stroke="currentColor">
                    <path d="M70 12 L85 32" stroke-width="3" stroke-linecap="round"/>
                    <path d="M100 22 L112 48" stroke-width="3.5" stroke-linecap="round"/>
                    <path d="M125 45 L132 75" stroke-width="3" stroke-linecap="round"/>
                    <path d="M135 82 L146 112" stroke-width="3.5" stroke-linecap="round"/>
                    <path d="M124 118 L138 138" stroke-width="3" stroke-linecap="round"/>
                    <path d="M94 136 L104 156" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>

            <!-- Doodle Annotation Scribble ("100% SUDAH DICUCI & SIAP PAKAI") - Desktop Only -->
            <div class="absolute left-[38%] md:left-[43%] lg:left-[45%] top-[40%] md:top-[38%] z-20 text-white select-none pointer-events-none hidden md:block">
                <p class="font-sans font-extrabold uppercase text-[12px] sm:text-[14px] lg:text-[16px] tracking-wider drop-shadow-md text-white max-w-[190px] sm:max-w-[220px] leading-tight">
                    100% SUDAH DICUCI & SIAP PAKAI
                </p>
                <!-- Hand-drawn curved arrow doodle SVG -->
                <svg class="w-12 h-12 sm:w-16 sm:h-16 text-white drop-shadow-lg mt-1 transform -rotate-12" viewBox="0 0 80 80" fill="none" stroke="currentColor">
                    <path d="M12 18 C 30 45, 45 55, 66 65 M50 67 L68 65 L64 48" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <!-- Hero Editorial Headline & CTAs (Bottom Left on Mobile, Bottom Right on Desktop) -->
            <div class="absolute bottom-8 sm:bottom-12 left-4 right-4 sm:left-auto sm:right-8 lg:right-16 z-20 text-left sm:text-right max-w-xl">
                <span class="block text-[11px] sm:text-[12px] font-bold uppercase tracking-[0.2em] text-white/90 mb-2 drop-shadow">
                    FRESH DROPS MINGGU INI
                </span>
                <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-normal text-white drop-shadow-md mb-5 sm:mb-6 leading-tight">
                    Kurasi Otentik 1 of 1.<br class="hidden sm:inline"> Gaya Vintage Pilihan.
                </h1>
                <div class="flex items-center justify-start sm:justify-end gap-3 sm:gap-4">
                    <a href="{{ route('categories.men') }}" 
                       class="flex-1 sm:flex-none text-center bg-white text-charcoal hover:bg-neutral-100 font-sans font-bold text-[11px] sm:text-[13px] uppercase tracking-wider sm:tracking-widest px-5 sm:px-9 py-3 sm:py-3.5 rounded-full shadow-lg transition">
                        KOLEKSI PRIA
                    </a>
                    <a href="{{ route('categories.women') }}" 
                       class="flex-1 sm:flex-none text-center bg-white text-charcoal hover:bg-neutral-100 font-sans font-bold text-[11px] sm:text-[13px] uppercase tracking-wider sm:tracking-widest px-5 sm:px-9 py-3 sm:py-3.5 rounded-full shadow-lg transition">
                        KOLEKSI WANITA
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. 4-Category Color Block Cards with Swipeable Carousel & Dynamic Center-Snap Morphing Oval Effect -->
    <section class="max-w-[1400px] mx-auto px-3 sm:px-6 py-4 sm:py-8 lg:py-10">
        <div class="bg-[#ece7e1] rounded-[24px] sm:rounded-[32px] p-3 sm:p-5 lg:p-8" 
             x-data="{
                 activeCat: 1,
                 checkActive() {
                     if (window.innerWidth >= 1024) {
                         this.activeCat = null;
                         return;
                     }
                     const el = this.$refs.slider;
                     if (!el) return;
                     const center = el.scrollLeft + (el.clientWidth / 2);
                     let closest = 1;
                     let minDiff = Infinity;
                     const children = el.querySelectorAll('[data-cat-card]');
                     children.forEach((card) => {
                         const cardCenter = card.offsetLeft + (card.offsetWidth / 2);
                         const diff = Math.abs(center - cardCenter);
                         if (diff < minDiff) {
                             minDiff = diff;
                             closest = parseInt(card.getAttribute('data-cat-card'));
                         }
                     });
                     this.activeCat = closest;
                 }
             }"
             x-init="$nextTick(() => { checkActive(); window.addEventListener('resize', () => checkActive()); })">
            
            <div x-ref="slider"
                 @scroll.debounce.30ms="checkActive()"
                 class="flex lg:grid lg:grid-cols-4 overflow-x-auto lg:overflow-visible gap-3 sm:gap-4 lg:gap-6 no-scrollbar snap-x snap-mandatory scroll-smooth py-1">
                
                <!-- Card 1: Slate Blue (#5c778a) -> DROP TERBARU -->
                <div data-cat-card="1"
                     @click="if (window.innerWidth < 1024) activeCat = 1"
                     :class="activeCat === 1 ? 'rounded-full ring-2 ring-white/60 shadow-xl' : 'rounded-[24px] sm:rounded-[28px] lg:hover:rounded-full'"
                     class="group relative bg-[#5c778a] flex-none w-[76vw] max-w-[320px] sm:w-[280px] lg:w-auto h-[420px] sm:h-[450px] lg:h-[490px] overflow-hidden flex flex-col items-center justify-center p-4 sm:p-5 lg:p-6 transition-all duration-500 ease-in-out cursor-pointer shadow-xs hover:shadow-xl select-none snap-center">
                    
                    <!-- Large Centered Transparent Product Cutout -->
                    <div class="w-full flex-1 flex items-center justify-center relative px-2 my-auto overflow-hidden">
                        <img src="{{ asset('images/home/cat-blue-runner.png') }}" 
                             alt="Kaos Vintage Band Tee Produk Terbaru fifa" 
                             class="w-auto h-auto max-w-[95%] max-h-[175px] sm:max-h-[200px] lg:max-h-[230px] object-contain drop-shadow-2xl scale-110 sm:scale-115 group-hover:scale-120 transition-transform duration-500">
                    </div>

                    <!-- Normal State Center Pill -->
                    <div :class="activeCat === 1 ? 'opacity-0 scale-95' : 'opacity-100 scale-100 lg:group-hover:opacity-0 lg:group-hover:scale-95'"
                         class="absolute inset-0 flex items-center justify-center pointer-events-none transition-all duration-300 px-4">
                        <span class="border border-white text-white font-semibold text-[11px] sm:text-[12px] uppercase tracking-widest px-6 py-2.5 rounded-full text-center leading-none shadow-xs backdrop-blur-2xs bg-black/10">
                            DROP TERBARU
                        </span>
                    </div>

                    <!-- Hover / Active State Overlay -->
                    <div :class="activeCat === 1 ? 'opacity-100 scale-100 pointer-events-auto' : 'opacity-0 scale-95 lg:group-hover:opacity-100 lg:group-hover:scale-100 pointer-events-none lg:group-hover:pointer-events-auto'"
                         class="absolute inset-0 flex flex-col items-center justify-center gap-2 sm:gap-2.5 transition-all duration-300 z-20 p-3 bg-black/20">
                        <span class="text-white font-extrabold text-[11px] sm:text-[12px] uppercase tracking-widest text-center drop-shadow mb-0.5">
                            DROP TERBARU
                        </span>
                        <a href="{{ route('collections.show', 'new-arrivals') }}?gender=men" 
                           class="border border-white hover:bg-white text-white hover:text-charcoal font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-4 sm:px-5 py-2 rounded-full transition duration-150 min-w-[125px] sm:min-w-[135px] text-center leading-tight backdrop-blur-2xs bg-black/20 hover:bg-white shadow-md">
                            KOLEKSI PRIA
                        </a>
                        <a href="{{ route('collections.show', 'new-arrivals') }}?gender=women" 
                           class="border border-white hover:bg-white text-white hover:text-charcoal font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-4 sm:px-5 py-2 rounded-full transition duration-150 min-w-[125px] sm:min-w-[135px] text-center leading-tight backdrop-blur-2xs bg-black/20 hover:bg-white shadow-md">
                            KOLEKSI WANITA
                        </a>
                    </div>
                </div>

                <!-- Card 2: Mocha / Warm Espresso (#4d4341) -> JAKET & WORKWEAR -->
                <div data-cat-card="2"
                     @click="if (window.innerWidth < 1024) activeCat = 2"
                     :class="activeCat === 2 ? 'rounded-full ring-2 ring-white/60 shadow-xl' : 'rounded-[24px] sm:rounded-[28px] lg:hover:rounded-full'"
                     class="group relative bg-[#4d4341] flex-none w-[76vw] max-w-[320px] sm:w-[280px] lg:w-auto h-[420px] sm:h-[450px] lg:h-[490px] overflow-hidden flex flex-col items-center justify-center p-4 sm:p-5 lg:p-6 transition-all duration-500 ease-in-out cursor-pointer shadow-xs hover:shadow-xl select-none snap-center">
                    
                    <!-- Large Centered Transparent Product Cutout -->
                    <div class="w-full flex-1 flex items-center justify-center relative px-2 my-auto overflow-hidden">
                        <img src="{{ asset('images/home/cat-grey-sneaker.png') }}" 
                             alt="Jaket Workwear Carhartt Vintage fifa" 
                             class="w-auto h-auto max-w-[95%] max-h-[175px] sm:max-h-[200px] lg:max-h-[230px] object-contain drop-shadow-2xl scale-110 sm:scale-115 group-hover:scale-120 transition-transform duration-500">
                    </div>

                    <!-- Normal State Center Pill -->
                    <div :class="activeCat === 2 ? 'opacity-0 scale-95' : 'opacity-100 scale-100 lg:group-hover:opacity-0 lg:group-hover:scale-95'"
                         class="absolute inset-0 flex items-center justify-center pointer-events-none transition-all duration-300 px-4">
                        <span class="border border-white text-white font-semibold text-[11px] sm:text-[12px] uppercase tracking-widest px-6 py-2.5 rounded-full text-center leading-none shadow-xs backdrop-blur-2xs bg-black/10">
                            JAKET & WORKWEAR
                        </span>
                    </div>

                    <!-- Hover / Active State Overlay -->
                    <div :class="activeCat === 2 ? 'opacity-100 scale-100 pointer-events-auto' : 'opacity-0 scale-95 lg:group-hover:opacity-100 lg:group-hover:scale-100 pointer-events-none lg:group-hover:pointer-events-auto'"
                         class="absolute inset-0 flex flex-col items-center justify-center gap-2 sm:gap-2.5 transition-all duration-300 z-20 p-3 bg-black/20">
                        <span class="text-white font-extrabold text-[11px] sm:text-[12px] uppercase tracking-widest text-center drop-shadow mb-0.5">
                            JAKET & WORKWEAR
                        </span>
                        <a href="{{ route('collections.show', 'men-workwear-jackets') }}" 
                           class="border border-white hover:bg-white text-white hover:text-charcoal font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-4 sm:px-5 py-2 rounded-full transition duration-150 min-w-[125px] sm:min-w-[135px] text-center leading-tight backdrop-blur-2xs bg-black/20 hover:bg-white shadow-md">
                            WORKWEAR & BOMBER
                        </a>
                        <a href="{{ route('collections.show', 'men-sweats-hoodies') }}" 
                           class="border border-white hover:bg-white text-white hover:text-charcoal font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-4 sm:px-5 py-2 rounded-full transition duration-150 min-w-[125px] sm:min-w-[135px] text-center leading-tight backdrop-blur-2xs bg-black/20 hover:bg-white shadow-md">
                            HOODIE VINTAGE
                        </a>
                    </div>
                </div>

                <!-- Card 3: Dusty Mauve (#9d7370) -> TOPI & HEADWEAR -->
                <div data-cat-card="3"
                     @click="if (window.innerWidth < 1024) activeCat = 3"
                     :class="activeCat === 3 ? 'rounded-full ring-2 ring-white/60 shadow-xl' : 'rounded-[24px] sm:rounded-[28px] lg:hover:rounded-full'"
                     class="group relative bg-[#9d7370] flex-none w-[76vw] max-w-[320px] sm:w-[280px] lg:w-auto h-[420px] sm:h-[450px] lg:h-[490px] overflow-hidden flex flex-col items-center justify-center p-4 sm:p-5 lg:p-6 transition-all duration-500 ease-in-out cursor-pointer shadow-xs hover:shadow-xl select-none snap-center">
                    
                    <!-- Large Centered Transparent Product Cutout -->
                    <div class="w-full flex-1 flex items-center justify-center relative px-2 my-auto overflow-hidden">
                        <img src="{{ asset('images/home/cat-pink-flat.png') }}" 
                             alt="Topi Vintage Snapback fifa" 
                             class="w-auto h-auto max-w-[95%] max-h-[175px] sm:max-h-[200px] lg:max-h-[230px] object-contain drop-shadow-2xl scale-110 sm:scale-115 group-hover:scale-120 transition-transform duration-500">
                    </div>

                    <!-- Normal State Center Pill -->
                    <div :class="activeCat === 3 ? 'opacity-0 scale-95' : 'opacity-100 scale-100 lg:group-hover:opacity-0 lg:group-hover:scale-95'"
                         class="absolute inset-0 flex items-center justify-center pointer-events-none transition-all duration-300 px-4">
                        <span class="border border-white text-white font-semibold text-[11px] sm:text-[12px] uppercase tracking-widest px-6 py-2.5 rounded-full text-center leading-none shadow-xs backdrop-blur-2xs bg-black/10">
                            TOPI & HEADWEAR
                        </span>
                    </div>

                    <!-- Hover / Active State Overlay -->
                    <div :class="activeCat === 3 ? 'opacity-100 scale-100 pointer-events-auto' : 'opacity-0 scale-95 lg:group-hover:opacity-100 lg:group-hover:scale-100 pointer-events-none lg:group-hover:pointer-events-auto'"
                         class="absolute inset-0 flex flex-col items-center justify-center gap-2 sm:gap-2.5 transition-all duration-300 z-20 p-3 bg-black/20">
                        <span class="text-white font-extrabold text-[11px] sm:text-[12px] uppercase tracking-widest text-center drop-shadow mb-0.5">
                            TOPI & HEADWEAR
                        </span>
                        <a href="{{ route('collections.show', 'men-vintage-snapback') }}" 
                           class="border border-white hover:bg-white text-white hover:text-charcoal font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-4 sm:px-5 py-2 rounded-full transition duration-150 min-w-[125px] sm:min-w-[135px] text-center leading-tight backdrop-blur-2xs bg-black/20 hover:bg-white shadow-md">
                            SNAPBACK 90S
                        </a>
                        <a href="{{ route('collections.show', 'men-knit-beanie') }}" 
                           class="border border-white hover:bg-white text-white hover:text-charcoal font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-4 sm:px-5 py-2 rounded-full transition duration-150 min-w-[125px] sm:min-w-[135px] text-center leading-tight backdrop-blur-2xs bg-black/20 hover:bg-white shadow-md">
                            BEANIE & KUPLUK
                        </a>
                    </div>
                </div>

                <!-- Card 4: Sage Green (#7d8d7e) -> CELANA & DENIM -->
                <div data-cat-card="4"
                     @click="if (window.innerWidth < 1024) activeCat = 4"
                     :class="activeCat === 4 ? 'rounded-full ring-2 ring-white/60 shadow-xl' : 'rounded-[24px] sm:rounded-[28px] lg:hover:rounded-full'"
                     class="group relative bg-[#7d8d7e] flex-none w-[76vw] max-w-[320px] sm:w-[280px] lg:w-auto h-[420px] sm:h-[450px] lg:h-[490px] overflow-hidden flex flex-col items-center justify-center p-4 sm:p-5 lg:p-6 transition-all duration-500 ease-in-out cursor-pointer shadow-xs hover:shadow-xl select-none snap-center">
                    
                    <!-- Large Centered Transparent Product Cutout -->
                    <div class="w-full flex-1 flex items-center justify-center relative px-2 my-auto overflow-hidden">
                        <img src="{{ asset('images/home/cat-sage-runner.png') }}" 
                             alt="Celana Denim Levi's 501 Vintage fifa" 
                             class="w-auto h-auto max-w-[95%] max-h-[175px] sm:max-h-[200px] lg:max-h-[230px] object-contain drop-shadow-2xl scale-110 sm:scale-115 group-hover:scale-120 transition-transform duration-500">
                    </div>

                    <!-- Normal State Center Pill -->
                    <div :class="activeCat === 4 ? 'opacity-0 scale-95' : 'opacity-100 scale-100 lg:group-hover:opacity-0 lg:group-hover:scale-95'"
                         class="absolute inset-0 flex items-center justify-center pointer-events-none transition-all duration-300 px-4">
                        <span class="border border-white text-white font-semibold text-[11px] sm:text-[12px] uppercase tracking-widest px-6 py-2.5 rounded-full text-center leading-none shadow-xs backdrop-blur-2xs bg-black/10">
                            CELANA & DENIM
                        </span>
                    </div>

                    <!-- Hover / Active State Overlay -->
                    <div :class="activeCat === 4 ? 'opacity-100 scale-100 pointer-events-auto' : 'opacity-0 scale-95 lg:group-hover:opacity-100 lg:group-hover:scale-100 pointer-events-none lg:group-hover:pointer-events-auto'"
                         class="absolute inset-0 flex flex-col items-center justify-center gap-2 sm:gap-2.5 transition-all duration-300 z-20 p-3 bg-black/20">
                        <span class="text-white font-extrabold text-[11px] sm:text-[12px] uppercase tracking-widest text-center drop-shadow mb-0.5">
                            CELANA & DENIM
                        </span>
                        <a href="{{ route('collections.show', 'men-vintage-denim') }}" 
                           class="border border-white hover:bg-white text-white hover:text-charcoal font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-4 sm:px-5 py-2 rounded-full transition duration-150 min-w-[125px] sm:min-w-[135px] text-center leading-tight backdrop-blur-2xs bg-black/20 hover:bg-white shadow-md">
                            JEANS LEVI'S 501
                        </a>
                        <a href="{{ route('collections.show', 'men-cargo-pants') }}" 
                           class="border border-white hover:bg-white text-white hover:text-charcoal font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-4 sm:px-5 py-2 rounded-full transition duration-150 min-w-[125px] sm:min-w-[135px] text-center leading-tight backdrop-blur-2xs bg-black/20 hover:bg-white shadow-md">
                            CARGO & CORDUROY
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 3. Best Sellers Carousel Grid (Interactive Smooth Scrolling Carousel) -->
    <section class="max-w-[1400px] mx-auto px-3 sm:px-6 py-6 sm:py-8" 
             x-data="{
                 canScrollLeft: false,
                 canScrollRight: true,
                 checkScroll() {
                     const el = this.$refs.carousel;
                     if (!el) return;
                     this.canScrollLeft = el.scrollLeft > 15;
                     this.canScrollRight = el.scrollLeft + el.clientWidth < el.scrollWidth - 15;
                 },
                 scrollLeft() {
                     const el = this.$refs.carousel;
                     if (!el) return;
                     const step = el.firstElementChild ? (el.firstElementChild.offsetWidth + 20) : 320;
                     el.scrollBy({ left: -step, behavior: 'smooth' });
                 },
                 scrollRight() {
                     const el = this.$refs.carousel;
                     if (!el) return;
                     const step = el.firstElementChild ? (el.firstElementChild.offsetWidth + 20) : 320;
                     el.scrollBy({ left: step, behavior: 'smooth' });
                 }
             }">
        <div class="bg-[#ece7e1] rounded-[28px] sm:rounded-[32px] p-5 sm:p-7 lg:p-8">
            <!-- Section Header Row -->
            <div class="flex items-center justify-between mb-5 sm:mb-7">
                <div>
                    <a href="{{ route('collections.show', 'best-sellers') }}" 
                       class="font-sans font-bold text-[13px] sm:text-[14px] uppercase tracking-wider text-black border-b border-black pb-0.5 hover:opacity-75 transition inline-block">
                        PALING BANYAK DIBURU (VAULT & RARE)
                    </a>
                </div>

                <!-- Left / Right Carousel Controls -->
                <div class="flex items-center space-x-2">
                    <button type="button" 
                            @click="scrollLeft()" 
                            :disabled="!canScrollLeft"
                            :class="canScrollLeft ? 'opacity-100 hover:bg-black hover:text-white cursor-pointer active:scale-95' : 'opacity-35 cursor-not-allowed'"
                            class="w-8 h-8 rounded-full border border-black/80 flex items-center justify-center text-black transition focus:outline-none shadow-2xs" 
                            aria-label="Produk Sebelumnya">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button type="button" 
                            @click="scrollRight()" 
                            :disabled="!canScrollRight"
                            :class="canScrollRight ? 'opacity-100 hover:bg-black hover:text-white cursor-pointer active:scale-95' : 'opacity-35 cursor-not-allowed'"
                            class="w-8 h-8 rounded-full border border-black/80 flex items-center justify-center text-black transition focus:outline-none shadow-2xs" 
                            aria-label="Produk Selanjutnya">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Product Cards Carousel Container -->
            <div x-ref="carousel" class="flex gap-4 sm:gap-5 overflow-x-auto scrollbar-none pb-2 scroll-smooth snap-x snap-mandatory select-none">
                @foreach ($newArrivals as $prod)
                    @php
                        $primaryImg = $prod->images->firstWhere('is_primary', true) ?? $prod->images->first();
                        $firstVariant = $prod->variants->first();
                    @endphp
                    <a href="{{ route('products.show', $prod->slug) }}" 
                       class="group flex-none w-[270px] sm:w-[290px] lg:w-[305px] block bg-white rounded-[20px] p-4 sm:p-5 flex flex-col justify-between h-[390px] sm:h-[420px] shadow-2xs hover:shadow-md transition duration-300 relative select-none snap-start">
                        <!-- Top Badge -->
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center bg-[#252220] text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full">
                                KONDISI 9.5/10
                            </span>
                            <span class="text-[10px] font-bold text-stone uppercase tracking-wider">
                                SIZE {{ $firstVariant?->size ?? 'L' }}
                            </span>
                        </div>

                        <!-- Centered Product Photography -->
                        <div class="flex-1 flex items-center justify-center my-2 overflow-hidden">
                            @if ($primaryImg)
                                <img src="{{ asset($primaryImg->image_path) }}" 
                                     alt="{{ $prod->name }}" 
                                     class="max-w-[95%] max-h-[170px] sm:max-h-[190px] object-contain drop-shadow-md group-hover:scale-105 transition-transform duration-300">
                            @endif
                        </div>

                        <!-- Bottom Details & Price Row -->
                        <div class="pt-2">
                            <h3 class="font-sans font-bold text-[11px] sm:text-[12px] uppercase tracking-wider text-black leading-snug group-hover:underline truncate">
                                {{ $prod->name }}
                            </h3>
                            <p class="text-[12px] sm:text-[13px] text-[#5c554e] font-normal mt-0.5 truncate">
                                {{ $prod->short_description }}
                            </p>
                            <!-- Price Row -->
                            <div class="mt-3 pt-0.5 flex items-center justify-between">
                                @if ($prod->compare_at_price && $prod->compare_at_price > $prod->base_price)
                                    <span class="text-[11px] text-stone line-through">Rp {{ number_format($prod->compare_at_price, 0, ',', '.') }}</span>
                                @else
                                    <span></span>
                                @endif
                                <div class="text-right">
                                    <span class="font-sans font-bold text-[13px] sm:text-[14px] text-black">
                                        Rp {{ number_format($prod->base_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 4. 3-Column Lifestyle Editorial Grid with Dual CTA Buttons (Gambar 4 - Non-transparent Model Photos) -->
    <section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-14">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
            
            <!-- Card 1: Workwear & Streetwear Vault -->
            <div class="group relative rounded-[24px] sm:rounded-[28px] overflow-hidden aspect-[3/4] sm:h-[480px] lg:h-[540px] block shadow-sm hover:shadow-lg transition duration-300 select-none">
                <img src="{{ asset('images/home/travel-slides.jpg') }}" 
                     alt="Koleksi Workwear dan Streetwear Vintage" 
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-black/30 group-hover:bg-black/20 transition"></div>
                
                <!-- Center Title -->
                <div class="absolute inset-0 flex items-center justify-center p-6 text-center">
                    <h2 class="font-display font-normal text-3xl sm:text-4xl text-white leading-tight drop-shadow-md">
                        Workwear &<br>Streetwear Vault
                    </h2>
                </div>

                <!-- Bottom Action Buttons -->
                <div class="absolute bottom-5 sm:bottom-6 inset-x-4 sm:inset-x-6 z-20 flex items-center justify-center gap-2 sm:gap-3">
                    <a href="{{ route('collections.show', 'workwear') }}?gender=men" 
                       class="border border-white/90 text-white hover:bg-white hover:text-charcoal font-sans font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-3 sm:px-5 py-2 sm:py-2.5 rounded-full transition duration-200 shadow-md backdrop-blur-2xs bg-black/15 hover:bg-white text-center flex-1 max-w-[150px]">
                        KOLEKSI PRIA
                    </a>
                    <a href="{{ route('collections.show', 'workwear') }}?gender=women" 
                       class="border border-white/90 text-white hover:bg-white hover:text-charcoal font-sans font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-3 sm:px-5 py-2 sm:py-2.5 rounded-full transition duration-200 shadow-md backdrop-blur-2xs bg-black/15 hover:bg-white text-center flex-1 max-w-[150px]">
                        KOLEKSI WANITA
                    </a>
                </div>
            </div>

            <!-- Card 2: Retro 90s & Y2K Aesthetic -->
            <div class="group relative rounded-[24px] sm:rounded-[28px] overflow-hidden aspect-[3/4] sm:h-[480px] lg:h-[540px] block shadow-sm hover:shadow-lg transition duration-300 select-none">
                <img src="{{ asset('images/home/woman-swing.jpg') }}" 
                     alt="Koleksi Retro 90s dan Y2K" 
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-black/30 group-hover:bg-black/20 transition"></div>
                
                <!-- Center Title -->
                <div class="absolute inset-0 flex items-center justify-center p-6 text-center">
                    <h2 class="font-display font-normal text-3xl sm:text-4xl text-white leading-tight drop-shadow-md">
                        Retro 90s &<br>Y2K Aesthetic
                    </h2>
                </div>

                <!-- Bottom Action Buttons -->
                <div class="absolute bottom-5 sm:bottom-6 inset-x-4 sm:inset-x-6 z-20 flex items-center justify-center gap-2 sm:gap-3">
                    <a href="{{ route('collections.show', 'vintage-90s') }}?gender=men" 
                       class="border border-white/90 text-white hover:bg-white hover:text-charcoal font-sans font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-3 sm:px-5 py-2 sm:py-2.5 rounded-full transition duration-200 shadow-md backdrop-blur-2xs bg-black/15 hover:bg-white text-center flex-1 max-w-[150px]">
                        KOLEKSI PRIA
                    </a>
                    <a href="{{ route('collections.show', 'vintage-90s') }}?gender=women" 
                       class="border border-white/90 text-white hover:bg-white hover:text-charcoal font-sans font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-3 sm:px-5 py-2 sm:py-2.5 rounded-full transition duration-200 shadow-md backdrop-blur-2xs bg-black/15 hover:bg-white text-center flex-1 max-w-[150px]">
                        KOLEKSI WANITA
                    </a>
                </div>
            </div>

            <!-- Card 3: Slow Fashion & Zero Waste -->
            <div class="group relative rounded-[24px] sm:rounded-[28px] overflow-hidden aspect-[3/4] sm:h-[480px] lg:h-[540px] block shadow-sm hover:shadow-lg transition duration-300 select-none">
                <img src="{{ asset('images/home/summer-rocks.jpg') }}" 
                     alt="Gerakan Slow Fashion dan Zero Waste" 
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-black/30 group-hover:bg-black/20 transition"></div>
                
                <!-- Center Title -->
                <div class="absolute inset-0 flex items-center justify-center p-6 text-center">
                    <h2 class="font-display font-normal text-3xl sm:text-4xl text-white leading-tight drop-shadow-md">
                        Slow Fashion &<br>Zero Waste
                    </h2>
                </div>

                <!-- Bottom Action Buttons -->
                <div class="absolute bottom-5 sm:bottom-6 inset-x-4 sm:inset-x-6 z-20 flex items-center justify-center gap-2 sm:gap-3">
                    <a href="{{ route('collections.show', 'best-sellers') }}?gender=men" 
                       class="border border-white/90 text-white hover:bg-white hover:text-charcoal font-sans font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-3 sm:px-5 py-2 sm:py-2.5 rounded-full transition duration-200 shadow-md backdrop-blur-2xs bg-black/15 hover:bg-white text-center flex-1 max-w-[150px]">
                        KOLEKSI PRIA
                    </a>
                    <a href="{{ route('collections.show', 'best-sellers') }}?gender=women" 
                       class="border border-white/90 text-white hover:bg-white hover:text-charcoal font-sans font-bold text-[10px] sm:text-[11px] uppercase tracking-wider px-3 sm:px-5 py-2 sm:py-2.5 rounded-full transition duration-200 shadow-md backdrop-blur-2xs bg-black/15 hover:bg-white text-center flex-1 max-w-[150px]">
                        KOLEKSI WANITA
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- 5. 3 Value Proposition Cards on Oat Milk Canvas -->
    <section class="bg-[#f5f4f0] py-16 sm:py-24 border-t border-[#e8e5dc]">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Value Card 1 -->
                <div class="bg-white rounded-[22px] p-8 sm:p-10 shadow-xs border border-black/5 flex flex-col justify-start">
                    <h3 class="font-sans font-bold text-[13px] sm:text-[14px] uppercase tracking-wide10 text-charcoal mb-4">
                        1 OF 1 VINTAGE CURATED
                    </h3>
                    <p class="text-body-sm text-iron leading-relaxed">
                        Setiap potong busana adalah karya kurasi unik dan langka yang memiliki cerita sejarah tersendiri. Dapatkan gaya otentik yang tidak akan Anda temukan di etalase toko lain.
                    </p>
                </div>

                <!-- Value Card 2 -->
                <div class="bg-white rounded-[22px] p-8 sm:p-10 shadow-xs border border-black/5 flex flex-col justify-start">
                    <h3 class="font-sans font-bold text-[13px] sm:text-[14px] uppercase tracking-wide10 text-charcoal mb-4">
                        100% SUDAH DICUCI & HIGIENIS
                    </h3>
                    <p class="text-body-sm text-iron leading-relaxed">
                        Kenyamanan dan kesehatan Anda adalah prioritas kami. Semua item telah melalui proses sanitasi deep clean, cuci higienis anti-bakteri, dan steam suhu tinggi siap langsung pakai.
                    </p>
                </div>

                <!-- Value Card 3 -->
                <div class="bg-white rounded-[22px] p-8 sm:p-10 shadow-xs border border-black/5 flex flex-col justify-start">
                    <h3 class="font-sans font-bold text-[13px] sm:text-[14px] uppercase tracking-wide10 text-charcoal mb-4">
                        GERAKAN SLOW FASHION BERKELANJUTAN
                    </h3>
                    <p class="text-body-sm text-iron leading-relaxed">
                        Dengan berbelanja pakaian vintage pre-loved, Anda berkontribusi nyata menyelamatkan ribuan liter air bersih dan mencegah pakaian berkualitas terbuang menjadi limbah tekstil.
                    </p>
                </div>

            </div>
        </div>
    </section>
@endsection

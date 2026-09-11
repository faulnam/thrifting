@extends('layouts.app')

@section('content')
<div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12" x-data="{ filterDrawerOpen: false }">
    
    <!-- Breadcrumb & Header -->
    <div class="mb-8 border-b border-sand pb-6">
        <div class="flex items-center gap-2 text-caption text-stone uppercase tracking-wide10 mb-2">
            <a href="{{ route('home') }}" class="hover:text-charcoal">Beranda</a>
            <span>/</span>
            <span class="text-charcoal">{{ $pageTitle }}</span>
        </div>
        <h1 class="font-display text-3xl sm:text-4xl font-normal text-charcoal">{{ $pageTitle }}</h1>
        @if ($pageDescription)
            <p class="text-body-sm text-iron mt-2 max-w-2xl">{{ $pageDescription }}</p>
        @endif
    </div>

    <!-- Control Bar: Mobile Filter Button & Desktop Sort -->
    <div class="flex items-center justify-between gap-4 mb-6 pb-4 border-b border-sand/60">
        <!-- Mobile Filter Button -->
        <button type="button" 
                @click="filterDrawerOpen = true" 
                class="lg:hidden btn-pill-light text-caption flex items-center gap-2 px-4 py-2 min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            <span>Filter & Urutkan</span>
            @if (request()->hasAny(['category', 'gender', 'color', 'size', 'min_price', 'max_price']))
                <span class="w-2 h-2 rounded-full bg-charcoal"></span>
            @endif
        </button>

        <span class="text-caption text-iron font-medium">
            Menampilkan {{ $products->total() }} Produk
        </span>

        <!-- Sort Form (Desktop & Tablet) -->
        <form method="GET" action="{{ url()->current() }}" class="hidden sm:flex items-center gap-2">
            @foreach(request()->except(['sort', 'page']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <label for="sort-desktop" class="text-caption font-bold uppercase tracking-wide10 text-charcoal whitespace-nowrap">
                Urutkan:
            </label>
            <select id="sort-desktop" 
                    name="sort" 
                    onchange="this.form.submit()" 
                    class="rounded-input text-caption py-2 pl-3 pr-8 border border-sand bg-canvas text-charcoal focus:ring-1 focus:ring-charcoal outline-none">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga: Terendah ke Tertinggi</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga: Tertinggi ke Terendah</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama Produk (A-Z)</option>
            </select>
        </form>
    </div>

    <div class="flex gap-8 items-start">
        
        <!-- Desktop Sidebar Filter (Sticky) -->
        <aside class="hidden lg:block w-64 flex-shrink-0 sticky top-28 space-y-6">
            <form method="GET" action="{{ url()->current() }}">
                <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">

                <!-- Filter Header & Reset -->
                <div class="flex items-center justify-between pb-4 border-b border-sand">
                    <span class="text-caption font-bold uppercase tracking-wide10 text-charcoal">Filter Produk</span>
                    @if (request()->hasAny(['category', 'gender', 'color', 'size', 'min_price', 'max_price']))
                        <a href="{{ url()->current() }}" class="text-caption text-iron hover:text-charcoal underline">
                            Reset
                        </a>
                    @endif
                </div>

                <!-- Gender Filter -->
                <div class="py-4 border-b border-sand space-y-2">
                    <span class="text-caption font-bold uppercase tracking-wide10 text-charcoal block mb-2">Gender</span>
                    <div class="space-y-1.5">
                        @foreach (['men' => 'Pria', 'women' => 'Wanita', 'unisex' => 'Unisex'] as $gVal => $gLabel)
                            <label class="flex items-center gap-2 cursor-pointer text-body-sm text-iron hover:text-charcoal min-h-[32px]">
                                <input type="radio" 
                                       name="gender" 
                                       value="{{ $gVal }}" 
                                       {{ request('gender') == $gVal ? 'checked' : '' }}
                                       onchange="this.form.submit()"
                                       class="rounded-full border-slateBorder text-charcoal focus:ring-charcoal">
                                <span>{{ $gLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="py-4 border-b border-sand space-y-2">
                    <span class="text-caption font-bold uppercase tracking-wide10 text-charcoal block mb-2">Kategori</span>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                        @foreach ($availableCategories as $pCat)
                            <label class="flex items-center gap-2 cursor-pointer text-body-sm text-iron hover:text-charcoal min-h-[32px]">
                                <input type="radio" 
                                       name="category" 
                                       value="{{ $pCat->slug }}" 
                                       {{ request('category') == $pCat->slug ? 'checked' : '' }}
                                       onchange="this.form.submit()"
                                       class="rounded-full border-slateBorder text-charcoal focus:ring-charcoal">
                                <span>{{ $pCat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Size Filter -->
                @if ($availableSizes->isNotEmpty())
                    <div class="py-4 border-b border-sand space-y-2">
                        <span class="text-caption font-bold uppercase tracking-wide10 text-charcoal block mb-2">Ukuran</span>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-1.5">
                            @foreach ($availableSizes as $sz)
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                           name="size" 
                                           value="{{ $sz }}" 
                                           {{ request('size') == $sz ? 'checked' : '' }}
                                           onchange="this.form.submit()"
                                           class="peer sr-only">
                                    <span class="flex items-center justify-center text-caption font-semibold border border-sand rounded-xl py-2 px-1 peer-checked:bg-charcoal peer-checked:text-canvas peer-checked:border-charcoal hover:border-charcoal transition text-center truncate">
                                        {{ $sz }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Color Filter -->
                @if ($availableColors->isNotEmpty())
                    <div class="py-4 border-b border-sand space-y-2">
                        <span class="text-caption font-bold uppercase tracking-wide10 text-charcoal block mb-2">Warna</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($availableColors as $col)
                                <label class="cursor-pointer" title="{{ $col->color_name }}">
                                    <input type="radio" 
                                           name="color" 
                                           value="{{ $col->color_name }}" 
                                           {{ request('color') == $col->color_name ? 'checked' : '' }}
                                           onchange="this.form.submit()"
                                           class="peer sr-only">
                                    <span class="w-6 h-6 rounded-full border border-stone/40 block peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-charcoal hover:scale-110 transition"
                                          style="background-color: {{ $col->color_hex }};"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            </form>
        </aside>

        <!-- Product Grid Area (Mobile 2 cols, md: 3 cols, lg: 4 cols) -->
        <main class="flex-1 min-w-0">
            @if ($products->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-x-4 gap-y-8 sm:gap-x-6 sm:gap-y-10">
                    @foreach ($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12 pt-8 border-t border-sand">
                    {{ $products->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16 px-4 bg-sand/20 rounded-card border border-sand space-y-4">
                    <p class="font-sans font-bold text-lg text-charcoal">Tidak Ada Produk yang Sesuai</p>
                    <p class="text-body-sm text-iron max-w-md mx-auto">
                        Coba sesuaikan pilihan filter atau cari dengan kata kunci lain.
                    </p>
                    <div class="pt-2">
                        <a href="{{ url()->current() }}" class="btn-pill-dark text-caption">
                            Hapus Semua Filter
                        </a>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Mobile Bottom-Sheet Filter & Sort Modal -->
    <div x-show="filterDrawerOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-50 lg:hidden"
         @click="filterDrawerOpen = false"
         style="display: none;">
    </div>

    <div x-show="filterDrawerOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed inset-x-0 bottom-0 max-h-[85vh] bg-canvas rounded-t-card shadow-2xl z-50 flex flex-col justify-between overflow-y-auto lg:hidden"
         style="display: none;">
        
        <!-- Bottom-Sheet Header -->
        <div class="flex items-center justify-between p-4 border-b border-sand sticky top-0 bg-canvas z-10">
            <span class="font-sans font-bold text-body text-charcoal">Filter & Urutkan</span>
            <button type="button" 
                    @click="filterDrawerOpen = false" 
                    class="p-2 text-charcoal min-h-[44px] min-w-[44px] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Filter Form Body -->
        <form method="GET" action="{{ url()->current() }}" class="p-4 space-y-6 flex-grow">
            <!-- Sort In Mobile Drawer -->
            <div class="space-y-2">
                <label for="sort-mobile" class="text-caption font-bold uppercase tracking-wide10 text-charcoal block">
                    Urutkan Berdasarkan
                </label>
                <select id="sort-mobile" 
                        name="sort" 
                        class="w-full rounded-input text-body-sm py-3 px-4 border border-sand bg-canvas text-charcoal focus:ring-1 focus:ring-charcoal outline-none min-h-[44px]">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga: Terendah ke Tertinggi</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga: Tertinggi ke Terendah</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama Produk (A-Z)</option>
                </select>
            </div>

            <!-- Gender Filter -->
            <div class="border-t border-sand pt-4 space-y-2">
                <span class="text-caption font-bold uppercase tracking-wide10 text-charcoal block">Gender</span>
                <div class="grid grid-cols-3 gap-2">
                    @foreach (['men' => 'Pria', 'women' => 'Wanita', 'unisex' => 'Unisex'] as $gVal => $gLabel)
                        <label class="cursor-pointer min-h-[44px]">
                            <input type="radio" name="gender" value="{{ $gVal }}" {{ request('gender') == $gVal ? 'checked' : '' }} class="peer sr-only">
                            <span class="flex items-center justify-center text-caption font-medium border border-sand rounded-input py-2.5 min-h-[44px] peer-checked:bg-charcoal peer-checked:text-canvas peer-checked:border-charcoal transition">
                                {{ $gLabel }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Size Filter -->
            @if ($availableSizes->isNotEmpty())
                <div class="border-t border-sand pt-4 space-y-2">
                    <span class="text-caption font-bold uppercase tracking-wide10 text-charcoal block">Ukuran</span>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ($availableSizes as $sz)
                            <label class="cursor-pointer min-h-[44px]">
                                <input type="radio" name="size" value="{{ $sz }}" {{ request('size') == $sz ? 'checked' : '' }} class="peer sr-only">
                                <span class="flex items-center justify-center text-caption font-semibold border border-sand rounded-xl py-2 min-h-[44px] peer-checked:bg-charcoal peer-checked:text-canvas peer-checked:border-charcoal transition">
                                    {{ $sz }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Action Buttons at bottom of mobile drawer -->
            <div class="pt-6 border-t border-sand sticky bottom-0 bg-canvas grid grid-cols-2 gap-3 pb-2">
                <a href="{{ url()->current() }}" class="btn-pill-light text-center w-full min-h-[44px]">
                    Reset
                </a>
                <button type="submit" class="btn-pill-dark text-center w-full min-h-[44px]">
                    Terapkan
                </button>
            </div>
        </form>

    </div>

</div>
@endsection

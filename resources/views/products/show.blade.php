@extends('layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description), 160))
@section('og_image', $product->images->first() ? (filter_var($product->images->first()->image_path, FILTER_VALIDATE_URL) ? $product->images->first()->image_path : asset('storage/' . $product->images->first()->image_path)) : asset('images/og-default.jpg'))

@push('styles')
<!-- Swiper CSS via CDN for mobile PDP gallery -->
<link rel="stylesheet" href="https://unpkg.com/swiper@11/swiper-bundle.min.css" />
<style>
    .swiper-pagination-bullet-active {
        background-color: #212121 !important;
    }
</style>
@endpush

@push('scripts')
<!-- Swiper JS via CDN -->
<script src="https://unpkg.com/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new Swiper('.pdp-mobile-swiper', {
            loop: false,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    });
</script>
@endpush

@section('content')
@php
    $images = $product->images->isNotEmpty() ? $product->images : collect([(object)['image_path' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80', 'is_primary' => true]]);
    $isDiscounted = $product->compare_at_price && $product->compare_at_price > $product->base_price;
    $initialColor = $colorsList[0] ?? ['color_name' => 'Default', 'color_hex' => '#212121', 'sizes' => []];
@endphp

<div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10" 
     x-data="{
        colors: {{ json_encode($colorsList) }},
        selectedColorIndex: 0,
        selectedVariantId: null,
        selectedSize: null,
        selectedStock: 0,
        isWishlisted: {{ auth()->check() && auth()->user()->wishlists->contains('product_id', $product->id) ? 'true' : 'false' }},
        wishlistLoading: false,
        get currentColor() {
            return this.colors[this.selectedColorIndex] || { color_name: '', sizes: [] };
        },
        selectColor(index) {
            this.selectedColorIndex = index;
            this.selectedVariantId = null;
            this.selectedSize = null;
            this.selectedStock = 0;
        },
        selectSize(variant) {
            if (variant.stock <= 0) return;
            this.selectedVariantId = variant.id;
            this.selectedSize = variant.size;
            this.selectedStock = variant.stock;
        },
        addToBag() {
            if (!this.selectedVariantId) return;
            $store.cart.addItem(this.selectedVariantId, 1);
        },
        async toggleWishlist() {
            if (this.wishlistLoading) return;
            this.wishlistLoading = true;
            try {
                const res = await fetch('{{ route('wishlist.toggle', $product->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
                if (res.status === 401) {
                    const data = await res.json();
                    window.location.href = data.redirect_url || '{{ route('login') }}';
                    return;
                }
                const data = await res.json();
                if (data.success) {
                    this.isWishlisted = data.in_wishlist;
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: data.in_wishlist ? 'Produk ditambahkan ke wishlist' : 'Produk dihapus dari wishlist',
                            type: 'info'
                        }
                    }));
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.wishlistLoading = false;
            }
        }
     }">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-caption text-stone uppercase tracking-wide10 mb-6">
        <a href="{{ route('home') }}" class="hover:text-charcoal">Beranda</a>
        <span>/</span>
        <a href="{{ route('collections.show', $product->category->slug ?? 'all') }}" class="hover:text-charcoal">
            {{ $product->category->name ?? 'Sepatu' }}
        </a>
        <span>/</span>
        <span class="text-charcoal truncate">{{ $product->name }}</span>
    </nav>

    <!-- Main PDP Grid: Gallery Left (Desktop), Details Right -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <!-- Left: Image Gallery -->
        <div class="lg:col-span-7">
            <!-- Mobile Swipeable Carousel (Swiper.js) -->
            <div class="lg:hidden relative">
                <div class="swiper pdp-mobile-swiper rounded-card overflow-hidden bg-[#f5f4f0] aspect-square flex items-center justify-center p-4">
                    <div class="swiper-wrapper">
                        @foreach ($images as $img)
                            <div class="swiper-slide flex items-center justify-center">
                                <img src="{{ $img->url ?? (filter_var($img->image_path, FILTER_VALIDATE_URL) ? $img->image_path : asset(ltrim($img->image_path, '/'))) }}" 
                                     alt="{{ $product->name }}" 
                                     class="max-w-full max-h-full object-contain object-center">
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination py-2"></div>
                </div>
            </div>

            <!-- Desktop Multi-Image Grid -->
            <div class="hidden lg:grid grid-cols-2 gap-4">
                @foreach ($images as $index => $img)
                    <div class="rounded-card overflow-hidden bg-[#f5f4f0] flex items-center justify-center p-6 {{ $index === 0 ? 'col-span-2 aspect-[4/3]' : 'aspect-square' }}">
                        <img src="{{ $img->url ?? (filter_var($img->image_path, FILTER_VALIDATE_URL) ? $img->image_path : asset(ltrim($img->image_path, '/'))) }}" 
                             alt="{{ $product->name }} view {{ $index + 1 }}" 
                             class="max-w-full max-h-full object-contain object-center hover:scale-105 transition duration-500">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Product Purchase Details & Specs (Sticky on Desktop) -->
        <div class="lg:col-span-5 lg:sticky lg:top-28 space-y-6">
            
            <!-- Title, Badge & Price -->
            <div class="space-y-2 border-b border-sand pb-4">
                @if ($product->collections->contains('slug', 'new-arrivals'))
                    <span class="inline-block text-caption font-medium uppercase tracking-wide10 text-charcoal">
                        Produk Terbaru
                    </span>
                @endif
                <h1 class="font-sans font-bold text-2xl sm:text-3xl text-charcoal leading-tight">
                    {{ $product->name }}
                </h1>
                <p class="text-body-sm text-iron">{{ $product->short_description }}</p>

                <!-- Price Display -->
                <div class="flex items-baseline gap-3 pt-2">
                    <span class="font-sans font-bold text-xl sm:text-2xl text-charcoal">
                        Rp {{ number_format($product->base_price, 0, ',', '.') }}
                    </span>
                    @if ($isDiscounted)
                        <span class="text-body-sm text-stone line-through">
                            Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}
                        </span>
                        <span class="bg-charcoal text-canvas text-[11px] font-bold uppercase tracking-wide10 px-2 py-0.5 rounded-pill">
                            Hemat Rp {{ number_format($product->compare_at_price - $product->base_price, 0, ',', '.') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Color Swatch Selector -->
            <div class="space-y-3" x-show="colors.length > 0">
                <div class="flex items-center justify-between">
                    <span class="text-caption font-bold uppercase tracking-wide10 text-charcoal">
                        Warna: <span class="font-normal text-iron" x-text="currentColor.color_name"></span>
                    </span>
                </div>

                <!-- Swatches List -->
                <div class="flex items-center gap-3">
                    <template x-for="(col, idx) in colors" :key="idx">
                        <button type="button" 
                                @click="selectColor(idx)"
                                :class="selectedColorIndex === idx ? 'ring-2 ring-offset-2 ring-charcoal scale-105' : 'opacity-80 hover:opacity-100'"
                                class="w-8 h-8 rounded-full border border-stone/50 transition-all flex items-center justify-center p-0.5"
                                :title="col.color_name">
                            <span class="w-full h-full rounded-full block border border-black/10" :style="'background-color: ' + col.color_hex"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Size Selector -->
            <div class="space-y-3 pt-2" x-show="currentColor.sizes.length > 0">
                <div class="flex items-center justify-between">
                    <span class="text-caption font-bold uppercase tracking-wide10 text-charcoal">
                        Pilih Ukuran
                    </span>
                    <a href="{{ route('pages.show', 'size-guide') }}" class="text-caption text-iron hover:text-charcoal underline">
                        Panduan Ukuran PxL
                    </a>
                </div>

                <!-- Size Grid Buttons (Clean Standard Pills) -->
                <div class="flex flex-wrap gap-2.5">
                    <template x-for="v in currentColor.sizes" :key="v.id">
                        <button type="button" 
                                @click="selectSize(v)"
                                :disabled="v.stock <= 0"
                                :class="{
                                    'bg-charcoal text-canvas border-charcoal font-bold shadow-xs': selectedVariantId === v.id,
                                    'bg-canvas text-charcoal border-sand hover:border-charcoal': selectedVariantId !== v.id && v.stock > 0,
                                    'bg-sand/40 text-stone border-sand/40 cursor-not-allowed line-through opacity-50': v.stock <= 0
                                }"
                                class="min-w-[54px] px-4 py-2.5 min-h-[44px] flex items-center justify-center border rounded-xl text-body-sm font-semibold transition duration-150 relative cursor-pointer">
                            <span x-text="v.size"></span>
                        </button>
                    </template>
                </div>

                <!-- Stock info message -->
                <div class="text-caption text-iron min-h-[20px]">
                    <template x-if="selectedVariantId && selectedStock <= 5 && selectedStock > 0">
                        <span class="text-amber-700 font-medium">Stok vintage 1-of-1: Tersisa <span x-text="selectedStock"></span> item!</span>
                    </template>
                    <template x-if="!selectedVariantId">
                        <span>Pilih ukuran untuk melanjutkan pembelian.</span>
                    </template>
                </div>
            </div>

            <!-- CTA: Add to Bag & Wishlist -->
            <div class="pt-2 space-y-2.5">
                <button type="button" 
                        @click="addToBag()"
                        :disabled="!selectedVariantId"
                        :class="!selectedVariantId ? 'opacity-50 cursor-not-allowed' : 'hover:bg-black'"
                        class="btn-pill-dark w-full text-center py-4 text-body-sm font-bold tracking-wide10 min-h-[50px] shadow-sm">
                    <span x-show="!selectedVariantId">Pilih Ukuran Terlebih Dahulu</span>
                    <span x-show="selectedVariantId">Tambahkan ke Keranjang</span>
                </button>

                <button type="button" 
                        @click="toggleWishlist()"
                        :disabled="wishlistLoading"
                        class="btn-pill-light w-full text-center py-3 text-body-sm font-bold tracking-wide10 flex items-center justify-center gap-2 min-h-[44px] border border-sand hover:border-charcoal transition">
                    <svg class="w-4 h-4 transition-colors" 
                         :class="isWishlisted ? 'fill-charcoal text-charcoal' : 'fill-none text-charcoal'" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span x-text="isWishlisted ? 'Tersimpan di Wishlist' : 'Simpan ke Wishlist'"></span>
                </button>

                <!-- Value Props -->
                <div class="grid grid-cols-2 gap-2 text-center text-[11px] text-stone pt-2">
                    <span class="flex items-center justify-center gap-1">
                        <span>✓</span> Gratis Ongkir > Rp 500k
                    </span>
                    <span class="flex items-center justify-center gap-1">
                        <span>✓</span> Uji Coba 30 Hari
                    </span>
                </div>
            </div>

            <!-- Toast Alert for Add to Bag -->
            <div x-show="addedToast" 
                 x-transition 
                 class="p-4 rounded-card bg-charcoal text-canvas text-body-sm flex items-center justify-between gap-3 shadow-lg" 
                 style="display: none;">
                <div class="flex items-center gap-2">
                    <span class="text-green-400 font-bold">✓</span>
                    <span>Berhasil ditambahkan ke keranjang (Ukuran <span x-text="selectedSize"></span>)!</span>
                </div>
                <a href="{{ route('cart.index') }}" class="underline text-caption uppercase tracking-wide10 font-bold">
                    Lihat Keranjang
                </a>
            </div>

            <!-- Accordions: Description, Materials, Sustainability, Shipping -->
            <div class="border-t border-sand pt-6 space-y-4" x-data="{
                activeAccordion: null,
                toggle(key) { this.activeAccordion = this.activeAccordion === key ? null : key }
            }">
                
                <!-- 1. Description & Details -->
                <div class="border-b border-sand pb-4">
                    <button type="button" 
                            @click="toggle('desc')" 
                            class="w-full flex items-center justify-between text-body-sm font-bold uppercase tracking-wide10 text-charcoal min-h-[44px]">
                        <span>Deskripsi & Fitur Produk</span>
                        <svg class="w-4 h-4 transform transition-transform" :class="activeAccordion === 'desc' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="activeAccordion === 'desc'" x-collapse class="pt-2 text-body-sm text-iron leading-relaxed space-y-2">
                        {!! $product->description !!}
                    </div>
                </div>

                <!-- 2. Materials -->
                @if ($product->material_info)
                    <div class="border-b border-sand pb-4">
                        <button type="button" 
                                @click="toggle('material')" 
                                class="w-full flex items-center justify-between text-body-sm font-bold uppercase tracking-wide10 text-charcoal min-h-[44px]">
                            <span>Material Alami & Spesifikasi</span>
                            <svg class="w-4 h-4 transform transition-transform" :class="activeAccordion === 'material' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="activeAccordion === 'material'" x-collapse class="pt-2 text-body-sm text-iron leading-relaxed">
                            <p>{{ $product->material_info }}</p>
                            <p class="text-caption text-stone mt-2">Berat Pengiriman: {{ $product->weight_grams }} gram</p>
                        </div>
                    </div>
                @endif

                <!-- 3. Sustainability -->
                @if ($product->sustainability_note)
                    <div class="border-b border-sand pb-4">
                        <button type="button" 
                                @click="toggle('sustain')" 
                                class="w-full flex items-center justify-between text-body-sm font-bold uppercase tracking-wide10 text-charcoal min-h-[44px]">
                            <span>Keberlanjutan & Jejak Karbon</span>
                            <svg class="w-4 h-4 transform transition-transform" :class="activeAccordion === 'sustain' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="activeAccordion === 'sustain'" x-collapse class="pt-2 text-body-sm text-iron leading-relaxed">
                            <p>{{ $product->sustainability_note }}</p>
                        </div>
                    </div>
                @endif

                <!-- 4. Shipping & Returns -->
                <div class="border-b border-sand pb-4">
                    <button type="button" 
                            @click="toggle('shipping')" 
                            class="w-full flex items-center justify-between text-body-sm font-bold uppercase tracking-wide10 text-charcoal min-h-[44px]">
                        <span>Pengiriman & Pengembalian</span>
                        <svg class="w-4 h-4 transform transition-transform" :class="activeAccordion === 'shipping' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="activeAccordion === 'shipping'" x-collapse class="pt-2 text-body-sm text-iron leading-relaxed space-y-2">
                        <p>Pengiriman didukung oleh <strong>Biteship</strong> dengan kurir terpercaya (JNE, J&T, SiCepat, AnterAja, GoSend). Cek estimasi ongkir otomatis saat checkout.</p>
                        <p>Pengembalian gratis dalam waktu 30 hari jika produk tidak cocok dengan ukuran kaki Anda.</p>
                    </div>
                </div>

                <!-- 5. Reviews & Ratings -->
                <div class="border-b border-sand pb-4" x-data="{ reviewFormOpen: false }">
                    <button type="button" 
                            @click="toggle('reviews')" 
                            class="w-full flex items-center justify-between text-body-sm font-bold uppercase tracking-wide10 text-charcoal min-h-[44px]">
                        <div class="flex items-center gap-2">
                            <span>Ulasan Pelanggan</span>
                            <span class="text-caption text-yellow-600 font-bold">★ {{ $product->average_rating }} ({{ $product->reviews_count }})</span>
                        </div>
                        <svg class="w-4 h-4 transform transition-transform" :class="activeAccordion === 'reviews' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="activeAccordion === 'reviews'" x-collapse class="pt-4 text-body-sm space-y-6">
                        <!-- Review Action Button -->
                        @auth
                            @if(auth()->user()->isCustomer())
                                <div class="bg-sand/20 border border-sand rounded-card p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-charcoal text-caption uppercase tracking-wide10">Punya produk ini?</span>
                                        <button type="button" @click="reviewFormOpen = !reviewFormOpen" class="btn-pill-dark text-caption px-4 py-1.5">
                                            Tulis Ulasan
                                        </button>
                                    </div>

                                    <!-- Review Form -->
                                    <form x-show="reviewFormOpen" x-collapse action="{{ route('products.reviews.store', $product->id) }}" method="POST" class="mt-4 pt-4 border-t border-sand space-y-4">
                                        @csrf
                                        <div>
                                            <label class="block text-caption font-bold uppercase text-charcoal mb-1">Rating Bintang</label>
                                            <select name="rating" required class="input-clean w-full text-body-sm">
                                                <option value="5">★★★★★ (5/5) Sangat Puas</option>
                                                <option value="4">★★★★☆ (4/5) Puas</option>
                                                <option value="3">★★★☆☆ (3/5) Cukup</option>
                                                <option value="2">★★☆☆☆ (2/5) Kurang</option>
                                                <option value="1">★☆☆☆☆ (1/5) Kecewa</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-caption font-bold uppercase text-charcoal mb-1">Judul Ulasan</label>
                                            <input type="text" name="title" required placeholder="Misal: Sepatu ternyaman yang pernah saya pakai" class="input-clean w-full text-body-sm">
                                        </div>
                                        <div>
                                            <label class="block text-caption font-bold uppercase text-charcoal mb-1">Komentar / Pengalaman</label>
                                            <textarea name="comment" rows="3" required placeholder="Ceritakan bagaimana kenyamanan dan fitting sepatu ini di kaki Anda..." class="input-clean w-full text-body-sm"></textarea>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="reviewFormOpen = false" class="btn-pill-light text-caption px-4 py-1.5">Batal</button>
                                            <button type="submit" class="btn-pill-dark text-caption px-5 py-1.5">Kirim Ulasan</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @else
                            <div class="p-3 bg-sand/20 border border-sand rounded-card text-center text-caption text-stone">
                                <a href="{{ route('login') }}" class="font-bold text-charcoal underline">Masuk ke akun</a> untuk memberikan ulasan produk.
                            </div>
                        @endauth

                        <!-- Approved Reviews List -->
                        <div class="space-y-4 divide-y divide-sand">
                            @forelse ($product->approvedReviews as $review)
                                <div class="pt-4 first:pt-0 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-charcoal">{{ $review->user->name ?? 'Customer' }}</span>
                                        <span class="text-yellow-500 font-bold text-caption">{{ str_repeat('★', $review->rating) }}</span>
                                    </div>
                                    <h4 class="font-bold text-body-sm text-charcoal">{{ $review->title }}</h4>
                                    <p class="text-iron text-caption leading-relaxed">{{ $review->comment }}</p>
                                    <span class="text-[10px] text-stone block pt-1">{{ $review->created_at->format('d M Y') }}</span>
                                </div>
                            @empty
                                <p class="text-caption text-stone italic text-center py-4">Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan ulasan!</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Related Products ("Produk Rekomendasi Lainnya") -->
    @if ($relatedProducts->isNotEmpty())
        <section class="mt-20 pt-12 border-t border-sand">
            <div class="text-center mb-10">
                <h2 class="section-title">Produk Rekomendasi Lainnya</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                @foreach ($relatedProducts as $relProduct)
                    @include('partials.product-card', ['product' => $relProduct])
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection

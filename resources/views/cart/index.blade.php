@extends('layouts.app')

@section('content')
<div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-caption text-stone uppercase tracking-wide10 mb-6">
        <a href="{{ route('home') }}" class="hover:text-charcoal">Beranda</a>
        <span>/</span>
        <span class="text-charcoal">Keranjang Belanja</span>
    </nav>

    <div class="space-y-6">
        <!-- Title & Counter -->
        <div class="flex items-baseline justify-between border-b border-sand pb-4">
            <h1 class="font-sans font-bold text-2xl sm:text-3xl text-charcoal">
                Keranjang Belanja
            </h1>
            <span class="text-body-sm text-iron font-medium" x-text="$store.cart.count + ' Item'"></span>
        </div>

        <!-- Empty State (When count === 0) -->
        <div x-show="$store.cart.items.length === 0" class="text-center py-20 bg-sand/15 rounded-card space-y-4">
            <div class="w-20 h-20 mx-auto rounded-full bg-sand flex items-center justify-center text-charcoal">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <h2 class="font-sans font-bold text-xl text-charcoal">Keranjang Anda Masih Kosong</h2>
            <p class="text-body-sm text-iron max-w-md mx-auto">
                Belum ada produk yang ditambahkan. Jelajahi koleksi sepatu dan apparel ramah lingkungan kami.
            </p>
            <div class="pt-4 flex flex-wrap justify-center gap-4">
                <a href="{{ route('categories.men') }}" class="btn-pill-dark px-8 py-3.5 text-body-sm font-bold tracking-wide10">
                    Sepatu Pria
                </a>
                <a href="{{ route('categories.women') }}" class="btn-pill-light px-8 py-3.5 text-body-sm font-bold tracking-wide10">
                    Sepatu Wanita
                </a>
            </div>
        </div>

        <!-- 2-Column Cart Layout -->
        <div x-show="$store.cart.items.length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            <!-- Left: Items List & Free Shipping Meter (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Free Shipping Progress Card -->
                <div class="p-4 sm:p-5 bg-[#f7f6f2] rounded-card border border-sand">
                    <div class="flex items-center justify-between text-body-sm mb-2">
                        <template x-if="$store.cart.is_free_shipping">
                            <span class="text-charcoal font-bold flex items-center gap-2">
                                <span>🎉</span> Anda telah memenuhi syarat <strong>Gratis Ongkir ke Seluruh Indonesia!</strong>
                            </span>
                        </template>
                        <template x-if="!$store.cart.is_free_shipping">
                            <span class="text-charcoal font-medium">
                                Tambah <strong class="text-charcoal" x-text="$store.cart.remaining_free_shipping_formatted"></strong> lagi untuk mendapatkan <strong>Gratis Ongkir</strong>
                            </span>
                        </template>
                        <span class="text-caption font-bold text-charcoal" x-text="$store.cart.free_shipping_percent + '%'"></span>
                    </div>
                    <div class="w-full bg-sand/70 rounded-full h-2 overflow-hidden">
                        <div class="bg-charcoal h-full transition-all duration-300 rounded-full" 
                             :style="'width: ' + $store.cart.free_shipping_percent + '%'"></div>
                    </div>
                </div>

                <!-- Items Table / Cards -->
                <div class="divide-y divide-sand border-y border-sand">
                    <template x-for="item in $store.cart.items" :key="item.id">
                        <div class="py-6 flex flex-col sm:flex-row gap-4 sm:gap-6 items-start justify-between">
                            
                            <!-- Left Item Details -->
                            <div class="flex gap-4 sm:gap-5 flex-1">
                                <!-- Thumbnail -->
                                <a :href="'/products/' + item.product_slug" class="w-24 h-24 sm:w-28 sm:h-28 bg-[#f5f4f0] rounded-card overflow-hidden flex-shrink-0">
                                    <img :src="item.image_url" :alt="item.product_name" class="w-full h-full object-cover object-center">
                                </a>

                                <!-- Title, Color, Size, Price -->
                                <div class="space-y-1.5 flex-1">
                                    <a :href="'/products/' + item.product_slug" class="font-sans font-bold text-body sm:text-lg text-charcoal hover:underline" x-text="item.product_name"></a>
                                    
                                    <div class="flex items-center gap-2 text-body-sm text-iron">
                                        <span class="w-3.5 h-3.5 rounded-full border border-stone/40 inline-block" :style="'background-color: ' + item.color_hex"></span>
                                        <span x-text="item.color_name"></span>
                                        <span>•</span>
                                        <span>Ukuran <span x-text="item.size"></span> EU</span>
                                    </div>

                                    <div class="text-caption text-stone">
                                        SKU: <span x-text="item.sku"></span>
                                    </div>

                                    <div class="text-body-sm font-medium text-charcoal pt-1">
                                        Harga Satuan: <span x-text="item.price_formatted"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Controls: Stepper, Line Subtotal & Delete -->
                            <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-4 pt-2 sm:pt-0">
                                <div class="font-sans font-bold text-lg sm:text-xl text-charcoal" x-text="item.subtotal_formatted"></div>

                                <!-- Stepper -->
                                <div class="flex items-center border border-sand rounded-pill overflow-hidden bg-sand/20">
                                    <button type="button" 
                                            @click="$store.cart.updateQty(item.id, item.qty - 1)" 
                                            class="w-9 h-9 flex items-center justify-center text-charcoal hover:bg-sand transition text-sm font-bold min-w-[44px] min-h-[44px]"
                                            aria-label="Kurangi kuantitas">
                                        -
                                    </button>
                                    <span class="w-8 text-center text-body-sm font-bold text-charcoal" x-text="item.qty"></span>
                                    <button type="button" 
                                            @click="$store.cart.updateQty(item.id, item.qty + 1)" 
                                            :disabled="item.qty >= item.stock_quantity"
                                            :class="item.qty >= item.stock_quantity ? 'opacity-40 cursor-not-allowed' : 'hover:bg-sand'"
                                            class="w-9 h-9 flex items-center justify-center text-charcoal transition text-sm font-bold min-w-[44px] min-h-[44px]"
                                            aria-label="Tambah kuantitas">
                                        +
                                    </button>
                                </div>

                                <!-- Remove Button -->
                                <button type="button" 
                                        @click="$store.cart.removeItem(item.id)" 
                                        class="text-stone hover:text-charcoal text-caption uppercase tracking-wide10 font-bold underline flex items-center gap-1 min-h-[44px]">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Continue Shopping Button -->
                <div class="pt-2">
                    <a href="{{ route('home') }}" class="text-caption font-bold uppercase tracking-wide10 text-charcoal hover:underline inline-flex items-center gap-1.5">
                        ← Lanjutkan Belanja
                    </a>
                </div>
            </div>

            <!-- Right: Order Summary Sidebar (4 cols, sticky) -->
            <div class="lg:col-span-4 lg:sticky lg:top-28">
                <div class="bg-[#fcfbf9] border border-sand rounded-card p-6 space-y-6 shadow-xs">
                    <h2 class="font-sans font-bold text-xl text-charcoal border-b border-sand pb-4">
                        Ringkasan Pesanan
                    </h2>

                    <!-- Calculation breakdown -->
                    <div class="space-y-3 text-body-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-iron">Subtotal Produk</span>
                            <span class="font-bold text-charcoal" x-text="$store.cart.subtotal_formatted"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-iron">Estimasi Pengiriman</span>
                            <span class="text-charcoal font-medium">
                                <template x-if="$store.cart.is_free_shipping">
                                    <span class="text-green-700 font-bold">GRATIS</span>
                                </template>
                                <template x-if="!$store.cart.is_free_shipping">
                                    <span class="text-stone">Dihitung saat checkout</span>
                                </template>
                            </span>
                        </div>
                        <div class="border-t border-sand pt-3 flex items-center justify-between text-body">
                            <span class="font-bold text-charcoal">Total Sementara</span>
                            <span class="font-sans font-bold text-xl text-charcoal" x-text="$store.cart.subtotal_formatted"></span>
                        </div>
                    </div>

                    <!-- Checkout CTA -->
                    <div class="space-y-3 pt-2">
                        <a href="{{ route('checkout.index') }}" 
                           class="btn-pill-dark w-full py-4 text-center text-body-sm font-bold tracking-wide10 block min-h-[50px] shadow-sm">
                            Lanjut ke Checkout
                        </a>
                        
                        <p class="text-[11px] text-stone text-center">
                            Pembayaran aman didukung oleh Midtrans Snap & kurir Biteship.
                        </p>
                    </div>

                    <!-- Value propositions -->
                    <div class="border-t border-sand pt-4 space-y-2.5 text-caption text-iron">
                        <div class="flex items-center gap-2">
                            <span class="text-charcoal font-bold">✓</span>
                            <span>Gratis ongkir untuk pesanan di atas Rp 500.000</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-charcoal font-bold">✓</span>
                            <span>Garansi tukar ukuran 30 hari</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-charcoal font-bold">✓</span>
                            <span>100% Autentik 1-of-1 & Sudah Dicuci Higienis Siap Pakai</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>
@endsection

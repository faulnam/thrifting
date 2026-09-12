<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@hasSection('title')@yield('title') - FIFA Thrifting@else{{ $title ?? 'FIFA — Kurasi Vintage & Thrifting Otentik 1-of-1' }}@endif</title>
    <meta name="description" content="@hasSection('description')@yield('description')@else{{ $metaDescription ?? 'FIFA Thrifting — Kurasi busana vintage 1-of-1, jaket workwear Carhartt, band tees 90s, denim Levi 501, dan streetwear bersih higienis siap pakai.' }}@endif">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@hasSection('title')@yield('title') - FIFA Thrifting@else{{ $title ?? 'FIFA — Kurasi Vintage & Thrifting Otentik 1-of-1' }}@endif">
    <meta property="og:description" content="@hasSection('description')@yield('description')@else{{ $metaDescription ?? 'FIFA Thrifting — Kurasi busana vintage 1-of-1, jaket workwear Carhartt, band tees 90s, denim Levi 501, dan streetwear bersih higienis siap pakai.' }}@endif">
    <meta property="og:image" content="@yield('og_image', asset('images/home/hero-dasher.jpg'))">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@hasSection('title')@yield('title') - FIFA Thrifting@else{{ $title ?? 'FIFA — Kurasi Vintage & Thrifting Otentik 1-of-1' }}@endif">
    <meta name="twitter:description" content="@hasSection('description')@yield('description')@else{{ $metaDescription ?? 'FIFA Thrifting — Kurasi busana vintage 1-of-1, jaket workwear Carhartt, band tees 90s, denim Levi 501, dan streetwear bersih higienis siap pakai.' }}@endif">
    <meta name="twitter:image" content="@yield('og_image', asset('images/home/hero-dasher.jpg'))">

    <!-- Google Fonts: Dancing Script, Caveat, Inter & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Caveat:wght@700&family=Inter:wght@400;500;700&family=Playfair+Display:ital,wght@0,400;1,400&display=swap" rel="stylesheet">

    <!-- Stylesheet -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time() }}">
    @endif

    <!-- Alpine.js Global Cart Store & CDN -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                open: false,
                items: [],
                count: 0,
                subtotal: 0,
                subtotal_formatted: 'Rp 0',
                free_shipping_threshold: 500000,
                remaining_free_shipping: 500000,
                remaining_free_shipping_formatted: 'Rp 500.000',
                is_free_shipping: false,
                free_shipping_percent: 0,
                loading: false,
                error: null,

                init() {
                    this.fetchCart();
                },

                async fetchCart() {
                    try {
                        const res = await fetch('{{ route('cart.data') }}', { credentials: 'same-origin' });
                        if (res.ok) {
                            const data = await res.json();
                            this.updateData(data);
                        }
                    } catch (e) {
                        console.error('Cart fetch failed', e);
                    }
                },

                updateData(data) {
                    if (!data) return;
                    this.items = data.items || [];
                    this.count = data.total_qty || 0;
                    this.subtotal = data.subtotal || 0;
                    this.subtotal_formatted = data.subtotal_formatted || 'Rp 0';
                    this.free_shipping_threshold = data.free_shipping_threshold || 500000;
                    this.remaining_free_shipping = data.remaining_free_shipping || 0;
                    this.remaining_free_shipping_formatted = data.remaining_free_shipping_formatted || 'Rp 0';
                    this.is_free_shipping = data.is_free_shipping || false;
                    this.free_shipping_percent = data.free_shipping_percent || 0;
                },

                async addItem(variantId, qty = 1) {
                    this.loading = true;
                    this.error = null;
                    try {
                        const res = await fetch('{{ route('cart.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ product_variant_id: variantId, qty: qty })
                        });
                        const data = await res.json();
                        if (!res.ok) {
                            this.error = data.message || 'Gagal menambahkan produk ke keranjang.';
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: this.error, type: 'error' } }));
                        } else {
                            this.updateData(data.cart);
                            this.open = true; // Slide open cart drawer!
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Produk berhasil ditambahkan ke keranjang!', type: 'success' } }));
                        }
                    } catch (e) {
                        this.error = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: this.error, type: 'error' } }));
                    } finally {
                        this.loading = false;
                    }
                },

                async updateQty(itemId, newQty) {
                    this.loading = true;
                    this.error = null;
                    try {
                        const res = await fetch('/cart/items/' + itemId, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ qty: newQty })
                        });
                        const data = await res.json();
                        if (!res.ok) {
                            this.error = data.message || 'Gagal memperbarui kuantitas.';
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: this.error, type: 'error' } }));
                        } else {
                            this.updateData(data.cart);
                        }
                    } catch (e) {
                        this.error = 'Terjadi kesalahan.';
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: this.error, type: 'error' } }));
                    } finally {
                        this.loading = false;
                    }
                },

                async removeItem(itemId) {
                    this.loading = true;
                    try {
                        const res = await fetch('/cart/items/' + itemId, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            }
                        });
                        const data = await res.json();
                        if (res.ok) {
                            this.updateData(data.cart);
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Item dihapus dari keranjang', type: 'info' } }));
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                    }
                }
            });
        });
    </script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.14.8/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.14.8/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-canvas text-charcoal font-sans antialiased selection:bg-sand selection:text-charcoal" x-data="{ mobileMenuOpen: false, searchOpen: false }">

    <!-- Announcement Bar -->
    @include('partials.announcement-bar')

    @if (Auth::check() && Auth::user()->isDemo())
        <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-orange-600 text-white px-4 py-2 text-center text-caption font-medium shadow-xs">
            <div class="max-w-container mx-auto flex flex-wrap items-center justify-between gap-2">
                <span class="flex items-center gap-1.5 mx-auto sm:mx-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-200 animate-pulse"></span>
                    <span>Mode Demo Pelanggan (<strong>{{ Auth::user()->name }}</strong>). Data dan pesanan demo otomatis direset dalam 10 menit.</span>
                </span>
                <form action="{{ route('demo.reset') }}" method="POST" class="mx-auto sm:mx-0" onsubmit="return confirm('Reset semua data demo Anda?')">
                    @csrf
                    <button type="submit" class="bg-white/20 hover:bg-white text-white hover:text-charcoal px-2.5 py-0.5 rounded-full text-[10px] font-bold transition cursor-pointer">
                        Reset Data Demo
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Global Header -->
    @include('partials.header')

    <!-- Global Event-Driven & Session Toast Notifications -->
    <div x-data="{
        toasts: [],
        init() {
            @if (session('success'))
                this.addToast('{{ addslashes(session('success')) }}', 'success');
            @endif
            @if (session('warning'))
                this.addToast('{{ addslashes(session('warning')) }}', 'warning');
            @endif
            @if (session('error'))
                this.addToast('{{ addslashes(session('error')) }}', 'error');
            @endif
        },
        addToast(msg, type = 'success') {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message: msg, type });
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 3500);
        }
    }" 
    @toast.window="addToast($event.detail.message, $event.detail.type || 'success')"
    class="fixed bottom-24 right-6 z-50 flex flex-col gap-2 pointer-events-none max-w-sm w-full px-4 sm:px-0">
        <template x-for="t in toasts" :key="t.id">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="pointer-events-auto bg-charcoal text-canvas px-5 py-3.5 rounded-card shadow-xl text-body-sm flex items-center justify-between gap-3 border border-white/10">
                <div class="flex items-center gap-2.5">
                    <template x-if="t.type === 'success'">
                        <span class="text-canvas text-base">✓</span>
                    </template>
                    <template x-if="t.type === 'error'">
                        <span class="text-canvas text-base">✕</span>
                    </template>
                    <template x-if="t.type === 'warning'">
                        <span class="text-amber-300 text-base">!</span>
                    </template>
                    <span x-text="t.message" class="font-medium text-body-sm"></span>
                </div>
                <button @click="toasts = toasts.filter(item => item.id !== t.id)" class="text-stone hover:text-canvas p-1 min-w-[24px] min-h-[24px] flex items-center justify-center">
                    <span class="text-xs">✕</span>
                </button>
            </div>
        </template>
    </div>

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Slide-over Cart Drawer -->
    @include('partials.cart-drawer')

    <!-- Interactive AI Assistant Chatbot Widget -->
    @include('partials.chatbot')

    <!-- Global Footer -->
    @include('partials.footer')

    @stack('scripts')
</body>
</html>


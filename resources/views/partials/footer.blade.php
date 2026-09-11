<footer class="bg-[#1c1c1c] text-white border-t border-[#2d2d2d] mt-16 sm:mt-24 pt-14 pb-10" x-data="{
    openAccordion: {
        help: false,
        shop: false,
        company: false
    }
}">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Top Section: Newsletter + Follow Us (Left) & Nav Columns (Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 pb-12">
            
            <!-- Left Side: Subscribe + Follow the flock (4 cols on desktop) -->
            <div class="lg:col-span-4 flex flex-col justify-between space-y-10">
                <!-- Newsletter Subscription -->
                <div>
                    <span class="font-sans font-bold text-[11px] sm:text-[12px] uppercase tracking-widest text-[#dcdcdc] block mb-3">
                        BERLANGGANAN NEWSLETTER
                    </span>
                    
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="max-w-[360px]" x-data="{
                        email: '',
                        loading: false,
                        successMsg: '',
                        async submitForm() {
                            if (!this.email) return;
                            this.loading = true;
                            try {
                                const res = await fetch('{{ route('newsletter.subscribe') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ email: this.email })
                                });
                                const data = await res.json();
                                if (data.success) {
                                    this.successMsg = data.message;
                                    this.email = '';
                                }
                            } catch (e) {
                                console.error(e);
                            } finally {
                                this.loading = false;
                            }
                        }
                    }" @submit.prevent="submitForm">
                        @csrf
                        <div class="bg-white rounded-full p-1 pl-5 flex items-center justify-between shadow-xs focus-within:ring-2 focus-within:ring-white">
                            <input type="email" 
                                   name="email" 
                                   x-model="email"
                                   placeholder="Alamat Email Anda" 
                                   required 
                                   class="bg-transparent text-black placeholder:text-[#737373] text-[13px] focus:outline-none flex-grow pr-2 min-w-0">
                            <button type="submit" 
                                    :disabled="loading" 
                                    class="bg-transparent text-black hover:opacity-75 font-sans font-bold text-[11px] uppercase tracking-wider px-4 py-2 flex-shrink-0 transition cursor-pointer">
                                <span x-show="!loading">DAFTAR</span>
                                <span x-show="loading" style="display: none;">...</span>
                            </button>
                        </div>
                        <p x-show="successMsg" x-text="successMsg" class="text-caption text-green-400 font-medium mt-2" style="display: none;"></p>
                    </form>
                </div>

                <!-- FOLLOW THE FLOCK (Social Icons) -->
                <div>
                    <span class="font-sans font-bold text-[11px] sm:text-[12px] uppercase tracking-widest text-[#dcdcdc] block mb-3.5">
                        IKUTI KAMI
                    </span>
                    <div class="flex items-center space-x-3">
                        <!-- Instagram -->
                        <a href="https://instagram.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-full border border-white/50 flex items-center justify-center text-white hover:border-white hover:bg-white/10 transition" aria-label="Instagram">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                            </svg>
                        </a>
                        <!-- Pinterest -->
                        <a href="https://pinterest.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-full border border-white/50 flex items-center justify-center text-white hover:border-white hover:bg-white/10 transition" aria-label="Pinterest">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345-.09.375-.291 1.199-.334 1.379-.057.24-.19.291-.439.175-1.644-.766-2.673-3.171-2.673-5.105 0-4.156 3.019-7.974 8.709-7.974 4.572 0 8.125 3.259 8.125 7.612 0 4.543-2.864 8.2-6.839 8.2-1.336 0-2.592-.695-3.022-1.514l-.824 3.143c-.298 1.146-1.104 2.583-1.645 3.456C9.37 23.856 10.655 24 12 24c6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </a>
                        <!-- Facebook -->
                        <a href="https://facebook.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-full border border-white/50 flex items-center justify-center text-white hover:border-white hover:bg-white/10 transition" aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.374 14.5 5 15.5 5H18V0h-3.808C10.597 0 9 1.583 9 4.615V8z"/>
                            </svg>
                        </a>
                        <!-- X (Twitter) -->
                        <a href="https://x.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-full border border-white/50 flex items-center justify-center text-white hover:border-white hover:bg-white/10 transition" aria-label="X">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <!-- TikTok -->
                        <a href="https://tiktok.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-full border border-white/50 flex items-center justify-center text-white hover:border-white hover:bg-white/10 transition" aria-label="TikTok">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                            </svg>
                        </a>
                        <!-- YouTube -->
                        <a href="https://youtube.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-full border border-white/50 flex items-center justify-center text-white hover:border-white hover:bg-white/10 transition" aria-label="YouTube">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.377.55a3.016 3.016 0 0 0-2.122 2.136C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.55 9.376.55 9.376.55s7.505 0 9.377-.55a3.016 3.016 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Side: Navigation Columns (8 cols on desktop) -->
            <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Column 1: HELP -->
                <div class="border-t border-[#333333] pt-4 md:border-t-0 md:pt-0">
                    <button type="button" 
                            @click="openAccordion.help = !openAccordion.help"
                            class="w-full flex items-center justify-between md:justify-start font-sans font-bold text-[11px] sm:text-[12px] uppercase tracking-widest text-[#dcdcdc] mb-0 md:mb-4 min-h-[44px] md:min-h-0">
                        <span>BANTUAN</span>
                        <svg class="w-4 h-4 md:hidden transform transition-transform text-white/70" :class="openAccordion.help ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div :class="openAccordion.help ? 'block' : 'hidden md:block'" class="space-y-2 pt-2 md:pt-0">
                        <a href="mailto:support@fifa.co.id" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">support@fifa.co.id</a>
                        <a href="{{ route('pages.show', 'faq') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">FAQ & Autentisitas</a>
                        <a href="{{ route('pages.show', 'size-guide') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Panduan Ukuran PxL</a>
                        <a href="{{ route('pages.show', 'condition-guide') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Standar Grading Kondisi</a>
                        <a href="{{ route('pages.show', 'shipping-returns') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Pengiriman & Garansi</a>
                    </div>
                </div>

                <!-- Column 2: SHOP -->
                <div class="border-t border-[#333333] pt-4 md:border-t-0 md:pt-0">
                    <button type="button" 
                            @click="openAccordion.shop = !openAccordion.shop"
                            class="w-full flex items-center justify-between md:justify-start font-sans font-bold text-[11px] sm:text-[12px] uppercase tracking-widest text-[#dcdcdc] mb-0 md:mb-4 min-h-[44px] md:min-h-0">
                        <span>BELANJA VINTAGE</span>
                        <svg class="w-4 h-4 md:hidden transform transition-transform text-white/70" :class="openAccordion.shop ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div :class="openAccordion.shop ? 'block' : 'hidden md:block'" class="space-y-2 pt-2 md:pt-0">
                        <a href="{{ route('collections.show', 'new-arrivals') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Fresh Drops Terbaru</a>
                        <a href="{{ route('collections.show', 'best-sellers') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Paling Diburu (Vault)</a>
                        <a href="{{ route('categories.men') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Koleksi Pria</a>
                        <a href="{{ route('categories.women') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Koleksi Wanita</a>
                        <a href="{{ route('collections.show', 'vintage-90s') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">90s & Y2K Aesthetic</a>
                    </div>
                </div>

                <!-- Column 3: COMPANY (2 Sub-columns on desktop) -->
                <div class="border-t border-[#333333] pt-4 md:border-t-0 md:pt-0">
                    <button type="button" 
                            @click="openAccordion.company = !openAccordion.company"
                            class="w-full flex items-center justify-between md:justify-start font-sans font-bold text-[11px] sm:text-[12px] uppercase tracking-widest text-[#dcdcdc] mb-0 md:mb-4 min-h-[44px] md:min-h-0">
                        <span>TENTANG KAMI</span>
                        <svg class="w-4 h-4 md:hidden transform transition-transform text-white/70" :class="openAccordion.company ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div :class="openAccordion.company ? 'block' : 'hidden md:block'" class="pt-2 md:pt-0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2">
                            <!-- Left Sub-column -->
                            <div class="space-y-2">
                                <a href="{{ route('pages.show', 'our-story') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Kisah FIFA</a>
                                <a href="{{ route('pages.show', 'sustainability') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Slow Fashion & Daur Ulang</a>
                                <a href="{{ route('stores.index') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Gerai Fisik</a>
                                <a href="{{ route('pages.show', 'contact') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Hubungi Kami</a>
                            </div>
                            <!-- Right Sub-column -->
                            <div class="space-y-2 pt-2 sm:pt-0">
                                <a href="{{ route('blog.index') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Jurnal & Panduan Vintage</a>
                                <a href="{{ route('collections.sale') }}" class="block text-[12px] sm:text-[13px] text-[#cccccc] hover:text-white transition py-0.5">Steal Deals & Cuci Gudang</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Country / Region Selector -->
        <div class="pt-4 pb-4">
            <button type="button" class="inline-flex items-center gap-1.5 font-bold text-[12px] text-white uppercase tracking-wider hover:opacity-80 transition cursor-pointer">
                <span>ID (Indonesia)</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>

        <!-- Bottom Legal & Copyright Bar -->
        <div class="border-t border-[#333333] pt-5 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] text-[#8e8e8e]">
            <!-- Copyright Left -->
            <div>
                <span>© 2026 fifa Indonesia. Hak Cipta Dilindungi Undang-Undang.</span>
            </div>

            <!-- Legal Links Right -->
            <div class="flex flex-wrap items-center justify-center md:justify-end gap-x-6 gap-y-2">
                <a href="{{ route('pages.show', 'refund-policy') }}" class="hover:text-white transition">Kebijakan Pengembalian Dana</a>
                <a href="{{ route('pages.show', 'privacy-policy') }}" class="hover:text-white transition">Kebijakan Privasi</a>
                <a href="{{ route('pages.show', 'terms-of-service') }}" class="hover:text-white transition">Syarat & Ketentuan</a>
                <a href="{{ route('pages.show', 'do-not-sell') }}" class="hover:text-white transition">Jangan Jual Informasi Pribadi Saya</a>
                <a href="{{ route('pages.show', 'california-transparency') }}" class="hover:text-white transition">Transparansi Rantai Pasok</a>
            </div>
        </div>

    </div>
</footer>

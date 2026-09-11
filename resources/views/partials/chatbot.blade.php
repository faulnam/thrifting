<!-- Global Floating AI Chatbot Widget (fifa Assistant) - Minimalist Neutral Brand Theme -->
<div x-data="fifaChatbot()" 
     class="fixed bottom-6 right-6 z-50 select-none font-sans"
     @keydown.escape.window="open = false">
    
    <!-- Chat Window Container -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-250 transform"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         x-cloak
         class="w-[calc(100vw-32px)] sm:w-[380px] h-[530px] max-h-[82vh] bg-[#f9f8f6] rounded-[22px] shadow-xl border border-[#ded8cf] flex flex-col overflow-hidden mb-3">
        
        <!-- Minimalist Header (Solid Charcoal) -->
        <div class="bg-[#212121] text-white px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/10 border border-white/15 flex items-center justify-center font-display italic font-bold text-base text-white">
                    f
                </div>
                <div>
                    <h3 class="font-sans font-bold text-[13px] tracking-wider uppercase text-white leading-tight">
                        Asisten FIFA Thrift
                    </h3>
                    <p class="text-[11px] text-white/60 mt-0.5">
                        Kurasi Vintage & Panduan Ukuran
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-1.5">
                <button @click="resetChat()" 
                        title="Mulai Ulang Percakapan"
                        class="text-white/60 hover:text-white p-1.5 rounded-full hover:bg-white/10 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <button @click="open = false" 
                        class="text-white/60 hover:text-white p-1.5 rounded-full hover:bg-white/10 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages Track -->
        <div x-ref="messagesContainer" class="flex-1 p-4 overflow-y-auto space-y-3.5 no-scrollbar bg-[#f9f8f6]">
            <!-- Subtle Badge Header -->
            <div class="text-center my-1">
                <span class="inline-block bg-[#eae5dc] text-[#554e45] text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full">
                    Kurasi Vintage 1-of-1 FIFA
                </span>
            </div>

            <!-- Messages List -->
            <template x-for="(msg, index) in messages" :key="index">
                <div>
                    <!-- Bot Message -->
                    <template x-if="msg.sender === 'bot'">
                        <div class="flex items-start gap-2.5 max-w-[92%]">
                            <div class="w-7 h-7 rounded-full bg-[#212121] text-white flex-shrink-0 flex items-center justify-center text-[11px] font-display italic font-bold mt-0.5">
                                f
                            </div>
                            <div class="space-y-2">
                                <div class="bg-white border border-[#e5e0d8] p-3.5 rounded-2xl rounded-tl-xs text-[13px] text-[#212121] leading-relaxed shadow-xs">
                                    <p x-html="msg.text"></p>
                                </div>

                                <!-- Action Buttons / Links in Bot Message -->
                                <template x-if="msg.links && msg.links.length > 0">
                                    <div class="flex flex-wrap gap-1.5 pt-0.5">
                                        <template x-for="(link, lIdx) in msg.links" :key="lIdx">
                                            <a :href="link.url" 
                                               class="inline-flex items-center gap-1.5 bg-white hover:bg-[#212121] text-[#212121] hover:text-white border border-[#212121]/30 hover:border-[#212121] text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full transition duration-150">
                                                <span x-text="link.label"></span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- User Message -->
                    <template x-if="msg.sender === 'user'">
                        <div class="flex items-end justify-end">
                            <div class="bg-[#212121] text-white p-3.5 rounded-2xl rounded-tr-xs text-[13px] leading-relaxed max-w-[85%] shadow-xs">
                                <p x-text="msg.text"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Typing Indicator Animation -->
            <div x-show="isTyping" class="flex items-center gap-2.5 max-w-[80%]" style="display: none;">
                <div class="w-7 h-7 rounded-full bg-[#212121] text-white flex-shrink-0 flex items-center justify-center text-[11px] font-display italic font-bold">
                    f
                </div>
                <div class="bg-white border border-[#e5e0d8] px-4 py-3 rounded-2xl rounded-tl-xs flex items-center space-x-1.5 shadow-xs">
                    <span class="w-1.5 h-1.5 bg-[#8a8073] rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-[#8a8073] rounded-full animate-bounce [animation-delay:0.2s]"></span>
                    <span class="w-1.5 h-1.5 bg-[#8a8073] rounded-full animate-bounce [animation-delay:0.4s]"></span>
                </div>
            </div>
        </div>

        <!-- Quick Prompts Chips -->
        <div class="px-4 py-2 border-t border-[#ded8cf] bg-white overflow-x-auto no-scrollbar flex items-center gap-1.5">
            <template x-for="(prompt, pIdx) in quickPrompts" :key="pIdx">
                <button type="button"
                        @click="sendUserMessage(prompt.text)" 
                        class="flex-none bg-[#f2eee9] hover:bg-[#212121] text-[#333333] hover:text-white text-[11px] font-semibold px-3 py-1 rounded-full transition whitespace-nowrap cursor-pointer">
                    <span x-text="prompt.label"></span>
                </button>
            </template>
        </div>

        <!-- Input Bar (Clean Bordered Input) -->
        <div class="p-3.5 bg-white border-t border-[#ded8cf]">
            <form @submit.prevent="handleSubmit()" class="flex items-center gap-2">
                <input type="text" 
                       x-model="userInput" 
                       placeholder="Tanya seputar ukuran PxL, keaslian, kurasi..."
                       class="flex-1 bg-[#f9f8f6] border border-[#ded8cf] focus:border-[#212121] focus:bg-white text-[13px] text-[#212121] placeholder-[#8a8073] rounded-full px-4 py-2.5 focus:outline-none transition">
                
                <button type="submit" 
                        :disabled="!userInput.trim()"
                        :class="userInput.trim() ? 'bg-[#212121] text-white hover:bg-black cursor-pointer' : 'bg-[#e5e0d8] text-[#8a8073] cursor-not-allowed'"
                        class="w-9 h-9 rounded-full flex items-center justify-center transition flex-shrink-0"
                        aria-label="Kirim Pesan">
                    <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Floating Toggle Button with FIFA Icon -->
    <div class="relative flex items-center justify-end">
        <button type="button" 
                @click="open = !open" 
                class="w-14 h-14 rounded-full bg-[#212121] hover:bg-black text-white shadow-xl hover:shadow-2xl flex items-center justify-center transition-all duration-200 focus:outline-none cursor-pointer group hover:scale-105 active:scale-95"
                aria-label="Buka Chatbot Bantuan">
            <template x-if="!open">
                <div class="flex flex-col items-center justify-center">
                    <span class="font-display italic font-extrabold text-xl leading-none">f</span>
                    <span class="text-[8px] font-bold tracking-tighter uppercase mt-0.5">THRIFT</span>
                </div>
            </template>
            <template x-if="open">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </template>
        </button>
    </div>
</div>

<script>
function fifaChatbot() {
    return {
        open: false,
        userInput: '',
        isTyping: false,
        messages: [],
        quickPrompts: [
            { label: 'Item Paling Diburu', text: 'Rekomendasi item vintage paling langka dan diburu' },
            { label: 'Panduan Ukuran PxL', text: 'Bagaimana cara mengukur ukuran baju dan celana vintage?' },
            { label: 'Keaslian & Sanitasi', text: 'Apakah barang vintage dijamin asli dan sudah bersih?' },
            { label: 'Gratis Ongkir & Kurir', text: 'Berapa minimal belanja untuk gratis ongkir dan kurirnya?' },
            { label: 'Garansi Retur', text: 'Bagaimana ketentuan garansi jika ukuran tidak pas?' },
            { label: 'Koleksi Pria', text: 'Lihat koleksi jaket dan kaos vintage pria' },
            { label: 'Koleksi Wanita', text: 'Lihat koleksi vintage wanita' }
        ],

        init() {
            this.resetChat();
        },

        resetChat() {
            this.messages = [
                {
                    sender: 'bot',
                    text: 'Halo! Selamat datang di <strong>FIFA Thrifting</strong>.<br><br>Saya asisten virtual FIFA, siap membantu Anda seputar kurasi item vintage 1-of-1, panduan ukuran nyata (PxL), informasi keaslian tag/jahitan, status pengiriman, atau promo drop minggu ini. Ada yang bisa dibantu?',
                    links: [
                        { label: 'Drop Terbaru', url: '{{ route('collections.show', 'new-arrivals') }}' },
                        { label: 'Koleksi Pria', url: '{{ route('categories.men') }}' },
                        { label: 'Koleksi Wanita', url: '{{ route('categories.women') }}' },
                        { label: 'Paling Diburu', url: '{{ route('collections.show', 'best-sellers') }}' }
                    ]
                }
            ];
        },

        handleSubmit() {
            if (!this.userInput.trim()) return;
            const text = this.userInput;
            this.userInput = '';
            this.sendUserMessage(text);
        },

        sendUserMessage(text) {
            this.messages.push({
                sender: 'user',
                text: text
            });

            this.scrollBottom();
            this.isTyping = true;

            setTimeout(() => {
                const response = this.generateBotResponse(text);
                this.isTyping = false;
                this.messages.push(response);
                this.scrollBottom();
            }, 400);
        },

        generateBotResponse(input) {
            const q = input.toLowerCase();

            // 1. Rekomendasi / Terlaris / Rare
            if (q.includes('terlaris') || q.includes('rekomendasi') || q.includes('favorit') || q.includes('populer') || q.includes('best seller') || q.includes('diburu') || q.includes('grail') || q.includes('langka')) {
                return {
                    sender: 'bot',
                    text: 'Berikut adalah item vintage paling dicari di vault FIFA saat ini:<br><br>' +
                          '&bull; <strong>Carhartt Detroit J97 Tan</strong>: Jaket canvas duck legendaris pudar alami.<br>' +
                          '&bull; <strong>Nirvana 1993 In Utero</strong>: Kaos band single-stitch tag Giant USA.<br>' +
                          '&bull; <strong>Nike Center Swoosh Hoodie</strong>: Silver tag 90s boxy fit.<br>' +
                          '&bull; <strong>Levi\'s 501 USA 1994</strong>: Denim kaku kancing 553 stonewash.',
                    links: [
                        { label: 'Lihat Semua Item Diburu', url: '{{ route('collections.show', 'best-sellers') }}' },
                        { label: 'Fresh Drops Minggu Ini', url: '{{ route('collections.show', 'new-arrivals') }}' }
                    ]
                };
            }

            // 2. Ukuran / Sizing / PxL
            if (q.includes('ukuran') || q.includes('size') || q.includes('pxl') || q.includes('lebar') || q.includes('panjang') || q.includes('pas') || q.includes('fitting')) {
                return {
                    sender: 'bot',
                    text: '<strong>Panduan Pengukuran PxL FIFA:</strong><br><br>' +
                          '&bull; Karena potongan pakaian vintage bervariasi (boxy, relaxed, true 90s), kami selalu menyertakan ukuran nyata <strong>Panjang x Lebar (PxL)</strong> dalam cm di setiap produk.<br>' +
                          '&bull; <strong>Panjang (P):</strong> Diukur dari pundak atas ke ujung bawah.<br>' +
                          '&bull; <strong>Lebar (L):</strong> Diukur dari ketiak kiri ke ketiak kanan (pit-to-pit).',
                    links: [
                        { label: 'Panduan Ukuran Lengkap', url: '{{ route('pages.show', 'size-guide') }}' }
                    ]
                };
            }

            // 3. Keaslian & Kebersihan / Sanitasi
            if (q.includes('asli') || q.includes('ori') || q.includes('autentik') || q.includes('cuci') || q.includes('bersih') || q.includes('higienis') || q.includes('wangi') || q.includes('laundry')) {
                return {
                    sender: 'bot',
                    text: '<strong>Jaminan Keaslian & Kebersihan 100%:</strong><br><br>' +
                          '&bull; <strong>Kurasi Ahli:</strong> Semua produk diperiksa keaslian tag era, jahitan single-stitch, zipper YKK/Talon, dan kualitas kainnya.<br>' +
                          '&bull; <strong>Sanitasi Medis:</strong> Seluruh pakaian telah melewati proses pencucian deep clean, anti-bakteri, dan steam suhu tinggi. <em>100% wangi dan siap langsung pakai!</em>',
                    links: [
                        { label: 'Standar Grading Kondisi', url: '{{ route('pages.show', 'condition-guide') }}' },
                        { label: 'Kisah Kurasi Kami', url: '{{ route('pages.show', 'our-story') }}' }
                    ]
                };
            }

            // 4. Pengiriman & Ongkir
            if (q.includes('ongkir') || q.includes('kirim') || q.includes('pengiriman') || q.includes('gratis') || q.includes('ekspedisi') || q.includes('resi') || q.includes('biteship')) {
                return {
                    sender: 'bot',
                    text: '<strong>Informasi Pengiriman FIFA:</strong><br><br>' +
                          '&bull; <strong>Gratis Ongkir</strong> untuk setiap pesanan minimal <strong>Rp 500.000</strong> ke seluruh wilayah Indonesia.<br>' +
                          '&bull; Terintegrasi resmi dengan <strong>Biteship</strong> (JNE, SiCepat, J&T, GoSend, Grab).<br>' +
                          '&bull; Estimasi pengiriman reguler: 1-3 hari kerja dengan nomor resi otomatis tercatat di akun.',
                    links: [
                        { label: 'Keranjang Belanja', url: '{{ route('cart.index') }}' },
                        { label: 'Status Pesanan Saya', url: '{{ auth()->check() ? route('account.orders') : route('login') }}' }
                    ]
                };
            }

            // 5. Garansi / Retur
            if (q.includes('garansi') || q.includes('retur') || q.includes('kembali') || q.includes('tukar') || q.includes('uang')) {
                return {
                    sender: 'bot',
                    text: '<strong>Garansi Keaslian & Retur:</strong><br><br>' +
                          'Kami memberikan <strong>Garansi 100% Uang Kembali</strong> jika produk terbukti tidak asli atau terdapat cacat berat yang tidak dicantumkan pada deskripsi.',
                    links: [
                        { label: 'Kebijakan Pengembalian', url: '{{ route('pages.show', 'shipping-returns') }}' }
                    ]
                };
            }

            // 6. Koleksi Pria
            if (q.includes('pria') || q.includes('men') || q.includes('cowok')) {
                return {
                    sender: 'bot',
                    text: 'Koleksi pria mencakup jaket workwear Carhartt, kaos band single-stitch, celana denim Levi\'s 501 USA, cargo baggy, dan topi snapback 90s.',
                    links: [
                        { label: 'Semua Koleksi Pria', url: '{{ route('categories.men') }}' },
                        { label: 'Jaket & Workwear', url: '{{ route('collections.show', 'men-workwear-jackets') }}' }
                    ]
                };
            }

            // 7. Koleksi Wanita
            if (q.includes('wanita') || q.includes('women') || q.includes('cewek')) {
                return {
                    sender: 'bot',
                    text: 'Koleksi wanita menghadirkan oversized bomber, graphic baby tees Y2K, vintage knit sweater, high-waist mom jeans, dan tas retro.',
                    links: [
                        { label: 'Semua Koleksi Wanita', url: '{{ route('categories.women') }}' }
                    ]
                };
            }

            // 8. Lokasi Toko Fisik
            if (q.includes('toko') || q.includes('outlet') || q.includes('store') || q.includes('lokasi') || q.includes('offline') || q.includes('gerai')) {
                return {
                    sender: 'bot',
                    text: 'Kunjungi gerai fisik FIFA Vintage Vault di Senayan City Jakarta, M Bloc Space Melawai, PVJ Bandung, Tunjungan Plaza Surabaya, dan Canggu Bali.',
                    links: [
                        { label: 'Lokasi Toko Fisik', url: '{{ route('stores.index') }}' }
                    ]
                };
            }

            // Fallback default
            return {
                sender: 'bot',
                text: 'Saya dapat membantu Anda seputar kurasi vintage, rekomendasi jaket & kaos band, panduan ukuran PxL, gratis ongkir, dan jaminan keaslian FIFA. Silakan pilih menu di bawah atau tanyakan apapun!',
                links: [
                    { label: 'Fresh Drops Minggu Ini', url: '{{ route('collections.show', 'new-arrivals') }}' },
                    { label: 'Koleksi Pria', url: '{{ route('categories.men') }}' },
                    { label: 'Koleksi Wanita', url: '{{ route('categories.women') }}' },
                    { label: 'FAQ', url: '{{ route('pages.show', 'faq') }}' }
                ]
            };
        },

        scrollBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTo({
                        top: container.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            });
        }
    };
}
</script>

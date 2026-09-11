<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Coupon;
use App\Models\HeroSlide;
use App\Models\Page;
use App\Models\StoreLocation;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Static Pages
        $pages = [
            [
                'title' => 'Kisah FIFA (Our Story)',
                'slug' => 'our-story',
                'meta_title' => 'Tentang Kami — Kisah Kurasi Vintage & Thrifting FIFA',
                'meta_description' => 'FIFA hadir untuk mengubah cara kita berpakaian: menghargai sejarah, keunikan 1-of-1, dan mendukung gerakan slow fashion berkelanjutan.',
                'content' => '<p><strong>FIFA Thrifting</strong> lahir dari kecintaan mendalam terhadap sejarah kultur pakaian vintage, streetwear klasik, dan estetika abadi era 80-an hingga 2000-an. Kami percaya pakaian terbaik adalah pakaian yang memiliki jiwa, cerita, dan kualitas material yang tak tertandingi.</p><h2>Gerakan Slow Fashion & Keaslian</h2><p>Di era fast fashion yang membanjiri bumi dengan limbah tekstil murah, kami memilih jalan berbeda: mengkurasi satu per satu pakaian pre-loved berkualitas tinggi dari berbagai belahan dunia (USA, Jepang, Eropa).</p><p>Setiap item di FIFA telah melalui <strong>inspeksi keaslian 100%</strong>, grading kondisi yang transparan, serta proses pencucian higienis berstandar medis menggunakan steam anti-bakteri.</p>',
            ],
            [
                'title' => 'Keberlanjutan & Slow Fashion',
                'slug' => 'sustainability',
                'meta_title' => 'Slow Fashion & Zero Waste — FIFA Thrifting',
                'meta_description' => 'Bagaimana memilih pakaian thrift dapat menyelamatkan ribuan liter air dan mencegah berton-ton limbah tekstil dari tempat pembuangan sampah.',
                'content' => '<p>Industri fashion adalah salah satu penyumbang emisi karbon dan limbah terbesar di dunia. Dengan berbelanja di FIFA, Anda berpartisipasi aktif dalam ekonomi sirkular (circular economy).</p><h3>Dampak Positif yang Anda Ciptakan:</h3><ul><li><strong>Hemat Air:</strong> Satu kaos katun vintage menghemat hingga 2.700 liter air minum.</li><li><strong>Bebas Emisi Produksi Baru:</strong> Mencegah rata-rata 15 kg emisi CO2e dari pembentukan bahan sintetis baru.</li><li><strong>Zero Waste:</strong> Memperpanjang umur pakai busana berkualitas hingga puluhan tahun ke depan.</li></ul>',
            ],
            [
                'title' => 'Tanya Jawab & Bantuan (FAQ)',
                'slug' => 'faq',
                'meta_title' => 'Pusat Bantuan & FAQ — FIFA Thrifting Indonesia',
                'meta_description' => 'Pertanyaan yang sering diajukan mengenai keaslian barang vintage, kebersihan produk, pengukuran PxL, dan pengiriman.',
                'content' => '<h3>Apakah semua pakaian di FIFA sudah dicuci dan bersih?</h3><p><strong>Ya, 100% siap pakai!</strong> Setiap pakaian telah melewati proses sanitasi deep clean, cuci higienis anti-bakteri, dan steam suhu tinggi sehingga wangi dan steril saat Anda buka.</p><h3>Bagaimana memastikan keaslian (authenticity) produk vintage?</h3><p>Kurator kami berpengalaman lebih dari 8 tahun dalam memeriksa tag era (vintage tags), metode jahitan (single/double stitch), zipper YKK/Talon/Scovill, nomor pabrik kancing, dan tekstur kain.</p><h3>Bagaimana cara memastikan ukuran pas di badan?</h3><p>Karena ukuran vintage bisa berbeda dengan ukuran modern, kami selalu menyertakan ukuran nyata <strong>Panjang x Lebar (PxL)</strong> dalam sentimeter pada setiap deskripsi produk.</p><h3>Berapa lama pengiriman pesanan?</h3><p>Pesanan dikirim langsung melalui integrasi logistik <strong>Biteship</strong> dengan opsi Instant (2-3 jam), Regular (1-3 hari), atau Express ke seluruh wilayah Indonesia.</p>',
            ],
            [
                'title' => 'Pengiriman & Kebijakan Garansi',
                'slug' => 'shipping-returns',
                'meta_title' => 'Kebijakan Pengiriman & Garansi Keaslian — FIFA Thrifting',
                'meta_description' => 'Informasi kurir pengiriman terpercaya via Biteship dan garansi keaslian uang kembali 100%.',
                'content' => '<p>Kami bekerjasama dengan kurir terpercaya di Indonesia (JNE, SiCepat, J&T, GoSend, Grab) melalui platform logistik <strong>Biteship</strong>.</p><p>Semua produk vintage kami dilindungi <strong>Garansi 100% Uang Kembali</strong> jika barang terbukti tidak asli atau terdapat cacat berat yang tidak disebutkan di deskripsi.</p>',
            ],
            [
                'title' => 'Panduan Ukuran (Size & Measurement Guide)',
                'slug' => 'size-guide',
                'meta_title' => 'Panduan Pengukuran PxL Baju & Celana Vintage — FIFA',
                'meta_description' => 'Cara mengukur panjang dan lebar pakaian vintage favorit Anda agar pas saat berbelanja online.',
                'content' => '<p>Pakaian vintage memiliki potongan unik (misal: Boxy 90s, Relaxed Fit, Oversized). Kami merekomendasikan untuk mengukur baju favorit Anda di rumah menggunakan meteran kain:</p><ul><li><strong>Panjang (P):</strong> Diukur dari titik tertinggi pundak/kerah samping hingga ke ujung bawah pakaian.</li><li><strong>Lebar Dada (L):</strong> Diukur dari ketiak kiri ke ketiak kanan (pit-to-pit).</li><li><strong>Lingkar Pinggang (LP):</strong> Diukur mendatar pada ban pinggang celana dikalikan 2.</li></ul>',
            ],
            [
                'title' => 'Standar Grading Kondisi (Condition Guide)',
                'slug' => 'condition-guide',
                'meta_title' => 'Standar Grading Kondisi Pakaian Vintage — FIFA',
                'meta_description' => 'Penjelasan skala kondisi barang mulai dari 10/10 (Deadstock) hingga 9/10 (Vintage Patina).',
                'content' => '<p>FIFA menerapkan standar kurasi yang ketat dan transparan:</p><ul><li><strong>10/10 (Deadstock / Mint):</strong> Barang vintage belum pernah dipakai, masih terdapat tag asli toko (New Old Stock).</li><li><strong>9.5/10 (Excellent / Like New):</strong> Kondisi sangat mulus, warna pekat, sablon utuh, karet rib kencang tanpa cacat.</li><li><strong>9.0/10 (Great Vintage):</strong> Kondisi prima dengan efek pudar (fade/patina) alami khas vintage yang menambah nilai estetika.</li></ul>',
            ],
            [
                'title' => 'Hubungi Kami',
                'slug' => 'contact',
                'meta_title' => 'Hubungi Tim Layanan Pelanggan FIFA Thrifting',
                'meta_description' => 'Layanan bantuan customer service via WhatsApp dan Email.',
                'content' => '<p>Tim Customer Support kami siap membantu Anda setiap hari (Senin - Minggu, 09:00 - 21:00 WIB).</p><p>Email: <strong>support@fifa.co.id</strong><br>WhatsApp: <strong>0812-3456-7890</strong><br>Instagram: <strong>@fifa.thrift</strong></p>',
            ],
        ];

        foreach ($pages as $p) {
            Page::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 2. Store Locations
        $stores = [
            [
                'name' => 'FIFA Vintage Vault Flagship Senayan',
                'address' => 'Senayan City Mall Lt. 1 Unit 1-28, Jl. Asia Afrika Lot 19, Gelora, Tanah Abang',
                'city' => 'Jakarta Pusat',
                'phone' => '(021) 7278-1234',
                'opening_hours' => 'Setiap hari 10:00 - 22:00 WIB',
                'latitude' => -6.2271230,
                'longitude' => 106.7974560,
                'is_active' => true,
            ],
            [
                'name' => 'FIFA Blok M Space Thrift Hub',
                'address' => 'M Bloc Space Unit B-04, Jl. Panglima Polim No. 37, Melawai, Kebayoran Baru',
                'city' => 'Jakarta Selatan',
                'phone' => '(021) 2358-5678',
                'opening_hours' => 'Setiap hari 10:00 - 22:00 WIB',
                'latitude' => -6.2441230,
                'longitude' => 106.8014560,
                'is_active' => true,
            ],
            [
                'name' => 'FIFA Paris Van Java Bandung',
                'address' => 'Paris Van Java Mall Resort Level, Jl. Sukajadi No. 131-139, Cipedes',
                'city' => 'Kota Bandung',
                'phone' => '(022) 8206-3456',
                'opening_hours' => 'Setiap hari 10:00 - 22:00 WIB',
                'latitude' => -6.8891230,
                'longitude' => 107.5964560,
                'is_active' => true,
            ],
            [
                'name' => 'FIFA Tunjungan Plaza Surabaya',
                'address' => 'Tunjungan Plaza 6 Lt. 3, Jl. Embong Malang No. 21-31, Kedungdoro',
                'city' => 'Kota Surabaya',
                'phone' => '(031) 5345-6789',
                'opening_hours' => 'Setiap hari 10:00 - 22:00 WIB',
                'latitude' => -7.2621230,
                'longitude' => 112.7384560,
                'is_active' => true,
            ],
            [
                'name' => 'FIFA Canggu Vintage & Sunset Vault',
                'address' => 'Jl. Pantai Batu Bolong No. 58, Canggu, Kuta Utara',
                'city' => 'Bali',
                'phone' => '(0361) 8464-1234',
                'opening_hours' => 'Setiap hari 10:00 - 23:00 WITA',
                'latitude' => -8.7181230,
                'longitude' => 115.1694560,
                'is_active' => true,
            ],
        ];

        foreach ($stores as $s) {
            StoreLocation::updateOrCreate(['name' => $s['name']], $s);
        }

        // 3. Blog Posts
        $posts = [
            [
                'title' => 'Panduan Membedakan Kaos Vintage Asli 90s: Single Stitch & Tag Otentik',
                'slug' => 'panduan-membedakan-kaos-vintage-asli-single-stitch',
                'excerpt' => 'Pelajari rahasia membedakan kaos band vintage asli era 90-an dari jahitan single stitch, tag legendaris Giant/Brockum, hingga patina sablon.',
                'cover_image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=1200&q=80',
                'content' => '<p>Dalam dunia vintage clothing, kaos band era 90-an adalah salah satu barang koleksi dengan nilai investasi tinggi. Ciri khas paling utama adalah <strong>jahitan single stitch</strong> pada ujung lengan dan bagian bawah kaos yang diproduksi sebelum mesin jahit jarum ganda modern populer di akhir dekade 90-an.</p><h2>Pentingnya Mengenali Tag Kerah</h2><p>Tag kerah seperti Giant by Anvil, Brockum Worldwide, Tultex, dan Screen Stars adalah bukti autentisitas era produksi. Kaos vintage asli memiliki bobot kain katun combed lembut yang menyerap keringat dengan sangat baik.</p>',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Kenapa Jaket Workwear Carhartt Detroit Menjadi Incaran Pecinta Fashion Dunia',
                'slug' => 'kenapa-jaket-carhartt-detroit-jadi-incaran-fashion-dunia',
                'excerpt' => 'Dari pakaian pekerja tambang Amerika Serikat hingga runway mode Paris: kisah di balik daya tarik jaket kanvas Carhartt Detroit.',
                'cover_image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=1200&q=80',
                'content' => '<p>Dibuat pertama kali pada tahun 1954, jaket Carhartt Detroit J97 dirancang untuk menghadapi medan kerja berat berkat kain katun duck canvas 12oz yang kokoh dan kerah corduroy yang nyaman di leher.</p><p>Kini, pudar alami dan patina aus dari pemakaian bertahun-tahun justru menjadikannya karya seni fungsional yang sangat bergengsi di kultur streetwear global.</p>',
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Tips Merawat Celana Denim Vintage Levi\'s 501 Agar Awet Seumur Hidup',
                'slug' => 'tips-merawat-denim-vintage-levis-501',
                'excerpt' => 'Langkah mencuci dan merawat denim katun kaku non-stretch agar karakter fading warnanya tetap tajam dan kain tidak mudah rapuh.',
                'cover_image' => 'https://images.unsplash.com/photo-1542272604-780c96856592?w=1200&q=80',
                'content' => '<p>Jangan terlalu sering mencuci denim vintage Anda. Cukup gantung dan angin-anginkan setelah dipakai. Jika perlu dicuci, balik celana (inside-out) dan gunakan air dingin dengan deterjen lembut tanpa pemutih.</p>',
                'is_published' => true,
                'published_at' => now()->subDay(),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(['slug' => $post['slug']], $post);
        }

        // 4. Hero Slides
        $slides = [
            [
                'page' => 'home',
                'title' => 'Kurasi Vintage 1 of 1. Gaya Otentik Berkarakter.',
                'subtitle' => 'Koleksi jaket workwear, band tees 90s, denim klasik, dan streetwear terkurasi.',
                'cta_text' => 'Koleksi Pria',
                'cta_link' => '/men',
                'image' => '/images/home/hero-dasher.jpg',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'page' => 'home',
                'title' => 'Slow Fashion, Kualitas Abadi.',
                'subtitle' => 'Pakaian pre-loved bersih, higienis, dan terverifikasi 100% keasliannya.',
                'cta_text' => 'Koleksi Wanita',
                'cta_link' => '/women',
                'image' => '/images/home/hero-dasher.jpg',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'page' => 'men',
                'title' => 'Koleksi Vintage & Streetwear Pria',
                'subtitle' => 'Jaket workwear, kaos band langka, jeans Levi\'s 501 USA, dan topi snapback.',
                'cta_text' => 'Lihat Semua Pria',
                'cta_link' => '/men',
                'image' => '/images/home/hero-dasher.jpg',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'page' => 'women',
                'title' => 'Koleksi Vintage & Retro Wanita',
                'subtitle' => 'Oversized bomber, graphic baby tees 90s, mom jeans, dan knitwear retro.',
                'cta_text' => 'Lihat Semua Wanita',
                'cta_link' => '/women',
                'image' => '/images/home/hero-dasher.jpg',
                'order' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::updateOrCreate([
                'page' => $slide['page'],
                'order' => $slide['order'],
            ], $slide);
        }

        // 5. Coupons
        $coupons = [
            [
                'code' => 'THRIFT10',
                'type' => 'percent',
                'value' => 10,
                'min_purchase' => 250000,
                'max_discount' => 100000,
                'starts_at' => now()->subDays(10),
                'expires_at' => now()->addMonths(6),
                'usage_limit' => 1000,
                'used_count' => 8,
                'is_active' => true,
            ],
            [
                'code' => 'VINTAGE50K',
                'type' => 'fixed',
                'value' => 50000,
                'min_purchase' => 450000,
                'max_discount' => null,
                'starts_at' => now()->subDays(5),
                'expires_at' => now()->addMonths(3),
                'usage_limit' => 500,
                'used_count' => 15,
                'is_active' => true,
            ],
            [
                'code' => 'FRESHDROP',
                'type' => 'percent',
                'value' => 15,
                'min_purchase' => 800000,
                'max_discount' => 200000,
                'starts_at' => now()->subDays(1),
                'expires_at' => now()->addMonths(1),
                'usage_limit' => 200,
                'used_count' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $c) {
            Coupon::updateOrCreate(['code' => $c['code']], $c);
        }
    }
}

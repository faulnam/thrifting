<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Fetch Categories
        $menWorkwear = Category::where('slug', 'men-workwear-jackets')->first() ?? Category::where('slug', 'men-jackets-outerwear')->first();
        $menVarsity = Category::where('slug', 'men-varsity-bomber')->first() ?? Category::where('slug', 'men-jackets-outerwear')->first();
        $menHoodies = Category::where('slug', 'men-sweats-hoodies')->first() ?? Category::where('slug', 'men-jackets-outerwear')->first();
        $menLeather = Category::where('slug', 'men-leather-jackets')->first() ?? Category::where('slug', 'men-jackets-outerwear')->first();
        
        $menBandTees = Category::where('slug', 'men-vintage-band-tees')->first() ?? Category::where('slug', 'men-tees-tops')->first();
        $menGraphicTees = Category::where('slug', 'men-graphic-tees')->first() ?? Category::where('slug', 'men-tees-tops')->first();
        $menFlannel = Category::where('slug', 'men-flannel-shirts')->first() ?? Category::where('slug', 'men-tees-tops')->first();
        
        $menDenim = Category::where('slug', 'men-vintage-denim')->first() ?? Category::where('slug', 'men-pants-bottoms')->first();
        $menCargo = Category::where('slug', 'men-cargo-pants')->first() ?? Category::where('slug', 'men-pants-bottoms')->first();
        $menCorduroy = Category::where('slug', 'men-corduroy-pants')->first() ?? Category::where('slug', 'men-pants-bottoms')->first();
        $menWorkPants = Category::where('slug', 'men-work-pants')->first() ?? Category::where('slug', 'men-pants-bottoms')->first();
        
        $menSnapback = Category::where('slug', 'men-vintage-snapback')->first() ?? Category::where('slug', 'men-hats-caps')->first();
        $menBeanie = Category::where('slug', 'men-knit-beanie')->first() ?? Category::where('slug', 'men-hats-caps')->first();
        
        $menSneakers = Category::where('slug', 'men-everyday-sneakers')->first() ?? Category::where('slug', 'men-shoes')->first();
        $menLoafers = Category::where('slug', 'men-slip-ons-loungers')->first() ?? Category::where('slug', 'men-shoes')->first();
        
        $menBags = Category::where('slug', 'men-crossbody-bags')->first() ?? Category::where('slug', 'men-bags-accessories')->first();
        $menSun = Category::where('slug', 'men-retro-sunglasses')->first() ?? Category::where('slug', 'men-bags-accessories')->first();

        $womenBomber = Category::where('slug', 'women-oversized-bomber')->first() ?? Category::where('slug', 'women-jackets-outerwear')->first();
        $womenDenimJkt = Category::where('slug', 'women-denim-jackets')->first() ?? Category::where('slug', 'women-jackets-outerwear')->first();
        $womenBabyTees = Category::where('slug', 'women-baby-tees')->first() ?? Category::where('slug', 'women-tees-tops')->first();
        $womenOversizedTees = Category::where('slug', 'women-oversized-tees')->first() ?? Category::where('slug', 'women-tees-tops')->first();
        $womenMomJeans = Category::where('slug', 'women-high-waist-denim')->first() ?? Category::where('slug', 'women-pants-bottoms')->first();
        $womenCargoSkirt = Category::where('slug', 'women-cargo-skirts')->first() ?? Category::where('slug', 'women-pants-bottoms')->first();
        $womenCaps = Category::where('slug', 'women-vintage-caps')->first() ?? Category::where('slug', 'women-hats-caps')->first();
        $womenLoafers = Category::where('slug', 'women-flats-loungers')->first() ?? Category::where('slug', 'women-shoes')->first();
        $womenTotes = Category::where('slug', 'women-vintage-totes')->first() ?? Category::where('slug', 'women-bags-accessories')->first();

        // 2. Fetch Collections
        $newArrivalsCol = Collection::where('slug', 'new-arrivals')->first();
        $bestSellersCol = Collection::where('slug', 'best-sellers')->first();
        $saleCol = Collection::where('slug', 'sale')->first();
        $vintage90sCol = Collection::where('slug', 'vintage-90s')->first();
        $workwearCol = Collection::where('slug', 'workwear')->first();

        // 3. Extensive Thrift Products Dataset
        $productsData = [
            // ==========================================
            // JAKET & OUTERWEAR
            // ==========================================
            [
                'category_id' => $menWorkwear?->id ?? 1,
                'name' => "Jaket Vintage Carhartt Detroit Canvas J97 Faded Tan",
                'slug' => 'vintage-carhartt-detroit-j97-tan',
                'short_description' => 'Kondisi 9.5/10. Tag Carhartt Made in USA 90s. Heavyweight duck canvas dengan kerah corduroy cokelat.',
                'description' => '<p>Item grail legendaris! Jaket Carhartt Detroit J97 vintage era 90-an dengan pudar alami (faded patina) yang sangat otentik. Menggunakan material heavyweight duck canvas 12oz dengan lapisan blanket lining bermotif Aztec di bagian dalam untuk kehangatan maksimal.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Carhartt Crafted with Pride in USA<br>&bull; Kondisi: 9.5/10 (Sangat terawat, zipper lancar YKK kuningan)<br>&bull; Ukuran: Size L (Panjang 69 cm x Lebar Dada 64 cm)<br>&bull; Sanitasi: Sudah melalui proses dry cleaning & steam higienis siap pakai.</p>',
                'material_info' => '100% Ring-Spun Cotton Duck Canvas (12oz) dengan kerah 100% katun corduroy dan resleting full brass vintage.',
                'sustainability_note' => 'Slow Fashion: Mengurangi emisi karbon 18.4 kg CO2e dan menghemat 4.500L air dibanding produksi jaket kanvas baru.',
                'base_price' => 1850000,
                'compare_at_price' => 2400000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 1100,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id, $workwearCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Faded Tan Ochre',
                        'color_hex' => '#c29b61',
                        'sizes' => ['L (PxL 69x64)' => 1],
                    ],
                    [
                        'color_name' => 'Washed Onyx Black',
                        'color_hex' => '#2b2927',
                        'sizes' => ['XL (PxL 72x68)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-carhartt-tan.png', 'order' => 1, 'is_primary' => true],
                    ['url' => '/images/products/vintage-carhartt-black.png', 'order' => 2, 'is_primary' => false],
                ],
            ],
            [
                'category_id' => $menHoodies?->id ?? 1,
                'name' => "Hoodie Vintage 90s Nike Center Mini Swoosh Embroidered",
                'slug' => 'vintage-nike-center-swoosh-hoodie',
                'short_description' => 'Kondisi 9.5/10. Tag Nike Silver Tag era 1996. Bordir logo Nike di tengah dada, bahan fleece tebal lembut.',
                'description' => '<p>Salah satu siluet vintage Nike paling diburu di dunia streetwear. Model center embroidered swoosh dengan potongan boxy fit khas 90-an. Karet rib di pinggang dan lengan masih sangat kencang dan tebal.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Nike Silver Tag Made in USA<br>&bull; Kondisi: 9.5/10 (No minus, no hole, no stain)<br>&bull; Ukuran: Size XL (Panjang 72 cm x Lebar 66 cm)<br>&bull; 1 of 1 Authentic curated piece.</p>',
                'material_info' => '80% Premium Heavy Cotton Fleece / 20% Polyester. Bahan tebal 400 GSM.',
                'sustainability_note' => 'Zero Textile Waste: Menjaga pakaian berkualitas tinggi tetap berputar tanpa limbah ke tempat pembuangan akhir.',
                'base_price' => 1250000,
                'compare_at_price' => 1650000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 850,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Heather Grey',
                        'color_hex' => '#a8a8a8',
                        'sizes' => ['XL (PxL 72x66)' => 1],
                    ],
                    [
                        'color_name' => 'Faded Charcoal Black',
                        'color_hex' => '#222222',
                        'sizes' => ['L (PxL 70x63)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-nike-hoodie-grey.png', 'order' => 1, 'is_primary' => true],
                    ['url' => '/images/products/vintage-nike-hoodie-black.png', 'order' => 2, 'is_primary' => false],
                ],
            ],
            [
                'category_id' => $menLeather?->id ?? 1,
                'name' => "Jaket Vintage Leather Racing Moto Biker 90s Multi-Patch",
                'slug' => 'vintage-racing-jacket-leather',
                'short_description' => 'Kondisi 9/10. Kulit sapi asli (genuine cowhide) dengan detail bordir patch balap retro dan zipper YKK.',
                'description' => '<p>Jaket motor balap vintage era 90-an dengan konstruksi kulit asli premium bertekstur tebal. Dihiasi patch sponsor balap klasik, padding bahu berkarakter, dan kancing snap leher khas pembalap sirkuit.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Material: 100% Genuine Heavy Cowhide Leather<br>&bull; Kondisi: 9/10 (Patina kulit alami sangat gagah, zipper lancar)<br>&bull; Ukuran: Size L (Panjang 66 cm x Lebar 58 cm x Panjang Lengan 62 cm).</p>',
                'material_info' => '100% Kulit Sapi Asli (Genuine Leather) dengan furing satin berlapis dakron tipis.',
                'sustainability_note' => 'Circular Vintage: Menghidupkan kembali karya kerajinan kulit asli legendaris yang tahan hingga puluhan tahun.',
                'base_price' => 2450000,
                'compare_at_price' => 3500000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 1600,
                'collections' => array_filter([$bestSellersCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Vintage Racing Tri-Tone (Black/Red/White)',
                        'color_hex' => '#9e2a2b',
                        'sizes' => ['L (PxL 66x58)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-racing-jacket-leather.png', 'order' => 1, 'is_primary' => true],
                ],
            ],
            [
                'category_id' => $menVarsity?->id ?? 1,
                'name' => "Jaket Vintage 90s Forest Green Wool & Leather Varsity Letterman",
                'slug' => 'vintage-varsity-jacket-green',
                'short_description' => 'Kondisi 9.5/10. Bodi wol tebal dengan lengan kulit asli krem dan chenille patch bordir tim rugby 1994.',
                'description' => '<p>Varsity letterman jacket otentik tahun 1994 buatan Amerika Serikat. Memadukan bodi wol Melton warna hijau botol dengan lengan kulit asli yang lentur. Sangat hangat, berbobot, dan memberikan aura Ivy League / American College vintage.</p>',
                'material_info' => 'Bodi: 80% Melton Wool / 20% Nylon. Lengan: 100% Genuine Leather. Furing: Quilted Satin.',
                'sustainability_note' => 'Mencegah pembuangan serat wol murni ke limbah lingkungan.',
                'base_price' => 1950000,
                'compare_at_price' => 2700000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 1400,
                'collections' => array_filter([$newArrivalsCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Forest Green & Cream Leather',
                        'color_hex' => '#2d4a3e',
                        'sizes' => ['XL (PxL 71x65)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-varsity-jacket-green.png', 'order' => 1, 'is_primary' => true],
                ],
            ],
            [
                'category_id' => $menWorkwear?->id ?? 1,
                'name' => "Jaket Vintage Ralph Lauren Harrington Chino Windbreaker",
                'slug' => 'vintage-ralph-harrington-beige',
                'short_description' => 'Kondisi 9.5/10. Tag Polo Ralph Lauren era 90s, furing tartan plaid klasik dengan bordir Pony logo di dada.',
                'description' => '<p>Jaket Harrington klasik paling ikonik dari Ralph Lauren. Potongan relaxed fit dengan bahan katun twill chino tahan angin, kerah double button, saku samping berpenutup, dan furing bermotif tartan khas Polo.</p>',
                'material_info' => '100% Cotton Chino Twill dengan furing 100% Cotton Tartan Plaid.',
                'sustainability_note' => 'Vintage Timeless: Desain abadi yang tidak pernah ketinggalan zaman.',
                'base_price' => 950000,
                'compare_at_price' => 1800000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 700,
                'collections' => array_filter([$newArrivalsCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Classic Khaki Beige',
                        'color_hex' => '#d4be9c',
                        'sizes' => ['L (PxL 68x62)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-ralph-harrington-beige.png', 'order' => 1, 'is_primary' => true],
                ],
            ],
            [
                'category_id' => $menWorkwear?->id ?? 1,
                'name' => "Jaket Vintage Patagonia Retro-X Deep Pile Fleece Zip",
                'slug' => 'vintage-patagonia-fleece-cream',
                'short_description' => 'Kondisi 9.5/10. Bulu fleece tebal shaggy dengan kantong dada nilon kontras biru tua, made in USA.',
                'description' => '<p>Jaket outdoor vintage paling diburu dari Patagonia. Dibuat dengan konstruksi deep pile fleece penahan angin berteknologi windproof membrane. Sangat hangat, stylish untuk gorpcore maupun streetwear harian.</p>',
                'material_info' => '100% Recycled Polyester Deep-Pile Sherpa Fleece (6mm pile) dengan nilon pocket.',
                'sustainability_note' => 'Patagonia Heritage: Pelopor keberlanjutan daur ulang tekstil sejak era 90-an.',
                'base_price' => 1650000,
                'compare_at_price' => 2400000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 750,
                'collections' => array_filter([$bestSellersCol?->id, $newArrivalsCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Natural Cream & Navy Pocket',
                        'color_hex' => '#ede6d8',
                        'sizes' => ['M (PxL 67x57)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-patagonia-fleece-cream.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // ==========================================
            // BAJU, KAOS & KEMEJA (BAND TEES & SHIRTS)
            // ==========================================
            [
                'category_id' => $menBandTees?->id ?? 2,
                'name' => "Kaos Vintage 1993 Nirvana In Utero Single Stitch Band Tee",
                'slug' => 'vintage-nirvana-in-utero-tee-1993',
                'short_description' => 'Kondisi 9/10. Tag Giant by Anvil Made in USA 1993. Jahitan Single Stitch atas bawah, pudar abu tua alami.',
                'description' => '<p>Holy grail t-shirt vintage rock dunia! Kaos original tur Nirvana In Utero tahun 1993 berlisensi resmi Nirvana Under License to Brockum. Jahitan single stitch utuh pada bagian lengan dan ujung bawah kaos. Sablon crackle alami yang sangat estetik tanpa bolong.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Giant by Anvil Made in USA (100% Cotton Pre-Shrunk)<br>&bull; Stitching: Single Stitch Sleeve & Hem<br>&bull; Ukuran: Size L fit XL (Panjang 74 cm x Lebar 58 cm)<br>&bull; Koleksi kurasi super rare.</p>',
                'material_info' => '100% Heavyweight Cotton Single-Stitch Konstruksi Vintage 90s.',
                'sustainability_note' => 'Koleksi seni busana bersejarah berumur lebih dari 30 tahun yang nilainya terus meningkat.',
                'base_price' => 2250000,
                'compare_at_price' => 3200000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 300,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Faded Vintage Charcoal Black',
                        'color_hex' => '#363434',
                        'sizes' => ['L (PxL 74x58)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-band-tee-nirvana.png', 'order' => 1, 'is_primary' => true],
                    ['url' => '/images/products/vintage-band-tee-metallica.png', 'order' => 2, 'is_primary' => false],
                ],
            ],
            [
                'category_id' => $menBandTees?->id ?? 2,
                'name' => "Kaos Vintage 1991 Metallica Pushead Damage Inc Band Tee",
                'slug' => 'vintage-metallica-damage-inc-tee',
                'short_description' => 'Kondisi 9.5/10. Tag Brockum Worldwide Made in USA. Jahitan Single Stitch, sablon artwork Pushead super detail.',
                'description' => '<p>Kaos vintage original rilisan tur Metallica era 1991 dengan ilustrasi karya seniman legendaris Brian Pushead Schroeder. Bahan katun vintage tebal berbulu halus khas 90-an dengan pudar warna sun-faded merata.</p>',
                'material_info' => '100% Combed Cotton Single Stitch 90s.',
                'sustainability_note' => 'Menghemat ribuan liter air dibanding membeli kaos grafis fast fashion modern.',
                'base_price' => 1850000,
                'compare_at_price' => 2500000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 300,
                'collections' => array_filter([$vintage90sCol?->id, $newArrivalsCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Sun Faded Black',
                        'color_hex' => '#282726',
                        'sizes' => ['XL (PxL 76x60)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-band-tee-metallica.png', 'order' => 1, 'is_primary' => true],
                ],
            ],
            [
                'category_id' => $menGraphicTees?->id ?? 2,
                'name' => "Kaos Vintage Harley Davidson 3D Emblem Eagle 90s Graphic Tee",
                'slug' => 'vintage-harley-davidson-3d-emblem-tee',
                'short_description' => 'Kondisi 9.5/10. Tag Holoubek / Harley Davidson Made in USA. Grafis elang 3D emblem gagah, pudar washed black.',
                'description' => '<p>Kaos Harley Davidson legendaris dengan cetakan grafis 3D Emblem Fort Worth Texas. Efek pudar washed black alami dengan fitting boxy santai yang sangat disukai para pecinta vintage biker look.</p>',
                'material_info' => '100% Pre-Shrunk Heavy Cotton.',
                'sustainability_note' => 'Autentik 100% tanpa bahan sintetis plastik mikro.',
                'base_price' => 1100000,
                'compare_at_price' => 1500000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 320,
                'collections' => array_filter([$bestSellersCol?->id, $workwearCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Washed Acid Black',
                        'color_hex' => '#383634',
                        'sizes' => ['L (PxL 71x57)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-harley-tee-eagle.png', 'order' => 1, 'is_primary' => true],
                ],
            ],
            [
                'category_id' => $menGraphicTees?->id ?? 2,
                'name' => "Kaos Vintage Stussy 8-Ball World Tour Heavyweight Streetwear Tee",
                'slug' => 'vintage-stussy-8ball-world-tour-tee',
                'short_description' => 'Kondisi 9.5/10. Tag Stussy Made in USA era awal 2000-an. Grafis bola 8 ikonik di punggung dan dada kiri.',
                'description' => '<p>Kaos grafis streetwear paling ikonik dari Shawn Stussy. Menampilkan artwork bola 8 legendaris dengan daftar kota dunia (London, Paris, Los Angeles, New York, Tokyo). Kain tebal kokoh dengan kerah rib lebar.</p>',
                'material_info' => '100% Heavyweight Cotton 220 GSM.',
                'sustainability_note' => 'Pre-loved authentic piece.',
                'base_price' => 850000,
                'compare_at_price' => 1200000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 310,
                'collections' => array_filter([$bestSellersCol?->id, $newArrivalsCol?->id, $workwearCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Off-White Cream',
                        'color_hex' => '#f2ece1',
                        'sizes' => ['L (PxL 72x56)' => 1],
                    ],
                    [
                        'color_name' => 'Pitch Black',
                        'color_hex' => '#1a1a1a',
                        'sizes' => ['XL (PxL 75x61)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-stussy-8ball-white.png', 'order' => 1, 'is_primary' => true],
                    ['url' => '/images/products/vintage-stussy-8ball-black.png', 'order' => 2, 'is_primary' => false],
                ],
            ],
            [
                'category_id' => $menFlannel?->id ?? 2,
                'name' => "Kemeja Vintage Heavy Flannel Plaid Overshirt Green/Navy",
                'slug' => 'vintage-flannel-shirt-green-navy',
                'short_description' => 'Kondisi 9.5/10. Tag Five Brother / Big Mac Made in USA. Katun flannel tebal berbulu lembut, kancing mutiara.',
                'description' => '<p>Kemeja flannel tebal vintage era 80-90an dari brand workwear Amerika Serikat. Jahitan double needle super kuat dengan dua kantong dada berkancing. Sangat cocok dipakai sebagai luaran (overshirt) dengan kaos polos di dalam.</p>',
                'material_info' => '100% Heavy Brushed Cotton Flannel.',
                'sustainability_note' => 'Daya tahan bahan katun murni yang awet hingga puluhan tahun.',
                'base_price' => 480000,
                'compare_at_price' => 750000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 600,
                'collections' => array_filter([$newArrivalsCol?->id, $saleCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Forest Hunter Plaid',
                        'color_hex' => '#2e473b',
                        'sizes' => ['L (PxL 73x58)' => 1],
                    ],
                    [
                        'color_name' => 'Rustic Red & Black Check',
                        'color_hex' => '#8b263e',
                        'sizes' => ['M (PxL 70x54)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-flannel-shirt-green.png', 'order' => 1, 'is_primary' => true],
                    ['url' => '/images/products/vintage-flannel-shirt-red.png', 'order' => 2, 'is_primary' => false],
                ],
            ],

            // ==========================================
            // CELANA & DENIM (JEANS, CARGO, CORDUROY)
            // ==========================================
            [
                'category_id' => $menDenim?->id ?? 3,
                'name' => "Celana Jeans Vintage 90s Levi's 501 Made in USA Light Wash",
                'slug' => 'vintage-levis-501-usa-light-wash',
                'short_description' => 'Kondisi 9.5/10. Tag Red Tab Batwing Made in USA 1994, Button Fly 553, pudar kumis (whiskers) alami.',
                'description' => '<p>Celana jeans paling ikonik dalam sejarah busana dunia: Levi\'s 501 original buatan Amerika Serikat pabrik nomor 553 tahun 1994. Menggunakan denim 100% katun kaku non-stretch yang menghasilkan potongan lurus klasik sempurna.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Care tag putih Levi Strauss & Co San Francisco Made in USA<br>&bull; Kancing: 5-Button Fly stamp 553<br>&bull; Ukuran di Tag: W32 L32 (Lingkar Pinggang 82 cm x Panjang 104 cm x Leg Opening 20 cm)<br>&bull; Kondisi: 9.5/10 (Hem rapi, selangkangan aman no sobek).</p>',
                'material_info' => '100% Rigid Heavy Cotton Denim (14.5oz Cone Mills Denim).',
                'sustainability_note' => 'Menghemat 10.000 liter air yang biasanya dihabiskan untuk pewarnaan celana denim baru.',
                'base_price' => 1150000,
                'compare_at_price' => 1650000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 850,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Vintage Light Stonewash',
                        'color_hex' => '#7b9bb6',
                        'sizes' => ['W32 L32 (LP 82cm)' => 1],
                    ],
                    [
                        'color_name' => 'Medium Indigo Wash',
                        'color_hex' => '#415e78',
                        'sizes' => ['W34 L32 (LP 86cm)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-levis-501-stonewash.png', 'order' => 1, 'is_primary' => true],
                    ['url' => '/images/products/vintage-levis-501-light.png', 'order' => 2, 'is_primary' => false],
                ],
            ],
            [
                'category_id' => $menCargo?->id ?? 3,
                'name' => "Celana Cargo Vintage Y2K Woodland Camo Multi-Pocket Baggy Pants",
                'slug' => 'vintage-camo-cargo-pants-woodland',
                'short_description' => 'Kondisi 9.5/10. Tag Propper Military Specification. 6 saku kancing ekspansi, tali serut ankle bawah.',
                'description' => '<p>Celana kargo motif loreng militer Woodland US Army dengan siluet potongan baggy santai yang sangat populer di kultur streetwear Y2K dan skater. Dilengkapi pengatur pinggang samping dan tali serut di ujung kaki.</p>',
                'material_info' => '50% Cotton / 50% Nylon Ripstop Tahan Robek Mil-Spec.',
                'sustainability_note' => 'Material ripstop militer autentik yang dirancang tahan puluhan tahun.',
                'base_price' => 650000,
                'compare_at_price' => 950000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 750,
                'collections' => array_filter([$newArrivalsCol?->id, $workwearCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Woodland Green Camo',
                        'color_hex' => '#4f583e',
                        'sizes' => ['Size 32 (LP 83cm)' => 1],
                    ],
                    [
                        'color_name' => 'Desert Sand Camo',
                        'color_hex' => '#bda27e',
                        'sizes' => ['Size 34 (LP 87cm)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-camo-cargo-green.png', 'order' => 1, 'is_primary' => true],
                    ['url' => '/images/products/vintage-camo-cargo-desert.png', 'order' => 2, 'is_primary' => false],
                ],
            ],
            [
                'category_id' => $menCorduroy?->id ?? 3,
                'name' => "Celana Vintage Wide-Wale Corduroy Loose Trousers Chocolate Brown",
                'slug' => 'vintage-corduroy-trousers-brown',
                'short_description' => 'Kondisi 9.5/10. Tag L.L. Bean Vintage. Bahan korduroi garis tebal lembut warna cokelat moka, potongan loose fit.',
                'description' => '<p>Celana panjang bahan corduroy tebal dengan tekstur garis lebar (wide wale). Sangat nyaman, lembut, dan memberikan aksen retro 70-80s yang hangat saat dipadukan dengan hoodie atau jaket denim.</p>',
                'material_info' => '100% Cotton Wide-Wale Corduroy.',
                'sustainability_note' => 'Serat katun alami yang nyaman tanpa plastik poliester murah.',
                'base_price' => 550000,
                'compare_at_price' => 850000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 700,
                'collections' => array_filter([$newArrivalsCol?->id, $saleCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Chocolate Espresso',
                        'color_hex' => '#4a3528',
                        'sizes' => ['Size 32 (LP 82cm)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-corduroy-pants-brown.png', 'order' => 1, 'is_primary' => true],
                ],
            ],
            [
                'category_id' => $menWorkPants?->id ?? 3,
                'name' => "Celana Vintage Dickies 874 Original Fit Work Pants Olive Green",
                'slug' => 'vintage-dickies-874-olive-green',
                'short_description' => 'Kondisi 9.5/10. Tag Dickies Made in USA / Mexico. Bahan twill tahan noda dan kerut, potongan lurus kokoh.',
                'description' => '<p>Celana kerja skate klasik paling terkenal di dunia. Dikenal karena kekuatannya yang tak tertandingi dan lipatan garis tengah celana yang permanen. Pilihan wajib para pekerja kreatif, musisi, dan skater.</p>',
                'material_info' => '65% Polyester / 35% Cotton Twill Heavy 8.5oz.',
                'sustainability_note' => 'Konstruksi tangguh tahan bertahun-tahun.',
                'base_price' => 450000,
                'compare_at_price' => 700000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 600,
                'collections' => array_filter([$workwearCol?->id, $saleCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Olive Military Green',
                        'color_hex' => '#4b5338',
                        'sizes' => ['Size 32 (LP 82cm)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-dickies-874-olive.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // ==========================================
            // TOPI & HEADWEAR (HATS, CAPS, BEANIES)
            // ==========================================
            [
                'category_id' => $menSnapback?->id ?? 4,
                'name' => "Topi Vintage 90s New York Yankees MLB Pro-Model Snapback Cap",
                'slug' => 'vintage-yankees-90s-snapback-cap',
                'short_description' => 'Kondisi 9.5/10. Tag The Game / Sports Specialties Made in USA. Lidah hijau (green underbrim) klasik 90-an.',
                'description' => '<p>Topi snapback baseball original era 1990-an dengan logo NY Yankees bordir timbul 3D tebal. Menggunakan lidah bagian bawah berwarna hijau zamrud (green underbrim) yang menjadi ciri khas topi pro model vintage sejati.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Official Major League Baseball Genuine Merchandise<br>&bull; Pengatur: Snapback plastik 7-lubang utuh fleksibel<br>&bull; Bahan: 100% Wool Twill tebal berkualitas tinggi<br>&bull; Kondisi: 9.5/10 (Crown tegak kokoh, no minus keringat).</p>',
                'material_info' => '100% Wool Twill dengan bordir timbul benang rayon dan lidah green underbrim.',
                'sustainability_note' => 'Koleksi headwear vintage langka yang tidak diproduksi lagi dengan spek yang sama.',
                'base_price' => 650000,
                'compare_at_price' => 900000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 200,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Navy Blue & White Logo',
                        'color_hex' => '#1d273c',
                        'sizes' => ['One Size Fits All (Adjustable)' => 1],
                    ],
                    [
                        'color_name' => 'Forest Green & Gold',
                        'color_hex' => '#21402e',
                        'sizes' => ['One Size Fits All (Adjustable)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-yankees-snapback-navy.png', 'order' => 1, 'is_primary' => true],
                    ['url' => '/images/products/vintage-yankees-snapback-green.png', 'order' => 2, 'is_primary' => false],
                ],
            ],
            [
                'category_id' => $menBeanie?->id ?? 4,
                'name' => "Topi Kupluk Vintage Ribbed Knit Fisherman Beanie Mustard Yellow",
                'slug' => 'vintage-ribbed-knit-fisherman-beanie',
                'short_description' => 'Kondisi 10/10. Rajutan benang wol akrilik tebal elastis dengan lipatan brim ganda, warna mustard hangat.',
                'description' => '<p>Beanie rajut model nelayan klasik dengan kedalaman sedang yang pas di atas daun telinga. Sangat hangat, tidak gatal, dan memberikan sentuhan warna pop cerah untuk outfit streetwear monokrom.</p>',
                'material_info' => '100% High-Grade Soft Acrylic Ribbed Knit.',
                'sustainability_note' => 'Serat awet yang tidak mudah melar.',
                'base_price' => 185000,
                'compare_at_price' => 290000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 120,
                'collections' => array_filter([$newArrivalsCol?->id, $saleCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Mustard Ochre',
                        'color_hex' => '#d99b26',
                        'sizes' => ['One Size (Stretch)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-knit-beanie-mustard.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // ==========================================
            // SEPATU & FOOTWEAR (SNEAKERS, LOAFERS)
            // ==========================================
            [
                'category_id' => $menSneakers?->id ?? 5,
                'name' => "Sepatu Vintage 90s Low-Top Retro Skate Sneakers Navy/White",
                'slug' => 'vintage-retro-skate-sneakers-dunk',
                'short_description' => 'Kondisi 9/10. Upper kombinasi kulit suede & leather asli, midsole kuning vintage alami, sol karet tebal.',
                'description' => '<p>Sneaker siluet skate 90-an dengan perpaduan suede lembut dan kulit asli warna biru navy kontras putih. Midsole telah mengalami penuaan warna kuning alami (vintage yellowing) yang sangat dicari para penggemar retro style.</p>',
                'material_info' => 'Upper: Genuine Suede & Cowhide Leather. Sol: Vulkanisir Karet Alam Mentah.',
                'sustainability_note' => 'Sepatu pre-loved yang sudah dibersihkan dan disanitasi menyeluruh menggunakan formula anti-bakteri.',
                'base_price' => 1350000,
                'compare_at_price' => 1950000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 950,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Midnight Navy & Vintage White',
                        'color_hex' => '#1e2b3c',
                        'sizes' => ['41 (Insole 26.5cm)' => 1, '42 (Insole 27cm)' => 1, '43 (Insole 28cm)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-retro-sneaker-dunk.png', 'order' => 1, 'is_primary' => true],
                    ['url' => '/images/products/vintage-retro-sneaker-skate.png', 'order' => 2, 'is_primary' => false],
                ],
            ],
            [
                'category_id' => $menLoafers?->id ?? 5,
                'name' => "Sepatu Vintage Chunky Leather Penny Loafers Lug Sole Black",
                'slug' => 'vintage-chunky-penny-loafers-black',
                'short_description' => 'Kondisi 9/10. Kulit sapi asli tebal polished black dengan sol gerigi commando lug sole yang kokoh.',
                'description' => '<p>Penny loafers berkarakter kuat dengan sol komando bergerigi tebal. Memberikan siluet modern preppy sekaligus edgy yang sangat cocok dipadukan dengan celana denim baggy atau celana bahan corduroy.</p>',
                'material_info' => '100% Polished Full-Grain Leather & Heavy Rubber Commando Sole.',
                'sustainability_note' => 'Kualitas konstruksi Goodyear welt yang dapat disol ulang seumur hidup.',
                'base_price' => 1450000,
                'compare_at_price' => 2100000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 1100,
                'collections' => array_filter([$newArrivalsCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Polished Jet Black',
                        'color_hex' => '#181818',
                        'sizes' => ['41 (Insole 26.5cm)' => 1, '42 (Insole 27cm)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-chunky-loafer-black.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // ==========================================
            // TAS & AKSESORIS (BAGS, SUNGLASSES)
            // ==========================================
            [
                'category_id' => $menBags?->id ?? 6,
                'name' => "Tas Vintage Distressed Leather Crossbody Messenger Bag Brown",
                'slug' => 'vintage-leather-crossbody-messenger-bag',
                'short_description' => 'Kondisi 9.5/10. Kulit asli bertekstur patina alami dengan gesper kuningan vintage dan tali selempang kokoh.',
                'description' => '<p>Tas selempang kulit vintage serbaguna untuk membawa tablet, buku catatan, dompet, dan kamera saku. Semakin lama dipakai, karakter kulitnya akan semakin berkilau dan mewah.</p>',
                'material_info' => '100% Genuine Full-Grain Leather & Solid Brass Hardware.',
                'sustainability_note' => 'Menghindari pembelian tas sintetis berbahan kulit PU plastik yang cepat terkelupas.',
                'base_price' => 750000,
                'compare_at_price' => 1250000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 650,
                'collections' => array_filter([$newArrivalsCol?->id, $saleCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Rustic Saddle Brown',
                        'color_hex' => '#6e4529',
                        'sizes' => ['One Size (28 x 22 x 8 cm)' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-leather-crossbody-bag.png', 'order' => 1, 'is_primary' => true],
                ],
            ],
            [
                'category_id' => $menSun?->id ?? 6,
                'name' => "Kacamata Vintage 90s Oval Wire Frame Sunglasses Dark Tint",
                'slug' => 'vintage-oval-wire-sunglasses-90s',
                'short_description' => 'Kondisi 10/10 (Deadstock). Bingkai kawat metal tipis warna gunmetal dengan lensa UV400 gelap.',
                'description' => '<p>Kacamata hitam vintage model oval wire frame khas musisi britpop dan aktor film 90-an. Sangat ringan, elegan, dan melindungi mata 100% dari radiasi sinar UV.</p>',
                'material_info' => 'Stainless Steel Wire Alloy Frame & Polycarbonate UV400 Lenses.',
                'sustainability_note' => 'Item deadstock terawat dalam kondisi prima.',
                'base_price' => 290000,
                'compare_at_price' => 450000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 90,
                'collections' => array_filter([$saleCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Gunmetal Silver & Dark Smoke',
                        'color_hex' => '#444444',
                        'sizes' => ['One Size' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-retro-sunglasses.png', 'order' => 1, 'is_primary' => true],
                ],
            ],
        ];

        // 4. Seed Products, Variants, Images & Reviews
        foreach ($productsData as $data) {
            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category_id' => $data['category_id'],
                    'name' => $data['name'],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'material_info' => $data['material_info'],
                    'sustainability_note' => $data['sustainability_note'],
                    'base_price' => $data['base_price'],
                    'compare_at_price' => $data['compare_at_price'],
                    'is_active' => $data['is_active'],
                    'is_featured' => $data['is_featured'],
                    'weight_grams' => $data['weight_grams'],
                ]
            );

            // Sync collections
            if (!empty($data['collections'])) {
                $product->collections()->sync($data['collections']);
            }

            // Sync Images
            $product->images()->delete();
            foreach ($data['images'] as $img) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $img['url'],
                    'order' => $img['order'],
                    'is_primary' => $img['is_primary'],
                ]);
            }

            // Sync Variants
            $product->variants()->delete();
            $varIndex = 1;
            foreach ($data['variants'] as $varGroup) {
                foreach ($varGroup['sizes'] as $size => $stock) {
                    $sku = 'FIF-THF-' . str_pad($product->id, 3, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(Str::slug($varGroup['color_name']), 0, 4)) . '-' . $varIndex;
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $sku,
                        'color_name' => $varGroup['color_name'],
                        'color_hex' => $varGroup['color_hex'],
                        'size' => (string) $size,
                        'stock' => (int) $stock,
                        'price_override' => null,
                        'is_active' => true,
                    ]);
                    $varIndex++;
                }
            }

            // Seed Sample Verified Reviews for each product
            $user = User::first();
            if ($user && $product->reviews()->count() === 0) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => 5,
                    'title' => 'Kondisi barang luar biasa mulus & wangi laundry!',
                    'comment' => 'Barang vintage 1-of-1 asli sesuai deskripsi. Pengukurannya sangat akurat pas di badan dan sudah wangi siap pakai.',
                    'is_approved' => true,
                ]);
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => 5,
                    'title' => 'Pelayanan cepat dan kurasi itemnya juara',
                    'comment' => 'Packing aman, dapat sertifikat keaslian dan sticker pack. Rekomendasi thrift store terbaik!',
                    'is_approved' => true,
                ]);
            }
        }

        echo "ProductSeeder completed: " . count($productsData) . " rich curated thrift products with transparent PNGs and variants seeded.\n";
    }
}

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
        // ----------------------------------------------------
        // 1. Fetch Categories (Men & Women)
        // ----------------------------------------------------
        $menWorkwear = Category::where('slug', 'men-workwear-jackets')->first();
        $menVarsity = Category::where('slug', 'men-varsity-bomber')->first();
        $menHoodies = Category::where('slug', 'men-sweats-hoodies')->first();
        $menTracktop = Category::where('slug', 'men-tracktop-windbreaker')->first();
        $menLeather = Category::where('slug', 'men-leather-jackets')->first();
        $menBandTees = Category::where('slug', 'men-vintage-band-tees')->first();
        $menGraphicTees = Category::where('slug', 'men-graphic-tees')->first();
        $menFlannel = Category::where('slug', 'men-flannel-shirts')->first();
        $menPolo = Category::where('slug', 'men-polo-shirts')->first();
        $menDenim = Category::where('slug', 'men-vintage-denim')->first();
        $menCargo = Category::where('slug', 'men-cargo-pants')->first();
        $menCorduroy = Category::where('slug', 'men-corduroy-pants')->first();
        $menWorkPants = Category::where('slug', 'men-work-pants')->first();
        $menSnapback = Category::where('slug', 'men-vintage-snapback')->first();
        $menSneakers = Category::where('slug', 'men-everyday-sneakers')->first();
        $menBoots = Category::where('slug', 'men-hiking-trail-shoes')->first();
        $menBags = Category::where('slug', 'men-crossbody-bags')->first();
        $menSunglasses = Category::where('slug', 'men-retro-sunglasses')->first();

        $womenBomber = Category::where('slug', 'women-oversized-bomber')->first();
        $womenSweaters = Category::where('slug', 'women-knit-sweaters')->first();
        $womenLeather = Category::where('slug', 'women-leather-jackets')->first();
        $womenOversizedTees = Category::where('slug', 'women-oversized-tees')->first();
        $womenBabyTees = Category::where('slug', 'women-baby-tees')->first();
        $womenMomJeans = Category::where('slug', 'women-high-waist-denim')->first();
        $womenCargoSkirts = Category::where('slug', 'women-cargo-skirts')->first();
        $womenCorduroy = Category::where('slug', 'women-corduroy-pants')->first();
        $womenSneakers = Category::where('slug', 'women-everyday-sneakers')->first();
        $womenBags = Category::where('slug', 'women-bags-accessories')->first();

        // ----------------------------------------------------
        // 2. Fetch Collections
        // ----------------------------------------------------
        $newArrivalsCol = Collection::where('slug', 'new-arrivals')->first();
        $bestSellersCol = Collection::where('slug', 'best-sellers')->first();
        $saleCol = Collection::where('slug', 'sale')->first();
        $vintage90sCol = Collection::where('slug', 'vintage-90s')->first();
        $workwearCol = Collection::where('slug', 'workwear')->first();

        // ----------------------------------------------------
        // 3. Complete Curated Vintage Thrift Products Dataset
        // ----------------------------------------------------
        $productsData = [
            // 1. Carhartt Detroit
            [
                'category_id' => $menWorkwear?->id ?? 1,
                'name' => 'Jaket Vintage Carhartt Detroit Canvas J97 Faded Tan',
                'slug' => 'vintage-carhartt-detroit-j97-tan',
                'short_description' => 'Kondisi 9.5/10. Tag Carhartt Made in USA 90s. Heavyweight duck canvas dengan kerah corduroy cokelat (PxL: 69 x 64 cm).',
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
                        'sizes' => ['L' => 1],
                    ],
                    [
                        'color_name' => 'Washed Onyx Black',
                        'color_hex' => '#2b2927',
                        'sizes' => ['XL' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-carhartt-tan.png', 'order' => 1, 'is_primary' => true],
                    ['url' => '/images/products/vintage-carhartt-black.png', 'order' => 2, 'is_primary' => false],
                ],
            ],

            // 2. Nike Swoosh Hoodie
            [
                'category_id' => $menHoodies?->id ?? 1,
                'name' => 'Hoodie Vintage 90s Nike Center Mini Swoosh Embroidered',
                'slug' => 'vintage-nike-center-swoosh-hoodie',
                'short_description' => 'Kondisi 9.5/10. Tag Nike Silver Tag era 1996. Bordir logo Nike di tengah dada, bahan fleece tebal lembut (PxL: 72 x 66 cm).',
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
                        'sizes' => ['XL' => 1],
                    ],
                    [
                        'color_name' => 'Faded Charcoal Black',
                        'color_hex' => '#222222',
                        'sizes' => ['L' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-nike-hoodie-grey.png', 'order' => 1, 'is_primary' => true],
                    ['url' => '/images/products/vintage-nike-hoodie-black.png', 'order' => 2, 'is_primary' => false],
                ],
            ],

            // 3. Vintage Leather Racing Jacket
            [
                'category_id' => $menLeather?->id ?? 1,
                'name' => 'Jaket Vintage Leather Racing Moto Biker 90s Multi-Patch',
                'slug' => 'vintage-racing-jacket-leather',
                'short_description' => 'Kondisi 9/10. Kulit sapi asli (genuine cowhide) dengan detail bordir patch balap retro dan zipper YKK (PxL: 66 x 58 cm).',
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
                        'color_name' => 'Vintage Racing Tri-Tone',
                        'color_hex' => '#9e2a2b',
                        'sizes' => ['L' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-racing-jacket-leather.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 4. Varsity Letterman
            [
                'category_id' => $menVarsity?->id ?? 1,
                'name' => 'Jaket Vintage 90s Forest Green Wool & Leather Varsity Letterman',
                'slug' => 'vintage-varsity-jacket-green',
                'short_description' => 'Kondisi 9.5/10. Bodi wol tebal dengan lengan kulit asli krem dan chenille patch bordir tim rugby 1994 (PxL: 71 x 65 cm).',
                'description' => '<p>Varsity letterman jacket otentik tahun 1994 buatan Amerika Serikat. Memadukan bodi wol Melton warna hijau botol dengan lengan kulit asli yang lentur.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Golden Bear Sportswear Made in USA<br>&bull; Kondisi: 9.5/10 (Wol tebal tanpa bolong, kancing snap lengkap)<br>&bull; Ukuran: Size XL (Panjang 71 cm x Lebar 65 cm).</p>',
                'material_info' => 'Bodi: 80% Melton Wool / 20% Nylon. Lengan: 100% Genuine Leather. Furing: Quilted Satin.',
                'sustainability_note' => 'Mencegah pembuangan serat wol murni ke limbah lingkungan.',
                'base_price' => 1950000,
                'compare_at_price' => 2700000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 1400,
                'collections' => array_filter([$newArrivalsCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Forest Green & Cream',
                        'color_hex' => '#2d4a3e',
                        'sizes' => ['XL' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-varsity-jacket-green.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 5. Patagonia Retro-X Fleece
            [
                'category_id' => $menWorkwear?->id ?? 1,
                'name' => 'Jaket Vintage Patagonia Retro-X Deep Pile Fleece 90s Oatmeal',
                'slug' => 'vintage-patagonia-retro-x-fleece',
                'short_description' => 'Kondisi 9/10. Tag P-6 Logo era 1998. Bahan deep pile fleece tebal dengan saku dada nilon biru dongker (PxL: 68 x 60 cm).',
                'description' => '<p>Salah satu grail outerwear outdoor vintage paling ikonik. Model Retro-X deep pile sherpa fleece yang empuk, hangat, dan tahan angin (windproof barrier membrane).</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Patagonia Made in USA (Late 90s)<br>&bull; Kondisi: 9/10 (Bulu fleece masih empuk, tidak ada bagian yang kempes/matted)<br>&bull; Ukuran: Size M (Panjang 68 cm x Lebar 60 cm).</p>',
                'material_info' => '100% Recycled Polyester Deep Pile Fleece dengan trim nilon Supplex anti air.',
                'sustainability_note' => 'Pionir pakaian ramah lingkungan: Awet hingga puluhan tahun.',
                'base_price' => 1650000,
                'compare_at_price' => 2200000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 750,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Oatmeal Natural & Navy',
                        'color_hex' => '#e8e1d5',
                        'sizes' => ['M' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-patagonia-fleece-cream.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 6. Vintage Leather Aviator Bomber
            [
                'category_id' => $menLeather?->id ?? 1,
                'name' => 'Jaket Vintage Distressed Leather Aviator Bomber Flight Jacket 80s',
                'slug' => 'vintage-leather-bomber-distressed',
                'short_description' => 'Kondisi 8.5/10. Kulit domba asli (genuine lambskin) tebal dengan efek distressed patina alami (PxL: 67 x 62 cm).',
                'description' => '<p>Jaket penerbang vintage model A-2 Aviator Bomber Jacket era 1980-an. Memiliki kerah kulit lipat, ribbing rajut tebal di pinggang dan lengan, serta saku samping model flap.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Material: 100% Genuine Distressed Lambskin Leather<br>&bull; Kondisi: 8.5/10 (Patina pudar alami vintage look, zipper Scovill USA)<br>&bull; Ukuran: Size L (Panjang 67 cm x Lebar 62 cm).</p>',
                'material_info' => '100% Genuine Distressed Leather dengan furing katun bermotif peta navigasi klasik.',
                'sustainability_note' => 'Pakaian kulit vintage berkualitas tinggi yang tak lekang oleh waktu.',
                'base_price' => 2150000,
                'compare_at_price' => 2900000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 1500,
                'collections' => array_filter([$vintage90sCol?->id, $saleCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Distressed Dark Brown',
                        'color_hex' => '#3d2b1f',
                        'sizes' => ['L' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-leather-jacket-distressed.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 7. Ralph Lauren Harrington
            [
                'category_id' => $menWorkwear?->id ?? 1,
                'name' => 'Jaket Vintage Polo Ralph Lauren Harrington Windbreaker Beige Khaki',
                'slug' => 'vintage-polo-ralph-lauren-harrington',
                'short_description' => 'Kondisi 9.5/10. Tag Polo Ralph Lauren Navy Tag 90s. Katun chintz tebal dengan furing tartan plaid klasik (PxL: 68 x 62 cm).',
                'description' => '<p>Siluet Harrington legendaris dari Polo Ralph Lauren. Dilengkapi kerah kancing snap ganda, resleting kuningan YKK, dan bordir logo pony khas Ralph Lauren di dada.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Polo Ralph Lauren Vintage 90s<br>&bull; Kondisi: 9.5/10 (Sangat bersih, karet pinggang elastis sempurna)<br>&bull; Ukuran: Size L (Panjang 68 cm x Lebar 62 cm).</p>',
                'material_info' => '100% Heavy Combed Cotton Twill dengan furing 100% Katun Tartan Plaid.',
                'sustainability_note' => 'Gaya preppy vintage abadi tanpa jejak limbah baru.',
                'base_price' => 950000,
                'compare_at_price' => 1400000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 700,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Beige Khaki Tan',
                        'color_hex' => '#d7c4a3',
                        'sizes' => ['L' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-ralph-harrington-beige.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 8. Adidas Tracktop
            [
                'category_id' => $menTracktop?->id ?? 1,
                'name' => 'Tracktop Vintage Adidas Originals 80s Trefoil Firebird Navy',
                'slug' => 'vintage-adidas-tracktop-navy',
                'short_description' => 'Kondisi 9.5/10. Tag Trefoil Made in West Germany 80s. Bahan polyester knit berkilau klasik dengan 3-stripes putih (PxL: 67 x 58 cm).',
                'description' => '<p>Tracktop legendaris era 80-an Adidas Firebird. Dilengkapi kerah tegak stand collar, resleting YKK Trefoil metalik, dan bordir logo daun semanggi klasik di dada kiri.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Adidas Vintage 80s Trefoil<br>&bull; Kondisi: 9.5/10 (Bahan mulus, resleting lancar, karet lengan elastis)<br>&bull; Ukuran: Size M/L (Panjang 67 cm x Lebar 58 cm).</p>',
                'material_info' => '100% Retro Shiny Poly-Tricot Knit.',
                'sustainability_note' => 'Classic Sportswear yang bertahan lebih dari 4 dekade.',
                'base_price' => 880000,
                'compare_at_price' => 1250000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 600,
                'collections' => array_filter([$newArrivalsCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Deep Navy & White',
                        'color_hex' => '#1b2a47',
                        'sizes' => ['M' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-adidas-tracktop-navy.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 9. Nirvana In Utero Band Tee
            [
                'category_id' => $menBandTees?->id ?? 1,
                'name' => 'Kaos Vintage Nirvana In Utero 1993 Tour Single Stitch',
                'slug' => 'vintage-nirvana-in-utero-1993-tee',
                'short_description' => 'Kondisi 9/10. Tag Giant by Tultex Made in USA. Jahitan single stitch rapi, washed charcoal fade alami (PxL: 73 x 57 cm).',
                'description' => '<p>Grail band tee paling legendaris dalam sejarah musik grunge! Kaos tur original Nirvana "In Utero" tahun 1993 dengan artwork bidadari bersayap emas ikonik dan daftar tanggal tur di bagian belakang.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Giant 100% Cotton Made in USA<br>&bull; Jahitan: Full Single Stitch (Lengan & Bawah)<br>&bull; Kondisi: 9/10 (Fading washed sempurna, kain lemas jatuh vintage)<br>&bull; Ukuran: Size L (Panjang 73 cm x Lebar 57 cm).</p>',
                'material_info' => '100% Heavyweight Pre-shrunk Cotton dengan proses fading alami 30+ tahun.',
                'sustainability_note' => 'Otentik 1993 Grail: Pakaian vintage murni bernilai koleksi tinggi.',
                'base_price' => 3200000,
                'compare_at_price' => 4500000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 280,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Washed Vintage Black',
                        'color_hex' => '#272626',
                        'sizes' => ['L' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-nirvana-tee-black.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 10. Harley Davidson 3D Emblem Tee
            [
                'category_id' => $menGraphicTees?->id ?? 1,
                'name' => 'Kaos Vintage Harley Davidson 3D Emblem Eagle Biker 90s',
                'slug' => 'vintage-harley-davidson-3d-emblem-eagle',
                'short_description' => 'Kondisi 9.5/10. Tag 3D Emblem Fort Worth TX 1991. Sablon timbul elang emas, washed charcoal grey (PxL: 74 x 60 cm).',
                'description' => '<p>Grafis elang legendaris dari 3D Emblem Fort Worth Texas era awal 90-an. Gradasi warna emas dan detail bulu elang masih sangat tajam dengan efek wash vintage yang merata.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: 3D Emblem Made in USA<br>&bull; Kondisi: 9.5/10 (No pinhole, kerah leher kencang, kain tebal)<br>&bull; Ukuran: Size XL (Panjang 74 cm x Lebar 60 cm).</p>',
                'material_info' => '100% Premium USA Cotton Single Stitch.',
                'sustainability_note' => 'Pakaian biker klasik yang tetap bertahan lebih dari 30 tahun.',
                'base_price' => 2100000,
                'compare_at_price' => 2800000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 290,
                'collections' => array_filter([$bestSellersCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Charcoal Sun-Faded Black',
                        'color_hex' => '#323031',
                        'sizes' => ['XL' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-harley-tee-washed.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 11. Pendleton Wool Flannel
            [
                'category_id' => $menFlannel?->id ?? 1,
                'name' => 'Kemeja Flannel Vintage Pendleton 100% Virgin Wool Plaid',
                'slug' => 'vintage-pendleton-virgin-wool-flannel',
                'short_description' => 'Kondisi 9.5/10. Tag Pendleton Blue Label Made in Oregon USA. 100% pure virgin wool motif tartan merah-navy (PxL: 76 x 61 cm).',
                'description' => '<p>Flannel wol legendaris dari Pendleton Mills Oregon. Dibuat dari 100% wol murni yang tahan dingin, breathable, dan memiliki ketahanan serat yang luar biasa.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Pendleton Woolen Mills USA<br>&bull; Kondisi: 9.5/10 (Wol rapat, tidak berbulu berlebih, kancing asli lengkap)<br>&bull; Ukuran: Size L (Panjang 76 cm x Lebar 61 cm).</p>',
                'material_info' => '100% Pure Virgin Wool woven in USA mills.',
                'sustainability_note' => 'Serat alami 100% wol murni yang dapat terurai secara alami.',
                'base_price' => 1150000,
                'compare_at_price' => 1600000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 650,
                'collections' => array_filter([$newArrivalsCol?->id, $workwearCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Red & Navy Tartan Plaid',
                        'color_hex' => '#8f1d22',
                        'sizes' => ['L' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-pendleton-flannel-red.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 12. Ralph Lauren Polo Shirt
            [
                'category_id' => $menPolo?->id ?? 1,
                'name' => 'Polo Shirt Vintage Ralph Lauren Classic Mesh Navy',
                'slug' => 'vintage-ralph-lauren-classic-polo-navy',
                'short_description' => 'Kondisi 9.5/10. Tag Polo by Ralph Lauren 90s. Katun pique mesh tebal dengan bordir logo pony merah (PxL: 71 x 56 cm).',
                'description' => '<p>Polo shirt klasik 90s Polo Ralph Lauren. Potongan relaxed fit klasik dengan ekor tenis (tennis tail) yang sedikit lebih panjang di bagian belakang dan kancing mutiara asli.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Polo by Ralph Lauren Classic Fit<br>&bull; Kondisi: 9.5/10 (Warna navy pekat, kerah tidak keriting)<br>&bull; Ukuran: Size M/L (Panjang 71 cm x Lebar 56 cm).</p>',
                'material_info' => '100% Combed Cotton Pique Mesh.',
                'sustainability_note' => 'Preppy Heritage: Pakaian katun tahan lama.',
                'base_price' => 450000,
                'compare_at_price' => 750000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 320,
                'collections' => array_filter([$newArrivalsCol?->id, $saleCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Classic Navy',
                        'color_hex' => '#16233b',
                        'sizes' => ['M' => 1, 'L' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-ralph-polo-navy.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 13. Levi's 501 Selvedge Denim
            [
                'category_id' => $menDenim?->id ?? 1,
                'name' => "Celana Vintage Levi's 501 Made in USA Straight Selvedge Denim 1994",
                'slug' => 'vintage-levis-501-made-in-usa-denim',
                'short_description' => 'Kondisi 9.5/10. Tag Red Tab Batwing 553 button stamp Made in USA 1994. Straight leg fit, medium vintage stone wash (W32 L32).',
                'description' => '<p>Grail denim wajib bagi pecinta vintage! Celana jeans Levi\'s 501 original Made in USA era pabrik Valencia Street (sebelum produksi dipindah ke luar AS). Memiliki button fly 5 kancing tembaga dan jahitan rantai kuat.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Levi Strauss & Co San Francisco CA Made in USA<br>&bull; Button Stamp: 553 USA<br>&bull; Kondisi: 9.5/10 (Kain tebal 14oz kaku mantap, whisker paha alami)<br>&bull; Ukuran: Size W32 L32 (Lingkar Pinggang 82 cm, Panjang 105 cm, Open Leg 20 cm).</p>',
                'material_info' => '100% USA Cone Mills Ring-Spun Cotton Denim 14oz.',
                'sustainability_note' => 'Denim vintage otentik menghemat lebih dari 7.000 liter air dibanding jeans baru.',
                'base_price' => 1450000,
                'compare_at_price' => 1950000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 850,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id, $workwearCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Medium Stone Wash Blue',
                        'color_hex' => '#3a5f85',
                        'sizes' => ['W32' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-levis-501-selvedge.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 14. Military M-65 Woodland Camo Cargo
            [
                'category_id' => $menCargo?->id ?? 1,
                'name' => 'Celana Vintage Military M-65 Woodland Camo Cargo Pants',
                'slug' => 'vintage-military-m65-camo-cargo',
                'short_description' => 'Kondisi 9/10. Kontrak militer US Army era 1989. Katun ripstop tebal 6 kantong dengan tali serut bawah (Size M-Reg, LP: 80-88 cm).',
                'description' => '<p>Celana kargo militer otentik US Army spesifikasi militer (Mil-Spec). Dilengkapi 6 kantong bervolume besar dengan kancing snap tersembunyi, pengatur pinggang samping, dan tali serut di ujung pergelangan kaki.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: DLA Contract Official US Military Issue<br>&bull; Kondisi: 9/10 (Bahan ripstop kuat tanpa robek, motif camo tajam)<br>&bull; Ukuran: Medium Regular (Lingkar Pinggang 80-88 cm fleksibel, Panjang 102 cm).</p>',
                'material_info' => '50% Cotton / 50% Nylon Heavyweight Ripstop Wind Resistant.',
                'sustainability_note' => 'Daur ulang surplus militer dengan durabilitas seumur hidup.',
                'base_price' => 920000,
                'compare_at_price' => 1300000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 780,
                'collections' => array_filter([$workwearCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Woodland Camo',
                        'color_hex' => '#4a5d3c',
                        'sizes' => ['M' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-military-cargo-camo.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 15. Corduroy Pleated Trousers
            [
                'category_id' => $menCorduroy?->id ?? 1,
                'name' => 'Celana Vintage Pleated Corduroy Trousers Warm Brown',
                'slug' => 'vintage-pleated-corduroy-trousers-brown',
                'short_description' => 'Kondisi 9.5/10. Tag Polo Ralph Lauren Andrew Pant 90s. Katun korduroi tebal lipit ganda (double pleat) warna cokelat tembakau (W33 L30).',
                'description' => '<p>Celana korduroi berlipit khas era 90-an dengan potongan relaxed fit yang jatuh elegan. Tekstur korduroi lembut bergaris tebal (8-wale corduroy) memberikan nuansa hangat klasik vintage.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Polo Ralph Lauren Andrew Pant<br>&bull; Kondisi: 9.5/10 (Korduroi utuh tidak botak, saku rapi)<br>&bull; Ukuran: Size W33 L30 (Lingkar Pinggang 84 cm, Panjang 100 cm).</p>',
                'material_info' => '100% Heavy Combed Cotton Corduroy.',
                'sustainability_note' => 'Vintage tailoring yang tak lekang oleh tren.',
                'base_price' => 780000,
                'compare_at_price' => 1100000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 720,
                'collections' => array_filter([$vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Chestnut Tobacco Brown',
                        'color_hex' => '#5c3a21',
                        'sizes' => ['W33' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-corduroy-pants-brown.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 16. Dickies 874 Work Pants
            [
                'category_id' => $menWorkPants?->id ?? 1,
                'name' => 'Celana Vintage Dickies 874 Original Fit Work Pants Khaki',
                'slug' => 'vintage-dickies-874-work-pants-khaki',
                'short_description' => 'Kondisi 9.5/10. Tag Dickies Horseshoe Logo Made in USA 90s. Kain twill kaku tahan noda garis lipatan permanen (W34 L32).',
                'description' => '<p>Celana kerja skate paling populer di dunia. Model Dickies 874 original era produksi USA dengan bahan twill campuran tebal 8.5oz yang terkenal tahan banting.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Dickies Williamson-Dickie Mfg Co USA<br>&bull; Kondisi: 9.5/10 (Kain masih kaku seperti baru, zipper kuningan)<br>&bull; Ukuran: Size W34 L32 (Lingkar Pinggang 86 cm, Panjang 104 cm).</p>',
                'material_info' => '65% Polyester / 35% Cotton 8.5oz Heavyweight Twill.',
                'sustainability_note' => 'Durabilitas kerja tanpa batas waktu.',
                'base_price' => 580000,
                'compare_at_price' => 850000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 620,
                'collections' => array_filter([$workwearCol?->id, $saleCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Classic Tan Khaki',
                        'color_hex' => '#c2a67e',
                        'sizes' => ['W34' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-dickies-874-khaki.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 17. Nike Dunk High 1999
            [
                'category_id' => $menSneakers?->id ?? 1,
                'name' => 'Sneakers Vintage Nike Dunk High 1999 Michigan Retro',
                'slug' => 'vintage-nike-dunk-high-1999-michigan',
                'short_description' => 'Kondisi 9/10. Rilis perdana retro Dunk 1999 (Navy/Yellow). Kulit asli tebal premium dengan midsole yellowing alami (Size US 9.5 / EU 43).',
                'description' => '<p>Salah satu grail sneakers paling diincar kolektor sepatu vintage. Rilis retro pertama tahun 1999 dari seri ikonik "Be True To Your School" (BTTYS) University of Michigan. Kualitas kulit tebal lentur yang tidak bisa ditemukan di rilisan modern saat ini.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Kode Rilis: 630335-471 (Tahun 1999)<br>&bull; Kondisi: 9/10 (Upper kulit mulus, outsole tebal bintang masih terlihat, midsole aged patina alami)<br>&bull; Ukuran: US 9.5 / UK 8.5 / EUR 43 / 27.5 cm.</p>',
                'material_info' => '100% Full-Grain Premium Leather Upper dengan rubber cupsole.',
                'sustainability_note' => 'Arsip sejarah sneaker yang terus bernilai investasi tinggi.',
                'base_price' => 2850000,
                'compare_at_price' => 3800000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 1200,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Midnight Navy & Maize Yellow',
                        'color_hex' => '#1d2a44',
                        'sizes' => ['43' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-nike-dunk-high.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 18. Red Wing 875 Moc Toe Boots
            [
                'category_id' => $menBoots?->id ?? 1,
                'name' => 'Boots Vintage Red Wing 875 Moc Toe Oro Legacy Leather',
                'slug' => 'vintage-red-wing-875-moc-toe-boots',
                'short_description' => 'Kondisi 9.5/10. Tag Red Wing Red Wing MN USA. Kulit sapi Oro Legacy dengan sol Traction Tred putih (Size US 9D / EUR 42).',
                'description' => '<p>Sepatu bot legendaris buatan tangan pengrajin Minnesota AS sejak 1952. Menggunakan konstruksi Goodyear Welt yang kokoh dan kulit minyak asli yang semakin lama dipakai semakin indah patinanya.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Red Wing Heritage Made in Red Wing MN USA<br>&bull; Konstruksi: Goodyear Welted dengan sol Crepe Traction Tred<br>&bull; Kondisi: 9.5/10 (Patina kulit sangat mewah, sol tebal)<br>&bull; Ukuran: US 9D / EUR 42 (Insole 27.0 cm).</p>',
                'material_info' => '100% Full Grain Oil-Tanned Oro Legacy Leather.',
                'sustainability_note' => 'Lifetime Boots: Dapat diganti sol berulang kali dan tahan seumur hidup.',
                'base_price' => 3100000,
                'compare_at_price' => 4500000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 1700,
                'collections' => array_filter([$bestSellersCol?->id, $workwearCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Oro Legacy Amber Brown',
                        'color_hex' => '#9e532b',
                        'sizes' => ['42' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-redwing-boots.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 19. Bulls 1996 Snapback
            [
                'category_id' => $menSnapback?->id ?? 1,
                'name' => 'Topi Snapback Vintage Chicago Bulls 1996 NBA Champions',
                'slug' => 'vintage-chicago-bulls-1996-snapback',
                'short_description' => 'Kondisi 9.5/10. Tag Starter / Logo Athletic Made in USA 1996. Bodi wol hitam dengan bordir banteng merah & green undervisor (All Size).',
                'description' => '<p>Topi selebrasi juara legendaris musim 72-10 Michael Jordan bersama Chicago Bulls tahun 1996. Memiliki bordir tebal timbul di bagian depan dan lidah bawah warna hijau klasik (green undervisor).</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Starter Genuine Merchandise NBA Made in USA<br>&bull; Kondisi: 9.5/10 (Struktur mahkota tegak, snap plastik utuh tidak retak)<br>&bull; Ukuran: Adjustable Snapback (All Size Dewasa).</p>',
                'material_info' => '80% Wool / 20% Acrylic dengan gesper snapback plastik vintage.',
                'sustainability_note' => 'Arsip budaya pop basket 90s yang bernilai tinggi.',
                'base_price' => 650000,
                'compare_at_price' => 950000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 180,
                'collections' => array_filter([$vintage90sCol?->id, $bestSellersCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Black & Bulls Red',
                        'color_hex' => '#1a1a1a',
                        'sizes' => ['All Size' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-bulls-snapback.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 20. Porter Yoshida Tanker Sling Bag
            [
                'category_id' => $menBags?->id ?? 1,
                'name' => 'Tas Selempang Vintage Porter Yoshida & Co Tanker Sling Olive',
                'slug' => 'vintage-porter-yoshida-tanker-sling-olive',
                'short_description' => 'Kondisi 9.5/10. Tag Porter Yoshida Made in Japan. Nilon MA-1 flight jacket 3 lapis warna sage green dengan furing rescue orange (PxT: 28 x 20 cm).',
                'description' => '<p>Seri Tanker ikonik dari Yoshida Kaban Tokyo yang terinspirasi jaket pilot US Air Force MA-1. Menggunakan material nilon twill halus dengan bantalan busa empuk di dalamnya dan resleting kuningan YKK.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Yoshida & Co Tokyo Made in Japan<br>&bull; Kondisi: 9.5/10 (Sangat bersih, velcro merekat kuat, hardware mulus)<br>&bull; Kompartemen: 2 kantong depan snap + ruang utama beritsleting ganda<br>&bull; Dimensi: Panjang 28 cm x Tinggi 20 cm x Tebal 10 cm.</p>',
                'material_info' => '3-Layer Bonding Nylon Twill dengan Rescue Orange Taffeta Lining.',
                'sustainability_note' => 'Japanese Craftsmanship: Tas tahan puluhan tahun.',
                'base_price' => 1350000,
                'compare_at_price' => 1800000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 380,
                'collections' => array_filter([$newArrivalsCol?->id, $workwearCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Sage Olive Green',
                        'color_hex' => '#606b56',
                        'sizes' => ['All Size' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-porter-sling-olive.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 21. B&L Ray-Ban Wayfarer Tortoiseshell
            [
                'category_id' => $menSunglasses?->id ?? 1,
                'name' => 'Kacamata Vintage B&L Ray-Ban Wayfarer Tortoiseshell 80s',
                'slug' => 'vintage-rayban-wayfarer-tortoiseshell',
                'short_description' => 'Kondisi 9.5/10. Tag Bausch & Lomb (B&L) Made in USA era 1980-an. Frame asetat motif kura-kura dengan lensa kaca mineral G-15 grafir BL.',
                'description' => '<p>Kacamata hitam paling berpengaruh dalam sejarah sinema dan fashion. Dibuat sebelum Ray-Ban diakuisisi Luxottica pada 1999, versi Bausch & Lomb buatan AS ini menggunakan kaca optik murni G-15 yang sangat jernih dan adem di mata.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Ukuran: 50-22 (Ukuran klasik medium)<br>&bull; Lensa: G-15 Green Neutral Glass dengan grafir "BL" di kedua sisi lensa<br>&bull; Kondisi: 9.5/10 (Engsel 7-barrel kokoh, frame asetat mengkilap tanpa goresan dalam)<br>&bull; Kelengkapan: Termasuk case kulit vintage original.</p>',
                'material_info' => '100% Handcrafted Acetate Frame & Mineral Glass UV400 Lenses.',
                'sustainability_note' => 'Kacamata kaca murni B&L USA yang abadi.',
                'base_price' => 1650000,
                'compare_at_price' => 2300000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 120,
                'collections' => array_filter([$vintage90sCol?->id, $saleCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Amber Tortoiseshell',
                        'color_hex' => '#63391b',
                        'sizes' => ['50mm' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-rayban-wayfarer.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // ----------------------------------------------------
            // 4. Curated Women Collection Items
            // ----------------------------------------------------
            // 22. Women Oversized Varsity
            [
                'category_id' => $womenBomber?->id ?? 1,
                'name' => 'Jaket Vintage Oversized Varsity Bomber Forest Green 90s',
                'slug' => 'vintage-women-oversized-varsity-green',
                'short_description' => 'Kondisi 9.5/10. Bodi wol Melton hijau botol dengan lengan kulit krem lentur. Potongan oversized boyfriend fit (PxL: 68 x 62 cm).',
                'description' => '<p>Varsity letterman vintage dengan siluet oversized yang sangat manis dipadukan dengan crop top atau mini skirt. Memiliki kancing snap metalik dan bordir chenille klasik di dada.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Kondisi: 9.5/10 (Wol tebal bersih, kulit lengan lentur)<br>&bull; Ukuran: Oversized Women (Panjang 68 cm x Lebar 62 cm).</p>',
                'material_info' => 'Melton Wool Body & Genuine Soft Leather Sleeves.',
                'sustainability_note' => 'Slow Fashion 90s.',
                'base_price' => 1750000,
                'compare_at_price' => 2400000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 1300,
                'collections' => array_filter([$newArrivalsCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Forest Green & Cream',
                        'color_hex' => '#2d4a3e',
                        'sizes' => ['All Size' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-varsity-jacket-green.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 23. Women Vintage Fleece
            [
                'category_id' => $womenSweaters?->id ?? 1,
                'name' => 'Cardigan Fleece Vintage Patagonia Deep Pile Sherpa Oatmeal',
                'slug' => 'vintage-women-patagonia-fleece-oatmeal',
                'short_description' => 'Kondisi 9/10. Bulu sherpa fleece tebal lembut warna krem oatmeal dengan aksen saku navy (PxL: 65 x 56 cm).',
                'description' => '<p>Fleece cardigan vintage Patagonia yang hangat, ringan, dan aesthetic. Sangat cocok untuk gaya busana gorpcore maupun santai sehari-hari.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Kondisi: 9/10 (Bulu tebal tidak menggumpal, resleting lancar)<br>&bull; Ukuran: Size S/M (Panjang 65 cm x Lebar 56 cm).</p>',
                'material_info' => '100% Recycled Polyester Deep Pile Sherpa Fleece.',
                'sustainability_note' => 'Eco-friendly Outdoor Vintage.',
                'base_price' => 1550000,
                'compare_at_price' => 2100000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 700,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Oatmeal Sherpa',
                        'color_hex' => '#e8e1d5',
                        'sizes' => ['S' => 1, 'M' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-patagonia-fleece-cream.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 24. Women Oversized Band Tee
            [
                'category_id' => $womenOversizedTees?->id ?? 1,
                'name' => 'Kaos Vintage Oversized Grunge Band Tee Nirvana 1993',
                'slug' => 'vintage-women-nirvana-oversized-tee',
                'short_description' => 'Kondisi 9/10. Tag Giant USA Single Stitch. Faded washed black lemas jatuh dengan grafis In Utero malaikat emas (PxL: 73 x 57 cm).',
                'description' => '<p>Kaos band grunge vintage otentik 1993 dengan efek washed yang sangat estetik untuk tampilan oversized vintage streetwear wanita.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Tag: Giant Made in USA<br>&bull; Kondisi: 9/10 (Kain lemas, jahitan single stitch asli)<br>&bull; Ukuran: One Size Oversized Fit (Panjang 73 cm x Lebar 57 cm).</p>',
                'material_info' => '100% Vintage Soft Washed Cotton Single Stitch.',
                'sustainability_note' => 'Authentic Vintage Grunge.',
                'base_price' => 3200000,
                'compare_at_price' => 4500000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 280,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id, $vintage90sCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Faded Vintage Black',
                        'color_hex' => '#272626',
                        'sizes' => ['All Size' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-nirvana-tee-black.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 25. Women High-Waist Levi's Jeans
            [
                'category_id' => $womenMomJeans?->id ?? 1,
                'name' => "Celana Jeans Vintage High-Waist Mom Jeans Levi's 501 USA",
                'slug' => 'vintage-women-levis-high-waist-denim',
                'short_description' => 'Kondisi 9.5/10. Tag Red Tab Made in USA. Potongan high-waist ramping di pinggang dan lurus di kaki (W28 L30).',
                'description' => '<p>Denim vintage high-waist yang sangat flattering untuk wanita. Dibuat di Amerika Serikat dengan katun 100% tanpa stretch yang menopang lekuk tubuh dengan sempurna.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Kondisi: 9.5/10 (Kain kaku mantap, wash medium vintage autentik)<br>&bull; Ukuran: W28 L30 (Lingkar Pinggang 72 cm, Rise 29 cm, Panjang 98 cm).</p>',
                'material_info' => '100% Vintage Rigid Cotton Denim 14oz.',
                'sustainability_note' => 'Denim abadi ramah lingkungan.',
                'base_price' => 1350000,
                'compare_at_price' => 1850000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 800,
                'collections' => array_filter([$newArrivalsCol?->id, $bestSellersCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Medium Stone Blue',
                        'color_hex' => '#3a5f85',
                        'sizes' => ['W28' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-levis-501-selvedge.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 26. Women Baggy Military Cargo
            [
                'category_id' => $womenCargoSkirts?->id ?? 1,
                'name' => 'Celana Baggy Cargo Pants Vintage Military Woodland Camo',
                'slug' => 'vintage-women-baggy-camo-cargo',
                'short_description' => 'Kondisi 9/10. US Military Spec 1989. Potongan relaxed baggy dengan tali serut pergelangan kaki yang modis.',
                'description' => '<p>Celana kargo militer vintage dengan potongan baggy yang menjadi favorit tren Y2K dan streetwear wanita terkini. Mudah dipadukan dengan tube top, baby tee, atau oversized hoodie.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Kondisi: 9/10 (Ripstop kuat, warna camo otentik)<br>&bull; Ukuran: Adjustable Waist (Lingkar Pinggang 68-78 cm).</p>',
                'material_info' => '50% Cotton / 50% Nylon Heavyweight Ripstop.',
                'sustainability_note' => 'Surplus militer otentik.',
                'base_price' => 890000,
                'compare_at_price' => 1250000,
                'is_active' => true,
                'is_featured' => false,
                'weight_grams' => 750,
                'collections' => array_filter([$vintage90sCol?->id, $workwearCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Woodland Camouflage',
                        'color_hex' => '#4a5d3c',
                        'sizes' => ['All Size' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-military-cargo-camo.png', 'order' => 1, 'is_primary' => true],
                ],
            ],

            // 27. Women Vintage Sling Bag
            [
                'category_id' => $womenBags?->id ?? 1,
                'name' => 'Tas Selempang Vintage Porter Yoshida Tanker Sling Bag Olive',
                'slug' => 'vintage-women-porter-tanker-sling',
                'short_description' => 'Kondisi 9.5/10. Yoshida Tokyo Made in Japan. Nilon twill sage olive dengan furing rescue orange cerah (PxT: 28 x 20 cm).',
                'description' => '<p>Tas selempang multifungsi dari Porter Yoshida Tokyo. Sangat ringan, empuk, dan memiliki gaya utilitas Jepang yang cocok untuk wanita aktif.</p><p><strong>Detail Spesifikasi:</strong><br>&bull; Kondisi: 9.5/10 (Mulus terawat, resleting lancar)<br>&bull; Dimensi: Panjang 28 cm x Tinggi 20 cm.</p>',
                'material_info' => '3-Layer Flight Nylon Twill.',
                'sustainability_note' => 'Japanese Craftsmanship.',
                'base_price' => 1350000,
                'compare_at_price' => 1800000,
                'is_active' => true,
                'is_featured' => true,
                'weight_grams' => 380,
                'collections' => array_filter([$newArrivalsCol?->id]),
                'variants' => [
                    [
                        'color_name' => 'Sage Olive Green',
                        'color_hex' => '#606b56',
                        'sizes' => ['All Size' => 1],
                    ],
                ],
                'images' => [
                    ['url' => '/images/products/vintage-porter-sling-olive.png', 'order' => 1, 'is_primary' => true],
                ],
            ],
        ];

        // ----------------------------------------------------
        // 5. Seed Products & Attach Relations
        // ----------------------------------------------------
        $users = User::all();
        if ($users->isEmpty()) {
            $users = collect([User::factory()->create()]);
        }

        foreach ($productsData as $data) {
            $collections = $data['collections'] ?? [];
            $variants = $data['variants'] ?? [];
            $images = $data['images'] ?? [];

            unset($data['collections'], $data['variants'], $data['images']);

            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            // Attach collections
            if (! empty($collections)) {
                $product->collections()->sync($collections);
            }

            // Sync Variants
            $skuIndex = 1;
            foreach ($variants as $vData) {
                $colorName = $vData['color_name'];
                $colorHex = $vData['color_hex'] ?? '#000000';
                $sizes = $vData['sizes'] ?? ['All Size' => 1];

                foreach ($sizes as $sizeName => $stock) {
                    $sku = strtoupper(Str::slug($product->slug)).'-'.strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $colorName), 0, 3)).'-'.strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', (string) $sizeName)).'-'.$skuIndex++;

                    ProductVariant::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'sku' => $sku,
                        ],
                        [
                            'size' => (string) $sizeName,
                            'color_name' => $colorName,
                            'color_hex' => $colorHex,
                            'stock' => $stock,
                            'price_override' => null,
                            'is_active' => true,
                        ]
                    );
                }
            }

            // Sync Images
            foreach ($images as $imgData) {
                ProductImage::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'image_path' => $imgData['url'],
                    ],
                    [
                        'order' => $imgData['order'] ?? 1,
                        'is_primary' => $imgData['is_primary'] ?? false,
                    ]
                );
            }

            // Add authentic reviews if none exist
            if ($product->reviews()->count() === 0) {
                $reviewCount = rand(2, 4);
                $randomUsers = $users->random(min($reviewCount, $users->count()));
                $ratingPool = [5, 5, 5, 4];

                $reviewComments = [
                    'Barang sampai sesuai deskripsi! Tag vintage-nya otentik dan wanginya enak banget udah bersih.',
                    'Kualitas kulit/bahannya gila sih, kondisi 9.5/10 beneran kayak baru. Rekomendasi banget!',
                    'Packing super rapi dan pengiriman kilat. Barangnya langka banget akhirnya dapet di fifa.',
                    'Sangat puas! PxL-nya pas banget di badan saya. Pasti bakal mantau drop berikutnya.',
                    'Detail jahitan dan wash-nya cakep parah. Beneran 1 of 1 vintage grail yang worth it!',
                ];

                foreach ($randomUsers as $u) {
                    Review::create([
                        'product_id' => $product->id,
                        'user_id' => $u->id,
                        'rating' => $ratingPool[array_rand($ratingPool)],
                        'title' => 'Vintage Grail Berkualitas!',
                        'comment' => $reviewComments[array_rand($reviewComments)],
                        'is_approved' => true,
                    ]);
                }
            }
        }
    }
}

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
        
        // 2. Fetch Collections
        $newArrivalsCol = Collection::where('slug', 'new-arrivals')->first();
        $bestSellersCol = Collection::where('slug', 'best-sellers')->first();
        $saleCol = Collection::where('slug', 'sale')->first();
        $vintage90sCol = Collection::where('slug', 'vintage-90s')->first();
        $workwearCol = Collection::where('slug', 'workwear')->first();

        // 3. Gemini-Generated Authentic Vintage Thrift Products Dataset
        $productsData = [
            [
                'category_id' => $menWorkwear?->id ?? 1,
                'name' => "Jaket Vintage Carhartt Detroit Canvas J97 Faded Tan",
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
            [
                'category_id' => $menHoodies?->id ?? 1,
                'name' => "Hoodie Vintage 90s Nike Center Mini Swoosh Embroidered",
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
            [
                'category_id' => $menLeather?->id ?? 1,
                'name' => "Jaket Vintage Leather Racing Moto Biker 90s Multi-Patch",
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
            [
                'category_id' => $menVarsity?->id ?? 1,
                'name' => "Jaket Vintage 90s Forest Green Wool & Leather Varsity Letterman",
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
            [
                'category_id' => $menWorkwear?->id ?? 1,
                'name' => "Jaket Vintage Patagonia Retro-X Deep Pile Fleece 90s Oatmeal",
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
            [
                'category_id' => $menLeather?->id ?? 1,
                'name' => "Jaket Vintage Distressed Leather Aviator Bomber Flight Jacket 80s",
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
            [
                'category_id' => $menWorkwear?->id ?? 1,
                'name' => "Jaket Vintage Polo Ralph Lauren Harrington Windbreaker Beige Khaki",
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
        ];

        // 4. Seed Products
        $users = User::all();
        if ($users->isEmpty()) {
            $users = collect([User::factory()->create()]);
        }

        foreach ($productsData as $data) {
            $collections = $data['collections'] ?? [];
            $variants = $data['variants'] ?? [];
            $images = $data['images'] ?? [];

            unset($data['collections'], $data['variants'], $data['images']);

            $product = Product::create($data);

            // Attach collections
            if (!empty($collections)) {
                $product->collections()->sync($collections);
            }

            // Create Variants
            $skuIndex = 1;
            foreach ($variants as $vData) {
                $colorName = $vData['color_name'];
                $colorHex = $vData['color_hex'] ?? '#000000';
                $sizes = $vData['sizes'] ?? ['All Size' => 1];

                foreach ($sizes as $sizeName => $stock) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => strtoupper(Str::slug($product->slug)) . '-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $colorName), 0, 3)) . '-' . strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $sizeName)) . '-' . $skuIndex++,
                        'size' => (string) $sizeName,
                        'color_name' => $colorName,
                        'color_hex' => $colorHex,
                        'stock' => $stock,
                        'price_override' => null,
                        'is_active' => true,
                    ]);
                }
            }

            // Create Images
            foreach ($images as $imgData) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imgData['url'],
                    'order' => $imgData['order'] ?? 1,
                    'is_primary' => $imgData['is_primary'] ?? false,
                ]);
            }

            // Add authentic reviews
            $reviewCount = rand(2, 4);
            $randomUsers = $users->random(min($reviewCount, $users->count()));
            $ratingPool = [5, 5, 5, 4];

            $reviewComments = [
                "Barang sampai sesuai deskripsi! Tag vintage-nya otentik dan wanginya enak banget udah bersih.",
                "Kualitas kulit/bahannya gila sih, kondisi 9.5/10 beneran kayak baru. Rekomendasi banget!",
                "Packing super rapi dan pengiriman kilat. Jaketnya langka banget akhirnya dapet di fifa.",
                "Sangat puas! PxL-nya pas banget di badan saya. Pasti bakal mantau drop berikutnya.",
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

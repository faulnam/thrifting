<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------
        // 1. MEN MAIN CATEGORY & SUBCATEGORIES
        // ----------------------------------------------------
        $men = Category::updateOrCreate(
            ['slug' => 'men'],
            [
                'parent_id' => null,
                'gender' => 'men',
                'name' => 'Pria',
                'description' => 'Koleksi busana vintage, jaket workwear, kaos band langka, denim, dan streetwear pria 1-of-1 terkurasi.',
                'order' => 1,
                'is_active' => true,
            ]
        );

        // Men Jackets & Outerwear
        $menJackets = Category::updateOrCreate(
            ['slug' => 'men-jackets-outerwear'],
            [
                'parent_id' => $men->id,
                'gender' => 'men',
                'name' => 'Jaket & Outerwear',
                'description' => 'Koleksi jaket workwear, bomber, varsity, tracktop, dan kulit vintage.',
                'order' => 1,
                'is_active' => true,
            ]
        );

        $menJacketTypes = [
            'Jaket Workwear & Canvas' => 'men-workwear-jackets',
            'Varsity & Bomber 90s' => 'men-varsity-bomber',
            'Hoodie & Crewneck Vintage' => 'men-sweats-hoodies',
            'Tracktop & Windbreaker' => 'men-tracktop-windbreaker',
            'Jaket Kulit & Moto Vintage' => 'men-leather-jackets',
        ];

        foreach ($menJacketTypes as $name => $slug) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $menJackets->id,
                    'gender' => 'men',
                    'name' => $name,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        // Men Tops & Tees
        $menApparel = Category::updateOrCreate(
            ['slug' => 'men-tees-tops'],
            [
                'parent_id' => $men->id,
                'gender' => 'men',
                'name' => 'Baju & Kaos',
                'description' => 'Kaos band single-stitch 90s, graphic tee vintage, kemeja flannel, dan polo retro.',
                'order' => 2,
                'is_active' => true,
            ]
        );

        $menTeeTypes = [
            'Vintage Band Tees' => 'men-vintage-band-tees',
            'Graphic Tees 90s & Y2K' => 'men-graphic-tees',
            'Kemeja Flannel & Plaid' => 'men-flannel-shirts',
            'Kemeja Vintage Motif' => 'men-vintage-shirts',
            'Polo Shirt Retro' => 'men-polo-shirts',
        ];

        foreach ($menTeeTypes as $name => $slug) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $menApparel->id,
                    'gender' => 'men',
                    'name' => $name,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        // Men Pants & Denim
        $menPants = Category::updateOrCreate(
            ['slug' => 'men-pants-bottoms'],
            [
                'parent_id' => $men->id,
                'gender' => 'men',
                'name' => 'Celana & Denim',
                'description' => 'Denim vintage Levi\'s, cargo pants multi-pocket, corduroy, dan work pants.',
                'order' => 3,
                'is_active' => true,
            ]
        );

        $menPantTypes = [
            'Denim & Jeans Vintage' => 'men-vintage-denim',
            'Cargo Pants & Baggy' => 'men-cargo-pants',
            'Celana Corduroy' => 'men-corduroy-pants',
            'Workwear Pants (Dickies/Carhartt)' => 'men-work-pants',
        ];

        foreach ($menPantTypes as $name => $slug) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $menPants->id,
                    'gender' => 'men',
                    'name' => $name,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        // Men Hats & Headwear
        $menHats = Category::updateOrCreate(
            ['slug' => 'men-hats-caps'],
            [
                'parent_id' => $men->id,
                'gender' => 'men',
                'name' => 'Topi & Headwear',
                'description' => 'Snapback MLB 90s, beanie knit, 5-panel cap, dan washed dad cap.',
                'order' => 4,
                'is_active' => true,
            ]
        );

        $menHatTypes = [
            'Snapback Vintage 90s' => 'men-vintage-snapback',
            'Beanie & Knit Hat' => 'men-knit-beanie',
            'Bucket Hat Retro' => 'men-bucket-hats',
            'Washed Dad Cap' => 'men-dad-caps',
        ];

        foreach ($menHatTypes as $name => $slug) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $menHats->id,
                    'gender' => 'men',
                    'name' => $name,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        // Men Shoes & Retro Footwear
        $menShoes = Category::updateOrCreate(
            ['slug' => 'men-shoes'],
            [
                'parent_id' => $men->id,
                'gender' => 'men',
                'name' => 'Sepatu & Sneakers',
                'description' => 'Sneakers retro, vintage skate shoes, boots kulit, dan chunky loafers.',
                'order' => 5,
                'is_active' => true,
            ]
        );

        $menShoeTypes = [
            'Retro & Skate Sneakers' => 'men-everyday-sneakers',
            'Vintage Running Shoes' => 'men-running-shoes',
            'Chunky Loafers & Derby' => 'men-slip-ons-loungers',
            'Boots Kulit Vintage' => 'men-hiking-trail-shoes',
        ];

        foreach ($menShoeTypes as $name => $slug) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $menShoes->id,
                    'gender' => 'men',
                    'name' => $name,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        // Men Bags & Accessories
        $menBags = Category::updateOrCreate(
            ['slug' => 'men-bags-accessories'],
            [
                'parent_id' => $men->id,
                'gender' => 'men',
                'name' => 'Tas & Aksesoris',
                'description' => 'Tas selempang kulit vintage, belt kulit retro, dan kacamata vintage.',
                'order' => 6,
                'is_active' => true,
            ]
        );

        $menBagTypes = [
            'Crossbody & Sling Bag' => 'men-crossbody-bags',
            'Ikat Pinggang Kulit' => 'men-leather-belts',
            'Kacamata Vintage' => 'men-retro-sunglasses',
        ];

        foreach ($menBagTypes as $name => $slug) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $menBags->id,
                    'gender' => 'men',
                    'name' => $name,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        // ----------------------------------------------------
        // 2. WOMEN MAIN CATEGORY & SUBCATEGORIES
        // ----------------------------------------------------
        $women = Category::updateOrCreate(
            ['slug' => 'women'],
            [
                'parent_id' => null,
                'gender' => 'women',
                'name' => 'Wanita',
                'description' => 'Koleksi busana vintage wanita, baby tees 90s, oversized streetwear, denim high-waist, dan knitwear.',
                'order' => 2,
                'is_active' => true,
            ]
        );

        // Women Jackets & Outerwear
        $womenJackets = Category::updateOrCreate(
            ['slug' => 'women-jackets-outerwear'],
            [
                'parent_id' => $women->id,
                'gender' => 'women',
                'name' => 'Jaket & Outerwear',
                'description' => 'Oversized bomber, jaket denim vintage, varsity, dan knit cardigan.',
                'order' => 1,
                'is_active' => true,
            ]
        );

        $womenJacketTypes = [
            'Oversized Bomber & Varsity' => 'women-oversized-bomber',
            'Jaket Denim Vintage' => 'women-denim-jackets',
            'Knit Sweater & Cardigan' => 'women-knit-sweaters',
            'Leather Jacket Vintage' => 'women-leather-jackets',
        ];

        foreach ($womenJacketTypes as $name => $slug) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $womenJackets->id,
                    'gender' => 'women',
                    'name' => $name,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        // Women Tops & Tees
        $womenApparel = Category::updateOrCreate(
            ['slug' => 'women-tees-tops'],
            [
                'parent_id' => $women->id,
                'gender' => 'women',
                'name' => 'Baju & Atasan',
                'description' => 'Graphic baby tees Y2K, oversized band tees, kemeja retro, dan blus vintage.',
                'order' => 2,
                'is_active' => true,
            ]
        );

        $womenTeeTypes = [
            'Graphic Baby Tees Y2K' => 'women-baby-tees',
            'Oversized Graphic Tees' => 'women-oversized-tees',
            'Blouse & Kemeja Vintage' => 'women-vintage-blouse',
        ];

        foreach ($womenTeeTypes as $name => $slug) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $womenApparel->id,
                    'gender' => 'women',
                    'name' => $name,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        // Women Pants & Skirts
        $womenPants = Category::updateOrCreate(
            ['slug' => 'women-pants-bottoms'],
            [
                'parent_id' => $women->id,
                'gender' => 'women',
                'name' => 'Celana & Bawahan',
                'description' => 'High-waist mom jeans, cargo skirt, celana corduroy, dan baggy denim.',
                'order' => 3,
                'is_active' => true,
            ]
        );

        $womenPantTypes = [
            'High-Waist Mom Jeans' => 'women-high-waist-denim',
            'Cargo Skirt & Baggy Pants' => 'women-cargo-skirts',
            'Corduroy Trousers' => 'women-corduroy-pants',
        ];

        foreach ($womenPantTypes as $name => $slug) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $womenPants->id,
                    'gender' => 'women',
                    'name' => $name,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        // Women Hats & Headwear
        $womenHats = Category::updateOrCreate(
            ['slug' => 'women-hats-caps'],
            [
                'parent_id' => $women->id,
                'gender' => 'women',
                'name' => 'Topi & Headwear',
                'description' => 'Vintage caps, beret retro, bucket hat, dan knit beanie.',
                'order' => 4,
                'is_active' => true,
            ]
        );

        $womenHatTypes = [
            'Vintage Caps & Beret' => 'women-vintage-caps',
            'Knit Beanie' => 'women-knit-beanies',
        ];

        foreach ($womenHatTypes as $name => $slug) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $womenHats->id,
                    'gender' => 'women',
                    'name' => $name,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        // Women Shoes & Bags
        $womenShoes = Category::updateOrCreate(
            ['slug' => 'women-shoes'],
            [
                'parent_id' => $women->id,
                'gender' => 'women',
                'name' => 'Sepatu',
                'description' => 'Sneakers retro, platform loafers, dan vintage boots.',
                'order' => 5,
                'is_active' => true,
            ]
        );

        $womenShoeTypes = [
            'Sneaker Retro Sehari-hari' => 'women-everyday-sneakers',
            'Platform Loafers & Mary Jane' => 'women-flats-loungers',
            'Retro Running Shoes' => 'women-running-shoes',
            'Slip-On Vintage' => 'women-slip-ons',
        ];

        foreach ($womenShoeTypes as $name => $slug) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $womenShoes->id,
                    'gender' => 'women',
                    'name' => $name,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        $womenBags = Category::updateOrCreate(
            ['slug' => 'women-bags-accessories'],
            [
                'parent_id' => $women->id,
                'gender' => 'women',
                'name' => 'Tas & Aksesoris',
                'description' => 'Vintage tote bag, shoulder bag kulit, dan syal retro.',
                'order' => 6,
                'is_active' => true,
            ]
        );

        // ----------------------------------------------------
        // 3. CURATED COLLECTIONS
        // ----------------------------------------------------
        $collections = [
            [
                'title' => 'Drop Terbaru (Fresh Drops)',
                'slug' => 'new-arrivals',
                'description' => 'Koleksi thrift 1-of-1 pilihan terbaru minggu ini. Semua item sudah dicuci bersih dan siap pakai.',
                'order' => 1,
            ],
            [
                'title' => 'Paling Diburu (Rare & Vault)',
                'slug' => 'best-sellers',
                'description' => 'Item vintage paling langka dan banyak dicari — mulai dari Carhartt Detroit, Nike Center Swoosh, hingga Band Tees 90s.',
                'order' => 2,
            ],
            [
                'title' => 'Steal Deals & Cuci Gudang',
                'slug' => 'sale',
                'description' => 'Penawaran harga terbaik untuk item vintage terkurasi dengan potongan spesial.',
                'order' => 3,
            ],
            [
                'title' => 'Koleksi 90s & Y2K Aesthetic',
                'slug' => 'vintage-90s',
                'description' => 'Pilihan busana era 90-an dan Y2K: single-stitch tees, baggy denim, dan oversized windbreaker.',
                'order' => 4,
            ],
            [
                'title' => 'Workwear & Streetwear Vault',
                'slug' => 'workwear',
                'description' => 'Koleksi workwear tangguh (Carhartt, Dickies) dan streetwear legendaris (Stussy, Supreme, Champion).',
                'order' => 5,
            ],
        ];

        foreach ($collections as $col) {
            Collection::updateOrCreate(
                ['slug' => $col['slug']],
                [
                    'title' => $col['title'],
                    'description' => $col['description'],
                    'is_active' => true,
                    'order' => $col['order'],
                ]
            );
        }
    }
}

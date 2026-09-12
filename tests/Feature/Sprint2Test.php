<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Sprint2Test extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
        $this->customer = User::where('role', 'customer')->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Public Catalog & PDP Tests
    |--------------------------------------------------------------------------
    */

    public function test_collection_pages_render_successfully(): void
    {
        // Men's collection
        $menRes = $this->get('/men');
        $menRes->assertStatus(200);
        $menRes->assertSee("Men's Collection");

        // Women's collection
        $womenRes = $this->get('/women');
        $womenRes->assertStatus(200);
        $womenRes->assertSee("Women's Collection");

        // Sale collection
        $saleRes = $this->get('/sale');
        $saleRes->assertStatus(200);

        // Specific collection slug
        $colRes = $this->get('/collections/new-arrivals');
        $colRes->assertStatus(200);
        $colRes->assertSee('Drop Terbaru');
    }

    public function test_collection_filtering_and_sorting(): void
    {
        $response = $this->get('/collections/new-arrivals?sort=price_asc&gender=men');
        $response->assertStatus(200);

        $product = Product::whereHas('collections', fn ($q) => $q->where('slug', 'new-arrivals'))->first();
        if ($product) {
            $response->assertSee($product->name);
        }
    }

    public function test_pdp_renders_with_variants_and_accordions(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        $response = $this->get('/products/'.$product->slug);
        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Pilih Ukuran');
        $response->assertSee('Deskripsi');
        $response->assertSee('Keberlanjutan');
        $response->assertSee('Pengiriman');
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Category & Collection CRUD Tests
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_create_category(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/categories', [
            'name' => 'Testing Hiking Shoes',
            'slug' => 'testing-hiking-shoes',
            'gender' => 'men',
            'description' => 'Great for outdoor mountain trails.',
            'order' => 5,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'slug' => 'testing-hiking-shoes',
            'gender' => 'men',
        ]);
    }

    public function test_admin_can_create_collection_with_products(): void
    {
        $this->actingAs($this->admin);
        $product = Product::first();

        $response = $this->post('/admin/collections', [
            'title' => 'Limited Pantone Edition',
            'slug' => 'limited-pantone-edition',
            'description' => 'Exclusive seasonal color palettes.',
            'products' => [$product->id],
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.collections.index'));
        $this->assertDatabaseHas('collections', [
            'slug' => 'limited-pantone-edition',
        ]);

        $col = Collection::where('slug', 'limited-pantone-edition')->first();
        $this->assertTrue($col->products->contains($product->id));
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Product & Nested Variants/Images CRUD Tests
    |--------------------------------------------------------------------------
    */

    public function test_product_validation_cannot_be_active_without_image(): void
    {
        $this->actingAs($this->admin);
        $category = Category::first();

        // 1. Creating active product without images should fail validation
        $response = $this->post('/admin/products', [
            'name' => 'Shoe Without Image Test',
            'category_id' => $category->id,
            'base_price' => 1500000,
            'weight_grams' => 500,
            'is_active' => '1',
        ]);
        $response->assertSessionHasErrors('is_active');

        // 2. Creating inactive product without images should succeed
        $createInactiveRes = $this->post('/admin/products', [
            'name' => 'Shoe Inactive Test',
            'category_id' => $category->id,
            'base_price' => 1500000,
            'weight_grams' => 500,
            'is_active' => 0,
        ]);
        $createInactiveRes->assertStatus(302);
        $created = Product::where('name', 'Shoe Inactive Test')->first();
        $this->assertNotNull($created);
        $this->assertFalse($created->is_active);

        // 3. Now try updating the inactive product to active without adding images -> should fail
        $updateRes = $this->put('/admin/products/'.$created->id, [
            'name' => 'Shoe Inactive Test',
            'slug' => $created->slug,
            'category_id' => $category->id,
            'base_price' => 1500000,
            'weight_grams' => 500,
            'is_active' => '1',
        ]);
        $updateRes->assertSessionHasErrors('is_active');
    }

    public function test_admin_can_create_and_manage_product_variants(): void
    {
        $this->actingAs($this->admin);
        $product = Product::first();

        $response = $this->post("/admin/products/{$product->id}/variants", [
            'color_name' => 'Forest Green',
            'color_hex' => '#1b4d3e',
            'size' => '42',
            'stock' => 20,
            'sku' => 'TEST-TREE-GRN-42',
        ]);

        $response->assertRedirect(route('admin.products.edit', $product));
        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'sku' => 'TEST-TREE-GRN-42',
            'color_hex' => '#1b4d3e',
            'stock' => 20,
        ]);
    }

    public function test_admin_can_upload_and_set_primary_image(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);
        $product = Product::first();

        $file = UploadedFile::fake()->image('shoe_test.jpg');

        $response = $this->post("/admin/products/{$product->id}/images", [
            'images' => [$file],
        ]);

        $response->assertRedirect(route('admin.products.edit', $product));
        $uploaded = ProductImage::where('product_id', $product->id)->latest('id')->first();
        $this->assertNotNull($uploaded);

        // Set as primary
        $primaryRes = $this->patch("/admin/products/{$product->id}/images/{$uploaded->id}/primary");
        $primaryRes->assertRedirect(route('admin.products.edit', $product));
        $this->assertTrue($uploaded->fresh()->is_primary);
    }
}

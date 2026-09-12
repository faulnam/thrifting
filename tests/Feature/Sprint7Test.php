<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\HeroSlide;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\HtmlSanitizer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class Sprint7Test extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;

    protected User $customerUser;

    protected Category $category;

    protected Product $product;

    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->adminUser = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first() ?? User::create([
            'name' => 'Admin Sprint 7',
            'email' => 'admin_test_sprint7@fifa.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->customerUser = User::where('role', 'customer')->first() ?? User::create([
            'name' => 'Customer Sprint 7',
            'email' => 'customer_test_sprint7@fifa.test',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $this->category = Category::first() ?? Category::create([
            'name' => 'Running Shoes',
            'slug' => 'running-shoes',
            'gender' => 'men',
            'is_active' => true,
        ]);

        $this->product = Product::first() ?? Product::create([
            'category_id' => $this->category->id,
            'name' => 'Tree Dasher 2',
            'slug' => 'tree-dasher-2',
            'base_price' => 1850000,
            'weight_grams' => 650,
            'is_active' => true,
        ]);

        $this->variant = $this->product->variants()->first() ?? ProductVariant::create([
            'product_id' => $this->product->id,
            'sku' => 'TD2-M-BLK-42',
            'color_name' => 'Blizzard / White',
            'color_hex' => '#212121',
            'size' => '42',
            'stock' => 15,
            'price' => 1850000,
            'is_active' => true,
        ]);
    }

    public function test_html_sanitizer_removes_dangerous_tags_and_event_handlers(): void
    {
        $dirtyHtml = '<h1>Judul Aman</h1><p>Paragraf <script>alert("XSS")</script>dengan teks <strong>tebal</strong>.</p><img src="x" onerror="alert(1)" /><a href="javascript:alert(1)">Link Bahaya</a><iframe src="https://evil.com"></iframe><style>body{color:red;}</style>';
        $cleaned = HtmlSanitizer::clean($dirtyHtml);

        $this->assertStringNotContainsString('<script', $cleaned);
        $this->assertStringNotContainsString('onerror', $cleaned);
        $this->assertStringNotContainsString('javascript:', $cleaned);
        $this->assertStringNotContainsString('<iframe', $cleaned);
        $this->assertStringNotContainsString('<style', $cleaned);
        $this->assertStringContainsString('<h1>Judul Aman</h1>', $cleaned);
        $this->assertStringContainsString('<strong>tebal</strong>', $cleaned);
    }

    public function test_coupon_validation_prevents_start_date_after_expiry_date(): void
    {
        $this->actingAs($this->adminUser);

        // Invalid date range: expires_at before starts_at
        $response = $this->post(route('admin.coupons.store'), [
            'code' => 'INVALIDDATE'.rand(100, 999),
            'type' => 'percent',
            'value' => 20,
            'starts_at' => '2026-10-10',
            'expires_at' => '2026-10-01',
        ]);

        $response->assertSessionHasErrors('expires_at');

        // Valid date range
        $code = 'VALIDDATE'.rand(100, 999);
        $validResponse = $this->post(route('admin.coupons.store'), [
            'code' => $code,
            'type' => 'percent',
            'value' => 20,
            'starts_at' => '2026-10-01',
            'expires_at' => '2026-10-10',
            'min_purchase' => 100000,
            'usage_limit' => 50,
            'is_active' => 1,
        ]);

        $validResponse->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => $code, 'value' => 20]);
    }

    public function test_admin_can_crud_coupons_and_toggle_active(): void
    {
        $this->actingAs($this->adminUser);

        $code = 'PROMO'.rand(100, 999);
        $coupon = Coupon::create([
            'code' => $code,
            'type' => 'percent',
            'value' => 10,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        // Toggle status
        $toggleRes = $this->patch(route('admin.coupons.toggle', $coupon->id));
        $toggleRes->assertRedirect();
        $this->assertFalse($coupon->fresh()->is_active);

        // Update coupon
        $updateCode = 'PROMOUP'.rand(100, 999);
        $updateRes = $this->put(route('admin.coupons.update', $coupon->id), [
            'code' => $updateCode,
            'type' => 'fixed',
            'value' => 150000,
            'is_active' => 1,
        ]);
        $updateRes->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => $updateCode, 'value' => 150000, 'type' => 'fixed']);

        // Delete coupon
        $delRes = $this->delete(route('admin.coupons.destroy', $coupon->id));
        $delRes->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    public function test_admin_can_crud_hero_slides(): void
    {
        $this->actingAs($this->adminUser);

        $title = 'Comfort Step '.rand(100, 999);
        $storeRes = $this->post(route('admin.hero-slides.store'), [
            'page' => 'home',
            'title' => $title,
            'subtitle' => 'Made from renewable sugarcane and wool.',
            'cta_text' => 'Shop Men',
            'cta_link' => '/men',
            'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff',
            'order' => 1,
            'is_active' => 1,
        ]);

        $storeRes->assertRedirect(route('admin.hero-slides.index'));
        $this->assertDatabaseHas('hero_slides', ['title' => $title, 'page' => 'home']);

        $slide = HeroSlide::where('title', $title)->first();

        // Update
        $newTitle = 'Updated '.$title;
        $updateRes = $this->put(route('admin.hero-slides.update', $slide->id), [
            'page' => 'home',
            'title' => $newTitle,
            'order' => 2,
            'is_active' => 1,
            'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff',
        ]);
        $updateRes->assertRedirect(route('admin.hero-slides.index'));
        $this->assertDatabaseHas('hero_slides', ['id' => $slide->id, 'title' => $newTitle]);

        // Delete
        $delRes = $this->delete(route('admin.hero-slides.destroy', $slide->id));
        $delRes->assertRedirect(route('admin.hero-slides.index'));
        $this->assertDatabaseMissing('hero_slides', ['id' => $slide->id]);
    }

    public function test_admin_can_crud_cms_pages_and_content_is_sanitized(): void
    {
        $this->actingAs($this->adminUser);

        $slug = 'tentang-kami-'.rand(100, 999);
        $response = $this->post(route('admin.pages.store'), [
            'title' => 'Tentang Kami',
            'slug' => $slug,
            'content' => '<p>Sejarah fifa <script>alert("hack")</script></p>',
            'meta_title' => 'Tentang Kami — fifa',
        ]);

        $response->assertRedirect(route('admin.pages.index'));
        $page = Page::where('slug', $slug)->first();
        $this->assertNotNull($page);
        $this->assertStringNotContainsString('<script', $page->content);
        $this->assertStringContainsString('Sejarah fifa', $page->content);

        // Public view
        $publicRes = $this->get(route('pages.show', $slug));
        $publicRes->assertStatus(200);
        $publicRes->assertSee('Tentang Kami');
    }

    public function test_admin_can_crud_blog_posts_and_public_can_view_published(): void
    {
        $this->actingAs($this->adminUser);

        $slug = 'inovasi-wol-zq-'.rand(100, 999);
        $storeRes = $this->post(route('admin.blog.store'), [
            'title' => 'Inovasi Wol ZQ '.$slug,
            'slug' => $slug,
            'excerpt' => 'Kisah wol alami yang nyaman dan lembut.',
            'content' => '<p>Wol Selandia Baru adalah kunci utama kenyamanan kami.</p>',
            'is_published' => 1,
            'published_at' => now()->format('Y-m-d'),
        ]);

        $storeRes->assertRedirect(route('admin.blog.index'));
        $this->assertDatabaseHas('blog_posts', ['slug' => $slug, 'is_published' => true]);

        // Public index & show
        $indexRes = $this->get(route('blog.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Inovasi Wol ZQ');

        $showRes = $this->get(route('blog.show', $slug));
        $showRes->assertStatus(200);
        $showRes->assertSee('Inovasi Wol ZQ');
    }

    public function test_admin_can_crud_store_locations_and_public_view(): void
    {
        $this->actingAs($this->adminUser);

        $name = 'fifa Senayan '.rand(100, 999);
        $storeRes = $this->post(route('admin.stores.store'), [
            'name' => $name,
            'city' => 'Jakarta Pusat',
            'address' => 'Senayan City Lt 1',
            'phone' => '021-123456',
            'opening_hours' => '10:00 - 22:00',
            'latitude' => -6.227,
            'longitude' => 106.797,
            'is_active' => 1,
        ]);

        $storeRes->assertRedirect(route('admin.stores.index'));
        $this->assertDatabaseHas('store_locations', ['name' => $name]);

        // Public store locator
        $publicRes = $this->get(route('stores.index'));
        $publicRes->assertStatus(200);
        $publicRes->assertSee($name);
        $publicRes->assertSee('Jakarta Pusat');
    }

    public function test_review_moderation_flow_and_pdp_only_displays_approved_reviews(): void
    {
        Review::where('product_id', $this->product->id)->where('user_id', $this->customerUser->id)->delete();

        // 1. Customer submits review
        $this->actingAs($this->customerUser);

        $submitRes = $this->post(route('products.reviews.store', $this->product->id), [
            'rating' => 5,
            'title' => 'Sepatu paling empuk!',
            'comment' => 'Sangat nyaman dipakai jogging harian tanpa kaus kaki.',
        ]);

        $submitRes->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->customerUser->id,
            'rating' => 5,
            'is_approved' => false, // Initially unapproved
        ]);

        $review = Review::where('product_id', $this->product->id)
            ->where('user_id', $this->customerUser->id)
            ->first();

        // 2. Check PDP before approval — unapproved review must NOT be displayed
        $pdpBefore = $this->get(route('products.show', $this->product->slug));
        $pdpBefore->assertStatus(200);
        $pdpBefore->assertDontSee('Sepatu paling empuk!');

        // 3. Admin approves review
        $this->actingAs($this->adminUser);
        $approveRes = $this->patch(route('admin.reviews.approve', $review->id));
        $approveRes->assertRedirect();
        $this->assertTrue($review->fresh()->is_approved);

        // 4. Check PDP after approval — approved review MUST appear
        $pdpAfter = $this->get(route('products.show', $this->product->slug));
        $pdpAfter->assertStatus(200);
        $pdpAfter->assertSee('Sepatu paling empuk!');
    }

    public function test_admin_can_update_site_settings_grouped_by_section(): void
    {
        $this->actingAs($this->adminUser);

        // Update Shipping Settings (origin_biteship_area_id)
        $shippingRes = $this->post(route('admin.settings.update'), [
            '_group' => 'shipping',
            'origin_biteship_area_id' => 'IDNP6IDNC148IDND859_CUSTOM',
            'origin_city' => 'Jakarta Selatan',
            'origin_postal_code' => '12190',
            'biteship_active' => '1',
        ]);
        $shippingRes->assertRedirect(route('admin.settings.index', ['tab' => 'shipping']));
        $this->assertEquals('IDNP6IDNC148IDND859_CUSTOM', SiteSetting::get('origin_biteship_area_id'));

        // Update Payment Settings (toggle gateway)
        $paymentRes = $this->post(route('admin.settings.update'), [
            '_group' => 'payment',
            'payment_gateway_active' => 'sandbox',
            'midtrans_is_production' => '0',
        ]);
        $paymentRes->assertRedirect(route('admin.settings.index', ['tab' => 'payment']));
        $this->assertEquals('sandbox', SiteSetting::get('payment_gateway_active'));
    }

    public function test_customer_can_subscribe_and_admin_can_export_csv(): void
    {
        // 1. Customer subscribes
        $subRes = $this->post(route('newsletter.subscribe'), [
            'email' => 'subscriber_test_'.uniqid().'@example.com',
        ]);
        $subRes->assertRedirect();

        // 2. Admin exports CSV
        $this->actingAs($this->adminUser);
        $exportRes = $this->get(route('admin.subscribers.export'));
        $exportRes->assertStatus(200);
        $exportRes->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_dashboard_and_reports_render_sales_analytics_and_low_stock_alerts(): void
    {
        $this->actingAs($this->adminUser);

        // Create a low stock variant (stock = 2 <= 5)
        $lowVariant = ProductVariant::create([
            'product_id' => $this->product->id,
            'sku' => 'TD2-M-LOW-'.rand(100, 999),
            'color_name' => 'Natural Black',
            'color_hex' => '#000000',
            'size' => '43',
            'stock' => 0,
            'price' => 1850000,
            'is_active' => true,
        ]);

        // Dashboard
        $dashRes = $this->get(route('admin.dashboard'));
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Ringkasan Toko');

        // Reports
        $reportRes = $this->get(route('admin.reports.index'));
        $reportRes->assertStatus(200);
        $reportRes->assertSee('Laporan Penjualan');
        $reportRes->assertSee('Peringatan Stok Menipis');
        $reportRes->assertSee($lowVariant->sku);
    }
}

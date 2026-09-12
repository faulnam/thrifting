<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class Sprint1Test extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('fifa');
        $response->assertSee('Koleksi Pria');
        $response->assertSee('Koleksi Wanita');
    }

    public function test_customer_login_and_register_pages_render(): void
    {
        $loginRes = $this->get('/login');
        $loginRes->assertStatus(200);
        $loginRes->assertSee('Masuk ke Akun Anda');

        $registerRes = $this->get('/register');
        $registerRes->assertStatus(200);
        $registerRes->assertSee('Buat Akun Baru');
    }

    public function test_customer_registration_creates_customer_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role' => 'customer',
        ]);
        $this->assertAuthenticated();
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::where('role', 'customer')->first();
        $this->actingAs($customer);

        $response = $this->get('/admin');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login_and_access_admin_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::where('email', 'admin@fifa.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());

        $this->actingAs($admin);
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Toko');
    }

    public function test_customer_cannot_login_via_admin_portal(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'demo.customer@fifa.test',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_category_seeder_hierarchy_exists(): void
    {
        $men = Category::where('slug', 'men')->first();
        $this->assertNotNull($men);
        $this->assertEquals('men', $men->gender);

        $shoes = Category::where('slug', 'men-shoes')->first();
        $this->assertNotNull($shoes);
        $this->assertEquals($men->id, $shoes->parent_id);
    }

    public function test_site_settings_can_be_retrieved(): void
    {
        $storeName = SiteSetting::get('store_name');
        $this->assertEquals('fifa', $storeName);

        $threshold = SiteSetting::get('free_shipping_threshold');
        $this->assertEquals('500000', $threshold);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Sprint8Test extends TestCase
{
    use DatabaseTransactions;

    protected User $customer;

    protected User $otherCustomer;

    protected Product $product;

    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->customer = User::create([
            'name' => 'Budi Customer',
            'email' => 'budi_sprint8_'.uniqid().'@test.com',
            'password' => bcrypt('Secret123!'),
            'role' => 'customer',
            'phone' => '081234567890',
        ]);

        $this->otherCustomer = User::create([
            'name' => 'Siti Customer',
            'email' => 'siti_sprint8_'.uniqid().'@test.com',
            'password' => bcrypt('Secret123!'),
            'role' => 'customer',
            'phone' => '089876543210',
        ]);

        $this->product = Product::first();
        $this->variant = $this->product->variants->first();
    }

    /**
     * Helper to create a test order.
     */
    protected function createCustomerOrder(User $user, string $status = 'paid'): Order
    {
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-'.strtoupper(uniqid()),
            'status' => $status,
            'subtotal' => 1850000,
            'shipping_cost' => 12000,
            'discount_amount' => 0,
            'total' => 1862000,
            'shipping_address_snapshot' => [
                'recipient_name' => $user->name,
                'phone' => $user->phone,
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'district' => 'Kebayoran Baru',
                'postal_code' => '12190',
                'address_line' => 'Jl. Senopati No. 88',
                'biteship_area_id' => 'IDNP6IDNC148IDND859',
            ],
            'courier_company' => 'jne',
            'courier_service' => 'REG',
            'courier_type' => 'reg',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'product_name_snapshot' => $this->product->name,
            'variant_snapshot' => [
                'color_name' => $this->variant->color_name,
                'size' => $this->variant->size,
                'sku' => $this->variant->sku,
            ],
            'price' => $this->variant->effective_price,
            'qty' => 1,
            'subtotal' => $this->variant->effective_price,
        ]);

        $shipment = Shipment::create([
            'order_id' => $order->id,
            'courier_company' => 'jne',
            'courier_type' => 'reg',
            'biteship_order_id' => 'bs_test_'.uniqid(),
            'tracking_id' => 'TRK_TEST_'.rand(1000, 9999),
            'waybill_id' => 'JNE'.rand(100000, 999999),
            'status' => 'requested',
        ]);

        ShipmentTracking::create([
            'shipment_id' => $shipment->id,
            'status' => 'allocated',
            'note' => 'Kurir sedang menuju lokasi penjemputan barang',
            'occurred_at' => now(),
        ]);

        return $order;
    }

    public function test_customer_can_view_account_dashboard_with_metrics_and_recent_orders(): void
    {
        $this->actingAs($this->customer);

        $order = $this->createCustomerOrder($this->customer, 'paid');

        $response = $this->get(route('account.dashboard'));
        $response->assertStatus(200);
        $response->assertSee($this->customer->name);
        $response->assertSee($order->order_number);
        $response->assertSee('Ringkasan');
        $response->assertSee('Pesanan');
        $response->assertSee('Tracking');
    }

    public function test_customer_can_view_order_history_and_detail_with_tracking_timeline(): void
    {
        $this->actingAs($this->customer);

        $order = $this->createCustomerOrder($this->customer, 'paid');

        // Order history list
        $listRes = $this->get(route('account.orders.index'));
        $listRes->assertStatus(200);
        $listRes->assertSee($order->order_number);

        // Order detail
        $detailRes = $this->get(route('account.orders.show', $order));
        $detailRes->assertStatus(200);
        $detailRes->assertSee($order->order_number);
        $detailRes->assertSee('Pelacakan Pengiriman (Live Tracking)');
        $detailRes->assertSee('Kurir sedang menuju lokasi penjemputan barang');
    }

    public function test_customer_cannot_access_other_customers_order_detail(): void
    {
        $this->actingAs($this->customer);

        // Order belongs to otherCustomer
        $otherOrder = $this->createCustomerOrder($this->otherCustomer, 'paid');

        $response = $this->get(route('account.orders.show', $otherOrder));
        $response->assertStatus(403);
    }

    public function test_customer_can_crud_addresses_and_set_default(): void
    {
        $this->actingAs($this->customer);

        // Create Address
        $storeRes = $this->post(route('account.addresses.store'), [
            'label' => 'Kantor Pusat',
            'recipient_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'district' => 'Kebayoran Baru',
            'postal_code' => '12190',
            'address_line' => 'Gedung Bursa Efek Tower 1 Lt. 12',
            'biteship_area_id' => 'IDNP6IDNC148IDND859',
            'is_default' => 1,
        ]);

        $storeRes->assertRedirect(route('account.addresses.index'));
        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->customer->id,
            'label' => 'Kantor Pusat',
            'is_default' => true,
        ]);

        $address = Address::where('user_id', $this->customer->id)->where('label', 'Kantor Pusat')->first();

        // Create a 2nd address
        $storeRes2 = $this->post(route('account.addresses.store'), [
            'label' => 'Rumah',
            'recipient_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'district' => 'Kebayoran Lama',
            'postal_code' => '12250',
            'address_line' => 'Jl. Kebayoran Lama No. 10',
            'biteship_area_id' => 'IDNP6IDNC148IDND843IDZ12250',
            'is_default' => 0,
        ]);

        $address2 = Address::where('user_id', $this->customer->id)->where('label', 'Rumah')->first();

        // Set 2nd address as default
        $defRes = $this->patch(route('account.addresses.default', $address2));
        $defRes->assertRedirect();
        $this->assertTrue($address2->fresh()->is_default);
        $this->assertFalse($address->fresh()->is_default);

        // Delete address
        $delRes = $this->delete(route('account.addresses.destroy', $address));
        $delRes->assertRedirect(route('account.addresses.index'));
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_customer_can_update_profile_info(): void
    {
        $this->actingAs($this->customer);

        $updateRes = $this->patch(route('account.profile.update'), [
            'name' => 'Budi Santoso Updated',
            'email' => $this->customer->email,
            'phone' => '081199887766',
        ]);

        $updateRes->assertRedirect(route('account.profile'));
        $this->assertDatabaseHas('users', [
            'id' => $this->customer->id,
            'name' => 'Budi Santoso Updated',
            'phone' => '081199887766',
        ]);
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $this->actingAs($this->customer);

        // 1. Wrong current password -> should fail validation
        $failRes = $this->patch(route('account.profile.password'), [
            'current_password' => 'WrongPassword123!',
            'password' => 'NewSecret888!',
            'password_confirmation' => 'NewSecret888!',
        ]);

        $failRes->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('Secret123!', $this->customer->fresh()->password));

        // 2. Correct current password -> should succeed
        $passRes = $this->patch(route('account.profile.password'), [
            'current_password' => 'Secret123!',
            'password' => 'NewSecret888!',
            'password_confirmation' => 'NewSecret888!',
        ]);

        $passRes->assertRedirect(route('account.profile'));
        $this->assertTrue(Hash::check('NewSecret888!', $this->customer->fresh()->password));
    }

    public function test_customer_can_view_their_submitted_reviews(): void
    {
        $this->actingAs($this->customer);

        // Create review for customer
        $review = Review::create([
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'rating' => 5,
            'title' => 'Kenyamanan luar biasa',
            'comment' => 'Sepatu wol ini sangat adem dan nyaman untuk dipakai seharian penuh.',
            'is_approved' => true,
        ]);

        $response = $this->get(route('account.reviews.index'));
        $response->assertStatus(200);
        $response->assertSee('Ulasan Produk');
        $response->assertSee('Kenyamanan luar biasa');
        $response->assertSee('Disetujui');
        $response->assertSee($this->product->name);
    }
}

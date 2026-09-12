<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\CartService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class Sprint3Test extends TestCase
{
    use DatabaseTransactions;

    protected User $customer;

    protected Product $product;

    protected ProductVariant $inStockVariant;

    protected ProductVariant $lowStockVariant;

    protected ProductVariant $outOfStockVariant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->withSession(['test_session' => true]);

        $this->customer = User::where('email', 'demo.customer@fifa.test')->first() ?? User::where('role', 'customer')->first() ?? User::factory()->create([
            'role' => 'customer',
            'email' => 'customer_sprint3@fifa.test',
            'password' => bcrypt('password'),
        ]);
        $this->customer->update(['password' => bcrypt('password')]);

        $this->product = Product::with('variants')->where('is_active', true)->first();

        // Ensure inStockVariant (stock = 10)
        $this->inStockVariant = $this->product->variants->first() ?? ProductVariant::create([
            'product_id' => $this->product->id,
            'color_name' => 'Natural Black',
            'color_hex' => '#212121',
            'size' => '42',
            'sku' => 'TEST-SKU-42',
            'stock' => 10,
        ]);
        $this->inStockVariant->update(['stock' => 10]);

        // Setup low stock variant (stock = 3)
        $this->lowStockVariant = $this->product->variants->skip(1)->first() ?? ProductVariant::create([
            'product_id' => $this->product->id,
            'color_name' => 'Natural Black',
            'color_hex' => '#212121',
            'size' => '43',
            'sku' => 'TEST-SKU-43',
            'stock' => 3,
        ]);
        $this->lowStockVariant->update(['stock' => 3]);

        // Setup out-of-stock variant (stock = 0)
        $this->outOfStockVariant = $this->product->variants->skip(2)->first() ?? ProductVariant::create([
            'product_id' => $this->product->id,
            'color_name' => 'Natural Black',
            'color_hex' => '#212121',
            'size' => '44',
            'sku' => 'TEST-SKU-44',
            'stock' => 0,
        ]);
        $this->outOfStockVariant->update(['stock' => 0]);
    }

    /*
    |--------------------------------------------------------------------------
    | Guest & User Cart Operations
    |--------------------------------------------------------------------------
    */

    public function test_guest_can_add_item_to_session_cart(): void
    {
        $session = ['session_token' => 'guest_add_test'];

        $response = $this->withSession($session)->postJson('/cart/add', [
            'product_variant_id' => $this->inStockVariant->id,
            'qty' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cart' => [
                'total_qty' => 1,
            ],
        ]);

        // Cart JSON data endpoint with same session
        $dataRes = $this->withSession($session)->getJson('/cart/data');
        $dataRes->assertStatus(200);
        $dataRes->assertJsonFragment([
            'variant_id' => $this->inStockVariant->id,
            'qty' => 1,
        ]);
    }

    public function test_cart_validates_stock_limit_on_add(): void
    {
        $session = ['session_token' => 'stock_limit_test'];

        // 1. Out of stock variant cannot be added
        if ($this->outOfStockVariant) {
            $resOut = $this->withSession($session)->postJson('/cart/add', [
                'product_variant_id' => $this->outOfStockVariant->id,
                'qty' => 1,
            ]);
            $resOut->assertStatus(422);
            $resOut->assertJsonFragment(['success' => false]);
        }

        // 2. Cannot add more than available stock (lowStockVariant stock = 3, requesting 4)
        if ($this->lowStockVariant) {
            $resOver = $this->withSession($session)->postJson('/cart/add', [
                'product_variant_id' => $this->lowStockVariant->id,
                'qty' => 4,
            ]);
            $resOver->assertStatus(422);
            $resOver->assertJsonFragment(['success' => false]);

            // Adding within stock succeeds (3 items)
            $resOk = $this->withSession($session)->postJson('/cart/add', [
                'product_variant_id' => $this->lowStockVariant->id,
                'qty' => 3,
            ]);
            $resOk->assertStatus(200);

            // Adding 1 more when already having 3 in cart fails
            $resAddMore = $this->withSession($session)->postJson('/cart/add', [
                'product_variant_id' => $this->lowStockVariant->id,
                'qty' => 1,
            ]);
            $resAddMore->assertStatus(422);
        }
    }

    public function test_cart_item_update_and_removal(): void
    {
        $session = ['session_token' => 'update_remove_test'];

        // Add item first
        $addRes = $this->withSession($session)->postJson('/cart/add', [
            'product_variant_id' => $this->inStockVariant->id,
            'qty' => 1,
        ]);
        $addRes->assertStatus(200);

        $itemId = $addRes->json('cart.items.0.id');
        $this->assertNotNull($itemId);

        // Update quantity to 2
        $updateRes = $this->withSession($session)->patchJson("/cart/items/{$itemId}", [
            'qty' => 2,
        ]);
        $updateRes->assertStatus(200);
        $updateRes->assertJsonPath('cart.total_qty', 2);

        // Update quantity to 0 removes the item
        $removeZeroRes = $this->withSession($session)->patchJson("/cart/items/{$itemId}", [
            'qty' => 0,
        ]);
        $removeZeroRes->assertStatus(200);
        $removeZeroRes->assertJsonPath('cart.total_qty', 0);

        // Re-add and test Delete endpoint
        $reAdd = $this->withSession($session)->postJson('/cart/add', [
            'product_variant_id' => $this->inStockVariant->id,
            'qty' => 1,
        ]);
        $newItemId = $reAdd->json('cart.items.0.id');

        $delRes = $this->withSession($session)->deleteJson("/cart/items/{$newItemId}");
        $delRes->assertStatus(200);
        $this->assertDatabaseMissing('cart_items', ['id' => $newItemId]);
    }

    public function test_cart_free_shipping_progress_calculation(): void
    {
        $service = app(CartService::class);
        $cart = Cart::create(['session_id' => 'test_calc_session']);

        // Item with price 200.000, qty 1 -> subtotal 200.000 (under 500k)
        $this->inStockVariant->update(['price_override' => 200000]);
        $cart->items()->create(['product_variant_id' => $this->inStockVariant->id, 'qty' => 1]);

        $summary = $service->getCartSummary($cart);
        $this->assertEquals(200000, $summary['subtotal']);
        $this->assertEquals(300000, $summary['remaining_free_shipping']);
        $this->assertFalse($summary['is_free_shipping']);
        $this->assertEquals(40, $summary['free_shipping_percent']);

        // Increase qty to 3 -> subtotal 600.000 (over 500k threshold)
        $cart->items()->first()->update(['qty' => 3]);
        $summary2 = $service->getCartSummary($cart->fresh());
        $this->assertEquals(600000, $summary2['subtotal']);
        $this->assertEquals(0, $summary2['remaining_free_shipping']);
        $this->assertTrue($summary2['is_free_shipping']);
        $this->assertEquals(100, $summary2['free_shipping_percent']);
    }

    public function test_guest_cart_auto_merges_to_user_account_on_login(): void
    {
        $session = ['session_token' => 'merge_login_test'];

        // 1. Guest adds inStockVariant (qty 2) to session cart
        $addRes = $this->withSession($session)->postJson('/cart/add', [
            'product_variant_id' => $this->inStockVariant->id,
            'qty' => 2,
        ]);
        $addRes->assertStatus(200);
        $guestCartId = $addRes->json('cart.id');

        // 2. User already had inStockVariant (qty 1) in their account cart
        $userCart = Cart::create(['user_id' => $this->customer->id]);
        $userCart->items()->create([
            'product_variant_id' => $this->inStockVariant->id,
            'qty' => 1,
        ]);

        // 3. User logs in with the same session
        $loginRes = $this->withSession($session)->post('/login', [
            'email' => $this->customer->email,
            'password' => 'password',
        ]);
        $loginRes->assertRedirect();

        // 4. Verify merge: user cart should now have qty = 3 (1 + 2) in a single row
        $userCart = Cart::where('user_id', $this->customer->id)->with('items')->first();
        $this->assertNotNull($userCart);
        $this->assertEquals(1, $userCart->items->count()); // No duplicate rows
        $this->assertEquals(3, $userCart->items->first()->qty);

        // 5. Guest cart is cleaned up
        $this->assertDatabaseMissing('carts', ['id' => $guestCartId]);
    }

    /*
    |--------------------------------------------------------------------------
    | Wishlist Feature Tests
    |--------------------------------------------------------------------------
    */

    public function test_guest_toggling_wishlist_redirects_to_login_with_context(): void
    {
        // Web request
        $response = $this->from('/products/'.$this->product->slug)
            ->post('/wishlist/toggle/'.$this->product->id);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('warning');
        $this->assertEquals(url('/products/'.$this->product->slug), session('url.intended'));

        // JSON AJAX request
        $jsonRes = $this->postJson('/wishlist/toggle/'.$this->product->id);
        $jsonRes->assertStatus(401);
        $jsonRes->assertJson([
            'success' => false,
            'requires_auth' => true,
        ]);
    }

    public function test_authenticated_customer_can_toggle_wishlist(): void
    {
        $this->actingAs($this->customer);

        // 1. Add to wishlist
        $addRes = $this->postJson('/wishlist/toggle/'.$this->product->id);
        $addRes->assertStatus(200);
        $addRes->assertJson([
            'success' => true,
            'in_wishlist' => true,
        ]);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
        ]);

        // 2. Remove from wishlist
        $removeRes = $this->postJson('/wishlist/toggle/'.$this->product->id);
        $removeRes->assertStatus(200);
        $removeRes->assertJson([
            'success' => true,
            'in_wishlist' => false,
        ]);
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
        ]);
    }

    public function test_full_cart_and_wishlist_pages_render_correctly(): void
    {
        // 1. /cart page
        $cartRes = $this->get('/cart');
        $cartRes->assertStatus(200);
        $cartRes->assertSee('Keranjang Belanja');

        // 2. /account/wishlist page (Protected)
        $guestWishlistRes = $this->get('/account/wishlist');
        $guestWishlistRes->assertRedirect(route('login'));

        $this->actingAs($this->customer);
        Wishlist::create(['user_id' => $this->customer->id, 'product_id' => $this->product->id]);

        $authWishlistRes = $this->get('/account/wishlist');
        $authWishlistRes->assertStatus(200);
        $authWishlistRes->assertSee('Wishlist Saya');
        $authWishlistRes->assertSee($this->product->name);
    }
}

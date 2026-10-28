<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_customer_can_create_an_order_and_view_payment_status(): void
    {
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
        ]);
        $product = Product::factory()->create([
            'price_per_day' => 10000,
            'stock_total' => 3,
        ]);

        $response = $this->actingAs($customer)
            ->withSession(['cart' => [
                $product->id => ['product_id' => $product->id, 'quantity' => 2],
            ]])
            ->get(route('checkout'));

        $response->assertOk()
            ->assertSee('Rp10.000')
            ->assertSee('Rp20.000');

        $response = $this->actingAs($customer)
            ->post(route('checkout.store'), [
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(4)->toDateString(),
                'pickup_method' => 'pickup',
            ]);

        $response->assertSessionHasNoErrors();
        $rental = Rental::query()->firstOrFail();

        $response->assertRedirect(route('orders.show', $rental->order_number));
        $this->assertDatabaseHas('rentals', [
            'id' => $rental->id,
            'subtotal' => 60000,
            'grand_total' => 60000,
        ]);
        $this->assertDatabaseHas('payments', [
            'rental_id' => $rental->id,
            'amount' => 60000,
            'method' => 'manual',
            'status' => 'pending',
        ]);
        $response->assertSessionMissing('cart');

        $this->get(route('orders.show', $rental->order_number))
            ->assertOk()
            ->assertSee('Status pembayaran')
            ->assertSee('Rp10.000')
            ->assertSee('Rp60.000')
            ->assertDontSee('Deposit')
            ->assertSee('Pembayaran online belum tersedia.');
    }

    public function test_customer_must_complete_contact_and_address_before_checkout(): void
    {
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
            'phone' => null,
            'address' => null,
            'city' => null,
            'kecamatan' => null,
        ]);
        $product = Product::factory()->create();

        $response = $this->actingAs($customer)
            ->withSession(['cart' => [
                $product->id => ['product_id' => $product->id, 'quantity' => 1],
            ]])
            ->post(route('checkout.store'), [
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'pickup_method' => 'pickup',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('warning');
        $this->assertDatabaseCount('rentals', 0);
    }

    public function test_delivery_order_requires_an_address(): void
    {
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
        ]);
        $product = Product::factory()->create();

        $response = $this->actingAs($customer)
            ->from(route('checkout'))
            ->withSession(['cart' => [
                $product->id => ['product_id' => $product->id, 'quantity' => 1],
            ]])
            ->post(route('checkout.store'), [
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'pickup_method' => 'delivery',
            ]);

        $response->assertRedirect(route('checkout'));
        $response->assertSessionHasErrors('address');
        $this->assertDatabaseCount('rentals', 0);
    }

    public function test_delivery_order_requires_gps_and_complete_address_details(): void
    {
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
        ]);
        $product = Product::factory()->create();

        $response = $this->actingAs($customer)
            ->from(route('checkout'))
            ->withSession(['cart' => [
                $product->id => ['product_id' => $product->id, 'quantity' => 1],
            ]])
            ->post(route('checkout.store'), [
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'pickup_method' => 'delivery',
                'address' => 'Jl. Cendana, Coblong',
                'delivery_rt_rw' => '002/005',
                'delivery_house_number' => '17',
            ]);

        $response->assertRedirect(route('checkout'));
        $response->assertSessionHasErrors(['delivery_latitude', 'delivery_longitude']);
        $this->assertDatabaseCount('rentals', 0);
    }

    public function test_delivery_order_stores_address_and_shared_gps_snapshot(): void
    {
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
        ]);
        $product = Product::factory()->create();

        $response = $this->actingAs($customer)
            ->withSession(['cart' => [
                $product->id => ['product_id' => $product->id, 'quantity' => 1],
            ]])
            ->post(route('checkout.store'), [
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'pickup_method' => 'delivery',
                'address' => 'Jl. Cendana, Coblong',
                'delivery_rt_rw' => '002/005',
                'delivery_house_number' => '17A',
                'delivery_latitude' => '-6.9000001',
                'delivery_longitude' => '107.6000002',
            ]);

        $response->assertSessionHasNoErrors();
        $rental = Rental::query()->firstOrFail();

        $this->assertDatabaseHas('rentals', [
            'id' => $rental->id,
            'notes' => 'Jl. Cendana, Coblong',
            'delivery_rt_rw' => '002/005',
            'delivery_house_number' => '17A',
            'delivery_latitude' => '-6.9000001',
            'delivery_longitude' => '107.6000002',
        ]);
    }

    public function test_delivery_order_rejects_gps_coordinates_outside_geographic_ranges(): void
    {
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
        ]);
        $product = Product::factory()->create();

        $response = $this->actingAs($customer)
            ->from(route('checkout'))
            ->withSession(['cart' => [
                $product->id => ['product_id' => $product->id, 'quantity' => 1],
            ]])
            ->post(route('checkout.store'), [
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'pickup_method' => 'delivery',
                'address' => 'Jl. Cendana, Coblong',
                'delivery_rt_rw' => '002/005',
                'delivery_house_number' => '17',
                'delivery_latitude' => '91',
                'delivery_longitude' => '107.6',
            ]);

        $response->assertRedirect(route('checkout'));
        $response->assertSessionHasErrors('delivery_latitude');
        $this->assertDatabaseCount('rentals', 0);
    }

    public function test_checkout_rejects_quantities_that_exceed_available_stock_for_the_selected_dates(): void
    {
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
        ]);
        $product = Product::factory()->create(['stock_total' => 3]);
        $startDate = now()->addDays(3)->toDateString();
        $endDate = now()->addDays(5)->toDateString();
        $existingRental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'paid',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        RentalItem::factory()->for($existingRental)->for($product)->create(['quantity' => 2]);

        $response = $this->actingAs($customer)
            ->from(route('checkout'))
            ->withSession(['cart' => [
                $product->id => ['product_id' => $product->id, 'quantity' => 2],
            ]])
            ->post(route('checkout.store'), [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'pickup_method' => 'pickup',
            ]);

        $response->assertRedirect(route('checkout'));
        $response->assertSessionHasErrors('quantity');
        $this->assertDatabaseCount('rentals', 1);
    }
}

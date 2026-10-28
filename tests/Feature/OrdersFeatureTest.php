<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_their_orders(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $otherCustomer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);

        Rental::factory()->for($customer, 'customer')->create(['status' => 'pending']);
        $otherRental = Rental::factory()->for($otherCustomer, 'customer')->create(['status' => 'paid']);

        $response = $this->actingAs($customer)->get('/orders');

        $response->assertOk();
        $response->assertDontSee($otherRental->order_number);
    }

    public function test_guest_cannot_view_orders(): void
    {
        $this->get('/orders')->assertRedirect('/login');
    }

    public function test_customer_can_view_order_detail(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $rental = Rental::factory()->for($customer, 'customer')->create();

        $response = $this->actingAs($customer)->get('/orders/'.$rental->order_number);

        $response->assertOk();
        $response->assertSee($rental->order_number);
    }

    public function test_customer_sees_expired_payment_status_and_deadline(): void
    {
        $this->travelTo('2026-10-27 12:00:00');
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'pending',
            'pickup_method' => 'pickup',
        ]);
        Payment::factory()->for($rental)->create([
            'status' => 'pending',
            'expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($customer)
            ->get(route('orders.show', $rental->order_number))
            ->assertOk()
            ->assertSee('Pembayaran Kedaluwarsa')
            ->assertSee('Batas waktu pembayaran telah habis');
    }

    public function test_customer_cannot_view_another_order(): void
    {
        $owner = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $intruder = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $rental = Rental::factory()->for($owner, 'customer')->create();

        $this->actingAs($intruder)->get('/orders/'.$rental->order_number)->assertNotFound();
    }

    public function test_customer_can_cancel_a_pending_order(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'pending',
            'pickup_method' => 'pickup',
        ]);

        $response = $this->actingAs($customer)->post("/orders/{$rental->order_number}/cancel");

        $response->assertRedirect(route('orders.index'));
        $this->assertSame('cancelled', $rental->refresh()->status);
    }

    public function test_customer_cannot_cancel_a_completed_order(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $rental = Rental::factory()->for($customer, 'customer')->create(['status' => 'completed']);

        $this->actingAs($customer)->post("/orders/{$rental->order_number}/cancel")->assertConflict();
        $this->assertSame('completed', $rental->refresh()->status);
    }

    public function test_admin_dashboard_shows_live_metrics(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value, 'verification_status' => 'pending']);
        Rental::factory()->for($customer, 'customer')->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_staff_dashboard_renders(): void
    {
        $staff = User::factory()->create(['role' => UserRole::STAFF->value]);

        $this->actingAs($staff)->get('/staff')->assertOk();
    }

    public function test_admin_can_update_rental_status(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'pending',
            'pickup_method' => 'pickup',
        ]);
        $payment = Payment::factory()->for($rental)->create(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->patch("/rentals/{$rental->id}/status", ['status' => 'paid']);

        $response->assertRedirect();
        $this->assertSame('paid', $rental->refresh()->status);
        $this->assertSame('paid', $payment->refresh()->status);
    }

    public function test_staff_cannot_access_admin_dashboard(): void
    {
        $staff = User::factory()->create(['role' => UserRole::STAFF->value]);

        $this->actingAs($staff)->get('/admin')->assertForbidden();
    }

    public function test_pages_are_accessible_to_guests(): void
    {
        $this->get('/pages/terms')->assertOk()->assertDontSee('Deposit');
        $this->get('/pages/privacy')->assertOk();
        $this->get('/pages/contact')->assertOk();
    }
}

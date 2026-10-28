<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_confirming_payment_marks_the_payment_as_paid(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'pending',
            'pickup_method' => 'pickup',
        ]);
        $payment = Payment::factory()->for($rental)->create(['status' => 'pending']);

        $response = $this->actingAs($admin)->patch(route('rentals.update-status', $rental), [
            'status' => 'paid',
        ]);

        $response->assertRedirect();
        $this->assertSame('paid', $rental->refresh()->status);
        $this->assertSame('paid', $payment->refresh()->status);
        $this->assertNotNull($payment->paid_at);
    }

    public function test_admin_can_view_rental_processing_controls(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'pending',
            'pickup_method' => 'delivery',
        ]);
        Payment::factory()->for($rental)->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->get(route('rentals.show', $rental))
            ->assertOk()
            ->assertSee('Jarak pengantaran terverifikasi (km)')
            ->assertSee('Konfirmasi pembayaran');
    }

    public function test_admin_rental_detail_shows_delivery_address_and_map_link(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'pickup_method' => 'delivery',
            'notes' => 'Jl. Cendana, Coblong',
            'delivery_rt_rw' => '002/005',
            'delivery_house_number' => '17A',
            'delivery_latitude' => '-6.9000001',
            'delivery_longitude' => '107.6000002',
        ]);

        $this->actingAs($admin)
            ->get(route('rentals.show', $rental))
            ->assertOk()
            ->assertSee('002/005')
            ->assertSee('17A')
            ->assertSee('Buka lokasi di peta')
            ->assertSee('https://www.google.com/maps?q=-6.9000001,107.6000002', false);
    }

    public function test_admin_rental_list_renders_and_marks_expired_payments(): void
    {
        $this->travelTo('2026-10-27 12:00:00');
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'pending',
            'pickup_method' => 'pickup',
        ]);
        Payment::factory()->for($rental)->create([
            'status' => 'pending',
            'expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($admin)
            ->get(route('rentals.index'))
            ->assertOk()
            ->assertSee($rental->order_number)
            ->assertSee('Pembayaran Kedaluwarsa')
            ->assertSee('Hapus Pesanan Terpilih')
            ->assertSee('Pilih semua pesanan yang dapat dihapus di halaman ini');
    }

    public function test_admin_can_bulk_delete_a_rental_with_an_expired_pending_payment(): void
    {
        $this->travelTo('2026-10-27 12:00:00');
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $rental = Rental::factory()->for($customer, 'customer')->create(['status' => 'pending']);
        $payment = Payment::factory()->for($rental)->create([
            'status' => 'pending',
            'expires_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('rentals.bulk-destroy'), ['rental_ids' => [$rental->id]])
            ->assertRedirect()
            ->assertSessionHas('status', '1 pesanan beserta detail dan pembayarannya berhasil dihapus permanen.');

        $this->assertDatabaseMissing('rentals', ['id' => $rental->id]);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
    }

    public function test_admin_bulk_delete_rejects_a_pending_rental_with_an_unexpired_payment(): void
    {
        $this->travelTo('2026-10-27 12:00:00');
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $rental = Rental::factory()->for($customer, 'customer')->create(['status' => 'pending']);
        Payment::factory()->for($rental)->create([
            'status' => 'pending',
            'expires_at' => now()->addMinute(),
        ]);

        $this->actingAs($admin)
            ->from(route('rentals.index'))
            ->delete(route('rentals.bulk-destroy'), ['rental_ids' => [$rental->id]])
            ->assertRedirect(route('rentals.index'))
            ->assertSessionHasErrors('rental_ids');

        $this->assertModelExists($rental);
    }

    public function test_admin_can_bulk_delete_selected_terminal_rentals_and_related_records(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $rental = Rental::factory()->for($customer, 'customer')->create(['status' => 'completed']);
        $item = RentalItem::factory()->for($rental)->create();
        $payment = Payment::factory()->for($rental)->create(['status' => 'paid']);

        $response = $this->actingAs($admin)->delete(route('rentals.bulk-destroy'), [
            'rental_ids' => [$rental->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', '1 pesanan beserta detail dan pembayarannya berhasil dihapus permanen.');
        $this->assertDatabaseMissing('rentals', ['id' => $rental->id]);
        $this->assertDatabaseMissing('rental_items', ['id' => $item->id]);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
    }

    public function test_admin_bulk_delete_rejects_active_rentals_without_deleting_any_selection(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $completedRental = Rental::factory()->for($customer, 'customer')->create(['status' => 'completed']);
        $activeRental = Rental::factory()->for($customer, 'customer')->create(['status' => 'in_use']);

        $response = $this->actingAs($admin)->from(route('rentals.index'))
            ->delete(route('rentals.bulk-destroy'), [
                'rental_ids' => [$completedRental->id, $activeRental->id],
            ]);

        $response->assertRedirect(route('rentals.index'));
        $response->assertSessionHasErrors('rental_ids');
        $this->assertModelExists($completedRental);
        $this->assertModelExists($activeRental);
    }

    public function test_staff_cannot_bulk_delete_rentals(): void
    {
        $staff = User::factory()->create(['role' => UserRole::STAFF]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $rental = Rental::factory()->for($customer, 'customer')->create(['status' => 'cancelled']);

        $this->actingAs($staff)->delete(route('rentals.bulk-destroy'), [
            'rental_ids' => [$rental->id],
        ])->assertForbidden();

        $this->assertModelExists($rental);
    }

    public function test_staff_rental_list_does_not_show_bulk_delete_controls(): void
    {
        $staff = User::factory()->create(['role' => UserRole::STAFF]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        Rental::factory()->for($customer, 'customer')->create(['status' => 'completed']);

        $this->actingAs($staff)
            ->get(route('rentals.index'))
            ->assertOk()
            ->assertDontSee('Hapus Pesanan Terpilih');
    }

    public function test_bulk_delete_rejects_selected_rentals_outside_the_visible_page(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        Rental::factory()->count(15)->for($customer, 'customer')->create(['status' => 'completed']);
        $rentalOnSecondPage = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'completed',
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($admin)->from(route('rentals.index'))
            ->delete(route('rentals.bulk-destroy', ['page' => 1]), [
                'rental_ids' => [$rentalOnSecondPage->id],
            ]);

        $response->assertRedirect(route('rentals.index'));
        $response->assertSessionHasErrors('rental_ids');
        $this->assertModelExists($rentalOnSecondPage);
    }

    public function test_admin_cannot_confirm_payment_after_its_expiration(): void
    {
        $this->travelTo('2026-10-27 12:00:00');
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'pending',
            'pickup_method' => 'pickup',
        ]);
        $payment = Payment::factory()->for($rental)->create([
            'status' => 'pending',
            'expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($admin)
            ->patch(route('rentals.update-status', $rental), ['status' => 'paid'])
            ->assertConflict();

        $this->assertSame('pending', $rental->refresh()->status);
        $this->assertSame('pending', $payment->refresh()->status);
    }

    public function test_staff_can_open_rental_details_from_the_rental_list(): void
    {
        $staff = User::factory()->create(['role' => UserRole::STAFF]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'pending',
            'pickup_method' => 'delivery',
        ]);
        Payment::factory()->for($rental)->create(['status' => 'pending']);

        $this->actingAs($staff)
            ->get(route('staff.rentals'))
            ->assertOk()
            ->assertSee(route('rentals.show', $rental));

        $this->get(route('rentals.show', $rental))
            ->assertOk()
            ->assertSee('Jarak pengantaran terverifikasi (km)');
    }

    public function test_admin_confirmation_applies_verified_delivery_distance_and_fee(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        Setting::query()->create([
            'store_name' => 'GHAZI OUTDOOR',
            'free_delivery_km' => 3,
            'fee_per_km' => 5000,
            'max_delivery_km' => 20,
        ]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'pending',
            'pickup_method' => 'delivery',
            'subtotal' => 60000,
        ]);
        $payment = Payment::factory()->for($rental)->create(['amount' => 60000, 'status' => 'pending']);

        $response = $this->actingAs($admin)->patch(route('rentals.update-status', $rental), [
            'status' => 'paid',
            'delivery_distance_km' => 5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rentals', [
            'id' => $rental->id,
            'delivery_distance_km' => 5,
            'delivery_fee' => 10000,
            'grand_total' => 70000,
            'status' => 'paid',
        ]);
        $this->assertSame(70000, $payment->refresh()->amount);
    }

    public function test_delivery_distance_cannot_exceed_store_limit(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        Setting::query()->create([
            'store_name' => 'GHAZI OUTDOOR',
            'max_delivery_km' => 10,
        ]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'pending',
            'pickup_method' => 'delivery',
        ]);
        Payment::factory()->for($rental)->create(['status' => 'pending']);

        $response = $this->actingAs($admin)->from(route('rentals.show', $rental))
            ->patch(route('rentals.update-status', $rental), [
                'status' => 'paid',
                'delivery_distance_km' => 10.01,
            ]);

        $response->assertRedirect(route('rentals.show', $rental));
        $response->assertSessionHasErrors('delivery_distance_km');
        $this->assertSame('pending', $rental->refresh()->status);
    }

    public function test_admin_can_cancel_delivery_without_entering_a_distance(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'pending',
            'pickup_method' => 'delivery',
        ]);
        $payment = Payment::factory()->for($rental)->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->patch(route('rentals.update-status', $rental), ['status' => 'cancelled'])
            ->assertRedirect();

        $this->assertSame('cancelled', $rental->refresh()->status);
        $this->assertSame('cancelled', $payment->refresh()->status);
    }

    public function test_staff_can_return_late_rental_and_system_calculates_fine_per_unit_per_day(): void
    {
        $this->travelTo('2026-10-27 12:00:00');
        $staff = User::factory()->create(['role' => UserRole::STAFF]);
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        Setting::query()->create([
            'store_name' => 'GHAZI OUTDOOR',
            'denda_per_hari' => 25000,
        ]);
        $rental = Rental::factory()->for($customer, 'customer')->create([
            'status' => 'in_use',
            'start_date' => '2026-10-20',
            'end_date' => '2026-10-25',
            'subtotal' => 60000,
            'grand_total' => 60000,
        ]);
        RentalItem::factory()->for($rental)->create(['quantity' => 3]);
        $payment = Payment::factory()->for($rental)->create([
            'amount' => 60000,
            'status' => 'paid',
            'paid_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($staff)->post(route('rentals.return', $rental));

        $response->assertRedirect();
        $this->assertDatabaseHas('rentals', [
            'id' => $rental->id,
            'status' => 'returned',
            'late_days' => 2,
            'late_fee_total' => 150000,
        ]);
        $this->assertSame(210000, $payment->refresh()->amount);
        $this->assertSame('pending', $payment->status);

        $this->post(route('rentals.payment.confirm', $rental))
            ->assertRedirect();
        $this->assertSame('paid', $payment->refresh()->status);
    }
}

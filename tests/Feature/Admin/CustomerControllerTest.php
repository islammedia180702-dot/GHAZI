<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_only_verified_customer_accounts(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $verifiedCustomer = User::factory()->create([
            'name' => 'Pelanggan Terverifikasi',
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
        ]);
        User::factory()->create([
            'name' => 'Pelanggan Belum Terverifikasi',
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'pending',
        ]);
        User::factory()->create([
            'name' => 'Staff Terverifikasi',
            'role' => UserRole::STAFF,
            'verification_status' => 'verified',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee($verifiedCustomer->name)
            ->assertSee('Pelanggan')
            ->assertDontSee('Pelanggan Belum Terverifikasi')
            ->assertDontSee('Staff Terverifikasi');
    }

    public function test_admin_can_create_a_verified_customer_account(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.customers.create'))
            ->assertOk()
            ->assertSee('Tambah Pelanggan Terverifikasi');

        $response = $this->post(route('admin.customers.store'), [
            'name' => 'Pelanggan Baru',
            'email' => 'baru@example.com',
            'phone' => '08123456789',
            'address' => 'Jl. Mawar No. 10, RT 001/RW 002',
            'city' => 'Bandung',
            'kecamatan' => 'Coblong',
            'password' => 'customer-password',
            'password_confirmation' => 'customer-password',
            'role' => UserRole::ADMIN->value,
            'verification_status' => 'pending',
        ]);

        $response->assertRedirect(route('admin.customers.index'));

        $customer = User::query()->where('email', 'baru@example.com')->firstOrFail();
        $this->assertSame(UserRole::CUSTOMER, $customer->role);
        $this->assertSame('verified', $customer->verification_status);
        $this->assertSame($admin->id, $customer->verified_by);
        $this->assertNotNull($customer->verified_at);
        $this->assertTrue(Hash::check('customer-password', $customer->password));
    }

    public function test_admin_can_view_and_update_a_verified_customer_account(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create([
            'name' => 'Nama Lama',
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
            'verified_by' => $admin->id,
        ]);
        $previousPassword = $customer->password;

        $this->actingAs($admin)
            ->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSee('Nama Lama')
            ->assertSee($customer->email);

        $this->get(route('admin.customers.edit', $customer))
            ->assertOk()
            ->assertSee('Ubah Akun Pelanggan');

        $response = $this->put(route('admin.customers.update', $customer), [
            'name' => 'Nama Baru',
            'email' => $customer->email,
            'phone' => '08129876543',
            'address' => 'Jl. Baru No. 5',
            'city' => 'Bandung',
            'kecamatan' => 'Sukajadi',
            'role' => UserRole::ADMIN->value,
            'verification_status' => 'pending',
            'verified_by' => null,
        ]);

        $response->assertRedirect(route('admin.customers.index'));
        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'name' => 'Nama Baru',
            'phone' => '08129876543',
            'verification_status' => 'verified',
            'role' => UserRole::CUSTOMER->value,
            'verified_by' => $admin->id,
        ]);
        $this->assertSame($previousPassword, $customer->fresh()->password);
    }

    public function test_admin_can_delete_a_verified_customer_without_rentals(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.customers.destroy', $customer));

        $response->assertRedirect(route('admin.customers.index'));
        $this->assertModelMissing($customer);
    }

    public function test_customer_with_rentals_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
        ]);
        $rental = Rental::factory()->for($customer, 'customer')->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.customers.index'))
            ->delete(route('admin.customers.destroy', $customer));

        $response->assertRedirect(route('admin.customers.index'));
        $response->assertSessionHasErrors('customer');
        $this->assertModelExists($customer);
        $this->assertModelExists($rental);
    }

    public function test_admin_cannot_manage_unverified_customers_or_other_roles_as_customers(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $unverifiedCustomer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'pending',
        ]);
        $staff = User::factory()->create([
            'role' => UserRole::STAFF,
            'verification_status' => 'verified',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.customers.edit', $unverifiedCustomer))
            ->assertNotFound();

        $this->get(route('admin.customers.show', $staff))
            ->assertNotFound();
    }

    public function test_customer_management_is_forbidden_to_staff_and_redirects_guests(): void
    {
        $staff = User::factory()->create(['role' => UserRole::STAFF]);

        $this->actingAs($staff)
            ->get(route('admin.customers.index'))
            ->assertForbidden();

        auth()->logout();

        $this->get(route('admin.customers.index'))
            ->assertRedirect(route('login'));
    }

    public function test_duplicate_email_is_rejected_when_creating_customer(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $existingCustomer = User::factory()->create([
            'email' => 'existing@example.com',
            'role' => UserRole::CUSTOMER,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), [
                'name' => 'Pelanggan Duplikat',
                'email' => $existingCustomer->email,
                'password' => 'customer-password',
                'password_confirmation' => 'customer-password',
            ]);

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('email');
        $this->assertSame(1, User::query()->where('email', 'existing@example.com')->count());
    }

    public function test_admin_cannot_create_customer_without_phone_or_complete_address(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), [
                'name' => 'Pelanggan Baru',
                'email' => 'baru@example.com',
                'phone' => '',
                'address' => '',
                'city' => '',
                'kecamatan' => '',
                'password' => 'customer-password',
                'password_confirmation' => 'customer-password',
            ]);

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors(['phone', 'address', 'city', 'kecamatan']);
        $this->assertDatabaseMissing('users', ['email' => 'baru@example.com']);
    }

    public function test_admin_cannot_remove_phone_or_complete_address_from_customer_account(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.customers.edit', $customer))
            ->put(route('admin.customers.update', $customer), [
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => '',
                'address' => '',
                'city' => '',
                'kecamatan' => '',
            ]);

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors(['phone', 'address', 'city', 'kecamatan']);
        $this->assertSame($customer->phone, $customer->fresh()->phone);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    public function test_staff_can_access_staff_dashboard(): void
    {
        $staff = User::factory()->create([
            'role' => UserRole::STAFF->value,
        ]);

        $response = $this->actingAs($staff)->get('/staff');

        $response->assertOk();
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER->value,
        ]);

        $response = $this->actingAs($customer)->get('/admin');

        $response->assertForbidden();
    }

    public function test_register_defaults_new_user_to_customer_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Customer Test',
            'email' => 'customer.test@example.com',
            'phone' => '08123456789',
            'address' => 'Jl. Cendana No. 17, RT 002/RW 005',
            'city' => 'Bandung',
            'kecamatan' => 'Coblong',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'customer.test@example.com',
            'role' => UserRole::CUSTOMER->value,
        ]);
    }

    public function test_customer_can_submit_identity_verification_files(): void
    {
        Storage::fake('local');

        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER->value,
            'verification_status' => 'unverified',
        ]);

        $response = $this->actingAs($customer)->post('/verification', [
            'nik' => '3201010101010001',
            'ktp_image' => UploadedFile::fake()->image('ktp.jpg', 600, 400),
            'selfie_image' => UploadedFile::fake()->image('selfie.jpg', 600, 400),
            'agreement' => '1',
        ]);

        $response->assertRedirect(route('verification.index'));
        $customer->refresh();
        $this->assertSame('pending', $customer->verification_status);
        $this->assertNotNull($customer->ktp_image);
    }

    public function test_admin_can_view_verification_queue(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $response = $this->actingAs($admin)->get('/admin/verifications');

        $response->assertOk();
    }

    public function test_admin_can_see_verified_customers_and_their_verifier_by_account(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);
        $verifier = User::factory()->create([
            'name' => 'Verifikasi Admin',
            'role' => UserRole::ADMIN->value,
        ]);
        $customer = User::factory()->create([
            'name' => 'Pelanggan Terverifikasi',
            'email' => 'verified.customer@example.com',
            'role' => UserRole::CUSTOMER->value,
            'verification_status' => 'verified',
            'nik' => Crypt::encryptString('3201010101010001'),
            'verified_by' => $verifier->id,
            'verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.verifications'))
            ->assertOk()
            ->assertSee('Pelanggan terverifikasi')
            ->assertSee('verified.customer@example.com')
            ->assertSee('Verifikasi Admin')
            ->assertSee('Terverifikasi')
            ->assertDontSee('3201010101010001');

        $this->assertSame($verifier->id, $customer->refresh()->verifiedBy->id);
    }
}

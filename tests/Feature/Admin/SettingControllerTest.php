<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_update_store_settings(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $setting = Setting::query()->create([
            'store_name' => 'GHAZI OUTDOOR',
            'denda_per_hari' => 25000,
            'pending_expiry_minutes' => 15,
            'free_delivery_km' => 3,
            'fee_per_km' => 5000,
            'max_delivery_km' => 20,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Pengaturan Toko');

        $response = $this->put(route('admin.settings.update'), [
            'store_name' => 'Ghazi Outdoor Bandung',
            'store_address' => 'Jl. Cendana 17',
            'latitude' => '-6.917464',
            'longitude' => '107.619123',
            'whatsapp' => '6281234567890',
            'operational_hours' => '08.00-21.00',
            'denda_per_hari' => 30000,
            'pending_expiry_minutes' => 30,
            'free_delivery_km' => 4,
            'fee_per_km' => 6000,
            'max_delivery_km' => 25,
            'terms' => 'Syarat baru.',
            'privacy_policy' => 'Kebijakan baru.',
        ]);

        $response->assertRedirect(route('admin.settings.edit'));
        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'store_name' => 'Ghazi Outdoor Bandung',
            'denda_per_hari' => 30000,
            'free_delivery_km' => 4,
            'fee_per_km' => 6000,
            'max_delivery_km' => 25,
        ]);
    }

    public function test_store_settings_reject_free_delivery_distance_greater_than_maximum(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->from(route('admin.settings.edit'))
            ->put(route('admin.settings.update'), [
                'store_name' => 'GHAZI OUTDOOR',
                'denda_per_hari' => 25000,
                'pending_expiry_minutes' => 15,
                'free_delivery_km' => 30,
                'fee_per_km' => 5000,
                'max_delivery_km' => 20,
            ]);

        $response->assertRedirect(route('admin.settings.edit'));
        $response->assertSessionHasErrors('free_delivery_km');
        $this->assertDatabaseCount('settings', 0);
    }

    public function test_staff_cannot_change_store_settings(): void
    {
        $staff = User::factory()->create(['role' => UserRole::STAFF]);

        $this->actingAs($staff)->get(route('admin.settings.edit'))->assertForbidden();
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_product_management_screens(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        Product::factory()->create(['name' => 'Tenda Keluarga']);

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Tenda Keluarga')
            ->assertDontSee('Deposit')
            ->assertSee('Tambah Produk');

        $this->get(route('admin.products.create'))
            ->assertOk()
            ->assertDontSee('Deposit')
            ->assertSee('Foto utama');
    }

    public function test_admin_can_create_a_product_with_a_cover_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Tenda Gunung',
            'slug' => 'tenda-gunung',
            'description' => 'Tenda untuk empat orang.',
            'price_per_day' => 45000,
            'damage_fee' => 200000,
            'stock_total' => 4,
            'is_active' => 1,
            'image' => UploadedFile::fake()->image('tenda.jpg'),
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'slug' => 'tenda-gunung',
            'category_id' => $category->id,
            'stock_total' => 4,
        ]);

        $image = ProductImage::query()->where('is_primary', true)->firstOrFail();
        Storage::disk('public')->assertExists($image->path);
    }

    public function test_admin_can_update_stock_and_archive_a_product(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $product = Product::factory()->create(['stock_total' => 5, 'is_active' => true]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'category_id' => $product->category_id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price_per_day' => $product->price_per_day,
            'damage_fee' => $product->damage_fee,
            'stock_total' => 2,
            'is_active' => 0,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_total' => 2,
            'is_active' => false,
        ]);
    }

    public function test_product_management_is_forbidden_to_staff(): void
    {
        $staff = User::factory()->create(['role' => UserRole::STAFF]);

        $this->actingAs($staff)->get(route('admin.products.index'))->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_product_management(): void
    {
        $this->get(route('admin.products.index'))->assertRedirect(route('login'));
    }

    public function test_invalid_product_data_is_rejected_without_creating_a_product(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->from(route('admin.products.create'))
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Tenda Tidak Valid',
                'slug' => 'tenda-tidak-valid',
                'price_per_day' => -1,
                'damage_fee' => 0,
                'stock_total' => 1,
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.products.create'));
        $response->assertSessionHasErrors('price_per_day');
        $this->assertDatabaseMissing('products', ['slug' => 'tenda-tidak-valid']);
    }
}

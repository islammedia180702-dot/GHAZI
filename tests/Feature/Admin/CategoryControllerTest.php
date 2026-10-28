<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_category_and_see_it_in_management(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Peralatan Masak',
            'slug' => 'peralatan-masak',
            'description' => 'Kompor dan perlengkapan memasak.',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Peralatan Masak',
            'slug' => 'peralatan-masak',
        ]);

        $this->get(route('admin.categories.index'))->assertOk()->assertSee('Peralatan Masak');
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        Product::factory()->for($category)->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.categories.index'))
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHasErrors('category');
        $this->assertModelExists($category);
    }

    public function test_admin_can_delete_an_empty_category(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertModelMissing($category);
    }
}

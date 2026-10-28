<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => CategoryFactory::new(),
            'name' => fake()->words(3, true).' Outdoor',
            'slug' => fake()->unique()->slug(3, '-'),
            'description' => fake()->sentence(),
            'price_per_day' => fake()->numberBetween(5000, 100000),
            'damage_fee' => fake()->numberBetween(75000, 300000),
            'stock_total' => fake()->numberBetween(1, 12),
            'is_active' => true,
            'rating_average' => 4.5,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\RentalItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RentalItem>
 */
class RentalItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rental = RentalFactory::new();

        return [
            'rental_id' => $rental,
            'product_id' => ProductFactory::new(),
            'quantity' => fake()->numberBetween(1, 3),
            'unit_price' => fake()->numberBetween(5000, 100000),
            'days' => fake()->numberBetween(1, 7),
            'subtotal' => fake()->numberBetween(10000, 300000),
        ];
    }
}

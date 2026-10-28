<?php

namespace Database\Factories;

use App\Models\Rental;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = now()->addDays(fake()->numberBetween(1, 14));

        return [
            'user_id' => UserFactory::new(),
            'order_number' => 'GZ-'.fake()->unique()->numerify('##########').'-'.fake()->randomDigit(),
            'status' => 'pending',
            'pickup_method' => fake()->randomElement(['pickup', 'delivery']),
            'start_date' => $startDate,
            'end_date' => $startDate->addDays(fake()->numberBetween(1, 5)),
            'subtotal' => fake()->numberBetween(20000, 500000),
            'delivery_fee' => 0,
            'grand_total' => fake()->numberBetween(50000, 800000),
        ];
    }
}

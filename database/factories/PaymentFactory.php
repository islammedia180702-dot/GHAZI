<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_id' => RentalFactory::new(),
            'amount' => fake()->numberBetween(20000, 500000),
            'method' => 'QRIS',
            'status' => 'pending',
            'reference' => 'PAY-'.fake()->unique()->numerify('####').'-'.now()->timestamp,
            'expires_at' => now()->addMinutes(15),
        ];
    }
}

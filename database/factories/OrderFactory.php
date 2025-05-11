<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'note' => $this->faker->optional()->sentence(),
            'total' => $this->faker->randomFloat(2, 35, 1000),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'delivering', 'delivered', 'completed', 'canceled']),
        ];
    }
}

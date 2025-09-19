<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
final class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payer_a_name' => $this->faker->name(),
            'payer_b_name' => $this->faker->name(),
            'title' => $this->faker->words(3, true),
            'amount' => $this->faker->numberBetween(100, 1000),
            'occurred_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

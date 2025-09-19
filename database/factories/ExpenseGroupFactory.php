<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseGroup>
 */
final class ExpenseGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'period_name' => $this->faker->sentence,
            'payer_a' => $this->faker->name(),
            'payer_b' => $this->faker->name(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ExpenseGroup;
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
            'expense_group_id' => ExpenseGroup::factory(),
            'title' => $this->faker->sentence,
            'amount' => $this->faker->randomFloat(2, 1, 100),
            'occurred_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

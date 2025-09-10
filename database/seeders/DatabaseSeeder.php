<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseGroup;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        ExpenseGroup::factory()->has(Expense::factory()->count(20))->create();
    }
}

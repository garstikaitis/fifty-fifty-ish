<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Expense::factory()->createMany(300);
    }
}

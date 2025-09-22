<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Expense;
use App\Services\ExpenseSplitter;

final class ExportExpenses {
    public function handle()
    {
        $expenses = Expense::all();
        ExpenseSplitter::split($expenses);
    }
}

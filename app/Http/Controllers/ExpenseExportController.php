<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ExportExpenses;
use App\Models\Expense;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExpenseExportController extends Controller
{
    public function __invoke(Request $request, ExportExpenses $action): StreamedResponse
    {
        return $action
            ->withQuery(Expense::query())
            ->withChunkSize(200)
            ->handle();
    }
}

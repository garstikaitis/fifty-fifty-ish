<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

final class ExpenseExportController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(['success' => true]);
    }
}

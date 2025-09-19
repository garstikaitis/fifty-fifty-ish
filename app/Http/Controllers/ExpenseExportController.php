<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ExpenseExportController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(['success' => true]);
    }
}

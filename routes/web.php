<?php

declare(strict_types=1);

use App\Http\Controllers\ExpenseExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', ExpenseExportController::class)->name('expenses.export');

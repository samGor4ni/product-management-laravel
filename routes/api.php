<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Grouping routes that require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Custom Bulk Delete Route
    Route::post('products/bulk-delete', [ProductController::class , 'bulkDelete']);

    // Export Route
    Route::get('products/export', [ProductController::class , 'export']);

    // Standard CRUD
    Route::apiResource('products', ProductController::class , ['as' => 'api']);
});
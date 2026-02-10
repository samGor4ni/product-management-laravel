<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::get('products/export', [ProductController::class , 'export'])->name('products.export');
Route::post('products/bulk-delete', [ProductController::class , 'bulkDelete'])->name('products.bulkDelete');
Route::resource('products', ProductController::class);
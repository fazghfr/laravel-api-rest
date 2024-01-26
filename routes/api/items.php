<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;


Route::middleware('auth:sanctum')->group(function () {
    Route::post('items', [ItemController::class, 'store']);
    Route::put('items/{item}', [ItemController::class, 'update']);
    Route::delete('items/{item}', [ItemController::class, 'destroy']);
});


Route::get('items', [ItemController::class, 'index']);
Route::get('items/{item}', [ItemController::class, 'show']);


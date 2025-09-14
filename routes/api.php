<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/customers', [\App\Http\Controllers\Api\CustomerController::class, 'search']);
Route::get('/orders', [\App\Http\Controllers\Api\OrderController::class, 'index']);
Route::post('/orders', [\App\Http\Controllers\Api\OrderController::class, 'store']);
Route::get('/orders/statistics', [\App\Http\Controllers\Api\OrderController::class, 'statistics']);
Route::put('/orders/{id}', [\App\Http\Controllers\Api\OrderController::class, 'update']);

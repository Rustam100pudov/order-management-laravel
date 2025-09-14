<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderWebController;

// Авторизация
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// Группа защищённых маршрутов
Route::middleware(['auth'])->group(function () {

    // Перенаправление по роли после входа
    Route::get('/', function () {
        $user = auth()->user();

        if ($user->role === 'operator') {
            return redirect()->route('orders.create'); // сразу на форму заказов
        } elseif ($user->role === 'manager') {
            return redirect()->route('orders.index'); // сразу на список заказов
        }

        return view('welcome'); // дефолт
    });

    // Маршруты для оператора
    Route::middleware('role:operator')->group(function () {
        Route::get('/operator', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/operator/orders', [OrderController::class, 'store'])->name('orders.store');
    });

    // Маршруты для руководителя
    Route::middleware('role:manager')->group(function () {
        Route::get('/manager', [OrderWebController::class, 'index'])->name('orders.index');
        Route::get('/manager/stats', [OrderWebController::class, 'stats'])->name('orders.stats');
    });
});

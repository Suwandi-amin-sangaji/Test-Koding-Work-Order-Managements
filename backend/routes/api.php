<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OperatorController;
use App\Http\Controllers\API\WorkOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Berikut adalah tempat untuk mendaftarkan rute API. Semua rute ini
| dimuat oleh RouteServiceProvider dan akan berada di dalam grup "api".
|
*/

// Rute Autentikasi
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Rute untuk Production Manager
Route::prefix('production-manager')->middleware(['auth:sanctum', 'role:Production Manager'])->group(function () {
    Route::apiResource('work-orders', WorkOrderController::class);
    Route::get('list-operators', [WorkOrderController::class, 'listOperators']);
    Route::get('reports', [WorkOrderController::class, 'generateReport']);
});

// Rute untuk Operator
Route::prefix('operator')->middleware(['auth:sanctum', 'role:Operator'])->group(function () {
    Route::get('assigned-work-orders', [OperatorController::class, 'index']);
    Route::get('work-orders/{workOrder}', [OperatorController::class, 'details']);
    Route::put('update-status/{workOrder}', [OperatorController::class, 'updateStatus']);
    Route::get('list-operators', [OperatorController::class, 'listOperators']);
});

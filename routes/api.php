<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UserController;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Protected routes (require Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('user2', [UserController::class,'GetUser']);
    Route::get('user', [UserController::class, 'show']);
    Route::get('users', [UserController::class, 'index']);
    Route::get('properties', [PropertyController::class, 'index']);
    Route::get('applications', [ApplicationController::class, 'index']);


    // Your other protected resources
    Route::apiResource('properties', PropertyController::class);
    Route::apiResource('tenants', \App\Http\Controllers\TenantController::class);
    Route::apiResource('admins', \App\Http\Controllers\AdminController::class);
    Route::apiResource('landlords', \App\Http\Controllers\LandlordController::class);
    Route::apiResource('applications', \App\Http\Controllers\ApplicationController::class);
    Route::apiResource('rental-contracts', \App\Http\Controllers\RentalContractController::class);
    Route::apiResource('favorites', \App\Http\Controllers\FavoriteController::class);
    Route::apiResource('messages', \App\Http\Controllers\MessageController::class);
    Route::apiResource('contracts', \App\Http\Controllers\ContractController::class);
});
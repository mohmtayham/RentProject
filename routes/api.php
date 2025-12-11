<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RentalContractController;
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
    // Convenience route matching your Postman URL
  //  Route::post('/addcontract', [RentalContractController::class, 'store']);
    Route::get('user2', [UserController::class,'GetUser']);
    Route::get('user', [UserController::class, 'show']);
    Route::get('users', [UserController::class, 'index']);
  // Public to any authenticated user: basic info routes kept here

  // Landlord-only routes
  Route::middleware(['\\App\\Http\\Middleware\\RoleMiddleware:landlord'])->group(function () {
    Route::get('properties', [PropertyController::class, 'index']);
  });

  // Tenant-only routes
  Route::middleware(['\\App\\Http\\Middleware\\RoleMiddleware:tenant'])->group(function () {
    Route::get('applications', [ApplicationController::class, 'index']);
  });

  // Routes shared between landlord and tenant
  Route::middleware(['\\App\\Http\\Middleware\\RoleMiddleware:landlord|tenant'])->group(function () {
    // Route::get('contracts', [ContractController::class, 'index']);
    Route::post('addcontract', [ContractController::class, 'store']);
    Route::post('addcontractWithoutConflict', [ContractController::class, 'addWithoutConflictInreservations']);
  });

 //   Route::apiResource('contracts', \App\Http\Controllers\ContractController::class);
});
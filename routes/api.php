<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Public routes (no authentication required)

    Route::post('/login', [App\Http\Controllers\UserController::class, 'login']);
    Route::post('/register', [App\Http\Controllers\UserController::class, 'register']);
    // OTP routes
    Route::post('/otp/send', [App\Http\Controllers\Auth\OtpController::class, 'sendOtp']);
    Route::post('/otp/verify', [App\Http\Controllers\Auth\OtpController::class, 'verifyOtp']);
    
    // Admin auth routes
    Route::post('/admin/register', [App\Http\Controllers\AdminController::class, 'registerAdmin']);
    Route::post('/admin/login', [App\Http\Controllers\AdminController::class, 'loginAdmin']);

     
    // Message routes
   
        Route::get('/message', [App\Http\Controllers\MessageController::class, 'index']);
        Route::post('/storemessage', [App\Http\Controllers\MessageController::class, 'store']);
        Route::get('message/users/{userId}', [App\Http\Controllers\MessageController::class, 'showUserMessages']);
        Route::delete('message/{id}', [App\Http\Controllers\MessageController::class, 'destory']);
// Protected routes (require Sanctum token)

    // Auth routes
    Route::post('/logout', [App\Http\Controllers\UserController::class, 'logout']);
    Route::post('/admin/logout', [App\Http\Controllers\AdminController::class, 'logoutAdmin']);
    
    // User routes
   
        Route::get('/me', [App\Http\Controllers\UserController::class, 'show']);
        Route::get('/', [App\Http\Controllers\UserController::class, 'index']);
        Route::post('/store1', [UserController::class, 'store']);
        Route::patch('/{id}', [App\Http\Controllers\UserController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\UserController::class, 'destroy']);
        
        // User profile routes
      
        Route::post('/{id}/id-photo', [App\Http\Controllers\UserController::class, 'editIdPhoto']);
       Route::post('/{id}/photo', [UserController::class, 'editPhoto']);
        
        // User favorites
        Route::prefix('/favorites')->group(function () {
            Route::post('/properties/{propertyId}', [App\Http\Controllers\UserController::class, 'addPropertyToFavorites']);
            Route::get('/properties', [App\Http\Controllers\UserController::class, 'getFavoriteProperties']);
            Route::delete('/properties/{propertyId}', [App\Http\Controllers\UserController::class, 'removePropertyFromFavorites']);
        
    
    
    // Property routes
    Route::prefix('properties')->group(function () {
        Route::get('/', [App\Http\Controllers\PropertyController::class, 'index']);
        Route::get('/available', [App\Http\Controllers\PropertyController::class, 'showAvillableProperties']);
        Route::get('/city/{city}', [App\Http\Controllers\PropertyController::class, 'filterBycity']);
        Route::get('/price-range', [App\Http\Controllers\PropertyController::class, 'filterBymonthly_rent']);
        
        Route::get('/{id}', [App\Http\Controllers\PropertyController::class, 'showPropertyDetails']);
        Route::put('/{id}', [App\Http\Controllers\PropertyController::class, 'updatePropertyDetails']);
        Route::delete('/{id}', [App\Http\Controllers\PropertyController::class, 'destroy']);
        
        // Property ratings
        Route::put('/{propertyId}/rating', [App\Http\Controllers\PropertyController::class, 'make_ave_rationg']);
    });
    
    // Application routes
    Route::prefix('applications')->group(function () {
        Route::get('/', [App\Http\Controllers\ApplicationController::class, 'index']);
        Route::post('/', [App\Http\Controllers\ApplicationController::class, 'store']);
        
        Route::middleware(['role:landlord|admin'])->group(function () {
            Route::put('/{id}/status', [App\Http\Controllers\ApplicationController::class, 'updateStatus']);
        });
    });
    
    // Contract routes
   
        Route::get('/', [App\Http\Controllers\ContractController::class, 'index']);
        
        Route::middleware(['role:landlord|tenant'])->group(function () {
            Route::post('/', [App\Http\Controllers\ContractController::class, 'store']);
            Route::post('/without-conflict', [App\Http\Controllers\ContractController::class, 'addWithoutConflictInreservations']);
            Route::post('/with-approval', [App\Http\Controllers\ContractController::class, 'addWithApprovalFromLandlord']);
            
            Route::get('/{id}', [App\Http\Controllers\ContractController::class, 'show']);
            Route::put('/{id}', [App\Http\Controllers\ContractController::class, 'edit']);
            
            // Contract ratings
            Route::put('/{id}/rate', [
                App\Http\Controllers\ContractController::class, 'addrate'
            ])->name('contracts.addrate');
            
            // Contract status
            Route::put('/{id}/status', [
                App\Http\Controllers\ContractController::class, 'editContractstatus'
            ])->name('contracts.status');
        });
        
        Route::middleware(['role:tenant'])->group(function () {
            Route::delete('/{id}', [App\Http\Controllers\ContractController::class, 'destroyWithApprovalFromTenant']);
        });
    
   
    
    
    // Admin only routes
   
        // User management
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'getAllUsers']);
        Route::patch('/users/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveUser']);
        Route::patch('/users/{id}/reject', [App\Http\Controllers\AdminController::class, 'rejectUser']);
       Route::patch('/users/{id}', [App\Http\Controllers\AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [App\Http\Controllers\AdminController::class, 'deleteUser']);
        
        // Application management
        Route::get('/applications', [App\Http\Controllers\ApplicationController::class, 'index']);

    
    
    // Landlord only routes
    
        // Application decisions
        Route::patch('/applications/{id}/approve', [App\Http\Controllers\LandlordController::class, 'approvecontract']);
        Route::patch('/applications/{id}/reject', [App\Http\Controllers\LandlordController::class, 'rejectcontract']);
        Route::patch('/applications/{id}/under-review', [App\Http\Controllers\LandlordController::class, 'underreviewcontract']);
        
        // Property management
        Route::get('/properties', [App\Http\Controllers\PropertyController::class, 'index']);
    
});
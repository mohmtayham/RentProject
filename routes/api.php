<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\MessageController;
//     Route::middleware('auth:sanctum')->group(function () {

// Route::post('/storeproduct',[ProductController::class,'store']);
//     });

use App\Http\Controllers\ProductController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use App\Http\Controllers\FriendController;
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
    Route::middleware('auth:sanctum')->group(function () {

Route::post('/storemessage', [MessageController::class, 'store']);});
    Route::middleware('auth:sanctum')->group(function () {
Route::get('/showwallet',[WalletController::class,'show']);
    });



    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/remove_product/{id}',[ProductController::class,'destroy']);
       


    });



    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/add_application/{propertyId}',[ContractController::class,'store']);

     

        Route::post('/add_applicationOrder',[ApplicationController::class,'makeOrderToLandlord']);

        Route::post('/add_contract',[ContractController::class,'store']);

        });
        Route::middleware('auth:sanctum')->group(function () {
 
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'approveOrRejectOrMakeUnderreview']);
});


   
  Route::patch('/users/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveUser']);

 Route::post('/addPropertytofavorite/{property_id}', [PropertyController::class, 'addToFavorites']);
  

        Route::middleware('auth:sanctum')->group(function () {

        Route::post('/friend/request/{friend}', [FriendController::class, 'sendRequest']);
        Route::post('/friend/accept/{user}', [FriendController::class, 'acceptRequest']);
        Route::delete('/friend/decline/{user}', [FriendController::class, 'declineRequest']);
        Route::delete('/friend/remove/{friend}', [FriendController::class, 'removeFriend']);


        });
        Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wallet', [WalletController::class, 'show']);
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
    Route::post('/wallet/withdraw', action: [WalletController::class, 'withdraw']);
});

        Route::get('/all_application',[ApplicationController::class,'index']);
        Route::get('/all_contract',[ContractController::class,'index']);


    


        Route::get('/properties', [PropertyController::class, 'index']); // For GET requests
        Route::get('/filterbymonthlyrent', [PropertyController::class, 'filterBymonthly_rent']);
        Route::get('/filterbycity', [PropertyController::class, 'filterBycity']);
          Route::get('/available', [App\Http\Controllers\PropertyController::class, 'showAvillableProperties']);
         

        Route::patch('/properties', [App\Http\Controllers\PropertyController::class, 'update']); // For PATCH requests
        Route::delete('/properties', [App\Http\Controllers\PropertyController::class, 'destroy']); // For DELETE requests
      
     Route::middleware('auth:sanctum')->group(function () {

      
      
        Route::delete('/removeFromProperty/{propertyId}', [PropertyController::class, 'removeFromFavorites']);
        
        Route::get('/showProperty', [PropertyController::class, 'listFavoriteProperties']);
        
      
        Route::get('/check/{propertyId}', [PropertyController::class, 'checkFavorite']);
    } );

    // CRUD Operations
 Route::middleware('auth:sanctum')->group(function () {
    // إضافة عقار جديد - للمالكين فقط
    Route::post('/addProperty', [PropertyController::class, 'store']);

    // باقي routes الخاصة بالعقارات لو موجودة
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::get('/properties/{id}', [PropertyController::class, 'showPropertyDetails']);
    // إلخ...
});    
             
    Route::put('updateProperty/{id}', [PropertyController::class, 'update']);         
    Route::delete('destryProperty/{id}', [PropertyController::class, 'destroy']);    
    
    // Filter Routes
    Route::get('/filterbycity', [PropertyController::class, 'filterBycity']);        
    Route::get('/filterbymonthlyrent', [PropertyController::class, 'filterBymonthly_rent']);
   
      Route::post('/fav/{id}', [PropertyController::class, 'addToFavorites']);
        
      
        Route::delete('fav/{id}', [PropertyController::class, 'removeFromFavorites']);
        
      
        Route::get('fav', [PropertyController::class, 'listFavoriteProperties']);
    
       
        

 Route::get('/message', [MessageController::class, 'index']);
      Route::middleware('auth:sanctum')->group(function () {
    
});
        Route::get('message/users/{userId}', [MessageController::class, 'showUserMessages']);
        Route::delete('message/{id}', [MessageController::class, 'destory']);
        

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
  
// Protected routes (require Sanctum token)

    // Auth routes
    Route::post('/logout', [App\Http\Controllers\UserController::class, 'logout']);
    Route::post('/admin/logout', [App\Http\Controllers\AdminController::class, 'logoutAdmin']);
    
    // User routes
        Route::middleware('auth:sanctum')->group(function () {
         
        Route::get('/me', [App\Http\Controllers\UserController::class, 'show']);
        Route::get('/', [App\Http\Controllers\UserController::class, 'index']);
        Route::post('/store1', [UserController::class, 'store']);
        Route::patch('/{id}', [App\Http\Controllers\UserController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\UserController::class, 'destroy']);
        });
        // User profile routes
      
        Route::post('/{id}/id-photo', [App\Http\Controllers\UserController::class, 'editIdPhoto']);
       Route::post('/{id}/photo', [UserController::class, 'editPhoto']);
        
        // User favorites
        Route::prefix('/favorites')->group(function () {
            Route::post('/properties/{propertyId}', [App\Http\Controllers\UserController::class, 'addPropertyToFavorites']);
            Route::get('/properties', [App\Http\Controllers\UserController::class, 'getFavoriteProperties']);
            Route::delete('/properties/{propertyId}', [App\Http\Controllers\UserController::class, 'removePropertyFromFavorites']);
        
        });
    
    // Property routes
   
       // Add these lines (probably in your properties routes section)
        
        Route::get('/{id}', [App\Http\Controllers\PropertyController::class, 'showPropertyDetails']);
        Route::put('/{id}', [App\Http\Controllers\PropertyController::class, 'updatePropertyDetails']);
        Route::delete('/{id}', [App\Http\Controllers\PropertyController::class, 'destroy']);
        
        // Property ratings
        Route::put('/{propertyId}/rating', [App\Http\Controllers\PropertyController::class, 'make_ave_rationg']);
   
    
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
    
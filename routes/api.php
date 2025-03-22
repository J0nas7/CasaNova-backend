<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\PropertyController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\UserOnly;

// Protected UserOnly Routes
Route::group(['middleware' => ['auth:api', UserOnly::class]], function () {
    // UserController Routes
    /**
     * GET /users - index
     * POST /users - store
     * GET /users/{property} - show
     * PUT /users/{property} - update
     * DELETE /users/{property} - destroy
     * This single line of code handles all these CRUD routes:
     */
    Route::apiResource('users', UserController::class);
    // Custom route to get user by user email
    Route::post('users/userByEmail', [UserController::class, 'getUserByEmail']);


    
    // PropertyController Routes
    /**
     * GET /properties - index
     * POST /properties - store
     * GET /properties/{property} - show
     * PUT /properties/{property} - update
     * DELETE /properties/{property} - destroy
     * This single line of code handles all these CRUD routes:
     */
    Route::apiResource('properties', PropertyController::class);



    // FavoriteController Routes
    /**
     * GET /favorites - index
     * POST /favorites - store
     * GET /favorites/{favorite} - show
     * PUT /favorites/{favorite} - update
     * DELETE /favorites/{favorite} - destroy
     * This single line of code handles all these CRUD routes:
     */
    Route::apiResource('favorites', FavoriteController::class);
    
    // UtilityController Routes
    // Custom route to get global search results
    // Route::get('search/{userId}/{searchString}', [UtilityController::class, 'globalSearch']);
});



// Public endpoints
Route::group(['middleware' => ['api']], function () {
    Route::get('/', function () {
        echo "test";
    });

    /**
     * AuthController Routes
     */
    Route::group(['middleware' => ['api']], function () {
        // Register a new user
        Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');

        // Login and generate JWT
        Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

        // Logout the authenticated user
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        // Get authenticated user details (requires authentication)
        Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:api')->name('auth.me');
    });
});
?>
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\PropertyController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PropertyImageController;
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
    //Route::apiResource('properties', PropertyController::class);
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{property}', [PropertyController::class, 'update']);
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy']);
    // Custom routes to get properties by user ID and price range
    Route::get('/properties/user/{userId}', [PropertyController::class, 'getPropertiesByUser']);
    // Custom route to get properties by price range
    Route::get('/properties/price-range/{minPrice}/{maxPrice}', [PropertyController::class, 'getPropertiesByPriceRange']);



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
    // Custom route to remove a favorite by User_ID and Property_ID
    Route::delete('/favorites/user/{userId}/property/{propertyId}', [FavoriteController::class, 'removeByUserAndProperty']);




    // MessageController Routes
    /**
     * GET /messages - index
     * POST /messages - store
     * GET /messages/{message} - show
     * PUT /messages/{message} - update
     * DELETE /messages/{message} - destroy
     * This single line of code handles all these CRUD routes:
     */
    Route::apiResource('messages', MessageController::class);




    // PropertyImageController Routes
    /**
     * GET /property-images - index
     * POST /property-images - store
     * GET /property-images/{propertyImage} - show
     * PUT /property-images/{propertyImage} - update
     * DELETE /property-images/{propertyImage} - destroy
     * This single line of code handles all these CRUD routes:
     */
    Route::apiResource('property-images', PropertyImageController::class);



    
    // UtilityController Routes
    // Custom route to get global search results
    // Route::get('search/{userId}/{searchString}', [UtilityController::class, 'globalSearch']);
});



// Public endpoints
Route::group(['middleware' => ['api']], function () {
    Route::get('/', function () {
        echo "test";
    });

    // PropertyController Routes
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::get('/properties/{property}', [PropertyController::class, 'show']);

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
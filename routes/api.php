<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\SearchController;
use App\Http\Controllers\V1\UserAuthController;
use App\Http\Controllers\V1\FavouriteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([
    'middleware' => 'api',
    'prefix' => 'v1/auth'
], function ($router) {
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/logout', [UserAuthController::class, 'logout']);
    Route::post('/refresh', [UserAuthController::class, 'refresh']);
});



Route::group([
    'middleware' => ['api', 'auth'],
    'prefix' => 'v1'
], function ($router) {
    Route::get('/searches', [SearchController::class, 'getSearches']);
    Route::delete('/searches/{id}', [SearchController::class, 'delete']);

    Route::get('/favourites', [FavouriteController::class, 'all']);
    Route::post('/favourites', [FavouriteController::class, 'add']);
    Route::get('/favourites/{id}', [FavouriteController::class, 'details']);
    Route::delete('/favourites/{word}', [FavouriteController::class, 'delete']);

});


Route::group([
    'middleware' => ['api'],
    'prefix' => 'v1'
], function ($router) {
    Route::get('/search', [SearchController::class, 'search']);
});


?>
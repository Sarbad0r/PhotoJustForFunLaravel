<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    //
    Route::post('/like-photo', [FavoriteController::class, 'like_photo']);
    Route::post('/remove-like-photo', [FavoriteController::class, 'remove_like_photo']);
    Route::get('/get-like-photos', [FavoriteController::class, 'get_liked_photos']);
    //
    Route::get('/check/token', [AuthController::class, 'check_token']);
    Route::post('/update/profile', [AuthController::class, 'update_profile']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::get('/get/user/image/{id}', [AuthController::class, 'get_user_image']);


Route::get('/email', [AuthController::class, 'log_and_register_by_email']);

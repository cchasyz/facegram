<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FollowsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('v1/auth/register', [AuthController::class, 'register']);
Route::post('v1/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('v1/auth/logout', [AuthController::class, 'logout']);
    Route::post('v1/posts', [PostController::class, 'create']);
    Route::delete('v1/posts/{id}', [PostController::class, 'destroy']);
    Route::get('v1/posts', [PostController::class, 'index']);

    Route::post('v1/users/{username}/follow', [FollowsController::class, 'follow']);
    Route::delete('v1/users/{username}/unfollow', [FollowsController::class, 'unfollow']);

    Route::get('v1/users/{username}/following', [FollowsController::class, 'getFollowing']);
    Route::get('v1/users/{username}/followers', [FollowsController::class, 'getFollowers']);

    Route::put('v1/users/{username}/accept', [FollowsController::class, 'accept']);

    Route::get('v1/users', [FollowsController::class, 'getUsers']);
    Route::get('v1/users/request', [PostController::class, 'getUserRequest']);
    Route::get('v1/users/{username}', [FollowsController::class, 'getDetailUsers']);
});
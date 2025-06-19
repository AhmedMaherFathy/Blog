<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);

Route::middleware('auth:api')->group(function(){
    Route::post('posts',[PostController::class,'store']);
    // Route::get('posts',[PostController::class,'index']);
    Route::get('posts/{slug}',[PostController::class,'show']);
    Route::post('posts/{slug}',[PostController::class,'update']);
    Route::delete('posts/{slug}',[PostController::class,'destroy']);

    Route::post('posts/{slug}/comments',[CommentController::class,'store']);
    Route::get('posts/{slug}/comments',[CommentController::class,'getPostComments']);
});


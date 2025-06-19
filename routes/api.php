<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);

Route::prefix('posts')->middleware('auth:api')->group(function(){
    Route::post('/',[PostController::class,'store']);
    Route::get('/{slug}',[PostController::class,'show']);
    Route::post('/{slug}',[PostController::class,'update']);
    Route::delete('/{slug}',[PostController::class,'destroy']);

    Route::post('/{slug}/comments',[CommentController::class,'store']);
    Route::get('/{slug}/comments',[CommentController::class,'getPostComments']);
});


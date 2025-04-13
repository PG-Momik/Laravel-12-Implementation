<?php

declare(strict_types=1);

use App\Http\Controllers\PostsController;
use App\Http\Controllers\WebzPostsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostsController::class, 'index']);

Route::get('/api/posts', function () {
    return response()->json([
        'status'=>true,
        'message'=>'Setup successfully'
    ]);
});

Route::get('/api/fetch-webz-posts',[WebzPostsController::class,'index']);
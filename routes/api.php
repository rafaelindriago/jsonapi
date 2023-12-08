<?php

declare(strict_types=1);

use App\Exceptions\Api\RequestNotFoundException;
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

Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());

Route::apiResource('users', \App\Http\Controllers\Api\User\UserController::class);

Route::apiResource('posts', \App\Http\Controllers\Api\Post\PostController::class);

Route::fallback(function (Request $request): void {
    throw new RequestNotFoundException();
});

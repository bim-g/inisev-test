<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Controllers\postController;
use App\Http\Controllers\websiteController;

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

Route::controller(userController::class)->group(function () {
    Route::get('/users', 'getUsers');
    Route::get('/users/{id}', 'getUsersById')->where(['id'=>'[0-9]']);
    Route::post('/users', 'registerUsers');
    Route::get('/users-subscribe', 'getSubscribers');
    Route::get('/users-subscribe/{id}', 'getSubscribersByWebsite')->where(['id'=>'[0-9]']);
    Route::post('/users-subscribe', 'subscribeUser');
});

Route::controller(postController::class)->group(function () {
    Route::get('/posts', 'getPosts');
    Route::get('/posts/{id}', 'getPostById')->where(['id'=>'[0-9]']);
    Route::post('/posts', 'AddPost');
});

Route::controller(websiteController::class)->group(function () {
    Route::get('/websites', 'getWebsites');
    Route::get('/websites/{id}', 'getWebsiteById')->where(['id'=>'[0-9]']);
    Route::post('/websites', 'addwebsite');
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

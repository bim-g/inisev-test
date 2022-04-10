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
/**
 * Users and Subscribers
 */
Route::controller(userController::class)->group(function () {
    Route::get('/users', 'getUsers');
    Route::get('/users/{id}', 'getUsersById')->where(['id'=>'[0-9]']);
    Route::post('/users', 'registerUsers');
    Route::get('/users-subscribe', 'getSubscribers');
    Route::get('/users-subscribe/{id}', 'getSubscribersByWebsite')->where(['id'=>'[0-9]']);
    Route::get('/subscribers', 'getSubscribers');
    Route::post('/subscribers', 'subscribeUser');
});

/**
 * Posts
 */
Route::controller(postController::class)->group(function () {
    Route::get('/posts', 'getPosts');
    Route::post('/posts', 'AddPost');
    Route::delete('/posts/{id}', 'deletePost');
    Route::get('/posts/{id}', 'getPostById');
    Route::get('/posts/emails', 'getPostsEmail');
    Route::get('/posts/emails/{status}/status', 'getPostEmailByStatus')->where(['id'=>'[0-1]']);
});

/**
 * WebSites
 */
Route::controller(websiteController::class)->group(function () {
    Route::get('/websites', 'getWebsites');
    Route::get('/websites/{id}', 'getWebsiteById')->where(['id'=>'[0-9]']);
    Route::post('/websites', 'addwebsite');
    Route::get('/websites/subscribers', 'getWebsiteSubscribers');
    Route::get('/websites/subscribers/{id}', 'getWebsiteSubscribersByID');
    Route::get('/websites/posts', 'getWebsitePosts');
    Route::get('/websites/posts/{id}', 'getWebsitePostsByID');
});
/**
 *
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

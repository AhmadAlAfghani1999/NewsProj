<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group(['middleware' => 'auth:api'],function (){
    Route::post('/newCatrgory',[\App\Http\Controllers\HomeController::class,'newcategory']);
    Route::post('/addNews',[\App\Http\Controllers\HomeController::class,'addnews']);
    Route::post('/updateNews/{id}',[\App\Http\Controllers\HomeController::class,'updatenews']);
    Route::post('/updateCategories/{id}',[\App\Http\Controllers\HomeController::class,'updatecategory']);
    Route::delete('/deletenews/{id}',[\App\Http\Controllers\HomeController::class,'destroy']);

});

Route::get('/getNews',[\App\Http\Controllers\HomeController::class,'getNews']);
Route::get('/getNewsId/{id}',[\App\Http\Controllers\HomeController::class,'getNewsId']);
Route::get('/getCategoriesId/{id}',[\App\Http\Controllers\HomeController::class,'getCategoriesId']);
Route::post('/login',[\App\Http\Controllers\HomeController::class,'login']);


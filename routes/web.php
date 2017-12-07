<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();


//    Route::middleware(['admin.user'])->group(function (){
//
//        Route::resource('questions', 'Voyager\QuestionsController');
//    });

//    Route::group(['middleware' => 'admin.user'], function () {
//        Route::resource('questions', 'Voyager\QuestionsController');
//    });
});

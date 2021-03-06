<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['as' => 'api.', 'namespace' => 'Api\V1', 'middleware' => ['auth:api']], function () {
    Route::resource('questions', 'QuestionController');
    Route::resource('fights', 'FightController');
    Route::resource('fightrecords', 'FightRecordController');
});

//user login,register
Route::group(['as' => 'api.', 'namespace' => 'Api\V1\Auth'], function () {
    Route::post('register', 'RegisterController@register')->name('register');
    Route::post('login', 'LoginController@login')->name('login');
    Route::post('refresh', 'LoginController@refresh')->name('refresh');
});



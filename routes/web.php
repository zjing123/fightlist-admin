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

    Route::group(['middleware' => 'admin.user', 'namespace' => 'Voyager'], function () {
        Route::get('questions/{question_id}/answer/{answer_id}/edit', 'VoyagerQuestionController@editAnswer')
            ->name('voyager.questions.answer.edit');

        Route::put('questions/{question_id}/answer/{answer_id}', 'VoyagerQuestionController@updateAnswer')
            ->name('voyager.questions.answer.update');

        Route::delete('questions/answer/{answer_id}', 'VoyagerQuestionController@destroyAnswer')
            ->name('voyager.questions.answer.destroy');

        Route::get('questions/answer', 'VoyagerQuestionController@indexAnswer')
            ->name('voyager.questions.answer.index');

        Route::post('questions/answer/update/multi', 'VoyagerQuestionController@updateMultiAnswer')
            ->name('voyager.question.multi.update');
    });
});

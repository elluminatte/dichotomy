<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});


// для модуля ОКВЭД
Route::get('okved/list/{sectionId?}', array('as' => 'okvedList', 'uses' => 'OkvedController@showTree'));
Route::get('okved/delete/{sectionId?}', array('as' => 'delOkved', 'uses' => 'OkvedController@deleteSection'));
Route::get('okved/add-form/{parentId?}', array('as' => 'addOkvedForm', 'uses' => 'OkvedController@showAddForm'));
Route::post('okved/add', array('before' => 'csrf', 'as' => 'addOkved', 'uses' => 'OkvedController@addSection'));
Route::get('okved/edit-form/{sectionId}', array('as' => 'editOkvedForm', 'uses' => 'OkvedController@showEditForm'));
Route::post('okved/edit', array('before' => 'csrf', 'as' => 'editOkved', 'uses' => 'OkvedController@editSection'));

//для модуля Модели
Route::get('models/list/{sectionId}', array('as' => 'modelsList', 'uses' => 'ModelsController@showList'));
Route::get('models/{modelId}', array('as' => 'modelDetail', 'uses' => 'ModelsController@showModel'));

// Confide routes
Route::get('users/create', 'UsersController@create');
Route::post('users', 'UsersController@store');
Route::get('users/login', 'UsersController@login');
Route::post('users/login', 'UsersController@doLogin');
Route::get('users/confirm/{code}', 'UsersController@confirm');
Route::get('users/forgot_password', 'UsersController@forgotPassword');
Route::post('users/forgot_password', 'UsersController@doForgotPassword');
Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
Route::post('users/reset_password', 'UsersController@doResetPassword');
Route::get('users/logout', 'UsersController@logout');

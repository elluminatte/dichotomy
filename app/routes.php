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

Route::group(array('prefix' => 'admin'), function()
{
	// Маршруты проблемных ситуаций
	Route::get('situations/list/{iParentSituationId?}', array('as' => 'situations.list', 'uses' => 'SituationController@index'));
	Route::get('situations/create/{iParentSituationId?}', array('as' => 'situations.create', 'uses' => 'SituationController@create'));
	Route::post('situations/store', array('before' => 'csrf', 'as' => 'situations.store', 'uses' => 'SituationController@store'));
	Route::get('situations/edit/{iSituationId}', array('as' => 'situations.edit', 'uses' => 'SituationController@edit'));
	Route::post('situations/update', array('before' => 'csrf', 'as' => 'situations.update', 'uses' => 'SituationController@update'));
	Route::get('situations/destroy/{iSituationId}', array('as' => 'situations.destroy', 'uses' => 'SituationController@destroy'));

	// Маршруты моделей
	Route::get('models/list/{iSituationId}', array('as' => 'models.list', 'uses' => 'ModelController@index'));
	Route::get('models/create/{iSituationId}', array('as' => 'models.create', 'uses' => 'ModelController@create'));
	Route::post('models/store', array('before' => 'csrf', 'as' => 'models.store', 'uses' => 'ModelController@store'));

});
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

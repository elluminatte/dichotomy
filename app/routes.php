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
//	var_dump(\Carbon\Carbon::now()->formatLocalized('%d %B %Y %H:%M'));
//	$test = Evaluation::find(1);
//	$t = $test->expired_moment;
//	var_dump(new \Carbon\Carbon($t)->format);
//
//	$date = \Carbon\Carbon::now()->addMinutes(1);
//	Queue::push('\Elluminate\Workers\Test', ['name' => 'worker1', 'duration' => 1111]);
//	Queue::push('\Elluminate\Workers\Test1', ['name' => 'worker2', 'duration' => 22222]);
//	$oEvaluation = \Evaluation::find(3);
//	$iRealResult = $oEvaluation->real_result;
//	$aCovariates = json_decode($oEvaluation->covariates);
//	$aRow = array_unshift($aCovariates, $iRealResult);
//	echo "<XMP>";
////	print_r($aCovariates);
//	echo "</XMP>";
	return View::make('hello');
});

Route::group(array('prefix' => 'admin'), function()
{
	// Маршруты проблемных ситуаций
	Route::get('situations/list/{iParentSituationId?}', array('as' => 'situations.list', 'uses' => 'AdminSituationController@index'));
	Route::get('situations/create/{iParentSituationId?}', array('as' => 'situations.create', 'uses' => 'AdminSituationController@create'));
	Route::post('situations/store', array('before' => 'csrf', 'as' => 'situations.store', 'uses' => 'AdminSituationController@store'));
	Route::get('situations/edit/{iSituationId}', array('as' => 'situations.edit', 'uses' => 'AdminSituationController@edit'));
	Route::post('situations/update', array('before' => 'csrf', 'as' => 'situations.update', 'uses' => 'AdminSituationController@update'));
	Route::get('situations/destroy/{iSituationId}', array('as' => 'situations.destroy', 'uses' => 'AdminSituationController@destroy'));

	// Маршруты моделей
	Route::get('models/list/{iSituationId}', array('as' => 'models.list', 'uses' => 'AdminModelController@index'));
	Route::get('models/detail/{iModelId}', array('as' => 'models.detail', 'uses' => 'AdminModelController@show'));
	Route::get('models/create/{iSituationId}', array('as' => 'models.create', 'uses' => 'AdminModelController@create'));
	Route::post('models/store', array('before' => 'csrf', 'as' => 'models.store', 'uses' => 'AdminModelController@store'));
	Route::get('models/destroy/{iModelId}', array('as' => 'models.destroy', 'uses' => 'AdminModelController@destroy'));
	Route::get('models/template/', array('as' => 'models.template', 'uses' => 'AdminModelController@downloadTemplate'));
	Route::get('models/inactive/', array('as' => 'models.inactive', 'uses' => 'AdminModelController@inactiveModels'));
	Route::get('models/dump/{iModelId}', array('as' => 'models.dump', 'uses' => 'AdminModelController@dump'));
});

Route::group(array('prefix' => 'client'), function() {
	Route::get('problems/list/{iParentSituationId?}', ['as' => 'problems.list', 'uses' => 'ClientSituationController@index']);
	Route::get('tasks/list/{iSituationId}', array('as' => 'tasks.list', 'uses' => 'ClientModelController@index'));
	Route::get('tasks/detail/{iModelId}', array('as' => 'tasks.detail', 'uses' => 'ClientModelController@showModelForm'));
	Route::post('tasks/compute', ['before' => 'csrf', 'as' => 'tasks.compute', 'uses' => 'ClientModelController@compute']);
	Route::post('/search/', array('before' => 'csrf', 'as' => 'search', 'uses' => 'SearchController@index'));
	Route::get('evaluations/list', array('as' => 'evaluations.list', 'uses' => 'EvaluationController@index'));
	Route::get('evaluations/detail/{iEvaluationId}', array('as' => 'evaluations.detail', 'uses' => 'EvaluationController@show'));
	Route::post('evaluations/confirm', ['before' => 'csrf', 'as' => 'evaluations.confirm', 'uses' => 'EvaluationController@confirm']);
});
Route::get('test', ['as' => 'test.ajax', 'uses' => 'HomeController@test']);

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

// пришлось вынести меню сюда, иначе не работают именованные маршруты
// меню авторизации
Menu::make('authNavBar', function($menu){
	// если пользователь залогинился, то покажем кто он и кнопку на выхож
	if(\Auth::user()) {
		$menu->add('<i class="fa fa-user"></i>  Вы вошли как '.\Auth::user()->username);
		$menu->add('<i class="fa fa-sign-out"></i> Выйти', 'users/logout');
	}
	// иначе предложим войти или зарегистрироваться
	else {
		$menu->add('<i class="fa fa-sign-in"></i> Войти', 'users/login');
		$menu->add('<i class="fa fa-user-plus"></i> Зарегистрироваться', 'users/create');
	}
});

// админское меню
Menu::make('adminNavBar', function($menu) {
	// проверим, есть ли у пользователя роль администратора
	if(\Entrust::hasRole('administrator')) {
		// назначим ему id, чтобы потом добавлять внутрь его, текстовые id почему-то не поддерживаются, придется так
		$menu->add('<i class="fa fa-cogs"></i> Управление', ['id' => 1]);
		$menu->find(1)->add('<i class="fa fa-folder-open"></i> Каталог проблемных ситуаций</a>', ['route'  => 'situations.list', 'id' => 2]);
		$menu->find(1)->add('<i class="fa fa-lock"></i> Неактивные задачи</a>', ['route'  => 'models.inactive', 'id' => 3]);
		if(\Request::is('/admin*'))
			$menu->find(1)->active();
		if(\Route::is('situations.list') || \Route::is('models.list'))
			$menu->find(2)->active();
	}
});

// меню зарегистрированного пользователя
Menu::make('userNavBar', function($menu) {
	// проверим, есть ли у пользователя роли юзера
	if(\Entrust::hasRole('user')) {
		$menu->add('<i class="fa fa-check"></i> Решение задач классификации', ['id' => 1]);
		$menu->find(1)->add('<i class="fa fa-magic"></i> Поиск и решение задачи', ['route' => 'problems.list',' id' => 2]);
		$menu->find(1)->add('<i class="fa fa-send-o"></i> Подтверждение решения (обратная связь)', ['route' => 'evaluations.list', 'id' => 3]);
		if(\Request::is('/client*'))
			$menu->find(1)->active();
		EvaluationRepository::getNotifications();
	}
});

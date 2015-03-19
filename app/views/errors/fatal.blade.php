<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @yield('title')
    </title>
    {{ HTML::style('assets/bootstrap/css/bootstrap.css') }}
    {{ HTML::style('assets/css/font-awesome.min.css') }}
    {{ HTML::style('assets/css/main.css') }}

    @yield('styles')

</head>
<body>
<div class="container">
<div class="col-lg-10">
    <div class="row">
        <div class="alert alert-dismissable alert-danger">
            <h4>Упс!</h4>
            <p>Произошла ошибка при работе приложения</p>
            <p>Вы можете зайти на <a class="alert-link" href="{{ URL::to('/') }}">главную страницу</a> или вернуться <a class="alert-link" href="javascript:history.go(-1)">назад</a> и попробовать еще раз</p>
            <p>Если система не заработала, обратитесь к администратору <a class="alert-link" href="mailto:{{ \Config::get('app.admin_email') }}">{{ \Config::get('app.admin_email') }}</a> или зайдите попозже.</p>
        </div>
    </div>
</div>
</div>
{{ HTML::script('assets/js/jquery-1.11.1.min.js') }}
{{ HTML::script('assets/bootstrap/js/bootstrap.min.js') }}
</body>
</html>
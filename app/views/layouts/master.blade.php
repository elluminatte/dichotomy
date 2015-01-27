<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @section('title')
            Diagnostic
        @show
    </title>

    <!-- CSS are placed here -->
    {{ HTML::style('assets/bootstrap/css/bootstrap.css') }}
    {{ HTML::style('assets/css/font-awesome.min.css') }}
    {{ HTML::style('assets/css/main.css') }}
</head>

<body>

<header>
    @yield('header')
    <div class="row">
        <div class="container">
    <div class="navbar navbar-default">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ URL::to('/') }}">Diagnostic</a>
        </div>
        <div class="navbar-collapse collapse navbar-responsive-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="{{ URL::route('okvedList') }}">Каталог проблемных ситуаций</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <form class="navbar-form navbar-left">
                    <input type="text" class="form-control col-lg-8" placeholder="Поиск (не работает)">
                </form>
            </ul>
        </div>
    </div>
        </div>
    </div>
</header>

<main>
    <div class="container">
        @yield('content')
    </div>
</main>

<footer>
    @yield('footer')
</footer>

<!-- Scripts are placed here -->
{{ HTML::script('assets/js/jquery-1.11.1.min.js') }}
{{ HTML::script('assets/bootstrap/js/bootstrap.min.js') }}

@yield('scripts')

</body>
</html>
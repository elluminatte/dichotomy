<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @section('title')
            Laravel 4 - Tutorial
        @show
    </title>

    <!-- CSS are placed here -->
    {{ HTML::style('assets/bootstrap/css/bootstrap.css') }}
    {{ HTML::style('assets/css/font-awesome.min.css') }}
</head>

<body>

<header>
    @yield('header')
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

</body>
</html>
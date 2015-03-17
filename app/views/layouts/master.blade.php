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
<header>

@include('partials.navbar')

</header>
<main>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="col-lg-5">
                @if ( Session::get('waiting_evaluations') )
                    @include('client.notifications.waiting_evaluations')
                @endif
                @if ( Session::get('expired_evaluations') )
                    @include('client.notifications.expired_evaluations')
                @endif
            </div>

                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                        @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</main>
<footer>
{{--@include('partials.footer')--}}
</footer>

{{ HTML::script('assets/js/jquery-1.11.1.min.js') }}
{{ HTML::script('assets/bootstrap/js/bootstrap.min.js') }}
{{ HTML::script('assets/js/search.js') }}
@yield('scripts')

</body>
</html>
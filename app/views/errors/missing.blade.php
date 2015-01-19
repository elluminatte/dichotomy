@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="alert alert-dismissable alert-warning">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <h4>Упс!</h4>
            <p>Кажется, Вы ищите что-то, чего нет в нашем приложении</p>
            <p>Вы можете зайти на <a class="alert-link" href="{{ URL::to('/') }}">главную страницу</a> или вернуться <a class="alert-link" href="{{ URL::previous() }}">назад</a> и попробовать еще раз</p>
        </div>
    </div>
@stop
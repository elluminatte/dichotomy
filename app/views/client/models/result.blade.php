@extends('layouts.master')
@section('styles')
    {{ HTML::style('assets/css/vertical-progressbar.css') }}
@stop
@section('title')
    Результаты решения
@stop
@section('content')
    {{ Breadcrumbs::render('client.models', $hierarchy, 'detail', $model_id) }}
    <blockquote>
        <p>{{ $reg_name }}</p>
        <small>{{ $comment }}</small>
        <small>Граничное значение вероятности - {{ $sill }}</small>
    </blockquote>
    <div>
    @if($result >= $sill)
    <span class="label label-success">Результат - Да</span>
    @else
    <span class="label label-danger">Результат - Нет</span>
    @endif
    </div>
    <div class="col-lg-10">
            <div class="vertical-progress">
                <div class="vertical-progress_bar" data-value="{{ round($result*100) }}%"></div>
                <div class="vertical-progress_tx">{{$result}}</div>
            </div>
        </div>
@stop
@section('scripts')
            {{ HTML::script('assets/js/vertical-progressbar.js') }}
@stop
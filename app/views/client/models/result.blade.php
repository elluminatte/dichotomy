@extends('layouts.master')
@section('title')
    Результаты решения
@stop
@section('content')
    {{ Breadcrumbs::render('client.models', $hierarchy, 'detail', $model_id) }}
    <blockquote>
        <p>{{ $reg_name }}</p>
        <small>{{ $comment }}</small>
    </blockquote>
    <div class="col-lg-5">
        <span class="label label-danger">0 - Нет</span>
        <span class="label label-success">1 - Да</span>
    <div class="progress">

        <div class="progress-bar" style="width: {{ round($result*100) }}%">{{ $result }}</div>
    </div>
    </div>
@stop
@extends('layouts.master')
@section('styles')
    {{ HTML::style('assets/css/bootstrap-slider.css') }}
@stop
@section('content')
    <div class="row">
        {{ Breadcrumbs::render('situations', $parent_tree, 'add') }}
    </div>
    {{ Form::open(['route' => 'models.store', 'class' => 'form-horizontal', 'files' => true]) }}
    <fieldset>
        <legend>Добавление задачи</legend>
        <div class="form-group">
            {{ Form::label('name', 'Название', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-5">
                {{ Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'placeholder' => 'Название']) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('duration', 'Время корректности решения', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-5">
                {{ Form::select('duration', $durations, null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('min_threshold', 'Минимальный порог отсечения', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-5">
                <input class="js__range_slider" data-slider-id='threshold_slider' type="text" data-slider-min="50" data-slider-max="100" data-slider-step="1" data-slider-value="75"/>
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('comment', 'Информация о задаче', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-5">
                {{ Form::textarea('comment', null, ['class' => 'form-control', 'rows' => 4]) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('file', 'Файл обучающей выборки', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-5">
                {{ Form::file('train_file') }}
            </div>
        </div>
        {{ Form::hidden('situation_id', $situation_id) }}
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
                {{ Form::submit('Добавить', ['class' => 'btn btn-primary']) }}
                {{ Form::reset('Отмена', ['class' => 'btn btn-default']) }}
            </div>
        </div>
    </fieldset>
    {{ Form::close() }}
    @if ( !$errors->isEmpty() )
        @foreach($errors->all() as $error)
            <div class="col-lg-5">
                <div class="alert alert-dismissable alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {{ $error }}.
                </div>
            </div>
        @endforeach
    @endif
@stop

@section('scripts')
    {{ HTML::script('assets/js/bootstrap-slider.js') }}
    {{ HTML::script('assets/js/slider.js') }}
@stop
@extends('layouts.master')
@section('title')
    Администрирование. Редактирование задачи {{ $model->name }}
@stop
@section('styles')
    {{ HTML::style('assets/css/bootstrap-slider.css') }}
@stop
@section('content')
        {{ Breadcrumbs::render('admin.models', $hierarchy, 'edit', $model->id) }}
    {{ Form::model($model, ['route' => ['models.update'], 'class' => 'form-horizontal', 'files' => true]) }}
    <fieldset>
        <legend>Редактирование параметров задачи</legend>
        <div class="form-group">
            {{ Form::label('name', 'Название', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-5">
                {{ Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'placeholder' => 'Название']) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('duration', 'Время корректности решения', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-5">
                {{ Form::select('duration', $durations, $model->durations_id, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('min_threshold', 'Минимальный порог отсечения', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-5">
                <input name='min_threshold' class="js__range_slider" data-slider-id='threshold_slider' type="text" data-slider-min="50" data-slider-max="100" data-slider-step="1" data-slider-value="{{ $model->min_threshold }}"/>
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
                <span class="help-block"><p class="text-warning">Внимание! При загрузке файла модель будет переобучена. При этом данные дополнительной выборки будут утеряны.</p>
                    <p class="text-warning">Кроме того будут удалены пользовательские вычисления для этой модели.</p>
                    <p class="text-warning">Если Вы не хотите сейчас переобучать модель, просто оставьте это поле незаполненным.</p>
                </span>
            </div>
        </div>
        {{ Form::hidden('model_id', $model->id) }}
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
                {{ Form::submit('Изменить', ['class' => 'btn btn-primary']) }}
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
    {{--{{ HTML::script('assets/js/checkFile.js') }}--}}
@stop
@extends('layouts.master')
@section('content')
<div class="col-lg-row">
    <blockquote>
        <p>{{ $model->name }}</p>
        <small>{{ $model->comment }}</small>
    </blockquote>
</div>
<div class="row">
    {{ Form::open(array('route' => 'tasks.compute', 'class' => 'form-horizontal')) }}
    <fieldset>
        <legend>Введите значения параметров для решения задачи</legend>
        @foreach($form as $field)
            <div class="form-group">
                {{ Form::label($field['tech_name'], $field['name'], ['class' => 'col-lg-2 control-label']) }}
                <div class="col-lg-5">
                    {{Form::number($field['tech_name'], null, ['id' => $field['tech_name'], 'class' => 'form-control', 'placeholder' => $field['comment']]);}}
                    <span class="help-block">{{$field['comment']}}</span>
                </div>
            </div>
        @endforeach
            {{ Form::hidden('model_id', $model->id) }}
            <div class="form-group">
                <div class="col-lg-10 col-lg-offset-1">
                    {{ Form::submit('Решить', ['class' => 'btn btn-primary']) }}
                    {{ Form::reset('Отмена', ['class' => 'btn btn-default']) }}
                </div>
            </div>
    </fieldset>
    {{ Form::close() }}
    </div>
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
@extends('layouts.master')
@section('content')
        {{ Breadcrumbs::render('situations', $parent_tree, 'edit') }}
    {{ Form::model($situation, ['route' => ['situations.update'], 'class' => 'form-horizontal']) }}
    <fieldset>
        <legend>Изменение реквизитов ситуации</legend>
        <div class="form-group">
            {{ Form::label('name', 'Название', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-5">
                {{ Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'placeholder' => 'Название']) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('okved_correspondence', 'Соответствие ОКВЭД', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-5">
                {{ Form::text('okved_correspondence', null, ['id' => 'okved_correspondence', 'class' => 'form-control', 'placeholder' => 'Соответствие ОКВЭД']) }}
            </div>
        </div>
        {{ Form::hidden('situation_id', $situation->id) }}
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
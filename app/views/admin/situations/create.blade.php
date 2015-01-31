@extends('layouts.master')
@section('content')
    <div class="row">
        {{ Breadcrumbs::render('situations', $parent_tree, 'add') }}
    </div>
    {{ Form::open(['route' => 'situations.store', 'class' => 'form-horizontal']) }}
    <fieldset>
        <legend>Добавление ситуации</legend>
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
        {{ Form::hidden('parent_id', $parent_situation_id) }}
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
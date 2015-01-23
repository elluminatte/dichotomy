@extends('layouts.master')
@section('content')
    <div class="row">
        {{ Breadcrumbs::render('okvedList', $breadcrumbs) }}
    </div>
{{ Form::open(array('route' => 'addOkved', 'class' => 'form-horizontal')) }}
<fieldset>
    <legend>Добавление раздела ОКВЭД</legend>
    <div class="form-group">
        {{ Form::label('name', 'Название', array('class' => 'col-lg-2 control-label')) }}
        <div class="col-lg-5">
            {{ Form::text('name', null, array('id' => 'name', 'class' => 'form-control', 'placeholder' => 'Название')) }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('okved_correspondence', 'Соответствие ОКВЭД', array('class' => 'col-lg-2 control-label')) }}
        <div class="col-lg-5">
            {{ Form::text('okved_correspondence', null, array('id' => 'okved_correspondence', 'class' => 'form-control', 'placeholder' => 'Соответствие ОКВЭД')) }}
        </div>
    </div>
    {{ Form::hidden('parent_id', $parentId) }}
    <div class="form-group">
        <div class="col-lg-10 col-lg-offset-2">
            {{ Form::submit('Добавить', array('class' => 'btn btn-primary')) }}
            {{ Form::reset('Отмена', array('class' => 'btn btn-default')) }}
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
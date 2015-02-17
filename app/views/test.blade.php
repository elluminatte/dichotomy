@extends('layouts.master')
@section('content')
    <div class="row">
    {{ Form::open(array('url' => 'foo/bar', 'class' => 'form-horizontal')) }}
    <fieldset>
    @foreach($fields as $field)
        <div class="form-group">
            {{ Form::label($field['name'], $field['label'], ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-5">
                {{Form::number($field['name'], null, ['id' => $field['name'], 'class' => 'form-control', 'placeholder' => $field['label']]);}}
                <span class="help-block">{{$field['label']}}</span>
            </div>
        </div>
    @endforeach
        </fieldset>
    {{ Form::close() }}
    </div>
@stop
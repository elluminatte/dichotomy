@extends('layouts.master)
@section('content')
    {{ Form::open(array('route' => 'addOkved')) }}
    //
    {{ Form::close() }}
@stop
@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <h4>Ой!</h4>
            <p>{{ $message }}</p>
        </div>
    </div>
@stop
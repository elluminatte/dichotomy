@extends('layouts.master')
@section('content')
    <div>{{ $comment }}</div>
    <div>{{ $reg_name }}</div>
    <div class="col-lg-5">
        0 - Нет
    <div class="progress">

        <div class="progress-bar" style="width: {{ $result*100 }}%">{{ $result }}</div>
    </div>
        1 - Да
    </div>
@stop
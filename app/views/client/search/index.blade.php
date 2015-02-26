@extends('layouts.master')
@section('title')
    Результаты поиска
@stop    
@section('content')
    <div class="col-lg-12">
        <h4 class="page-header">Совпадения по коду ОКВЭД</h4>
        @if($okved_code->isEmpty())
            <div class="row">
                <div class="col-lg-5">
                    <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        Совпадений по коду ОКВЭД не найдено.
                    </div>
                </div>
            </div>
        @else
            <ul class="list-group">
                @foreach($okved_code as $value)
                    <li class="list-group-item">
                        <a href="{{ URL::route('problems.list', ['iParentSituationId' => $value->parent_id]) }}">{{$value->name}}</a>
                        <span class="badge">{{ $value->okved_correspondence }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
        @if($code_overlimit)
            <div class="row">
                <div class="col-lg-7">
                    <div class="alert alert-dismissible alert-info">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        Здесь показаны не все совпадения по коду ОКВЭД, их слишком много. Пожалуйста,
                        уточните условия поиска.
                    </div>
                </div>
            </div>
        @endif
        <h4 class="page-header">Совпадения по названию проблемной ситуации</h4>
        @if($situation_name->isEmpty())
            <div class="row">
                <div class="col-lg-5">
                    <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        Совпадений по названию проблемной ситуации не найдено.
                    </div>
                </div>
            </div>
        @else
            <ul class="list-group">
                @foreach($situation_name as $value)
                    <li class="list-group-item">
                        <a href="{{ URL::route('problems.list', ['iParentSituationId' => $value->parent_id]) }}">{{$value->name}}</a>
                        <span class="badge">{{ $value->okved_correspondence }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
        @if($situation_overlimit)
            <div class="row">
                <div class="col-lg-7">
                    <div class="alert alert-dismissible alert-info">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        Здесь показаны не все совпадения по названию проблемной ситуации, их слишком много.
                        Пожалуйста, уточните условия поиска.
                    </div>
                </div>
            </div>
        @endif
        <h4 class="page-header">Совпадения по названию решаемой проблемы</h4>
        @if($model_name->isEmpty())
            <div class="row">
                <div class="col-lg-5">
                    <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        Совпадений по названию решаемой проблемы не найдено.
                    </div>
                </div>
            </div>
        @else
            <ul class="list-group">
                @foreach($model_name as $value)
                    <li class="list-group-item">
                        <a href="{{ URL::route('task.detail', ['iTaskId' => $value->id]) }}">{{$value->name}}</a>
                    </li>
                @endforeach
            </ul>
        @endif
        @if($model_overlimit)
            <div class="row">
                <div class="col-lg-7">
                    <div class="alert alert-dismissible alert-info">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        Здесь показаны не все совпадения, их слишком много. Пожалуйста, уточните условия
                        поиска.
                    </div>
                </div>
            </div>
        @endif
    </div>
@stop
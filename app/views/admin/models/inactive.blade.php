@extends('layouts.master')
@section('title')
    Администрирование. Список неактивных задач
@stop
@section('content')
    {{ Breadcrumbs::render('models.inactive') }}
        <div class="page-header">
            <h3><i class="fa fa-angle-double-down"></i> Список неактивных задач</h3>
        </div>
        <div style="margin-bottom: 20px"></div>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Название</th>
            </tr>
            </thead>
            <tbody>
            @if(!$models->isEmpty())
                @foreach($models as $model)
                    <tr>
                        <td class="td-align_left"><a href="{{ URL::route('models.detail', ['iModelId' => $model->id])}}">{{ $model->name }}</a></td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
@stop
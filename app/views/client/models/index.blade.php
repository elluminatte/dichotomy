@extends('layouts.master')
@section('content')
    {{ Breadcrumbs::render('client.models', $hierarchy, 'list') }}
    <div class="col-lg-12">
        <div class="page-header">
            <h3><i class="fa fa-angle-double-down"></i> Список решаемых задач</h3>
        </div>
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
                    <td class="td-align_left"><a href="{{ URL::route('tasks.detail', ['iModelId' => $model->id])}}">{{ $model->name }}</a></td>
                </tr>
            @endforeach
            @endif
            </tbody>
        </table>
    </div>
@stop

@extends('layouts.master')
@section('title')
    Список проблемных ситуаций
@stop
@section('content')
            <div class="page-header">
                <h3><i class="fa fa-angle-double-down"></i> Каталог проблемных ситуаций</h3>
            </div>
            {{ Breadcrumbs::render('client.situations', $hierarchy) }}
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Соответствие ОКВЭД</th>
                    <th>Решаемые задачи</th>
                </tr>
                </thead>
                <tbody>
                @if(!$situations->isEmpty())
                @foreach($situations as $situation)
                    <tr>
                        @if( isset($situation->children) && count($situation->children))
                            <td class="td-align_left"><a href="{{ URL::route('problems.list', ['iParentSituationId' => $situation->id])}}"><i class="fa fa-level-down"></i> {{ $situation->name }}</a></td>
                        @else
                            <td class="td-align_left">{{ $situation->name }}</td>
                        @endif
                        <td>{{ $situation->okved_correspondence }}</td>
                        <td>
                        @if( isset($situation->activeModels) && count($situation->activeModels))
                        <a title="Решаемые задачи" href="{{ URL::route('tasks.list', ['iSituationId' => $situation->id]) }}" class="btn btn-primary"> <span class="badge">{{ count($situation->activeModels) }}</span></a>
                            @else
                                <span class="btn btn-default disabled"><i class="fa fa-times-circle"></i></span>
                        @endif
                        </td>
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>
@stop
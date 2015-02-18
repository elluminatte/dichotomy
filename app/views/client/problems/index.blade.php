@extends('layouts.master')
@section('content')
        <div class="col-md-12">
            <div class="page-header">
                <h3><i class="fa fa-angle-double-down"></i> Каталог проблемных ситуаций</h3>
            </div>
{{--            {{ Breadcrumbs::render('situations', $parent_tree) }}--}}
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Решаемые задачи</th>
                </tr>
                </thead>
                <tbody>
                @foreach($situations as $situation)
                    <tr>
                        @if( isset($situation->children) && count($situation->children))
                            <td class="td-align_left"><a href="{{ URL::route('problems.list', ['iParentProblemId' => $situation->id])}}"><i class="fa fa-level-down"></i> {{ $situation->name }}</a></td>
                        @else
                            <td class="td-align_left">{{ $situation->name }}</td>
                        @endif
                        <td>
                        @if( isset($situation->modelsId) && count($situation->modelsId))
                        <a title="Решаемые задачи" href="{{ URL::route('models.list', ['iSituationId' => $situation->id]) }}" class="btn btn-primary"><i class="fa fa-share"></i></a>
                            @else
                                <span class="btn btn-default disabled"><i class="fa fa-times-circle"></i></span>
                        @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
@stop
@extends('layouts.master')
@section('title')
    Администрирование. Список решаемых задач
@stop
@section('content')
    {{--немного магии, взяли из сессии имя шаблона результата операции (удаление, изменение, добавление) и отрисовали его вот тут--}}
    @if ( Session::get('form_result') )
            @include('admin.forms.'.Session::get('form_result'))
    @endif
            <div class="page-header">
                <h3><i class="fa fa-angle-double-down"></i> Список решаемых задач</h3>
            </div>
                {{ Breadcrumbs::render('admin.models', $hierarchy, 'list') }}
                <a title="Добавить задачу" href="{{ URL::route('models.create', ['iSituationId' => $situation_id]) }}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Добавить задачу</a>
            <div style="margin-bottom: 20px"></div>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Редактирование параметров</th>
                    <th>Удалить задачу</th>
                </tr>
                </thead>
                <tbody>
                @if(!$models->isEmpty())
                @foreach($models as $model)
                    <tr>
                        <td class="td-align_left">@if($model->threshold < $model->min_threshold)<i class="fa fa-lock"></i>@endif <a href="{{ URL::route('models.detail', ['iModelId' => $model->id])}}">{{ $model->name }}</a></td>
                        <td><a title="Редактировать параметры" href="{{ URL::route('models.edit', ['iModelId' => $model->id]) }}" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                        <td><a title="Удалить задачу" data-href="{{ URL::route('models.destroy', ['iModelId' => $model->id]) }}" class="btn btn-danger" type="button" data-toggle="modal" data-target="#delModal"><i class="fa fa-times"></i></a></td>
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>
    <div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Подтверждение удаления</h4>
                </div>
                <div class="modal-body">
                    Действительно хотите удалить эту запись?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Нет</button>
                    <a href="#" class="btn btn-danger danger">Да</a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    {{ HTML::script('assets/js/list.js') }}
@stop
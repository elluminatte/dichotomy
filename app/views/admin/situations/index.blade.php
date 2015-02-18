@extends('layouts.master')
@section('content')
    <div class="row">
    </div>
    {{--немного магии, взяли из сессии имя шаблона результата операции (удаление, изменение, добавление) и отрисовали его вот тут--}}
    @if ( Session::get('form_result') )
        <div class="row">
            @include('admin.forms.'.Session::get('form_result'))
        </div>
    @endif
        <div class="col-lg-12">
            <div class="page-header">
                <h3><i class="fa fa-angle-double-down"></i> Каталог проблемных ситуаций</h3>
            </div>
                {{ Breadcrumbs::render('situations', $parent_tree) }}
                <a title="Добавить ситуацию" href="{{ URL::route('situations.create', ['iParentSituationId' => $parent_situation]) }}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Добавить ситуацию</a>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Соответствие ОКВЭД</th>
                    <th>Редактировать реквизиты</th>
                    <th>Удалить</th>
                    <th>Решаемые задачи</th>
                </tr>
                </thead>
                <tbody>
                @foreach($situations as $situation)
                    <tr>
                        <td class="td-align_left"><a href="{{ URL::route('situations.list', ['iParentSituationId' => $situation->id]) }}"><i class="fa fa-level-down"></i> {{ $situation->name }}</a></td>
                        <td>{{ $situation->okved_correspondence }}</td>
                        <td><a title="Редактировать реквизиты записи" href="{{ URL::route('situations.edit', ['iSituationId' => $situation->id]) }}" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                        <td><a title="Удалить запись" data-href="{{ URL::route('situations.destroy', ['iSituationId' => $situation->id]) }}" class="btn btn-danger" type="button" data-toggle="modal" data-target="#delModal"><i class="fa fa-times"></i></a></td>
                        <td><a title="Решаемые задачи" href="{{ URL::route('models.list', ['iSituationId' => $situation->id]) }}" class="btn btn-warning"><i class="fa fa-question-circle"></i></a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
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
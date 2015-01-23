@extends('layouts.master')
@section('content')
    <div class="row">
        {{ Breadcrumbs::render('okvedList', $breadcrumbs) }}
    </div>
    <div class="row">
        <a href="{{ URL::route('addOkvedForm', array('parentId' => $parentId)) }}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Добавить</a>
    </div>
    <div class="row">
        <div class="col-lg-10">
            <div class="page-header">
                <h3><i class="fa fa-angle-double-down"></i> Классификационные группы</h3>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Соответствие ОКВЭД</th>
                    <th>Изменить</th>
                    <th>Удалить</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($sections as $section)
                        <tr>
                            <td><a href="{{ URL::route('okvedList', array('sectionId' => $section->id)) }}">{{ $section->name }}</a></td>
                            <td>{{ $section->okved_correspondence }}</td>
                            <td><a href="{{ URL::route('editOkvedForm', array('sectionId' => $section->id)) }}" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                            {{-- ToDo: Подтверждение удаления --}}
                            <td><a data-href="{{ URL::route('delOkved', array('sectionId' => $section->id)) }}" class="btn btn-primary" type="button" data-toggle="modal" data-target="#delModal"><i class="fa fa-times"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
    {{ HTML::script('assets/js/okved/okved.js') }}
@stop
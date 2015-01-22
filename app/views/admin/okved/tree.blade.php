@extends('......layouts.master')
@section('content')
    <div class="row">
        <ul class="breadcrumb">
            <li><a href="#">Home</a></li>
            <li><a href="#">Library</a></li>
            <li class="active">Data</li>
        </ul>
    </div>
    <div class="row">
        <button type="button" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Добавить</button>
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
                            <td><a href="#">{{ $section->name }}</a></td>
                            <td>{{ $section->okved_correspondence }}</td>
                            <td><button class="btn btn-primary" type="button"><i class="fa fa-pencil"></i></button></td>
                            <td><button class="btn btn-primary" type="button" data-toggle="modal" data-target="#delModal"><i class="fa fa-times"></i></button></td>
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
                    <button type="button" class="btn btn-primary">Да</button>
                </div>
            </div>
        </div>
    </div>
@stop
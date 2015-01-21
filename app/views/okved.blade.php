@extends('layouts.master')
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
                </tr>
                </thead>
                <tbody>
                @foreach($okveds as $okved)
                    <tr>
                        <td>{{ $okved->name }}</td>
                        <td>{{ $okved->okved_correspondence }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
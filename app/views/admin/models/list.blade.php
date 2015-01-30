@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-lg-10">
            <div class="page-header">
                <h3><i class="fa fa-angle-double-down"></i> Список решаемых задач</h3>
            </div>
            <div class="row">
                <a title="Добавить задачу" href="{{ URL::route('addOkvedForm') }}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Добавить задачу</a>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Название</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop
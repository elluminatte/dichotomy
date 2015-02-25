@extends('layouts.master')
@section('content')
    {{ Breadcrumbs::render('evaluations', 'list') }}
    <div class="col-lg-10">
        <div class="page-header">
            <h3><i class="fa fa-angle-double-down"></i> Решения, которые ждут Вашей обратной связи</h3>
        </div>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Модель</th>
                <th>Окончание времени корректности решения</th>
                <th>Перейти к обратной связи</th>
            </tr>
            </thead>
            <tbody>
            @if(!$evaluations->isEmpty())
            @foreach($evaluations as $evaluation)
            <tr>
                <td>{{ $evaluation->model->name }}</td>
                <td>{{ $evaluation->expired_moment }}</td>
                <td><a title="Перейти к обратной связи" href="{{ URL::route('evaluations.detail', ['iEvaluationId' => $evaluation->id]) }}" class="btn btn-primary"><i class="fa fa-send-o"></i></a></td>
            </tr>
            @endforeach
            @endif
            </tbody>
            </table>
    </div>
@stop
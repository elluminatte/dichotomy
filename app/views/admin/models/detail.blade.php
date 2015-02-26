@extends('layouts.master')
@section('content')
    {{ Breadcrumbs::render('admin.models', $hierarchy, 'detail', $model->id) }}
    <a href="{{ URL::route('models.dump', ['iModelId' => $model->id]) }}">Скачать основную обучающую выборку</a>
    <blockquote>
        <p>@if($model->threshold < $model->min_threshold)<i class="fa fa-lock"></i>@endif {{ $model->name }}</p>
        <small>{{ $model->comment }}</small>
        <small>Результат - {{ $model->reg_name }} ({{ $model->reg_comment }})</small>
    </blockquote>
    <div class="col-lg-4">
    <ul class="list-group">
        <li class="list-group-item">
            <span class="badge">{{ $model->min_threshold }}</span>
            Минимальный порог отсечения
        </li>
        <li class="list-group-item">
            <span class="badge">{{ $model->threshold }}</span>
            Фактический порог отсечения
        </li>
        <li class="list-group-item">
            <span class="badge">{{ $model->curve_area }}</span>
            Площадь под кривой
        </li>
    </ul>
    </div>
    <div class="row">
    <div class="col-lg-10">
        <table class="table table-striped table-hover">
            <thead>
            </thead>
            <tbody>
            <tr>
                <th><i class="fa fa-tag"></i> Название</th>
                <th>Свободный член</th>
                @if(!empty($model->cov_names))
                @foreach($model->cov_names as $cov_name)
                    <th> {{ $cov_name }}</th>
                @endforeach
                @endif
            </tr>
            <tr>
                <th><i class="fa fa-tachometer"></i> Единицы измерения</th>
                <td>-</td>
                @if(!empty($model->cov_comments))
                @foreach($model->cov_comments as $cov_comment)
                    <td> {{ $cov_comment }}</td>
                @endforeach
                @endif
            </tr>
            <tr>
                <th><i class="fa fa-sliders"></i> Коэффициенты</th>
                @foreach($model->coefficients as $coefficient)
                    <td> {{ $coefficient }}</td>
                @endforeach
            </tr>
            <tr>
                <th><i class="fa fa-link"></i> Эластичные коэффициенты</th>
                <td>-</td>
                @if(!empty($model->elastic_coeff))
                @foreach($model->elastic_coeff as $elastic_coeff)
                    <td> {{ $elastic_coeff }}</td>
                @endforeach
                @endif
            </tr>
            <tr>
                <th><i class="fa fa-signal"></i> Стандартизованные коэффициенты</th>
                <td>-</td>
                @if(!empty($model->elastic_coeff))
                @foreach($model->std_coeff as $std_coeff)
                    <td> {{ $std_coeff }}</td>
                @endforeach
                @endif
            </tr>
            </tbody>
        </table>
    </div>
    </div>
@stop
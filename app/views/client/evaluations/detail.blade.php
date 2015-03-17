@extends('layouts.master')
@section('title')
    Обратная связь
@stop
@section('content')
{{--<div class="col-lg-row">--}}
    {{--<blockquote>--}}
        {{--<p>{{ $model->name }}</p>--}}
        {{--<small>{{ $model->comment }}</small>--}}
    {{--</blockquote>--}}
{{--</div>--}}
    {{ Breadcrumbs::render('evaluations', 'detail', $model_id) }}
<div class="col-lg-4">
    <ul class="list-group">
        <li class="list-group-item">
    <div>Результат, полученный моделью <span class="badge">{{ $estimated_result }} {{ $estimated_result ? ' (Да)' : ' (Нет)' }}</span></div>
            </li>
        </ul>
    </div>
<div class="col-lg-10">
    {{ Form::open(array('route' => 'evaluations.confirm', 'class' => 'form-horizontal')) }}
    <fieldset>
        <legend>Выберите фактический результат</legend>
    <div class="form-group">
        {{ Form::label('real_result', 'Реальный результат', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-5">
            {{ Form::select('real_result', ['-1' => 'Нет сведений', '0' => '0 - Нет', '1' => '1 - Да'], null, ['class' => 'form-control']) }}
            <span class="help-block">1 - Да; 0 - Нет</span>
        </div>
    </div>

    {{ Form::hidden('evaluation_id', $iEvaluationId) }}
    <div class="form-group">
        <div class="col-lg-10 col-lg-offset-2">
            {{ Form::submit('Ок', ['class' => 'btn btn-primary']) }}
        </div>
    </div>
    </fieldset>
    {{ Form::close() }}
    <p class="text-primary" id="eval_form_collapse">Показать значения параметров</p>
    </div>
    @if ( !$errors->isEmpty() )
        @foreach($errors->all() as $error)
            <div class="col-lg-5">
                <div class="alert alert-dismissable alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {{ $error }}.
                </div>
            </div>
        @endforeach
    @endif
<span  id="eval_form" class="collapse">
<div class="col-lg-10">
<div class="row">
    {{ Form::open(array('class' => 'form-horizontal')) }}
    <fieldset>
        @if(!empty($form))
        @foreach($form as $field)
            <div class="form-group">
                {{ Form::label($field['tech_name'], $field['name'], ['class' => 'col-lg-2 control-label']) }}
                <div class="col-lg-5">
                    {{Form::number($field['tech_name'], null, ['id' => $field['tech_name'], 'class' => 'form-control', 'placeholder' => $field['value'], 'disabled']);}}
                    <span class="help-block">{{$field['comment']}}</span>
                </div>
            </div>
        @endforeach
        @endif
    </fieldset>
    {{ Form::close() }}
    </div>
    </div>
    </span>
@stop
@section('scripts')
    {{ HTML::script('assets/js/evaluation.js') }}
@stop
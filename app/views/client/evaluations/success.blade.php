@extends('layouts.master')
@section('content')
    <div class="col-lg-10">
        <div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Всё прошло успешно!</strong> Спасибо за Вашу обратную связь, теперь Вы можете <a class="alert-link" href="{{ URL::route('problems.list') }}">решать задачи без ограничений</a>.
        </div>
    </div>
@stop
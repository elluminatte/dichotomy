@extends('layouts.master')
@section('content')
@if (count($result))
    <div class="alert alert-dismissable alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>Удаление записи прошло успешно!</strong> Теперь можно вернуться <a href="{{ URL::previous() }}" class="alert-link">на предыдущую страницу</a>.
    </div>
@else
    <div class="alert alert-dismissable alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>Ой!</strong> При удалении записи произошла ошибка. Попробуйте вернуться <a href="{{ URL::previous() }}" class="alert-link">на предыдущую страницу</a> и повторить попытку.
    </div>
@endif
@stop
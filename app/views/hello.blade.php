@extends('layouts.master')
@section('content')
	<div class="well">
		{{ link_to_route('situations.list', 'Каталог проблемных ситуаций') }}
	</div>
@stop
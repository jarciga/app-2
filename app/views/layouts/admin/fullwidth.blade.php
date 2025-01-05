{{-- Child Template --}}
@extends('layouts.base')

@section('head')
	@parent
@stop

@section('body')	
<div class="full-width">@yield('fullwidth')</div>
@stop

@section('foot')
	@parent    
@stop
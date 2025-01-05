{{-- Child Template --}}
@extends('layouts.base')

@section('head')
	@parent
@stop

@section('body')	
<div class="content">@yield('content')</div>
<div class="sidebar">@yield('sidebar')</div>
@stop

@section('foot')
	@parent    
@stop
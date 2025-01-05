{{-- Child Template --}}
@extends('layouts.base')

@section('head')
	@parent
@stop

@section('body')	
<div class="sidebar">@yield('primarysidebar')</div>
<div class="content">@yield('content')</div>
<div class="sidebar">@yield('secondarysidebar')</div>
@stop

@section('foot')
	@parent    
@stop
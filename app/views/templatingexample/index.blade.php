{{-- Child Template --}}
@extends('sidebarcontent')

@section('head')
	@parent
@stop

@section('sidebar')
	sidebar
	@include('navigation')
@stop

@section('content')
	content
@stop

@section('foot')
	@parent    
@stop
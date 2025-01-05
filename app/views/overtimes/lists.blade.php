{{-- Child Template --}}
@extends('layouts.admin.sidebarcontent')

@section('head')
	@parent

@stop

@section('sidebar')
	@include('partials.admin.sidebar')
@stop

@section('content')		

  @include('overtimes._lists')

@stop

@section('foot')
	@parent    

@stop
{{-- Child Template --}}
@extends('layouts.base')

@section('head')
	@parent

    <!-- Custom styles for this template -->
    <link href="{{ URL::asset('assets/css/dashboard.css') }}" rel="stylesheet">   
    <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet">   	
@stop

@section('body')

@include('partials.navigationtop')

<div class="container-fluid">
  <div class="row">

	<div class="col-sm-3 col-md-2 sidebar">
	@yield('sidebar')
	</div>

	<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
		@yield('content')
	</div>

  </div>
</div>

@stop

@section('foot')
	@parent    
@stop
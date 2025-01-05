{{-- Child Template --}}
@extends('layouts.base')

@section('head')
	@parent
@stop

@section('body')
@include('partials.navigationtop')

<div class="container-fluid">
  <div class="row">

	<div class="col-sm-12 col-md-12 main">
		@yield('fullwidth')
	</div>

  </div>
</div>

@stop

@section('foot')
	@parent    
@stop
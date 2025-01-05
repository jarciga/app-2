{{-- Child Template --}}
@extends('layouts.fullwidth')

@section('head')
	@parent
    <link href="{{ URL::asset('assets/css/fullwidth.css') }}" rel="stylesheet">   
    <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet">  	

@stop

@section('fullwidth')		

  @include('leave._leaveform')

@stop

@section('foot')
	@parent    

<script>
	//#Jquery UI
		
	
	$(function() {

		$( ".datepicker" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			minDate: 0,
			maxDate: new Date(new Date().setYear(new Date().getFullYear() + 1))
		});

		var othersCb = $( "input[value='others']" ).prop("checked");		

		if(othersCb) {

			$("#other-nature-of-leave").show().removeClass("hide hidden");;

		} else {

			$("#other-nature-of-leave").hide().addClass("hide hidden");

		}

		$( "[name=nature_of_leave]" ).click(function() {

			var othersCb = $( "input[value='others']" ).prop("checked");

			if(othersCb) {

				$("#other-nature-of-leave").show().removeClass("hide hidden");;

			} else {

				$("#other-nature-of-leave").hide().addClass("hide hidden");

			}

		});	  

	});	
</script>


@stop
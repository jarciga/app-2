{{-- Child Template --}}
@extends('layouts.admin.fullwidth')

@section('head')
	@parent

@stop

@section('content')		

@stop

@section('foot')
	@parent    

<script>

/**
*
* SIDEBAR
*
*/

$(function () {
$('#collapse1, #collapse2, #collapse3').collapse('hide');        
$('#collapse4, #collapse5, #collapse6').collapse('show');        

$('#menu').metisMenu();
});    

//#Jquery UI
$(function() {
  $( ".datepicker" ).datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
  });

});

</script>	

@stop
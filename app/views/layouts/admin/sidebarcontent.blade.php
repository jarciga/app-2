{{-- Child Template --}}
@extends('layouts.base')

@section('head')
	@parent	

	<!-- Custom styles for this template -->
    <link href="{{ URL::asset('assets/css/admin/dashboard.css') }}" rel="stylesheet"> 	
	<link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet">	
	<link href="{{ URL::asset('assets/css/metisMenu-default-theme.css') }}" rel="stylesheet">  	
@stop

@section('body')

@include('partials.admin.navigationtop')

<div class="container-fluid">
  <div class="row">

	<div class="col-sm-2 col-md-2 sidebar">
	@yield('sidebar')
	</div>

	<div class="col-sm-10 main">
		@yield('content')
	</div>

  </div>
</div>

@stop

@section('foot')
	@parent  

  <script>

  $(function () {
  $('#collapse1, #collapse2, #collapse3').collapse('hide');        
  $('#collapse4, #collapse5, #collapse6').collapse('show');        

  $('#menu').metisMenu();

  $('.dropdown-toggle').dropdown()
  });  

  //check all checkboxes
  $('tbody').children().children('.check-column').find(':checkbox').click( function(e) {
    if ( 'undefined' == e.shiftKey ) { return true; }
    if ( e.shiftKey ) {
      if ( !lastClicked ) { return true; }
      checks = $( lastClicked ).closest( 'form' ).find( ':checkbox' );
      first = checks.index( lastClicked );
      last = checks.index( this );
      checked = $(this).prop('checked');
      if ( 0 < first && 0 < last && first != last ) {
        sliced = ( last > first ) ? checks.slice( first, last ) : checks.slice( last, first );
        sliced.prop( 'checked', function() {
          if ( $(this).closest('tr').is(':visible') )
            return checked;

          return false;
        });
      }
    }
    lastClicked = this;

    // toggle "check all" checkboxes
    var unchecked = $(this).closest('tbody').find(':checkbox').filter(':visible').not(':checked');
    $(this).closest('table').children('thead, tfoot').find(':checkbox').prop('checked', function() {
      return ( 0 === unchecked.length );
    });

    return true;
  });

  $('thead, tfoot').find('.check-column :checkbox').on( 'click.wp-toggle-checkboxes', function( event ) {
    var $this = $(this),
      $table = $this.closest( 'table' ),
      controlChecked = $this.prop('checked'),
      toggle = event.shiftKey || $this.data('wp-toggle');

    $table.children( 'tbody' ).filter(':visible')
      .children().children('.check-column').find(':checkbox')
      .prop('checked', function() {
        if ( $(this).is(':hidden') ) {
          return false;
        }

        if ( toggle ) {
          return ! $(this).prop( 'checked' );
        } else if ( controlChecked ) {
          return true;
        }

        return false;
      });

    $table.children('thead,  tfoot').filter(':visible')
      .children().children('.check-column').find(':checkbox')
      .prop('checked', function() {
        if ( toggle ) {
          return false;
        } else if ( controlChecked ) {
          return true;
        }

        return false;
      });
  });

  </script>	
@stop
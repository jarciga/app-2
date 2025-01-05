<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Schedules</h3>
  </div>  
  <div class="panel-body">

	{{ Form::open(array('route' => 'search.user.lists', 'id' => 'formScheduleSearchLists', 'class' => 'form-horizontal hide hiden')) }}

		<div class="form-group">
		    {{ Form::label('Search', 'Search', array('class' => 'col-sm-3 control-label')) }}
		    <div class="col-sm-3">
		    {{ Form::text('s', '', array('id' => '', 'class' => 'form-control', 'placeholder' => '')) }}
		    </div>
		    <div class="col-sm-3">                        
	        {{ Form::submit('Search', array('class' => '', 'class' => 'btn btn-primary')) }}
	        </div>                                        
		</div>

	{{ Form::close() }}  	
 	
	<div class="tablenav top hide hidden">
		<div class="actions bulk-actions">
		  
		  <div class="form-group">
		    <label for="bulk-action-selector-top" class="screen-reader-text"></label>
		    
		    <div class="col-sm-3">
		      <select name="action" id="bulk-action-selector-top" class="form-control">
		        <option value="-1" selected="selected">Bulk Actions</option>
		        <option value="0" class="hide-if-no-js"></option>
		        <option value="1"></option>
		      </select>                      
		    </div>
		    <input type="submit" name="" id="doaction" class="btn btn-custom-default action" value="Apply" class="pull-right"> 
		  </div>

		</div>            
	</div>  	

<table class="table table-striped table-hover display list-table users" cellspacing="0" width="100%">
	<thead>
		<tr>		           				           		
       		<th>#</th>		           				           		
       		<th>Description</th>                        		           				           
       		<th>Type</th>						
       		<th>From</th>
       		<th>To</th>
       		<th>Status</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
    	<?php
		$clockingStatus = '';
		foreach($listSchedules as $listSchedule):	

			if ( $listSchedule->holiday_status === 1 ) {
					
				$holidayStatus = '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">active</span>';

			} else {

				$holidayStatus = '<span class="label label-default" style="padding: 2px 4px; font-size: 11px;">in active</span>';
				
			}

		?>

		<tr>		           				           		
       		<td>{{ $listSchedule->id }}</td>		           				           		
       		<td>{{ $listSchedule->description }}</td>                        		           				           
       		<td>{{ $listSchedule->holiday_type }}</td>						
       		<td>{{ $listSchedule->holiday_date_from }}</td>
       		<td>{{ $listSchedule->holiday_date_to }}</td>
       		<td> {{ $holidayStatus }}</td>
			<td><a href="{{ URL::to('/admin/holiday/' . $listSchedule->id . '/edit/') }}">Edit</a></td>								           		                        	
		</tr>

		<?php endforeach; ?>
    </tbody>
	<tfoot>
		<tr>		           				           		
       		<th>#</th>		           				           		
       		<th>Description</th>                        		           				           
       		<th>Type</th>						
       		<th>From</th>
       		<th>To</th>
			<th>Actions</th>						           		                        	
		</tr>
	</tfoot>
</table>    
    
    <?php echo $listSchedules->links(); ?>

  </div>
</div>
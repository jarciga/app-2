<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Leaves</h3>
  </div>  
  <div class="panel-body">

	{{ Form::open(array('route' => 'search.user.lists', 'id' => 'formLeaveSearchLists', 'class' => 'form-horizontal hide hiden')) }}

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

	{{ Form::open(array('route' => array('process.leave.lists'), 'id' => '', 'class' => 'form-horizontal')) }}	
 	
	<div class="tablenav top">
		<div class="actions bulk-actions">
		  
		  <div class="form-group">
		    <label for="bulk-action-selector-top" class="screen-reader-text"></label>
		    
		    <div class="col-sm-3">
		      <select name="action" id="bulk-action-selector-top" class="form-control">
		        <option value="-1" selected="selected">Bulk Actions</option>
		        <option value="0" class="hide-if-no-js">Denied</option>
		        <option value="1">Approved</option>
		      </select>                      
		    </div>
		    <input type="submit" name="" id="doaction" class="btn btn-custom-default action" value="Apply" class="pull-right"> 
		  </div>

		</div>            
	</div>  	

<table class="table table-striped table-hover display list-table users" cellspacing="0" width="100%">
	<thead>
		<tr>
			<!--input id="cb-select-all-1" type="checkbox"-->
			<th id="cb" class="manage-column column-cb check-column">							
				{{ Form::checkbox('', '', '', array('id' => 'cb-select-all-1')) }}
			</th>
			<th>#</th>	      
			<th>Employee #</th>
			<th>Name</th>
			<th>Nature of Leave</th>	           				           		
			<th>Reason</th>
			<th>Day(s)</th>
       		<th>Date(s)</th>								
			<th>Status</th>			
			<th class="hide hidden">Actions</th>
		</tr>
	</thead>
	<tbody>

    	<?php
    	if ( $leaveCount !== 0 ) :
    		foreach($listLeaves as $listLeave): 

    		$employeeInfo = Employee::find($listLeave->employee_id);
			$EmployeeName = $employeeInfo->firstname.' '.$employeeInfo->lastname;
    	?>

		<tr>		           				           		
			<td class="check-column">
				{{ Form::checkbox('check[]', $listLeave->id, false, array('id' => 'cb-select-'.$listLeave->id, 'class' => 'checkbox')) }}
			</td>			
       		<td>{{ $listLeave->id }}</td>
       		<td>{{ $employeeInfo->employee_number }}</td>		           				           				           				           		
       		<td>{{ $EmployeeName }}</td>		           				           				           				           		       	
       		<td>{{ $listLeave->nature_of_leave }}</td>                        		           				           
       		<td>{{ $listLeave->reason }}</td>
       		<td>{{ $listLeave->number_of_days }}</td>
			<td>{{ date('D, M d', strtotime($listLeave->from_date)) .' - '. date('D, M d', strtotime($listLeave->to_date)) }}</td>
			<td>
			<?php
			if ( $listLeave->status === NULL || $listLeave->status === -1 ):
				echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Pending</span>';
			elseif ( $listLeave->status === 0 ):
				echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Denied</span>';
			elseif ( $listLeave->status === 1 ):
				echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Approved</span>';
			endif;
			?>							
			</td>
			<td class="hide hidden"><a href="{{ URL::to('/admin/leave/' . $listLeave->id . '/edit/') }}">Edit</a></td>			

		</tr>

		<?php		
			endforeach;
		endif;
		?>
    </tbody>
	<tfoot>
		<tr>
			<!--input id="cb-select-all-1" type="checkbox"-->
			<th id="cb" class="manage-column column-cb check-column">							
				{{ Form::checkbox('', '', '', array('id' => 'cb-select-all-1')) }}
			</th>
			<th>#</th>	   
			<th>Employee #</th>
			<th>Name</th>   
			<th>Nature of Leave</th>	           				           		
			<th>Reason</th>
			<th>Day(s)</th>
       		<th>Date(s)</th>								
			<th>Status</th>
			<th class="hide hidden">Actions</th>
			
		</tr>
	</tfoot>
</table>  

{{ Form::close() }} 
    
    <?php 
    if( !empty($listLeaves) ) {

    	echo $listLeaves->links(); 
    	
    }
    ?>

  </div>
</div>
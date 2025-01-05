<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Absent Lists: {{ $employee->firstname }} {{ $employee->lastname }}</h3>
  </div>  
  <div class="panel-body">

 	{{ Form::open(array('route' => array('process.absent.lists', $employee->id), 'id' => '', 'class' => 'form-horizontal')) }} 
	<div class="tablenav top">
		<div class="actions bulk-actions">
		  
		  <div class="form-group">
		    <label for="bulk-action-selector-top" class="screen-reader-text"></label>
		    
		    <div class="col-sm-3">
		      <select name="action" id="bulk-action-selector-top" class="form-control">
		        <option value="-1" selected="selected">Bulk Actions</option>
		        <option value="0" class="hide-if-no-js">Absent without leave</option>
		        <option value="1">Absent with leave</option>
		      </select>                      
		    </div>
		    <input type="submit" name="" id="doaction" class="btn btn-custom-default action" value="Apply" class="pull-right"> 
		  </div>

		</div>            
	</div>  	
	
  	

    <table class="table table-striped table-hover table-condensed display table-list">
      <thead>
        <tr>
			<th id="cb" class="manage-column column-cb check-column">							
			{{ Form::checkbox('', '', '', array('id' => 'cb-select-all-1')) }}
			</th>
			<th>Date</th>          
			<th>Status</th>
        </tr>
      </thead>
      <tbody>
	  <?php

	  	/*$perPage = 2;
		$currentPage = Input::get('page') - 1;
		$items = array_slice($listCurrentAbsencesPerCutoff, $currentPage * $perPage, $perPage);
		$totalItems = sizeof($listCurrentAbsencesPerCutoff);

		$listCurrentAbsencesPerCutoff = Paginator::make($items, $totalItems, $perPage);*/

		$listCurrentAbsencesPerCutoff = simplePaginateArray($listCurrentAbsencesPerCutoff, $perPage = 15);

	  	for($i = 0; $i < sizeof($listCurrentAbsencesPerCutoff); $i++) : 	    	  		
	  ?>
      <tr>
		<td class="check-column">
			{{ Form::checkbox('check[]', $employee->id.'|'.$listCurrentAbsencesPerCutoff[$i]["schedule_date"], false, array('id' => 'cb-select-'.$employee->id, 'class' => 'checkbox')) }}
		</td>
        <td><?php echo $listCurrentAbsencesPerCutoff[$i]["schedule_date"]; ?></td>        
		<td><?php echo $listCurrentAbsencesPerCutoff[$i]["absent"]; ?></td>                
      </tr>
      <?php endfor; ?>
  	  </tbody>
    </table>    
    {{ Form::close() }}  	

    <?php echo $listCurrentAbsencesPerCutoff->links(); ?>

  </div>
</div>
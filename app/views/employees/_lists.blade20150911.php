<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Users</h3>
  </div>  
  <div class="panel-body">

	{{ Form::open(array('route' => 'search.user.lists', 'id' => 'formUserSearchLists', 'class' => 'form-horizontal')) }}

		<div class="form-group">
		    {{ Form::label('Search', 'Search', array('class' => 'col-sm-3 control-label')) }}
		    <div class="col-sm-3">
		    {{ Form::text('s', '', array('id' => '', 'class' => 'form-control', 'placeholder' => '')) }}
		    </div>
		    <div class="col-sm-3">                        
	        {{ Form::submit('Filter', array('class' => '', 'class' => 'btn btn-primary')) }}
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
		           		<th>Employee Number</th>		           				           		
		           		<th>Full Name</th>                        
		           		<th>Nick Name</th>		           	
		           		<th>Group</th>
						<th>Date</th>		           		                        							
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
                <?php foreach( $listEmployees as $listEmployee ): ?>
	
					<tr>
						<td><?php echo $listEmployee->id; ?></td>	
		           		<td><?php echo $listEmployee->employee_number; ?></td>		           				           		
		           		<td><?php echo $listEmployee->firstname.' '.$listEmployee->middle_name.', '.$listEmployee->lastname; ?></td>                        
		           		<td><?php echo $listEmployee->nick_name; ?></td>		           	
						<td><?php echo $listEmployee->name; ?></td>	
						<td><?php echo $listEmployee->created_at; ?></td>	

						<td><a href="{{ URL::to('/admin/user/' . $listEmployee->employee_id . '/edit/') }}">Edit</a> <span class="hide hidden">|</span> <a href="{{ URL::to('/admin/user/' . $listEmployee->employee_id . '/delete/') }}" class="hide hidden">Delete</a></td>						
					</tr>
						
				<?php endforeach; ?>
                </tbody>
				<tfoot>
					<tr>
						<th>#</th>		           				           		
		           		<th>Employee Number</th>		           				           		
		           		<th>Full Name</th>                        
		           		<th>Nick Name</th>		           	
		           		<th>Group</th>
						<th>Date</th>	
						<th>Actions</th>							           		                        	
					</tr>
				</tfoot>
			</table>    
    {{ Form::close() }}  	

    <?php echo $listEmployees->links(); ?>

  </div>
</div>
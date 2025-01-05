<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Employees</h3>
  </div>  
  <div class="panel-body">

	{{ Form::open(array('route' => 'search.user.lists', 'id' => 'formUserSearchLists', 'class' => 'form-horizontal hide hidden')) }}

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
       		<th>Employee No.</th>		           				           		
       		<th>Name</th>                        		           				           
       		<th class="hide hidden">Company</th>						
       		<th class="hide hidden">Department</th>
       		<th class="hide hidden">Manager</th>
       		<th class="hide hidden">Supervisor</th>		           		
       		<th>Status</th>		           		
			<th class="hide hidden">Actions</th>
		</tr>
	</thead>
	<tbody>
    		<?php
		$clockingStatus = '';
		if( !empty($listEmployees) ) :
			foreach($listEmployees as $listEmployee): 

				$company = DB::table('companies')->where('id', $listEmployee->company_id)->get(); 							
				$department = DB::table('departments')->where('id', $listEmployee->department_id)->get(); 							
				$manager = DB::table('employees')->where('id', $listEmployee->manager_id)->get(); 
				$supervisor = DB::table('employees')->where('id', $listEmployee->supervisor_id)->get();
				//$jobTitle = DB::table('job_title')->where('id', $listEmployee->position_id)->get();
				
				//$listEmployeesByManager = DB::table('employees')->where('manager_id', $listEmployeeId)->get();

				$timesheet = DB::table('employee_timesheet')->where('employee_id', $listEmployee->id)->where('daydate', trim($currentDate))->first();
				$schedule = DB::table('employee_schedule')->where('employee_id', $listEmployee->id)->where('schedule_date', trim($currentDate))->get();							
				
				if( !empty($timesheet) ) {

					//var_dump($timesheet->employee_id.' '.$timesheet->clocking_status);
					
					if ( $timesheet->clocking_status === 'clock_in_1' ||
						 $timesheet->clocking_status === 'clock_in_2' ||
						 $timesheet->clocking_status === 'clock_in_3' ) {
						
						$clockingStatus = '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">in</span>';

					} elseif ( $timesheet->clocking_status === 'clock_out_1' ||
						 $timesheet->clocking_status === 'clock_out_2' ||
						 $timesheet->clocking_status === 'clock_out_3' ) {
						
						$clockingStatus = '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">out</span>';

					} elseif ( $timesheet->clocking_status === 'open' ) {

						$clockingStatus = '<span class="label label-default" style="padding: 2px 4px; font-size: 11px;">open</span>';

					}

				} else {

						$clockingStatus = '<span class="label label-default" style="padding: 2px 4px; font-size: 11px;">open</span>';
					
				}

		?>	

			<?php if ( !empty($schedule) ) { ?>
				
				<?php
				$scheduled['start_time'] = $schedule[0]->start_time;
				$scheduled['end_time'] = $schedule[0]->end_time;			
				?>
				<tr>
				<td><?php  echo $listEmployee->employee_number; ?></td>
				<td><?php  echo $listEmployee->firstname.', '.$listEmployee->lastname; ?></td>
				<td class="hide hidden">
				<?php if( !empty($company) ) { ?>
					<?php  echo $company[0]->name; ?>
				<?php } ?>
				</td>
				<td class="hide hidden">
				<?php if( !empty($department) ) { ?>
					<?php  echo $department[0]->name; ?>
				<?php } ?>
				</td>
				<td class="hide hidden">
				<?php if( !empty($manager) ) { ?>
					<?php  echo $manager[0]->firstname.', '.$manager[0]->lastname; ?>
				<?php } ?>
				</td>
				<td class="hide hidden">
				<?php if( !empty($supervisor) ) { ?>
					<?php  echo $supervisor[0]->firstname.', '.$supervisor[0]->lastname; ?>
				<?php } ?>
				</td>								
				<td><?php  echo $clockingStatus; ?></td>
				<td class="hide hidden"><a href="{{ URL::to('/admin/user/' . $listEmployee->id . '/edit/') }}">Edit</a></td>								
				</tr>

			<?php } else { ?>
				<tr>
				<td><?php  echo $listEmployee->employee_number; ?></td>
				<td><?php echo $listEmployee->firstname.', '.$listEmployee->lastname; ?></td>
				<td class="hide hidden">
				<?php if( !empty($company) ) { ?>
					<?php  echo $company[0]->name; ?>
				<?php } ?>
				</td>								
				<td class="hide hidden">
				<?php if( !empty($department) ) { ?>
					<?php  echo $department[0]->name; ?>
				<?php } ?>
				</td>
				<td class="hide hidden">
				<?php if( !empty($manager) ) { ?>
					<?php  echo $manager[0]->firstname.', '.$manager[0]->lastname; ?>
				<?php } ?>
				</td>
				<td class="hide hidden">
				<?php if( !empty($supervisor) ) { ?>
					<?php  echo $supervisor[0]->firstname.', '.$supervisor[0]->lastname; ?>
				<?php } ?>
				</td>									
				<td><?php echo $clockingStatus; ?></td>
				<td class="hide hidden"><a href="{{ URL::to('/admin/user/' . $listEmployee->id . '/edit/') }}">Edit</a></td>
				</tr>

			<?php } ?>
			

		<?php 
			endforeach;
		endif;
		?>
    </tbody>
	<tfoot>
		<tr>		           				           		
       		<th>Employee No.</th>		           				           		
       		<th>Name</th>                        		           	
       		<th class="hide hidden">Company</th>						
       		<th class="hide hidden">Department</th>
       		<th class="hide hidden">Manager</th>
       		<th class="hide hidden">Supervisor</th>		           		
       		<th>Status</th>		           		
			<th class="hide hidden">Actions</th>							           		                        	
		</tr>
	</tfoot>
</table>    
    
    <?php //echo $listEmployees->links(); ?>

  </div>
</div>
<?php //var_dump($overtimes); ?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Overtime Lists</h3>
  </div>  
  <div class="panel-body">


{{ Form::open(array('route' => array('process.overtime.lists'), 'id' => '', 'class' => 'form-horizontal')) }} 

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


	<table class="table table-striped table-hover display table-list" cellspacing="0" width="100%">
		<thead>
			<tr>
				<!--input id="cb-select-all-1" type="checkbox"-->
				<th id="cb" class="manage-column column-cb check-column">							
					{{ Form::checkbox('', '', '', array('id' => 'cb-select-all-1')) }}
				</th>
				<th>Timesheet ID</th>	           				           		
   				           		
           		<th>Date</th>
           		<th>Full Name</th>
           		<th>1st OT</th>			           		
				<th>2nd OT</th>			           					           		                        
				<th>3rd OT</th>			           					           		                        
				<th>Status</th>
				
			</tr>
		</thead>
		<tbody>
			
        <?php foreach($overtimes as $overtime):

        	//$employee = DB::table('employees')->where('id', $overtime->employee_id)->first();	
			//$timesheet = DB::table('employee_timesheet')->where('id', $overtime->timesheet_id)->first();		                	

         ?>

			<tr>
				<!--input id="cb-select-8" type="checkbox" name="post[]" value="8"-->							
				<td class="check-column">
					{{ Form::checkbox('check[]', $overtime->id, false, array('id' => 'cb-select-'.$overtime->id, 'class' => 'checkbox')) }}
				</td>
				<td><?php echo $overtime->timesheet_id; ?></td>		           				           	
   				      
           		<td><?php echo date('D, M d', strtotime($overtime->daydate)); ?></td>		           				      
           		<td><?php echo $overtime->firstname.' '.$overtime->middle_name.', '.$overtime->lastname; ?></td>           
				
				<td>

				<?php if ( ( ($overtime->seq_no === 1 && $overtime->overtime_status === -1) ||
						   ($overtime->seq_no === 1 && $overtime->overtime_status === 1) ||
						   ($overtime->seq_no === 1 && $overtime->overtime_status === 0) ) ) : ?>

							<?php echo $overtime->total_overtime_1; ?>
					
				<?php endif; ?>
				</td>


				<td>

				<?php if ( ( ($overtime->seq_no === 2 && $overtime->overtime_status === -1) ||
						   ($overtime->seq_no === 2 && $overtime->overtime_status === 1) ||
						   ($overtime->seq_no === 2 && $overtime->overtime_status === 0) ) ) : ?>

							<?php echo $overtime->total_overtime_2; ?>

				<?php endif; ?>
				</td>					

				<td>

				<?php if ( ( ($overtime->seq_no === 3 && $overtime->overtime_status === -1) ||
							($overtime->seq_no === 3 && $overtime->overtime_status === 1) ||
							($overtime->seq_no === 3 && $overtime->overtime_status === 0) ) ) : ?>								
					
							<?php echo $overtime->total_overtime_3; ?>
				
				<?php endif; ?>								
				</td>

				<td>
					<?php
					if ( $overtime->overtime_status === -1 ):
						echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Pending</span>';
					elseif ( $overtime->overtime_status === 0 ):
						echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Denied</span>';
					elseif ( $overtime->overtime_status === 1 ):
						echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Approved</span>';
					endif;
					?>							
				</td>
					
				
			</tr>

		<?php endforeach; ?>
	
        </tbody>
		<tfoot class="hide hidden">
			<!--tr>
				<div class="clearfix pull-right">                        
            	{{ Form::submit('Approve', array('class' => '', 'class' => 'btn btn-primary')) }}
            	</div>                                        
			</tr-->	
		
			<tr>
				<!--input id="cb-select-all-1" type="checkbox"-->
				<th id="cb" class="manage-column column-cb check-column">{{ Form::checkbox('', '', '', array('id' => 'cb-select-all-2')) }}</th>
				<th>Timesheet ID</th>	           				           		
   				           		
           		<th>Date</th>
           		<th>Full Name</th>
           		<th>1st OT</th>			           		
				<th>2nd OT</th>			           					           		                        
				<th>3rd OT</th>	
				<th>Status</th>
			</tr>
		</tfoot>
	</table>

{{ Form::close() }} 
	

    <?php //echo $listCurrentOvertimePerCutoff->links(); ?>

  </div>
</div>
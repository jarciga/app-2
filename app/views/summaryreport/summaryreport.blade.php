<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Summary Report</h3>
  </div>  
  <div class="panel-body">

 	
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


	<div class="table-responsive">
		
		<table class="table table-striped table-hover display list-table users" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>Lates / UT</th>
				<th>Absences</th>	
				<th>Paid SL</th>
				<th>Paid VL</th>
				<th>Leave w/o Pay</th>
				<th>Maternity Leave</th>	
				<th>Paternity Leave</th>
				<th>Regular</th>
				<th>Reg OT</th>
				<th>Reg OT+ND</th>
				<th>Reg ND</th>	
				<th>RD (First 8hrs)</th>
				<th>RD OT</th>
				<th>RD OT+ND</th>
				<th>RD ND</th>	
				<th>SPL Holiday (First 8Hrs)</th>
				<th>SPL Holiday OT</th>
				<th>SPL Holiday OT+ND</th>
				<th>SPL Holiday ND</th>	
				<th>LEGAL Holiday</th>
				<th>LEGAL Holiday OT</th>
				<th>LEGAL Holiday OT+ND</th>	
				<th>LEGAL Holiday ND</th>
				<th>RD SPL Holiday (First 8Hrs)</th>
				<th>RD SPL Holiday OT</th>
				<th>RD SPL Holiday OT+ND</th>
				<th>RD SPL Holiday ND</th>
				<th>RD LEGAL Holiday</th>
				<th>RD LEGAL Holiday OT</th>
				<th>RD LEGAL Holiday OT+ND</th>
				<th>RD LEGAL Holiday ND</th>																
			</tr>
		</thead>
		<tbody>
			<?php 
			//http://stackoverflow.com/questions/4483540/php-show-a-number-to-2-decimal-places
			//foreach( $summaries as $summary ):
			?>

				<td>{{ number_format((float) $summary->lates, 2, '.', '') . ' / ' . number_format((float) $summary->undertime, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->absent, 2, '.', '') }}</td>	
				<td>{{ number_format((float) $summary->paid_sick_leave, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->paid_vacation_leave, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->leave_without_pay, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->maternity_leave, 2, '.', '') }}</td>	
				<td>{{ number_format((float) $summary->paternity_leave, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->regular, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->regular_overtime, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->regular_overtime_night_diff, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->regular_night_differential, 2, '.', '') }}</td>	
				<td>{{ number_format((float) $summary->rest_day, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->rest_day_overtime, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->rest_day_overtime_night_diff, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->rest_day_night_differential, 2, '.', '') }}</td>	
				<td>{{ number_format((float) $summary->special_holiday, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->special_holiday_overtime, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->special_holiday_overtime_night_diff, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->special_holiday_night_diff, 2, '.', '') }}</td>	
				<td>{{ number_format((float) $summary->legal_holiday, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->legal_holiday_overtime, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->legal_holiday_overtime_night_diff, 2, '.', '') }}</td>	
				<td>{{ number_format((float) $summary->legal_holiday_night_diff, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->rest_day_special_holiday, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->rest_day_special_holiday_overtime, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->rest_day_special_holiday_overtime_night_diff, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->rest_day_special_holiday_night_diff, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->rest_day_legal_holiday, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->rest_day_legal_holiday_overtime, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->rest_day_legal_holiday_overtime_night_diff, 2, '.', '') }}</td>
				<td>{{ number_format((float) $summary->rest_day_legal_holiday_night_diff, 2, '.', '') }}</td>						
			
			<?php //endforeach; ?>
		</tbody>
		</table>

	</div>	  
    

  </div>
</div>
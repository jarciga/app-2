<?php

class TimesheetSubmitted extends \Eloquent {
	protected $fillable = [];
	protected $table = 'timesheet_submitted';	
	
	
	public function getStatusByCutoff($empid, $from_date, $to_date) {

		return DB::table('timesheet_submitted')
			->where('employee_id', $empid)
			->where('cutoff_starting_date', $from_date)
			->where('cutoff_ending_date', $to_date)
			->get();					  					  		

    }
	
	
}

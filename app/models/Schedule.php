<?php

class Schedule extends \Eloquent {
	protected $fillable = [];
	protected $table = 'employee_schedule';


    public function getSchedule($employeeId, $dayDate) {

        return DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($dayDate))->get();

    }	

}
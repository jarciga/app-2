<?php

class Employeesetting extends \Eloquent {
	protected $fillable = [];
	protected $table = 'employee_setting';	
	
	public function getEmpSettingById($eid)
	{

		return DB::table('employee_setting')
				->where('employee_id', $eid)
				->first();					  					  		

    }
}
<?php

class Earning extends \Eloquent {
	protected $fillable = [];

	protected $table = 'tbl_earnings';
	
	public function getEmployeeEarningById($id) {

		return DB::table('tbl_earnings')
				->where('employee_id', $id)
				->where('cutfrom', '!=', '0000-00-00')
				->where('cutto', '!=', '0000-00-00')
				->get();					  					  		

    }
	
	
	public function getEmpEarningById($eid)
	{

		return DB::table('tbl_earnings')
				->where('ID', $eid)
				->first();					  					  		

    }
	
	
}


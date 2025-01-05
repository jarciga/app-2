<?php

class Deduction extends \Eloquent {
	protected $fillable = [];

	protected $table = 'tbl_deductions';
	
	public function getEmployeeDeductionById($id) {

		return DB::table('tbl_deductions')
				->where('employee_id', $id)
				->where('cutfrom', '!=', '0000-00-00')
				->where('cutto', '!=', '0000-00-00')
				->get();					  					  		

    }
	
	
	public function getEmpDeductionById($eid)
	{

		return DB::table('tbl_deductions')
				->where('ID', $eid)
				->first();					  					  		

    }
	
}


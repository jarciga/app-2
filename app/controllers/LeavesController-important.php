<?php

class LeavesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /leaves
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /leaves/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /leaves
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /leaves/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /leaves/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /leaves/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /leaves/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function init() {

		$currentUserId = Session::get('currentUserId');
		$groupName = Session::get('groupName');		
		$dayDateArr = Session::get('dayDateArr');
		$currentDate = Config::get('euler.current_date');
		$currentTime = Config::get('euler.current_time');
		$currentDateTime = Config::get('euler.current_date_time');
		$yesterDayDate = date( "Y-m-d", strtotime('yesterday') );

		$user = User::find($currentUserId);
		$userGroup = DB::table('users_groups')->where('user_id', $user->id)->first();
		$group = DB::table('groups')->where('id', $userGroup->group_id)->first();	

		$cutOffSetting = Cutoffsetting::where('id', 1)->first();

		$employee = Employee::where('id', $currentUserId)->first();

		$employeeType = $employee->employee_type;

		$employeeSetting = Employeesetting::where('employee_id', $currentUserId)->first();
		
		$timesheets = Timesheet::where('employee_id', $currentUserId)
							  ->whereIn('daydate', $dayDateArr)->get();

		$timesheet = Timesheet::where('employee_id', $currentUserId)
							  ->where('daydate', $currentDate)->first();							  

		$summaries = Summary::where('employee_id', $currentUserId)
							->whereIn('daydate', $dayDateArr)->get();

		//$summary = Summary::where('employee_id', $currentUserId)->first();					
		$summary = Summary::where('employee_id', $currentUserId)
						  ->where('daydate', $currentDate)->first();					

		$schedule = Schedule::where('employee_id', $currentUserId)
							->where('schedule_date', $currentDate)->first();
		
		//$company = company::where('company_date', $currentDate)->first();

		$companies = Company::all();
		$departments = Department::all();
		$jobTitles = DB::table('job_title')->get();

		$companies = (count($companies) !== 0) ? $companies : '';
		$departments = (count($departments) !== 0) ? $departments : '';
		$jobTitles = (count($jobTitles) !== 0) ? $jobTitles : '';

		//$managers = Employee::where('id', '<>', 1)->get();
		//$supervisors = Employee::where('id', '<>', 1)->get();
		$managers = Employee::all();
		$supervisors = Employee::all();
		$roles = DB::table('groups')->get();

		//Yesterday
		$timesheetYesterday = Timesheet::where('employee_id', $currentUserId) //<<<<<<<<<
							  ->where('daydate', $yesterDayDate)->first();							  							  		

		$summaryYesterday = Summary::where('employee_id', $currentUserId)
						  ->where('daydate', $yesterDayDate)->first();												  

		$scheduleYesterday = Schedule::where('employee_id', $currentUserId)
							->where('schedule_date', $yesterDayDate)->first();
		
		//$ = company::where('company_date', $yesterDayDate)->first();	

		$leaveYesterday = DB::select("SELECT * FROM `boph_leave` WHERE ? BETWEEN `from_date` AND `to_date` AND `employee_id` = ?", array($yesterDayDate, 1));		


		return $dataArr = array( 
					'currentUserId' => $currentUserId,
					'groupName' => $groupName,					
					'dayDateArr' => $dayDateArr,
					'cutOffSetting' => $cutOffSetting,
					'user' => $user,
					'userGroup' => $userGroup,
					'group' => $group,					
					'employee' => $employee,
					'employeeType' => $employeeType,
					'employeeSetting' => $employeeSetting,
					'timesheets' => $timesheets,
					'timesheet' => $timesheet,
					'timesheetYesterday' => $timesheetYesterday,
					'summaryYesterday' => $summaryYesterday,
					'scheduleYesterday' => $scheduleYesterday,
					//'companyYesterday' => $companyYesterday,
					'leaveYesterday' => $leaveYesterday,
					'summaries' => $summaries,
					'summary' => $summary,
					'schedule' => $schedule,
					//'company' => $company,
					'currentDate' => $currentDate,
					'currentTime' => $currentTime,
					'currentDateTime' => $currentDateTime,
					'yesterDayDate' => $yesterDayDate,
					'companies' => $companies,
					'departments' => $departments,
					'jobTitles' => $jobTitles,
					'managers' => $managers,
					'supervisors' => $supervisors,
					'roles' => $roles
					);							

	}	


	public function showLeaveLists() {

		$dataArr = $this->init();	
		$dataArr["resourceId"] = 'admin.leaves.show.leave.lists';

		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$groupName = $dataArr["groupName"];
		$employee = $dataArr["employee"];

		//GROUP
		if( !empty($groupName) ) :
		  //ADMINISTRATOR
		  if( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ) :              

		    $leaveEmployees = DB::table('employees')
		      ->where('id', '<>', $employee->id)
		      ->get();  
		    
		  //MANAGER
		  elseif( strcmp(strtolower($groupName), strtolower('Manager')) === 0 ) :                  

		    $leaveEmployees = DB::table('employees')
		      ->where('id', '<>', $employee->id)  
		      //->where('manager_id', $employee->id)
		      //->orWhere('supervisor_id', $employee->id)
		      ->where('employee_type', 2) //2: supervisor
		      ->where('manager_id', $employee->id)
		      ->get();

		  //SUPERVISOR
		  elseif( strcmp(strtolower($groupName), strtolower('Supervisor')) === 0 ) :                        

		    $leaveEmployees = DB::table('employees')
		      ->where('id', '<>', $employee->id)  
		      //->where('manager_id', $employee->id)
		      //->orWhere('supervisor_id', $employee->id)
		      ->where('employee_type',0) //2: supervisor
		      ->where('supervisor_id', $employee->id)
		      ->get(); 

		  //HUMAN RESOURCE
		  elseif( strcmp(strtolower($groupName), strtolower('HR')) === 0 ) :                        

		    $leaveEmployees = DB::table('employees')
		      ->where('id', '<>', $employee->id)
		      ->get();  		           

		  endif;
		endif;

		//if( count($leaveEmployees) !== 0 ) {
		if( !empty($leaveEmployees) ) {			
			foreach($leaveEmployees as $leaveEmployeeVal) {

				$leaveEmployeeArr[] = $leaveEmployeeVal->id;

			}	
		}

		if( !empty($leaveEmployeeArr) ) {
			////$dataArr["leaveCount"] = Leave::count();
			$dataArr["listLeaves"] = Leave::whereIn('employee_id', $leaveEmployeeArr)
										  ->orderBy('id')
										  //->take(5)
										  ->paginate(10);

			$dataArr["leaveCount"] = count($dataArr["listLeaves"]);										  

		} else {

			$dataArr["listLeaves"] = array();
			$dataArr["leaveCount"] = 0;

		}			


		//$dataArr["leaveCount"] = Leave::count();
		//$dataArr["listLeaves"] = Leave::orderBy('id')->paginate(15);
		

		return View::make('leaves.lists', $dataArr);

	}


	public function processLeaveLists($id = '') {

		$data = Input::get();

		//return dd($data);

		if ( -1 !== (int) $data["action"] ) {

			if ( !empty($data["check"]) ) {

				if ( is_array($data["check"]) ) {

		        	if ( sizeof($data["check"]) > 1 ) { //THE CHECKED CHECKBOX IS GREATER THAN 1

		        		$leaves = Leave::whereIn('id', $data["check"])->get();

		        		foreach($leaves as $leave) {						

		        			$employeeId = $leave->employee_id;

		        			$leaveSetting = DB::table('leave_setting')->where('employee_id', $employeeId)->get();

	    					$leaveCredits = $leaveSetting[0]->leave_credits;
	        				$leaveBalance = $leaveSetting[0]->leave_balance;	
	        				$leaveNumberOfDays = $leave->number_of_days;  

		        			$data["action"] = (int) $data["action"];

							// Start date
							$fromDate = $leave->from_date;
							// End date
							$toDate = $leave->to_date;	        			

							$leaveDateArr = array($fromDate, $toDate);


		        			if ( ($data["action"] === 1) && 
		        				 (-1 === (int) $leave->status) ||
		        				 ($data["action"] === 1) && 
		        				 (0 === (int) $leave->status) ) { //Aprroved

		        				if ( 0 !== (int) $leaveSetting[0]->leave_credits ) {		        					
		        					
		        					//if ( (int) $leaveBalance >= (int) $leaveCredits ) {

				        				//$leaveBalance = $leaveSetting[0]->leave_balance -= 1;
				        				$leaveBalance = ($leaveBalance - $leaveNumberOfDays);

				        				/*if($leaveBalance < 0) {

				        					$leaveBalance = 0;

				        				}*/

										DB::table('leave_setting')
											->where('employee_id', $employeeId)
											->update(array('leave_balance' => $leaveBalance));

				   	        			DB::table('leave')
				   	        				->where('id', $leave->id)
				   	        				->update(array('status' => 1));


										//Paid Sick Leave
										if ( 'sick leave' === strtolower($leave->nature_of_leave) ) {

											$update = array('paid_sick_leave' => number_format(8, 2));

					   	        			DB::table('employee_summary')
					   	        				->where('employee_id', $employeeId)
					   	        				->whereBetween('daydate', $leaveDateArr)
					   	        				->update($update);											
			
				   	        			//Paid Vacation Leave
				   	        			} elseif ( 'vacation leave' === strtolower($leave->nature_of_leave) ) {

				   	        				$update = array('paid_vacation_leave' => number_format(8, 2));

					   	        			DB::table('employee_summary')
					   	        				->where('employee_id', $employeeId)
					   	        				->whereBetween('daydate', $leaveDateArr)
					   	        				->update($update);				   	        				

				   	        			//Marternity Leave
										} elseif ( 'maternity leave' === strtolower($leave->nature_of_leave) ) {				   	        				

											$update = array('maternity_leave' => number_format(8, 2));

					   	        			DB::table('employee_summary')
					   	        				->where('employee_id', $employeeId)
					   	        				->whereBetween('daydate', $leaveDateArr)
					   	        				->update($update);											

										//Paternity Leave
										} elseif ( 'paternity leave' === strtolower($leave->nature_of_leave) ) {											

											$update = array('paternity_leave' => number_format(8, 2));

					   	        			DB::table('employee_summary')
					   	        				->where('employee_id', $employeeId)
					   	        				->whereBetween('daydate', $leaveDateArr)
					   	        				->update($update);

										//Leave Without Pay
				   	        			} elseif ( 'leave without Pay' === strtolower($leave->nature_of_leave) ) {											

				   	        				$update = array('leave_without_pay' => number_format(8, 2));

					   	        			DB::table('employee_summary')
					   	        				->where('employee_id', $employeeId)
					   	        				->whereBetween('daydate', $leaveDateArr)
					   	        				->update($update);				   	        				

				   	        			}

				   	        		//} else {

				   	        			//check your leave credits

				   	        		//}

			   	        		}

	   	        			} 
	  	        			
	  	        			if( ($data["action"] === 0) && 
		        				(-1 === (int) $leave->status) ) { //Denied	 


		        				if ( $leaveSetting[0]->leave_balance <= (int) $leaveSetting[0]->leave_credits ) {        				

			        				/*$leaveBalance = $leaveSetting[0]->leave_balance;

									DB::table('leave_setting')
										->where('employee_id', $employeeId)
										->update(array('leave_balance' => $leaveBalance));*/

			   	        			DB::table('leave')
			   	        				->where('id', $leave->id)
			   	        				->update(array('status' => 0));

			   	        		}


		        			} elseif( ($data["action"] === 0) && 
	   	        					  (1 === (int) $leave->status) ) { //Denied

		        				if ( $leaveSetting[0]->leave_balance <= (int) $leaveSetting[0]->leave_credits ) {        				

			        				//$leaveBalance = $leaveSetting[0]->leave_balance += 1;
			        			    $leaveBalance = ($leaveBalance + $leaveNumberOfDays);

			        			    /*if ($leaveBalance > $leaveCredits) {

										$leaveBalance = $leaveCredits;
			        			    }*/

									DB::table('leave_setting')
										->where('employee_id', $employeeId)
										->update(array('leave_balance' => $leaveBalance));

			   	        			DB::table('leave')
			   	        				->where('id', $leave->id)
			   	        				->update(array('status' => 0));

									//Paid Sick Leave
									if ( 'sick leave' === strtolower($leave->nature_of_leave) ) {

										$update = array('paid_sick_leave' => '');

				   	        			DB::table('employee_summary')
				   	        				->where('employee_id', $employeeId)
				   	        				->whereBetween('daydate', $leaveDateArr)
				   	        				->update($update);											

			   	        			//Paid Vacation Leave
			   	        			} elseif ( 'vacation leave' === strtolower($leave->nature_of_leave) ) {

			   	        				$update = array('paid_vacation_leave' => '0');

				   	        			DB::table('employee_summary')
				   	        				->where('employee_id', $employeeId)
				   	        				->whereBetween('daydate', $leaveDateArr)
				   	        				->update($update);				   	        				

			   	        			//Marternity Leave
									} elseif ( 'maternity leave' === strtolower($leave->nature_of_leave) ) {				   	        				

										$update = array('maternity_leave' => '');

				   	        			DB::table('employee_summary')
				   	        				->where('employee_id', $employeeId)
				   	        				->whereBetween('daydate', $leaveDateArr)
				   	        				->update($update);											

									//Paternity Leave
									} elseif ( 'paternity leave' === strtolower($leave->nature_of_leave) ) {											

										$update = array('paternity_leave' => '');

				   	        			DB::table('employee_summary')
				   	        				->where('employee_id', $employeeId)
				   	        				->whereBetween('daydate', $leaveDateArr)
				   	        				->update($update);

									//Leave Without Pay
			   	        			} elseif ( 'leave without Pay' === strtolower($leave->nature_of_leave) ) {											

			   	        				$update = array('leave_without_pay' => '');

				   	        			DB::table('employee_summary')
				   	        				->where('employee_id', $employeeId)
				   	        				->whereBetween('daydate', $leaveDateArr)
				   	        				->update($update);				   	        				

			   	        			}								   	        				

			   	        		}

	   	        			} 

		        		}		        		
		        		

		        	} elseif ( sizeof($data["check"]) === 1 ) { //ONLY 1 CHECKBOX IS CHECK
					
						$leave = Leave::whereIn('id', $data["check"])->first();		        		

						$employeeId = $leave->employee_id;

	        			$leaveSetting = DB::table('leave_setting')->where('employee_id', $employeeId)->first();

    					$leaveCredits = $leaveSetting->leave_credits;
        				$leaveBalance = $leaveSetting->leave_balance;	
        				$leaveNumberOfDays = $leave->number_of_days; 

	        			$data["action"] = (int) $data["action"];

						// Start date
						$fromDate = $leave->from_date;
						// End date
						$toDate = $leave->to_date;	        			

						$leaveDateArr = array($fromDate, $toDate);	        			

	        			if ( ($data["action"] === 1) && 
	        				 (-1 === (int) $leave->status) ||
	        				 ($data["action"] === 1) && 
	        				 (0 === (int) $leave->status) ) { //Aprroved

	        				if ( 0 !== (int) $leaveSetting->leave_credits ) {		        						
	        					
	        					//if ( (int) $leaveBalance >= (int) $leaveCredits ) {

			        				//$leaveBalance = $leaveSetting->leave_balance -= 1;
			        				$leaveBalance = ($leaveBalance - $leaveNumberOfDays);

			        				/*if($leaveBalance < 0) {

			        					$leaveBalance = 0;

			        				}*/

									DB::table('leave_setting')
										->where('employee_id', $employeeId)
										->update(array('leave_balance' => $leaveBalance));

			   	        			DB::table('leave')
			   	        				->where('id', $leave->id)
			   	        				->update(array('status' => 1));


									//Paid Sick Leave
									if ( 'sick leave' === strtolower($leave->nature_of_leave) ) {

										$update = array('paid_sick_leave' => number_format(8, 2));

				   	        			DB::table('employee_summary')
				   	        				->where('employee_id', $employeeId)
				   	        				->whereBetween('daydate', $leaveDateArr)
				   	        				->update($update);											
		
			   	        			//Paid Vacation Leave
			   	        			} elseif ( 'vacation leave' === strtolower($leave->nature_of_leave) ) {

			   	        				$update = array('paid_vacation_leave' => number_format(8, 2));

				   	        			DB::table('employee_summary')
				   	        				->where('employee_id', $employeeId)
				   	        				->whereBetween('daydate', $leaveDateArr)
				   	        				->update($update);				   	        				

			   	        			//Marternity Leave
									} elseif ( 'maternity leave' === strtolower($leave->nature_of_leave) ) {				   	        				

										$update = array('maternity_leave' => number_format(8, 2));

				   	        			DB::table('employee_summary')
				   	        				->where('employee_id', $employeeId)
				   	        				->whereBetween('daydate', $leaveDateArr)
				   	        				->update($update);											

									//Paternity Leave
									} elseif ( 'paternity leave' === strtolower($leave->nature_of_leave) ) {											

										$update = array('paternity_leave' => number_format(8, 2));

				   	        			DB::table('employee_summary')
				   	        				->where('employee_id', $employeeId)
				   	        				->whereBetween('daydate', $leaveDateArr)
				   	        				->update($update);

									//Leave Without Pay
			   	        			} elseif ( 'leave without Pay' === strtolower($leave->nature_of_leave) ) {											

			   	        				$update = array('leave_without_pay' => number_format(8, 2));

				   	        			DB::table('employee_summary')
				   	        				->where('employee_id', $employeeId)
				   	        				->whereBetween('daydate', $leaveDateArr)
				   	        				->update($update);				   	        				

			   	        			}

			   	        		//} else {

			   	        			//check your leave credits

			   	        		//}

		   	        		}

   	        			} 
  	        			
  	        			if( ($data["action"] === 0) && 
	        				(-1 === (int) $leave->status) ) { //Denied	 


	        				if ( $leaveSetting->leave_balance <= (int) $leaveSetting->leave_credits ) {        				

		        				/*$leaveBalance = $leaveSetting->leave_balance;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));*/

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 0));

		   	        		}


	        			} elseif( ($data["action"] === 0) && 
   	        					  (1 === (int) $leave->status) ) { //Denied

	        				if ( $leaveSetting->leave_balance <= (int) $leaveSetting->leave_credits ) {        				

		        				//$leaveBalance = $leaveSetting->leave_balance += 1;
		        			    $leaveBalance = ($leaveBalance + $leaveNumberOfDays);

		        			    /*if ($leaveBalance > $leaveCredits) {

									$leaveBalance = $leaveCredits;
		        			    }*/

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 0));

								//Paid Sick Leave
								if ( 'sick leave' === strtolower($leave->nature_of_leave) ) {

									$update = array('paid_sick_leave' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);											

		   	        			//Paid Vacation Leave
		   	        			} elseif ( 'vacation leave' === strtolower($leave->nature_of_leave) ) {

		   	        				$update = array('paid_vacation_leave' => '0');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);				   	        				

		   	        			//Marternity Leave
								} elseif ( 'maternity leave' === strtolower($leave->nature_of_leave) ) {				   	        				

									$update = array('maternity_leave' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);											

								//Paternity Leave
								} elseif ( 'paternity leave' === strtolower($leave->nature_of_leave) ) {											

									$update = array('paternity_leave' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);

								//Leave Without Pay
		   	        			} elseif ( 'leave without Pay' === strtolower($leave->nature_of_leave) ) {											

		   	        				$update = array('leave_without_pay' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);				   	        				

		   	        			}								   	        				

		   	        		}

   	        			} 

		        								

		        	}
				
				}

				//return Redirect::to('/absent-lists/'.$id);
				return Redirect::route('process.leave.lists', array($id));

			} elseif ( empty($data["check"]) ) {

				return Redirect::route('process.leave.lists', array($id));

			}

		} else {

			return Redirect::route('process.leave.lists', array($id));

		}

	}




}
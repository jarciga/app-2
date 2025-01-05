<?php

class AbsencesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /absences
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /absences/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /absences
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /absences/{id}
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
	 * GET /absences/{id}/edit
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
	 * PUT /absences/{id}
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
	 * DELETE /absences/{id}
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

		$employeeSetting = Employeesetting::where('employee_id', $currentUserId)->first();

		//$employeeType = $employee->employee_type;
		
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
		
		$holiday = Holiday::where('holiday_date', $currentDate)->first();


		//Yesterday
		$timesheetYesterday = Timesheet::where('employee_id', $currentUserId) //<<<<<<<<<
							  ->where('daydate', $yesterDayDate)->first();							  							  		

		$summaryYesterday = Summary::where('employee_id', $currentUserId)
						  ->where('daydate', $yesterDayDate)->first();												  

		$scheduleYesterday = Schedule::where('employee_id', $currentUserId)
							->where('schedule_date', $yesterDayDate)->first();
		
		$holidayYesterday = Holiday::where('holiday_date', $yesterDayDate)->first();	

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
					//'employeeType' => $employeeType,
					'employeeSetting' => $employeeSetting,
					'timesheets' => $timesheets,
					'timesheet' => $timesheet,
					'timesheetYesterday' => $timesheetYesterday,
					'summaryYesterday' => $summaryYesterday,
					'scheduleYesterday' => $scheduleYesterday,
					'holidayYesterday' => $holidayYesterday,
					'leaveYesterday' => $leaveYesterday,
					'summaries' => $summaries,
					'summary' => $summary,
					'schedule' => $schedule,
					'holiday' => $holiday,
					'currentDate' => $currentDate,
					'currentTime' => $currentTime,
					'currentDateTime' => $currentDateTime,
					'yesterDayDate' => $yesterDayDate
					);							

	}	

	public function showAbsentLists($id = '') {

		$employeesController = new EmployeesController;
		$dataArr["employee"] = $employeesController->employeeById($id);
		$dataArr["listCurrentAbsencesPerCutoff"] = $this->listCurrentAbsencesPerCutoff($id);
		
		return View::make('absences.lists', $dataArr);

	}

	public function processAbsentLists($id = '') {

		$data = Input::get();

		//return dd($data);

		if ( -1 !== (int) $data["action"] ) {

			if ( !empty($data["check"]) ) {

				if ( is_array($data["check"]) ) {

		        	if ( sizeof($data["check"]) > 1 ) { //THE CHECKED CHECKBOX IS GREATER THAN 1

		        		foreach($data["check"] as $checkVal) {

		        			list($employeeIdArr[], $scheduleDateArr[]) = explode('|', $checkVal);

		        		}

		        		if ( 1 === (int) $data["action"] ) { //Aprroved:Yes
		
							//return "Aprroved:Yes";

							//return dd($employeeIdVal.'-'.$scheduleDateVal);

							foreach( $employeeIdArr as $employeeIdVal ) {

								foreach( $scheduleDateArr as $scheduleDateVal ) {									

			        				$summary = Summary::where('employee_id', $employeeIdVal)
															->where('daydate', $scheduleDateVal)
									       					->first();

									$summary->absent = 8.00;
									$summary->save();

			        			}

			        		}											

			        	} elseif ( 0 === (int) $data["action"] ) { //Denied:No

			        		//return "Denied:No";

							//return dd($employeeIdVal.'-'.$scheduleDateVal);

							foreach( $employeeIdArr as $employeeIdVal ) {

								foreach( $scheduleDateArr as $scheduleDateVal ) {									


			        				$summary = Summary::where('employee_id', $employeeIdVal)
															->where('daydate', $scheduleDateVal)
									       					->first();

									$summary->absent = '';
									$summary->save();					        		

			        			}

			        		}											

			        	}			        		


		        	} elseif ( sizeof($data["check"]) === 1 ) { //THE CHECKED CHECKBOX IS GREATER THAN 1

		        		list($employeeId, $scheduleDate) = explode('|', $data["check"][0]);

        				$summary = Summary::where('employee_id', $employeeId)
												->where('daydate', $scheduleDate)
						       					->first();

		        		if ( 1 === (int) $data["action"] ) { //Aprroved:Yes
		
							//return "Aprroved:Yes";
							$summary->absent = 8.00;
							$summary->save();


			        	} elseif ( 0 === (int) $data["action"] ) { //Denied:No

			        		//return "Denied:No";
							$summary->absent = '';
							$summary->save();					        		

			        	}
		        		
		        	}
				
				}

				//return Redirect::to('/absent-lists/'.$id);
				return Redirect::route('process.absent.lists', array($id));

			} elseif ( empty($data["check"]) ) {

				return Redirect::route('process.absent.lists', array($id));

			}

		} else {

			return Redirect::route('process.absent.lists', array($id));

		}

	}


	public function currentAbsencesPerCutoff() {

		$dataArr = $this->init();

		$dayDateArr = $dataArr["dayDateArr"];
		$currentUserId = $dataArr["currentUserId"];
		$yesterDayDate = $dataArr["yesterDayDate"];
		
		$employeesController = new EmployeesController;		
		$employees = $employeesController->employeeByGroup();

		if( !empty($employees) ) {

		    foreach($employees as $employeeVal) {

				foreach($dayDateArr as $dayDateVal) {

					//GET ONLY THE DATE FROM THE BEGINNING OF CUTOFF UNTIL YESTERDAY
					if(strtotime($dayDateVal) <= strtotime($yesterDayDate)) {

						$scheduleArr[] = Schedule::where('employee_id', $employeeVal->id)
											  ->where('schedule_date', $dayDateVal)->first();

					}

				}
		 
		  	}
			$ctr = 0;
			$totalAbsent = 0;
	 		if( !empty($scheduleArr) ) {
		 		foreach($scheduleArr as $scheduleVal) {

		 			if( !empty($scheduleVal) ) {

						$employeeId = $scheduleVal["employee_id"];
						$scheduleDate = $scheduleVal["schedule_date"];
						$restDay = $scheduleVal["rest_day"];
			  		
				  		$employee = Employee::where('id', $employeeId)->first();

						$scheduleArr[] = array(
											"employee_id" => $employee->employee_id,
											"lastname" => $employee->lastname,
											"middlename" => $employee->middle_name,
											"firstname" =>$employee->firstname,
											"schedule_date" => $scheduleDate,
											"rest_day" => $restDay
										);

						/*$summaryArr[] = Summary::where('employee_id', $employeeId)
									      ->where('daydate', $scheduleDate)
									      ->first();*/
						

						$leave[] = DB::select("SELECT * FROM `boph_leave` WHERE ? BETWEEN `from_date` AND `to_date` AND `employee_id` = ? AND `status` = ?", array($scheduleDate, $employeeId, 1));	

					}

				}
			}

			//echo "<pre>";
			//dd($scheduleArr);
			//echo "</pre>";

			if( !empty($leave) ) {

				for($j = 0; $j < sizeof($leave); $j++) {

					$employeeId = $scheduleArr[$j]["employee_id"];
					$scheduleDate = $scheduleArr[$j]["schedule_date"];
					$restDay = $scheduleArr[$j]["rest_day"];

					//$employee = Employee::where('id', $employeeId)->first();
					$timesheet = Timesheet::where('employee_id', $employeeId)
										  ->where('daydate', $scheduleDate)->first();

				    //CHECK IF HAS LEAVE
				    //if ( empty($leave[$j]) && empty($summaryArr[$j]->absent) ) {
				    if ( empty($leave[$j]) ) {

				        $hasNoLeave[$j] = TRUE;

				    } else {

				        $hasNoLeave[$j] = FALSE;

				    }

					//ABSENT
				    if ( $restDay === 0 && $hasNoLeave[$j] && $timesheet->clocking_status === 'open' ) {

				    	$absencesPerCutOff[] = array(
													"employeeId" => $employeeId,
													"lastname" => $employee->lastname,
													"middlename" => $employee->middle_name,
													"firstname" => $employee->firstname,
													"scheduledate" => $scheduleDate,
													"rest_day" => $restDay,
													
												);
	 
				    }

				}

			} 

			if ( !empty($absencesPerCutOff) ) {
				
				return $absencesPerCutOff;
				
			}

		}

	}	


	public function listCurrentAbsencesPerCutoff($id = '') {	

		$employeeId = $id;

		$dataArr = $this->init();

		$dayDateArr = $dataArr["dayDateArr"];
		$currentUserId = $dataArr["currentUserId"];
		$yesterDayDate = $dataArr["yesterDayDate"];
		
		$employeesController = new EmployeesController;		
		$employee = $employeesController->employeeById($id);
		
		if( !empty($employee) ) {

		    //foreach($employees as $employeeVal) {

				foreach($dayDateArr as $dayDateVal) {

					//GET ONLY THE DATE FROM THE BEGINNING OF CUTOFF UNTIL YESTERDAY
					if(strtotime($dayDateVal) <= strtotime($yesterDayDate)) {

						$scheduleArr[] = Schedule::where('employee_id', $employee->id)
											  ->where('schedule_date', $dayDateVal)->first();

					}

				}
		 
		  	//}

			$ctr = 0;
			$totalAbsent = 0;

	 		foreach($scheduleArr as $scheduleVal) {

	 			if( !empty($scheduleVal) ) {

					$employeeId = $scheduleVal["employee_id"];
					$scheduleDate = $scheduleVal["schedule_date"];
					$restDay = $scheduleVal["rest_day"];

			  		$employee = Employee::where('id', $employeeId)->first();

					$scheduleArr[] = array(
										"employee_id" => $employee->employee_id,
										"lastname" => $employee->lastname,
										"middlename" => $employee->middle_name,
										"firstname" =>$employee->firstname,
										"schedule_date" => $scheduleDate,
										"rest_day" => $restDay
									);

					$summaryArr[] = Summary::where('employee_id', $employeeId)
								      ->where('daydate', $scheduleDate)
								      ->first();					
					

					$leave[] = DB::select("SELECT * FROM `boph_leave` WHERE ? BETWEEN `from_date` AND `to_date` AND `employee_id` = ? AND `status` = ?", array($scheduleDate, $employeeId, 1));	

				}

			}

			for($j = 0; $j < sizeof($leave); $j++) {

				$employeeId = $scheduleArr[$j]["employee_id"];
				$scheduleDate = $scheduleArr[$j]["schedule_date"];
				$restDay = $scheduleArr[$j]["rest_day"];

				//$employee = Employee::where('id', $employeeId)->first();
				$timesheet = Timesheet::where('employee_id', $employeeId)
									  ->where('daydate', $scheduleDate)->first();				

				if ( !empty($summaryArr[$j]->absent) ) {

					$absent[$j] = '<h5><span class="label label-success">Absent with leave</span></h5>';


				} else {

					$absent[$j] = '<h5><span class="label label-danger">Absent without leave</span></h5>';

				}

			    //CHECK IF HAS LEAVE				
			    if ( empty($leave[$j]) ) {

			        $hasNoLeave[$j] = TRUE;

			    } else {

			        $hasNoLeave[$j] = FALSE;

			    }

				//ABSENT
			    if ( $restDay === 0 && $hasNoLeave[$j] && $timesheet->clocking_status === 'open' ) {

			    	$absencesPerCutOff[] = array(
												"employeeId" => $employeeId,
												"lastname" => $employee->lastname,
												"middlename" => $employee->middle_name,
												"firstname" =>$employee->firstname,
												"schedule_date" => $scheduleDate,
												"rest_day" => $restDay,
												"absent" => $absent[$j]
											);

			    }

			}

			return $absencesPerCutOff;

		}

	}	
		

}
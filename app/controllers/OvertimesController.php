<?php

class OvertimesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /overtimes
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /overtimes/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /overtimes
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /overtimes/{id}
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
	 * GET /overtimes/{id}/edit
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
	 * PUT /overtimes/{id}
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
	 * DELETE /overtimes/{id}
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

		$employeeType = $employee->employee_type;
		
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

	public function showOvertimeLists() {
		
		$dataArr = $this->init();

		$dayDateArr = $dataArr["dayDateArr"];
		$currentUserId = $dataArr["currentUserId"];
		$yesterDayDate = $dataArr["yesterDayDate"];						

		$employeesController = new EmployeesController;		
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();
		$employees = $employeesController->employeeByGroup();
		//$dataArr["listCurrentOvertimePerCutoff"] = $this->listCurrentOvertimePerCutoff($id);

		//return dd($employees);

		$cutOffDate["from"] = $dayDateArr[0]; 
		$cutOffDate["to"] = $dayDateArr[count($dayDateArr)-1];

		if( !empty($employees) ) {

		    foreach($employees as $employeeVal) {

		    	$employeeIdArr[] = $employeeVal->id;
				//Session::put('employeeIdArr', $employeeIdArr);

		    }

		    //return dd($employeeIdArr);
			$dataArr["overtimes"] = DB::table('employees')
							->join('employee_timesheet', 'employees.id', '=', 'employee_timesheet.employee_id')
							->join('overtime', 'employee_timesheet.id', '=', 'overtime.timesheet_id')
							->whereBetween('daydate', array($cutOffDate["from"], $cutOffDate["to"]))
							->whereIn('employee_timesheet.employee_id', $employeeIdArr)							
							->where('overtime_status', '=', -1)
							->orWhere('overtime_status', '=', 1)
							->orWhere('overtime_status', '=', 0)														
							->get();		

			//return var_dump($dataArr["overtimes"]);
			
		} else {

			$dataArr["overtimes"] = array();

		}

		return View::make('overtimes.lists', $dataArr);		
		
	}


	public function processOvertimeLists($id = '') {

		$data = Input::get();

		//return dd($data);

		if ( -1 !== (int) $data["action"] ) {

			if ( !empty($data["check"]) ) {

				if ( is_array($data["check"]) ) {

		        	if ( sizeof($data["check"]) > 1 ) { //THE CHECKED CHECKBOX IS GREATER THAN 1

		        		$overtimeIdArr = Overtime::whereIn('id', $data["check"])->get();

		        		$totalNightDiff = array();
						$nightDiff = array();
						$hasNightDiff = array();

						$overtimeStatus = array();
						$totalNightDiff = array();

		        		foreach($overtimeIdArr as $overtime) {

			                $employeeId = $overtime->employee_id;

			                if ( $overtime->seq_no === 1 || 
			                	 $overtime->seq_no === 2 ) {

			                		$shift = 1;

			                } elseif ( $overtime->seq_no === 3 ) {

			                		$shift = 2;
			                }

							$employeeSetting = Employeesetting::where('employee_id', $employeeId)->first();	

							//$timesheet = new Timesheet;
							$timesheet = Timesheet::where('id', $overtime->timesheet_id)->first();

							//$summary = new Summary;
							$summary = Summary::where('employee_id', $employeeId)->where('daydate', trim($timesheet->daydate))->first();

							//$schedule = new Schedule;
							$schedule = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($timesheet->daydate))->first();

							//$holiday = new Holiday;
							$holiday = DB::table('holiday')->where('holiday_date', trim($timesheet->daydate))->first();

							//CHECK SCHEDULE IF HAS NIGHTDIFF;
							/*$scheduleHasNightDiff = nightDiff($timesheet->schedule_in, $timesheet->schedule_out, $timesheet->schedule_in);

							if ( $scheduleHasNightDiff > 0 ) { //Value of NightDiff is positive
								
								$scheduleHasNightDiff = TRUE;

							} else {

								$scheduleHasNightDiff = FALSE;

							}*/							

							if ( $overtime->seq_no === 1 ) {

								/*DB::table('employee_timesheet')
									->where('id', $overtime->timesheet_id)				
									->update(array('overtime_status_1' => $data["action"]));*/

								$timesheet->overtime_status_1 = $data["action"];
								$timesheet->save();

								DB::table('overtime')
									->where('id', $overtime->id)
									->where('seq_no', 1)
									->update(array('overtime_status' => $data["action"]));

								/*if(!$scheduleHasNightDiff[1]) {

									//$nightDiffArr[1] = nightDiff($timesheet->time_in_1, $timesheet->time_out_1, $schedule->start_time);
									$nightDiffArr[1] = nightDiff($schedule->end_time, $timesheet->time_out_1, $schedule->start_time);
									
									if ( $nightDiffArr[1] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiffArr[1] = TRUE;

									} else {

										$hasNightDiffArr[1] = FALSE;

									}	
								}								

								if(!$scheduleHasNightDiff[2]) {

									//$nightDiffArr[2] = nightDiff($timesheet->time_in_2, $timesheet->time_out_2, $schedule->start_time);
									$nightDiffArr[2] = nightDiff($schedule->end_time, $timesheet->time_out_2, $schedule->start_time);
									
									if ( $nightDiffArr[2] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiffArr[2] = TRUE;

									} else {

										$hasNightDiffArr[2] = FALSE;

									}

								}									

								if(!$scheduleHasNightDiff[3]) {

									//$nightDiffArr[3] = nightDiff($timesheet->time_in_3, $timesheet->time_out_3, $schedule->start_time);
									$nightDiffArr[3] = nightDiff($schedule->end_time, $timesheet->time_out_3, $schedule->start_time);
									
									if ( $nightDiffArr[3] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiffArr[3] = TRUE;

									} else {

										$hasNightDiffArr[3] = FALSE;

									}

								}*/	

								//return $timesheet->overtime_status_1;

							} elseif ( $overtime->seq_no === 2 ) {

								/*DB::table('employee_timesheet')
									->where('id', $overtime->timesheet_id)								
									->update(array('overtime_status_2' => $data["action"]));*/

								$timesheet->overtime_status_2 = $data["action"];
								$timesheet->save();									

								DB::table('overtime')
									->where('id', $overtime->id)
									->where('seq_no', 2)
									->update(array('overtime_status' => $data["action"]));

								/*if(!$scheduleHasNightDiff[1]) {

									//$nightDiffArr[1] = nightDiff($timesheet->time_in_1, $timesheet->time_out_1, $schedule->start_time);
									$nightDiffArr[1] = nightDiff($schedule->end_time, $timesheet->time_out_1, $schedule->start_time);
									
									if ( $nightDiffArr[1] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiffArr[1] = TRUE;

									} else {

										$hasNightDiffArr[1] = FALSE;

									}	
								}								

								if(!$scheduleHasNightDiff[2]) {

									//$nightDiffArr[2] = nightDiff($timesheet->time_in_2, $timesheet->time_out_2, $schedule->start_time);
									$nightDiffArr[2] = nightDiff($schedule->end_time, $timesheet->time_out_2, $schedule->start_time);
									
									if ( $nightDiffArr[2] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiffArr[2] = TRUE;

									} else {

										$hasNightDiffArr[2] = FALSE;

									}

								}

								if(!$scheduleHasNightDiff[3]) {

									//$nightDiffArr[3] = nightDiff($timesheet->time_in_3, $timesheet->time_out_3, $schedule->start_time);
									$nightDiffArr[3] = nightDiff($schedule->end_time, $timesheet->time_out_3, $schedule->start_time);
									
									if ( $nightDiffArr[3] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiffArr[3] = TRUE;

									} else {

										$hasNightDiffArr[3] = FALSE;

									}

								}*/									

							} elseif ( $overtime->seq_no === 3 ) {

								/*DB::table('employee_timesheet')
									->where('id', $overtime->timesheet_id)								
									->update(array('overtime_status_3' => $data["action"]));*/

								$timesheet->overtime_status_3 = $data["action"];
								$timesheet->save();									

								DB::table('overtime')
									->where('id', $overtime->id)
									->where('seq_no', 3)
									->update(array('overtime_status' => $data["action"]));

								/*if(!$scheduleHasNightDiff[1]) {

									//$nightDiffArr[1] = nightDiff($timesheet->time_in_1, $timesheet->time_out_1, $schedule->start_time);
									$nightDiffArr[1] = nightDiff($schedule->end_time, $timesheet->time_out_1, $schedule->start_time);
									
									if ( $nightDiffArr[1] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiffArr[1] = TRUE;

									} else {

										$hasNightDiffArr[1] = FALSE;

									}	
								}								

								if(!$scheduleHasNightDiff[2]) {

									//$nightDiffArr[2] = nightDiff($timesheet->time_in_2, $timesheet->time_out_2, $schedule->start_time);
									$nightDiffArr[2] = nightDiff($schedule->end_time, $timesheet->time_out_2, $schedule->start_time);
									
									if ( $nightDiffArr[2] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiffArr[2] = TRUE;

									} else {

										$hasNightDiffArr[2] = FALSE;

									}

								}									

								if(!$scheduleHasNightDiff[3]) {

									//$nightDiffArr[3] = nightDiff($timesheet->time_in_3, $timesheet->time_out_3, $schedule->start_time);
									$nightDiffArr[3] = nightDiff($schedule->end_time, $timesheet->time_out_3, $schedule->start_time);
									
									if ( $nightDiffArr[3] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiffArr[3] = TRUE;

									} else {

										$hasNightDiffArr[3] = FALSE;

									}

								}*/													

							}

							//TEMPORARY SETTING
							if ( $timesheet->overtime_status_1 === -1 || 
								 $timesheet->overtime_status_1 === NULL ||
								 empty($timesheet->overtime_status_1) ) {

									$overtimeStatusArr[1] = 0;											

							} else {

								$overtimeStatusArr[1] = (int) $timesheet->overtime_status_1;											

							}

							if ( $timesheet->overtime_status_2 === -1 || 
								 $timesheet->overtime_status_2 === NULL ||
								 empty($timesheet->overtime_status_2) ) {

									$overtimeStatusArr[2] = 0;											

							} else {

									$overtimeStatusArr[2] = (int) $timesheet->overtime_status_2;											

							}

							if ( $timesheet->overtime_status_3 === -1 || 
								 $timesheet->overtime_status_3 === NULL ||
								 empty($timesheet->overtime_status_3) ) {

									$overtimeStatusArr[3] = 0;											

							} else {

								$overtimeStatusArr[3] = (int) $timesheet->overtime_status_3;											

							}


							//CHECK IF REST DAY: FALSE							
							if ( $schedule->rest_day !== 1 ) {

								if ( !empty($holiday) ) { //HOLIDAY: TRUE

									echo "HOLIDAY: TRUE \n";

									if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

										if ( 1 === (int) $data["action"] ) { //APPROVED

											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

											}


											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_3:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													//Overtime		
													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}	

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

												}
												
												//clock_out_3:1,0,1:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																							
												}													
												
												//clock_out_3:1,1,1:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																			
												}

											}											

											$summary->legal_holiday_overtime = $totalOvertime;
											$summary->legal_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();

										} elseif ( 0 === (int) $data["action"] ) { //DENIED

											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:0,0,0:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[1] = '';
													$totalNightDiff[1] = '';

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}

											}

											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:0,0,0:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[1] = 0;
													$totalNightDiff[1] = 0;

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}
												
												//clock_out_2:1,0,0:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}


													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}												

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_2:0,0,0:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[1] = 0;
													$totalNightDiff[1] = 0;

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}											

												//clock_out_3:1,0,0:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}												

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																								

												}
												
												//clock_out_3:1,0,1:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																								

												}													
												
												//clock_out_3:1,1,1:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																				

												}

											}

											$summary->legal_holiday_overtime = $totalOvertime;
											$summary->legal_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();


										}										

									} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day

										if ( 1 === (int) $data["action"] ) { //APPROVED

											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

											}


											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_3:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													//Overtime		
													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}	

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

												}
												
												//clock_out_3:1,0,1:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																							
												}													
												
												//clock_out_3:1,1,1:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																			
												}

											}											

											$summary->special_holiday_overtime = $totalOvertime;
											$summary->special_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();

										} elseif ( 0 === (int) $data["action"] ) { //DENIED

											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:0,0,0:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[1] = '';
													$totalNightDiff[1] = '';

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}

											}

											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:0,0,0:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[1] = 0;
													$totalNightDiff[1] = 0;

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}
												
												//clock_out_2:1,0,0:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}


													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}												

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_2:0,0,0:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[1] = 0;
													$totalNightDiff[1] = 0;

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}											

												//clock_out_3:1,0,0:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
															
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}												

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Denied
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																								

												}
												
												//clock_out_3:1,0,1:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																								

												}													
												
												//clock_out_3:1,1,1:Denied
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																				

												}

											}

											$summary->special_holiday_overtime = $totalOvertime;
											$summary->special_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();


										}


									}

								} else { //HOLIDAY: FALSE - Regular Day

									echo "HOLIDAY: FALSE - Regular Day \n";

									if ( 1 === (int) $data["action"] ) { //APPROVED

										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:1,0,0:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

										}


										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:1,0,0:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Approved
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
												
												//Overtime		
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												//Overtime
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 1
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}																																			

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_3:1,0,0:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Approved
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
												
												//Overtime		
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												//Overtime
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 1
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}																																			

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Approved
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												//Overtime		
												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}	

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Approved
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

											}
											
											//clock_out_3:1,0,1:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																						
											}													
											
											//clock_out_3:1,1,1:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																		
											}

										}											

										$summary->regular_overtime = $totalOvertime;
										$summary->regular_overtime_night_diff = $totalNightDiff;								
										$summary->save();

									} elseif ( 0 === (int) $data["action"] ) { //DENIED

										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = '';
												$totalNightDiff[1] = '';

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}

										}

										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}
											
											//clock_out_2:1,0,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}


												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}											

											//clock_out_3:1,0,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

											

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																							

											}
											
											//clock_out_3:1,0,1:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																							

											}													
											
											//clock_out_3:1,1,1:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																			

											}

										}

										$summary->regular_overtime = $totalOvertime;
										$summary->regular_overtime_night_diff = $totalNightDiff;								
										$summary->save();


									}

								}
	
							//CHECK IF REST DAY: TRUE							
							} elseif ( $schedule->rest_day === 1 ) {
																							 
								if ( !empty($holiday) ) { //HOLIDAY: TRUE

									echo "HOLIDAY: TRUE \n";

									if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

										if ( 1 === (int) $data["action"] ) { //APPROVED


											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

											}


											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_3:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													//Overtime		
													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}	

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

												}
												
												//clock_out_3:1,0,1:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																							
												}													
												
												//clock_out_3:1,1,1:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																			
												}

											}											

											$summary->rest_day_legal_holiday_overtime = $totalOvertime;
											$summary->rest_day_legal_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();

										} elseif ( 0 === (int) $data["action"] ) { //DENIED

										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = '';
												$totalNightDiff[1] = '';

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}

										}

										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}
											
											//clock_out_2:1,0,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}


												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}											

											//clock_out_3:1,0,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

											

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																							

											}
											
											//clock_out_3:1,0,1:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																							

											}													
											
											//clock_out_3:1,1,1:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																			

											}

										}

											$summary->rest_day_legal_holiday_overtime = $totalOvertime;
											$summary->rest_day_legal_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();


										}										

									} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day

										if ( 1 === (int) $data["action"] ) { //APPROVED


											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

											}


											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_3:1,0,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													//Overtime		
													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}	

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Approved
												if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

												}
												
												//clock_out_3:1,0,1:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																							
												}													
												
												//clock_out_3:1,1,1:Approved
												if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																			
												}

											}											

											$summary->rest_day_special_holiday_overtime = $totalOvertime;
											$summary->rest_day_special_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();

										} elseif ( 0 === (int) $data["action"] ) { //DENIED

										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = '';
												$totalNightDiff[1] = '';

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}

										}

										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}
											
											//clock_out_2:1,0,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}


												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}											

											//clock_out_3:1,0,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

											

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																							

											}
											
											//clock_out_3:1,0,1:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																							

											}													
											
											//clock_out_3:1,1,1:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																			

											}

										}

											$summary->rest_day_special_holiday_overtime = $totalOvertime;
											$summary->rest_day_special_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();


										}


									}

								} else { //HOLIDAY: FALSE - Regular Day

									echo "HOLIDAY: FALSE - Regular Day \n";

									if ( 1 === (int) $data["action"] ) { //APPROVED


										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:1,0,0:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

										}


										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:1,0,0:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Approved
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
												
												//Overtime		
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												//Overtime
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 1
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}																																			

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_3:1,0,0:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Approved
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
												
												//Overtime		
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												//Overtime
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 1
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}																																			

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Approved
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												//Overtime		
												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}	

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Approved
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

											}
											
											//clock_out_3:1,0,1:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																						
											}													
											
											//clock_out_3:1,1,1:Approved
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																		
											}

										}										

										$summary->rest_day_overtime = $totalOvertime;
										$summary->rest_day_overtime_night_diff = $totalNightDiff;								
										$summary->save();

									} elseif ( 0 === (int) $data["action"] ) { //DENIED

										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = '';
												$totalNightDiff[1] = '';

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}

										}

										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}
											
											//clock_out_2:1,0,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}


												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}											

											//clock_out_3:1,0,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Denied
											if ( ($overtimeStatusArr[1] === 0 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

											

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																							

											}
											
											//clock_out_3:1,0,1:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 0 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																							

											}													
											
											//clock_out_3:1,1,1:Denied
											if ( ($overtimeStatusArr[1] === 1 && $overtimeStatusArr[2] === 1 && $overtimeStatusArr[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																			

											}

										}

										$summary->rest_day_overtime = $totalOvertime;
										$summary->rest_day_overtime_night_diff = $totalNightDiff;								
										$summary->save();


									}

								}																										

							}				                

			            }		        		


		        	} elseif ( sizeof($data["check"]) === 1 ) { //ONLY ONE CHECK BOX IS CHECKED


		        		//foreach($data["check"] as $check) {
						
							$overtimeById = Overtime::whereIn('id', $data["check"])->first();

			                $employeeId = $overtimeById->employee_id;

			                if ( $overtimeById->seq_no === 1 || 
			                	 $overtimeById->seq_no === 2 ) {

			                		$shift = 1;

			                } elseif ( $overtimeById->seq_no === 3 ) {

			                		$shift = 2;
			                }

							$employeeSetting = Employeesetting::where('employee_id', $employeeId)->first();			                

							//$timesheet = new Timesheet;
							$timesheet = Timesheet::where('id', $overtimeById->timesheet_id)->first();

							//$summary = new Summary;
							$summary = Summary::where('employee_id', $employeeId)->where('daydate', trim($timesheet->daydate))->first();

							//$schedule = new Schedule;
							$schedule = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($timesheet->daydate))->first();

							//$holiday = new Holiday;
							$holiday = DB::table('holiday')->where('holiday_date', trim($timesheet->daydate))->first();

							//CHECK SCHEDULE IF HAS NIGHTDIFF;
							/*$scheduleHasNightDiff = nightDiff($timesheet->schedule_in, $timesheet->schedule_out, $timesheet->schedule_in);

							if ( $scheduleHasNightDiff > 0 ) { //Value of NightDiff is positive
								
								$scheduleHasNightDiff = TRUE;

							} else {

								$scheduleHasNightDiff = FALSE;

							}*/	

							if ( $overtimeById->seq_no === 1 ) {

								/*DB::table('employee_timesheet')
									->where('id', $overtimeById->timesheet_id)				
									->update(array('overtime_status_1' => $data["action"]));*/

								$timesheet->overtime_status_1 = $data["action"];
								$timesheet->save();

								DB::table('overtime')
									->where('id', $overtimeById->id)
									->where('seq_no', 1)
									->update(array('overtime_status' => $data["action"]));

								/*if(!$scheduleHasNightDiff[1]) {

									//$nightDiff[1] = nightDiff($timesheet->time_in_1, $timesheet->time_out_1, $schedule->start_time);
									$nightDiff[1] = nightDiff($schedule->end_time, $timesheet->time_out_1, $schedule->start_time);
									
									if ( $nightDiff[1] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiff[1] = TRUE;

									} else {

										$hasNightDiff[1] = FALSE;

									}	
								}								

								if(!$scheduleHasNightDiff[2]) {

									//$nightDiff[2] = nightDiff($timesheet->time_in_2, $timesheet->time_out_2, $schedule->start_time);
									$nightDiff[2] = nightDiff($schedule->end_time, $timesheet->time_out_2, $schedule->start_time);
									
									if ( $nightDiff[2] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiff[2] = TRUE;

									} else {

										$hasNightDiff[2] = FALSE;

									}

								}									

								if(!$scheduleHasNightDiff[3]) {

									//$nightDiff[3] = nightDiff($timesheet->time_in_3, $timesheet->time_out_3, $schedule->start_time);
									$nightDiff[3] = nightDiff($schedule->end_time, $timesheet->time_out_3, $schedule->start_time);
									
									if ( $nightDiff[3] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiff[3] = TRUE;

									} else {

										$hasNightDiff[3] = FALSE;

									}

								}*/	

								//return $timesheet->overtime_status_1;

							} elseif ( $overtimeById->seq_no === 2 ) {

								/*DB::table('employee_timesheet')
									->where('id', $overtimeById->timesheet_id)								
									->update(array('overtime_status_2' => $data["action"]));*/

								$timesheet->overtime_status_2 = $data["action"];
								$timesheet->save();									

								DB::table('overtime')
									->where('id', $overtimeById->id)
									->where('seq_no', 2)
									->update(array('overtime_status' => $data["action"]));

								/*if(!$scheduleHasNightDiff[1]) {

									//$nightDiff[1] = nightDiff($timesheet->time_in_1, $timesheet->time_out_1, $schedule->start_time);
									$nightDiff[1] = nightDiff($schedule->end_time, $timesheet->time_out_1, $schedule->start_time);
									
									if ( $nightDiff[1] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiff[1] = TRUE;

									} else {

										$hasNightDiff[1] = FALSE;

									}	
								}								

								if(!$scheduleHasNightDiff[2]) {

									//$nightDiff[2] = nightDiff($timesheet->time_in_2, $timesheet->time_out_2, $schedule->start_time);
									$nightDiff[2] = nightDiff($schedule->end_time, $timesheet->time_out_2, $schedule->start_time);
									
									if ( $nightDiff[2] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiff[2] = TRUE;

									} else {

										$hasNightDiff[2] = FALSE;

									}

								}									

								if(!$scheduleHasNightDiff[3]) {

									//$nightDiff[3] = nightDiff($timesheet->time_in_3, $timesheet->time_out_3, $schedule->start_time);
									$nightDiff[3] = nightDiff($schedule->end_time, $timesheet->time_out_3, $schedule->start_time);
									
									if ( $nightDiff[3] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiff[3] = TRUE;

									} else {

										$hasNightDiff[3] = FALSE;

									}

								}*/

							} elseif ( $overtimeById->seq_no === 3 ) {

								/*DB::table('employee_timesheet')
									->where('id', $overtimeById->timesheet_id)								
									->update(array('overtime_status_3' => $data["action"]));*/

								$timesheet->overtime_status_3 = $data["action"];
								$timesheet->save();									

								DB::table('overtime')
									->where('id', $overtimeById->id)
									->where('seq_no', 3)
									->update(array('overtime_status' => $data["action"]));

								/*if(!$scheduleHasNightDiff[1]) {

									//$nightDiff[1] = nightDiff($timesheet->time_in_1, $timesheet->time_out_1, $schedule->start_time);
									$nightDiff[1] = nightDiff($schedule->end_time, $timesheet->time_out_1, $schedule->start_time);
									
									if ( $nightDiff[1] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiff[1] = TRUE;

									} else {

										$hasNightDiff[1] = FALSE;

									}	
								}								

								if(!$scheduleHasNightDiff[2]) {

									//$nightDiff[2] = nightDiff($timesheet->time_in_2, $timesheet->time_out_2, $schedule->start_time);
									$nightDiff[2] = nightDiff($schedule->end_time, $timesheet->time_out_2, $schedule->start_time);
									
									if ( $nightDiff[2] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiff[2] = TRUE;

									} else {

										$hasNightDiff[2] = FALSE;

									}

								}									

								if(!$scheduleHasNightDiff[3]) {

									//$nightDiff[3] = nightDiff($timesheet->time_in_3, $timesheet->time_out_3, $schedule->start_time);
									$nightDiff[3] = nightDiff($schedule->end_time, $timesheet->time_out_3, $schedule->start_time);
									
									if ( $nightDiff[3] > 0 ) { //Value of NightDiff is positive
									
										$hasNightDiff[3] = TRUE;

									} else {

										$hasNightDiff[3] = FALSE;

									}

								}*/												

							}

							//TEMPORARY SETTING
							if ( $timesheet->overtime_status_1 === -1 || 
								 $timesheet->overtime_status_1 === NULL ||
								 empty($timesheet->overtime_status_1) ) {

									$overtimeStatus[1] = 0;											

							} else {

								$overtimeStatus[1] = (int) $timesheet->overtime_status_1;											

							}

							if ( $timesheet->overtime_status_2 === -1 || 
								 $timesheet->overtime_status_2 === NULL ||
								 empty($timesheet->overtime_status_2) ) {

									$overtimeStatus[2] = 0;											

							} else {

									$overtimeStatus[2] = (int) $timesheet->overtime_status_2;											

							}

							if ( $timesheet->overtime_status_3 === -1 || 
								 $timesheet->overtime_status_3 === NULL ||
								 empty($timesheet->overtime_status_3) ) {

									$overtimeStatus[3] = 0;											

							} else {

								$overtimeStatus[3] = (int) $timesheet->overtime_status_3;											

							}


							//CHECK IF REST DAY: FALSE							
							if ( $schedule->rest_day !== 1 ) {

								if ( !empty($holiday) ) { //HOLIDAY: TRUE

									echo "HOLIDAY: TRUE \n";

									if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

										if ( 1 === (int) $data["action"] ) { //APPROVED

											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

											}


											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_3:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													//Overtime		
													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}	

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

												}
												
												//clock_out_3:1,0,1:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																							
												}													
												
												//clock_out_3:1,1,1:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																			
												}

											}											

											$summary->legal_holiday_overtime = $totalOvertime;
											$summary->legal_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();

										} elseif ( 0 === (int) $data["action"] ) { //DENIED

											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:0,0,0:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[1] = '';
													$totalNightDiff[1] = '';

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}

											}

											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:0,0,0:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[1] = 0;
													$totalNightDiff[1] = 0;

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}
												
												//clock_out_2:1,0,0:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}


													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}												

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_2:0,0,0:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[1] = 0;
													$totalNightDiff[1] = 0;

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}											

												//clock_out_3:1,0,0:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}												

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																								

												}
												
												//clock_out_3:1,0,1:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																								

												}													
												
												//clock_out_3:1,1,1:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																				

												}

											}

											$summary->legal_holiday_overtime = $totalOvertime;
											$summary->legal_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();


										}										

									} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day

										if ( 1 === (int) $data["action"] ) { //APPROVED

											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

											}


											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_3:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													//Overtime		
													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}	

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

												}
												
												//clock_out_3:1,0,1:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																							
												}													
												
												//clock_out_3:1,1,1:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																			
												}

											}											

											$summary->special_holiday_overtime = $totalOvertime;
											$summary->special_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();

										} elseif ( 0 === (int) $data["action"] ) { //DENIED

											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:0,0,0:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[1] = '';
													$totalNightDiff[1] = '';

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}

											}

											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:0,0,0:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[1] = 0;
													$totalNightDiff[1] = 0;

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}
												
												//clock_out_2:1,0,0:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}


													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}												

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_2:0,0,0:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[1] = 0;
													$totalNightDiff[1] = 0;

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalNightDiff[1];

												}											

												//clock_out_3:1,0,0:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
															
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}												

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Denied
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																								

												}
												
												//clock_out_3:1,0,1:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																								

												}													
												
												//clock_out_3:1,1,1:Denied
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																				

												}

											}

											$summary->special_holiday_overtime = $totalOvertime;
											$summary->special_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();


										}


									}

								} else { //HOLIDAY: FALSE - Regular Day

									echo "HOLIDAY: FALSE - Regular Day \n";

									if ( 1 === (int) $data["action"] ) { //APPROVED

										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:1,0,0:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

										}


										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:1,0,0:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Approved
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
												
												//Overtime		
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												//Overtime
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 1
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}																																			

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_3:1,0,0:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Approved
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
												
												//Overtime		
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												//Overtime
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 1
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}																																			

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Approved
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												//Overtime		
												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}	

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Approved
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

											}
											
											//clock_out_3:1,0,1:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																						
											}													
											
											//clock_out_3:1,1,1:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																		
											}

										}											

										$summary->regular_overtime = $totalOvertime;
										$summary->regular_overtime_night_diff = $totalNightDiff;								
										$summary->save();

									} elseif ( 0 === (int) $data["action"] ) { //DENIED

										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = '';
												$totalNightDiff[1] = '';

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}

										}

										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}
											
											//clock_out_2:1,0,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}


												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}											

											//clock_out_3:1,0,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

											

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																							

											}
											
											//clock_out_3:1,0,1:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																							

											}													
											
											//clock_out_3:1,1,1:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																			

											}

										}

										$summary->regular_overtime = $totalOvertime;
										$summary->regular_overtime_night_diff = $totalNightDiff;								
										$summary->save();


									}

								}
	
							//CHECK IF REST DAY: TRUE							
							} elseif ( $schedule->rest_day === 1 ) {
																							 
								if ( !empty($holiday) ) { //HOLIDAY: TRUE

									echo "HOLIDAY: TRUE \n";

									if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

										if ( 1 === (int) $data["action"] ) { //APPROVED


											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

											}


											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_3:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													//Overtime		
													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}	

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

												}
												
												//clock_out_3:1,0,1:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																							
												}													
												
												//clock_out_3:1,1,1:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																			
												}

											}											

											$summary->rest_day_legal_holiday_overtime = $totalOvertime;
											$summary->rest_day_legal_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();

										} elseif ( 0 === (int) $data["action"] ) { //DENIED

										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = '';
												$totalNightDiff[1] = '';

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}

										}

										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}
											
											//clock_out_2:1,0,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}


												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}											

											//clock_out_3:1,0,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

											

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																							

											}
											
											//clock_out_3:1,0,1:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																							

											}													
											
											//clock_out_3:1,1,1:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																			

											}

										}

											$summary->rest_day_legal_holiday_overtime = $totalOvertime;
											$summary->rest_day_legal_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();


										}										

									} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day

										if ( 1 === (int) $data["action"] ) { //APPROVED


											if  ( $timesheet->clocking_status === "clock_out_1" ) {

												//clock_out_1:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

											}


											if  ( $timesheet->clocking_status === "clock_out_2" ) {

												//clock_out_2:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_2:0,1,0:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_2:1,1,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}

											}	

											if  ( $timesheet->clocking_status === "clock_out_3" ) {

												//clock_out_3:1,0,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
													
													//Overtime
													$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}											

													$totalOvertime = $totalOvertime[1];
													$totalNightDiff = $totalOvertimeNightDiff[1];

												}

												//clock_out_3:0,1,0:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
													
													//Overtime		
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}	

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2];

												}											

												//clock_out_3:1,1,0:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

													//Overtime
													$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 1
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}																																			

													$totalOvertime = $totalOvertime[2];
													$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

													//return $totalNightDiff;

												}


												//clock_out_3:0,0,1:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													//Overtime		
													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}	

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3];

												}
												
												//clock_out_3:0,1,1:Approved
												if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

												}
												
												//clock_out_3:1,0,1:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																							
												}													
												
												//clock_out_3:1,1,1:Approved
												if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

													$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

													//Overtime + Night Differential 1
													$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

													if( $overtimeNightDiff[1] !== FALSE ) {

														$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
													
													} else {

														$totalOvertimeNightDiff[1] = 0;

													}

													//Overtime + Night Differential 2
													$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

													if( $overtimeNightDiff[2] !== FALSE ) {

														$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
													
													} else {

														$totalOvertimeNightDiff[2] = 0;

													}


													//Overtime + Night Differential 3
													$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

													if( $overtimeNightDiff[3] !== FALSE ) {

														$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
													
													} else {

														$totalOvertimeNightDiff[3] = 0;

													}												

													$totalOvertime = $totalOvertime[3];
													$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																			
												}

											}											

											$summary->rest_day_special_holiday_overtime = $totalOvertime;
											$summary->rest_day_special_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();

										} elseif ( 0 === (int) $data["action"] ) { //DENIED

										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = '';
												$totalNightDiff[1] = '';

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}

										}

										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}
											
											//clock_out_2:1,0,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}


												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}											

											//clock_out_3:1,0,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

											

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																							

											}
											
											//clock_out_3:1,0,1:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																							

											}													
											
											//clock_out_3:1,1,1:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																			

											}

										}

											$summary->rest_day_special_holiday_overtime = $totalOvertime;
											$summary->rest_day_special_holiday_overtime_night_diff = $totalNightDiff;								
											$summary->save();


										}


									}

								} else { //HOLIDAY: FALSE - Regular Day

									echo "HOLIDAY: FALSE - Regular Day \n";

									if ( 1 === (int) $data["action"] ) { //APPROVED


										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:1,0,0:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

										}


										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:1,0,0:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Approved
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
												
												//Overtime		
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												//Overtime
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 1
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}																																			

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_3:1,0,0:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
												
												//Overtime
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}											

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Approved
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
												
												//Overtime		
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												//Overtime
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 1
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}																																			

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Approved
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												//Overtime		
												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}	

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Approved
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];														

											}
											
											//clock_out_3:1,0,1:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	
																						
											}													
											
											//clock_out_3:1,1,1:Approved
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	
																																		
											}

										}										

										$summary->rest_day_overtime = $totalOvertime;
										$summary->rest_day_overtime_night_diff = $totalNightDiff;								
										$summary->save();

									} elseif ( 0 === (int) $data["action"] ) { //DENIED

										if  ( $timesheet->clocking_status === "clock_out_1" ) {

											//clock_out_1:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = '';
												$totalNightDiff[1] = '';

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}

										}

										if  ( $timesheet->clocking_status === "clock_out_2" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}
											
											//clock_out_2:1,0,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);												

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}


												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_2:0,1,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_2:1,1,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

												//return $totalNightDiff;

											}

										}	

										if  ( $timesheet->clocking_status === "clock_out_3" ) {

											//clock_out_2:0,0,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = 0;
												$totalNightDiff[1] = 0;

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalNightDiff[1];

											}											

											//clock_out_3:1,0,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[1] = number_format((double) ($timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												$totalOvertime = $totalOvertime[1];
												$totalNightDiff = $totalOvertimeNightDiff[1];

											}

											//clock_out_3:0,1,0:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {
														
												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2), 2);

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalOvertimeNightDiff[2];

											}											

											//clock_out_3:1,1,0:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 0) ) {

												$totalOvertime[2] = number_format((double) ($timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}												

												$totalOvertime = $totalOvertime[2];
												$totalNightDiff = $totalNightDiff[2] + $totalNightDiff[1];	

												//return $totalNightDiff;

											}


											//clock_out_3:0,0,1:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3), 2);

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3];

											}
											
											//clock_out_3:0,1,1:Denied
											if ( ($overtimeStatus[1] === 0 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2), 2);


												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}

											

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2];	

																							

											}
											
											//clock_out_3:1,0,1:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 0 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[1];	

																							

											}													
											
											//clock_out_3:1,1,1:Denied
											if ( ($overtimeStatus[1] === 1 && $overtimeStatus[2] === 1 && $overtimeStatus[3] === 1) ) {

												$totalOvertime[3] = number_format((double) ($timesheet->total_overtime_3 + $timesheet->total_overtime_2 + $timesheet->total_overtime_1), 2);

												//Overtime + Night Differential 1
												$overtimeNightDiff[1] = nightDifferential($timesheet->schedule_out, $timesheet->time_out_1, nightDifferentialRange());												

												if( $overtimeNightDiff[1] !== FALSE ) {

													$totalOvertimeNightDiff[1] = number_format((double) ($overtimeNightDiff[1]), 2);
												
												} else {

													$totalOvertimeNightDiff[1] = 0;

												}

												//Overtime + Night Differential 2
												$overtimeNightDiff[2] = nightDifferential($timesheet->time_in_2, $timesheet->time_out_2, nightDifferentialRange());												

												if( $overtimeNightDiff[2] !== FALSE ) {

													$totalOvertimeNightDiff[2] = number_format((double) ($overtimeNightDiff[2]), 2);
												
												} else {

													$totalOvertimeNightDiff[2] = 0;

												}	


												//Overtime + Night Differential 3
												$overtimeNightDiff[3] = nightDifferential($timesheet->time_in_3, $timesheet->time_out_3, nightDifferentialRange());												

												if( $overtimeNightDiff[3] !== FALSE ) {

													$totalOvertimeNightDiff[3] = number_format((double) ($overtimeNightDiff[3]), 2);
												
												} else {

													$totalOvertimeNightDiff[3] = 0;

												}												

												$totalOvertime = $totalOvertime[3];
												$totalNightDiff = $totalOvertimeNightDiff[3] + $totalOvertimeNightDiff[2] + $totalOvertimeNightDiff[1];	

																																			

											}

										}

										$summary->rest_day_overtime = $totalOvertime;
										$summary->rest_day_overtime_night_diff = $totalNightDiff;								
										$summary->save();


									}

								}																										

							}								


		        		//}

		        	}
				
				}

				return Redirect::route('process.overtime.lists');

			} elseif ( empty($data["check"]) ) {

				return Redirect::route('process.overtime.lists');

			}

		} else {

			return Redirect::route('process.overtime.lists');

		}

	}


}
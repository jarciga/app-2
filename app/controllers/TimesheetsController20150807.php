<?php

class TimesheetsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /timesheets
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /timesheets/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /timesheets
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /timesheets/{id}
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
	 * GET /timesheets/{id}/edit
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
	 * PUT /timesheets/{id}
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
	 * DELETE /timesheets/{id}
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

		$employee = Employee::where('id', $currentUserId)->first();

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
		
		$holiday = Holiday::where('holiday_date', $currentDate)->first();

		return $dataArr = array( 			
					'employee' => $employee,
					'employeeSetting' => $employeeSetting,
					'timesheets' => $timesheets,
					'timesheet' => $timesheet,
					'summaries' => $summaries,
					'summary' => $summary,
					'schedule' => $schedule,
					'holiday' => $holiday,
					'currentDate' => $currentDate,
					'currentTime' => $currentTime,
					'currentDateTime' => $currentDateTime
					);							

	}

	public function showTimesheet() {

		$dataArr = $this->init();

		return View::make('index', $dataArr);

	}

	public function updateServerTime() {

	    if(Request::ajax()){
	        return date('H:').date('i').'<span class="" style="font-size:21px; margin-top:0px;">'.date('s').'</span>';
	    }
	}

	public function getServerDateTime() {
		
		if(Request::ajax()){

			$dateTime = new DateTime();
			return $dateTime->format('D, M d');

		}

	}	

	public function redrawTimesheet()
	{

		if( Request::ajax() ) {
			
			$timesheet = new Timesheet;
			$timesheetJson = $timesheet->timesheetJson(Session::get('currentUserId'), Session::get('dayDateArr'));	
			
			return $timesheetJson;

		}

	}


	public function timeClocking() {

		$data = Input::all();	

		$dataArr = $this->init();

		$employee = $dataArr["employee"];
		$employeeSetting = $dataArr["employeeSetting"];
		$timesheets = $dataArr["timesheets"];
		$timesheet = $dataArr["timesheet"];
		$summary = $dataArr["summary"];
		$summaries = $dataArr["summaries"];
		$schedule = $dataArr["schedule"];		
		$holiday = $dataArr["holiday"];		 

		if(Request::ajax()) {

			/**
			*
			* CLOCKING IN
			*
			*/	

			if ( $data['timeclocking'] == "in" )
			{

				echo "IN \n";

				//GET INPUT DATA
				$timesheetTimeIn = $data["timein"];			

				//DB TABLE
				$isFlexible = $employeeSetting->is_flexible;
				$scheduleStartTime = $schedule->start_time;

				//CHECK CLOCKING STATUS: OPEN
				if ( $timesheet->clocking_status === "open" ) {

					// CHECK IF FLEXIBLE SCHEDULE: FALSE
					if ( !$isFlexible ) {

						$timesheet->clocking_status = "clock_in_1";
						$timesheet->time_in_1 = $timesheetTimeIn;

						//WITH COMPUTATION: TRUE

						//GET TARDINESS
						$tardinessTime = tardinessTime($timesheetTimeIn, $scheduleStartTime);
						$timesheet->tardiness_1 = $tardinessTime;
						$summary->lates = $tardinessTime;

						if ($timesheet->save() ) {
							$summary->save();														
							return Redirect::to('/redraw/timesheet');

						}

					} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

						$timesheet->clocking_status = "clock_in_1";
						$timesheet->time_in_1 = $timesheetTimeIn;
						
						//WITH COMPUTATION: FALSE

						if ($timesheet->save() ) {
							$summary->save();														
							return Redirect::to('/redraw/timesheet');

						}						

					} 

				}



				//CHECK CLOCKING STATUS: CLOCK_OUT_1
				if ( $timesheet->clocking_status === "clock_out_1" ) {

					$timesheet->clocking_status = "clock_in_2";
					$timesheet->time_in_2 = $timesheetTimeIn;

					if ($timesheet->save() ) {
						$summary->save();														
						return Redirect::to('/redraw/timesheet');

					}					

				}



			}

			/**
			*
			* CLOCKING OUT
			*
			*/

			if ( $data['timeclocking'] == "out" ) 
			{

				echo "OUT \n";

				//return dd($schedule);

				//GET INPUT DATA
				$timesheetTimeOut = $data["timeout"];			

				//DB TABLE
				
				if ( $timesheet->clocking_status === "clock_in_1" ) {

					$timesheetTimeIn = $timesheet->time_in_1;

				} elseif ( $timesheet->clocking_status === "clock_in_2" ) {

					$timesheetTimeIn = $timesheet->time_in_2;

				} elseif ( $timesheet->clocking_status === "clock_in_3" ) {

					$timesheetTimeIn = $timesheet->time_in_3;					

				}

				$scheduleStartTime = $schedule->start_time;
				$scheduleEndTime = $schedule->end_time;
				$isFlexible = $employeeSetting->is_flexible;
				$hasBreak = $employeeSetting->has_break;
				$breakTime = $employeeSetting->break_time; //date('G', timestamp($employeeSetting->break_time));
				$hoursPerDay = $employeeSetting->hours_per_day;

				$halfOfhoursPerDay = ($hoursPerDay / 2);

				//CHECK CLOCKING STATUS: OPEN
				if ( $timesheet->clocking_status === "clock_in_1" ) {


					// CHECK IF FLEXIBLE SCHEDULE: FALSE
					if ( !$isFlexible ) {

						//CHECK SCHEDULE : TRUE
						if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
						     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

							//CHECK IF REST DAY: FALSE							
							if ( $schedule->rest_day !== 1 ) {

								/*$timesheet->clocking_status = "clock_out_1";
								$timesheet->time_out_1 = $timesheetTimeOut;	
								
								//WITH COMPLETE COMPUTATION: TRUE
								extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
								
								//GET TOTALHOURS											
								//GET TOTALHOURS WITH OVERTIME
								echo $timesheet->total_hours_1 = $totalHours;
								echo "\n";

								//GET WORKHOURS
								echo $timesheet->work_hours_1 = $workHours;
								echo "\n";

								//GET OVERTIME													
								if($hasOvertime) {

									$timesheet->total_overtime_1 = $overtimeHours;
									echo "\n";

								} 
								
								//GET UNDERTIME
								echo $timesheet->undertime_1 = $underTimeHours;
								$summary->undertime = $underTimeHours;
								echo "\n";

								//GET NIGHTDIFF;
								if( $hasNightDiff ) {

									if ( $nightDiff > 0 ) { //Value of NightDiff is positive
										
										$timesheet->night_differential_1 = $nightDiff;							
										echo "\n";
									}

								}
																 
								if ( !empty($holiday) ) { //HOLIDAY: TRUE

									echo "HOLIDAY: TRUE \n";

									if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

										echo "Regular holiday \n";

										echo $summary->legal_holiday = $workHours;

										if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
											$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

										}

										if($hasOvertime) { //ISOVERTIME: TRUE

											$summary->legal_holiday_overtime = $overtimeHours;

										}

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summary->legal_holiday_night_diff = $nightDiff;

										}
									
									} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

										echo "Special non-working day \n";										

										$summary->special_holiday = $workHours;

										if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;						

										}					

										if($hasOvertime) { //ISOVERTIME: TRUE

											$summary->special_holiday_overtime = $overtimeHours;
											
										}

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summary->special_holiday_night_diff = $nightDiff;

										}										

									}

								} else { //HOLIDAY: FALSE - Regular Day



									echo "HOLIDAY: FALSE - Regular Day \n";

									$summary->regular = $workHours;									

									if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

										$overtimeNightdiff = $overtimeHours + $nightDiff;
										$summary->regular_overtime_night_diff = $overtimeNightdiff;						

									}

									if ($hasOvertime) { //ISOVERTIME: TRUE

										$summary->regular_overtime = $overtimeHours;

									}				

									if($hasNightDiff) { //HASNIGHTDIFF: TRUE

										$summary->regular_night_differential = $nightDiff;

									}									

								}
			
								if ($timesheet->save() ) {

									return Redirect::to('/redraw/timesheet');

								}*/

								regularDay();

							//CHECK IF REST DAY: TRUE							
							} elseif ( $schedule->rest_day === 1 ) {


							}								

						}
											

					} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

						$timesheet->clocking_status = "clock_out_1";
						$timesheet->time_out_1 = $timesheetTimeOut;	
						
						//WITHOUT THE COMPLETE COMPUTATION: TRUE

						//GET TOTALHOURS WITH OVERTIME
						echo $timesheet->total_hours_1 = totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

						//GET WORKHOURS
						echo $timesheet->work_hours_1 = workHours($timesheetTimeIn, $timesheetTimeOut, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
						
						//GET UNDERTIME
						echo $timesheet->undertime_1 = underTimeHours($timesheetTimeOut, $scheduleEndTime);
						
						// GET NIGHTDIFF;
						$nightDiff = nightDiff($timesheetTimeIn, $timesheetTimeOut);																

						if ($timesheet->save() ) {

							return Redirect::to('/redraw/timesheet');

						}						

					}

				}




				//CHECK CLOCKING STATUS: CLOCK_IN_2
				if ( $timesheet->clocking_status === "clock_in_2" ) {

					// CHECK IF FLEXIBLE SCHEDULE: FALSE
					if ( !$isFlexible ) {

						//CHECK SCHEDULE : TRUE
						if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
						     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

							//CHECK IF REST DAY: FALSE							
							if ( $schedule->rest_day !== 1 ) {

								$timesheet->clocking_status = "clock_out_2";
								$timesheet->time_out_2 = $timesheetTimeOut;	
								
								//WITH COMPLETE COMPUTATION: TRUE

								extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
								//return dd($this->timesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));																
								//$this->timesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
								
								//GET TOTALHOURS											
								//GET TOTALHOURS WITH OVERTIME
								echo $timesheet->total_hours_2 = $totalHours;
								echo "\n";

								//GET WORKHOURS
								echo $timesheet->work_hours_2 = $workHours;
								echo "\n";

								//GET OVERTIME													
								if($hasOvertime) {

									$timesheet->total_overtime_2 = $overtimeHours;
									echo "\n";

								} 
								
								//GET NIGHTDIFF;
								if( $hasNightDiff ) {

									if ( $nightDiff > 0 ) { //Value of NightDiff is positive
										
										$timesheet->night_differential_2 = $nightDiff;							
										echo "\n";
									}

								}
																 
								if ( !empty($holiday) ) { //HOLIDAY: TRUE

									echo "HOLIDAY: TRUE \n";

									if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

										echo "Regular holiday \n";

										echo $summary->legal_holiday = $workHours;

										if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
											$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

										}

										if($hasOvertime) { //ISOVERTIME: TRUE

											$summary->legal_holiday_overtime = $overtimeHours;

										}

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summary->legal_holiday_night_diff = $nightDiff;

										}
									
									} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

										echo "Special non-working day \n";										

										$summary->special_holiday = $workHours;

										if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;						

										}					

										if($hasOvertime) { //ISOVERTIME: TRUE

											$summary->special_holiday_overtime = $overtimeHours;
											
										}

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summary->special_holiday_night_diff = $nightDiff;

										}										

									}

								} else { //HOLIDAY: FALSE - Regular Day

									echo "HOLIDAY: FALSE - Regular Day \n";

									$summary->regular = $workHours;

									if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

										$overtimeNightdiff = $overtimeHours + $nightDiff;
										$summary->regular_overtime_night_diff = $overtimeNightdiff;						

									}

									if ($hasOvertime) { //ISOVERTIME: TRUE

										$summary->regular_overtime = $overtimeHours;

									}				

									if($hasNightDiff) { //HASNIGHTDIFF: TRUE

										$summary->regular_night_differential = $nightDiff;

									}									

								}
			
								if ($timesheet->save() ) {

									return Redirect::to('/redraw/timesheet');

								}

							//CHECK IF REST DAY: TRUE							
							} elseif ( $schedule->rest_day === 1 ) {


							}								

						}
												

					} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

						$timesheet->clocking_status = "clock_out_2";
						$timesheet->time_out_2 = $timesheetTimeOut;	
						
						//WITHOUT THE COMPLETE COMPUTATION: TRUE

						//GET TOTALHOURS WITH OVERTIME
						echo $timesheet->total_hours_2 = totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

						//GET WORKHOURS
						echo $timesheet->work_hours_2 = workHours($timesheetTimeIn, $timesheetTimeOut, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
						
						//GET UNDERTIME
						echo $timesheet->undertime_2 = underTimeHours($timesheetTimeOut, $scheduleEndTime);
						
						// GET NIGHTDIFF;
						$nightDiff = nightDiff($timesheetTimeIn, $timesheetTimeOut);																

						if ($timesheet->save() ) {

							return Redirect::to('/redraw/timesheet');

						}						

					}

				}								

			}

		}		

	}

	public function TimeIn() {

		return 'time in';

	}	


	public function TimeOut() {


	}

	public function processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak) {

		$dataArr = $this->init();

		$employee = $dataArr["employee"];
		$employeeSetting = $dataArr["employeeSetting"];
		$timesheets = $dataArr["timesheets"];
		$timesheet = $dataArr["timesheet"];
		$summary = $dataArr["summary"];
		$summaries = $dataArr["summaries"];
		$schedule = $dataArr["schedule"];		
		$holiday = $dataArr["holiday"];	

		//GET TOTALHOURS											
		//echo $timesheet->total_hours_1 = totalHours($timesheetTimeIn, $timesheetTimeOut);
		//echo "\n";

		//GET TOTALHOURS WITH OVERTIME
		$totalHours = totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
		//echo $timesheet->total_hours_1 = $totalHours;

		//GET WORKHOURS
		$workHours = workHours($timesheetTimeIn, $timesheetTimeOut, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
		//echo $timesheet->work_hours_1 = $workHours;
		echo "\n";

		//GET OVERTIME						
		$overtimeHours = overtimeHours($timesheetTimeOut, $scheduleEndTime);
		
		if ( $overtimeHours ) {

			$hasOvertime = $overtimeHours;

		} else {
		
			$hasOvertime = FALSE;			

		}

		//echo $timesheet->total_overtime_1 = $overtimeHours;
		echo "\n";

		//GET UNDERTIME
		$underTimeHours = underTimeHours($timesheetTimeOut, $scheduleEndTime);
		//echo $timesheet->undertime_1 = $underTimeHours;
		//echo $summary->undertime = $underTimeHours;
		echo "\n";

		//GET NIGHTDIFF;
		$nightDiff = nightDiff($timesheetTimeIn, $timesheetTimeOut);
		//$hasNightDiff = $nightDiff;

		if ( $nightDiff > 0 ) { //Value of NightDiff is positive
			
			//$timesheet->night_differential_1 = $nightDiff;							
			$hasNightDiff = TRUE;

		} else {

			$hasNightDiff = FALSE;

		}

		echo "\n";

		return array(
					'totalHours' => $totalHours,
			 		'workHours' => $workHours,
			 		'overtimeHours' => $overtimeHours,
			 		'hasOvertime' => $hasOvertime,
					'underTimeHours' => $underTimeHours,			 		
			 		'hasNightDiff' => $hasNightDiff,
			 		'nightDiff' => $nightDiff
		 		);		

	}


	public function regularDay() {

		$dataArr = $this->init();

		$employee = $dataArr["employee"];
		$employeeSetting = $dataArr["employeeSetting"];
		$timesheets = $dataArr["timesheets"];
		$timesheet = $dataArr["timesheet"];
		$summary = $dataArr["summary"];
		$summaries = $dataArr["summaries"];
		$schedule = $dataArr["schedule"];		
		$holiday = $dataArr["holiday"];	

		//GET INPUT DATA
		$timesheetTimeOut = $data["timeout"];			

		//DB TABLE
		
		if ( $timesheet->clocking_status === "clock_in_1" ) {

			$timesheetTimeIn = $timesheet->time_in_1;

		} elseif ( $timesheet->clocking_status === "clock_in_2" ) {

			$timesheetTimeIn = $timesheet->time_in_2;

		} elseif ( $timesheet->clocking_status === "clock_in_3" ) {

			$timesheetTimeIn = $timesheet->time_in_3;					

		}

		$scheduleStartTime = $schedule->start_time;
		$scheduleEndTime = $schedule->end_time;
		$isFlexible = $employeeSetting->is_flexible;
		$hasBreak = $employeeSetting->has_break;
		$breakTime = $employeeSetting->break_time; //date('G', timestamp($employeeSetting->break_time));
		$hoursPerDay = $employeeSetting->hours_per_day;

		$halfOfhoursPerDay = ($hoursPerDay / 2);

		//WITH COMPLETE COMPUTATION: TRUE
		extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

		//GET TOTALHOURS											
		//GET TOTALHOURS WITH OVERTIME
		echo $timesheet->total_hours_1 = $totalHours;
		echo "\n";

		//GET WORKHOURS
		echo $timesheet->work_hours_1 = $workHours;
		echo "\n";

		//GET OVERTIME													
		if($hasOvertime) {

			$timesheet->total_overtime_1 = $overtimeHours;
			echo "\n";

		} 

		//GET UNDERTIME
		echo $timesheet->undertime_1 = $underTimeHours;
		$summary->undertime = $underTimeHours;
		echo "\n";

		//GET NIGHTDIFF;
		if( $hasNightDiff ) {

			if ( $nightDiff > 0 ) { //Value of NightDiff is positive
				
				$timesheet->night_differential_1 = $nightDiff;							
				echo "\n";
			}

		}
										 
		if ( !empty($holiday) ) { //HOLIDAY: TRUE

			echo "HOLIDAY: TRUE \n";

			if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

				echo "Regular holiday \n";

				echo $summary->legal_holiday = $workHours;

				if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

					$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
					$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

				}

				if($hasOvertime) { //ISOVERTIME: TRUE

					$summary->legal_holiday_overtime = $overtimeHours;

				}

				if($hasNightDiff) { //HASNIGHTDIFF: TRUE

					$summary->legal_holiday_night_diff = $nightDiff;

				}
			
			} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

				echo "Special non-working day \n";										

				$summary->special_holiday = $workHours;

				if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

					$overtimeNightdiff = $overtimeHours + $nightDiff;
					$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;						

				}					

				if($hasOvertime) { //ISOVERTIME: TRUE

					$summary->special_holiday_overtime = $overtimeHours;
					
				}

				if($hasNightDiff) { //HASNIGHTDIFF: TRUE

					$summary->special_holiday_night_diff = $nightDiff;

				}										

			}

		} else { //HOLIDAY: FALSE - Regular Day



			echo "HOLIDAY: FALSE - Regular Day \n";

			$summary->regular = $workHours;									

			if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

				$overtimeNightdiff = $overtimeHours + $nightDiff;
				$summary->regular_overtime_night_diff = $overtimeNightdiff;						

			}

			if ($hasOvertime) { //ISOVERTIME: TRUE

				$summary->regular_overtime = $overtimeHours;
																
			}

			if($hasNightDiff) { //HASNIGHTDIFF: TRUE

				$summary->regular_night_differential = $nightDiff;

			}									

		}

		if ($timesheet->save() ) {

			return Redirect::to('/redraw/timesheet');

		}

	}

}
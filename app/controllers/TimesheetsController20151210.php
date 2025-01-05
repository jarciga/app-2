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
		$groupName = Session::get('groupName');
		$currentUserEmployeType = Session::get('currentUserEmployeType');
		$currentUserEmployeTypeName = Session::get('currentUserEmployeTypeName');

		$dayDateArr = Session::get('dayDateArr');
		$currentDate = Config::get('euler.current_date');
		$currentTime = Config::get('euler.current_time');
		$currentDateTime = Config::get('euler.current_date_time');
		$hoursPerDay = Config::get('euler.hours_per_day');		
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
					'currentUserEmployeType' => $currentUserEmployeType,					
					'currentUserEmployeTypeName' => $currentUserEmployeTypeName,					
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


	public function showTimesheet() {

		$dataArr = $this->init();

		$employeesController = new EmployeesController;		
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();
		
		$absencesController = new AbsencesController;
		$dataArr["currentAbsencesPerCutoff"] = $absencesController->currentAbsencesPerCutoff();		

		return View::make('index', $dataArr);

	}


	public function showSearchTimesheet($id) {

		$data = Input::all();
		$id = (int) $id;

		$dataArr = $this->init();
		$dayDateArr = $dataArr["dayDateArr"];

		$d = date('d', strtotime($dayDateArr[0])); //previous
		//GET THE FIRST INDEX ON THE SESSION DAYDATEARR
        $cutoffStart = Config::get('euler.cutoff.cutOffStart'); //previous
        $cutOffEnd = Config::get('euler.cutoff.cutOffEnd'); //previous

        $prevMonthNumberOfDays = Session::get('prevMonthNumberOfDays');

        //begin previous
		$dayDateArrCount = count($dayDateArr);               
		//echo $dayDateArr[$dayDateArrCount-1];

		if($cutoffStart[1] === (int) $d) { //If 11 is the first index of the current timesheet then the previous cutoff is 26 to 10

		  //echo $cutoffStart[2];
		  //echo $cutOffEnd[2]; 
		  
 		  $date1 = new DateTime($dayDateArr[0]); //set the first index of the current month;        

 		  //return dd($date1); //015-09-11 00:00:00.000000

 		  //return dd($dayDateArrCount);

		  if(31 === (int) $prevMonthNumberOfDays) {
			
			  $date1->modify('-'.($dayDateArrCount+1).' day');          
			  $cutoffStart['previous'] = $date1->format('Y-m-d');

		  	  $date2 = new DateTime($dayDateArr[0]); //set the first index of the current month;         
			  $date2->modify('-1 day');          
			  $cutoffEnd['previous'] = $date2->format('Y-m-d');  

		  } else {

			  //$date1->modify('-'.($dayDateArrCount-1).' day');          
			  $date1->modify('-'.($dayDateArrCount).' day');          
			  $cutoffStart['previous'] = $date1->format('Y-m-d');

		  	  $date2 = new DateTime($dayDateArr[0]); //set the first index of the current month;         
			  $date2->modify('+1 day');          
			  $cutoffEnd['previous'] = $date2->format('Y-m-d');  			  

		  }
	 	 
		} 
		
		if($cutoffStart[2] === (int) $d) { //If 26 is the first index of the current timesheet then the previous cutoff is 11 to 25

		  //echo $cutoffStart[1];
		  //echo $cutOffEnd[1];
		  
		  //CHECK THIS
		  if(31 === (int) $prevMonthNumberOfDays) {

			  $date1 = new DateTime($dayDateArr[0]); //set the first index of the current month;        
			  $date1->modify('-'.($dayDateArrCount-1).' day'); //Bug here           
			  $cutoffStart['previous'] = $date1->format('Y-m-d');

		  } else {

		  	  $date1 = new DateTime($dayDateArr[0]); //set the first index of the current month;        
			  $date1->modify('-'.($dayDateArrCount).' day'); //Bug here           
			  $cutoffStart['previous'] = $date1->format('Y-m-d');		  
		  }
		  
		  //echo $cutoffStart['previous'];

		  $date2 = new DateTime($dayDateArr[0]); //set the first index of the current month;         
		  $date2->modify('-1 day');          
		  $cutoffEnd['previous'] = $date2->format('Y-m-d');  

		 //echo $cutoffEnd['previous'];            
		}
        // end previous

		$employeesController = new EmployeesController;		
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		//$absencesController = new AbsencesController;
		//$dataArr["currentAbsencesPerCutoff"] = $absencesController->currentAbsencesPerCutoff();

		//$employeeSearchId = (int) $data['employeeid'];		

		//$employeeSearchId = (isset($data['employeeid']) && !empty($data['employeeid'])) ? $data['employeeid'] : 0;

		$employeeSearchId = (isset($id) && !empty($id)) ? $id : 0;
		
		if ( $employeeSearchId !== 0 ) {
			
			$dataArr["employeeSearchId"] = $employeeSearchId;
			$dataArr["employeeSearch"] = Employee::where('id', $employeeSearchId)->first();
			$dataArr["searchTimesheets"] = Timesheet::where('employee_id', $employeeSearchId)
								   				    ->whereIn('daydate', $dayDateArr)->paginate(count($dayDateArr));


  		    $dataArr["previousSearchTimesheets"] = Timesheet::where('employee_id', $employeeSearchId)
  	                                                        ->whereBetween('daydate', array($cutoffStart['previous'], $cutoffEnd['previous']))->get();
			
			Session::put('employeeSearchId', $employeeSearchId);			

			Session::put('previousSearchTimesheets', $dataArr["previousSearchTimesheets"]);

			//Session::put('dataArr', $dataArr);

			$previousSearchTimesheets = Session::get('previousSearchTimesheets');
			
			foreach($previousSearchTimesheets as $previousSearchTimesheetVal) {

				$previousCutoffdayDateArr[] = $previousSearchTimesheetVal->daydate;

			}

			if(!empty($previousCutoffdayDateArr)) {
				Session::put('previousCutoffdayDateArr', $previousCutoffdayDateArr);
			} else {
				Session::put('previousCutoffdayDateArr', '');
			}


			/*
			$employee = new Employee;		
			$employeeInfo = $employee->getEmployeeInfoById($employeeId);
			$employeeSearchInfo = $employee->getEmployeeInfoById($searchEmployeeId);
			['employeeInfo' => $employeeInfo, 'employeeSearchInfo' => $employeeSearchInfo, 'searchEmployeeId' => $searchEmployeeId]
			*/			

			return View::make('searchtimesheet.index', $dataArr);

		} else {

			return Redirect::to('/search/timesheet');
			//return View::make('searchtimesheet.index', $dataArr);

		}

	}


	public function showPreviousSearchTimesheet($id) {

		$data = Input::all();
		$id = (int) $id;

		$dataArr = $this->init();
		$dayDateArr = $dataArr["dayDateArr"];		

		$employeesController = new EmployeesController;		
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$employeeSearchId = (isset($id) && !empty($id)) ? $id : 0;
		
		if ( $employeeSearchId !== 0 ) {
			
			$dataArr["employeeSearchId"] = $employeeSearchId;

			$dataArr["employeeSearch"] = Employee::where('id', $employeeSearchId)->first();
			$dataArr["searchTimesheets"] = Timesheet::where('employee_id', $employeeSearchId)
								   				    ->whereIn('daydate', $dayDateArr)->paginate(count($dayDateArr));

			Session::put('employeeSearchId', $employeeSearchId);			
			//Session::put('dataArr', $dataArr);

			$d = date('d', strtotime($dayDateArr[0]));

			//'cutoff' => array('cutOffStart' => array(1 => 11, 2 => 26), 'cutOffEnd' => array(1 => 25, 2 => 10))
			//GET THE FIRST INDEX ON THE SESSION DAYDATEARR
			$cutoffStart = Config::get('euler.cutoff.cutOffStart');
			$cutOffEnd = Config::get('euler.cutoff.cutOffEnd');

			$dayDateArrCount = count($dayDateArr);               
			//echo $dayDateArr[$dayDateArrCount-1];

			if($cutoffStart[1] === (int) $d) { //If 11 is the first index of the current timesheet then the previous cutoff is 26 to 10

			  //echo $cutoffStart[2];
			  //echo $cutOffEnd[2];          

			} 

			if($cutoffStart[2] === (int) $d) { //If 26 is the first index of the current timesheet then the previous cutoff is 11 to 25

			  //echo $cutoffStart[1];
			  //echo $cutOffEnd[1];
			  
			  $date1 = new DateTime($dayDateArr[0]); //set the first index of the current month;        
			  $date1->modify('-'.($dayDateArrCount-1).' day');          
			  $cutoffStart['previous'] = $date1->format('Y-m-d');
			  
			  //echo $cutoffStart['previous'];

			  $date2 = new DateTime($dayDateArr[0]); //set the first index of the current month;         
			  $date2->modify('-1 day');          
			  $cutoffEnd['previous'] = $date2->format('Y-m-d');  

			 //echo $cutoffEnd['previous'];        

			  $dataArr["previousSearchTimesheets"] = Timesheet::where('employee_id', $employeeSearchId)
			                                          ->whereBetween('daydate', array($cutoffStart['previous'], $cutoffEnd['previous']))->get();

			  //dd($dataArr["searchTimesheets"]);

			  //foreach($dataArr["searchTimesheets"] as $searchTimesheet) {

			    //echo $searchTimesheet->daydate;
			    //echo '<br />';

			 // }
			                      

	            //Session::put('previousCutoffDayDateArr', $searchTimesheet->daydate);
	            Session::put('dataArr', $dataArr);

			}

			

			return View::make('searchtimesheet.index', $dataArr);

		} else {

			return Redirect::to('/search/timesheet');
			//return View::make('searchtimesheet.index', $dataArr);

		}

		//===============================================================================

	}	


	public function updatePreviousSearchTimesheet($id) {

		$data = Input::all();
		
		//echo '<pre>';
		//return dd($data);
		//echo '</pre>';

		$id = (int) Session::get('employeeSearchId');

		$recordCount = ( !empty($data["recordcount"]) && isset($data["recordcount"]) ) ? (int) $data["recordcount"] : 0;

		Session::put('recordCount', $recordCount);
		$recordCount = Session::get('recordCount');

		$in1 = ( !empty($data["timesheetrowin1"]) && isset($data["timesheetrowin1"]) ) ? $data["timesheetrowin1"] : array();
		$in2 = ( !empty($data["timesheetrowin2"]) && isset($data["timesheetrowin2"]) ) ? $data["timesheetrowin2"] : array();
		$in3 = ( !empty($data["timesheetrowin3"]) && isset($data["timesheetrowin3"]) ) ? $data["timesheetrowin3"] : array();

		$out1 = ( !empty($data["timesheetrowout1"]) && isset($data["timesheetrowout1"]) ) ? $data["timesheetrowout1"] : array();
		$out2 = ( !empty($data["timesheetrowout2"]) && isset($data["timesheetrowout2"]) ) ? $data["timesheetrowout2"] : array();
		$out3	= ( !empty($data["timesheetrowout3"]) && isset($data["timesheetrowout3"]) ) ? $data["timesheetrowout3"] : array();
		
		//echo '<pre>';
		//return dd($out1);
		//echo '</pre>';

		//echo '<pre>';
		//return dd(array_merge($in1, $out1, $in2, $out2, $in3, $out3));
		//echo '</pre>';

		$previousSearchTimesheet = array_merge($in1, $out1, $in2, $out2, $in3, $out3);

		//Session::put('searchTimesheets', array_merge($in1, $out1, $in2, $out2, $in3, $out3));

		//$previousSearchTimesheet = Session::get('searchTimesheets');

		//return dd(count($previousSearchTimesheet));

		for( $i = 0; $i < count($previousSearchTimesheet); $i++ ) {

			echo '<pre>';
			//return dd($previousSearchTimesheet);
			echo '</pre>';			
			for ( $j = 0; $j < $recordCount; $j++ ) {
			//for ( $j = 0; $j < 14; $j++ ) {

				//$previousSearchTimesheet["in1"][$j];

				///$timesheetId = $previousSearchTimesheet["timesheetid"][$j];
				///$dayDate = $previousSearchTimesheet["daydate"][$j];
				//$time = $previousSearchTimesheet[$clocking][$j];
				
				Session::put('timesheetId', $previousSearchTimesheet["timesheetid"][$j]);
				Session::put('dayDate', $previousSearchTimesheet["daydate"][$j]);

				$timesheetId = Session::get('timesheetId');
				
				$dayDate = Session::get('dayDate');

				Session::put('timeIn1', $previousSearchTimesheet["in1"][$j]);
				Session::put('timeIn2', $previousSearchTimesheet["in2"][$j]);
				Session::put('timeIn3', $previousSearchTimesheet["in3"][$j]);
				Session::put('timeOut1', $previousSearchTimesheet["out1"][$j]);
				Session::put('timeOut2', $previousSearchTimesheet["out2"][$j]);
				Session::put('timeOut3', $previousSearchTimesheet["out3"][$j]);

				$timeIn1 = Session::get('timeIn1');
				$timeIn2 = Session::get('timeIn2');
				$timeIn3 = Session::get('timeIn3');
				$timeOut1 = Session::get('timeOut1');
				$timeOut2 = Session::get('timeOut2');
				$timeOut3 = Session::get('timeOut3');

				$employeeSetting = Employeesetting::where('employee_id', Session::get('employeeSearchId'))->first();

				$summary = Summary::where('employee_id', Session::get('employeeSearchId'))
								  ->where('daydate', $dayDate)->first();					

				$schedule = Schedule::where('employee_id', Session::get('employeeSearchId'))
									->where('schedule_date', $dayDate)->first();
				
				$holiday = Holiday::where('holiday_date', $dayDate)->first();

		        $timesheet = Timesheet::where('employee_id', Session::get('employeeSearchId'))
		                              ->where('daydate', $dayDate)
		                              ->first();

		        /*$overtime = Overtime::where('employee_id', Session::get('employeeSearchId'))
		        					->where('timesheet_id', $timesheetId)
		        					->first();*/

				//$isFlexible = $employeeSetting->is_flexible;
				$scheduleStartTime = $schedule->start_time;	
				$scheduleEndTime = $schedule->end_time;
				$isFlexible = $employeeSetting->is_flexible;
				$hasBreak = $employeeSetting->has_break;
				$breakTime = $employeeSetting->break_time; //date('G', timestamp($employeeSetting->break_time));
				$hoursPerDay = $employeeSetting->hours_per_day;
				$halfOfhoursPerDay = ($hoursPerDay / 2);

				/*$scheduleStartTime = (!empty($schedule->start_time)) ? $schedule->start_time : '';
				$scheduleEndTime = (!empty($schedule->end_time)) ? $schedule->start_time : '';
				$isFlexible = (!empty($employeeSetting->is_flexible)) ? $schedule->start_time : '';
				$hasBreak = (!empty($employeeSetting->has_break)) ? $schedule->start_time : '';
				$breakTime = (!empty($employeeSetting->break_time)) ? $schedule->start_time : ''; //date('G', timestamp($employeeSetting->break_time));
				$hoursPerDay = (!empty($employeeSetting->hours_per_day)) ? $schedule->start_time : '';
				$halfOfhoursPerDay = ($hoursPerDay / 2);*/


				if ( strtotime(date('H:i', strtotime($timesheet->schedule_in))) >
					 strtotime(date('H:i', strtotime($timesheet->schedule_out))) ) {

					 $checkSchedule = TRUE;

				} elseif ( strtotime(date('H:i', strtotime($timesheet->schedule_in))) < 
						   strtotime(date('H:i', strtotime($timesheet->schedule_out))) ) {

					$checkSchedule = FALSE;

				}


				// TIMESHEET DATETIME MANIPULATION

				if( !empty($timeIn1) && empty($timeOut1) ) {

					$scheduleInDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

				} elseif( !empty($timeIn1) && !empty($timeOut1) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00

						$scheduleInDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));	

					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						$scheduleInDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));	

						if ( strtotime(date('H:i', strtotime($timeIn1))) > 
							 strtotime(date('H:i', strtotime($timeOut1))) ) {

							$scheduleInDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));		

						} elseif ( strtotime(date('H:i', strtotime($timeIn1))) < 
							       strtotime(date('H:i', strtotime($timeOut1))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut1)) - date('G', strtotime($timeIn1));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleInDate1 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleInDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}										

						}

					}

				}


				if( !empty($timeIn2) && empty($timeOut2) ) {

					$scheduleInDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

				} elseif( !empty($timeIn2) && !empty($timeOut2) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00

						$scheduleInDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));	

					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						$scheduleInDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));	

						if ( strtotime(date('H:i', strtotime($timeIn2))) > 
							 strtotime(date('H:i', strtotime($timeOut2))) ) {

							$scheduleInDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));		

						} elseif ( strtotime(date('H:i', strtotime($timeIn2))) < 
							       strtotime(date('H:i', strtotime($timeOut2))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut2)) - date('G', strtotime($timeIn2));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleInDate2 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleInDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}										

						}

					}

				}					


				if( !empty($timeIn3) && empty($timeOut3) ) {

					$scheduleInDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));	

				} elseif( !empty($timeIn3) && !empty($timeOut3) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00

						$scheduleInDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));	

					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						$scheduleInDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));	

						if ( strtotime(date('H:i', strtotime($timeIn3))) > 
							 strtotime(date('H:i', strtotime($timeOut3))) ) {

							$scheduleInDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));		

						} elseif ( strtotime(date('H:i', strtotime($timeIn3))) < 
							       strtotime(date('H:i', strtotime($timeOut3))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut3)) - date('G', strtotime($timeIn3));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleInDate3 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleInDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}										

						}

					}

				}


				if( !empty($timeIn1) && empty($timeOut1) ) {

					$scheduleOutDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));		

					//$timesheet->clocking_status = 'clock_in_1';					
										
				} elseif( !empty($timeIn1) && !empty($timeOut1) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00
						
						if ( strtotime(date('H:i', strtotime($timeIn1))) > 
							 strtotime(date('H:i', strtotime($timeOut1))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut1)) - date('G', strtotime($timeIn1));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate1 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));
								//$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_out));

							} else {

								$scheduleOutDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn1))) < 
							       strtotime(date('H:i', strtotime($timeOut1))) ) {

							$scheduleOutDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}


					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						if ( strtotime(date('H:i', strtotime($timeIn1))) > 
							 strtotime(date('H:i', strtotime($timeOut1))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut1)) - date('G', strtotime($timeIn1));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate1 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleOutDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn1))) < 
							       strtotime(date('H:i', strtotime($timeOut1))) ) {

							$scheduleOutDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}							

					}

				}


				if( !empty($timeIn2) && empty($timeOut2) ) {

					$scheduleOutDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));	

					//$timesheet->clocking_status = 'clock_in_2';					
										
				} elseif( !empty($timeIn2) && !empty($timeOut2) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00
						
						if ( strtotime(date('H:i', strtotime($timeIn2))) > 
							 strtotime(date('H:i', strtotime($timeOut2))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut2)) - date('G', strtotime($timeIn2));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate2 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));
								//$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_out));

							} else {

								$scheduleOutDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn2))) < 
							       strtotime(date('H:i', strtotime($timeOut2))) ) {

							$scheduleOutDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}


					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						if ( strtotime(date('H:i', strtotime($timeIn2))) > 
							 strtotime(date('H:i', strtotime($timeOut2))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut2)) - date('G', strtotime($timeIn2));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate2 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleOutDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn2))) < 
							       strtotime(date('H:i', strtotime($timeOut2))) ) {

							$scheduleOutDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}							

					}

				}


				if( !empty($timeIn3) && empty($timeOut3) ) {

					$scheduleOutDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));	

					//$timesheet->clocking_status = 'clock_in_3';					
										
	
				} elseif( !empty($timeIn3) && !empty($timeOut3) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00
						
						if ( strtotime(date('H:i', strtotime($timeIn3))) > 
							 strtotime(date('H:i', strtotime($timeOut3))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut3)) - date('G', strtotime($timeIn3));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate3 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));
								//$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_out));

							} else {

								$scheduleOutDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn3))) < 
							       strtotime(date('H:i', strtotime($timeOut3))) ) {

							$scheduleOutDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}


					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						if ( strtotime(date('H:i', strtotime($timeIn3))) > 
							 strtotime(date('H:i', strtotime($timeOut3))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut3)) - date('G', strtotime($timeIn3));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate3 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleOutDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn3))) < 
							       strtotime(date('H:i', strtotime($timeOut3))) ) {

							$scheduleOutDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}							

					}

					//$timesheet->clocking_status = 'clock_out_3';													

				}				

				//===============================================================================================================	

				// Check if time and schedule date are empty or not

				if( !empty($timeIn1) && !empty($scheduleInDate1) ) {

					$time = date('H:i:s', strtotime($timeIn1));

					$dateTimeFormatIn1 = $scheduleInDate1.' '.$time;
					Session::put('dateTimeFormatIn1', $dateTimeFormatIn1);					

				} else {

					//$dateTimeFormatIn1 = '';
					$dateTimeFormatIn1 = $timesheet->time_in_1;
					Session::put('dateTimeFormatIn1', $dateTimeFormatIn1);

				}


				if( !empty($timeIn2) && !empty($scheduleInDate2) ) {

					$time = date('H:i:s', strtotime($timeIn2));

					$dateTimeFormatIn2 = $scheduleInDate2.' '.$time;
					Session::put('dateTimeFormatIn2', $dateTimeFormatIn2);

				} else {

					//$dateTimeFormatIn2 = '';
					$dateTimeFormatIn2 = $timesheet->time_in_2;
					Session::put('dateTimeFormatIn2', $dateTimeFormatIn2);

				}


				if( !empty($timeIn3) && !empty($scheduleInDate3) ) {

					$time = date('H:i:s', strtotime($timeIn3));

					$dateTimeFormatIn3 = $scheduleInDate3.' '.$time;
					Session::put('dateTimeFormatIn3', $dateTimeFormatIn3);

				} else {

					//$dateTimeFormatIn3 = '';
					$dateTimeFormatIn3 = $timesheet->time_in_3;
					Session::put('dateTimeFormatIn3', $dateTimeFormatIn3);

				}


				if( !empty($timeOut1) && !empty($scheduleOutDate1) ) {

					$time = date('H:i:s', strtotime($timeOut1));

					$dateTimeFormatOut1 = $scheduleOutDate1.' '.$time;
					Session::put('dateTimeFormatOut1', $dateTimeFormatOut1);

				} else {

					//$dateTimeFormatOut1 = '';
					$dateTimeFormatOut1 = $timesheet->time_out_1;
					Session::put('dateTimeFormatOut1', $dateTimeFormatOut1);

				}


				if( !empty($timeOut2) && !empty($scheduleOutDate2) ) {

					$time = date('H:i:s', strtotime($timeOut2));

					$dateTimeFormatOut2 = $scheduleOutDate2.' '.$time;
					Session::put('dateTimeFormatOut2', $dateTimeFormatOut2);

				} else {

					//$dateTimeFormatOut2 = '';
					$dateTimeFormatOut2 = $timesheet->time_out_2;
					Session::put('dateTimeFormatOut2', $dateTimeFormatOut2);

				}


				if( !empty($timeOut3) && !empty($scheduleOutDate3) ) {

					$time = date('H:i:s', strtotime($timeOut3));

					$dateTimeFormatOut3 = $scheduleOutDate3.' '.$time;
					Session::put('dateTimeFormatOut3', $dateTimeFormatOut3);

				} else {

					//$dateTimeFormatOut3 = '';
					$dateTimeFormatOut3 = $timesheet->time_out_3;					
					Session::put('dateTimeFormatOut3', $dateTimeFormatOut3);

				}																				


				//===============================================================================================================

				//TIMESHEET EDITING FILTERING AND VALIDATION

				//TIME IN 1 &&  TIME OUT 1	
				//TIME IN 2 &&  TIME OUT 2	
				//TIME IN 3 &&  TIME OUT 3		
				if ( ( empty($timeIn1) && empty($timeOut1) ) && 
				     ( empty($timeIn2) && empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

					    //RESET
						$timesheet->clocking_status = 'open';
						$timesheet->time_in_1 = '';
						$timesheet->time_out_1 = '';
						$timesheet->time_in_2 = '';
						$timesheet->time_out_2 = '';
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';
					
						//RESET TARDINESS	
						$timesheet->tardiness_1 = '';
						$summary->lates = '';

						if ($timesheet->save() ) {
							$summary->save();														
						}				

				}	

				if ( ( !empty($timeIn1) && empty($timeOut1) ) && 
				     ( empty($timeIn2) && empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

					    //COMPUTE TARDINESS
						//$timesheetTimeIn = $dateTimeFormatIn1;						
						$timesheet->clocking_status = 'clock_in_1';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = '';
						$timesheet->time_in_2 = '';
						$timesheet->time_out_2 = '';
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';							
						
						//CODE FOR TARDINESS
						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//WITH COMPUTATION: TRUE
							//GET TARDINESS
							$tardinessTime = tardinessTime($dateTimeFormatIn1, $scheduleStartTime);
							$timesheet->tardiness_1 = $tardinessTime;
							$summary->lates = $tardinessTime;

							if ($timesheet->save() ) {
								$summary->save();														
							}

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							//WITH COMPUTATION: FALSE							
							$timesheet->save();						
						
						}						

				}

				if ( ( !empty($timeIn1) && !empty($timeOut1) ) && 
				     ( empty($timeIn2) && empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

						$dateTimeFormatIn1 = Session::get('dateTimeFormatIn1');
						$dateTimeFormatOut1 = Session::get('dateTimeFormatOut1');

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'clock_out_1';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = $dateTimeFormatOut1;
						$timesheet->time_in_2 = '';
						$timesheet->time_out_2 = '';
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';


						//RESET 
						$timesheet->total_hours_2 = '';
						$timesheet->work_hours_2 = '';
						$timesheet->total_overtime_2 = '';
						$timesheet->undertime_2 = '';
						$timesheet->night_differential_2 = '';											
					
						
						//CODE FOR TARDINESS
						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//WITH COMPUTATION: TRUE
							//GET TARDINESS
							$tardinessTime = tardinessTime($dateTimeFormatIn1, $scheduleStartTime);
							$timesheet->tardiness_1 = $tardinessTime;
							$summary->lates = $tardinessTime;

							if ($timesheet->save() ) {
								$summary->save();														
							}

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							//WITH COMPUTATION: FALSE							
							$timesheet->save();						
						
						}

					//RECOMPUTE ABSENCES
					if( !empty($summary->absent) ) {
					
						$summary->absent = ($summary->absent - $hoursPerDay);
					}						


					//TIMESHEET 3.1 - EMPTY COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours3 = '';
									$workHours3 = '';
									$hasOvertime3 = FALSE;
									$overtimeHours3 = '';
									$underTimeHours3 = '';
									$hasNightDiff3 = FALSE;
									$nightDiff3 = '';

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;
									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;
									

									//GET OVERTIME													
									if(!$hasOvertime3) {

										$timesheet->total_overtime_3 = $overtimeHours3;
										

									} 
									
									//GET NIGHTDIFF;
									if(!$hasNightDiff3) {

										/*if ( $nightDiff3 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_3 = $nightDiff3;							
											
										}*/

											$timesheet->night_differential_3 = $nightDiff3;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->legal_holiday = $workHours3;

											}

											if(!$hasOvertime3 && !$hasNightDiff3) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->legal_holiday_overtime = $overtimeHours3;
													
												}												

											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->legal_holiday_night_diff = $nightDiff3;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->special_holiday = $workHours3;

											}

											if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;												

											}					

											if(!$hasOvertime3) { //ISOVERTIME: TRUE
												
												if( !empty($timesheet->total_overtime_1) ) {

													$summary->special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->special_holiday_overtime = $overtimeHours3;
													
												}													
												
											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->special_holiday_night_diff = $nightDiff3;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->regular = $timesheet->work_hours_1;

										} else {

											$summary->regular = $workHours3;

										}										

										if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE								

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
												$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->regular_overtime_night_diff = $overtimeNightdiff;																		

										}

										if ($hasOvertime3) { //ISOVERTIME: TRUE

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->regular_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->regular_overtime = $overtimeHours3;
												
											}											

										}				

										if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->regular_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->regular_night_differential = $nightDiff3;
												
											}												

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours3 = '';
									$workHours3 = '';
									$hasOvertime3 = '';
									$overtimeHours3 = '';
									$underTimeHours3 = '';
									$hasNightDiff3 = '';
									$nightDiff3 = '';									
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;
									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;
									

									//GET OVERTIME													
									if(!$hasOvertime3) {

										$timesheet->total_overtime_3 = $overtimeHours3;
										

									} 
									
									//GET NIGHTDIFF;
									if( $hasNightDiff3 ) {

										/*if ( $nightDiff3 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_3 = $nightDiff3;							
											
										}*/


										$timesheet->night_differential_3 = $nightDiff3;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_legal_holiday = $workHours3;

											}											

											if(!$hasOvertime3 && !$hasNightDiff3) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;																	

											}

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_legal_holiday_overtime = $overtimeHours3;
													
												}														

											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_legal_holiday_night_diff = $nightDiff3;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_special_holiday = $workHours3;

											}											

											if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}					

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_special_holiday_overtime = $overtimeHours3;
													
												}													
												
											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff3;

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_special_holiday_night_diff = $nightDiff3;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->rest_day = $timesheet->work_hours_1;

										} else {

											$summary->rest_day = $workHours3;

										}										

										if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
												$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->rest_day_overtime_night_diff = $overtimeNightdiff;																		

										}

										if ($hasOvertime3) { //ISOVERTIME: TRUE

											$summary->rest_day_overtime = $overtimeHours3;

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->rest_day_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->rest_day_overtime = $overtimeHours3;
												
											}											

										}				

										if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->rest_day_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->rest_day_night_differential = $nightDiff3;
												
											}											

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_3 = totalHoursWithOvertime($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_3 = workHours($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_2 = underTimeHours($dateTimeFormatOut3, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff3 = nightDiff($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================							



					//TIMESHEET 2.1 - EMPTY COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours2 = '';
									$workHours2 = '';
									$hasOvertime2 = FALSE;
									$overtimeHours2 = '';
									$underTimeHours2 = '';
									$hasNightDiff2 = FALSE;
									$nightDiff2 = '';

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;
									

									//GET OVERTIME													
									if(!$hasOvertime2) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										

									} 
									
									//GET NIGHTDIFF;
									if( !$hasNightDiff2 ) {

										/*if ( $nightDiff2 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_2 = $nightDiff2;							
											
										}*/

											$timesheet->night_differential_2 = $nightDiff2;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->legal_holiday = $workHours2;

											}

											if(!$hasOvertime2 && !$hasNightDiff2) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
													$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}

											if(!$hasOvertime2) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->legal_holiday_overtime = $overtimeHours2;
													
												}												

											}

											if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->legal_holiday_night_diff = $nightDiff2;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->special_holiday = $workHours2;

											}

											if(!$hasOvertime2 && !$hasNightDiff2) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
													$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;												

											}					

											if(!$hasOvertime2) { //ISOVERTIME: TRUE
												
												if( !empty($timesheet->total_overtime_1) ) {

													$summary->special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->special_holiday_overtime = $overtimeHours2;
													
												}													
												
											}

											if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->special_holiday_night_diff = $nightDiff2;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->regular = $timesheet->work_hours_1;

										} else {

											$summary->regular = $workHours2;

										}										

										if(!$hasOvertime2 && !$hasNightDiff2) { //ISOVERTIME AND HASNIGHTDIFF: TRUE								

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
												$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->regular_overtime_night_diff = $overtimeNightdiff;																		

										}

										if (!$hasOvertime2) { //ISOVERTIME: TRUE

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->regular_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->regular_overtime = $overtimeHours2;
												
											}											

										}				

										if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->regular_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->regular_night_differential = $nightDiff2;
												
											}												

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours2 = '';
									$workHours2 = '';
									$hasOvertime2 = '';
									$overtimeHours2 = '';
									$underTimeHours2 = '';
									$hasNightDiff2 = '';
									$nightDiff2 = '';									
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;
									

									//GET OVERTIME													
									if($hasOvertime2) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										

									} 
									
									//GET NIGHTDIFF;
									if( $hasNightDiff2 ) {

										/*if ( $nightDiff2 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_2 = $nightDiff2;							
											
										}*/

										$timesheet->night_differential_2 = $nightDiff2;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_legal_holiday = $workHours2;

											}											

											if(!$hasOvertime2 && !$hasNightDiff2) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
													$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;																	

											}

											if($hasOvertime2) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_legal_holiday_overtime = $overtimeHours2;
													
												}														

											}

											if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_legal_holiday_night_diff = $nightDiff2;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_special_holiday = $workHours2;

											}											

											if(!$hasOvertime2 && !$hasNightDiff2) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
													$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}					

											if($hasOvertime2) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_special_holiday_overtime = $overtimeHours2;
													
												}													
												
											}

											if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff2;

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_special_holiday_night_diff = $nightDiff2;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->rest_day = $timesheet->work_hours_1;

										} else {

											$summary->rest_day = $workHours2;

										}										

										if(!$hasOvertime2 && !$hasNightDiff2) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
												$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->rest_day_overtime_night_diff = $overtimeNightdiff;																		

										}

										if (!$hasOvertime2) { //ISOVERTIME: TRUE

											$summary->rest_day_overtime = $overtimeHours2;

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->rest_day_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->rest_day_overtime = $overtimeHours2;
												
											}											

										}				

										if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->rest_day_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->rest_day_night_differential = $nightDiff2;
												
											}											

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_2 = totalHoursWithOvertime($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_2 = workHours($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_2 = underTimeHours($dateTimeFormatOut2, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff2 = nightDiff($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================		



					//TIMESHEET 1.1 - WITH COMPUTATION =========================================================================================================


						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {

									//$timesheet->clocking_status = "clock_out_1";
									$timesheet->time_out_1 = $dateTimeFormatOut1;	
									
									//WITH COMPLETE COMPUTATION: TRUE									 
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());										

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}

										}

									}


									//GET OVERTIME													
									if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';
										
										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										

									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
									
		 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->legal_holiday = $workHours1;										

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->legal_holiday_night_diff = '';

											}												

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day


										"HOLIDAY: FALSE - Regular Day \n";

										$summary->regular = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->regular_overtime = $overtimeHours1;

											} else {

												$summary->regular_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->regular_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->regular_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->regular_night_differential = $nightDiff1;

										} else {

											$summary->regular_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									

									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}
											
										}

									}																	

									//GET OVERTIME													
									if($hasOvertime1 && empty($timesheet->tardiness_1)) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										
									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->rest_day_legal_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_legal_holiday_night_diff = '';

											}

										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->rest_day_special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										$summary->rest_day = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->rest_day_overtime = $overtimeHours1;

											} else {

												$summary->rest_day_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->rest_day_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->rest_day_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->rest_day_night_differential = $nightDiff1;

										} else {

											$summary->rest_day_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}															


								}								

							}
												

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_1 = totalHoursWithOvertime($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_1 = workHours($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_1 = underTimeHours($dateTimeFormatOut1, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff1 = nightDiff($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}	

					
					//=========================================================================================================

				}	


				if ( ( !empty($timeIn1) && !empty($timeOut1) ) && 
				     ( !empty($timeIn2) && empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'clock_in_2';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = $dateTimeFormatOut1;
						$timesheet->time_in_2 = $dateTimeFormatIn2;
						$timesheet->time_out_2 = '';
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';
						$timesheet->save();						

				}	


				if ( ( !empty($timeIn1) && !empty($timeOut1) ) && 
				     ( !empty($timeIn2) && !empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'clock_out_2';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = $dateTimeFormatOut1;
						$timesheet->time_in_2 = $dateTimeFormatIn2;
						$timesheet->time_out_2 = $dateTimeFormatOut2;
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';
						//$timesheet->save();

						//CODE FOR TARDINESS
						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//WITH COMPUTATION: TRUE
							//GET TARDINESS
							$tardinessTime = tardinessTime($dateTimeFormatIn1, $scheduleStartTime);
							$timesheet->tardiness_1 = $tardinessTime;
							$summary->lates = $tardinessTime;

							if ($timesheet->save() ) {
								$summary->save();														
							}

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							//WITH COMPUTATION: FALSE							
							$timesheet->save();						
						
						}	

					//RECOMPUTE ABSENCES
					if( !empty($summary->absent) ) {
					
						$summary->absent = ($summary->absent - $hoursPerDay);
					}												

					//TIMESHEET 3.2 - EMPTY COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours3 = '';
									$workHours3 = '';
									$hasOvertime3 = FALSE;
									$overtimeHours3 = '';
									$underTimeHours3 = '';
									$hasNightDiff3 = FALSE;
									$nightDiff3 = '';

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;
									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;
									

									//GET OVERTIME													
									if(!$hasOvertime3) {

										$timesheet->total_overtime_3 = $overtimeHours3;
										

									} 
									
									//GET NIGHTDIFF;
									if(!$hasNightDiff3) {

										/*if ( $nightDiff3 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_3 = $nightDiff3;							
											
										}*/

											$timesheet->night_differential_3 = $nightDiff3;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->legal_holiday = $workHours3;

											}

											if(!$hasOvertime3 && !$hasNightDiff3) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->legal_holiday_overtime = $overtimeHours3;
													
												}												

											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->legal_holiday_night_diff = $nightDiff3;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->special_holiday = $workHours3;

											}

											if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;												

											}					

											if(!$hasOvertime3) { //ISOVERTIME: TRUE
												
												if( !empty($timesheet->total_overtime_1) ) {

													$summary->special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->special_holiday_overtime = $overtimeHours3;
													
												}													
												
											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->special_holiday_night_diff = $nightDiff3;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->regular = $timesheet->work_hours_1;

										} else {

											$summary->regular = $workHours3;

										}										

										if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE								

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
												$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->regular_overtime_night_diff = $overtimeNightdiff;																		

										}

										if ($hasOvertime3) { //ISOVERTIME: TRUE

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->regular_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->regular_overtime = $overtimeHours3;
												
											}											

										}				

										if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->regular_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->regular_night_differential = $nightDiff3;
												
											}												

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours3 = '';
									$workHours3 = '';
									$hasOvertime3 = '';
									$overtimeHours3 = '';
									$underTimeHours3 = '';
									$hasNightDiff3 = '';
									$nightDiff3 = '';									
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;
									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;
									

									//GET OVERTIME													
									if(!$hasOvertime3) {

										$timesheet->total_overtime_3 = $overtimeHours3;
										

									} 
									
									//GET NIGHTDIFF;
									if( $hasNightDiff3 ) {

										/*if ( $nightDiff3 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_3 = $nightDiff3;							
											
										}*/


										$timesheet->night_differential_3 = $nightDiff3;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_legal_holiday = $workHours3;

											}											

											if(!$hasOvertime3 && !$hasNightDiff3) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;																	

											}

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_legal_holiday_overtime = $overtimeHours3;
													
												}														

											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_legal_holiday_night_diff = $nightDiff3;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_special_holiday = $workHours3;

											}											

											if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}					

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_special_holiday_overtime = $overtimeHours3;
													
												}													
												
											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff3;

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_special_holiday_night_diff = $nightDiff3;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->rest_day = $timesheet->work_hours_1;

										} else {

											$summary->rest_day = $workHours3;

										}										

										if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
												$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->rest_day_overtime_night_diff = $overtimeNightdiff;																		

										}

										if ($hasOvertime3) { //ISOVERTIME: TRUE

											$summary->rest_day_overtime = $overtimeHours3;

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->rest_day_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->rest_day_overtime = $overtimeHours3;
												
											}											

										}				

										if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->rest_day_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->rest_day_night_differential = $nightDiff3;
												
											}											

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_3 = totalHoursWithOvertime($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_3 = workHours($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_2 = underTimeHours($dateTimeFormatOut3, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff3 = nightDiff($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================


					//TIMESHEET 1.2 - WITH COMPUTATION =========================================================================================================


						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {

									//$timesheet->clocking_status = "clock_out_1";
									$timesheet->time_out_1 = $dateTimeFormatOut1;	
									
									//WITH COMPLETE COMPUTATION: TRUE									 
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());										

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}

										}

									}


									//GET OVERTIME													
									if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';
										
										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										

									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
									
		 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->legal_holiday = $workHours1;										

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->legal_holiday_night_diff = '';

											}												

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day


										"HOLIDAY: FALSE - Regular Day \n";

										$summary->regular = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->regular_overtime = $overtimeHours1;

											} else {

												$summary->regular_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->regular_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->regular_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->regular_night_differential = $nightDiff1;

										} else {

											$summary->regular_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									

									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}
											
										}

									}																	

									//GET OVERTIME													
									if($hasOvertime1 && empty($timesheet->tardiness_1)) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										
									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->rest_day_legal_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_legal_holiday_night_diff = '';

											}

										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->rest_day_special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										$summary->rest_day = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->rest_day_overtime = $overtimeHours1;

											} else {

												$summary->rest_day_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->rest_day_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->rest_day_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->rest_day_night_differential = $nightDiff1;

										} else {

											$summary->rest_day_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}															


								}								

							}
												

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_1 = totalHoursWithOvertime($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_1 = workHours($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_1 = underTimeHours($dateTimeFormatOut1, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff1 = nightDiff($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}	

					
					//=========================================================================================================	


					//TIMESHEET 2.2 - WITH COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours2 = totalHours($dateTimeFormatIn2, $dateTimeFormatOut2);
									$workHours2 = $workHours;
									$hasOvertime2 = TRUE;
									$overtimeHours2 = $workHours;
									//$underTimeHours2 = $underTimeHours;
									//$hasOvertimeNightDiff2 = $hasNightDiff;
									//$nightDiff2 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;

									//GET NIGHTDIFF;
									$hasOvertimeNightDiff2 = FALSE;

									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 2
										$overtimeNightDiff2 = nightDifferential($dateTimeFormatIn2, $dateTimeFormatOut2, nightDifferentialRange());

										if( $overtimeNightDiff2 !== FALSE ) {

											$hasOvertimeNightDiff2 = TRUE;
											$timesheet->night_differential_2 = $overtimeNightDiff2;

										} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

										}

									}

									//GET OVERTIME													
									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										

									} else {

										$timesheet->total_overtime_2 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_2 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 2)								        
								        					 ->update(array('overtime_status' => NULL));										


									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->legal_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->legal_holiday = $workHours2;

											}
											
											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime = '';

													}

												} else {

													$summary->legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->legal_holiday_overtime_night_diff = '';
													
												}																									

											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->special_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->special_holiday = $workHours2;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime = '';

													}

												} else {

													$summary->special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->special_holiday_overtime_night_diff = '';
													
												}																									

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->regular = ($workHours2 + $timesheet->work_hours_1);

										} else {

											$summary->regular = $workHours2;

										}										
										
										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_2 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime = number_format(($overtimeHours2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime = '';

												}

											} else {

												$summary->regular_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime_night_diff = '';

												}

											} else {

												$summary->regular_overtime_night_diff = '';
												
											}																									

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours2 = totalHours($dateTimeFormatIn2, $dateTimeFormatOut2);
									$workHours2 = $workHours;
									$hasOvertime2 = TRUE;
									$overtimeHours2 = $workHours;
									//$underTimeHours2 = $underTimeHours;
									//$hasOvertimeNightDiff2 = $hasNightDiff;
									//$nightDiff2 = $nightDiff;							
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;

									//GET NIGHTDIFF;
									$hasOvertimeNightDiff2 = FALSE;

									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 2
										$overtimeNightDiff2 = nightDifferential($dateTimeFormatIn2, $dateTimeFormatOut2, nightDifferentialRange());

										if( $overtimeNightDiff2 !== FALSE ) {

											$hasOvertimeNightDiff2 = TRUE;
											$timesheet->night_differential_2 = $overtimeNightDiff2;

										} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

										}

									}

									//GET OVERTIME																						
									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										
									} else {

										$timesheet->total_overtime_2 = '';


										//RESET OVERTIME STATUS
										$timesheet->overtime_status_2 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 2)								        
								        					 ->update(array('overtime_status' => NULL));										


									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Rest Day Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_legal_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->rest_day_legal_holiday = $workHours2;

											}
											
											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime = '';

													}

												} else {

													$summary->rest_day_legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->rest_day_legal_holiday_overtime_night_diff = '';
													
												}																									

											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Rest Day Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_special_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->rest_day_special_holiday = $workHours2;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime = '';

													}

												} else {

													$summary->rest_day_special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->rest_day_special_holiday_overtime_night_diff = '';
													
												}																									

											}

										}

									} else { //HOLIDAY: FALSE - Rest Day

										"HOLIDAY: FALSE - Rest Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->rest_day = ($workHours2 + $timesheet->work_hours_1);

										} else {

											$summary->rest_day = $workHours2;

										}										
										
										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_2 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime = number_format(($overtimeHours2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime = '';

												}

											} else {

												$summary->rest_day_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime_night_diff = '';

												}

											} else {

												$summary->rest_day_overtime_night_diff = '';
												
											}																									

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_2 = totalHoursWithOvertime($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_2 = workHours($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_2 = underTimeHours($dateTimeFormatOut2, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff2 = nightDiff($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================					


				}


				if ( ( !empty($timeIn1) && !empty($timeOut1) ) && 
				     ( !empty($timeIn2) && !empty($timeOut2) ) &&
				     ( !empty($timeIn3) && empty($timeOut3) )  ) {

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'clock_in_3';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = $dateTimeFormatOut1;
						$timesheet->time_in_2 = $dateTimeFormatIn2;
						$timesheet->time_out_2 = $dateTimeFormatOut2;
						$timesheet->time_in_3 = $dateTimeFormatIn3;
						$timesheet->time_out_3 = '';
						//$timesheet->save();							

				}	

				if ( ( !empty($timeIn1) && !empty($timeOut1) ) && 
				     ( !empty($timeIn2) && !empty($timeOut2) ) &&
				     ( !empty($timeIn3) && !empty($timeOut3) )  ) {

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'clock_out_3';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = $dateTimeFormatOut1;
						$timesheet->time_in_2 = $dateTimeFormatIn2;
						$timesheet->time_out_2 = $dateTimeFormatOut2;
						$timesheet->time_in_3 = $dateTimeFormatIn3;
						$timesheet->time_out_3 = $dateTimeFormatOut3;
						//$timesheet->save();

						//CODE FOR TARDINESS
						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//WITH COMPUTATION: TRUE
							//GET TARDINESS
							$tardinessTime = tardinessTime($dateTimeFormatIn1, $scheduleStartTime);
							$timesheet->tardiness_1 = $tardinessTime;
							$summary->lates = $tardinessTime;

							if ($timesheet->save() ) {
								$summary->save();														
							}

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							//WITH COMPUTATION: FALSE							
							$timesheet->save();						
						
						}


					//RECOMPUTE ABSENCES
					if( !empty($summary->absent) ) {
					
						$summary->absent = ($summary->absent - $hoursPerDay);
					}												


					//TIMESHEET 1.3 - WITH COMPUTATION =========================================================================================================


						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {

									//$timesheet->clocking_status = "clock_out_1";
									$timesheet->time_out_1 = $dateTimeFormatOut1;	
									
									//WITH COMPLETE COMPUTATION: TRUE									 
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());										

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}

										}

									}


									//GET OVERTIME													
									if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';
										
										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										

									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
									
		 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->legal_holiday = $workHours1;										

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->legal_holiday_night_diff = '';

											}												

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day


										"HOLIDAY: FALSE - Regular Day \n";

										$summary->regular = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->regular_overtime = $overtimeHours1;

											} else {

												$summary->regular_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->regular_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->regular_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->regular_night_differential = $nightDiff1;

										} else {

											$summary->regular_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									

									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}
											
										}

									}																	

									//GET OVERTIME													
									if($hasOvertime1 && empty($timesheet->tardiness_1)) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										
									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->rest_day_legal_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_legal_holiday_night_diff = '';

											}

										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->rest_day_special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										$summary->rest_day = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->rest_day_overtime = $overtimeHours1;

											} else {

												$summary->rest_day_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->rest_day_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->rest_day_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->rest_day_night_differential = $nightDiff1;

										} else {

											$summary->rest_day_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}															


								}								

							}
												

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_1 = totalHoursWithOvertime($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_1 = workHours($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_1 = underTimeHours($dateTimeFormatOut1, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff1 = nightDiff($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}	

					
					//=========================================================================================================	


					//TIMESHEET 2.3 - WITH COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours2 = totalHours($dateTimeFormatIn2, $dateTimeFormatOut2);
									$workHours2 = $workHours;
									$hasOvertime2 = TRUE;
									$overtimeHours2 = $workHours;
									//$underTimeHours2 = $underTimeHours;
									//$hasOvertimeNightDiff2 = $hasNightDiff;
									//$nightDiff2 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;

									//GET NIGHTDIFF;
									$hasOvertimeNightDiff2 = FALSE;

									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 2
										$overtimeNightDiff2 = nightDifferential($dateTimeFormatIn2, $dateTimeFormatOut2, nightDifferentialRange());

										if( $overtimeNightDiff2 !== FALSE ) {

											$hasOvertimeNightDiff2 = TRUE;
											$timesheet->night_differential_2 = $overtimeNightDiff2;

										} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

										}

									}

									//GET OVERTIME													
									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										

									} else {

										$timesheet->total_overtime_2 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_2 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 2)								        
								        					 ->update(array('overtime_status' => NULL));										


									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->legal_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->legal_holiday = $workHours2;

											}
											
											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime = '';

													}

												} else {

													$summary->legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->legal_holiday_overtime_night_diff = '';
													
												}																									

											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->special_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->special_holiday = $workHours2;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime = '';

													}

												} else {

													$summary->special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->special_holiday_overtime_night_diff = '';
													
												}																									

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->regular = ($workHours2 + $timesheet->work_hours_1);

										} else {

											$summary->regular = $workHours2;

										}										
										
										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_2 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime = number_format(($overtimeHours2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime = '';

												}

											} else {

												$summary->regular_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime_night_diff = '';

												}

											} else {

												$summary->regular_overtime_night_diff = '';
												
											}																									

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours2 = totalHours($dateTimeFormatIn2, $dateTimeFormatOut2);
									$workHours2 = $workHours;
									$hasOvertime2 = TRUE;
									$overtimeHours2 = $workHours;
									//$underTimeHours2 = $underTimeHours;
									//$hasOvertimeNightDiff2 = $hasNightDiff;
									//$nightDiff2 = $nightDiff;							
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;

									//GET NIGHTDIFF;
									$hasOvertimeNightDiff2 = FALSE;

									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 2
										$overtimeNightDiff2 = nightDifferential($dateTimeFormatIn2, $dateTimeFormatOut2, nightDifferentialRange());

										if( $overtimeNightDiff2 !== FALSE ) {

											$hasOvertimeNightDiff2 = TRUE;
											$timesheet->night_differential_2 = $overtimeNightDiff2;

										} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

										}

									}

									//GET OVERTIME																						
									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										
									} else {

										$timesheet->total_overtime_2 = '';


										//RESET OVERTIME STATUS
										$timesheet->overtime_status_2 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 2)								        
								        					 ->update(array('overtime_status' => NULL));										


									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Rest Day Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_legal_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->rest_day_legal_holiday = $workHours2;

											}
											
											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime = '';

													}

												} else {

													$summary->rest_day_legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->rest_day_legal_holiday_overtime_night_diff = '';
													
												}																									

											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Rest Day Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_special_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->rest_day_special_holiday = $workHours2;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime = '';

													}

												} else {

													$summary->rest_day_special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->rest_day_special_holiday_overtime_night_diff = '';
													
												}																									

											}

										}

									} else { //HOLIDAY: FALSE - Rest Day

										"HOLIDAY: FALSE - Rest Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->rest_day = ($workHours2 + $timesheet->work_hours_1);

										} else {

											$summary->rest_day = $workHours2;

										}										
										
										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_2 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime = number_format(($overtimeHours2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime = '';

												}

											} else {

												$summary->rest_day_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime_night_diff = '';

												}

											} else {

												$summary->rest_day_overtime_night_diff = '';
												
											}																									

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_2 = totalHoursWithOvertime($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_2 = workHours($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_2 = underTimeHours($dateTimeFormatOut2, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff2 = nightDiff($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================		


					//TIMESHEET 3.3 - WITH COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours3 = totalHours($dateTimeFormatIn3, $dateTimeFormatOut3);									
									$workHours3 = $workHours;
									$hasOvertime3 = TRUE;
									$overtimeHours3 = $workHours;
									//$underTimeHours3 = $underTimeHours;
									//$hasOvertimeNightDiff3 = $hasNightDiff;
									//$nightDiff3 = $nightDiff;


									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;

									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;


									//GET NIGHTDIFF;
									$hasOvertimeNightDiff3 = FALSE;
									
									if( $hasOvertime3 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 3
										$overtimeNightDiff3 = nightDifferential($dateTimeFormatIn3, $dateTimeFormatOut3, nightDifferentialRange());

										if( $overtimeNightDiff3 !== FALSE ) {

											$hasOvertimeNightDiff3 = TRUE;
											$timesheet->night_differential_3 = $overtimeNightDiff3;

										} else {

											$hasOvertimeNightDiff3 = FALSE;
											$timesheet->night_differential_3 = '';

										}
										
									}									

									//GET OVERTIME													
									if( $hasOvertime3 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_3 = $overtimeHours3;										

									} else {

										$timesheet->total_overtime_3 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_3= NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 3)								        
								        					 ->update(array('overtime_status' => NULL));										

									}									
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

												$summary->legal_holiday = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

											} else {

												$summary->legal_holiday = $workHours3;

											}
											if( $timesheet->overtime_status_3 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_2), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->legal_holiday_overtime = '';

													}

												} else {

													$summary->legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->legal_holiday_overtime_night_diff = '';													
												} 


											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

												$summary->special_holiday = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

											} else {

												$summary->special_holiday = $workHours3;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_3 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_2), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->special_holiday_overtime = '';

													}

												} else {

													$summary->special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->special_holiday_overtime_night_diff = '';													
												} 


											}																					

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

											$summary->regular = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

										} else {

											$summary->regular = $workHours3;

										}										

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_3 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_1), 2);									

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime = number_format(($overtimeHours3), 2);												

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_2), 2);															

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

													$summary->regular_overtime = '';

												}

											} else {

												$summary->regular_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

													$summary->regular_overtime_night_diff = '';

												}

											} else {

												$summary->regular_overtime_night_diff = '';													
											} 


										}																			

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours3 = $totalHours;
									$workHours3 = $workHours;
									$hasOvertime3 = '';
									$overtimeHours3 = $overtimeHours;
									//$underTimeHours3 = $underTimeHours;
									//$hasOvertimeNightDiff3 = '';
									//$nightDiff3 = $nightDiff;									
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;
									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;


									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff3 = TRUE;

									} else {

										$scheduleHasNightDiff3 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasOvertimeNightDiff3 = FALSE;
									
									if( $hasOvertime3 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 3
										$overtimeNightDiff3 = nightDifferential($dateTimeFormatIn3, $dateTimeFormatOut3, nightDifferentialRange());

										if( $overtimeNightDiff3 !== FALSE ) {

											$hasOvertimeNightDiff3 = TRUE;
											$timesheet->night_differential_3 = $overtimeNightDiff3;

										} else {

											$hasOvertimeNightDiff3 = FALSE;
											$timesheet->night_differential_3 = '';

										}
										
									}									
									

									//GET OVERTIME													
									if( $hasOvertime3 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_3 = $overtimeHours3;
										
									} else {

										$timesheet->total_overtime_3 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_3 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 3)								        
								        					 ->update(array('overtime_status' => NULL));										

									}
									
									//GET NIGHTDIFF;
									/*if( $hasOvertimeNightDiff3 ) {

										if ( $nightDiff3 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_3 = $nightDiff3;							
											
										}

									} else {

										$timesheet->night_differential_3 = '';							

									}*/
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

												$summary->legal_holiday = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

											} else {

												$summary->legal_holiday = $workHours3;

											}
											if( $timesheet->overtime_status_3 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_2), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->legal_holiday_overtime = '';

													}

												} else {

													$summary->legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->legal_holiday_overtime_night_diff = '';													
												} 


											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

												$summary->special_holiday = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

											} else {

												$summary->special_holiday = $workHours3;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_3 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_2), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->special_holiday_overtime = '';

													}

												} else {

													$summary->special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->special_holiday_overtime_night_diff = '';													
												} 


											}																					

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

											$summary->regular = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

										} else {

											$summary->regular = $workHours3;

										}										

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_3 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_1), 2);									

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime = number_format(($overtimeHours3), 2);												

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_2), 2);															

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

													$summary->rest_day_overtime = '';

												}

											} else {

												$summary->rest_day_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

													$summary->rest_day_overtime_night_diff = '';

												}

											} else {

												$summary->rest_day_overtime_night_diff = '';													
											} 


										}																			

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_3 = totalHoursWithOvertime($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_3 = workHours($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_3 = underTimeHours($dateTimeFormatOut3, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff3 = nightDiff($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================


				}



				//EMPTY=========================================================================================================													
				
				if ( ( empty($timeIn1) && empty($timeOut1) ) && 
				     ( empty($timeIn2) && empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'open';
						$timesheet->time_in_1 = '';
						$timesheet->time_out_1 = '';
						$timesheet->time_in_2 = '';
						$timesheet->time_out_2 = '';
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';

						//RESET TARDINESS/LATES
						$timesheet->tardiness_1 = '';
						$summary->lates = '';

						//RESET TIMESHEET AND SUMMARY DATA
						$timesheet->total_hours_1 = '';
						$timesheet->work_hours_1 = '';
						$timesheet->total_overtime_1 = '';
						$timesheet->undertime_1 = '';
						$timesheet->night_differential_1 = '';

						$timesheet->total_hours_2 = '';
						$timesheet->work_hours_2 = '';
						$timesheet->total_overtime_2 = '';
						$timesheet->undertime_2 = '';
						$timesheet->night_differential_2 = '';						

						$timesheet->total_hours_3 = '';
						$timesheet->work_hours_3 = '';
						$timesheet->total_overtime_3 = '';
						$timesheet->undertime_3 = '';
						$timesheet->night_differential_3 = '';												

						$summary->undertime = '';
						$summary->legal_holiday = '';
						$summary->legal_holiday_overtime_night_diff = '';
						$summary->legal_holiday_overtime = '';
						$summary->legal_holiday_night_diff = '';
						$summary->special_holiday = '';
						$summary->special_holiday_overtime_night_diff = '';
						$summary->special_holiday_overtime = '';
						$summary->special_holiday_night_diff = '';		
						$summary->regular = '';									
						$summary->regular_overtime_night_diff = '';						
						$summary->regular_overtime = '';
						$summary->regular_night_differential = '';
						$summary->rest_day_legal_holiday = '';
						$summary->rest_day_legal_holiday_overtime_night_diff = '';						
						$summary->rest_day_legal_holiday_overtime = '';
						$summary->rest_day_legal_holiday_night_diff = '';
						$summary->rest_day_special_holiday = '';
						$summary->rest_day_special_holiday_overtime_night_diff = '';				
						$summary->rest_day_special_holiday_overtime = '';
						$summary->rest_day_special_holiday_night_diff = '';
						$summary->rest_day_overtime_night_diff = '';						
						$summary->rest_day_overtime = '';
						$summary->rest_day_night_differential = '';							

						if ($timesheet->save() ) {
							$summary->save();														
						}

				}					



			}
		
		}

		return Redirect::to('/previous/timesheet/'.$id);

	}


	public function updateSearchTimesheet($id) {

		$data = Input::all();
		
		//echo '<pre>';
		//return dd($data);
		//echo '</pre>';

		$id = (int) Session::get('employeeSearchId');

		$recordCount = ( !empty($data["recordcount"]) && isset($data["recordcount"]) ) ? (int) $data["recordcount"] : 0;

		Session::put('recordCount', $recordCount);
		$recordCount = Session::get('recordCount');

		$in1 = ( !empty($data["timesheetrowin1"]) && isset($data["timesheetrowin1"]) ) ? $data["timesheetrowin1"] : array();
		$in2 = ( !empty($data["timesheetrowin2"]) && isset($data["timesheetrowin2"]) ) ? $data["timesheetrowin2"] : array();
		$in3 = ( !empty($data["timesheetrowin3"]) && isset($data["timesheetrowin3"]) ) ? $data["timesheetrowin3"] : array();

		$out1 = ( !empty($data["timesheetrowout1"]) && isset($data["timesheetrowout1"]) ) ? $data["timesheetrowout1"] : array();
		$out2 = ( !empty($data["timesheetrowout2"]) && isset($data["timesheetrowout2"]) ) ? $data["timesheetrowout2"] : array();
		$out3	= ( !empty($data["timesheetrowout3"]) && isset($data["timesheetrowout3"]) ) ? $data["timesheetrowout3"] : array();
		
		//echo '<pre>';
		//return dd($out1);
		//echo '</pre>';

		//echo '<pre>';
		//return dd(array_merge($in1, $out1, $in2, $out2, $in3, $out3));
		//echo '</pre>';

		$searchTimesheets = array_merge($in1, $out1, $in2, $out2, $in3, $out3);

		//Session::put('searchTimesheets', array_merge($in1, $out1, $in2, $out2, $in3, $out3));

		//$searchTimesheets = Session::get('searchTimesheets');

		//return dd(count($searchTimesheets));

		for( $i = 0; $i < count($searchTimesheets); $i++ ) {

			echo '<pre>';
			//return dd($searchTimesheets);
			echo '</pre>';			
			for ( $j = 0; $j < $recordCount; $j++ ) {
			//for ( $j = 0; $j < 14; $j++ ) {

				//$searchTimesheets["in1"][$j];

				///$timesheetId = $searchTimesheets["timesheetid"][$j];
				///$dayDate = $searchTimesheets["daydate"][$j];
				//$time = $searchTimesheets[$clocking][$j];
				
				Session::put('timesheetId', $searchTimesheets["timesheetid"][$j]);
				Session::put('dayDate', $searchTimesheets["daydate"][$j]);

				$timesheetId = Session::get('timesheetId');
				
				$dayDate = Session::get('dayDate');

				Session::put('timeIn1', $searchTimesheets["in1"][$j]);
				Session::put('timeIn2', $searchTimesheets["in2"][$j]);
				Session::put('timeIn3', $searchTimesheets["in3"][$j]);
				Session::put('timeOut1', $searchTimesheets["out1"][$j]);
				Session::put('timeOut2', $searchTimesheets["out2"][$j]);
				Session::put('timeOut3', $searchTimesheets["out3"][$j]);

				$timeIn1 = Session::get('timeIn1');
				$timeIn2 = Session::get('timeIn2');
				$timeIn3 = Session::get('timeIn3');
				$timeOut1 = Session::get('timeOut1');
				$timeOut2 = Session::get('timeOut2');
				$timeOut3 = Session::get('timeOut3');

				$employeeSetting = Employeesetting::where('employee_id', Session::get('employeeSearchId'))->first();

				$summary = Summary::where('employee_id', Session::get('employeeSearchId'))
								  ->where('daydate', $dayDate)->first();					

				$schedule = Schedule::where('employee_id', Session::get('employeeSearchId'))
									->where('schedule_date', $dayDate)->first();
				
				$holiday = Holiday::where('holiday_date', $dayDate)->first();

		        $timesheet = Timesheet::where('employee_id', Session::get('employeeSearchId'))
		                              ->where('daydate', $dayDate)
		                              ->first();




				//$isFlexible = $employeeSetting->is_flexible;
				$scheduleStartTime = $schedule->start_time;	
				$scheduleEndTime = $schedule->end_time;
				$isFlexible = $employeeSetting->is_flexible;
				$hasBreak = $employeeSetting->has_break;
				$breakTime = $employeeSetting->break_time; //date('G', timestamp($employeeSetting->break_time));
				$hoursPerDay = $employeeSetting->hours_per_day;
				$halfOfhoursPerDay = ($hoursPerDay / 2);

				/*$scheduleStartTime = (!empty($schedule->start_time)) ? $schedule->start_time : '';
				$scheduleEndTime = (!empty($schedule->end_time)) ? $schedule->start_time : '';
				$isFlexible = (!empty($employeeSetting->is_flexible)) ? $schedule->start_time : '';
				$hasBreak = (!empty($employeeSetting->has_break)) ? $schedule->start_time : '';
				$breakTime = (!empty($employeeSetting->break_time)) ? $schedule->start_time : ''; //date('G', timestamp($employeeSetting->break_time));
				$hoursPerDay = (!empty($employeeSetting->hours_per_day)) ? $schedule->start_time : '';
				$halfOfhoursPerDay = ($hoursPerDay / 2);*/


				if ( strtotime(date('H:i', strtotime($timesheet->schedule_in))) >
					 strtotime(date('H:i', strtotime($timesheet->schedule_out))) ) {

					$checkSchedule = TRUE;

				} elseif ( strtotime(date('H:i', strtotime($timesheet->schedule_in))) < 
						   strtotime(date('H:i', strtotime($timesheet->schedule_out))) ) {

					$checkSchedule = FALSE;

				}


				// TIMESHEET DATETIME MANIPULATION

				if( !empty($timeIn1) && empty($timeOut1) ) {

					$scheduleInDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

				} elseif( !empty($timeIn1) && !empty($timeOut1) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00

						$scheduleInDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));	

					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						$scheduleInDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));	

						if ( strtotime(date('H:i', strtotime($timeIn1))) > 
							 strtotime(date('H:i', strtotime($timeOut1))) ) {

							$scheduleInDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));		

						} elseif ( strtotime(date('H:i', strtotime($timeIn1))) < 
							       strtotime(date('H:i', strtotime($timeOut1))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut1)) - date('G', strtotime($timeIn1));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleInDate1 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleInDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}										

						}

					}

				}


				if( !empty($timeIn2) && empty($timeOut2) ) {

					$scheduleInDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

				} elseif( !empty($timeIn2) && !empty($timeOut2) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00

						$scheduleInDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));	

					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						$scheduleInDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));	

						if ( strtotime(date('H:i', strtotime($timeIn2))) > 
							 strtotime(date('H:i', strtotime($timeOut2))) ) {

							$scheduleInDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));		

						} elseif ( strtotime(date('H:i', strtotime($timeIn2))) < 
							       strtotime(date('H:i', strtotime($timeOut2))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut2)) - date('G', strtotime($timeIn2));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleInDate2 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleInDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}										

						}

					}

				}					


				if( !empty($timeIn3) && empty($timeOut3) ) {

					$scheduleInDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));	

				} elseif( !empty($timeIn3) && !empty($timeOut3) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00

						$scheduleInDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));	

					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						$scheduleInDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));	

						if ( strtotime(date('H:i', strtotime($timeIn3))) > 
							 strtotime(date('H:i', strtotime($timeOut3))) ) {

							$scheduleInDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));		

						} elseif ( strtotime(date('H:i', strtotime($timeIn3))) < 
							       strtotime(date('H:i', strtotime($timeOut3))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut3)) - date('G', strtotime($timeIn3));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleInDate3 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleInDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}										

						}

					}

				}


				if( !empty($timeIn1) && empty($timeOut1) ) {

					$scheduleOutDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));		

					//$timesheet->clocking_status = 'clock_in_1';					
										
				} elseif( !empty($timeIn1) && !empty($timeOut1) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00
						
						if ( strtotime(date('H:i', strtotime($timeIn1))) > 
							 strtotime(date('H:i', strtotime($timeOut1))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut1)) - date('G', strtotime($timeIn1));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate1 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));
								//$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_out));

							} else {

								$scheduleOutDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn1))) < 
							       strtotime(date('H:i', strtotime($timeOut1))) ) {

							$scheduleOutDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}


					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						if ( strtotime(date('H:i', strtotime($timeIn1))) > 
							 strtotime(date('H:i', strtotime($timeOut1))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut1)) - date('G', strtotime($timeIn1));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate1 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleOutDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn1))) < 
							       strtotime(date('H:i', strtotime($timeOut1))) ) {

							$scheduleOutDate1 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}							

					}

				}


				if( !empty($timeIn2) && empty($timeOut2) ) {

					$scheduleOutDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));	

					//$timesheet->clocking_status = 'clock_in_2';					
										
				} elseif( !empty($timeIn2) && !empty($timeOut2) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00
						
						if ( strtotime(date('H:i', strtotime($timeIn2))) > 
							 strtotime(date('H:i', strtotime($timeOut2))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut2)) - date('G', strtotime($timeIn2));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate2 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));
								//$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_out));

							} else {

								$scheduleOutDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn2))) < 
							       strtotime(date('H:i', strtotime($timeOut2))) ) {

							$scheduleOutDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}


					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						if ( strtotime(date('H:i', strtotime($timeIn2))) > 
							 strtotime(date('H:i', strtotime($timeOut2))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut2)) - date('G', strtotime($timeIn2));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate2 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleOutDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn2))) < 
							       strtotime(date('H:i', strtotime($timeOut2))) ) {

							$scheduleOutDate2 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}							

					}

				}


				if( !empty($timeIn3) && empty($timeOut3) ) {

					$scheduleOutDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));	

					//$timesheet->clocking_status = 'clock_in_3';															
	
				} elseif( !empty($timeIn3) && !empty($timeOut3) ) {

					if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00
						
						if ( strtotime(date('H:i', strtotime($timeIn3))) > 
							 strtotime(date('H:i', strtotime($timeOut3))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut3)) - date('G', strtotime($timeIn3));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate3 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));
								//$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_out));

							} else {

								$scheduleOutDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn3))) < 
							       strtotime(date('H:i', strtotime($timeOut3))) ) {

							$scheduleOutDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}


					} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

						if ( strtotime(date('H:i', strtotime($timeIn3))) > 
							 strtotime(date('H:i', strtotime($timeOut3))) ) {

							//TIME OUT - TIME IN
							$gVal = date('G', strtotime($timeOut3)) - date('G', strtotime($timeIn3));

							if($gVal < 0) { //IF NEGATIVE VALUE

								$scheduleOutDate3 = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

							} else {

								$scheduleOutDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));

							}	


						} elseif ( strtotime(date('H:i', strtotime($timeIn3))) < 
							       strtotime(date('H:i', strtotime($timeOut3))) ) {

							$scheduleOutDate3 = date('Y-m-d', strtotime($timesheet->schedule_in));

						}							

					}

					//$timesheet->clocking_status = 'clock_out_3';													

				}				

				//===============================================================================================================	

				// Check if time and schedule date are empty or not

				if( !empty($timeIn1) && !empty($scheduleInDate1) ) {

					$time = date('H:i:s', strtotime($timeIn1));

					$dateTimeFormatIn1 = $scheduleInDate1.' '.$time;
					Session::put('dateTimeFormatIn1', $dateTimeFormatIn1);					

				} else {

					//$dateTimeFormatIn1 = '';
					$dateTimeFormatIn1 = $timesheet->time_in_1;
					Session::put('dateTimeFormatIn1', $dateTimeFormatIn1);

				}


				if( !empty($timeIn2) && !empty($scheduleInDate2) ) {

					$time = date('H:i:s', strtotime($timeIn2));

					$dateTimeFormatIn2 = $scheduleInDate2.' '.$time;
					Session::put('dateTimeFormatIn2', $dateTimeFormatIn2);

				} else {

					//$dateTimeFormatIn2 = '';
					$dateTimeFormatIn2 = $timesheet->time_in_2;
					Session::put('dateTimeFormatIn2', $dateTimeFormatIn2);

				}


				if( !empty($timeIn3) && !empty($scheduleInDate3) ) {

					$time = date('H:i:s', strtotime($timeIn3));

					$dateTimeFormatIn3 = $scheduleInDate3.' '.$time;
					Session::put('dateTimeFormatIn3', $dateTimeFormatIn3);

				} else {

					//$dateTimeFormatIn3 = '';
					$dateTimeFormatIn3 = $timesheet->time_in_3;
					Session::put('dateTimeFormatIn3', $dateTimeFormatIn3);

				}


				if( !empty($timeOut1) && !empty($scheduleOutDate1) ) {

					$time = date('H:i:s', strtotime($timeOut1));

					$dateTimeFormatOut1 = $scheduleOutDate1.' '.$time;
					Session::put('dateTimeFormatOut1', $dateTimeFormatOut1);

				} else {

					//$dateTimeFormatOut1 = '';
					$dateTimeFormatOut1 = $timesheet->time_out_1;
					Session::put('dateTimeFormatOut1', $dateTimeFormatOut1);

				}


				if( !empty($timeOut2) && !empty($scheduleOutDate2) ) {

					$time = date('H:i:s', strtotime($timeOut2));

					$dateTimeFormatOut2 = $scheduleOutDate2.' '.$time;
					Session::put('dateTimeFormatOut2', $dateTimeFormatOut2);

				} else {

					//$dateTimeFormatOut2 = '';
					$dateTimeFormatOut2 = $timesheet->time_out_2;
					Session::put('dateTimeFormatOut2', $dateTimeFormatOut2);

				}


				if( !empty($timeOut3) && !empty($scheduleOutDate3) ) {

					$time = date('H:i:s', strtotime($timeOut3));

					$dateTimeFormatOut3 = $scheduleOutDate3.' '.$time;
					Session::put('dateTimeFormatOut3', $dateTimeFormatOut3);

				} else {

					//$dateTimeFormatOut3 = '';
					$dateTimeFormatOut3 = $timesheet->time_out_3;					
					Session::put('dateTimeFormatOut3', $dateTimeFormatOut3);

				}																				


				//===============================================================================================================

				//TIMESHEET EDITING FILTERING AND VALIDATION

				//TIME IN 1 &&  TIME OUT 1	
				//TIME IN 2 &&  TIME OUT 2	
				//TIME IN 3 &&  TIME OUT 3		
				if ( ( empty($timeIn1) && empty($timeOut1) ) && 
				     ( empty($timeIn2) && empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

					    //RESET
						$timesheet->clocking_status = 'open';
						$timesheet->time_in_1 = '';
						$timesheet->time_out_1 = '';
						$timesheet->time_in_2 = '';
						$timesheet->time_out_2 = '';
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';
					
						//RESET TARDINESS	
						$timesheet->tardiness_1 = '';
						$summary->lates = '';

						if ($timesheet->save() ) {
							$summary->save();														
						}				

				}	

				if ( ( !empty($timeIn1) && empty($timeOut1) ) && 
				     ( empty($timeIn2) && empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

					    //COMPUTE TARDINESS
						//$timesheetTimeIn = $dateTimeFormatIn1;						
						$timesheet->clocking_status = 'clock_in_1';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = '';
						$timesheet->time_in_2 = '';
						$timesheet->time_out_2 = '';
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';							
						
						//CODE FOR TARDINESS
						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//WITH COMPUTATION: TRUE
							//GET TARDINESS
							$tardinessTime = tardinessTime($dateTimeFormatIn1, $scheduleStartTime);
							$timesheet->tardiness_1 = $tardinessTime;
							$summary->lates = $tardinessTime;

							if ($timesheet->save() ) {
								$summary->save();														
							}

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							//WITH COMPUTATION: FALSE							
							$timesheet->save();						
						
						}						

				}

				if ( ( !empty($timeIn1) && !empty($timeOut1) ) && 
				     ( empty($timeIn2) && empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

						$dateTimeFormatIn1 = Session::get('dateTimeFormatIn1');
						$dateTimeFormatOut1 = Session::get('dateTimeFormatOut1');

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'clock_out_1';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = $dateTimeFormatOut1;
						$timesheet->time_in_2 = '';
						$timesheet->time_out_2 = '';
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';


						//RESET 
						$timesheet->total_hours_2 = '';
						$timesheet->work_hours_2 = '';
						$timesheet->total_overtime_2 = '';
						$timesheet->undertime_2 = '';
						$timesheet->night_differential_2 = '';											
					
						
						//CODE FOR TARDINESS
						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//WITH COMPUTATION: TRUE
							//GET TARDINESS
							$tardinessTime = tardinessTime($dateTimeFormatIn1, $scheduleStartTime);
							$timesheet->tardiness_1 = $tardinessTime;
							$summary->lates = $tardinessTime;

							if ($timesheet->save() ) {
								$summary->save();														
							}

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							//WITH COMPUTATION: FALSE							
							$timesheet->save();						
						
						}

					//RECOMPUTE ABSENCES
					if( !empty($summary->absent) ) {
					
						$summary->absent = ($summary->absent - $hoursPerDay);
					}

					//TIMESHEET 3.1 - EMPTY COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours3 = '';
									$workHours3 = '';
									$hasOvertime3 = FALSE;
									$overtimeHours3 = '';
									$underTimeHours3 = '';
									$hasNightDiff3 = FALSE;
									$nightDiff3 = '';

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;
									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;
									

									//GET OVERTIME													
									if(!$hasOvertime3) {

										$timesheet->total_overtime_3 = $overtimeHours3;
										

									} 
									
									//GET NIGHTDIFF;
									if(!$hasNightDiff3) {

										/*if ( $nightDiff3 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_3 = $nightDiff3;							
											
										}*/

											$timesheet->night_differential_3 = $nightDiff3;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->legal_holiday = $workHours3;

											}

											if(!$hasOvertime3 && !$hasNightDiff3) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->legal_holiday_overtime = $overtimeHours3;
													
												}												

											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->legal_holiday_night_diff = $nightDiff3;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->special_holiday = $workHours3;

											}

											if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;												

											}					

											if(!$hasOvertime3) { //ISOVERTIME: TRUE
												
												if( !empty($timesheet->total_overtime_1) ) {

													$summary->special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->special_holiday_overtime = $overtimeHours3;
													
												}													
												
											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->special_holiday_night_diff = $nightDiff3;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->regular = $timesheet->work_hours_1;

										} else {

											$summary->regular = $workHours3;

										}										

										if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE								

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
												$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->regular_overtime_night_diff = $overtimeNightdiff;																		

										}

										if ($hasOvertime3) { //ISOVERTIME: TRUE

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->regular_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->regular_overtime = $overtimeHours3;
												
											}											

										}				

										if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->regular_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->regular_night_differential = $nightDiff3;
												
											}												

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours3 = '';
									$workHours3 = '';
									$hasOvertime3 = '';
									$overtimeHours3 = '';
									$underTimeHours3 = '';
									$hasNightDiff3 = '';
									$nightDiff3 = '';									
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;
									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;
									

									//GET OVERTIME													
									if(!$hasOvertime3) {

										$timesheet->total_overtime_3 = $overtimeHours3;
										

									} 
									
									//GET NIGHTDIFF;
									if( $hasNightDiff3 ) {

										/*if ( $nightDiff3 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_3 = $nightDiff3;							
											
										}*/


										$timesheet->night_differential_3 = $nightDiff3;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_legal_holiday = $workHours3;

											}											

											if(!$hasOvertime3 && !$hasNightDiff3) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;																	

											}

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_legal_holiday_overtime = $overtimeHours3;
													
												}														

											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_legal_holiday_night_diff = $nightDiff3;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_special_holiday = $workHours3;

											}											

											if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}					

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_special_holiday_overtime = $overtimeHours3;
													
												}													
												
											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff3;

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_special_holiday_night_diff = $nightDiff3;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->rest_day = $timesheet->work_hours_1;

										} else {

											$summary->rest_day = $workHours3;

										}										

										if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
												$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->rest_day_overtime_night_diff = $overtimeNightdiff;																		

										}

										if ($hasOvertime3) { //ISOVERTIME: TRUE

											$summary->rest_day_overtime = $overtimeHours3;

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->rest_day_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->rest_day_overtime = $overtimeHours3;
												
											}											

										}				

										if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->rest_day_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->rest_day_night_differential = $nightDiff3;
												
											}											

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_3 = totalHoursWithOvertime($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_3 = workHours($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_2 = underTimeHours($dateTimeFormatOut3, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff3 = nightDiff($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================							



					//TIMESHEET 2.1 - EMPTY COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours2 = '';
									$workHours2 = '';
									$hasOvertime2 = FALSE;
									$overtimeHours2 = '';
									$underTimeHours2 = '';
									$hasNightDiff2 = FALSE;
									$nightDiff2 = '';

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;
									

									//GET OVERTIME													
									if(!$hasOvertime2) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										

									} 
									
									//GET NIGHTDIFF;
									if( !$hasNightDiff2 ) {

										/*if ( $nightDiff2 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_2 = $nightDiff2;							
											
										}*/

											$timesheet->night_differential_2 = $nightDiff2;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->legal_holiday = $workHours2;

											}

											if(!$hasOvertime2 && !$hasNightDiff2) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
													$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}

											if(!$hasOvertime2) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->legal_holiday_overtime = $overtimeHours2;
													
												}												

											}

											if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->legal_holiday_night_diff = $nightDiff2;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->special_holiday = $workHours2;

											}

											if(!$hasOvertime2 && !$hasNightDiff2) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
													$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;												

											}					

											if(!$hasOvertime2) { //ISOVERTIME: TRUE
												
												if( !empty($timesheet->total_overtime_1) ) {

													$summary->special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->special_holiday_overtime = $overtimeHours2;
													
												}													
												
											}

											if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->special_holiday_night_diff = $nightDiff2;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->regular = $timesheet->work_hours_1;

										} else {

											$summary->regular = $workHours2;

										}										

										if(!$hasOvertime2 && !$hasNightDiff2) { //ISOVERTIME AND HASNIGHTDIFF: TRUE								

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
												$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->regular_overtime_night_diff = $overtimeNightdiff;																		

										}

										if (!$hasOvertime2) { //ISOVERTIME: TRUE

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->regular_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->regular_overtime = $overtimeHours2;
												
											}											

										}				

										if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->regular_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->regular_night_differential = $nightDiff2;
												
											}												

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours2 = '';
									$workHours2 = '';
									$hasOvertime2 = '';
									$overtimeHours2 = '';
									$underTimeHours2 = '';
									$hasNightDiff2 = '';
									$nightDiff2 = '';									
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;
									

									//GET OVERTIME													
									if($hasOvertime2) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										

									} 
									
									//GET NIGHTDIFF;
									if( $hasNightDiff2 ) {

										/*if ( $nightDiff2 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_2 = $nightDiff2;							
											
										}*/

										$timesheet->night_differential_2 = $nightDiff2;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_legal_holiday = $workHours2;

											}											

											if(!$hasOvertime2 && !$hasNightDiff2) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
													$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;																	

											}

											if($hasOvertime2) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_legal_holiday_overtime = $overtimeHours2;
													
												}														

											}

											if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_legal_holiday_night_diff = $nightDiff2;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_special_holiday = $workHours2;

											}											

											if(!$hasOvertime2 && !$hasNightDiff2) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
													$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}					

											if($hasOvertime2) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_special_holiday_overtime = $overtimeHours2;
													
												}													
												
											}

											if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff2;

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_special_holiday_night_diff = $nightDiff2;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->rest_day = $timesheet->work_hours_1;

										} else {

											$summary->rest_day = $workHours2;

										}										

										if(!$hasOvertime2 && !$hasNightDiff2) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours2 = ($overtimeHours2 + $timesheet->total_overtime_1);
												$nightDiff2 = ($nightDiff2 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours2 + $nightDiff2, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->rest_day_overtime_night_diff = $overtimeNightdiff;																		

										}

										if (!$hasOvertime2) { //ISOVERTIME: TRUE

											$summary->rest_day_overtime = $overtimeHours2;

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->rest_day_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->rest_day_overtime = $overtimeHours2;
												
											}											

										}				

										if(!$hasNightDiff2) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->rest_day_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->rest_day_night_differential = $nightDiff2;
												
											}											

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_2 = totalHoursWithOvertime($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_2 = workHours($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_2 = underTimeHours($dateTimeFormatOut2, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff2 = nightDiff($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================		



					//TIMESHEET 1.1 - WITH COMPUTATION =========================================================================================================


						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {

									//$timesheet->clocking_status = "clock_out_1";
									$timesheet->time_out_1 = $dateTimeFormatOut1;	
									
									//WITH COMPLETE COMPUTATION: TRUE									 
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());										

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}

										} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

										}

									}


									//GET OVERTIME													
									if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';
										
										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										

									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
									
		 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->legal_holiday = $workHours1;										

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->legal_holiday_night_diff = '';

											}												

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day


										"HOLIDAY: FALSE - Regular Day \n";

										$summary->regular = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->regular_overtime = $overtimeHours1;

											} else {

												$summary->regular_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->regular_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->regular_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->regular_night_differential = $nightDiff1;

										} else {

											$summary->regular_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									

									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}
											
										} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

										}

									}																	

									//GET OVERTIME													
									if($hasOvertime1 && empty($timesheet->tardiness_1)) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										
									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->rest_day_legal_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_legal_holiday_night_diff = '';

											}

										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->rest_day_special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										$summary->rest_day = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->rest_day_overtime = $overtimeHours1;

											} else {

												$summary->rest_day_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->rest_day_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->rest_day_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->rest_day_night_differential = $nightDiff1;

										} else {

											$summary->rest_day_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}															


								}								

							}
												

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_1 = totalHoursWithOvertime($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_1 = workHours($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_1 = underTimeHours($dateTimeFormatOut1, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff1 = nightDiff($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}	

					
					//=========================================================================================================	
				}	


				if ( ( !empty($timeIn1) && !empty($timeOut1) ) && 
				     ( !empty($timeIn2) && empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'clock_in_2';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = $dateTimeFormatOut1;
						$timesheet->time_in_2 = $dateTimeFormatIn2;
						$timesheet->time_out_2 = '';
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';
						$timesheet->save();						

				}	


				if ( ( !empty($timeIn1) && !empty($timeOut1) ) && 
				     ( !empty($timeIn2) && !empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'clock_out_2';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = $dateTimeFormatOut1;
						$timesheet->time_in_2 = $dateTimeFormatIn2;
						$timesheet->time_out_2 = $dateTimeFormatOut2;
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';
						//$timesheet->save();

						//CODE FOR TARDINESS
						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//WITH COMPUTATION: TRUE
							//GET TARDINESS
							$tardinessTime = tardinessTime($dateTimeFormatIn1, $scheduleStartTime);
							$timesheet->tardiness_1 = $tardinessTime;
							$summary->lates = $tardinessTime;

							if ($timesheet->save() ) {
								$summary->save();														
							}

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							//WITH COMPUTATION: FALSE							
							$timesheet->save();						
						
						}

					//RECOMPUTE ABSENCES
					if( !empty($summary->absent) ) {
					
						$summary->absent = ($summary->absent - $hoursPerDay);
					}							

					//TIMESHEET 3.2 - EMPTY COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours3 = '';
									$workHours3 = '';
									$hasOvertime3 = FALSE;
									$overtimeHours3 = '';
									$underTimeHours3 = '';
									$hasNightDiff3 = FALSE;
									$nightDiff3 = '';

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;
									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;
									

									//GET OVERTIME													
									if(!$hasOvertime3) {

										$timesheet->total_overtime_3 = $overtimeHours3;
										

									} 
									
									//GET NIGHTDIFF;
									if(!$hasNightDiff3) {

										/*if ( $nightDiff3 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_3 = $nightDiff3;							
											
										}*/

											$timesheet->night_differential_3 = $nightDiff3;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->legal_holiday = $workHours3;

											}

											if(!$hasOvertime3 && !$hasNightDiff3) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->legal_holiday_overtime = $overtimeHours3;
													
												}												

											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->legal_holiday_night_diff = $nightDiff3;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->special_holiday = $workHours3;

											}

											if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;												

											}					

											if(!$hasOvertime3) { //ISOVERTIME: TRUE
												
												if( !empty($timesheet->total_overtime_1) ) {

													$summary->special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->special_holiday_overtime = $overtimeHours3;
													
												}													
												
											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->special_holiday_night_diff = $nightDiff3;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->regular = $timesheet->work_hours_1;

										} else {

											$summary->regular = $workHours3;

										}										

										if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE								

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
												$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->regular_overtime_night_diff = $overtimeNightdiff;																		

										}

										if ($hasOvertime3) { //ISOVERTIME: TRUE

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->regular_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->regular_overtime = $overtimeHours3;
												
											}											

										}				

										if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->regular_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->regular_night_differential = $nightDiff3;
												
											}												

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours3 = '';
									$workHours3 = '';
									$hasOvertime3 = '';
									$overtimeHours3 = '';
									$underTimeHours3 = '';
									$hasNightDiff3 = '';
									$nightDiff3 = '';									
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;
									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;
									

									//GET OVERTIME													
									if(!$hasOvertime3) {

										$timesheet->total_overtime_3 = $overtimeHours3;
										

									} 
									
									//GET NIGHTDIFF;
									if( $hasNightDiff3 ) {

										/*if ( $nightDiff3 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_3 = $nightDiff3;							
											
										}*/


										$timesheet->night_differential_3 = $nightDiff3;							

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_legal_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_legal_holiday = $workHours3;

											}											

											if(!$hasOvertime3 && !$hasNightDiff3) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;																	

											}

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_legal_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_legal_holiday_overtime = $overtimeHours3;
													
												}														

											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_legal_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_legal_holiday_night_diff = $nightDiff3;
													
												}												

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_special_holiday = $timesheet->work_hours_1;

											} else {

												$summary->rest_day_special_holiday = $workHours3;

											}											

											if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

													$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
													$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

													$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

												} else {

													$overtimeNightdiff = '';
												}

												$summary->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;																		

											}					

											if(!$hasOvertime3) { //ISOVERTIME: TRUE

												if( !empty($timesheet->total_overtime_1) ) {

													$summary->rest_day_special_holiday_overtime = $timesheet->total_overtime_1;
													
												} else {

													$summary->rest_day_special_holiday_overtime = $overtimeHours3;
													
												}													
												
											}

											if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff3;

												if( !empty($timesheet->night_differential_1) ) {

													$summary->rest_day_special_holiday_night_diff = $timesheet->night_differential_1;
													
												} else {

													$summary->rest_day_special_holiday_night_diff = $nightDiff3;
													
												}													

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->rest_day = $timesheet->work_hours_1;

										} else {

											$summary->rest_day = $workHours3;

										}										

										if(!$hasOvertime3 && !$hasNightDiff3) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											if( !empty($timesheet->total_overtime_1) && !empty($timesheet->night_differential_1) ) {

												$overtimeHours3 = ($overtimeHours3 + $timesheet->total_overtime_1);
												$nightDiff3 = ($nightDiff3 + $timesheet->night_differential_1);

												$overtimeNightdiff = number_format($overtimeHours3 + $nightDiff3, 2);	

											} else {

												$overtimeNightdiff = '';
											}

											$summary->rest_day_overtime_night_diff = $overtimeNightdiff;																		

										}

										if ($hasOvertime3) { //ISOVERTIME: TRUE

											$summary->rest_day_overtime = $overtimeHours3;

											if( !empty($timesheet->total_overtime_1) ) {

												$summary->rest_day_overtime = $timesheet->total_overtime_1;
												
											} else {

												$summary->rest_day_overtime = $overtimeHours3;
												
											}											

										}				

										if(!$hasNightDiff3) { //HASNIGHTDIFF: TRUE

											if( !empty($timesheet->night_differential_1) ) {

												$summary->rest_day_night_differential = $timesheet->night_differential_1;
												
											} else {

												$summary->rest_day_night_differential = $nightDiff3;
												
											}											

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_3 = totalHoursWithOvertime($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_3 = workHours($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_2 = underTimeHours($dateTimeFormatOut3, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff3 = nightDiff($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================


					//TIMESHEET 1.2 - WITH COMPUTATION =========================================================================================================


						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {

									//$timesheet->clocking_status = "clock_out_1";
									$timesheet->time_out_1 = $dateTimeFormatOut1;	
									
									//WITH COMPLETE COMPUTATION: TRUE									 
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());										

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}

										} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

										}

									}


									//GET OVERTIME													
									if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';
										
										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										

									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
									
		 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->legal_holiday = $workHours1;										

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->legal_holiday_night_diff = '';

											}												

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day


										"HOLIDAY: FALSE - Regular Day \n";

										$summary->regular = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->regular_overtime = $overtimeHours1;

											} else {

												$summary->regular_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->regular_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->regular_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->regular_night_differential = $nightDiff1;

										} else {

											$summary->regular_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									

									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}
											
										} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

										}

									}																	

									//GET OVERTIME													
									if($hasOvertime1 && empty($timesheet->tardiness_1)) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										
									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->rest_day_legal_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_legal_holiday_night_diff = '';

											}

										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->rest_day_special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										$summary->rest_day = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->rest_day_overtime = $overtimeHours1;

											} else {

												$summary->rest_day_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->rest_day_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->rest_day_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->rest_day_night_differential = $nightDiff1;

										} else {

											$summary->rest_day_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}															


								}								

							}
												

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_1 = totalHoursWithOvertime($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_1 = workHours($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_1 = underTimeHours($dateTimeFormatOut1, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff1 = nightDiff($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}	

					
					//=========================================================================================================	


					//TIMESHEET 2.1 - WITH COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours2 = totalHours($dateTimeFormatIn2, $dateTimeFormatOut2);
									$workHours2 = $workHours;
									$hasOvertime2 = TRUE;
									$overtimeHours2 = $workHours;
									//$underTimeHours2 = $underTimeHours;
									//$hasOvertimeNightDiff2 = $hasNightDiff;
									//$nightDiff2 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;

									//GET NIGHTDIFF;
									$hasOvertimeNightDiff2 = FALSE;

									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 2
										$overtimeNightDiff2 = nightDifferential($dateTimeFormatIn2, $dateTimeFormatOut2, nightDifferentialRange());

										if( $overtimeNightDiff2 !== FALSE ) {

											$hasOvertimeNightDiff2 = TRUE;
											$timesheet->night_differential_2 = $overtimeNightDiff2;

										} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

										}

									} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

									}

									//GET OVERTIME													
									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										

									} else {

										$timesheet->total_overtime_2 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_2 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 2)								        
								        					 ->update(array('overtime_status' => NULL));										


									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->legal_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->legal_holiday = $workHours2;

											}
											
											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime = '';

													}

												} else {

													$summary->legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->legal_holiday_overtime_night_diff = '';
													
												}																									

											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->special_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->special_holiday = $workHours2;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime = '';

													}

												} else {

													$summary->special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->special_holiday_overtime_night_diff = '';
													
												}																									

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->regular = ($workHours2 + $timesheet->work_hours_1);

										} else {

											$summary->regular = $workHours2;

										}										
										
										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_2 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime = number_format(($overtimeHours2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime = '';

												}

											} else {

												$summary->regular_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime_night_diff = '';

												}

											} else {

												$summary->regular_overtime_night_diff = '';
												
											}																									

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours2 = totalHours($dateTimeFormatIn2, $dateTimeFormatOut2);
									$workHours2 = $workHours;
									$hasOvertime2 = TRUE;
									$overtimeHours2 = $workHours;
									//$underTimeHours2 = $underTimeHours;
									//$hasOvertimeNightDiff2 = $hasNightDiff;
									//$nightDiff2 = $nightDiff;							
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;

									//GET NIGHTDIFF;
									$hasOvertimeNightDiff2 = FALSE;

									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 2
										$overtimeNightDiff2 = nightDifferential($dateTimeFormatIn2, $dateTimeFormatOut2, nightDifferentialRange());

										if( $overtimeNightDiff2 !== FALSE ) {

											$hasOvertimeNightDiff2 = TRUE;
											$timesheet->night_differential_2 = $overtimeNightDiff2;

										} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

										}

									} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

									}

									//GET OVERTIME																						
									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										
									} else {

										$timesheet->total_overtime_2 = '';


										//RESET OVERTIME STATUS
										$timesheet->overtime_status_2 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 2)								        
								        					 ->update(array('overtime_status' => NULL));										


									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Rest Day Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_legal_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->rest_day_legal_holiday = $workHours2;

											}
											
											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime = '';

													}

												} else {

													$summary->rest_day_legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->rest_day_legal_holiday_overtime_night_diff = '';
													
												}																									

											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Rest Day Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_special_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->rest_day_special_holiday = $workHours2;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime = '';

													}

												} else {

													$summary->rest_day_special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->rest_day_special_holiday_overtime_night_diff = '';
													
												}																									

											}

										}

									} else { //HOLIDAY: FALSE - Rest Day

										"HOLIDAY: FALSE - Rest Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->rest_day = ($workHours2 + $timesheet->work_hours_1);

										} else {

											$summary->rest_day = $workHours2;

										}										
										
										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_2 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime = number_format(($overtimeHours2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime = '';

												}

											} else {

												$summary->rest_day_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime_night_diff = '';

												}

											} else {

												$summary->rest_day_overtime_night_diff = '';
												
											}																									

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_2 = totalHoursWithOvertime($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_2 = workHours($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_2 = underTimeHours($dateTimeFormatOut2, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff2 = nightDiff($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================									


				}


				if ( ( !empty($timeIn1) && !empty($timeOut1) ) && 
				     ( !empty($timeIn2) && !empty($timeOut2) ) &&
				     ( !empty($timeIn3) && empty($timeOut3) )  ) {

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'clock_in_3';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = $dateTimeFormatOut1;
						$timesheet->time_in_2 = $dateTimeFormatIn2;
						$timesheet->time_out_2 = $dateTimeFormatOut2;
						$timesheet->time_in_3 = $dateTimeFormatIn3;
						$timesheet->time_out_3 = '';
						//$timesheet->save();							

				}	

				if ( ( !empty($timeIn1) && !empty($timeOut1) ) && 
				     ( !empty($timeIn2) && !empty($timeOut2) ) &&
				     ( !empty($timeIn3) && !empty($timeOut3) )  ) {

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'clock_out_3';
						$timesheet->time_in_1 = $dateTimeFormatIn1;
						$timesheet->time_out_1 = $dateTimeFormatOut1;
						$timesheet->time_in_2 = $dateTimeFormatIn2;
						$timesheet->time_out_2 = $dateTimeFormatOut2;
						$timesheet->time_in_3 = $dateTimeFormatIn3;
						$timesheet->time_out_3 = $dateTimeFormatOut3;
						//$timesheet->save();

						//CODE FOR TARDINESS
						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//WITH COMPUTATION: TRUE
							//GET TARDINESS
							$tardinessTime = tardinessTime($dateTimeFormatIn1, $scheduleStartTime);
							$timesheet->tardiness_1 = $tardinessTime;
							$summary->lates = $tardinessTime;

							if ($timesheet->save() ) {
								$summary->save();														
							}

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							//WITH COMPUTATION: FALSE							
							$timesheet->save();						
						
						}	

					//RECOMPUTE ABSENCES
					if( !empty($summary->absent) ) {
					
						$summary->absent = ($summary->absent - $hoursPerDay);
					}											


					//TIMESHEET 1.3 - WITH COMPUTATION =========================================================================================================


						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {

									//$timesheet->clocking_status = "clock_out_1";
									$timesheet->time_out_1 = $dateTimeFormatOut1;	
									
									//WITH COMPLETE COMPUTATION: TRUE									 
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());										

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}

										} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

										}

									}


									//GET OVERTIME													
									if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';
										
										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										

									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
									
		 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->legal_holiday = $workHours1;										

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->legal_holiday_night_diff = '';

											}												

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day


										"HOLIDAY: FALSE - Regular Day \n";

										$summary->regular = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->regular_overtime = $overtimeHours1;

											} else {

												$summary->regular_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->regular_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->regular_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->regular_night_differential = $nightDiff1;

										} else {

											$summary->regular_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									

									$totalHours1 = $totalHours;
									$workHours1 = $workHours;
									$hasOvertime1 = $hasOvertime;
									$overtimeHours1 = $overtimeHours;
									$underTimeHours1 = $underTimeHours;
									//$hasNightDiff1 = $hasNightDiff;
									//$nightDiff1 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_1 = $totalHours1;
									

									//GET WORKHOURS
									$timesheet->work_hours_1 = $workHours1;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff1 = TRUE;

									} else {

										$scheduleHasNightDiff1 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasNightDiff1 = FALSE;
									$hasOvertimeNightDiff1 = FALSE;

									if( $scheduleHasNightDiff1 !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff1 = nightDifferential($dateTimeFormatIn1, $dateTimeFormatOut1, nightDifferentialRange());

										if( $nightDiff1 !== FALSE ) {

											$hasNightDiff1 = TRUE;
											$timesheet->night_differential_1 = $nightDiff1;

										} else {

											$hasNightDiff1 = FALSE;
											$timesheet->night_differential_1 = '';


										}

									} else {

										if( $hasOvertime1 && empty($timesheet->tardiness_1) ) {
											
											//Overtime + Night Differential 1
											echo $overtimeNightDiff1 = nightDifferential($scheduleEndTime, $dateTimeFormatOut1, nightDifferentialRange());

											if( $overtimeNightDiff1 !== FALSE ) {

												$hasOvertimeNightDiff1 = TRUE;
												$timesheet->night_differential_1 = $overtimeNightDiff1;

											} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

											}
											
										} else {

												$hasOvertimeNightDiff1 = FALSE;
												$timesheet->night_differential_1 = '';

										}

									}																	

									//GET OVERTIME													
									if($hasOvertime1 && empty($timesheet->tardiness_1)) {

										$timesheet->total_overtime_1 = $overtimeHours1;
										

									} else {

										$timesheet->total_overtime_1 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_1 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 1)								        
								        					 ->update(array('overtime_status' => NULL));										
									}
									
									//GET UNDERTIME
									$timesheet->undertime_1 = $underTimeHours1;
									$summary->undertime = $underTimeHours1;
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											$summary->rest_day_legal_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_legal_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_legal_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_legal_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_legal_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_legal_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_legal_holiday_night_diff = '';

											}

										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										

											$summary->rest_day_special_holiday = $workHours1;

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_1 === 1 ) {

												if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													$summary->rest_day_special_holiday_overtime = $overtimeHours1;

												} else {

													$summary->rest_day_special_holiday_overtime = '';

												} 

												
												if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

													$summary->rest_day_special_holiday_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

												} else {

													$summary->rest_day_special_holiday_overtime_night_diff = '';

												}																								

											}

											if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff1;

											} else {

												$summary->rest_day_special_holiday_night_diff = '';

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										$summary->rest_day = $workHours1;									

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_1 === 1 ) {

											if( $hasOvertime1 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												$summary->rest_day_overtime = $overtimeHours1;

											} else {

												$summary->rest_day_overtime = '';

											} 

											
											if($hasOvertimeNightDiff1) { //HASOVERTIMENIGHTDIFF: TRUE

												$summary->rest_day_overtime_night_diff = number_format($overtimeNightDiff1, 2);													

											} else {

												$summary->rest_day_overtime_night_diff = '';

											}																								

										}

										if($hasNightDiff1) { //HASNIGHTDIFF: TRUE

											$summary->rest_day_night_differential = $nightDiff1;

										} else {

											$summary->rest_day_night_differential = '';

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}															


								}								

							}
												

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_1 = totalHoursWithOvertime($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_1 = workHours($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_1 = underTimeHours($dateTimeFormatOut1, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff1 = nightDiff($dateTimeFormatIn1, $dateTimeFormatOut1, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}	

					
					//=========================================================================================================		


					//TIMESHEET 2.3 - WITH COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours2 = totalHours($dateTimeFormatIn2, $dateTimeFormatOut2);
									$workHours2 = $workHours;
									$hasOvertime2 = TRUE;
									$overtimeHours2 = $workHours;
									//$underTimeHours2 = $underTimeHours;
									//$hasOvertimeNightDiff2 = $hasNightDiff;
									//$nightDiff2 = $nightDiff;

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;

									//GET NIGHTDIFF;
									$hasOvertimeNightDiff2 = FALSE;

									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 2
										$overtimeNightDiff2 = nightDifferential($dateTimeFormatIn2, $dateTimeFormatOut2, nightDifferentialRange());

										if( $overtimeNightDiff2 !== FALSE ) {

											$hasOvertimeNightDiff2 = TRUE;
											$timesheet->night_differential_2 = $overtimeNightDiff2;

										} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

										}

									} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

									}

									//GET OVERTIME													
									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										

									} else {

										$timesheet->total_overtime_2 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_2 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 2)								        
								        					 ->update(array('overtime_status' => NULL));										


									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->legal_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->legal_holiday = $workHours2;

											}
											
											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime = '';

													}

												} else {

													$summary->legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->legal_holiday_overtime_night_diff = '';
													
												}																									

											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->special_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->special_holiday = $workHours2;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime = '';

													}

												} else {

													$summary->special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->special_holiday_overtime_night_diff = '';
													
												}																									

											}

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->regular = ($workHours2 + $timesheet->work_hours_1);

										} else {

											$summary->regular = $workHours2;

										}										
										
										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_2 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime = number_format(($overtimeHours2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime = '';

												}

											} else {

												$summary->regular_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->regular_overtime_night_diff = '';

												}

											} else {

												$summary->regular_overtime_night_diff = '';
												
											}																									

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours2 = totalHours($dateTimeFormatIn2, $dateTimeFormatOut2);
									$workHours2 = $workHours;
									$hasOvertime2 = TRUE;
									$overtimeHours2 = $workHours;
									//$underTimeHours2 = $underTimeHours;
									//$hasOvertimeNightDiff2 = $hasNightDiff;
									//$nightDiff2 = $nightDiff;							
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_2 = $totalHours2;
									

									//GET WORKHOURS
									$timesheet->work_hours_2 = $workHours2;

									//GET NIGHTDIFF;
									$hasOvertimeNightDiff2 = FALSE;

									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 2
										$overtimeNightDiff2 = nightDifferential($dateTimeFormatIn2, $dateTimeFormatOut2, nightDifferentialRange());

										if( $overtimeNightDiff2 !== FALSE ) {

											$hasOvertimeNightDiff2 = TRUE;
											$timesheet->night_differential_2 = $overtimeNightDiff2;

										} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

										}

									} else {

											$hasOvertimeNightDiff2 = FALSE;
											$timesheet->night_differential_2 = '';

									}

									//GET OVERTIME																						
									if( $hasOvertime2 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_2 = $overtimeHours2;
										
									} else {

										$timesheet->total_overtime_2 = '';


										//RESET OVERTIME STATUS
										$timesheet->overtime_status_2 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 2)								        
								        					 ->update(array('overtime_status' => NULL));										


									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Rest Day Regular holiday \n";

											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_legal_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->rest_day_legal_holiday = $workHours2;

											}
											
											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime = '';

													}

												} else {

													$summary->rest_day_legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->rest_day_legal_holiday_overtime_night_diff = '';
													
												}																									

											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Rest Day Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) ) {

												$summary->rest_day_special_holiday = ($workHours2 + $timesheet->work_hours_1);

											} else {

												$summary->rest_day_special_holiday = $workHours2;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_2 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($overtimeHours2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime = '';

													}

												} else {

													$summary->rest_day_special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

														$summary->rest_day_special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->rest_day_special_holiday_overtime_night_diff = '';
													
												}																									

											}

										}

									} else { //HOLIDAY: FALSE - Rest Day

										"HOLIDAY: FALSE - Rest Day \n";

										if( !empty($timesheet->work_hours_1) ) {

											$summary->rest_day = ($workHours2 + $timesheet->work_hours_1);

										} else {

											$summary->rest_day = $workHours2;

										}										
										
										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_2 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime = number_format(($overtimeHours2 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime = number_format(($overtimeHours2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime = '';

												}

											} else {

												$summary->rest_day_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff2) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff2 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1  ) {

													$summary->rest_day_overtime_night_diff = '';

												}

											} else {

												$summary->rest_day_overtime_night_diff = '';
												
											}																									

										}

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_2 = totalHoursWithOvertime($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_2 = workHours($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_2 = underTimeHours($dateTimeFormatOut2, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff2 = nightDiff($dateTimeFormatIn2, $dateTimeFormatOut2, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================		


					//TIMESHEET 3.3 - WITH COMPUTATION =========================================================================================================


						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {
								
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									$totalHours3 = totalHours($dateTimeFormatIn3, $dateTimeFormatOut3);									
									$workHours3 = $workHours;
									$hasOvertime3 = TRUE;
									$overtimeHours3 = $workHours;
									//$underTimeHours3 = $underTimeHours;
									//$hasOvertimeNightDiff3 = $hasNightDiff;
									//$nightDiff3 = $nightDiff;


									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;

									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;


									//GET NIGHTDIFF;
									$hasOvertimeNightDiff3 = FALSE;
									
									if( $hasOvertime3 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 3
										$overtimeNightDiff3 = nightDifferential($dateTimeFormatIn3, $dateTimeFormatOut3, nightDifferentialRange());

										if( $overtimeNightDiff3 !== FALSE ) {

											$hasOvertimeNightDiff3 = TRUE;
											$timesheet->night_differential_3 = $overtimeNightDiff3;

										} else {

											$hasOvertimeNightDiff3 = FALSE;
											$timesheet->night_differential_3 = '';

										}
										
									} else {

											$hasOvertimeNightDiff3 = FALSE;
											$timesheet->night_differential_3 = '';

									}									

									//GET OVERTIME													
									if( $hasOvertime3 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_3 = $overtimeHours3;										

									} else {

										$timesheet->total_overtime_3 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_3= NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 3)								        
								        					 ->update(array('overtime_status' => NULL));										

									}									
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

												$summary->legal_holiday = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

											} else {

												$summary->legal_holiday = $workHours3;

											}
											if( $timesheet->overtime_status_3 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_2), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->legal_holiday_overtime = '';

													}

												} else {

													$summary->legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->legal_holiday_overtime_night_diff = '';													
												} 


											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

												$summary->special_holiday = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

											} else {

												$summary->special_holiday = $workHours3;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_3 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_2), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->special_holiday_overtime = '';

													}

												} else {

													$summary->special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->special_holiday_overtime_night_diff = '';													
												} 


											}																					

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

											$summary->regular = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

										} else {

											$summary->regular = $workHours3;

										}										

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_3 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_1), 2);									

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime = number_format(($overtimeHours3), 2);												

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_2), 2);															

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

													$summary->regular_overtime = '';

												}

											} else {

												$summary->regular_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->regular_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

													$summary->regular_overtime_night_diff = '';

												}

											} else {

												$summary->regular_overtime_night_diff = '';													
											} 


										}																			

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									$totalHours3 = $totalHours;
									$workHours3 = $workHours;
									$hasOvertime3 = '';
									$overtimeHours3 = $overtimeHours;
									//$underTimeHours3 = $underTimeHours;
									//$hasOvertimeNightDiff3 = '';
									//$nightDiff3 = $nightDiff;									
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									$timesheet->total_hours_3 = $totalHours3;
									

									//GET WORKHOURS
									$timesheet->work_hours_3 = $workHours3;


									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff3 = TRUE;

									} else {

										$scheduleHasNightDiff3 = FALSE;

									}

									//GET NIGHTDIFF;
									$hasOvertimeNightDiff3 = FALSE;
									
									if( $hasOvertime3 && empty($timesheet->tardiness_1) ) {
									
										//Overtime + Night Differential 3
										$overtimeNightDiff3 = nightDifferential($dateTimeFormatIn3, $dateTimeFormatOut3, nightDifferentialRange());

										if( $overtimeNightDiff3 !== FALSE ) {

											$hasOvertimeNightDiff3 = TRUE;
											$timesheet->night_differential_3 = $overtimeNightDiff3;

										} else {

											$hasOvertimeNightDiff3 = FALSE;
											$timesheet->night_differential_3 = '';

										}
										
									} else {

											$hasOvertimeNightDiff3 = FALSE;
											$timesheet->night_differential_3 = '';

									}									
									

									//GET OVERTIME													
									if( $hasOvertime3 && empty($timesheet->tardiness_1) ) {

										$timesheet->total_overtime_3 = $overtimeHours3;
										
									} else {

										$timesheet->total_overtime_3 = '';

										//RESET OVERTIME STATUS
										$timesheet->overtime_status_3 = NULL;
										
										DB::table('overtime')->where('employee_id', Session::get('employeeSearchId'))
															 ->where('timesheet_id', $timesheetId)
															 ->where('seq_no', 3)								        
								        					 ->update(array('overtime_status' => NULL));										

									}
									
									//GET NIGHTDIFF;
									/*if( $hasOvertimeNightDiff3 ) {

										if ( $nightDiff3 > 0 ) { //Value of NightDiff is positive
											
											$timesheet->night_differential_3 = $nightDiff3;							
											
										}

									} else {

										$timesheet->night_differential_3 = '';							

									}*/
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										"HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											"Regular holiday \n";

											if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

												$summary->legal_holiday = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

											} else {

												$summary->legal_holiday = $workHours3;

											}
											if( $timesheet->overtime_status_3 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($overtimeHours3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_2), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->legal_holiday_overtime = '';

													}

												} else {

													$summary->legal_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->legal_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->legal_holiday_overtime_night_diff = '';													
												} 


											}

										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											"Special non-working day \n";										


											if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

												$summary->special_holiday = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

											} else {

												$summary->special_holiday = $workHours3;

											}

											//CHECK IF OVERTIME STATUS IS APPROVED
											if( $timesheet->overtime_status_3 === 1 ) {

												if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($overtimeHours3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_2), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->special_holiday_overtime = '';

													}

												} else {

													$summary->special_holiday_overtime = '';													
												} 

												
												if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

													if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->legal_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

													} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

														$summary->special_holiday_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

													} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

														$summary->special_holiday_overtime_night_diff = '';

													}

												} else {

													$summary->special_holiday_overtime_night_diff = '';													
												} 


											}																					

										}

									} else { //HOLIDAY: FALSE - Regular Day

										"HOLIDAY: FALSE - Regular Day \n";

										if( !empty($timesheet->work_hours_1) && !empty($timesheet->work_hours_2) ) {

											$summary->regular = $workHours3 + $timesheet->work_hours_1 + $timesheet->work_hours_2;

										} else {

											$summary->regular = $workHours3;

										}										

										//CHECK IF OVERTIME STATUS IS APPROVED
										if( $timesheet->overtime_status_3 === 1 ) {

											if( $hasOvertime2 && empty($timesheet->tardiness_1) ) { //ISOVERTIME: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_1 + $timesheet->total_overtime_2), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime = number_format(($overtimeHours3 + $timesheet->total_overtime_1), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_1), 2);									

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime = number_format(($overtimeHours3), 2);												

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_2), 2);															

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime = number_format(($timesheet->total_overtime_3 + $timesheet->total_overtime_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

													$summary->rest_day_overtime = '';

												}

											} else {

												$summary->rest_day_overtime = '';													
											} 

											
											if($hasOvertimeNightDiff3) { //HASOVERTIMENIGHTDIFF: TRUE

												if( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1 + $timesheet->night_differential_3), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->night_differential_1 + $timesheet->night_differential_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff3 + $timesheet->night_differential_1), 2);

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);									

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 !== 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($overtimeNightDiff3), 2);												

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 !== 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->night_differential_1), 2);															

												} elseif( $timesheet->overtime_status_1 !== 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1 ) {

													$summary->rest_day_overtime_night_diff = number_format(($timesheet->total_overtime_3 + $timesheet->night_differential_1), 2);			

												} elseif( $timesheet->overtime_status_1 === 1 && $timesheet->overtime_status_2 === 1 && $timesheet->overtime_status_3 === 1  ) {

													$summary->rest_day_overtime_night_diff = '';

												}

											} else {

												$summary->rest_day_overtime_night_diff = '';													
											} 


										}																			

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							$timesheet->total_hours_3 = totalHoursWithOvertime($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							$timesheet->work_hours_3 = workHours($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							$timesheet->undertime_3 = underTimeHours($dateTimeFormatOut3, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff3 = nightDiff($dateTimeFormatIn3, $dateTimeFormatOut3, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								

							}						

						}


					//=========================================================================================================		


				}



				//EMPTY=========================================================================================================													
				
				if ( ( empty($timeIn1) && empty($timeOut1) ) && 
				     ( empty($timeIn2) && empty($timeOut2) ) &&
				     ( empty($timeIn3) && empty($timeOut3) )  ) {

					    //COMPUTE WORKHOURS, TOTAL HOURS, TOTAL OVERTIME,
					    // NIGHT DIFF, TARDINESS, UNDERTIME
						$timesheet->clocking_status = 'open';
						$timesheet->time_in_1 = '';
						$timesheet->time_out_1 = '';
						$timesheet->time_in_2 = '';
						$timesheet->time_out_2 = '';
						$timesheet->time_in_3 = '';
						$timesheet->time_out_3 = '';

						//RESET TARDINESS/LATES
						$timesheet->tardiness_1 = '';
						$summary->lates = '';

						//RESET TIMESHEET AND SUMMARY DATA
						$timesheet->total_hours_1 = '';
						$timesheet->work_hours_1 = '';
						$timesheet->total_overtime_1 = '';
						$timesheet->undertime_1 = '';
						$timesheet->night_differential_1 = '';

						$timesheet->total_hours_2 = '';
						$timesheet->work_hours_2 = '';
						$timesheet->total_overtime_2 = '';
						$timesheet->undertime_2 = '';
						$timesheet->night_differential_2 = '';						

						$timesheet->total_hours_3 = '';
						$timesheet->work_hours_3 = '';
						$timesheet->total_overtime_3 = '';
						$timesheet->undertime_3 = '';
						$timesheet->night_differential_3 = '';												

						$summary->undertime = '';
						$summary->legal_holiday = '';
						$summary->legal_holiday_overtime_night_diff = '';
						$summary->legal_holiday_overtime = '';
						$summary->legal_holiday_night_diff = '';
						$summary->special_holiday = '';
						$summary->special_holiday_overtime_night_diff = '';
						$summary->special_holiday_overtime = '';
						$summary->special_holiday_night_diff = '';		
						$summary->regular = '';									
						$summary->regular_overtime_night_diff = '';						
						$summary->regular_overtime = '';
						$summary->regular_night_differential = '';
						$summary->rest_day_legal_holiday = '';
						$summary->rest_day_legal_holiday_overtime_night_diff = '';						
						$summary->rest_day_legal_holiday_overtime = '';
						$summary->rest_day_legal_holiday_night_diff = '';
						$summary->rest_day_special_holiday = '';
						$summary->rest_day_special_holiday_overtime_night_diff = '';				
						$summary->rest_day_special_holiday_overtime = '';
						$summary->rest_day_special_holiday_night_diff = '';
						$summary->rest_day_overtime_night_diff = '';						
						$summary->rest_day_overtime = '';
						$summary->rest_day_night_differential = '';							

						if ($timesheet->save() ) {
							$summary->save();														
						}

				}					



			}
		
		}

		return Redirect::to('/search/timesheet/'.$id);

	}


	private function _updateSearchTimesheet($clocking = '', $clockingArr = array(), $clockingInArr = array(), $clockingOutArr = array()) {

		for($i = 0; $i < sizeof($clockingArr); $i++) {

			if ( !empty($clockingArr[$clocking]) ) {
				
				$timesheetId = $clockingArr["timesheetid"][$i];
				$dayDate = $clockingArr["daydate"][$i];
				$time = $clockingArr[$clocking][$i];

				$employeeSetting = Employeesetting::where('employee_id', Session::get('employeeSearchId'))->first();
				
				$summary = Summary::where('employee_id', Session::get('employeeSearchId'))
								  ->where('daydate', $dayDate)->first();					

				$schedule = Schedule::where('employee_id', Session::get('employeeSearchId'))
									->where('schedule_date', $dayDate)->first();
				
				$holiday = Holiday::where('holiday_date', $dayDate)->first();

		        $timesheet = Timesheet::where('employee_id', Session::get('employeeSearchId'))
		                              ->where('daydate', $dayDate)
		                              ->first();

				//$isFlexible = $employeeSetting->is_flexible;
				$scheduleStartTime = $schedule->start_time;	
				$scheduleEndTime = $schedule->end_time;
				$isFlexible = $employeeSetting->is_flexible;
				$hasBreak = $employeeSetting->has_break;
				$breakTime = $employeeSetting->break_time; //date('G', timestamp($employeeSetting->break_time));
				$hoursPerDay = $employeeSetting->hours_per_day;
				$halfOfhoursPerDay = ($hoursPerDay / 2);

				/*$scheduleStartTime = (!empty($schedule->start_time)) ? $schedule->start_time : '';
				$scheduleEndTime = (!empty($schedule->end_time)) ? $schedule->start_time : '';
				$isFlexible = (!empty($employeeSetting->is_flexible)) ? $schedule->start_time : '';
				$hasBreak = (!empty($employeeSetting->has_break)) ? $schedule->start_time : '';
				$breakTime = (!empty($employeeSetting->break_time)) ? $schedule->start_time : ''; //date('G', timestamp($employeeSetting->break_time));
				$hoursPerDay = (!empty($employeeSetting->hours_per_day)) ? $schedule->start_time : '';
				$halfOfhoursPerDay = ($hoursPerDay / 2);*/						
											                              

				//CHECK SCHEDULE IN AND SCHEDULE OUT IF IT IS GREATER THAN OR LESS THAN 
				/*if ( date('G', strtotime($timesheet->schedule_in)) > 
				       date('G', strtotime($timesheet->schedule_out)) ) {

					$checkSchedule = TRUE;

				} elseif ( date('G', strtotime($timesheet->schedule_in)) < 
				           date('G', strtotime($timesheet->schedule_out)) ) {

					$checkSchedule = FALSE;

				}*/


				if ( strtotime(date('H:i', strtotime($timesheet->schedule_in))) >
					 strtotime(date('H:i', strtotime($timesheet->schedule_out))) ) {

					$checkSchedule = TRUE;

				} elseif ( strtotime(date('H:i', strtotime($timesheet->schedule_in))) < 
						   strtotime(date('H:i', strtotime($timesheet->schedule_out))) ) {

					$checkSchedule = FALSE;

				}

				if($clocking === 'in1') {

					if( !empty($clockingInArr["in1"][$i]) && empty($clockingOutArr["out1"][$i]) ) {

						$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

					} elseif( !empty($clockingInArr["in1"][$i]) && !empty($clockingOutArr["out1"][$i]) ) {

						if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00

							$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));	

						} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

							$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));	

							if ( strtotime(date('H:i', strtotime($clockingInArr["in1"][$i]))) > 
								 strtotime(date('H:i', strtotime($clockingOutArr["out1"][$i]))) ) {

								$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));		

							} elseif ( strtotime(date('H:i', strtotime($clockingInArr["in1"][$i]))) < 
								       strtotime(date('H:i', strtotime($clockingOutArr["out1"][$i]))) ) {

								//TIME OUT - TIME IN
								$gVal = date('G', strtotime($clockingOutArr["out1"][$i])) - date('G', strtotime($clockingInArr["in1"][$i]));

								if($gVal < 0) { //IF NEGATIVE VALUE

									$scheduleInDate = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

								} else {

									$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

								}										

							}

						}

					} else {

						//$timesheet->clocking_status = 'open';							

					}


					if( !empty($time) && !empty($scheduleInDate) ) {

						//$scheduleInDate = date('Y-m-d', timestamp($scheduleInDate));
						$time = date('H:i:s', strtotime($time));

						$dateTimeFormatIn1 = $scheduleInDate.' '.$time;
						Session::put($dateTimeFormatIn1, $dateTimeFormatIn1);

					} else {

						$dateTimeFormatIn1 = '';
						Session::put($dateTimeFormatIn1, $dateTimeFormatIn1);

					}						

				} 

				if($clocking === 'in2') {
	
					if( !empty($clockingInArr["in2"][$i]) && empty($clockingOutArr["out2"][$i]) ) {

						$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

					} elseif( !empty($clockingInArr["in2"][$i]) && !empty($clockingOutArr["out2"][$i]) ) {

						if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00

							$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));	

						} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

							$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));	

							if ( strtotime(date('H:i', strtotime($clockingInArr["in2"][$i]))) > 
								 strtotime(date('H:i', strtotime($clockingOutArr["out2"][$i]))) ) {

								$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));		

							} elseif ( strtotime(date('H:i', strtotime($clockingInArr["in2"][$i]))) < 
								       strtotime(date('H:i', strtotime($clockingOutArr["out2"][$i]))) ) {

								//TIME OUT - TIME IN
								$gVal = date('G', strtotime($clockingOutArr["out2"][$i])) - date('G', strtotime($clockingInArr["in2"][$i]));

								if($gVal < 0) { //IF NEGATIVE VALUE

									$scheduleInDate = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

								} else {

									$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

								}										

							}

						}

					}


					if( !empty($time) && !empty($scheduleInDate) ) {

						//$scheduleInDate = date('Y-m-d', timestamp($scheduleInDate));
						$time = date('H:i:s', strtotime($time));

						$dateTimeFormatIn2 = $scheduleInDate.' '.$time;
						Session::put($dateTimeFormatIn2, $dateTimeFormatIn2);

					} else {

						$dateTimeFormatIn2 = '';
						Session::put($dateTimeFormatIn2, $dateTimeFormatIn2);

					}					
					
				}

				if($clocking === 'in3') {

					if( !empty($clockingInArr["in3"][$i]) && empty($clockingOutArr["out3"][$i]) ) {

						$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));	

					} elseif( !empty($clockingInArr["in3"][$i]) && !empty($clockingOutArr["out3"][$i]) ) {

						if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00

							$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));	

						} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

							$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));	

							if ( strtotime(date('H:i', strtotime($clockingInArr["in3"][$i]))) > 
								 strtotime(date('H:i', strtotime($clockingOutArr["out3"][$i]))) ) {

								$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));		

							} elseif ( strtotime(date('H:i', strtotime($clockingInArr["in3"][$i]))) < 
								       strtotime(date('H:i', strtotime($clockingOutArr["out3"][$i]))) ) {

								//TIME OUT - TIME IN
								$gVal = date('G', strtotime($clockingOutArr["out3"][$i])) - date('G', strtotime($clockingInArr["in3"][$i]));

								if($gVal < 0) { //IF NEGATIVE VALUE

									$scheduleInDate = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

								} else {

									$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

								}										

							}

						}

					}

					if( !empty($time) && !empty($scheduleInDate) ) {

						//$scheduleInDate = date('Y-m-d', timestamp($scheduleInDate));
						$time = date('H:i:s', strtotime($time));

						$dateTimeFormatIn3 = $scheduleInDate.' '.$time;
						Session::put($dateTimeFormatIn3, $dateTimeFormatIn3);

					} else {

						$dateTimeFormatIn3 = '';
						Session::put($dateTimeFormatIn3, $dateTimeFormatIn3);

					}						
								
				}

				if($clocking === 'out1') {

					if( !empty($clockingInArr["in1"][$i]) && empty($clockingOutArr["out1"][$i]) ) {

						$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));		

						//$timesheet->clocking_status = 'clock_in_1';					
											
					} elseif( !empty($clockingInArr["in1"][$i]) && !empty($clockingOutArr["out1"][$i]) ) {

						if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00
							
							if ( strtotime(date('H:i', strtotime($clockingInArr["in1"][$i]))) > 
								 strtotime(date('H:i', strtotime($clockingOutArr["out1"][$i]))) ) {

								//TIME OUT - TIME IN
								$gVal = date('G', strtotime($clockingOutArr["out1"][$i])) - date('G', strtotime($clockingInArr["in1"][$i]));

								if($gVal < 0) { //IF NEGATIVE VALUE

									$scheduleInDate = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));
									//$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_out));

								} else {

									$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

								}	


							} elseif ( strtotime(date('H:i', strtotime($clockingInArr["in1"][$i]))) < 
								       strtotime(date('H:i', strtotime($clockingOutArr["out1"][$i]))) ) {

								$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

							}


						} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

							if ( strtotime(date('H:i', strtotime($clockingInArr["in1"][$i]))) > 
								 strtotime(date('H:i', strtotime($clockingOutArr["out1"][$i]))) ) {

								//TIME OUT - TIME IN
								$gVal = date('G', strtotime($clockingOutArr["out1"][$i])) - date('G', strtotime($clockingInArr["in1"][$i]));

								if($gVal < 0) { //IF NEGATIVE VALUE

									$scheduleInDate = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

								} else {

									$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

								}	


							} elseif ( strtotime(date('H:i', strtotime($clockingInArr["in1"][$i]))) < 
								       strtotime(date('H:i', strtotime($clockingOutArr["out1"][$i]))) ) {

								$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

							}							

						}

						//$timesheet->clocking_status = 'clock_out_1';							

					}


					if( !empty($time) && !empty($scheduleInDate) ) {

						//$scheduleInDate = date('Y-m-d', timestamp($scheduleInDate));
						$time = date('H:i:s', strtotime($time));

						$dateTimeFormatOut1 = $scheduleInDate.' '.$time;

					} else {

						$dateTimeFormatOut1 = '';

					}								

				}

				if($clocking === 'out2') {
					
					if( !empty($clockingInArr["in2"][$i]) && empty($clockingOutArr["out2"][$i]) ) {

						$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));	

						//$timesheet->clocking_status = 'clock_in_2';					
											
					} elseif( !empty($clockingInArr["in2"][$i]) && !empty($clockingOutArr["out2"][$i]) ) {

						if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00
							
							if ( strtotime(date('H:i', strtotime($clockingInArr["in2"][$i]))) > 
								 strtotime(date('H:i', strtotime($clockingOutArr["out2"][$i]))) ) {

								//TIME OUT - TIME IN
								$gVal = date('G', strtotime($clockingOutArr["out2"][$i])) - date('G', strtotime($clockingInArr["in2"][$i]));

								if($gVal < 0) { //IF NEGATIVE VALUE

									$scheduleInDate = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));
									//$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_out));

								} else {

									$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

								}	


							} elseif ( strtotime(date('H:i', strtotime($clockingInArr["in2"][$i]))) < 
								       strtotime(date('H:i', strtotime($clockingOutArr["out2"][$i]))) ) {

								$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

							}


						} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

							if ( strtotime(date('H:i', strtotime($clockingInArr["in2"][$i]))) > 
								 strtotime(date('H:i', strtotime($clockingOutArr["out2"][$i]))) ) {

								//TIME OUT - TIME IN
								$gVal = date('G', strtotime($clockingOutArr["out2"][$i])) - date('G', strtotime($clockingInArr["in2"][$i]));

								if($gVal < 0) { //IF NEGATIVE VALUE

									$scheduleInDate = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

								} else {

									$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

								}	


							} elseif ( strtotime(date('H:i', strtotime($clockingInArr["in2"][$i]))) < 
								       strtotime(date('H:i', strtotime($clockingOutArr["out2"][$i]))) ) {

								$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

							}							

						}


						//$timesheet->clocking_status = 'clock_out_2';													

					}	

					if( !empty($time) && !empty($scheduleInDate) ) {

						//$scheduleInDate = date('Y-m-d', timestamp($scheduleInDate));
						$time = date('H:i:s', strtotime($time));

						$dateTimeFormatOut2 = $scheduleInDate.' '.$time;

					} else {

						$dateTimeFormatOut2 = '';

					}								

				}

				if($clocking === 'out3') {

					if( !empty($clockingInArr["in3"][$i]) && empty($clockingOutArr["out3"][$i]) ) {

						$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));	

						//$timesheet->clocking_status = 'clock_in_3';					
											
		
					} elseif( !empty($clockingInArr["in3"][$i]) && !empty($clockingOutArr["out3"][$i]) ) {

						if ( $checkSchedule ) { //IF SCHEDULE IN IS GREATER THAN SCHEDULE OUT - e.g 22:00 to 06:00
							
							if ( strtotime(date('H:i', strtotime($clockingInArr["in3"][$i]))) > 
								 strtotime(date('H:i', strtotime($clockingOutArr["out3"][$i]))) ) {

								//TIME OUT - TIME IN
								$gVal = date('G', strtotime($clockingOutArr["out3"][$i])) - date('G', strtotime($clockingInArr["in3"][$i]));

								if($gVal < 0) { //IF NEGATIVE VALUE

									$scheduleInDate = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));
									//$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_out));

								} else {

									$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

								}	


							} elseif ( strtotime(date('H:i', strtotime($clockingInArr["in3"][$i]))) < 
								       strtotime(date('H:i', strtotime($clockingOutArr["out3"][$i]))) ) {

								$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

							}


						} else {  //IF SCHEDULE IN IS LESS THAN THAN SCHEDULE OUT - e.g 8:00 to 17:00

							if ( strtotime(date('H:i', strtotime($clockingInArr["in3"][$i]))) > 
								 strtotime(date('H:i', strtotime($clockingOutArr["out3"][$i]))) ) {

								//TIME OUT - TIME IN
								$gVal = date('G', strtotime($clockingOutArr["out3"][$i])) - date('G', strtotime($clockingInArr["in3"][$i]));

								if($gVal < 0) { //IF NEGATIVE VALUE

									$scheduleInDate = date('Y-m-d', strtotime('+1 day', strtotime($timesheet->schedule_in)));

								} else {

									$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

								}	


							} elseif ( strtotime(date('H:i', strtotime($clockingInArr["in3"][$i]))) < 
								       strtotime(date('H:i', strtotime($clockingOutArr["out3"][$i]))) ) {

								$scheduleInDate = date('Y-m-d', strtotime($timesheet->schedule_in));

							}							

						}

						//$timesheet->clocking_status = 'clock_out_3';													

					}	


					if( !empty($time) && !empty($scheduleInDate) ) {

						//$scheduleInDate = date('Y-m-d', timestamp($scheduleInDate));
						$time = date('H:i:s', strtotime($time));

						$dateTimeFormatOut3 = $scheduleInDate.' '.$time;

					} else {

						$dateTimeFormatOut3 = '';

					}								

				}	

				/*if( !empty($time) && !empty($scheduleInDate) ) {

					//$scheduleInDate = date('Y-m-d', timestamp($scheduleInDate));
					$time = date('H:i:s', strtotime($time));

					$dateTimeFormat = $scheduleInDate.' '.$time;

				} else {

					$dateTimeFormat = '';

				}*/		

				if ($clocking === 'in1') {

					$timesheetTimeIn = $dateTimeFormatIn1;
					//$timesheetTimeOut = $timesheet->time_out_1;

					//WITH TARDINESS COMPUTATION
					if( ($dateTimeFormatIn1 !== '' && empty($timesheet->time_out_1)) && 
						(empty($timesheet->time_in_2) && empty($timesheet->time_out_2)) &&
						(empty($timesheet->time_in_3) && empty($timesheet->time_out_3)) ||

						($dateTimeFormatIn1 !== '' && !empty($timesheet->time_out_1)) && 
						(empty($timesheet->time_in_2) && empty($timesheet->time_out_2)) &&
						(empty($timesheet->time_in_3) && empty($timesheet->time_out_3)) ) {

							$timesheet->time_in_1 = $dateTimeFormatIn1;
							$timesheet->save();

					} 

					//RESET TARDINESS COMPUTATION
					if( ($dateTimeFormatIn1 === '' && empty($timesheet->time_out_1)) && 
						(empty($timesheet->time_in_2) && empty($timesheet->time_out_2)) &&
						(empty($timesheet->time_in_3) && empty($timesheet->time_out_3)) ||

						($dateTimeFormatIn1 === '' && !empty($timesheet->time_out_1)) && 
						(empty($timesheet->time_in_2) && empty($timesheet->time_out_2)) &&
						(empty($timesheet->time_in_3) && empty($timesheet->time_out_3)) ) {

							$timesheet->time_in_1 = '';
							$timesheet->save();

					}					

				} 		


				if($clocking === 'out1') {

					//$timesheetTimeIn = $timesheet->time_in_1;					
					//$timesheetTimeOut = $dateTimeFormatOut1;
					//Session::get($dateTimeFormatIn1)

					//WITH COMPUTATION
					if( (!empty($timesheet->time_in_1) && $dateTimeFormatOut1 !== '') && 
						(empty($timesheet->time_in_2) && empty($timesheet->time_out_2)) &&
						(empty($timesheet->time_in_3) && empty($timesheet->time_out_3)) ) {

							$timesheet->time_out_1 = $dateTimeFormatOut1;
							$timesheet->save();

					}

					//RESET TARDINESS COMPUTATION
					if( (!empty($timesheet->time_in_1) && $dateTimeFormatOut1 === '') && 
						(empty($timesheet->time_in_2) && empty($timesheet->time_out_2)) &&
						(empty($timesheet->time_in_3) && empty($timesheet->time_out_3)) ||

						(empty($timesheet->time_in_1) && $dateTimeFormatOut1 === '') && 
						(empty($timesheet->time_in_2) && empty($timesheet->time_out_2)) &&
						(empty($timesheet->time_in_3) && empty($timesheet->time_out_3)) ) {

							$timesheet->time_out_1 = '';
							$timesheet->save();

					}

				}


				if($clocking === 'in2') {

					$timesheetTimeIn = $dateTimeFormatIn2;
					$timesheetTimeOut = $timesheet->time_out_2;


				} 					


				if($clocking === 'out2') {


				} 				

				if($clocking === 'in3') {

					$timesheet->time_in_3 = $dateTimeFormatIn3;

				} 					
				

				if($clocking === 'out3') {

					$timesheet->time_out_3 = $dateTimeFormatOut3;

				} 	

				//$timesheet->save();

			}

		}

	}

	/*public function selectEmployeeByGroup() {

		$employeesController = new EmployeesController;
		
		$employees = $employeesController->employeeByGroup();
		

		$employeeArr[0] = 'Select Employee to';
		$employeeIdArr = array();
		
		if( !empty($employees) ) {

		    foreach($employees as $employeeVal) {
		    
		      $employeeArr[$employeeVal->id] = $employeeVal->firstname. ', ' .$employeeVal->lastname;
		      //$employeeIdArr[] = $employeeVal->id;

		  	}
		  	
		  	return $employeeArr;
		  
		}

	}*/

	public function updateServerTime() {

	    if(Request::ajax()){
	        return date('H:').date('i').'<span class="" style="font-size:21px; margin-top:0px;">'.date('s').'</span>';
	    }
	}

	public function getServerDateTime() {
		
		if(Request::ajax()){

			//header("Cache-Control: no-cache, must-revalidate");
			//header("Expires: Mon, 01 Jan 2000 01:00:00 GMT");
		    
		    /*Response::header('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate');
		    Response::header('Pragma', 'no-cache');
		    Response::header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');*/			

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


	public function redrawSearchTimesheet()
	{

		$dataArr = $this->init();
		$dayDateArr = Session::get('dayDateArr');

		//Fix to pagination problem
		/*$dayDateArrCount = count($dayDateArr);
		
		$date = new DateTime($dayDateArr[$dayDateArrCount-1]);
		$date->add(new DateInterval('P1D'));
		array_push($dayDateArr, $date->format('Y-m-d'));*/

		if( Request::ajax() ) {

			$timesheet = new Timesheet;
			//$searchTimesheetJson = $timesheet->searchTimesheetJson(Session::get('employeeSearchId'), Session::get('dayDateArr'));	
			$searchTimesheetJson = $timesheet->searchTimesheetJson(Session::get('employeeSearchId'), $dayDateArr);	
			
			return $searchTimesheetJson;

		}

	}

	public function redrawPreviousSearchTimesheet()
	{

		$dataArr = $this->init();
		//$dayDateArr = Session::get('dayDateArr');
		//return var_dump(Session::get('previousSearchTimesheets'));

		$previousCutoffdayDateArr = Session::get('previousCutoffdayDateArr');

		//Fix to pagination problem
		/*$dayDateArrCount = count($dayDateArr);
		
		$date = new DateTime($dayDateArr[$dayDateArrCount-1]);
		$date->add(new DateInterval('P1D'));
		array_push($dayDateArr, $date->format('Y-m-d'));*/

		//return dd($dataArr["previousSearchTimesheets"]);

		/*foreach($dataArr["previousSearchTimesheets"] as $searchTimesheet) {

			$previousCutoffDayDateArr[] = $searchTimesheet->daydate;

		}*/

		if( Request::ajax() ) {

			$timesheet = new Timesheet;
			//$searchTimesheetJson = $timesheet->searchTimesheetJson(Session::get('employeeSearchId'), Session::get('dayDateArr'));	
			$searchTimesheetJson = $timesheet->searchTimesheetJson(Session::get('employeeSearchId'), $previousCutoffdayDateArr);	
			
			return $searchTimesheetJson;

		}

	}	

	public function timeClocking() {

		$data = Input::all();	

		$dataArr = $this->init();

		$cutOffSetting = $dataArr["cutOffSetting"];
		$employee = $dataArr["employee"];
		$user = $dataArr["user"];
		$userGroup = $dataArr["userGroup"];
		$group = $dataArr["group"];		
		$employeeSetting = $dataArr["employeeSetting"];
		$timesheets = $dataArr["timesheets"];
		$timesheet = $dataArr["timesheet"];
		$timesheetYesterday = $dataArr["timesheetYesterday"];
		$summaryYesterday = $dataArr["summaryYesterday"];
		$scheduleYesterday = $dataArr["scheduleYesterday"];
		$holidayYesterday = $dataArr["holidayYesterday"];		
		$summary = $dataArr["summary"];
		$summaries = $dataArr["summaries"];
		$schedule = $dataArr["schedule"];		
		$holiday = $dataArr["holiday"];

		//return dd($employeeSetting);

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

				//DB TABLE DATA

				//$isFlexible = (!empty($employeeSetting)) ? $employeeSetting->is_flexible : '';
				$isFlexible = $employeeSetting->is_flexible;
				$scheduleStartTime = $schedule->start_time;

				if(	$timesheetYesterday->clocking_status !== "clock_in_1" ||
					$timesheetYesterday->clocking_status !== "clock_in_2" ||
					$timesheetYesterday->clocking_status !== "clock_in_3" ) {			

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
								//$summary->save();														
								return Redirect::to('/redraw/timesheet');

							}						

						} 

					}

					//CHECK CLOCKING STATUS: CLOCK_OUT_1
					if ( $timesheet->clocking_status === "clock_out_1" ) {

						$timesheet->clocking_status = "clock_in_2";
						$timesheet->time_in_2 = $timesheetTimeIn;

						if ($timesheet->save() ) {
							//$summary->save();														
							return Redirect::to('/redraw/timesheet');

						}					

					}

					//CHECK CLOCKING STATUS: CLOCK_OUT_2
					if ( $timesheet->clocking_status === "clock_out_2" ) {

						$timesheet->clocking_status = "clock_in_3";
						$timesheet->time_in_3 = $timesheetTimeIn;

						if ($timesheet->save() ) {
							//$summary->save();														
							return Redirect::to('/redraw/timesheet');

						}					

					}


					//CHECK CLOCKING STATUS: CLOCK_OUT_3									
					if ( $timesheet->clocking_status === "clock_out_3" ) {

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

				//DB TABLE DATA
				
				if ( $timesheetYesterday->clocking_status === "clock_in_1" ) {

					$timesheetTimeIn = $timesheetYesterday->time_in_1;

				} elseif ( $timesheetYesterday->clocking_status === "clock_in_2" ) {

					$timesheetTimeIn = $timesheetYesterday->time_in_2;

				} elseif ( $timesheetYesterday->clocking_status === "clock_in_3" ) {

					$timesheetTimeIn = $timesheetYesterday->time_in_3;					

				} elseif ( $timesheet->clocking_status === "clock_in_1" ) {

					$timesheetTimeIn = $timesheet->time_in_1;

				} elseif ( $timesheet->clocking_status === "clock_in_2" ) {

					$timesheetTimeIn = $timesheet->time_in_2;

				} elseif ( $timesheet->clocking_status === "clock_in_3" ) {

					$timesheetTimeIn = $timesheet->time_in_3;					

				}			

				if ( $timesheetYesterday->clocking_status === "clock_in_1" ||
					$timesheetYesterday->clocking_status === "clock_in_2" ||
					$timesheetYesterday->clocking_status === "clock_in_3" ) {

					$scheduleStartTime = $scheduleYesterday->start_time;
					$scheduleEndTime = $scheduleYesterday->end_time;
					
				} elseif ( $timesheet->clocking_status === "clock_in_1" ||
					$timesheet->clocking_status === "clock_in_2" ||
					$timesheet->clocking_status === "clock_in_3" ) {

					$scheduleStartTime = $schedule->start_time;
					$scheduleEndTime = $schedule->end_time;
				
				}

				$isFlexible = $employeeSetting->is_flexible;
				$hasBreak = $employeeSetting->has_break;
				$breakTime = $employeeSetting->break_time; //date('G', timestamp($employeeSetting->break_time));
				$hoursPerDay = $employeeSetting->hours_per_day;
				
				$halfOfhoursPerDay = ($hoursPerDay / 2);

				if( $timesheetYesterday->clocking_status !== "open" ||
					$timesheetYesterday->clocking_status !== "clock_out_1" ||
					$timesheetYesterday->clocking_status !== "clock_out_2" ||
					$timesheetYesterday->clocking_status !== "clock_out_3" ) {	

					/**
					*
					* TODAY: CLOCKING STATUS
					*
					*/

					//CHECK CLOCKING STATUS: OPEN
					if ( $timesheet->clocking_status === "clock_in_1" ) {

						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {

									$timesheet->clocking_status = "clock_out_1";
									$timesheet->time_out_1 = $timesheetTimeOut;	

									//TESTER
									/*$scheduleStartTime = "2015-09-22 22:00:00";									
									$scheduleEndTime = "2015-09-23 06:00:00";
									
									$timesheetTimeIn = "2015-09-22 01:00:00";
									$timesheetTimeOut = "2015-09-23 03:34:55";*/

									//WITH COMPLETE COMPUTATION: TRUE									 
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));

									echo "\n\n\n\n\n";

									/*echo dd(nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange()));
									return;
									die();*/									

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheet->total_hours_1 = $totalHours;
									echo "\n";

									//GET WORKHOURS
									echo $timesheet->work_hours_1 = $workHours;
									echo "\n";

									$hasNightDiff = FALSE;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}


									//GET NIGHTDIFF;									
									if( $scheduleHasNightDiff !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$hasNightDiff = TRUE;
											$timesheet->night_differential_1 = $nightDiff;

											echo 'Night Differential '.$nightDiff;
											echo "\n";

										}

									} else {
										
										//Overtime + Night Differential 1

										//echo strtotime($timesheetTimeOut);
										//echo '<br />';
										//echo strtotime($scheduleEndTime);

										if ( strtotime($timesheetTimeOut) > strtotime($scheduleEndTime) ) {

											$overtimeNightDiff = nightDifferential($scheduleEndTime, $timesheetTimeOut, nightDifferentialRange());

											if( $overtimeNightDiff !== FALSE ) {

												$timesheet->night_differential_1 = $overtimeNightDiff;

												echo "\n";
												echo 'overtimeNightDiff '.$overtimeNightDiff;
												echo "\n";

											}

										}

									}

									//return;
									//die();

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheet->total_overtime_1 = $overtimeHours;
										echo "\n";

									} 								

									//GET UNDERTIME
									echo $timesheet->undertime_1 = $underTimeHours;
									$summary->undertime = $underTimeHours;
									echo "\n";	

									//return;
									//die();

									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summary->legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->legal_holiday_overtime = $overtimeHours;

											}*/

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->legal_holiday_night_diff = $nightDiff;

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summary->special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->special_holiday_overtime = $overtimeHours;
												
											}*/

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->special_holiday_night_diff = $nightDiff;

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day


										echo "HOLIDAY: FALSE - Regular Day \n";

										echo $summary->regular = $workHours;									

										/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summary->regular_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summary->regular_overtime = $overtimeHours;

										}*/				

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summary->regular_night_differential = $nightDiff;

										}								

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										return Redirect::to('/redraw/timesheet');

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {

									$timesheet->clocking_status = "clock_out_1";
									$timesheet->time_out_1 = $timesheetTimeOut;	
									
									//WITH COMPLETE COMPUTATION: TRUE
									//extract($this->processTimesheetComputation($isFlexible = 0, $timesheet->time_in_1, $timesheet->time_out_1, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));									

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheet->total_hours_1 = $totalHours;
									echo "\n";

									//GET WORKHOURS
									echo $timesheet->work_hours_1 = $workHours;
									echo "\n";

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									/*if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}*/					

									$hasNightDiff = FALSE;			

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}

									//GET NIGHTDIFF;
									if( $scheduleHasNightDiff !== FALSE ) { //TRUE
	
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$hasNightDiff = TRUE;
											$timesheet->night_differential_1 = $nightDiff;

										}

										echo "\n";

									}

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheet->total_overtime_1 = $overtimeHours;
										echo "\n";

									} 
									
									//GET UNDERTIME
									echo $timesheet->undertime_1 = $underTimeHours;
									$summary->undertime = $underTimeHours;
									echo "\n";									
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summary->rest_day_legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summary->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->rest_day_legal_holiday_overtime = $overtimeHours;

											}*/

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_legal_holiday_night_diff = $nightDiff;

											}
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summary->rest_day_special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summary->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->rest_day_special_holiday_overtime = $overtimeHours;
												
											}*/

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff;

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day



										echo "HOLIDAY: FALSE - Regular Day \n";

										$summary->rest_day = $workHours;									

										/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summary->rest_day_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summary->rest_day_overtime = $overtimeHours;

										}*/				

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summary->rest_day_night_differential = $nightDiff;

										}									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										return Redirect::to('/redraw/timesheet');

									}															


								}								

							}
												

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							$timesheet->clocking_status = "clock_out_1";
							$timesheet->time_out_1 = $timesheetTimeOut;	
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							echo $timesheet->total_hours_1 = totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							echo $timesheet->work_hours_1 = workHours($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							echo $timesheet->undertime_1 = underTimeHours($timesheetTimeOut, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff = nightDiff($timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
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

									//TESTER
									/*$scheduleStartTime = "2015-09-22 22:00:00";									
									$scheduleEndTime = "2015-09-23 06:00:00";*/
									
									//$timesheetTimeIn = "2015-09-22 17:00:00";
									//$timesheetTimeOut = "2015-09-23 03:34:55";
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheet->total_hours_2 = $totalHours;
									echo "\n";

									//GET WORKHOURS
									echo $timesheet->work_hours_2 = $workHours;
									echo "\n";

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									/*if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}*/

									//GET NIGHTDIFF;
									/*if( $scheduleHasNightDiff !== FALSE ) { //TRUE
	
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$timesheet->night_differential_2 = $nightDiff;

										}

										echo "\n";

									}*/

									//GET NIGHTDIFF;
									//Overtime + Night Differential 2
									$overtimeNightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

									if( $overtimeNightDiff !== FALSE ) {

										$timesheet->night_differential_2 = $overtimeNightDiff;

										echo "\n";
										echo 'overtimeNightDiff '.$overtimeNightDiff;
										echo "\n";


									}

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheet->total_overtime_2 = $overtimeHours;
										echo "\n";

									} 

									//return;
									//die();
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summary->legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->legal_holiday_overtime = $overtimeHours;

											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->legal_holiday_night_diff = $nightDiff;

											}*/
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summary->special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->special_holiday_overtime = $overtimeHours;
												
											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->special_holiday_night_diff = $nightDiff;

											}*/										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										echo "HOLIDAY: FALSE - Regular Day \n";

										$summary->regular = $workHours;

										/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summary->regular_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summary->regular_overtime = $overtimeHours;

										}				

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summary->regular_night_differential = $nightDiff;

										}*/									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										return Redirect::to('/redraw/timesheet');

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {

									$timesheet->clocking_status = "clock_out_2";
									$timesheet->time_out_2 = $timesheetTimeOut;	
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheet->total_hours_2 = $totalHours;
									echo "\n";

									//GET WORKHOURS
									echo $timesheet->work_hours_2 = $workHours;
									echo "\n";

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}

									//GET NIGHTDIFF;
									if( $scheduleHasNightDiff !== FALSE ) { //TRUE
	
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$timesheet->night_differential_2 = $nightDiff;

										}

										echo "\n";

									}

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheet->total_overtime_2 = $overtimeHours;
										echo "\n";

									} 
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summary->rest_day_legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summary->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->rest_day_legal_holiday_overtime = $overtimeHours;

											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_legal_holiday_night_diff = $nightDiff;

											}*/
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summary->rest_day_special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summary->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->rest_day_special_holiday_overtime = $overtimeHours;
												
											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff;

											}*/										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										echo "HOLIDAY: FALSE - Regular Day \n";

										$summary->rest_day = $workHours;

										/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summary->rest_day_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summary->rest_day_overtime = $overtimeHours;

										}				

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summary->rest_day_night_differential = $nightDiff;

										}*/									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										return Redirect::to('/redraw/timesheet');

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							$timesheet->clocking_status = "clock_out_2";
							$timesheet->time_out_2 = $timesheetTimeOut;	
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							echo $timesheet->total_hours_2 = totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							echo $timesheet->work_hours_2 = workHours($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							echo $timesheet->undertime_2 = underTimeHours($timesheetTimeOut, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff = nightDiff($timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								return Redirect::to('/redraw/timesheet');

							}						

						}

					}


					//CHECK CLOCKING STATUS: CLOCK_IN_3
					if ( $timesheet->clocking_status === "clock_in_3" ) {	

						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($schedule->start_time) && $schedule->start_time !== '0000-00-00 00:00:00' && $schedule->start_time !== '') &&
							     (!empty($schedule->end_time) && $schedule->end_time !== '0000-00-00 00:00:00' && $schedule->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $schedule->rest_day !== 1 ) {

									$timesheet->clocking_status = "clock_out_3";
									$timesheet->time_out_3 = $timesheetTimeOut;	

									//TESTER
									/*$scheduleStartTime = "2015-09-22 22:00:00";									
									$scheduleEndTime = "2015-09-23 06:00:00";*/
									
									//$timesheetTimeIn = "2015-09-23 02:00:00";
									//$timesheetTimeOut = "2015-09-23 09:34:55";									
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheet->total_hours_3 = $totalHours;
									echo "\n";

									//GET WORKHOURS
									echo $timesheet->work_hours_3 = $workHours;
									echo "\n";

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									/*if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}*/

									//GET NIGHTDIFF;
									/*if( $scheduleHasNightDiff !== FALSE ) { //TRUE
	
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$timesheet->night_differential_3 = $nightDiff;

										}

										echo "\n";

									}*/

									//GET NIGHTDIFF;
									//Overtime + Night Differential 3
									$overtimeNightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

									if( $overtimeNightDiff !== FALSE ) {

										$timesheet->night_differential_3 = $overtimeNightDiff;

										echo "\n";
										echo 'overtimeNightDiff '.$overtimeNightDiff;
										echo "\n";


									}

									//return;
									//die();									

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheet->total_overtime_3 = $overtimeHours;
										echo "\n";

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summary->legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summary->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->legal_holiday_overtime = $overtimeHours;

											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->legal_holiday_night_diff = $nightDiff;

											}*/
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summary->special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summary->special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->special_holiday_overtime = $overtimeHours;
												
											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->special_holiday_night_diff = $nightDiff;

											}*/										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										echo "HOLIDAY: FALSE - Regular Day \n";

										$summary->regular = $workHours;

										/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summary->regular_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summary->regular_overtime = $overtimeHours;

										}				

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summary->regular_night_differential = $nightDiff;

										}*/									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										return Redirect::to('/redraw/timesheet');

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $schedule->rest_day === 1 ) {

									$timesheet->clocking_status = "clock_out_3";
									$timesheet->time_out_3 = $timesheetTimeOut;	
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheet->total_hours_3 = $totalHours;
									echo "\n";

									//GET WORKHOURS
									echo $timesheet->work_hours_3 = $workHours;
									echo "\n";

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}

									//GET NIGHTDIFF;
									if( $scheduleHasNightDiff !== FALSE ) { //TRUE
	
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$timesheet->night_differential_3 = $nightDiff;

										}

										echo "\n";

									}

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheet->total_overtime_3 = $overtimeHours;
										echo "\n";

									}
																	 
									if ( !empty($holiday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summary->rest_day_legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summary->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->rest_day_legal_holiday_overtime = $overtimeHours;

											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_legal_holiday_night_diff = $nightDiff;

											}*/
										
										} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summary->rest_day_special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summary->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summary->rest_day_special_holiday_overtime = $overtimeHours;
												
											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summary->rest_day_special_holiday_night_diff = $nightDiff;

											}*/										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										echo "HOLIDAY: FALSE - Regular Day \n";

										$summary->rest_day = $workHours;

										/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summary->rest_day_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summary->rest_day_overtime = $overtimeHours;

										}				

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summary->rest_day_night_differential = $nightDiff;

										}*/									

									}
				
									if ($timesheet->save() ) {
										$summary->save();
										return Redirect::to('/redraw/timesheet');

									}								

								}								

							}
													
						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							$timesheet->clocking_status = "clock_out_3";
							$timesheet->time_out_3 = $timesheetTimeOut;	
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							echo $timesheet->total_hours_3 = totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							echo $timesheet->work_hours_3 = workHours($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							echo $timesheet->undertime_3 = underTimeHours($timesheetTimeOut, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff = nightDiff($timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime);																

							if ($timesheet->save() ) {
								$summary->save();
								return Redirect::to('/redraw/timesheet');

							}						

						}

					}

					/**
					*
					* YESTERDAY: CLOCKING STATUS
					*
					*/

					//CHECK CLOCKING STATUS: OPEN
					if ( $timesheetYesterday->clocking_status === "clock_in_1" ) {

						/*echo $isFlexible;
						echo "\n";
						echo $timesheetTimeIn;				
						echo "\n";
						echo $timesheetTimeOut;
						echo "\n";
						echo $scheduleEndTime;
						echo "\n";
						echo $breakTime;
						echo "\n";
						echo $hoursPerDay;
						echo "\n";				
						echo $halfOfhoursPerDay;
						echo "\n";									
						echo $hasBreak;
						echo "\n";
						return;*/
						

						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($scheduleYesterday->start_time) && $scheduleYesterday->start_time !== '0000-00-00 00:00:00' && $scheduleYesterday->start_time !== '') &&
							     (!empty($scheduleYesterday->end_time) && $scheduleYesterday->end_time !== '0000-00-00 00:00:00' && $scheduleYesterday->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $scheduleYesterday->rest_day !== 1 ) {

									$timesheetYesterday->clocking_status = "clock_out_1";
									$timesheetYesterday->time_out_1 = $timesheetTimeOut;	

									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));			

									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheetYesterday->total_hours_1 = $totalHours;
									echo "\n";

									//GET WORKHOURS
									echo $timesheetYesterday->work_hours_1 = $workHours;
									echo "\n";

									$hasNightDiff = FALSE;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}

									//GET NIGHTDIFF;
									if( $scheduleHasNightDiff !== FALSE ) { //TRUE
										
										//Night Differential 1
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$hasNightDiff = TRUE;
											$timesheetYesterday->night_differential_1 = $nightDiff;

											echo 'Night Differential '.$nightDiff;
											echo "\n";

										}

									} else {

										//Overtime + Night Differential 1
										echo $overtimeNightDiff = nightDifferential($scheduleEndTime, $timesheetTimeOut, nightDifferentialRange());

										if( $overtimeNightDiff !== FALSE ) {

											$timesheetYesterday->night_differential_1 = $overtimeNightDiff;

											echo "\n";
											echo 'overtimeNightDiff '.$overtimeNightDiff;
											echo "\n";

										}

									}

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheetYesterday->total_overtime_1 = $overtimeHours;
										echo "\n";

									}
									
									//GET UNDERTIME
									echo $timesheetYesterday->undertime_1 = $underTimeHours;
									$summaryYesterday->undertime = $underTimeHours;
									echo "\n";
																	 
									if ( !empty($holidayYesterday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holidayYesterday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summaryYesterday->legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summaryYesterday->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->legal_holiday_overtime = $overtimeHours;

											}*/

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->legal_holiday_night_diff = $nightDiff;

											}
										
										} elseif ( 'Special non-working day' === $holidayYesterday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summaryYesterday->special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summaryYesterday->special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->special_holiday_overtime = $overtimeHours;
												
											}*/

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->special_holiday_night_diff = $nightDiff;

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day


										echo "HOLIDAY: FALSE - Regular Day \n";

										echo $summaryYesterday->regular = $workHours;									

										/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summaryYesterday->regular_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summaryYesterday->regular_overtime = $overtimeHours;

										}*/					

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summaryYesterday->regular_night_differential = $nightDiff;

										}								

									}
				
									if ($timesheetYesterday->save() ) {
										$summaryYesterday->save();
										return Redirect::to('/redraw/timesheet');

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $scheduleYesterday->rest_day === 1 ) {

									$timesheetYesterday->clocking_status = "clock_out_1";
									$timesheetYesterday->time_out_1 = $timesheetTimeOut;	

									//WITH COMPLETE COMPUTATION: TRUE
									//return $this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheetYesterday->total_hours_1 = $totalHours;
									echo "\n";


									//GET WORKHOURS
									echo $timesheetYesterday->work_hours_1 = $workHours;
									echo "\n";

									$hasNightDiff = FALSE;

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}

									//GET NIGHTDIFF;
									if( $scheduleHasNightDiff !== FALSE ) { //TRUE
	
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$hasNightDiff = TRUE;
											$timesheetYesterday->night_differential_1 = $nightDiff;

										}

										echo "\n";

									}

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheetYesterday->total_overtime_1 = $overtimeHours;
										echo "\n";

									}
								
									//GET UNDERTIME
									echo $timesheetYesterday->undertime_1 = $underTimeHours;
									$summaryYesterday->undertime = $underTimeHours;
									echo "\n";

									if ( !empty($holidayYesterday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holidayYesterday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summaryYesterday->rest_day_legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summaryYesterday->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->rest_day_legal_holiday_overtime = $overtimeHours;

											}*/

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->rest_day_legal_holiday_night_diff = $nightDiff;

											}
										
										} elseif ( 'Special non-working day' === $holidayYesterday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summaryYesterday->rest_day_special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summaryYesterday->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->rest_day_special_holiday_overtime = $overtimeHours;
												
											}*/

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->rest_day_special_holiday_night_diff = $nightDiff;

											}										

										}

									} else { //HOLIDAY: FALSE - Regular Day



										echo "HOLIDAY: FALSE - Regular Day \n";

										$summaryYesterday->rest_day = $workHours;	


										/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summaryYesterday->rest_day_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summaryYesterday->rest_day_overtime = $overtimeHours;

										}*/					

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summaryYesterday->rest_day_night_differential = $nightDiff;

										}								

									}
				
									if ($timesheetYesterday->save() ) {
										$summaryYesterday->save();
										return Redirect::to('/redraw/timesheet');

									}															


								}								

							}
												

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							$timesheetYesterday->clocking_status = "clock_out_1";
							$timesheetYesterday->time_out_1 = $timesheetTimeOut;	
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							echo $timesheetYesterday->total_hours_1 = totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							echo $timesheetYesterday->work_hours_1 = workHours($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							echo $timesheetYesterday->undertime_1 = underTimeHours($timesheetTimeOut, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff = nightDiff($timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime);																

							if ($timesheetYesterday->save() ) {
								$summaryYesterday->save();
								return Redirect::to('/redraw/timesheet');

							}						

						}

					}


					//CHECK CLOCKING STATUS: CLOCK_IN_2
					if ( $timesheetYesterday->clocking_status === "clock_in_2" ) {

						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($scheduleYesterday->start_time) && $scheduleYesterday->start_time !== '0000-00-00 00:00:00' && $scheduleYesterday->start_time !== '') &&
							     (!empty($scheduleYesterday->end_time) && $scheduleYesterday->end_time !== '0000-00-00 00:00:00' && $scheduleYesterday->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $scheduleYesterday->rest_day !== 1 ) {

									$timesheetYesterday->clocking_status = "clock_out_2";
									$timesheetYesterday->time_out_2 = $timesheetTimeOut;	
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheetYesterday->total_hours_2 = $totalHours;
									echo "\n";

									//GET WORKHOURS
									echo $timesheetYesterday->work_hours_2 = $workHours;
									echo "\n";

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									/*if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}*/

									//GET NIGHTDIFF;
									/*if( $scheduleHasNightDiff !== FALSE ) { //TRUE
	
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$timesheetYesterday->night_differential_2 = $nightDiff;

										}

										echo "\n";

									}*/

									//GET NIGHTDIFF;
									//Overtime + Night Differential 2
									$overtimeNightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

									if( $overtimeNightDiff !== FALSE ) {

										$timesheetYesterday->night_differential_2 = $overtimeNightDiff;

										echo "\n";
										echo 'overtimeNightDiff '.$overtimeNightDiff;
										echo "\n";


									}									

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheetYesterday->total_overtime_2 = $overtimeHours;
										echo "\n";

									}
																	 
									if ( !empty($holidayYesterday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holidayYesterday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summaryYesterday->legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summaryYesterday->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->legal_holiday_overtime = $overtimeHours;

											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->legal_holiday_night_diff = $nightDiff;

											}*/
										
										} elseif ( 'Special non-working day' === $holidayYesterday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summaryYesterday->special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summaryYesterday->special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->special_holiday_overtime = $overtimeHours;
												
											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->special_holiday_night_diff = $nightDiff;

											}*/										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										echo "HOLIDAY: FALSE - Regular Day \n";

										$summaryYesterday->regular = $workHours;

										/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summaryYesterday->regular_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summaryYesterday->regular_overtime = $overtimeHours;

										}				

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summaryYesterday->regular_night_differential = $nightDiff;

										}*/									

									}
				
									if ($timesheetYesterday->save() ) {
										$summaryYesterday->save();
										return Redirect::to('/redraw/timesheet');

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $scheduleYesterday->rest_day === 1 ) {

									$timesheetYesterday->clocking_status = "clock_out_2";
									$timesheetYesterday->time_out_2 = $timesheetTimeOut;	
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheetYesterday->total_hours_2 = $totalHours;
									echo "\n";

									//GET WORKHOURS
									echo $timesheetYesterday->work_hours_2 = $workHours;
									echo "\n";

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}

									//GET NIGHTDIFF;
									if( $scheduleHasNightDiff !== FALSE ) { //TRUE
	
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$timesheetYesterday->night_differential_2 = $nightDiff;

										}

										echo "\n";

									}

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheetYesterday->total_overtime_2 = $overtimeHours;
										echo "\n";

									}								 
									if ( !empty($holidayYesterday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holidayYesterday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summaryYesterday->rest_day_legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summaryYesterday->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->rest_day_legal_holiday_overtime = $overtimeHours;

											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->rest_day_legal_holiday_night_diff = $nightDiff;

											}*/
										
										} elseif ( 'Special non-working day' === $holidayYesterday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summaryYesterday->rest_day_special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summaryYesterday->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->rest_day_special_holiday_overtime = $overtimeHours;
												
											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->rest_day_special_holiday_night_diff = $nightDiff;

											}*/										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										echo "HOLIDAY: FALSE - Regular Day \n";

										$summaryYesterday->rest_day = $workHours;

										/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summaryYesterday->rest_day_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summaryYesterday->rest_day_overtime = $overtimeHours;

										}				

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summaryYesterday->rest_day_night_differential = $nightDiff;

										}*/									

									}
				
									if ($timesheetYesterday->save() ) {
										$summaryYesterday->save();
										return Redirect::to('/redraw/timesheet');

									}								

								}								

							}
													

						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							$timesheetYesterday->clocking_status = "clock_out_2";
							$timesheetYesterday->time_out_2 = $timesheetTimeOut;	
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							echo $timesheetYesterday->total_hours_2 = totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							echo $timesheetYesterday->work_hours_2 = workHours($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							echo $timesheetYesterday->undertime_2 = underTimeHours($timesheetTimeOut, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff = nightDiff($timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime);																

							if ($timesheetYesterday->save() ) {
								$summaryYesterday->save();
								return Redirect::to('/redraw/timesheet');

							}						

						}

					}


					//CHECK CLOCKING STATUS: CLOCK_IN_3
					if ( $timesheetYesterday->clocking_status === "clock_in_3" ) {	

						// CHECK IF FLEXIBLE SCHEDULE: FALSE
						if ( !$isFlexible ) {

							//CHECK SCHEDULE : TRUE
							if ( (!empty($scheduleYesterday->start_time) && $scheduleYesterday->start_time !== '0000-00-00 00:00:00' && $scheduleYesterday->start_time !== '') &&
							     (!empty($scheduleYesterday->end_time) && $scheduleYesterday->end_time !== '0000-00-00 00:00:00' && $scheduleYesterday->end_time !== '') ) {

								//CHECK IF REST DAY: FALSE							
								if ( $scheduleYesterday->rest_day !== 1 ) {

									$timesheetYesterday->clocking_status = "clock_out_3";
									$timesheetYesterday->time_out_3 = $timesheetTimeOut;	
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheetYesterday->total_hours_3 = $totalHours;
									echo "\n";

									//GET WORKHOURS
									echo $timesheetYesterday->work_hours_3 = $workHours;
									echo "\n";

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									/*if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}*/

									//GET NIGHTDIFF;
									/*if( $scheduleHasNightDiff !== FALSE ) { //TRUE
	
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$timesheetYesterday->night_differential_3 = $nightDiff;

										}

										echo "\n";

									}*/

									//GET NIGHTDIFF;
									//Overtime + Night Differential 3
									$overtimeNightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

									if( $overtimeNightDiff !== FALSE ) {

										$timesheetYesterday->night_differential_3 = $overtimeNightDiff;

										echo "\n";
										echo 'overtimeNightDiff '.$overtimeNightDiff;
										echo "\n";

									}									

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheetYesterday->total_overtime_3 = $overtimeHours;
										echo "\n";

									}
																	 
									if ( !empty($holidayYesterday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holidayYesterday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summaryYesterday->legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summaryYesterday->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->legal_holiday_overtime = $overtimeHours;

											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->legal_holiday_night_diff = $nightDiff;

											}*/
										
										} elseif ( 'Special non-working day' === $holidayYesterday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summaryYesterday->special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summaryYesterday->special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->special_holiday_overtime = $overtimeHours;
												
											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->special_holiday_night_diff = $nightDiff;

											}*/										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										echo "HOLIDAY: FALSE - Regular Day \n";

										$summaryYesterday->regular = $workHours;

										if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summaryYesterday->regular_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summaryYesterday->regular_overtime = $overtimeHours;

										}				

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summaryYesterday->regular_night_differential = $nightDiff;

										}									

									}
				
									if ($timesheetYesterday->save() ) {
										$summaryYesterday->save();
										return Redirect::to('/redraw/timesheet');

									}

								//CHECK IF REST DAY: TRUE							
								} elseif ( $scheduleYesterday->rest_day === 1 ) {

									$timesheetYesterday->clocking_status = "clock_out_3";
									$timesheetYesterday->time_out_3 = $timesheetTimeOut;	
									
									//WITH COMPLETE COMPUTATION: TRUE
									extract($this->processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak));
									
									//GET TOTALHOURS											
									//GET TOTALHOURS WITH OVERTIME
									echo $timesheetYesterday->total_hours_3 = $totalHours;
									echo "\n";

									//GET WORKHOURS
									echo $timesheetYesterday->work_hours_3 = $workHours;
									echo "\n";

									//CHECK SCHEDULE IF HAS NIGHT DIFFERENTIAL
									if ( nightDifferential($scheduleStartTime, $scheduleEndTime, nightDifferentialRange()) ) {

										$scheduleHasNightDiff = TRUE;

									} else {

										$scheduleHasNightDiff = FALSE;

									}

									//GET NIGHTDIFF;
									if( $scheduleHasNightDiff !== FALSE ) { //TRUE
	
										echo $nightDiff = nightDifferential($timesheetTimeIn, $timesheetTimeOut, nightDifferentialRange());

										if( $nightDiff !== FALSE ) {

											$timesheetYesterday->night_differential_3 = $nightDiff;

										}

										echo "\n";

									}

									//GET OVERTIME													
									if($hasOvertime) {

										echo $timesheetYesterday->total_overtime_3 = $overtimeHours;
										echo "\n";

									}
																	 
									if ( !empty($holidayYesterday) ) { //HOLIDAY: TRUE

										echo "HOLIDAY: TRUE \n";

										if ( 'Regular holiday' === $holidayYesterday->holiday_type ) { //Regular holiday

											echo "Regular holiday \n";

											echo $summaryYesterday->rest_day_legal_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //HASOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = number_format($overtimeHours + $nightDiff, 2);
												$summaryYesterday->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;						

											}

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->rest_day_legal_holiday_overtime = $overtimeHours;

											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->rest_day_legal_holiday_night_diff = $nightDiff;

											}*/
										
										} elseif ( 'Special non-working day' === $holidayYesterday->holiday_type ) { //Special non-working day														

											echo "Special non-working day \n";										

											$summaryYesterday->rest_day_special_holiday = $workHours;

											/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

												$overtimeNightdiff = $overtimeHours + $nightDiff;
												$summaryYesterday->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;						

											}					

											if($hasOvertime) { //ISOVERTIME: TRUE

												$summaryYesterday->rest_day_special_holiday_overtime = $overtimeHours;
												
											}

											if($hasNightDiff) { //HASNIGHTDIFF: TRUE

												$summaryYesterday->rest_day_special_holiday_night_diff = $nightDiff;

											}*/										

										}

									} else { //HOLIDAY: FALSE - Regular Day

										echo "HOLIDAY: FALSE - Regular Day \n";

										$summaryYesterday->rest_day = $workHours;

										/*if($hasOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

											$overtimeNightdiff = $overtimeHours + $nightDiff;
											$summaryYesterday->rest_day_overtime_night_diff = $overtimeNightdiff;						

										}

										if ($hasOvertime) { //ISOVERTIME: TRUE

											$summaryYesterday->rest_day_overtime = $overtimeHours;

										}				

										if($hasNightDiff) { //HASNIGHTDIFF: TRUE

											$summaryYesterday->rest_day_night_differential = $nightDiff;

										}*/									

									}
				
									if ($timesheetYesterday->save() ) {
										$summaryYesterday->save();
										return Redirect::to('/redraw/timesheet');

									}								

								}								

							}
													
						} else { // CHECK IF FLEXIBLE SCHEDULE: TRUE

							$timesheetYesterday->clocking_status = "clock_out_3";
							$timesheetYesterday->time_out_3 = $timesheetTimeOut;	
							
							//WITHOUT THE COMPLETE COMPUTATION: TRUE

							//GET TOTALHOURS WITH OVERTIME
							echo $timesheetYesterday->total_hours_3 = totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);						

							//GET WORKHOURS
							echo $timesheetYesterday->work_hours_3 = workHours($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
							
							//GET UNDERTIME
							echo $timesheetYesterday->undertime_3 = underTimeHours($timesheetTimeOut, $scheduleEndTime);
							
							// GET NIGHTDIFF;
							$nightDiff = nightDiff($timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime);																

							if ($timesheetYesterday->save() ) {
								$summaryYesterday->save();
								return Redirect::to('/redraw/timesheet');

							}						

						}

					}			

				}


				/*if( $timesheetYesterday->clocking_status === "clock_in_1" ||
					$timesheetYesterday->clocking_status !== "clock_in_2" ||
					$timesheetYesterday->clocking_status !== "clock_in_3" ) {*/

			}

		}		

	}

	public function processTimesheetComputation($isFlexible = 0, $timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak) {

		$dataArr = $this->init();

		$employee = $dataArr["employee"];
		$employeeSetting = $dataArr["employeeSetting"];
		$timesheets = $dataArr["timesheets"];
		$timesheet = $dataArr["timesheet"];
		$timesheetYesterday = $dataArr["timesheetYesterday"];
		$summaryYesterday = $dataArr["summaryYesterday"];
		$scheduleYesterday = $dataArr["scheduleYesterday"];
		$holidayYesterday = $dataArr["holidayYesterday"];		
		$summary = $dataArr["summary"];
		$summaries = $dataArr["summaries"];
		$schedule = $dataArr["schedule"];		
		$holiday = $dataArr["holiday"];

		//GET TOTALHOURS											
		echo $timesheet->total_hours_1 = totalHours($timesheetTimeIn, $timesheetTimeOut);
		echo "\n";

		//GET TOTALHOURS WITH OVERTIME
		$totalHours = totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
		echo $timesheet->total_hours_1 = $totalHours;

		//GET WORKHOURS
		$workHours = workHours($timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
		echo $timesheet->work_hours_1 = $workHours;
		echo "\n";

		//GET OVERTIME						
		$overtimeHours = overtimeHours($timesheetTimeOut, $scheduleEndTime);
		
		if ( $overtimeHours ) {

			$hasOvertime = $overtimeHours;

		} else {
		
			$hasOvertime = FALSE;			

		}

		echo $timesheet->total_overtime_1 = $overtimeHours;
		echo "\n";

		//GET UNDERTIME
		$underTimeHours = underTimeHours($timesheetTimeOut, $scheduleEndTime);
		echo $timesheet->undertime_1 = $underTimeHours;
		echo $summary->undertime = $underTimeHours;
		echo "\n";
		

		//GET NIGHTDIFF;
		$nightDiff = nightDiff($timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime);
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

}
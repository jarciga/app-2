<?php

class SchedulesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /schedules
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /schedules/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /schedules
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /schedules/{id}
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
	 * GET /schedules/{id}/edit
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
	 * PUT /schedules/{id}
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
	 * DELETE /schedules/{id}
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
		
		$holiday = Holiday::where('holiday_date', $currentDate)->first();

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
					'yesterDayDate' => $yesterDayDate,
					'companies' => $companies,
					'departments' => $departments,
					'jobTitles' => $jobTitles,
					'managers' => $managers,
					'supervisors' => $supervisors,
					'roles' => $roles
					);							

	}


	public function scheduleGenerator($userEmployeeId = '')
	{

		if (!empty($userEmployeeId) )
		{

			$user['employeeId'] = $userEmployeeId;
			
		}

		$currentYear = date('Y');
		$currentMonth = date('m');
		$lastDayPerMonth = (int) date('t');
		$currentDayOfTheMonth = (int) date('j');
		$currentDate = date('Y-m-d');


		$checkSchedule = Schedule::where('employee_id', $user['employeeId'])
				   ->where('year', $currentYear)
				   ->where('month', $currentMonth)
				   ->get();

		if( count($checkSchedule) === 0 ) //If table is empty or no record found: insert new schedule to employee_schedule table
		{							
			//Insert record for current year and currente month
			for ($i = 1; $i <= $lastDayPerMonth; $i++) 
			{				

				$scheduleDate = date('Y-m-d', strtotime($currentYear.'-'.$currentMonth.'-'.$i));
				$monToSun = date('D', strtotime($currentYear.'-'.$currentMonth.'-'.$i));

				if($monToSun === 'Sat' || $monToSun === 'Sun') 
				{
					$restDay = 1;

				} else {

					$restDay = 0;

				}

				DB::table('employee_schedule')->insert(
					array(
						'employee_id' => $user['employeeId'],
						'year' => $currentYear,
						'month' => $currentMonth,
						'day' => $i,
						'shift' => 1,
						'rest_day' => $restDay,
						'hours_per_day' => Config::get('euler.hours_per_day'),
						'start_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'05:00:00')), 
						'end_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'17:00:00')),
						'schedule_date' => $scheduleDate
					)
				);

			}


		} else {

			//If has record for the month. Do nothing
			return 0;

		}		

	}

	public function cutOffScheduleGenerator1($cutOffStart, $cutOffEnd, $cutOffRange, $currentYear, $currentMonth, $currentDayOfTheMonth, $prevMonthNumberOfDays, $userEmployeeId = '') 
	{

			//11-25 - 5
			//26-10 - 20

			if (!empty($userEmployeeId) )
			{

				$user['employeeId'] = $userEmployeeId;
				
			}	

			$currentYear = date('Y');
			$currentMonth = date('m');
			$lastDayPerMonth = (int) date('t');
			$currentDayOfTheMonth = (int) date('j');
			$currentDate = date('Y-m-d');

			$checkSchedule = Schedule::where('employee_id', $userEmployeeId)
							   ->where('year', $currentYear)
							   ->where('month', $currentMonth)
							   ->first();

			if ( count($checkSchedule) === 0 ) //If table is empty or no record found: insert new schedule to employee_schedule table
			{			

				$date = new DateTime(date('Y-'.'m-'.$cutOffStart));
				//$date = DateTime::createFromFormat('Y-m-d', date('Y-'.'m-'.$cutOffStart));									

				for ($i = 0; $i < count($cutOffRange); $i++) 
				{				
					if ( $cutOffEnd !== (int) $date->format('d') )
					{
						$date->modify('+1 day');
						//$cutOffArr[] = $date->format('Y-m-d');
					}

					//$scheduleDate = date('Y-m-d', strtotime($currentYear.'-'.$currentMonth.'-'.$i));
					//$monToSun = date('D', strtotime($currentYear.'-'.$currentMonth.'-'.$i));
	 				
	 				/*$year = $date->format('Y');
					$month = $date->format('M');				
					$scheduleDate = $date->format('Y-m-d');
					$monToSun = $date->format('D');
					$day = $date->format('d');*/

					/*if($monToSun === 'Sat' || $monToSun === 'Sun') 
					{
						$restDay = 1;

					} else {

						$restDay = 0;

					}*/

					/*return array(
							'employee_id' => $user['employeeId'],
							'year' => $year,
							'month' => $month,
							'day' => $day,
							'shift' => 1,
							'rest_day' => $restDay,
							'hours_per_day' => Config::get('euler.hours_per_day'),
							'start_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'05:00:00')), 
							'end_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'17:00:00')),
							'schedule_date' => $scheduleDate
						);*/

					/*DB::table('employee_schedule')->insert(
						array(
							'employee_id' => $user['employeeId'],
							'year' => $year,
							'month' => $month,
							'day' => $day,
							'shift' => 1,
							'rest_day' => $restDay,
							'hours_per_day' => Config::get('euler.hours_per_day'),
							'start_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'08:00:00')), 
							'end_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'17:00:00')),
							'schedule_date' => $scheduleDate
						)
					);*/

					$scheduleDateArr[] = $date->format('Y-m-d');			

				}

			} /*else {

				//If has record for the month. Do nothing
				return 0;

			}*/


			if ( is_array($scheduleDateArr) )
			{

				//Bug
			  	foreach($scheduleDateArr as $scheduleDateVal) 
			  	{
			  										  		
					//$date = new DateTime(date($scheduleDateVal));
					$date = new DateTime($scheduleDateVal);
				
					//$date->modify('-1 month'); //>> //BUG FOR 2nd Cutoff
					
					/*echo $currentDayOfTheMonth;
					echo '<br />';
					echo gettype($cutOffEnd);*/
									
					if( ((int) $currentDayOfTheMonth) === 1 || ((int) $currentDayOfTheMonth <= $cutOffEnd) ) {										

						//$date->modify('-1 month');

						//$date->modify('-'.$prevMonthNumberOfDays.' days');
						//$date->modify('-28 days'); // set it to 30 days
						//$date->modify('-29 days'); // set it to 30 days
						//$date->modify('-30 days'); // 26-11
						//$date->modify('-31 days'); // 25-10

						//check this out
						/*if ( in_array($prevMonthNumberOfDays, array('28', '29')) ) {

							$date->modify('-30 days');

						} elseif ($prevMonthNumberOfDays === '30') {

							$date->modify('-30 days');

						} elseif ( $prevMonthNumberOfDays === '31' ) {

							$date->modify('-31 days');

						}*/

						//echo '<br />';											
					}

					$scheduleDateVal = $date->format('Y-m-d');
			  		$dayDateTempArr[] = $scheduleDateVal;
			  		//$dayDate = $scheduleDateVal;
			  		//$dayDateArr[] = $dayDate;

					//echo $semiMonthly['cutOffStart'][2] + 1;
					//echo $cutOffEnd;

					if ((int) $date->format('d') === $cutOffStart + 1) {

						$cutOffStartArr[] = $date->format('Y-m-d');

					}

					if ((int) $date->format('d') === $cutOffEnd) {

						$cutOffEndArr[] = $date->format('Y-m-d');

					}											

					
			  	}
			  	
			  	//return dd($dayDateTempArr);

			  	foreach($dayDateTempArr as $dayDateTempVal) {

			  		if( ( strtotime($dayDateTempVal) >= strtotime($cutOffStartArr[0]) ) && 
			  			( strtotime($dayDateTempVal) <= strtotime($cutOffEndArr[0]) ) ) {

						//db code here

						$year = date('Y', strtotime($dayDateTempVal)); //$date->format('Y');
						$month = date('M', strtotime($dayDateTempVal)); //$date->format('M');				
						$scheduleDate = date('Y-m-d', strtotime($dayDateTempVal)); //$date->format('Y-m-d');
						$monToSun = date('D', strtotime($dayDateTempVal)); //$date->format('D');
						$day = date('d', strtotime($dayDateTempVal)); //$date->format('d');

						if($monToSun === 'Sat' || $monToSun === 'Sun') 
						{
							$restDay = 1;

						} else {

							$restDay = 0;

						}

						DB::table('employee_schedule')->insert(
							array(
								'employee_id' => $user['employeeId'],
								'year' => $year,
								'month' => $month,
								'day' => $day,
								'shift' => 1,
								'rest_day' => $restDay,
								'hours_per_day' => Config::get('euler.hours_per_day'),
								'start_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'08:00:00')), 
								'end_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'17:00:00')),
								'schedule_date' => $scheduleDate
							)
						);					

			  		}

			  	}

			}

	
	}


	public function cutOffScheduleGenerator2($cutOffStart, $cutOffEnd, $cutOffRange, $currentYear, $currentMonth, $currentDayOfTheMonth, $prevMonthNumberOfDays, $userEmployeeId = '')  
	{

			//11-25 - 5
			//26-10 - 20

			if (!empty($userEmployeeId) )
			{

				$user['employeeId'] = $userEmployeeId;
				
			}	

			$currentYear = date('Y');
			$currentMonth = date('m');
			$lastDayPerMonth = (int) date('t');
			$currentDayOfTheMonth = (int) date('j');
			$currentDate = date('Y-m-d');

			$checkSchedule = Schedule::where('employee_id', $userEmployeeId)
							   ->where('year', $currentYear)
							   ->where('month', $currentMonth)
							   ->first();

			if ( count($checkSchedule) === 0 ) //If table is empty or no record found: insert new schedule to employee_schedule table
			{			

				$date = new DateTime(date('Y-'.'m-'.$cutOffStart));
				//$date = DateTime::createFromFormat('Y-m-d', date('Y-'.'m-'.$cutOffStart));									

				for ($i = 0; $i < count($cutOffRange); $i++) 
				{				
					if ( $cutOffEnd !== (int) $date->format('d') )
					{
						$date->modify('+1 day');
						//$cutOffArr[] = $date->format('Y-m-d');
					}

					//$scheduleDate = date('Y-m-d', strtotime($currentYear.'-'.$currentMonth.'-'.$i));
					//$monToSun = date('D', strtotime($currentYear.'-'.$currentMonth.'-'.$i));
	 				
	 				$year = $date->format('Y');
					$month = $date->format('M');				
					$scheduleDate = $date->format('Y-m-d');
					$monToSun = $date->format('D');
					$day = $date->format('d');

					if($monToSun === 'Sat' || $monToSun === 'Sun') 
					{
						$restDay = 1;

					} else {

						$restDay = 0;

					}

					/*return array(
							'employee_id' => $user['employeeId'],
							'year' => $year,
							'month' => $month,
							'day' => $day,
							'shift' => 1,
							'rest_day' => $restDay,
							'hours_per_day' => Config::get('euler.hours_per_day'),
							'start_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'05:00:00')), 
							'end_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'17:00:00')),
							'schedule_date' => $scheduleDate
						);*/

					DB::table('employee_schedule')->insert(
						array(
							'employee_id' => $user['employeeId'],
							'year' => $year,
							'month' => $month,
							'day' => $day,
							'shift' => 1,
							'rest_day' => $restDay,
							'hours_per_day' => Config::get('euler.hours_per_day'),
							'start_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'08:00:00')), 
							'end_time' => date('Y-m-d H:i:s', strtotime($scheduleDate.' '.'17:00:00')),
							'schedule_date' => $scheduleDate
						)
					);

					$dayDateArr[] = $scheduleDate;		

				}

				return $dayDateArr;

			} else {

				//If has record for the month. Do nothing
				return 0;

			}
	
	}


	public function showScheduleUploader() 
	{

		$dataArr = $this->init();	
		$dataArr["resourceId"] = 'admin.schedules.show.schedule.uploader';	

		$employeesController = new EmployeesController;
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		return View::make('schedules.index', $dataArr);			

	}


	//public function postShift()
	public function processUploadedSchedule()
	{
			$path = public_path();
	        $allowedExts = array("xls","xlsx","csv");
			$temp = explode(".", $_FILES["file"]["name"]);
	        $extension = end($temp);
	        //$filename= $temp[0];
	        $filename= $temp[0] . '_' . date('YmdHis') . '_' . rand(111,999);
	        $destinationPath =  $path .'/uploads/'.$filename.'.'.$extension;

	        if(in_array($extension, $allowedExts)&&($_FILES["file"]["size"] < 20000000)) {
	              
	            if($_FILES["file"]["error"] > 0) {
	            
	                //echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
	            
	            } else {
	                 
	                  //if (file_exists($path."/uploads/" . $_FILES["file"]["name"]))
	              	 if (file_exists($path."/uploads/" . $filename)) {
	                 
	                    //echo $_FILES["file"]["name"] . " already exists. ";
	                 
	                 } else {
	                    $uploadSuccess = move_uploaded_file($_FILES["file"]["tmp_name"],$destinationPath);


	                        Excel::load($destinationPath, function($reader) {

						        //$results = Excel::load('public/products2.xls')->get();
						        $result = $reader->all();
						        $reader->toArray();

						       //$reader->dump();

						        // Loop through all the sheets
						        $reader->each(function($sheet) {

							        // Loop through all rows
						            $sheet->each(function($row) {

							            //$date = $row->schedule_date;
										//$scheduleDate = date('Y-m-d', strtotime($schedule_date));

						                $schedule = new Schedule;
						                $schedule->employee_id = $row->employee_id;
						                $schedule->year = $row->year;
						                $schedule->month = date('M', strtotime($row->schedule_date)); //$row->month;
						                $schedule->day = date('j', strtotime($row->schedule_date)); //$row->day;
						                $schedule->shift = $row->shift;
						                $schedule->rest_day = $row->rest_day;
						                $schedule->hours_per_day = $row->hours_per_day;
						                $schedule->start_time = $row->start_time;
						                $schedule->end_time = $row->end_time;
						                $schedule->schedule_date = date('Y-m-d', strtotime($row->schedule_date));						                
						                $schedule->save();

							        });

						        });

						    });

	                    /*if( $uploadSuccess )
	                    {
	                       $document_details=Response::json(Author::insert_document_details_Call($filename,$destinationPath));
	                       return $document_details; // or do a redirect with some message that file was uploaded
	                    }
	                    else
	                    {
	                       return Response::json('error', 400);
	                    }*/
	                    Session::flash('error', 'Uploading of employee is successful');
	                    //return Redirect::action('EmployeesController@indexshift');
	                    return Redirect::route('show.schedule.uploader');
	                }
	               
	            }
	                
	        } else {
	            
	            return "Invalid file";
	        }
	}


	public function processSearchSchedule()
	{

		$data = Input::all();
		$dataArr["resourceId"] = 'admin.schedules.show.schedule.uploader';	
			
		$employeesController = new EmployeesController;
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		if( !empty($data['employee_number']) && !empty($data["schedule_date_from"]) && !empty($data["schedule_date_from"]) ) {
		
			$employeeNumber = Employee::where('employee_number', '=', trim($data['employee_number']))->first();

			$dataArr["uploadedSchedules"] = Schedule::where('employee_id', '=', trim($employeeNumber->id))
											->whereBetween('schedule_date', array($data["schedule_date_from"], $data["schedule_date_to"]))
											->get();	

		} elseif( !empty($data['employee_number']) && empty($data["schedule_date_from"]) && empty($data["schedule_date_from"]) ) {

			$employeeNumber = Employee::where('employee_number', '=', trim($data['employee_number']))->first();

			$dataArr["uploadedSchedules"] = Schedule::where('employee_id', '=', trim($employeeNumber->id))->get();	

		} 

		
  		return View::make('schedules.index', $dataArr);			
		
	}


	public function processScheduleEdit() 
	{

		$data = Input::all();

		//Todo: Validation

		$schedule = new Schedule;

		for ( $i = 0; $i <= sizeof($data["schedule"]) - 1; $i++ ) {

			$startTime = date('H:i:s', strtotime($data["schedule"][$i]["starttimehh"] . ':' . $data["schedule"][$i]["starttimemm"]));
			$endTime = date('H:i:s', strtotime($data["schedule"][$i]["endtimehh"] . ':' . $data["schedule"][$i]["endtimemm"]));

			$startDate = date('Y-m-d', strtotime($data["schedule"][$i]["startdate"]));
			$endDate = date('Y-m-d', strtotime($data["schedule"][$i]["enddate"]));

			$startDateTime = date('Y-m-d H:i:s', strtotime($startTime.' '.$startDate));
			$endDateTime =  date('Y-m-d H:i:s', strtotime($endTime.' '.$endDate));

			$scheduleDate = $startDate;

			DB::table('employee_schedule')
				->where('id', (int) $data["schedule"][$i]["uploadedScheduleId"])
				->update(array(				
					'year' => date('Y', strtotime($startDate)), 
					'month' => date('M', strtotime($startDate)),
					'day' => date('j', strtotime($startDate)),
					'shift' => (int) $data["schedule"][$i]["shift"],
					'rest_day' => $data["schedule"][$i]["restday"],
					'start_time' => $startDateTime,
					'end_time' => $endDateTime,
					'schedule_date' => $scheduleDate
					
			));

		}	

		return Redirect::route('show.schedule.uploader');

	}			

}
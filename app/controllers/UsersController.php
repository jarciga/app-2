<?php

class UsersController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /users
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /users/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /users
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /users/{id}
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
	 * GET /users/{id}/edit
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
	 * PUT /users/{id}
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
	 * DELETE /users/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


	//Additional method

	public function showLogin()
	{
	

		//Reset a User Password
		/*try
		{
		    // Find the user using the user email address
		    $user = Sentry::findUserByLogin('11298');

		    // Get the password reset code
		    echo $resetCode = $user->getResetPasswordCode();

		    // Now you can send this code to your user via email for example.
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    echo 'User was not found.';
		}

		try
		{
		    // Find the user using the user id
		    $user = Sentry::findUserById(1);

		    // Check if the reset password code is valid
		    if ($user->checkResetPasswordCode('WScJ5cRSofqR1niu4CnN1Tu8hS4iHUzlyAQ9nsTob1'))
		    {
		        // Attempt to reset the user password
		        if ($user->attemptResetPassword('WScJ5cRSofqR1niu4CnN1Tu8hS4iHUzlyAQ9nsTob1', 'testing'))
		        {
		            // Password reset passed
		            echo 'Password reset passed';
		        }
		        else
		        {
		            // Password reset failed
		            echo 'Password reset failed';
		        }
		    }
		    else
		    {
		        // The provided password reset code is Invalid
		    }
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    echo 'User was not found.';
		}*/

		return View::make('users.login'); //Return back to login page		

	}

	public function processLogin() 
	{

		$employeeno = Input::get('employeeno');	
		$password = Input::get('password');

		// Login credentials
		$credentials = array(			
			'employee_number' => $employeeno,			
			'password' => $password,
		);		

		try
		{

			$user = Sentry::authenticate($credentials, false);

			if( $user )
			{

				if( !empty($user->status) && $user->status === 1 ) { // Check user status if active or not

					//e.g 11-25	 - 5
					//e.g 26-10 - 20

					$cutoffStart = Config::get('euler.cutoff.cutOffStart');
					$cutOffEnd = Config::get('euler.cutoff.cutOffEnd');

					$cutOff = new CutoffController;
					$schedule = new SchedulesController;

					$cutOffSetting = Cutoffsetting::where('id', 1)->first();

					$employeeSetting = Employeesetting::where('employee_id', $user->id)->first();

					$semiMonthly['currentYear'] = date('Y'); //< e.g "2015"
					$semiMonthly['currentMonth'] = date('M'); //< e.g "Nov"
					$semiMonthly['lastDayPerMonth'] = (int) date('t');
					$semiMonthly['currentDayOfTheMonth'] = (int) date('j'); //< e.g 1
					$semiMonthly['currentDate'] = date('Y-m-d'); // <
					//$currentMonth = date('F');
					$prevMonthNumberOfDays = Date('t', strtotime($semiMonthly['currentMonth'] . " last month"));
					Session::put('prevMonthNumberOfDays', $prevMonthNumberOfDays);


					$semiMonthly['cutOffStart'][1] = (11-1);
					$semiMonthly['cutOffEnd'][1] = 25;
					$semiMonthly['cutOffRange'][1] = range($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1]);

					$semiMonthly['cutOffStart'][2] = (26-1);
					$semiMonthly['cutOffEnd'][2] = 10;
					$semiMonthly['cutOffRange'][2] = range($semiMonthly['cutOffStart'][2], $semiMonthly['cutOffEnd'][2]);	


					//=========================================================================================

					$currentMonthNumberOfDays = '';

					//echo $semiMonthly['currentMonth'];

					$with28Days = in_array($semiMonthly['currentMonth'], array("Feb")); // with 28 days: February

					$with29Days = in_array($semiMonthly['currentMonth'], array("Feb")); // with 29 days: February

					$with30Days = in_array($semiMonthly['currentMonth'], array("Apr", "Jun", "Sep", "Nov")); // with 30 days: April, June, September, November

					$with31Days = in_array($semiMonthly['currentMonth'], array("Jan", "Mar", "May", "Jul", "Aug", "Oct", "Dec")); // with 31 days: January, March, May, July, August, October, December

					if($with28Days) {
					   
					   $currentMonthNumberOfDays = 28; // 28 Days

						$cutOffRange = range(($cutoffStart[2]-5), $cutOffEnd[2]); //28 days - 26 to 10				   

					} elseif($with29Days) {
					   
					  $currentMonthNumberOfDays = 29; // 29 Days

						$cutOffRange = range(($cutoffStart[2]-4), $cutOffEnd[2]); //29 days leap year - 26 to 10					  

					}

					if($with30Days) {
					   
					  $currentMonthNumberOfDays = 30; // 30 Days

						$cutOffRange = range(($cutoffStart[2]-3), $cutOffEnd[2]); //30 days - 26 to 10					  

					}

					if($with31Days) {
					   
					   $currentMonthNumberOfDays = 31; // 31 Days

						$cutOffRange = range(($cutoffStart[2]-2), $cutOffEnd[2]); //31 days - 26 to 10						   

					}

					//echo '<br>';
					if ( $semiMonthly['currentDayOfTheMonth'] >= $cutoffStart[1] && 
						 $semiMonthly['currentDayOfTheMonth'] <= $cutOffEnd[1] ) {
								
						echo "11 - 25";

						$cutOffRange = range($cutoffStart[1], $cutOffEnd[1]);

						$m = date('m', strtotime($semiMonthly['currentMonth']));

						//$date = new DateTime(date('Y-'.'m-'.$cutoffStart[1]));
						$date = new DateTime(date('Y-'.$m.'-'.$cutoffStart[1]));
						
						//echo $date->format('Y-m-d')."<br />";
						$cutOffArr[] = $date->format('Y-m-d');

						for ($i = 0; $i < count($cutOffRange)-1; $i++) 
						{				
							
							$date->modify('+1 day');
							//echo $date->format('Y-m-d')."<br />";
							$cutOffArr[] = $date->format('Y-m-d');

						}				

						/*echo '<pre>';
						var_dump($cutOffArr);
						echo '</pre>';*/

					} elseif ( $semiMonthly['currentDayOfTheMonth'] >= $cutoffStart[2] && 
						 $semiMonthly['currentDayOfTheMonth'] <= $currentMonthNumberOfDays ) {  // 26 to (28|29|30|31)

						 //echo "26 - (28|29|30|31)";	

						//$cutOffRange = range(26, $currentMonthNumberOfDays);						
						
						//$cutOffRange = range(($cutoffStart[2]-5), $cutOffEnd[2]); //28 days - 26 to 10

						//$cutOffRange = range(($cutoffStart[2]-4), $cutOffEnd[2]); //29 days leap year - 26 to 10

						//$cutOffRange = range(($cutoffStart[2]-3), $cutOffEnd[2]); //30 days - 26 to 10

						//$cutOffRange = range(($cutoffStart[2]-2), $cutOffEnd[2]); //31 days - 26 to 10
	

						//echo count($cutOffRange);

						$m = date('m', strtotime($semiMonthly['currentMonth']));
					
						//$date = new DateTime(date('Y-'.'m-'.$cutoffStart[2]));
						$date = new DateTime(date('Y-'.$m.'-'.$cutoffStart[2]));						 
						
						//echo $date->format('Y-m-d')."<br />";
						$cutOffArr[] = $date->format('Y-m-d');

						for ($i = 0; $i < count($cutOffRange); $i++) 
						{				
							
							$date->modify('+1 day');
							//echo $date->format('Y-m-d')."<br />";
							$cutOffArr[] = $date->format('Y-m-d');

						}						 

						/* OR
						$cutOffRange = range(26, $currentMonthNumberOfDays);

						$date = new DateTime(date('Y-'.'m-'.$cutoffStart[2]));
						
						//echo $date->format('Y-m-d')."<br />";
						$cutOffArr[] = $date->format('Y-m-d');

						for ($i = 0; $i < count($cutOffRange)-1; $i++) 
						{				
							
							$date->modify('+1 day');
							//echo $date->format('Y-m-d')."<br />";
							$cutOffArr[] = $date->format('Y-m-d');

						}
						*/						

						/*echo '<pre>';
						var_dump($cutOffArr);
						echo '</pre>';*/						 

						 
							
					} elseif ( $semiMonthly['currentDayOfTheMonth'] >= 1 && 
						 $semiMonthly['currentDayOfTheMonth'] <= $cutOffEnd[2] ) { // 1 to 10


						//echo "1 - 10";

						//echo $prevMonthNumberOfDays;

						$previousDate = DateTime::createFromFormat("M", $semiMonthly['currentMonth']);
						$interval = new DateInterval("P1M"); // 1 months
						$oneMonthEarlier = $previousDate->sub($interval);
						$semiMonthly['previousMonth'] = $oneMonthEarlier->format("M");
										
						$with28Days = in_array($semiMonthly['previousMonth'], array("Feb")); // with 28 days: February

						$with29Days = in_array($semiMonthly['previousMonth'], array("Feb")); // with 29 days: February

						$with30Days = in_array($semiMonthly['previousMonth'], array("Apr", "Jun", "Sep", "Nov")); // with 30 days: April, June, September, November

						$with31Days = in_array($semiMonthly['previousMonth'], array("Jan", "Mar", "May", "Jul", "Aug", "Oct", "Dec")); // with 31 days: January, March, May, July, August, October, December						

						if($with28Days) {
						   
						   $prevMonthNumberOfDays = 28; // 28 Days

							$cutOffRange = range(($cutoffStart[2]-5), $cutOffEnd[2]); //28 days - 26 to 10				   

						} elseif($with29Days) {
						   
						  $prevMonthNumberOfDays = 29; // 29 Days

							$cutOffRange = range(($cutoffStart[2]-4), $cutOffEnd[2]); //29 days leap year - 26 to 10					  

						}

						if($with30Days) {
						   
						  $prevMonthNumberOfDays = 30; // 30 Days

							$cutOffRange = range(($cutoffStart[2]-3), $cutOffEnd[2]); //30 days - 26 to 10					  

						}

						if($with31Days) {
						   
						   $prevMonthNumberOfDays = 31; // 31 Days

							$cutOffRange = range(($cutoffStart[2]-2), $cutOffEnd[2]); //31 days - 26 to 10						   

						}		

						//$semiMonthly['previousMonth']
						//$prevMonthNumberOfDays
						
						$previousMonth = date('m', strtotime($semiMonthly['previousMonth']));

						//echo $cutoffStart[2];
						//echo count($cutOffRange);
						
						//$date = new DateTime(date('Y-'.'m-'.$cutoffStart[2]));
						$date = new DateTime(date('Y-'.$previousMonth.'-'.$cutoffStart[2]));


						if ( $semiMonthly['currentMonth'] === "Jan" ) {
								
								$date->modify('-1 year');
								
						}						
						
						//echo $date->format('Y-m-d')."<br />";
						$cutOffArr[] = $date->format('Y-m-d');

						for ($i = 0; $i < count($cutOffRange); $i++) 
						{				
														

							$date->modify('+1 day');
							//echo $date->format('Y-m-d')."<br />";
							$cutOffArr[] = $date->format('Y-m-d');

						}	
						
						/*echo '<pre>';
						var_dump($cutOffArr);
						echo '</pre>';*/


					}
					
					//return;
					//die();

					//========================================================================================

					if( count($cutOffSetting) >= 1 ) 
					{
						$cutOffOptions = $cutOffSetting->cutoff_options;	
						$cutOffType = $cutOffSetting->cutoff_type; //Monthly, Semi Monthly
					}
		
					if ( $cutOffOptions === 1 ) //1st cutoff is within the same month
					{					

						if ( $cutOffType === 1 ) // Monthly
						{


						} 
						elseif ( $cutOffType === 2 ) // Semi onthly
						{


						}					

					}				
					elseif ( $cutOffOptions === 2 ) //2nd cutoff is overlap to next month
					{
						
						if ( $cutOffType === 1 ) // Monthly
						{


						} 
						elseif ( $cutOffType === 2 ) // Semi Monthly
						{

							
							$checkTimesheet = Timesheet::where('employee_id', $user->id)
			  										   ->where('daydate', $semiMonthly['currentDate'])
			  										   ->first();
							

							$checkSchedule = Schedule::where('employee_id', $user->id)
			  										   ->where('schedule_date', $semiMonthly['currentDate'])
			  										   ->first();
			  				
			  				//return dd($checkSchedule);


			  				if ( empty($checkSchedule) && empty($checkTimesheet) ) { //Has no schedule found < test
			  					
			  					//return 'debug 1';

								/*echo '<pre>';
								var_dump($cutOffArr);
								echo '</pre>';*/

								//Insert new schedule to employee schedule table, base on default schedule								
								//*Add uploaded column to employee schedule table = true|false default to false
								//To determine if came from uploaded spreedsheet
								//Insert timesheet table, overtime table, summary

								//echo date('d', strtotime($cutOffArr[0]));
								//echo "<br />";
								//echo date('d', strtotime($cutOffArr[count($cutOffArr)-1]));


								$checkDefaultSchedule = DB::table('default_schedule')
													 ->where('employee_id', $user->id)					 
													 ->get();								


								//return dd($employeeSetting->is_flexible);

								//if($employeeSetting->is_flexible === 0) { //FALSE

									if ( count($checkDefaultSchedule) !== 0 ) {									

										if ( is_array($cutOffArr) ) {

											//$defaultSchedule = DB::table('default_schedule')->where('employee_id', $user->id)->get();

											//Check if name_of_day_from and name_of_day_to are the same

											//var_dump($defaultSchedule);

										  	foreach($cutOffArr as $cutOffVal) 
										  	{

										  		$nameOfDayFrom = date('l', strtotime($cutOffVal));
										  		$date = new DateTime($cutOffVal);

									  			$defaultSchedule = DB::table('default_schedule')
									  								 ->where('employee_id', $user->id)
									  								 ->where('name_of_day_from', $nameOfDayFrom)
									  								 ->first();		

									  			if( !empty($defaultSchedule) ) {						  		

											  		if ($nameOfDayFrom === "Monday") {

											  			//Monday|Monday
											  			//Monday|Tuesday

											  			//echo $defaultSchedule->name_of_day_from;
											  			//echo $defaultSchedule->name_of_day_to;

											  			if (strcmp($defaultSchedule->name_of_day_from, $defaultSchedule->name_of_day_to) === 0) {

											  				//If "Name of day from" and "Name of day to" are equal	

											  				$startDate = $cutOffVal; //as is
											  				$endDate = $cutOffVal; //as is
														    
														} else {

															//If "Name of day from" and "Name of day to" are not equal	

															$startDate = $cutOffVal; //as is
											  				$endDate = $date->modify('+1 day'); //Add 1 day
															$endDate = $date->format('Y-m-d');

														}


											  		} elseif ($nameOfDayFrom === "Tuesday") {

											  			//Tuesday|Tuesday
											  			//Tuesday|Wednesday

											  			if (strcmp($defaultSchedule->name_of_day_from, $defaultSchedule->name_of_day_to) === 0) {

											  				//If "Name of day from" and "Name of day to" are equal	

											  				$startDate = $cutOffVal; //as is
											  				$endDate = $cutOffVal; //as is
														    
														} else {

															//If "Name of day from" and "Name of day to" are not equal	

															$startDate = $cutOffVal; //as is
											  				$endDate = $date->modify('+1 day'); //Add 1 day
															$endDate = $date->format('Y-m-d');


														}								  			

											  		} elseif ($nameOfDayFrom === "Wednesday") {

											  			//Wednesday|Wednesday
											  			//Wednesday|Thursday

											  			if (strcmp($defaultSchedule->name_of_day_from, $defaultSchedule->name_of_day_to) === 0) {

											  				//If "Name of day from" and "Name of day to" are equal	

											  				$startDate = $cutOffVal; //as is
											  				$endDate = $cutOffVal; //as is
														    
														} else {

															//If "Name of day from" and "Name of day to" are not equal	

															$startDate = $cutOffVal; //as is
											  				$endDate = $date->modify('+1 day'); //Add 1 day
															$endDate = $date->format('Y-m-d');


														}								  			

											  		} elseif ($nameOfDayFrom === "Thursday") {

											  			//Thursday|Thursday
											  			//Thursday|Friday

											  			if (strcmp($defaultSchedule->name_of_day_from, $defaultSchedule->name_of_day_to) === 0) {

											  				//If "Name of day from" and "Name of day to" are equal	

											  				$startDate = $cutOffVal; //as is
											  				$endDate = $cutOffVal; //as is
														    
														} else {

															//If "Name of day from" and "Name of day to" are not equal	

															$startDate = $cutOffVal; //as is
											  				$endDate = $date->modify('+1 day'); //Add 1 day
															$endDate = $date->format('Y-m-d');


														}								  			

											  		} elseif ($nameOfDayFrom === "Friday") {

											  			//Friday|Friday
											  			//Friday|Thursday

											  			if (strcmp($defaultSchedule->name_of_day_from, $defaultSchedule->name_of_day_to) === 0) {

											  				//If "Name of day from" and "Name of day to" are equal	

											  				$startDate = $cutOffVal; //as is
											  				$endDate = $cutOffVal; //as is
														    
														} else {

															//If "Name of day from" and "Name of day to" are not equal	

															$startDate = $cutOffVal; //as is
											  				$endDate = $date->modify('+1 day'); //Add 1 day
															$endDate = $date->format('Y-m-d');


														}								  			

											  		} elseif ($nameOfDayFrom === "Saturday") {

											  			//Saturday|Saturday
											  			//Saturday|Sunday

											  			if (strcmp($defaultSchedule->name_of_day_from, $defaultSchedule->name_of_day_to) === 0) {

											  				//If "Name of day from" and "Name of day to" are equal	

											  				$startDate = $cutOffVal; //as is
											  				$endDate = $cutOffVal; //as is
														    
														} else {

															//If "Name of day from" and "Name of day to" are not equal	

															$startDate = $cutOffVal; //as is
											  				$endDate = $date->modify('+1 day'); //Add 1 day
															$endDate = $date->format('Y-m-d');


														}								  			

											  		} elseif ($nameOfDayFrom === "Sunday") {

											  			//Sunday|Sunday
											  			//Sunday|Monday

											  			if (strcmp($defaultSchedule->name_of_day_from, $defaultSchedule->name_of_day_to) === 0) {

											  				//If "Name of day from" and "Name of day to" are equal	

											  				$startDate = $cutOffVal; //as is
											  				$endDate = $cutOffVal; //as is
														    
														} else {

															//If "Name of day from" and "Name of day to" are not equal	

															$startDate = $cutOffVal; //as is
											  				$endDate = $date->modify('+1 day'); //Add 1 day


														}								  			

											  		}								  		

													$startTime = $defaultSchedule->start_time;
													$endTime = $defaultSchedule->end_time;
											  		
											  		/*echo "<br />";
											  		echo $startDateTime = $startDate.' '.$startTime;
											  		echo "<br />";
											  		echo $endDateTime = $endDate.' '.$endTime;
											  		echo "<br />";
											  		echo "<br />";*/

											  		$startDateTime = $startDate.' '.$startTime;
											  		$endDateTime = $endDate.' '.$endTime;

											  		$scheduleDate = new DateTime($cutOffVal);

											  		$year = $scheduleDate->format('Y');
											  		$month = $scheduleDate->format('M');
											  		$day = $scheduleDate->format('d');

											  		$restDay = $defaultSchedule->rest_day;

													DB::table('employee_schedule')->insert(
														array(
															'employee_id' => $user->id,
															'year' => $year,
															'month' => $month,
															'day' => $day,
															'shift' => 1,
															'rest_day' => $restDay,
															'hours_per_day' => Config::get('euler.hours_per_day'),
															'start_time' => date('Y-m-d H:i:s', strtotime($startDateTime)),
															'end_time' => date('Y-m-d H:i:s', strtotime($endDateTime)),
															'schedule_date' => $scheduleDate->format('Y-m-d')
														)
													);

												}
												
												$dayDateArr[] = $cutOffVal;
													
													//return;
													//die();

											}	

											if( is_array($dayDateArr) ) {
											
												foreach($dayDateArr as $dayDate) {

													$schedule = DB::table('employee_schedule')->where('employee_id', $user->id)->where('schedule_date', trim($dayDate))->first();

													//echo $schedule->schedule_date;

													if(!empty($schedule)) {
														
												  		$timesheetId = DB::table('employee_timesheet')
														 ->insertGetId(
															array(
																'employee_id' => $user->id,
																'daydate' => $dayDate,
																'schedule_in' => $schedule->start_time,
																'schedule_out' => $schedule->end_time,
																'night_shift_time_out' => 0,
																'clocking_status' => 'open'						
															)
														);

														DB::table('employee_summary')
														->insert(
															array(
															'employee_id' => $user->id,
															'daydate' => $schedule->schedule_date
														));


														for ($i = 1; $i <= 3; $i++) {
															DB::table('overtime')
															->insert(
																array(
																'employee_id' => $user->id,
																'timesheet_id' => $timesheetId,
																'seq_no' => $i,
																'shift' => $i
															));							

														}	

													}			

												}

											}	

										}	

									} else {

										return 'No Default Schedule Found.';

									}

								/*} elseif($employeeSetting->is_flexible === 1) { //TRUE


								}*/


							} elseif ( !empty($checkSchedule) && !empty($checkTimesheet) ) { // Has schedule found

								//return 'debug 2';
								/*
								echo 'Has schedule found'.'<br />';
								echo '<pre>';
								var_dump($checkSchedule);
								echo '</pre>';
								*/

								//$dayDateArr = $cutOffArr;

								if ( is_array($cutOffArr) )
								{
									
								  	foreach($cutOffArr as $cutOffVal) 
								  	{

								  		$dayDate = $cutOffVal;
								  		$dayDateArr[] = $dayDate;

										$schedule = DB::table('employee_schedule')->where('employee_id', $user->id)->where('schedule_date', trim($dayDate))->first();

										if( !empty($schedule) ) {

											DB::table('employee_timesheet')
											  ->where('employee_id', $user->id)
											  ->where('daydate', $dayDate)
											  ->update(array('schedule_in' => $schedule->start_time, 'schedule_out' => $schedule->end_time));

										}

											  //echo $schedule->start_time;

								  	}

								}

								//return dd($dayDateArr);

							} elseif ( !empty($checkSchedule) && empty($checkTimesheet) ) { // Has schedule and has no timesheet
								
								//return 'debug 3';

								if( is_array($cutOffArr) ) {

									foreach($cutOffArr as $cutOffVal) {

										$dayDate = $cutOffVal;
								  		$dayDateArr[] = $dayDate;

										$schedule = DB::table('employee_schedule')->where('employee_id', $user->id)->where('schedule_date', trim($dayDate))->first();

										//echo $schedule->schedule_date;

										if(!empty($schedule)) {
											
									  		$timesheetId = DB::table('employee_timesheet')
											 ->insertGetId(
												array(
													'employee_id' => $user->id,
													'daydate' => $dayDate,
													'schedule_in' => $schedule->start_time,
													'schedule_out' => $schedule->end_time,
													'night_shift_time_out' => 0,
													'clocking_status' => 'open'						
												)
											);

											DB::table('employee_summary')
											->insert(
												array(
												'employee_id' => $user->id,
												'daydate' => $schedule->schedule_date
											));


											for ($i = 1; $i <= 3; $i++) {
												DB::table('overtime')
												->insert(
													array(
													'employee_id' => $user->id,
													'timesheet_id' => $timesheetId,
													'seq_no' => $i,
													'shift' => $i
												));							

											}

										}			

									}

								}

							}

						}

					}	
					//return;
					//die();
					/*
					$employee = Employee::where('id', $user->id)->first();
					
					$timesheet = Timesheet::where('employee_id', $user->id)
										  ->whereIn('daydate', $dayDateArr)->get();

					$summary = Summary::where('employee_id', $user->id)->first();
					*/

					/*
					Session::put('employee', $employee);				
					Session::put('timesheet', $timesheet);				
					Session::put('summary', $summary);
					*/

					$usersGroups = DB::table('users_groups')->where('user_id', $user->id)->first();					

					if( !empty($usersGroups) ) {

						$groups = DB::table('groups')->where('id', $usersGroups->group_id)->first();					

					}

					if( !empty($groups) ) {

						Session::put('groupName', $groups->name);

					}	

					$currentUserEmployeType = Employee::select('employee_type')->where('id', $user->id)->first();

					if( !empty($currentUserEmployeType) ) {
						
						Session::put('currentUserEmployeType', $currentUserEmployeType->employee_type);
						Session::put('currentUserEmployeTypeName', employeeTypeName($currentUserEmployeType->employee_type));
						
					}												

					Session::put('currentUserId', $user->id);

					//if( !empty($dayDateArr) ) {

						Session::put('dayDateArr', $dayDateArr);				

						//return View::make( 'index', array( 'employee' => $employee, 'timesheet' => $timesheet, 'summary' => $summary) );

					//}

					return Redirect::route( 'show.timesheet');						

				}				
				
			}

		}
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
		{
			$getMessages = 'Login field is required.';
			return Redirect::to('/login')->withErrors(array('login' => $getMessages));				
		}
		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
		{
			$getMessages = 'Password field is required.';
			return Redirect::to('/login')->withErrors(array('login' => $getMessages));								
		}
		catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
		{
			$getMessages = 'Wrong password, try again.';
			return Redirect::to('/login')->withErrors($getMessages);								
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			$getMessages = 'User was not found.';
			return Redirect::to('/login')->withErrors(array('login' => $getMessages));								
		}
		catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
		{
			$getMessages = 'User is not activated.';
			return Redirect::to('/login')->withErrors(array('login' => $getMessages));								
		}		

	}

	public function processLogout()
	{				
		Sentry::logout();		
		Session::flush();
		return Redirect::to('/login');
	} 	

	public function cuttOff() {
		
		$currentMonth = date('M');

		$cutOff = Cutoff::where('month', $currentMonth)->first();

		//Semi Monthly
		//$cutOff = array();
		//2nd Cutoff 26-11		
		//11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25		
		//$cutOff[0] = range(26, 10);		
		//return dd($cutOff);

		//with 31: jan, mar, may, jul, aug, oct, dec
		//26, 27, 28, 29, 30, 31

		$monthsWith31 = array('Jan', 'Mar', 'May', 'Jul', 'Aug', 'Oct', 'Dec');
		
		//with 30: apr, jun, sep,nov
		//26, 27, 28, 29, 30
		
		$monthsWith30 = array('Apr', 'Jun', 'Sep','Nov');

		//with 29: feb
		//26, 27, 28, 29
			
		//with 28: feb
		//26, 27, 28
		
		$monthsWith28Or29 = array('Feb');

		if ( in_array($currentMonth, $monthsWith31) ) {

			//return dd($monthsWith31);

			for($i = 0; $i < sizeof(range(26, 10))-1; $i++) {
				$date = DateTime::createFromFormat('Y-m-d', '2015-07-26');
				$date->modify('+'.$i.' day');
				echo $date->format('Y-m-d');			
				echo '<br />';
			}				

		} elseif( in_array($currentMonth, $monthsWith30) ) {

			//return dd($monthsWith30);

		} elseif( in_array($currentMonth, $monthsWith28Or29) ) { 

			return dd($monthsWith28Or29);
		}
	



		/*foreach($cutOff[0] as $CutOffValue) {
				
			echo $dateTime = date('Y').'-'.date('m').'-'.$CutOffValue;

			$date = new DateTime($dateTime);

			$date->modify('+1 day');
			echo $date->format('Y-m-d') . "\n";
			echo '<br />';

		}*/

	}
	        	    		

}
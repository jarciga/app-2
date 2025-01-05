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

					$semiMonthly['currentYear'] = date('Y');
					$semiMonthly['currentMonth'] = "Nov"; //date('M'); //<
					$semiMonthly['lastDayPerMonth'] = (int) date('t');
					$semiMonthly['currentDayOfTheMonth'] = 1; //(int) date('j'); //<
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



					$currentMonthNumberOfDays = '';

					//echo $semiMonthly['currentMonth'];

					$with28Days = in_array($semiMonthly['currentMonth'], array("Feb")); // with 28 days: February

					$with29Days = in_array($semiMonthly['currentMonth'], array("Feb")); // with 29 days: February

					$with30Days = in_array($semiMonthly['currentMonth'], array("Apr", "Jun", "Sep", "Nov")); // with 30 days: April, June, September, November

					$with31Days = in_array($semiMonthly['currentMonth'], array("Jan", "Mar", "May", "Jul", "Aug", "Oct", "Dec")); // with 31 days: January, March, May, July, August, October, December

					if($with28Days) {
					   
					   echo $currentMonthNumberOfDays = 28; // 28 Days

						$cutOffRange = range(($cutoffStart[2]-5), $cutOffEnd[2]); //28 days - 26 to 10				   

					} elseif($with29Days) {
					   
					  echo $currentMonthNumberOfDays = 29; // 29 Days

						$cutOffRange = range(($cutoffStart[2]-4), $cutOffEnd[2]); //29 days leap year - 26 to 10					  

					}

					if($with30Days) {
					   
					  echo $currentMonthNumberOfDays = 30; // 30 Days

						$cutOffRange = range(($cutoffStart[2]-3), $cutOffEnd[2]); //30 days - 26 to 10					  

					}

					if($with31Days) {
					   
					   echo $currentMonthNumberOfDays = 31; // 31 Days

						$cutOffRange = range(($cutoffStart[2]-2), $cutOffEnd[2]); //31 days - 26 to 10						   

					}

					echo '<br>';
					if ( $semiMonthly['currentDayOfTheMonth'] >= $cutoffStart[1] && 
						 $semiMonthly['currentDayOfTheMonth'] <= $cutOffEnd[1] ) {
								
						echo "11 - 25";

						$cutOffRange = range($cutoffStart[1], $cutOffEnd[1]);

						$date = new DateTime(date('Y-'.'m-'.$cutoffStart[1]));
						
						//echo $date->format('Y-m-d')."<br />";
						$cutOffArr[] = $date->format('Y-m-d');

						for ($i = 0; $i < count($cutOffRange)-1; $i++) 
						{				
							
							$date->modify('+1 day');
							//echo $date->format('Y-m-d')."<br />";
							$cutOffArr[] = $date->format('Y-m-d');

						}													

						echo '<pre>';
						var_dump($cutOffArr);
						echo '</pre>';

					} elseif ( $semiMonthly['currentDayOfTheMonth'] >= $cutoffStart[2] && 
						 $semiMonthly['currentDayOfTheMonth'] <= $currentMonthNumberOfDays ) {  // 26 to (28|29|30|31)

						 echo "26 - (28|29|30|31)";	

						//$cutOffRange = range(26, $currentMonthNumberOfDays);						
						
						//$cutOffRange = range(($cutoffStart[2]-5), $cutOffEnd[2]); //28 days - 26 to 10

						//$cutOffRange = range(($cutoffStart[2]-4), $cutOffEnd[2]); //29 days leap year - 26 to 10

						//$cutOffRange = range(($cutoffStart[2]-3), $cutOffEnd[2]); //30 days - 26 to 10

						//$cutOffRange = range(($cutoffStart[2]-2), $cutOffEnd[2]); //31 days - 26 to 10
	

						//echo count($cutOffRange);
						
						//$date = new DateTime(date('Y-'.'m-'.$cutoffStart[2]));
						$date = new DateTime(date('Y-'.'m-'.$cutoffStart[2]));
						
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

						echo '<pre>';
						var_dump($cutOffArr);
						echo '</pre>';						 

						 
							
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
						   
						   echo $prevMonthNumberOfDays = 28; // 28 Days

							$cutOffRange = range(($cutoffStart[2]-5), $cutOffEnd[2]); //28 days - 26 to 10				   

						} elseif($with29Days) {
						   
						  echo $prevMonthNumberOfDays = 29; // 29 Days

							$cutOffRange = range(($cutoffStart[2]-4), $cutOffEnd[2]); //29 days leap year - 26 to 10					  

						}

						if($with30Days) {
						   
						  echo $prevMonthNumberOfDays = 30; // 30 Days

							$cutOffRange = range(($cutoffStart[2]-3), $cutOffEnd[2]); //30 days - 26 to 10					  

						}

						if($with31Days) {
						   
						   echo $prevMonthNumberOfDays = 31; // 31 Days

							$cutOffRange = range(($cutoffStart[2]-2), $cutOffEnd[2]); //31 days - 26 to 10						   

						}		

						//$semiMonthly['previousMonth']
						//$prevMonthNumberOfDays
						
						$previousMonth = date('m', strtotime($semiMonthly['previousMonth']));

						//echo $cutoffStart[2];
						//echo count($cutOffRange);
						
						//$date = new DateTime(date('Y-'.'m-'.$cutoffStart[2]));
						$date = new DateTime(date('Y-'.$previousMonth.'-'.$cutoffStart[2]));
						
						//echo $date->format('Y-m-d')."<br />";
						$cutOffArr[] = $date->format('Y-m-d');

						for ($i = 0; $i < count($cutOffRange); $i++) 
						{				
							
							$date->modify('+1 day');
							//echo $date->format('Y-m-d')."<br />";
							$cutOffArr[] = $date->format('Y-m-d');

						}	
						
						echo '<pre>';
						var_dump($cutOffArr);
						echo '</pre>';	


					}
					



					return;
					die();

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

							if ( empty($checkTimesheet) && empty($checkSchedule) ) //If empty or no record found: insert new timesheet to employee_timesheet table
							{	

								if ($semiMonthly['currentDayOfTheMonth'] > $semiMonthly['cutOffStart'][2] || $semiMonthly['currentDayOfTheMonth'] <= $semiMonthly['cutOffEnd'][2]) 
								{

									$schedule->cutOffScheduleGenerator1($semiMonthly['cutOffStart'][2], $semiMonthly['cutOffEnd'][2], $semiMonthly['cutOffRange'][2], $semiMonthly['currentYear'], $semiMonthly['currentMonth'], $semiMonthly['currentDayOfTheMonth'], $prevMonthNumberOfDays, $user->id);

									//e.g 26-10 - 20
									$cutOffArr = explode(',', $cutOff->cutOffGenerator1($semiMonthly['cutOffStart'][2], $semiMonthly['cutOffEnd'][2], $semiMonthly['cutOffRange'][2], $semiMonthly['currentDayOfTheMonth'], $prevMonthNumberOfDays));																	

								}
								elseif ($semiMonthly['currentDayOfTheMonth'] > $semiMonthly['cutOffStart'][1] || $semiMonthly['currentDayOfTheMonth'] <= $semiMonthly['cutOffEnd'][1]) 
								{

									$schedule->cutOffScheduleGenerator2($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1], $semiMonthly['cutOffRange'][1], $semiMonthly['currentYear'], $semiMonthly['currentMonth'], $semiMonthly['currentDayOfTheMonth'], $prevMonthNumberOfDays, $user->id);

									//e.g 11-25 - 5
									$cutOffArr = explode(',', $cutOff->cutOffGenerator2($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1], $semiMonthly['cutOffRange'][1]));																

								}

								//Insert New Timesheet, Overtime, Summary blank Record
								if ( is_array($cutOffArr) )
								{

									$cutOffArr = $cutOffArr;
								  	foreach($cutOffArr as $cutOffVal) 
								  	{

								  		$dayDate = $cutOffVal;
								  		$dayDateArr[] = $dayDate;

										$schedule = DB::table('employee_schedule')->where('employee_id', $user->id)->where('schedule_date', trim($dayDate))->first();

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


										DB::table('employee_summary')
										->insert(
											array(
											'employee_id' => $user->id,
											'daydate' => $dayDate
										));									 

								  	}

								  	//return;

								  	//Session::put('dayDateArr', $dayDateArr);
								}

							}
							elseif ( !empty($checkTimesheet) && !empty($checkSchedule) )  //If not empty or has record found: update the employee_timesheet table
							{


								if ($semiMonthly['currentDayOfTheMonth'] > $semiMonthly['cutOffStart'][2] || $semiMonthly['currentDayOfTheMonth'] <= $semiMonthly['cutOffEnd'][2]) 
								{

									//$schedule->cutOffScheduleGenerator($semiMonthly['cutOffStart'][2], $semiMonthly['cutOffEnd'][2], $semiMonthly['cutOffRange'][2], $semiMonthly['currentYear'], $semiMonthly['currentMonth'], $user->id);

									//e.g 26-10 - 20
									$cutOffArr = explode(',', $cutOff->cutOffGenerator1($semiMonthly['cutOffStart'][2], $semiMonthly['cutOffEnd'][2], $semiMonthly['cutOffRange'][2], $semiMonthly['currentDayOfTheMonth'], $prevMonthNumberOfDays));

									//echo strtotime(date('Y', $cutOffArr));

									//return dd($cutOffArr);

									if ( is_array($cutOffArr) )
									{

										//Bug
									  	foreach($cutOffArr as $cutOffVal) 
									  	{
									  										  		
											//$date = new DateTime(date($cutOffVal));
											$date = new DateTime($cutOffVal);
										
											//$date->modify('-1 month'); //>> //BUG FOR 2nd Cutoff
											
											/*echo $semiMonthly['currentDayOfTheMonth'];
											echo '<br />';
											echo gettype($semiMonthly['cutOffEnd'][2]);*/
											
											echo '<br />';										
											if( ((int) $semiMonthly['currentDayOfTheMonth']) === 1 || ((int) $semiMonthly['currentDayOfTheMonth'] <= $semiMonthly['cutOffEnd'][2]) ) {										

												//$date->modify('-1 month');

												//$date->modify('-'.$prevMonthNumberOfDays.' days');
												//$date->modify('-28 days'); // set it to 30 days
												//$date->modify('-29 days'); // set it to 30 days
												//$date->modify('-30 days'); // 26-11
												//$date->modify('-31 days'); // 25-10

												/*if ( in_array($prevMonthNumberOfDays, array('28', '29')) ) {

													$date->modify('-30 days');

												} elseif ($prevMonthNumberOfDays === '30') {

													$date->modify('-30 days');

												} elseif ( $prevMonthNumberOfDays === '31' ) {

													$date->modify('-31 days');

												}*/

												//echo '<br />';											
											}

											$cutOffVal = $date->format('Y-m-d');
									  		$dayDateTempArr[] = $cutOffVal;
									  		//$dayDate = $cutOffVal;	
									  		//$dayDateArr[] = $dayDate;

											//echo $semiMonthly['cutOffStart'][2] + 1;
											//echo $semiMonthly['cutOffEnd'][2];
																			  		
											//echo $cutOffStartArr[] = $date->format('Y-m-d');
											
											if ((int) $date->format('d') === $semiMonthly['cutOffStart'][2] + 1) {

												$cutOffStartArr[] = $date->format('Y-m-d');

											} 

											if ((int) $date->format('d') === $semiMonthly['cutOffEnd'][2]) {

												$cutOffEndArr[] = $date->format('Y-m-d');

											}											

											
											/*$schedule = DB::table('employee_schedule')->where('employee_id', $user->id)->where('schedule_date', trim($dayDate))->first();

											DB::table('employee_timesheet')
											  ->where('employee_id', $user->id)
											  ->where('daydate', $dayDate)
											  ->update(array('schedule_in' => $schedule->start_time, 'schedule_out' => $schedule->end_time));*/
											
											
									  	}
									  	
									  	foreach($dayDateTempArr as $dayDateTempVal) {

									  		if( ( strtotime($dayDateTempVal) >= strtotime($cutOffStartArr[0]) ) && 
									  			( strtotime($dayDateTempVal) <= strtotime($cutOffEndArr[0]) ) ) {

												$schedule = DB::table('employee_schedule')->where('employee_id', $user->id)->where('schedule_date', trim($dayDateTempVal))->first();

												DB::table('employee_timesheet')
												  ->where('employee_id', $user->id)
												  ->where('daydate', $dayDateTempVal)
												  ->update(array('schedule_in' => $schedule->start_time, 'schedule_out' => $schedule->end_time));

												$dayDateArr[] = $dayDateTempVal;

												//echo $dayDateTempVal;
												//echo '<br />';

									  		}

									  	}

										/*$dayDateArrCount = count($dayDateArr);											
										$date = new DateTime($dayDateArr[$dayDateArrCount-1]);
										$date->add(new DateInterval('P1D'));											
										array_push($dayDateArr, $date->format('Y-m-d'));*/

									}

									//return dd($dayDateArr);

									//Session::put('dayDateArr', $dayDateArr);

								}
								elseif ($semiMonthly['currentDayOfTheMonth'] > $semiMonthly['cutOffStart'][1] || $semiMonthly['currentDayOfTheMonth'] <= $semiMonthly['cutOffEnd'][1]) 
								{

									//$schedule->cutOffScheduleGenerator($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1], $semiMonthly['cutOffRange'][1], $semiMonthly['currentYear'], $semiMonthly['currentMonth'], $user->id);

									//e.g 11-25 - 5
									$cutOffArr = explode(',', $cutOff->cutOffGenerator2($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1], $semiMonthly['cutOffRange'][1]));																

									if ( is_array($cutOffArr) )
									{
									  	
									  	foreach($cutOffArr as $cutOffVal) 
									  	{

									  		$dayDate = $cutOffVal;
									  		$dayDateArr[] = $dayDate;

											$schedule = DB::table('employee_schedule')->where('employee_id', $user->id)->where('schedule_date', trim($dayDate))->first();

											DB::table('employee_timesheet')
											  ->where('employee_id', $user->id)
											  ->where('daydate', $dayDate)
											  ->update(array('schedule_in' => $schedule->start_time, 'schedule_out' => $schedule->end_time));

									  	}

									}

									//Session::put('dayDateArr', $dayDateArr);									

								}

							} 
							elseif ( !empty($checkTimesheet) && empty($checkSchedule) ) 
							{

								//return 'empty schedule';
								//return dd($schedule->cutOffScheduleGenerator2($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1], $semiMonthly['cutOffRange'][1], $semiMonthly['currentYear'], $semiMonthly['currentMonth'], $semiMonthly['currentDayOfTheMonth'], $prevMonthNumberOfDays, $user->id));
								$dayDateArr = $schedule->cutOffScheduleGenerator2($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1], $semiMonthly['cutOffRange'][1], $semiMonthly['currentYear'], $semiMonthly['currentMonth'], $semiMonthly['currentDayOfTheMonth'], $prevMonthNumberOfDays, $user->id);

								//return dd($dayDateArr);

							}

						}

					}	


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

					Session::put('currentUserId', $user->id);
					Session::put('dayDateArr', $dayDateArr);				

					//return View::make( 'index', array( 'employee' => $employee, 'timesheet' => $timesheet, 'summary' => $summary) );
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
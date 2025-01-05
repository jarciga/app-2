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

					$cutOff = new CutoffController;
					$schedule = new SchedulesController;

					$cutOffSetting = Cutoffsetting::where('id', 1)->first();

					$semiMonthly['currentYear'] = date('Y');
					$semiMonthly['currentMonth'] = date('M');
					$semiMonthly['lastDayPerMonth'] = (int) date('t');
					$semiMonthly['currentDayOfTheMonth'] = (int) date('j'); //<
					$semiMonthly['currentDate'] = date('Y-m-d'); // <
					//$currentMonth = date('F');
					$prevMonthNumberOfDays = Date('t', strtotime($semiMonthly['currentMonth'] . " last month"));


					$semiMonthly['cutOffStart'][1] = (11-1);
					$semiMonthly['cutOffEnd'][1] = 25;
					$semiMonthly['cutOffRange'][1] = range($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1]);

					$semiMonthly['cutOffStart'][2] = (26-1);
					$semiMonthly['cutOffEnd'][2] = 10;
					$semiMonthly['cutOffRange'][2] = range($semiMonthly['cutOffStart'][2], $semiMonthly['cutOffEnd'][2]);				

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

							$checkTimesheet = Timesheet::where('employee_id', $user->employee_id)
			  										   ->where('daydate', $semiMonthly['currentDate'])
			  										   ->first();

							if ( empty($checkTimesheet) ) //If empty or no record found: insert new timesheet to employee_timesheet table
							{	

								if ($semiMonthly['currentDayOfTheMonth'] > $semiMonthly['cutOffStart'][2] || $semiMonthly['currentDayOfTheMonth'] <= $semiMonthly['cutOffEnd'][2]) 
								{

									$schedule->cutOffScheduleGenerator($semiMonthly['cutOffStart'][2], $semiMonthly['cutOffEnd'][2], $semiMonthly['cutOffRange'][2], $semiMonthly['currentYear'], $semiMonthly['currentMonth'], $user->employee_id);

									//e.g 26-10 - 20
									$cutOffArr = explode(',', $cutOff->cutOffGenerator($semiMonthly['cutOffStart'][2], $semiMonthly['cutOffEnd'][2], $semiMonthly['cutOffRange'][2]));																	

								}
								elseif ($semiMonthly['currentDayOfTheMonth'] > $semiMonthly['cutOffStart'][1] || $semiMonthly['currentDayOfTheMonth'] <= $semiMonthly['cutOffEnd'][1]) 
								{

									$schedule->cutOffScheduleGenerator($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1], $semiMonthly['cutOffRange'][1], $semiMonthly['currentYear'], $semiMonthly['currentMonth'], $user->employee_id);

									//e.g 11-25 - 5
									$cutOffArr = explode(',', $cutOff->cutOffGenerator($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1], $semiMonthly['cutOffRange'][1]));																

								}

								//Insert New Timesheet, Overtime, Summary blank Record
								if ( is_array($cutOffArr) )
								{

									$cutOffArr = $cutOffArr;
								  	foreach($cutOffArr as $cutOffVal) 
								  	{

								  		$dayDate = $cutOffVal;
								  		$dayDateArr[] = $dayDate;

										$schedule = DB::table('employee_schedule')->where('employee_id', $user->employee_id)->where('schedule_date', trim($dayDate))->first();

								  		$timesheetId = DB::table('employee_timesheet')
										 ->insertGetId(
											array(
												'employee_id' => $user->employee_id,
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
												'employee_id' => $user->employee_id,
												'timesheet_id' => $timesheetId,
												'seq_no' => $i,
												'shift' => $i
											));							

										}					


										DB::table('employee_summary')
										->insert(
											array(
											'employee_id' => $user->employee_id,
											'daydate' => $dayDate
										));										 

								  	}
								}

							}
							else //If not empty or has record found: update the employee_timesheet table
							{


								if ($semiMonthly['currentDayOfTheMonth'] > $semiMonthly['cutOffStart'][2] || $semiMonthly['currentDayOfTheMonth'] <= $semiMonthly['cutOffEnd'][2]) 
								{

									//$schedule->cutOffScheduleGenerator($semiMonthly['cutOffStart'][2], $semiMonthly['cutOffEnd'][2], $semiMonthly['cutOffRange'][2], $semiMonthly['currentYear'], $semiMonthly['currentMonth'], $user->employee_id);

									//e.g 26-10 - 20
									$cutOffArr = explode(',', $cutOff->cutOffGenerator($semiMonthly['cutOffStart'][2], $semiMonthly['cutOffEnd'][2], $semiMonthly['cutOffRange'][2]));

									//echo strtotime(date('Y', $cutOffArr));	

									if ( is_array($cutOffArr) )
									{
									  	
									  	foreach($cutOffArr as $cutOffVal) 
									  	{
								  		
											$date = new DateTime(date($cutOffVal));
											//$date->modify('-1 month'); //>> //BUG FOR 2nd Cutoff
											$date->modify('-'.$prevMonthNumberOfDays.' days'); //>> //BUG FOR 2nd Cutoff											
									  		
											$cutOffVal = $date->format('Y-m-d');									  		

									  		$dayDate = $cutOffVal;
									  		$dayDateArr[] = $dayDate;						  											  		

											$schedule = DB::table('employee_schedule')->where('employee_id', $user->employee_id)->where('schedule_date', trim($dayDate))->first();

											DB::table('employee_timesheet')
											  ->where('employee_id', $user->employee_id)
											  ->where('daydate', $dayDate)
											  ->update(array('schedule_in' => $schedule->start_time, 'schedule_out' => $schedule->end_time));

									  	}

									  	//CHECK MONTH IF IS 31 days
								  		if(31 === (int) $prevMonthNumberOfDays) {
											
											$dayDateArrCount = count($dayDateArr);											
											$date = new DateTime($dayDateArr[$dayDateArrCount-1]);
											$date->add(new DateInterval('P1D'));
											array_push($dayDateArr, $date->format('Y-m-d'));

										}

								  		//return dd($dayDateArr);
										//die();

									}

								}
								elseif ($semiMonthly['currentDayOfTheMonth'] > $semiMonthly['cutOffStart'][1] || $semiMonthly['currentDayOfTheMonth'] <= $semiMonthly['cutOffEnd'][1]) 
								{

									//$schedule->cutOffScheduleGenerator($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1], $semiMonthly['cutOffRange'][1], $semiMonthly['currentYear'], $semiMonthly['currentMonth'], $user->employee_id);

									//e.g 11-25 - 5
									$cutOffArr = explode(',', $cutOff->cutOffGenerator($semiMonthly['cutOffStart'][1], $semiMonthly['cutOffEnd'][1], $semiMonthly['cutOffRange'][1]));																

									if ( is_array($cutOffArr) )
									{
									  	
									  	foreach($cutOffArr as $cutOffVal) 
									  	{

									  		$dayDate = $cutOffVal;
									  		$dayDateArr[] = $dayDate;

											$schedule = DB::table('employee_schedule')->where('employee_id', $user->employee_id)->where('schedule_date', trim($dayDate))->first();

											DB::table('employee_timesheet')
											  ->where('employee_id', $user->employee_id)
											  ->where('daydate', $dayDate)
											  ->update(array('schedule_in' => $schedule->start_time, 'schedule_out' => $schedule->end_time));

									  	}

									}									

								}

							}

						}

					}	


					/*
					$employee = Employee::where('id', $user->employee_id)->first();
					
					$timesheet = Timesheet::where('employee_id', $user->employee_id)
										  ->whereIn('daydate', $dayDateArr)->get();

					$summary = Summary::where('employee_id', $user->employee_id)->first();
					*/

					/*
					Session::put('employee', $employee);				
					Session::put('timesheet', $timesheet);				
					Session::put('summary', $summary);
					*/					

					Session::put('currentUserId', $user->employee_id);
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
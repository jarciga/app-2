<?php

class InstallerController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /installer
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /installer/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /installer
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /installer/{id}
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
	 * GET /installer/{id}/edit
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
	 * PUT /installer/{id}
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
	 * DELETE /installer/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function showInstaller() {

		return View::make('install');	

	}


	public function processInstaller() {
		
		$data = Input::all();

		$rules = array(
					'employenumber' => 'required|alpha_num',
					'firstname' => 'required',
					'lastname' => 'required',
					'middlename' => 'required',
					'nickname' => 'required',
					'email' => 'required|unique:users|email',
					'password' => 'required|confirmed'
				);

		$validator = Validator::make($data, $rules);

		if ( $validator->fails() ) {

			$messages = $validator->messages();
		    return Redirect::to('/install')->withErrors($validator)->withInput(Input::except('password'))->withInput(Input::except('password_confirmation'));

		} else {


			//TODO: check employment date
			/*$userCount = User::count();
				
			if ( $userCount >= 0 ) {		

				$number = $userCount += 1;
				$totalDigits = strlen($number);

				$currentDate = date('Y');

				if ( $totalDigits === 1 ) {

					//$digit = 'Ones';
					$zeros = '0000';
					$employeeNumber = $currentDate.$zeros.$number;	
					
				} elseif ( $totalDigits === 2 ) {

					//$digit = 'Tens';	
					$zeros = '000';
					$employeeNumber = $currentDate.$zeros.$number;		
					
				} elseif ( $totalDigits  === 3 ) {

					//$digit = 'Hundreds';
					$zeros = '00';
					$employeeNumber = $currentDate.$zeros.$number;			

				} elseif ( $totalDigits  === 4 || $totalDigits  === 5 || $totalDigits  === 6) {

					//$digit = 'thousands';
					$zeros = '0';
					$employeeNumber = $currentDate.$zeros.$number;				

				} elseif ( $totalDigits  === 7 || $totalDigits  === 8 || $totalDigits  === 9) {

					$digit = 'millions';
					$zeros = '';
					$employeeNumber = $currentDate.$zeros.$number;					

				}

			}*/		

			$employee = new Employee;
			$employee->employee_number = trim($data["employenumber"]);						
			//$employee->employee_number = trim($employeeNumber);	
			$employee->firstname = trim(ucwords($data["firstname"]));
			$employee->lastname = trim(ucwords($data["lastname"]));							
			$employee->middle_name = trim(ucwords($data["middlename"]));
			$employee->nick_name = trim(ucwords($data["nickname"]));	

			if ( $employee->save() ) {				

		 	try {

					// Create the user
					$SentryUser = Sentry::createUser(array(
					    'email'    => trim($data['email']),
					    'employee_id' => $employee->id,
					    //'employee_number' => trim($employeeNumber),
					    'employee_number' => trim($data["employenumber"]),
					    'password' => trim($data['password']),
					    'first_name' => trim(ucwords($data['firstname'])),
					    'last_name' => trim(ucwords($data['lastname'])),
					    'activated'   => true,
					));

					if($SentryUser) {

						$userId = $SentryUser->id;

						DB::table('employee_setting')
								->insert(array(
									'employee_id' => $employee->id,
									'has_overtime' => 1,
									'has_break' => 1,
									'break_time' => '01:00:00',
									'hours_per_day' => number_format(8, 2)
									
								));						

						$administrator = DB::table('groups')->where('name', 'Administrator')->first();

						if( isset($userId) && !empty($userId) ) {
							
							DB::table('users_groups')
									->insert(array(
										'user_id' => $userId, 
										'group_id' => $administrator->id //$data["role_id"]
									));	

							$Workshift = new Workshift;

							//Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday

							$nameOfDay = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');

							for ( $i = 0; $i <= 6; $i++ ) {
								
								if ( in_array('saturday', $nameOfDay) || 
									 in_array('sunday', $nameOfDay) ) {

									$restDay = 0;
									$startTime = date('H:i:s', strtotime('08:00:00'));
									$endTime = date('H:i:s', strtotime('17:00:00'));							

								} else {

									$restDay = 1;
									$startTime = '';
									$endTime = '';

								}

								DB::table('work_shift')
									->insert(array(
										'employee_id' => $employee->id,
										'name_of_day' => ucwords($nameOfDay[$i]),
										'rest_day' => $restDay,
										'start_time' => $startTime,
										'end_time' => $endTime									
								));

							}								

						}				

						//send an email or go to another page to view users crendetial
						return Redirect::to('/login');					

					}
				
				}

				catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
				{

					$getMessages = 'Login field is required.';
					return Redirect::to('/install')->withErrors(array('login' => $getMessages));				

				}
				catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
				{
					$getMessages = 'Password field is required.';
					return Redirect::to('/install')->withErrors(array('login' => $getMessages));								
				}
				catch (Cartalyst\Sentry\Users\UserExistsException $e)
				{
					$getMessages = 'User with this login already exists.';
					return Redirect::to('/install')->withErrors(array('login' => $getMessages));								
				}

			}

		}

	}

}
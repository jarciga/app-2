<?php

class EmployeesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /employees
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /employees/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /employees
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /employees/{id}
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
	 * GET /employees/{id}/edit
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
	 * PUT /employees/{id}
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
	 * DELETE /employees/{id}
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
		//$jobTitles = JobTitle::all();

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


	public function employeeByGroup() {

		$dataArr = $this->init();
		
		$cutOffSetting = $dataArr["cutOffSetting"];
		$user = $dataArr["user"];
		$userGroup = $dataArr["userGroup"];
		$group = $dataArr["group"];
		$employee = $dataArr["employee"];
		
		if( !empty($group) ) :
		  //ADMINISTRATOR
		  if( strcmp(strtolower($group->name), strtolower('Administrator')) === 0 ) :              

		    return $employees = DB::table('employees')
		      ->where('id', '<>', $employee->id)
		      ->get();  
		    
		  //MANAGER
		  elseif( strcmp(strtolower($group->name), strtolower('Manager')) === 0 ) :                  

		    return $employees = DB::table('employees')
		      ->where('id', '<>', $employee->id)  
		      //->where('manager_id', $employee->id)
		      //->orWhere('supervisor_id', $employee->id)
		      ->where('manager_id', $employee->id)
		      ->get();  

		  //SUPERVISOR
		  elseif( strcmp(strtolower($group->name), strtolower('Supervisor')) === 0 ) :                        

		    return $employees = DB::table('employees')
		      ->where('id', '<>', $employee->id)  
		      //->where('manager_id', $employee->id)
		      //->orWhere('supervisor_id', $employee->id)
		      ->where('supervisor_id', $employee->id)
		      ->get();      

		  endif;
		endif;

	}

	public function employeeById($id = '') {

		$dataArr = $this->init();
		$employeeId = $id;

	    return $employee = DB::table('employees')
	      ->where('id', '=', $employeeId)  	      
	      ->first();      

	
	}	


	public function selectEmployeeByGroup() {

		//$employeesController = new EmployeesController;
		
		$employees = $this->employeeByGroup();
		
		//$employeeArr[0] = 'Select Employee to';
		$employeeIdArr = array();
		
		if( !empty($employees) ) {

		    foreach($employees as $employeeVal) {

		      $employeeArr[$employeeVal->id] = $employeeVal->firstname. ', ' .$employeeVal->lastname;
		      //$employeeIdArr[] = $employeeVal->id;

		      //array(2) { ["fullname"]=> string(14) "Catherine, Lor" ["id"]=> int(4) }
		     //$employeeArr["fullname"] = $employeeVal->firstname. ', ' .$employeeVal->lastname;
		      //$employeeArr["id"] = $employeeVal->id;
		      

		  	}
		  	
		  	return $employeeArr;
		  
		}

	}


	public function showUserLists() {

		$dataArr = $this->init();	
		//$dataArr["resourceId"] = 'show.user.lists';
		$dataArr["employeeArr"] = $this->selectEmployeeByGroup();

		$dataArr["listEmployees"] = Employee::paginate(15);
		return View::make('employees.lists', $dataArr);			

	}

	public function searchUserLists() {
		
		$data = Input::all();
		$dataArr = $this->init();	
		$dataArr["resourceId"] = 'showUserLists';

		$dataArr["employeeArr"] = $this->selectEmployeeByGroup();

		//ADMINISTRATOR
		if(isset($data["s"]) && !empty($data["s"])) {		
			
			/*$dataArr["listEmployees"] = DB::table('users')            
				->join('employees', 'users.employee_id', '=', 'employees.id')
				->join('users_groups', 'users_groups.user_id', '=', 'users.id')
				->join('groups', 'users_groups.group_id', '=', 'groups.id')
				->where('users.employee_number','like',  '%'.trim($data["s"]).'%')
				->orWhere('employees.firstname','like', '%'.trim(ucfirst($data["s"])).'%')
				->orWhere('employees.lastname','like', '%'.trim(ucfirst($data["s"])).'%')
				->orWhere('employees.nick_name','like', '%'.trim(ucfirst($data["s"])).'%')
				->paginate(15);*/		

			$dataArr["listEmployees"] = DB::table('employees') 
				->where('employee_number','like',  '%'.trim($data["s"]).'%')
				->orWhere('firstname','like', '%'.trim(ucfirst($data["s"])).'%')
				->orWhere('lastname','like', '%'.trim(ucfirst($data["s"])).'%')
				->orWhere('nick_name','like', '%'.trim(ucfirst($data["s"])).'%')
				->paginate(15);						

			return View::make('employees.lists', $dataArr);

		} else {

			return View::make('employees.lists', $dataArr);

		}

	}


	public function processUserSearchResultLists() {

		$data = Input::all();
		$dataArr = $this->init();	
		//$dataArr["resourceId"] = 'showUserLists';
		$dataArr["employeeArr"] = $this->selectEmployeeByGroup();

		//ADMINISTRATOR
		if(isset($data["s"]) && !empty($data["s"])) {

			//Session::put('s', $data["s"]);			
			
			$dataArr["listEmployees"] = DB::table('users')            
				->join('employees', 'users.employee_id', '=', 'employees.id')
				->join('users_groups', 'users_groups.user_id', '=', 'users.id')
				->join('groups', 'users_groups.group_id', '=', 'groups.id')
				->where('users.employee_number','like',  '%'.trim($data["s"]).'%')
				->orWhere('employees.firstname','like', '%'.trim(ucfirst($data["s"])).'%')
				->orWhere('employees.lastname','like', '%'.trim(ucfirst($data["s"])).'%')
				->orWhere('employees.nick_name','like', '%'.trim(ucfirst($data["s"])).'%')
				->paginate(15);		


			//echo "<pre>";
			//var_dump($dataArr["listEmployees"]);
			//echo "</pre>";
			
			return View::make('employees.lists', $dataArr);

		} else {

			return View::make('employees.lists', $dataArr);

		}		

	}	


	public function showUserNew() {

		$dataArr = $this->init();
		$dataArr["resourceId"] = 'admin.employees.show.user.new';
		
		//$employeesController = new EmployeesController;		
		//$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();
		$dataArr["employeeArr"] = $this->selectEmployeeByGroup();

		$companies = !empty($dataArr["companies"]) ? $dataArr["companies"] : array();
		$departments = !empty($dataArr["departments"]) ? $dataArr["departments"] : array();
		$jobTitles = !empty($dataArr["jobTitles"]) ? $dataArr["jobTitles"] : array();
		$managers = !empty($dataArr["managers"]) ? $dataArr["managers"] : array();
		$supervisors = !empty($dataArr["supervisors"]) ? $dataArr["supervisors"] : array();
		$roles = !empty($dataArr["roles"]) ? $dataArr["roles"] : array();

		//$companyArr[0] = '';
		$dataArr["companyArr"][0] = '';
		foreach ($companies as $company) {
			
		    //$companyArr[$company->id] = $company->name;
		    $dataArr["companyArr"][$company->id] = $company->name;

		}

		//$departmentArr[0] = '';
		$dataArr["departmentArr"][0] = '';
		foreach ($departments as $department) {

		    //$departmentArr[$department->id] = $department->name;
		    $dataArr["departmentArr"][$department->id] = $department->name;

		}

		//$jobTitleArr[0] = '';
		$dataArr["jobTitleArr"][0] = '';
		foreach ($jobTitles as $jobTitle) {

		    //$jobTitleArr[$jobTitle->id] = $jobTitle->name;
		    $dataArr["jobTitleArr"][$jobTitle->id] = $jobTitle->name;
		    
		}

		//$managerArr[0] = '';
		$dataArr["managerArr"][0] = '';
		foreach ($managers as $manager) {

		   $fullname = $manager->firstname.', '.$manager->lastname;
		   //$managerArr[$manager->id] = $fullname;

		   $dataArr["managerArr"][$manager->id] = $fullname;

		}

		//$supervisorArr[0] = '';
		$dataArr["supervisorArr"][0] = '';
		foreach ($supervisors as $supervisor) {

		   $fullname = $supervisor->firstname.', '.$supervisor->lastname;
		  //$supervisorArr[$supervisor->id] = $fullname;
		   $dataArr["supervisorArr"][$supervisor->id] = $fullname;

		}

		//$roleArr[0] = '';
		$dataArr["roleArr"][0] = '';
		foreach($roles as $role) {			
		    
		    //$roleArr[$role->id] = $role->name;
		    $dataArr["roleArr"][$role->id] = $role->name;

		}		

		return View::make('employees.index', $dataArr);

	}


	public function processUserNew() {

		$data = Input::all();
		$dataArr = $this->init();
		//$dataArr["resourceId"] = 'admin';

		//$employeesController = new EmployeesController;		
		//$dataArr["employeeArr"] = $this->selectEmployeeByGroup();	

		$companies = !empty($dataArr["companies"]) ? $dataArr["companies"] : array();
		$departments = !empty($dataArr["departments"]) ? $dataArr["departments"] : array();
		$jobTitles = !empty($dataArr["jobTitles"]) ? $dataArr["jobTitles"] : array();
		$managers = !empty($dataArr["managers"]) ? $dataArr["managers"] : array();
		$supervisors = !empty($dataArr["supervisors"]) ? $dataArr["supervisors"] : array();
		$roles = !empty($dataArr["roles"]) ? $dataArr["roles"] : array();

		//$companyArr[0] = '';
		$dataArr["companyArr"][0] = '';
		foreach ($companies as $company) {
			
		    $dataArr["companyArr"][$company->id] = $company->id;

		}

		//$departmentArr[0] = '';
		$dataArr["departmentArr"][0] = '';
		foreach ($departments as $department) {

		    $dataArr["departmentArr"][$department->id] = $department->id;

		}

		//$jobTitleArr[0] = '';
		$dataArr["jobTitleArr"][0] = '';
		foreach ($jobTitles as $jobTitle) {
		    
		    $dataArr["jobTitleArr"][$jobTitle->id] = $jobTitle->id;
		    
		}

		//$managerArr[0] = '';
		$dataArr["managerArr"][0] = '';
		foreach ($managers as $manager) {

		   $dataArr["managerArr"][$manager->id] = $manager->id;

		}

		//$supervisorArr[0] = '';
		$dataArr["supervisorArr"][0] = '';
		foreach ($supervisors as $supervisor) {

		   $dataArr["supervisorArr"][$supervisor->id] = $supervisor->id;

		}

		//$roleArr[0] = '';
		$dataArr["roleArr"][0] = '';
		foreach($roles as $role) {			
		    		    
		    $dataArr["roleArr"][$role->id] = $role->id;

		}		

		//VALIDATION		
		$isEmployeeType = (isset($data['is_employee_type'])) ? $data['is_employee_type'] : 'is_employee';

		if ( !empty($isEmployeeType) ) {

			if( $isEmployeeType === 'is_manager' ) { //Manager:1

				//$employeeType = 1;

				$rules = array(
							'employee_number' => 'required|unique:users',
							//'designation' => array('required', 'in:1,2,3'),
							'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),
							'firstname' => 'required',
							'lastname' => 'required',
							'middlename' => 'required',
							'nick_name' => 'required',
							'company_id' => array('required', 'in:'.implode(',', $dataArr["companyArr"])),
							//'department_head' => array('required', 'in:'.implode(',', $dataArr["departmentArr"])), //in:foo,bar,...
							//'supervisor_id' => array('required', 'in:'.implode(',', $dataArr["supervisorArr"])), //in:foo,bar,...
							'position_id' => array('required', 'in:'.implode(',', $dataArr["jobTitleArr"])),
							'role_id' => array('required', 'in:'.implode(',', $dataArr["roleArr"])),
							//'email' => 'required|unique:users|email',
							'email' => 'email',
							'password' => 'required|min:5|confirmed'
						 );

			} elseif ( $isEmployeeType === 'is_supervisor' ) { //Supervisor:2

				//$employeeType = 2;

				$rules = array(
							'employee_number' => 'required|unique:users',				
							//'designation' => array('required', 'in:1,2,3'),				
							'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),			
							'firstname' => 'required',
							'lastname' => 'required',
							'middlename' => 'required',
							'nick_name' => 'required',
							'company_id' => array('required', 'in:'.implode(',', $dataArr["companyArr"])),
							'department_id' => array('required', 'in:'.implode(',', $dataArr["departmentArr"])), //in:foo,bar,...
							'position_id' => array('required', 'in:'.implode(',', $dataArr["jobTitleArr"])),
							'department_head' => array('required', 'in:'.implode(',', $dataArr["managerArr"])),										
							'role_id' => array('required', 'in:'.implode(',', $dataArr["roleArr"])),
							//'email' => 'required|unique:users|email',
							'email' => 'email',
							'password' => 'required|min:5|confirmed'
						 );

			} elseif ( $isEmployeeType === 'is_employee' ) { //Employee:0

				//$employeeType = 0;

				$rules = array(
							'employee_number' => 'required|unique:users',				
							//'designation' => array('required', 'in:1,2,3'),				
							'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),			
							'firstname' => 'required',
							'lastname' => 'required',
							'middlename' => 'required',
							'nick_name' => 'required',
							'company_id' => array('required', 'in:'.implode(',', $dataArr["companyArr"])),
							'department_id' => array('required', 'in:'.implode(',', $dataArr["departmentArr"])), //in:foo,bar,...
							'position_id' => array('required', 'in:'.implode(',', $dataArr["jobTitleArr"])),
							'department_head' => array('required', 'in:'.implode(',', $dataArr["managerArr"])),				
							'supervisor_id' => array('required', 'in:'.implode(',', $dataArr["supervisorArr"])),
							'role_id' => array('required', 'in:'.implode(',', $dataArr["roleArr"])),
							//'email' => 'required|unique:users|email',
							'email' => 'email',
							'password' => 'required|min:5|confirmed'
						 );

			}

		} elseif( empty($isEmployeeType) ) {

				//$employeeType = 0;

				$rules = array(
							'employee_number' => 'required|unique:users',				
							//'designation'  => array('required', 'in:1,2,3'),		
							'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),							
							'firstname' => 'required',
							'lastname' => 'required',
							'middlename' => 'required',
							'nick_name' => 'required',
							'company_id' => array('required', 'in:'.implode(',', $dataArr["companyArr"])),
							'department_id' => array('required', 'in:'.implode(',', $dataArr["departmentArr"])), //in:foo,bar,...
							'position_id' => array('required', 'in:'.implode(',', $dataArr["jobTitleArr"])),
							'department_head' => array('required', 'in:'.implode(',', $dataArr["managerArr"])),				
							'supervisor_id' => array('required', 'in:'.implode(',', $dataArr["supervisorArr"])),						
							'role_id' => array('required', 'in:'.implode(',', $dataArr["roleArr"])),
							//'email' => 'required|unique:users|email',
							'email' => 'email',
							'password' => 'required|min:5|confirmed'
						 );

		}



		$validator = Validator::make($data, $rules);

		if ( $validator->fails() ) {

			$messages = $validator->messages();

			//return Redirect::to('/admin/user/new')->withErrors($validator)->withInput(Input::except('password'))->withInput(Input::except('designation'));		
		    return Redirect::to('/admin/user/new')->withErrors($validator)->withInput(Input::except('password'));

		} else {

			try
			{

				// Create the user
				$SentryUser = Sentry::createUser(array(
				    'email'    => trim($data["email"]),
				    //'employee_id' => $employee->id,
				    'employee_number' => trim(ucwords($data["employee_number"])),
				    'password' => $data['password'],
				    'first_name' => trim(ucwords($data['firstname'])),
				    'last_name' => trim(ucwords($data['lastname'])),
				    'activated'   => true,
				));

				if($SentryUser) {

					$userId = $SentryUser->id;		

					if( isset($userId) ) {
						
						DB::table('users_groups')
								->insert(array(
									'user_id' => $userId, 
									'group_id' => $data["role_id"]
								));	

						$employee = new Employee;
						$employee->employee_number = trim(ucwords($data["employee_number"]));
						$employee->firstname = trim(ucwords($data["firstname"]));
						$employee->lastname = trim(ucwords($data["lastname"]));	
						$employee->middle_name = trim(ucwords($data["middlename"]));
						$employee->nick_name = trim(ucwords($data["nick_name"]));								
						
						if( $isEmployeeType === 'is_manager' ) { //Manager:1
							
							$employee->employee_type = 1;
							$employee->manager_id = 0;
							$employee->supervisor_id = 0;	

						} elseif ( $isEmployeeType === 'is_supervisor' ) { //Supervisor:2

							$employee->employee_type = 2;
							$employee->manager_id = $data["department_head"];
							$employee->supervisor_id = 0;				

						} elseif ( $isEmployeeType === 'is_employee' ) { //Employee:0		

							$employee->employee_type = 0;
							$employee->manager_id = $data["department_head"];
							$employee->supervisor_id = $data["supervisor_id"];

						}			
						
						$employee->company_id = $data["company_id"];
						$employee->department_id = $data["department_id"];
						$employee->position_id = $data["position_id"];

						$email = (!empty($data['email'])) ? $data['email'] : strtolower($employee->firstname).'.'.strtolower($employee->lastname).'@backofficeph.com';

						if ( $employee->save() ) {

							DB::table('users')
							->where('id', $userId)
							->update(array('employee_id' => $employee->id));

							DB::table('employee_setting')
							->insert(array(
								'employee_id' => $employee->id,
								'has_overtime' => 1,
								'has_break' => 1,
								'break_time' => '01:00:00',
								'hours_per_day' => number_format(8, 2)
								
							));							

						}

					}		

					$message = 'Added Successfully.';					
	    			return Redirect::to('/admin/user/new')->with('message', $message);			
					
					////return Redirect::route('admin.dashboard');	
					////return Redirect::route('process.user.new');	
					
					////Session::put('newEmployeeId', $employee->id);	
					//return Redirect::route('adminDashboard');	
					////return Redirect::route('adminUserNewSchedule', array('newEmployeeId' => $employee->id));

				}
			
			}
			catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
			{
				echo 'Login field is required.';
			}
			catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
			{
				echo 'Password field is required.';
			}
			catch (Cartalyst\Sentry\Users\UserExistsException $e)
			{
				echo 'User with this login already exists.';
			}			

		}		

	}


	public function showUserEdit($employeeEditId) {
		
		$dataArr = $this->init();		
		$dataArr["resourceId"] = 'admin.employees.show.user.edit';
		$dataArr["employeeArr"] = $this->selectEmployeeByGroup();

		$dataArr["employeeEditId"] = (int) $employeeEditId; 

		$dataArr["employeeEditInfo"] = DB::table('employees')->where('id', $employeeEditId)->first();

		if ( isset($employeeEditId) && !empty($employeeEditId) ) {
		  $userEdit = DB::table('users')->where('employee_id', $employeeEditId)->first();  
		  $dataArr["userEdit"] = $userEdit;
		}

		if( isset($userEdit) && !empty($userEdit) ) {
		  $userGroupEdit = DB::table('users_groups')->where('user_id', $userEdit->id)->first(); 
		  $dataArr["userGroupEdit"] = $userGroupEdit;
		}

		if( isset($userGroupEdit) && !empty($userGroupEdit) ) {
		  $groupEdit = DB::table('groups')->where('id', (int) $userGroupEdit->group_id)->first(); 
		  $dataArr["groupEdit"] = $groupEdit;
		}		

		$companies = !empty($dataArr["companies"]) ? $dataArr["companies"] : array();
		$departments = !empty($dataArr["departments"]) ? $dataArr["departments"] : array();
		$jobTitles = !empty($dataArr["jobTitles"]) ? $dataArr["jobTitles"] : array();
		$managers = !empty($dataArr["managers"]) ? $dataArr["managers"] : array();
		$supervisors = !empty($dataArr["supervisors"]) ? $dataArr["supervisors"] : array();
		$roles = !empty($dataArr["roles"]) ? $dataArr["roles"] : array();

		//$companyArr[0] = '';
		$dataArr["companyArr"][0] = '';
		foreach ($companies as $company) {
			
		    //$companyArr[$company->id] = $company->name;
		    $dataArr["companyArr"][$company->id] = $company->name;

		}

		//$departmentArr[0] = '';
		$dataArr["departmentArr"][0] = '';
		foreach ($departments as $department) {

		    //$departmentArr[$department->id] = $department->name;
		    $dataArr["departmentArr"][$department->id] = $department->name;

		}

		//$jobTitleArr[0] = '';
		$dataArr["jobTitleArr"][0] = '';
		foreach ($jobTitles as $jobTitle) {

		    //$jobTitleArr[$jobTitle->id] = $jobTitle->name;
		    $dataArr["jobTitleArr"][$jobTitle->id] = $jobTitle->name;
		    
		}

		//$managerArr[0] = '';
		$dataArr["managerArr"][0] = '';
		foreach ($managers as $manager) {

		   $fullname = $manager->firstname.', '.$manager->lastname;
		   //$managerArr[$manager->id] = $fullname;

		   $dataArr["managerArr"][$manager->id] = $fullname;

		}

		//$supervisorArr[0] = '';
		$dataArr["supervisorArr"][0] = '';
		foreach ($supervisors as $supervisor) {

		   $fullname = $supervisor->firstname.', '.$supervisor->lastname;
		  //$supervisorArr[$supervisor->id] = $fullname;
		   $dataArr["supervisorArr"][$supervisor->id] = $fullname;

		}

		//$roleArr[0] = '';
		$dataArr["roleArr"][0] = '';
		foreach($roles as $role) {			
		    
		    //$roleArr[$role->id] = $role->name;
		    $dataArr["roleArr"][$role->id] = $role->name;

		}	

		//CHECK EMPLOYEE TYPE
		if ( isset($employeeEditId) && !empty($employeeEditId) ) {

		  $checkEmployeeType = DB::table('employees')->select('employee_type')->where('id', $employeeEditId)->first();

		  //dd($checkEmployeeType->employee_type);

		  if ( isset($checkEmployeeType) && !empty($checkEmployeeType) ) {

		    if ( $checkEmployeeType->employee_type === 1 ) { //is_manager
		    
		      $dataArr["isManager"] = TRUE;      
		    
		    } else {

		       $dataArr["isManager"] = '';

		    }

		    if ( $checkEmployeeType->employee_type === 2 ) { //is_supervisor
		    
		       $dataArr["isSupervisor"] = TRUE;  
		    
		    } else {

		       $dataArr["isSupervisor"] = '';

		    }

		    if ( $checkEmployeeType->employee_type === 0 ) { //is_employee
		    
		       $dataArr["isEmployee"] = TRUE;
		    
		    } else {

		       $dataArr["isEmployee"] = '';

		    }

		  }

		}

		return View::make('employees.index', $dataArr);		

	}

	public function processUserEdit($employeeEditId) {
		
		$data = Input::all();
		$dataArr = $this->init();
		$dataArr["employeeArr"] = $this->selectEmployeeByGroup();

		$dataArr["employeeEditId"] = (int) $employeeEditId; 

		$companies = Company::all();
		$departments = Department::all();
		//$jobTitles = JobTitle::all();

		$jobTitles = DB::table('job_title')->get();

		$companies = (count($companies) !== 0) ? $companies : '';
		$departments = (count($departments) !== 0) ? $departments : '';
		$jobTitles = (count($jobTitles) !== 0) ? $jobTitles : '';

		$managers = Employee::where('id', '<>', $employeeEditId)->get();
		$supervisors = Employee::where('id', '<>', $employeeEditId)->get();
		$roles = DB::table('groups')->get();


		//$companyArr[0] = '';
		$dataArr["companyArr"][0] = '';
		foreach ($companies as $company) {
			
		    $dataArr["companyArr"][$company->id] = $company->id;

		}

		//$departmentArr[0] = '';
		$dataArr["departmentArr"][0] = '';
		foreach ($departments as $department) {

		    $dataArr["departmentArr"][$department->id] = $department->id;

		}

		//$jobTitleArr[0] = '';
		$dataArr["jobTitleArr"][0] = '';
		foreach ($jobTitles as $jobTitle) {
		    
		    $dataArr["jobTitleArr"][$jobTitle->id] = $jobTitle->id;
		    
		}

		//$managerArr[0] = '';
		$dataArr["managerArr"][0] = '';
		foreach ($managers as $manager) {

		   $dataArr["managerArr"][$manager->id] = $manager->id;

		}

		//$supervisorArr[0] = '';
		$dataArr["supervisorArr"][0] = '';
		foreach ($supervisors as $supervisor) {

		   $dataArr["supervisorArr"][$supervisor->id] = $supervisor->id;

		}

		//$roleArr[0] = '';
		$dataArr["roleArr"][0] = '';
		foreach($roles as $role) {			
		    		    
		    $dataArr["roleArr"][$role->id] = $role->id;

		}		

		//VALIDATION		
		$isEmployeeType = (isset($data['is_employee_type'])) ? $data['is_employee_type'] : 'is_employee';

		if ( !empty($isEmployeeType) ) {

			if( $isEmployeeType === 'is_manager' ) { //Manager:1

				$employeeType = 1;
				$managerId = 0;
				$supervisorId = 0;

				$rules = array(
							//'designation' => array('required', 'in:1,2,3'),
							'employee_number' => 'required',				
							'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),
							'firstname' => 'required',
							'lastname' => 'required',
							'middlename' => 'required',
							'nick_name' => 'required',
							'company_id' => array('required', 'in:'.implode(',', $dataArr["companyArr"])),
							//'department_id' => array('required', 'in:'.implode(',', $dataArr["departmentArr"])), //in:foo,bar,...
							//'supervisor_id' => array('required', 'in:'.implode(',', $dataArr["supervisorArr"])), //in:foo,bar,...
							'position_id' => array('required', 'in:'.implode(',', $dataArr["jobTitleArr"])),
							'role_id' => array('required', 'in:'.implode(',', $dataArr["roleArr"])),
							//'email' => 'required|unique:users|email'
							'email' => 'email',
							'password' => 'min:5'						
						 );

			} elseif ( $isEmployeeType === 'is_supervisor' ) { //Supervisor:2

				$employeeType = 2;
				$managerId = $data["department_head"];
				$supervisorId = $data["supervisor_id"];			

				$rules = array(
							//'designation' => array('required', 'in:1,2,3'),
							'employee_number' => 'required',										
							'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),			
							'firstname' => 'required',
							'lastname' => 'required',
							'middlename' => 'required',
							'nick_name' => 'required',
							'company_id' => array('required', 'in:'.implode(',', $dataArr["companyArr"])),
							'department_id' => array('required', 'in:'.implode(',', $dataArr["departmentArr"])), //in:foo,bar,...
							'position_id' => array('required', 'in:'.implode(',', $dataArr["jobTitleArr"])),
							'department_head' => array('required', 'in:'.implode(',', $dataArr["managerArr"])),										
							'role_id' => array('required', 'in:'.implode(',', $dataArr["roleArr"])),
							//'email' => 'required|unique:users|email'
							'email' => 'email',
							'password' => 'min:5'						
						 );

			} elseif ( $isEmployeeType === 'is_employee' ) { //Employee:0

				$employeeType = 0;
				$managerId = $data["department_head"];
				$supervisorId = $data["supervisor_id"];			

				$rules = array(
							//'designation' => array('required', 'in:1,2,3'),
							'employee_number' => 'required',										
							'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),			
							'firstname' => 'required',
							'lastname' => 'required',
							'middlename' => 'required',
							'nick_name' => 'required',
							'company_id' => array('required', 'in:'.implode(',', $dataArr["companyArr"])),
							'department_id' => array('required', 'in:'.implode(',', $dataArr["departmentArr"])), //in:foo,bar,...
							'position_id' => array('required', 'in:'.implode(',', $dataArr["jobTitleArr"])),
							'department_head' => array('required', 'in:'.implode(',', $dataArr["managerArr"])),				
							'supervisor_id' => array('required', 'in:'.implode(',', $dataArr["supervisorArr"])),						
							'role_id' => array('required', 'in:'.implode(',', $dataArr["roleArr"])),
							//'email' => 'required|unique:users|email'
							'email' => 'email',
							'password' => 'min:5'						
						 );

			}

		} elseif( empty($isEmployeeType) ) {

				$employeeType = 0;
				$managerId = $data["department_head"];
				$supervisorId = $data["supervisor_id"];

				$rules = array(
							//'designation'  => array('required', 'in:1,2,3'),
							'employee_number' => 'required',								
							'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),							
							'firstname' => 'required',
							'lastname' => 'required',
							'middlename' => 'required',
							'nick_name' => 'required',
							'company_id' => array('required', 'in:'.implode(',', $dataArr["companyArr"])),
							'department_id' => array('required', 'in:'.implode(',', $dataArr["departmentArr"])), //in:foo,bar,...
							'position_id' => array('required', 'in:'.implode(',', $dataArr["jobTitleArr"])),
							'department_head' => array('required', 'in:'.implode(',', $dataArr["managerArr"])),				
							'supervisor_id' => array('required', 'in:'.implode(',', $dataArr["supervisorArr"])),						
							'role_id' => array('required', 'in:'.implode(',', $dataArr["roleArr"])),
							//'email' => 'required|unique:users|email'
							'email' => 'email',
							'password' => 'min:5'						
						 );

		}

		if( !empty($data["password"]) || $data["password"] !== '' ) {
			$rules['password'] = 'confirmed';
		}		



		$validator = Validator::make($data, $rules);

		if ( $validator->fails() ) {

			$messages = $validator->messages();

		    return Redirect::to('/admin/user/'.$employeeEditId.'/edit')->withErrors($validator)->withInput(Input::except('password'));

		} else {

			try
			{
			    
				$userUpdate = User::where('employee_id', $employeeEditId)->first();
				$userId = $userUpdate->id;

			    // Find the user using the user id
				$userUpdate = Sentry::findUserById($userId);

			    // Update the user details

				//$email = (!empty($data['email'])) ? $data['email'] : 'dummy@backofficeph.com';
				$email = (!empty($data['email'])) ? $data['email'] : strtolower($employeeUpdate->firstname).'.'.strtolower($employeeUpdate->lastname).'@backofficeph.com';
				
				$userUpdate->employee_id = $employeeEditId;
				$userUpdate->employee_number = trim(ucwords($data["employee_number"]));
				$userUpdate->first_name = $data["firstname"];
				$userUpdate->last_name = $data["lastname"];
				$userUpdate->email = trim($email); //$data["email"];
				//$userUpdate->password = Hash::make($data["password"]);	

			    // Update the user table
			    if ( $userUpdate->save() ) {
			     
			     	//return dd($userUpdate);
			     	//break;
			        // User information was updated
					//$userId = $userUpdate->id;	


					if( !empty($data["password"]) ) {
					//if( !empty($data["password"]) || $data["password"] !== '' ) {			    	

						try
						{
						    // Find the user using the user email address
						    //$user = Sentry::findUserByLogin($data["email"]);

						    $user = Sentry::findUserById($userId);

						    // Get the password reset code
						    $resetCode = $user->getResetPasswordCode();

						    // Now you can send this code to your user via email for example.

						    // OR 

							// Check if the reset password code is valid
						    if ( $user->checkResetPasswordCode($resetCode) )
						    {
						        // Attempt to reset the user password
						        if ( $user->attemptResetPassword($resetCode, $data["password"]) )
						        {
						            // Password reset passed
						            //echo 'Password reset passed';
						            
						            $user->password = $data["password"];
						            $user->save();
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
						        echo 'The provided password reset code is Invalid';					      
						    }


						}
						catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
						{
						    echo 'User was not found.';
						}

					}				

					if( isset($userId) ) {

						DB::table('users_groups')
							->where('user_id', $userId)
							->update(array(				
								'group_id' => $data["role_id"]
							));	

						$employee = new Employee;
						$employeeUpdate = Employee::where('id', $employeeEditId)->first();

						$employeeUpdate->employee_number = trim(ucwords($data["employee_number"]));
						$employeeUpdate->firstname = trim(ucwords($data["firstname"]));
						$employeeUpdate->lastname = trim(ucwords($data["lastname"]));	
						$employeeUpdate->middle_name = trim(ucwords($data["middlename"]));
						$employeeUpdate->nick_name = trim(ucwords($data["nick_name"]));						

						//$employeeUpdate->employee_type = $employeeType;		
						//$employeeUpdate->manager_id = $managerId;
						//$employeeUpdate->supervisor_id = $supervisorId;	

						if( $isEmployeeType === 'is_manager' ) { //Manager:1
							
							$employeeUpdate->employee_type = 1;
							$employeeUpdate->manager_id = 0;
							$employeeUpdate->supervisor_id = 0;	

						} elseif ( $isEmployeeType === 'is_supervisor' ) { //Supervisor:2

							$employeeUpdate->employee_type = 2;
							$employeeUpdate->manager_id = $data["department_head"];
							$employeeUpdate->supervisor_id = 0;				

						} elseif ( $isEmployeeType === 'is_employee' ) { //Employee:0		

							$employeeUpdate->employee_type = 0;
							$employeeUpdate->manager_id = $data["department_head"];
							$employeeUpdate->supervisor_id = $data["supervisor_id"];

						}		

						$employeeUpdate->company_id = $data["company_id"];
						$employeeUpdate->department_id = $data["department_id"];
						$employeeUpdate->position_id = $data["position_id"];							

					}

					if( $employeeUpdate->save() ) {

							/*DB::table('users')
							->where('id', $userId)
							->update(array('employee_id' => $employee->id));*/

							/*DB::table('employee_setting')
							->insert(array(
								'employee_id' => $employee->id,
								'has_overtime' => 1,
								'has_break' => 1,
								'break_time' => '01:00:00',
								'hours_per_day' => number_format(8, 2)
								
							));*/

					}					


					$message = 'Updated Successfully.';
					//return Redirect::route('adminDashboard');		        
					//return Redirect::route('adminHumanResourceEmployees')->with('message', $message);
	    			return Redirect::to('/admin/user/'.$employeeEditId.'/edit')->with('message', $message);	
	    			//return Redirect::route('adminUserEdit');		        
	    												

			    } else {

			    	// User information was not updated
			        echo 'User information was not updated';
			    }

			}
			catch (Cartalyst\Sentry\Users\UserExistsException $e)
			{
			    echo 'User with this login already exists.';
			}
			catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
			    echo 'User was not found.';
			}

		}		

	}


}



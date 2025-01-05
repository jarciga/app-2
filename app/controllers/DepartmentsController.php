<?php

class DepartmentsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /departments
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /departments/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /departments
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /departments/{id}
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
	 * GET /departments/{id}/edit
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
	 * PUT /departments/{id}
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
	 * DELETE /departments/{id}
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
		
		//$company = Department::where('company_date', $currentDate)->first();

		$companies = Department::all();
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
		
		//$ = Department::where('company_date', $yesterDayDate)->first();	

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
					'employeeType' => $employeeType,
					'employeeSetting' => $employeeSetting,
					'timesheets' => $timesheets,
					'timesheet' => $timesheet,
					'timesheetYesterday' => $timesheetYesterday,
					'summaryYesterday' => $summaryYesterday,
					'scheduleYesterday' => $scheduleYesterday,
					//'companyYesterday' => $companyYesterday,
					'leaveYesterday' => $leaveYesterday,
					'summaries' => $summaries,
					'summary' => $summary,
					'schedule' => $schedule,
					//'company' => $company,
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

	public function showDepartmentLists() {

		$dataArr = $this->init();	
		$dataArr["resourceId"] = 'admin.departments.show.department.lists';

		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$dataArr["departmentCount"] = Department::count();
		$dataArr["listDepartments"] = Department::paginate(15);

		return View::make('departments.lists', $dataArr);

	}	


	public function showDepartmentNew() {

		$dataArr = $this->init();
		$dataArr["resourceId"] = 'admin.departments.show.department.new';
		
		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		return View::make('departments.index', $dataArr);

	}


	public function processDepartmentNew() {

		$data = Input::all();
		$dataArr = $this->init();
		$dataArr["resourceId"] = 'admin.departments.show.department.new';

		$employeesController = new EmployeesController;		
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();	

		$rules = array(
			 	'department_name' => 'required'
			 );

		$validator = Validator::make($data, $rules);

		if ( $validator->fails() ) {

			$messages = $validator->messages();
		    return Redirect::to('/admin/department/new')->withErrors($validator);

		} else {	

			$department = new Department;
			$department->name = trim(ucwords($data['department_name']));

			if ( $department->save() ) {

				$message = 'Created Successfully.';
				return Redirect::to('/admin/department/new')->with('message', $message);

			}

		}		

	}	


	public function showDepartmentEdit($departmentEditId) {
		
		$dataArr = $this->init();
		$dataArr["resourceId"] = 'admin.departments.show.department.edit';
		
		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$dataArr["departmentEditId"] = (int) $departmentEditId; 

		$dataArr["departmentArr"] = Department::find($dataArr["departmentEditId"]);	

		return View::make('departments.index', $dataArr);		

	}


	public function processDepartmentEdit($departmentEditId) {

		$data = Input::all();		
		$dataArr = $this->init();		
		$dataArr["resourceId"] = 'admin.holidays.show.holiday.edit';

		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$dataArr["departmentEditId"] = (int) $departmentEditId; 

		$dataArr["departmentArr"] = Department::find($dataArr["departmentEditId"]);

		$rules = array(
			 	'department_name' => 'required'
			 );

		$validator = Validator::make($data, $rules);

		if ( $validator->fails() ) {

			$messages = $validator->messages();
		    return Redirect::to('/admin/department/'.$departmentEditId.'/edit')->withErrors($validator);

		} else {	



			$department = Department::find($departmentEditId);
			$department->name = trim(ucwords($data['department_name']));

			if ( $department->save() ) {

				$message = 'Updated Successfully.';				
				//return Redirect::to('/admin/department/lists')->with('message', $message);
				return Redirect::route('show.department.lists', array('message' => $message));

			}

		}
	

	}	


}
<?php

class JobtitlesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /jobtitles
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /jobtitles/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /jobtitles
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /jobtitles/{id}
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
	 * GET /jobtitles/{id}/edit
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
	 * PUT /jobtitles/{id}
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
	 * DELETE /jobtitles/{id}
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
		
		//$company = Jobtitle::where('company_date', $currentDate)->first();

		$companies = Jobtitle::all();
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
		
		//$ = Jobtitle::where('company_date', $yesterDayDate)->first();	

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

	public function showJobTitleLists() {

		$dataArr = $this->init();	
		$dataArr["resourceId"] = 'admin.jobtitles.show.jobtitle.lists';

		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$dataArr["jobTitleCount"] = Jobtitle::count();
		$dataArr["listJobTitles"] = Jobtitle::paginate(15);

		return View::make('jobtitles.lists', $dataArr);

	}	


	public function showJobTitleNew() {

		$dataArr = $this->init();
		$dataArr["resourceId"] = 'admin.jobtitles.show.jobtitle.new';
		
		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		return View::make('jobtitles.index', $dataArr);

	}


	public function processJobTitleNew() {

		$data = Input::all();
		$dataArr = $this->init();
		$dataArr["resourceId"] = 'admin.jobtitles.show.jobtitle.new';

		$employeesController = new EmployeesController;		
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();	

		$rules = array(
			 	'job_title_name' => 'required'
			 );

		$validator = Validator::make($data, $rules);

		if ( $validator->fails() ) {

			$messages = $validator->messages();
		    return Redirect::to('/admin/jobtitle/new')->withErrors($validator);

		} else {	

			$jobTitle = new Jobtitle;
			$jobTitle->name = trim(ucwords($data['job_title_name']));

			if ( $jobTitle->save() ) {

				$message = 'Created Successfully.';
				return Redirect::to('/admin/jobtitle/new')->with('message', $message);

			}

		}		

	}	


	public function showJobTitleEdit($jobTitleEditId) {
		
		$dataArr = $this->init();
		$dataArr["resourceId"] = 'admin.jobtitles.show.jobtitle.edit';
		
		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$dataArr["jobTitleEditId"] = (int) $jobTitleEditId; 

		$dataArr["jobTitleArr"] = Jobtitle::find($dataArr["jobTitleEditId"]);	

		return View::make('jobtitles.index', $dataArr);		

	}


	public function processJobTitleEdit($jobTitleEditId) {

		$data = Input::all();		
		$dataArr = $this->init();		
		$dataArr["resourceId"] = 'admin.holidays.show.holiday.edit';

		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$dataArr["jobTitleEditId"] = (int) $jobTitleEditId; 

		$dataArr["jobTitleArr"] = Jobtitle::find($dataArr["jobTitleEditId"]);

		$rules = array(
			 	'job_title_name' => 'required'
			 );

		$validator = Validator::make($data, $rules);

		if ( $validator->fails() ) {

			$messages = $validator->messages();
		    return Redirect::to('/admin/jobtitle/'.$jobTitleEditId.'/edit')->withErrors($validator);

		} else {	



			$jobTitle = Jobtitle::find($jobTitleEditId);
			$jobTitle->name = trim(ucwords($data['job_title_name']));

			if ( $jobTitle->save() ) {

				$message = 'Updated Successfully.';				
				return Redirect::to('/admin/jobtitle/lists/')->with('message', $message);

			}

		}
	

	}	

}
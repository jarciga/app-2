<?php

class HolidaysController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /holidays
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /holidays/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /holidays
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /holidays/{id}
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
	 * GET /holidays/{id}/edit
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
	 * PUT /holidays/{id}
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
	 * DELETE /holidays/{id}
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


	public function showHolidayLists() {

		$dataArr = $this->init();	
		$dataArr["resourceId"] = 'admin.holidays.show.holiday.lists';

		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$firstDateOfTheYear = new DateTime();
		$firstDateOfTheYear = $firstDateOfTheYear->setDate($firstDateOfTheYear->format('Y'), 01, 01);

		$lastDateOfTheYear = new DateTime();
		$lastDateOfTheYear = $lastDateOfTheYear->setDate($firstDateOfTheYear->format('Y'), 12, 31);

		$listHolidays = Holiday::whereBetween('holiday_date', array($firstDateOfTheYear, $lastDateOfTheYear))->paginate(100);

		if( count($listHolidays) !== 0 ) {

			$dataArr["listHolidays"] = $listHolidays;

		} else {

			$listHolidays = Holiday::paginate(15);

			if( count($listHolidays) !== 0 ) {

				$dataArr["listHolidays"] = $listHolidays;
				
			}

		}

		return View::make('holidays.lists', $dataArr);

	}

	public function showHolidayNew() {

		$dataArr = $this->init();
		$dataArr["resourceId"] = 'admin.holidays.show.holiday.new';
		
		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		return View::make('holidays.index', $dataArr);

	}


	public function processHolidayNew() {

		$data = Input::all();
		$dataArr = $this->init();
		$dataArr["resourceId"] = 'admin.holidays.show.holiday.new';

		$employeesController = new EmployeesController;		
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();	

		$rules = array(
					'description' => 'required',
					'holiday_type' => array('required', 'in:"Regular holiday", "Special non-working day"'),
					'holiday_date_from' => 'required',
					'holiday_date_to' => 'required'					
				 );

		$validator = Validator::make($data, $rules);

		if ( $validator->fails() ) {

			$messages = $validator->messages();			
		    return Redirect::to('/admin/holiday/new')->withErrors($validator)->withInput();

		} else {

			//return 'save holiday';

			//id, description, holiday_type, holiday_date_from, holiday_date_to,
			//holiday_status, recurring, length, operational_country_id

			$holidayDateFrom = new DateTime($data["holiday_date_from"]);			
			$holidayDateFrom->format('Y-m-d');
			$holidayDateTo = new DateTime($data["holiday_date_to"]);			
			$holidayDateTo->format('Y-m-d');
			$noOfDays = $holidayDateFrom->diff($holidayDateTo);

			$holiday = new Holiday;

			$holiday->description = trim(ucwords($data["description"]));
			$holiday->holiday_type = $data["holiday_type"];
			$holiday->holiday_date = date("Y-m-d", strtotime(trim(ucwords($data["holiday_date_from"]))));
			$holiday->holiday_date_from = date("Y-m-d", strtotime(trim(ucwords($data["holiday_date_from"]))));
			$holiday->holiday_date_to = date("Y-m-d", strtotime(trim(ucwords($data["holiday_date_to"]))));
			$holiday->holiday_status = (int) trim($data["is_active"]);
			$holiday->length = ($noOfDays->d + 1);

			if( $holiday->save() ) {

				$message = 'Added Successfully.';					
		    	return Redirect::to('/admin/holiday/new')->with('message', $message);					

	    	}

		}		

	}	


	public function showHolidayEdit($holidayEditId) {
		
		$dataArr = $this->init();		
		$dataArr["resourceId"] = 'admin.holidays.show.holiday.edit';

		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$dataArr["holidayEditId"] = (int) $holidayEditId; 

		$dataArr["holidayArr"] = Holiday::find($dataArr["holidayEditId"]);

		if($dataArr["holidayArr"]->holiday_status === 1) {

			$dataArr["holidayStatusCheck"] = TRUE;

		} else {

			$dataArr["holidayStatusCheck"] = FALSE;

		}

		return View::make('holidays.index', $dataArr);		

	}


	public function processHolidayEdit($holidayEditId) {

		$data = Input::all();		
		$dataArr = $this->init();		
		$dataArr["resourceId"] = 'admin.holidays.show.holiday.edit';

		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$rules = array(
					'description' => 'required',
					'holiday_type' => array('required', 'in:"Regular holiday", "Special non-working day"'),
					'holiday_date_from' => 'required',
					'holiday_date_to' => 'required'					
				 );

		$validator = Validator::make($data, $rules);

		if ( $validator->fails() ) {

			$messages = $validator->messages();			
		    return Redirect::to('/admin/holiday/'.$holidayEditId.'/edit')->withErrors($validator)->withInput();

		} else {

			//return 'save holiday';

			//id, description, holiday_type, holiday_date_from, holiday_date_to,
			//holiday_status, recurring, length, operational_country_id

			//$isActive = ( $dataArr["is_active"] === 0 ) ? (int) trim($data["is_active"]) : 0;

			$holidayDateFrom = new DateTime($data["holiday_date_from"]);			
			$holidayDateFrom->format('Y-m-d');
			$holidayDateTo = new DateTime($data["holiday_date_to"]);			
			$holidayDateTo->format('Y-m-d');
			$noOfDays = $holidayDateFrom->diff($holidayDateTo);

			$holiday = Holiday::find((int) $holidayEditId);

			$holiday->description = trim(ucwords($data["description"]));
			$holiday->holiday_type = $data["holiday_type"];
			$holiday->holiday_date = date("Y-m-d", strtotime(trim(ucwords($data["holiday_date_from"]))));
			$holiday->holiday_date_from = date("Y-m-d", strtotime(trim(ucwords($data["holiday_date_from"]))));
			$holiday->holiday_date_to = date("Y-m-d", strtotime(trim(ucwords($data["holiday_date_to"]))));
			$holiday->holiday_status = isset($data["is_active"]) ? 1 : 0;
			$holiday->length = ($noOfDays->d + 1);

			if( $holiday->save() ) {

		    	$message = 'Updated Successfully.';
	    		return Redirect::to('/admin/holiday/'.$holidayEditId.'/edit')->with('message', $message);	

	    	}

		}
	

	}					

}
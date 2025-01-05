<?php

class AdminController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /admin
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /admin/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /admin
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /admin/{id}
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
	 * GET /admin/{id}/edit
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
	 * PUT /admin/{id}
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
	 * DELETE /admin/{id}
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
					'yesterDayDate' => $yesterDayDate
					);							

	}	

	/**
	 *
	 * ADMIN: DASHBOARD
	 *
	 */
	public function showAdmin() {

		$dataArr = $this->init();
		$dataArr["resourceId"] = 'admin';

		$employeesController = new EmployeesController;		
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();
									
		$absencesController = new AbsencesController;
		$dataArr["currentAbsencesPerCutoff"] = $absencesController->currentAbsencesPerCutoff();		
		
		$groupName = $dataArr["groupName"];
		$currentUserId = $dataArr["currentUserId"];
		$cutOffSetting = $dataArr["cutOffSetting"];
		$user = $dataArr["user"];
		$userGroup = $dataArr["userGroup"];
		$group = $dataArr["group"];
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
		$leaveYesterday = $dataArr["leaveYesterday"];

		//FOR EMPLOYEE LISTS
		//$dataArr["listEmployees"] = Employee::take(5)->get();
		//$dataArr["listEmployees"] = $employeesController->employeeByGroup();
		$dataArr["listEmployees"] = $employeesController->listEmployeeByGroup();

		//FOR OVERTIME LISTS
		$dayDateArr = $dataArr["dayDateArr"];
		$currentUserId = $dataArr["currentUserId"];
		$yesterDayDate = $dataArr["yesterDayDate"];			

		$cutOffDate["from"] = $dayDateArr[0]; 
		$cutOffDate["to"] = $dayDateArr[count($dayDateArr)-1];

		$employees = $employeesController->employeeByGroup();

		if( !empty($employees) ) {

		    foreach($employees as $employeeVal) {

		    	$employeeIdArr[] = $employeeVal->id;
				//Session::put('employeeIdArr', $employeeIdArr);

		    }

		    //return dd($employeeIdArr);
			$dataArr["overtimes"] = DB::table('employees')
							->join('employee_timesheet', 'employees.id', '=', 'employee_timesheet.employee_id')
							->join('overtime', 'employee_timesheet.id', '=', 'overtime.timesheet_id')
							->whereBetween('daydate', array($cutOffDate["from"], $cutOffDate["to"]))
							->whereIn('employee_timesheet.employee_id', $employeeIdArr)							
							->where('overtime_status', '=', -1)
							->orWhere('overtime_status', '=', 1)
							->orWhere('overtime_status', '=', 0)														
							->take(5)
							->get();	

			//return View::make('overtimes.lists', $dataArr);

		} else {

			$dataArr["overtimes"] = array();

		}

		

		//GROUP
		if( !empty($groupName) ) :
		  //ADMINISTRATOR
		  if( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ) :              

		    $leaveEmployees = DB::table('employees')
		      ->where('id', '<>', $employee->id)
		      ->get();  
		    
		  //MANAGER
		  elseif( strcmp(strtolower($groupName), strtolower('Manager')) === 0 ) :                  

		   $leaveEmployees = DB::table('employees')
		      //->where('id', '<>', $employee->id)  
		      //->where('manager_id', $employee->id)
		      //->orWhere('supervisor_id', $employee->id)
		      ->where('employee_type', 2) //2: supervisor		      
		      ->where('manager_id', $employee->id)
		      ->orWhere('supervisor_id', $employee->id)		      
		      ->orWhere('id', $employee->id)
		      ->orderBy('manager_id', 'asc')
		      ->orderBy('lastname', 'asc')	
		      ->get();  

		  //SUPERVISOR
		  elseif( strcmp(strtolower($groupName), strtolower('Supervisor')) === 0 ) :                        

		    $leaveEmployees = DB::table('employees')
		      ->where('id', '<>', $employee->id)  
		      //->where('manager_id', $employee->id)
		      //->orWhere('supervisor_id', $employee->id)
		      ->where('employee_type', 0) //2: supervisor
		      ->where('supervisor_id', $employee->id)
		      ->orderBy('lastname', 'asc')		      
		      ->get();      

		  //PAYROLL
		  elseif( strcmp(strtolower($groupName), strtolower('Payroll')) === 0 ) :                        

		    $leaveEmployees = DB::table('employees')
		      ->where('id', '<>', $employee->id)
		      ->get();        		      		       

		  //HUMAN RESOURCE
		  elseif( strcmp(strtolower($groupName), strtolower('HR')) === 0 ) :                        

		    $leaveEmployees = DB::table('employees')
		      ->where('id', '<>', $employee->id)
		      ->get();  		           

		  endif;
		endif;


		//FOR LEAVE LISTS		
		if( !empty($leaveEmployees)) {
			
			foreach($leaveEmployees as $leaveEmployeeVal) {

				$leaveEmployeeArr[] = $leaveEmployeeVal->id;

			}	

		} else {

			$leaveEmployees = 0;

		}

		if( !empty($leaveEmployeeArr) ) {
			////$dataArr["leaveCount"] = Leave::count();
			$dataArr["listLeaves"] = Leave::whereIn('employee_id', $leaveEmployeeArr)
										  ->orderBy('from_date', 'desc')
										  //->take(5)
										  ->get();

			$dataArr["leaveCount"] = count($dataArr["listLeaves"]);										  

		} else {
			
			$dataArr["listLeaves"] = array();
			$dataArr["leaveCount"] = 0;

		}			  

		
		return View::make('admin.index', $dataArr);

	}


}
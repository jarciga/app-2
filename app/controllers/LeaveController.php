<?php

class LeaveController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /leave
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /leave/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /leave
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /leave/{id}
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
	 * GET /leave/{id}/edit
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
	 * PUT /leave/{id}
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
	 * DELETE /leave/{id}
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
		
		$companies = Company::all();

		$departments = Department::all();

		$jobTitles = DB::table('job_title')->get();

		$companies = (count($companies) !== 0) ? $companies : '';
		$departments = (count($departments) !== 0) ? $departments : '';
		$jobTitles = (count($jobTitles) !== 0) ? $jobTitles : '';

		$managers = Employee::where('id', '<>', $currentUserId)->get();

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
					'dayDateArr' => $dayDateArr,
					'cutOffSetting' => $cutOffSetting,
					'user' => $user,
					'userGroup' => $userGroup,
					'group' => $group,					
					'employee' => $employee,
					'employeeType' => $employeeType,
					'employeeSetting' => $employeeSetting,

					'companies' => $companies,
					'departments' => $departments,
					'jobTitles' => $jobTitles,
					'managers' => $managers,

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

	public function showLeaveRequestForm() 
	{
		
		$dataArr = $this->init();

		$currentUserId = $dataArr["currentUserId"];		
		$employee = $dataArr["employee"];
		$companies = $dataArr["companies"];
		$departments = $dataArr["departments"];
		$jobTitles = $dataArr["jobTitles"];
		$managers = $dataArr["managers"];

		$employeesController = new EmployeesController;		
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$companyArr[0] = '';
		foreach ($companies as $company) {
		    
		    $companyArr[$company->id] = $company->name;

		}

		$departmentArr[0] = '';
		foreach ($departments as $department) {

		    $departmentArr[$department->id] = $department->name;

		}

		$jobTitleArr[0] = '';
		foreach ($jobTitles as $jobTitle) {

		    $jobTitleArr[$jobTitle->id] = $jobTitle->name;

		}

		$managerArr[0] = '';
		foreach ($managers as $manager) {

		   $fullname = $manager->firstname.', '.$manager->lastname;
		    $managerArr[$manager->id] = $fullname;

		}		

		$dataArr["companyArr"] = $companyArr;
		$dataArr["departmentArr"] = $departmentArr;
		$dataArr["jobTitleArr"] = $jobTitleArr;
		$dataArr["managerArr"] = $jobTitleArr;

		$dataArr["companyId"] = $employee->company_id;
		$dataArr["departmentId"] = $employee->department_id;
		$dataArr["jobTitleId"] = $employee->position_id;
		$dataArr["supervisorId"] = $employee->supervisor_id;
		$dataArr["managerId"] = $employee->manager_id;

		return View::make("leave.leaveform", $dataArr);

	}


	public function processLeaveRequest() 
	{
		
		$data = Input::all();
		$dataArr = $this->init();

		$employeesController = new EmployeesController;		
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		( isset( $data["nature_of_leave"] ) && ($data["nature_of_leave"] === "others") ) ? $otherNatureOfLeave = TRUE : $otherNatureOfLeave = FALSE;

		if ( $otherNatureOfLeave ) {

			$rules = array(
				'nature_of_leave' => 'required',		
				'other_nature_of_leave' => 'required',			
				'with_pay' => 'required',			
				'number_of_days' => 'required',					
				'from' => 'required',
				'to' => 'required',
				'reasons' => 'required'						

			);			
	
		} elseif ( !$otherNatureOfLeave ) {

			$rules = array(
				'nature_of_leave' => 'required',		
				//'other_nature_of_leave' => 'required',			
				'with_pay' => 'required',			
				'number_of_days' => 'required',					
				'from' => 'required',
				'to' => 'required',
				'reasons' => 'required'						

			);

		}

		$validator = Validator::make($data, $rules);

		if ( $validator->fails() ) {

			$messages = $validator->messages();
	        return Redirect::to('/admin/leave/form')->withErrors($validator)->withInput();		

		} else {

			$leaveDateFrom = new DateTime($data["from"]);			
			$leaveDateFrom->format('Y-m-d');
			$leaveDateTo = new DateTime($data["to"]);			
			$leaveDateTo->format('Y-m-d');
			$noOfDays = $leaveDateFrom->diff($leaveDateTo);			

			/*DB::table('leave')
			  ->insert(array(
			  	  'employee_id' => (int) $data["employee_id"],
				  'company_id' => strtolower($data["company_id"]),
				  'position_id' => $data["position_id"],
				  'department_id' => $data["department_id"],
				  'manager_id' => $data["manager_id"],
				  'supervisor_id' => $data["supervisor_id"],				  
				  'department_head' => ucwords($data["department_head"]),

				  'nature_of_leave' => $data["nature_of_leave"],			
				  'other_nature_of_leave' => $data["other_nature_of_leave"],

				  'with_pay' => $data["with_pay"],				

				  'number_of_days' =>  $data["number_of_days"], //($noOfDays->d + 1),			
				  'from_date' => $data["from"],		
				  'to_date' => $data["to"],		
				  'reason' => $data["reasons"],
				  'status' => -1																								
			));*/	

			$leave = new Leave;

			$leave->employee_id = (int) $data["employee_id"];
			$leave->company_id = $data["company_id"];
			$leave->position_id = $data["position_id"];
			$leave->department_id = $data["department_id"];
			$leave->manager_id = $data["manager_id"];
			$leave->supervisor_id = $data["supervisor_id"];
			//$leave->department_head = '';
			$leave->nature_of_leave = $data["nature_of_leave"];
			$leave->other_nature_of_leave = trim(ucwords($data["other_nature_of_leave"]));
			$leave->with_pay = $data["with_pay"];
			$leave->number_of_days = (int) trim($data["number_of_days"]); //($noOfDays->d + 1);
			$leave->from_date = $data["from"];
			$leave->to_date = $data["to"];
			$leave->reason = trim(ucwords($data["reasons"]));
			$leave->status = -1;

			if( $leave->save() ) {

				//emailer code here
				//$employee = Employee::where('id', $currentUserId)->first();

				$message = 'Created Successfully.';
				//return Redirect::to('/admin/leave/form')->with('message', $message);						
				return Redirect::route('show.leave.form', array('message' => $message));


			}

		}	

	}	


}
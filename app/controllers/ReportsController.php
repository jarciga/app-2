<?php

class ReportsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /reports
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /reports/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /reports
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /reports/{id}
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
	 * GET /reports/{id}/edit
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
	 * PUT /reports/{id}
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
	 * DELETE /reports/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function init() 
	{

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

	public function showEmployeeSummaryReport() 
	{
		$dataArr = $this->init();

		$employeesController = new EmployeesController;				
		$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

		$dayDateArr = $dataArr["dayDateArr"];
		$currentUserId = $dataArr["currentUserId"];

		$cutOffDateFrom = $dayDateArr[0];
		$cutOffDateTo = end($dayDateArr);

		
		$dataArr["summary"] = DB::table('employee_summary')
		                     ->select(DB::raw('SUM(lates) as lates, SUM(undertime) as undertime, SUM(absent) as absent, SUM(paid_sick_leave) as paid_sick_leave, SUM(paid_vacation_leave) as paid_vacation_leave, SUM(leave_without_pay) as leave_without_pay, SUM(maternity_leave) as maternity_leave, SUM(paternity_leave) as paternity_leave,
		                      SUM(regular) as regular, SUM(regular_overtime) as regular_overtime, SUM(regular_overtime_night_diff) as regular_overtime_night_diff, SUM(regular_night_differential) as regular_night_differential, SUM(rest_day) as rest_day, SUM(rest_day_overtime) as rest_day_overtime, SUM(rest_day_overtime_night_diff) as rest_day_overtime_night_diff, SUM(rest_day_night_differential) as rest_day_night_differential,
		                       SUM(rest_day_special_holiday) as rest_day_special_holiday, SUM(rest_day_special_holiday_overtime) as rest_day_special_holiday_overtime, SUM(rest_day_special_holiday_overtime_night_diff) as rest_day_special_holiday_overtime_night_diff, SUM(rest_day_special_holiday_night_diff) as rest_day_special_holiday_night_diff, SUM(rest_day_legal_holiday) as rest_day_legal_holiday, SUM(rest_day_legal_holiday_overtime) as rest_day_legal_holiday_overtime,
		                       SUM(rest_day_legal_holiday_overtime_night_diff) as rest_day_legal_holiday_overtime_night_diff, SUM(rest_day_legal_holiday_night_diff) as rest_day_legal_holiday_night_diff, SUM(special_holiday) as special_holiday, SUM(special_holiday_overtime) as special_holiday_overtime, SUM(special_holiday_overtime_night_diff) as special_holiday_overtime_night_diff, SUM(special_holiday_night_diff) as special_holiday_night_diff, SUM(legal_holiday) as legal_holiday,
		                       SUM(legal_holiday_overtime) as legal_holiday_overtime, SUM(legal_holiday_overtime_night_diff) as legal_holiday_overtime_night_diff, SUM(legal_holiday_night_diff) as legal_holiday_night_diff'))
							 ->where('employee_id', $currentUserId)
							 ->whereBetween('daydate', [$cutOffDateFrom, $cutOffDateTo])                     
		                     ->first();	

		return View::make('summaryreport.index', $dataArr);		                     		

	}


	public function showEmployeesSummaryReports() 
		{
			$dataArr = $this->init();

			$employeesController = new EmployeesController;				
			$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

			$dayDateArr = $dataArr["dayDateArr"];
			$currentUserId = $dataArr["currentUserId"];

			$cutOffDateFrom = $dayDateArr[0];
			$cutOffDateTo = end($dayDateArr);

			//Todo get only the that are under admin, manager, supervisor
			//emplyee type 1:manager, 2:supervisor, 0:employee

			/*
			$employeeType = Employee::where('id', $currentUserId)
								->where('employee_type', 1)
								->orWhere('employee_type', 2)
								->first();		
			*/

			$employeeType = Employee::where('id', $currentUserId)
								->where('employee_type', 1)
								->orWhere('employee_type', 2)
			  					//->orWhere('employee_type', 0) 
								->first();										

			//return dd($employeeType->employee_type);	

			if ( $employeeType->employee_type === 1 ) {

				$employeeArr = Employee::where('manager_id', $currentUserId)
									  ->get();							

			} elseif ( $employeeType->employee_type === 2 ) {

				$employeeArr = Employee::where('supervisor_id', $currentUserId)
									->get();											
				
			} /*elseif ( $employeeType->employee_type === 0 ) {

				//return dd('trs');
				$employeeArr = Employee::all();
									 								
			}*/

			//return dd($employeeArr);

			//die();

			foreach($employeeArr as $employeeVal) {

				$employeeIdArr[] = $employeeVal->id;	
				//$dataArr["summariesReport"][]
				$summariesReport[] = DB::table('employee_summary')
									 ->join('employees', 'employee_summary.employee_id', '=', 'employees.id')
				                     ->select(DB::raw('firstname, lastname, employee_id, SUM(lates) as lates, SUM(undertime) as undertime, SUM(absent) as absent, SUM(paid_sick_leave) as paid_sick_leave, SUM(paid_vacation_leave) as paid_vacation_leave, SUM(leave_without_pay) as leave_without_pay, SUM(maternity_leave) as maternity_leave, SUM(paternity_leave) as paternity_leave,
				                      SUM(regular) as regular, SUM(regular_overtime) as regular_overtime, SUM(regular_overtime_night_diff) as regular_overtime_night_diff, SUM(regular_night_differential) as regular_night_differential, SUM(rest_day) as rest_day, SUM(rest_day_overtime) as rest_day_overtime, SUM(rest_day_overtime_night_diff) as rest_day_overtime_night_diff, SUM(rest_day_night_differential) as rest_day_night_differential,
				                       SUM(rest_day_special_holiday) as rest_day_special_holiday, SUM(rest_day_special_holiday_overtime) as rest_day_special_holiday_overtime, SUM(rest_day_special_holiday_overtime_night_diff) as rest_day_special_holiday_overtime_night_diff, SUM(rest_day_special_holiday_night_diff) as rest_day_special_holiday_night_diff, SUM(rest_day_legal_holiday) as rest_day_legal_holiday, SUM(rest_day_legal_holiday_overtime) as rest_day_legal_holiday_overtime,
				                       SUM(rest_day_legal_holiday_overtime_night_diff) as rest_day_legal_holiday_overtime_night_diff, SUM(rest_day_legal_holiday_night_diff) as rest_day_legal_holiday_night_diff, SUM(special_holiday) as special_holiday, SUM(special_holiday_overtime) as special_holiday_overtime, SUM(special_holiday_overtime_night_diff) as special_holiday_overtime_night_diff, SUM(special_holiday_night_diff) as special_holiday_night_diff, SUM(legal_holiday) as legal_holiday,
				                       SUM(legal_holiday_overtime) as legal_holiday_overtime, SUM(legal_holiday_overtime_night_diff) as legal_holiday_overtime_night_diff, SUM(legal_holiday_night_diff) as legal_holiday_night_diff'))
									 ->where('employee_id', $employeeVal->id)
									 ->whereBetween('daydate', [$cutOffDateFrom, $cutOffDateTo])                     
				                     ->first();

				//$dataArr["fullname"][] = $employeeVal->firstname.' '.$employeeVal->lastname;                  
			}

			//return dd($summariesReport);

			//foreach($dataArr["summariesReport"] as $summaryReport) {

			if(!empty($summariesReport)) {

				for($i = 0; $i < sizeof($summariesReport); $i++) {

					//var_dump($summariesReport[$i]);
					//echo $summariesReport[$i]->lates;

					$dataArr["summariesReportArr"][] = array(				    
						"employee_id" => $summariesReport[$i]->employee_id,
						"firstname" =>	$summariesReport[$i]->firstname,
						"lastname" =>	$summariesReport[$i]->lastname,				
						"lates" => $summariesReport[$i]->lates,
						"undertime" => $summariesReport[$i]->undertime,
						"absent" => $summariesReport[$i]->absent,
						"paid_sick_leave" => $summariesReport[$i]->paid_sick_leave,
						"paid_vacation_leave" => $summariesReport[$i]->paid_vacation_leave,
						"leave_without_pay" => $summariesReport[$i]->leave_without_pay,
						"maternity_leave" => $summariesReport[$i]->maternity_leave,
						"paternity_leave" => $summariesReport[$i]->paternity_leave,
						"regular" => $summariesReport[$i]->regular,
						"regular_overtime" => $summariesReport[$i]->regular_overtime,
						"regular_overtime_night_diff" => $summariesReport[$i]->regular_overtime_night_diff,
						"regular_night_differential" => $summariesReport[$i]->regular_night_differential,
						"rest_day" => $summariesReport[$i]->rest_day,
						"rest_day_overtime" => $summariesReport[$i]->rest_day_overtime,
						"rest_day_overtime_night_diff" => $summariesReport[$i]->rest_day_overtime_night_diff,
						"rest_day_night_differential" => $summariesReport[$i]->rest_day_night_differential,
						"rest_day_special_holiday" => $summariesReport[$i]->rest_day_special_holiday,
						"rest_day_special_holiday_overtime" => $summariesReport[$i]->rest_day_special_holiday_overtime,
						"rest_day_special_holiday_overtime_night_diff" => $summariesReport[$i]->rest_day_special_holiday_overtime_night_diff,
						"rest_day_special_holiday_night_diff" => $summariesReport[$i]->rest_day_special_holiday_night_diff,
						"rest_day_legal_holiday" => $summariesReport[$i]->rest_day_legal_holiday,
						"rest_day_legal_holiday_overtime" => $summariesReport[$i]->rest_day_legal_holiday_overtime,
						"rest_day_legal_holiday_overtime_night_diff" => $summariesReport[$i]->rest_day_legal_holiday_overtime_night_diff,
						"rest_day_legal_holiday_night_diff" => $summariesReport[$i]->rest_day_legal_holiday_night_diff,
						"special_holiday" => $summariesReport[$i]->special_holiday,
						"special_holiday_overtime" => $summariesReport[$i]->special_holiday_overtime,
						"special_holiday_overtime_night_diff" => $summariesReport[$i]->special_holiday_overtime_night_diff,
						"special_holiday_night_diff" => $summariesReport[$i]->special_holiday_night_diff,
						"legal_holiday" => $summariesReport[$i]->legal_holiday,
						"legal_holiday_overtime" => $summariesReport[$i]->legal_holiday_overtime,
						"legal_holiday_overtime_night_diff" => $summariesReport[$i]->legal_holiday_overtime_night_diff,
						"legal_holiday_night_diff" => $summariesReport[$i]->legal_holiday_night_diff
					 );		

				}

			} else {

				$dataArr["summariesReportArr"] = array();

			}

			//return dd($dataArr["summariesReportArr"]);
			//if (count($employeeIdArr) !== 0 ) {

				/*for($i = 0; $i < count($employeeIdArr); $i++ ) {

					//echo $employeeIdArr[$i];

					$dataArr["summariesReport"][] = DB::table('employee_summary')
					                     ->select(DB::raw('SUM(lates) as lates, SUM(undertime) as undertime, SUM(absent) as absent, SUM(paid_sick_leave) as paid_sick_leave, SUM(paid_vacation_leave) as paid_vacation_leave, SUM(leave_without_pay) as leave_without_pay, SUM(maternity_leave) as maternity_leave, SUM(paternity_leave) as paternity_leave,
					                      SUM(regular) as regular, SUM(regular_overtime) as regular_overtime, SUM(regular_overtime_night_diff) as regular_overtime_night_diff, SUM(regular_night_differential) as regular_night_differential, SUM(rest_day) as rest_day, SUM(rest_day_overtime) as rest_day_overtime, SUM(rest_day_overtime_night_diff) as rest_day_overtime_night_diff, SUM(rest_day_night_differential) as rest_day_night_differential,
					                       SUM(rest_day_special_holiday) as rest_day_special_holiday, SUM(rest_day_special_holiday_overtime) as rest_day_special_holiday_overtime, SUM(rest_day_special_holiday_overtime_night_diff) as rest_day_special_holiday_overtime_night_diff, SUM(rest_day_special_holiday_night_diff) as rest_day_special_holiday_night_diff, SUM(rest_day_legal_holiday) as rest_day_legal_holiday, SUM(rest_day_legal_holiday_overtime) as rest_day_legal_holiday_overtime,
					                       SUM(rest_day_legal_holiday_overtime_night_diff) as rest_day_legal_holiday_overtime_night_diff, SUM(rest_day_legal_holiday_night_diff) as rest_day_legal_holiday_night_diff, SUM(special_holiday) as special_holiday, SUM(special_holiday_overtime) as special_holiday_overtime, SUM(special_holiday_overtime_night_diff) as special_holiday_overtime_night_diff, SUM(special_holiday_night_diff) as special_holiday_night_diff, SUM(legal_holiday) as legal_holiday,
					                       SUM(legal_holiday_overtime) as legal_holiday_overtime, SUM(legal_holiday_overtime_night_diff) as legal_holiday_overtime_night_diff, SUM(legal_holiday_night_diff) as legal_holiday_night_diff'))
										  //->distinct()
										  ->where('employee_id', $employeeIdArr[$i])										  
										  ->whereBetween('daydate', [$cutOffDateFrom, $cutOffDateTo])                     
					                      ->first();

					//echo $employeeIdArr[$i];					                     

				}*/				                     

				//return dd($dataArr["summariesReport"]);
				return View::make('summaryreports.index', $dataArr);				                     

			//}
	                     

					                     		

		}	

}
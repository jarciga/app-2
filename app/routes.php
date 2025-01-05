<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/**
*
* START HERE: JUSTINO A. ARCIGA JR.
*
*/

date_default_timezone_set('Asia/Manila');

Route::get('/', function()
{
	
	//return View::make('index');

	//CHECK Users table
	if ( Schema::hasTable('users') ) {

		$userCount = User::count();

		if ( $userCount === 0 ) {
								
			return Redirect::to('install');

		} elseif ( $userCount >= 1 ) {

			return Redirect::to('/login');

		}

	}	

});


Route::get('/install', array('as' => 'show.installer', 'uses' => 'InstallerController@showInstaller' ));
Route::post('/install', array('as' => 'process.installer', 'uses' => 'InstallerController@processInstaller' ));

//Route::get('/', array('as' => '', 'uses' => 'UsersController@showLogin' ));
Route::get('/login', array('as' => 'show.login', 'uses' => 'UsersController@showLogin' ));
Route::post('/login', array('as' => 'process.login', 'uses' => 'UsersController@processLogin' ));
Route::get('/logout', array('as' => 'process.logout', 'uses' => 'UsersController@processLogout' ));

Route::get('/cutoff', array('as' => 'cutoff', 'uses' => 'UsersController@cuttOff' ));

/**
*
* TIMESHEET SECTION
*
*/

Route::get('/timesheet/servertime', array('as' => 'updateServerTime', 'uses' => 'TimesheetsController@updateServerTime') );
Route::get('/timesheet/serverdatetime', array('as' => 'getServerDateTime', 'uses' => 'TimesheetsController@getServerDateTime') );

Route::get('/timesheet', array('as' => 'show.timesheet', 'uses' => 'TimesheetsController@showTimesheet'));
Route::post('/timesheet', array('as' => 'timeClocking', 'uses' => 'TimesheetsController@timeClocking'));

Route::get('/redraw/timesheet', array('as' => 'redraw.timesheet', 'uses' => 'TimesheetsController@redrawTimesheet'));
Route::get('/redraw/summary', array('as' => 'redraw.summary', 'uses' => 'SummariesController@redrawSummary'));
Route::post('/redraw/overtimestatus', array('as' => 'redraw.overtimestatus', 'uses' => 'OvertimeController@redrawOvertimeStatus'));

/**
*
* TIMESHEET:SEARCH TIMESHEET
*
*/

Route::get('/search/timesheet/{id}', array('as' => 'show.search.timesheet', 'uses' => 'TimesheetsController@showSearchTimesheet'));
Route::post('/search/timesheet', array('as' => 'process.search.timesheet', 'uses' => 'TimesheetsController@showSearchTimesheet'));

Route::get('/redraw/search/timesheet', array('as' => 'redraw.search.timesheet', 'uses' => 'TimesheetsController@redrawSearchTimesheet'));
Route::get('/redraw/search/summary', array('as' => 'redraw.search.summary', 'uses' => 'SummariesController@redrawSearchSummary'));

Route::post('/search/timesheet/{id}', array('as' => 'update.search.timesheet', 'uses' => 'TimesheetsController@updateSearchTimesheet'));

/**
*
* TIMESHEET: PREVIOUS SEARCH TIMESHEET
*
*/

Route::get('/previous/timesheet/{id}', array('as' => 'show.previous.timesheet', 'uses' => 'TimesheetsController@showSearchTimesheet'));

Route::get('/redraw/previous/timesheet', array('as' => 'redraw.previous.timesheet', 'uses' => 'TimesheetsController@redrawPreviousSearchTimesheet'));
Route::get('/redraw/previous/summary', array('as' => 'redraw.previous.summary', 'uses' => 'SummariesController@redrawPreviousSearchSummary'));

Route::post('/previous/timesheet/{id}', array('as' => 'update.previous.timesheet', 'uses' => 'TimesheetsController@updatePreviousSearchTimesheet'));

/**
*
* TIMESHEET: LEAVE
*
**/

Route::get('/admin/leave/form', array('as' => 'show.leave.form', 'uses' => 'LeaveController@showLeaveRequestForm'));
Route::post('/admin/leave/request', array('as' => 'process.leave.request', 'uses' => 'LeaveController@processLeaveRequest'));


/**
*
* TIMESHEET: SUMMARY REPORT
*
*/

Route::get('/summary/report/employee', array('as' => 'show.summary.report.employee', 'uses' => 'ReportsController@showEmployeeSummaryReport'));
Route::get('/summary/reports/employees', array('as' => 'show.summary.reports.employees', 'uses' => 'ReportsController@showEmployeesSummaryReports'));


/**
*
* ADMINISTRATOR SECTION
*
*/

Route::get('/admin', array('as' => 'admin.dashboard', 'uses' => 'AdminController@showAdmin'));

/**
*
* DASHBOARD: ABSENCES/ABSENT
*
**/

Route::get('/absent-lists/{id}', array('as' => 'show.absent.lists', 'uses' => 'AbsencesController@showAbsentLists'));
Route::post('/absent-lists/{id}', array('as' => 'process.absent.lists', 'uses' => 'AbsencesController@processAbsentLists'));


/**
*
* EMPLOYEES
*
**/

Route::get('/admin/user/lists', array('as' => 'show.user.lists', 'uses' => 'EmployeesController@showUserLists'));
Route::post('/admin/user/lists', array('as' => 'search.user.lists', 'uses' => 'EmployeesController@searchUserLists'));

//Route::post('/admin/user/lists', array('as' => 'process.user.search.result.lists', 'uses' => 'EmployeesController@processUserSearchResultLists'));

Route::get('/admin/user/new', array('as' => 'show.user.new', 'uses' => 'EmployeesController@showUserNew'));
Route::post('/admin/user/new', array('as' => 'process.user.new', 'uses' => 'EmployeesController@processUserNew'));

Route::get('/admin/user/{employeeEditId}/edit', array('as' => 'show.user.edit', 'uses' => 'EmployeesController@showUserEdit'));
Route::post('/admin/user/{employeeEditId}/edit', array('as' => 'process.user.edit', 'uses' => 'EmployeesController@processUserEdit'));


/**
*
* SCHEDULE: HOLIDAY
*
**/

Route::get('/admin/holiday/lists', array('as' => 'show.holiday.lists', 'uses' => 'HolidaysController@showHolidayLists'));
Route::post('/admin/holiday/lists', array('as' => 'search.holiday.lists', 'uses' => 'HolidaysController@searchHolidayLists'));

Route::get('/admin/holiday/new', array('as' => 'show.holiday.new', 'uses' => 'HolidaysController@showHolidayNew'));
Route::post('/admin/holiday/new', array('as' => 'process.holiday.new', 'uses' => 'HolidaysController@processHolidayNew'));

Route::get('/admin/holiday/{holidayEditId}/edit', array('as' => 'show.holiday.edit', 'uses' => 'HolidaysController@showHolidayEdit'));
Route::post('/admin/holiday/{holidayeeEditId}/edit', array('as' => 'process.holiday.edit', 'uses' => 'HolidaysController@processHolidayEdit'));

/**
*
* SCHEDULE: SCHEDULE
*
**/

Route::get('/admin/schedule/uploader', array('as' => 'show.schedule.uploader', 'uses' => 'SchedulesController@showScheduleUploader'));


//Route::post('/admin/schedule/upload', array('as' => 'process.schedule.upload', 'uses' => 'EmployeesController@postShift'));	
Route::post( '/admin/schedule/uploaded', array('as' => 'process.schedule.uploaded', 'uses' => 'SchedulesController@processUploadedSchedule'));

Route::post('/admin/schedule/search', array('as' => 'process.schedule.search', 'uses' => 'SchedulesController@processSearchSchedule'));

Route::post('/admin/schedule/edit', array('as' => 'process.schedule.edit', 'uses' => 'SchedulesController@processScheduleEdit'));

/*Route::post('/admin/user/edit/uploaded/schedule', array('as' => 'editUploadedSchedule', 'uses' => function()
{	

	$data = Input::all();

	//Todo: Validation


	$schedule = new Schedule;

	for ( $i = 0; $i <= sizeof($data["schedule"]) - 1; $i++ ) {

		$startTime = date('H:i:s', strtotime($data["schedule"][$i]["starttimehh"] . ':' . $data["schedule"][$i]["starttimemm"]));
		$endTime = date('H:i:s', strtotime($data["schedule"][$i]["endtimehh"] . ':' . $data["schedule"][$i]["endtimemm"]));

		$startDate = date('Y-m-d', strtotime($data["schedule"][$i]["startdate"]));
		$endDate = date('Y-m-d', strtotime($data["schedule"][$i]["enddate"]));

		$startDateTime = date('Y-m-d H:i:s', strtotime($startTime.' '.$startDate));
		$endDateTime =  date('Y-m-d H:i:s', strtotime($endTime.' '.$endDate));

		$scheduleDate = $startDate;

		DB::table('employee_schedule')
			->where('id', (int) $data["schedule"][$i]["uploadedScheduleId"])
			->update(array(				
				'year' => date('Y', strtotime($startDate)), 
				'month' => date('M', strtotime($startDate)),
				'day' => date('j', strtotime($startDate)),
				'shift' => (int) $data["schedule"][$i]["shift"],
				'rest_day' => $data["schedule"][$i]["restday"],
				'start_time' => $startDateTime,
				'end_time' => $endDateTime,
				'schedule_date' => $scheduleDate
				
		));

	}	

	return Redirect::route('show.schedule.uploader');
	//return Redirect::route('adminScheduling');

}));*/

Route::get('/admin/schedule/default', array('as' => 'show.schedule.default', 'uses' => 'SchedulesController@showScheduleDefault'));

Route::post( '/admin/schedule/default', array('as' => 'process.schedule.default', 'uses' => 'SchedulesController@processScheduleDefault'));


Route::post( '/admin/schedule/default/search', array('as' => 'process.search.schedule.default', 'uses' => 'SchedulesController@processSearchScheduleDefault'));


/**
*
* TIMECLOCK: OVERTIME
*
**/

Route::get('/admin/overtime/lists', array('as' => 'show.overtime.lists', 'uses' => 'OvertimesController@showOvertimeLists'));
Route::post('/admin/overtime/lists', array('as' => 'process.overtime.lists', 'uses' => 'OvertimesController@processOvertimeLists'));


/**
*
* REQUESTS: LEAVE
*
**/

Route::get('/admin/leave/lists', array('as' => 'show.leave.lists', 'uses' => 'LeavesController@showLeaveLists'));
Route::post('/admin/leave/lists', array('as' => 'process.leave.lists', 'uses' => 'LeavesController@processLeaveLists'));


/**
*
* COMPANY: COMPANY
*
**/
Route::get('/admin/company/lists', array('as' => 'show.company.lists', 'uses' => 'CompaniesController@showCompanyLists'));
Route::post('/admin/company/lists', array('as' => 'search.company.lists', 'uses' => 'CompaniesController@searchCompanyLists'));

Route::get('/admin/company/new', array('as' => 'show.company.new', 'uses' => 'CompaniesController@showCompanyNew'));
Route::post('/admin/company/new', array('as' => 'process.company.new', 'uses' => 'CompaniesController@processCompanyNew'));

Route::get('/admin/company/{companyEditId}/edit', array('as' => 'show.company.edit', 'uses' => 'CompaniesController@showCompanyEdit'));
Route::post('/admin/company/{companyEditId}/edit', array('as' => 'process.company.edit', 'uses' => 'CompaniesController@processCompanyEdit'));

/**
*
* COMPANY: DEPARTMENT
*
**/
Route::get('/admin/department/lists', array('as' => 'show.department.lists', 'uses' => 'DepartmentsController@showDepartmentLists'));
Route::post('/admin/department/lists', array('as' => 'search.department.lists', 'uses' => 'DepartmentsController@searchDepartmentLists'));

Route::get('/admin/department/new', array('as' => 'show.department.new', 'uses' => 'DepartmentsController@showDepartmentNew'));
Route::post('/admin/department/new', array('as' => 'process.department.new', 'uses' => 'DepartmentsController@processDepartmentNew'));

Route::get('/admin/department/{departmentEditId}/edit', array('as' => 'show.department.edit', 'uses' => 'DepartmentsController@showDepartmentEdit'));
Route::post('/admin/department/{departmenteeEditId}/edit', array('as' => 'process.department.edit', 'uses' => 'DepartmentsController@processDepartmentEdit'));

/**
*
* COMPANY: JOBTITLE
*
**/

Route::get('/admin/jobtitle/lists', array('as' => 'show.jobtitle.lists', 'uses' => 'JobtitlesController@showjobTitleLists'));
Route::post('/admin/jobtitle/lists', array('as' => 'search.jobtitle.lists', 'uses' => 'JobtitlesController@searchjobTitleLists'));

Route::get('/admin/jobtitle/new', array('as' => 'show.jobtitle.new', 'uses' => 'JobtitlesController@showjobTitleNew'));
Route::post('/admin/jobtitle/new', array('as' => 'process.jobtitle.new', 'uses' => 'JobtitlesController@processjobTitleNew'));

Route::get('/admin/jobtitle/{jobTitleEditId}/edit', array('as' => 'show.jobtitle.edit', 'uses' => 'JobtitlesController@showjobTitleEdit'));
Route::post('/admin/jobtitle/{jobTitleeeEditId}/edit', array('as' => 'process.jobtitle.edit', 'uses' => 'JobtitlesController@processjobTitleEdit'));


/**
*
* Administration: Human Resources
*
*/

Route::get('/admin/hr', array('as' => 'adminHumanResource', 'uses' => function() {

	//return 'Administration: Human Resources';

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();	
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();	

	//Admin view	
	//return View::make('admin.hr', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.hr', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

		} else {

			//http://jsfiddle.net/umz8t/458/
			//http://stackoverflow.com/questions/16344354/how-to-make-blinking-flashing-text-with-css3
			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	

}));

Route::post('/admin/hr', array('as' => '', 'uses' => function() {

	$data = Input::get();

	//General Settings
	//$nightDiff['from'] = strtotime('22:00:00');
	//$nightDiff['to'] = strtotime('06:00:00');

	//return dd($data);

	//$dayOfTheWeek = date('l');	
	//$currentDate = date('Y-m-d');

	if ( -1 !== (int) $data["action"] ) {

		if ( !empty($data["check"]) ) {

			if ( is_array($data["check"]) ) {



	        	if ( sizeof($data["check"]) > 1 ) { //Mulitple check

	        		$leaves = Leave::whereIn('id', $data["check"])->get();

	        		//$totalLeave = array();

	        		foreach($leaves as $leave) {						

	        			$employeeId = $leave->employee_id;

	        			$leaveSetting = DB::table('leave_setting')->where('employee_id', $employeeId)->get();

	        			$data["action"] = (int) $data["action"];

						// Start date
						$fromDate = $leave->from_date;
						// End date
						$toDate = $leave->to_date;	        			

						$leaveDateArr = array($fromDate, $toDate);


	        			if ( ($data["action"] === 1) && 
	        				 (-1 === (int) $leave->status) ||
	        				 ($data["action"] === 1) && 
	        				 (0 === (int) $leave->status) ) { //Aprroved

	        				if ( 0 !== (int) $leaveSetting[0]->leave_credits ) {

		        				$leaveBalance = $leaveSetting[0]->leave_balance -= 1;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 1));


								//Paid Sick Leave
								if ( 'sick leave' === strtolower($leave->nature_of_leave) ) {

									$update = array('paid_sick_leave' => number_format(8, 2));

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);											
	
		   	        			//Paid Vacation Leave
		   	        			} elseif ( 'vacation leave' === strtolower($leave->nature_of_leave) ) {

		   	        				$update = array('paid_vacation_leave' => number_format(8, 2));

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);				   	        				

		   	        			//Marternity Leave
								} elseif ( 'maternity leave' === strtolower($leave->nature_of_leave) ) {				   	        				

									$update = array('maternity_leave' => number_format(8, 2));

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);											

								//Paternity Leave
								} elseif ( 'paternity leave' === strtolower($leave->nature_of_leave) ) {											

									$update = array('paternity_leave' => number_format(8, 2));

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);

								//Leave Without Pay
		   	        			} elseif ( 'leave without Pay' === strtolower($leave->nature_of_leave) ) {											

		   	        				$update = array('leave_without_pay' => number_format(8, 2));

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);				   	        				

		   	        			}

		   	        		}

   	        			} 
  	        			
  	        			if( ($data["action"] === 0) && 
	        				      (-1 === (int) $leave->status) ) { //Denied	 


	        				if ( $leaveSetting[0]->leave_balance <= (int) $leaveSetting[0]->leave_credits ) {        				

		        				/*$leaveBalance = $leaveSetting[0]->leave_balance;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));*/

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 0));

		   	        		}


	        			} elseif( ($data["action"] === 0) && 
   	        					  (1 === (int) $leave->status) ) { //Denied

	        				if ( $leaveSetting[0]->leave_balance <= (int) $leaveSetting[0]->leave_credits ) {        				

		        				$leaveBalance = $leaveSetting[0]->leave_balance += 1;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 0));

								//Paid Sick Leave
								if ( 'sick leave' === strtolower($leave->nature_of_leave) ) {

									$update = array('paid_sick_leave' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);											

		   	        			//Paid Vacation Leave
		   	        			} elseif ( 'vacation leave' === strtolower($leave->nature_of_leave) ) {

		   	        				$update = array('paid_vacation_leave' => '0');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);				   	        				

		   	        			//Marternity Leave
								} elseif ( 'maternity leave' === strtolower($leave->nature_of_leave) ) {				   	        				

									$update = array('maternity_leave' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);											

								//Paternity Leave
								} elseif ( 'paternity leave' === strtolower($leave->nature_of_leave) ) {											

									$update = array('paternity_leave' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);

								//Leave Without Pay
		   	        			} elseif ( 'leave without Pay' === strtolower($leave->nature_of_leave) ) {											

		   	        				$update = array('leave_without_pay' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);				   	        				

		   	        			}								   	        				

		   	        		}

   	        			} 

	        		}	        		


	        		return Redirect::route('adminHumanResource');

	        	} else { //Mulitple check


	        		//Code here

	        		foreach($data["check"] as $check) {
					
						$leave = Leave::whereIn('id', $data["check"])->first();

						$employeeId = $leave->employee_id;

	        			$leaveSetting = DB::table('leave_setting')->where('employee_id', $employeeId)->get();						

	        			$data["action"] = (int) $data["action"];

						if ( ($data["action"] === 1) && 
	        				 (-1 === (int) $leave->status) ||
							 ($data["action"] === 1) && 
	        				 (0 === (int) $leave->status) ) { //Aprroved

	        				if ( 0 !== (int) $leaveSetting[0]->leave_credits ) {

		        				$leaveBalance = $leaveSetting[0]->leave_balance -= 1;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 1));

		   	        		}	   	        			

   	        			} 

   	        			if( ($data["action"] === 0) && 
	        				      (-1 === (int) $leave->status) ) { //Denied	 


	        				if ( $leaveSetting[0]->leave_balance <= (int) $leaveSetting[0]->leave_credits ) {        				

		        				/*$leaveBalance = $leaveSetting[0]->leave_balance;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));*/

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 0));

		   	        		}


	        			} elseif( ($data["action"] === 0) && 
   	        					  (1 === (int) $leave->status) ) { //Denied

	        				if ( $leaveSetting[0]->leave_balance <= (int) $leaveSetting[0]->leave_credits ) {        				

		        				$leaveBalance = $leaveSetting[0]->leave_balance += 1;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 0));

		   	        		}

   	        			}      			

	        		}

	        		return Redirect::route('adminHumanResource');

	        	}
			
			}

		} else {

			return Redirect::route('adminHumanResource');

		}


	} else {

		return Redirect::route('adminHumanResource');

	}

}));


Route::get('/admin/hr/employees', array('as' => 'adminHumanResourceEmployees', 'uses' => function() {

	//return 'Administration: Human Resources';

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();	
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();	

	//Admin view	
	//return View::make('admin.hr', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.employees', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

		} else {

			//http://jsfiddle.net/umz8t/458/
			//http://stackoverflow.com/questions/16344354/how-to-make-blinking-flashing-text-with-css3
			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	

}));



/**
*
* END HERE: JUSTINO A. ARCIGA JR.
*
*/


//~!@#$%^&*()_+~!@#$%^&*()_+~!@#$%^&*()_+~!@#$%^&*()_+~!@#$%^&*()_+~!@#$%^&*()_+~!@#$%^&*()_+~!@#$%^&*()_+~!@#$%^&*()_+~!@#$%^&*()_+


/**
*
* END HERE: IVY LANE F. OPON
*
*/

/**
*
* PAYROLL: EMAIL SETTINGS
*
*/

/**
*
* Administration: Email Notification
*
*/

Route::get('/admin/emailNotifysettings', array('as' => '', 'uses' => function() {

	//return 'Administration: Payroll';

	$adminController = new AdminController;
	$dataArr = $adminController->init();	

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}	
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();	

	//Admin view
	//return View::make('admin.payroll', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.emailSettings', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);

		} else {

			//http://jsfiddle.net/umz8t/458/
			//http://stackoverflow.com/questions/16344354/how-to-make-blinking-flashing-text-with-css3
			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	

}));

// edit body of email notification

Route::post('/admin/email/emailSettings', array('uses' => function()
{
		$data = Input::all();
		
		DB::table('email_settings')
			->update(array(
						'text_email' => $data['text_email'], 
						'created_at' => date('Y-m-d-H:i:s')
					));	
		
		$email = new EmailSettings;		
		$emailInfo = $email->getTextEmail();		

		$employeeId = Session::get('currentUserId');	
		$userId = Session::get('currentUserId');

		$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
		//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

		if( !empty($userGroups) ) {

		  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

		}

		$currentUser = Sentry::getUser();	
		
		$employee = new Employee;		
		$employeeInfo = $employee->getEmployeeInfoById($employeeId);

		$getUserEmployee = DB::table('users')            
			->join('employees', 'users.employee_id', '=', 'employees.id')
			->join('users_groups', 'users_groups.user_id', '=', 'users.id')
			->join('groups', 'users_groups.group_id', '=', 'groups.id')
			->get();	

		//Admin view	
		//return View::make('admin.hr', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

		if( !empty($groups) ) {

			if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

				//return View::make('admin.employees', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);   
				return View::make('admin.emailSettings', ['emailInfo' => $emailInfo, 'employeeInfo' => $employeeInfo]); 

			} else {

				//http://jsfiddle.net/umz8t/458/
				//http://stackoverflow.com/questions/16344354/how-to-make-blinking-flashing-text-with-css3
				echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

			}

		}

					
		

}));


Route::post('/admin/emailNotify', array('as' => '', 'uses' => function() {
	
	$emailInfo = DB::table('email_settings')->get();

	$vals = Input::get('check');
	//echo $vals;
	var_dump($vals);
	$arr_emails = '';
	foreach($vals as $val => $data)
	{
		//echo $data;
		//echo $val->check;
		//echo $val[0];
		//$items[] = $val;
		$emails = DB::table('users')            
				->where('employee_id', '=', $data)
				->get();
		
		foreach($emails as $e)
		{
			$arr_emails .= $e->email . ', ';
			//foreach ($mailuserlist as $mailuser) {
				//Mail::queue('mail_template', $data, function($message) use ($e) {
				//Mail::raw('This is a test email generated by the system.', function ($message) {
				//	$message->from('ivylane.opon@backofficeph.com', $name = null);
				//	$message->to($e->email, $name = null);
				//	$message->subject('Testing mail');
					  
					/*->from('ivylane.opon@backofficeph.com', 'Mail Notification')
					  ->to($e->email)
					  ->subject('Testing mail'); */
				//});
			//}
		}
		
	}
	//print_r($items);
	$to_email = rtrim($arr_emails, ',');
	//echo $to_email;
	//echo "Email Sent please check your inbox....";
	
	$to = $to_email;
	$subject = "My subject";
	//$txt = "This is a test email generated by the system, kindly ignore.";
	$txt = $emailInfo[0]->text_email;
	$headers = "From: webmaster@example.com" . "\r\n";

	mail($to,$subject,$txt,$headers);
	echo "Email Sent please check your inbox....";
	
}));


/**
*
* PAYROLL: PAYROLL MODULE
*
*/

Route::get('/admin/payroll', array('as' => '', 'uses' => function() {

	//return 'Administration: Payroll';

	$adminController = new AdminController;
	$dataArr = $adminController->init();	

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}	
	
	
	$cutdate = date('Y-m-d');						
	$givenDate = date('Y-m-d', strtotime($cutdate));						
	$dDate = date("d", strtotime($givenDate));						
	//if($dDate >= 30)
	//{
	//	$givenDate = date('Y-m-d', strtotime("+10 days"));
	//}
	
	//elseif()
					
	if(($dDate >= 25) && ($dDate <= 30) || ($dDate >= 1 && $dDate <= 10)) 						
	{							
		$year = date("Y");							
		$month = date("m", strtotime($givenDate));							 
		$startday = 11;							
		$date = new DateTime();							
		$date->setDate($year, $month, $startday);							
		$startdate = $date->format('Y-m-d');
		
		$endday = 25 ;							
							
		$date2 = new DateTime();							
		$date2->setDate($year, $month, $endday);							
		$enddate = $date2->format('Y-m-d'); 														
							
		//echo "<h3 class='panel-title'>Please CLICK HERE to submit timesheets for cutoff: " . $startdate . " - " . $enddate . "</h3>";
	}						
	elseif(($dDate >= 10) && ($dDate <= 14) || ($dDate >= 15 && $dDate <= 25)) 						
	{							
		$year = date("Y");							
		$month = date("m", strtotime($givenDate));							
		$last_month = $month-1%12;							
		$startday = 26;							
		$date = new DateTime();							
		$date->setDate($year, $last_month, $startday);							
		$startdate = $date->format('Y-m-d');															
		$endday = 10;							
							
		$date2 = new DateTime();							
		$date2->setDate($year, $month, $endday);							
		$enddate = $date2->format('Y-m-d');														
							
		//echo "<h3 class='panel-title'>Please CLICK HERE to submit timesheets for cutoff: " . $startdate . " - " . $enddate . "</h3>";
	}
	
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();	
		
	$getTimeSubmitted = DB::table('employees')
		->join('users', 'users.employee_id', '=', 'employees.id')
		->join('timesheet_submitted', 'timesheet_submitted.head_id', '=', 'employees.id')
		->join('job_title', 'job_title.id', '=', 'employees.position_id')
		->join('departments', 'departments.id', '=', 'employees.department_id')
		->select('employees.employee_number', 'lastname', 'firstname', 'middle_name', 'cutoff_starting_date', 'cutoff_ending_date', 'email', 'job_title.name as jobtitle', 'departments.name as departmentname')
		->distinct()
		->get();
	
	$getHeads = DB::table('employees')
		->select('id')
		->where('employee_type', 1)
		->orWhere('employee_type', 2)
		->get();
		
	$getEmployees = DB::table('employees')
		->select('id')
		//->where('employee_type', 0)
		->get();
		
	$getTimesheet = DB::table('timesheet_submitted')
		->select('head_id')
		->distinct()
		->get();
	
	/*$getEmps = DB::table('timesheet_submitted')
		->select('employee_id')
		->distinct()
		//->whereBetween('daydate', array($startdate, $enddate))
		->get(); 
	*/
	
	$getEmps = DB::table('timesheet_submitted')
		->select('employee_id')
		->distinct()
		->where('cutoff_starting_date', $startdate)
		->where('cutoff_ending_date', $enddate)
		->get();
		
	$arr_heads = array();
	$arr_times = array();
	$arr_emps = array();
	$arr_emps2 = array();
	/*foreach($getHeads as $key => $value)
	{
		$arr_heads[] = (array)$value->id;
	}*/
		
	foreach($getHeads as $getHead)
	{
		$arr_heads[] = $getHead->id;
	}
	
	foreach($getTimesheet as $getTime)
	{
		$arr_times[] = $getTime->head_id;
	}
	
	foreach($getEmps as $getEmp)
	{
		$arr_emps[] = $getEmp->employee_id;
	}
	
	foreach($getEmployees as $getEmployee)
	{
		$arr_emps2[] = $getEmployee->id;
	}
	
	
	//$results = array_diff($arr_heads, $arr_times);
	//$resultemps = array_diff($arr_emps, $arr_emps2);
	$results = array_merge(array_diff($arr_heads, $arr_times), array_diff($arr_times, $arr_heads));
	
	//$resultemps = array_merge(array_diff($arr_emps, $arr_emps2), array_diff($arr_emps2, $arr_emps));
	$resultemps = array_diff($arr_emps2, $arr_emps);
	
	//var_dump($getEmps);
	//echo "<br><br>";
	//var_dump($arr_emps2);
	
	//$results2 = implode(',',$results);
	//echo $results2;
	
	$arr_names = '';
	foreach($results as $result => $value)
	{
		$arr_names .= $value . ',';
	}
	$list_ids = substr($arr_names,0,-1);
	//echo $list_ids;
	//$startdate = '2015-11-11';
	//$enddate = '2015-11-25';
	
	$getlists = DB::table('employees')
		->join('users', 'users.employee_id', '=', 'employees.id')
		//->join('timesheet_submitted', 'timesheet_submitted.head_id', '=', 'employees.id')
		->join('departments', 'departments.id', '=', 'employees.department_id')
		//->join('employee_summary', 'employee_summary.employee_id', '=', 'employees.id')
		->select('employees.id as empid', 'employees.employee_number', 'lastname', 'firstname', 'middle_name', 'email', 'employee_type', 'departments.name as departmentname')
		->distinct()
		->where('employee_type', 1)
		->orWhere('employee_type', 2)
		//->whereBetween('daydate', array($startdate, $enddate))
		//->where('cutoff_starting_date', $startdate)
		//->where('cutoff_ending_date', $enddate)
		->orderBy('departments.name', 'asc')
		//->whereIn('employees.id', $results)
		->get();
		
	$getlistemps = DB::table('employees')
		->join('users', 'users.employee_id', '=', 'employees.id')
		//->join('timesheet_submitted', 'timesheet_submitted.employee_id', '=', 'employees.id')
		->join('job_title', 'job_title.id', '=', 'employees.position_id')
		->join('departments', 'departments.id', '=', 'employees.department_id')
		->join('employee_summary', 'employee_summary.employee_id', '=', 'employees.id')
		//->join('employees', 'employees.id', '=', 'employees.supervisor_id')
		->select('employees.id as empid', 'employees.employee_number', 'lastname', 'firstname', 'middle_name', 'email', 'job_title.name as jobtitle', 'employee_type', 'departments.name as departmentname', 'employees.supervisor_id as supervisorid', 'employees.manager_id as managerid')
		->distinct()
		->whereIn('employees.id', $resultemps)
		->whereBetween('daydate', array($startdate, $enddate))
		//->where('cutoff_starting_date', $startdate)
		//->where('cutoff_ending_date', $enddate)
		->orderBy('departments.name', 'asc')
		->orderBy('lastname', 'asc')
		->get();
		
	//var_dump($enddate);
		
	/*$getpays = DB::table('employees')
		->join('timesheet_submitted', 'timesheet_submitted.employee_id', '=', 'employees.id')
		->join('job_title', 'job_title.id', '=', 'employees.position_id')
		->join('departments', 'departments.id', '=', 'employees.department_id')
		->join('employee_setting', 'employee_setting.employee_id', '=', 'employees.id')
		->select('employees.id as empid', 'employees.employee_number', 'lastname', 'firstname', 'middle_name', 'email', 'job_title.name as jobtitle', 'departments.name as departmentname', 'basicpay', 'dailysal')
		->where('is_processed', 0);
		->get();*/

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.payroll', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee, 'getTimeSubmitted' => $getTimeSubmitted, 'getlists' => $getlists, 'getlistemps' => $getlistemps, 'startdate' => $startdate, 'enddate' => $enddate));

		} else {

			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}

}));


Route::get('/admin/payroll/list', array('as' => '', 'uses' => function() {

	$adminController = new AdminController;
	$dataArr = $adminController->init();

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);
	
	//$empType = DB::table('employees')->where('user_id', $userId)->first();

	/*$data = Input::all();

	$summaries = DB::table('employee_summary')
                     ->select(DB::raw('employee_number, firstname, lastname, basicpay, dailysal, tax_status, boph_employee_summary.employee_id, SUM(lates) as lates, SUM(undertime) as undertime, SUM(absent) as absent, SUM(paid_sick_leave) as paid_sick_leave, SUM(paid_vacation_leave) as paid_vacation_leave, SUM(leave_without_pay) as leave_without_pay, SUM(maternity_leave) as maternity_leave, SUM(paternity_leave) as paternity_leave,
                       SUM(regular) as regular, SUM(regular_overtime) as regular_overtime, SUM(regular_overtime_night_diff) as regular_overtime_night_diff, SUM(regular_night_differential) as regular_night_differential, SUM(rest_day) as rest_day, SUM(rest_day_overtime) as rest_day_overtime, SUM(rest_day_overtime_night_diff) as rest_day_overtime_night_diff, SUM(rest_day_night_differential) as rest_day_night_differential,
                       SUM(rest_day_special_holiday) as rest_day_special_holiday, SUM(rest_day_special_holiday_overtime) as rest_day_special_holiday_overtime, SUM(rest_day_special_holiday_overtime_night_diff) as rest_day_special_holiday_overtime_night_diff, SUM(rest_day_special_holiday_night_diff) as rest_day_special_holiday_night_diff, SUM(rest_day_legal_holiday) as rest_day_legal_holiday, SUM(rest_day_legal_holiday_overtime) as rest_day_legal_holiday_overtime,
                       SUM(rest_day_legal_holiday_overtime_night_diff) as rest_day_legal_holiday_overtime_night_diff, SUM(rest_day_legal_holiday_night_diff) as rest_day_legal_holiday_night_diff, SUM(special_holiday) as special_holiday, SUM(special_holiday_overtime) as special_holiday_overtime, SUM(special_holiday_overtime_night_diff) as special_holiday_overtime_night_diff, SUM(special_holiday_night_diff) as special_holiday_night_diff, SUM(legal_holiday) as legal_holiday,
                       SUM(legal_holiday_overtime) as legal_holiday_overtime, SUM(legal_holiday_overtime_night_diff) as legal_holiday_overtime_night_diff, SUM(legal_holiday_night_diff) as legal_holiday_night_diff'))
					 ->join('employee_setting', 'employee_setting.employee_id', '=', 'employee_summary.employee_id')
					 ->join('employees', 'employees.id', '=', 'employee_summary.employee_id')
					 ->whereBetween('daydate', array($data["schedule_date_from"], $data["schedule_date_to"]))
					 ->groupBy('employee_summary.employee_id')			 
					 ->get();*/
	
	/*$getmaxs = DB::table('timesheet_submitted')
				->select('cutoff_starting_date', 'cutoff_ending_date')
				->where('id', DB::raw("(select max(`id`) from boph_timesheet_submitted)"))
				->first();*/
				
	$cutdate = date('Y-m-d');						
	$givenDate = date('Y-m-d', strtotime($cutdate));						
	$dDate = date("d", strtotime($givenDate));	

	//$startdate = '2015-11-11';
	//$enddate = '2015-11-25';
					
	if(($dDate >= 25) && ($dDate <= 31)  || ($dDate >= 15 && $dDate <= 24)) 						
	{							
		$year = date("Y");							
		$month = date("m", strtotime($givenDate));							
		$startday = 11;							
		$date = new DateTime();							
		$date->setDate($year, $month, $startday);							
		$startdate = $date->format('Y-m-d');														
		$endday = 25;							
							
		$date2 = new DateTime();							
		$date2->setDate($year, $month, $endday);							
		$enddate = $date2->format('Y-m-d'); 														
							
	}						
	elseif(($dDate >= 10) && ($dDate <= 14) || ($dDate >= 1 && $dDate <= 10)) 						
	{							
		$year = date("Y");							
		$month = date("m", strtotime($givenDate));							
		$last_month = $month-1%12;							
		$startday = 26;							
		$date = new DateTime();							
		$date->setDate($year, $last_month, $startday);							
		$startdate = $date->format('Y-m-d');															
		$endday = 10;							
							
		$date2 = new DateTime();							
		$date2->setDate($year, $month, $endday);							
		$enddate = $date2->format('Y-m-d');														
							
	}
					 
	//if(is_array($getmaxs))
	//{
	//$startdate = '2015-11-11';
	//$enddate = '2015-11-25';
		
		$getpays = DB::table('timesheet_submitted')
		->join('employees', 'timesheet_submitted.employee_id', '=', 'employees.id')
		->join('users', 'users.employee_id', '=', 'timesheet_submitted.employee_id')
		->join('job_title', 'job_title.id', '=', 'employees.position_id')
		->join('departments', 'departments.id', '=', 'employees.department_id')
		->join('employee_setting', 'employee_setting.employee_id', '=', 'employees.id')
		->select('employees.id as empid', 'employees.employee_number', 'lastname', 'firstname', 'middle_name', 'email', 'cutoff_starting_date', 'cutoff_ending_date', 'job_title.name as jobtitle', 'employee_type', 'departments.name as departmentname', 'employees.supervisor_id', 'employees.manager_id')
		//->where('is_supervisor', 0)
		//->where('supervisor_id', $employeeId)
		->where('is_published', 0)
		->where('cutoff_starting_date', $startdate)
		->where('cutoff_ending_date', $enddate)
		->get();	

	//var_dump($getmaxs);

	//return View::make('admin.payrollsummary', ['summaries' => $summaries, 'employeeInfo' => $employeeInfo]);
return View::make('admin.payrolllist', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo, 'getpays' => $getpays, 'startdate' => $startdate, 'enddate' => $enddate));

}));


//submit employee payroll list per supervisor


Route::get('/admin/payroll/paylist', array('as' => '', 'uses' => function() {

	$adminController = new AdminController;
	$dataArr = $adminController->init();

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	/*$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}*/
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);
	
	//$getmaxs = DB::table('timesheet_submitted')->where('id', DB::raw("(select max(`id`) from boph_timesheet_submitted)"))->first();
	
	//var_dump($getmaxs);
	
	/*$getmaxs2 = DB::table('timesheet_submitted')
			->where('head_id', $employeeId)
			->where('cutoff_starting_date', $getmaxs->cutoff_starting_date)
			->where('cutoff_ending_date', $getmaxs->cutoff_ending_date)
			->first();
	
	if(is_array($getmaxs2) || (count($getmaxs2) != 0))
	{
		
		$fromdate = date($getmaxs->cutoff_starting_date);
		$todate = date($getmaxs->cutoff_ending_date);
		$dfrom = date("d", strtotime($fromdate));
		$dto = date("d",strtotime($todate));
		
		if($dfrom == 11 && $dto == 25) 
		{
			
			$year = date("Y");
			$month = date("m", strtotime($fromdate));
			$last_month = $month-1%12;
			$startday = 26;
			$date = new DateTime();
			$date->setDate($year, $last_month, $startday);
			$startdate = $date->format('Y-m-d');
			
			$endday = 10;
			$date2 = new DateTime();
			$date2->setDate($year, $month, $endday);
			$enddate = $date2->format('Y-m-d');
		
		}
		elseif($dfrom == 26 && $dto == 10)
		{
		
			$year = date("Y");
			$month = date("m", strtotime($todate));
			$startday = 11;
			$date = new DateTime();
			$date->setDate($year, $month, $startday);
			$startdate = $date->format('Y-m-d');
			
			$endday = 25;
			$date2 = new DateTime();
			$date2->setDate($year, $month, $endday);
			$enddate = $date2->format('Y-m-d');
		
		}
	}
	else
	{
		
		$startdate = date($getmaxs->cutoff_starting_date);
		$enddate = date($getmaxs->cutoff_ending_date);
		
		$dfrom = date("d", strtotime($startdate));
		$dto = date("d", strtotime($enddate));
		
		if($dfrom == 11 && $dto == 25) 
		{
			
			$year = date("Y");
			$month = date("m", strtotime($enddate));
			$startday = 11;
			$date = new DateTime();
			$date->setDate($year, $month, $startday);
			$startdate = $date->format('Y-m-d');
			
			$endday = 25;
			$date2 = new DateTime();
			$date2->setDate($year, $month, $endday);
			$enddate = $date2->format('Y-m-d');
			
		
		}
		elseif($dfrom == 26 && $dto == 10)
		{
			
			$year = date("Y");
			$month = date("m", strtotime($startdate));
			$last_month = $month-1%12;
			$startday = 26;
			$date = new DateTime();
			$date->setDate($year, $last_month, $startday);
			$startdate = $date->format('Y-m-d');
			
			$endday = 10;
			$date2 = new DateTime();
			$date2->setDate($year, $month, $endday);
			$enddate = $date2->format('Y-m-d');
		
		} 
			
	} */
	
	//$empType = DB::table('employees')->where('user_id', $userId)->first();

	//$data = Input::all();

	  //$getpays = DB::table('timesheet_submitted')
	  //->join('employees', 'timesheet_submitted.employee_id', '=', 'employees.id')
	  
	
	$cutdate = date('Y-m-d');						
	$givenDate = date('Y-m-d', strtotime($cutdate));						
	$dDate = date("d", strtotime($givenDate));						
					
	if(($dDate >= 25) && ($dDate <= 29)) 						
	{							
		$year = date("Y");							
		$month = date("m", strtotime($givenDate));							
		$startday = 11;							
		$date = new DateTime();							
		$date->setDate($year, $month, $startday);							
		$startdate = $date->format('Y-m-d');														
		$endday = 25;							
							
		$date2 = new DateTime();							
		$date2->setDate($year, $month, $endday);							
		$enddate = $date2->format('Y-m-d'); 														
							
	}						
	elseif(($dDate >= 10) && ($dDate <= 14)) 						
	{							
		$year = date("Y");							
		$month = date("m", strtotime($givenDate));							
		$last_month = $month-1%12;							
		$startday = 26;							
		$date = new DateTime();							
		$date->setDate($year, $last_month, $startday);							
		$startdate = $date->format('Y-m-d');															
		$endday = 10;							
							
		$date2 = new DateTime();							
		$date2->setDate($year, $month, $endday);							
		$enddate = $date2->format('Y-m-d');														
							
	} 
	
	//$startdate = '2015-11-11';
	//$enddate = '2015-11-25';
	  
	$emptype = DB::table('employees')->where('id', $userId)->first(); 

	//$creatives = DB::table('employees')->where('id', $userId)
	
	//echo "Employee Type is: " . $emptype->employee_type;
	
	if($emptype->employee_type == 2)
	{
		$getpays = DB::table('employees')
		//->join('users', 'users.employee_id', '=', 'timesheet_submitted.employee_id')
		->join('employee_summary', 'employees.id', '=', 'employee_summary.employee_id')
		->join('job_title', 'job_title.id', '=', 'employees.position_id')
		->join('departments', 'departments.id', '=', 'employees.department_id')
		->join('employee_setting', 'employee_setting.employee_id', '=', 'employees.id')
		->select('employees.id as empid', 'employees.employee_number', 'lastname', 'firstname', 'middle_name', 'job_title.name as jobtitle', 'employee_type', 'departments.name as departmentname', 'employees.supervisor_id', 'employees.manager_id')
		->distinct()
		->where('supervisor_id', $employeeId)
		->whereBetween('daydate', array($startdate, $enddate))
		//->where('is_supervisor', 0)
		//->where('is_processed', 0)
		//->whereIn('employees.id', $results)
		->get();
	}
	/*elseif($emptype->employee_type == 1 && $emptype->employee_number == 10209)
	{
		$getpays = DB::table('employees')
		//->join('users', 'users.employee_id', '=', 'timesheet_submitted.employee_id')
		->join('employee_summary', 'employees.id', '=', 'employee_summary.employee_id')
		->join('job_title', 'job_title.id', '=', 'employees.position_id')
		->join('departments', 'departments.id', '=', 'employees.department_id')
		->join('employee_setting', 'employee_setting.employee_id', '=', 'employees.id')
		->select('employees.id as empid', 'employees.employee_number', 'lastname', 'firstname', 'middle_name', 'job_title.name as jobtitle', 'employee_type', 'departments.name as departmentname', 'employees.supervisor_id', 'employees.manager_id')
		->distinct()
		//->where('manager_id', $userId)
		//->where('employee_type', 2)
		//->orWhere('employees.id', $userId)
		->whereIn('employees.id', array())
		->whereBetween('daydate', array($startdate, $enddate))
		//->where('is_supervisor', 0)
		//->where('is_processed', 0)
		
		->get();
	}*/
	elseif($emptype->employee_type == 1)
	{
		$getpays = DB::table('employees')
		//->join('users', 'users.employee_id', '=', 'timesheet_submitted.employee_id')
		->join('employee_summary', 'employees.id', '=', 'employee_summary.employee_id')
		->join('job_title', 'job_title.id', '=', 'employees.position_id')
		->join('departments', 'departments.id', '=', 'employees.department_id')
		->join('employee_setting', 'employee_setting.employee_id', '=', 'employees.id')
		->select('employees.id as empid', 'employees.employee_number', 'lastname', 'firstname', 'middle_name', 'job_title.name as jobtitle', 'employee_type', 'departments.name as departmentname', 'employees.supervisor_id', 'employees.manager_id')
		->distinct()
		->where('manager_id', $userId)
		->where('employee_type', 2)
		->orWhere('supervisor_id', $userId)
		->orWhere('employees.id', $userId)
		->whereBetween('daydate', array($startdate, $enddate))
		//->where('is_supervisor', 0)
		//->where('is_processed', 0)
		//->whereIn('employees.id', $results)
		->get();
	}
	
	
	
	
	//echo "value is: " . $employeeId;
	
	

	//return View::make('admin.payrollsummary', ['summaries' => $summaries, 'employeeInfo' => $employeeInfo]);
	//return View::make('admin.paylist', ['dataArr' => $dataArr, 'employeeInfo' => $employeeInfo, 'getpays' => $getpays, 'getmaxs' => $getmaxs, 'employeeId' => $employeeId]);
	
		return View::make('admin.paylist', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo, 'getpays' => $getpays, 'employeeId' => $employeeId, 'startdate' => $startdate, 'enddate' => $enddate));

}));


Route::post('/admin/payroll/computation', array('as' => '', 'uses' => function() {

	//$data = Input::all();

	//$data['employee_number'] = Input::get('employee_number');

	/*$employeeNumber = Employee::where('employee_number', '=', trim($data['employee_number']))->first();

	$uploadedSchedules = Schedule::where('employee_id', '=', trim($employeeNumber->id))
									->whereBetween('schedule_date', array($data["schedule_date_from"], $data["schedule_date_to"]))
									->get();	

	$employeeId = Session::get('currentUserId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	
	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();



	return View::make('admin.scheduling', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee, 'uploadedSchedules' => $uploadedSchedules]);*/
	//return View::make('admin.scheduling', ['employeeInfo' => $employeeInfo, 'defaultSchedules' => $defaultSchedules]);

	$adminController = new AdminController;
	$dataArr = $adminController->init();
	
	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$data = Input::all();
	$vals = Input::get('check');
	
	Session::put('values', $vals);
	Session::put('cutfrom', $data["schedule_date_from"]);
	Session::put('cutto', $data["schedule_date_to"]);
	
	//var_dump($vals);

	/*$summaries = DB::table('employee_summary')
                     ->select(DB::raw('employee_number, firstname, lastname, basicpay, dailysal, tax_status, boph_employee_summary.employee_id, SUM(lates) as lates, SUM(undertime) as undertime, SUM(absent) as absent, SUM(paid_sick_leave) as paid_sick_leave, SUM(paid_vacation_leave) as paid_vacation_leave, SUM(leave_without_pay) as leave_without_pay, SUM(maternity_leave) as maternity_leave, SUM(paternity_leave) as paternity_leave,
                       SUM(regular) as regular, SUM(regular_overtime) as empregular_overtime, SUM(regular_overtime_night_diff) as rond , SUM(regular_night_differential) as rnd, SUM(rest_day) as rd, SUM(rest_day_overtime) as rdot, SUM(rest_day_overtime_night_diff) as rdond, SUM(rest_day_night_differential) as rdnd,
                       SUM(rest_day_special_holiday) as rdspl_holiday, SUM(rest_day_special_holiday_overtime) as rdsho, SUM(rest_day_special_holiday_overtime_night_diff) as rdshond, SUM(rest_day_special_holiday_night_diff) as rdshnd, SUM(rest_day_legal_holiday) as rdlh, SUM(rest_day_legal_holiday_overtime) as rdlhot,
                       SUM(rest_day_legal_holiday_overtime_night_diff) as rdlhond, SUM(rest_day_legal_holiday_night_diff) as rdlhnd, SUM(special_holiday) as splemp_holiday, SUM(special_holiday_overtime) as sho, SUM(special_holiday_overtime_night_diff) as shond, SUM(special_holiday_night_diff) as shnd, SUM(legal_holiday) as lh,
                       SUM(legal_holiday_overtime) as lho, SUM(legal_holiday_overtime_night_diff) as lhond, SUM(legal_holiday_night_diff) as lhnd'))
					 ->join('employee_setting', 'employee_setting.employee_id', '=', 'employee_summary.employee_id')
					 ->join('employees', 'employees.id', '=', 'employee_summary.employee_id')
					 ->join('timesheet_submitted', 'timesheet_submitted.employee_id', '=', 'employee_summary.employee_id')
					 ->whereBetween('daydate', array($data["schedule_date_from"], $data["schedule_date_to"]))
					 //->where('is_processed', 0)
					 ->whereIn('employees.id', $vals)
					 ->groupBy('employee_summary.employee_id')			 
					 ->get();*/

	//new version
	$summaries = DB::table('employee_summary')
                     ->select(DB::raw('employee_number, firstname, lastname, basicpay, dailysal, tax_status, company_loan, hdmf_loan, hmo_dep, sss_salary_loan, telephone_charges, cash_advance, ecola, deminimis, mobile_allowance, previous_payroll, reimbursible_allowance, retro_payment, transportation_allowance, previous_OT_adjustment, 
					   previous_payroll_adjustment, retro_payment_adjustment, boph_employee_summary.employee_id, SUM(lates) as lates, SUM(undertime) as undertime, SUM(absent) as absent, SUM(paid_sick_leave) as paid_sick_leave, 
					   SUM(paid_vacation_leave) as paid_vacation_leave, SUM(leave_without_pay) as leave_without_pay, SUM(maternity_leave) as maternity_leave, SUM(paternity_leave) as paternity_leave,SUM(regular) as regular, SUM(regular_overtime) as empregular_overtime, SUM(regular_overtime_night_diff) as rond , SUM(regular_night_differential) as rnd, 
					   SUM(rest_day) as rd, SUM(rest_day_overtime) as rdot, SUM(rest_day_overtime_night_diff) as rdond, SUM(rest_day_night_differential) as rdnd, SUM(rest_day_special_holiday) as rdspl_holiday, SUM(rest_day_special_holiday_overtime) as rdsho, SUM(rest_day_special_holiday_overtime_night_diff) as rdshond, SUM(rest_day_special_holiday_night_diff) as rdshnd, 
					   SUM(rest_day_legal_holiday) as rdlh, SUM(rest_day_legal_holiday_overtime) as rdlhot, SUM(rest_day_legal_holiday_overtime_night_diff) as rdlhond, SUM(rest_day_legal_holiday_night_diff) as rdlhnd, SUM(special_holiday) as splemp_holiday, SUM(special_holiday_overtime) as sho, SUM(special_holiday_overtime_night_diff) as shond, SUM(special_holiday_night_diff) as shnd, 
					   SUM(legal_holiday) as lh, SUM(legal_holiday_overtime) as lho, SUM(legal_holiday_overtime_night_diff) as lhond, SUM(legal_holiday_night_diff) as lhnd, boph_employees.id as employeeid'))
					 ->join('employee_setting', 'employee_setting.employee_id', '=', 'employee_summary.employee_id')
					 //->join('employees', 'employees.id', '=', 'employee_summary.employee_id')
					 //->join('tbl_deductions', 'employee_summary.employee_id', '=', 'tbl_deductions.employee_id')
					 //->join('tbl_earnings', 'employee_summary.employee_id', '=', 'tbl_earnings.employee_id')
					 ->join('employees', 'employees.id', '=', 'employee_summary.employee_id')
					 ->join('tbl_payroll_register', 'tbl_payroll_register.emp_number', '=', 'employees.id')
					 ->join('timesheet_submitted', 'timesheet_submitted.employee_id', '=', 'employee_summary.employee_id')
					 ->WhereBetween('daydate', array($data["schedule_date_from"], $data["schedule_date_to"]))
					 //->orWhereBetween('daydate', array($data["schedule_date_from"], $data["schedule_date_to"]))
					 //->where('tbl_earnings.cutfrom', '=', $data["schedule_date_from"])
					 //->where('tbl_earnings.cutto', '=', $data["schedule_date_to"])
					 ->whereIn('employees.id', $vals)
					 ->groupBy('employee_summary.employee_id')		
					 ->get(); 
					 
	/*$earnings = DB::table('tbl_earnings')
                     ->select(DB::raw('firstname, lastname, boph_employees.id as employid, COLA, deminimis, mobile_allowance, previous_payroll, reimbursible_allowance, retro_payment, transportation_allowance, previous_OT_adjustment, previous_payroll_adjustment, retro_payment_adjustment'))
					 ->join('employees', 'employees.id', '=', 'tbl_earnings.employee_id')
					 ->where('tbl_earnings.cutfrom', '=', $data["schedule_date_from"])
					 ->where('tbl_earnings.cutto', '=', $data["schedule_date_to"])
					 ->get();*/ 			 

	//var_dump($summaries);
	
	//return View::make('admin.payrollsummary', ['summaries' => $summaries, 'employeeInfo' => $employeeInfo]);
    return View::make('admin.payrollsummary', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'summaries' => $summaries, 'employeeInfo' => $employeeInfo, 'cutoffFrom' => $data["schedule_date_from"], 'cutoffTo' => $data["schedule_date_to"], 'vals' => $vals));

}));


Route::post('/admin/payroll/exportXLS', array('uses' => function()
{
		$data = Input::all();

			
		$employeeId = Session::get('currentUserId');	
		$userId = Session::get('currentUserId');

		$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
		//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

		if( !empty($userGroups) ) {

		  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

		}

		$currentUser = Sentry::getUser();	
		
		$employee = new Employee;		
		$employeeInfo = $employee->getEmployeeInfoById($employeeId);

		$getUserEmployee = DB::table('users')            
			->join('employees', 'users.employee_id', '=', 'employees.id')
			->join('users_groups', 'users_groups.user_id', '=', 'users.id')
			->join('groups', 'users_groups.group_id', '=', 'groups.id')
			->get();	

		//Admin view	
		//return View::make('admin.hr', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

		/*if( !empty($groups) ) {
			if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

				return View::make('admin.emailSettings', ['emailInfo' => $emailInfo, 'employeeInfo' => $employeeInfo]); 
			} else {

				echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

			}
		}*/
		
		Excel::create('PayrollRegister', function($excel) {


        $excel->sheet('Summary', function($sheet) {

				/*$sheet->setColumnFormat(array(
				 'A' => '0.00',
   				 'B' => '0.00',
    			 'C' => '0.00',
    			 'D' => '0.00',
    			 'E' => '0.00',
    			 'F' => '0.00',
    			 'G' => '0.00',
    			 'H' => '0.00',

				));*/
        		
        		$sheet->loadView('admin.export2XLS');
    	});

        
        })->export('xlsx');
		//})->download('pdf');
		
 //   }

					
}));


Route::post('/admin/payroll/exportPDF', array('uses' => function()
{
		$data = Input::all();
		
		//get the variables
		$data['empid'] = Input::get('empid');
		$data['cutfrom'] = Input::get('cutfrom');
		$data['cutto'] = Input::get('cutto');

			
		$employeeId = Session::get('currentUserId');	
		$userId = Session::get('currentUserId');
		$vals = Session::get('values');
		
		//var_dump($vals);

		$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
		//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

		if( !empty($userGroups) ) {

		  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

		}

		$currentUser = Sentry::getUser();	
		
		$employee = new Employee;		
		$employeeInfo = $employee->getEmployeeInfoById($employeeId);

		$getUserEmployee = DB::table('users')            
			->join('employees', 'users.employee_id', '=', 'employees.id')
			->join('users_groups', 'users_groups.user_id', '=', 'users.id')
			->join('groups', 'users_groups.group_id', '=', 'groups.id')
			->get();	
		
		$payInfos = DB::table('employees')
		->join('users', 'users.employee_id', '=', 'employees.id')
		->join('job_title', 'employees.position_id', '=', 'job_title.id')
		->join('departments', 'employees.department_id', '=', 'departments.id')
		->join('employee_setting', 'employees.id', '=', 'employee_setting.employee_id')
		->join('tbl_payroll_register', 'employees.id', '=', 'tbl_payroll_register.emp_number')
		//->join('tbl_overtime', 'employees.id', '=', 'tbl_overtime.emp_number')
		->select('employees.id as empid', 'employees.employee_number as employnumber', 'lastname', 'firstname', 'basicpay', 'tax_status', 'email', 'job_title.name as jobtitle', 'departments.name as departmentname', 
				 'sumlates', 'sumabsences', 'amt_lates', 'amt_absences', 'wtax', 'sss_ec', 'pagibig_ec', 'philhealth_ec', 'total_OT', 'wtax_basis', 'total_deductions', 'sss_salary_loan', 'net_pay', 'total_nottax_earnings', 'total_tax_earnings')
		->where('tbl_payroll_register.cutfrom',$data["cutfrom"])
		->where('tbl_payroll_register.cutto', $data["cutto"])
		->whereIn('employees.id', $vals)
		
		//->where('employees.id', $data['empid'])
		//->whereIn('employees.id', $results)
		->get();
		
		//var_dump($payInfos);

		$payOTs = DB::table('tbl_overtime')
		->join('tbl_ot_rates', 'tbl_ot_rates.Code', '=', 'tbl_overtime.overtime_type')
		->join('employees', 'employees.id', '=', 'tbl_overtime.emp_number')
		->select('emp_number', 'OT_name', 'total_num_hrs', 'total_amt', 'cutfrom', 'cutto')
        ->distinct()
		->where('tbl_overtime.cutfrom',$data["cutfrom"])
		->where('tbl_overtime.cutto', $data["cutto"])
		->whereIn('employees.id', $vals)
		->get();

        //var_dump($payOTs);
		
		//var_dump($payInfos);
		//$pdf = PDF::loadView('admin.exportPayslip', [compact('payInfos'))->setPaper('a4')->setOrientation('landscape');;
	$pdf = PDF::loadView('admin.exportPayslip',['payInfos'=>$payInfos,'payOTs'=>$payOTs])->setPaper('legal')->setOrientation('portrait');
		//$pdf = PDF::loadView('admin.export2XLS');
    return $pdf->download('payslip.pdf');

					

}));


Route::get('/admin/payroll/exportPDF_2', array('uses' => function()
{

		$adminController = new AdminController;
		$dataArr = $adminController->init();

		$cfrom = Session::get('cfrom');
		$cto = Session::get('cto');
	
		$employeeId = Session::get('currentUserId');	
		$userId = Session::get('currentUserId');
		$vals = Session::get('values');

		$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
		//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

		if( !empty($userGroups) ) {

		  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

		}

		$currentUser = Sentry::getUser();	
		
		$employee = new Employee;		
		$employeeInfo = $employee->getEmployeeInfoById($employeeId);

		$getUserEmployee = DB::table('users')            
			->join('employees', 'users.employee_id', '=', 'employees.id')
			->join('users_groups', 'users_groups.user_id', '=', 'users.id')
			->join('groups', 'users_groups.group_id', '=', 'groups.id')
			->get();	
		
		$payInfos = DB::table('employees')
		->join('users', 'users.employee_id', '=', 'employees.id')
		->join('job_title', 'employees.position_id', '=', 'job_title.id')
		->join('departments', 'employees.department_id', '=', 'departments.id')
		->join('employee_setting', 'employees.id', '=', 'employee_setting.employee_id')
		->join('tbl_payroll_register', 'employees.id', '=', 'tbl_payroll_register.emp_number')
		//->join('tbl_overtime', 'employees.id', '=', 'tbl_overtime.emp_number')
		->select('employees.id as empid', 'employees.employee_number as employnumber', 'lastname', 'firstname', 'basicpay', 'tax_status', 'email', 'job_title.name as jobtitle', 'departments.name as departmentname', 
				 'sumlates', 'sumabsences', 'amt_lates', 'amt_absences', 'wtax', 'sss_ec', 'pagibig_ec', 'philhealth_ec', 'total_OT', 'wtax_basis', 'total_deductions', 'net_pay', 'total_nottax_earnings', 'total_tax_earnings')
		->where('tbl_payroll_register.cutfrom', $cfrom)
		->where('tbl_payroll_register.cutto', $cto)
		->where('tbl_payroll_register.emp_number', $employeeId)
		
		//->where('employees.id', $data['empid'])
		//->whereIn('employees.id', $results)
		->get();

		$payOTs = DB::table('tbl_overtime')
		->join('tbl_ot_rates', 'tbl_ot_rates.Code', '=', 'tbl_overtime.overtime_type')
		->join('employees', 'employees.id', '=', 'tbl_overtime.emp_number')
		->select('emp_number', 'OT_name', 'total_num_hrs', 'total_amt', 'cutfrom', 'cutto')
		->distinct()
		->where('tbl_overtime.cutfrom',$cfrom)
		->where('tbl_overtime.cutto', $cto)
		->where('tbl_overtime.emp_number', $employeeId)
		->get();
		
		//var_dump($payInfos);
		//$pdf = PDF::loadView('admin.exportPayslip', [compact('payInfos'))->setPaper('a4')->setOrientation('landscape');;
		$pdf = PDF::loadView('admin.exportPayslip',['payInfos'=>$payInfos,'payOTs'=>$payOTs])->setPaper('legal')->setOrientation('portrait');
		//$pdf = PDF::loadView('admin.export2XLS');
        return $pdf->download('payslip.pdf');

					

}));


Route::post('/admin/payroll/saveData', array('uses' => function()
{
		
		$adminController = new AdminController;
		$dataArr = $adminController->init();
	
		$data = Input::all();

		/*$vals = array();
		$arr_SOM = array();
		$arr_ROT = array();
		$arr_SRF = array();
		$arr_RON = array();
		$arr_NSN = array();
		$arr_RDF = array();
		$arr_RDF2 = array();
		$arr_RDX = array();
		$arr_RDN = array();
		$arr_SRX = array();
		$arr_SRN = array();
		$arr_SRN2 = array();
		$arr_LRF = array();
		$arr_LRX = array();
		$arr_LRN = array();
		$arr_LRN2 = array();
		$arr_SOX = array();
		$arr_RDN3 = array();
		$arr_LHF = array();
		$arr_LHX = array();
		$arr_LHN = array();
		$arr_LHN2 = array();*/
		
		$vals = Session::get('arr_values');
		
		//for OTs
		$arrROT = Session::get('arr_ROT');
		$arrSOM = Session::get('arr_SOM');
		$arrSRF = Session::get('arr_SRF');
		$arrRON = Session::get('arr_RON');
		$arrNSN = Session::get('arr_NSN');
		$arrRDF = Session::get('arr_RDF');
		$arrRDX = Session::get('arr_RDX');
		$arrRDF2 = Session::get('arr_RDF2');
		$arrRDN = Session::get('arr_RDN');
		$arrSRX = Session::get('arr_SRX');
		$arrSRN = Session::get('arr_SRN');
		$arrSRN2 = Session::get('arr_SRN2');
		$arrLRF = Session::get('arr_LRF');
		$arrLRX = Session::get('arr_LRX');
		$arrLRN = Session::get('arr_LRN');
		$arrLRN2 = Session::get('arr_LRN2');
		$arrSOX = Session::get('arr_SOX');
		$arrRDN3 = Session::get('arr_RDN3');
		$arrLHF = Session::get('arr_LHF');
		$arrLHX = Session::get('arr_LHX');
		$arrLHN = Session::get('arr_LHN');
		$arrLHN2 = Session::get('arr_LHN2');

		//var_dump($vals);
		//var_dump($arrRON);
		//var_dump($arrNSN);
			
		$employeeId = Session::get('currentUserId');	
		$userId = Session::get('currentUserId');

		$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
		//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

		if( !empty($userGroups) ) {

		  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

		}

		$currentUser = Sentry::getUser();	
		
		$employee = new Employee;		
		$employeeInfo = $employee->getEmployeeInfoById($employeeId);

		$getUserEmployee = DB::table('users')            
			->join('employees', 'users.employee_id', '=', 'employees.id')
			->join('users_groups', 'users_groups.user_id', '=', 'users.id')
			->join('groups', 'users_groups.group_id', '=', 'groups.id')
			->get();	
									
		/*DB::table('tbl_overtime')
			->insert(array(
						'employee_id' => $employee->id,
						'has_overtime' => 1,
						'has_break' => 1,
						'break_time' => '01:00:00',
						'hours_per_day' => number_format(8, 2)					
			));	*/
		
		foreach($vals as $val)
		{
			//var_dump($val["emp_number"]);
			
			/*$payreg = DB::table('tbl_payroll_register')
				->select('emp_number', 'cutfrom', 'cutto')
				->where('cutfrom', $data['cutfrom'])
				->where('cutto', $data['cutto'])
				->where('emp_number', $data['empid'])
				->get();*/
			
			//var_dump($payOTs);
			
			/*if(is_array($payreg) || !empty($payreg))
			{
				echo "This is an array.";
			}
			else
			{
				echo "This is not an array.";
			}*/
				
			
			
			/*DB::table('tbl_payroll_register')
				->insert(array(
							'emp_number' => $val['emp_number'],
							'sumlates' => $val['sumlates'],
							'amt_lates' => $val['amt_lates'],
							'sumabsences' => $val['sumabsences'],
							'amt_absences' => $val['amt_absences'],
							'total_ot' => $val['total_ot'],
							'sss_ec' => $val['sss_ec'],
							'philhealth_ec' => $val['philhealth_ec'],
							'pagibig_ec' => $val['pagibig_ec'],
							'sss_es' => $val['sss_es'],
							'ec_es' => $val['ec_es'],
							'philhealth_es' => $val['philhealth_es'],
							'pagibig_es' => $val['pagibig_es'],
							'gross_pay' => $val['gross_pay'],
							'wtax_basis' => $val['wtax_basis'],
							'wtax' => $val['wtax'],
							'total_deductions' => $val['total_deductions'],
							'total_nottax_earnings' => $val['totalnottax'],
							'total_tax_earnings' => $val['totaltax'],
							'net_pay' => $val['net_pay'],
							'cutfrom' => $val['cutfrom'],
							'cutto' => $val['cutto']			
				)); */
				
				
				DB::table('tbl_payroll_register')
					 ->where('emp_number', $val["emp_number"])
					 ->where('cutfrom', $data["cutfrom"])
					 ->where('cutto', $data["cutto"])
					 //->where('seq_no', 1)
					 ->update(array('emp_number' => $val['emp_number'],
							'sumlates' => $val['sumlates'],
							'amt_lates' => $val['amt_lates'],
							'sumabsences' => $val['sumabsences'],
							'amt_absences' => $val['amt_absences'],
							'total_ot' => $val['total_ot'],
							'sss_ec' => $val['sss_ec'],
							'philhealth_ec' => $val['philhealth_ec'],
							'pagibig_ec' => $val['pagibig_ec'],
							'sss_es' => $val['sss_es'],
							'ec_es' => $val['ec_es'],
							'philhealth_es' => $val['philhealth_es'],
							'pagibig_es' => $val['pagibig_es'],
							'gross_pay' => $val['gross_pay'],
							'wtax_basis' => $val['wtax_basis'],
							'wtax' => $val['wtax'],
							'total_deductions' => $val['total_deductions'],
							'total_nottax_earnings' => $val['totalnottax'],
							'total_tax_earnings' => $val['totaltax'],
							'net_pay' => $val['net_pay']));
				
				
				DB::table('timesheet_submitted')
					 ->where('employee_id', $val["emp_number"])
					 ->where('cutoff_starting_date', $data["cutfrom"])
					 ->where('cutoff_ending_date', $data["cutto"])
					 //->where('seq_no', 1)
					 ->update(array('is_processed' => 1));
				
		}
		
		
		
		//for OTs
		if(!empty($arrROT))
		{
			foreach($arrROT as $rot)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $rot['emp_number'],
						'total_num_hrs' => $rot['total_num_hrs'],
						'overtime_type' => $rot['overtime_type'],
						'total_amt' => $rot['total_amt'],
						'cutfrom' => $rot['cutfrom'],
						'cutto' => $rot['cutto']
					));
				
			}
		}
		
		
		if(!empty($arrSOM))
		{
			foreach($arrSOM as $som)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $som['emp_number'],
						'total_num_hrs' => $som['total_num_hrs'],
						'overtime_type' => $som['overtime_type'],
						'total_amt' => $som['total_amt'],
						'cutfrom' => $som['cutfrom'],
						'cutto' => $som['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrSRF))
		{
			foreach($arrSRF as $srf)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $srf['emp_number'],
						'total_num_hrs' => $srf['total_num_hrs'],
						'overtime_type' => $srf['overtime_type'],
						'total_amt' => $srf['total_amt'],
						'cutfrom' => $srf['cutfrom'],
						'cutto' => $srf['cutto']
					));
			}
		}
		
		
		if(!empty($arrRON))
		{
			foreach($arrRON as $ron)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $ron['emp_number'],
						'total_num_hrs' => $ron['total_num_hrs'],
						'overtime_type' => $ron['overtime_type'],
						'total_amt' => $ron['total_amt'],
						'cutfrom' => $ron['cutfrom'],
						'cutto' => $ron['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrNSN))
		{
			foreach($arrNSN as $nsn)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $nsn['emp_number'],
						'total_num_hrs' => $nsn['total_num_hrs'],
						'overtime_type' => $nsn['overtime_type'],
						'total_amt' => $nsn['total_amt'],
						'cutfrom' => $nsn['cutfrom'],
						'cutto' => $nsn['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrRDF))
		{
			foreach($arrRDF as $rdf)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $rdf['emp_number'],
						'total_num_hrs' => $rdf['total_num_hrs'],
						'overtime_type' => $rdf['overtime_type'],
						'total_amt' => $rdf['total_amt'],
						'cutfrom' => $rdf['cutfrom'],
						'cutto' => $rdf['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrRDX))
		{
			foreach($arrRDX as $rdx)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $rdx['emp_number'],
						'total_num_hrs' => $rdx['total_num_hrs'],
						'overtime_type' => $rdx['overtime_type'],
						'total_amt' => $rdx['total_amt'],
						'cutfrom' => $rdx['cutfrom'],
						'cutto' => $rdx['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrRDF2))
		{
			foreach($arrRDF2 as $rdf2)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $rdf2['emp_number'],
						'total_num_hrs' => $rdf2['total_num_hrs'],
						'overtime_type' => $rdf2['overtime_type'],
						'total_amt' => $rdf2['total_amt'],
						'cutfrom' => $rdf2['cutfrom'],
						'cutto' => $rdf2['cutto']
					));
				
			}	
		}
		

		if(!empty($arrRDN))
		{
			foreach($arrRDN as $rdn)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $rdn['emp_number'],
						'total_num_hrs' => $rdn['total_num_hrs'],
						'overtime_type' => $rdn['overtime_type'],
						'total_amt' => $rdn['total_amt'],
						'cutfrom' => $rdn['cutfrom'],
						'cutto' => $rdn['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrSRX))
		{
			foreach($arrSRX as $srx)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $srx['emp_number'],
						'total_num_hrs' => $srx['total_num_hrs'],
						'overtime_type' => $srx['overtime_type'],
						'total_amt' => $srx['total_amt'],
						'cutfrom' => $srx['cutfrom'],
						'cutto' => $srx['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrSRN))
		{
			foreach($arrSRN as $srn)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $srn['emp_number'],
						'total_num_hrs' => $srn['total_num_hrs'],
						'overtime_type' => $srn['overtime_type'],
						'total_amt' => $srn['total_amt'],
						'cutfrom' => $srn['cutfrom'],
						'cutto' => $srn['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrSRN2))
		{
			foreach($arrSRN2 as $srn2)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $srn2['emp_number'],
						'total_num_hrs' => $srn2['total_num_hrs'],
						'overtime_type' => $srn2['overtime_type'],
						'total_amt' => $srn2['total_amt'],
						'cutfrom' => $srn2['cutfrom'],
						'cutto' => $srn2['cutto']
					));
			}	
		}
		
		if(!empty($arrLRF))
		{
			foreach($arrLRF as $lrf)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $lrf['emp_number'],
						'total_num_hrs' => $lrf['total_num_hrs'],
						'overtime_type' => $lrf['overtime_type'],
						'total_amt' => $lrf['total_amt'],
						'cutfrom' => $lrf['cutfrom'],
						'cutto' => $lrf['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrLRX))
		{
			foreach($arrLRX as $lrx)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $lrx['emp_number'],
						'total_num_hrs' => $lrx['total_num_hrs'],
						'overtime_type' => $lrx['overtime_type'],
						'total_amt' => $lrx['total_amt'],
						'cutfrom' => $lrx['cutfrom'],
						'cutto' => $lrx['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrLRN))
		{
			foreach($arrLRN as $lrn)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $lrn['emp_number'],
						'total_num_hrs' => $lrn['total_num_hrs'],
						'overtime_type' => $lrn['overtime_type'],
						'total_amt' => $lrn['total_amt'],
						'cutfrom' => $lrn['cutfrom'],
						'cutto' => $rdn['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrLRN2))
		{
			foreach($arrLRN2 as $lrn2)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $lrn2['emp_number'],
						'total_num_hrs' => $lrn2['total_num_hrs'],
						'overtime_type' => $lrn2['overtime_type'],
						'total_amt' => $lrn2['total_amt'],
						'cutfrom' => $lrn2['cutfrom'],
						'cutto' => $lrn2['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrSOX))
		{
			foreach($arrSOX as $sox)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $sox['emp_number'],
						'total_num_hrs' => $sox['total_num_hrs'],
						'overtime_type' => $sox['overtime_type'],
						'total_amt' => $sox['total_amt'],
						'cutfrom' => $sox['cutfrom'],
						'cutto' => $sox['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrRDN3))
		{
			foreach($arrRDN3 as $rdn3)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $rdn3['emp_number'],
						'total_num_hrs' => $rdn3['total_num_hrs'],
						'overtime_type' => $rdn3['overtime_type'],
						'total_amt' => $rdn3['total_amt'],
						'cutfrom' => $rdn3['cutfrom'],
						'cutto' => $rdn3['cutto']
					));
				
			}	
		}
		
		
		if(!empty($arrLHF))
		{
			foreach($arrLHF as $lhf)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $lhf['emp_number'],
						'total_num_hrs' => $lhf['total_num_hrs'],
						'overtime_type' => $lhf['overtime_type'],
						'total_amt' => $lhf['total_amt'],
						'cutfrom' => $lhf['cutfrom'],
						'cutto' => $lhf['cutto']
					));
			}	
		}
		
		
		if(!empty($arrLHX))
		{
			foreach($arrLHX as $lhx)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $lhx['emp_number'],
						'total_num_hrs' => $lhx['total_num_hrs'],
						'overtime_type' => $lhx['overtime_type'],
						'total_amt' => $lhx['total_amt'],
						'cutfrom' => $lhx['cutfrom'],
						'cutto' => $lhx['cutto']
					));
				 
			}	
		}
		
		
		if(!empty($arrLHN))
		{
			foreach($arrLHN as $lhn)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $lhn['emp_number'],
						'total_num_hrs' => $lhn['total_num_hrs'],
						'overtime_type' => $lhn['overtime_type'],
						'total_amt' => $lhn['total_amt'],
						'cutfrom' => $lhn['cutfrom'],
						'cutto' => $lhn['cutto']
					));
				 
			}	
		}
		
		
		if(!empty($arrLHN2))
		{
			foreach($arrLHN2 as $lhn2)
			{
					DB::table('tbl_overtime')
						->insert(array(
						'emp_number' => $lhn2['emp_number'],
						'total_num_hrs' => $lhn2['total_num_hrs'],
						'overtime_type' => $lhn2['overtime_type'],
						'total_amt' => $lhn2['total_amt'],
						'cutfrom' => $lhn2['cutfrom'],
						'cutto' => $lhn2['cutto']
					));
				 
			}	
		} 
		
		$getpays = DB::table('timesheet_submitted')
		->join('employees', 'timesheet_submitted.employee_id', '=', 'employees.id')
		->join('users', 'users.employee_id', '=', 'timesheet_submitted.employee_id')
		->join('job_title', 'job_title.id', '=', 'employees.position_id')
		->join('departments', 'departments.id', '=', 'employees.department_id')
		->join('employee_setting', 'employee_setting.employee_id', '=', 'employees.id')
		->select('employees.id as empid', 'employees.employee_number', 'lastname', 'firstname', 'middle_name', 'email', 'cutoff_starting_date', 'cutoff_ending_date', 'job_title.name as jobtitle', 'employee_type', 'departments.name as departmentname', 'employees.supervisor_id', 'employees.manager_id')
		->where('is_processed', 0)
		//->whereIn('employees.id', $results)
		->get(); 
		
		$message = 'Data Saved.';
		
		return View::make('admin.payrolllist', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo, 'getpays' => $getpays, 'message' => $message, 'startdate' => $data["cutfrom"], 'enddate' => $data["cutto"]));
		
		//return Redirect::to('users/login');
		
		//echo "<script> alert('data saved.... ');</script>";
		
		//echo "data saved.....";
		
		/*$values = array();
		$values[] = Session::get('values');
		$cutfrom = Session::get('cutfrom');
		$cutto = Session::get('cutto');
		
		$summaries = DB::table('employee_summary')
                     ->select(DB::raw('employee_number, firstname, lastname, basicpay, dailysal, tax_status, boph_employee_summary.employee_id, SUM(lates) as lates, SUM(undertime) as undertime, SUM(absent) as absent, SUM(paid_sick_leave) as paid_sick_leave, SUM(paid_vacation_leave) as paid_vacation_leave, SUM(leave_without_pay) as leave_without_pay, SUM(maternity_leave) as maternity_leave, SUM(paternity_leave) as paternity_leave,
                       SUM(regular) as regular, SUM(regular_overtime) as empregular_overtime, SUM(regular_overtime_night_diff) as rond , SUM(regular_night_differential) as rnd, SUM(rest_day) as rd, SUM(rest_day_overtime) as rdot, SUM(rest_day_overtime_night_diff) as rdond, SUM(rest_day_night_differential) as rdnd,
                       SUM(rest_day_special_holiday) as rdspl_holiday, SUM(rest_day_special_holiday_overtime) as rdsho, SUM(rest_day_special_holiday_overtime_night_diff) as rdshond, SUM(rest_day_special_holiday_night_diff) as rdshnd, SUM(rest_day_legal_holiday) as rdlh, SUM(rest_day_legal_holiday_overtime) as rdlhot,
                       SUM(rest_day_legal_holiday_overtime_night_diff) as rdlhond, SUM(rest_day_legal_holiday_night_diff) as rdlhnd, SUM(special_holiday) as splemp_holiday, SUM(special_holiday_overtime) as sho, SUM(special_holiday_overtime_night_diff) as shond, SUM(special_holiday_night_diff) as shnd, SUM(legal_holiday) as lh,
                       SUM(legal_holiday_overtime) as lho, SUM(legal_holiday_overtime_night_diff) as lhond, SUM(legal_holiday_night_diff) as lhnd'))
					 ->join('employee_setting', 'employee_setting.employee_id', '=', 'employee_summary.employee_id')
					 ->join('employees', 'employees.id', '=', 'employee_summary.employee_id')
					 ->join('timesheet_submitted', 'timesheet_submitted.employee_id', '=', 'employee_summary.employee_id')
					 ->whereBetween('daydate', array($cutfrom, $cutto))
					 //->where('is_processed', 0)
					 ->whereIn('employees.id', $values)
					 ->groupBy('employee_summary.employee_id')			 
					 ->get();*/
		
		//var_dump($cutfrom);
		//var_dump($cutto);
	
		//return View::make('admin.payrollsummary', ['summaries' => $summaries, 'employeeInfo' => $employeeInfo, 'cutoffFrom' => $cutfrom, 'cutoffTo' => $cutto]);
		
}));


// publish PaySlip

Route::post('/admin/payroll/publishPayslip', array('uses' => function()
{
		
		$adminController = new AdminController;
		$dataArr = $adminController->init();
		
		$dataArr["resourceId"] = 'admin';
	
		$data = Input::all();
			
		$employeeId = Session::get('currentUserId');	
		$userId = Session::get('currentUserId');

		$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
		//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

		if( !empty($userGroups) ) {

		  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

		}

		$currentUser = Sentry::getUser();	
		
		$employee = new Employee;		
		$employeeInfo = $employee->getEmployeeInfoById($employeeId);

		$getUserEmployee = DB::table('users')            
			->join('employees', 'users.employee_id', '=', 'employees.id')
			->join('users_groups', 'users_groups.user_id', '=', 'users.id')
			->join('groups', 'users_groups.group_id', '=', 'groups.id')
			->get();	
		
		$vals = Session::get('arr_values');
		
		foreach($vals as $val)
		{
			
				DB::table('timesheet_submitted')
					 ->where('employee_id', $val["emp_number"])
					 ->where('cutoff_starting_date', $data["cutfrom"])
					 ->where('cutoff_ending_date', $data["cutto"])
					 //->where('seq_no', 1)
					 ->update(array('is_published' => 1));
				
		}
		
		$message = 'Payslips published.';
		
		return View::make('admin.index', array('dataArr' => $dataArr, 'employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee));
		//return View::make('admin.index', array('dataArr' => $dataArr));
		
		//return View::make('admin.payrolllist', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo, 'message' => $message));
		
}));


Route::get('/employee/report/payslip', array('as' => 'reportSummary', 'uses' => function()
{

	$adminController = new AdminController;
	$dataArr = $adminController->init();

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');
	
	//$dte = DB::table('timesheet_submitted')->max('id')->where('employee_id', $employeeId)->first();		

	$dte = DB::table('timesheet_submitted')
                     ->select(DB::raw('max(id), cutoff_starting_date, cutoff_ending_date'))
                     ->where('employee_id', $employeeId)
                     //->groupBy('status')
                     ->first();
	
	//var_dump($dte);
	$cutfrom = $dte->cutoff_starting_date;
	$cutto = $dte->cutoff_ending_date;
	
	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');
	$vals = Session::get('values');
	
	Session::put('cfrom', $cutfrom);
	Session::put('cto', $cutto);

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) 
	{
		$groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  
	}

	$currentUser = Sentry::getUser();	
		
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
		->join('employees', 'users.employee_id', '=', 'employees.id')
		->join('users_groups', 'users_groups.user_id', '=', 'users.id')
		->join('groups', 'users_groups.group_id', '=', 'groups.id')
		->get();	
		
	
	$payInfos = DB::table('employees')
		->join('users', 'users.employee_id', '=', 'employees.id')
		->join('job_title', 'employees.position_id', '=', 'job_title.id')
		->join('departments', 'employees.department_id', '=', 'departments.id')
		->join('employee_setting', 'employees.id', '=', 'employee_setting.employee_id')
		->join('tbl_payroll_register', 'employees.id', '=', 'tbl_payroll_register.emp_number')
		//->join('tbl_overtime', 'employees.id', '=', 'tbl_overtime.emp_number')
		->select('employees.id as empid', 'employees.employee_number as employnumber', 'lastname', 'firstname', 'basicpay', 'tax_status', 'email', 'job_title.name as jobtitle', 'departments.name as departmentname', 
				 'sumlates', 'sumabsences', 'amt_lates', 'amt_absences', 'wtax', 'sss_ec', 'pagibig_ec', 'philhealth_ec', 'total_OT', 'wtax_basis', 'total_deductions', 'net_pay', 'total_nottax_earnings', 'total_tax_earnings', 'cutfrom', 'cutto')
		->where('tbl_payroll_register.cutfrom', $cutfrom)
		->where('tbl_payroll_register.cutto', $cutto)
		->where('tbl_payroll_register.emp_number', $employeeId)
		//->whereIn('employees.id', $vals)
		
		//->where('employees.id', $data['empid'])
		//->whereIn('employees.id', $results)
		->get();

	$payOTs = DB::table('tbl_overtime')
		->join('tbl_ot_rates', 'tbl_ot_rates.Code', '=', 'tbl_overtime.overtime_type')
		->join('employees', 'employees.id', '=', 'tbl_overtime.emp_number')
		->select('emp_number', 'OT_name', 'total_num_hrs', 'total_amt', 'cutfrom', 'cutto')
		->distinct()
		->where('tbl_overtime.cutfrom',$cutfrom)
		->where('tbl_overtime.cutto', $cutto)
		->where('tbl_overtime.emp_number', $employeeId)
		//->whereIn('employees.id', $vals)
		->get();
		

	//return View::make('admin.dashboard', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        
	//return View::make('admin.reportsummary', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);
	
	return View::make('admin.viewpayslip', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee, 'payInfos'=>$payInfos, 'payOTs'=>$payOTs));

}));


Route::post('/admin/payrollEdit/', array('as' => 'adminPayrollEdit', 'uses' => function()
{		
	$data = Input::all();
	
	if(Request::ajax())
	{
		return dd($data['id']);
	}
	/*$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');
	$employeeEditId = (int) $employeeEditId;

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);
	$employeeEditInfo = $employee->getEmployeeInfoById($employeeEditId);
	
	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			//return View::make('admin.useredit',['employeeInfo' => $employeeInfo, 'employeeEditId' => $employeeId, 'user' => $user]); 
			return View::make('admin.useredit', ['employeeInfo' => $employeeInfo, 'employeeEditInfo' => $employeeEditInfo]);				

		} else {

			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	} */
	
	

	//var_dump($data);
	//return View::make('admin.helloworld');
	
}));

Route::get('/admin/payroll/masterfile/{empID}', array('as' => 'adminPayrollMasterfile', 'uses' => function($empID) {

	//return 'Administration: Human Resources';

	$adminController = new AdminController;
	$dataArr = $adminController->init();	

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();	
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();	

	//Admin view	
	//return View::make('admin.hr', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]); 

	$empID = (int) $empID;
	Session::put('employID', $empID);
	
	$earn = new Earning;		
	$earnInfos = $earn->getEmployeeEarningById($empID);
	
	$deduc = new Deduction;
	$deductionInfos = $deduc->getEmployeeDeductionById($empID);
	
	$empsetting = new EmployeeSetting;
	$empsettingInfo = $empsetting->getEmpSettingById($empID);
	

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.masterfile', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee, 'earnInfos' => $earnInfos, 'deductionInfos' => $deductionInfos, 'empsettingInfo' => $empsettingInfo));        	

		} else {

			//http://jsfiddle.net/umz8t/458/
			//http://stackoverflow.com/questions/16344354/how-to-make-blinking-flashing-text-with-css3
			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	

}));


//CREATE: NEW EMPLOYEE EARNINGS
Route::get('/admin/earnings/new', array('uses' => function()
{	

	$adminController = new AdminController;
	$dataArr = $adminController->init();	
	
	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	//return View::make('admin.usernew',['employeeInfo' => $employeeInfo]);

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.earningsnew', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo));

		} else {

			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	

}));



Route::post('/admin/earnings/new', array('uses' => function()
{	

	$data = Input::all();
	
	$employeeId = Session::get('currentUserId');
	$employID = Session::get('employID');
	
	//$earn = new Earning;		
	//$earnInfo = $employee->getEmployeeInfoById($employeeId);
	
	$earn = new Earning;
	$earn->employee_id = $employID;
	//$earn->cola = $data["COLA"];
	//$earn->deminimis = $data["deminimis"];
	$earn->mobile_allowance = $data["mobile_allowance"];	
	$earn->previous_payroll = $data["previous_payroll"];
	$earn->reimbursible_allowance = $data["reimbursible_allowance"];
	$earn->retro_payment = $data["retro_payment"];
	$earn->transportation_allowance = $data["transportation_allowance"];
	$earn->previous_OT_adjustment = $data["previous_OT_adjustment"];
	$earn->previous_payroll_adjustment = $data["previous_payroll_adjustment"];
	$earn->retro_payment_adjustment = $data["retro_payment_adjustment"];
	$earn->cutfrom = $data["cutfrom"];
	$earn->cutto = $data["cutto"];
	$earn->save();
	
	
	DB::table('tbl_payroll_register')
		->where('emp_number', $employID)
		->where('cutfrom', $data["cutfrom"])
		->where('cutto', $data["cutto"])
			->update(array(				
						//'COLA' => $data["COLA"],
						//'deminimis' => $data["deminimis"],
						'mobile_allowance' => $data["mobile_allowance"],	
						'previous_payroll' => $data["previous_payroll"],
						'reimbursible_allowance' => $data["reimbursible_allowance"],
						'retro_payment' => $data["retro_payment"],
						'transportation_allowance' => $data["transportation_allowance"],
						'previous_OT_adjustment' => $data["previous_OT_adjustment"],
						'previous_payroll_adjustment' => $data["previous_payroll_adjustment"],
						'retro_payment_adjustment' => $data["retro_payment_adjustment"]
					));	

	$message = 'Added Record Successfully.';		
	//return Redirect::route('adminNewJobTitle')->with('message', $message);
	return Redirect::to('/admin/payroll/masterfile/' . $employID)->with('message', $message);
	
	
	//Session::get('newEmployeeId', 32);		
	//return Redirect::route('adminUserNewSchedule', array('user' => 1));

}));



Route::get('/admin/earnings/{eId}/edit', array('as' => 'earningEmpEdit', 'uses' => function($eId)
{	

	$data = Input::all();

	$adminController = new AdminController;
	$dataArr = $adminController->init();	

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');
	$eId = (int) $eId;

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);
	
	$earn = new Earning;
	$earningEditInfo = $earn->getEmpEarningById($eId);
	
	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			//return View::make('admin.useredit',['employeeInfo' => $employeeInfo, 'employeeEditId' => $employeeId, 'user' => $user]);
			return View::make('admin.earningsedit', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo, 'earningEditInfo' => $earningEditInfo));				

		} else {

			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	


}));


Route::post('/admin/earnings/{eId}/edit', array('as' => 'earningEmpEdit', 'uses' => function($eId)
{	

	$data = Input::all();
	
	//return dd($data);
	
	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');
	$eId = (int) $eId;
	
	$employID = Session::get('employID');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();
	
	//$earns = new Earning;
	//$earn->employee_id = $employeeId;
	//$earn = Earning::where('ID', $eId)->first();
	
	//$earn = Earning::find($eId);
	
	//(double) $data["previous_payroll_adjustment"];
	DB::table('tbl_earnings')
         ->where('ID', $eId)
         //->where('seq_no', 1)
         ->update(array(//'COLA' => $data["COLA"],
						//'deminimis' => $data["deminimis"],
						'mobile_allowance' => $data["mobile_allowance"],	
						'previous_payroll' => $data["previous_payroll"],
						'reimbursible_allowance' => $data["reimbursible_allowance"],
						'retro_payment' => $data["retro_payment"],
						'transportation_allowance' => $data["transportation_allowance"],
						'previous_OT_adjustment' => $data["previous_OT_adjustment"],
						'previous_payroll_adjustment' => $data["previous_payroll_adjustment"],
						'retro_payment_adjustment' => $data["retro_payment_adjustment"],
						'cutfrom' => $data["cutfrom"],
						'cutto' => $data["cutto"]));
						
	
	DB::table('tbl_payroll_register')
         ->where('emp_number', $employID)
		 ->where('cutfrom', $data["cutfrom"])
		 ->where('cutto', $data["cutto"])
         //->where('seq_no', 1)
         ->update(array(//'COLA' => $data["COLA"],
						//'deminimis' => $data["deminimis"],
						'mobile_allowance' => $data["mobile_allowance"],	
						'previous_payroll' => $data["previous_payroll"],
						'reimbursible_allowance' => $data["reimbursible_allowance"],
						'retro_payment' => $data["retro_payment"],
						'transportation_allowance' => $data["transportation_allowance"],
						'previous_OT_adjustment' => $data["previous_OT_adjustment"],
						'previous_payroll_adjustment' => $data["previous_payroll_adjustment"],
						'retro_payment_adjustment' => $data["retro_payment_adjustment"]));
	
	
	$message = 'Updated Successfully.';		
		
	return Redirect::to('/admin/payroll/masterfile/' . $employID)->with('message', $message);
		
	/*$earn->COLA = (double) $data["COLA"];
	$earn->deminimis = (double) $data["deminimis"];
	$earn->mobile_allowance = (double) $data["mobile_allowance"];	
	$earn->previous_payroll = (double) $data["previous_payroll"];
	$earn->reimbursible_allowance = (double) $data["reimbursible_allowance"];
	$earn->retro_payment = (double) $data["retro_payment"];
	$earn->transportation_allowance = (double) $data["transportation_allowance"];
	$earn->previous_OT_adjustment = (double) $data["previous_OT_adjustment"];
	$earn->previous_payroll_adjustment = (double) $data["previous_payroll_adjustment"];
	$earn->retro_payment_adjustment = (double) $data["retro_payment_adjustment"];
	$earn->cutfrom = (double) $data["cutfrom"];
	$earn->cutto = (double) $data["cutto"];
	$earn->save();*/
	
	//$empid = (int) Input::get('id');	
	//$earn = Earning::find($empid);
	//
	
	//$earn = Earning::where('ID', $eId)->first();
    /*$earn->cutfrom = Input::get('cutfrom');
    $earn->cutto = Input::get('cutto');
	$earn->COLA = (double) Input::get('COLA');
	$earn->deminimis = (double) Input::get('deminimis');
	$earn->mobile_allowance = (double) Input::get('mobile_allowance');
	$earn->previous_payroll = (double) Input::get('previous_payroll');
    $earn->reimbursible_allowance = (double) Input::get('reimbursible_allowance');
	$earn->retro_payment = (double) Input::get('retro_payment');
	$earn->transportation_allowance = (double) Input::get('transportation_allowance');
	$earn->previous_OT_adjustment = (double) Input::get('previous_OT_adjustment');
	$earn->previous_payroll_adjustment = (double) 300.00; //Input::get('previous_payroll_adjustment');
	$earn->retro_payment_adjustment = Input::get('retro_payment_adjustment');*/
    
	/*if($earn->save())
	{
		echo "the value is: " . $eId;
		var_dump($earn);
	}*/


}));


///===============================================================


//DELETE: EXISTING COMPANY
Route::get('/admin/earnings/delete/{id}', array('as' => 'adminDeleteEarning', 'uses' => function($id)
{
	$id = (int) $id;

	$adminController = new AdminController;
	$dataArr = $adminController->init();

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);	
	
	$earn = new Earning;
	$empEarningInfo = $earn->getEmpEarningById($id);
	
	//var_dump($empEarningInfo);
	
	//return 'Update Company';
	return View::make('admin.earningsdelete', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'id' => $id, 'employeeInfo' => $employeeInfo, 'empEarningInfo' => $empEarningInfo));

}));

//DELETE: EXISTING COMPANY
Route::post('/admin/earning/delete/{id}', array('as' => 'adminProcessDeleteEarning', 'uses' => function($id)
{
	$data = Input::all();
	$id = (int) $id;

	$employID = Session::get('employID');
	//$earn = Earning::find($employID);
	
	//if ( $earn->delete() ) {
		
		DB::table('tbl_earnings')->where('ID', '=', $id)->delete();
		
		DB::table('tbl_payroll_register')
         ->where('emp_number', $employID)
		 ->where('cutfrom', $data["cutfrom"])
		 ->where('cutto', $data["cutto"])
         //->where('seq_no', 1)
         ->update(array('COLA' => null,
						'deminimis' => null,
						'mobile_allowance' => null,	
						'previous_payroll' => null,
						'reimbursible_allowance' => null,
						'retro_payment' => null,
						'transportation_allowance' => null,
						'previous_OT_adjustment' => null,
						'previous_payroll_adjustment' => null,
						'retro_payment_adjustment' => null));

		$message = 'Deleted Successfully.';		
		//return Redirect::route('adminNewJobTitle')->with('message', $message);
		return Redirect::to('/admin/payroll/masterfile/' . $employID)->with('message', $message);

	//}

}));


//MODULE FOR DEDUCTIONS OF EMPLOYEES

//CREATE: NEW EMPLOYEE DEDUCTIONS
Route::get('/admin/deductions/new', array('uses' => function()
{	

	$adminController = new AdminController;
	$dataArr = $adminController->init();
	
	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	//return View::make('admin.usernew',['employeeInfo' => $employeeInfo]);

	if( !empty($groups) ) {
		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.deductionsnew', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo));

		} else {

			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	
}));



Route::post('/admin/deductions/new', array('uses' => function()
{	

	$data = Input::all();
	
	$employeeId = Session::get('currentUserId');
	$employID = Session::get('employID');
	
	//$earn = new Earning;		
	//$earnInfo = $employee->getEmployeeInfoById($employeeId);
	
	$deduc = new Deduction;
	$deduc->employee_id = $employID;
	$deduc->cash_advance = $data["cash_advance"];
	$deduc->company_loan = $data["company_loan"];
	$deduc->hdmf_loan = $data["hdmf_loan"];
	$deduc->hmo_dep = $data["hmo_dep"];	
	$deduc->sss_salary_loan = $data["sss_salary_loan"];
	$deduc->telephone_charges = $data["telephone_charges"];
	$deduc->cutfrom = $data["cutfrom"];
	$deduc->cutto = $data["cutto"];
	$deduc->save();
	
	
	DB::table('tbl_payroll_register')
		->where('emp_number', $employID)
		->where('cutfrom', $data["cutfrom"])
		->where('cutto', $data["cutto"])
			->update(array(				
						'cash_advance' => $data["cash_advance"],
						'company_loan' => $data["company_loan"],
						'hdmf_loan' => $data["hdmf_loan"],	
						'hmo_dep' => $data["hmo_dep"],
						'sss_salary_loan' => $data["sss_salary_loan"],
						'telephone_charges' => $data["telephone_charges"]
					));	

	$message = 'Added Record Successfully.';		
	//return Redirect::route('adminNewJobTitle')->with('message', $message);
	return Redirect::to('/admin/payroll/masterfile/' . $employID)->with('message', $message);
	//Session::get('newEmployeeId', 32);		
	//return Redirect::route('adminUserNewSchedule', array('user' => 1));
}));



Route::get('/admin/deductions/{eId}/edit', array('as' => 'earningEmpEdit', 'uses' => function($eId)
{	

	$adminController = new AdminController;
	$dataArr = $adminController->init();

	$data = Input::all();

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');
	$eId = (int) $eId;

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);
	
	$deduc = new Deduction;
	$deductionEditInfo = $deduc->getEmpDeductionById($eId);
	
	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			//return View::make('admin.useredit',['employeeInfo' => $employeeInfo, 'employeeEditId' => $employeeId, 'user' => $user]);
			return View::make('admin.deductionsedit', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo, 'deductionEditInfo' => $deductionEditInfo));				

		} else {

			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	


}));


Route::post('/admin/deductions/{eId}/edit', array('as' => 'earningEmpEdit', 'uses' => function($eId)
{	

	$data = Input::all();
	
	//return dd($data);
	
	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');
	$eId = (int) $eId;
	
	$employID = Session::get('employID');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) 
	{
		$groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  
	}

	$currentUser = Sentry::getUser();
	//$earns = new Earning;
	//$earn->employee_id = $employeeId;
	//$earn = Earning::where('ID', $eId)->first();
	
	//$earn = Earning::find($eId);
	
	//(double) $data["previous_payroll_adjustment"];
	DB::table('tbl_deductions')
         ->where('ID', $eId)
         //->where('seq_no', 1)
         ->update(array('cash_advance' => $data["cash_advance"],
						'company_loan' => $data["company_loan"],	
						'hdmf_loan' => $data["hdmf_loan"],
						'hmo_dep' => $data["hmo_dep"],
						'sss_salary_loan' => $data["sss_salary_loan"],
						'telephone_charges' => $data["telephone_charges"],
						'cutfrom' => $data["cutfrom"],
						'cutto' => $data["cutto"]));
						
	DB::table('tbl_payroll_register')
         ->where('emp_number', $employID)
		 ->where('cutfrom', $data["cutfrom"])
		 ->where('cutto', $data["cutto"])
         //->where('seq_no', 1)
         ->update(array('cash_advance' => $data["cash_advance"],
						'company_loan' => $data["company_loan"],	
						'hdmf_loan' => $data["hdmf_loan"],
						'hmo_dep' => $data["hmo_dep"],
						'sss_salary_loan' => $data["sss_salary_loan"],
						'telephone_charges' => $data["telephone_charges"]));
	
	$message = 'Updated Successfully.';		
		
	return Redirect::to('/admin/payroll/masterfile/' . $employID)->with('message', $message);
}));



//DELETE: EXISTING COMPANY
Route::get('/admin/deductions/delete/{id}', array('as' => 'adminDeleteDeduction', 'uses' => function($id)
{

	$adminController = new AdminController;
	$dataArr = $adminController->init();

	$id = (int) $id;

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);	
	
	$deduc = new Deduction;
	$empdeducInfo = $deduc->getEmpDeductionById($id);
	
	//var_dump($empdeducInfo);
	//return 'Update Company';
	return View::make('admin.deductionsdelete', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'id' => $id, 'employeeInfo' => $employeeInfo, 'empdeducInfo' => $empdeducInfo));

}));

//DELETE: EXISTING COMPANY
Route::post('/admin/deductions/delete/{id}', array('as' => 'adminProcessDeleteEarning', 'uses' => function($id)
{
	$data = Input::all();
	$id = (int) $id;

	$employID = Session::get('employID');
	//$earn = Earning::find($employID);
	
	//if ( $earn->delete() ) {
		
		DB::table('tbl_deductions')->where('ID', '=', $id)->delete();
		
		DB::table('tbl_payroll_register')
         ->where('emp_number', $employID)
		 ->where('cutfrom', $data["cutfrom"])
		 ->where('cutto', $data["cutto"])
         //->where('seq_no', 1)
         ->update(array('cash_advance' => null,
						'company_loan' => null,	
						'hdmf_loan' => null,
						'hmo_dep' => null,
						'sss_salary_loan' => null,
						'telephone_charges' => null));
		

		$message = 'Deleted Successfully.';		
		//return Redirect::route('adminNewJobTitle')->with('message', $message);
		
		//return Redirect::to('/admin/payroll/masterfile/' . $employID . '#listDeductions')->with('message', $message);
		return Redirect::to('/admin/payroll/list/');

	//}

}));


//generate payroll register

Route::get('/admin/payroll/payregister', array('as' => '', 'uses' => function() {

	$adminController = new AdminController;
	$dataArr = $adminController->init();	

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);
	

	//return View::make('admin.payrollsummary', ['summaries' => $summaries, 'employeeInfo' => $employeeInfo]);
	return View::make('admin.payrollregister', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo));

}));



Route::post('/admin/payroll/displaycutoff', array('as' => '', 'uses' => function() {

	$data = Input::all();

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);
	
	$payfrom = $data["cutoff_date_from"];
	$payto = $data["cutoff_date_to"];
	
	$payregisters = DB::table('tbl_payroll_register')
		->join('employees', 'tbl_payroll_register.emp_number', '=', 'employees.id')
		->where('cutfrom', $data['cutoff_date_from'])
		->where('cutto', $data['cutoff_date_to'])
		->select('firstname', 'lastname', 'sumlates', 'amt_lates', 'sumabsences', 'amt_absences', 'total_OT', 'gross_pay',    'company_loan', 'hdmf_loan', 'hmo_dep', 'sss_salary_loan', 'telephone_charges', 'cash_advance','mobile_allowance', 'previous_payroll', 'reimbursible_allowance', 'retro_payment', 'transportation_allowance', 'previous_OT_adjustment', 'previous_payroll_adjustment', 'retro_payment_adjustment', 'total_nottax_earnings', 'total_tax_earnings', 'earning_13th_pay', 'sss_ec', 'philhealth_ec', 'pagibig_ec', 'sss_es', 'ec_es', 'philhealth_es', 'pagibig_es', 'wtax_basis', 'wtax', 'total_deductions', 'net_pay')
		->get();

			
	//return View::make('admin.payrollsummary', ['summaries' => $summaries, 'employeeInfo' => $employeeInfo]);
	return View::make('admin.payregisterlist', ['employeeInfo' => $employeeInfo, 'payregisters' => $payregisters]);

}));

//confirm submission of timesheets

Route::post('/admin/confirmsubmit', array('as' => '', 'uses' => function()
{

	$data = Input::all();
	
	$adminController = new AdminController;	
	$dataArr = $adminController->init();
	//$id = (int) $id;
	
	$employeesController = new EmployeesController;
	$data["employeeArr"] = $employeesController->selectEmployeeByGroup();

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);	
	
	//$deduc = new Deduction;
	//$empdeducInfo = $deduc->getEmpDeductionById($id);
	
	$emp_id = $data['empid'];
	$headID = $data['head_ID'];
	$cutfrom = $data['schedule_date_from'];
	$cutto = $data['schedule_date_to'];
	
	$checks = Input::get('check');
	
	Session::put('eid', $emp_id);
	Session::put('hid', $headID);
	Session::put('cfrom', $cutfrom);
	Session::put('cto', $cutto);
	Session::put('chks', $checks);
	
	//var_dump($empdeducInfo);
	//return 'Update Company';
	return View::make('admin.confirmsubmit', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employeeInfo' => $employeeInfo));

}));



//submit Employee timesheet per supervisor

Route::post('/admin/timesheet/submitEmpTimesheet', array('as' => 'submitEmpTimesheet', 'uses' => function()
{	

	$adminController = new AdminController;
	$dataArr = $adminController->init();
	
	$dataArr["resourceId"] = 'admin';
	
	$employeesController = new EmployeesController;				
	$dataArr["employeeArr"] = $employeesController->selectEmployeeByGroup();

	$absencesController = new AbsencesController;
	$dataArr["currentAbsencesPerCutoff"] = $absencesController->currentAbsencesPerCutoff();			
	
	//return 'Save New Company';
	
	$data = Input::all();
	//Session::put('statusButton', 'disable');
	
	//for cut-off
	
	//$startdate = '2015-11-11';
	//$enddate = '2015-11-25';
	
	$cutdate = date('Y-m-d');						
	$givenDate = date('Y-m-d', strtotime($cutdate));						
	$dDate = date("d", strtotime($givenDate));						
					
	if(($dDate >= 25) && ($dDate <= 29)) 						
	{							
		$year = date("Y");							
		$month = date("m", strtotime($givenDate));							
		$startday = 11;							
		$date = new DateTime();							
		$date->setDate($year, $month, $startday);							
		$startdate = $date->format('Y-m-d');														
		$endday = 25;							
							
		$date2 = new DateTime();							
		$date2->setDate($year, $month, $endday);							
		$enddate = $date2->format('Y-m-d'); 														
							
	}						
	elseif(($dDate >= 10) && ($dDate <= 14)) 						
	{							
		$year = date("Y");							
		$month = date("m", strtotime($givenDate));							
		$last_month = $month-1%12;							
		$startday = 26;							
		$date = new DateTime();							
		$date->setDate($year, $last_month, $startday);							
		$startdate = $date->format('Y-m-d');															
		$endday = 10;							
							
		$date2 = new DateTime();							
		$date2->setDate($year, $month, $endday);							
		$enddate = $date2->format('Y-m-d');														
							
	}
	
	
	//$vals = Input::get('check');
	$vals = Session::get('chks');
	$headid = Session::get('hid');
	$from = Session::get('cfrom');
	$to = Session::get('cto');
	
	
	
	//var_dump($vals);

	foreach($vals as $val)
	{
		$timesheet = new TimesheetSubmitted;
		$timesheet->head_id = $headid;
		$timesheet->employee_id = $val;
		$timesheet->cutoff_starting_date = $from;
		$timesheet->cutoff_ending_date = $to;
		$timesheet->status = 'submitted';
		$timesheet->is_processed = 0;
		$timesheet->save();
		$status = "submitted"; 
		
		
		DB::table('tbl_payroll_register')
			->insert(array(
						'emp_number' => $val,
						'cutfrom' => $startdate,
						'cutto' => $enddate
		));
	}
	
	//add instance to payroll register
	
	
	
	
	//$data = Input::all();
	
	//var_dump($timesheet);
	
	$timestatus = new TimesheetSubmitted;		
	$timestatusInfo = $timestatus->getStatusByCutoff(Input::get('employeeid'), Input::get('cutoff_starting_date'), Input::get('cutoff_ending_date'));
  
	/*foreach($timestatusInfo as $timestat)
	{
		$stat = $timestat->status;
	}*/
	

	$employeeId = Session::get('currentUserId');

	$searchEmployeeId = Input::get('employeeid');

	Session::put('searchEmployeeId', $searchEmployeeId);

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);
	$employeeSearchInfo = $employee->getEmployeeInfoById($searchEmployeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();
		
		//FOR EMPLOYEE LISTS
		$dataArr["listEmployees"] = Employee::take(5)->get();


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

		}

		//FOR LEAVE LISTS
		$dataArr["leaveCount"] = Leave::count();
		$dataArr["listLeaves"] = Leave::orderBy('id')->take(5)->get();			
		

//	return View::make('employees.admin.clockingsearch', ['employeeInfo' => $employeeInfo, 'employeeSearchInfo' => $employeeSearchInfo, 'searchEmployeeId' => $searchEmployeeId, 'timestatusInfo' => $timestatusInfo]);	

	//return View::make('admin.index', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);
		return View::make('admin.index', $dataArr);

}));


//display cutoff to view payslip


Route::get('/admin/payslip/rangepayslip', array('as' => '', 'uses' => function()
{
	//$id = (int) $id;

	$adminController = new AdminController;
	$dataArr = $adminController->init();

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);	
	
	//$earn = new Earning;
	//$empEarningInfo = $earn->getEmpEarningById($id);
	
	//var_dump($empEarningInfo);
	
	//return 'Update Company';
	//$customer_options = DB::table('customers')->select(DB::raw('concat (first_name," ",last_name) as full_name,id'))->lists('full_name', 'id');
	$timesheets = DB::table('timesheet_submitted')->select(DB::raw('concat (cutoff_starting_date," - ",cutoff_ending_date) as cutdate'))->where('employee_id', $employeeId)->where('is_published', 1)->get();
	
	
	$timesheetArr[0] = '--- Please Select Cut-Off Date ---';
	foreach ($timesheets as $timesheet) {

		$timesheetArr[$timesheet->cutdate] = $timesheet->cutdate;		
		//var_dump($timesheet->cutdate);

	}
	
	//var_dump($timesheetArr);
	
	//return;
	//die();
	
	return View::make('admin.rangepayslip', array('dataArr' => $dataArr, 'timesheetArr' => $timesheetArr, 'employeeInfo' => $employeeInfo));

}));


Route::post('/admin/payslip/rangepayslip', array('as' => '', 'uses' => function()
{
	//$id = (int) $id;

	$adminController = new AdminController;
	$dataArr = $adminController->init();

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);	
	
	//$earn = new Earning;
	//$empEarningInfo = $earn->getEmpEarningById($id);
	
	//var_dump($empEarningInfo);
	
	//return 'Update Company';
	//$customer_options = DB::table('customers')->select(DB::raw('concat (first_name," ",last_name) as full_name,id'))->lists('full_name', 'id');
	$timesheets = DB::table('timesheet_submitted')->select(DB::raw('concat (cutoff_starting_date," - ",cutoff_ending_date) as cutdate'))->where('employee_id', $employeeId)->where('is_published', 1)->lists('cutdate');
	
	//var_dump($timesheets);
	
	return View::make('admin.rangepayslip', array('dataArr' => $dataArr, 'timesheetArr' => $timesheetArr, 'employeeInfo' => $employeeInfo));

}));


Route::get('/employee/payslip/view/{payslipfromdate}/{paysliptodate}', array('as' => '', 'uses' => function($payslipfromdate, $paysliptodate)
{

	$data = Input::all();
	
	//return $payslipfromdate . '-' . $paysliptodate;
	
	//$payslipfromdate = Session::get('payslip_fromdate');
	//$paysliptodate = Session::get('payslip_todate');

	$adminController = new AdminController;
	$dataArr = $adminController->init();

	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');
	
	
	//$dte = DB::table('timesheet_submitted')->max('id')->where('employee_id', $employeeId)->first();		

	/*$dte = DB::table('timesheet_submitted')
                     ->select(DB::raw('max(id), cutoff_starting_date, cutoff_ending_date'))
                     ->where('employee_id', $employeeId)
                     //->groupBy('status')
                     ->first();*/
	
	//var_dump($dte);
	//$cutfrom = $dte->cutoff_starting_date;
	//$cutto = $dte->cutoff_ending_date;
	
	$cutfrom = substr($payslipfromdate,0,10);
	$cutto = substr($paysliptodate,-10);
	
	//echo $cutfrom;
	//echo $cutto;
	
	$employeeId = Session::get('currentUserId');	
	$userId = Session::get('currentUserId');
	$vals = Session::get('values');
	
	Session::put('cfrom', $cutfrom);
	Session::put('cto', $cutto);

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) 
	{
		$groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  
	}

	$currentUser = Sentry::getUser();	
		
	//$employee = new Employee;		
	//$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$employee = Employee::where('id', $employeeId)->first();
	
	$getUserEmployee = DB::table('users')            
		->join('employees', 'users.employee_id', '=', 'employees.id')
		->join('users_groups', 'users_groups.user_id', '=', 'users.id')
		->join('groups', 'users_groups.group_id', '=', 'groups.id')
		->get();	
		
	$paytimesheets = DB::table('timesheet_submitted')->select(DB::raw('concat (cutoff_starting_date," - ",cutoff_ending_date) as cutdate'))->where('employee_id', $employeeId)->where('is_published', 1)->orderBy('cutoff_ending_date', 'desc')->get();
	
	//var_dump($paytimesheets);
	
	$payInfos = DB::table('employees')
		->join('users', 'users.employee_id', '=', 'employees.id')
		->join('job_title', 'employees.position_id', '=', 'job_title.id')
		->join('departments', 'employees.department_id', '=', 'departments.id')
		->join('employee_setting', 'employees.id', '=', 'employee_setting.employee_id')
		->join('tbl_payroll_register', 'employees.id', '=', 'tbl_payroll_register.emp_number')
		//->join('tbl_overtime', 'employees.id', '=', 'tbl_overtime.emp_number')
		->select('employees.id as empid', 'employees.employee_number as employnumber', 'lastname', 'firstname', 'basicpay', 'tax_status', 'email', 'job_title.name as jobtitle', 'departments.name as departmentname', 
				 'sumlates', 'sumabsences', 'amt_lates', 'amt_absences', 'wtax', 'sss_ec', 'pagibig_ec', 'philhealth_ec', 'total_OT', 'wtax_basis', 'total_deductions', 'sss_salary_loan', 'gross_pay', 'net_pay', 'total_nottax_earnings', 'total_tax_earnings', 'cutfrom', 'cutto')
		->where('tbl_payroll_register.cutfrom', $cutfrom)
		->where('tbl_payroll_register.cutto', $cutto)
		->where('tbl_payroll_register.emp_number', $employeeId)
		//->whereIn('employees.id', $vals)
		
		//->where('employees.id', $data['empid'])
		//->whereIn('employees.id', $results)
		->get();

	//var_dump($payInfos);
		
	$payOTs = DB::table('tbl_overtime')
		->join('tbl_ot_rates', 'tbl_ot_rates.Code', '=', 'tbl_overtime.overtime_type')
		->join('employees', 'employees.id', '=', 'tbl_overtime.emp_number')
		->select('emp_number', 'OT_name', 'total_num_hrs', 'total_amt', 'cutfrom', 'cutto')
		->distinct()
		->where('tbl_overtime.cutfrom',$cutfrom)
		->where('tbl_overtime.cutto', $cutto)
		->where('tbl_overtime.emp_number', $employeeId)
		//->whereIn('employees.id', $vals)
		->get();	

	//return View::make('admin.dashboard', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        
	//return View::make('admin.reportsummary', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);
	
	return View::make('index_payslip', array('dataArr' => $dataArr, 'groupName' => $dataArr['groupName'], 'employee' => $employee, 'getUserEmployee' => $getUserEmployee, 'payInfos'=>$payInfos, 'payOTs'=>$payOTs, 'paytimesheets'=>$paytimesheets));

}));


/**
*
* END HERE: IVY LANE F. OPON
*
*/


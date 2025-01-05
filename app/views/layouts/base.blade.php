<?php
//if employee has no leave yesterday and has schedule yesterday and rest day is not equal to 1 and is not holiday
//employee, schedule, holiday, timesheet, leave

    /*$currentUserId = Session::get('currentUserId');
    $dayDateArr = Session::get('dayDateArr');
    $currentDate = Config::get('euler.current_date');
    $currentTime = Config::get('euler.current_time');
    $currentDateTime = Config::get('euler.current_date_time');
    $yesterDayDate = date( "Y-m-d", strtotime('yesterday') );

    $employee = Employee::where('id', $currentUserId)->first();

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

    $schedules = Schedule::where('employee_id', $currentUserId)
                         ->whereIn('schedule_date', $dayDateArr)->get();                        

    $holiday = Holiday::where('holiday_date', $currentDate)->first();

    //Yesterday
    $timesheetYesterday = Timesheet::where('employee_id', $currentUserId)
                          ->where('daydate', $yesterDayDate)->first();                                                              

    $summaryYesterday = Summary::where('employee_id', $currentUserId)
                      ->where('daydate', $yesterDayDate)->first();                                                

    $scheduleYesterday = Schedule::where('employee_id', $currentUserId)
                        ->where('schedule_date', $yesterDayDate)->first();
    
    $holidayYesterday = Holiday::where('holiday_date', $yesterDayDate)->first();    

    $leaveYesterday = DB::select("SELECT * FROM `boph_leave` WHERE ? BETWEEN `from_date` AND `to_date` AND `employee_id` = ?", array($yesterDayDate, 1));

    //CHECK IF HAS LEAVE YESTERDAY
    if ( empty($leaveYesterday) ) {

        $hasNoLeaveYesterday = TRUE;

    } else {

        $hasNoLeaveYesterday = FALSE;

    }

    //CHECK IF YESTERDAY SCHEDULE REST DAY: FALSE                               
    //IF HAS NO LEAVE YESTERDAY
    if ( $scheduleYesterday->rest_day !== 1 &&
         $hasNoLeaveYesterday ) {

    }*/

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">    
    <title>Welcome to Back Office TimeTracker! BPO TimeTracker!</title>

    @section('head')
    <!-- Bootstrap core CSS -->
    <!--link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"-->    

    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('assets/css/jquery-ui.css') }}">     

    <!-- Google Font -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:700,300,400|Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>    

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ URL::asset('assets/css/font-awesome.min.css') }}">   

    <!-- Custom styles for this template -->
    <!--link href="{{ URL::asset('assets/css/dashboard.css') }}" rel="stylesheet"-->   
    <!--link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet"-->   

    <link href="{{ URL::asset('assets/css/metisMenu.min.css') }}" rel="stylesheet">    
    <!--link href="{{ URL::asset('assets/css/metisMenu-default-theme.css') }}" rel="stylesheet"-->    

    <link href="{{ URL::asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet"> 

    <link href="{{ URL::asset('assets/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet"> 

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src={{ URL::asset('assets/js/ie8-responsive-file-warning.js') }}"></script><![endif]-->
    <script src="{{ URL::asset('assets/js/ie-emulation-modes-warning.js') }}"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    @show
  </head>

	<body>
	    @yield('body')
	</body>

  	@section('foot')
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->    
    <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>    
    <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
    <!--script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script-->    
    <script src="{{ URL::asset('assets/js/jquery-ui.min.js') }}"></script>     
    
    
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ URL::asset('assets/js/ie10-viewport-bug-workaround.js') }}"></script>

    <!-- Moment.js -->
    <script src="{{ URL::asset('assets/js/moment.js') }}"></script>

    <!-- Datatables code -->
    <script src="{{ URL::asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/jquery.jeditable.js') }}"></script>

    <!-- Bootbox code http://bootboxjs.com/-->
    <script src="{{ URL::asset('assets/js/bootbox.js') }}"></script>  

    <!-- Twitter Bootstrap specific plugin -->
    <script src="{{ URL::asset('assets/js/collapse.js') }}"></script>
    <script src="{{ URL::asset('assets/js/transition.js') }}"></script>
    <script src="{{ URL::asset('assets/js/dropdown.js') }}"></script>

    <script src="{{ URL::asset('assets/js/holder.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/metisMenu.min.js') }}"></script>

    <script src="{{ URL::asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>


    <script src="{{ URL::asset('assets/js/scripts.js') }}"></script>
    
    @show    
  </body>
</html>  
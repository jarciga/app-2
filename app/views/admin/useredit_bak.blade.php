{{-- Child Template --}}
@extends('layouts.admin.sidebarcontent')

@section('head')
  @parent

@stop

@section('sidebar')
  @include('partials.admin.sidebar')
@stop

@section('content')

<?php

$employeeEditId = $employeeEditInfo[0]->id;

$message = Session::get('message');

//echo 'employeeEdit:'.$employeeEditId;
//exit;

$companies = Company::all();
$departments = Department::all();
$jobTitles = JobTitle::all();

$managers = Employee::all();
$supervisors = Employee::all();

$roles = DB::table('groups')->get();

if ( isset($employeeEditId) && !empty($employeeEditId) ) {
  $userEdit = DB::table('users')->where('employee_id', $employeeEditId)->first();  
  
  $payEdit = DB::table('employee_setting')->where('employee_id', $employeeEditId)->first();
}

if( isset($userEdit) && !empty($userEdit) ) {
  $userGroupEdit = DB::table('users_groups')->where('user_id', $userEdit->id)->first(); 
}

if( isset($userGroupEdit) && !empty($userGroupEdit) ) {
  $groupEdit = DB::table('groups')->where('id', (int) $userGroupEdit->group_id)->first(); 
}

$companyArr[0] = '';

if( !empty($companies) ) {
  foreach ($companies as $company) {

      $companyArr[$company->id] = $company->name;

  }
}

$departmentArr[0] = '';

if( !empty($departments) ) {
  foreach ($departments as $department) {

      $departmentArr[$department->id] = $department->name;

  }
}

$jobTitleArr[0] = '';

if( !empty($jobTitles) ) {
  foreach ($jobTitles as $jobTitle) {

      $jobTitleArr[$jobTitle->id] = $jobTitle->name;

  }
}

$managerArr[0] = '';

if( !empty($managers) ) {
  foreach ($managers as $manager) {

     $fullname = $manager->firstname.', '.$manager->lastname;

      $managerArr[$manager->id] = $fullname;

  }
}

$supervisorArr[0] = '';

if( !empty($supervisors) ) {
  foreach ($supervisors as $supervisor) {

     $fullname = $supervisor->firstname.', '.$supervisor->lastname;

      $supervisorArr[$supervisor->id] = $fullname;

  }
}

$roleArr[0] = '';

if( !empty($roles) ) {

  foreach($roles as $role) {

    //echo $role->name;
    $roleArr[$role->id] = $role->name;

  }

}



//Check type of Employee
if ( isset($employeeEditId) && !empty($employeeEditId) ) {

  $checkEmployeeType = DB::table('employees')->select('employee_type')->where('id', $employeeEditId)->first();

  //dd($checkEmployeeType->employee_type);

  if ( isset($checkEmployeeType) && !empty($checkEmployeeType) ) {

    if ( $checkEmployeeType->employee_type === 1 ) { //is_manager
    
      $isManager = true;      
    
    } else {

       $isManager = '';

    }

    if ( $checkEmployeeType->employee_type === 2 ) { //is_supervisor
    
       $isSupervisor = true;  
    
    } else {

       $isSupervisor = '';

    }

    if ( $checkEmployeeType->employee_type === 0 ) { //is_employee
    
       $isEmployee = true;
    
    } else {

       $isEmployee = '';

    }

  }

}

?>

<!--div class="page-container"-->

        <!--div class="row" style="padding-bottom:20px;"-->

          <div id="content" class="col-md-12" role="main">

            <ol class="breadcrumb hide hidden">
              <li><a href="#">Home</a></li>
              <li class="active">Page</li>
            </ol>

            <h1 class="page-header">Edit</h1>

        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading" style="font-size:14px; font-weight:bold;">Edit Employee</div>
          <div class="panel-body">

            @if ($errors->has())
            <div class="alert alert-danger" role="alert">
                <ul style="list-style:none; margin:0; padding:0;">
                    @foreach ($errors->all() as $error)
                        <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Error:</span>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @elseif ( !empty($message) )
                <div class="alert alert-success" role="alert">
                    <ul style="list-style:none; margin:0; padding:0;">
                        
                        <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon" aria-hidden="true"></span> <span class="sr-only">Success:</span>{{ $message }}</li>

                    </ul>
                </div>
            @endif 

            {{-- Form::open(array('url' => '/admin/user/edit', 'id' => '', 'class' => 'form-horizontal')) --}}              
            {{-- Form::open( array('route' => array('adminUserProcessUpdate'), 'id' => '', 'class' => 'form-horizontal') ) --}}              


            {{ Form::open(array('url' => '/admin/user/'.$employeeEditId.'/edit', 'id' => '', 'class' => 'form-horizontal')) }}              

            {{ Form::hidden('employee_id',  $employeeEditInfo[0]->id); }}
            <!--div class="form-group hide hidden">
                {{ Form::label('Designation', 'Designation', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('designation', array(0 => '', 1 => 'Manager', 2 => 'Supervisor', 3 => 'Employee'), 0, array('id' => 'designation', 'class' => 'form-control')) }}                
                </div>
            </div-->             

            <div class="form-group">
                {{ Form::label('employe_number', 'Employee Number', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::text('employee_number', $employeeEditInfo[0]->employee_number, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Employee Number')) }}
                </div>
            </div>            

            <div class="form-group">           
                {{ Form::label('firstname', 'Firstname', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('firstname', $employeeEditInfo[0]->firstname, array('id' => '', 'class' => 'form-control', 'placeholder' => 'First name')) }}            
                </div>                        
            </div>            

            <div class="form-group">           
                {{ Form::label('lastname', 'Lastname', array('class' => 'col-sm-2 control-label')) }}            
                <div class="col-sm-3">            
                {{ Form::text('lastname', $employeeEditInfo[0]->lastname, array('id' => '', 'class' => 'form-control', 'placeholder' => 'last name')) }}            
                </div>            
            </div>            
            

            
            <div class="form-group">           
                {{ Form::label('middlename', 'Middlename', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('middlename', $employeeEditInfo[0]->middle_name, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Middle name')) }}            
                </div>                        
            </div>            
            
            <div class="form-group">           
                {{ Form::label('nick_name', 'Nick name', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">           
                {{ Form::text('nick_name', $employeeEditInfo[0]->nick_name, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Nick name')) }}            
                </div>                        
            </div>

            <div class="form-group">
                {{ Form::label('Company', 'Company', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('company_id', $companyArr, $employeeEditInfo[0]->company_id, array('id' => '', 'class' => 'form-control')) }}
                </div>
            </div>            

            <div class="form-group">
                {{ Form::label('Department/Campaign', 'Department/Campaign', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('department_id', $departmentArr, $employeeEditInfo[0]->department_id, array('id' => '', 'class' => 'form-control')) }}                
                </div>
            </div>

            <div class="form-group">
              {{ Form::label('Type of Employee', 'Type of Employee', array('class' => 'col-sm-2 control-label')) }}                         
              <div class="checkbox">                
                  <label for="Is Employee">
                  {{ Form::radio('is_employee_type', 'is_employee', $isEmployee, array('id' => 'is-employee-type')) }}
                  Is Employee
                  </label>    

                  <label for="Is Manager">
                  {{ Form::radio('is_employee_type', 'is_manager', $isManager, array('id' => 'is-employee-type')) }}
                  Is Manager
                  </label>    

                  <label for="Is Supervisor">
                  {{ Form::radio('is_employee_type', 'is_supervisor', $isSupervisor, array('id' => 'is-employee-type')) }}
                  Is Supervisor
                  </label> 
                  
              </div>   
            </div>                        

            <div class="form-group department-head-form-group">

                {{ Form::label('Department Head', 'Department Head', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('department_head', $managerArr, $employeeEditInfo[0]->manager_id, array('id' => 'department-head', 'class' => 'form-control')) }}                
                </div>
            </div>            

            <div class="form-group supervisor-form-group">
                {{ Form::label('Supervisor', 'Supervisor', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('supervisor_id', $supervisorArr, $employeeEditInfo[0]->supervisor_id, array('id' => 'supervisor', 'class' => 'form-control')) }}                
                </div>
            </div>            

            <div class="form-group">
                {{ Form::label('Job Title', 'Job Title', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('position_id', $jobTitleArr, $employeeEditInfo[0]->position_id, array('id' => '', 'class' => 'form-control')) }}                
                </div>
            </div> 

            <div class="form-group">
                {{ Form::label('Role', 'Role', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('role_id', $roleArr, $groupEdit->id, array('id' => '', 'class' => 'form-control')) }}                
                </div>
            </div>                       

            <div class="form-group">           
                {{ Form::label('email', 'Email', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">           
                {{ Form::text('email', $userEdit->email, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Email')) }}            
                </div>                        
            </div>            

            <div class="form-group">           
                {{ Form::label('New Password', 'New Password', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::password('password', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Password')) }}            
                </div>                        
            </div>   

            <div class="form-group">           
                {{ Form::label('Confirm New Password', 'Confirm New Password', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::password('password_confirmation', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Confirm Password')) }}            
                </div>                        
            </div>                           
            		
			<hr>
			
			<!-- for employee settings -->
			<div class="form-group">           
                {{ Form::label('Basic Monthly Rate', 'Basic Monthly Rate', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('basicmonthly', $payEdit->basicmonthly, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Basic Monthly Rate')) }}            
                </div>                        
            </div>
			
			<div class="form-group">           
                {{ Form::label('Daily Rate', 'Daily Rate', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('dailysal', $payEdit->dailysal, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Daily Rate')) }}            
                </div>                        
            </div>
			
			<div class="form-group">           
                {{ Form::label('Basic Pay', 'Basic Pay', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('basicpay', $payEdit->basicpay, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Email')) }}            
                </div>                        
            </div>
			
			<hr>
			
			<div class="form-group">           
                {{ Form::label('Ecola', 'Ecola', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('ecola', $payEdit->ecola, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Ecola')) }}            
                </div>                        
            </div>
			
			<div class="form-group">           
                {{ Form::label('Deminimis', 'Deminimis', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('deminimis', $payEdit->deminimis, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Deminimis')) }}            
                </div>                        
            </div>
			
			<hr>
			
			<div class="form-group">           
                {{ Form::label('Tax Exemption', 'Tax Exemption', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ 	Form::select('tax_status', array('S' => 'Single', 'M' => 'Married', 'S1' => 'Single w/ 1 Dependent', 'S2' => 'Single w/ 2 Dependents', 'S3' => 'Single w/ 3 Dependents', 'S4' => 'Single w/ 4 Dependents', 'M1' => 'Married w/ 1 Dependent', 'M2' => 'Married w/ 2 Dependents', 'M3' => 'Married w/ 3 Dependents', 'M4' => 'Married w/ 4 Dependents'), $payEdit->tax_status, ['class' => 'form-control'])}}            
                </div>                        
            </div>
                               

            <div class="col-sm-offset-2 col-sm-10">
                <div class="col-sm-3">                        
                {{ Form::submit('Update User', array('class' => '', 'class' => 'btn btn-primary')) }}
                </div>                                        
            </div>            
            
          </div>        
        </div>           

          </div><!--//#content .col-md-12-->          

        <!--/div--><!--//.row-->

      <!--/div--><!--//.page-container-->

@stop

@section('foot')
  @parent    

<script>

/**
*
* SIDEBAR
*
*/

$(function () {
$('#collapse1, #collapse2, #collapse3').collapse('hide');        
$('#collapse4, #collapse5, #collapse6').collapse('show');        

$('#menu').metisMenu();
});    


//Server Time
function updateServerTime() {

  $.get("{{ route('updateServerTime') }}", function(data, status){    

    //console.log("Data: " + data + "\nStatus: " + status);
    $("#clock").html(data); 

  }); 

}
setInterval('updateServerTime()', 1000);

function getServerDateTime(handleData) {

  var serverDateTime;
  var getServerTime = $.ajax({
    type: "GET",
    url : "{{ route('getServerDateTime') }}", //http://localhost:8000/employee",          
    success : function(data) {
      
      handleData(data);

    }
  },"json");

}

$(document).ready(function() {

  /**
  *
  * INIT: TIMESHEET/SUMMARY
  *
  */  

  getTimesheet();
  getSummary();

  //td.ot-status-btn .ot-apply-btn
  $('#timesheet tbody, #timesheet-ajax tbody').on('click', 'td.ot-status-btn .ot-apply-btn', function () {

        //console.log('td.ot-status-btn .ot-apply-btn');

        var tr = $(this).closest('tr');
        ////var row = table.row( tr );
        var rowIdx = $(this).parent().index(); //row.index(); // start at 0
        ////var rowObj = row.data();    
        
        var TimesheetId = tr.attr('id'); //rowObj.id;
        var dataType;
        //var dataString;

        var dataString = 'timesheetId=' + TimesheetId + '&otStatus=' + -1;

        //console.log(dataString);

        $.ajax({                    
          type: "POST",
          url : "{{ route('redraw.overtimestatus') }}",
          data : dataString,
          success : function(data) {
            
            var overtimeStatusText = $('td.ot-status-btn:eq(' + rowIdx + ') .ot-apply-btn').text();

            if ('Apply OT' === overtimeStatusText) {

              //console.log(data);

              //var dataString = 'timesheetId=' + TimesheetId + '&otStatus=' + -1;                         
              $('td.ot-status-btn:eq(' + rowIdx + ') .ot-apply-btn').remove();
              $('td.ot-status-btn:eq(' + rowIdx + ')').html('<span class=\"label label-success\" style=\"padding: 2px 4px; font-size: 11px;\">Pending</span>');
                
            }

              $.ajax({
                type: "GET",
                url : "{{ route('redraw.timesheet') }}", //http://localhost:8000/employee",
                data : '',
                success : function(data) {
                  //console.log(data);
                  var obj = JSON.parse(data);

                 // console.log(obj.data);

                 //Timesheet
                  getTimesheet();

                }
            },"json");


          },
          dataType: dataType    
        });

   } );     

  /**
  *
  * BUTTON: TIME IN / TIME OUT
  *
  */

  //Time In button
  $('#time-clocking-btn.time-in').click(function(e) {                                                    
      e.preventDefault();

      //console.log('Time In button');

      var employeeNumber = $('#employee-number').val();
      var dayDate = $('#day-date').val();
      var schedIn = $('#sched-in').val();
      var schedOut = $('#sched-out').val();      
      var timeIn = $('#time-in').val();
      var timeIn1 = $('#time-in-1').val();
      var timeIn2 = $('#time-in-2').val();
      var timeIn3 = $('#time-in-3').val();
      var timeNow = $('#time-now').val();

      var timeClocking = 'in';             
      var forgotYesterdayTimeOut = 0;

      var dataString = 'timeclocking=' + timeClocking + '&timenow=' + timeNow + '&employeeno=' + employeeNumber + '&daydate=' + dayDate + '&schedin=' + schedIn + '&schedout=' + schedOut;
          dataString += '&timein=' + timeIn + '&timein1=' + timeIn1 + '&timein2=' + timeIn2 + '&timein3=' + timeIn3 + '&forgotyesterdaytimeout=' + forgotYesterdayTimeOut;                                                    

      //console.log(dataString);

      $.ajax({
          type: "POST",
          url : "{{ route('timeClocking') }}", //http://localhost:8000/employee",
          data : dataString,
          success : function(data) {

            //console.log(data);

            //Timesheet
            getTimesheet('post', 'in');

            //Summary Computation Init
            getSummary();             

            var timeClockingBtn = $('#time-clocking-btn.time-in').text();

            if (timeClockingBtn.toLowerCase() === 'time in') {

                console.log(timeClockingBtn.toLowerCase());

                $('#time-clocking-btn.time-in').addClass('hide').hide();     
                $('#time-clocking-btn.time-out').removeClass('hide').show(); 

                //$("#wait").hide();

            }             

          }
      },"json"); 

  }); 


  //Time Out button
  $('#time-clocking-btn.time-out').click(function(e) {                                                    
      e.preventDefault();

      //console.log('Time Out button');

      var employeeNumber = $('#employee-number').val();
      var dayDate = $('#day-date').val();
      var schedIn = $('#sched-in').val();
      var schedOut = $('#sched-out').val();      
      var timeOut = $('#time-out').val();
      var timeOut1 = $('#time-out-1').val();
      var timeOut2 = $('#time-out-2').val();
      var timeOut3 = $('#time-out-3').val();
      var timeNow = $('#time-now').val();

      var timeClocking = 'out';             
      var forgotYesterdayTimeOut = 0;

      var dataString = 'timeclocking=' + timeClocking + '&timenow=' + timeNow + '&employeeno=' + employeeNumber + '&daydate=' + dayDate + '&schedin=' + schedIn + '&schedout=' + schedOut;
          dataString += '&timeout=' + timeOut + '&timeout1=' + timeOut1 + '&timeout2=' + timeOut2 + '&timeout3=' + timeOut3 + '&forgotyesterdaytimeout=' + forgotYesterdayTimeOut;                                                    

      //console.log(dataString); 

      $.ajax({
          type: "POST",
          url : "{{ route('timeClocking') }}", //http://localhost:8000/employee",
          data : dataString,
          success : function(data) {

            //console.log(data);

            //Timesheet
            getTimesheet('post', 'out');

            //Summary Computation Init
            getSummary(); 

            var timeClockingBtn = $('#time-clocking-btn.time-out').text();

             console.log(timeClockingBtn);

            if (timeClockingBtn.toLowerCase() === 'time out') {

                console.log(timeClockingBtn.toLowerCase());

                $('#time-clocking-btn.time-in').removeClass('hide').show();                               
                $('#time-clocking-btn.time-out').addClass('hide').hide();   

                //$("#wait").hide();                 
           
            }                        

          }
      },"json");                    

  });  

});     

var method = 'get';
function getTimesheet(method, button) {
  
  var message;

  $.ajax({
      type: "GET",
      url : "{{ route('redraw.timesheet') }}", //http://localhost:8000/employee",
      data : '',
      success : function(data) {

        var obj = JSON.parse(data);
        $.each(obj.data, function(i, item) {

          //console.log(item.id);
          
          $(".timesheet-id-"+item.id).text(item.id);
          $(".timesheet-daydate-"+item.id).text(item.daydate);
          $(".timesheet-schedule-"+item.id).text(item.schedule);
          $(".timesheet-in1-"+item.id).text(item.in_1);
          $(".timesheet-out1-"+item.id).text(item.out_1);
          $(".timesheet-in2-"+item.id).text(item.in_2);
          $(".timesheet-out2-"+item.id).text(item.out_2);
          $(".timesheet-in3-"+item.id).text(item.in_3);
          $(".timesheet-out3-"+item.id).text(item.out_3);
          $(".timesheet-nightdifferential-"+item.id).text(item.night_differential);
          $(".timesheet-totalhours-"+item.id).text(item.total_hours);
          $(".timesheet-workhours-"+item.id).text(item.work_hours);
          $(".timesheet-totalovertime-"+item.id).text(item.total_overtime);
          $(".timesheet-tardiness-"+item.id).text(item.tardiness);
          $(".timesheet-undertime-"+item.id).text(item.undertime);
          $(".timesheet-otstatus-"+item.id).html(item.overtime_status);

            if('post' === method && 'in' === button && item.message_in !== "") {
              
              console.log(item.message_in);          
              bootbox.alert(item.message_in);
              
            } else if('post' === method && 'out' === button && item.message_out !== "") {

              console.log(item.message_out);          
              bootbox.alert(item.message_out);

            }
        
        }); 
  


      }

    },"json");
}

function getSummary() {

  //Summary Computation Init
  $.ajax({
      type: "GET",
      url : "{{ route('redraw.summary') }}", //http://localhost:8000/employee",
      data : '',
      success : function(data) {
        
        var obj = JSON.parse(data);

        var lates = '', undertime = '', absences = '', paidVacationLeave = '', paidSickLeave = '', leaveWithoutPay = '', maternityLeave = '', paternityLeave = '';
        var regular = '', regularOt = '', restDay = '', restDayOt = '';
        var restDaySpecialHoliday = '', restDaySpecialHolidayOt = '', restDayLegalHoliday = '', restDayLegalHolidayOt = '';
        var specialHoliday = '', specialHolidayOt = '', legalHoliday = '', legalHolidayOt = '';

        //With Night Diff

        var regularOtNd = '', regularNd = '', restDayOtNd = '', restDayNd = '';
        var restDaySpecialHolidayOtNd = '', restDaySpecialHolidayNd = '', restDayLegalHolidayOtNd = '', restDayLegalHolidayNd = '';
        var specialHolidayOtNd = '', specialHolidayNd = '', legalHolidayOtNd = '', legalHolidayNd = '';                  


        //SUMMARY: 1st column
        lates = obj.data[0].tardiness;
        undertime = obj.data[0].undertime;
        absences = obj.data[0].absences; 

        paidVacationLeave = obj.data[0].paid_vacation_leave; 
        paidSickLeave = obj.data[0].paid_sick_leave;
        leaveWithoutPay = obj.data[0].leave_without_pay;
        maternityLeave = obj.data[0].maternity_leave;
        paternityLeave = obj.data[0].paternity_leave;  

        //SUMMARY: 2nd Column
        regular = obj.data[0].regular;                  
        regularOt = obj.data[0].regular_overtime;                  
        restDay = obj.data[0].rest_day;
        restDayOt = obj.data[0].rest_day_overtime;

        //With Night Diff
        regularOtNd = obj.data[0].regular_overtime_night_diff;
        regularNd = obj.data[0].regular_night_differential;
        restDayOtNd = obj.data[0].rest_day_overtime_night_diff;
        restDayNd = obj.data[0].rest_day_night_diff;


        //SUMMARY: 3rd Column
        restDaySpecialHoliday = obj.data[0].rest_day_special_holiday;
        restDaySpecialHolidayOt = obj.data[0].rest_day_special_holiday_overtime;
        restDayLegalHoliday = obj.data[0].rest_day_legal_holiday;
        restDayLegalHolidayOt = obj.data[0].rest_day_legal_holiday_overtime;

        //With Night Diff
        restDaySpecialHolidayOtNd = obj.data[0].rest_day_special_holiday_overtime_night_diff;
        restDaySpecialHolidayNd = obj.data[0].rest_day_special_holiday_night_diff;
        restDayLegalHolidayOtNd = obj.data[0].rest_day_legal_holiday_overtime_night_diff;
        restDayLegalHolidayNd = obj.data[0].rest_day_legal_holiday_night_diff;


        //SUMMARY: 4th Column
        specialHoliday = obj.data[0].special_holiday;
        specialHolidayOt = obj.data[0].special_holiday_overtime;
        legalHoliday = obj.data[0].legal_holiday;
        legalHolidayOt = obj.data[0].legal_holiday_overtime;

        //With Night Diff
        specialHolidayOtNd = obj.data[0].special_holiday_overtime_night_diff;
        specialHolidayNd = obj.data[0].special_holiday_night_diff;
        legalHolidayOtNd = obj.data[0].legal_holiday_overtime_night_diff;
        legalHolidayNd = obj.data[0].legal_holiday_night_diff;

        //SUMMARY: 1st Column
        if (lates !== '0') {

          $('#lates-ut').text(lates);

        } 

        if (undertime !== '0') {

          $('#lates-ut').text(undertime);

        }    

        if (lates !== '0' && undertime !== '0') {

          $('#lates-ut').text(lates + ' / ' + undertime);

        }

        if (absences !== '0') {

          $('#absences').text(absences);

        }

        if (paidVacationLeave !== '0') {

          $('#paid-sl').text(paidSickLeave);

        }

        if (paidSickLeave !== '0') {

          $('#paid-vl').text(paidVacationLeave);

        }

        if (leaveWithoutPay !== '0') {

          $('#leave-without-pay').text(leaveWithoutPay);

        }

        if (maternityLeave !== '0') {

          $('#maternity-leave').text(maternityLeave);

        }

        if (paternityLeave !== '0') {

          $('#paternity-leave').text(paternityLeave);

        }                                                                        


        //SUMMARY: 2nd Column
        if (regular !== '0') {

          $('#regular').text(regular);

        }

        if (regularOt !== '0') {

          $('#reg-ot').text(regularOt);

        }

        if (restDay !== '0') {

          $('#rd').text(restDay);

        }                  

        if (restDayOt !== '0') {

          $('#rd-ot').text(restDayOt);

        }

        //With Night Diff
        if (regularOtNd !== '0') {

          $('#reg-ot-nd').text(regularOtNd);

        }

        if (regularNd !== '0') {

          $('#reg-nd').text(regularNd);

        }                                    

        if (restDayOtNd !== '0') {

          $('#rd-ot-nd').text(restDayOtNd);

        }                  

        if (restDayNd !== '0') {

          $('#rd-nd').text(restDayNd);

        }


        //SUMMARY: 3rd Column 
        if (restDaySpecialHoliday !== '0') {

          $('#rd-spl-holiday').text(restDaySpecialHoliday);

        }

        if (restDaySpecialHolidayOt !== '0') {

          $('#rd-spl-holiday-ot').text(restDaySpecialHolidayOt);

        }

        if (restDayLegalHoliday !== '0') {

          $('#rd-legal-holiday').text(restDayLegalHoliday);

        }

        if (restDayLegalHolidayOt !== '0') {

          $('#rd-legal-holiday-ot').text(restDayLegalHolidayOt);

        }                  

        //With Night Diff 
        if (restDaySpecialHolidayOtNd !== '0') {

          $('#rd-spl-holiday-ot-nd').text(restDaySpecialHolidayOtNd);

        }

        if (restDaySpecialHolidayNd !== '0') {

          $('#rd-spl-holiday-nd').text(restDaySpecialHolidayNd);

        }

        if (restDayLegalHolidayOtNd !== '0') {

          $('#rd-legal-holiday-ot-nd').text(restDayLegalHolidayOtNd);

        }

        if (restDayLegalHolidayNd !== '0') {

          $('#rd-legal-holiday-nd').text(restDayLegalHolidayNd);

        }                  
                          


        //SUMMARY: 4th Column
        
        if (specialHoliday !== '0') {

          $('#spl-holiday').text(specialHoliday);

        }                                                                     

        if (specialHolidayOt !== '0') {

          $('#spl-holiday-ot').text(specialHolidayOt);

        } 

        if (legalHoliday !== '0') {

          $('#legal-holiday').text(legalHoliday);

        } 

        if (legalHolidayOt !== '0') {

          $('#legal-holiday-ot').text(legalHolidayOt);

        }    

        //With Night Diff
        if (specialHolidayOtNd !== '0') {

          $('#spl-holiday-ot-nd').text(specialHolidayOtNd);

        }                                                                     

        if (specialHolidayNd !== '0') {

          $('#spl-holiday-nd').text(specialHolidayNd);

        } 

        if (legalHolidayOtNd !== '0') {

          $('#legal-holiday-ot-nd').text(legalHolidayOtNd);

        } 

        if (legalHolidayNd !== '0') {

          $('#legal-hoiday-nd').text(legalHolidayNd);

        }                                                                     

  
      }
  },"json");  

}        

</script> 

@stop
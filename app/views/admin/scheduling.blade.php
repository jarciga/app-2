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

$employeeId = Session::get('currentUserId');
$dayOfTheWeek = date('l');
$currentDate = date('Y-m-d');
$shift = 1;

$employee = DB::table('employees')->where('id', $employeeId)->get();

$company = DB::table('employees')->where('id', $employee[0]->company_id)->get(); 
$manager = DB::table('employees')->where('id', $employee[0]->manager_id)->get(); 
$jobTitle = DB::table('job_title')->where('id', $employee[0]->position_id)->get(); 
$department = DB::table('departments')->where('id', $employee[0]->department_id)->get(); 

$employeesByManager = DB::table('employees')->where('manager_id', $employeeId)->get();

//$getSchedule = DB::table('employee_schedule')->where('employee_id', $employee->id)->where('schedule_date', trim($currentDate))->get();
//$getWorkShiftByDayOfTheWeek = DB::table('work_shift')->where('employee_id',  $employee->id)->where('name_of_day', $dayOfTheWeek)->where('shift', $shift)->get();

?>
<!--div class="page-container"-->

        <!--div class="row" style="padding-bottom:20px;"-->

          <div id="content" class="col-md-12" role="main">

            <ol class="breadcrumb hide hidden">
              <li><a href="#">Home</a></li>
              <li class="active">Page</li>
            </ol>

            <h1 class="page-header">Employee Scheduling</h1>

       		<div class="panel panel-default">
			  <!-- Default panel contents -->
			  <div class="panel-heading" style="font-size:14px; font-weight:bold;">Employee Scheduling <div class="pull-right"><a href="{{ url('/admin/user/new') }}" class="">Add New</a></div></div>
			  <div class="panel-body">

				<div role="tabpanel">

				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#updateDefaultSchedule" aria-controls="updateDefaultSchedule" role="tab" data-toggle="tab">Update Default Schedule</a></li>
				<li role="presentation"><a href="#uploadNewScheduleUpdateUploadedSchedule" aria-controls="uploadNewScheduleUpdateUploadedSchedule" role="tab" data-toggle="tab">Upload New Schedule / Update Uploaded Schedule</a></li>
				</ul>

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="updateDefaultSchedule">

							<div class="tabpanel-container" style="padding:10px;">

								{{ Form::open(array('url' => '/admin/scheduling/search/default/schedule/')) }}

									{{ Form::label('Employee Number', 'Employee Number'); }}
									{{ Form::text('employee_number', ''); }}
									{{ Form::submit('Search', array('class' => '', 'class' => 'btn btn-primary')) }}

								{{ Form::close() }}


								<?php if( !empty($defaultSchedules) ) { ?>

								{{ Form::open(array('route' => 'editDefaultSchedule', 'id' => '', 'class' => 'form-horizontal')) }}            							
								            
								<table class="table table-striped table-hover table-list display" cellspacing="0" width="100%">

								        <thead>
								            <tr>
								                <th>Day</th>
								                <!--th>Shift</th-->                
								                <th>Rest day</th>
								                <!--th>Hours per day</th-->                
								                <th>Start time</th>
								                <th>End time</th>                                                
								            </tr>
								        </thead>

								        <tbody>            
								            	
									            <?php

									            foreach($defaultSchedules as $key => $defaultSchedule) {

									            	list($starttimehh, $starttimemm) = explode(':', $defaultSchedule->start_time);
									               	list($endtimehh, $endtimemm) = explode(':', $defaultSchedule->end_time);

									            ?>

									            <tr>
									                <td>
									                	<?php echo Form::hidden('schedule['.$key.'][defaultScheduleId]',  $defaultSchedule->id); ?>
									                	<?php echo Form::hidden('schedule['.$key.'][employeeId]',  $defaultSchedule->employee_id); ?>
									                	<?php echo Form::text('schedule['.$key.'][nameofday]', $defaultSchedule->name_of_day, array()); ?>
									                </td>
									                <!--td>Shift</td-->                
									                <td><?php echo Form::select('schedule['.$key.'][restday]', array(0 => 'No', 1 => 'Yes'), $defaultSchedule->rest_day, array()); ?></td>                
									                <!--td>Hours per day</td-->                
									                <td>
									                <?php
									                    echo Form::select(
									                             'schedule['.$key.'][starttimehh]',
									                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10', 11 => '11', 12 => '12',
									                                   13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24'
									                                  ),
									                             $starttimehh,
									                             array()
									                         );
									                ?>
									                
									                <?php
									                    echo Form::select(
									                             'schedule['.$key.'][starttimemm]',
									                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
									                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
									                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
									                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
									                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
									                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
									                                   60 => '60'),
									                             $starttimemm,
									                             array()
									                         );
									                ?>
									                </td>
									                <td>
									                <?php
									                    echo Form::select(
									                             'schedule['.$key.'][endtimehh]',
									                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10', 11 => '11', 12 => '12',
									                                   13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24'
									                                  ),
									                             $endtimehh,
									                             array()
									                         );
									                ?>
									                
									                <?php
									                    echo Form::select(
									                             'schedule['.$key.'][endtimemm]',
									                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
									                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
									                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
									                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
									                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
									                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
									                                   60 => '60'),
									                             $endtimemm,
									                             array()
									                         );
									                ?>
									                </td>                                                
									            </tr>

									            <?php } ?>
											
								        </tbody>

								    </table>

								        <div class="form-group pull-right">
								            <div class="col-md-12">
								            {{ Form::submit('Update', array('class' => '', 'class' => 'btn btn-primary')) }}          
								            </div>
								        </div> 

								    {{ Form::close() }}

								<?php } ?>							

								

							</div>

						</div><!--//#updateDefaultSchedule-->

					<div role="tabpanel" class="tab-pane" id="uploadNewScheduleUpdateUploadedSchedule">
						


							<div class="tabpanel-container" style="padding:10px;">

								<h4 style="font-family: "Times New Roman", Times, serif;">Upload New Schedule</h4>
								{{ Form::open(array('url'=>'/admin/scheduling/upload/new/schedule','files'=>true, 'method'=>'POST')) }}

								    <div class="form-group">							    	
									{{-- Form::label('file','Upload New Schedule',array('id'=> '', 'class' => 'col-sm-3 control-label')) --}}	
									{{ Form::file('file','',array('id' => '', 'class' => 'form-control')) }}    
								    </div>

								    <div class="form-group">
									{{ Form::submit('Save', array('class' => '', 'class' => 'btn btn-primary')) }}
									{{ Form::reset('Reset', array('class' => '', 'class' => 'btn btn-primary')) }}
									</div>

								{{ Form::close() }}


								<h4 style="font-family: Times New Roman, Times, serif;">Update Uploaded Schedule</h4>

								{{ Form::open(array('url' => '/admin/scheduling/search/uploaded/schedule')) }}

									{{ Form::label('Employee Number', 'Employee Number'); }}
									{{ Form::text('employee_number', ''); }}

									{{ Form::label('Date From', 'Date From') }}
									{{ Form::text('schedule_date_from', '', array('class' => 'datepicker')) }}
									{{ Form::label('Date To', 'Date to') }}
									{{ Form::text('schedule_date_to', '', array('class' => 'datepicker')) }}

									{{ Form::submit('Search', array('class' => '', 'class' => 'btn btn-primary')) }}								

								{{ Form::close() }}


								<?php if( !empty($uploadedSchedules) ) { ?>

									
								{{ Form::open(array('route' => 'editUploadedSchedule', 'id' => '', 'class' => 'form-horizontal')) }}            							
								            
								<table class="table table-striped table-hover table-list display" cellspacing="0" width="100%">

								        <thead>
								            <tr>
								                <th>Schedule Date</th>
								                <th>Shift</th>                
								                <th>Rest day</th>
								                <!--th>Hours per day</th-->                
								                <th>Start time</th>
								                <th>Start Date</th>
								                <th>End time</th>  
								                <th>End Date</th>                                              
								            </tr>
								        </thead>

								        <tbody>            
								            	
											<?php

								            foreach($uploadedSchedules as $key => $uploadedSchedule) {

												list($startDate, $startTime) = explode(' ', $uploadedSchedule->start_time);
												list($endDate, $endTime) = explode(' ', $uploadedSchedule->end_time);

								            	list($starttimehh, $starttimemm) = explode(':', trim($startTime));
								               	list($endtimehh, $endtimemm) = explode(':', trim($endTime));							            	

								            ?>

								            <tr>
								                <td>
								                	<?php echo $uploadedSchedule->schedule_date; ?>
								                	<?php echo Form::hidden('schedule['.$key.'][uploadedScheduleId]',  $uploadedSchedule->id); ?>
								                	<?php echo Form::hidden('schedule['.$key.'][employeeId]',  $uploadedSchedule->employee_id); ?>
								                	<?php echo Form::text('schedule['.$key.'][scheduledate]', $uploadedSchedule->schedule_date, array('readonly' => 'readonly')); ?>
								                </td>
								                <!--td>Shift</td-->                
												<td><?php echo Form::select('schedule['.$key.'][shift]', array(1 => 1, 2 => 2), $uploadedSchedule->shift, array()); ?></td>                							                
								                <td><?php echo Form::select('schedule['.$key.'][restday]', array(0 => 'No', 1 => 'Yes'), $uploadedSchedule->rest_day, array()); ?></td>                
								                <!--td>Hours per day</td-->                
								                <td>
								                <?php
								                    echo Form::select(
								                             'schedule['.$key.'][starttimehh]',
								                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10', 11 => '11', 12 => '12',
								                                   13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24'
								                                  ),
								                             $starttimehh,
								                             array()
								                         );
								                ?>
								                
								                <?php
								                    echo Form::select(
								                             'schedule['.$key.'][starttimemm]',
								                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
								                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
								                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
								                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
								                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
								                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
								                                   60 => '60'),
								                             $starttimemm,
								                             array()
								                         );
								                ?>
								                </td>
												<td>							                	
								                	<?php echo Form::text('schedule['.$key.'][startdate]', $startDate, array('class' => 'datepicker')); ?>
								                </td>							                
								                <td>
								                <?php
								                    echo Form::select(
								                             'schedule['.$key.'][endtimehh]',
								                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10', 11 => '11', 12 => '12',
								                                   13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24'
								                                  ),
								                             $endtimehh,
								                             array()
								                         );
								                ?>
								                
								                <?php
								                    echo Form::select(
								                             'schedule['.$key.'][endtimemm]',
								                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
								                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
								                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
								                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
								                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
								                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
								                                   60 => '60'),
								                             $endtimemm,
								                             array()
								                         );
								                ?>
								                </td>                                                
												<td>							                	
								                	<?php echo Form::text('schedule['.$key.'][enddate]', $endDate, array('class' => 'datepicker')); ?>
								                </td>							                
								            </tr>

								            <?php } ?>								            
											
								        </tbody>

								    </table>

								        <div class="form-group pull-right">
								            <div class="col-md-12">
								            {{ Form::submit('Update', array('class' => '', 'class' => 'btn btn-primary')) }}          
								            </div>
								        </div> 

								    {{ Form::close() }}
								            

								<?php } ?>							

								

							</div>


					</div><!--//#uploadNewScheduleUpdateUploadedSchedule-->

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
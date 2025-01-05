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

 //var_dump($getpays);

?>

<!--div class="page-container"-->
        <!--div class="row" style="padding-bottom:20px;"-->

          <div id="content" class="col-md-12" role="main">

            <ol class="breadcrumb hide hidden">
              <li><a href="#">Home</a></li>
              <li class="active">Page</li>
            </ol>

            <h1 class="page-header">Timesheets for Submission</h1>
			<h5><strong>Cutoff Date: </strong><?php echo $startdate . ' to ' . $enddate; ?></h5><br> 	
			<?php	
				if(!empty($message))
				{ ?>
					<div class="alert alert-success" role="alert">
						<ul style="list-style:none; margin:0; padding:0;">
							
							<li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon" aria-hidden="true"></span> <span class="sr-only">Success:</span>{{ $message }}</li>

						</ul>
					</div>
			<?php   } ?>		
				
	        	<div class="table-responsive">
				
					<table class="table table-striped table-hover display table-list" cellspacing="0" width="100%">
						<thead>
							<tr>
								<!--input id="cb-select-all-1" type="checkbox"-->
								<th id="cb" class="manage-column column-cb check-column">{{ Form::checkbox('', '', '', array('id' => 'cb-select-all-1')) }}</th>
								<th>Employee Number</th>	      
								<th>Name</th>	           				           		
				           		<!--th>Email</th-->
								<th>Designation</th>
								<th>Department Head</th>
								<th>Department</th>
				           		<!--th>Cut Off Date</th-->	
							</tr>
						</thead>
						<tbody>
							{{-- Form::open(array('route' => 'submitEmpTimesheet', 'method' => 'post', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) --}}
							
							{{ Form::open(array('url' => '/admin/confirmsubmit', 'method' => 'post', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}
							
							<?php
								if(is_array($getpays) && !empty($getpays))
								{
									//var_dump($getpays);
									foreach($getpays as $gpay)
									{
										if(!is_array($gpay) && !empty($gpay))
										{
											//echo $emp->id; 
											//$user = DB::table('employees')->where('id', $gpay->supervisor_id)->first();
											//var_dump($user);
											
											if($gpay->employee_type == 2)
											{
												//$manager = Employee::where('id', '=', $gpay->supervisorid)->first();
												//$headFullname = $manager->lastname.', '.$manager->firstname;
												$user = DB::table('employees')->where('id', $gpay->manager_id)->first();
												$headFullname = $user->lastname.', '.$user->firstname;
											}
											elseif($gpay->employee_type == 0)
											{
												//$manager = Employee::where('id', '=', $gpay->managerid)->first();
												//$headFullname = $manager->lastname.', '.$manager->firstname;
												$user = DB::table('employees')->where('id', $gpay->supervisor_id)->first();
												$headFullname = $user->lastname.', '.$user->firstname;
												
											}
											elseif($gpay->employee_type == 1)
											{
												//$manager = Employee::where('id', '=', $gEmp->managerid)->first();
												//$user = DB::table('employees')->where('id', $gpay->manager_id)->first();
												//$headFullname = $user->lastname.', '.$user->firstname;
												$headFullname = $gpay->lastname.', '.$gpay->firstname;
											}
														
											
											?>
											<tr>
												<td class="check-column">{{ Form::checkbox('check[]', $gpay->empid, false, array('id' => 'cb-select-' . $gpay->empid, 'class' => 'checkbox')) }}</td>
												<td><?php echo $gpay->employee_number; ?></td>
												<td><?php echo $gpay->lastname . ', ' . $gpay->firstname . ' ' . $gpay->middle_name ; ?></td>
												<!--td><?php //echo $gList->cutoff_starting_date . ' to ' . $gList->cutoff_ending_date; ?></td-->
												<!--td><?php //echo $gpay->email; ?></td-->
												<td><?php echo $gpay->jobtitle; ?></td>
												<td><?php echo $headFullname; ?></td>
												<td><?php echo $gpay->departmentname; ?></td>
												<!--td><?php //echo $getmaxs->cutoff_starting_date . ' to ' . $getmaxs->cutoff_ending_date; ?></td-->
											</tr>
							<?php   	} 
										else
										{
											echo "No payroll data to process yet...";
										}
									}
							?>
									<tr><td colspan="8"></td></tr>
									<input type="hidden" value="<?php echo $gpay->empid ?>" name="empid">
									<input type="hidden" value="<?php echo $employeeId ?>" name="head_ID">
									<input type="hidden" value="<?php echo $startdate ?>" name="schedule_date_from">
									<input type="hidden" value="<?php echo $enddate ?>" name="schedule_date_to">
									<tr>
										<td colspan="8">{{ Form::submit('Submit Timesheets', array('class' => '', 'class' => 'btn btn-primary')) }}</td>
									</tr>
									
					<?php		}	
								else
								{
									echo "<tr><td colspan='5'>No records available.</td></tr>";		
								}
							?>
						</tbody>
						</form>
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
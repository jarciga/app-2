{{-- Child Template --}}
@extends('layouts.admin.sidebarcontent')

@section('head')
	@parent

@stop

@section('sidebar')
	@include('partials.admin.sidebar')
@stop

@section('content')		

  <?php if( ($resourceId === "admin") || 
            ($resourceId === "admin.companies.show.company.new") ||
            ($resourceId === "admin.companies.show.company.edit") ): ?>
    @include('partials.admin.companies')
  <?php endif; ?>

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

//#Jquery UI
$(function() {
  $( ".datepicker" ).datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
  });

});

$(document).ready(function(){

  var isEmployeeType = $('input:radio[name=is_employee_type]:checked').val();  
  //var isEmployeeType = $('input:checkbox[name=is_employee_type]').val();
  //console.log(isEmployeeType);

  if ( isEmployeeType === 'is_manager' ) { //Manager
      
      $('#department-head-new').val( $('#department-head').prop('defaultSelected') );
      $('#supervisor-new').val( $('#supervisor').prop('defaultSelected') );
      $('.department-head-form-group').hide();
      $('.supervisor-form-group').hide();

  } else if ( isEmployeeType === 'is_supervisor' ) { //Supervisor

    $('#department-head-new').val( $('#department-head').prop('defaultSelected') );
    $('#supervisor-new').val( $('#supervisor').prop('defaultSelected') );
    $('.department-head-form-group').show();
    $('.supervisor-form-group').hide();

  } else if ( isEmployeeType === 'is_employee' || isEmployeeType === '0' || isEmployeeType === '' ) { //Employee or empty string

    $('#department-head-new').val( $('#department-head').prop('defaultSelected') );
    $('#supervisor-new').val( $('#supervisor').prop('defaultSelected') );
    $('.department-head-form-group').show();
    $('.supervisor-form-group').show();

  } else {

    $('#department-head-new').val( $('#department-head').prop('defaultSelected') );
    $('#supervisor-new').val( $('#supervisor').prop('defaultSelected') );
    $('.department-head-form-group').show();
    $('.supervisor-form-group').show();

  }     

  $('input:radio[name=is_employee_type]').change(function(){

    var isEmployeeType = $('input:radio[name=is_employee_type]:checked').val();  
    //var isEmployeeType = $('input:checkbox[name=is_employee_type]').val();
    //console.log(isEmployeeType);

    if ( isEmployeeType === 'is_manager' ) { //Manager
        
        $('#department-head-new').val( $('#department-head').prop('defaultSelected') );
        $('#supervisor-new').val( $('#supervisor').prop('defaultSelected') );
        $('.department-head-form-group').hide();
        $('.supervisor-form-group').hide();

    } else if ( isEmployeeType === 'is_supervisor' ) { //Supervisor

      $('#department-head-new').val( $('#department-head').prop('defaultSelected') );
      $('#supervisor-new').val( $('#supervisor').prop('defaultSelected') );
      $('.department-head-form-group').show();
      $('.supervisor-form-group').hide();

    } else if ( isEmployeeType === 'is_employee' || isEmployeeType === '0' || isEmployeeType === '' ) { //Employee or empty string

      $('#department-head-new').val( $('#department-head').prop('defaultSelected') );
      $('#supervisor-new').val( $('#supervisor').prop('defaultSelected') );
      $('.department-head-form-group').show();
      $('.supervisor-form-group').show();

    } else {

      $('#department-head-new').val( $('#department-head').prop('defaultSelected') );
      $('#supervisor-new').val( $('#supervisor').prop('defaultSelected') );
      $('.department-head-form-group').show();
      $('.supervisor-form-group').show();

    }
    
  });

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
        var restDaySpecialcompany = '', restDaySpecialcompanyOt = '', restDayLegalcompany = '', restDayLegalcompanyOt = '';
        var specialcompany = '', specialcompanyOt = '', legalcompany = '', legalcompanyOt = '';

        //With Night Diff

        var regularOtNd = '', regularNd = '', restDayOtNd = '', restDayNd = '';
        var restDaySpecialcompanyOtNd = '', restDaySpecialcompanyNd = '', restDayLegalcompanyOtNd = '', restDayLegalcompanyNd = '';
        var specialcompanyOtNd = '', specialcompanyNd = '', legalcompanyOtNd = '', legalcompanyNd = '';                  


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
        restDaySpecialcompany = obj.data[0].rest_day_special_company;
        restDaySpecialcompanyOt = obj.data[0].rest_day_special_company_overtime;
        restDayLegalcompany = obj.data[0].rest_day_legal_company;
        restDayLegalcompanyOt = obj.data[0].rest_day_legal_company_overtime;

        //With Night Diff
        restDaySpecialcompanyOtNd = obj.data[0].rest_day_special_company_overtime_night_diff;
        restDaySpecialcompanyNd = obj.data[0].rest_day_special_company_night_diff;
        restDayLegalcompanyOtNd = obj.data[0].rest_day_legal_company_overtime_night_diff;
        restDayLegalcompanyNd = obj.data[0].rest_day_legal_company_night_diff;


        //SUMMARY: 4th Column
        specialcompany = obj.data[0].special_company;
        specialcompanyOt = obj.data[0].special_company_overtime;
        legalcompany = obj.data[0].legal_company;
        legalcompanyOt = obj.data[0].legal_company_overtime;

        //With Night Diff
        specialcompanyOtNd = obj.data[0].special_company_overtime_night_diff;
        specialcompanyNd = obj.data[0].special_company_night_diff;
        legalcompanyOtNd = obj.data[0].legal_company_overtime_night_diff;
        legalcompanyNd = obj.data[0].legal_company_night_diff;

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
        if (restDaySpecialcompany !== '0') {

          $('#rd-spl-company').text(restDaySpecialcompany);

        }

        if (restDaySpecialcompanyOt !== '0') {

          $('#rd-spl-company-ot').text(restDaySpecialcompanyOt);

        }

        if (restDayLegalcompany !== '0') {

          $('#rd-legal-company').text(restDayLegalcompany);

        }

        if (restDayLegalcompanyOt !== '0') {

          $('#rd-legal-company-ot').text(restDayLegalcompanyOt);

        }                  

        //With Night Diff 
        if (restDaySpecialcompanyOtNd !== '0') {

          $('#rd-spl-company-ot-nd').text(restDaySpecialcompanyOtNd);

        }

        if (restDaySpecialcompanyNd !== '0') {

          $('#rd-spl-company-nd').text(restDaySpecialcompanyNd);

        }

        if (restDayLegalcompanyOtNd !== '0') {

          $('#rd-legal-company-ot-nd').text(restDayLegalcompanyOtNd);

        }

        if (restDayLegalcompanyNd !== '0') {

          $('#rd-legal-company-nd').text(restDayLegalcompanyNd);

        }                  
                          


        //SUMMARY: 4th Column
        
        if (specialcompany !== '0') {

          $('#spl-company').text(specialcompany);

        }                                                                     

        if (specialcompanyOt !== '0') {

          $('#spl-company-ot').text(specialcompanyOt);

        } 

        if (legalcompany !== '0') {

          $('#legal-company').text(legalcompany);

        } 

        if (legalcompanyOt !== '0') {

          $('#legal-company-ot').text(legalcompanyOt);

        }    

        //With Night Diff
        if (specialcompanyOtNd !== '0') {

          $('#spl-company-ot-nd').text(specialcompanyOtNd);

        }                                                                     

        if (specialcompanyNd !== '0') {

          $('#spl-company-nd').text(specialcompanyNd);

        } 

        if (legalcompanyOtNd !== '0') {

          $('#legal-company-ot-nd').text(legalcompanyOtNd);

        } 

        if (legalcompanyNd !== '0') {

          $('#legal-hoiday-nd').text(legalcompanyNd);

        }                                                                     

  
      }
  },"json");  

}        

</script>	

@stop
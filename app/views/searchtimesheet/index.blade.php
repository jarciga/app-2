{{-- Child Template --}}
@extends('layouts.sidebarcontent')

@section('head')
	@parent
@stop

@section('sidebar')
	@include('partials.searchsidebar')
@stop

@section('content')		
  {{-- @include('partials.clocking') --}}


<div>
  
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#current-timesheet" aria-controls="current-timesheet" role="tab" data-toggle="tab">Current Timesheet</a></li>
    <li role="presentation"><a href="#previous-timesheet" aria-controls="previous-timesheet" role="tab" data-toggle="tab">Previous Timesheet</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="current-timesheet">

      @include('partials.searchtimesheet')
      @include('partials.summary')  

    </div><!--#current-timesheet-->
    <div role="tabpanel" class="tab-pane" id="previous-timesheet">  

      <?php         

        /*$timesheetController = new TimesheetsController();

        $dataArr = $timesheetController->init();
        $dayDateArr = $dataArr["dayDateArr"];
        //$dayDateArr = Session::get('dayDateArr');  

        $d = date('d', strtotime($dayDateArr[0]));

        //'cutoff' => array('cutOffStart' => array(1 => 11, 2 => 26), 'cutOffEnd' => array(1 => 25, 2 => 10))
        //GET THE FIRST INDEX ON THE SESSION DAYDATEARR
        $cutoffStart = Config::get('euler.cutoff.cutOffStart');
        $cutOffEnd = Config::get('euler.cutoff.cutOffEnd');
 
        $dayDateArrCount = count($dayDateArr);               
        //echo $dayDateArr[$dayDateArrCount-1];

        if($cutoffStart[1] === (int) $d) { //If 11 is the first index of the current timesheet then the previous cutoff is 26 to 10

          //echo $cutoffStart[2];
          //echo $cutOffEnd[2];          

        } 

        if($cutoffStart[2] === (int) $d) { //If 26 is the first index of the current timesheet then the previous cutoff is 11 to 25

          //echo $cutoffStart[1];
          //echo $cutOffEnd[1];
          
          $date1 = new DateTime($dayDateArr[0]); //set the first index of the current month;        
          $date1->modify('-'.($dayDateArrCount-1).' day');          
          $cutoffStart['previous'] = $date1->format('Y-m-d');
          
          //echo $cutoffStart['previous'];

          $date2 = new DateTime($dayDateArr[0]); //set the first index of the current month;         
          $date2->modify('-1 day');          
          $cutoffEnd['previous'] = $date2->format('Y-m-d');  

         //echo $cutoffEnd['previous'];        

          $dataArr["previousSearchTimesheets"] = Timesheet::where('employee_id', $employeeSearchId)
                                                  ->whereBetween('daydate', array($cutoffStart['previous'], $cutoffEnd['previous']))->get();

          //dd($dataArr["searchTimesheets"]);

          //foreach($dataArr["searchTimesheets"] as $searchTimesheet) {

            //echo $searchTimesheet->daydate;
            //echo '<br />';

         // }
                              

        } */             

        //, ['dataArr' => $dataArr]
      ?>

      @include('partials.previoustimesheet')
      @include('partials.previoussummary')      


    </div><!--#previous-timesheet-->
  </div>

</div><!--.tab-content-->

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

  $('.dropdown-toggle').dropdown()

    //#Jquery UI Calendar
    $( ".datepicker" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
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

    /*$.ajax({
        type: "GET",
        url : "{{ route('redraw.search.timesheet') }}", //http://localhost:8000/employee",
        data : '',
        success : function(data) {
          
          console.log(data);
          var obj = JSON.parse(data);
          ////console.log(obj);

          $.each(obj.data, function(i, item) {

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

          }); 

        }

    });*/

    getSearchTimesheet();
    getPreviousSearchTimesheet();
    

    /*$("#update-search-timesheet").click(function() {  
      var obj = {};    
      $("#timesheet-ajax tr.row-data").each(function(i) {

        var item = $(this).closest("tr").find("input:text").map(function () {

        });  

        var id = item.context.id;
        var arr = []; 
        //arr.push(id);  

        var in1 = $("#timesheet-ajax tr.row-data td #timesheet-row-in1-"+id).val();
        var in2 = $("#timesheet-ajax tr.row-data td #timesheet-row-in2-"+id).val();
        var in3 = $("#timesheet-ajax tr.row-data td #timesheet-row-in3-"+id).val();
        var out1 = $("#timesheet-ajax tr.row-data td #timesheet-row-out1-"+id).val();
        var out2 = $("#timesheet-ajax tr.row-data td #timesheet-row-out2-"+id).val();
        var out3 = $("#timesheet-ajax tr.row-data td #timesheet-row-out3-"+id).val();

        obj[i] = { in1: in1, in2: in2, in3: in3, out1: out1, out2: out2, out3: out3, id: id };

        //console.log(obj);

        $.ajax({
          type: "POST",
          url: "update.search.timesheet",
          data: obj,
          //contentType: "application/json; charset=utf-8",
          dataType: "json",
          success: function(data) {
            console.log(data);
          }
        });      

      });

    });*/

  });    


  /*$.ajax({                    
    type: "POST",
    url : "",
    data : dataString,
    success : function(data) {

    },
    dataType: dataType    
  });*/




  var method = 'get';
  function getSearchTimesheet(method, button) {
    
    var message;

    $.ajax({
        type: "GET",
        url : "{{ route('redraw.search.timesheet') }}", //http://localhost:8000/employee",
        data : '',
        success : function(data) {

          //console.log(data);

          var obj = JSON.parse(data);
          $.each(obj.data, function(i, item) {

            //console.log(i);

            $("#timesheet-row-in1-date-"+item.id+", #timesheet-row-out1-date-"+item.id
              +", #timesheet-row-in2-date-"+item.id
              +", #timesheet-row-out2-date-"+item.id
              +", #timesheet-row-in3-date-"+item.id
              +", #timesheet-row-out3-date-"+item.id).datetimepicker({

              format: 'MMM-D'

            });  

            $("#timesheet-row-in1-"+item.id+", #timesheet-row-out1-"+item.id
              +", #timesheet-row-in2-"+item.id
              +", #timesheet-row-out2-"+item.id
              +", #timesheet-row-in3-"+item.id
              +", #timesheet-row-out3-"+item.id).datetimepicker({

              format: 'HH:mm'

            });
            
            $(".timesheet-id-"+item.id).text(item.id);
            $(".timesheet-daydate-"+item.id).text(item.daydate);
            $(".timesheet-schedule-"+item.id).text(item.schedule);
      
            $("#timesheet-row-in1-"+item.id).val(item.in_1);
            $("#timesheet-row-out1-"+item.id).val(item.out_1);
            $("#timesheet-row-in2-"+item.id).val(item.in_2);
            $("#timesheet-row-out2-"+item.id).val(item.out_2);
            $("#timesheet-row-in3-"+item.id).val(item.in_3);
            $("#timesheet-row-out3-"+item.id).val(item.out_3);

            $("#timesheet-row-in1-date-"+item.id).val(item.in_1_date);
            $("#timesheet-row-out1-date-"+item.id).val(item.out_1_date);
            $("#timesheet-row-in2-date-"+item.id).val(item.in_2_date);
            $("#timesheet-row-out2-date-"+item.id).val(item.out_2_date);
            $("#timesheet-row-in3-date-"+item.id).val(item.in_3_date);
            $("#timesheet-row-out3-date-"+item.id).val(item.out_3_date);          

            /*$(".timesheet-in1-"+item.id).text(item.in_1);
            $(".timesheet-out1-"+item.id).text(item.out_1);
            $(".timesheet-in2-"+item.id).text(item.in_2);
            $(".timesheet-out2-"+item.id).text(item.out_2);
            $(".timesheet-in3-"+item.id).text(item.in_3);
            $(".timesheet-out3-"+item.id).text(item.out_3);*/
            
            $(".timesheet-nightdifferential-"+item.id).text(item.night_differential);
            $(".timesheet-totalhours-"+item.id).text(item.total_hours);
            $(".timesheet-workhours-"+item.id).text(item.work_hours);
            $(".timesheet-totalovertime-"+item.id).text(item.total_overtime);
            $(".timesheet-tardiness-"+item.id).text(item.tardiness);
            $(".timesheet-undertime-"+item.id).text(item.undertime);
            $(".timesheet-otstatus-"+item.id).html(item.overtime_status);
            
            getSearchSummary();

          }); 
    

        }

      },"json");
  }

  function getPreviousSearchTimesheet(method, button) {
    
    var message;

    $.ajax({
        type: "GET",
        url : "{{ route('redraw.previous.timesheet') }}", //http://localhost:8000/employee",
        data : '',
        success : function(data) {

          //console.log(data);

          var obj = JSON.parse(data);
          $.each(obj.data, function(i, item) {

            //console.log(i);

            $("#timesheet-previous-row-in1-date-"+item.id+", #timesheet-previous-row-out1-date-"+item.id
              +", #timesheet-previous-row-in2-date-"+item.id
              +", #timesheet-previous-row-out2-date-"+item.id
              +", #timesheet-previous-row-in3-date-"+item.id
              +", #timesheet-previous-row-out3-date-"+item.id).datetimepicker({

              format: 'MMM-D'

            });  

            $("#timesheet-previous-row-in1-"+item.id+", #timesheet-previous-row-out1-"+item.id
              +", #timesheet-previous-row-in2-"+item.id
              +", #timesheet-previous-row-out2-"+item.id
              +", #timesheet-previous-row-in3-"+item.id
              +", #timesheet-previous-row-out3-"+item.id).datetimepicker({

              format: 'HH:mm'

            });
            
            $(".timesheet-previous-id-"+item.id).text(item.id);
            $(".timesheet-previous-daydate-"+item.id).text(item.daydate);
            $(".timesheet-previous-schedule-"+item.id).text(item.schedule);
      
            $("#timesheet-previous-row-in1-"+item.id).val(item.in_1);
            $("#timesheet-previous-row-out1-"+item.id).val(item.out_1);
            $("#timesheet-previous-row-in2-"+item.id).val(item.in_2);
            $("#timesheet-previous-row-out2-"+item.id).val(item.out_2);
            $("#timesheet-previous-row-in3-"+item.id).val(item.in_3);
            $("#timesheet-previous-row-out3-"+item.id).val(item.out_3);

            $("#timesheet-previous-row-in1-date-"+item.id).val(item.in_1_date);
            $("#timesheet-previous-row-out1-date-"+item.id).val(item.out_1_date);
            $("#timesheet-previous-row-in2-date-"+item.id).val(item.in_2_date);
            $("#timesheet-previous-row-out2-date-"+item.id).val(item.out_2_date);
            $("#timesheet-previous-row-in3-date-"+item.id).val(item.in_3_date);
            $("#timesheet-previous-row-out3-date-"+item.id).val(item.out_3_date);          

            /*$(".timesheet-previous-in1-"+item.id).text(item.in_1);
            $(".timesheet-previous-out1-"+item.id).text(item.out_1);
            $(".timesheet-previous-in2-"+item.id).text(item.in_2);
            $(".timesheet-previous-out2-"+item.id).text(item.out_2);
            $(".timesheet-previous-in3-"+item.id).text(item.in_3);
            $(".timesheet-previous-out3-"+item.id).text(item.out_3);*/
            
            $(".timesheet-previous-nightdifferential-"+item.id).text(item.night_differential);
            $(".timesheet-previous-totalhours-"+item.id).text(item.total_hours);
            $(".timesheet-previous-workhours-"+item.id).text(item.work_hours);
            $(".timesheet-previous-totalovertime-"+item.id).text(item.total_overtime);
            $(".timesheet-previous-tardiness-"+item.id).text(item.tardiness);
            $(".timesheet-previous-undertime-"+item.id).text(item.undertime);
            $(".timesheet-previous-otstatus-"+item.id).html(item.overtime_status);
            
            getPreviousSearchSummary();

          }); 
    

        }

      },"json");
  }

  function getSearchSummary() {

    //Summary Computation Init
    $.ajax({
        type: "GET",
        url : "{{ route('redraw.search.summary') }}", //http://localhost:8000/employee",
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

  function getPreviousSearchSummary() {

    //Summary Computation Init
    $.ajax({
        type: "GET",
        url : "{{ route('redraw.previous.summary') }}", //http://localhost:8000/employee",
        data : '',
        success : function(data) {
          console.log(data);
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

            $('#previous-lates-ut').text(lates);

          } 

          if (undertime !== '0') {

            $('#previous-lates-ut').text(undertime);

          }    

          if (lates !== '0' && undertime !== '0') {

            $('#previous-lates-ut').text(lates + ' / ' + undertime);

          }

          if (absences !== '0') {

            $('#previous-absences').text(absences);

          }

          if (paidVacationLeave !== '0') {

            $('#previous-paid-sl').text(paidSickLeave);

          }

          if (paidSickLeave !== '0') {

            $('#previous-paid-vl').text(paidVacationLeave);

          }

          if (leaveWithoutPay !== '0') {

            $('#previous-leave-without-pay').text(leaveWithoutPay);

          }

          if (maternityLeave !== '0') {

            $('#previous-maternity-leave').text(maternityLeave);

          }

          if (paternityLeave !== '0') {

            $('#previous-paternity-leave').text(paternityLeave);

          }                                                                        


          //SUMMARY: 2nd Column
          if (regular !== '0') {

            $('#previous-regular').text(regular);

          }

          if (regularOt !== '0') {

            $('#previous-reg-ot').text(regularOt);

          }

          if (restDay !== '0') {

            $('#previous-rd').text(restDay);

          }                  

          if (restDayOt !== '0') {

            $('#previous-rd-ot').text(restDayOt);

          }

          //With Night Diff
          if (regularOtNd !== '0') {

            $('#previous-reg-ot-nd').text(regularOtNd);

          }

          if (regularNd !== '0') {

            $('#previous-reg-nd').text(regularNd);

          }                                    

          if (restDayOtNd !== '0') {

            $('#previous-rd-ot-nd').text(restDayOtNd);

          }                  

          if (restDayNd !== '0') {

            $('#previous-rd-nd').text(restDayNd);

          }


          //SUMMARY: 3rd Column 
          if (restDaySpecialHoliday !== '0') {

            $('#previous-rd-spl-holiday').text(restDaySpecialHoliday);

          }

          if (restDaySpecialHolidayOt !== '0') {

            $('#previous-rd-spl-holiday-ot').text(restDaySpecialHolidayOt);

          }

          if (restDayLegalHoliday !== '0') {

            $('#previous-rd-legal-holiday').text(restDayLegalHoliday);

          }

          if (restDayLegalHolidayOt !== '0') {

            $('#previous-rd-legal-holiday-ot').text(restDayLegalHolidayOt);

          }                  

          //With Night Diff 
          if (restDaySpecialHolidayOtNd !== '0') {

            $('#previous-rd-spl-holiday-ot-nd').text(restDaySpecialHolidayOtNd);

          }

          if (restDaySpecialHolidayNd !== '0') {

            $('#previous-rd-spl-holiday-nd').text(restDaySpecialHolidayNd);

          }

          if (restDayLegalHolidayOtNd !== '0') {

            $('#previous-rd-legal-holiday-ot-nd').text(restDayLegalHolidayOtNd);

          }

          if (restDayLegalHolidayNd !== '0') {

            $('#previous-rd-legal-holiday-nd').text(restDayLegalHolidayNd);

          }                  
                            


          //SUMMARY: 4th Column
          
          if (specialHoliday !== '0') {

            $('#previous-spl-holiday').text(specialHoliday);

          }                                                                     

          if (specialHolidayOt !== '0') {

            $('#previous-spl-holiday-ot').text(specialHolidayOt);

          } 

          if (legalHoliday !== '0') {

            $('#previous-legal-holiday').text(legalHoliday);

          } 

          if (legalHolidayOt !== '0') {

            $('#previous-legal-holiday-ot').text(legalHolidayOt);

          }    

          //With Night Diff
          if (specialHolidayOtNd !== '0') {

            $('#previous-spl-holiday-ot-nd').text(specialHolidayOtNd);

          }                                                                     

          if (specialHolidayNd !== '0') {

            $('#previous-spl-holiday-nd').text(specialHolidayNd);

          } 

          if (legalHolidayOtNd !== '0') {

            $('#previous-legal-holiday-ot-nd').text(legalHolidayOtNd);

          } 

          if (legalHolidayNd !== '0') {

            $('#previous-legal-hoiday-nd').text(legalHolidayNd);

          }                                                                     

    
        }
    },"json");  

  } 


</script>	

@stop
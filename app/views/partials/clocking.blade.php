<div class="row">  
<div class="col-md-3">
    <p id="clock" style="margin:0 0 0 0; padding:0 0 0 0; text-align:left; line-height:1; font-size:65px; font-weight:normal;">--:--</p>
    <p style="margin:0 0 0 0; padding:0 0 0 0; text-align:left; line-height:1; font-size:16px; font-weight:normal;"><?php //echo date('l, F d\t\h Y'); ?></p>
  </div>

    <div class="col-md-3 hide hidden">
    <!--form method="POST" action="http://www.backofficephhosting.com/timesheet/employee/clocking" accept-charset="UTF-8" =""="" id="timeClockingFormIn"><input name="_token" type="hidden" value="g5FZKjwb8hKhVUYBYpmlz7UsxskO7k6kxVWqpFYU">        
               
    <input id="employee-number" name="employeenumber" type="hidden" value="<?php //echo $employee->employee_number; ?>">              

    <input id="day-date" name="daydate" type="hidden" value="<?php //echo $timesheet->daydate; ?>">             

    <input id="sched-in" name="schedin" type="hidden" value="<?php //echo strtotime($schedule->start_time); ?>">             
    <input id="sched-out" name="schedout" type="hidden" value="<?php //echo strtotime($schedule->end_time); ?>">                 

    <input id="time-now" name="timenow" type="hidden" value="<?php //echo strtotime($currentDateTime); ?>">             

    <input id="time-in" name="timein" type="hidden" value="<?php //echo $currentDateTime; ?>">

    <input id="time-in-1" name="timein1" type="hidden" value="<?php //echo $currentDateTime; ?>">

    <input id="time-in-2" name="timein2" type="hidden" value="<?php //echo $currentDateTime; ?>">

    <input id="time-in-3" name="timein3" type="hidden" value="<?php //echo $currentDateTime; ?>">

                                                                                                                                                  
    <button id="time-clocking-btn" class="time-in btn btn-custom-default" style="font-size:36px; font-weight:normal" type="button">Time In</button>                                                                                          
    </form>    

    <form method="POST" action="http://www.backofficephhosting.com/timesheet/employee/clocking" accept-charset="UTF-8" =""="" id="timeClockingFormOut"><input name="_token" type="hidden" value="g5FZKjwb8hKhVUYBYpmlz7UsxskO7k6kxVWqpFYU">        
                                     
    <input id="employee-number" name="employeenumber" type="hidden" value="<?php //echo $employee->employee_number; ?>">              

    <input id="day-date" name="daydate" type="hidden" value="<?php //echo $timesheet->daydate; ?>">    

    <input id="sched-in" name="schedin" type="hidden" value="<?php //echo strtotime($schedule->start_time); ?>">             
    <input id="sched-out" name="schedout" type="hidden" value="<?php //echo strtotime($schedule->end_time); ?>">

    <input id="time-now" name="timenow" type="hidden" value="<?php //echo strtotime($currentDateTime); ?>">             

    <input id="time-out" name="timeout" type="hidden" value="<?php //echo $currentDateTime; ?>">

    <input id="time-out-1" name="timeout1" type="hidden" value="<?php //echo $currentDateTime; ?>">

    <input id="time-out-2" name="timeout2" type="hidden" value="<?php //echo $currentDateTime; ?>">

    <input id="time-out-3" name="timeout3" type="hidden" value="<?php //echo $currentDateTime; ?>">
                                                                                                                                                
    <button id="time-clocking-btn" class="time-out btn btn-custom-default" style="font-size:36px; font-weight:normal" type="button">Time Out</button>                                                                                                                                                     
    </form-->        
    </div>

    <div class="col-md-3">
    <form method="POST" action="/timesheet" accept-charset="UTF-8" id="timeClockingFormInOut"><input name="_token" type="hidden" value="g5FZKjwb8hKhVUYBYpmlz7UsxskO7k6kxVWqpFYU">        

    <input id="employee-number" name="employeenumber" type="hidden" value="<?php echo $employee->employee_number; ?>">              
    <input id="day-date" name="daydate" type="hidden" value="<?php echo $timesheet->daydate; ?>">             
    <input id="sched-in" name="schedin" type="hidden" value="<?php echo strtotime($schedule->start_time); ?>">             
    <input id="sched-out" name="schedout" type="hidden" value="<?php echo strtotime($schedule->end_time); ?>">                 

        <input id="time-now" name="timenow" type="hidden" value="<?php echo strtotime($currentDateTime); ?>">             
        <input id="time-in" name="timein" type="hidden" value="<?php echo $currentDateTime; ?>">
        <input id="time-in-1" name="timein1" type="hidden" value="<?php echo $currentDateTime; ?>">
        <input id="time-in-2" name="timein2" type="hidden" value="<?php echo $currentDateTime; ?>">
        <input id="time-in-3" name="timein3" type="hidden" value="<?php echo $currentDateTime; ?>">

        <input id="time-out" name="timeout" type="hidden" value="<?php echo $currentDateTime; ?>">
        <input id="time-out-1" name="timeout1" type="hidden" value="<?php echo $currentDateTime; ?>">
        <input id="time-out-2" name="timeout2" type="hidden" value="<?php echo $currentDateTime; ?>">
        <input id="time-out-3" name="timeout3" type="hidden" value="<?php echo $currentDateTime; ?>">              
       
    <?php 
    //BUTTON: TIME IN & BUTTON: TIME OUT

    if( empty($timesheetYesterday->clocking_status) ||
        $timesheetYesterday->clocking_status === "open" ||
        $timesheetYesterday->clocking_status === "clock_out_1" ||
        $timesheetYesterday->clocking_status === "clock_out_2" ||
        $timesheetYesterday->clocking_status === "clock_out_3" ) {  

        if ( $timesheet->clocking_status === "open" ) {

    ?>
        {{--@include('partials.timeinbutton')--}}

        <button id="time-clocking-btn" class="time-in btn btn-custom-default" style="font-size:36px; font-weight:normal" type="button">Time In</button>
        <button id="time-clocking-btn" class="time-out btn btn-custom-default hide" style="font-size:36px; font-weight:normal" type="button">Time Out</button>

    <?php } elseif ( $timesheet->clocking_status === "clock_in_1" ||
                     $timesheet->clocking_status === "clock_in_2" ||
                     $timesheet->clocking_status === "clock_in_3" ) { ?>
        
        {{--@include('partials.timeoutbutton')--}}


        <button id="time-clocking-btn" class="time-in btn btn-custom-default hide" style="font-size:36px; font-weight:normal" type="button">Time In</button>
        <button id="time-clocking-btn" class="time-out btn btn-custom-default" style="font-size:36px; font-weight:normal" type="button">Time Out</button>

    <?php } elseif ( $timesheet->clocking_status === "clock_out_1" ||
                     $timesheet->clocking_status === "clock_out_2" ||
                     $timesheet->clocking_status === "clock_out_3" ) { ?>
        
        @include('partials.timeinbutton')

        <button id="time-clocking-btn" class="time-in btn btn-custom-default" style="font-size:36px; font-weight:normal" type="button">Time In</button>
        <button id="time-clocking-btn" class="time-out btn btn-custom-default hide" style="font-size:36px; font-weight:normal" type="button">Time Out</button>
    <?php } ?>
    
    <?php if ( $timesheet->clocking_status === "clock_out_1" &&
                     $timesheet->clocking_status === "clock_out_2" &&
                     $timesheet->clocking_status === "clock_out_3" ) { ?>
        
        @include('partials.timeinbutton')

        <button id="time-clocking-btn" class="time-in btn btn-custom-default disable" style="font-size:36px; font-weight:normal" type="button">Time In</button>
        <!--button id="time-clocking-btn" class="time-out btn btn-custom-default hide" style="font-size:36px; font-weight:normal" type="button">Time Out</button-->

    <?php } ?>        

    <?php } elseif ( $timesheetYesterday->clocking_status === "clock_in_1" ||
        $timesheetYesterday->clocking_status === "clock_in_2" ||
        $timesheetYesterday->clocking_status === "clock_in_3" ) {  

        if ( $timesheet->clocking_status === "open" ) {

    ?>
        {{--@include('partials.timeoutbutton')--}}

        <button id="time-clocking-btn" class="time-in btn btn-custom-default hide" style="font-size:36px; font-weight:normal" type="button">Time In</button>
        <button id="time-clocking-btn" class="time-out btn btn-custom-default" style="font-size:36px; font-weight:normal" type="button">Time Out</button>

    <?php } ?>

    <?php } ?>    
   

    </div>  


</div>
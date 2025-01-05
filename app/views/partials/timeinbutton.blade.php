    <!--form method="POST" action="/timesheet" accept-charset="UTF-8" id="timeClockingFormIn"><input name="_token" type="hidden" value="g5FZKjwb8hKhVUYBYpmlz7UsxskO7k6kxVWqpFYU"-->        
    <!--button id="time-in" class="btn btn-primary btn-lg" role="button">Time In</button--> 
                                            
    <input id="employee-number" name="employeenumber" type="hidden" value="<?php echo $employee->employee_number; ?>">              

    <input id="day-date" name="daydate" type="hidden" value="<?php echo $timesheet->daydate; ?>">             

    <input id="sched-in" name="schedin" type="hidden" value="<?php echo strtotime($schedule->start_time); ?>">             
    <input id="sched-out" name="schedout" type="hidden" value="<?php echo strtotime($schedule->end_time); ?>">                 

    <input id="time-now" name="timenow" type="hidden" value="<?php echo strtotime($currentDateTime); ?>">             

    <input id="time-in" name="timein" type="hidden" value="<?php echo $currentDateTime; ?>">

    <input id="time-in-1" name="timein1" type="hidden" value="<?php echo $currentDateTime; ?>">

    <input id="time-in-2" name="timein2" type="hidden" value="<?php echo $currentDateTime; ?>">

    <input id="time-in-3" name="timein3" type="hidden" value="<?php echo $currentDateTime; ?>">

    <!--input id="forgot-yesterday-timeout" name="forgotyesterdaytimeout" type="hidden" value=""-->                                                                                                                                                    
    <button id="time-clocking-btn" class="hide time-in btn btn-custom-default" style="font-size:36px; font-weight:normal" type="button">Time In</button>                                                                                          
    <!--/form-->
<?php

class Timesheet extends \Eloquent {
	protected $fillable = [];
	protected $table = 'employee_timesheet';


    public function timesheetJson($employeeId, $dayDateArr) { 
        

        $currentDate = Config::get('euler.current_date');

        //foreach($dayDateArr as $dayDate) {}

		$timesheet = Timesheet::where('employee_id', Session::get('currentUserId'))
				  			  ->whereIn('daydate', $dayDateArr)
				  			  ->get();        

		//return dd($timesheet);

		$output = '';
		$output = '{';
        $output .= '"data": [';
        $ctr = 1;
        
        foreach ( $timesheet as $timesheetVal )
        {

        	////Session::put('dayDate', $dayDate);

 			$output .= '{';
           
        	//$output .= '"DT_RowId": '. '"'.$timesheetVal->id.'",';
        	$output .= '"id": '. '"'.$timesheetVal->id.'",';


            //Date
            $output .= '"daydate": '. '"'.date('D, M d', strtotime($timesheetVal->daydate)).'",';


           //Schedule
            if ( !empty($timesheetVal->schedule_in) && !empty($timesheetVal->schedule_out) ) {
                $output .= '"schedule": '. '"'.date('H:i', strtotime($timesheetVal->schedule_in)). ' - ' .date('H:i', strtotime($timesheetVal->schedule_out)).'",';             
            } else {
                $output .= '"schedule": '. '"'.'00:00:00 - 00:00:00'.'",';                             
            }


            //in-out 1
            if ( !empty($timesheetVal->time_in_1) ) {

                $output .= '"in_1": '. '"'.date('H:i', strtotime($timesheetVal->time_in_1)).'",';                            
                $output .= '"in_1_date": '. '"'.date('M-d', strtotime($timesheetVal->time_in_1)).'",';                            
                
            } else {

                $output .= '"in_1": '. '"'. ' --:-- ' .'",';

            }

            if ( !empty($timesheetVal->time_out_1) ) {

                $output .= '"out_1": '. '"'.date('H:i', strtotime($timesheetVal->time_out_1)).'",';                            
                $output .= '"out_1_date": '. '"'.date('M-d', strtotime($timesheetVal->time_out_1)).'",';                            
                
            } else {

                $output .= '"out_1": '. '"'. ' --:-- ' .'",';

            }        

            if ( !empty($timesheetVal->time_in_2) ) {

                $output .= '"in_2": '. '"'.date('H:i', strtotime($timesheetVal->time_in_2)).'",';                            
                $output .= '"in_2_date": '. '"'.date('M-d', strtotime($timesheetVal->time_in_2)).'",';                            
                
            } else {

                $output .= '"in_2": '. '"'. ' --:-- ' .'",';

            }

            if ( !empty($timesheetVal->time_out_2) ) {

                $output .= '"out_2": '. '"'.date('H:i', strtotime($timesheetVal->time_out_2)).'",';                            
                $output .= '"out_2_date": '. '"'.date('M-d', strtotime($timesheetVal->time_out_2)).'",';

                
            } else {

                $output .= '"out_2": '. '"'. ' --:-- ' .'",';

            }                      

            //in-out 3
            if ( !empty($timesheetVal->time_in_3) ) {

                $output .= '"in_3": '. '"'.date('H:i', strtotime($timesheetVal->time_in_3)).'",';                            
                $output .= '"in_3_date": '. '"'.date('M-d', strtotime($timesheetVal->time_in_3)).'",';                            
                
            } else {

                $output .= '"in_3": '. '"'. ' --:-- ' .'",';

            }

            if ( !empty($timesheetVal->time_out_3) ) {

                $output .= '"out_3": '. '"'.date('H:i', strtotime($timesheetVal->time_out_3)).'",';                            
                $output .= '"out_3_date": '. '"'.date('M-d', strtotime($timesheetVal->time_out_3)).'",';                            
                
            } else {

                $output .= '"out_3": '. '"'. ' --:-- ' .'",';

            }


            //Total Hours           
            if ( !empty($timesheetVal->total_hours_1) && !empty($timesheetVal->total_hours_2) ) {                

                $output .= '"total_hours": '. '"'. getTotal($timesheetVal->total_hours_1, $timesheetVal->total_hours_2).'",';

            } elseif ( !empty($timesheetVal->total_hours_1) && !empty($timesheetVal->total_hours_2) && !empty($timesheetVal->total_hours_3) ) {                

                //$output .= '"total_hours": '. '"'. getTotal($timesheetVal->total_hours_1, $timesheetVal->total_hours_3).'",';                
                $output .= '"total_hours": '. '"'. getTotal($timesheetVal->total_hours_1, $timesheetVal->total_hours_2, $timesheetVal->total_hours_3).'",';                                

            } elseif ( !empty($timesheetVal->total_hours_1) ) {

                $output .= '"total_hours": '. '"'.number_format((double) $timesheetVal->total_hours_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->total_hours_2) ) {

                $output .= '"total_hours": '. '"'.number_format((double) $timesheetVal->total_hours_2, 2, '.', '').'",';                

            } else {

                $output .= '"total_hours": '. '"'. ' - ' .'",';

            }

            //Work Hours
            if ( !empty($timesheetVal->work_hours_1) && !empty($timesheetVal->work_hours_2) ) {                

                $output .= '"work_hours": '. '"'. getTotal($timesheetVal->work_hours_1, $timesheetVal->work_hours_2).'",';

            } elseif ( !empty($timesheetVal->work_hours_1) && !empty($timesheetVal->work_hours_2) && !empty($timesheetVal->work_hours_3) ) {                

                //$output .= '"work_hours": '. '"'. getTotal($timesheetVal->work_hours_1, $timesheetVal->work_hours_3).'",';                
                $output .= '"work_hours": '. '"'. getTotal($timesheetVal->work_hours_1, $timesheetVal->work_hours_2, $timesheetVal->work_hours_3).'",';                                

            } elseif ( !empty($timesheetVal->work_hours_1) ) {

                $output .= '"work_hours": '. '"'.number_format((double) $timesheetVal->work_hours_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->work_hours_2) ) {

                $output .= '"work_hours": '. '"'.number_format((double) $timesheetVal->work_hours_2, 2, '.', '').'",';                

            } else {

                $output .= '"work_hours": '. '"'. ' - ' .'",';

            }

            //Total Overtime
            if ( !empty($timesheetVal->total_overtime_1) && !empty($timesheetVal->total_overtime_2) ) {                

                $output .= '"total_overtime": '. '"'. getTotal($timesheetVal->total_overtime_1, $timesheetVal->total_overtime_2).'",';

            } elseif ( !empty($timesheetVal->total_overtime_1) && !empty($timesheetVal->total_overtime_2) && !empty($timesheetVal->total_overtime_3) ) {                

                //$output .= '"total_overtime": '. '"'. getTotal($timesheetVal->total_overtime_1, $timesheetVal->total_overtime_3).'",';                
                $output .= '"total_overtime": '. '"'. getTotal($timesheetVal->total_overtime_1, $timesheetVal->total_overtime_2, $timesheetVal->total_overtime_3).'",';                

            } elseif ( !empty($timesheetVal->total_overtime_1) ) {

                $output .= '"total_overtime": '. '"'.number_format((double) $timesheetVal->total_overtime_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->total_overtime_2) ) {

                $output .= '"total_overtime": '. '"'.number_format((double) $timesheetVal->total_overtime_2, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->total_overtime_3) ) {

                $output .= '"total_overtime": '. '"'.number_format((double) $timesheetVal->total_overtime_3, 2, '.', '').'",';                

            } else {

                $output .= '"total_overtime": '. '"'. ' - ' .'",';

            }

            //Night Differential          
            if ( !empty($timesheetVal->night_differential_1) && !empty($timesheetVal->night_differential_2) ) {                

                $output .= '"night_differential": '. '"'. getTotal($timesheetVal->night_differential_1, $timesheetVal->night_differential_2).'",';

            } elseif (!empty($timesheetVal->night_differential_1) && !empty($timesheetVal->night_differential_2) && !empty($timesheetVal->night_differential_3) ) {                

                //$output .= '"night_differential": '. '"'. getTotal($timesheetVal->night_differential_1, $timesheetVal->night_differential_3).'",';                
                $output .= '"night_differential": '. '"'. getTotal($timesheetVal->night_differential_1, $timesheetVal->night_differential_2, $timesheetVal->night_differential_3).'",';                                

            } elseif ( !empty($timesheetVal->night_differential_1) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timesheetVal->night_differential_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->night_differential_2) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timesheetVal->night_differential_2, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->night_differential_3) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timesheetVal->night_differential_3, 2, '.', '').'",';                                

            } else {

                $output .= '"night_differential": '. '"'. ' - ' .'",';

            }            


            //Tardiness
            if ( !empty($timesheetVal->tardiness_1) && !empty($timesheetVal->tardiness_2) ) {                

                $output .= '"tardiness": '. '"'. getTotal($timesheetVal->tardiness_1, $timesheetVal->tardiness_2).'",';

            } elseif ( !empty($timesheetVal->tardiness_1) && !empty($timesheetVal->tardiness_2) && !empty($timesheetVal->tardiness_3) ) {                

                //$output .= '"tardiness": '. '"'. getTotal($timesheetVal->tardiness_1, $timesheetVal->tardiness_3).'",';                
                $output .= '"tardiness": '. '"'. getTotal($timesheetVal->tardiness_1, $timesheetVal->tardiness_2, $timesheetVal->tardiness_3).'",';                                

            } elseif ( !empty($timesheetVal->tardiness_1) ) {

                $output .= '"tardiness": '. '"'.number_format((double) $timesheetVal->tardiness_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->tardiness_2) ) {

                $output .= '"tardiness": '. '"'.number_format((double) $timesheetVal->tardiness_2, 2, '.', '').'",';                

            } else {

                $output .= '"tardiness": '. '"'. ' - ' .'",';

            }

            //Undertime
            if ( !empty($timesheetVal->undertime_1) && !empty($timesheetVal->undertime_3) ) {                

                $output .= '"undertime": '. '"'. getTotal($timesheetVal->undertime_1, $timesheetVal->undertime_1).'",';

            } elseif ( !empty($timesheetVal->undertime_1) && !empty($timesheetVal->undertime_2) && !empty($timesheetVal->undertime_3) ) {                

                //$output .= '"undertime": '. '"'. getTotal($timesheetVal->undertime_1, $timesheetVal->undertime_3).'",';                
                $output .= '"undertime": '. '"'. getTotal($timesheetVal->undertime_1, $timesheetVal->undertime_2, $timesheetVal->undertime_3).'",';                                

            } elseif ( !empty($timesheetVal->undertime_1) ) {

                $output .= '"undertime": '. '"'.number_format((double) $timesheetVal->undertime_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->undertime_2) ) {

                $output .= '"undertime": '. '"'.number_format((double) $timesheetVal->undertime_2, 2, '.', '').'",';                

            } else {

                $output .= '"undertime": '. '"'. ' - ' .'",';

            } 

            //Overtime Status
            if( (!empty($timesheetVal->total_overtime_1) && 
                is_null($timesheetVal->overtime_status_1)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                is_null($timesheetVal->overtime_status_2)) ||

                (!empty($timesheetVal->total_overtime_1) && 
                is_null($timesheetVal->overtime_status_1)) && 

                (!empty($timesheetVal->total_overtime_3) && 
                is_null($timesheetVal->overtime_status_3)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                is_null($timesheetVal->overtime_status_2)) && 

                (!empty($timesheetVal->total_overtime_3) && 
                is_null($timesheetVal->overtime_status_3)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'",'; 

            } elseif ( (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === -1)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === -1)) ||            

                (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === -1)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === -1)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === -1)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === -1)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Applied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'",'; 

            } elseif ( (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === 0)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === 0)) ||            

                (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === 0)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === 0)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === 0)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === 0)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Denied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'",';                 

            } elseif ( (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === 1)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === 1)) ||            

                (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === 1)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === 1)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === 1)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === 1)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Approved OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'",';                 

            } else {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'",';

            } 

            //CHECK CLOCKING STATUS: CLOCK_OUT_2                                    
            if ( $timesheetVal->clocking_status === "clock_out_3" && $timesheetVal->daydate === $currentDate) {

                $messageIn = 'Your are already in.';                    
                $messageOut = 'Your are already Out.';                    
                $output .= '"message_in": '. '"'.$messageIn.'",';
                $output .= '"message_out": '. '"'.$messageOut.'"';
                
            } else {

                $messageIn = '';                    
                $messageOut = '';                    
                $output .= '"message_in": '. '"'.$messageIn.'",';
                $output .= '"message_out": '. '"'. $messageOut.'"';

            }


	        if ( $ctr == sizeof($timesheet) ) {
	            $output .= '}';
	        } else {
	            $output .= '},';
	        }

	        $ctr++;            		

        }

		$output .= ']';
        $output .= '}'; 

        return (string) $output; //json_encode($timeSheetObj);
    }	


    public function searchTimesheetJson($employeeSearchId, $dayDateArr) { 
        

        $currentDate = Config::get('euler.current_date');

        //foreach($dayDateArr as $dayDate) {}

        $timesheet = Timesheet::where('employee_id', Session::get('employeeSearchId'))
                              ->whereIn('daydate', $dayDateArr)
                              ->get();        

        //return dd($timesheet);

        $output = '';
        $output = '{';
        $output .= '"data": [';
        $ctr = 1;
        
        foreach ( $timesheet as $timesheetVal )
        {

            ////Session::put('dayDate', $dayDate);

            $output .= '{';
           
            //$output .= '"DT_RowId": '. '"'.$timesheetVal->id.'",';
            $output .= '"id": '. '"'.$timesheetVal->id.'",';


            //Date
            $output .= '"daydate": '. '"'.date('D, M d', strtotime($timesheetVal->daydate)).'",';


           //Schedule
            if ( !empty($timesheetVal->schedule_in) && !empty($timesheetVal->schedule_out) ) {
                $output .= '"schedule": '. '"'.date('H:i', strtotime($timesheetVal->schedule_in)). ' - ' .date('H:i', strtotime($timesheetVal->schedule_out)).'",';             
            } else {
                $output .= '"schedule": '. '"'.'00:00:00 - 00:00:00'.'",';                             
            }


            //in-out 1
            if ( !empty($timesheetVal->time_in_1) ) {

                $output .= '"in_1": '. '"'.date('H:i', strtotime($timesheetVal->time_in_1)).'",';                            
                $output .= '"in_1_date": '. '"'.date('M-d', strtotime($timesheetVal->time_in_1)).'",';                            
                
            } else {

                $output .= '"in_1": '. '"'.''.'",';
                $output .= '"in_1_date": '. '"'.''.'",';                                            

            }

            if ( !empty($timesheetVal->time_out_1) ) {

                $output .= '"out_1": '. '"'.date('H:i', strtotime($timesheetVal->time_out_1)).'",';                            
                $output .= '"out_1_date": '. '"'.date('M-d', strtotime($timesheetVal->time_out_1)).'",';                            
                
            } else {

                $output .= '"out_1": '. '"'.''.'",';
                $output .= '"out_1_date": '. '"'.''.'",';                                            

            }        

            if ( !empty($timesheetVal->time_in_2) ) {

                $output .= '"in_2": '. '"'.date('H:i', strtotime($timesheetVal->time_in_2)).'",';                            
                $output .= '"in_2_date": '. '"'.date('M-d', strtotime($timesheetVal->time_in_2)).'",';                            
                
            } else {

                $output .= '"in_2": '. '"'.''.'",';
                $output .= '"in_2_date": '. '"'.''.'",';                                                            

            }

            if ( !empty($timesheetVal->time_out_2) ) {

                $output .= '"out_2": '. '"'.date('H:i', strtotime($timesheetVal->time_out_2)).'",';                            
                $output .= '"out_2_date": '. '"'.date('M-d', strtotime($timesheetVal->time_out_2)).'",';

                
            } else {

                $output .= '"out_2": '. '"'.''.'",';
                $output .= '"out_2_date": '. '"'.''.'",';                                            

            }                      

            //in-out 3
            if ( !empty($timesheetVal->time_in_3) ) {

                $output .= '"in_3": '. '"'.date('H:i', strtotime($timesheetVal->time_in_3)).'",';                            
                $output .= '"in_3_date": '. '"'.date('H:i', strtotime($timesheetVal->time_in_3)).'",';                            
                
            } else {

                $output .= '"in_3": '. '"'.''.'",';
                $output .= '"in_3_date": '. '"'.''.'",';                                            

            }

            if ( !empty($timesheetVal->time_out_3) ) {

                $output .= '"out_3": '. '"'.date('H:i', strtotime($timesheetVal->time_out_3)).'",';                            
                $output .= '"out_3_date": '. '"'.date('Y-m-d', strtotime($timesheetVal->time_out_3)).'",';                            
                
            } else {

                $output .= '"out_3": '. '"'.''.'",';
                $output .= '"out_1_date": '. '"'.''.'",';                                            

            }


            //Total Hours 
            $totalHoursTotal = (double) getTotal($timesheetVal->total_hours_1, $timesheetVal->total_hours_2, $timesheetVal->total_hours_3);
            
            if( $totalHoursTotal ) {

                $output .= '"total_hours": '. '"'. number_format($totalHoursTotal, 2, '.', '').'",';

            } else {

                $output .= '"total_hours": '. '"'. ' - ' .'",';

            }

            /*if ( !empty($timesheetVal->total_hours_1) && !empty($timesheetVal->total_hours_2) ) {                

                $output .= '"total_hours": '. '"'. getTotal($timesheetVal->total_hours_1, $timesheetVal->total_hours_2).'",';

            } elseif ( !empty($timesheetVal->total_hours_1) && !empty($timesheetVal->total_hours_2) && !empty($timesheetVal->total_hours_3) ) {                

                //$output .= '"total_hours": '. '"'. getTotal($timesheetVal->total_hours_1, $timesheetVal->total_hours_3).'",';                
                $output .= '"total_hours": '. '"'. getTotal($timesheetVal->total_hours_1, $timesheetVal->total_hours_2, $timesheetVal->total_hours_3).'",';                                

            } elseif ( !empty($timesheetVal->total_hours_1) ) {

                $output .= '"total_hours": '. '"'.number_format((double) $timesheetVal->total_hours_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->total_hours_2) ) {

                $output .= '"total_hours": '. '"'.number_format((double) $timesheetVal->total_hours_2, 2, '.', '').'",';                

            } else {

                $output .= '"total_hours": '. '"'. ' - ' .'",';

            }*/


            //Work Hours
            $workHoursTotal = (double) getTotal($timesheetVal->work_hours_1, $timesheetVal->work_hours_2, $timesheetVal->work_hours_3);
            
            if( $workHoursTotal ) {

                $output .= '"work_hours": '. '"'. number_format($workHoursTotal, 2, '.', '').'",';

            } else {

                $output .= '"work_hours": '. '"'. ' - ' .'",';

            }

            /*if ( !empty($timesheetVal->work_hours_1) && !empty($timesheetVal->work_hours_2) ) {                

                $output .= '"work_hours": '. '"'. getTotal($timesheetVal->work_hours_1, $timesheetVal->work_hours_2).'",';

            } elseif ( !empty($timesheetVal->work_hours_1) && !empty($timesheetVal->work_hours_2) && !empty($timesheetVal->work_hours_3) ) {                

                //$output .= '"work_hours": '. '"'. getTotal($timesheetVal->work_hours_1, $timesheetVal->work_hours_3).'",';                
                $output .= '"work_hours": '. '"'. getTotal($timesheetVal->work_hours_1, $timesheetVal->work_hours_2, $timesheetVal->work_hours_3).'",';                                

            } elseif ( !empty($timesheetVal->work_hours_1) ) {

                $output .= '"work_hours": '. '"'.number_format((double) $timesheetVal->work_hours_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->work_hours_2) ) {

                $output .= '"work_hours": '. '"'.number_format((double) $timesheetVal->work_hours_2, 2, '.', '').'",';                

            } else {

                $output .= '"work_hours": '. '"'. ' - ' .'",';

            }*/

            //Total Overtime
            $overtimeTotal = (double) getTotal($timesheetVal->total_overtime_1, $timesheetVal->total_overtime_2, $timesheetVal->total_overtime_3);
            
            if( $overtimeTotal ) {

                $output .= '"total_overtime": '. '"'. number_format($overtimeTotal, 2, '.', '').'",';

            } else {

                $output .= '"total_overtime": '. '"'. ' - ' .'",';

            }            
            
            /*if ( !empty($timesheetVal->total_overtime_1) && !empty($timesheetVal->total_overtime_2) && !empty($timesheetVal->total_overtime_3) ) {                

                //$output .= '"total_overtime": '. '"'. getTotal($timesheetVal->total_overtime_1, $timesheetVal->total_overtime_3).'",';                
                $output .= '"total_overtime": '. '"'. getTotal($timesheetVal->total_overtime_1, $timesheetVal->total_overtime_2, $timesheetVal->total_overtime_3).'",';                

            } elseif ( !empty($timesheetVal->total_overtime_1) && !empty($timesheetVal->total_overtime_2) ) {                

                $output .= '"total_overtime": '. '"'. getTotal($timesheetVal->total_overtime_1, $timesheetVal->total_overtime_2).'",';                

            } elseif ( !empty($timesheetVal->total_overtime_1) ) {

                $output .= '"total_overtime": '. '"'.number_format((double) $timesheetVal->total_overtime_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->total_overtime_2) ) {

                $output .= '"total_overtime": '. '"'. getTotal($timesheetVal->total_overtime_1, $timesheetVal->total_overtime_2, $timesheetVal->total_overtime_3).'",';                

            } elseif ( !empty($timesheetVal->total_overtime_3) ) {

                $output .= '"total_overtime": '. '"'.number_format((double) $timesheetVal->total_overtime_3, 2, '.', '').'",';                

            } else {

                $output .= '"total_overtime": '. '"'. ' - ' .'",';

            }*/

            //Night Differential          

            $nightDiffTotal = (double) getTotal($timesheetVal->night_differential_1, $timesheetVal->night_differential_2, $timesheetVal->night_differential_3);
            
            if( $nightDiffTotal ) {

                $output .= '"night_differential": '. '"'. number_format($nightDiffTotal, 2, '.', '').'",';

            } else {

                $output .= '"night_differential": '. '"'. ' - ' .'",';

            }

            /*if ( !empty($timesheetVal->night_differential_1) && !empty($timesheetVal->night_differential_2) ) {                

                $output .= '"night_differential": '. '"'. getTotal($timesheetVal->night_differential_1, $timesheetVal->night_differential_2).'",';

            } elseif (!empty($timesheetVal->night_differential_1) && !empty($timesheetVal->night_differential_2) && !empty($timesheetVal->night_differential_3) ) {                

                //$output .= '"night_differential": '. '"'. getTotal($timesheetVal->night_differential_1, $timesheetVal->night_differential_3).'",';                
                $output .= '"night_differential": '. '"'. getTotal($timesheetVal->night_differential_1, $timesheetVal->night_differential_2, $timesheetVal->night_differential_3).'",';                                

            } elseif ( !empty($timesheetVal->night_differential_1) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timesheetVal->night_differential_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->night_differential_2) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timesheetVal->night_differential_2, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->night_differential_3) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timesheetVal->night_differential_3, 2, '.', '').'",';                                

            } else {

                $output .= '"night_differential": '. '"'. ' - ' .'",';

            }*/                         


            //Tardiness
            $tardinessTotal = (double) getTotal($timesheetVal->tardiness_1, $timesheetVal->tardiness_2, $timesheetVal->tardiness_3);
            
            if( $tardinessTotal ) {

                $output .= '"tardiness": '. '"'. number_format($tardinessTotal, 2, '.', '').'",';

            } else {

                $output .= '"tardiness": '. '"'. ' - ' .'",';

            }      

            /*if ( !empty($timesheetVal->tardiness_1) && !empty($timesheetVal->tardiness_2) ) {                

                $output .= '"tardiness": '. '"'. getTotal($timesheetVal->tardiness_1, $timesheetVal->tardiness_2).'",';

            } elseif ( !empty($timesheetVal->tardiness_1) && !empty($timesheetVal->tardiness_2) && !empty($timesheetVal->tardiness_3) ) {                

                //$output .= '"tardiness": '. '"'. getTotal($timesheetVal->tardiness_1, $timesheetVal->tardiness_3).'",';                
                $output .= '"tardiness": '. '"'. getTotal($timesheetVal->tardiness_1, $timesheetVal->tardiness_2, $timesheetVal->tardiness_3).'",';                                

            } elseif ( !empty($timesheetVal->tardiness_1) ) {

                $output .= '"tardiness": '. '"'.number_format((double) $timesheetVal->tardiness_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->tardiness_2) ) {

                $output .= '"tardiness": '. '"'.number_format((double) $timesheetVal->tardiness_2, 2, '.', '').'",';                

            } else {

                $output .= '"tardiness": '. '"'. ' - ' .'",';

            }*/

            //Undertime
            $undertimeTotal = (double) getTotal($timesheetVal->undertime_1, $timesheetVal->undertime_2, $timesheetVal->undertime_3);
            
            if( $undertimeTotal ) {

                $output .= '"undertime": '. '"'. number_format($undertimeTotal, 2, '.', '').'",';

            } else {

                $output .= '"undertime": '. '"'. ' - ' .'",';

            } 

            /*if ( !empty($timesheetVal->undertime_1) && !empty($timesheetVal->undertime_3) ) {                

                $output .= '"undertime": '. '"'. getTotal($timesheetVal->undertime_1, $timesheetVal->undertime_1).'",';

            } elseif ( !empty($timesheetVal->undertime_1) && !empty($timesheetVal->undertime_2) && !empty($timesheetVal->undertime_3) ) {                

                //$output .= '"undertime": '. '"'. getTotal($timesheetVal->undertime_1, $timesheetVal->undertime_3).'",';                
                $output .= '"undertime": '. '"'. getTotal($timesheetVal->undertime_1, $timesheetVal->undertime_2, $timesheetVal->undertime_3).'",';                                

            } elseif ( !empty($timesheetVal->undertime_1) ) {

                $output .= '"undertime": '. '"'.number_format((double) $timesheetVal->undertime_1, 2, '.', '').'",';                

            } elseif ( !empty($timesheetVal->undertime_2) ) {

                $output .= '"undertime": '. '"'.number_format((double) $timesheetVal->undertime_2, 2, '.', '').'",';                

            } else {

                $output .= '"undertime": '. '"'. ' - ' .'",';

            }*/ 

            //Overtime Status
            if( (!empty($timesheetVal->total_overtime_1) && 
                is_null($timesheetVal->overtime_status_1)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                is_null($timesheetVal->overtime_status_2)) ||

                (!empty($timesheetVal->total_overtime_1) && 
                is_null($timesheetVal->overtime_status_1)) && 

                (!empty($timesheetVal->total_overtime_3) && 
                is_null($timesheetVal->overtime_status_3)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                is_null($timesheetVal->overtime_status_2)) && 

                (!empty($timesheetVal->total_overtime_3) && 
                is_null($timesheetVal->overtime_status_3)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'",'; 

            } elseif ( (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === -1)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === -1)) ||            

                (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === -1)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === -1)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === -1)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === -1)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Applied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'",'; 

            } elseif ( (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === 0)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === 0)) ||            

                (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === 0)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === 0)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === 0)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === 0)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Denied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'",';                 

            } elseif ( (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === 1)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === 1)) ||            

                (!empty($timesheetVal->total_overtime_1) && 
                (!is_null($timesheetVal->overtime_status_1) &&
                $timesheetVal->overtime_status_1 === 1)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === 1)) ||

                (!empty($timesheetVal->total_overtime_2) && 
                (!is_null($timesheetVal->overtime_status_2) &&
                $timesheetVal->overtime_status_2 === 1)) &&

                (!empty($timesheetVal->total_overtime_3) && 
                (!is_null($timesheetVal->overtime_status_3) &&
                $timesheetVal->overtime_status_3 === 1)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Approved OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'",';                 

            } else {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'",';

            } 

            //CHECK CLOCKING STATUS: CLOCK_OUT_2                                    
            if ( $timesheetVal->clocking_status === "clock_out_3" && $timesheetVal->daydate === $currentDate) {

                $messageIn = 'Your are already in.';                    
                $messageOut = 'Your are already Out.';                    
                $output .= '"message_in": '. '"'.$messageIn.'",';
                $output .= '"message_out": '. '"'.$messageOut.'"';
                
            } else {

                $messageIn = '';                    
                $messageOut = '';                    
                $output .= '"message_in": '. '"'.$messageIn.'",';
                $output .= '"message_out": '. '"'. $messageOut.'"';

            }


            if ( $ctr == sizeof($timesheet) ) {
                $output .= '}';
            } else {
                $output .= '},';
            }

            $ctr++;                 

        }

        $output .= ']';
        $output .= '}'; 

        return (string) $output; //json_encode($timeSheetObj);
    }    

}
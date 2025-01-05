<?php
/**
*
* TARDINESS TIME
*
*/

function tardinessTime($TimesheetTimeIn, $scheduleStartTime) {

	if( timestamp($TimesheetTimeIn) > timestamp($scheduleStartTime) ) {

		$interval = getDateTimeDiffInterval($TimesheetTimeIn, $scheduleStartTime);		

		$hh = $interval->format('%H');
		$mm = $interval->format('%I');
		$ss = $interval->format('%S');	

		//$tardiness = getTimeToDecimalHours($hh, $mm, $ss); //number_format($hours, 2);
		$tardiness = getTimeToDecimalMinutes($hh, $mm, $ss);

		return number_format($tardiness, 2);

	} else {

		return FALSE;

	}

}


/**
*
* TOTAL HOURS
*
*/

function totalHours($timesheetTimeIn = '', $timesheetTimeOut = '') {

	/*$interval = getDateTimeDiffInterval($timesheetTimeIn, $timesheetTimeOut);

	$hh = $interval->format('%H');
	$mm = $interval->format('%I');
	$ss = $interval->format('%S');*/	

	$interval = getDateTimeDiffInterval($timesheetTimeIn, $timesheetTimeOut);

	$days = $interval->format('%a');
	$days = (int) $days;

	if ( $days !== 0 ) {
		
		$hhToDays = ($days * 24);
		$hh = (int) $hhToDays;				

	} else {

		$hh = (int) $interval->format('%h');

	}

	$mm = (int) $interval->format('%i');
	$ss = (int) $interval->format('%s');


	return number_format(getTimeToDecimalHours($hh, $mm, $ss), 2);
	
}


/**
*
* WORK HOURS
*
*/

//function workHours($timesheetTimeIn, $timesheetTimeOut, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak) {
function workHours($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak) {

	//$totalHours = totalHours($timesheetTimeIn, $timesheetTimeOut);										  
	$totalHours = totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);

	if( $hasBreak ) {						

		list($breakTimeHh, $breakTimeMm, $breakTimeSs) = explode(':', $breakTime);
		$breakTime = getTimeToDecimalHours($breakTimeHh, $breakTimeMm, $breakTimeSs);												
		
		if ( $totalHours >= $halfOfhoursPerDay ) {
			$workhours = $totalHours - $breakTime;
			return number_format($workhours, 2);

		} else {

			//$workhours = $totalHours
			$workhours = $totalHours;
			return number_format($workhours, 2);
		}

	} else {

		$workhours = $hoursPerDay;
		return number_format($workhours, 2);

	}

}


/**
*
* TOTAL HOURS WITH OVERTIME
*
*/

function totalHoursWithOvertime($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak) {

	$totalHours = totalHours($timesheetTimeIn, $timesheetTimeOut);
	//$workhours = workHours($timesheetTimeIn, $timesheetTimeOut, $scheduleEndTime, $breakTime, $hoursPerDay, $halfOfhoursPerDay, $hasBreak);
	$overtime = overtimeHours($timesheetTimeOut, $scheduleEndTime);

	//overtime hours = worked hours - regular hours or hours per day
		
	if ( !$overtime ) {
	
		return number_format($totalHours, 2);

	} else {

		$totalHoursWithOvertime = $totalHours + $overtime;
		return number_format($totalHoursWithOvertime, 2);

	}
	
}


/**
*
* OVERTIME
*
*/

function overtimeHours($timesheetTimeOut, $scheduleEndTime) {

	/*
	$interval = getDateTimeDiffInterval($timesheetTimeOut, $scheduleEndTime);	

	$hh = $interval->format('%H');
	$mm = $interval->format('%I');
	$ss = $interval->format('%S');
	*/


	$interval = getDateTimeDiffInterval($timesheetTimeOut, $scheduleEndTime);	

	$days = $interval->format('%a');
	$days = (int) $days;

	if ( $days !== 0 ) {
		
		$hhToDays = ($days * 24);
		$hh = (int) $hhToDays;				

	} else {

		$hh = (int) $interval->format('%h');

	}

	$mm = (int) $interval->format('%i');
	$ss = (int) $interval->format('%s');	

	if ( timestamp(date('Y-m-d H:i', timestamp($timesheetTimeOut))) > timestamp(date('Y-m-d H:i', timestamp($scheduleEndTime))) ) {

		$overtimeHours = getTimeToDecimalHours($hh, $mm, $ss);
		return number_format($overtimeHours, 2);

	} else {

		return FALSE;

	}

}


/**
*
* UNDERTIME
*
*/

function underTimeHours($timesheetTimeOut, $scheduleEndTime) {
	
	/*
	$interval = getDateTimeDiffInterval($timesheetTimeOut, $scheduleEndTime);

	$hh = (int) $interval->format('%h');
	$mm = (int) $interval->format('%i');
	$ss = (int) $interval->format('%s');
	*/

	$interval = getDateTimeDiffInterval($timesheetTimeOut, $scheduleEndTime);	

	$days = $interval->format('%a');
	$days = (int) $days;

	if ( $days !== 0 ) {
		
		$hhToDays = ($days * 24);
		$hh = (int) $hhToDays;				

	} else {

		$hh = (int) $interval->format('%h');

	}

	$mm = (int) $interval->format('%i');
	$ss = (int) $interval->format('%s');	

	if ( timestamp(date('Y-m-d H:i', timestamp($timesheetTimeOut))) < timestamp(date('Y-m-d H:i', timestamp($scheduleEndTime))) ) {

		$underTimeHours = getTimeToDecimalMinutes($hh, $mm, $ss);
		
		return number_format($underTimeHours, 2);		

	} else {

		return FALSE;

	}


}

//http://stackoverflow.com/questions/10532687/time-night-differential-computation-in-php
function nightDiff($timesheetTimeIn, $timesheetTimeOut, $scheduleStartTime) {

	$nightDiffStartTime = Config::get('euler.night_diff_start_time');
	$nightDiffEndTime = Config::get('euler.night_diff_end_time');

	$start_schedule = timestamp(date('Y-m-d H:i', timestamp($scheduleStartTime)));
	$start_work = timestamp(date('Y-m-d H:i', timestamp($timesheetTimeIn)));
	$end_work = timestamp(date('Y-m-d H:i', timestamp($timesheetTimeOut)));

	$start_work_date = date('Y-m-d', timestamp($timesheetTimeIn));

	//return $start_work_date.' '.$nightDiffStartTime;
	$nightDiffStartTimeModify = timestamp(date('Y-m-d H:i', timestamp($start_work_date.' '.$nightDiffStartTime)));

	if ( $start_work < $nightDiffStartTimeModify ) {		

		$start_work = $nightDiffStartTimeModify;

	}

	list($nightDiffStartTimeHour, $nightDiffStartTimeMinute, $nightDiffStartTimeSecond) = explode(':', $nightDiffStartTime);	
	list($nightDiffEndTimeHour, $nightDiffEndTimeMinute, $nightDiffEndTimeSecond) = explode(':', $nightDiffEndTime);	

    $start_night = mktime($nightDiffStartTimeHour, $nightDiffStartTimeMinute, $nightDiffStartTimeSecond, date('m',$start_work), date('d',$start_work), date('Y',$start_work));
    	
    //WITH IN THE SAME DAY
    if ( timestamp(date('Y-m-d', $start_work)) === timestamp(date('Y-m-d', $end_work)) ) {
	
    	$end_night = mktime($nightDiffEndTimeHour, $nightDiffEndTimeMinute, $nightDiffEndTimeSecond, date('m',$end_work), date('d',$end_work), date('Y',$end_work));

	} else { //NOT IN THE SAME DAY

		$end_night = mktime($nightDiffEndTimeHour, $nightDiffEndTimeMinute, $nightDiffEndTimeSecond, date('m',$start_work), date('d',$start_work) + 1, date('Y',$start_work));
	
	}	


    if($start_work >= $start_night && $start_work <= $end_night)
    {

        if($end_work >= $end_night) {
            
            return ($end_night - $start_work) / 3600;
        
        } else {
        
            return ($end_work - $start_work) / 3600;
        }
    }
    elseif($end_work >= $start_night && $end_work <= $end_night) {


        if($start_work <= $start_night) {

            return ($end_work - $start_night) / 3600;

        } else {

            return ($end_work - $start_work) / 3600;

        }

    } else { //WITH IN THE SAME DAY

        if($start_work < $start_night && $end_work > $end_night) {
            
           //return ($end_night - $start_night) / 3600; //Result has negative value if time out is greater than night diff end time           

			if( ($end_work - $start_work) / 3600 > 0  ) {

				$end_work_hour = date('H', $end_work);
				$nightDiffEndTimeHour = date('H', timestamp(Config::get('euler.night_diff_end_time')));
				$nightDiffEndTimeDateTime = date('Y-m-d H:i', timestamp(Config::get('euler.night_diff_end_time')));

				//WITH IN THE SAME DAY
				if ( $end_work_hour > $nightDiffEndTimeHour ) { //<	FIX THIS ISSUE		

					$end_work = timestamp(date('Y-m-d H:i', timestamp(date('Y-m-d H:i', timestamp(Config::get('euler.night_diff_end_time')))) ));
					
					$end_night = mktime($nightDiffEndTimeHour, $nightDiffEndTimeMinute, $nightDiffEndTimeSecond, date('m',$end_work), date('d',$end_work), date('Y',$end_work));
					
					return ($end_night - $start_night) / 3600;
				
				} else {

					return ($end_night - $start_night) / 3600;

				}	

			} else {

				return 'FALSE';

			}        	

        } else {

        	return ($end_work - $start_work) / 3600;

        }       

    }
}


//22, 23, 0, 1, 2, 3, 4, 5, 6
function nightDifferentialRange() {
	
	//GET THE NIGHT DIFF START TO END TIME
	$nightDiffStartTime = Config::get('euler.night_diff_start_time');
	//$nightDiffEndTime = Config::get('euler.night_diff_end_time');
	$nightDiffRangeCount = Config::get('euler.night_diff_range_count');

	$datetime = new DateTime($nightDiffStartTime);
	$datetime->modify('-1 hour');

	$nightDiffStartToEndTimeArr = array();
	for($i = 0; $i < $nightDiffRangeCount; $i++) {

	    $datetime->modify('+1 hour');
	    //echo $datetime->format('Y-m-d H:i:s')."\n";
	    $nightDiffStartToEndTimeArr[] = $datetime->format('G');

	}	

	return $nightDiffStartToEndTimeArr;

}

function nightDifferential($startTime = '', $endTime = '', $nightDiffStartToEndTimeArr = array()) {

	//http://php.net/manual/en/function.date.php

	//$startTime = "2015-09-22 03:00:00";
	//$endTime = "2015-09-22 12:00:00";

	$datetimeStartTime = new DateTime($startTime);
	//$datetimeStartTime = new DateTime('2015-09-21 23:00:00');
	$datetimeStartTime->modify('-1 hour');

	$datetimeEndTime = new DateTime($endTime);
	$datetimeEndTime->modify('+1 hour');

	//echo $datetimeStartTime->format('Y-m-d H:i:s')."\n";

	
	$nightDiffArr = array();
	for($i = 0; $i < 72; $i++) {

		//echo $i;
	     
	    $datetimeStartTime->modify('+1 hour');
	    //echo $datetimeStartTime->format('Y-m-d H:i:s')."\n";
		//echo $datetimeStartTime->format('G'); 
		//echo date('G', strtotime($endTime));

		if ( (int) $datetimeStartTime->format('G') !== (int) $datetimeEndTime->format('G') ) {

			if ( in_array($datetimeStartTime->format('G'), $nightDiffStartToEndTimeArr) ) {

				$nightDiffArr[] = $datetimeStartTime->format('Y-m-d H:i:s');

			}

		} else {

			break;

		}

	}

	if( !empty($nightDiffArr) ) {

		//dd($nightDiffArr);				
		//return array('nightDiffStart' => $nightDiffArr[0], 'nightDiffEnd' => $nightDiffArr[count($nightDiffArr) - 1]);

		$nightDiffStart = $nightDiffArr[0];
		$nightDiffEnd = $nightDiffArr[count($nightDiffArr) - 1];

		return totalHours($nightDiffStart, $nightDiffEnd);

	} else {

		return FALSE;

	}

}

/**
*
* SCHEDULEDIFF: To get the regular hours or hours per day
*
*/

function scheduleDiff($scheduleStartTime = '', $scheduleEndTime = '') {

	/*
	$interval = getDateTimeDiffInterval($scheduleStartTime, $scheduleEndTime);

	$hh = $interval->format('%H');
	$mm = $interval->format('%I');
	$ss = $interval->format('%S');								
	*/

	$interval = getDateTimeDiffInterval($scheduleStartTime, $scheduleEndTime);	

	$days = $interval->format('%a');
	$days = (int) $days;

	if ( $days !== 0 ) {
		
		$hhToDays = ($days * 24);
		$hh = (int) $hhToDays;				

	} else {

		$hh = (int) $interval->format('%h');

	}

	$mm = (int) $interval->format('%i');
	$ss = (int) $interval->format('%s');	

	return number_format(getTimeToDecimalHours($hh, $mm, $ss) - 1, 2);
	
}

function hasHoliday($currentDate) {

	$holiday = Holiday::where('holiday_date', $currentDate)->first();

	if( !empty($holiday->holiday_type) ) {

		return TRUE;

	} else {

		return FALSE;

	}

}


/*
|--------------------------------------------------------------------------
| DateTime Controller
| http://www.calculatorsoup.com/calculators/time/
|--------------------------------------------------------------------------	
*/

function getTimeToDecimalHours($hh = 0, $mm = 0, $ss = 0) { //Used

	//To convert time to just hours	
	$hours = $hh * (1 / 1); // or $hh	
	$minutes = $mm * (1 / 60); // or $mm / 60 hours	
	$seconds = $ss * (1 / 3600); // $ss / 3600 hours	

	return $hours + $minutes + $seconds;

}

function getTimeToDecimalMinutes($hh, $mm, $ss) { //Used

	//To convert time to just minutes	
	$hourstominutes = $hh * (60 / 1); // or $hh * 60 minutes	
	$minutes = $mm * (1 / 1); // or $mm * 1 minutes 	
	$secondstominutes = $ss * (1 / 60); // or $ss / 60 minutes	
	return $hourstominutes + $minutes + $secondstominutes;
	
}

function getTimeToDecimalSeconds($hh, $mm, $ss) { //Used

	//To convert time to just seconds
	$hourstoseconds = $hh * (3600 / 1); // or $hh * 3600 seconds
	$minutestoseconds = $mm * (60 / 1); // or $mm * 60 seconds
	$seconds = $ss * (1 / 1); // or $ss / 1 seconds		
	return $hourstoseconds + $minutestoseconds + $seconds;		

}

function getDateTimeDiffInterval($datetime1, $datetime2) { //Used

	$datetime1 = new DateTime($datetime1);
	$datetime2 = new DateTime($datetime2);
	$interval = $datetime1->diff($datetime2);	
	
	//return $interval->format($format);

	return $interval;

}

function getDateTimeDiffIntervalReverse($datetime1, $datetime2) { //Used

	$datetime1 = new DateTime($datetime2);
	$datetime2 = new DateTime($datetime1);
	$interval = $datetime1->diff($datetime2);	
	
	//return $interval->format($format);

	return $interval;

}


//http://php.net/manual/en/datetime.gettimestamp.php
function timestamp($dateTime = '') {

	$date = new DateTime($dateTime);
	return $date->getTimestamp();

}

function getTotal($decimaltime1 = 0.00, $decimaltime2 = 0.00, $decimaltime3 = 0.00) {

	$decimaltime = (double) $decimaltime1 + (double) $decimaltime2 + (double) $decimaltime3;
	
		return number_format($decimaltime, 2, '.', '');			

}


function GetDays($sStartDate, $sEndDate){  
      // Firstly, format the provided dates.  
      // This function works best with YYYY-MM-DD  
      // but other date formats will work thanks  
      // to strtotime().  
      $sStartDate = gmdate("Y-m-d", strtotime($sStartDate));  
      $sEndDate = gmdate("Y-m-d", strtotime($sEndDate));  

      // Start the variable off with the start date  
     $aDays[] = $sStartDate;  

     // Set a 'temp' variable, sCurrentDate, with  
     // the start date - before beginning the loop  
     $sCurrentDate = $sStartDate;  

     // While the current date is less than the end date  
     while($sCurrentDate < $sEndDate){  
       // Add a day to the current date  
       $sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));  

       // Add this new day to the aDays array  
       $aDays[] = $sCurrentDate;  
     }  

     // Once the loop has finished, return the  
     // array of days.  
     return $aDays;  
   }  


//Summary Computation
function cutoffTotal($decimalArr) {

	$total = 0;
	foreach ( $decimalArr as $decimal ) {

		$total += $decimal;

	}  

	//return $total;
	return number_format((double) $total, 2, '.', '');
}	


function simplePaginateArray($array = array(), $perPage = 0) {

  	/*$perPage = 2;
	$currentPage = Input::get('page') - 1;
	$items = array_slice($listCurrentAbsencesPerCutoff, $currentPage * $perPage, $perPage);
	$totalItems = sizeof($listCurrentAbsencesPerCutoff);

	$listCurrentAbsencesPerCutoff = Paginator::make($items, $totalItems, $perPage);*/	

  	//$perPage = 2;
	$currentPage = Input::get('page') - 1;
	$items = array_slice($array, $currentPage * $perPage, $perPage);
	$totalItems = sizeof($array);

	return $array = Paginator::make($items, $totalItems, $perPage);

}

/*function paginateArray($array, $perPage) {

$page = Input::get('page', 1); // Get the current page or default to 1, this is what you miss!
//$perPage = 20;
$offset = ($page * $perPage) - $perPage;

return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

}*/

function employeeTypeName($employeeType) {

	switch ($employeeType) {
		case 1:
			return 'Manager';
			break;

		case 2:
			return 'Supervisor';
			break;			
		
		default:
			return 'Employee';
			break;
	}


}


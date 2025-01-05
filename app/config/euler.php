<?php
/**
*
* EMPLOYEE SETTINGS
* http://laravel.com/docs/4.2/configuration
*/

return array(

	'current_date_time' => date('Y-m-d H:i:s'),
	'current_date' => date('Y-m-d'),
	'current_time' => date('H:i:s'),
	
	'has_overtime' => '',
	'overtime_start_time' => '',

	'has_break' => 1,
	'break_time' => '01:00:00',
	'hours_per_day' => '8.00', // Hours worked (Maximum per day)
	'is_flexible' => 0,
	'flexible_from_time' => '',
	'flexible_to_time' => '',
	'has_grace_period' => '',
	'grace_period' => '',

	'night_diff_start_time' => '22:00:00',
	'night_diff_end_time' => '06:00:00',
	'night_diff_range' => array(22, 23, 0, 1, 2, 3, 4, 5, 6),	
	'night_diff_range_count' => 9,

	//'cutoff' => array('cutOffStart' => array(1 => 11, 2 => 26), 'cutOffEnd' => array(1 => 25, 2 => 10))

	'cutoff' => array('cutOffStart' => array(1 => 11, 2 => 26), 'cutOffEnd' => array(1 => 25, 2 => 10))
		
);
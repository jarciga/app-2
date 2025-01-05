<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	// View users/index.blade.php
	//return View::make('users.index'); 

	//1st Cutoff - e.g 11-25 - 5
	$currentDate = date('Y-m-d');	
	//$currentDate = '2015-08-01';
	$currentMonth = date('M');

	$cutOff = Cutoff::where('month', $currentMonth)->first();
	echo $cutOff->month;

	$begin = new DateTime( $cutOff->cutoff_date_from_1 );
	$end = new DateTime( $cutOff->cutoff_date_to_1 );	
	$end = $end->modify('+1 day');

	$interval = DateInterval::createFromDateString('1 day');
	$period = new DatePeriod($begin, $interval, $end);

	foreach ( $period as $dt )
	{
		$cutoffArr[] = $dt->format( "Y-m-d" );	
	}

	if( in_array($currentDate, $cutoffArr) ) 
	{
		echo 'Match found';
		$cutOffBool = TRUE;
	} 
	else
	{
		echo 'Match not found';
		$cutOffBool = FALSE;

	}

	if ($cutOffBool)
	{

		/*$begin1 = new DateTime( $cutOff->cutoff_date_from_1 );
		$end1 = new DateTime( $cutOff->cutoff_date_to_1 );	
		$end1 = $end1->modify('+1 day');

		$interval1 = DateInterval::createFromDateString('1 day');
		$period1 = new DatePeriod($begin1, $interval1, $end1);

		foreach ( $period1 as $dt1 )
		{
			$cutoffArr1[] = $dt1->format( "Y-m-d" );	
		}

		return dd($cutoffArr1);*/
		return dd($cutoffArr);

	} else {

		$begin2 = new DateTime( $cutOff->cutoff_date_from_2 );
		$end2 = new DateTime( $cutOff->cutoff_date_to_2 );	
		$end2 = $end2->modify('+1 day');

		$interval2 = DateInterval::createFromDateString('1 day');
		$period2 = new DatePeriod($begin2, $interval, $end2);

		foreach ( $period2 as $dt2 )
		{
			$cutoffArr2[] = $dt2->format( "Y-m-d" );	
		}

		return dd($cutoffArr2);

	}

});

//Route::get('/', array('as' => '', 'uses' => 'UsersController@showLogin' ));

//Route::get('/login', array('as' => 'show.login', 'uses' => 'UsersController@showLogin' ));
//Route::post('/login', array('as' => 'process.login', 'uses' => 'UsersController@processLogin' ));
//Route::get('/logout', array('as' => 'process.logout', 'uses' => 'UsersController@processLogout' ));

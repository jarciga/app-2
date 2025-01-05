<?php

class CutoffController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /cutoff
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /cutoff/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /cutoff
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /cutoff/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /cutoff/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /cutoff/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /cutoff/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function cutOffGenerator1($cutOffStart, $cutOffEnd, $cutOffRange, $currentDayOfTheMonth, $prevMonthNumberOfDays) {

		//11-25 - 5
		//26-10 - 20

		$date = new DateTime(date('Y-'.'m-'.$cutOffStart));
		//$date = DateTime::createFromFormat('Y-m-d', date('Y-'.'m-'.$cutOffStart));									

		for ($i = 0; $i < count($cutOffRange)+1; $i++) 
		{				
			if ( $cutOffEnd !== (int) $date->format('d') )
			{
				$date->modify('+1 day');
				$cutOffArr[] = $date->format('Y-m-d');
				
			} /*else {

				$cutOffArr[] = $date->format('Y-m-d');
				
			}*/

		}

		if ( is_array($cutOffArr) )
		{

			//Bug
		  	foreach($cutOffArr as $cutOffVal) 
		  	{

				$date = new DateTime($cutOffVal);
				
				if( ((int) $currentDayOfTheMonth) === 1 || ((int) $currentDayOfTheMonth <= $cutOffEnd) ) {										

					//$date->modify('-1 month');

					//$date->modify('-'.$prevMonthNumberOfDays.' days');
					//$date->modify('-28 days'); // set it to 30 days
					//$date->modify('-29 days'); // set it to 30 days
					//$date->modify('-30 days'); // 26-11
					//$date->modify('-31 days'); // 25-10

					if ( in_array($prevMonthNumberOfDays, array('28', '29')) ) {

						$date->modify('-30 days');

					} elseif ($prevMonthNumberOfDays === '30') {

						$date->modify('-30 days');

					} elseif ( $prevMonthNumberOfDays === '31' ) {

						//$date->modify('-31 days');
						$date->modify('-30 days');

					}
									
				}

				$cutOffVal = $date->format('Y-m-d');
		  		$dayDateTempArr[] = $cutOffVal;



				if ((int) $date->format('d') === $cutOffStart + 1) {

					$cutOffStartArr[] = $date->format('Y-m-d');

				}

				if ((int) $date->format('d') === $cutOffEnd) {

					$cutOffEndArr[] = $date->format('Y-m-d');

				}											
				
		  	}

		  	

		  	foreach($dayDateTempArr as $dayDateTempVal) {

		  		//if( ( strtotime($dayDateTempVal) >= strtotime($cutOffStartArr[0]) ) && 
		  		//	( strtotime($dayDateTempVal) <= strtotime($cutOffEndArr[0]) ) ) {

		  		//if( strtotime($dayDateTempArr[0]) >= 26 && 
		  		//	strtotime($dayDateTempVal) <= 10 ) {

					$cutOffModifiedArr[] = $dayDateTempVal;

		  		//}


		  		

		  	}

		  	return implode(',', $cutOffModifiedArr);

		}

	}


	public function cutOffGenerator2($cutOffStart, $cutOffEnd, $cutOffRange) {

		//11-25 - 5
		//26-10 - 20

		$date = new DateTime(date('Y-'.'m-'.$cutOffStart));
		//$date = DateTime::createFromFormat('Y-m-d', date('Y-'.'m-'.$cutOffStart));									

		for ($i = 0; $i < count($cutOffRange); $i++) 
		{				
			if ( $cutOffEnd !== (int) $date->format('d') )
			{
				$date->modify('+1 day');
				$cutOffArr[] = $date->format('Y-m-d');
				
			} /*else {

				$cutOffArr[] = $date->format('Y-m-d');
				
			}*/

		}

		return implode(',', $cutOffArr);

	}

}
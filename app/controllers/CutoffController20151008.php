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


	public function cutOffGenerator($cutOffStart, $cutOffEnd, $cutOffRange) {

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
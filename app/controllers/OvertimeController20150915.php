<?php

class OvertimeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /overtime
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /overtime/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /overtime
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /overtime/{id}
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
	 * GET /overtime/{id}/edit
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
	 * PUT /overtime/{id}
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
	 * DELETE /overtime/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function redrawOvertimeStatus()
	{
		$data = Input::all();

		/*return dd($data);
		exit;*/

		$timesheet = Timesheet::where('id', '=', $data['timesheetId'])->first();															

		if ( Request::ajax() ){		
			
			if ( $timesheet->clocking_status === 'clock_out_1' ) {

				$timesheet->overtime_status_1 = $data['otStatus'];

				if( $timesheet->save() ) {

					DB::table('overtime')
								->where('timesheet_id', $data['timesheetId'])
								->where('seq_no', 1)
								->update(['overtime_status' => $data['otStatus']]);

					return Redirect::to('/redraw/timesheet');	

				}

			} elseif ( $timesheet->clocking_status === 'clock_out_2' ) {
				
				$timesheet->overtime_status_2 = $data['otStatus'];			

				if( $timesheet->save() ) {

					DB::table('overtime')
								->where('timesheet_id', $data['timesheetId'])
								->where('seq_no', 2)
								->update(['overtime_status' => $data['otStatus']]);

					return Redirect::to('/redraw/timesheet');	

				}		

			} elseif ( $timesheet->clocking_status === 'clock_out_3' ) {
				
				$timesheet->overtime_status_3 = $data['otStatus'];						

				if( $timesheet->save() ) {

					DB::table('overtime')
								->where('timesheet_id', $data['timesheetId'])
								->where('seq_no', 3)
								->update(['overtime_status' => $data['otStatus']]);

					return Redirect::to('/redraw/timesheet');	

				}		

			}

		}

	}



}
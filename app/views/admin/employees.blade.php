@extends('layouts.admin.default')

@section('content')
<?php 

$employeeId = Session::get('userEmployeeId');
$dayOfTheWeek = date('l');
$currentDate = date('Y-m-d');
$shift = 1;

$message = Session::get('message');

/*$employee = DB::table('employees')->where('id', $employeeId)->get();

$company = DB::table('employees')->where('id', $employee[0]->company_id)->get(); 
$manager = DB::table('employees')->where('id', $employee[0]->manager_id)->get(); 
$jobTitle = DB::table('job_title')->where('id', $employee[0]->position_id)->get(); 
$department = DB::table('departments')->where('id', $employee[0]->department_id)->get(); 

$employeesByManager = DB::table('employees')->where('manager_id', $employeeId)->get();*/


$employees = Employee::paginate(1000);

//$getSchedule = DB::table('employee_schedule')->where('employee_id', $employee->id)->where('schedule_date', trim($currentDate))->get();
//$getWorkShiftByDayOfTheWeek = DB::table('work_shift')->where('employee_id',  $employee->id)->where('name_of_day', $dayOfTheWeek)->where('shift', $shift)->get();

?>
<div class="page-container">

        <div class="row" style="padding-bottom:20px;">

          <div class="col-md-3 clearfix">

            <aside class="sidebar">
              <nav class="sidebar-nav">
                <ul id="menu">
                  <li>
                    <a href="{{ url('/admin/dashboard') }}">
                      <span class="sidebar-nav-item-icon fa fa-tachometer fa-lg"></span>                      
                      <span class="sidebar-nav-item">Dashboard</span>                      
                    </a>
                    
                  </li>
                  <li>
                      <a href="{{ url('/admin/timeclock') }}">
                      <span class="sidebar-nav-item-icon fa fa-clock-o fa-lg"></span>
                      <span class="sidebar-nav-item">TimeClock & Attendance</span>
                      </a>
                      <!--ul class="submenu-1 collapse">
                          <li><a href="{{ url('/admin/timeclock') }}">Overtime</a></li>
                          <li><a href="{{ url('/admin/timeclock/report') }}">Overtime</a></li>
                      </ul-->
                  </li>
                  <li>
                      <a href="{{ url('/admin/scheduling') }}">
                      <span class="sidebar-nav-item-icon fa fa-calendar fa-lg"></span>
                      <span class="sidebar-nav-item">Employee Scheduling</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>
                  <li>
                      <a href="{{ url('/admin/hr') }}">
                      <span class="sidebar-nav-item-icon fa fa-users fa-lg"></span>
                      <span class="sidebar-nav-item">Human Resources</span>
                      </a>
                      <ul class="submenu-1 collapse">
                          <li><a href="{{ url('/admin/hr/employees') }}">Employees</a></li>
                      </ul>
                  </li>                  
                  <li>
                      <a href="{{ url('/admin/payroll') }}">
                      <span class="sidebar-nav-item-icon fa fa-calculator fa-lg"></span>
                      <span class="sidebar-nav-item">Payroll</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>
                  <li>
                      <a href="{{ url('/admin/emailNotifysettings') }}">
                      <span class="sidebar-nav-item-icon fa fa-cogs fa-lg"></span>
                      <span class="sidebar-nav-item">Email Notification Settings</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>                                                      
                </ul>
               </nav>
            </aside>                  

          </div><!--//.col-md-2-->

          <div id="content" class="col-md-9" role="main">

            <ol class="breadcrumb hide hidden">
              <li><a href="#">Home</a></li>
              <li class="active">Page</li>
            </ol>

            <h1 class="page-header">Employees</h1>

			<div class="well hide hidden">

            	<div class="row" style="margin-bottom:20px;">

					<div class="col-md-3"><a href="#" class="btn btn-custom-default btn-block">Add Administrator</a></div>
					<div class="col-md-3"><a href="#" class="btn btn-custom-default btn-block">Add Manager</a></div>
					<div class="col-md-3"><a href="#" class="btn btn-custom-default btn-block">Add Supervisor</a></div>
					<div class="col-md-3"><a href="#" class="btn btn-custom-default btn-block">Add User</a></div>					

 				</div><!--/.row-->

            	<div class="row" style="margin-bottom:20px;">

					<div class="col-md-3"><a href="#" class="btn btn-custom-default btn-block">Create Holiday</a></div>
					<div class="col-md-3"><a href="#" class="btn btn-custom-default btn-block">Configure Cutoffs</a></div>

 				</div><!--/.row-->
   
			</div><!--/.well-->            


			<div class="row">

				<div class="col-md-12">			
					<div class="panel panel-default">
					  <!-- Default panel contents -->
					  <div class="panel-heading" style="font-size:14px; font-weight:bold;">Employees List <div class="pull-right"><a href="{{ url('/admin/user/new') }}" class="">Add New</a></div></div>
					  <div class="panel-body">
					    
						<table class="table table-striped table-hover table-list display">
						<thead>
							<tr>
								<th id="cb" class="manage-column column-cb check-column">{{ Form::checkbox('', '', '', array('id' => 'cb-select-all-1')) }}</th>
								<th>Name</th>
								<th>Company</th>
								<th>Department</th>
								<th>Manager</th>
								<th>Supervisor</th>
								<th>Status</th>
								<th style="width:10%; text-align:center;">Action</th>	
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							
						<?php
						
						//Session::push('emps', $employees);
						
						$clockingStatus = '';
						foreach($employees as $employee): 

							$company = DB::table('companies')->where('id', $employee->company_id)->get(); 							
							$department = DB::table('departments')->where('id', $employee->department_id)->get(); 							
							$manager = DB::table('employees')->where('id', $employee->manager_id)->get(); 
							$supervisor = DB::table('employees')->where('id', $employee->supervisor_id)->get();
							//$jobTitle = DB::table('job_title')->where('id', $employee->position_id)->get();
							
							//$employeesByManager = DB::table('employees')->where('manager_id', $employeeId)->get();



							$getSchedule = DB::table('employee_schedule')->where('employee_id', $employee->id)->where('schedule_date', trim($currentDate))->get();
							$getWorkShiftByDayOfTheWeek = DB::table('work_shift')->where('employee_id',  $employee->id)->where('name_of_day', $dayOfTheWeek)->where('shift', $shift)->get();

							$getTimeSheet = DB::table('employee_timesheet')->where('employee_id', $employee->id)->where('daydate', trim($currentDate))->get();
						
							/*echo $getTimeSheet[0]->id;
							echo '<br />';
							echo $getTimeSheet[0]->daydate;*/

							if( !empty($getTimeSheet) ) {

								//var_dump($getTimeSheet[0]->clocking_status);

								if( in_array($getTimeSheet[0]->clocking_status, array('clock_in_1', 'clock_in_2')) ) {
									
									$clockingStatus = '<span class="label label-success" style="padding: 2px 13px; font-size: 11px;">in</span>';

								} elseif( $getTimeSheet[0]->clocking_status === 'clock_in_3' ) {

									$clockingStatus = '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">in</span>';

								} else {

									$clockingStatus = '<span class="label label-default" style="padding: 2px 4px; font-size: 11px;">open</span>';

								}

							}
						?>										
						{{ Form::open(array('url' => '/admin/emailNotify', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}
							<?php if ( !empty($getSchedule) ) { ?>
								
								<?php
								$scheduled['start_time'] = $getSchedule[0]->start_time;
								$scheduled['end_time'] = $getSchedule[0]->end_time;			
								?>
								<tr>
								<td class="check-column">{{ Form::checkbox('check[]', $employee->id, false, array('id' => 'cb-select-'.$employee->id, 'class' => 'checkbox')) }}</td>
								<td><?php  echo $employee->firstname.', '.$employee->lastname; ?></td>
								<td>
								<?php if( !empty($company) ) { ?>
									<?php  echo $company[0]->name; ?>
								<?php } ?>
								</td>
								<td>
								<?php if( !empty($department) ) { ?>
									<?php  echo $department[0]->name; ?>
								<?php } ?>
								</td>
								<td>
								<?php if( !empty($manager) ) { ?>
									<?php  echo $manager[0]->firstname.', '.$manager[0]->lastname; ?>
								<?php } ?>
								</td>
								<td>
								<?php if( !empty($supervisor) ) { ?>
									<?php  echo $supervisor[0]->firstname.', '.$supervisor[0]->lastname; ?>
								<?php } ?>
								</td>								
								<td><?php  echo $clockingStatus; ?></td>
								<td style="text-align: center"><a href="{{ URL::to('/admin/user/' . $employee->id . '/edit/') }}">Edit</a></td>
								<td><a href="{{ URL::to('/admin/payroll/masterfile/' . $employee->id) }}">Masterfile</a></td>
								</tr>
							
							<?php }elseif( !empty($getWorkShiftByDayOfTheWeek) ) { ?>

								<?php
								$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek[0]->start_time;
								$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek[0]->end_time;		
								?>
								<tr>
								<td class="check-column">{{ Form::checkbox('check[]', $employee->id, false, array('id' => 'cb-select-'.$employee->id, 'class' => 'checkbox')) }}</td>
								<td>
									<?php  echo $employee->firstname.', '.$employee->lastname; ?>
								</td>
								<td>
								<?php if( !empty($company) ) { ?>
									<?php  echo $company[0]->name; ?>
								<?php } ?>
								</td>								
								<td>
								<?php if( !empty($department) ) { ?>
									<?php  echo $department[0]->name; ?>
								<?php } ?>
								</td>
								<td>
								<?php if( !empty($manager) ) { ?>
									<?php  echo $manager[0]->firstname.', '.$manager[0]->lastname; ?>
								<?php } ?>
								</td>
								<td>
								<?php if( !empty($supervisor) ) { ?>
									<?php  echo $supervisor[0]->firstname.', '.$supervisor[0]->lastname; ?>
								<?php } ?>
								</td>									
								<td><?php  echo $clockingStatus; ?></td>
								<td style="text-align: center"><a href="{{ URL::to('/admin/user/' . $employee->id . '/edit/') }}">Edit</a></td>
								<td><a href="{{ URL::to('/admin/payroll/masterfile/'  . $employee->id) }}">Masterfile</a></td>
								</tr>

							<?php } else { ?>
								<tr>
								<td class="check-column">{{ Form::checkbox('check[]', $employee->id, false, array('id' => 'cb-select-'.$employee->id, 'class' => 'checkbox')) }}</td>
								<td><?php echo $employee->firstname.', '.$employee->lastname; ?></td>
								<td>
								<?php if( !empty($company) ) { ?>
									<?php  echo $company[0]->name; ?>
								<?php } ?>
								</td>								
								<td>
								<?php if( !empty($department) ) { ?>
									<?php  echo $department[0]->name; ?>
								<?php } ?>
								</td>
								<td>
								<?php if( !empty($manager) ) { ?>
									<?php  echo $manager[0]->firstname.', '.$manager[0]->lastname; ?>
								<?php } ?>
								</td>
								<td>
								<?php if( !empty($supervisor) ) { ?>
									<?php  echo $supervisor[0]->firstname.', '.$supervisor[0]->lastname; ?>
								<?php } ?>
								</td>									
								<td><?php echo $clockingStatus; ?></td>
								<td style="text-align: center"><a href="{{ URL::to('/admin/user/' . $employee->id . '/edit/') }}">Edit</a></td>
								<td><a href="{{ URL::to('/admin/payroll/masterfile/'  . $employee->id) }}">Masterfile</a></td>
								</tr>

							<?php } ?> 
							

						<?php endforeach; ?>
							<tr><td colspan="8">{{ Form::submit('Email', array('class' => '', 'class' => 'btn btn-primary')) }}</td></tr>								
						 </form>
						</tbody>
						</table>			

						<nav class="pull-right"><?php echo $employees->links(); ?></nav>							    


					  </div><!--/.pane-body-->        
					</div><!--/.panel-->
				</div>

			</div><!--/.row-->            

             

          </div><!--//#content .col-md-9-->          

        </div><!--//.row-->

      </div><!--//.page-container-->

@stop
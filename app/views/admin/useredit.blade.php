@extends('layouts.admin.default')

@section('content')

<?php

$employeeEditId = $employeeEditInfo[0]->id;

$message = Session::get('message');

//echo 'employeeEdit:'.$employeeEditId;
//exit;

$companies = Company::all();
$departments = Department::all();
$jobTitles = JobTitle::all();

$managers = Employee::all();
$supervisors = Employee::all();

$roles = DB::table('groups')->get();

if ( isset($employeeEditId) && !empty($employeeEditId) ) {
  $userEdit = DB::table('users')->where('employee_id', $employeeEditId)->first();  
  
  $payEdit = DB::table('employee_setting')->where('employee_id', $employeeEditId)->first();
}

if( isset($userEdit) && !empty($userEdit) ) {
  $userGroupEdit = DB::table('users_groups')->where('user_id', $userEdit->id)->first(); 
}

if( isset($userGroupEdit) && !empty($userGroupEdit) ) {
  $groupEdit = DB::table('groups')->where('id', (int) $userGroupEdit->group_id)->first(); 
}

$companyArr[0] = '';

if( !empty($companies) ) {
  foreach ($companies as $company) {

      $companyArr[$company->id] = $company->name;

  }
}

$departmentArr[0] = '';

if( !empty($departments) ) {
  foreach ($departments as $department) {

      $departmentArr[$department->id] = $department->name;

  }
}

$jobTitleArr[0] = '';

if( !empty($jobTitles) ) {
  foreach ($jobTitles as $jobTitle) {

      $jobTitleArr[$jobTitle->id] = $jobTitle->name;

  }
}

$managerArr[0] = '';

if( !empty($managers) ) {
  foreach ($managers as $manager) {

     $fullname = $manager->firstname.', '.$manager->lastname;

      $managerArr[$manager->id] = $fullname;

  }
}

$supervisorArr[0] = '';

if( !empty($supervisors) ) {
  foreach ($supervisors as $supervisor) {

     $fullname = $supervisor->firstname.', '.$supervisor->lastname;

      $supervisorArr[$supervisor->id] = $fullname;

  }
}

$roleArr[0] = '';

if( !empty($roles) ) {

  foreach($roles as $role) {

    //echo $role->name;
    $roleArr[$role->id] = $role->name;

  }

}



//Check type of Employee
if ( isset($employeeEditId) && !empty($employeeEditId) ) {

  $checkEmployeeType = DB::table('employees')->select('employee_type')->where('id', $employeeEditId)->first();

  //dd($checkEmployeeType->employee_type);

  if ( isset($checkEmployeeType) && !empty($checkEmployeeType) ) {

    if ( $checkEmployeeType->employee_type === 1 ) { //is_manager
    
      $isManager = true;      
    
    } else {

       $isManager = '';

    }

    if ( $checkEmployeeType->employee_type === 2 ) { //is_supervisor
    
       $isSupervisor = true;  
    
    } else {

       $isSupervisor = '';

    }

    if ( $checkEmployeeType->employee_type === 0 ) { //is_employee
    
       $isEmployee = true;
    
    } else {

       $isEmployee = '';

    }

  }

}

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
                      <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-cogs fa-lg"></span>
                      <span class="sidebar-nav-item">Admin</span>
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

            <h1 class="page-header">Edit</h1>

        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading" style="font-size:14px; font-weight:bold;">Edit Employee</div>
          <div class="panel-body">

            @if ($errors->has())
            <div class="alert alert-danger" role="alert">
                <ul style="list-style:none; margin:0; padding:0;">
                    @foreach ($errors->all() as $error)
                        <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Error:</span>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @elseif ( !empty($message) )
                <div class="alert alert-success" role="alert">
                    <ul style="list-style:none; margin:0; padding:0;">
                        
                        <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon" aria-hidden="true"></span> <span class="sr-only">Success:</span>{{ $message }}</li>

                    </ul>
                </div>
            @endif 

            {{-- Form::open(array('url' => '/admin/user/edit', 'id' => '', 'class' => 'form-horizontal')) --}}              
            {{-- Form::open( array('route' => array('adminUserProcessUpdate'), 'id' => '', 'class' => 'form-horizontal') ) --}}              


            {{ Form::open(array('url' => '/admin/user/'.$employeeEditId.'/edit', 'id' => '', 'class' => 'form-horizontal')) }}              

            {{ Form::hidden('employee_id',  $employeeEditInfo[0]->id); }}
            <!--div class="form-group hide hidden">
                {{ Form::label('Designation', 'Designation', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('designation', array(0 => '', 1 => 'Manager', 2 => 'Supervisor', 3 => 'Employee'), 0, array('id' => 'designation', 'class' => 'form-control')) }}                
                </div>
            </div-->             

            <div class="form-group">
                {{ Form::label('employe_number', 'Employee Number', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::text('employee_number', $employeeEditInfo[0]->employee_number, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Employee Number')) }}
                </div>
            </div>            

            <div class="form-group">           
                {{ Form::label('firstname', 'Firstname', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('firstname', $employeeEditInfo[0]->firstname, array('id' => '', 'class' => 'form-control', 'placeholder' => 'First name')) }}            
                </div>                        
            </div>            

            <div class="form-group">           
                {{ Form::label('lastname', 'Lastname', array('class' => 'col-sm-2 control-label')) }}            
                <div class="col-sm-3">            
                {{ Form::text('lastname', $employeeEditInfo[0]->lastname, array('id' => '', 'class' => 'form-control', 'placeholder' => 'last name')) }}            
                </div>            
            </div>            
            

            
            <div class="form-group">           
                {{ Form::label('middlename', 'Middlename', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('middlename', $employeeEditInfo[0]->middle_name, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Middle name')) }}            
                </div>                        
            </div>            
            
            <div class="form-group">           
                {{ Form::label('nick_name', 'Nick name', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">           
                {{ Form::text('nick_name', $employeeEditInfo[0]->nick_name, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Nick name')) }}            
                </div>                        
            </div>

            <div class="form-group">
                {{ Form::label('Company', 'Company', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('company_id', $companyArr, $employeeEditInfo[0]->company_id, array('id' => '', 'class' => 'form-control')) }}
                </div>
            </div>            

            <div class="form-group">
                {{ Form::label('Department/Campaign', 'Department/Campaign', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('department_id', $departmentArr, $employeeEditInfo[0]->department_id, array('id' => '', 'class' => 'form-control')) }}                
                </div>
            </div>

            <div class="form-group">
              {{ Form::label('Type of Employee', 'Type of Employee', array('class' => 'col-sm-2 control-label')) }}                         
              <div class="checkbox">                
                  <label for="Is Employee">
                  {{ Form::radio('is_employee_type', 'is_employee', $isEmployee, array('id' => 'is-employee-type')) }}
                  Is Employee
                  </label>    

                  <label for="Is Manager">
                  {{ Form::radio('is_employee_type', 'is_manager', $isManager, array('id' => 'is-employee-type')) }}
                  Is Manager
                  </label>    

                  <label for="Is Supervisor">
                  {{ Form::radio('is_employee_type', 'is_supervisor', $isSupervisor, array('id' => 'is-employee-type')) }}
                  Is Supervisor
                  </label> 
                  
              </div>   
            </div>                        

            <div class="form-group department-head-form-group">

                {{ Form::label('Department Head', 'Department Head', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('department_head', $managerArr, $employeeEditInfo[0]->manager_id, array('id' => 'department-head', 'class' => 'form-control')) }}                
                </div>
            </div>            

            <div class="form-group supervisor-form-group">
                {{ Form::label('Supervisor', 'Supervisor', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('supervisor_id', $supervisorArr, $employeeEditInfo[0]->supervisor_id, array('id' => 'supervisor', 'class' => 'form-control')) }}                
                </div>
            </div>            

            <div class="form-group">
                {{ Form::label('Job Title', 'Job Title', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('position_id', $jobTitleArr, $employeeEditInfo[0]->position_id, array('id' => '', 'class' => 'form-control')) }}                
                </div>
            </div> 

            <div class="form-group">
                {{ Form::label('Role', 'Role', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('role_id', $roleArr, $groupEdit->id, array('id' => '', 'class' => 'form-control')) }}                
                </div>
            </div>                       

            <div class="form-group">           
                {{ Form::label('email', 'Email', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">           
                {{ Form::text('email', $userEdit->email, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Email')) }}            
                </div>                        
            </div>            

            <div class="form-group">           
                {{ Form::label('New Password', 'New Password', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::password('password', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Password')) }}            
                </div>                        
            </div>   

            <div class="form-group">           
                {{ Form::label('Confirm New Password', 'Confirm New Password', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::password('password_confirmation', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Confirm Password')) }}            
                </div>                        
            </div>                           
            		
			<hr>
			
			<!-- for employee settings -->
			<div class="form-group">           
                {{ Form::label('Basic Monthly Rate', 'Basic Monthly Rate', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('basicmonthly', $payEdit->basicmonthly, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Basic Monthly Rate')) }}            
                </div>                        
            </div>
			
			<div class="form-group">           
                {{ Form::label('Daily Rate', 'Daily Rate', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('dailysal', $payEdit->dailysal, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Daily Rate')) }}            
                </div>                        
            </div>
			
			<div class="form-group">           
                {{ Form::label('Basic Pay', 'Basic Pay', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('basicpay', $payEdit->basicpay, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Email')) }}            
                </div>                        
            </div>
			
			<hr>
			
			<div class="form-group">           
                {{ Form::label('Ecola', 'Ecola', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('ecola', $payEdit->ecola, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Ecola')) }}            
                </div>                        
            </div>
			
			<div class="form-group">           
                {{ Form::label('Deminimis', 'Deminimis', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('deminimis', $payEdit->deminimis, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Deminimis')) }}            
                </div>                        
            </div>
			
			<hr>
			
			<div class="form-group">           
                {{ Form::label('Tax Exemption', 'Tax Exemption', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ 	Form::select('tax_status', array('S' => 'Single', 'M' => 'Married', 'S1' => 'Single w/ 1 Dependent', 'S2' => 'Single w/ 2 Dependents', 'S3' => 'Single w/ 3 Dependents', 'S4' => 'Single w/ 4 Dependents', 'M1' => 'Married w/ 1 Dependent', 'M2' => 'Married w/ 2 Dependents', 'M3' => 'Married w/ 3 Dependents', 'M4' => 'Married w/ 4 Dependents'), $payEdit->tax_status, ['class' => 'form-control'])}}            
                </div>                        
            </div>
                               

            <div class="col-sm-offset-2 col-sm-10">
                <div class="col-sm-3">                        
                {{ Form::submit('Update User', array('class' => '', 'class' => 'btn btn-primary')) }}
                </div>                                        
            </div>            
            
          </div>        
        </div>           

          </div><!--//#content .col-md-9-->          

        </div><!--//.row-->

      </div><!--//.page-container-->

@stop
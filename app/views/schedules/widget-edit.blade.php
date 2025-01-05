<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Edit User</h3>
  </div>  
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

    {{-- Form::open(array('url' => '/admin/user/'.$employeeEditId.'/edit', 'id' => '', 'class' => 'form-horizontal')) --}}              

    {{ Form::open(array('url' => '', 'id' => '', 'class' => 'form-horizontal')) }}              

      {{ Form::hidden('employee_id',  $employeeEditInfo[0]->id) }}
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
      
                         

      <div class="col-sm-offset-2 col-sm-10">
          <div class="col-sm-3">                        
          {{ Form::submit('Update User', array('class' => '', 'class' => 'btn btn-primary')) }}
          </div>                                        
      </div>
    {{ Form::close() }}           


  </div>
</div>
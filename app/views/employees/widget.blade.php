<div class="panel panel-default">
  <div class="panel-heading">
    
    <h3 class="panel-title">
      @if("admin.employees.show.user.new" === $resourceId)
        Add New User      
      @elseif("admin.employees.show.user.edit" === $resourceId)
        Edit User
      @endif      
    </h3>
    

  </div>  
  <div class="panel-body">
 <?php $message = Session::get('message') ?>

  @if ($errors->has())
  <div class="alert alert-danger" role="alert">
      <ul style="list-style:none; margin:0; padding:0;">
          @foreach ($errors->all() as $error)
              <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Error:</span>{{ $error }}</li>
          @endforeach
      </ul>
  </div>
  @endif
  
  @if ( !empty($message) )
      <div class="alert alert-success" role="alert">
          <ul style="list-style:none; margin:0; padding:0;">
              
              <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon" aria-hidden="true"></span> <span class="sr-only">Success:</span>{{ $message }}</li>

          </ul>
      </div>
  @endif
  
  @if("admin.employees.show.user.new" === $resourceId)  
  
    {{-- Form::open(array('route' => array('process.user.new'), 'method' => 'post', 'id' => 'formUserNew', 'class' => 'form-horizontal')) --}}    
    {{ Form::open(array('url' => '/admin/user/new', 'id' => 'formUserNew', 'class' => 'form-horizontal')) }}                  
    
    <!--div class="form-group hide hidden">
        {{ Form::label('Designation', 'Designation', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::select('designation', array(0 => '', 1 => 'Manager', 2 => 'Supervisor', 3 => 'Employee'), 0, array('id' => 'designation', 'class' => 'form-control')) }}                
        </div>
    </div-->             
                
    <div class="form-group">
        {{ Form::label('employe_number', 'Employee Number', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::text('employee_number', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Employee Number')) }}
        </div>
    </div>            

    <div class="form-group">           
        {{ Form::label('firstname', 'Firstname', array('class' => 'col-sm-2 control-label')) }}
        <div class="col-sm-3">            
        {{ Form::text('firstname', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'First name')) }}            
        </div>                        
    </div>            

    <div class="form-group">           
        {{ Form::label('lastname', 'Lastname', array('class' => 'col-sm-2 control-label')) }}            
        <div class="col-sm-3">            
        {{ Form::text('lastname', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'last name')) }}            
        </div>            
    </div>            
    

    
    <div class="form-group">           
        {{ Form::label('middlename', 'Middlename', array('class' => 'col-sm-2 control-label')) }}
        <div class="col-sm-3">            
        {{ Form::text('middlename', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Middle name')) }}            
        </div>                        
    </div>            
    
    <div class="form-group">           
        {{ Form::label('nick_name', 'Nick name', array('class' => 'col-sm-2 control-label')) }}
        <div class="col-sm-3">           
        {{ Form::text('nick_name', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Nick name')) }}            
        </div>                        
    </div>

    <div class="form-group">
        {{ Form::label('Company', 'Company', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::select('company_id', $companyArr, 0, array('id' => '', 'class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('Department/Campaign', 'Department/Campaign', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::select('department_id', $departmentArr, 0, array('id' => '', 'class' => 'form-control')) }}                
        </div>
    </div>      

    <div class="form-group">
      {{ Form::label('Type of Employee', 'Type of Employee', array('class' => 'col-sm-2 control-label')) }}                         
      <div class="checkbox">                
          <label for="Is Employee">
          {{ Form::radio('is_employee_type', 'is_employee', true, array('id' => 'is-employee-type')) }}
          Is Employee
          </label>    

          <label for="Is Manager">
          {{ Form::radio('is_employee_type', 'is_manager', false, array('id' => 'is-employee-type')) }}
          Is Manager
          </label>    

          <label for="Is Supervisor">
          {{ Form::radio('is_employee_type', 'is_supervisor', false, array('id' => 'is-employee-type')) }}
          Is Supervisor
          </label> 
          
      </div>   
    </div>          

    <!--div class="form-group">
      {{ Form::label('Type of Employee', 'Type of Employee', array('class' => 'col-sm-2 control-label')) }}                         
      <div class="checkbox">                
          <label for="Is Manager">
          {{ Form::checkbox('is_employee_type', 'is_manager', false, array('id' => 'is-employee-type')) }}
          Is Manager
          </label>    

      </div>   
    </div-->            

    <div class="form-group department-head-form-group">

        {{ Form::label('Department Head', 'Department Head', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::select('department_head', $managerArr, 0, array('id' => 'department-head-new', 'class' => 'form-control')) }}                
        </div>
    </div>

    <div class="form-group supervisor-form-group">
        {{ Form::label('Supervisor', 'Supervisor', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::select('supervisor_id', $supervisorArr, 0, array('id' => 'supervisor-new', 'class' => 'form-control')) }}                
        </div>
    </div>             

    <div class="form-group">
        {{ Form::label('Job Title', 'Job Title', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::select('position_id', $jobTitleArr, 0, array('id' => 'position', 'class' => 'form-control')) }}                
        </div>
    </div>       


    <div class="form-group">
        {{ Form::label('Role', 'Role', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::select('role_id', $roleArr, 0, array('id' => '', 'class' => 'form-control')) }}                
        </div>
    </div>                   

    <div class="form-group">           
        {{ Form::label('email', 'Email', array('class' => 'col-sm-2 control-label')) }}
        <div class="col-sm-3">           
        {{ Form::text('email', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Email')) }}            
        </div>                        
    </div>            
    
    <div class="form-group">           
        {{ Form::label('password', 'Password', array('class' => 'col-sm-2 control-label')) }}
        <div class="col-sm-3">            
        {{ Form::password('password', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Password')) }}            
        </div>                        
    </div>   

    <div class="form-group">           
        {{ Form::label('Confirm password', 'Confirm Password', array('class' => 'col-sm-2 control-label')) }}
        <div class="col-sm-3">            
        {{ Form::password('password_confirmation', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Confirm Password')) }}            
        </div>                        
    </div>     	<hr>				<!-- for employee settings -->	<div class="form-group">               		{{ Form::label('Basic Monthly Rate', 'Basic Monthly Rate', array('class' => 'col-sm-2 control-label')) }}                <div class="col-sm-3">            		{{ Form::text('basicmonthly', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Basic Monthly Rate')) }}            		</div>   		    </div>				<div class="form-group">               		{{ Form::label('Daily Rate', 'Daily Rate', array('class' => 'col-sm-2 control-label')) }}                <div class="col-sm-3">            		{{ Form::text('dailysal', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Daily Rate')) }}                            		</div>                                    	</div>				<div class="form-group">               		{{ Form::label('Basic Pay', 'Basic Pay', array('class' => 'col-sm-2 control-label')) }}                <div class="col-sm-3">            		{{ Form::text('basicpay', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Basic Pay')) }}            		</div>                        		</div>				<hr>				<div class="form-group">                           		{{ Form::label('Ecola', 'Ecola', array('class' => 'col-sm-2 control-label')) }}         		<div class="col-sm-3">                            {{ Form::text('ecola', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Ecola')) }}            		</div>                        	</div>				<div class="form-group">           	        {{ Form::label('Deminimis', 'Deminimis', array('class' => 'col-sm-2 control-label')) }}                <div class="col-sm-3">            		{{ Form::text('deminimis', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Deminimis')) }}            		</div>                        		</div>				<hr>				<div class="form-group">               		{{ Form::label('Tax Exemption', 'Tax Exemption', array('class' => 'col-sm-2 control-label')) }}                <div class="col-sm-3">            		{{ 	Form::select('tax_status', array('S' => 'Single', 'M' => 'Married', 'S1' => 'Single w/ 1 Dependent', 'S2' => 'Single w/ 2 Dependents', 'S3' => 'Single w/ 3 Dependents', 'S4' => 'Single w/ 4 Dependents', 'M1' => 'Married w/ 1 Dependent', 'M2' => 'Married w/ 2 Dependents', 'M3' => 'Married w/ 3 Dependents', 'M4' => 'Married w/ 4 Dependents'), 'S', ['class' => 'form-control'])}}                            		</div>                                	</div>

    <!--<div class="form-group">           
        {{ Form::label('Role', 'Role', array('class' => 'col-sm-2 control-label')) }}
        <div class="col-sm-3">           
        {{ Form::select('role', array('1' => 'Employee', '3' => 'Administrator', '4' => 'Supervisor', '7' => 'HR User'), '1', array('class' => 'form-control')) }}            
        </div>                        
    </div-->                                 

    <div class="col-sm-offset-2 col-sm-10">
        <div class="col-sm-3">                        
        {{ Form::submit('Add User', array('class' => '', 'class' => 'btn btn-primary')) }}
        </div>                                        
    </div>            
    
    {{ Form::close() }}   
    
  @elseif("admin.employees.show.user.edit" === $resourceId)

    {{ Form::open(array('url' => '/admin/user/'.$employeeEditId.'/edit', 'id' => '', 'class' => 'form-horizontal')) }}              

  {{ Form::hidden('employee_id',  $employeeEditInfo->id) }}
  <!--div class="form-group hide hidden">
      {{ Form::label('Designation', 'Designation', array('class' => 'col-sm-2 control-label')) }}           
      <div class="col-sm-3">
      {{ Form::select('designation', array(0 => '', 1 => 'Manager', 2 => 'Supervisor', 3 => 'Employee'), 0, array('id' => 'designation', 'class' => 'form-control')) }}                
      </div>
  </div-->             

  <div class="form-group">
      {{ Form::label('employe_number', 'Employee Number', array('class' => 'col-sm-2 control-label')) }}           
      <div class="col-sm-3">
      {{ Form::text('employee_number', $employeeEditInfo->employee_number, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Employee Number')) }}
      </div>
  </div>            

  <div class="form-group">           
      {{ Form::label('firstname', 'Firstname', array('class' => 'col-sm-2 control-label')) }}
      <div class="col-sm-3">            
      {{ Form::text('firstname', $employeeEditInfo->firstname, array('id' => '', 'class' => 'form-control', 'placeholder' => 'First name')) }}            
      </div>                        
  </div>            

  <div class="form-group">           
      {{ Form::label('lastname', 'Lastname', array('class' => 'col-sm-2 control-label')) }}            
      <div class="col-sm-3">            
      {{ Form::text('lastname', $employeeEditInfo->lastname, array('id' => '', 'class' => 'form-control', 'placeholder' => 'last name')) }}            
      </div>            
  </div>            
  

  
  <div class="form-group">           
      {{ Form::label('middlename', 'Middlename', array('class' => 'col-sm-2 control-label')) }}
      <div class="col-sm-3">            
      {{ Form::text('middlename', $employeeEditInfo->middle_name, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Middle name')) }}            
      </div>                        
  </div>            
  
  <div class="form-group">           
      {{ Form::label('nick_name', 'Nick name', array('class' => 'col-sm-2 control-label')) }}
      <div class="col-sm-3">           
      {{ Form::text('nick_name', $employeeEditInfo->nick_name, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Nick name')) }}            
      </div>                        
  </div>

  <div class="form-group">
      {{ Form::label('Company', 'Company', array('class' => 'col-sm-2 control-label')) }}           
      <div class="col-sm-3">
      {{ Form::select('company_id', $companyArr, $employeeEditInfo->company_id, array('id' => '', 'class' => 'form-control')) }}
      </div>
  </div>            

  <div class="form-group">
      {{ Form::label('Department/Campaign', 'Department/Campaign', array('class' => 'col-sm-2 control-label')) }}           
      <div class="col-sm-3">
      {{ Form::select('department_id', $departmentArr, $employeeEditInfo->department_id, array('id' => '', 'class' => 'form-control')) }}                
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
      {{ Form::select('department_head', $managerArr, $employeeEditInfo->manager_id, array('id' => 'department-head', 'class' => 'form-control')) }}                
      </div>
  </div>            

  <div class="form-group supervisor-form-group">
      {{ Form::label('Supervisor', 'Supervisor', array('class' => 'col-sm-2 control-label')) }}           
      <div class="col-sm-3">
      {{ Form::select('supervisor_id', $supervisorArr, $employeeEditInfo->supervisor_id, array('id' => 'supervisor', 'class' => 'form-control')) }}                
      </div>
  </div>            

  <div class="form-group">
      {{ Form::label('Job Title', 'Job Title', array('class' => 'col-sm-2 control-label')) }}           
      <div class="col-sm-3">
      {{ Form::select('position_id', $jobTitleArr, $employeeEditInfo->position_id, array('id' => '', 'class' => 'form-control')) }}                
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
  </div>  	<hr>				<!-- for employee settings -->				<div class="form-group">               		{{ Form::label('Basic Monthly Rate', 'Basic Monthly Rate', array('class' => 'col-sm-2 control-label')) }}                <div class="col-sm-3">             		{{ Form::text('basicmonthly', $payEdit->basicmonthly, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Basic Monthly Rate')) }}                            </div>                        	</div>				<div class="form-group">                   		{{ Form::label('Daily Rate', 'Daily Rate', array('class' => 'col-sm-2 control-label')) }}                <div class="col-sm-3">            		{{ Form::text('dailysal', $payEdit->dailysal, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Daily Rate')) }}            		</div>                        	</div>					<div class="form-group">           		{{ Form::label('Basic Pay', 'Basic Pay', array('class' => 'col-sm-2 control-label')) }}                <div class="col-sm-3">                            		{{ Form::text('basicpay', $payEdit->basicpay, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Email')) }}                            </div>                        	</div>				<hr>				<div class="form-group">                           		{{ Form::label('Ecola', 'Ecola', array('class' => 'col-sm-2 control-label')) }}                		<div class="col-sm-3">                            {{ Form::text('ecola', $payEdit->ecola, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Ecola')) }}            		</div>                        		</div>					<div class="form-group">               		{{ Form::label('Deminimis', 'Deminimis', array('class' => 'col-sm-2 control-label')) }}                <div class="col-sm-3">                  		{{ Form::text('deminimis', $payEdit->deminimis, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Deminimis')) }}                            </div>                        	</div>				<hr>				<div class="form-group">               		{{ Form::label('Tax Exemption', 'Tax Exemption', array('class' => 'col-sm-2 control-label')) }}                <div class="col-sm-3">            		{{ 	Form::select('tax_status', array('S' => 'Single', 'M' => 'Married', 'S1' => 'Single w/ 1 Dependent', 'S2' => 'Single w/ 2 Dependents', 'S3' => 'Single w/ 3 Dependents', 'S4' => 'Single w/ 4 Dependents', 'M1' => 'Married w/ 1 Dependent', 'M2' => 'Married w/ 2 Dependents', 'M3' => 'Married w/ 3 Dependents', 'M4' => 'Married w/ 4 Dependents'), $payEdit->tax_status, ['class' => 'form-control'])}}            		</div>                        	</div>	
  <div class="col-sm-offset-2 col-sm-10">
      <div class="col-sm-3">                        
      {{ Form::submit('Update User', array('class' => '', 'class' => 'btn btn-primary')) }}
      </div>                                        
  </div>

      
    {{ Form::close() }} 

  @endif

  </div>
</div>
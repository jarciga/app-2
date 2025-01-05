<div class="row">
    <div class="col-sm-8 col-md-offset-2 col-md-8 col-md-offset-2 main">        

        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Leave Form</h3>
          </div>  
          <div class="panel-body">

         <?php $message = Input::get('message') ?>

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

            {{ Form::open(array('url' => '/admin/leave/request', 'id' => 'formUserLeave', 'class' => 'form-horizontal')) }}                

            {{ Form::hidden('employee_id', $currentUserId) }}

            {{ Form::hidden('company_id', $companyId) }}
            {{ Form::hidden('department_id', $departmentId) }}
            {{ Form::hidden('position_id', $jobTitleId); }}            
            {{ Form::hidden('manager_id', $managerId) }}
            {{ Form::hidden('supervisor_id', $supervisorId) }}

            <div class="form-group hide hidden">
                {{-- Form::label('Company', 'Company', array('class' => 'col-sm-2 control-label')) --}}           
                <div class="col-sm-4">
                {{-- Form::select('company_id', $companyArr, 0, array('id' => '', 'class' => 'form-control')) --}}
                </div>
            </div>            

            <div class="form-group hide hidden">
                {{-- Form::label('Department/Campaign', 'Department/Campaign', array('class' => 'col-sm-2 control-label')) --}}           
                <div class="col-sm-4">
                {{-- Form::select('department_id', $departmentArr, 0, array('id' => '', 'class' => 'form-control')) --}}                
                </div>
            </div>

            <div class="form-group hide hidden">
                {{-- Form::label('Job Title', 'Job Title', array('class' => 'col-sm-2 control-label')) --}}           
                <div class="col-sm-4">
                {{-- Form::select('position_id', $jobTitleArr, 0, array('id' => '', 'class' => 'form-control')) --}}                
                </div>
            </div>  

            <div class="form-group hide hidden">
                {{-- Form::label('Department Head', 'Department Head', array('class' => 'col-sm-2 control-label')) --}}           
                <div class="col-sm-4">
                {{-- Form::select('department_head', $managerArr, 0, array('id' => '', 'class' => 'form-control')) --}}                
                </div>
            </div>                                   

            <h5>Nature of Leave Application</h5>
            
            <div class="form-group">            
                <div class="checkbox">                
                    <label for="Vacation Leave">
                    {{ Form::radio('nature_of_leave', 'Vacation Leave') }}
                    Vacation Leave
                    </label>    

                    <label for="Sick Leave">
                    {{ Form::radio('nature_of_leave', 'Sick Leave') }}
                    Sick Leave
                    </label> 

                    <label for="Maternity/Paternity Leave">
                    {{ Form::radio('nature_of_leave', 'Maternity/Paternity Leave') }}
                    Maternity/Paternity Leave
                    </label>                       
                </div>

                <div class="checkbox">                                    
                    <label for="Others">
                    {{ Form::radio('nature_of_leave', 'others') }}
                    Others
                    </label>                   
                </div> 

            </div>                                      

            <div id="other-nature-of-leave" class="form-group hide hidden">
                {{ Form::label('Other Nature of Leave', 'Other Nature of Leave', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-4">
                {{ Form::text('other_nature_of_leave', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Other Nature of Leave')) }}
                </div>
            </div>                                     

            <div class="form-group"> 
                <div class="checkbox">                
                    <label for="With Pay">
                    {{ Form::radio('with_pay', '1') }}
                    With Pay
                    </label> 
                    <label for="Without Pay">
                    {{ Form::radio('with_pay', '0') }}
                    Without Pay
                    </label>                   
                </div> 
            </div>     

            <div class="form-group">
                {{ Form::label('Number of Day/s', 'Number of Day/s', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-4">
                {{ Form::text('number_of_days', '', array('id' => '', 'class' => 'form-control  ', 'placeholder' => 'Number of days')) }}
                </div>
            </div>   

            <div class="form-group">
                {{ Form::label('From', 'From', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-4">
                {{ Form::text('from', '', array('id' => '', 'class' => 'form-control datepicker', 'placeholder' => '')) }}
                </div>
            </div>   

            <div class="form-group">
                {{ Form::label('To', 'To', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-4">
                {{ Form::text('to', '', array('id' => '', 'class' => 'form-control datepicker', 'placeholder' => '')) }}
                </div>
            </div>            


            <div class="form-group">
                {{ Form::label('Reason/s', 'Reason/s', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-8">
                {{ Form::textarea('reasons', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Reason/s')) }}
                </div>
            </div>            

            <div class="form-group">
                <div class="col-sm-10">
                    <div class="col-sm-8">                        
                    {{ Form::submit('File Leave', array('class' => '', 'class' => 'btn btn-primary')) }}
                    </div>                                        
                </div>
            </div>

            {{ Form::close() }}   


          </div>
        </div>

    </div>
</div>
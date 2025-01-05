<div class="panel panel-default">
  <div class="panel-heading">
    
    <h3 class="panel-title">
      @if("admin.departments.show.department.new" === $resourceId)
        Add New User      
      @elseif("admin.departments.show.department.edit" === $resourceId)
        Edit User
      @endif      
    </h3>
    

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
  @endif

  {{-- @elseif ( !empty($message) ) --}}
      <div class="alert alert-success hide hidden" role="alert">
          <ul style="list-style:none; margin:0; padding:0;">
              
              <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon" aria-hidden="true"></span> <span class="sr-only">Success:</span>{{-- $message --}}</li>

          </ul>
      </div>
  {{-- @endif --}}
  
  @if("admin.departments.show.department.new" === $resourceId)  
  
    {{ Form::open(array('url' => '/admin/department/new', 'id' => '', 'class' => 'form-inline')) }}                
   
    <div class="form-group">
      {{ Form::label('Department Name', 'Department Name', array('class' => 'sr-only')) }}           
      {{ Form::text('department_name', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Department Name')) }}
    </div>            
      {{ Form::submit('Create', array('class' => '', 'class' => 'btn btn-custom-default')) }}
    </div>            
    
    {{ Form::close() }}    
    
  @elseif("admin.departments.show.department.edit" === $resourceId)

            {{ Form::open(array('url' => '/admin/department/'.$departmentEditId.'/edit', 'id' => '', 'class' => 'form-inline')) }}              
            {{ Form::hidden('Department_id', $departmentEditId); }}
            <div class="form-group">
              {{ Form::label('Department Name', 'Department Name', array('class' => 'sr-only')) }}           
              {{ Form::text('department_name', $departmentArr->name, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Department Name')) }}
            </div>            
              {{ Form::submit('Update', array('class' => '', 'class' => 'btn btn-custom-default')) }}
            </div>            
            
            {{ Form::close() }}  
    

  @endif

  </div>
</div>
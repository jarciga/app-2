<div class="panel panel-default">
  <div class="panel-heading">
    
    <h3 class="panel-title">
      @if("admin.companies.show.company.new" === $resourceId)
        Add New User      
      @elseif("admin.companies.show.company.edit" === $resourceId)
        Edit User
      @endif      
    </h3>
    

  </div>  
  <div class="panel-body">
  {{-- $message = Session::get('message') --}}

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
  
  @if("admin.companies.show.company.new" === $resourceId)  
  
    {{ Form::open(array('url' => '/admin/company/new', 'id' => '', 'class' => 'form-inline')) }}                   
    <div class="form-group">
      {{ Form::label('Company Name', 'Company Name', array('class' => 'sr-only')) }}           
      {{ Form::text('company_name', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Company Name')) }}
    </div>            
      {{ Form::submit('Create Company', array('class' => '', 'class' => 'btn btn-custom-default')) }}
    </div>            
    
    {{ Form::close() }}

  @elseif("admin.companies.show.company.edit" === $resourceId)


    {{ Form::open(array('url' => '/admin/company/'.$companyEditId.'/edit', 'id' => '', 'class' => 'form-inline')) }}              
    {{ Form::hidden('company_id', $companyEditId); }}
    <div class="form-group">
      {{ Form::label('Company Name', 'Company Name', array('class' => 'sr-only')) }}           
      {{ Form::text('company_name', $companyArr->name, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Company Name')) }}
    </div>            
      {{ Form::submit('Update Company', array('class' => '', 'class' => 'btn btn-custom-default')) }}
    </div>            
    
    {{ Form::close() }}  
    

  @endif

  </div>
</div>
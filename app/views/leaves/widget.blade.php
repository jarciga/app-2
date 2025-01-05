<div class="panel panel-default">
  <div class="panel-heading">
    
    <h3 class="panel-title">
      @if("admin.leaves.show.leave.new" === $resourceId)
        Add New User      
      @elseif("admin.leaves.show.leave.edit" === $resourceId)
        Edit User
      @endif      
    </h3>
    

  </div>  
  <div class="panel-body">
  {{ $message = Session::get('message') }}

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
  
  @if("admin.leaves.show.leave.new" === $resourceId)  
  
  //new

  @elseif("admin.leaves.show.leave.edit" === $resourceId)

  //edit    

  @endif

  </div>
</div>
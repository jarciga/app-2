<div class="panel panel-default">
  <div class="panel-heading">
    
    <h3 class="panel-title">
      @if("admin.holidays.show.holiday.new" === $resourceId)
        Add New User      
      @elseif("admin.holidays.show.holiday.edit" === $resourceId)
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

  @elseif ( !empty($message) )
      <div class="alert alert-success" role="alert">
          <ul style="list-style:none; margin:0; padding:0;">
              
              <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon" aria-hidden="true"></span> <span class="sr-only">Success:</span>{{ $message }}</li>

          </ul>
      </div>
  @endif
  
  @if("admin.holidays.show.holiday.new" === $resourceId)  
  
    {{-- Form::open(array('route' => array('process.holiday.new'), 'method' => 'post', 'id' => 'formHolidayNew', 'class' => 'form-horizontal')) --}}    
    {{ Form::open(array('url' => '/admin/holiday/new', 'id' => 'formHolidayNew', 'class' => 'form-horizontal')) }}                  
                
    <div class="form-group">
        {{ Form::label('description', 'Description', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::text('description', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Description')) }}
        </div>
    </div>            

    <div class="form-group">
        {{ Form::label('holiday_type', 'Holiday Type', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::select('holiday_type', array('Regular holiday' => 'Regular holiday', 'Special non-working day' => 'Special non-working day'), 0, array('id' => '', 'class' => 'form-control')) }}
        </div>
    </div>           

    <div class="form-group">           
        {{ Form::label('holiday_date_from', 'Holiday Date From', array('class' => 'col-sm-2 control-label')) }}            
        <div class="col-sm-3">            
        {{ Form::text('holiday_date_from', '', array('id' => '', 'class' => 'form-control datepicker', 'placeholder' => 'Holiday Date From')) }}            
        </div>            
    </div>            
    
    <div class="form-group">           
        {{ Form::label('holiday_date_to', 'Holiday Date To', array('class' => 'col-sm-2 control-label')) }}
        <div class="col-sm-3">            
        {{ Form::text('holiday_date_to', '', array('id' => '', 'class' => 'form-control datepicker', 'placeholder' => 'Holiday Date To')) }}            
        </div>                        
    </div>            

    <div class="form-group">
      {{ Form::label('is_active', 'Is Active', array('class' => 'col-sm-2 control-label')) }}                         
      <div class="checkbox">  
        <label for="Is Active">                        
          {{ Form::checkbox('is_active', 1, true, array('id' => '')) }}  
        </label>
      </div>   
    </div>            
    
    <div class="col-sm-offset-2 col-sm-10">
        <div class="col-sm-3">                        
        {{ Form::submit('Add Holiday', array('class' => '', 'class' => 'btn btn-primary')) }}
        </div>                                        
    </div>            
    
  {{ Form::close() }}   
    
  @elseif("admin.holidays.show.holiday.edit" === $resourceId)

    {{ Form::open(array('url' => '/admin/holiday/'.$holidayEditId.'/edit', 'id' => '', 'class' => 'form-horizontal')) }}              

   <div class="form-group">
        {{ Form::label('description', 'Description', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::text('description', $holidayArr->description, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Description')) }}
        </div>
    </div>            

    <div class="form-group">
        {{ Form::label('holiday_type', 'Holiday Type', array('class' => 'col-sm-2 control-label')) }}           
        <div class="col-sm-3">
        {{ Form::select('holiday_type', array('Regular holiday' => 'Regular holiday', 'Special non-working day' => 'Special non-working day'), $holidayArr->holiday_type, array('id' => '', 'class' => 'form-control')) }}
        </div>
    </div>           

    <div class="form-group">           
        {{ Form::label('holiday_date_from', 'Holiday Date From', array('class' => 'col-sm-2 control-label')) }}            
        <div class="col-sm-3">            
        {{ Form::text('holiday_date_from', $holidayArr->holiday_date_from, array('id' => '', 'class' => 'form-control datepicker', 'placeholder' => 'Holiday Date From')) }}            
        </div>            
    </div>            
    
    <div class="form-group">           
        {{ Form::label('holiday_date_to', 'Holiday Date To', array('class' => 'col-sm-2 control-label')) }}
        <div class="col-sm-3">            
        {{ Form::text('holiday_date_to', $holidayArr->holiday_date_to, array('id' => '', 'class' => 'form-control datepicker', 'placeholder' => 'Holiday Date To')) }}            
        </div>                        
    </div>            

    <div class="form-group">
      {{ Form::label('is_active', 'Is Active', array('class' => 'col-sm-2 control-label')) }}                         
      <div class="checkbox">  
          <label for="Is Active">  

          <?php if ($holidayArr->holiday_status === 1) { ?>

            {{ Form::checkbox('is_active', 1, true, array('id' => '')) }}

          <?php }elseif ($holidayArr->holiday_status === 0) { ?>

            {{ Form::checkbox('is_active', 0, false, array('id' => '')) }}

          <?php } ?>
            
          </label>      
      </div>   
    </div>    

    <div class="col-sm-offset-2 col-sm-10">
        <div class="col-sm-3">                        
        {{ Form::submit('Update User', array('class' => '', 'class' => 'btn btn-primary')) }}
        </div>                                        
    </div>

  @endif

  {{ Form::close() }}   

  </div>
</div>
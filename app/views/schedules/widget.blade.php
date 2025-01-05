<div class="panel panel-default">
  <div class="panel-heading">
    
    <h3 class="panel-title">
      @if("admin.schedules.show.schedule.uploader" === $resourceId)
        Upload New Schedule/Update New Schedule
      @elseif("admin.schedules.show.schedule.default" === $resourceId)        
        New Default Schedule/Update Default Schedule
      @elseif("admin.schedules.show.schedule.edit" === $resourceId)
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
  
  @if("admin.schedules.show.schedule.uploader" === $resourceId)  
  
    
    {{-- Form::open(array('url' => '/admin/schedule/new', 'id' => 'formScheduleNew', 'class' => 'form-horizontal')) --}}                  
    
    {{-- Form::close() --}}   

    <h4>Upload New Schedule</h4>
    {{ Form::open(array('url'=>'/admin/schedule/uploaded','files'=>true, 'method'=>'POST')) }}

    <div class="form-group">                    
    {{-- Form::label('file','Upload New Schedule',array('id'=> '', 'class' => 'col-sm-3 control-label')) --}} 
    {{ Form::file('file','',array('id' => '', 'class' => 'form-control')) }}    
    </div>

    <div class="form-group">
    {{ Form::submit('Save', array('class' => '', 'class' => 'btn btn-primary')) }}
    {{ Form::reset('Reset', array('class' => '', 'class' => 'btn btn-primary')) }}
    </div>

    {{ Form::close() }}

    <hr>
    <h4 style="font-family: "Times New Roman", Times, serif;">Update Schedule</h4>

    {{ Form::open(array('url' => '/admin/schedule/search')) }}

      {{ Form::label('Employee Number', 'Employee Number'); }}
      {{ Form::text('employee_number', ''); }}

      {{ Form::label('Date From', 'Date From') }}
      {{ Form::text('schedule_date_from', '', array('class' => 'datepicker')) }}
      {{ Form::label('Date To', 'Date to') }}
      {{ Form::text('schedule_date_to', '', array('class' => 'datepicker')) }}

      {{ Form::submit('Search', array('class' => '', 'class' => 'btn btn-primary')) }}                

    {{ Form::close() }}


    <?php if( !empty($uploadedSchedules) ) { ?>

      
    {{ Form::open(array('route' => 'process.schedule.edit', 'id' => '', 'class' => 'form-horizontal')) }}                          

    <div class="form-group pull-right">
        <div class="col-md-12">
        {{ Form::submit('Update', array('class' => '', 'class' => 'btn btn-primary')) }}          
        </div>
    </div>                 
                
    <table class="table table-striped table-hover table-list display" cellspacing="0" width="100%">

            <thead>
                <tr>
                    <th>Schedule Date</th>
                    <th class="hide hidden">Shift</th>                
                    <th>Rest day</th>
                    <!--th>Hours per day</th-->                
                    <th>Start time</th>
                    <th>Start Date</th>
                    <th>End time</th>  
                    <th>End Date</th>                                              
                </tr>
            </thead>

            <tbody>            
                  
            <?php

                foreach($uploadedSchedules as $key => $uploadedSchedule) {

                  list($startDate, $startTime) = explode(' ', $uploadedSchedule->start_time);
                  list($endDate, $endTime) = explode(' ', $uploadedSchedule->end_time);

                  list($starttimehh, $starttimemm) = explode(':', trim($startTime));
                  list($endtimehh, $endtimemm) = explode(':', trim($endTime));                            

            ?>

                <tr>
                    <td>                                  
                      <?php echo Form::hidden('schedule['.$key.'][uploadedScheduleId]',  $uploadedSchedule->id); ?>
                      <?php echo Form::hidden('schedule['.$key.'][employeeId]',  $uploadedSchedule->employee_id); ?>
                      <strong><?php echo date("D, F j Y", strtotime($uploadedSchedule->schedule_date)); ?></strong>
                      <?php //echo Form::text('schedule['.$key.'][scheduledate]', $uploadedSchedule->schedule_date, array('readonly' => 'readonly')); ?>
                    </td>
                    <!--td>Shift</td-->                
                    <td class="hide hidden"><?php echo Form::select('schedule['.$key.'][shift]', array(1 => 1, 2 => 2), $uploadedSchedule->shift, array()); ?></td>                                             
                    <td><?php echo Form::select('schedule['.$key.'][restday]', array(0 => 'No', 1 => 'Yes'), $uploadedSchedule->rest_day, array()); ?></td>                
                    <!--td>Hours per day</td-->                
                    <td>
                    <?php
                        echo Form::select(
                                 'schedule['.$key.'][starttimehh]',
                                  array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                       '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                      ),
                                 $starttimehh,
                                 array()
                             );
                    ?>
                    
                    <?php
                        echo Form::select(
                                 'schedule['.$key.'][starttimemm]',
                                 array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                       11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                       20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                       30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                       40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                       50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                       60 => '60'),
                                 $starttimemm,
                                 array()
                             );
                    ?>
                    </td>
                    <td>                                
                      <?php 
                      //echo Form::hidden('schedule['.$key.'][startdate]', $startDate);
                      echo Form::text('schedule['.$key.'][startdate]', $startDate, array('class' => 'datepicker'));
                      ?>
                      
                      <strong><?php //echo $startDate; ?></strong>
                    </td>                             
                    <td>
                    <?php
                        echo Form::select(
                                 'schedule['.$key.'][endtimehh]',
                                  array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                       '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                      ),
                                 $endtimehh,
                                 array()
                             );
                    ?>
                    
                    <?php
                        echo Form::select(
                                 'schedule['.$key.'][endtimemm]',
                                 array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                       11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                       20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                       30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                       40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                       50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                       60 => '60'),
                                 $endtimemm,
                                 array()
                             );
                    ?>
                    </td>                                                
                    <td>                                                                  
                      <?php 
                      //echo Form::hidden('schedule['.$key.'][enddate]', $endDate);
                      echo Form::text('schedule['.$key.'][enddate]', $endDate, array('class' => 'datepicker'));
                      ?>
                      
                      <strong><?php //echo $endDate; ?></strong>                                  
                    </td>                             
                </tr>

                <?php } ?>                            
          
            </tbody>

    </table>
    {{-- $uploadedSchedules->links() --}}
    <div class="form-group pull-right">
        <div class="col-md-12">
        {{ Form::submit('Update', array('class' => '', 'class' => 'btn btn-primary')) }}          
        </div>
    </div> 

    {{ Form::close() }}
                

    <?php } ?>              

  @elseif("admin.schedules.show.schedule.default" === $resourceId)

    {{ Form::open(array('url' => '/admin/schedule/default/search')) }}

      {{ Form::label('Employee Number', 'Employee Number'); }}
      {{ Form::text('employee_number', ''); }}

      {{ Form::submit('Search', array('class' => '', 'class' => 'btn btn-primary')) }}                

    {{ Form::close() }}  

    <?php 
    if( !empty($searchEmployeeId) ) {
    //if( !empty($defaultSchedules) && count($defaultSchedules) !== 0 ) {
    ?>

    {{ Form::open(array('url' => '/admin/schedule/default', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}            
    

    {{-- Form::hidden('search_employee_id',  $defaultScheduled->employee_id) --}}     

    <div class="form-group pull-right">
        <div class="col-md-12">
        {{ Form::submit('Update Default Schedule', array('class' => '', 'class' => 'btn btn-primary')) }}          
        </div>
    </div>       

    <table class="table table-striped table-hover display" cellspacing="0" width="100%">

        <thead>
            <tr>
                <th>Name of Day From</th>
                <th>Name of Day To</th>
                <!--th>Shift</th-->                
                <th>Rest day</th>
                <!--th>Hours per day</th-->                
                <th>Start time</th>
                <th>End time</th>                                                
            </tr>
        </thead>

        <tbody>                     
            <tr>
                <td>
                  {{-- $employeeId[0] --}}
                  <?php echo Form::hidden('schedule[0][employeeId]',  $employeeId[0]); ?>                  
                  <label for="Monday">Monday</label><?php echo Form::hidden('schedule[0][nameofdayfrom]', 'Monday'); ?></td>
                <td>                
                {{ Form::select('schedule[0][nameofdayto]', [
                   'Monday' => 'Monday',
                   'Tuesday' => 'Tuesday'], $nameOfDayTo[0]) }}
                </td>
                <!--td>Shift</td-->                
                <td><?php echo Form::select('schedule[0][restday]', array(0 => 'No', 1 => 'Yes'), $restDay[0], array()); ?></td>                
                <!--td>Hours per day</td-->                
                <td>
                <?php
                    echo Form::select(
                             'schedule[0][starttimehh]',
                              array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                   '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                  ),
                             //'08',
                             $starttimehh[0],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[0][starttimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'00',
                             $starttimemm[0],
                             array()
                         );
                ?>
                </td>
                <td>
                <?php
                    echo Form::select(
                             'schedule[0][endtimehh]',
                              array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                   '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                  ),
                             //'17',
                             $endtimehh[0],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[0][endtimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'00',
                             $endtimemm[0],
                             array()
                         );
                ?>
                </td>                                                
            </tr>


            <tr>
                <td>
                  {{-- $employeeId[1] --}}
                  <?php echo Form::hidden('schedule[1][employeeId]',  $employeeId[1]); ?>                  
                  <label for="Tuesday">Tuesday</label><?php echo Form::hidden('schedule[1][nameofdayfrom]', 'Tuesday'); ?></td>
                <td>
                {{ Form::select('schedule[1][nameofdayto]', [
                   'Tuesday' => 'Tuesday',
                   'Wednesday' => 'Wednesday'], $nameOfDayTo[1]) }}
                </td>                
                <!--td>Shift</td-->                
                <td><?php echo Form::select('schedule[1][restday]', array(0 => 'No', 1 => 'Yes'), $restDay[1], array()); ?></td>                
                <!--td>Hours per day</td-->                
                <td>
                <?php
                    echo Form::select(
                             'schedule[1][starttimehh]',
                              array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                   '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                  ),
                             //'08',
                             $starttimehh[1],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[1][starttimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'00',
                             $starttimemm[1],
                             array()
                         );
                ?>
                </td>
                <td>
                <?php
                    echo Form::select(
                             'schedule[1][endtimehh]',
                              array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                   '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                  ),
                             //'17',
                             $endtimehh[1],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[1][endtimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'00',
                             $endtimemm[1],
                             array()
                         );
                ?>
                </td>                                                
            </tr>


            <tr>
                <td>
                  {{-- $employeeId[2] --}}
                  <?php echo Form::hidden('schedule[2][employeeId]',  $employeeId[2]); ?>                  
                  <label for="Wednesday">Wednesday</label><?php echo Form::hidden('schedule[2][nameofdayfrom]', 'Wednesday'); ?></td>
                <td>
                {{ Form::select('schedule[2][nameofdayto]', [
                   'Wednesday' => 'Wednesday',
                   'Thursday' => 'Thursday'], $nameOfDayTo[2]) }}
                </td>                                
                <!--td>Shift</td-->                
                <td><?php echo Form::select('schedule[2][restday]', array(0 => 'No', 1 => 'Yes'), $restDay[2], array()); ?></td>                
                <!--td>Hours per day</td-->                
                <td>
                <?php
                    echo Form::select(
                             'schedule[2][starttimehh]',
                              array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                   '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                  ),
                             //'08',
                             $starttimehh[2],                             
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[2][starttimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'00',
                             $starttimemm[2],
                             array()
                         );
                ?>
                </td>
                <td>
                <?php
                    echo Form::select(
                             'schedule[2][endtimehh]',
                              array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                   '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                  ),
                             //'17',
                             $endtimehh[2],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[2][endtimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'00',
                             $endtimemm[2],
                             array()
                         );
                ?>
                </td>                                                
            </tr>


            <tr>
                <td>
                  {{-- $employeeId[3] --}}
                  <?php echo Form::hidden('schedule[3][employeeId]',  $employeeId[3]); ?>                  
                  <label for="Thursday">Thursday</label><?php echo Form::hidden('schedule[3][nameofdayfrom]', 'Thursday'); ?></td>
                <td>
                {{ Form::select('schedule[3][nameofdayto]', [
                   'Thursday' => 'Thursday',
                   'Friday' => 'Friday'], $nameOfDayTo[3]) }}
                </td>                                                
                <!--td>Shift</td-->                
                <td><?php echo Form::select('schedule[3][restday]', array(0 => 'No', 1 => 'Yes'), $restDay[3], array()); ?></td>                
                <!--td>Hours per day</td-->                
                <td>
                <?php
                    echo Form::select(
                             'schedule[3][starttimehh]',
                              array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                   '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                  ),
                             //'08',
                             $starttimehh[3],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[3][starttimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'00',
                             $starttimemm[3],
                             array()
                         );
                ?>
                </td>
                <td>
                <?php
                    echo Form::select(
                             'schedule[3][endtimehh]',
                              array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                   '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                  ),
                             //'17',
                             $endtimehh[3],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[3][endtimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'00',
                             $endtimemm[3],
                             array()
                         );
                ?>
                </td>                                                
            </tr>


            <tr>
                <td>
                  {{-- $employeeId[4] --}}
                  <?php echo Form::hidden('schedule[4][employeeId]',  $employeeId[4]); ?>                  
                  <label for="Friday">Friday</label><?php echo Form::hidden('schedule[4][nameofdayfrom]', 'Friday'); ?></td>
                <td>
                {{ Form::select('schedule[4][nameofdayto]', [
                   'Friday' => 'Friday',
                   'Saturday' => 'Saturday'], $nameOfDayTo[4]) }}
                </td>                                 
                <!--td>Shift</td-->                
                <td><?php echo Form::select('schedule[4][restday]', array(0 => 'No', 1 => 'Yes'), $restDay[4], array()); ?></td>                
                <!--td>Hours per day</td-->                
                <td>
                <?php
                    echo Form::select(
                             'schedule[4][starttimehh]',
                            array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                 '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                ),
                             //'08',
                             $starttimehh[4],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[4][starttimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'00',
                             $starttimemm[4],
                             array()
                         );
                ?>
                </td>
                <td>
                <?php
                    echo Form::select(
                             'schedule[4][endtimehh]',
                            array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                 '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                ),
                             //'17',
                             $endtimehh[4],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[4][endtimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'00',
                             $endtimemm[4],
                             array()
                         );
                ?>
                </td>                                                
            </tr>


            <tr>
                <td>
                  {{-- $employeeId[5] --}}
                  <?php echo Form::hidden('schedule[5][employeeId]',  $employeeId[5]); ?>                  
                  <label for="Saturday">Saturday</label><?php echo Form::hidden('schedule[5][nameofdayfrom]', 'Saturday'); ?></td>
                <td>
                {{ Form::select('schedule[5][nameofdayto]', [
                   'Saturday' => 'Saturday',
                   'Sunday' => 'Sunday'], $nameOfDayTo[5]) }}
                </td>                                     
                <!--td>Shift</td-->                
                <td><?php echo Form::select('schedule[5][restday]', array(0 => 'No', 1 => 'Yes'), $restDay[5], array()); ?></td>                
                <!--td>Hours per day</td-->                
                <td>
                <?php
                    echo Form::select(
                             'schedule[5][starttimehh]',
                            array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                 '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                ),
                             //'',
                             $starttimehh[5],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[5][starttimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'',
                             $starttimemm[5],
                             array()
                         );
                ?>
                </td>
                <td>
                <?php
                    echo Form::select(
                             'schedule[5][endtimehh]',
                            array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                 '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                ),
                             //'',
                             $endtimehh[5],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[5][endtimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'',
                             $endtimemm[5],
                             array()
                         );
                ?>
                </td>                                                
            </tr>


            <tr>
                <td>
                  {{-- $employeeId[6] --}}
                  <?php echo Form::hidden('schedule[6][employeeId]',  $employeeId[6]); ?>                  
                  <label for="Sunday">Sunday</label><?php echo Form::hidden('schedule[6][nameofdayfrom]', 'Sunday'); ?></td>
                <td>
                {{ Form::select('schedule[6][nameofdayto]', [
                   'Sunday' => 'Sunday',
                   'Monday' => 'Monday'], $nameOfDayTo[6]) }}
                </td>                                                     
                <!--td>Shift</td-->                
                <td><?php echo Form::select('schedule[6][restday]', array(0 => 'No', 1 => 'Yes'), $restDay[6], array()); ?></td>                
                <!--td>Hours per day</td-->                
                <td>
                <?php
                    echo Form::select(
                             'schedule[6][starttimehh]',
                              array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                   '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                  ),
                             //'',
                             $starttimehh[6],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[6][starttimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'',
                             $starttimemm[6],
                             array()
                         );
                ?>
                </td>
                <td>
                <?php
                    echo Form::select(
                             'schedule[6][endtimehh]',
                              array('24' => '12 a.m.', '01' => '1 a.m.', '02' => '2 a.m.', '03' => '3 a.m.', '04' => '4 a.m.', '05' => '5 a.m.', '06' => '6 a.m.', '07' => '7 a.m.', '08' => '8 a.m.', '09' => '9 a.m.', 10 => '10 a.m.', 11 => '11 a.m.', 12 => '12 p.m.',
                                   '13' => '1 p.m.', '14' => '2 p.m.', '15' => '3 p.m.', '16' => '4 p.m.', '17' => '5 p.m.', '18' => '6 p.m.', '19' => '7 p.m.', '20' => '8 p.m.', '21' => '9 p.m.', '22' => '10 p.m.', '23' => '11 p.m.'
                                  ),
                             //'',
                             $endtimehh[6],
                             array()
                         );
                ?>
                
                <?php
                    echo Form::select(
                             'schedule[6][endtimemm]',
                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
                                   60 => '60'),
                             //'',
                             $endtimemm[6],
                             array()
                         );
                ?>
                </td>                                                
            </tr>            



        </tbody>

    </table>

    <div class="form-group pull-right">
        <div class="col-md-12">
        {{ Form::submit('Update Default Schedule', array('class' => '', 'class' => 'btn btn-primary')) }}          
        </div>
    </div> 

    {{ Form::close() }}       
    
    <?php } ?>          
    
  @elseif("admin.schedules.show.schedule.edit" === $resourceId)

 

  @endif  

  </div>
</div>
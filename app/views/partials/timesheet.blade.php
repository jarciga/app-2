<div class="row" style="margin-bottom:20px;">  
    <div class="col-md-6">

    <div class="hide hidden pull-right">
      <form method="GET" action="http://www.backofficephhosting.com/timesheet/admin/search/timesheet" accept-charset="UTF-8" id="searchTimesheetForm" class="form-inline search"> 
        <label for="Employees" class="sr-only">Employees</label>
        <select id="employee-id" class="form-control" name="employeeid"><option value="0"></option><option value="2">Richard, Lim</option><option value="3">Catherine Rose, Lor</option><option value="4">Jessie, Dayrit</option><option value="5">Ivy Lane, Opon</option><option value="6">Roneth, Tan</option><option value="7">Leonila, Hayahay</option><option value="8">Edmund, Thay</option><option value="9">Michelle, Prado</option><option value="10">Ricky, Punzalan</option><option value="11">Jacquiline, Mabini</option><option value="12">Argeine, Longhay</option><option value="13">Vivian, Estevez</option><option value="14">Edwin, Lapesura</option><option value="15">Carla, Villena</option><option value="16">Nestor, Ada</option><option value="17">Jerry, Arsolon</option><option value="18">Romnick, Belo</option><option value="19">Vyron Joule, Muños</option><option value="20">Michael, Sabanal</option><option value="21">Erick, Campos</option><option value="22">Voltaire, Bautista</option><option value="23">Jason, Pandili</option><option value="24">Ramil, Caraos</option><option value="25">Jovamar, Gercan</option><option value="33">Vesper, Sabanal</option><option value="39">Runilo, Ibaoc</option><option value="44">Moore, Narciso</option><option value="46">Felix, Torregosa</option><option value="48">Janella, Dondoyano</option><option value="49">Hyacinth Joy, Peñaflorida</option><option value="50">Mary Ann, Ursabia</option><option value="51">Kathleen Anne, Madrid</option><option value="52">Mark Joseph, Divina</option><option value="53">Marievel, Ibaoc</option></select>
        <input id="search-timesheet-btn" class="btn btn-custom-default" type="submit" value="Edit">
      </form>  
    </div>
  </div>

</div>

<div class="row">
  <div class="col-md-12"> 
    <div class="table-body-container table-responsive">
      <div class="table-head-container"></div><!--/.table-head-container-->

      <!--h3 style="font-size:14px; font-weight:bold;">My Timesheet</h3-->
      <div id="timesheet-message"><!--//Timesheet Mesage--></div>
      <table id="timesheet-ajax" class="timesheet table table-striped table-hover table-condensed display" cellspacing="0" width="100%">
      <thead>                             
        <tr style="background-color: #b32728; color:#dcdcdc; text-transform: uppercase;">
          <th class="hide hidden">ID</th>                 
          <th>Date</th>                 
          <th>Schedule</th>                   
          <th style="text-align:right;">Time<br />In&nbsp;1</th>
          <th>-</th>
          <th style="text-align:left;">Time<br />Out&nbsp;1</th>
          <th style="text-align:right;">Time<br />In&nbsp;2</th>
          <th>-</th>
          <th style="text-align:left;">Time<br />Out&nbsp;2</th>
          <th style="text-align:right;">Time<br />In&nbsp;3</th>
          <th>-</th>
          <th style="text-align:left;">Time<br />Out&nbsp;3</th>                            
          <th>Work<br />Hours</th>    
          <th>Total<br />Hours</th>     
          <th>Total<br />Overtime</th>   
          <th>Night<br />Diff</th>                                
          <th>Tardiness</th>                
          <th>Undertime</th>
          <th>OT&nbsp;Status</th>
        </tr>
      </thead>

      <tfoot class="hide hidden">
        <tr style="background-color: #b32728; color:#dcdcdc; text-transform: uppercase;">
          <th class="hide hidden">ID</th>                 
          <th>Date</th>                 
          <th>Schedule</th>                   
          <th style="text-align:right;">Time<br />In&nbsp;1</th>
          <th>-</th>
          <th style="text-align:left;">Time<br />Out&nbsp;1</th>
          <th style="text-align:right;">Time<br />In&nbsp;2</th>
          <th>-</th>
          <th style="text-align:left;">Time<br />Out&nbsp;2</th>
          <th style="text-align:right;">Time<br />In&nbsp;3</th>
          <th>-</th>
          <th style="text-align:left;">Time<br />Out&nbsp;3</th>                            
          <th>Work<br />Hours</th>    
          <th>Total<br />Hours</th>          
          <th>Total<br />Overtime</th>    
          <th>Night<br />Diff</th>                               
          <th>Tardiness</th>                
          <th>Undertime</th>
          <th>OT&nbsp;Status</th>
        </tr>
      </tfoot>
      <tbody>                  
      <?php foreach($timesheets as $timesheetVal) : ?>        
        <tr id="<?php echo $timesheetVal->id; ?>">
          <td class="timesheet-id-<?php echo $timesheetVal->id; ?> hide hidden"><?php //echo $timesheetVal->id; ?></td>                 
          <td class="timesheet-daydate-<?php echo $timesheetVal->id; ?>"><?php //echo $timesheetVal->daydate; ?></td>                 
          <td class="timesheet-schedule-<?php echo $timesheetVal->id; ?>"><?php //echo $timesheetVal->schedule_in . ' - ' . $timesheetVal->schedule_out; ?></td>                   
          <td class="edit-cell timesheet-in1-<?php echo $timesheetVal->id; ?>" style="text-align:right;"><?php
          /*if ( !empty($timesheetVal->time_in_1) ) {
               echo date('H:i', strtotime($timesheetVal->time_in_1));
          }*/
          ?></td>
          <td style="text-align:center;">-</td>
          <td class="edit-cell timesheet-out1-<?php echo $timesheetVal->id; ?>" style="text-align:left;"><?php
          /*if ( !empty($timesheetVal->time_out_1) ) {
               echo date('H:i', strtotime($timesheetVal->time_out_1));
          }*/
          ?></td>                      

          <td class="edit-cell timesheet-in2-<?php echo $timesheetVal->id; ?>" style="text-align:right;"><?php
          /*if ( !empty($timesheetVal->time_in_2) ) {
               echo date('H:i', strtotime($timesheetVal->time_in_2));
          }*/
          ?></td>
          <td style="text-align:center;">-</td>
          <td class="edit-cell timesheet-out2-<?php echo $timesheetVal->id; ?>" style="text-align:left;"><?php
          /*if ( !empty($timesheetVal->time_out_2) ) {
               echo date('H:i', strtotime($timesheetVal->time_out_2));
          }*/
          ?></td>
          <td class="edit-cell timesheet-in3-<?php echo $timesheetVal->id; ?>" style="text-align:right;"><?php
          /*if ( !empty($timesheetVal->time_in_3) ) {
               echo date('H:i', strtotime($timesheetVal->time_in_3));
          }*/
          ?></td>
          <td style="text-align:center;">-</td>
          <td class="edit-cell timesheet-out3-<?php echo $timesheetVal->id; ?>" style="text-align:left;"><?php
          /*if ( !empty($timesheetVal->time_out_3) ) {
               echo date('H:i', strtotime($timesheetVal->time_out_3));
          }*/
          ?></td>
          <td class="timesheet-workhours-<?php echo $timesheetVal->id; ?>"><?php //echo $timesheetVal->work_hours; ?></td>                           
          <td class="timesheet-totalhours-<?php echo $timesheetVal->id; ?>"><?php //echo $timesheetVal->total_hours; ?></td>          
          <td class="timesheet-totalovertime-<?php echo $timesheetVal->id; ?>"><?php //echo $timesheetVal->total_overtime; ?></td>
          <td class="timesheet-nightdifferential-<?php echo $timesheetVal->id; ?>"><?php //echo $timesheetVal->night_differential; ?></td-->                      
          <td class="timesheet-tardiness-<?php echo $timesheetVal->id; ?>"><?php //echo $timesheetVal->tardiness; ?></td>                
          <td class="timesheet-undertime-<?php echo $timesheetVal->id; ?>"><?php //echo $timesheetVal->undertime; ?></td>
          <td class="ot-status-btn timesheet-otstatus-<?php echo $timesheetVal->id; ?>" style="text-align:center;"><?php //echo $timesheetVal->overtime_status; ?></td>
        </tr>

      <?php endforeach; ?>
      </tbody>
      </table>   

        <div class="table-foot-container"></div><!--/.table-footer-container-->               

    <nav class="hide hidden pull-right">{{-- $timesheetVal->links() --}}</nav>

    </div><!--/.table-body-container--> 

  </div>
</div>
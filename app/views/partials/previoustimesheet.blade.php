<?php 

//dd($previousSearchTimesheets); 


/*foreach($previousSearchTimesheets as $previousSearchTimesheetVal) {

    echo $previousSearchTimesheetVal->daydate;
    echo '<br />';
}*/

?>
<div class="row" style="margin-bottom:20px; position:relative;">  
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

  {{ Form::open(array('route' => array('update.previous.timesheet'), 'method' => 'post', 'id' => 'formUpdatePreviousSearchTimesheet', 'class' => '')) }}
      <div style="position:absolute; top:-58px; right:15px;">{{ Form::submit("Update", array('id' => 'update-search-timesheet', 'class' => 'btn btn-custom-default')) }}</div>
      <?php echo Form::hidden('recordcount', count($previousSearchTimesheets) ); ?>
      {{-- count($previousSearchTimesheets) --}}
      <div class="table-body-container table-responsive clearfix" style="margin-top:0;">
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
            <th class="hide hiddn">OT&nbsp;Status</th>
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
            <th class="hide hiddn">OT&nbsp;Status</th>
          </tr>
        </tfoot>
        <tbody>                  
        <?php
        $ctr = 0;
        foreach($previousSearchTimesheets as $previousSearchTimesheetVal) :

         ?>              
          <tr id="<?php echo $previousSearchTimesheetVal->id; ?>" class="row-data" data-id="<?php echo $previousSearchTimesheetVal->id; ?>">
            <td class="timesheet-previous-id-<?php echo $previousSearchTimesheetVal->id; ?> hide hidden"><?php //echo $previousSearchTimesheetVal->id; ?></td>                 
            <td class="timesheet-previous-daydate-<?php echo $previousSearchTimesheetVal->id; ?>"><?php //echo $previousSearchTimesheetVal->daydate; ?></td>                 
            <td class="timesheet-previous-schedule-<?php echo $previousSearchTimesheetVal->id; ?>"><?php //echo $previousSearchTimesheetVal->schedule_in . ' - ' . $previousSearchTimesheetVal->schedule_out; ?></td>                   
            <td class="edit-cell timesheet-in1-<?php echo $previousSearchTimesheetVal->id; ?>" style="text-align:right;">

            <input type="text" id="timesheet-previous-row-in1-<?php echo $previousSearchTimesheetVal->id; ?>" placeholder="--:--" name="timesheetrowin1[in1][]" value="<?php echo !empty($previousSearchTimesheetVal->time_in_1) ? date('H:i', strtotime($previousSearchTimesheetVal->time_in_1)) : ''; ?>" style="text-align:right;" />          
            <?php echo  Form::hidden('timesheetrowin1[timesheetid][]', $previousSearchTimesheetVal->id); ?>
            <?php echo  Form::hidden('timesheetrowin1[daydate][]', $previousSearchTimesheetVal->daydate); ?>
            <?php echo  Form::hidden('timesheetrowin1[schedulein][]', $previousSearchTimesheetVal->schedule_in); ?>


           </td>
            <td style="text-align:center;">-</td>
            <td class="edit-cell timesheet-out1-<?php echo $previousSearchTimesheetVal->id; ?>" style="text-align:left;">

            <input type="text" id="timesheet-previous-row-out1-<?php echo $previousSearchTimesheetVal->id; ?>" placeholder="--:--" name="timesheetrowout1[out1][]" value="<?php echo !empty($previousSearchTimesheetVal->time_out_1) ? date('H:i', strtotime($previousSearchTimesheetVal->time_out_1)) : ''; ?>" style="text-align:left;" />          
            <?php echo  Form::hidden('timesheetrowout1[timesheetid][]', $previousSearchTimesheetVal->id); ?>
            <?php echo  Form::hidden('timesheetrowout1[daydate][]', $previousSearchTimesheetVal->daydate); ?>
            <?php echo  Form::hidden('timesheetrowout1[scheduleout][]', $previousSearchTimesheetVal->schedule_out); ?>

            </td>                      

            <td class="edit-cell timesheet-in2-<?php echo $previousSearchTimesheetVal->id; ?>" style="text-align:right;">

            <input type="text" id="timesheet-previous-row-in2-<?php echo $previousSearchTimesheetVal->id; ?>" placeholder="--:--" name="timesheetrowin2[in2][]" value="<?php echo !empty($previousSearchTimesheetVal->time_in_2) ? date('H:i', strtotime($previousSearchTimesheetVal->time_in_2)) : ''; ?>" style="text-align:right;" />          
            <?php echo  Form::hidden('timesheetrowin2[timesheetid][]', $previousSearchTimesheetVal->id); ?>
            <?php echo  Form::hidden('timesheetrowin2[daydate][]', $previousSearchTimesheetVal->daydate); ?>
            <?php echo  Form::hidden('timesheetrowin2[schedulein][]', $previousSearchTimesheetVal->schedule_in); ?>          
            
            </td>
            <td style="text-align:center;">-</td>
            <td class="edit-cell timesheet-out2-<?php echo $previousSearchTimesheetVal->id; ?>" style="text-align:left;">

            <input type="text" id="timesheet-previous-row-out2-<?php echo $previousSearchTimesheetVal->id; ?>" placeholder="--:--" name="timesheetrowout2[out2][]" value="<?php echo !empty($previousSearchTimesheetVal->time_out_2) ? date('H:i', strtotime($previousSearchTimesheetVal->time_out_2)) : ''; ?>" style="text-align:left;" />
            <?php echo  Form::hidden('timesheetrowout2[timesheetid][]', $previousSearchTimesheetVal->id); ?>
            <?php echo  Form::hidden('timesheetrowout2[daydate][]', $previousSearchTimesheetVal->daydate); ?>
            <?php echo  Form::hidden('timesheetrowout2[scheduleout][]', $previousSearchTimesheetVal->schedule_out); ?>

            </td>
            <td class="edit-cell timesheet-in3-<?php echo $previousSearchTimesheetVal->id; ?>" style="text-align:right;">
            
            <input type="text" id="timesheet-previous-row-in3-<?php echo $previousSearchTimesheetVal->id; ?>" placeholder="--:--" name="timesheetrowin3[in3][]" value="<?php echo !empty($previousSearchTimesheetVal->time_in_3) ? date('H:i', strtotime($previousSearchTimesheetVal->time_in_3)) : ''; ?>" style="text-align:right;" />          
            <?php echo  Form::hidden('timesheetrowin3[timesheetid][]', $previousSearchTimesheetVal->id); ?>
            <?php echo  Form::hidden('timesheetrowin3[daydate][]', $previousSearchTimesheetVal->daydate); ?>
            <?php echo  Form::hidden('timesheetrowin3[schedulein][]', $previousSearchTimesheetVal->schedule_in); ?>             
            
            </td>
            <td style="text-align:center;">-</td>
            <td class="edit-cell timesheet-out3-<?php echo $previousSearchTimesheetVal->id; ?>" style="text-align:left;">

            <input type="text" id="timesheet-previous-row-out3-<?php echo $previousSearchTimesheetVal->id; ?>" placeholder="--:--" name="timesheetrowout3[out3][]" value="<?php echo !empty($previousSearchTimesheetVal->time_out_3) ? date('H:i', strtotime($previousSearchTimesheetVal->time_out_3)) : ''; ?>" style="text-align:left;" />          
            <?php echo  Form::hidden('timesheetrowout3[timesheetid][]', $previousSearchTimesheetVal->id); ?>
            <?php echo  Form::hidden('timesheetrowout3[daydate][]', $previousSearchTimesheetVal->daydate); ?>
            <?php echo  Form::hidden('timesheetrowout3[scheduleout][]', $previousSearchTimesheetVal->schedule_out); ?>         
            
            </td>
            <td class="timesheet-previous-workhours-<?php echo $previousSearchTimesheetVal->id; ?>"><?php //echo $previousSearchTimesheetVal->work_hours; ?></td>                           
            <td class="timesheet-previous-totalhours-<?php echo $previousSearchTimesheetVal->id; ?>"><?php //echo $previousSearchTimesheetVal->total_hours; ?></td>          
            <td class="timesheet-previous-totalovertime-<?php echo $previousSearchTimesheetVal->id; ?>"><?php //echo $previousSearchTimesheetVal->total_overtime; ?></td>
            <td class="timesheet-previous-nightdifferential-<?php echo $previousSearchTimesheetVal->id; ?>"><?php //echo $previousSearchTimesheetVal->night_differential; ?></td-->                      
            <td class="timesheet-previous-tardiness-<?php echo $previousSearchTimesheetVal->id; ?>"><?php //echo $previousSearchTimesheetVal->tardiness; ?></td>                
            <td class="timesheet-previous-undertime-<?php echo $previousSearchTimesheetVal->id; ?>"><?php //echo $previousSearchTimesheetVal->undertime; ?></td>
            <td class="hide hiddn ot-status-btn timesheet-otstatus-<?php echo $previousSearchTimesheetVal->id; ?>" style="text-align:center;"><?php //echo $previousSearchTimesheetVal->overtime_status; ?></td>
          </tr>

        <?php endforeach; ?>
        </tbody>
        </table>   

          <div class="table-foot-container"></div><!--/.table-footer-container-->                     
      <nav class="pull-right">{{-- $previousSearchTimesheets->links() --}}</nav>
      </div><!--/.table-body-container--> 
      {{ Form::open() }}


  </div>
</div>
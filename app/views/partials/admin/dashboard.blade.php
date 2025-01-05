
<div class="row">
  <div class="col-md-12"> 
    
    <h3>Dashboard</h3>
    <br>			
	<div class="row">			
		<div class="col-md-12">						
			<?php 											
					/*disable first this module*/												
					/*$cutdate = date('Y-m-d');						
					$givenDate = date('Y-m-d', strtotime($cutdate));						
					$dDate = date("d", strtotime($givenDate));						
					
					if(($dDate >= 25) && ($dDate < 29)) 						
					{							
							$year = date("Y");							
							$month = date("m", strtotime($givenDate));							
							$startday = 11;							
							$date = new DateTime();							
							$date->setDate($year, $month, $startday);							
							$startdate = $date->format('Y-m-d');														
							$endday = 25;							
							
							$date2 = new DateTime();							
							$date2->setDate($year, $month, $endday);							
							$enddate = $date2->format('Y-m-d'); 														
							
							echo "<h3 class='panel-title'>Please CLICK HERE to submit timesheets for cutoff: " . $startdate . " - " . $enddate . "</h3>";
					}						
					elseif(($dDate >= 10) && ($dDate < 14)) 						
					{							
							$year = date("Y");							
							$month = date("m", strtotime($givenDate));							
							$last_month = $month-1%12;							
							$startday = 26;							
							$date = new DateTime();							
							$date->setDate($year, $last_month, $startday);							
							$startdate = $date->format('Y-m-d');															
							$endday = 10;							
							
							$date2 = new DateTime();							
							$date2->setDate($year, $month, $endday);							
							$enddate = $date2->format('Y-m-d');														
							
							echo "<h3 class='panel-title'>Please CLICK HERE to submit timesheets for cutoff: " . $startdate . " - " . $enddate . "</h3>";
					}						
					echo "<br>" */ ?>						
			</div>					
		</div>
    <div class="col-md-6">
    @include('employees.dashboard') 
    </div>   

    <div class="col-md-6">
    @include('leaves.dashboard')
    </div>

    <div class="col-md-6">
    @include('absences.widget')
    </div>

    <div class="col-md-6">
    @include('overtimes.dashboard')
    </div>

  </div>

</div>
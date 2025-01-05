<aside class="sidebar">
  <nav class="sidebar-nav">
    <ul id="menu">
      <li>
        <a href="{{ url('/admin') }}">
          <span class="sidebar-nav-item-icon fa fa-tachometer fa-lg"></span>                      
          <span class="sidebar-nav-item">Dashboard</span>                      
        </a>
        
      </li>
      <li>
        <a href="#">
          <span class="sidebar-nav-item-icon fa fa-users fa-lg"></span>                      
          <span class="sidebar-nav-item">Employees</span>                      
        </a>

        <ul class="submenu-1 collapse">
            <li><a href="{{ url('/admin/user/lists') }}">List of Employees</a></li>
            <li><a href="{{ url('/admin/user/new') }}">Add New Employee</a></li>
        </ul>                    
        
      </li>                  
      <li>
          <a href="{{ url('/admin/scheduling') }}">
          <span class="sidebar-nav-item-icon fa fa-calendar fa-lg"></span>
          <span class="sidebar-nav-item">Schedule</span>
          </a>
          <ul class="submenu-1 collapse">
            <li><a href="{{ url('/admin/holiday/lists') }}">List of Holidays</a></li>
            <?php
            if( !empty($groupName) ) :
              if ( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('HR')) === 0 ) :
            ?>            
            <li><a href="{{ url('/admin/holiday/new') }}">Add New Holiday</a></li>
            <?php
              endif;
            endif;
            ?>              

            <li><a href="{{ url('/admin/schedule/uploader') }}">Upload Schedule</a></li>
            <li><a href="{{ url('/admin/schedule/default') }}">Default Schedule</a></li>
          </ul>          
      </li>                  
      <li>
          <a href="#">
          <span class="sidebar-nav-item-icon fa fa-clock-o fa-lg"></span>
          <span class="sidebar-nav-item">TimeClock</span>
          </a>
          <!--ul class="submenu-1 collapse">
              <li><a href="{{ url('/admin/overtime/lists') }}">All Overtimes</a></li>                            
          </ul-->
      </li>
      <li>
          <a href="#">
          <span class="sidebar-nav-item-icon fa fa-folder-o fa-lg"></span>
          <span class="sidebar-nav-item">Requests</span>
          </a>
          <ul class="submenu-1">
              <li><a href="{{ url('/admin/overtime/lists') }}">Overtime</a></li>
              <li><a href="{{ url('/admin/leave/lists') }}">Leaves</a></li>              
          </ul>
      </li>
      <li>
          <a href="#">
          <span class="sidebar-nav-item-icon fa fa-bar-chart fa-lg"></span>
          <span class="sidebar-nav-item">Reports</span>
          </a>
          <ul class="submenu-1">
              <li><a href="{{ url('/summary/report/employee') }}">My Summary Report</a></li>
              <li><a href="{{ url('/summary/reports/employees?page=1') }}">Employees Summary Report</a></li>              
          </ul>
      </li>

      <?php	  		
							
					if( !empty($groupName) ) :
						if ( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ||
							strcmp(strtolower($groupName), strtolower('Payroll')) === 0 ) :
      ?>      

      <li>
          <a href="">
          <span class="sidebar-nav-item-icon fa fa-building fa-lg"></span>
          <span class="sidebar-nav-item">Payroll</span>
          </a>
          <ul class="submenu-1 collapse">
            <li><a href="{{ url('/admin/payroll') }}">Payroll Reminder</a></li>            
            <li><a href="{{ url('/admin/payroll/list') }}">Process Payroll</a></li>
            <li><a href="{{ url('/admin/payroll/payregister') }}">Payroll List</a></li>            
            <li><a href="{{ url('/admin/payroll/paylist') }}">Submit Timesheet</a></li>            
          </ul>                     
      </li>

      <?php			
			//endif;	  
        endif;
      endif;
      ?>        

      <?php
	  
		$cutdate = date('Y-m-d');		
		$givenDate = date('Y-m-d', strtotime($cutdate));		
		$dDate = date("d", strtotime($givenDate));			
		
		//if( (($dDate > 25) && ($dDate <= 29)) || (($dDate > 10) && ($dDate <= 14))) :
			if( !empty($groupName) ) :
				if ( strcmp(strtolower($groupName), strtolower('Manager')) === 0 ||
					strcmp(strtolower($groupName), strtolower('Supervisor')) === 0 ) :
      ?>      

				  <li>
					  <a href="">
					  <span class="sidebar-nav-item-icon fa fa-building fa-lg"></span>
					  <span class="sidebar-nav-item">Payroll</span>
					  </a>
					  <ul class="submenu-1 collapse">
						<li><a href="{{ url('/admin/payroll/paylist') }}">Submit Timesheet</a></li>            
					  </ul>                     
				  </li>

      <?php
				endif;
			endif;
		//endif;
      ?>              

      <li>
          <a href="">
          <span class="sidebar-nav-item-icon fa fa-building fa-lg"></span>
          <span class="sidebar-nav-item">Company</span>
          </a>
          <ul class="submenu-1 collapse">
            <li><a href="{{ url('/admin/company/lists') }}">List of Companies</a></li>

            <?php
            if( !empty($groupName) ) :
              if ( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('HR')) === 0 ) :
            ?>              
            <li><a href="{{ url('/admin/company/new') }}">Add New Company</a></li>
            <?php
              endif;
            endif;
            ?>               

            <li><a href="{{ url('/admin/department/lists') }}">List of Departments</a></li>

            <?php
            if( !empty($groupName) ) :
              if ( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('HR')) === 0 ) :
            ?>            
            <li><a href="{{ url('/admin/department/new') }}">Add New Department</a></li>
            <?php
              endif;
            endif;
            ?>               

            <li><a href="{{ url('/admin/jobtitle/lists') }}">List of Job Titles</a></li>

            <?php
            if( !empty($groupName) ) :
              if ( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('HR')) === 0 ) :
            ?>            
            <li><a href="{{ url('/admin/jobtitle/new') }}">Add New Job Title</a></li>
            <?php
              endif;
            endif;
            ?>               
          </ul>            
      </li>                  

      <li class="hide hidden">
          <a href="#">
          <span class="sidebar-nav-item-icon fa fa-cogs fa-lg"></span>
          <span class="sidebar-nav-item">Admin</span>
          </a>
      </li> 

      <?php
      if( !empty($groupName) ) :
        if ( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ||
            strcmp(strtolower($groupName), strtolower('HR')) === 0 ) :
      ?>                                                                                
      <li>
          <a href="#">
          <span class="sidebar-nav-item-icon fa fa-cogs fa-lg"></span>
          <span class="sidebar-nav-item">Settings</span>
          </a>
          <!--ul class="submenu-1 collapse hide hidden">
              <li><a href="#">item 0.1</a></li>
          </ul-->
      </li>
      <?php
        endif;
      endif;
      ?>                                                                                                    
    </ul>
  </nav>

</aside>   
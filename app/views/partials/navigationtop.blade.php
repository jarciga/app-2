    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><span>Welcome to</span> <strong><span>BackOffice</span> TimeTracker!</strong> BPO TimeTracker!</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav navbar-right">            
            <li class="hide hidden" style="color:white;"><a href="#">Welcome <?php //echo $employeeInfo[0]->firstname. ', ' .$employeeInfo[0]->lastname; ?></a></li>
            <li id="timesheet-link" class="active"><a href="{{ url('/timesheet') }}"><i class="fa fa-clock-o"></i> My Timesheet</a></li>
            <?php
            if( !empty($groupName) ) :

              if ( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('Manager')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('Supervisor')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('Payroll')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('HR')) === 0 ) :
            ?>

            <li><a href="{{ url('/admin') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
            
            <?php
              endif;
            endif;
            ?>    


            <li><a href="{{ URL::to('/logout') }}">Log Out <i class="fa fa-sign-out"></i></a></li>            
          </ul>

          <!--form method="GET" action="http://www.backofficephhosting.com/timesheet/admin/search/timesheet" accept-charset="UTF-8" id="searchTimesheetForm" class="hide hidden navbar-form navbar-right"> 
            <label for="Employees" class="sr-only">Employees</label>
            <select id="employee-id" class="form-control" name="employeeid"><option value="0"></option><option value="2">Richard, Lim</option><option value="3">Catherine Rose, Lor</option><option value="4">Jessie, Dayrit</option><option value="5">Ivy Lane, Opon</option><option value="6">Roneth, Tan</option><option value="7">Leonila, Hayahay</option><option value="8">Edmund, Thay</option><option value="9">Michelle, Prado</option><option value="10">Ricky, Punzalan</option><option value="11">Jacquiline, Mabini</option><option value="12">Argeine, Longhay</option><option value="13">Vivian, Estevez</option><option value="14">Edwin, Lapesura</option><option value="15">Carla, Villena</option><option value="16">Nestor, Ada</option><option value="17">Jerry, Arsolon</option><option value="18">Romnick, Belo</option><option value="19">Vyron Joule, Muños</option><option value="20">Michael, Sabanal</option><option value="21">Erick, Campos</option><option value="22">Voltaire, Bautista</option><option value="23">Jason, Pandili</option><option value="24">Ramil, Caraos</option><option value="25">Jovamar, Gercan</option><option value="33">Vesper, Sabanal</option><option value="39">Runilo, Ibaoc</option><option value="44">Moore, Narciso</option><option value="46">Felix, Torregosa</option><option value="48">Janella, Dondoyano</option><option value="49">Hyacinth Joy, Peñaflorida</option><option value="50">Mary Ann, Ursabia</option><option value="51">Kathleen Anne, Madrid</option><option value="52">Mark Joseph, Divina</option><option value="53">Marievel, Ibaoc</option></select>
            <input id="search-timesheet-btn" class="btn btn-custom-default" type="submit" value="Edit">
          </form-->
          {{-- dd(count($currentAbsencesPerCutoff)) --}}
            <?php
            if( !empty($groupName) ) :

              if ( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('Manager')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('Supervisor')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('Payroll')) === 0 ||
                  strcmp(strtolower($groupName), strtolower('HR')) === 0 ) : 
            ?>

                  {{-- Form::open(array('route' => array('process.search.timesheet'), 'method' => 'get', 'id' => 'searchTimesheetForm', 'class' => 'navbar-form navbar-right hide hidden')) --}}

                    
                    {{-- Form::label('Employees', 'Employees', array('class' => 'sr-only')) --}}
                    {{-- Form::select('employeeid', $employeeArr, '', array('id' => 'employee-id', 'class' => 'form-control')) --}}

                    {{-- Form::button('<i class="fa fa-search"></i>', array('id' => 'search-timesheet-btn', 'class' => 'btn btn-custom-default')) --}}
                    {{-- Form::submit('Edit', array('id' => 'search-timesheet-btn', 'class' => 'btn btn-custom-default')) --}}

                  {{-- Form::close() --}}

          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Edit Employee <span class="caret"></span></a>
              <ul class="dropdown-menu scrollable-menu">

                <?php
                if(!empty($employeeArr) && is_array($employeeArr)):
                  foreach($employeeArr as $key => $val):
                ?>
                <!--li><a href="{{ URL::to('/search/timesheet/' . $key) }}"><?php echo $val; ?></a></li-->
                <li><a href="{{ URL::to('/search/timesheet/' . $key) }}"><?php echo $val; ?></a></li>

                <?php 
                  endforeach;
                endif;
                ?>   

              </ul>
            </li>
          </ul>   

            <?php
              endif;
            endif;
            ?>            

        </div><!--/.nav-collapse -->
      </div>
    </nav>
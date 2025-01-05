<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Absences</h3>
  </div>  
  <div class="panel-body">
    
    {{-- dd($currentAbsencesPerCutoff) --}}
    

    <?php 
    for($i = 0; $i < sizeof($currentAbsencesPerCutoff); $i++) {    
      $employeeIdArr[] = $currentAbsencesPerCutoff[$i]["employeeId"];      
    }

    if (!empty($employeeIdArr)) { 
      
      $employeeWithAbsent = Employee::whereIn('id', array_unique($employeeIdArr))->paginate(10);
    
    }
    ?>

    <?php if( !empty($employeeWithAbsent) ): ?>
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th># of absence/s</th>      
            <th></th>
          </tr>
        </thead>

        <?php foreach( $employeeWithAbsent as $employeeWithAbsentVal ) : ?>

        <tr>
          <td>{{{ $employeeWithAbsentVal->id }}}</td>
          <td>{{{ $employeeWithAbsentVal->lastname }}} {{{ $employeeWithAbsentVal->firstname }}} </td>
          <td>
          <?php   
          $ctr = 0;
          for($i = 0; $i < sizeof($currentAbsencesPerCutoff); $i++) {

            if ( $employeeWithAbsentVal->id === $currentAbsencesPerCutoff[$i]["employeeId"] ) {

                $ctr++;          

            }

          }
          echo $ctr;

          ?>
          </td>      
          <td><a href="{{ url('/absent-lists', $employeeWithAbsentVal->id) }}" class="">View Details</a></td>
        </tr>


        <?php endforeach;  ?>

      </table>
      <?php echo $employeeWithAbsent->links(); ?>
    
    <?php else: ?>
      <h5>No Absence(s) yesterday</h5>
    <?php endif; ?>

  </div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Companies</h3>
  </div>  
  <div class="panel-body">

  <?php $message = Input::get('message') ?>

  @if ($errors->has())
  <div class="alert alert-danger" role="alert">
      <ul style="list-style:none; margin:0; padding:0;">
          @foreach ($errors->all() as $error)
              <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Error:</span>{{ $error }}</li>
          @endforeach
      </ul>
  </div>

  @elseif ( isset($message) )
      <div class="alert alert-success" role="alert">
          <ul style="list-style:none; margin:0; padding:0;">
              
              <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon" aria-hidden="true"></span> <span class="sr-only">Success:</span>{{ $message }}</li>

          </ul>
      </div>
  @endif


	{{ Form::open(array('route' => 'search.user.lists', 'id' => 'formCompanySearchLists', 'class' => 'form-horizontal hide hiden')) }}

		<div class="form-group">
		    {{ Form::label('Search', 'Search', array('class' => 'col-sm-3 control-label')) }}
		    <div class="col-sm-3">
		    {{ Form::text('s', '', array('id' => '', 'class' => 'form-control', 'placeholder' => '')) }}
		    </div>
		    <div class="col-sm-3">                        
	        {{ Form::submit('Search', array('class' => '', 'class' => 'btn btn-primary')) }}
	        </div>                                        
		</div>

	{{ Form::close() }}  	
 	
	<div class="tablenav top hide hidden">
		<div class="actions bulk-actions">
		  
		  <div class="form-group">
		    <label for="bulk-action-selector-top" class="screen-reader-text"></label>
		    
		    <div class="col-sm-3">
		      <select name="action" id="bulk-action-selector-top" class="form-control">
		        <option value="-1" selected="selected">Bulk Actions</option>
		        <option value="0" class="hide-if-no-js"></option>
		        <option value="1"></option>
		      </select>                      
		    </div>
		    <input type="submit" name="" id="doaction" class="btn btn-custom-default action" value="Apply" class="pull-right"> 
		  </div>

		</div>            
	</div>  	

<table class="table table-striped table-hover display list-table users" cellspacing="0" width="100%">
	<thead>
		<tr>		           				           		
       		<th>#</th>		           				           		
       		<th>Name</th>  
            <?php
            if( !empty($groupName) ) :
		        if ( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ||
		            strcmp(strtolower($groupName), strtolower('HR')) === 0 ) :
            ?>       		
			<th>Actions</th>
            <?php
              endif;
            endif;
            ?>   
		</tr>
	</thead>
	<tbody>

    	<?php
    	if ( $companyCount !== 0 ) :
    		foreach($listCompanies as $listCompany): 
    	?>

		<tr>		           				           		
       		<td>{{ $listCompany->id }}</td>		           				           		
       		<td>{{ $listCompany->name }}</td>      
            <?php
            if( !empty($groupName) ) :
		        if ( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ||
		            strcmp(strtolower($groupName), strtolower('HR')) === 0 ) :
            ?>        		                  		           				           
			<td><a href="{{ URL::to('/admin/company/' . $listCompany->id . '/edit/') }}">Edit</a></td>			
            <?php
              endif;
            endif;
            ?>  								           		                        	
		</tr>

		<?php		
			endforeach;
		endif;
		?>
    </tbody>
	<tfoot>
		<tr>		           				           		
       		<th>#</th>		           				           		
       		<th>Name</th>                        		           				           
            <?php
            if( !empty($groupName) ) :
		        if ( strcmp(strtolower($groupName), strtolower('Administrator')) === 0 ||
		            strcmp(strtolower($groupName), strtolower('HR')) === 0 ) :
            ?>       		
			<th>Actions</th>
            <?php
              endif;
            endif;
            ?>   						           		                        	
		</tr>
	</tfoot>
</table>    
    
    <?php echo $listCompanies->links(); ?>

  </div>
</div>
<div class="page-container">

        <div class="row" style="padding-bottom:20px;">

        	<div id="content" class="col-md-6 col-md-offset-3" role="main">

	            <ol class="breadcrumb hide hidden">
	              <li><a href="#">Home</a></li>
	              <li class="active">Page</li>
	            </ol>

            	<h1 class="page-header hide hidden">Installation</h1>

				<div class="panel panel-default">
					<!-- Default panel contents -->
					<div class="panel-heading" style="font-size:14px; font-weight:bold;"></div>
					<div class="panel-body">


			            @if ($errors->has())
			            <div class="alert alert-danger" role="alert">
			                <ul style="list-style:none; margin:0; padding:0;">
			                    @foreach ($errors->all() as $error)
			                        <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Error:</span>{{ $error }}</li>
			                    @endforeach
			                </ul>
			            </div>
			            @endif 						

            			{{ Form::open(array('url' => '/install', 'id' => 'install', 'class' => '')) }}                

							<div class="form-group">
								<label for="">Employee Number</label>
								<input type="text" name="employenumber" class="form-control" id="" placeholder="Employee Number">
							</div>							            
						
							<div class="form-group">
								<label for="">Firstname</label>
								<input type="text" name="firstname" class="form-control" id="" placeholder="Firstname">
							</div>							

							<div class="form-group">
								<label for="">lastname</label>
								<input type="text" name="lastname" class="form-control" id="" placeholder="Lastname">
							</div>													  

							<div class="form-group">
								<label for="">Middlename</label>
								<input type="text" name="middlename" class="form-control" id="" placeholder="Middlename">
							</div>	

							<div class="form-group">
								<label for="">Nickname</label>
								<input type="text" name="nickname" class="form-control" id="" placeholder="Nickname">
							</div>															

							<div class="form-group">
								<label for="">Email address</label>
								<input type="email" name="email" class="form-control" id="" placeholder="Email">
							</div>

							<div class="form-group">           
								{{ Form::label('password', 'Password', array('class' => 'control-label')) }}
								{{ Form::password('password', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Password')) }}            
							</div>   

							<div class="form-group">           
								{{ Form::label('Confirm password', 'Confirm Password', array('class' => 'control-label')) }}
								{{ Form::password('password_confirmation', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Confirm Password')) }}            
							</div> 						  

						  <button type="submit" class="btn btn-custom-default">Submit</button>

            			{{ Form::close() }} 						


					</div><!--/.pane-body-->        
					</div><!--/.panel-->
				</div>

        	</div><!--//#content .col-md-9-->          

        </div><!--//.row-->

</div><!--//.page-container-->

<?php 
//$employeeSearch = array();
?>
<!--div id="sidebar-container" class="col-md-2"-->
	<!--div class="sidebar"-->

	  <div class="panel panel-custom panel-custom-default">
	    <div class="panel-heading">
	      
	      <p><img src="" data-src="holder.js/48x48?bg=959595&fg=dcdcdc" alt="" class="img-circle"></p>	      	
	      <h3 class="panel-title" style="font-size:11px;">

	      <?php if ( !empty($employeeSearch) ) { ?>
	        {{{ $employeeSearch->firstname }}}, {{{ $employeeSearch->lastname }}}
	      <?php } ?>
	      </h3>
	      
	    </div>
	    <div class="panel-body hide hidden"></div>
	      <section id="designation" style="background-color:#1a1a19;">
	        <div class="row">                              
	          <div class="col-md-12">

	            <table class="table table-inline table-condensed">
	              <tbody>
	              <tr>
	                <td class="first-tr-td">Employee No. <span>
	                  <?php if ( !empty($employeeSearch) ) { ?>
	                    {{{ $employeeSearch->employee_number }}}
	                  <?php } ?>
	                </span></td>                            
	              </tr>
	              <tr>
	                <td>Designation:<br />
	                  <span>
	                    <?php //if ( !empty($jobTitle) ) { ?>
	                      {{{-- $jobTitle->name --}}}
	                    <?php //} ?>

	                  </span></td>
	              </tr>
	              <tr>
	                <td>Team:<br />
	                  <span>{{{-- $department->name --}}}</span></td>
	              </tr>
	              <tr
	>                            <td>Manager / Supervisor:<br />
	                  <span>{{{-- $managerFullname --}}}</span></td>
	              </tr>
	              <tr>
	                <td>Default Shift:<br />
	                    <span class="hide hidden">8:00 am 5:00 pm</span><br />
	                    <span class="hide hidden">Monday - Friday</span>
	                </td>
	              </tr>                                       
	              </tbody>
	            </table>

	          </div>          
	        <div>
	      </section><!--//#designation-->                 

	  </div><!--//.panel-default-->                 


	  <div class="panel-group panel-custom-group" id="accordion" role="tablist" aria-multiselectable="true">
	    <div class="panel panel-custom panel-custom-default">
	      <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading1">
	        <h4 class="panel-title">
	          <a data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="true" aria-controls="collapse1">
	            Employee Info                        
	          <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span>                        
	          </a>                      
	        </h4>
	      </div>
	      <div id="collapse1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading1">
	        <div class="panel-body hide hidden"></div>

	        <section style="background-color:#1a1a19;">
	          <div class="row">                              
	            <div class="col-md-12">

	              <table class="table table-inline table-condensed">
	                <tbody>
	                <tr>
	                  <td class="first-tr-td">Employee Info <span>Content</span></td>                            
	                </tr>                                       
	                </tbody>
	              </table>

	            </div>          
	          <div>
	        </section><!--//#section-->

	      </div>
	    </div>
	    <div class="panel panel-custom panel-custom-default">
	      <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading2">
	        <h4 class="panel-title">
	          <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="false" aria-controls="collapse2">
	            Compensation                        
	          <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span>                        
	          </a>
	        </h4>
	      </div>
	      <div id="collapse2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading2">
	        
	        <div class="panel-body hide hidden"></div>

	        <section style="background-color:#1a1a19;">
	          <div class="row">                              
	            <div class="col-md-12">

	              <table class="table table-inline table-condensed">
	                <tbody>
	                <tr>
	                  <td class="first-tr-td">Compensation <span>Content</span></td>                            
	                </tr>                                                                  
	                </tbody>
	              </table>

	            </div>          
	          <div>
	        </section><!--//#section-->

	      </div>
	    </div>
	    <div class="panel panel-custom panel-custom-default">
	      <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading3">
	        <h4 class="panel-title">
	          <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3">
	            Tax Exemption
	          <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span>                        
	          </a>

	        </h4>
	      </div>
	      <div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading3">
	        
	        <div class="panel-body hide hidden"></div>

	        <section style="background-color:#1a1a19;">
	          <div class="row">                              
	            <div class="col-md-12">

	              <table class="table table-inline table-condensed">
	                <tbody>
	                <tr>
	                  <td class="first-tr-td">Tax Exemption <span>Content</span></td>                            
	                </tr>                                                                  
	                </tbody>
	              </table>

	            </div>          
	          <div>
	        </section><!--//#section-->

	      </div>
	    </div>
	    <div class="panel panel-custom panel-custom-default">
	      <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading4">
	        <h4 class="panel-title">
	          <a class="collapsed hide hidden" data-toggle="collapse" data-parent="#accordion" href="#collapse4" aria-expanded="false" aria-controls="collapse4">
	            Leave Credits                        
	            <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span> 
	          </a>
	            Leave Credits
	        </h4>
	      </div>
	      <div id="collapse4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading4">
	        
	        <div class="panel-body hide hidden"></div>

	        <section style="background-color:#1a1a19;">
	          <div class="row">                              
	            <div class="col-md-12">


	              <table class="table table-inline table-condensed">
	                <tbody>
	                <tr>
	                  <td class="first-tr-td">Sick Leave: <span>5.5</span></td>                            
	                </tr>                                      
	                <tr>
	                  <td class="first-tr-td">Vacation Leave: <span>7</span></td>                            
	                </tr>                                      
	                <tr>
	                  <td class="first-tr-td">
	                    <a href="{{{ url('/leave-form') }}}" class="btn btn-custom-default center-block" style="font-size:11px;">Leave Application</a>
	                  </td>                            
	                </tr>                                                                                              
	                </tbody>
	              </table>

	            </div>          
	          <div>
	        </section><!--//#section-->

	      </div>
	    </div>

	    <div class="panel panel-custom panel-custom-default">
	      <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading5">
	        <h4 class="panel-title">
	          <a class="collapsed hide hidden" data-toggle="collapse" data-parent="#accordion" href="#collapse5" aria-expanded="false" aria-controls="collapse5">
	            Change Schedule
	            <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span>                        
	          </a>
	            Change Schedule                      
	        </h4>
	      </div>
	      <div id="collapse5" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading5">
	        
	        <div class="panel-body hide hidden"></div>

	        <section style="background-color:#1a1a19;">
	          <div class="row">                              
	            <div class="col-md-12">

	              <table class="table table-inline table-condensed">
	                <tbody>
	                <tr>
	                  <td class="first-tr-td">
	                    <a href="#" class="btn btn-custom-default center-block" style="font-size:11px;">Change Schedule</a>
	                  </td>                            
	                </tr>                                    
	                </tbody>
	              </table>

	            </div>          
	          <div>
	        </section><!--//#section-->


	      </div>
	    </div>

	    <div class="panel panel-custom panel-custom-default">
	      <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading6">
	        <h4 class="panel-title">
	          <a class="collapsed hide hidden" data-toggle="collapse" data-parent="#accordion" href="#collapse6" aria-expanded="false" aria-controls="collapse5">
	            Other Request
	            <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span>                        
	          </a>
	          Other Request
	        </h4>
	      </div>
	      <div id="collapse6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading6">
	        
	        <div class="panel-body hide hidden"></div>

	        <section style="background-color:#1a1a19;">
	          <div class="row">                              
	            <div class="col-md-12">

	              <table class="table table-inline table-condensed">
	                <tbody>
	                <tr>
	                  <td class="first-tr-td">For Other Request, Please ask Human Resource Personel</td>                            
	                </tr>                                      
	                </tbody>
	              </table>

	            </div>          
	          <div>
	        </section><!--//#section-->


	      </div>
	    </div>                

	  </div>             

	<!--/div--><!--//.sidebar-->              
<!--/div-->
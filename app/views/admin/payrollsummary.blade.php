{{-- Child Template --}}
@extends('layouts.admin.sidebarcontent')

@section('head')
	@parent

@stop

@section('sidebar')
	@include('partials.admin.sidebar')
@stop

@section('content')

<?php

	//$users = Session::get('values');
	//foreach($users as $user)
	//{
	//	echo $user;
	//}
	//var_dump($vals);
	//Session::put('values', $vals);

	//var_dump($values);
	/*if (Session::has('values'))
	{
		  $user = Session::get('values');
		  echo $user[0];
	}*/
	//var_dump($summaries);
	//$cutoffFrom = Session::get('cutfrom');
	
	$string = $cutoffFrom;
	$timestamp = strtotime($string);
	$cutoffF = date("d", $timestamp);
	
	$string2 = $cutoffTo;
	$timestamp2 = strtotime($string2);
	$cutoffT = date("d", $timestamp2);

?>

<!--div class="page-container"-->

        <!--div class="row" style="padding-bottom:20px;"-->

          <div id="content" class="col-md-12" role="main">

            <ol class="breadcrumb hide hidden">
              <li><a href="#">Home</a></li>
              <li class="active">Page</li>
            </ol>

            <h1 class="page-header">Payroll Summary</h1>
			<span>Cuttoff Date: <?php echo $cutoffFrom . ' to ' . $cutoffTo; ?></span><br>
	        	<div class="table-responsive">
	        			<table class="table table-striped table-hover table-list display" border="1">
							<thead>
								<tr>
									<th>Employee</th>
									<th>Employee</th>
									<th>Employee Tax</th>									
									<th>Daily</th>
									<th>Days</th>
									<th>Basic</th>
									<th colspan="2">Late / UT</th>	
									<th colspan="2">Absences / LWOP</th>
									<!--th colspan="2">Premium pay on RD</th-->
									<!--th colspan="2">Regular Overtime</th>
									<th colspan="2">Regular ND</th>
									<th colspan="2">RD (First 8hrs)</th>
									<th colspan="2">RD (excess 8 hrs)</th-->
					<?php			
									//***Overtime Section
									
							$ctrA = 0;
							$ctr1A = 0;		
							$ctr2A = 0;
							$ctr3A = 0;
							$ctr4A = 0;
							$ctr5A = 0;
							$ctr6A = 0;
							$ctr7A = 0;
							$ctr8A = 0;
							$ctr9A = 0;
							$ctr10A = 0;
							$ctr11A = 0;
							$ctr12A = 0;
							$ctr13A = 0;
							$ctr14A = 0;
							$ctr15A = 0;
							$ctr16A = 0;
							$ctr17A = 0;
							$ctr18A = 0;
							$ctr19A = 0;
							$ctr20A = 0;
							$ctr21A = 0;
							$ctr22A = 0;
							
							$ctr23A = 0;
							$ctr24A = 0;
							$ctr25A = 0;
							$ctr26A = 0;
							
							//foreach ($summaries as $summary) 
							//{
									
									//if(!empty($summary->empregular_overtime))
									//{
										
									//for leaves	
									
										$ctr23 = 0;
										foreach ($summaries as $sum23)
										{
											if($sum23->paid_sick_leave > 0)
											{
												$ctr23A++;
												$ctr23 = $ctr23A;
											}
											
										}
										
										if($ctr23 > 0)
											echo "<th colspan='2'>Sick Leave</th>"; 
									
									
										$ctr24 = 0;
										foreach ($summaries as $sum24)
										{
											if($sum24->paid_vacation_leave > 0)
											{
												$ctr24A++;
												$ctr24 = $ctr24A;
											}
											
										}
										
										if($ctr24 > 0)
											echo "<th colspan='2'>Vacation Leave</th>"; 
										
										
										
										$ctr25 = 0;
										foreach ($summaries as $sum25)
										{
											if($sum25->maternity_leave > 0)
											{
												$ctr25A++;
												$ctr25 = $ctr25A;
											}
											
										}
										
										if($ctr25 > 0)
											echo "<th colspan='2'>Maternity Leave</th>"; 
										
										
										
										$ctr26 = 0;
										foreach ($summaries as $sum26)
										{
											if($sum26->paternity_leave > 0)
											{
												$ctr26A++;
												$ctr26 = $ctr26A;
											}
											
										}
										
										if($ctr26 > 0)
											echo "<th colspan='2'>Paternity Leave</th>"; 
									
									
									// --------------------------------------------------

										$ctr = 0;
										foreach ($summaries as $sum)
										{
											if($sum->empregular_overtime > 0)
											{
												$ctrA++;
												$ctr = $ctrA;
											}
											
										}
										
										if($ctr > 0)
											echo "<th colspan='2'>Regular Overtime</th>"; 
										
									//}
											
									//if(!empty($summary->splemp_holiday))
									//{
										$ctr = 0;
										foreach ($summaries as $sum1)
										{
											if($sum1->splemp_holiday > 0)
											{
												$ctr1A++;
												$ctr = $ctr1A;
											}
											
										}
										
										if($ctr > 0)
											echo "<th colspan='2'>Special Holiday Pay OT(First 8 hrs)</th>"; 
										
									//}
											
									//if(!empty($summary->rdspl_holiday))
									//{
										$ctr1 = 0;
										foreach ($summaries as $sum2)
										{
											if($sum2->rdspl_holiday > 0)
											{
												$ctr2A++;
												$ctr1 = $ctr2A;
											}
											
										}
										
										if($ctr1 > 0)
											echo "<th colspan='2'>Special Holiday/Rest Day(First 8 hrs)</th>"; 
										
									//}
											
									//if(!empty($summary->rond))
									//{
										$ctr2 = 0;
										foreach ($summaries as $sum3)
										{
											if($sum3->rond > 0)
											{
												$ctr3A++;
												$ctr2 = $ctr3A;
											}
											
										}
										
										if($ctr2 > 0)
											echo "<th colspan='2'>Regular OT Night Diff</th>"; 
										
									//}
											
									//if(!empty($summary->rnd))
									//{
										$ctr3 = 0;
										foreach ($summaries as $sum4)
										{
											if($sum4->rnd > 0)
											{
												$ctr4A++;
												$ctr3 = $ctr4A;
											}
											
										}
										
										
										if($ctr3  > 0)
											echo "<th colspan='2'>Night Diff</th>"; 
										
									//}
											
									//if(!empty($summary->rd))
									//{
										$ctr6 = 0;
										foreach ($summaries as $sum5)
										{
											if($sum5->rd > 0)
											{
												$ctr5A++;
												$ctr6 = $ctr5A;
											}
											
										}
										
										//$ctr5A++;
										if($ctr6  > 0)
										echo "<th colspan='2'>Rest Day OT(First 8 hrs)</th>"; 
										
									//}
											
									//if(!empty($summary->rdot))
									//{
										$ctr7 = 0;
										foreach ($summaries as $sum6)
										{
											if($sum6->rdot > 0)
											{
												$ctr6A++;
												$ctr7 = $ctr6A;
											}
											
										}
										
										//$ctr6A++;
										if($ctr7  > 0)
										echo "<th colspan='2'>Rest Day OT(Excess in 8 hrs)</th>"; 
									//}
											
									//if(!empty($summary->rdond))
									//{
										$ctr8 = 0;
										foreach ($summaries as $sum7)
										{
											if($sum7->rdond > 0)
											{
												$ctr7A++;
												$ctr8 = $ctr7A;
											}
											
										}
										
										//$ctr7A++;
										if($ctr8  > 0)
											echo "<th colspan='2'>Rest Day OT(First 8 hrs)</th>"; 
										
									//}
											
									//if(!empty($summary->rdnd))
									//{
										$ctr9 = 0;
										foreach ($summaries as $sum8)
										{
											if($sum8->rdnd > 0)
											{
												$ctr8A++;
												$ctr9 = $ctr8A;
											}
											
										}
										
										//$ctr8A++;
										if($ctr9  > 0)
											echo "<th colspan='2'>Rest Day Night Diff</th>"; 
										
									//}
											
									//if(!empty($summary->rdsho))
									//{
										$ctr10 = 0;
										foreach ($summaries as $sum9)
										{
											if($sum9->rdsho > 0)
											{
												$ctr9A++;
												$ctr10 = $ctr9A;
											}
											
										}
										
										//$ctr9A++;
										if($ctr10  > 0)
											echo "<th colspan='2'>Special Holiday/RD(Excess in 8 hrs)</th>"; 
										
									//}
											
									//if(!empty($summary->rdshond))
									//{
										$ctr11 = 0;
										foreach ($summaries as $sum10)
										{
											if($sum10->rdshond > 0)
											{
												$ctr10A++;
												$ctr11 = $ctr10A;
											}
											
										}
										
										//$ctr10A++;
										if($ctr11  > 0)
											echo "<th colspan='2'>RD Special Hol OT ND</th>"; 
										
									//}
											
									//if(!empty($summary->rdshnd))
									//{
										$ctr12 = 0;
										foreach ($summaries as $sum11)
										{
											if($sum11->rdshnd > 0)
											{
												$ctr11A++;
												$ctr12 = $ctr11A;
											}
											
										}
										
										//$ctr11A++;
										if($ctr12  > 0)
											echo "<th colspan='2'>RD Special Hol ND</th>"; 
										
									//}
											
									//if(!empty($summary->rdlh))
									//{
										$ctr13 = 0;
										foreach ($summaries as $sum12)
										{
											if($sum12->rdlh > 0)
											{
												$ctr12A++;
												$ctr13 = $ctr12A;
											}
											
										}
										
										//$ctr12A++;
										if($ctr13  > 0)
											echo "<th colspan='2'>Legal Holiday/Restday Pay(First 8 hrs)</th>"; 
										
									//}
											
									//if(!empty($summary->rdlhot))
									//{
										$ctr14 = 0;
										foreach ($summaries as $sum13)
										{
											if($sum13->rdlhot > 0)
											{
												$ctr13A++;
												$ctr14 = $ctr13A;
											}
											
										}
										
										//$ctr13A++;
										if($ctr14  > 0)
											echo "<th colspan='2'>Legal Holiday/Rest Day OT</th>"; 
										
									//}
											
									//if(!empty($summary->rdlhond))
									//{
										$ctr14 = 0;
										foreach ($summaries as $sum)
										{
											if($sum->rdlhond > 0)
											{
												$ctr14A++;
												$ctr14 = $ctr14A;
											}
											
										}
										
										//$ctr14A++;
										if($ctr14  > 0)
											echo "<th colspan='2'>Legal Holiday/Rest Day OT ND</th>"; 
										
									//}
											
									//if(!empty($summary->rdlhnd) && ($summary->empregular_overtime > 0))
									//{
										$ctr15 = 0;
										foreach ($summaries as $sum)
										{
											if($sum->rdlhnd > 0)
											{
												$ctr15A++;
												$ctr15 = $ctr15A;
											}
											
										}
										
										//$ctr15A++;
										if($ctr15  > 0)
											echo "<th colspan='2'>Legal Holiday/Rest Day ND</th>"; 
										
									//}
											
									//if(!empty($summary->sho))
									//{
										$ctr16 = 0;
										foreach ($summaries as $sum)
										{
											if($sum->sho > 0)
											{
												$ctr16A++;
												$ctr16 = $ctr16A;
											}
											
										}
										
										//$ctr16A++;
										if($ctr16  > 0)
											echo "<th colspan='2'>Special Holiday Pay OT</th>"; 
										
									//}
											
									//if(!empty($summary->shond))
									//{
										$ctr17 = 0;
										foreach ($summaries as $sum)
										{
											if($sum->shond > 0)
											{
												$ctr17A++;
												$ctr17 = $ctr17A;
											}
											
										}
										
										//$ctr17A++;
										if($ctr17  > 0)
											echo "<th colspan='2'>Special Holiday Pay OT ND</th>"; 
										
									//}
											
									//if(!empty($summary->shnd))
									//{
										$ctr18 = 0;
										foreach ($summaries as $sum)
										{
											if($sum->shnd > 0)
											{
												$ctr18A++;
												$ctr18 = $ctr18A;
											}
											
										}
										
										//$ctr18A++;
										if($ctr18  > 0)
											echo "<th colspan='2'>Special Holiday Pay ND</th>"; 
										
									//}
											
									//if(!empty($summary->lh))
									//{
										$ctr19 = 0;
										foreach ($summaries as $sum)
										{
											if($sum->lh > 0)
											{
												$ctr19A++;
												$ctr19 = $ctr19A;
											}
											
										}
										
										//$ctr19A++;
										if($ctr19  > 0)
											echo "<th colspan='2'>Legal Holiday</th>"; 
										
									//}
											
									//if(!empty($summary->lho))
									//{
										$ctr20 = 0;
										foreach ($summaries as $sum)
										{
											if($sum->lho > 0)
											{
												$ctr20A++;
												$ctr20 = $ctr20A;
											}
											
										}
										
										//$ctr20A++;
										if($ctr20  > 0)
											echo "<th colspan='2'>Legal Holiday OT</th>"; 
										
									//}
											
									//if(!empty($summary->lhond))
									//{
										$ctr21 = 0;
										foreach ($summaries as $sum)
										{
											if($sum->lhond > 0)
											{
												$ctr21A++;
												$ctr21 = $ctr21;
											}
											
										}
										
										//$ctr21A++;
										if($ctr21  > 0)
											echo "<th colspan='2'>Legal Holiday OT ND</th>"; 
										
									//}
											
									//if(!empty($summary->lhnd))
									//{
										$ctr22 = 0;
										foreach ($summaries as $sum)
										{
											if($sum->lhnd > 0)
											{
												$ctr22A++;
												$ctr22 = $ctr22A;
											}
											
										}
										
										//$ctr22A++;
										if($ctr22  > 0)
											echo "<th colspan='2'>Legal Holiday ND</th>"; 
										
									//}
							//}
						?>
									<th>Total</th>
									<th>Previous OT</th>	
									<th>Previous Payroll</th>
									<th>Retro Payment</th>
									<th>Transportation</th>
									<th>Gross</th>	
									<th>Cost of Living</th>
									<th>Deminimis</th>
									<th>Mobile</th>	
									<th>Previous</th>
									<th>Reimbursable</th>
									<th>Retro</th>
									<th>Total Non-Taxable</th>
									<th colspan="3">Employee Contribution</th>
									<th colspan="4">Employer Share</th>
									<th>Cash</th>
									<th>Company</th>
									<th>HDMF</th>
									<th>HMO Addt'l</th>
									<th>SSS Salary</th>
									<th>Telephone</th>
									<th>Withholding</th>
									<th>Withholding</th>
									<th>Total</th>
									<th>Net</th>															
								</tr>
								<tr>
									<th>No</th>
									<th>Name</th>
									<th>Status</th>
									<th>Rate</th>
									<th>Worked</th>
									<th>Pay</th>		
									<th>Hrs.</th>
									<th>Amount</th>
									<th>Days</th>
									<th>Amount</th>
									
									<!--th>Hrs.</th>
									<th>Amount</th>
									<th>Hrs.</th>
									<th>Amount</th>
									<th>Hrs.</th>
									<th>Amount</th>
									<th>Hrs.</th>
									<th>Amount</th>
									<th>Hrs.</th>
									<th>Amount</th-->
									
									<?php			
									//***Overtime Section
							$ctrB = 0;
							$ctr1B = 0;		
							$ctr2B = 0;
							$ctr3B = 0;
							$ctr4B = 0;
							$ctr5B = 0;
							$ctr6B = 0;
							$ctr7B = 0;
							$ctr8B = 0;
							$ctr9B = 0;
							$ctr10B = 0;
							$ctr11B = 0;
							$ctr12B = 0;
							$ctr13B = 0;
							$ctr14B = 0;
							$ctr15B = 0;
							$ctr16B = 0;
							$ctr17B = 0;
							$ctr18B = 0;
							$ctr19B = 0;
							$ctr20B = 0;
							$ctr21B = 0;
							$ctr22B = 0;		
							
							$ctr23B = 0;
							$ctr24B = 0;
							$ctr25B = 0;
							$ctr26B = 0;
									
							foreach ($summaries as $summary) 
							{
									// for leaves
									
									if(!empty($summary->paid_sick_leave))
									{
										$ctr23B++;
										if($ctr23B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
										
									}
									
									if(!empty($summary->paid_vacation_leave))
									{
										$ctr24B++;
										if($ctr24B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
										
									}
									
									if(!empty($summary->maternity_leave))
									{
										$ctr25B++;
										if($ctr25B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
										
									}
									
									if(!empty($summary->paternity_leave))
									{
										$ctr26B++;
										if($ctr26B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
										
									}
									
									
									//--------------------------------------
									
									if(!empty($summary->empregular_overtime))
									{
										$ctrB++;
										if($ctrB==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
										
									}
											
									if(!empty($summary->splemp_holiday))
									{
										$ctr1B++;
										if($ctr1B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rdspl_holiday))
									{
										$ctr2B++;
										if($ctr2B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rond))
									{
										$ctr3B++;
										if($ctr3B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rnd))
									{
										$ctr4B++;
										if($ctr4B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rd))
									{
										$ctr5B++;
										if($ctr5B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rdot))
									{
										$ctr6B++;
										if($ctr6B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rdond))
									{
										$ctr7B++;
										if($ctr7B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rdnd))
									{
										$ctr8B++;
										if($ctr8B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rdsho))
									{
										$ctr9B++;
										if($ctr9B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rdshond))
									{
										$ctr10B++;
										if($ctr10B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rdshnd))
									{
										$ctr11B++;
										if($ctr11B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rdlh))
									{
										$ctr12B++;
										if($ctr12B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rdlhot))
									{
										$ctr13B++;
										if($ctr13B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rdlhond))
									{
										$ctr14B++;
										if($ctr14B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->rdlhnd))
									{
										$ctr15B++;
										if($ctr15B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->sho))
									{
										$ctr16B++;
										if($ctr16B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->shond))
									{
										$ctr17B++;
										if($ctr17B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->shnd))
									{
										$ctr18B++;
										if($ctr18B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->lh))
									{
										$ctr19B++;
										if($ctr19B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->lho))
									{
										$ctr20B++;
										if($ctr20B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->lhond))
									{
										$ctr21B++;
										if($ctr21B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
											
									if(!empty($summary->lhnd))
									{
										$ctr22B++;
										if($ctr22B==1)
										{
											echo "<th>Hrs.</th>";
											echo "<th>Amount</th>";
											
										}
									}
									
							}
								?>
									
									
									<th>Overtime</th>	
									<th>Adjustment</th>	
									<th>Adjustment</th>
									<th>Adjustment</th>
									<th>Allowance</th>
									<th>Pay</th>	
									<th>Allowance</th>
									<th></th>
									<th>Allowance</th>	
									<th>Payroll</th>
									<th>Allowance </th>
									<th>Payment</th>
									<th>Earnings </th>
									<th>SSS</th>
									<th>Philhealth</th>
									<th>Pag-Ibig</th>
									<th>SSS</th>
									<th>EC</th>
									<th>Philhealth</th>
									<th>Pag-Ibig</th>
									<th>Advance</th>
									<th>Loan</th>
									<th>Loan</th>
									<th>Dependent</th>
									<th>Loan</th>
									<th>Charges</th>
									<th>Tax Basis</th>
									<th>Tax</th>
									<th>Deductions</th>
									<th>Pay</th>															
								</tr>
							</thead>
							<tbody>
									<?php

										//$BP = 11500;
										//$MR = $BP * 2;
										//$DR = $MR / 21.75;
										//$min = $DR / 480;
										//$amt_lates = $sumlates * $min;
										//$amt_undertime = $sumundertime * $min;
						        		
										
										//getting the day portion from the cutoff date
										$frm_timestamp = strtotime($cutoffFrom);
										$cutofffrom = date('d', $frm_timestamp);
										
										$to_timestamp = strtotime($cutoffTo);
										$cutoffto = date('d', $to_timestamp);
										
								/*		$ctr = 0;
										$ctr1 = 0;		
										$ctr2 = 0;
										$ctr3 = 0;
										$ctr4 = 0;
										$ctr5 = 0;
										$ctr6 = 0;
										$ctr7 = 0;
										$ctr8 = 0;
										$ctr9 = 0;
										$ctr10 = 0;
										$ctr11 = 0;
										$ctr12 = 0;
										$ctr13 = 0;
										$ctr14 = 0;
										$ctr15 = 0;
										$ctr16 = 0;
										$ctr17 = 0;
										$ctr18 = 0;
										$ctr19 = 0;
										$ctr20 = 0;
										$ctr21 = 0;
										$ctr22 = 0;	 */

										
										
						        		foreach ($summaries as $summary) 
						        		{
						        			# code...
											
											$contrib_sss = 0;
											$contrib_philhealth = 0;
											$wtax = 0;
											$totalOT = 0.00;
											$tax_bracket = 0.00;
											$percent = 0.00;
											$exempt = 0.00;
											$deminimis = 0.00;
											
											$phealth_phee = 0;
											$phealth_pher = 0;
					                        $phealth_phtmp = 0;
											
											$sss_employee = 0;
											$sss_employer = 0;
											
						        			$empid = $summary->employee_id;
											$employeeid = $summary->employeeid;
						        			$taxstatus = $summary->tax_status;
											//echo $taxstatus;
						        			//$min = number_format(($summary->dailysal / 480), 2);
											$min = $summary->dailysal / 480;

						        			$BP = $summary->basicpay;
						        			$DR = $summary->dailysal;
						        			$HR = $DR / 8;
											
											//LATES
						        			$sumlates = ($summary->lates);	//in minutes
											//$sumlates2 = ($summary->lates)/60;	//in hours
						        			$amt_lates = $sumlates * $min;

						        			//ABSENCES
						        			$sumabsences2 = ($summary->absent)/8;	//in days
											$sumabsences = $summary->absent;
						        			$amt_absences = $sumabsences * $HR;
											
											$totalnottax = 0;
											
											//non-taxable earnings
											$ecola = $summary->ecola;
											
											//computation for deminimis
											if(empty($sumabsences) && !isset($sumabsences))
											{
												$deminimis = ($summary->deminimis) * .5;
											}	
											elseif($sumabsences > 0)
											{
												$dem = $summary->deminimis;
												$dem2 = ($summary->deminimis) * .5;
												$ratedem = $dem / 21.75;
												$totaldem = $sumabsences2 * $ratedem;
												$deminimis = $dem2 - $totaldem;
											}
											 //echo "the value of deminimis is: " . $totaldem . "<br>";
											 //echo "the value of dem"
											
											$mobile_allowance = $summary->mobile_allowance;
											$previous_payroll = $summary->previous_payroll;
											$reimbursible_allowance = $summary->reimbursible_allowance;
											$retro_payment = $summary->retro_payment;
											
											if($ecola > 0.00)
											{
												$totalnottax = $totalnottax + $ecola;
											}
											
											if($deminimis > 0.00)
											{
												$totalnottax = $totalnottax + $deminimis;
											}
											
											if($mobile_allowance > 0.00)
											{
												$totalnottax = $totalnottax + $mobile_allowance;
											}
											
											if($previous_payroll > 0.00)
											{
												$totalnottax = $totalnottax + $previous_payroll;
											}
											
											if($reimbursible_allowance > 0.00)
											{
												$totalnottax = $totalnottax + $reimbursible_allowance;
											}
											
											if($retro_payment > 0.00)
											{
												$totalnottax = $totalnottax + $retro_payment;
											}
											
											
											//taxable earnings
											
											$totaltax = 0;
											
											$transportation_allowance = $summary->transportation_allowance;
											$previous_OT_adjustment = $summary->previous_OT_adjustment;
											$previous_payroll_adjustment = $summary->previous_payroll_adjustment;
											$retro_payment_adjustment = $summary->retro_payment_adjustment;
											
											if($transportation_allowance > 0.00)
											{
												$totaltax = $totaltax + $transportation_allowance;
											}
											
											
											if($previous_OT_adjustment > 0.00)
											{
												$totaltax = $totaltax + $previous_OT_adjustment;
											}
											
											
											if($previous_payroll_adjustment > 0.00)
											{
												$totaltax = $totaltax + $previous_payroll_adjustment;
											}
											
											
											if($retro_payment_adjustment > 0.00)
											{
												$totaltax = $totaltax + $retro_payment_adjustment;
											}
											
											//other deductions
											$company_loan = $summary->company_loan;
											$hdmf_loan = $summary->hdmf_loan;
											$hmo_dep = $summary->hmo_dep;
											$sss_salary_loan = $summary->sss_salary_loan;
											$telephone_charges = $summary->telephone_charges;
											$cash_advance = $summary->cash_advance;

						        			//OT
						        			//$sumOT = $summary->regular_overtime;
						        			//$ROT = $HR * 1.25 * $sumOT;

						        			//ND
						        			//$sumND = $summary->regular_night_differential;
						        			//$NSN = $HR * 0.10 * $sumND;

						        			//$totalOT = $ROT + $NSN;
						        			
											//** end of Overtime Computation
											
						        			//$GP = $BP + ($ROT + $NSN) - ($amt_lates + $amt_absences);
						        			//$GP = number_format($BP + ($ROT + $NSN) - ($amt_lates + $amt_absences),2);
											

						        			//Contributions
						        			$sss_users = DB::table('tbl_sss')
							                ->where('start_salary', '<=', $BP)
							                ->where('end_salary', '>=', $BP)
							                //->orWhere('end_salary', '<', $salary)
							                ->get();

							                //var_dump()


									        $deducs = DB::table('tbl_philhealth')
									                ->where('starting_salary', '<=', $BP)
									                ->where('ending_salary', '>=', $BP)
									                //->orWhere('end_salary', '<', $salary)
									                ->get();

									        /*foreach($deducs as $deduc)
									        {
									        	echo "the value is: " . $deduc->TMP;
									        }*/
  

									        foreach ($sss_users as $sss_user)
						        			{
						        				$contrib_sss = $sss_user->TCEE;
						        				//echo "<br><br>Employer Contribution: " . $sss_user->TCER;
					                            //echo "<br>Employee / Members Contribution: " . $sss_user->TCEE;
					                            //echo "<br>Total Monthly Contribution: " . $sss_user->TCTOTAL;
					                            $sss_tcer = $sss_user->TCER;
					                            $sss_tcee = $sss_user->TCEE;
					                            $sss_tctotal = $sss_user->TCTOTAL;

						        			}
					                        
					                        foreach ($deducs as $deduc)
					                        {
					                        	$contrib_philhealth = $deduc->employee_share;
					                            //echo "<br><br>Employee Share: " . $deduc->employee_share;
					                            //echo "<br>Employer Share: " . $deduc->employer_share;
					                            //echo "<br>Total Monthly Contribution: " . $deduc->TMP;
					                            $phealth_phee = $deduc->employee_share;
					                            $phealth_pher = $deduc->employer_share;
					                            $phealth_phtmp = $deduc->TMP;

					                        }

											//
											//
					                        $pagibig_employee = 100;
									        $pagibig_employer = 100;
									
											if($cutofffrom == 26 && $cutoffto == 10)
											{
												$total_contrib = $contrib_philhealth + $pagibig_employee;
											}
											elseif($cutofffrom == 11 && $cutoffto == 25)
											{
												$total_contrib = $contrib_sss;
											}
										

											//echo "<br><br>Net Pay: " . $netpay;

											//echo '<hr style="color: red;">';
											//echo "<br><br>";
											?>

									        
									        <tr>
												<td><?php echo $summary->employee_number; ?></td>
												<td><?php echo $summary->firstname . ' ' . $summary->lastname;  ?></td>
												<td><?php echo $summary->tax_status; ?></td>
												<td><?php echo $DR; ?></td>
												<td> - </td>
												<td><?php echo $BP; ?></td>		
												<td><?php echo $sumlates; ?></td>
												<td><?php echo number_format($amt_lates,2); ?></td>
												<td><?php echo $sumabsences2; ?></td>
												<td><?php echo number_format($amt_absences,2); ?></td>
												<!--td> - </td>
												<td> - </td>
												<td><?php /*echo $sumOT; ?></td>
												<td><?php echo $ROT; ?></td>
												<td><?php echo $sumND; ?></td>
												<td><?php echo $NSN;*/ ?></td-->
												
									<?php			
									
												//*** Leave Section
												//sick leave
												$sumSL = $summary->paid_sick_leave;
												$amtSL = $HR * $sumSL;
												
												//vacation leave
												$sumVL = $summary->paid_vacation_leave;
												$amtVL = $HR * $sumVL;
												
												//maternity leave
												$sumML = $summary->maternity_leave;
												$amtML = $HR * $sumML;
												
												//paternity leave
												$sumPL = $summary->paternity_leave;
												$amtPL = $HR * $sumPL;
									
												//***Overtime Section
												$sumROT = 0.00;
												$rateROT = 0.00;												
												$sumSOM = 0.00;												
												$rateSOM = 0.00;												
												$sumSRF = 0.00;												
												$rateSRF = 0.00;												
												$sumRON = 0.00;												
												$rateRON = 0.00;												
												$sumNSN = 0.00;												
												$rateNSN = 0.00;												
												$sumRDF = 0.00;												
												$rateRDF = 0.00;												
												$sumRDX = 0.00;												
												$rateRDX = 0.00;												
												$sumRDF2 = 0.00;												
												$rateRDF2 = 0.00;												
												$sumRDN = 0.00;												
												$rateRDN = 0.00;												
												$sumSRX = 0.00;												
												$rateSRX = 0.00;
												$sumSRN = 0.00;												
												$rateSRN = 0.00;												
												$sumSRN2 = 0.00;												
												$rateSRN2 = 0.00;												
												$sumLRF = 0.00;												
												$rateLRF = 0.00;												
												$sumLRX = 0.00;												
												$rateLRX = 0.00;												
												$sumLRN = 0.00;												
												$rateLRN = 0.00;												
												$sumLRN2 = 0.00;												
												$rateLRN2 = 0.00;												
												$sumSOX = 0.00;												
												$rateSOX = 0.00;												
												$sumRDN2 = 0.00;												
												$rateRDN2 = 0.00;												
												$sumRDN3 = 0.00;												
												$rateRDN3 = 0.00;												
												$sumLHF = 0.00;												
												$rateLHF = 0.00;												
												$sumLHX = 0.00;												
												$rateLHX = 0.00;												
												$sumLHN = 0.00;												
												$rateLHN = 0.00;												
												$sumLHN2 = 0.00;												
												$rateLHN2 = 0.00;												
												$empROT = 0;
												$empSOM = 0;
												$empSRF = 0;
												$empRON = 0;
												$empNSN = 0;
												$empRDF = 0;
												$empRDX = 0;
												$empRDF2 = 0;
												$empRDN = 0;
												$empSRX = 0;
												$empSRN = 0;
												$empSRN2 = 0;
												$empLRF = 0;
												$empLRX = 0;
												$empLRN = 0;
												$empLRN2 = 0;
												$empSOX = 0;
												$empRDN3 = 0;
												$empLHF = 0;
												$empLHX = 0;
												$empLHN = 0;
												$empLHN2 = 0;
												$empRDN2 = 0;
												
												
									//for sick leave									
									if(!empty($summary->paid_sick_leave) && ($summary->paid_sick_leave > 0))													
									{
												
												echo "<td>" . $sumSL . "</td>															  
													  <td>" . number_format($amtSL,2) . "</td>";	
																					
									}									
									elseif(!empty($ctr23A) && ($ctr23A>0))											
									{
														echo "<td> - </td>
														      <td> - </td>";
									}
									
									
									//for vacation leave									
									if(!empty($summary->paid_vacation_leave) && ($summary->paid_vacation_leave > 0))												
									{
												
												echo "<td>" . $sumVL . "</td>															  
													  <td>" . number_format($amtVL,2) . "</td>";	
																					
									}									
									elseif(!empty($ctr24A) && ($ctr24A>0))											
									{
														echo "<td> - </td>
														      <td> - </td>";
									}
									
									
									//for maternity leave									
									if(!empty($summary->maternity_leave) && ($summary->maternity_leave > 0))													
									{
												
												echo "<td>" . $sumML . "</td>															  
													  <td>" . number_format($amtML,2) . "</td>";	
																					
									}									
									elseif(!empty($ctr25A) && ($ctr25A>0))											
									{
														echo "<td> - </td>
														      <td> - </td>";
									}
									
									
									//for paternity leave									
									if(!empty($summary->paternity_leave) && ($summary->paternity_leave > 0))													
									{
												
												echo "<td>" . $sumPL . "</td>															  
													  <td>" . number_format($amtPL,2) . "</td>";	
																					
									}									
									elseif(!empty($ctr26A) && ($ctr26A>0))											
									{
														echo "<td> - </td>
														      <td> - </td>";
									}
							
									
												
									//for regular overtime									
									if(!empty($summary->empregular_overtime) && ($summary->empregular_overtime > 0))													
									{
												$sumROT = $summary->empregular_overtime;												
												$codeROT = 'ROT';												
												$coderateRO = DB::table('tbl_ot_rates')->where('Code', $codeROT)->first();												
												$ratero = $coderateRO->Rate;												
												$rateROT = $HR * $ratero * $sumROT;												
												$empROT = $empid;												
												$totalOT += $rateROT; 
												//if($sumROT != 0 || $rateROT != 0)													
												//{
														echo "<td>" . $sumROT . "</td>															  
														      <td>" . number_format($rateROT,2) . "</td>";	
												//}									
									}									
									elseif(!empty($ctrA) && ($ctrA>0))											
									{
														echo "<td> - </td>
														      <td> - </td>";
									}	
									
									
									//for special holiday									
									if(!empty($summary->splemp_holiday) && ($summary->splemp_holiday > 0))														
									{
												$sumSOM = $summary->splemp_holiday;												
												$codeSOM = 'SOM';												
												$coderateSH = DB::table('tbl_ot_rates')->where('Code', $codeSOM)->first();												
												$ratespl = $coderateSH->Rate;												
												$rateSOM = $HR * $ratespl * $sumSOM;												
												$empSOM = $empid;												
												$totalOT += $rateSOM; 	
												
														
												echo "<td>" . $sumSOM . "</td>															  
												<td>" . number_format($rateSOM,2) . "</td>";																						
									}												
									elseif(!empty($ctr1A) && ($ctr1A>0))														
									{														
												echo "<td> - </td>														
												<td> - </td>";													
									}																		
															  
									//for rest day special holiday									
									if(!empty($summary->rdspl_holiday) && ($summary->rdspl_holiday > 0))								
									{
												$sumSRF = $summary->rdspl_holiday;												
												$codeSRF = 'SRF';												
												$coderateSRF = DB::table('tbl_ot_rates')->where('Code', $codeSRF)->first();												
												$raterdh = $coderateSRF->Rate;												
												$rateSRF = $HR * $raterdh * $sumSRF;												
												$empSRF = $empid;												
												$totalOT += $rateSRF; 
												//if($sumSRF != 0 || $rateSRF != 0)												
												//{	
														echo "<td>" . $sumSRF . "</td>
														      <td>" . number_format($rateSRF,2) . "</td>";													
												//}									
									}												
									elseif(!empty($ctr2A) && ($ctr2A>0))
									{
														echo "<td> - </td>
														      <td> - </td>";
									}
														
														
									//for regular OT night diff									
									if(!empty($summary->rond) && ($summary->rond > 0))															
									{
												$sumRON = $summary->rond;
												$codeRON = 'RON';												
												$coderateRON = DB::table('tbl_ot_rates')->where('Code', $codeRON)->first();
												$rater = $coderateRON->Rate;												
												$rateRON = $HR * $rater * $sumRON;												
												$empRON = $empid;												
												$totalOT += $rateRON; 
												//if($sumRON != 0 || $rateRON != 0)												
												//{	
														echo "<td>" . $sumRON . "</td>
														      <td>" . number_format($rateRON,2) . "</td>";	
												//}
									}										
									elseif(!empty($ctr3A) && ($ctr3A>0))												
									{
														echo "<td> - </td>
														      <td> - </td>";
									}
																						
									
									//for regular night differential									
									if(!empty($summary->rnd) && ($summary->rnd > 0))															
									{													
													$sumNSN = $summary->rnd;
													$codeNSN = 'NSN';
													$coderateNSN = DB::table('tbl_ot_rates')->where('Code', $codeNSN)->first();
													$ratens = $coderateNSN->Rate;
													$rateNSN = $HR * $ratens * $sumNSN;
													$empNSN = $empid;
													$totalOT += $rateNSN; 
												//if($sumNSN != 0 || $rateNSN != 0)												
													//{															
															echo "<td>" . $sumNSN . "</td>
														         <td>" . number_format($rateNSN,2) . "</td>";	
												//}									
									}												
									elseif(!empty($ctr4A) && ($ctr4A>0))													
									{																										
													echo "<td> - </td>														      
													<td> - </td>";
									}
									
									
									//for rest day									
									if(!empty($summary->rd) && ($summary->rd > 0))																	
									{		
												$sumRDF = $summary->rd;
												$codeRDF = 'RDF';
												$coderateRDF = DB::table('tbl_ot_rates')->where('Code', $codeRDF)->first();
												$raterd = $coderateRDF->Rate;
												$rateRDF = $HR * $raterd * $sumRDF;
												$empRDF = $empid;
												$totalOT += $rateRDF; 
												//if($sumRDF != 0 || $rateRDF != 0)												
												//{															
														echo "<td>" . $sumRDF . "</td>														      
														<td>" . number_format($rateRDF,2) . "</td>";													
														//}									
									}												
									elseif(isset($ctr5A) && ($ctr5A>0))														
									{														
												echo "<td> - </td>														      
												<td> - </td>";									
									}
									

									//rest day OT									
									if(!empty($summary->rdot) && ($summary->rdot > 0))												
									{			
												$sumRDX = $summary->rdot;
												$codeRDX = 'RDX';
												$coderateRDX = DB::table('tbl_ot_rates')->where('Code', $codeRDX)->first();
												$raterx = $coderateRDX->Rate;
												$rateRDX = $HR * $raterx * $sumRDX;
												$empRDX = $empid;
												$totalOT += $rateRDX; 												
												
												//if($sumRDX != 0 || $rateRDX != 0)												
												//{															
														echo "<td>" . $sumRDX . "</td>														      
														      <td>" . number_format($rateRDX,2) . "</td>";													
												//}									
									}
									elseif(isset($ctr6A) && ($ctr6A>0))															
									{														
												echo "<td> - </td>														      
													  <td> - </td>";									
									}
									
									
									//for rest day OT ND									
									if(!empty($summary->rdond) && ($summary->rdond > 0))																
									{
												$sumRDF2 = $summary->rdond;
												$codeRDF2 = 'RDF';
												$coderateRDF2 = DB::table('tbl_ot_rates')->where('Code', $codeRDF2)->first();
												$raterf = $coderateRDF2->Rate;
												$rateRDF2 = $HR * $raterf * $sumRDF2;
												$empRDF2 = $empid;
												$totalOT += $rateRDF2; 
												//if($sumRDF2 != 0 || $rateRDF2 != 0)												
												//{															
														echo "<td>" . $sumRDF2 . "</td>														      
														      <td>" . number_format($rateRDF2,2) . "</td>";													
															  //}									
									}
									elseif(isset($ctr7A) && ($ctr7A>0))														
									{														
												echo "<td> - </td>														      
													<td> - </td>";									
									}
																			

									//for rest day ND									
									if(!empty($summary->rdnd) && ($summary->rdnd > 0))																
									{
												$sumRDN = $summary->rdnd;
												$codeRDN = 'RDN';
												$coderateRDN = DB::table('tbl_ot_rates')->where('Code', $codeRDN)->first();
												$ratern = $coderateRDN->Rate;
												$rateRDN = $HR * $ratern * $sumRDN;
												$empRDN = $empid;
												$totalOT += $rateRDN; 
												//if($sumRDN != 0 || $rateRDN != 0)												
												//{															
														echo "<td>" . $sumRDN . "</td>														      
														      <td>" . number_format($rateRDN,2) . "</td>";													
												//}									
									}
									elseif(isset($ctr8A) && ($ctr8A>0))														
									{														
												echo "<td> - </td>														     
													  <td> - </td>";									
									}
																					
									
									//for rest day special holiday OT									
									if(!empty($summary->rdsho) && ($summary->rdsho > 0))																
									{
												$sumSRX = $summary->rdsho;												
												$codeSRX = 'SRX';												
												$coderateSRX = DB::table('tbl_ot_rates')->where('Code', $codeSRX)->first();												
												$ratesx = $coderateSRX->Rate;												
												$rateSRX = $HR * $ratesx * $sumSRX;												
												$empSRX = $empid;
												$totalOT += $rateSRX; 
												//if($sumSRX != 0 || $rateSRX != 0)												
												//{															
														echo "<td>" . $sumSRX . "</td>														      
														      <td>" . number_format($rateSRX,2) . "</td>";													
												//}									
									}												
									elseif(isset($ctr9A) && ($ctr9A>0))														
									{														
												echo "<td> - </td>														      
												<td> - </td>";									
									}
																					
																					
									//for rest day special holiday OT ND									
									if(!empty($summary->rdshond) && ($summary->rdshond > 0))															
									{
												$sumSRN = $summary->rdshond;
												$codeSRN = 'SRN';
												$coderateSRN = DB::table('tbl_ot_rates')->where('Code', $codeSRN)->first();												
												$ratesr = $coderateSRN->Rate;
												$rateSRN = $HR * $ratesr * $sumSRN;
												$empSRN = $empid;
												$totalOT += $rateSRN; 
												//if($sumSRN != 0 || $rateSRN != 0)												
												//{															
													echo "<td>" . $sumSRN . "</td>														      
													<td>" . number_format($rateSRN,2) . "</td>";													
												//}									
									}												
									elseif(isset($ctr10A) && ($ctr10A>0))														
									{														
												echo "<td> - </td>														      
												<td> - </td>";									
									}

									
									//for rest day special holiday ND									
									if(!empty($summary->rdshnd) && ($summary->rdshnd > 0))															
									{
												$sumSRN2 = $summary->rdshnd;
												$codeSRN2 = 'SRN';
												$coderateSRN2 = DB::table('tbl_ot_rates')->where('Code', $codeSRN2)->first();
												$ratesr2 = $coderateSRN2->Rate;
												$rateSRN2 = $HR * $ratesr2 * $sumSRN2;
												$empSRN2 = $empid;
												$totalOT += $rateSRN2;
												//if($sumSRN2 != 0 || $rateSRN2 != 0)												
												//{															
														echo "<td>" . $sumSRN2 . "</td>														      
															  <td>" . number_format($rateSRN2,2) . "</td>";													
												//}									
									}										
									elseif(isset($ctr11A) && ($ctr11A>0))												
									{														
												echo "<td> - </td>														      
												<td> - </td>";									
									}	
													
													
									//for rest day legal holiday									
									if(!empty($summary->rdshnd) && ($summary->rdshnd > 0))															
									{
												$sumLRF = $summary->rdlh;
												$codeLRF = 'LRF';
												$coderateLRF = DB::table('tbl_ot_rates')->where('Code', $codeLRF)->first();
												$ratelr = $coderateLRF->Rate;
												$rateLRF = $HR * $ratelr * $sumLRF;
												$empLRF = $empid;
												$totalOT += $rateLRF; 
												//if($sumLRF != 0 || $rateLRF != 0)												
												//{															
														echo "<td>" . $sumLRF . "</td>														      
															  <td>" . number_format($rateLRF,2) . "</td>";													
												//}																					
									}												
									elseif(isset($ctr12A) && ($ctr12A>0))														
									{														
												echo "<td> - </td>														      
													  <td> - </td>";									
									}
									
									
									//for rest day legal holiday OT													
									if(!empty($summary->rdlhot) && ($summary->rdlhot > 0))												{			
												$sumLRX = $summary->rdlhot;
												$codeLRX = 'LRX';
												$coderateLRX = DB::table('tbl_ot_rates')->where('Code', $codeLRX)->first();
												$ratelx = $coderateLRX->Rate;
												$rateLRX = $HR * $ratelx * $sumLRX;
												$empLRX = $empid;
												$totalOT += $rateLRX; 
												//if($sumLRX != 0 || $rateLRX != 0)			
												//{															
														echo "<td>" . $sumLRX . "</td>														      
															  <td>" . number_format($rateLRX,2) . "</td>";													
												//}									
									}
									elseif(isset($ctr13A) && ($ctr13A>0))															
									{
												echo "<td> - </td>														     
													  <td> - </td>";									
									}
									
									
									//for rest day legal holiday OT ND									
									if(!empty($summary->rdlhond) && ($summary->rdlhond > 0))																	
									{	
												$sumLRN = $summary->rdlhond;
												$codeLRN = 'LRN';
												$coderateLRN = DB::table('tbl_ot_rates')->where('Code', $codeLRN)->first();
												$rateln = $coderateLRN->Rate;
												$rateLRN = $HR * $rateln * $sumLRN;
												$empLRN = $empid;
												$totalOT += $rateLRN; 
												//if($sumLRN != 0 || $rateLRN != 0)												
												//{															
														echo "<td>" . $sumLRN . "</td>														      
														<td>" . number_format($rateLRN,2) . "</td>";													
												//}
									}
									elseif(isset($ctr14A) && ($ctr14A>0))														
									{														
												echo "<td> - </td>														      
													  <td> - </td>";	
									}
									
									
									//for rest day legal holiday ND									
									if(!empty($summary->rdlhnd) && ($summary->rdlhnd > 0))															
									{
												$sumLRN2 = $summary->rdlhnd;
												$codeLRN2 = 'LRN';
												$coderateLRN2 = DB::table('tbl_ot_rates')->where('Code', $codeLRN2)->first();
												$rateln2 = $coderateLRN2->Rate;
												$rateLRN2 = $HR * $rateln2 * $sumLRN2;
												$empLRN2 = $empid;
												$totalOT += $rateLRN2; 
												//if($sumLRN2 != 0 || $rateLRN2 != 0)												
												//{															
														echo "<td>" . $sumLRN2 . "</td>														      
															  <td>" . number_format($rateLRN2,2) . "</td>";													
												//}									
									}										
									elseif(isset($ctr15A) && ($ctr15A>0))												
									{														
														echo "<td> - </td>														      
															  <td> - </td>";									
									}
									

									//for special holiday OT									
									if(!empty($summary->sho) && ($summary->sho > 0))										
									{
												$sumSOX = $summary->sho;
												$codeSOX = 'SOX';
												$coderateSOX = DB::table('tbl_ot_rates')->where('Code', $codeSOX)->first();
												$rate_sox = $coderateSOX->Rate;
												$rateSOX = $HR * $rate_sox * $sumSOX;
												$empSOX = $empid;
												$totalOT += $rateSOX; 
												//if($sumSOX != 0 || $rateSOX != 0)												
												//{															
														echo "<td>" . $sumSOX . "</td>														      
															 <td>" . number_format($rateSOX,2) . "</td>";													
												//}									
									}												
									elseif(isset($ctr16A) && ($ctr16A>0))			
									{														
												echo "<td> - </td>														      
													  <td> - </td>";									
									}																					
									
									
									//for special holiday OT ND									
									if(!empty($summary->shond) && ($summary->shond > 0))																
									{
												$sumRDN2 = $summary->shond;
												$codeRDN2 = 'RDN';
												$coderateRDN2 = DB::table('tbl_ot_rates')->where('Code', $codeRDN2)->first();
												$ratern2 = $coderateRDN2->Rate;
												$rateRDN2 = $HR * $ratern2 * $sumRDN2;
												$empRDN2 = $empid;
												$totalOT += $rateRDN2; 
												//if($sumRDN2 != 0 || $rateRDN2 != 0)												
												//{															
															echo "<td>" . $sumRDN2 . "</td>														      
																  <td>" . number_format($rateRDN2,2) . "</td>";													
												//}									
									}										
									elseif(isset($ctr17A) && ($ctr17A>0))												
									{														
												echo "<td> - </td>														      
												     <td> - </td>";									
									}
									
									
									//for special holiday ND									
									if(!empty($summary->shnd) && ($summary->shnd > 0))																		
									{
												$sumRDN3 = $summary->shnd;
												$codeRDN3 = 'RDN';
												$coderateRDN3 = DB::table('tbl_ot_rates')->where('Code', $codeRDN3)->first();
												$ratern3 = $coderateRDN3->Rate;
												$rateRDN3 = $HR * $ratern3 * $sumRDN3;
												$empRDN3 = $empid;
												$totalOT += $rateRDN3; 
												//if($sumRDN3 != 0 || $rateRDN3 != 0)												
												//{															
															echo "<td>" . $sumRDN3 . "</td>														      
																  <td>" . number_format($rateRDN3,2) . "</td>";													
												//}									
									}									
									elseif(isset($ctr18A) && ($ctr18A>0))															
									{														
												echo "<td> - </td>
													 <td> - </td>";									
									}
									

									//for legal holiday									
									if(!empty($summary->lh) && ($summary->lh > 0))													
									{
												$sumLHF = $summary->lh;
												$codeLHF = 'LHF';
												$coderateLHF = DB::table('tbl_ot_rates')->where('Code', $codeLHF)->first();
												$ratelf = $coderateLHF->Rate;
												$rateLHF = $HR * $ratelf * $sumLHF;
												$empLHF = $empid;
												$totalOT += $rateLHF; 
												//if($sumLHF != 0 || $rateLHF != 0)												
												//{															
														echo "<td>" . $sumLHF . "</td>														      
														      <td>" . number_format($rateLHF,2) . "</td>";													
												//}									
									}											
									elseif(isset($ctr19A) && ($ctr19A>0))													
									{														
												echo "<td> - </td>														      
													  <td> - </td>";									
									}
									

									//for legal holiday OT									
									if(!empty($summary->lho) && ($summary->lho > 0))											
									{
												$sumLHX = $summary->lho;
												$codeLHX = 'LHX';
												$coderateLHX = DB::table('tbl_ot_rates')->where('Code', $codeLHX)->first();
												$rate_lh = $coderateLHX->Rate;
												$rateLHX = $HR * $rate_lh * $sumLHX;
												$empLHX = $empid;
												$totalOT += $rateLHX; 
												//if($sumLHX != 0 || $rateLHX != 0)												
												//{															
														echo "<td>" . $sumLHX . "</td>														      
														<td>" . number_format($rateLHX,2) . "</td>";													
												//}									
									}										
									elseif(isset($ctr20A) && ($ctr20A>0))												
									{														
												echo "<td> - </td>														      
												      <td> - </td>";									
									}
													
													
									//for legal holiday OT ND									
									if(!empty($summary->lhond) && ($summary->lhond > 0))															
									{
												$sumLHN = $summary->lhond;
												$codeLHN = 'LHN';
												$coderateLHN = DB::table('tbl_ot_rates')->where('Code', $codeLHN)->first();
												$rate_ln = $coderateLHN->Rate;
												$rateLHN = $HR * $rate_ln * $sumLHN;
												$empLHN = $empid;
												$totalOT += $rateLHN; 
												//if($sumLHN != 0 || $rateLHN != 0)												
												//{															
														echo "<td>" . $sumLHN . "</td>														      
														      <td>" . number_format($rateLHN,2) . "</td>";													
												//}									
									}												
									elseif(isset($ctr21A) && ($ctr21A>0))														
									{														
												echo "<td> - </td>														      
													  <td> - </td>";									
									}	
																					
									
									//for legal holiday ND									
									if(!empty($summary->lhnd) && ($summary->lhnd > 0))																
									{
												$sumLHN2 = $summary->lhnd;
												$codeLHN2 = 'LHN';
												$coderateLHN2 = DB::table('tbl_ot_rates')->where('Code', $codeLHN2)->first();
												$rate_ln2 = $coderateLHN2->Rate;
												$rateLHN2 = $HR * $rate_ln2 * $sumLHN2;
												$empLHN2 = $empid;
												$totalOT += $rateLHN2; 												
												//if($sumLHN2 != 0 || $rateLHN2 != 0)												
												//{															
														echo "<td>" . $sumLHN2 . "</td>														      
														<td>" . number_format($rateLHN2,2) . "</td>";													
												//}									
									}									
									elseif(isset($ctr22A) && ($ctr22A>0))														
									{														
												echo "<td> - </td>														      
													  <td> - </td>";									
									}
												
												
												//$taxable_income = $GP - $total_contrib;
												$GP = $BP - ($amt_lates + $amt_absences) + $totalOT;
												$taxable_income = $BP + $totalOT - $total_contrib - $amt_lates - $amt_absences;
												
												$taxs = DB::table('tbl_tax')
									                ->where('starting_salary', '<=', $taxable_income)
									                ->where('ending_salary', '>=', $taxable_income)
									                ->where('tax_status', '=', $taxstatus)
									                //->orWhere('end_salary', '<', $salary)
									                ->get();
											
												foreach($taxs as $tax)
												{
													$percent = $tax->percentage;
													$exempt = $tax->exemption;
													$tax_bracket = $tax->starting_salary;

												}
												
												$wtax =($taxable_income - $tax_bracket) * $percent + $exempt;
												$total_deduc = $total_contrib + $wtax;
												$netpay = $GP - $total_deduc + $totalnottax;
												$OT[$wtax] = $wtax;
												
									?>
												
												<!--td> - </td>
												<td> - </td>
												<td> - </td>
												<td> - </td-->
												<td><?php echo number_format($totalOT,2); ?></td>
									<?php 
										if($summary->previous_OT_adjustment > 0.00)
										{
												echo '<td> ' . $summary->previous_OT_adjustment . '</td>';
										}	
										else
										{
												echo '<td> - </td>';
										} 
										
										
										if($summary->previous_payroll_adjustment > 0.00)
										{
												echo '<td> ' . $summary->previous_payroll_adjustment . '</td>';
										}	
										else
										{
												echo '<td> - </td>';
										}
										
										
										if($summary->retro_payment_adjustment > 0.00)
										{
												echo '<td> ' . $summary->retro_payment_adjustment . '</td>';
										}	
										else
										{
												echo '<td> - </td>';
										}
										
										
										if($summary->transportation_allowance > 0.00)
										{
												echo '<td> ' . $summary->transportation_allowance . '</td>';
										}	
										else
										{
												echo '<td> - </td>';
										} ?>
										
												<td><?php echo number_format($GP,2); ?></td>	
												
										<?php 
										
												if(!empty($summary->ecola) && $summary->ecola > 0.00)
												{
														echo '<td> ' . $summary->ecola . '</td>';
												}	
												else
												{
														echo '<td> - </td>';
												} 	
												
														
												//if(!empty($summary->deminimis) && $summary->deminimis > 0.00)
												if(!empty($deminimis) && $deminimis > 0.00)
												{
														//echo '<td> ' . $summary->deminimis . '</td>';
														echo '<td> ' . $deminimis . '</td>';
												}	
												else
												{
														echo '<td> - </td>';
												}
												
												
												if(!empty($summary->mobile_allowance) && $summary->mobile_allowance > 0.00)
												{
														echo '<td> ' . $summary->mobile_allowance . '</td>';
												}	
												else
												{
														echo '<td> - </td>';
												}
												
												
												if(!empty($summary->previous_payroll) && $summary->previous_payroll > 0.00)
												{
														echo '<td> ' . $summary->previous_payroll . '</td>';
												}	
												else
												{
														echo '<td> - </td>';
												}
												
												
												if(!empty($summary->reimbursible_allowance) && $summary->reimbursible_allowance > 0.00)
												{
														echo '<td> ' . $summary->reimbursible_allowance . '</td>';
												}	
												else
												{
														echo '<td> - </td>';
												}
												
												
												if(!empty($summary->retro_payment) && $summary->retro_payment > 0.00)
												{
														echo '<td> ' . $summary->retro_payment . '</td>';
												}	
												else
												{
														echo '<td> - </td>';
												}
	
											
										//for total non-taxable earnings
										if($totalnottax > 0.00)
										{
												echo '<td> ' . $totalnottax . '</td>';
										}	
										else
										{
												echo '<td> - </td>';
										}
										
			
											if($cutoffF == 26 && $cutoffT == 10)
											{ ?>
												<td> - </td>
												<td><?php echo $phealth_phee; ?></td>
												<td><?php echo $pagibig_employee; ?></td>
												<td> - </td>
												<td> - </td>
												<td><?php echo $phealth_pher; ?></td>
												<td><?php echo $pagibig_employer; ?></td>
									<?php	}
											elseif($cutoffF == 11 && $cutoffT == 25)
											{ 
												$sss_employee = $sss_tcee;
												$sss_employer = $sss_tcer;
											?>
												<td><?php echo $sss_tcee; ?></td>
												<td> - </td>
												<td> - </td>
												<td><?php echo $sss_tcer; ?></td>
												<td><?php echo $sss_tctotal ?></td>
												<td> - </td>
												<td> - </td>
									<?php	} 
												
												//earnings
													if($cash_advance > 0.00)
													{
															$total_deduc = $total_deduc + $cash_advance;
															echo '<td> ' . $cash_advance . '</td>';
													}	
													else
													{
															echo '<td> - </td>';
													}
													
													if($company_loan > 0.00)
													{
															$total_deduc = $total_deduc + $company_loan;
															echo '<td> ' . $company_loan . '</td>';
													}	
													else
													{
															echo '<td> - </td>';
													}
													
													
													if($hdmf_loan > 0.00)
													{
															$total_deduc = $total_deduc + $hdmf_loan;
															echo '<td> ' . $hdmf_loan . '</td>';
													}	
													else
													{
															echo '<td> - </td>';
													}
													
													
													if($hmo_dep > 0.00)
													{
															$total_deduc = $total_deduc + $hmo_dep;
															echo '<td> ' . $hmo_dep . '</td>';
													}	
													else
													{
															echo '<td> - </td>';
													}
													
													
													if($sss_salary_loan > 0.00)
													{
															$total_deduc = $total_deduc + $sss_salary_loan;
															echo '<td> ' . $sss_salary_loan . '</td>';
													}	
													else
													{
															echo '<td> - </td>';
													}
													
													
													if($telephone_charges > 0.00)
													{
															$total_deduc = $total_deduc + $telephone_charges;
															echo '<td> ' . $telephone_charges . '</td>';
													}	
													else
													{
															echo '<td> - </td>';
													}
											
											$netpay = $GP - $total_deduc + $totalnottax;
											
											?>
										
											
												<td><?php echo number_format($taxable_income,2); ?></td>
												<td><?php echo number_format($wtax,2); ?></td>
												<td><?php echo number_format($total_deduc,2); ?></td>
												<td><?php echo number_format($netpay,2); ?></td>
											</tr>
								<?php
								
											/*$arr_SOM = array();
											$arr_ROT = array();
											$arr_SRF = array();
											$arr_RON = array();
											$arr_NSN = array();
											$arr_RDF = array();
											$arr_RDF2 = array();
											$arr_RDX = array();
											$arr_RDN = array();
											$arr_SRX = array();
											$arr_SRN = array();
											$arr_SRN2 = array();
											$arr_LRF = array();
											$arr_LRX = array();
											$arr_LRN = array();
											$arr_LRN2 = array();
											$arr_SOX = array();
											$arr_RDN3 = array();
											$arr_LHF = array();
											$arr_LHX = array();
											$arr_LHN = array();
											$arr_LHN2 = array();*/
											
											
											$arrSOM = array();
											$arrROT = array();
											$arrSRF = array();
											$arrRON = array();
											$arrNSN = array();
											$arrRDF = array();
											$arrRDF2 = array();
											$arrRDX = array();
											$arrRDN = array();
											$arrSRX = array();
											$arrSRN = array();
											$arrSRN2 = array();
											$arrLRF = array();
											$arrLRX = array();
											$arrLRN = array();
											$arrLRN2 = array();
											$arrSOX = array();
											$arrRDN3 = array();
											$arrLHF = array();
											$arrLHX = array();
											$arrLHN = array();
											$arrLHN2 = array(); 

											//$arr_data = array();
											
											//for($i=0; $i<sizeof($summaries); $i++)
											//{
											
												$arr_data[] = array('emp_number' => $empid,
																'cutfrom'  => $cutoffFrom,
																'cutto'   => $cutoffTo,
																'sumlates' => $sumlates,
																'amt_lates' => $amt_lates,
																'sumabsences' => $sumabsences,
																'amt_absences' => $amt_absences,
																'sum_sl' => $sumSL,
																'amt_sl' => $amtSL,
																'sum_vl' => $sumVL,
																'amt_vl' => $amtVL,
																'sum_ml' => $sumML,
																'amt_ml' => $amtML,
																'sum_pl' => $sumPL,
																'amt_pl' => $amtPL,
																'total_ot' => $totalOT,
																'gross_pay' => $GP,
																'wtax_basis' => $taxable_income,
																'wtax' => $wtax,
																'total_deductions' => $total_deduc,
																'totalnottax' => $totalnottax,
																'totaltax' => $totaltax,
																'net_pay' => $netpay,
																'sss_ec' => $sss_employee,
																'philhealth_ec' => $phealth_phee,
																'pagibig_ec' => $pagibig_employee,
																'sss_es' => $sss_employer,
																'ec_es' => $sss_tctotal,
																'philhealth_es' => $phealth_pher,
																'pagibig_es' => $pagibig_employer);
																
										if(!empty($summary->empregular_overtime))
										{			
												$arr_ROT[] = array('emp_number' => $empid,
																'total_num_hrs' => $sumROT,
																'overtime_type' => "ROT",
																'total_amt' => $rateROT,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->splemp_holiday))
										{
												$arr_SOM[] = array('emp_number' => $empSOM,
																'total_num_hrs' => $sumSOM,
																'overtime_type' => "SOM",
																'total_amt' => $rateSOM,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->rdspl_holiday))
										{
												$arr_SRF[] = array('emp_number' => $empSRF,
																'total_num_hrs' => $sumSRF,
																'overtime_type' => "SRF",
																'total_amt' => $rateSRF,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										} ?>
												<!--input type="hidden" name="sumSRF" value="<?php //echo $sumSRF; ?>">
												<input type="hidden" name="rateSRF" value="<?php //echo $rateSRF; ?>">
												<input type="hidden" name="codeSRF" value="SRF"-->
									<?php	
										if(!empty($summary->rond))
										{
												$arr_RON[] = array('emp_number' => $empRON,
																'total_num_hrs' => $sumRON,
																'overtime_type' => "RON",
																'total_amt' => $rateRON,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
												
										}
										
										
										if(!empty($summary->rnd))
										{
												$arr_NSN[] = array('emp_number' => $empNSN,
																'total_num_hrs' => $sumNSN,
																'overtime_type' => "NSN",
																'total_amt' => $rateNSN,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->rd))
										{
												$arr_RDF[] = array('emp_number' => $empRDF,
																'total_num_hrs' => $sumRDF,
																'overtime_type' => "RDF",
																'total_amt' => $rateRDF,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->rdot))
										{
												$arr_RDX[] = array('emp_number' => $empRDX,
																'total_num_hrs' => $sumRDX,
																'overtime_type' => "RDX",
																'total_amt' => $rateRDX,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->rdond))
										{
												$arr_RDF2[] = array('emp_number' => $empRDF2,
																'total_num_hrs' => $sumRDF2,
																'overtime_type' => "RDF",
																'total_amt' => $rateRDF2,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->rdnd))
										{
												$arr_RDN[] = array('emp_number' => $empRDN,
																'total_num_hrs' => $sumRDN,
																'overtime_type' => "RDN",
																'total_amt' => $rateRDN,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->rdsho))
										{
												$arr_SRX[] = array('emp_number' => $empSRX,
																'total_num_hrs' => $sumSRX,
																'overtime_type' => "SRX",
																'total_amt' => $rateSRX,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->rdshond))
										{
												$arr_SRN[] = array('emp_number' => $empSRN,
																'total_num_hrs' => $sumSRN,
																'overtime_type' => "SRN",
																'total_amt' => $rateSRN,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->rdshnd))
										{
												$arr_SRN2[] = array('emp_number' => $empSRN2,
																'total_num_hrs' => $sumSRN2,
																'overtime_type' => "SRN2",
																'total_amt' => $rateSRN2,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}


										if(!empty($summary->rdshnd))
										{
												$arr_LRF[] = array('emp_number' => $empLRF,
																'total_num_hrs' => $sumLRF,
																'overtime_type' => "LRF",
																'total_amt' => $rateLRF,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->rdlhot))
										{
												$arr_LRX[] = array('emp_number' => $empLRX,
																'total_num_hrs' => $sumLRX,
																'overtime_type' => "LRX",
																'total_amt' => $rateLRX,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->rdlhond))
										{
												$arr_LRN[] = array('emp_number' => $empLRN,
																'total_num_hrs' => $sumLRN,
																'overtime_type' => "LRN",
																'total_amt' => $rateLRN,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->rdlhnd))
										{
												$arr_LRN2[] = array('emp_number' => $empLRN2,
																'total_num_hrs' => $sumLRN2,
																'overtime_type' => "SOM",
																'total_amt' => $rateSOM,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->sho))
										{
												$arr_SOX[] = array('emp_number' => $empSOX,
																'total_num_hrs' => $sumSOX,
																'overtime_type' => "SOX",
																'total_amt' => $rateSOM,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}

										
										if(!empty($summary->shnd))
										{
											$arr_RDN3[] = array('emp_number' => $empRDN3,
																'total_num_hrs' => $sumRDN3,
																'overtime_type' => "RDN",
																'total_amt' => $rateRDN3,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}


										if(!empty($summary->shond))
										{
											$arr_RDN2[] = array('emp_number' => $empRDN2,
																'total_num_hrs' => $sumRDN2,
																'overtime_type' => "RDN",
																'total_amt' => $rateRDN2,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}


										if(!empty($summary->lh))
										{
											$arr_LHF[] = array('emp_number' => $empLHF,
																'total_num_hrs' => $sumLHF,
																'overtime_type' => "LHF",
																'total_amt' => $rateLHF,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										
										if(!empty($summary->lho))
										{
												$arr_LHX[] = array('emp_number' => $empLHX,
																'total_num_hrs' => $sumLHX,
																'overtime_type' => "LHX",
																'total_amt' => $rateLHX,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										if(!empty($summary->lhond))
										{
												$arr_LHN[] = array('emp_number' => $empLHN,
																'total_num_hrs' => $sumLHN,
																'overtime_type' => "LHN",
																'total_amt' => $rateLHN,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
										}
										
										
										
										if(!empty($summary->lhnd))
										{
												$arr_LHN2[] = array('emp_number' => $empLHN2,
																'total_num_hrs' => $sumLHN2,
																'overtime_type' => "LHN",
																'total_amt' => $rateSOM,
																'cutfrom' => $cutoffFrom,
																'cutto' => $cutoffTo);
												
										}
										//var_dump($arr_RON);	
										/*$arrROT[] = $arr_ROT;
										$arrSOM[] =  $arr_SOM;
										$arrSRF[] = $arr_SRF;
										$arrRON[]= $arr_RON;
										$arrNSN[] = $arr_NSN;
										$arrRDF[] = $arr_RDF;
										$arrRDX[] = $arr_RDX;
										$arrRDF2[] = $arr_RDF2;
										$arrRDN[] = $arr_RDN;
										$arrSRX[] = $arr_SRX;
										$arrSRN[] = $arr_SRN;
										$arrSRN2[] = $arr_SRN2;
										$arrLRF[] = $arr_LRF;
										$arrLRX[] = $arr_LRX;
										$arrLRN[] = $arr_LRN;
										$arrLRN2[] = $arr_LRN2;
										$arrSOX[] = $arr_SOX;
										$arrRDN3[] = $arr_RDN3;
										$arrLHF[] = $arr_LHF;
										$arrLHX[] = $arr_LHX;
										$arrLHN[] = $arr_LHN;
										$arrLHN2[] = $arr_LHN2;*/
										//var_dump($arrNSN);
										
										/*$items = array();

										foreach($arr_RON as $ron) 
										{
											echo $ron->;
										}*/
										
						        	}  //end of array summaries
									
										//var_dump($arr_RON);		
										//print_r(array_values($arr_RON));
									if(!empty($arr_data))
										Session::put('arr_values', $arr_data);
										
									if(!empty($arr_ROT))
										Session::put('arr_ROT', $arr_ROT);
									
									if(!empty($arr_SOM))
										Session::put('arr_SOM', $arr_SOM);
									
									if(!empty($arr_SRF))
										Session::put('arr_SRF', $arr_SRF);
									
									if(!empty($arr_RON))
										Session::put('arr_RON', $arr_RON);
									
									if(!empty($arr_NSN))
										Session::put('arr_NSN', $arr_NSN);
									
									if(!empty($arr_RDF))
										Session::put('arr_RDF', $arr_RDF);
									
									if(!empty($arr_RDX))
										Session::put('arr_RDX', $arr_RDX);
									
									if(!empty($arr_RDF2))
										Session::put('arr_RDF2', $arr_RDF2);
									
									if(!empty($arr_RDN))
										Session::put('arr_RDN', $arr_RDN);
									
									if(!empty($arr_SRX))
										Session::put('arr_SRX', $arr_SRX);
									
									if(!empty($arr_SRN))
										Session::put('arr_SRN', $arr_SRN);
									
									if(!empty($arr_SRN2))
										Session::put('arr_SRN2', $arr_SRN2);
									
									if(!empty($arr_LRF))
										Session::put('arr_LRF', $arr_LRF);
									
									if(!empty($arr_LRX))
										Session::put('arr_LRX', $arr_LRX);
									
									if(!empty($arr_LRN))
										Session::put('arr_LRN', $arr_LRN);
									
									if(!empty($arr_LRN2))
										Session::put('arr_LRN2', $arr_LRN2);
									
									if(!empty($arr_SOX))
										Session::put('arr_SOX', $arr_SOX);
									
									if(!empty($arr_RDN3))
										Session::put('arr_RDN3', $arr_RDN3);
									
									if(!empty($arr_LHF))
										Session::put('arr_LHF', $arr_LHF);
									
									if(!empty($arr_LHX))
										Session::put('arr_LHX', $arr_LHX);
									
									if(!empty($arr_LHN))
										Session::put('arr_LHN', $arr_LHN);
									
									if(!empty($arr_LHN2))
										Session::put('arr_LHN2', $arr_LHN2);
						        	?>	
							</tbody>
							</table>
				</div>
				<br>
				
				<table cellspacing="30" width = "70%">
					<tr>
						<td>
							{{ Form::open(array('url' => '/admin/payroll/saveData', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}
								
								<!--input type="hidden" name="vals[]" value="<?php //echo $values; ?>"-->
								<input type="hidden" name="cutfrom" value="<?php echo $cutoffFrom; ?>">
								<input type="hidden" name="cutto" value="<?php echo $cutoffTo; ?>">
								<input type="hidden" name="empid" value="<?php echo $empid; ?>">
							{{ Form::submit('Save Payroll Data', array('class' => '', 'class' => 'btn btn-primary')) }}
							{{ Form::close() }}	
						</td>
						<td>
							{{ Form::open(array('url' => '/admin/payroll/publishPayslip', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}
								
								<!--input type="hidden" name="vals[]" value="<?php //echo $values; ?>"-->
								<input type="hidden" name="cutfrom" value="<?php echo $cutoffFrom; ?>">
								<input type="hidden" name="cutto" value="<?php echo $cutoffTo; ?>">
								<input type="hidden" name="empid" value="<?php echo $empid; ?>">
							{{ Form::submit('Publish Payslip', array('class' => '', 'class' => 'btn btn-primary')) }}
							{{ Form::close() }}	
						</td>
						<td>
							{{ Form::open(array('url' => '/admin/payroll/exportXLS', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}
											<input type="hidden" name="cutfrom" value="<?php echo $cutoffFrom; ?>">
											<input type="hidden" name="cutto" value="<?php echo $cutoffTo; ?>">
											<!--input type="hidden" name="vals[]" value="<?php //echo $values; ?>"-->
							{{ Form::submit('Export to Excel', array('class' => '', 'class' => 'btn btn-primary')) }}
							{{ Form::close() }}	
						</td>
						<td>
							{{ Form::open(array('url' => '/admin/payroll/exportPDF', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}
										<input type="hidden" name="cutfrom" value="<?php echo $cutoffFrom; ?>">
										<input type="hidden" name="cutto" value="<?php echo $cutoffTo; ?>">
										<input type="hidden" name="empid" value="<?php echo $empid; ?>">
							{{ Form::submit('Generate Payslip', array('class' => '', 'class' => 'btn btn-primary')) }}
							{{ Form::close() }}									
						</td>
						
						
					</tr>
				</table>

		</div><!--//#content .col-md-12-->           

        <!--/div--><!--//.row-->

      <!--/div--><!--//.page-container-->
	  
	  <!-- Modal -->
		<!--div id="myModal" class="myModal modal fade">
		  <div class="modal-dialog">
			<div class="modal-content">
			  
			  <div id="myValue"></div>
			  <div class="modal-body" >
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				Hello world!
			  </div>
			  
			  <div class="modal-footer"><button type="button" class="btn btn-primary">OK</button></div>
			</div>
		  </div>
		</div-->

		<!--form id="payrollSummaryForm" class="form-horizontal" method="POST" style="display:none;">
			<input type="hidden" name="cafeId" value=""/>
				<fieldset>
					<div class="control-group">
						<label class="control-label" for="cafeName">Where I am Coding</label>
						<div class="controls">
							<input id="cafeName" name="cafeName" class="input-xlarge disabled" type="text" readonly="readonly" type="text" value="B&amp;Js"/>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="date">Date</label>
						<div class="controls">
							<input type="text" class="input-xlarge" id="date" name="date" />
							<p class="help-block"></p>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="date">The value passed is: </label><br>
						<div id="val"></div>
						<div class="controls">
							<input type="text" class="input-xlarge" id="date" name="date" />
							<p class="help-block"></p>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="submit" class="btn btn-primary" value="create" />
							<input type="button" class="btn" value="Cancel" onclick="closeDialog ();" />
						</div>
					</div>
				</fieldset>
		</form-->	


		<!-- The form which is used to populate the item data -->
		<form id="payrollSummaryForm" method="post" class="form-horizontal" style="display: none;">
			<div class="form-group">
				<label class="col-xs-3 control-label">ID</label>
				<div class="col-xs-3">
					<input type="text" class="form-control" name="id" disabled="disabled" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xs-3 control-label">Full name</label>
				<div class="col-xs-5">
					<input type="text" class="form-control" name="name" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xs-3 control-label">Email</label>
				<div class="col-xs-5">
					<input type="text" class="form-control" name="email" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xs-3 control-label">Website</label>
				<div class="col-xs-5">
					<input type="text" class="form-control" name="website" />
				</div>
			</div>

			<div class="form-group">
				<div class="col-xs-5 col-xs-offset-3">
					<button type="submit" class="btn btn-default">Save</button>
				</div>
			</div>
		</form>		
	  
@stop

@section('foot')
	@parent    

<script>

/**
*
* SIDEBAR
*
*/

$(function () {
$('#collapse1, #collapse2, #collapse3').collapse('hide');        
$('#collapse4, #collapse5, #collapse6').collapse('show');        

$('#menu').metisMenu();
});    


//Server Time
function updateServerTime() {

  $.get("{{ route('updateServerTime') }}", function(data, status){    

    //console.log("Data: " + data + "\nStatus: " + status);
    $("#clock").html(data); 

  }); 

}
setInterval('updateServerTime()', 1000);

function getServerDateTime(handleData) {

  var serverDateTime;
  var getServerTime = $.ajax({
    type: "GET",
    url : "{{ route('getServerDateTime') }}", //http://localhost:8000/employee",          
    success : function(data) {
      
      handleData(data);

    }
  },"json");

}

$(document).ready(function() {

  /**
  *
  * INIT: TIMESHEET/SUMMARY
  *
  */  

  getTimesheet();
  getSummary();

  //td.ot-status-btn .ot-apply-btn
  $('#timesheet tbody, #timesheet-ajax tbody').on('click', 'td.ot-status-btn .ot-apply-btn', function () {

        //console.log('td.ot-status-btn .ot-apply-btn');

        var tr = $(this).closest('tr');
        ////var row = table.row( tr );
        var rowIdx = $(this).parent().index(); //row.index(); // start at 0
        ////var rowObj = row.data();    
        
        var TimesheetId = tr.attr('id'); //rowObj.id;
        var dataType;
        //var dataString;

        var dataString = 'timesheetId=' + TimesheetId + '&otStatus=' + -1;

        //console.log(dataString);

        $.ajax({                    
          type: "POST",
          url : "{{ route('redraw.overtimestatus') }}",
          data : dataString,
          success : function(data) {
            
            var overtimeStatusText = $('td.ot-status-btn:eq(' + rowIdx + ') .ot-apply-btn').text();

            if ('Apply OT' === overtimeStatusText) {

              //console.log(data);

              //var dataString = 'timesheetId=' + TimesheetId + '&otStatus=' + -1;                         
              $('td.ot-status-btn:eq(' + rowIdx + ') .ot-apply-btn').remove();
              $('td.ot-status-btn:eq(' + rowIdx + ')').html('<span class=\"label label-success\" style=\"padding: 2px 4px; font-size: 11px;\">Pending</span>');
                
            }

              $.ajax({
                type: "GET",
                url : "{{ route('redraw.timesheet') }}", //http://localhost:8000/employee",
                data : '',
                success : function(data) {
                  //console.log(data);
                  var obj = JSON.parse(data);

                 // console.log(obj.data);

                 //Timesheet
                  getTimesheet();

                }
            },"json");


          },
          dataType: dataType    
        });

   } );     

  /**
  *
  * BUTTON: TIME IN / TIME OUT
  *
  */

  //Time In button
  $('#time-clocking-btn.time-in').click(function(e) {                                                    
      e.preventDefault();

      //console.log('Time In button');

      var employeeNumber = $('#employee-number').val();
      var dayDate = $('#day-date').val();
      var schedIn = $('#sched-in').val();
      var schedOut = $('#sched-out').val();      
      var timeIn = $('#time-in').val();
      var timeIn1 = $('#time-in-1').val();
      var timeIn2 = $('#time-in-2').val();
      var timeIn3 = $('#time-in-3').val();
      var timeNow = $('#time-now').val();

      var timeClocking = 'in';             
      var forgotYesterdayTimeOut = 0;

      var dataString = 'timeclocking=' + timeClocking + '&timenow=' + timeNow + '&employeeno=' + employeeNumber + '&daydate=' + dayDate + '&schedin=' + schedIn + '&schedout=' + schedOut;
          dataString += '&timein=' + timeIn + '&timein1=' + timeIn1 + '&timein2=' + timeIn2 + '&timein3=' + timeIn3 + '&forgotyesterdaytimeout=' + forgotYesterdayTimeOut;                                                    

      //console.log(dataString);

      $.ajax({
          type: "POST",
          url : "{{ route('timeClocking') }}", //http://localhost:8000/employee",
          data : dataString,
          success : function(data) {

            //console.log(data);

            //Timesheet
            getTimesheet('post', 'in');

            //Summary Computation Init
            getSummary();             

            var timeClockingBtn = $('#time-clocking-btn.time-in').text();

            if (timeClockingBtn.toLowerCase() === 'time in') {

                console.log(timeClockingBtn.toLowerCase());

                $('#time-clocking-btn.time-in').addClass('hide').hide();     
                $('#time-clocking-btn.time-out').removeClass('hide').show(); 

                //$("#wait").hide();

            }             

          }
      },"json"); 

  }); 


  //Time Out button
  $('#time-clocking-btn.time-out').click(function(e) {                                                    
      e.preventDefault();

      //console.log('Time Out button');

      var employeeNumber = $('#employee-number').val();
      var dayDate = $('#day-date').val();
      var schedIn = $('#sched-in').val();
      var schedOut = $('#sched-out').val();      
      var timeOut = $('#time-out').val();
      var timeOut1 = $('#time-out-1').val();
      var timeOut2 = $('#time-out-2').val();
      var timeOut3 = $('#time-out-3').val();
      var timeNow = $('#time-now').val();

      var timeClocking = 'out';             
      var forgotYesterdayTimeOut = 0;

      var dataString = 'timeclocking=' + timeClocking + '&timenow=' + timeNow + '&employeeno=' + employeeNumber + '&daydate=' + dayDate + '&schedin=' + schedIn + '&schedout=' + schedOut;
          dataString += '&timeout=' + timeOut + '&timeout1=' + timeOut1 + '&timeout2=' + timeOut2 + '&timeout3=' + timeOut3 + '&forgotyesterdaytimeout=' + forgotYesterdayTimeOut;                                                    

      //console.log(dataString); 

      $.ajax({
          type: "POST",
          url : "{{ route('timeClocking') }}", //http://localhost:8000/employee",
          data : dataString,
          success : function(data) {

            //console.log(data);

            //Timesheet
            getTimesheet('post', 'out');

            //Summary Computation Init
            getSummary(); 

            var timeClockingBtn = $('#time-clocking-btn.time-out').text();

             console.log(timeClockingBtn);

            if (timeClockingBtn.toLowerCase() === 'time out') {

                console.log(timeClockingBtn.toLowerCase());

                $('#time-clocking-btn.time-in').removeClass('hide').show();                               
                $('#time-clocking-btn.time-out').addClass('hide').hide();   

                //$("#wait").hide();                 
           
            }                        

          }
      },"json");                    

  });  

});     

var method = 'get';
function getTimesheet(method, button) {
  
  var message;

  $.ajax({
      type: "GET",
      url : "{{ route('redraw.timesheet') }}", //http://localhost:8000/employee",
      data : '',
      success : function(data) {

        var obj = JSON.parse(data);
        $.each(obj.data, function(i, item) {

          //console.log(item.id);
          
          $(".timesheet-id-"+item.id).text(item.id);
          $(".timesheet-daydate-"+item.id).text(item.daydate);
          $(".timesheet-schedule-"+item.id).text(item.schedule);
          $(".timesheet-in1-"+item.id).text(item.in_1);
          $(".timesheet-out1-"+item.id).text(item.out_1);
          $(".timesheet-in2-"+item.id).text(item.in_2);
          $(".timesheet-out2-"+item.id).text(item.out_2);
          $(".timesheet-in3-"+item.id).text(item.in_3);
          $(".timesheet-out3-"+item.id).text(item.out_3);
          $(".timesheet-nightdifferential-"+item.id).text(item.night_differential);
          $(".timesheet-totalhours-"+item.id).text(item.total_hours);
          $(".timesheet-workhours-"+item.id).text(item.work_hours);
          $(".timesheet-totalovertime-"+item.id).text(item.total_overtime);
          $(".timesheet-tardiness-"+item.id).text(item.tardiness);
          $(".timesheet-undertime-"+item.id).text(item.undertime);
          $(".timesheet-otstatus-"+item.id).html(item.overtime_status);

            if('post' === method && 'in' === button && item.message_in !== "") {
              
              console.log(item.message_in);          
              bootbox.alert(item.message_in);
              
            } else if('post' === method && 'out' === button && item.message_out !== "") {

              console.log(item.message_out);          
              bootbox.alert(item.message_out);

            }
        
        }); 
  


      }

    },"json");
}

function getSummary() {

  //Summary Computation Init
  $.ajax({
      type: "GET",
      url : "{{ route('redraw.summary') }}", //http://localhost:8000/employee",
      data : '',
      success : function(data) {
        
        var obj = JSON.parse(data);

        var lates = '', undertime = '', absences = '', paidVacationLeave = '', paidSickLeave = '', leaveWithoutPay = '', maternityLeave = '', paternityLeave = '';
        var regular = '', regularOt = '', restDay = '', restDayOt = '';
        var restDaySpecialHoliday = '', restDaySpecialHolidayOt = '', restDayLegalHoliday = '', restDayLegalHolidayOt = '';
        var specialHoliday = '', specialHolidayOt = '', legalHoliday = '', legalHolidayOt = '';

        //With Night Diff

        var regularOtNd = '', regularNd = '', restDayOtNd = '', restDayNd = '';
        var restDaySpecialHolidayOtNd = '', restDaySpecialHolidayNd = '', restDayLegalHolidayOtNd = '', restDayLegalHolidayNd = '';
        var specialHolidayOtNd = '', specialHolidayNd = '', legalHolidayOtNd = '', legalHolidayNd = '';                  


        //SUMMARY: 1st column
        lates = obj.data[0].tardiness;
        undertime = obj.data[0].undertime;
        absences = obj.data[0].absences; 

        paidVacationLeave = obj.data[0].paid_vacation_leave; 
        paidSickLeave = obj.data[0].paid_sick_leave;
        leaveWithoutPay = obj.data[0].leave_without_pay;
        maternityLeave = obj.data[0].maternity_leave;
        paternityLeave = obj.data[0].paternity_leave;  

        //SUMMARY: 2nd Column
        regular = obj.data[0].regular;                  
        regularOt = obj.data[0].regular_overtime;                  
        restDay = obj.data[0].rest_day;
        restDayOt = obj.data[0].rest_day_overtime;

        //With Night Diff
        regularOtNd = obj.data[0].regular_overtime_night_diff;
        regularNd = obj.data[0].regular_night_differential;
        restDayOtNd = obj.data[0].rest_day_overtime_night_diff;
        restDayNd = obj.data[0].rest_day_night_diff;


        //SUMMARY: 3rd Column
        restDaySpecialHoliday = obj.data[0].rest_day_special_holiday;
        restDaySpecialHolidayOt = obj.data[0].rest_day_special_holiday_overtime;
        restDayLegalHoliday = obj.data[0].rest_day_legal_holiday;
        restDayLegalHolidayOt = obj.data[0].rest_day_legal_holiday_overtime;

        //With Night Diff
        restDaySpecialHolidayOtNd = obj.data[0].rest_day_special_holiday_overtime_night_diff;
        restDaySpecialHolidayNd = obj.data[0].rest_day_special_holiday_night_diff;
        restDayLegalHolidayOtNd = obj.data[0].rest_day_legal_holiday_overtime_night_diff;
        restDayLegalHolidayNd = obj.data[0].rest_day_legal_holiday_night_diff;


        //SUMMARY: 4th Column
        specialHoliday = obj.data[0].special_holiday;
        specialHolidayOt = obj.data[0].special_holiday_overtime;
        legalHoliday = obj.data[0].legal_holiday;
        legalHolidayOt = obj.data[0].legal_holiday_overtime;

        //With Night Diff
        specialHolidayOtNd = obj.data[0].special_holiday_overtime_night_diff;
        specialHolidayNd = obj.data[0].special_holiday_night_diff;
        legalHolidayOtNd = obj.data[0].legal_holiday_overtime_night_diff;
        legalHolidayNd = obj.data[0].legal_holiday_night_diff;

        //SUMMARY: 1st Column
        if (lates !== '0') {

          $('#lates-ut').text(lates);

        } 

        if (undertime !== '0') {

          $('#lates-ut').text(undertime);

        }    

        if (lates !== '0' && undertime !== '0') {

          $('#lates-ut').text(lates + ' / ' + undertime);

        }

        if (absences !== '0') {

          $('#absences').text(absences);

        }

        if (paidVacationLeave !== '0') {

          $('#paid-sl').text(paidSickLeave);

        }

        if (paidSickLeave !== '0') {

          $('#paid-vl').text(paidVacationLeave);

        }

        if (leaveWithoutPay !== '0') {

          $('#leave-without-pay').text(leaveWithoutPay);

        }

        if (maternityLeave !== '0') {

          $('#maternity-leave').text(maternityLeave);

        }

        if (paternityLeave !== '0') {

          $('#paternity-leave').text(paternityLeave);

        }                                                                        


        //SUMMARY: 2nd Column
        if (regular !== '0') {

          $('#regular').text(regular);

        }

        if (regularOt !== '0') {

          $('#reg-ot').text(regularOt);

        }

        if (restDay !== '0') {

          $('#rd').text(restDay);

        }                  

        if (restDayOt !== '0') {

          $('#rd-ot').text(restDayOt);

        }

        //With Night Diff
        if (regularOtNd !== '0') {

          $('#reg-ot-nd').text(regularOtNd);

        }

        if (regularNd !== '0') {

          $('#reg-nd').text(regularNd);

        }                                    

        if (restDayOtNd !== '0') {

          $('#rd-ot-nd').text(restDayOtNd);

        }                  

        if (restDayNd !== '0') {

          $('#rd-nd').text(restDayNd);

        }


        //SUMMARY: 3rd Column 
        if (restDaySpecialHoliday !== '0') {

          $('#rd-spl-holiday').text(restDaySpecialHoliday);

        }

        if (restDaySpecialHolidayOt !== '0') {

          $('#rd-spl-holiday-ot').text(restDaySpecialHolidayOt);

        }

        if (restDayLegalHoliday !== '0') {

          $('#rd-legal-holiday').text(restDayLegalHoliday);

        }

        if (restDayLegalHolidayOt !== '0') {

          $('#rd-legal-holiday-ot').text(restDayLegalHolidayOt);

        }                  

        //With Night Diff 
        if (restDaySpecialHolidayOtNd !== '0') {

          $('#rd-spl-holiday-ot-nd').text(restDaySpecialHolidayOtNd);

        }

        if (restDaySpecialHolidayNd !== '0') {

          $('#rd-spl-holiday-nd').text(restDaySpecialHolidayNd);

        }

        if (restDayLegalHolidayOtNd !== '0') {

          $('#rd-legal-holiday-ot-nd').text(restDayLegalHolidayOtNd);

        }

        if (restDayLegalHolidayNd !== '0') {

          $('#rd-legal-holiday-nd').text(restDayLegalHolidayNd);

        }                  
                          


        //SUMMARY: 4th Column
        
        if (specialHoliday !== '0') {

          $('#spl-holiday').text(specialHoliday);

        }                                                                     

        if (specialHolidayOt !== '0') {

          $('#spl-holiday-ot').text(specialHolidayOt);

        } 

        if (legalHoliday !== '0') {

          $('#legal-holiday').text(legalHoliday);

        } 

        if (legalHolidayOt !== '0') {

          $('#legal-holiday-ot').text(legalHolidayOt);

        }    

        //With Night Diff
        if (specialHolidayOtNd !== '0') {

          $('#spl-holiday-ot-nd').text(specialHolidayOtNd);

        }                                                                     

        if (specialHolidayNd !== '0') {

          $('#spl-holiday-nd').text(specialHolidayNd);

        } 

        if (legalHolidayOtNd !== '0') {

          $('#legal-holiday-ot-nd').text(legalHolidayOtNd);

        } 

        if (legalHolidayNd !== '0') {

          $('#legal-hoiday-nd').text(legalHolidayNd);

        }                                                                     

  
      }
  },"json");  

}        

</script>	

@stop
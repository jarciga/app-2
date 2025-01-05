 <?php

			$users = Session::get('values');
			$cutfrom = Session::get('cutfrom');
			$cutto = Session::get('cutto');
			
			$timestamp = strtotime($cutfrom);
			$cutoffF = date("d", $timestamp);
	
			$timestamp2 = strtotime($cutto);
			$cutoffT = date("d", $timestamp2);
			//var_dump($payOTs);
							
			/*$summaries = DB::table('employee_summary')
                     ->select(DB::raw('employee_number, firstname, lastname, dailysal, basicpay, tax_status, boph_employee_summary.employee_id, SUM(lates) as lates, SUM(undertime) as undertime, SUM(absent) as absent, SUM(paid_sick_leave) as paid_sick_leave, SUM(paid_vacation_leave) as paid_vacation_leave, SUM(leave_without_pay) as leave_without_pay, SUM(maternity_leave) as maternity_leave, SUM(paternity_leave) as paternity_leave,
                       SUM(regular) as regular, SUM(regular_overtime) as empregular_overtime, SUM(regular_overtime_night_diff) as rond , SUM(regular_night_differential) as rnd, SUM(rest_day) as rd, SUM(rest_day_overtime) as rdot, SUM(rest_day_overtime_night_diff) as rdond, SUM(rest_day_night_differential) as rdnd,
                       SUM(rest_day_special_holiday) as rdspl_holiday, SUM(rest_day_special_holiday_overtime) as rdsho, SUM(rest_day_special_holiday_overtime_night_diff) as rdshond, SUM(rest_day_special_holiday_night_diff) as rdshnd, SUM(rest_day_legal_holiday) as rdlh, SUM(rest_day_legal_holiday_overtime) as rdlhot,
                       SUM(rest_day_legal_holiday_overtime_night_diff) as rdlhond, SUM(rest_day_legal_holiday_night_diff) as rdlhnd, SUM(special_holiday) as splemp_holiday, SUM(special_holiday_overtime) as sho, SUM(special_holiday_overtime_night_diff) as shond, SUM(special_holiday_night_diff) as shnd, SUM(legal_holiday) as lh,
                       SUM(legal_holiday_overtime) as lho, SUM(legal_holiday_overtime_night_diff) as lhond, SUM(legal_holiday_night_diff) as lhnd'))
					 ->join('employee_setting', 'employee_setting.employee_id', '=', 'employee_summary.employee_id')
					 ->join('employees', 'employees.id', '=', 'employee_summary.employee_id')
					 ->join('timesheet_submitted', 'timesheet_submitted.employee_id', '=', 'employee_summary.employee_id')
					 ->whereBetween('daydate', array($cutfrom, $cutto))
					 //->where('is_processed', 0)
					 ->whereIn('employees.id', $users)
					 ->groupBy('employee_summary.employee_id')			 
					 ->get();*/
?>

            <!--h1 class="page-header">Payroll Summary</h1>
			<span>Cuttoff Date: <?php //echo $cutoffFrom . ' to ' . $cutoffTo; ?></span><br-->
	        	<div class="table-responsive">			
					<?php			
							foreach ($payInfos as $payInfo) 
							{
								echo '<table class="table table-striped table-hover table-list display" style="font-size: 9px; width: 100%;">
									  <thead>
										<tr><th colspan="5">BACKOFFICE INC.</th></tr>
										<tr><th colspan="5" align="right">Pay Period: </th></tr>
									  </thead>';
								
							  echo "<tbody>
									<tr>
										<td align='center'>" . $payInfo->employnumber . "</td>
										<td align='center'>" . $payInfo->firstname . ' ' . $payInfo->lastname . "</td>
										<td align='center'>" . $payInfo->tax_status . "</td>
										<td align='center'>" . $payInfo->jobtitle . "</td>
								        <td align='center'>" . $payInfo->departmentname . "</td>
									</tr>";
									
							  echo "<tr>
										<td colspan='5' style='border-bottom:1px black solid;'></td>	
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td align='center'>NAME</td>
										<td align='center'>EXEMPTION</td>
										<td align='center'>POSITION</td>
										<td align='center'>DEPARTMENT</td>
									</tr>
									</tbody>
									</table>";
								
								echo "<table class='table table-striped table-hover table-list display' style='font-size: 9px; width: 100%; border: 1px solid #000000' >
									  <tr>
									  <td style='border-right: 1px black solid;'>
										<table class='table table-striped table-hover table-list display' style='font-size: 9px; width: 100%;'>";
										echo "<tr><td><b>Monthly Rate</b></td><td>" . ($payInfo->basicpay * 2) . "</td><td>&nbsp;</td><td>&nbsp;</td></tr>
											<tr><td>Basic Pay</td><td>" . $payInfo->basicpay . "</td><td>&nbsp;</td><td>&nbsp;</td></tr>
											<tr><td>Absences"; 
									  //if($payInfo->) 				
											if(isset($payInfo->sumabsences) && ($payInfo->sumabsences != 0))
												echo "&nbsp;&nbsp;&nbsp;<strong>" . $payInfo->sumabsences . "</strong></td>";
											else
												echo "&nbsp;&nbsp;&nbsp;</td>";
										echo "<td>(" . $payInfo->amt_absences . ")</td><td>W. Tax</td><td>" . $payInfo->wtax . "</td></tr>";
										echo "<tr><td>Lates";  				
											if(isset($payInfo->sumlates) && ($payInfo->sumlates != 0))
												echo "&nbsp;&nbsp;&nbsp;<strong>" . $payInfo->sumlates . "</strong></td>";
											else
												echo "&nbsp;&nbsp;&nbsp;</td>";
										echo "<td>(" . $payInfo->amt_lates . ")</td>
											  <td>SSS</td>";
										
										if($cutoffF == 11 && $cutoffT == 25)
										{
											  echo "<td>" . $payInfo->sss_ec . "</td>";
										}
										else
										{
											  echo "<td></td>";
										}
											  
										echo  "</tr>
											  <tr><td><b>S. Total</b></td><td>" . ($payInfo->basicpay - ($payInfo->amt_absences + $payInfo->amt_lates)) . "</td><td>HDMF</td>";
										
										if($cutoffF == 26 && $cutoffT == 10)
										{										
											  echo "<td>" . $payInfo->pagibig_ec . "</td>";
										}
										else
										{
											  echo "<td></td>";
										}
											  
										echo  "</tr>
											  <tr><td>SL"; 
									  //if($payInfo->) 				
											if(isset($payInfo->sum_sl) && ($payInfo->sum_sl != 0))
												echo "&nbsp;&nbsp;&nbsp;<strong>" . $payInfo->sum_sl . "</strong></td>";
											else
												echo "&nbsp;&nbsp;&nbsp;</td>";
											
											if(isset($payInfo->amt_sl) && ($payInfo->amt_sl != 0))
												echo "<td>" . $payInfo->amt_sl . "</td>";
											else
												echo "<td></td>";
											
												echo "<td>Philhealth</td>";
												
											if($cutoffF == 26 && $cutoffT == 10)
											{										
												  echo "<td>" . $payInfo->philhealth_ec . "</td>";
											}
											else
											{
												  echo "<td></td>";
											}	
												
										echo "</tr>
											  <tr><td>VL"; 
									  //if($payInfo->) 				
											if(isset($payInfo->sum_vl) && ($payInfo->sum_vl != 0))
												echo "&nbsp;&nbsp;&nbsp;<strong>" . $payInfo->sum_vl . "</strong></td>";
											else
												echo "&nbsp;&nbsp;&nbsp;</td>";
											
											if(isset($payInfo->amt_vl) && ($payInfo->amt_vl != 0))
												echo "<td>" . $payInfo->amt_vl . "</td>";
											else
												echo "<td></td>";
											
											echo "<td>SSS Sal Loan</td><td>" . $payInfo->sss_salary_loan . "</td></tr>
											  <tr><td>Overtime</td><td>" . $payInfo->total_OT . "</td><td>SSS Cal Loan</td><td>&nbsp;</td></tr>";										
										echo  "<tr><td>Taxable Earnings</td>";										
										if(isset($payInfo->total_tax_earnings) && ($payInfo->total_tax_earnings != 0))	
											echo  "<td>" . $payInfo->total_tax_earnings . "</td>";										
										else											
											echo  "<td>&nbsp;</td>";										
										//echo "</tr>";																				
										echo  "<td>HDMF Loan</td><td>&nbsp;</td></tr>";
										echo  "<tr><td>Non-Tax Earnings</td>";										
										if(isset($payInfo->total_nottax_earnings) && ($payInfo->total_nottax_earnings != 0))												
												echo  "<td>" . $payInfo->total_nottax_earnings . "</td>";										
										else											
												echo  "<td>&nbsp;</td>";										
										//echo "</tr>";																														
										echo  "<td>Other Loan</td><td>&nbsp;</td></tr>
											  <tr><td>Taxable (13th M)</td><td></td><td>Other Deduction</td><td>&nbsp;</td></tr>
											  <tr><td>Taxfree (13th M)</td><td></td><td>Deduction</td><td>" . $payInfo->total_deductions . "</td></tr>
											  <tr><td>EARNINGS</td><td>" . $payInfo->wtax_basis . "</td><td>NET PAY</td><td>" . $payInfo->net_pay . "</td></tr>
											  <tr><td colspan='4'>&nbsp;</td></tr>";
					
							   echo	'</table>
									</td>
									<td style="vertical-align: top">
										<table class="table table-striped table-hover table-list display" style="font-size: 9px; width: 90%;">';
											
											echo "<tr><th colspan='5' align='left'><b>OTHER INFO:</b></th></tr>";
											echo "<tr><th colspan='5' align='left'>OVERTIME DETAILS:</th></tr>";
								
												foreach($payOTs as $payOT)
												{
													if($payInfo->empid == $payOT->emp_number)
													{
														echo "<tr>
																<td style='vertical-align: top;'>" . $payOT->OT_name . "</td>
																<td>" . $payOT->total_num_hrs . "</td>
																<td>" . $payOT->total_amt . "</td>
																<td>&nbsp;</td>
																<td>&nbsp;</td>
															</tr>"; 
													}
												}
											
											if((!empty($payInfo->deminimis) && ($payInfo->deminimis > 0)) || (!empty($payInfo->ecola) && ($payInfo->ecola > 0)))
											{
												echo "<tr><th colspan='2' align='left'>OTHER EARNING DETAILS:</th></tr>";
												if(!empty($payInfo->deminimis) && ($payInfo->deminimis > 0))
												{
													echo "<tr>
																<td><strong>Deminimis</strong></td>
																<td>" . $payInfo->deminimis . "</td>
														 </tr>";
												}
												
												if(!empty($payInfo->ecola) && ($payInfo->ecola > 0))
												{
													echo "<tr>
																<td><strong>Ecola</strong></td>
																<td>" . $payInfo->ecola . "</td>
														 </tr>";
												}
											}		
								echo    '</table>
									</td>
									<td style="vertical-align: top">
											<table class="table table-striped table-hover table-list display" style="font-size: 9px; width: 90%;">
												<tr><td colspan="2">Leave Balance</td></tr>
												<tr><td>VL:</td><td>&nbsp;</td></tr>
												<tr><td>SL:</td><td>&nbsp;</td></tr>
											</table>	
									</td>
									</tr>									
									</tbody>
									</table>
									<table>
										<tr>
											<td>----------------------------------------------------------------------------------------------------------------------------------</td>	
										</tr>
									</table>
									';
								}
							

						    ?>	
							
						
<?php
session_start();
require_once 'Library.php';
require_once 'attendenceFunctions.php';
error_reporting("E_ALL");
$db=connectToDB();
?>
<html>
	<head>
	<script>
	function CallPrint() {
		$("#delete").remove();
		$("#print").remove();
		$(".ui-datepicker-trigger").remove();
		$("#divbody input, select, textarea").each(function() {
		    $(this).replaceWith($(this).val());
		});
		if(navigator.userAgent.match(/Chrome/g)=="Chrome") {
			$("#divbody table td,td input,td textarea,td select").css("font-size","11px");
		} else {
			$("#divbody table td,td input,td textarea,td select").css("font-size","12px");
		}
	    var prtContent = document.getElementById("divbody");
	    var WinPrint = window.open('', '', 'letf=0,top=0,width=400,height=400,toolbar=0,scrollbars=0,status=0');
	    WinPrint.document.write("<html>"+
					"<head>"+
						"<style type=\"text/css\">"+
							".errmsg {"+
								"color: red;"+
								"float: right;"+
							"}"+
							".errormsg {"+
								"color: red;"+
							"}"+
							"#mytable"+
							"{"+
					 			"border-spacing: 0;"+
					 			"border-collapse: collapse;"+
					 			"width:60% !important;"+
							"}"+
							"#mytable input {"+
								"width: 70px;"+
								"outline : none;"+
							"}"+
							"#mytable th {"+
	                         	"font-size: 10px;"+
	                        "}"+
						"</style>"+				
					"</head><body>");
	    WinPrint.document.write(prtContent.innerHTML);
	    WinPrint.document.write("</body></html>");
	    WinPrint.document.close();
	    WinPrint.focus();
	    WinPrint.print();
	    WinPrint.close();
}
</script>
	
<style type="text/css" >
	.errmsg {
		color: red;
		float: right;
	}
	.errormsg {
		color: red;
		
	}
	#mytable
	{
	 	border-spacing: 0;
	 	border-collapse: collapse;
	 	width:100% !important;
	}
	#mytable input {
		width: 120px;
		outline : none;
	}
	
	#mytable th {
		font-size: 13px;
	}
	.btn {
		font-size:15px;
	}
</style>
	<?php
		
		if ( empty($_REQUEST)) {
			echo '<script type="text/javascript" src="projectjs/voe.js"></script>';
		}
	?>
	</head>
	<body>
	<?php
		//container fluid div start
		echo '<div class="container-fluid">';
			//row start
			echo '<div class="row" id="divbody">';
			echo '<div class="col-sm-12">';// 12 column start
						echo '<div class="panel panel-primary">'; //panel div start
							echo '<div class="panel-heading text-center">
								<strong style="font-size:20px;">Employee Profile(VOE)</strong>
							</div>';
			
				$fuel = array("diesel", "petrol", "Gas");
				$vehicletypewheeler = array("Four Wheeler", "Two Wheeler");
				$employee_number=$_SESSION['u_empid'] ;
				$sqlquery = "SELECT * FROM empprofile WHERE empid='$employee_number'";
				$sqlquery1=  "SELECT * FROM empprofile WHERE empid='$employee_number' and (empprofile.address!='' or empprofile.phonenumber!='' or empprofile.fathername!='')";
			    $resultset = $db->query($sqlquery);
			    $resultset1 = $db->query($sqlquery1);
			    if ($db->countRows($resultset1)!= 0) {
			    	$row=$db->fetchArray($resultset1);
			    }
			    // Insert employee profile into database
			   if(isset($_REQUEST['profile']))
			   {
					$_REQUEST['residentialAddress']=addslashes($_REQUEST['residentialAddress']);
					$sql="insert into empprofile(empid,address,phonenumber,fathername) values('".$_REQUEST['employee_number']."','".$_REQUEST['residentialAddress']."','".$_REQUEST['phoneNo']."','".$_REQUEST['father_name']."')";
						$res=$db->query($sql);
			   } elseif($db->countRows($resultset1)== 0) {
			   	// Display employee profile form as there is no employee profile data present in database
			   	
			   	echo "<div class='panel-body systemtable'>";
				echo '<form id="AttInd" name="AttInd" method="post" action="voe.php?insert=1">';
					
						$fuel = array("diesel", "petrol", "Gas");
						$vehicletypewheeler = array("Four Wheeler", "Two Wheeler");
						$employee_number=$_SESSION['u_empid'] ;
						$sqlquery = "SELECT * FROM empprofile WHERE empid='$employee_number'";
						$sqlquery1=  "SELECT * FROM empprofile WHERE empid='$employee_number' and (empprofile.address!='' or empprofile.phonenumber!='' or empprofile.fathername!='')";
						$resultset = $db->query($sqlquery);
						$resultset1 = $db->query($sqlquery1);
						if ($db->countRows($resultset1)!= 0) {
							$row=$db->fetchArray($resultset1);
						}
						// Insert employee profile into database
						if(isset($_REQUEST['profile']))
						{
							$_REQUEST['residentialAddress']=addslashes($_REQUEST['residentialAddress']);
							$sql="insert into empprofile(empid,address,phonenumber,fathername) values('".$_REQUEST['employee_number']."','".$_REQUEST['residentialAddress']."','".$_REQUEST['phoneNo']."','".$_REQUEST['father_name']."')";
							$res=$db->query($sql);
						} elseif($db->countRows($resultset1)== 0) {
							// Display employee profile form as there is no employee profile data present in database
							echo "<form name='firstform' id='firstform' method='POST' action='voe.php?profile=1'>";
								//panel body start
								echo "
									<hr><p><strong>NOTE:</strong><i>
									Details of actual expenses incurred on running (including wear & tear) and maintenance 
									of Motor Car owned by the employee for commuting between residence to office and back in 
									excess of amount deductible in Sl No. 2(ii)/Sl No. 1( c)(i) of Rule3(2)(A)
									</i><hr>
								</p>";
								//1st row start
								echo "<div class='form-group'>
									<div class='row'>
										<div class='col-sm-2'>
											<label>Employee ID</label>
										</div>
										<div class='col-sm-4'>
											<input type='text' class='form-control input' id='employee_number' name='employee_number' value='$employee_number'/>
										</div>
										<div class='col-sm-2'>
											<label>Father's Name</label>
										</div>
										<div class='col-sm-4'>
											<input type='text' class='form-control' id='father_name' name='father_name'/>&nbsp;<span class='errmsg' id='errmsg16'></span>
										</div>
									</div>
								</div>";
								//1st row end	
											
								//2nd row start
								echo "<div class='form-group'>
									<div class='row'>
										<div class='col-sm-2'>
											<label>Residential Address</label>
										</div>
										<div class='col-sm-4'>
											<textarea id='residentialAddress' class='form-control' name='residentialAddress'/></textarea>
										</div>
										<div class='col-sm-2'>
											<label>Phone Number</label>
										</div>
										<div class='col-sm-4'>
											<input type='text' class='form-control input' id='phoneNo' name='phoneNo'/>&nbsp;<span class='errmsg' id='errmsg10'></span>
										</div>
									</div>
								</div>";//2nd row end
											
								//3rd row start
								echo "<div class='form-group'>
									<div class='row'>
										<div class='col-sm-12 text-center'>
											<input type='submit' class='btn btn-primary' name='submit' value='submit' />
										</div>
									</div>
								</div>";
							//form end
							echo "</form>";
							//panel body end
							echo "</div>";
						}
					} 
								elseif (isset($_REQUEST['update'])) 
								{
									// Update employee profile information into database
									$_REQUEST['residentialAddress']=addslashes($_REQUEST['residentialAddress']);
									$sql="UPDATE empprofile SET address='".$_REQUEST['residentialAddress']."',phonenumber = '".$_REQUEST['phoneNo']."',fathername = '".$_REQUEST['father_name']."' WHERE empid='$employee_number'";
									$res=$db->query($sql);
								} 
								elseif ($db->countRows($resultset1)!=0  && ($row['address']!="" && $row['phonenumber']!="" && $row['fathername']!="")) 
								{
									// If employee profile information is present in database, then display voe form      
									if(isset($_REQUEST['insert']))
									{
									// Insert voe form into database
										if($_REQUEST['claim_period']==date("F").",".date("Y"))
										{
											echo "dontSubmit";
										}
										else 
										{
											$sql="insert into dynamic_table(employee_number,vehicle_regno,vehicle_model,vehicle_type,nature_offuel,original_cost,claim_period,distance_from_residenceto_office,vehicle_milage,fuelcost_perlitre,total_fuelcost,repairs_maintence_expenses,wear_tear_cost,driver_salary,total_voe) values('".$_REQUEST['employee_number']."','".$_REQUEST['vehicle_regno']."','".$_REQUEST['vehicle_model']."','".$_REQUEST['vehicle_type']."','".$_REQUEST['fuel_nature']."','".$_REQUEST['original_cost']."','".$_REQUEST['claim_period']."','".$_REQUEST['distance_from_residenceto_office']."', '".$_REQUEST['vehicle_milage']."', '".$_REQUEST['fuelcost_perlitre']."', '".$_REQUEST['total_fuelcost']."','".$_REQUEST['repairs_maintence_expenses']."', '".$_REQUEST['wear_tear_cost']."', '".$_REQUEST['driver_salary']."', '".$_REQUEST['total_voe']."')";
											$res=$db->query($sql);
												
											for ($i = 1; $i <= 31; $i++)
											{
												if(isset($_REQUEST['dates'][$i]))
												{
														
													$sql="insert into vehicleuseddays(employee_number,claim_period,vehicleused_dates,vehicleused_days) values('".$_REQUEST['employee_number']."','".$_REQUEST['claim_period']."','".$_REQUEST['dates'][$i]."', '".$_REQUEST['days'][$i]."')";
													$res=$db->query($sql);
												}
												else
												{
													continue;
												}
											}
											if (isset($_REQUEST['specific_date']))
											{
												for($j=0; $j<count($_REQUEST['specific_date']); $j++)
												{
													if (($_REQUEST['specific_date'][$j])!=0){	
														$date= $_REQUEST['specific_date'][$j];
														$dates=explode("/", $date);
														$nmonth = date('m',strtotime($dates[1]));
														$jd=cal_to_jd(CAL_GREGORIAN,$nmonth,$dates[0],$dates[2]);
														$day=jddayofweek($jd,1);
														$sql="insert into specificdays(employee_number,claim_period,vehicleused_dates,vehicleused_days) values('".$_REQUEST['employee_number']."','".$_REQUEST['claim_period']."','$date', '$day')";
														$res=$db->query($sql);
													}
												}
											}
											echo "<script>
														$(\"#loadvoeform\").load('voe.php');
												</script>";
											
										}
									} 
									else 
									{
										//insert employee vehicle details in the form
										echo "<form name='formvoe' id='formvoe' method='POST' action='voe.php?insert=1'>";
										
											echo "<div class='panel-body systemtable'>
												<hr>
												<p><strong>NOTE:</strong><i>
													Details of actual expenses incurred on running (including wear & tear) and maintenance 
													of Motor Car owned by the employee for commuting between residence to office and back in 
													excess of amount deductible in Sl No. 2(ii)/Sl No. 1( c)(i) of Rule3(2)(A)
												</i><hr>
												</p>
											
											<div class='form-group'>
												<div class='row'>
													<div class='col-sm-2'>
														<label>Employee ID</label>
													</div>
													<div class='col-sm-4'>
														<input type='text' readonly class='form-control input' id='employee_number' name='employee_number' value='$employee_number'/>
													</div>";
													$strSQL = "SELECT * FROM dynamic_table WHERE employee_number='$employee_number'";
													$rs = $db->query($strSQL);
													$row1 = $db->fetchArray($rs);
													// If  data about this employee is not presnt in dynamic table
													if($db->countRows($rs) == 0)
													{
														$sql=$db->query("SELECT * FROM `emp`,`empprofile` WHERE emp.empid='$employee_number' and empprofile.empid='$employee_number'");
								
														while ($row = $db->fetchArray($sql))
														{
															$employee_name=$row['empname'];
															$homeAddress=$row['address'];
															$father_name=$row['fathername'];
															$employeeLoc=$row['location'];
															if ($employeeLoc == "BLR") {
																$officialAddress="ECI Telecom India PVt. Ltd., 5th Floor, Innovator Building, ITPL, Whitefield, Bangalore-560066";
															} elseif ($employeeLoc == "MUM") {
																$officialAddress="ECI Telecom India Pvt. Ltd. Unit No 901,9thFloor,Awing Reliable Tech Park, Airoli Thane Belapur Road MIDC, Navi Mumbai-400708.";
															}
															echo "<div class='col-sm-2'>
																	<label>Employee Name</label>
																</div>
																<div class='col-sm-4'>
																	<input type='text' class='form-control' readonly id='employee_name' name='employee_name' value='$employee_name'/>&nbsp;<span class='errmsg' id='errmsg6'></span>
																</div>
															</div>
															</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																			<label>Residential Address</label>
																		</div>
																		<div class='col-sm-4'>
																			<textarea readonly class='form-control' name='residential_address' id='residential_address'>$homeAddress</textarea>
																		</div>
																		<div class='col-sm-2'>
																			<label>Official Address</label>
																		</div>
																		<div class='col-sm-4'>
																			<textarea class='form-control' readonly name='official_address' id='official_address'>$officialAddress</textarea>
																		</div>
																	</div>
																</div>";
													
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																			<label>Claim period</label>
																		</div>
																		<div class='col-sm-4'>
																			<div class='input-group'>
																				<input type='text' class='form-control' id='claim_period' name='claim_period' value='".date("F").",".date("Y")."' onchange='change()'/>
																				<label class='input-group-addon btn' for='claim_period'>
																				   <span class='fa fa-calendar'></span>
																				</label>
																			</div>
																		</div>
																		<div class='col-sm-2'>
																			<label>Vehicle Regn No</label>
																		</div>
																		<div class='col-sm-4'>
																			<input type='text' class='form-control' name='vehicle_regno' id='vehicle_regno'/>
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																			<label>Vehicle make & Model</label>
																		</div>
																		<div class='col-sm-4'>
																			<input type='text' class='form-control' name='vehicle_model' id='vehicle_model'/>
																		</div>
																		<div class='col-sm-2'>
																			<label>Vehicle Type</label>
																		</div>
																		<div class='col-sm-4'>
																			<select name='vehicle_type' class='form-control' id='vehicle_type'>";
																				foreach($vehicletypewheeler as $vehiType)
																				{
																					echo "<option value=$vehiType>$vehiType</option>";    
																				}
																			echo "</select>
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																			<label>Fuel Nature</label>
																		</div>
																		<div class='col-sm-4'>
																			<select class='form-control' name='fuel_nature' id='fuel_nature'>";
																				foreach($fuel as $col)
																				{
																					echo "<option value=$col>$col</option>";    
																				}
																			echo"</select>
																		</div>
																		<div class='col-sm-2'>
																			<label>Vehicle Original Cost </label>
																		</div>
																		<div class='col-sm-4'>
																			<input type='text' class='form-control input' id='vehiclecost' name='original_cost' onchange='weartearCost();'/>&nbsp;<span class='errmsg' id='errmsg5'></span>
																		</div>
																	</div>
																</div>";
													
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																			<label>Fuel cost per litre in INR</label>
																		</div>
																		<div class='col-sm-4'>
																			<input type='text' class='form-control input' id='fuelcost_perlitre' name='fuelcost_perlitre' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg2'></span> 
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-12'>
																			<table class='table table-bordered' id='mytable' ></table>
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																			<label>Repairs & Maintenance expenses incurred</label>
																		</div>
																		<div class='col-sm-4'>
																			<input type='text' class='form-control input' type='text' id='expenses' name='repairs_maintence_expenses' onchange='totalVoe();' value='0'/> &nbsp;<span class='errmsg' id='errmsg3'></span>
																		</div>
																		<div class='col-sm-2'>
																			<label>Driver's salary in INR</label>
																		</div>
																		<div class='col-sm-4'>
																			<input type='text' class='form-control input' id='drivers_salary' name='driver_salary' onchange='totalVoe();' value='0'/> &nbsp;<span class='errmsg' id='errmsg4' ></span>
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																	<div class='col-sm-2'>
																		<label>Wear & tear cost of the vehicle</label>
																	</div>
																	<div class='col-sm-4'>
																		<input type='text' class='form-control input' id='wear_tear_cost' name='wear_tear_cost'/> 
																	</div>
																	<div class='col-sm-4'>
																		<label> Distance between residence and office of employee</label>
																	</div>
																	<div class='col-sm-8'>
																		<input type='text' class='form-control input' id='distance_from_residenceto_office' name='distance_from_residenceto_office' onchange='distance()'/>&nbsp;<span class='errmsg' id='e4rrmsg'></span>
																	</div>
																</div>
															</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-4'>
																			<label>Milage of the vehicle-km/litre</label>
																		</div>
																		<div class='col-sm-8'>
																			<input type='text' class='form-control input' id='vehicle_milage' name='vehicle_milage' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg1'></span>
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-4'>
																			<label>Total VOE eligibility(14+15+16+17)</label>
																		</div>
																		<div class='col-sm-8'>
																			<input type='text' readonly class='form-control input' id='total_voe' name='total_voe' onclick='totalVoe();' />
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group' style='display:none' id='formOf'><div class='row'><div class='><p>I, <b>$employee_name</b> the daughter/son of Mr. <b>$father_name</b> do hereby declare that all the details furnished in this document is true to the best of my information.</p></div></div></div>";
															echo "<div class='form-group'><div class='row'><div class='col-sm-6' style='display:none'id='verification'>Date:</div><div class='col-sm-6 text-center' style='display:none text-align:right;'id='sig'>Signature of Employee</div></div></div>";
															echo "<div class='form-group'><div class='row'><div class='col-sm-4' style='display:none'id='delete'><center><input name='b_print' type='button' class='btn btn-primary ipt' onClick='CallPrint();' value='Print'/></center></div>";
															echo "<div class='col-sm-4 submit text-center' colsapn='2'><input type='submit' class='form-control' name='voesubmit' id='voeSubmit' value='submit' onclick='totalVoe();'/></div>";
															echo"<div class='col-sm-4 text-center' style='display:none' id='delete'><input type='button' class='form-control' name='delete' value='Delete' onclick='deletion()' /></div></div></div>";
																						
														}
													} 
													$count = $db->countRows($rs);
													if($count!=0) {
														$officialAddress="ECI Telecom India PVt. Ltd., 5th Floor, Innovator Building, ITPL, Whitefield, Bangalore-560066";
														//If data, about this employee is not presnt in dynamic table for that specific claim period. Then,
														//get the dynamic table information for previous month using mysql_data_seek
														$sql=$db->query("SELECT * FROM `emp`,`empprofile` WHERE emp.empid='$employee_number' and empprofile.empid='$employee_number'");
														
														while ($row = $db->fetchArray($sql))
														{
															$employee_name=$row['empname'];
															$father_name=$row['fathername'];
															$homeAddress=$row['address'];
															$employeeLoc=$row['location'];
															if ($employeeLoc == "BLR") {
																$officialAddress="ECI Telecom India PVt. Ltd., 5th Floor, Innovator Building, ITPL, Whitefield, Bangalore-560066";
															} elseif ($employeeLoc == "MUM") {
																$officialAddress="ECI Telecom India Pvt. Ltd. Unit No 901,9thFloor,Awing Reliable Tech Park, Airoli Thane Belapur Road MIDC, Navi Mumbai-400708.";
															}
														}
														mysql_data_seek($rs, ($count - 1));
														while ($row = $db->fetchArray($rs))
														{
															$vehicle_regno=$row['vehicle_regno'];
															$vehicle_model=$row['vehicle_model'];
															$vehicle_type=$row['vehicle_type'];
															$fuel_nature= $row['nature_offuel'];
															$original_cost=$row['original_cost'];
															$distance=$row['distance_from_residenceto_office'];
															$milage=$row['vehicle_milage'];
															$fuelcost=$row['fuelcost_perlitre'];
															$repairs=$row['repairs_maintence_expenses'];
															$wear_tearcost=$row['wear_tear_cost'];
															$driversal=$row['driver_salary'];
															$totalvoe=$row['total_voe'];
															echo "
															<div class='col-sm-2'>
																<label>Employee name</label>
															</div>
															<div class='col-sm-4'>
																<input type='text' class='form-control' readonly value='$employee_name' id='employee_name' name='employee_name'/>&nbsp;<span class='errmsg' id='errmsg6'></span>
															</div>
															</div></div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																			<label>Residential address</label>
																		</div>
																		<div class='col-sm-4'>
																			<textarea class='form-control'readonly id='residential_address' name='residential_address'>$homeAddress </textarea>
																		</div>
																		<div class='col-sm-2'>
																			<label>Officiai address</label>
																		</div>
																		<div class='col-sm-4'>
																			<textarea readonly class='form-control' id='official_address' name='official_address'>$officialAddress</textarea>																</div>
																		</div>
																	</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																			<label>Claim period</label>
																		</div>
																		<div class='col-sm-4'>
																			<div class='input-group'>
																				<input type='text' class='form-control' id='claim_period' name='claim_period' value='".date("F").",".date("Y")."' onchange='change()'/>
																				<label class='input-group-addon btn' for='claim_period'>
																				   <span class='fa fa-calendar'></span>
																				</label>
																			</div>
																		</div>
																		<div class='col-sm-2'>
																			<label>Vehicle Regn No</label>
																		</div>
																		<div class='col-sm-4'>
																			<input type='text' class='form-control' value='$vehicle_regno' id='vehicle_regno' name='vehicle_regno'/>
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																			<label>Vehicle make & Model</label>
																		</div>
																		<div class='col-sm-4'>
																			<input type='text' class='form-control' value='$vehicle_model' id='vehicle_model' name='vehicle_model'/>
																		</div>
																		<div class='col-sm-2'>
																			<label>Vehicle Type</label>
																		</div>
																		<div class='col-sm-4'>
																			<select class='form-control' name='vehicle_type' id='vehicle_type'>";
																				foreach($vehicletypewheeler as $vehiType)
																				{
																					if($vehiType==$vehicle_type) {
																						echo "<option value=$vehiType selected>$vehiType</option>";
																					} else {
																						echo "<option value=$vehiType>$vehiType</option>";
																					}
																				}
																			echo "</select>
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																			<label>Fuel Nature</label>
																		</div>
																		<div class='col-sm-4'>
																			<select class='form-control' name='fuel_nature' id='fuel_nature'>";
																				echo "<option value=$fuel_nature>$fuel_nature</option>";
																				for($i=0;$i<sizeof($fuel);$i++)
																				{ 
																					if($fuel[$i]!=$fuel_nature){
																						echo "<option value=$fuel[$i]>$fuel[$i]</option>";
																					}   
																				}
																			echo"</select>
																		</div>
																		<div class='col-sm-2'>
																			<label>Vehicle original cost</label>
																		</div>
																		<div class='col-sm-4'>
																			<input type='text' class='form-control input' id='vehiclecost' value='$original_cost' name='original_cost' onchange='weartearCost()'/><span class='errmsg' id='errmsg5'></span>
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																				<label>Fuel cost per litre in INR</label>
																		</div>
																		<div class='col-sm-4'>
																			<input type='text' class='form-control input' id='fuelcost_perlitre' name='fuelcost_perlitre' value='$fuelcost' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg2'></span>
																		</div>
																		<div class='col-sm-2'>
																			<label>Milage of the vehicle-km/litre</label>
																		</div>
																		<div class='col-sm-4'>
																			<input type='text' class='form-control input' id='vehicle_milage' name='vehicle_milage' value='$milage' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg1'></span>
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-5' class='dynamic'>
																			<label>Distance between residence and office of employee</label>
																		</div>
																		<div class='col-sm-7'>
																			<input type='text' class='form-control input' id='distance_from_residenceto_office' name='distance_from_residenceto_office' value='$distance' onchange='distance()'/>&nbsp;<span class='errmsg' id='errmsg'></span>
																		</div>
																	</div>
																</div>";
															echo "<div class='form-group'>
															<div class='row'>
															<div class='col-sm-12'>
																<table class='table table-bordered' id='mytable'></table>
															</div>
															</div>";
															echo "<div class='form-group'>
															<div class='row'>
															<div class='col-sm-2'>
																<label>Repairs & Maintenance expenses incurred</label>
															</div>
															<div class='col-sm-4'>
																<input type='text' class='form-control input' type='text' id='expenses' name='repairs_maintence_expenses' onchange='totalVoe();' value='$repairs'/> &nbsp;<span class='errmsg' id='errmsg3'></span>
															</div>
															<div class='col-sm-2'>
																<label>Wear & tear cost of the vehicle</label>
															</div>
															<div class='col-sm-4'>
																<input type='text' class='form-control input' id='wear_tear_cost' name='wear_tear_cost' value='$wear_tearcost'/>
															</div>
														</div>
														</div>";
															echo "<div class='form-group'>
																	<div class='row'>
																		<div class='col-sm-2'>
																		<label>Driver's salary in INR</label>
																	</div>
																	<div class='col-sm-4'>
																		<input type='text' class='form-control input' id='drivers_salary' name='driver_salary' onchange='totalVoe();' value='$driversal'/> &nbsp;<span class='errmsg' id='errmsg4'></span>
																	</div>
																	<div class='col-sm-2'>
																		<label>Total VOE eligibility(14+15+16+17)</label>
																	</div>
																	<div class='col-sm-4'>
																		<input type='text' readonly class='form-control input' id='total_voe' name='total_voe' onclick='totalVoe();'/>
																	</div>																	
																</div>
															</div>";
										
													}
													echo "<div class='form-group' style='display:none' id='formOf'><div class='row'><div class='col-sm-12'><p>I, <b>$employee_name</b> the daughter/son of Mr. <b>$father_name</b> do hereby declare that all the details furnished in this document is true to the best of my information.</p></div></div></div>";
													echo "<div class='form-group'><div class='row'><div class='col-sm-6' style='display:none'id='verification'>Date:</div><div class='col-sm-6' style='display:none'id='sig'><center>Signature of Employee</center></div></div></div>";
													echo "<div class='form-group'><div class='row'><div class='col-sm-12 submit text-center'><input type='submit' class='btn btn-primary' id='voeSubmit' name='voesubmit' value='submit' onclick='totalVoe();' /></div></div></div>";
													echo "<div class='form-group'><div class='row'><div class='col-sm-6 text-center' style='display:none' id='delete'><input type='button' class='btn btn-info' name='delete' value='Delete' onclick='deletion()' /></div>";
													echo "<div class='col-sm-6' style='display:none' id='print'><input name='b_print' type='button' class='btn btn-success ipt' onClick='CallPrint();' value='Print' /></div></div></div>";
													
												}
											echo "</div>
										</form>";
									}
								}
								elseif ($db->countRows($resultset1)!=0  && ($row['address']!="" || $row['phonenumber']!="" || $row['fathername']!="")) 
								{
									if(isset($row['address'])) { $empaddress=$row['address']; } else { $empaddress=""; } 
									if(isset($row['phonenumber'])) { $empphonenumber=$row['phonenumber']; } else { $empphonenumber=""; }
									if(isset($row['fathername'])) { $empfathername=$row['fathername']; } else { $empfathername=""; }
									//form to update employee vehicle details
									echo "<form name='firstform' id='firstform' method='POST' action='voe.php?update=1'>";
										echo "<div class='panel-body'>";
										echo "
											<hr><p><strong>NOTE:</strong><i>
												Details of actual expenses incurred on running (including wear & tear) and maintenance 
												of Motor Car owned by the employee for commuting between residence to office and back in 
												excess of amount deductible in Sl No. 2(ii)/Sl No. 1( c)(i) of Rule3(2)(A)
											</i><hr>
											</p>
										
										<div class='form-group'>
											<div class='row'>
												<div class='col-sm-2'>
													<label>Employee ID</label>
												</div>
												<div class='col-sm-4'>
													<input type='text' readonly class='form-control input' id='employee_number' name='employee_number' value='$employee_number'/>
												</div>
												<div class='col-sm-2'>
													<label>Father's Name</label>
												</div>
												<div class='col-sm-4'>
													<input type='text' class='form-control' id='father_name' name='father_name' value='".$empfathername."'/>&nbsp;<span class='errmsg' id='errmsg17'></span>
												</div>
											</div>
										</div>";
										echo "<div class='form-group'>
												<div class='row'>
													<div class='col-sm-2'>
														<label>Residential Address</label>
													</div>
													<div class='col-sm-4'>
														<textarea id='residentialAddress' class='form-control' name='residentialAddress'>$empaddress</textarea>&nbsp;<span class='errmsg' id='errmsg16'></span>
													</div>
													<div class='col-sm-2'>
														<label>Phone Number</label>
													</div>
													<div class='col-sm-4'>
														<input type='text' class='form-control input' id='phoneNo' name='phoneNo' value='".$empphonenumber."'/>&nbsp;<span class='errmsg' id='errmsg10'></span>
													</div>
												</div>
											</div>";
										echo "<div class='form-group'>
												<div class='row'>
													<div class='col-sm-12 text-center' class='submit'>
														<input type='submit' class='btn btn-primary' name='submit' value='submit' />
													</div>
												</div>
											</div>
										</div>
									</form>";
								}
								$db->closeConnection();
							?>
						<?php 
						echo '</div>';// panel body div close 
					echo '</div>';//panel div close
				echo '</form>';//insert form close 
			echo '</div>';// row div close -->
		echo '</div>';//container-fluid div close -->
		?>
	</body>
</html>

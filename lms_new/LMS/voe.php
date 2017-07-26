<?php
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
$db=connectToDB();

?>
<html>
<head>
<?php

if ( empty($_REQUEST)) {
	echo '<script type="text/javascript" src="projectjs/voe.js"></script>';
}
?>
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
				 			"width:100% !important;"+
						"}"+
						"#mytable input {"+
							"width: 120px;"+
							"outline : none;"+
						"}"+
						"#mytable th {"+
                                                        "font-size: 10px;"+
                                                "}"+
						"#table-2 td,$table-2 td input,#table-2 td textarea,#table-2 td select {"+
							"font-size: 15px !important;"+
						"}"+
						"#table-2 td {"+
							"border-bottom: 0px !important;"+
							"border-top: 0px !important;"+
							"line-height:14px !important;"+
						"}"+
						"#table-2 td textarea {"+
					        	"width : 400px !important;"+
					        	"height : 60px !important;"+
					        	"overflow: visible !important;"+
						"}"+
						"div#ecilogo {"+
							"display:inline-block !important;"+
							"line-height:0;"+
						"}"+
						"#ecilogoimg {"+
							"float:left;"+	
							"white-space: nowrap;"+
							"width: 4%;"+
							"padding-right: 20px"+
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

#table-2 td {
	border-bottom: 0px !important;
	border-top: 0px !important;
	line-height:15px !important;
}

#table-2 td textarea {
        width : 400px !important;
        height : 60px !important;
        overflow: visible !important;
}

div#ecilogo {
	display:inline-block !important;
	float: left !important;
	line-height:0;
}

#ecilogoimg {
	float:left;
	padding-right:20px;
	padding-bottom:10px;	
	white-space: nowrap;
	width: 6%;
}

</style>
</head>

<body>
	<div id="divbody">
		<?php
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
			echo "<table id='table-2'>";
			echo"<tr><h3 align='center'>
			<u>Employee Profile(VOE)</u>
			</h3></tr><tr></tr>";
			echo "
 			<tr>
				<td colspan=2><i>
				<hr>
					Details of actual expenses incurred on running (including wear & tear) and maintenance 
					of Motor Car owned by the employee for commuting between residence to office and back in 
					excess of amount deductible in Sl No. 2(ii)/Sl No. 1( c)(i) of Rule3(2)(A)
				<hr>
				</i></td>
			</tr>
 			<tr>
			<td>1.Employee No:</td>
			<td><input type='text' readonly class='input' id='employee_number' name='employee_number' value='$employee_number'/></td>
			</tr>";
			echo "<tr>
			<td>2.Father's Name:</td>
			<td><input type='text' id='father_name' name='father_name'/>&nbsp;<span class='errmsg' id='errmsg16'></span></td>
			</tr>";
			echo "<tr>
			<td>3.Residential Address:</td>
			<td><textarea id='residentialAddress' name='residentialAddress'/></td>
			</tr>";
			echo "<tr>
			<td>4.Phone Number:</td>
			<td><input type='text' class='input' id='phoneNo' name='phoneNo'/>&nbsp;<span class='errmsg' id='errmsg10'></span></td>
			</tr>";
			
			echo "<tr><td class='submit'><input type='submit' name='submit' value='submit' /></tr></td></table></form>";
		   
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
				echo "<form name='formvoe' id='formvoe' method='POST' action='voe.php?insert=1'>";
				echo "<table id='table-2'>";
				echo "<tr>
						<img src='images/ecilogo.png' id='ecilogoimg' alt='ECI Telecom'/>
						<b><div id='ecilogo'><pre style=\"font-size:15px\">ECI Telecom India Pvt. Ltd. Vehicle Operating Expense Voucher(VOE)</pre>
						    </div></b>
					</tr>";
				echo "
				<tr>
					<td colspan=2><i>
					<hr>
						Details of actual expenses incurred on running (including wear & tear) and maintenance 
						of Motor Car owned by the employee for commuting between residence to office and back in 
						excess of amount deductible in Sl No. 2(ii)/Sl No. 1( c)(i) of Rule3(2)(A)
					<hr>
					</i></td>
				</tr>
				<tr>
				<td>1.Employee No:</td>
				<td><input type='text' readonly class='input' id='employee_number' name='employee_number' value='$employee_number'/></td>
				</tr>";
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
						echo "<tr>
						<td>2.Name of the Employee:</td>
						<td><input type='text' readonly id='employee_name' name='employee_name' value='$employee_name'/>&nbsp;<span class='errmsg' id='errmsg6'></span></td>
						</tr>";
						echo "<tr>
						<td>3.Residential address:</td>
						<td><textarea readonly name='residential_address' id='residential_address'>$homeAddress</textarea></td>
						</tr>";
	
						echo "<tr>
						<td>4.Officiai address:</td>
						<td><textarea readonly name='official_address' id='official_address'>$officialAddress</textarea></td>
						</tr>";
						
						echo "<tr><td>5.Claim period:</td>
						<td><input type='text' id='claim_period' name='claim_period' value='".date("F").",".date("Y")."' onchange='change()'/></td>
						</tr>";
						echo "<tr>
						<td>6.Vehicle Regn No:</td>
						<td><input type='text' name='vehicle_regno' id='vehicle_regno'/></td>
						</tr>";
						echo "<tr>
						<td>7.Vehicle make & Model:</td>
						<td><input type='text' name='vehicle_model' id='vehicle_model'/></td>
						</tr>";
						echo "<tr>
                                                <td>8.Vehicle Type:</td>
						<td><select name='vehicle_type' id='vehicle_type'>";
                                                foreach($vehicletypewheeler as $vehiType)
                                                {
						    echo "<option value=$vehiType>$vehiType</option>";    
                                                }
                                                echo "</td></tr>";
						echo "<tr><td>9.Nature of Fuel:</td>
						<td><select name='fuel_nature' id='fuel_nature'>";
						foreach($fuel as $col)
						{
						    echo "<option value=$col>$col</option>";    
						}
						echo"</tr>";
						echo "<tr><td>10.original cost of Vehicle:</td>
						<td><input type='text' class='input' id='vehiclecost' name='original_cost' onchange='weartearCost();'/>&nbsp;<span class='errmsg' id='errmsg5'></span></td>
						</tr>";
						echo "<tr><td>11. Distance between residence and office of employee:</td>
						<td><input type='text' class='input' id='distance_from_residenceto_office' name='distance_from_residenceto_office' onchange='distance()'/>&nbsp;<span class='errmsg' id='e4rrmsg'></span></td>
						</tr>";
						echo "<tr><td>12. Milage of the vehicle-km/litre:</td>
						<td><input type='text' class='input' id='vehicle_milage' name='vehicle_milage' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg1'></span></td>
						</tr>";
						echo "<tr><td>13.Fuel cost per litre in INR:</td>
						<td><input type='text' class='input' id='fuelcost_perlitre' name='fuelcost_perlitre' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg2'></span> </td>
						</tr>";
						echo "<tr><td colspan='2'><table id='mytable'></table>";
						 echo "<tr><td>15.Repairs & Maintenance expenses incurred:</td>
	                                        <td><input type='text' class='input' type='text' id='expenses' name='repairs_maintence_expenses' onchange='totalVoe();' value='0'/> &nbsp;<span class='errmsg' id='errmsg3'></span></td>
	                                        </tr>";
						echo "<tr><td>16.Wear & tear cost of the vehicle:</td>
						<td><input type='text' class='input' id='wear_tear_cost' name='wear_tear_cost'/> </td>
						</tr>";
						echo "<tr><td>17.Driver's salary in INR:</td>
	                                        <td><input type='text' class='input' id='drivers_salary' name='driver_salary' onchange='totalVoe();' value='0'/> &nbsp;<span class='errmsg' id='errmsg4' ></span></td>
	                                        </tr>";
						echo "<tr><td>18.Total VOE eligibility(14+15+16+17):</td>
						<td><input type='text' readonly class='input' id='total_voe' name='total_voe' onclick='totalVoe();' /> </td>
						</tr>";
						echo "<tr style='display:none' id='formOf'><td colspan='2'><p>I, <b>$employee_name</b> the daughter/son of Mr. <b>$father_name</b> do hereby declare that all the details furnished in this document is true to the best of my information.</p></td></tr>";
						echo "<tr><td colspan=2></td></tr><tr><td colspan=2></td></tr><tr><td style='display:none'id='verification'>Date:</td>&nbsp&nbsp&nbsp&nbsp&nbsp<td style='display:none'id='sig'><center>Signature of Employee</center></td></tr>";
						echo "<tr><td style='display:none'id='delete'><center><input name='b_print' type='button' class='ipt' onClick='CallPrint();' value='Print'/></center></td>";
						echo "<td class='submit' colsapn='2'><center><input type='submit' name='voesubmit' id='voeSubmit' value='submit' onclick='totalVoe();'/></center></td>";
						echo"<td style='display:none' id='delete'><center><input type='button' name='delete' value='Delete' onclick='deletion()' /></center></td></tr>";
						
						echo "</table></form>";
					}
				} 
				$count = $db->countRows($rs);
				if($count!=0) {
						$officialAddress="ECI Telecom India PVt. Ltd., 5th Floor, Innovator Building, ITPL, Whitefield, Bangalore-560066";
					//	If data, about this employee is not presnt in dynamic table for that specific claim period. Then,
					//	get the dynamic table information for previous month using mysql_data_seek
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
					    		echo "<tr><td>2.Name of the Employee:</td>
					    		<td><input type='text' readonly value='$employee_name' id='employee_name' name='employee_name'/>&nbsp;<span class='errmsg' id='errmsg6'></span></td>
					    		</tr>";
					    		echo "<tr><td>3.Residential address:</td>
					    		<td><textarea readonly id='residential_address' name='residential_address'>$homeAddress </textarea></td>
					    		</tr>";
					    		echo "<tr><td>4.Officiai address:</td>
					    		<td><textarea readonly id='official_address' name='official_address'>$officialAddress</textarea></td>
					    		</tr>";
					    		echo "<tr><td>5.Claim period:</td>
					    		<td><input type='text' id='claim_period' name='claim_period' value='".date("F").",".date("Y")."' onchange='change()'/></td>
					    		</tr>";
					    		echo "<tr><td>6.Vehicle Regn No:</td>
					    		<td><input type='text'value='$vehicle_regno' id='vehicle_regno' name='vehicle_regno'/></td>
					    		</tr>";
					    		echo "<tr><td>7.Vehicle make & Model:</td>
					    		<td><input type='text'  value='$vehicle_model' id='vehicle_model' name='vehicle_model'/></td>
					    		</tr>";
							echo "<tr><td>8.Vehicle Type:</td>
							<td><select name='vehicle_type' id='vehicle_type'>";
                                                	foreach($vehicletypewheeler as $vehiType)
                                                	{
							    if($vehiType==$vehicle_type) {
	                                        	            echo "<option value=$vehiType selected>$vehiType</option>";
							     } else {
								    echo "<option value=$vehiType>$vehiType</option>";
							     }
                                                	}
                                                	echo "</td></tr>";
					        	echo "<tr><td>9.Nature of Fuel:</td>
										<td><select name='fuel_nature' id='fuel_nature'>";
								      echo "<option value=$fuel_nature>$fuel_nature</option>";
										for($i=0;$i<sizeof($fuel);$i++)
										{ 
										    if($fuel[$i]!=$fuel_nature){
											    echo "<option value=$fuel[$i]>$fuel[$i]</option>";
										    }   
									 	}
								echo"</tr>";
					    		echo "<tr><td>10.original cost of Vehicle:</td>
					    		<td><input type='text' class='input' id='vehiclecost' value='$original_cost' name='original_cost' onchange='weartearCost()'/><span class='errmsg' id='errmsg5'></span></td>
					    		</tr>";
					    		echo "<tr><td class='dynamic'>11. Distance between residence and office of employee:</td>
					    		<td><input type='text' class='input' id='distance_from_residenceto_office' name='distance_from_residenceto_office' value='$distance' onchange='distance()'/>&nbsp;<span class='errmsg' id='errmsg'></span></td>
					    		</tr>";
					    		echo "<tr><td>12. Milage of the vehicle-km/litre:</td>
					    		<td><input type='text' class='input' id='vehicle_milage' name='vehicle_milage' value='$milage' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg1'></span></td>
					    		</tr>";
					    		echo "<tr><td>13.Fuel cost per litre in INR:</td>
					    		<td><input type='text' class='input' id='fuelcost_perlitre' name='fuelcost_perlitre' value='$fuelcost' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg2'></span></td>
					    		</tr>";
					    		echo "<tr><td colspan='2'><table border='1'id='mytable'></table>";
					    		echo "<tr><td>15.Repairs & Maintenance expenses incurred:</td>
					    		<td><input type='text' class='input' type='text' id='expenses' name='repairs_maintence_expenses' onchange='totalVoe();' value='$repairs'/> &nbsp;<span class='errmsg' id='errmsg3'></span></td>
					    		</tr>";
					    		echo "<tr><td>16.Wear & tear cost of the vehicle:</td>
					    		<td><input type='text' class='input' id='wear_tear_cost' name='wear_tear_cost' value='$wear_tearcost'/></td>
					    		</tr>";
					    		echo "<tr><td>17.Driver's salary in INR:</td>
					    		<td><input type='text' class='input' id='drivers_salary' name='driver_salary' onchange='totalVoe();' value='$driversal'/> &nbsp;<span class='errmsg' id='errmsg4'></span></td>
					    		</tr>";
					    		echo "<tr><td>18.Total VOE eligibility(14+15+16+17):</td>
					    		<td><input type='text' readonly class='input' id='total_voe' name='total_voe' onclick='totalVoe();'/></td>
					    		</tr>";
				
					    }
					    echo "<tr style='display:none' id='formOf'><td colspan='2'><p>I, <b>$employee_name</b> the daughter/son of Mr. <b>$father_name</b> do hereby declare that all the details furnished in this document is true to the best of my information.</p></td></tr>";
					    echo "<tr><td colspan=2></td></tr><tr><td colspan=2></td></tr><tr><td style='display:none'id='verification'>Date:</td>&nbsp&nbsp&nbsp&nbsp&nbsp<td style='display:none'id='sig'><center>Signature of Employee</center></td></tr>";
						echo "<tr><td class='submit' colspan='2'><center><input type='submit' id='voeSubmit' name='voesubmit' value='submit' onclick='totalVoe();' /></center></td>";
		    			echo "<td style='display:none' id='delete'><center><input type='button'name='delete' value='Delete' onclick='deletion()' /></center></td>";
		    			echo "<td style='display:none' id='print'><center><input name='b_print' type='button' class='ipt' onClick='CallPrint();' value='Print' /></center></td></tr>";
		    			echo "</table></form>";
					}
			}
		}
        elseif ($db->countRows($resultset1)!=0  && ($row['address']!="" || $row['phonenumber']!="" || $row['fathername']!="")) 
        {
			if(isset($row['address'])) { $empaddress=$row['address']; } else { $empaddress=""; } 
			if(isset($row['phonenumber'])) { $empphonenumber=$row['phonenumber']; } else { $empphonenumber=""; }
			if(isset($row['fathername'])) { $empfathername=$row['fathername']; } else { $empfathername=""; }
		   	echo "<form name='firstform' id='firstform' method='POST' action='voe.php?update=1'>";
		   	echo "<table id='table-2'>";
		   	echo"<tr><h3 align='center'>
		   	<u>Employee Profile(VOE)</u>
		   	</h3></tr><tr></tr>";
		   	echo "
			<tr>
				<td colspan=2><center><i>
				<hr>
					Details of actual expenses incurred on running (including wear & tear) and maintenance 
					of Motor Car owned by the employee for commuting between residence to office and back in 
					excess of amount deductible in Sl No. 2(ii)/Sl No. 1( c)(i) of Rule3(2)(A)
				<hr>
				</i></center></td>
			</tr>
			<tr>
		   	<td>1.Employee No:</td>
		   	<td><input type='text' readonly class='input' id='employee_number' name='employee_number' value='$employee_number'/></td>
		   	</tr>";
		   	echo "<tr>
		   	<td>2.Father's Name:</td>
		   	<td><input type='text' id='father_name' name='father_name' value='".$empfathername."'/>&nbsp;<span class='errmsg' id='errmsg17'></span></td>
		   	</tr>";
		   	echo "<tr>
		   	<td>3.Residential Address:</td>
		   	<td><textarea id='residentialAddress' name='residentialAddress'>$empaddress</textarea>&nbsp;<span class='errmsg' id='errmsg16'></span></td>
		   	</tr>";
		   	echo "<tr>
		   	<td>4.Phone Number:</td>
		   	<td><input type='text' class='input' id='phoneNo' name='phoneNo' value='".$empphonenumber."'/>&nbsp;<span class='errmsg' id='errmsg10'></span></td>
		   	</tr>";
		   	echo "<tr><td class='submit'><input type='submit' name='submit' value='submit' /></tr></td></table></form>";
		 }
		$db->closeConnection();
		?>

	</div>
</body>
</html>

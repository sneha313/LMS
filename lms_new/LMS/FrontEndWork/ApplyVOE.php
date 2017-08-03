<?php
session_start();
require_once '../Library.php';
require_once '../attendenceFunctions.php';
error_reporting("E_ALL");
$db=connectToDB();
?>
<html>
	<head>
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<style>
			.footer1 {
				background: #031432 url("../images/footer/footer-bg.png") repeat scroll left top;
				padding-top: 40px;
				padding-right: 0;
				padding-bottom: 20px;
				padding-left: 0;/*	border-top-width: 4px;
				border-top-style: solid;
				border-top-color: #003;*/
				margin-top:-20px;
				color:white;
			}

			.title-widget {
				color: #898989;
				font-size: 20px;
				font-weight: 300;
				line-height: 1;
				position: relative;
				text-transform: uppercase;
				font-family: 'Fjalla One', sans-serif;
				margin-top: 0;
				margin-right: 0;
				margin-bottom: 25px;
				margin-left: 0;
				padding-left: 28px;
			}

			.title-widget::before {
				background-color: #ea5644;
				content: "";
				height: 22px;
				left: 0px;
				position: absolute;
				top: -2px;
				width: 5px;
			}



			.widget_nav_menu ul {
				list-style: outside none none;
				padding-left: 0;
			}

			.widget_archive ul li {
				background-color: rgba(0, 0, 0, 0.3);
				content: "";
				height: 3px;
				left: 0;
				position: absolute;
				top: 7px;
				width: 3px;
			}


			.widget_nav_menu ul li {
				font-size: 13px;
				font-weight: 700;
				line-height: 20px;
				position: relative;
				text-transform: uppercase;
				border-bottom: 1px solid rgba(0, 0, 0, 0.05);
				margin-bottom: 7px;
				padding-bottom: 7px;
				width:95%;
			}



			.title-median {
				color: #636363;
				font-size: 20px;
				line-height: 20px;
				margin: 0 0 15px;
				text-transform: uppercase;
				font-family: 'Fjalla One', sans-serif;
			}

			.footerp p {font-family: 'Gudea', sans-serif; }


			#social:hover {
    			-webkit-transform:scale(1.1); 
				-moz-transform:scale(1.1); 
				-o-transform:scale(1.1); 
			}
			#social {
				-webkit-transform:scale(0.8);
                /* Browser Variations: */
				-moz-transform:scale(0.8);
				-o-transform:scale(0.8); 
				-webkit-transition-duration: 0.5s; 
				-moz-transition-duration: 0.5s;
				-o-transition-duration: 0.5s;
			}           
			/* Only Needed in Multi-Coloured Variation  */
			.social-fb:hover {
				color: #3B5998;
			}
			.social-tw:hover {
				color: #4099FF;
			}
			.social-gp:hover {
				color: #d34836;
			}
			.social-em:hover {
				color: #f39c12;
			}
			.nomargin { margin:0px; padding:0px;}

			.footer-bottom {
				background-color: #15224f;
				min-height: 30px;
				width: 100%;
				margin-bottom:3px;
			}
			.copyright {
				color: #fff;
				line-height: 30px;
				min-height: 30px;
				padding: 7px 0;
			}
			.design {
				color: #fff;
				line-height: 30px;
				min-height: 30px;
				padding: 7px 0;
				text-align: right;
			}
			.design a {
				color: #fff;
			}
			#img{
				float:left;
			}
			#text{
				margin-left:7px;
				float:left;
				color:grey;
			}
			.navbar-inverse{
				background-color:#031432;
			}
			.rectangle{
				width:180px;
				height:180px;
				background:#4682B4;
				border-radius:2px;
				padding:10px;
			}
			#teamMemberLeave{
				background:white;
				padding:18px;
				margin:5px;
			}
			#upcomingHoliday{
				background:white;
				padding:18px;
				margin:5px;
			}
			#teamMemberBirthday{
				background:white;
				padding:18px;
				margin:5px;
			}
			.navbar-default{
				background:white;
				margin-top:-20px;
				margin-bottom:20px;
				padding-left:25px;
			}
		</style>
	</head>
	<body>
		<?php
	$name = $_SESSION['u_fullname'];
	$firstname = strtok($name, ' ');
	$lastname = strstr($name, ' ');
	?>
	
		<nav class="navbar navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<div id="img">
						<img class="img-responsive" src="img/3.jpg" style="height:50px;">
					</div>
					
				</div>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#" style="font-size:16px; color:white; padding-top:20px; padding-right:30px; font-family:cursive;"><b>  Welcome, <?php echo $firstname; ?></b></a></li>
					<li><a href="help.php" style="font-size:16px; color:white; padding-top:20px;"><i class="fa fa-question-circle" aria-hidden="true"></i><b> Need Help</b></a></li>
					<li><a href="login.php" style="font-size:16px; color:white; padding-top:20px;"><i class="fa fa-sign-out" aria-hidden="true"></i><b> Logout</b></a></li>
				</ul>
			</div>
		</nav>
		<nav class="navbar navbar-default navbar-static-top">
		<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button><!--button close-->
			<a class="navbar-brand" href="#">Leave Management System</a>
		</div><!--navbar header-->
		<div id="navbar" class="navbar-collapse collapse">
		<ul class="nav navbar-nav navbar-right" style="padding-right:80px;">
		<li id="home"><a href="Holidays.php">Holiday List</a></li>
		<li><a href="attendance.php">Attendance</a></li>
		<li><a href="trackLeaves.php">Track Leaves</a></li>
		<li><a href="leavecalender.php">Leave Calender</a></li>
		<li class="active"><a href="ApplyVOE.php">Apply VOE</a></li>
		</ul>
		
		</div>
		</div><!--container div close-->
		</nav><!--nav close-->
		
		<div class="container-fluid well" style="margin-top:-20px;">
		<!--row start-->
		<div class="row">
		<!--2 column start-->
			<div class="col-sm-2">
				<div class="rectangle">
					<a href="#"><img src="img/4.jpg" class="img-circle img-responsive" alt="" width="150px;" height="80px;"></a>
				<h6 class="text-center" style="color:white; font-size:14px; font-family:Times New Roman, Georgia, Serif;"><?php echo $_SESSION['u_fullname']?></h6>
				
					 <center><span class="text-size-small" style="color:white;">
					 <?php 
						$fullname = $_SESSION['u_fullname'];
						$location=$db->query("select location from emp where empname='".$fullname."'");
						$emprow=$db->fetchAssoc($location);
						$emplocation=$emprow['location'];
						echo $emplocation.", India"
					?>
					</span>
					</center>
		</div>
							
			
				<hr>
				<ul class="list-group">
					<li class="list-group-item active"><a href="#" style="color:white; font-size:18px;">My Account</a></li>
					<li class="list-group-item"><a href="lms.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp; Profile<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:50px;"></i></a></li>
					<li class="list-group-item"><a href="personalinfo.php"><i class="fa fa-user-secret" aria-hidden="true"></i>&nbsp;Personal Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:30px;"></i></a></li>
					<li class="list-group-item"><a href="officialinfo.php"><i class="fa fa-building" aria-hidden="true"></i>&nbsp;Official Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<li class="list-group-item"><a href="applyLeave.php"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;Apply Leave<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<?php
					$query = "select * from privileges where role='" . $_SESSION['user_desgn'] . "'";
					$result = $db -> query($query);
					$row = $db -> fetchAssoc($result);
					$keys = array_keys($row);
					if(strtoupper($_SESSION['user_dept'])=="HR") {?>
					<li class="list-group-item"><a href="hr.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;HR Section<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<?php }elseif(strtoupper($_SESSION['user_desgn'])=="MANAGER") {?>
					<li class="list-group-item"><a href="manager.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Manager Section<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:10px;"></i></a></li>
					<?php }?>
					<!--  <li class="list-group-item"><a href="leaveinfo.php"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;My Leave Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:20px;"></i></a></li>-->
				</ul>
			</div><!--2 column end-->
			<div class="col-sm-1"></div>
			<form id="AttInd" name="AttInd" method="post" action="ApplyVOE.php?insert=1">
			<div class="col-sm-8">
				<div class="panel panel-primary">
					<div class="panel-heading text-center">
						<strong style="font-size:20px;">Employee Profile(VOE)</strong>
					</div>
					
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
						echo "<form name='firstform' id='firstform' method='POST' action='ApplyVOE.php?profile=1'>";
						echo "<div class='panel-body'>";
						echo"<hr><p><strong>NOTE:</strong><i>
								Details of actual expenses incurred on running (including wear & tear) and maintenance 
								of Motor Car owned by the employee for commuting between residence to office and back in 
								excess of amount deductible in Sl No. 2(ii)/Sl No. 1( c)(i) of Rule3(2)(A)
							</i><hr>
							</p>
			 			<div class='form-group'>
							<div class='row'>
							<div class='col-sm-2'><label>Emp ID:</label></div>
							<div class='col-sm-4'><input type='text'  class='form-control input' id='employee_number' name='employee_number' value='$employee_number'/></div>
						";
						echo "
							<div class='col-sm-2'><label>Father's Name:</label></div>
						<div class='col-sm-4'><input type='text' class='form-control' id='father_name' name='father_name'/>&nbsp;<span class='errmsg' id='errmsg16'></span></div>
						</div>
						</div>";
						echo "<div class='form-group'>
							<div class='row'>
							<div class='col-sm-2'><label>Residential Address:</label></div>
						<div class='col-sm-4'><textarea id='residentialAddress' class='form-control' name='residentialAddress'/></textarea></div>
						";
						echo "
							<div class='col-sm-2'><label>Phone Number:</label></div>
						<div class='col-sm-4'><input type='text' class='form-control input' id='phoneNo' name='phoneNo'/>&nbsp;<span class='errmsg' id='errmsg10'></span></div>
						</div></div>";
						
						echo "<div class='form-group'>
							<div class='row'>
							<div class='col-sm-12'><input type='submit' class='btn btn-primary' name='submit' value='submit' /></div></div></div></div></form>";
					   
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
											$(\"#loadvoeform\").load('ApplyVOE.php');
									</script>";
								
							}
						} 
						else 
						{
							echo "<form name='formvoe' id='formvoe' method='POST' action='ApplyVOE.php?insert=1'>";
							
							echo "<div class='panel-body'>
								<hr>
								<p><strong>NOTE:</strong><i>
									Details of actual expenses incurred on running (including wear & tear) and maintenance 
									of Motor Car owned by the employee for commuting between residence to office and back in 
									excess of amount deductible in Sl No. 2(ii)/Sl No. 1( c)(i) of Rule3(2)(A)
								</i><hr>
								</p>
							
							<div class='form-group'>
							<div class='row'>
							<div class='col-sm-2'><label>Emp ID:</label></div>
							<div class='col-sm-4'><input type='text' readonly class='form-control input' id='employee_number' name='employee_number' value='$employee_number'/></div>
							";
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
									echo "
									<div class='col-sm-2'><label>Emp Name:</label></div>
									<div class='col-sm-4'><input type='text' class='form-control' readonly id='employee_name' name='employee_name' value='$employee_name'/>&nbsp;<span class='errmsg' id='errmsg6'></span></div>
									</div></div>";
									echo "<div class='form-group'>
									<div class='row'>
									<div class='col-sm-2'><label>Residential Address:</label></div>
									<div class='col-sm-4'><textarea readonly class='form-control' name='residential_address' id='residential_address'>$homeAddress</textarea></div>
									";
				
									echo "
									<div class='col-sm-2'><label>Official Address:</label></div>
									<div class='col-sm-4'><textarea class='form-control' readonly name='official_address' id='official_address'>$officialAddress</textarea></div>
									</div></div>";
									
									echo "<div class='form-group'>
									<div class='row'>
									<div class='col-sm-2'><label>Claim period:</label></div>
									<div class='col-sm-4'><input type='text' class='form-control' id='claim_period' name='claim_period' value='".date("F").",".date("Y")."' onchange='change()'/></div>
									";
									echo "
									<div class='col-sm-2'><label>Vehicle Regn No:</label></div>
									<div class='col-sm-4'><input type='text' class='form-control' name='vehicle_regno' id='vehicle_regno'/></div>
									</div></div>";
									echo "<div class='form-group'>
									<div class='row'>
									<div class='col-sm-2'><label>Vehicle make & Model:</label></div>
									<div class='col-sm-4'><input type='text' class='form-control' name='vehicle_model' id='vehicle_model'/></div>
									";
									echo "
									<div class='col-sm-2'><label>Vehicle Type:</label></div>
									<div class='col-sm-4'><select name='vehicle_type' class='form-control' id='vehicle_type'>";
			                                                foreach($vehicletypewheeler as $vehiType)
			                                                {
									    echo "<option value=$vehiType>$vehiType</option>";    
			                                                }
			                                                echo "</select></div></div></div>";
									echo "<div class='form-group'>
									<div class='row'>
									<div class='col-sm-2'><label>Fuel Nature:</label></div>
									<div class='col-sm-4'><select class='form-control' name='fuel_nature' id='fuel_nature'>";
									foreach($fuel as $col)
									{
									    echo "<option value=$col>$col</option>";    
									}
									echo"</select></div>";
									echo "
									<div class='col-sm-2'><label>Vehicle Original Cost :</label></div>
									<div class='col-sm-4'><input type='text' class='form-control input' id='vehiclecost' name='original_cost' onchange='weartearCost();'/>&nbsp;<span class='errmsg' id='errmsg5'></span></div>
									</div></div>";
									
									echo "<div class='form-group'>
									<div class='row'>
									<div class='col-sm-2'><label>Fuel cost per litre in INR:</label></div>
									<div class='col-sm-4'><input type='text' class='form-control input' id='fuelcost_perlitre' name='fuelcost_perlitre' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg2'></span> </div>
									";
									echo "
									<div class='col-sm-2'><label>Driver's salary in INR:</label></div>
				                    <div class='col-sm-4'><input type='text' class='form-control input' id='drivers_salary' name='driver_salary' onchange='totalVoe();' value='0'/> &nbsp;<span class='errmsg' id='errmsg4' ></span></div>
				                                        </div></div>";
									
									
									echo "<div class='form-group'>
									<div class='row'>
									<div class='col-sm-2'><label>Repairs & Maintenance expenses incurred:</label></div>
				                    <div class='col-sm-4'><input type='text' class='form-control input' type='text' id='expenses' name='repairs_maintence_expenses' onchange='totalVoe();' value='0'/> &nbsp;<span class='errmsg' id='errmsg3'></span></div>
				                                        ";
									echo "
									<div class='col-sm-2'><label>Wear & tear cost of the vehicle:</label></div>
									<div class='col-sm-4'><input type='text' class='form-control input' id='wear_tear_cost' name='wear_tear_cost'/> </div>
									</div></div>";
									echo "<div class='form-group'>
									<div class='row'>
									<div class='col-sm-4'><label> Distance between residence and office of employee:</label></div>
									<div class='col-sm-8'><input type='text' class='form-control input' id='distance_from_residenceto_office' name='distance_from_residenceto_office' onchange='distance()'/>&nbsp;<span class='errmsg' id='e4rrmsg'></span></div>
									</div></div>";
									echo "<div class='form-group'>
									<div class='row'>
									<div class='col-sm-4'><label>Milage of the vehicle-km/litre:</label></div>
									<div class='col-sm-8'><input type='text' class='form-control input' id='vehicle_milage' name='vehicle_milage' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg1'></span></div>
									</div></div>";
									echo "<div class='form-group'>
									<div class='row'>
									<div class='col-sm-4'><label>Total VOE eligibility(14+15+16+17):</label></div>
									<div class='col-sm-8'><input type='text' readonly class='form-control input' id='total_voe' name='total_voe' onclick='totalVoe();' /> </div>
									</div></div>";
									echo "<div class='form-group' style='display:none' id='formOf'><div class='row'><div class='><p>I, <b>$employee_name</b> the daughter/son of Mr. <b>$father_name</b> do hereby declare that all the details furnished in this document is true to the best of my information.</p></div></div></div>";
									echo "<div class='form-group'><div class='row'><div class='col-sm-6' style='display:none'id='verification'>Date:</div><div class='col-sm-6' style='display:none text-align:right;'id='sig'><center>Signature of Employee</center></div></div></div>";
									echo "<div class='form-group'><div class='row'><div class='col-sm-4' style='display:none'id='delete'><center><input name='b_print' type='button' class='btn btn-primary ipt' onClick='CallPrint();' value='Print'/></center></div>";
									echo "<div class='col-sm-4 submit' colsapn='2'><center><input type='submit' class='form-control' name='voesubmit' id='voeSubmit' value='submit' onclick='totalVoe();'/></center></div>";
									echo"<div class='col-sm-4 style='display:none' id='delete'><center><input type='button' class='form-control' name='delete' value='Delete' onclick='deletion()' /></center></div></div></div>";
									
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
								    		echo "
								    		<div class='col-sm-2'><label>Emp name:</label></div>
								    		<div class='col-sm-4'><input type='text' class=fomr-control' readonly value='$employee_name' id='employee_name' name='employee_name'/>&nbsp;<span class='errmsg' id='errmsg6'></span></div>
								    		</div></div>";
								    		echo "<div class='form-group'>
								    		<div class='row'>
								    		<div class='col-sm-2'><label>Residential address:</label></div>
								    		<div class='col-sm-4'><textarea class='form-control'readonly id='residential_address' name='residential_address'>$homeAddress </textarea></div>
								    		";
								    		echo "
								    		<div class='col-sm-2'><label>Officiai address:</label></div>
								    		<div class='col-sm-4'><textarea readonly class='form-control' id='official_address' name='official_address'>$officialAddress</textarea></div>
								    		</div></div>";
								    		echo "<div class='form-group'>
											<div class='row'>
								    		<div class='col-sm-2'><label>Claim period:</label></div>
								    		<div class='col-sm-4'><input type='text' class='form-control' id='claim_period' name='claim_period' value='".date("F").",".date("Y")."' onchange='change()'/></div>
								    		";
								    		echo "
								    		<div class='col-sm-2'><label>Vehicle Regn No:</label></div>
								    		<div class='col-sm-4'><input type='text' class='form-control' value='$vehicle_regno' id='vehicle_regno' name='vehicle_regno'/></div>
								    		</div></div>";
								    		echo "<div class='form-group'>
								    		<div class='row'>
								    		<div class='col-sm-2'><label>Vehicle make & Model:</label></div>
								    		<div class='col-sm-4'><input type='text' class='form-control' value='$vehicle_model' id='vehicle_model' name='vehicle_model'/></div>
								    		";
										echo "
								    		<div class='col-sm-2'><label>Vehicle Type:</label></div>
										<div class='col-sm-4'><select class='form-control' name='vehicle_type' id='vehicle_type'>";
			                                                	foreach($vehicletypewheeler as $vehiType)
			                                                	{
										    if($vehiType==$vehicle_type) {
				                                        	            echo "<option value=$vehiType selected>$vehiType</option>";
										     } else {
											    echo "<option value=$vehiType>$vehiType</option>";
										     }
			                                                	}
			                                                	echo "</select></div></div></div>";
								        	echo "<div class='form-group'><div class='row'>
								    		<div class='col-sm-2'><label>Fuel Nature:</label></div>
											<div class='col-sm-4'><select class='form-control' name='fuel_nature' id='fuel_nature'>";
											      echo "<option value=$fuel_nature>$fuel_nature</option>";
													for($i=0;$i<sizeof($fuel);$i++)
													{ 
													    if($fuel[$i]!=$fuel_nature){
														    echo "<option value=$fuel[$i]>$fuel[$i]</option>";
													    }   
												 	}
											echo"</select></div>";
								    		echo "
								    		<div class='col-sm-2'><label>Vehicle original cost:</label></div>
								    		<div class='col-sm-4'><input type='text' class='form-control input' id='vehiclecost' value='$original_cost' name='original_cost' onchange='weartearCost()'/><span class='errmsg' id='errmsg5'></span></div>
								    		</div></div>";
								    		echo "<div class='form-group'><div class='row'>
								    		<div class='col-sm-2' class='dynamic'><label>Distance between residence and office of employee:</label></div>
								    		<div class='col-sm-4'><input type='text' class='form-control input' id='distance_from_residenceto_office' name='distance_from_residenceto_office' value='$distance' onchange='distance()'/>&nbsp;<span class='errmsg' id='errmsg'></span></div>
								    		";
								    		echo "
								    		<div class='col-sm-2'><label>Milage of the vehicle-km/litre:</label></div>
								    		<div class='col-sm-4'><input type='text' class='form-control input' id='vehicle_milage' name='vehicle_milage' value='$milage' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg1'></span></div>
								    		</div></div>";
								    		echo "<div class='form-group'>
								    		<div class='row'>
								    		<div class='col-sm-2'><label>Fuel cost per litre in INR:</label></div>
								    		<div class='col-sm-4'><input type='text' class='form-control input' id='fuelcost_perlitre' name='fuelcost_perlitre' value='$fuelcost' onchange='fuelCost();'/>&nbsp;<span class='errmsg' id='errmsg2'></span></div>
								    		";
								    		echo "
								    		<div class='col-sm-2'><label>Repairs & Maintenance expenses incurred:</label></div>
								    		<div class='col-sm-4'><input type='text' class='input' type='text' id='expenses' name='repairs_maintence_expenses' onchange='totalVoe();' value='$repairs'/> &nbsp;<span class='errmsg' id='errmsg3'></span></td>
								    		</div></div>";
								    		echo "<div class='form-group'><div class='row'>
								    		<div class='col-sm-2'><label>Wear & tear cost of the vehicle:</label></div>
								    		<div class='col-sm-4'><input type='text' class='form-control input' id='wear_tear_cost' name='wear_tear_cost' value='$wear_tearcost'/></div>
								    		";
								    		echo "
								    		<div class='col-sm-2'><label>Driver's salary in INR:</label></div>
								    		<div class='col-sm-4'><input type='text' class='form-control input' id='drivers_salary' name='driver_salary' onchange='totalVoe();' value='$driversal'/> &nbsp;<span class='errmsg' id='errmsg4'></span></div>
								    		</div></div>";
								    		echo "<div class='form-group'><div class='row'>
								    		<div class='col-sm-4'><label>Total VOE eligibility(14+15+16+17):</label></div>
								    		<div class='col-sm-8'><input type='text' readonly class='form-control input' id='total_voe' name='total_voe' onclick='totalVoe();'/></div>
								    		</div></div>";
							
								    }
								    echo "<div class='form-group' style='display:none' id='formOf'><div class='row'><div class='col-sm-12'><p>I, <b>$employee_name</b> the daughter/son of Mr. <b>$father_name</b> do hereby declare that all the details furnished in this document is true to the best of my information.</p></div></div></div>";
								    echo "<div class='form-group'><div class='row'><div class='col-sm-6' style='display:none'id='verification'>Date:</div><div class='col-sm-6' style='display:none'id='sig'><center>Signature of Employee</center></div></div></div>";
									echo "<div class='form-group'><div class='row'><div class='col-sm-4 submit'><center><input type='submit' class='btn btn-primary' id='voeSubmit' name='voesubmit' value='submit' onclick='totalVoe();' /></center></div>";
					    			echo "<div class='col-sm-4' style='display:none' id='delete'><center><input type='button'name='delete' value='Delete' onclick='deletion()' /></center></div>";
					    			echo "<div class='col-sm-4' style='display:none' id='print'><center><input name='b_print' type='button' class='btn btn-primary ipt' onClick='CallPrint();' value='Print' /></center></div></div></div>";
					    			
								}
								echo "</div></form>";
						}
					}
			        elseif ($db->countRows($resultset1)!=0  && ($row['address']!="" || $row['phonenumber']!="" || $row['fathername']!="")) 
			        {
						if(isset($row['address'])) { $empaddress=$row['address']; } else { $empaddress=""; } 
						if(isset($row['phonenumber'])) { $empphonenumber=$row['phonenumber']; } else { $empphonenumber=""; }
						if(isset($row['fathername'])) { $empfathername=$row['fathername']; } else { $empfathername=""; }
					   	echo "<form name='firstform' id='firstform' method='POST' action='ApplyVOEvoe.php?update=1'>";
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
					   	<div class='col-sm-4'><label>Employee ID:</label></div>
					   	<div class='col-sm-8'><input type='text' readonly class='form-control input' id='employee_number' name='employee_number' value='$employee_number'/></div>
					   	</div></div>";
					   	echo "<div class='form-group'>
						<div class='row'>
					   	<div class='col-sm-4'><label>Father's Name:</label></div>
					   	div class='col-sm-8'<input type='text' id='father_name' name='father_name' value='".$empfathername."'/>&nbsp;<span class='errmsg' id='errmsg17'></span></div>
					   	</div></div>";
					   	echo "<div class='form-group'>
						<div class='row'>
					   	<div class='col-sm-4'><label>Residential Address:</label></div>
					   	<div class='col-sm-8'><textarea id='residentialAddress' class='form-control' name='residentialAddress'>$empaddress</textarea>&nbsp;<span class='errmsg' id='errmsg16'></span></div>
					   	</div></div>";
					   	echo "<div class='form-group'>
						<div class='row'>
					   	<div class='col-sm-4'><label>Phone Number:</label></div>
					   	<div class='col-sm-8'><input type='text' class='form-control input' id='phoneNo' name='phoneNo' value='".$empphonenumber."'/>&nbsp;<span class='errmsg' id='errmsg10'></span></div>
					   	</div></div>";
					   	echo "<div class='form-group'>
						<div class='row'>
					   	<div class='col-sm-12' class='submit'><input type='submit' class='btn btn-primary' name='submit' value='submit' /></div></div></div>
						</div></form>";
					 }
					$db->closeConnection();
				?>
				</div>
				</form>
			</div>
		</div>
		   
		   </div>
		</div>
		</div>
		<footer class="footer1">
		<div class="container">
			<div class="row"><!-- row -->
				<div class="col-lg-4 col-md-4"><!-- widgets column left -->
					<ul class="list-unstyled clear-margins"><!-- widgets -->
						<li class="widget-container widget_nav_menu"><!-- widgets list -->
							<h1 class="title-widget">Email Us</h1>
							<p><b>Anil Kumar Thatavarthi:</b> <a href="mailto:#"></a></p>
							<p><b>Naidile Basvagde :</b> <a href="mailto:#"></a></p>
							<p><b>Sneha Kumari:</b> <a href="mailto:#"></a></p>
						</li>
					</ul>
				</div><!-- widgets column left end -->
				
				<div class="col-lg-4 col-md-4"><!-- widgets column left -->
					<ul class="list-unstyled clear-margins"><!-- widgets -->
						<li class="widget-container widget_nav_menu"><!-- widgets list -->
							<h1 class="title-widget">Contact Us</h1>
							<p><b>Helpline Numbers </b> 
								<b style="color:#ffc106;">(8AM to 10PM): </b></p>
							<p>  +91-9740464882, +91-9945732712  </p>
							<p><b>Phone Numbers : </b>7042827160, </p>
							<p> 011-2734562, 9745049768</p>
						</li>
					</ul>
				</div><!-- widgets column left end -->
						
				<div class="col-lg-4 col-md-4"><!-- widgets column left -->
					<ul class="list-unstyled clear-margins"><!-- widgets -->
						<li class="widget-container widget_nav_menu"><!-- widgets list -->
							<h1 class="title-widget">Office Address</h1>
							<p><b>Corp Office / Postal Address</b></p>
							<p>5th Floor ,Innovator Building, International Tech Park, Pattandur Agrahara Road, Whitefield, Bengaluru, Karnataka 560066</p>
						</li>
					</ul>
				</div><!-- widgets column left end -->
			</div>
		</div>
		</footer>
		<!--header-->

		<div class="footer-bottom">

			<div class="container">

				<div class="row">

					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

						<div class="copyright">

							© 2017, All rights reserved

						</div>

					</div>

					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

						<div class="design">

							 <a href="#"><b>ECI TELECOM</b> </a> |  <a href="#">LMS by ECI</a>

						</div>

					</div>

				</div>

			</div>

		</div>
			
			
			<!--footer end-->
	</body>
</html>
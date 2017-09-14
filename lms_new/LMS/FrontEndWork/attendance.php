<?php
	session_start();
	require_once 'Library.php';
	error_reporting("E_ALL");
	ini_set('max_execution_time', 600);
	$db=connectToDB();
?>
<html>
	<head>
		<style>
			label{
				font-size:16px;
			}
		</style>
		<script>
			function showDialog(id, empid, first, month) {
				$("#dialog-modal" + id).dialog({
					width : 1250,
					height : 500,
					open : function(event, ui) {
						var url = 'graph.php?empid=' + empid + '&first=' + first + '&month=' + month;
						$(this).addClass("dialogOpen");
						$(this).load(url);
					}
				});
			}
			function toggle(thisname) {
				tr = document.getElementsByTagName('tr')
				for ( i = 0; i < tr.length; i++) {
					if (tr[i].getAttribute(thisname)) {
						if (tr[i].style.display == 'none') {
							tr[i].style.display = '';
						} else {
							tr[i].style.display = 'none';
						}
					}
				}
			}
			$('#AttInd').submit(function() {
				$('#accessData').html(" ");
				if ( $("#hideDept").val()=="none" ) {
					alert("Please selct the Department");
					return false;
				}
				if ( $("#hideDept").val()=="ALL" && $("#getEmpName").val()=="ALL") {
					alert("Please wait for few minutes to get the results for all ECI Employeees.It will take more than a minute.");
				}
				$('#loadingmessage').show();
				$.ajax({
					data : $(this).serialize(),
					type : $(this).attr('method'),
					url : $(this).attr('action'),
					success : function(response) {
						$('#loadattendance').html(response);
						if($("#balanceDialog")) {
                           $("#balanceDialog").hide();
                        }
					}
				});
				return false;
			});

			$( "#accordion" ).accordion({
				heightStyle: "content",
				collapsible: true
			});

			$.each( $( "#accordion h3"), function( i, val ) {
				var first=$($(val)).next().find(".teamMorning").text().match((/(\((.*)(%)\))/i))[2];
				var second=$($(val)).next().find(".teamEvening").text().match((/(\((.*)(%)\))/i))[2];
				var third=$($(val)).next().find(".teamDaily").text().match((/(\((.*)(%)\))/i))[2];
				var fourth=$($(val)).next().find(".teamWeek").text().match((/(\((.*)(%)\))/i))[2];
				var fifth=$($(val)).next().find(".teamExtraWFH").text().match((/(\((.*)(%)\))/i))[2];
				var sixth=$($(val)).next().find(".teamtotalhour").text().match((/(\((.*)(%)\))/i))[2];
				if ( first > 40 ) { 
					var firstString = "<font color=red>"+$(val).next().find(".teamMorning").text();
				}  else { 
					var firstString =$(val).next().find(".teamMorning").text();
				}
				if ( second > 40 ) { 
					var secondString = "<font color=red>"+$(val).next().find(".teamEvening").text();
				}  else { 
					var secondString =$(val).next().find(".teamEvening").text();
				}
				if ( third > 40 ) { 
					var thirdString = "<font color=red>"+$(val).next().find(".teamDaily").text();
				}  else { 
					var thirdString =$(val).next().find(".teamDaily").text();
				}
				if ( fourth > 40 ) { 
					var fourthString = "<font color=red>"+$(val).next().find(".teamWeek").text();
				}  else { 
					var fourthString =$(val).next().find(".teamWeek").text();
				}
				if ( fifth > 40 ) { 
					var fifthString = "<font color=red>"+$(val).next().find(".teamExtraWFH").text();
				}  else { 
					var fifthString =$(val).next().find(".teamExtraWFH").text();
				}
				if ( sixth > 40 ) { 
					var sixthString = "<font color=red>"+$(val).next().find(".teamtotalhour").text();
				}  else { 
					var sixthString =$(val).next().find(".teamtotalhour").text();
				}
				$(val).html("<table class='table'><tr><td><b>"+$(val).text()+"</b></td><td><table class='table'><tr><td>"+
				firstString+"</td><td>"+
				secondString+"</td><td>"+
				thirdString+'</td><td>'+
				fourthString+"</td><td>"+
				fifthString+"</td><td>"+
				sixthString+"</td></tr></table></td></tr></table>");
			});
		</SCRIPT>
		<?php
			echo '
			<script>
			$("#hideDept").change(function() {
				var dept=$("#hideDept").val();
				var empid="' . $_SESSION['u_empid'] . '";
				if(empid==dept || dept=="none") {
					$("#hideName").hide();
				} else {
					$("#hideName").show();
					$.post("getSplLeaveOptions.php?dept="+escape(dept),function(data) {
						$("#getEmpName").empty();
						$("#getEmpName").append(data);
					});
				}
			});
			</script>';
		?>
		<title>Attendence analyze</title>
	</head>
	<body>
		<!--container fluid start-->
		<div class="container-fluid">
			<!--row div start-->
			<div class="row">
				<?php
					$sumOfInDays=0;
					$sumOfTotalInDays=0;
					$sumOfOutDays=0;
					$sumOfTotalOutDays=0;
					$sumOfDailyhrDays=0;
					$sumOfTotalDailyhrDays=0;
					$sumOfWeeklyhrDays=0;
					$sumOfTotalWeeklyhrDays=0;
					$dayhours = 8.5;
					$db = connectToDB();
					$count = 0;
					$deps = " <option selected value=\"ALL\">ALL</option>";
					if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
						$query = "SELECT * FROM `emp` where state='Active' ORDER BY empname ASC";
						$querydept = "SELECT distinct(dept) FROM `emp` ORDER BY dept ASC";
						$resultdept = $db -> query($querydept);
					} else if (strtoupper($_SESSION['user_desgn']) == 'MANAGER') {
						$deps = "";
						if ($_SESSION['u_managerlevel'] != 'level1') {
							$query = "SELECT distinct(dept) FROM `emp` WHERE managerid='" . $_SESSION['u_empid'] . "' ORDER BY empname ASC";
							$result = $db -> query($query);
							$deps = " <option selected value=\"none\">NONE</option>";
							$deps = $deps." <option value=\"ALL\">ALL</option>";
						} else {
							$query = "SELECT * FROM `emp` WHERE state='Active' and managerid='" . $_SESSION['u_empid'] . "' ORDER BY empname ASC";
							$result = $db -> query($query);
							$deps = " <option selected value=\"ALL\">ALL</option>";
						}
						//Including self name also in the list
						$deps = $deps . '<option value="' . $_SESSION["u_empid"] . '">';
						$deps = $deps . $_SESSION['u_fullname'];
						$deps = $deps . '</option>';
						if ($_SESSION['u_managerlevel'] != 'level1') {
							while ($row = mysql_fetch_assoc($result)) {
								$deps = $deps . '<option value="' . $row["dept"] . '">';
								$deps = $deps . $row["dept"];
								$deps = $deps . '</option>';
							}
						} else {
							while ($row = mysql_fetch_assoc($result)) {
								$deps = $deps . '<option value="' . $row["empid"] . '">';
								$deps = $deps . $row["empname"];
								$deps = $deps . '</option>';
							}
						}
					} else {
						$query = "SELECT * FROM `emp` WHERE state='Active' and empusername='" . $_SESSION['user_name'] . "'";
						$result = $db -> query($query);
						$deps = "";
						$deps = $deps . '<option value="' . $_SESSION["u_empid"] . '">';
						$deps = $deps . $_SESSION['u_fullname'];
						$deps = $deps . '</option>';
					}

					$typeofday = "";
					//Department name
					
					$department = '<option value="none">';
					$department = $department . "None";
					$department = $department . '</option>';
					if($resultdept) {
						while ($row = mysql_fetch_assoc($resultdept)) {
							$department = $department . '<option value="' . $row["dept"] . '">';
							$department = $department . $row["dept"];
							$department = $department . '</option>';
						}
						$department = $department . '<option value="ALL">';
						$department = $department . "ALL";
						$department = $department . '</option>';
					}
					
					?>
			
				<form id="AttInd" name="AttInd" method="post" action="attendance.php?AttInd=1">
				<div class="col-sm-12">
					<div class="panel panel-primary">
						<div class="panel-heading text-center">
							<strong style="font-size:20px;">Attendance History</strong>
						</div>
						<div class="panel-body">
							<?php 
								if($_SESSION['u_emplocation'] != 'MUM')
								{
								 echo '<div class="form-group">
										<div class="row">
											<div class="col-sm-2"></div>
											<div class="col-sm-3">
												<label>Before 10 and After 4:</label>
											</div>
											<div class="col-sm-5">
												<select class="form-control" size="1" name="AdvCond1">
													<option value="NOTMEET" selected>Not Meet</option>
													<option value="MEET">Meet</option>
												</select>
											</div>
										</div>
									</div>';	
								} else {
									echo '<div class="form-group" style="display:none">
										<div class="row">
											<div class="col-sm-2"></div>
										<div class="col-sm-3">
											<label>Before 10 and After 4:</label>
										</div>
										<div class="col-sm-5">
											<select class="form-control" size="1" name="AdvCond1">
												<option value="NOTMEET" selected>Not Meet</option>
												<option value="MEET">Meet</option>
											</select>
										</div>
										<div class="col-sm-2"></div>
									</div>
								</div>';
								}
								?>
								
								<?php
								if (($_SESSION['u_managerlevel'] != 'level1') || ($_SESSION['user_dept'] == 'HR')) {
									if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
										echo '<div class="form-group">
												<div class="row">
													<div class="col-sm-2"></div>
													<div class="col-sm-3">
														<label>Department:</label>
													</div>
													<div class="col-sm-5">
														<select class="form-control" id="hideDept" size="0" name="UDept">' . $department . '</select>
													</div>
													<div class="col-sm-2"></div>
													</div>
												</div>';
										echo '<div class="form-group" id="hideName" style="display:none">
												<div class="row">
													<div class="col-sm-2"></div>
													<div class="col-sm-3">
														<label>Emp Name:</label>
													</div>
													<div class="col-sm-5">
														<select class="form-control" id="getEmpName" size="0" name="getDeptemp"></select>
													</div>
													<div class="col-sm-2"></div>
												</div>
											</div>';
									} else {
										echo '<div class="form-group">
												<div class="row">
													<div class="col-sm-2"></div>
													<div class="col-sm-3">
														<label>Department:</label>
													</div>
													<div class="col-sm-5">
														<select class="form-control" id="hideDept" size="0" name="UDept">' . $deps . '</select>
													</div>
													<div class="col-sm-2"></div>
												</div>
											</div>';
										echo '<div class="form-group" id="hideName" style="display:none">
												<div class="row">
													<div class="col-sm-2"></div>
													<div class="col-sm-3"><label>Emp Name:</label></div>
													<div class="col-sm-5">
														<select class="form-control" id="getEmpName" size="0" name="getDeptemp"></select>
													</div>
													<div class="col-sm-2"></div>
												</div>
											</div>';
									}
								} else {
									echo '<div class="form-group" id="hideEmpName">
											<div class="row">
												<div class="col-sm-2"></div>
												<div class="col-sm-3">
													<label>Emp Name:</label>
												</div>
												<div class="col-sm-5">
													<select class="form-control" size="0" name="UGroup">'.$deps.'</select>
												</div>
												<div class="col-sm-2"></div>
											</div>
										</div>';
								}
								?>
								
								<div class="form-group">
									<div class="row">
										<div class="col-sm-2"></div>
										<div class="col-sm-3">
											<label><?php echo $dayhours; ?>Hr/Day:</label>
										</div>
										<div class="col-sm-5">
											<select class="form-control" size="1" name="AdvCond2">
												<option value="NOTMEET" selected>Not Meet</option>
												<option value="MEET">Meet</option>
											</select>
										</div>
										<div class="col-sm-2"></div>
									</div>
								</div>
								
								<div class="form-group">
									<div class="row">
										<div class="col-sm-2"></div>
										<div class="col-sm-3">
											<label><?php echo $dayhours * 5; ?>Hr/Week:</label>
										</div>
										<div class="col-sm-5">
											<select class="form-control" size="1" name="AdvCond3">
												<option value="NOTMEET" selected>Not Meet</option>
												<option value="MEET">Meet</option>
											</select>
										</div>
										<div class="col-sm-2"></div>
									</div>
								</div>
								
								<div class="form-group">
									<div class="row">
										<div class="col-sm-2"></div>
										<div class="col-sm-3">
											<label>From Date:</label>
										</div>
										<div class="col-sm-5">
										<!--  <input class="form-control" type="text" name="fromdate" value='<?php echo add_day(-30, 'Y-m-d'); ?>' id="fromdate" size="8" />-->
											<div class="input-group">
												<input type="text" id="datetimepicker" class="form-control open-datetimepicker" name="fromdate" value='<?php echo add_day(-30, 'Y-m-d') ?>'>
												<label class="input-group-addon btn" for="date">
												   <span class="fa fa-calendar open-datetimepicker"></span>
												</label>
											</div>
										</div>
										<div class="col-sm-2"></div>
									</div>
								</div>
								
								<div class="form-group">
									<div class="row">
										<div class="col-sm-2"></div>
										<div class="col-sm-3"><label>To Date:</label></div>
										<div class="col-sm-5">
										<!--  <input type="text" class="form-control" size="8" name="todate" id="todate" value = '<?php echo date('Y-m-d') ?>'  />-->
											<div class="input-group">
												<input type="text" id="datetimepicker1" class="form-control open-datetimepicker1" name="todate" value='<?php echo date('Y-m-d')  ?>'>
												<label class="input-group-addon btn" for="date">
												   <span class="fa fa-calendar open-datetimepicker1"></span>
												</label>
											</div>
										</div>
										<div class="col-sm-2"></div>
									</div>
								</div>
								
								<div class="form-group">
								<div class="row">
								<div class="col-sm-12 text-center">
								<input type="submit" class="btn btn-primary submitBtn" value="Submit" name="AttInd">
								</div>
								</div>
								</div>
							</div>
						</div>
						
			   </div>
			   </form>
			   <script>
				   $(document).ready(function(){
					$(".open-datetimepicker").datetimepicker({
						format: "YY-MM-DD"
					});
					$(".open-datetimepicker1").datetimepicker({
						format: "YY-MM-DD"
					});
				   });
			   </script>
				<?php
					$defaultIn='10:00:00';
					$defaultOut='16:00:00';
					$halfDayDefault='13:00:00';
					function timediffinHR ($first, $last) {
						if ($last == '00:00:00') {return 0;}
						$seconds= strtotime($last)-strtotime($first);
						$days    = floor($seconds / 86400);
						$hours   = floor(($seconds - ($days * 86400)) / 3600);
						$minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
						$seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));
						return "$hours:$minutes:$seconds";
					}
					function timeAdd ($first, $last) {
						$hour=0;$min=0;$sec=0;
						list($hour1,$m1,$s1)=explode(":",$first);
						list($hour2,$m2,$s2)=explode(":",$last);
						$sec=$s1+$s2;
						if($sec>=60) {
							if($sec==60) {
								$sec=00;
							}
							if($sec>60) {
							   $sec=$sec-60;
							}
							$min=1;
						}
						$min=$min+$m1+$m2;
						if ($min >= 60) {
							if($min==60) {
								$min=00;
							}
							if($min>60) {
								$min=$min-60;
							}
							$hour=1;;
						}
						$hour=$hour+$hour1+$hour2;
						return "$hour:$min:$sec";
					}
				
					function getDay($empid,$date) {
						global $db;
						$query="select a.transactionid,a.leavetype,a.shift,a.compoffreason,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empid."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
						$result=$db->query($query);
						$row = $db->fetchArray($result);
						$statusQuery="select * from `empleavetransactions` WHERE `transactionid` ='".$row['transactionid']."'";
						$statusresult=$db->query($statusQuery);
						$statusRow = $db->fetchArray($statusresult);
						if($statusRow['approvalstatus']=="Approved") {
							if ($row['leavetype']=='On Site') {
								return "On Site";
							}
							if(isset($row['compoffreason']) && $row['compoffreason']!="") {
								return $row['compoffreason'];
							} else {
								if ($row['leavetype']=='WFH') {
									if(empty($row['shift'])) {
										return $row['leavetype'];
									} else {
										if($row['shift']=="fullDay") {
											return $row['leavetype'];
										} else {
											return $row['leavetype']." (".$row['shift'].")";
										}
									}
								} 
								if ($row['leavetype']) {
									if ($row['leavetype']=="First Half-WFH & Second Half-HalfDay" || $row['leavetype']=="First Half-HalfDay & second Half-WFH")
									{
										$retunVal=$row['leavetype'];	
									} else {
										$retunVal=$row['leavetype']." PTO";
									} 
									if ($row['leavetype']=="Other Leave Type") {
										return $statusRow['reason'];
									}
									return $retunVal;
								}
							}
						}
						#Check whether holiday is present on that day
						$getHoliday="select holidayname from holidaylist where date='".$date."'";
							
						#Check whether employee birthday is present on that day
						$getBirthday="SELECT birthdaydate FROM emp WHERE empid='".$empid."' and state='Active'";
							
						$holidayResult=$db->query($getHoliday);
						$birthdayResult=$db->query($getBirthday);
									
						if($holidayResult && $birthdayResult) {
							$holidayRow = $db->fetchArray($holidayResult);
							$birthdayRow = $db->fetchArray($birthdayResult);
							if($db->countRows($holidayResult) > 0 && isBirthday($empid,$date)) {
								return "Birthday and ".$holidayRow['holidayname']; 
							}
							elseif ($db->countRows($holidayResult) > 0) {
								if ($holidayRow['holidayname']) {
									return $holidayRow['holidayname'];
								}  
							}
							elseif (isBirthday($empid,$date)) {
								return "Birthday";
							}
							else {
								return "No Data";
			#			# Check whether employee took compoff on that day   
			#	                $checkCompoff="select * from `inout` where compofftakenday='".$date."'";
			#                       $compOffRes=$db->query($checkCompoff);
			#                      if($db->countRows($compOffRes)!=0) {
			#                                $row=$db->fetchAssoc($compOffRes);
			#                                $workedHolidayDate=$row['Date'];
			#                                return "Compoff taken on behalf of $workedHolidayDate";
			#                        } else {
			#                                return "No Data";
			#                        }    
							}
						}
					}
			
					function isBirthday($empid,$date) {
						global $db;
						#Check whether employee birthday is present on that day
						$getBirthday="SELECT birthdaydate FROM emp WHERE empid='".$empid."' and state='Active'";
						$birthdayResult=$db->query($getBirthday);
						if($birthdayResult) {
							$birthdayRow = $db->fetchArray($birthdayResult);
							if($db->countRows($birthdayResult)>0) {
								list($by,$bm,$bd) = explode('-', $birthdayRow['birthdaydate']);
								list($y,$m,$d) = explode('-', $date);
								if($bm==$m && $bd==$d) {
									return 1;
								} else {
									return 0;
								}
							}
						}
					}
			
					function isHoliday($date) {
						global $db;
						#Check whether holiday is present on that day 
						$getHoliday="select holidayname from holidaylist where date='".$date."' and leavetype='Fixed'";
						$holidayResult=$db->query($getHoliday);
						if($holidayResult && $db->hasRows($holidayResult)) {
							return 1;
						} else {
							return 0;
						}
					}
			
					function isOptionalHolidayApplied($date,$empid) {
						global $db;
						#Check whether holiday is present on that day and is optional
						$getHoliday="select holidayname from holidaylist where date='".$date."' and leavetype='Optional'";
						$holidayResult=$db->query($getHoliday);
						# check whether employee applied optional holiday
						$getOptionalHolidayResult=$db->query("select * from empoptionalleavetaken where empid='".$empid."' and date='".$date."'");
						if($holidayResult && $db->hasRows($holidayResult) && $db->hasRows($getOptionalHolidayResult)) {
							return 1;
						} else {
							return 0;
						}
					}
			
					function isOptionalHoliday($date) {
						global $db;
						#Check whether holiday is present on that day and it is optional
						$getHoliday="select holidayname from holidaylist where date='".$date."' and leavetype='Optional'";
						$holidayResult=$db->query($getHoliday);
						if($holidayResult && $db->hasRows($holidayResult)) {
							return 1;
						} else {
							return 0;
						}
					}
			
					function isFullDayPTO($date,$empId) {
						global $db;
						$getHoliday="select a.transactionid,a.leavetype,a.shift,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empId."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
						#Check whether employee is on Full Day PTO on the given day 
						//$getHoliday="select leavetype from perdaytransactions where date='".$date."' and empid='".$empId."'";
						$holidayResult=$db->query($getHoliday);
						if($holidayResult && $db->hasRows($holidayResult)) {
							$PTORow = $db->fetchArray($holidayResult);
						if ( $PTORow['leavetype']=="FullDay") {
							return 1;
						} else {
							return 0;
						}
						} else {
							return 0;
						}
					}
			
					function isHalfDayPTO($date,$empId) {
						global $db;
					
						#Check whether employee is on Half Day PTO on the given day 
						//$getHalfDayPTO="select leavetype from perdaytransactions where date='".$date."' and empid='".$empId."'";
						$getHalfDayPTO="select a.transactionid,a.leavetype,a.shift,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empId."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
						$HalfDayPTOResult=$db->query($getHalfDayPTO);
						if($HalfDayPTOResult && $db->hasRows($HalfDayPTOResult)) {
							$PTORow = $db->fetchArray($HalfDayPTOResult);
							if ( $PTORow['leavetype']=="HalfDay") {
								return 1;
							} else {
								return 0;
							}
						} else {
							return 0;
						}
					}	
			
					//to get total hour for each employee
			
					function isWFH($date,$empId) {
						global $db;
						#Check whether employee is on WFH on the given day 
						//$getWFH="select leavetype from perdaytransactions where date='".$date."' and empid='".$empId."'";
						$getWFH="select a.transactionid,a.leavetype,a.shift,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empId."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
						$WFHResult=$db->query($getWFH);
						if($WFHResult && $db->hasRows($WFHResult)) {
							$WFHRow = $db->fetchArray($WFHResult);
							if ( $WFHRow['leavetype']=="WFH") {
							   return 1;
							} else {
								return 0;
							}
						} else {
							return 0;
						}
					}
			
					function isFulldayWFH($date,$empId) {
						global $db;
						#Check whether employee is on WFH on the given day
						$getWFH="select a.transactionid,a.leavetype,a.shift,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empId."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
						$WFHResult=$db->query($getWFH);
						if($WFHResult && $db->hasRows($WFHResult)) {
							$WFHRow = $db->fetchArray($WFHResult);
							if ( $WFHRow['leavetype']=="WFH" && strtoupper($WFHRow['shift'])=="FULLDAY") {
								return 1;
							} else {
								return 0;
							}
						} else {
							return 0;
						}
				
					}
			
					function isHalfdayWFH($date,$empId) {
						global $db;
						#Check whether employee is on WFH on the given day
						$getWFH="select a.transactionid,a.leavetype,a.shift,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empId."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
						$WFHResult=$db->query($getWFH);
						if($WFHResult && $db->hasRows($WFHResult)) {
							$WFHRow = $db->fetchArray($WFHResult);
							if ( $WFHRow['leavetype']=="WFH" && (strtoupper($WFHRow['shift'])=="FIRSTHALF" || strtoupper($WFHRow['shift'])=="SECONDHALF")) {
								return 1;
							} else {
								return 0;
							}
						} else {
							return 0;
						}
					}
			
					function getShiftforDay($leavetype,$empId,$date) {
						global $db,$defaultIn, $defaultOut,$halfDayDefault;
						#Check whether employee is on WFH on the given day 
						$getShift="select shift from perdaytransactions where date='".$date."' and empid='".$empId."' and leavetype='".$leavetype."'";
						$shiftResult=$db->query($getShift);
						if($shiftResult && $db->hasRows($shiftResult)) {
							$shiftRow = $db->fetchArray($shiftResult);
							return $shiftRow['shift'];
						} else {
							return "";
						}
					}
					
					function isWeekend($date) {
						$date1 = strtotime($date);
						$date2 = date("l", $date1);
						$date3 = strtolower($date2);
						if(($date3 == "saturday" )|| ($date3 == "sunday")){
							return 1;
						} else {
							return 0;
						}
					}
					
					function getFriday ($givenDate) {
						$off= 5 - date('w', strtotime($givenDate));
						return date('Y-m-d', strtotime("$givenDate $off day"));
					}
			
					function getHalfDayShift($row,$empId,$date,$time,$subempinfo,$leaveType) {
						global $defaultIn, $defaultOut,$halfDayDefault;
						if($time=="first") {
							if (strtoupper(getShiftforDay($leaveType,$empId,$date)) =="FIRSTHALF") {
								if (strtotime($row["First"]) > strtotime($halfDayDefault) && $_SESSION['u_emplocation'] == "BLR") {
									$subempinfo=$subempinfo. '<font color=red>'.$row["First"].'</font>';
								} else {
									$subempinfo=$subempinfo. $row["First"];
								}	
							}
							if (strtoupper(getShiftforDay($leaveType,$empId,$date)) =="SECONDHALF") {
								if (strtotime($row["First"]) > strtotime($defaultIn) && $_SESSION['u_emplocation']=="BLR") {
									$subempinfo=$subempinfo. '<font color=red>'.$row["First"].'</font>';
								} else {
									$subempinfo=$subempinfo. $row["First"];
								}	
								
							}
						
						}
						if($time=="last") {
							if (strtoupper(getShiftforDay($leaveType,$empId,$date))=="FIRSTHALF") {
								if (strtotime($defaultOut) > strtotime($row["Last"]) && $_SESSION['u_emplocation']=="BLR") {
									$subempinfo=$subempinfo. '<font color=red>'.$row["Last"].'</font>';
								} else {
									$subempinfo=$subempinfo. $row["Last"];
								}
							}
							if (strtoupper(getShiftforDay($leaveType,$empId,$date)) =="SECONDHALF") {
								if (strtotime($halfDayDefault) > strtotime($row["Last"]) && $_SESSION['u_emplocation']=="BLR") {
									$subempinfo=$subempinfo. '<font color=red>'.$row["Last"].'</font>';
								} else {
									$subempinfo=$subempinfo. $row["Last"];
								}
							}
						
						}
						return $subempinfo;
					}
			
					function getTotalHRDiff($tot,$dayCount) {
						$totalWorkingHRsPerWeek=0;
						$perDayWorkingHrs="8:30:00";
						list($H,$M,$S)=explode(":",$perDayWorkingHrs);
						$H=$H*$dayCount;
						$M=$M*$dayCount;
						$extraHRS=$M/60;
						if ($extraHRS>=1) {
							$val=floor($extraHRS);
							if (is_integer($extraHRS)) {
								$m=00;
								$H=$H+$val;
								$totalWorkingHRsPerWeek="$H:$m:$S";
							} 
							if (is_double($extraHRS)) {
								$m=$M-(60*$val);
								$H=$H+$val;
								$totalWorkingHRsPerWeek="$H:$m:$S";
							}
			
						} else {
								$totalWorkingHRsPerWeek="$H:$M:$S";
						}
						list($Th,$Tm,$Ts)=explode(":",$totalWorkingHRsPerWeek);
						list($Eh,$Em,$Es)=explode(":",$tot);
						if ($Eh<$Th) {
							return 1;
						} else {
							if ( $Eh==$Th) {
								if($Em<$Tm) {
									return 1;
								} else {
									if ($Es<$Ts) {
										return 1;
									} else {
										return 0;
									}
								}
							} else {
								return 0;
							}
						}
					}
			
					function getColorofRow($count,$totalCount) {
						if($totalCount==0) { 
							return $count."/".($totalCount); 
						} elseif(($count/($totalCount))>0.5) {
							if ($_SESSION['u_emplocation']=='BLR') { 
								return "<font color=red>".$count."/".($totalCount); 
							} else {
								return "<font >".$count."/".($totalCount);
							}
						} else { 
							return $count."/".($totalCount);
						}
					}
			
					//get work from home hour
					function getWFHhour($empid,$date){
						global $db;
						$query="select wfhHrs from extrawfh where eid='".$empid."' and date='".$date."' and status='Approved'";
						$result=$db->query($query);
						if($db->hasRows($result)) {
							$row = $db->fetchArray($result);
							$noh=$row['wfhHrs'];
						} else {
							$noh='-';
						}
						return $noh;
					}
			
					//to get total hours
					function getTotal($numWFH,$diffTime){
						$total="00:00:00";
						if($numWFH=="-")
						{
							$total=timeAdd($total,$diffTime);
						}
						else {
							//$total=$total+$diffTime+$numWFH;
							$totWFH=timeAdd($numWFH,$diffTime);
							$total=timeAdd($total,$totWFH);
						}
						return $total;
					}
			
					function getDataForEMP($empID,$empName,$first,$last,$P10to4,$hr9,$hr45) {
						global $defaultIn, $defaultOut,$db, $defaultIn, $defaultOut,$halfDayDefault;
						$tmpDate='00:00:00';
						$query='select * from `inout` WHERE `First`!= \''.$tmpDate.'\' and `Last`!= \''.$tmpDate.'\' and `EmpID` ='.$empID.' AND 
						`Date` >= \''.getMonday($first).'\' AND `Date` <= \''.getSunday($last).'\';';
						$result=$db->query($query);
						$FinalTotalhour=0;
						$totInAfter=0;
						$totInAfterV=0;
						$totOutBefore=0;
						$totOutBeforeV=0;
						$totdayHr=0;
						$totdayHrV=0;
						$totweekHr=0;
						$totweekHrV=0;
						$totFinalHrv=0;
						$totFinalHr=0;
						$numWFH="00:00:00";
						$finalWFH="00:00:00";
						$subempinfoComp='<table class="table"><tr>';
						$empinfo="";
						$empinfo=$empinfo. '<tr><td><u>';
						$counter=0;
						$count=0;
						while ($row = mysql_fetch_assoc($result)) {
							$in = $row["First"];
							$out= $row["Last"];
							$dayHr1=timediffinHR($in,$out);
							if(strtotime($dayHr1) < strtotime("8:30:00")){
								$totdayHrV=$totdayHrV+1;	
							}else {
								$totdayHr=$totdayHr+1;	
							}
							if (strtotime($in) > strtotime($defaultIn)) {
								$totInAfterV=$totInAfterV+1;
							}else {
								$totInAfter=$totInAfter+1;
								
							}
							if (strtotime($defaultOut) > strtotime($out)) {
								$totOutBeforeV=$totOutBeforeV+1;
							} else {
								$totOutBefore=$totOutBefore+1;
								
							}
						}
						$wkst = getMonday($first);
						$w1= date('Y-m-d', strtotime("$wkst 7 day"));
						while ($w1 < date('Y-m-d',strtotime(getFriday($last)))) {
							$subempinfo= '<td><table class="table table-bordered table-hover">';
							$subempinfo=$subempinfo. '<tr class="info">
								<th>Day</th>
								<th">In</th>
								<th>Out</th>
								<th>total Hr</th>
								<th>WFH</th>
								<th>Total WFH</th>
								<th>Type Of day</th>
							</tr>';
							$w1= date('Y-m-d', strtotime("$wkst 7 day"));
							$tmpDate='00:00:00';
							$queryS='select * from `inout` WHERE `First`!= \''.$tmpDate.'\' and `Last`!= \''.$tmpDate.'\' and `EmpID` ='.$empID.' AND `Date` >= \''.$wkst.'\' AND `Date` < \''.$w1.'\';';
							$result=$db->query($queryS);
							$tempwkst=$wkst;
							$wkst=$w1;
							if (mysql_num_rows($result) == 0) {
								//continue;
							}
							$tot="00:00:00";
							$finalTotal="00:00:00";
							$flag=1;
							$flag_1=0;
							$skip=0;
							$dayCount=0;
							for ($j=0;$j<7;$j++) {
								if ($flag){
									$row = mysql_fetch_assoc($result);
								}
								$curday=date('Y-m-d', strtotime("$tempwkst $j day"));
								if ($row["Date"] != $curday) {
									//check whether the day is saturday or sunday
									$day=date('D,d M y', strtotime($curday));
									if (preg_match('/sun|sat/i',$day)){
										$flag=0;
										continue;
									}
									$subempinfo=$subempinfo. '<tr><td>'.$day.'</td>';
									// Check whether the day is On Site
									
									// Check whether the leave type is On Site
									if (preg_match('/On Site/i',getDay($empID,$curday)))
									{
										$wfhPerDay=getWFHhour($empID,$curday);
										$wfhPerDayTotal=getTotal($wfhPerDay,"8:30:00");
										$tot=timeAdd($tot,"8:30:00");
										$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
										$subempinfo=$subempinfo. '<td>10:00:00</td>';
										$subempinfo=$subempinfo. '<td>18:30:00</td>';
										$subempinfo=$subempinfo. '<td>8:30:00</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDayTotal.'</td>';
										$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
										$flag=0;
														
										$dayCount=$dayCount+1;
										$totInAfter=$totInAfter+1;
										$totOutBefore=$totOutBefore+1;
										$totdayHr=$totdayHr+1;
										continue;
									}
										
									//check whether the day is full day WFH
									if (preg_match('/WFH/i',getDay($empID,$curday)) && getShiftforDay("WFH",$empID,$curday)!="")  
									{
										$wfhPerDay=getWFHhour($empID,$curday);
										$wfhPerDayTotal=getTotal($wfhPerDay,"8:30:00");
										$tot=timeAdd($tot,"8:30:00");
										$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
										$subempinfo=$subempinfo. '<td>10:00:00</td>';
										$subempinfo=$subempinfo. '<td>18:30:00</td>';
										$subempinfo=$subempinfo. '<td>8:30:00</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDayTotal.'</td>';
										$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).' ('.getShiftforDay("WFH",$empID,$curday).')</td></tr>';
										$flag=0;
										$dayCount=$dayCount+1;
										$totInAfter=$totInAfter+1;
										$totOutBefore=$totOutBefore+1;
										$totdayHr=$totdayHr+1;
										continue;
									}
									
									// Check whether the day is First Half-WFH & Second Half-HalfDay
									if (getDay($empID,$curday)=="First Half-WFH & Second Half-HalfDay" && getShiftforDay("First Half-WFH & Second Half-HalfDay",$empID,$curday)!="")  
									{
										$wfhPerDay=getWFHhour($empID,$curday);
										$wfhPerDayTotal=getTotal($wfhPerDay,"4:15:00");
										$tot=timeAdd($tot,"4:15:00");
										$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
										$subempinfo=$subempinfo. '<td>10:00:00</td>';
										$subempinfo=$subempinfo. '<td>14:15:00</td>';
										$subempinfo=$subempinfo. '<td>4:15:00</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDayTotal.'</td>';
										$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
										$flag=0;
										$dayCount=$dayCount+0.5;
										$totInAfter=$totInAfter+1;
										$totOutBefore=$totOutBefore+1;
										$totdayHr=$totdayHr+1;
										continue;
									}
							
									// Check whether the day is "First Half-HalfDay & second Half-WFH"
									if (getDay($empID,$curday)=="First Half-HalfDay & second Half-WFH" && getShiftforDay("First Half-HalfDay & second Half-WFH",$empID,$curday)!="")  
									{
										$wfhPerDay=getWFHhour($empID,$curday);
										$wfhPerDayTotal=getTotal($wfhPerDay,"4:15:00");
										$tot=timeAdd($tot,"4:15:00");
										$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
										$subempinfo=$subempinfo. '<td>14:15:00</td>';
										$subempinfo=$subempinfo. '<td>18:30:00</td>';
										$subempinfo=$subempinfo. '<td>4:15:00</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDayTotal.'</td>';
										$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
										$flag=0;
										$dayCount=$dayCount+0.5;
										$totInAfter=$totInAfter+1;
										$totOutBefore=$totOutBefore+1;
										$totdayHr=$totdayHr+1;
										continue;
									}
									$wfhPerDay=getWFHhour($empID,$curday);
									$finalTotal=timeAdd($finalTotal,$wfhPerDay);
									$subempinfo=$subempinfo. '<td colspan="3">No Data</td>';
									$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
									$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
									$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
									$flag=0;
									continue;
								}
								
								// If the employee comes under the below days, count those days also
								if (isWeekend($row["Date"]) || isFulldayWFH($row["Date"],$empID) || isHoliday($row["Date"]) || isFullDayPTO($row["Date"],$empID)) {
									if (strtotime($row["First"]) > strtotime($defaultIn)) {
										$totInAfterV=$totInAfterV-1;
										$totInAfter=$totInAfter+1;
									}
									if (strtotime($defaultOut) > strtotime($row["Last"])) {
										$totOutBeforeV= $totOutBeforeV-1;
										$totOutBefore=$totOutBefore+1;
									}
									$diffTime=timediffinHR($row["First"],$row["Last"]);
									if(strtotime($diffTime) < strtotime("8:30:00")){
										$totdayHrV=$totdayHrV-1;
										$totdayHr=$totdayHr+1;
									}
									$dayCount=$dayCount-1;
								}
								 
								$dayHr=timediffinHR($row["First"],$row["Last"]);
								$tot=timeAdd($tot,$dayHr);
								$wfhPerDay=getWFHhour($empID,$curday);
								$wfhPerDayTotal=getTotal($wfhPerDay,$dayHr);
								$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
								$subempinfo=$subempinfo. '<tr><td>'.date('D,d-M-y', strtotime($row["Date"])).'</td>';
								$subempinfo=$subempinfo. '<td>';
								
								// If employee applies WFH, either fisr half / second half and decide what data goes to "FIRST" column
								if(isWFH($row['Date'], $empID) ) {
									$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"first",$subempinfo,"WFH");
									if(strtotime($dayHr)>strtotime("4:15:00")) {
										$totdayHr=$totdayHr+1;
										$totdayHrV=$totdayHrV-1;
									}
									if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="FIRSTHALF") {
										if (strtotime($row["First"]) > strtotime($halfDayDefault)) {
										} else {
											$totInAfterV=$totInAfterV-1;
											$totInAfter=$totInAfter+1;
										}
									}
									if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="SECONDHALF") {
										if (strtotime($row["First"]) > strtotime($defaultIn)) {
											$totInAfterV=$totInAfterV-1;
											$totInAfter=$totInAfter+1;
										}
									}
									$tot=timeAdd($tot,"4:15:00");
									$wfhPerDay=getWFHhour($empID,$curday);
									$wfhPerDayTotal=getTotal($wfhPerDay,"4:15:00");
									$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
								} elseif(isHalfDayPTO($row['Date'], $empID)) {
									// If employee applies Half Day PTO, either fisr half / second half and decide what data goes to "FIRST" column
									$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"first",$subempinfo,"HalfDay");
									if(strtotime($dayHr)>strtotime("4:15:00")) {
										$totdayHr=$totdayHr+1;
										$totdayHrV=$totdayHrV-1;
									}
									if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="FIRSTHALF") {
											if (strtotime($row["First"]) > strtotime($halfDayDefault)) {
											} else {
												$totInAfterV=$totInAfterV-1;
												$totInAfter=$totInAfter+1;
											}
									}
									if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="SECONDHALF") {
											if (strtotime($row["First"]) > strtotime($defaultIn)) {
												$totInAfterV=$totInAfterV-1;
												$totInAfter=$totInAfter+1;
											} 
									}
								} 
								else {
									if (strtotime($row["First"]) > strtotime($defaultIn)) {
										if (isWeekend($row["Date"]) || isHoliday($row["Date"]) || isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID)) {
											$subempinfo=$subempinfo. $row["First"];
										}
										else {
											if ($_SESSION['u_emplocation']=='BLR') {
												$subempinfo=$subempinfo. '<font color=red>'.$row["First"].'</font>';
											} else {
												$subempinfo=$subempinfo. '<font>'.$row["First"].'</font>';
											}
										}
									} else {
										$subempinfo=$subempinfo. $row["First"];
									}
								}
							 
								$subempinfo=$subempinfo. '</td>';
								$subempinfo=$subempinfo. '<td>';
								
								// If employee applies WFH, either fisr half / second half and decide what data goes to "LAST" column
								if(isWFH($row['Date'], $empID) ) {
									$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"last",$subempinfo,"WFH");
									if (strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="FIRSTHALF") {
										if (strtotime($defaultOut) > strtotime($row["Last"])) {
											$totOutBeforeV=$totOutBeforeV-1;
											$totOutBefore=$totOutBefore+1;
										} 
									}
									if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="SECONDHALF") {
										if (strtotime($halfDayDefault) > strtotime($row["Last"])) {
										} else {
											$totOutBeforeV=$totOutBeforeV-1;
											$totOutBefore=$totOutBefore+1;
										}
									}
								} elseif(isHalfDayPTO($row['Date'], $empID) ) {
									// If employee applies Half Day PTO, either fisr half / second half and decide what data goes to "LAST" column
									$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"last",$subempinfo,"HalfDay");
									if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date']))=="FIRSTHALF") {
										if (strtotime($defaultOut) > strtotime($row["Last"])) {
											$totOutBeforeV=$totOutBeforeV-1;
											$totOutBefore=$totOutBefore+1;
										} 
									}
									if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="SECONDHALF") {
										if (strtotime($halfDayDefault) > strtotime($row["Last"])) {
										} else {
											$totOutBeforeV=$totOutBeforeV-1;
											$totOutBefore=$totOutBefore+1;
										}
									}
								} 
								else {
									if (strtotime($defaultOut) > strtotime($row["Last"])) {
										if (isWeekend($row["Date"]) || isHoliday($row["Date"])|| isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID)) {
											$subempinfo=$subempinfo. $row["Last"];
										} else {
											if ($_SESSION['u_emplocation']=='BLR') {
												$subempinfo=$subempinfo. '<font color=red>'.$row["Last"].'</font>';
											} else {
												$subempinfo=$subempinfo. '<font>'.$row["Last"].'</font>';
											}
										}
									}
									else {
										$subempinfo=$subempinfo. $row["Last"];
									}
								}
								$subempinfo=$subempinfo. '</td>';
								
								// Get the defaultdayHr based on halfday or fullday or First Half or Second Half WFH
								if(isWFH($row['Date'], $empID)) {
									if (strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="FIRSTHALF" || strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="SECONDHALF") {
										$dayHr=timeAdd($dayHr,"4:15:00");
									}
									$defaultdayHr=strtotime("8:30:00");
								} elseif(isHalfDayPTO($row['Date'], $empID) ) {
									$dayCount=$dayCount-0.5;
									$defaultdayHr=strtotime("4:15:00");
								} else {
									$defaultdayHr=strtotime("8:30:00");
								}
								
								// Get the data for the totaldayHr column
								if(strtotime($dayHr) < $defaultdayHr){
									if (isWeekend($row["Date"]) || isHoliday($row["Date"])|| isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID))  {
										$subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
									} else {
										if ($_SESSION['u_emplocation']=='BLR') {
											$subempinfo=$subempinfo. '<td><font color=red>'.$dayHr.'</font></td>';
										} else {
											$subempinfo=$subempinfo. '<td><font>'.$dayHr.'</font></td>';
										}
									}
								} else {
									$subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
								}
								$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
								$subempinfo=$subempinfo. '<td>'.$wfhPerDayTotal.'</td>';
								
								// Get the data for "Leavetype" column
								if(getDay($empID,$curday)=="No Data") {
									$subempinfo=$subempinfo. '<td>'.$row['TypeOfDay'].'</td></tr>';
								} else {
									$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
								}
							 // Check whether the employee came to office on optional holiday	
								if(isHoliday($row["Date"])) {
									if(isOptionalHoliday($row["Date"])) {
										if(!isOptionalHolidayApplied($row["Date"],$empID)) {
											$dayCount=$dayCount+1;
										}
									}
								}
								$dayCount=$dayCount+1;
								$flag=1;
							}
							# Even if emp comes on saturday/sunday dayCount should not be more than 5.
							if($dayCount>=5) {
								$dayCount=5;
							}
			
							// Get the total Hr's emp came to office per week
							if (getTotalHRDiff($tot,$dayCount) && $_SESSION['u_emplocation']!='MUM') {
								
								$subempinfo=$subempinfo. '<tr><td colspan=2 align="right"><font color=red >Total Hr</font></td>';
								$subempinfo=$subempinfo. '<td><font color=red>'.$tot.'</font></td>';
								$subempinfo=$subempinfo. '<td colspan=3 align="right"><font color=red >Total Hr with WFH</font></td>';
								$subempinfo=$subempinfo. '<td><font color=red>'.$finalTotal.'</font></td></tr>';
								$totweekHrV=$totweekHrV+1;	
								$totFinalHrv=$totFinalHrv+1;
							} else {
								$subempinfo=$subempinfo. '<tr><td colspan=2 align="right">Total Hr</td>';
								$subempinfo=$subempinfo. '<td >'.$tot.'</td>';
								$subempinfo=$subempinfo. '<td colspan=3 align="right">Total Hr with WFH</td>';
								$subempinfo=$subempinfo. '<td >'.$finalTotal.'</td></tr>';
								$totweekHr=$totweekHr+1;
								$totFinalHr=$totFinalHr+1;
									
							}
				
							$subempinfo=$subempinfo. '</table>';	
							$subempinfoComp=$subempinfoComp. $subempinfo;
							if ($counter < 1) 
							{
								$subempinfoComp=$subempinfoComp. '</td>';
							} else {
								$subempinfoComp=$subempinfoComp. '</td></tr><tr>';
								$counter=-1;
							}
							$counter=$counter+1;
							$subempinfo="";
						}
				
						$empinfo=$empinfo. $empName.'</span></u></td>';
						$empinfo=$empinfo. '<td>';
						if ($P10to4 == "NOTMEET") {
							$empinfo=$empinfo.getColorofRow($totInAfterV,($totInAfterV+$totInAfter));
						} else {
							$empinfo=$empinfo.getColorofRow($totInAfter,($totInAfterV+$totInAfter));
						}
						$empinfo=$empinfo. '</td><td>';
						if($P10to4 == "NOTMEET") {
							$empinfo=$empinfo.getColorofRow($totOutBeforeV,($totOutBefore+$totOutBeforeV));
						} else {
							$empinfo=$empinfo.getColorofRow($totOutBefore,($totOutBefore+$totOutBeforeV));
						}
						$empinfo=$empinfo. '</td><td>';
						if ($hr9 == "NOTMEET"){
							$empinfo=$empinfo.getColorofRow($totdayHrV,($totdayHr+$totdayHrV));
						}else {
							$empinfo=$empinfo.getColorofRow($totdayHr,($totdayHr+$totdayHrV));
						}
						$empinfo=$empinfo. '</td><td>';
						if ($hr45 == "NOTMEET") {
							$empinfo=$empinfo.getColorofRow($totweekHrV,($totweekHr+$totweekHrV));
						} else { 
							$empinfo=$empinfo.getColorofRow($totweekHr,($totweekHr+$totweekHrV));
						}	
						$empinfo=$empinfo. '</td><td>';
						$count=$count+ 1;
						
						$empinfo=$empinfo."<p><a href='javascript:void(null);' 
						onclick='showDialog(\"".$count."\",\"".$empID."\",\"".$first."\",\"".$last."\");'>Open</a></p>
						<div id='dialog-modal".$count."' title='Access Detail Graph' style='display: none;'></div>";
						
						$empinfo=$empinfo. '</td></tr>';
						echo $empinfo;
						
						echo $subempinfoComp.'</table>';
						echo '</td></tr>';
					}
			
					function getDataForEMP1($empID,$empName,$first,$last,$P10to4,$hr9,$hr45) {
						global $defaultIn, $defaultOut,$db, $defaultIn, $defaultOut,$halfDayDefault;
						global $sumOfInDays;
						global $sumOfTotalInDays;
						global $sumOfOutDays;
						global $sumOfTotalOutDays;
						global $sumOfDailyhrDays;
						global $sumOfTotalDailyhrDays;
						global $sumOfWeeklyhrDays;
						global $sumOfTotalWeeklyhrDays;
						global $count;
						$tmpDate='00:00:00';
						$query='select * from `inout` WHERE `First`!= \''.$tmpDate.'\' and `Last`!= \''.$tmpDate.'\' and `EmpID` ='.$empID.' AND `Date` >= \''.getMonday($first).'\' AND `Date` <= \''.getSunday($last).'\';';
						$result=$db->query($query);
						$FinalTotalhour=0;
						$totInAfter=0;
						$totInAfterV=0;
						$totOutBefore=0;
						$totOutBeforeV=0;
						$totdayHr=0;
						$totdayHrV=0;
						$totweekHr=0;
						$totweekHrV=0;
						$totFinalHrv=0;
						$totFinalHr=0;
						$numWFH="00:00:00";
						$subempinfoComp="";
						$empinfo="";
						$empinfo=$empinfo. '<tr><td><u><span onclick="toggle(\'sub-'.$empID.'\');">';
						while ($row = mysql_fetch_assoc($result)) {
							$in = $row["First"];
							$out= $row["Last"];
							$dayHr1=timediffinHR($in,$out);
							if(strtotime($dayHr1) < strtotime("8:30:00")){
								$totdayHrV=$totdayHrV+1;	
							}else {
								$totdayHr=$totdayHr+1;	
							}
							if (strtotime($in) > strtotime($defaultIn)) {
								$totInAfterV=$totInAfterV+1;
							}else {
								$totInAfter=$totInAfter+1;
								
							}
							if (strtotime($defaultOut) > strtotime($out)) {
								$totOutBeforeV=$totOutBeforeV+1;
							} else {
								$totOutBefore=$totOutBefore+1;
							}
						}
					
						$curday=date('Y-m-d', strtotime("$tempwkst $j day"));
						$numWFH=getWFHhour($empID,$curday);
						$diffTime=timediffinHR($row["First"],$row["Last"]);
						//$finalWFH=timeAdd($finalWFH, getTotal($numWFH, $diffTime));
						$wkst = getMonday($first);
						$w1= date('Y-m-d', strtotime("$wkst 7 day"));
						while ($w1 < date('Y-m-d',strtotime($last))) {
							$subempinfo= '<table class="table table-hover">';
							$subempinfo=$subempinfo. '<tr class="info">
									<th>Day</th>
									<th>In</th>
									<th>Out</th>
									<th>Total Hr</th>
									<th>WFH Hr</th>
									<th>Total</th>
									<th>Type Of day</th>
								</tr>';
							$w1= date('Y-m-d', strtotime("$wkst 7 day"));
							$tmpDate='00:00:00';
							$queryS='select * from `inout` WHERE `First`!= \''.$tmpDate.'\' and `Last`!= \''.$tmpDate.'\' and `EmpID` ='.$empID.' AND `Date` >= \''.$wkst.'\' AND `Date` < \''.$w1.'\';';
							$result=$db->query($queryS);
							$tempwkst=$wkst;
							$wkst=$w1;
							if (mysql_num_rows($result) == 0) {
								//continue;
							}
							$tot="00:00:00";
							$finalTotal="00:00:00";
							$flag=1;
							$dayCount=0;
							for ($j=0;$j<7;$j++){
								if ($flag){
									$row = mysql_fetch_assoc($result);
								}
								$curday=date('Y-m-d', strtotime("$tempwkst $j day"));
								if ($row["Date"] != $curday){
									
									//check whether the day is saturday or sunday
									$day=date('D,d M y', strtotime($curday));
									if (preg_match('/sun|sat/i',$day)){
										$flag=0;
										continue;
									}
									$subempinfo=$subempinfo. '<tr><td>'.$day.'</td>';
									
									// Check whether the leave type is On Site
									if (preg_match('/On Site/i',getDay($empID,$curday))){
										$wfhPerDay=getWFHhour($empID,$curday);
										$wfhPerDayTotal=getTotal($wfhPerDay,"8:30:00");
										$tot=timeAdd($tot,"8:30:00");
										$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
										$subempinfo=$subempinfo. '<td>10:00:00</td>';
										$subempinfo=$subempinfo. '<td>18:30:00</td>';
										$subempinfo=$subempinfo. '<td>8:30:00</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDayTotal.'</td>';
										$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).' ('.getShiftforDay("WFH",$empID,$curday).')</td></tr>';
										$flag=0;
										$dayCount=$dayCount+1;
										$totInAfter=$totInAfter+1;
										$totOutBefore=$totOutBefore+1;
										$totdayHr=$totdayHr+1;
										continue;
									}
									
									//check whether the day is full day WFH
									if (preg_match('/WFH/i',getDay($empID,$curday)) && getShiftforDay("WFH",$empID,$curday)!="")  
									{
										$wfhPerDay=getWFHhour($empID,$curday);
										$wfhPerDayTotal=getTotal($wfhPerDay,"8:30:00");
										$tot=timeAdd($tot,"8:30:00");
										$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
										$subempinfo=$subempinfo. '<td>10:00:00</td>';
										$subempinfo=$subempinfo. '<td>18:30:00</td>';
										$subempinfo=$subempinfo. '<td>8:30:00</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDayTotal.'</td>';
										$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).' ('.getShiftforDay("WFH",$empID,$curday).')</td></tr>';
										$flag=0;
										$dayCount=$dayCount+1;
										$totInAfter=$totInAfter+1;
										$totOutBefore=$totOutBefore+1;
										$totdayHr=$totdayHr+1;
										continue;
									}
									
									// Check whether the day is First Half-WFH & Second Half-HalfDay
									if (getDay($empID,$curday)=="First Half-WFH & Second Half-HalfDay" && getShiftforDay("First Half-WFH & Second Half-HalfDay",$empID,$curday)!="")  
									{
					
										$wfhPerDay=getWFHhour($empID,$curday);
										$wfhPerDayTotal=getTotal($wfhPerDay,"4:15:00");
										$tot=timeAdd($tot,"4:15:00");
										$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
										$subempinfo=$subempinfo. '<td>10:00:00</td>';
										$subempinfo=$subempinfo. '<td>14:15:00</td>';
										$subempinfo=$subempinfo. '<td>4:15:00</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDayTotal.'</td>';
										$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
										$flag=0;
										$dayCount=$dayCount+0.5;
										$totInAfter=$totInAfter+1;
										$totOutBefore=$totOutBefore+1;
										$totdayHr=$totdayHr+1;
										continue;
									}
									
									// Check whether the day is "First Half-HalfDay & second Half-WFH"
									if (getDay($empID,$curday)=="First Half-HalfDay & second Half-WFH" && getShiftforDay("First Half-HalfDay & second Half-WFH",$empID,$curday)!="")  
									{
										$wfhPerDay=getWFHhour($empID,$curday);
										$wfhPerDayTotal=getTotal($wfhPerDay,"4:15:00");
										$tot=timeAdd($tot,"4:15:00");
										$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
										$subempinfo=$subempinfo. '<td>14:15:00</td>';
										$subempinfo=$subempinfo. '<td>18:30:00</td>';
										$subempinfo=$subempinfo. '<td>4:15:00</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
										$subempinfo=$subempinfo. '<td>'.$wfhPerDayTotal.'</td>';
										$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
										$flag=0;
										$dayCount=$dayCount+0.5;
										$totInAfter=$totInAfter+1;
										$totOutBefore=$totOutBefore+1;
										$totdayHr=$totdayHr+1;
										continue;
									}
									$wfhPerDay=getWFHhour($empID,$curday);
									$finalTotal=timeAdd($finalTotal,$wfhPerDay);
									$subempinfo=$subempinfo. '<td colspan="3">No Data</td>';
									$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
									$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
									$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
									$flag=0;
									continue;
								}
								
								// If the employee comes under the below days, count those days also
								if (isWeekend($row["Date"]) || isFulldayWFH($row["Date"],$empID) || isHoliday($row["Date"])|| isFullDayPTO($row["Date"],$empID)) {
									if (strtotime($row["First"]) > strtotime($defaultIn)) {
										$totInAfterV=$totInAfterV-1;
										$totInAfter=$totInAfter+1;
									}
									if (strtotime($defaultOut) > strtotime($row["Last"])) {
										$totOutBeforeV= $totOutBeforeV-1;
										$totOutBefore=$totOutBefore+1;
									}
									$diffTime=timediffinHR($row["First"],$row["Last"]);
									if(strtotime($diffTime) < strtotime("8:30:00")){
										$totdayHrV=$totdayHrV-1;
										$totdayHr=$totdayHr+1;
									}
									$dayCount=$dayCount-1;
								}
								
								$dayHr=timediffinHR($row["First"],$row["Last"]);
								
								$tot=timeAdd($tot,$dayHr);
								$wfhPerDay=getWFHhour($empID,$curday);
								$wfhPerDayTotal=getTotal($wfhPerDay,$dayHr);
								$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
								$subempinfo=$subempinfo. '<tr><td>'.date('D,d-M-y', strtotime($row["Date"])).'</td>';
								$subempinfo=$subempinfo. '<td>';
								
								// If employee applies WFH, either fisr half / second half and decide what data goes to "FIRST" column
								if(isWFH($row['Date'], $empID) ) {
									$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"first",$subempinfo,"WFH");
									if(strtotime($dayHr)>strtotime("4:15:00")) {
										$totdayHr=$totdayHr+1;
										$totdayHrV=$totdayHrV-1;
									}
									if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="FIRSTHALF") {
										if (strtotime($row["First"]) > strtotime($halfDayDefault)) {
										} else {
											$totInAfterV=$totInAfterV-1;
											$totInAfter=$totInAfter+1;
										}
									}
									if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="SECONDHALF") {
										if (strtotime($row["First"]) > strtotime($defaultIn)) {
											$totInAfterV=$totInAfterV-1;
											$totInAfter=$totInAfter+1;
										}
									}
									$tot=timeAdd($tot,"4:15:00");
									$wfhPerDay=getWFHhour($empID,$curday);
									$wfhPerDayTotal=getTotal($wfhPerDay,"4:15:00");
									$finalTotal=timeAdd($finalTotal,$wfhPerDayTotal);
								} elseif(isHalfDayPTO($row['Date'], $empID) ) {
									// If employee applies Half Day PTO, either fisr half / second half and decide what data goes to "FIRST" column
									$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"first",$subempinfo,"HalfDay");
									if(strtotime($dayHr)>strtotime("4:15:00")) {
										$totdayHr=$totdayHr+1;
										$totdayHrV=$totdayHrV-1;
									}
									if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="FIRSTHALF") {
										if (strtotime($row["First"]) > strtotime($halfDayDefault)) {
										} else {
											$totInAfterV=$totInAfterV-1;
											$totInAfter=$totInAfter+1;
										}
									}
									if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="SECONDHALF") {
										if (strtotime($row["First"]) > strtotime($defaultIn)) {
											$totInAfterV=$totInAfterV-1;
											$totInAfter=$totInAfter+1;
										} 
									}
								} else {
										if (strtotime($row["First"]) > strtotime($defaultIn)) {
											if (isWeekend($row["Date"]) || isHoliday($row["Date"]) || isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID)) {
												$subempinfo=$subempinfo. $row["First"];
											} else {
												$subempinfo=$subempinfo. '<font color=red>'.$row["First"].'</font>';
											}
										} else {
											$subempinfo=$subempinfo. $row["First"];
										}
								}
							 
								$subempinfo=$subempinfo. '</td>';
								$subempinfo=$subempinfo. '<td>';
								
								// If employee applies WFH, either fisr half / second half and decide what data goes to "LAST" column
								if(isWFH($row['Date'], $empID) ) {
									$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"last",$subempinfo,"WFH");
									if (strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="FIRSTHALF") {
										if (strtotime($defaultOut) > strtotime($row["Last"])) {
											$totOutBeforeV=$totOutBeforeV-1;
											$totOutBefore=$totOutBefore+1;
										} 
									}
									if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="SECONDHALF") {
										if (strtotime($halfDayDefault) > strtotime($row["Last"])) {
										} else {
											$totOutBeforeV=$totOutBeforeV-1;
											$totOutBefore=$totOutBefore+1;
										}
									}
								}
								elseif(isHalfDayPTO($row['Date'], $empID) ) {
									// If employee applies Half Day PTO, either fisr half / second half and decide what data goes to "LAST" column
									$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"last",$subempinfo,"HalfDay");
									if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date']))=="FIRSTHALF") {
										if (strtotime($defaultOut) > strtotime($row["Last"])) {
												$totOutBeforeV=$totOutBeforeV-1;
												$totOutBefore=$totOutBefore+1;
										} 
									}
									if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="SECONDHALF") {
										if (strtotime($halfDayDefault) > strtotime($row["Last"])) {
										} else {
												$totOutBeforeV=$totOutBeforeV-1;
												$totOutBefore=$totOutBefore+1;
										}
									}
								} 
								else {
									if (strtotime($defaultOut) > strtotime($row["Last"])) {
										if (isWeekend($row["Date"]) || isHoliday($row["Date"])|| isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID)) {
											$subempinfo=$subempinfo. $row["Last"];
										} else {
											$subempinfo=$subempinfo. '<font color=red>'.$row["Last"].'</font>';
										}
									} else {
										$subempinfo=$subempinfo. $row["Last"];
									}
								}
								$subempinfo=$subempinfo. '</td>';
								
								// Get the defaultdayHr based on halfday or fullday or First Half or Second Half WFH
								if(isWFH($row['Date'], $empID)) {
									if (strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="FIRSTHALF" || strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="SECONDHALF") {
										$dayHr=timeAdd($dayHr,"4:15:00");
									}
									$defaultdayHr=strtotime("8:30:00");
								} elseif(isHalfDayPTO($row['Date'], $empID) ) {
									$dayCount=$dayCount-0.5;
									$defaultdayHr=strtotime("4:15:00");
								} else {
									$defaultdayHr=strtotime("8:30:00");
								}
								
								// Get the data for the totaldayHr column
								if(strtotime($dayHr) < $defaultdayHr){
									if (isWeekend($row["Date"]) || isHoliday($row["Date"])|| isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID)) {
										   $subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
									} else {
										   $subempinfo=$subempinfo. '<td><font color=red>'.$dayHr.'</font></td>';
									}
								} else {
									$subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
								}
								//to get Extra WFH hour detail
								$subempinfo=$subempinfo. '<td>'.$wfhPerDay.'</td>';
								$subempinfo=$subempinfo. '<td>'.$wfhPerDayTotal.'</td>';
								// Get the data for the LeaveType column
								if(getDay($empID,$curday)=="No Data") {
									  $subempinfo=$subempinfo. '<td>'.$row['TypeOfDay'].'</td></tr>';
								} else {
									  $subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
								}
								
								// Check whether the employee came to office on optional holiday
								if(isHoliday($row["Date"])) {
									if(isOptionalHoliday($row["Date"])) {
										if(!isOptionalHolidayApplied($row["Date"],$empID)) {
											$dayCount=$dayCount+1;
										}
									}
								}
								$dayCount=$dayCount+1;
								$flag=1;
							}
							
							# Even if emp comes on saturday/sunday dayCount should not be more than 5.
							if($dayCount>=5) {
								$dayCount=5;
							}
							// Get the total Hr's emp came to office per week
							if (getTotalHRDiff($tot,$dayCount) && $_SESSION['u_emplocation']=='BLR') {
							
								$subempinfo=$subempinfo. '<tr><td colspan=2 align="right"><font color=red >Total Hr</font></td>';
								$subempinfo=$subempinfo. '<td><font color=red>'.$tot.'</font></td>';
								$subempinfo=$subempinfo. '<td colspan=3 align="right"><font color=red >Total Hr with WFH</font></td>';
								$subempinfo=$subempinfo. '<td ><font color=red>'.$finalTotal.'</font></td></tr>';
								$totweekHrV=$totweekHrV+1;	
								$totFinalHrv=$totFinalHrv+1;
							} else {
								$subempinfo=$subempinfo. '<tr><td colspan=2 align="right"><font color=red >Total Hr</font></td>';
								$subempinfo=$subempinfo. '<td><font color=red>'.$tot.'</font></td>';
								$subempinfo=$subempinfo. '<td colspan=3 align="right"><font color=red >Total Hr with WFH</font></td>';
								$subempinfo=$subempinfo. '<td><font color=red>'.$finalTotal.'</font></td></tr>';
								$totweekHr=$totweekHr+1;
								$totFinalHr=$totFinalHr+1;
							}
							$subempinfo=$subempinfo. '</table>';	
							$subempinfoComp=$subempinfoComp. $subempinfo;
							$subempinfo="";
						}
						$empinfo=$empinfo. $empName.'</span></u></td>';
						$empinfo=$empinfo. '<td>';
						/*if ($P10to4 == "NOTMEET") {
							$empinfo=$empinfo.getColorofRow($totInAfterV,($totInAfterV+$totInAfter));
							$sumOfInDays=$sumOfInDays+$totInAfterV;
							$sumOfTotalInDays=$sumOfTotalInDays+$totInAfterV+$totInAfter;
						} else {
							$empinfo=$empinfo.getColorofRow($totInAfter,($totInAfterV+$totInAfter));
							$sumOfInDays=$sumOfInDays+$totInAfter;
							$sumOfTotalInDays=$sumOfTotalInDays+$totInAfterV+$totInAfter;
						}*/
						$empinfo=$empinfo. '</td><td>';
						if($P10to4 == "NOTMEET") {
							$empinfo=$empinfo.getColorofRow($totOutBeforeV,($totOutBefore+$totOutBeforeV));
							$sumOfOutDays=$sumOfOutDays+$totOutBeforeV;
							$sumOfTotalOutDays=$sumOfTotalOutDays+$totOutBefore+$totOutBeforeV;
						} else {
							$empinfo=$empinfo.getColorofRow($totOutBefore,($totOutBefore+$totOutBeforeV));
							$sumOfOutDays=$sumOfOutDays+$totOutBefore;
							$sumOfTotalOutDays=$sumOfTotalOutDays+$totOutBefore+$totOutBeforeV;
						}
						$empinfo=$empinfo. '</td><td>';
						if ($hr9 == "NOTMEET") {
							$empinfo=$empinfo.getColorofRow($totdayHrV,($totdayHr+$totdayHrV));
							$sumOfDailyhrDays=$sumOfDailyhrDays+$totdayHrV;
							$sumOfTotalDailyhrDays=$sumOfTotalDailyhrDays+$totdayHr+$totdayHrV;
						} else {
							$empinfo=$empinfo.getColorofRow($totdayHr,($totdayHr+$totdayHrV));
							$sumOfDailyhrDays=$sumOfDailyhrDays+$totdayHr;
							$sumOfTotalDailyhrDays=$sumOfTotalDailyhrDays+$totdayHr+$totdayHrV;
						}
						$empinfo=$empinfo. '</td><td>';
						if ($hr45 == "NOTMEET") { 
							$empinfo=$empinfo.getColorofRow($totweekHrV,($totweekHr+$totweekHrV));
							$sumOfWeeklyhrDays=$sumOfWeeklyhrDays+$totweekHrV;
							$sumOfTotalWeeklyhrDays=$sumOfTotalWeeklyhrDays+$totweekHr+$totweekHrV;
						} else {
							$empinfo=$empinfo.getColorofRow($totweekHr,($totweekHr+$totweekHrV));
							$sumOfWeeklyhrDays=$sumOfWeeklyhrDays+$totweekHr;
							$sumOfTotalWeeklyhrDays=$sumOfTotalWeeklyhrDays+$totweekHr+$totweekHrV;
						}
						$empinfo=$empinfo. '</td><td>';
					
						$count=$count+ 1;
						$empinfo=$empinfo."<p><a href='javascript:void(null);' 
								onclick='showDialog(\"".$count."\",\"".$empID."\",\"".$first."\",\"".$last."\");'>Open</a></p>
								<div id='dialog-modal".$count."' title='Access Detail Graph' style='display: none;'></div>";	
						$empinfo=$empinfo. '</td></tr>';
						$empinfo=$empinfo. '<tr style="display:none;" sub-'.$empID.'="att" ><td colspan="5">';
						echo $empinfo;
						echo $subempinfoComp;
						echo '</td></tr>';
					}
			
					function getMonday ($givenDate) {
						$off= 1 - date('w', strtotime($givenDate));
						return date('Y-m-d', strtotime("$givenDate $off day"));
					}
					
					function getSunday($givenDate) {
						$off= 7 - date('w', strtotime($givenDate));
						return date('Y-m-d', strtotime("$givenDate $off day"));
					
					}
			
					function createTableHeader($P10to4, $hr9, $hr45) {
						echo '<table class="table table-bordered">
								<tr class="info">
									<th>Name</th>
									<th>No of days';
						if ($P10to4 == "NOTMEET") {
							echo ' after ';
						} else {
							echo ' before ';
						}
						echo '10 AM</th><th>No of days';
						if ($P10to4 == "NOTMEET") {
							echo ' before ';
						} else {
							echo ' after ';
						}	
						echo '4 PM</th><th>No of Daily Hr-';
						echo $hr9;
						echo '</th><th>No of Total Hr-';
						echo $hr45;
						echo ' </th><th> Graph</th></tr>';
					}
			
					function getTeamPercentage() {
						global $sumOfInDays;
						global $sumOfTotalInDays;
						global $sumOfOutDays;
						global $sumOfTotalOutDays;
						global $sumOfDailyhrDays;
						global $sumOfTotalDailyhrDays;
						global $sumOfWeeklyhrDays;
						global $sumOfTotalWeeklyhrDays;
						If($sumOfTotalInDays!=0 && $sumOfTotalOutDays!=0 && $sumOfTotalDailyhrDays!=0 && $sumOfWeeklyWFHhrDays!=0 && $sumOfTotalWeeklyDays!=0 &&$sumOfTotalWeeklyhrDays!=0) {
							echo "<tr>
									<td><b>Total</b>
									<td class='teamMorning'><b>".$sumOfInDays."/".$sumOfTotalInDays." (".round((($sumOfInDays/$sumOfTotalInDays)*100),2)."%)</b></td>
									<td class='teamEvening'><b>".$sumOfOutDays."/".$sumOfTotalOutDays." (".round((($sumOfOutDays/$sumOfTotalOutDays)*100),2)."%)</b></td>
									<td class='teamDaily'><b>".$sumOfDailyhrDays."/".$sumOfTotalDailyhrDays." (".round((($sumOfDailyhrDays/$sumOfTotalDailyhrDays)*100),2)."%)</b></td>
									<td class='teamWeek'><b>".$sumOfWeeklyhrDays."/".$sumOfTotalWeeklyhrDays." (".round((($sumOfWeeklyhrDays/$sumOfTotalWeeklyhrDays)*100),2)."%)</b></td>
									<td></td>
								</tr></table>";
						}
					}
			
					function setTeamPercentageNull() {
						global $sumOfInDays;
						global $sumOfTotalInDays;
						global $sumOfOutDays;
						global $sumOfTotalOutDays;
						global $sumOfDailyhrDays;
						global $sumOfTotalDailyhrDays;
						global $sumOfWeeklyhrDays;
						global $sumOfTotalWeeklyhrDays;
						$sumOfInDays=0;
						$sumOfTotalInDays=0;
						$sumOfOutDays=0;
						$sumOfTotalOutDays=0;
						$sumOfDailyhrDays=0;
						$sumOfTotalDailyhrDays=0;
						$sumOfWeeklyhrDays=0;
						$sumOfTotalWeeklyhrDays=0;
					}
			
			
					if (isset($_REQUEST['AttInd'])) {
								$fromDate = $_REQUEST['fromdate'];
								$toDate = $_REQUEST['todate'];
								$P10to4 = $_REQUEST['AdvCond1'];
								$hr9 = $_REQUEST['AdvCond2'];
								$hr45 = $_REQUEST['AdvCond3'];
								if(isset($_REQUEST['UGroup'])) {
									$grp = $_REQUEST['UGroup'];
								}
								if (isset($_REQUEST['getDeptemp'])) {
									$getDeptemp = $_REQUEST['getDeptemp'];
								} else {
									$getDeptemp = "";
								}
								if (isset($_REQUEST['UDept'])) {
									$getDept = $_REQUEST['UDept'];
								} else {
									$getDept = "";
								}
								echo '<script>
										$("#fromdate").val("' . $fromDate . '");
										$("#todate").val("' . $toDate . '");
										$(".ui-dialog").remove();
										</script>';
								echo "<div id='accessData'>";
								echo "<h5><center><font color=\"red\">Total working hours per day is changed from 8:00 hrs to 8:30 hrs.</font></u></center></h5>";
								echo "<h3><u><center>Access details from $fromDate to $toDate for ";
								
								if (isset($_REQUEST['getDeptemp'])) {
									echo getempName($_REQUEST['getDeptemp']);
								}
								if (isset($_REQUEST['UDept'])) {
									echo " (" . $_REQUEST['UDept'] . ")</center><u></h3><br>";
								}
					
								// Check whether the employee department is HR or employee is Srinivas Goli
								if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
									if ($getDept == 'ALL') {
										$query = "SELECT distinct(dept) FROM `emp` ORDER BY `dept` ASC";
										//$query = "SELECT distinct(a.dept),b.wfhHrs,b.eid,b.date FROM emp a, extrawfh b where b.eid=a.empid ORDER BY a.dept ASC ";
										$self = 1;
									} elseif ($getDeptemp == 'ALL') {
											$query = "SELECT * FROM emp WHERE state='Active' and dept='" . $getDept . "'";
											//$query="select  a.empid,a.empusername,a.empname,a.joiningdate,a.birthdate,a.dept,a.managerid, a.managerusername,a.managername, a.managerlevel,a.role, a.group,a.emp_emailid, a.manager_emailid, a.location, a.state,a.track,b.eid,b.date,b.wfhhrs,b.status FROM emp a, extrawfh b WHERE a.state='Active' and `a.dept` = '" . $getDept . "' ORDER BY `a.emp`.`a.empname` ASC";
											$self = 1;
									} elseif ($_REQUEST['UDept'] == $_SESSION['u_empid']) {
										$query = "SELECT a.empid,a.empusername,a.empname,a.joiningdate,a.birthdate,a.dept,a.managerid, a.managerusername,a.managername, a.managerlevel,a.role, a.group,a.emp_emailid, a.manager_emailid, a.location, a.state,a.track,b.eid,b.date,b.wfhhrs,b.status FROM emp a, extrawfh b WHERE a.state='Active' and `a.empid` = '" . $getDept . "' ORDER BY `a.emp`.`a.empname` ASC";
									} else {
										$query = "SELECT * FROM `emp` WHERE state='Active' and `empid` = '".$_REQUEST['getDeptemp']."' ORDER BY `emp`.`empname` ASC";
										//$query = "SELECT a.empid,a.empusername,a.empname,a.joiningdate,a.birthdate,a.dept,a.managerid, a.managerusername,a.managername, a.managerlevel,a.role, a.group,a.emp_emailid, a.manager_emailid, a.location, a.state,a.track,b.eid,b.date,b.wfhhrs,b.status FROM emp a, extrawfh b  WHERE a.state='Active' and `a.empid` = '".$_REQUEST['getDeptemp']."' ORDER BY `a.emp`.`a.empname` ASC";
									}
									$result = $db -> query($query);
									if ($getDept == 'ALL') {
										echo '<div id="accordion">';
										while ($row = mysql_fetch_assoc($result)) {
											echo "<h3>".$row['dept']."</h3><div>";
											setTeamPercentageNull();
											createTableHeader($P10to4, $hr9, $hr45);
											$getEmployeesQueryResult=$db ->query("select * from emp where state='Active' and dept='".$row['dept']."'");
											while($getEmpRow= mysql_fetch_assoc($getEmployeesQueryResult)) {
												getDataForEMP1($getEmpRow["empid"], $getEmpRow["empname"], $fromDate, $toDate, $P10to4, $hr9, $hr45);
											}
											getTeamPercentage();
											echo "</div>";
										}
										echo "</div>";
									} else {
										if ($getDeptemp == 'ALL') {
											createTableHeader($P10to4, $hr9, $hr45);
											while ($row = mysql_fetch_assoc($result)) {
												getDataForEMP1($row["empid"], $row["empname"], $fromDate, $toDate, $P10to4, $hr9, $hr45);
											}
											getTeamPercentage();
										} else {
											createTableHeader($P10to4, $hr9, $hr45);
											while ($row = mysql_fetch_assoc($result)) {
												getDataForEMP($row["empid"], $row["empname"], $fromDate, $toDate, $P10to4, $hr9, $hr45);
											}
										}
									}
								} else if (strtoupper($_SESSION['user_desgn']) == 'MANAGER') {
									// Check whether the employee is a manager
									if ($getDept == 'ALL') {
										//$query = "SELECT distinct(a.dept),b.wfhHrs,b.eid,b.date FROM emp a, extrawfh b where a.managerid='".$_SESSION['u_empid']."' and a.state='Active'";
											
										$query = "SELECT distinct(dept) FROM emp WHERE managerid='".$_SESSION['u_empid']."' and state='Active'";
										$self = 1;
									} elseif ($getDeptemp == 'ALL') {
										//$query="select  a.empid,a.empusername,a.empname,a.joiningdate,a.birthdate,a.dept,a.managerid, a.managerusername,a.managername, a.managerlevel,a.role, a.group,a.emp_emailid, a.manager_emailid, a.location, a.state,a.track,b.eid,b.date,b.wfhhrs,b.status FROM emp a, extrawfh b WHERE a.state='Active' and `a.dept` = '" . $getDept . "'";
										
										$query = "SELECT * FROM emp WHERE state='Active' and dept='" . $getDept . "'";
									} elseif ($_REQUEST['UDept'] == $_SESSION['u_empid']) {
										//$query="select  a.empid,a.empusername,a.empname,a.joiningdate,a.birthdate,a.dept,a.managerid, a.managerusername,a.managername, a.managerlevel,a.role, a.group,a.emp_emailid, a.manager_emailid, a.location, a.state,a.track,b.eid,b.date,b.wfhhrs,b.status FROM emp a, extrawfh b WHERE a.state='Active' and `a.empid` = '" . $getDept . "' ORDER BY `a.emp`.`a.empname` ASC";
										
										$query = "SELECT * FROM emp WHERE state='Active' and `empid` = '" . $getDept . "' ORDER BY `emp`.`empname` ASC";
									} else {
										if ($_SESSION['u_managerlevel'] != 'level1') {
											//$query="select  a.empid,a.empusername,a.empname,a.joiningdate,a.birthdate,a.dept,a.managerid, a.managerusername,a.managername, a.managerlevel,a.role, a.group,a.emp_emailid, a.manager_emailid, a.location, a.state,a.track,b.eid,b.date,b.wfhhrs,b.status FROM emp a, extrawfh b WHERE a.state='Active' and `empid` = '".$_REQUEST['getDeptemp']."' ORDER BY `a.emp`.`a.empname` ASC";
											$query = "SELECT * FROM emp WHERE state='Active' and `empid` = '".$_REQUEST['getDeptemp']."' ORDER BY `emp`.`empname` ASC";
										} else {
											if($grp=="ALL") {
												//$query="select  a.empid,a.empusername,a.empname,a.joiningdate,a.birthdate,a.dept,a.managerid, a.managerusername,a.managername, a.managerlevel,a.role, a.group,a.emp_emailid, a.manager_emailid, a.location, a.state,a.track,b.eid,b.date,b.wfhhrs,b.status FROM emp a, extrawfh b WHERE a.state='Active' and `empid` = '".$_REQUEST['getDeptemp']."' ORDER BY `a.emp`.`a.empname` ASC";
												
												$query = "SELECT * FROM emp WHERE state='Active' and managerid='".$_SESSION['u_empid']."' union SELECT * FROM emp WHERE state='Active' and empid='".$_SESSION['u_empid']."' ORDER BY `empname` ASC";
											} else {
												//$query="select  a.empid,a.empusername,a.empname,a.joiningdate,a.birthdate,a.dept,a.managerid, a.managerusername,a.managername, a.managerlevel,a.role, a.group,a.emp_emailid, a.manager_emailid, a.location, a.state,a.track,b.eid,b.date,b.wfhhrs,b.status FROM emp a, extrawfh b WHERE a.state='Active' and `empid` = '".$grp."' ORDER BY `a.emp`.`a.empname` ASC";
												
												$query = "SELECT * FROM emp WHERE state='Active' and `empid` = '".$grp."' ORDER BY `emp`.`empname` ASC";
											}
										}
									}
									$result = $db -> query($query);
									if ($getDept == 'ALL') {
										echo '<div id="accordion">';
										while ($row = mysql_fetch_assoc($result)) {
											echo "<h3>".$row['dept']."</h3><div>";
											createTableHeader($P10to4, $hr9, $hr45);
											
											$getEmployeesQueryResult=$db -> query("select * from emp where state='Active' and dept='".$row['dept']."'");
											while($getEmpRow= mysql_fetch_assoc($getEmployeesQueryResult)) {
												getDataForEMP1($getEmpRow["empid"], $getEmpRow["empname"], $fromDate, $toDate, $P10to4, $hr9, $hr45);
											}
											getTeamPercentage();
											echo "</div>";
										}
										echo "</div>";
									} elseif ($getDeptemp == 'ALL') {
										createTableHeader($P10to4, $hr9,$hr45);
										while ($row = mysql_fetch_assoc($result)) {
											getDataForEMP1($row["empid"], $row["empname"], $fromDate, $toDate, $P10to4, $hr9, $hr45);
											if ($self == 1) {
												getDataForEMP1($_SESSION["u_empid"], $_SESSION['u_fullname'], $fromDate, $toDate, $P10to4, $hr9, $hr45);
												$self = 0;
											}
										}
										getTeamPercentage();
									} else {
											createTableHeader($P10to4, $hr9, $hr45);
											while ($row = mysql_fetch_assoc($result)) {
												if($grp=="ALL") {
													getDataForEMP1($row["empid"], $row["empname"], $fromDate, $toDate, $P10to4, $hr9, $hr45);
												} else {
													getDataForEMP($row["empid"], $row["empname"], $fromDate, $toDate, $P10to4, $hr9, $hr45);
												}
											}
											if($grp=="ALL") {
												getTeamPercentage();
											}
									}
								} else {
								// Here the employee is at last position in hierarchy
									createTableHeader($P10to4, $hr9, $hr45);
									while ($row = mysql_fetch_assoc($result)) {
										getDataForEMP($_SESSION['u_empid'], $_SESSION['u_fullname'], $fromDate, $toDate, $P10to4, $hr9, $hr45);
									}
								}
						$db -> closeConnection();
					}
				?>
			</div><!-- row div end -->
		</div><!-- container-fluid div start -->
	</body>
</html>
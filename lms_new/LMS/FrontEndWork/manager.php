<?php
session_start();
require_once 'Library.php';
require_once 'attendenceFunctions.php';
require_once 'generalFunctions.php';
error_reporting("E_ALL");
$db=connectToDB();
?>
<html>
	<head>
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/frontend.css" />
		<script src="public/js/jquery/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.searchFilter.js"></script>
		<script src="public/js/countdown/countdown.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
		<script type="text/javascript" src="projectjs/index.js"></script>
		<script>
		$(document).ready(function() {
			$('body').bind('mousedown keydown', function(event) {
				$('#counter').countdown('option', {
					until : +1200
				});
			});
		$(".open-datetimepicker").datetimepicker({
		    format: "dd/mm/yy"
		});
		$(".open-datetimepicker1").datetimepicker({
		    format: "dd/mm/yy"
		});
		$("#addextrawfhmanager").click(function() {
		     hidealldiv('loadmanagersection');
     		 //$("#loadmanagersection").load('wfhhours/manageraddwfhforemp.php?role=manager');
  		});
		});
		</script>
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
			<label style="margin-left:60px; margin-right:5px;margin-top:14px; font-size:16px;">Time Left:-</label>
		</div><!--navbar header-->
		<h4 id="counter" class="countdown"></h4>

			<script>
				$('#counter').countdown({
					until : +1200,
					compact : true,
					description : '',
					onExpiry : liftOff,
					format : 'HMS'
				});
			
				function liftOff() {
					var r = confirm("Your session is expired. Do you want to extend the session?");
					if (r == true) {
						window.location = "lms.php";
					} else {
						alert("Your session is expired. Logging out");
						window.location = "login.php";
					}
				}
			</script>
		<div id="navbar" class="navbar-collapse collapse">
		<ul class="nav navbar-nav navbar-right" style="padding-right:10px;">
		<li id="home"><a href="Holidays.php">Holiday List</a></li>
		<li><a href="attendance.php">Attendance</a></li>
		<li><a href="trackLeaves.php">Track Leaves</a></li>
		<li><a href="leavecalender.php">Leave Calender</a></li>
		<li><a href="ApplyVOE.php">Apply VOE</a></li>
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
				<h6 class="text-center" style="color:white; font-size:14px; font-family:Times New Roman, Georgia, Serif;"><?php echo $_SESSION['u_fullname']; ?></h6>
				
					 <center><span class="text-size-small" style="color:white;">
					 <?php 
						
						echo $_SESSION['u_emplocation'].", India";
					
					?>
					</span>
					</center>
		</div>
							
			
				<hr>
				<ul class="list-group">
					<li class="list-group-item active"><a href="#" style="color:white; font-size:18px;">My Account</a></li>
					<li class="list-group-item"><a href="lms.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;My Profile<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:50px;"></i></a></li>
					<li class="list-group-item"><a href="personalinfo.php"><i class="fa fa-user-secret" aria-hidden="true"></i>&nbsp;Personal Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:30px;"></i></a></li>
					<li class="list-group-item"><a href="officialinfo.php"><i class="fa fa-building" aria-hidden="true"></i>&nbsp;Official Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<li class="list-group-item"><a href="applyLeave.php"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;Apply Leave<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<?php
					if(strtoupper($_SESSION['user_dept'])=="HR") {?>
					<li class="list-group-item"><a href="hr.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;HR Section<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<?php }elseif(strtoupper($_SESSION['user_desgn'])=="MANAGER") {?>
					<li class="list-group-item"><a href="manager.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Manager Section<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:10px;"></i></a></li>
					<?php }?>
					<!--  <li class="list-group-item"><a href="leaveinfo.php"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;My Leave Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:20px;"></i></a></li>-->
				</ul>
			</div><!--2 column end-->
			<div class="col-sm-2"></div>
			<div class="col-sm-6">
				<div class="panel panel-primary">
  								<div class="panel-heading text-center"><strong style="font-size:20px;">Manager Section</strong></div>
  								<div class="panel-body table-responsive">
									<table class="table table-bordered table-hover">
										 <tr>
                                            <td><a id="applyteammemberleaveid" href="applyteammemberleave.php?getEmp=1" >Apply Leave For Team</a></td>
                                            <td>Manager can apply leaves for their team members.</td>
                                        </tr>
										<tr>
                                            <td><a id='addextrawfhmanager' href="wfhhours/manageraddwfhforemp.php?role=manager&approveview=1">Add Extra WFH Hour</a></td>
                                            <td>Manager can apply extra WFH hour for their team member.</td>
                                        </tr>
										<tr>
											<td><a id="managermodifyempapprovedleaves" href="modifyempapprovedleaves.php">Modify Employee Approved Leaves</a></td>
											<td>Manager can delete/edit/modify employee approved leaves.</td>
										</tr>
										<tr>
                                            <td><a id="viewextrawfhmanager" href="wfhhours/managerviewwfhform.php?viewform=1">View/Modify Extra WFH Hour</a></td>
                                            <td>Manager can view or modify extra WFH hour for their team member.</td>
                                        </tr>
										<tr>
                                            <td><a id="managerApproveEmpLeave" href="approveEmpleave.php?role=manager">Approve Employee Leaves</a></td>
                                            <td>Manager can approved employee pending leaves.</td>
                                        </tr>
                                         <tr>
                                            <td><a id="approveextrawfhmanager" href="wfhhours/approveEmpExtrawfhhour.php?viewapprovalform=1">Approve/Cancel Extra WFH Hour</a></td>
                                            <td>Manager can approve/delete extra WFH hour applied by their team member.</td>
                                        </tr>
                                        <tr>
                                            <td><a id="teamreport" accesskey="2" title="Team Leave Report" href="teamleavereport.php">Team Leave Report</a></td>
                                            <td>Manager can check their team leave report.</td>
                                        </tr>  
									</table>
									<!-- <table class="table table-bordered table-hover">
									for ($i = 0; $i < sizeof($keys); $i++) {
					switch ($keys[$i]) {
						case "applyleave" :
							if ($row['applyleave'] == 1) {
								echo '<li class="first"><a href="#" id="applyleaveid">Apply Leave</a></li>';
							}
							break;
						case "compoff" :
							if ($row['compoff'] == 1) {
								echo '<li><a href="#" id="compoffleaveid">Apply Comp Off Leave</a></li>';
							}
							break;
						case "ExtraWFHHour" :
							if (($row['ExtraWFHHour'] == 1)) {
								echo '<li><a href="#" id="extrawfhhrid">Apply Extra WFH Hour</a></li>';
							}
							break;
						case "selfleavestatus" :
							if ($row['selfleavestatus'] == 1) {
								echo '<li><a href="#" id="selfleavestatusid">Emp Leave Status</a></li>';
							}
							break;
						case "selfleavehistory" :
							if ($row['selfleavehistory'] == 1) {
								echo '<li><a href="#" id="selfleavehistoryid">Emp Leave History</a></li>';
							}
							break;
						case "editprofile" :
							if ($row['editprofile'] == 1) {
								echo '<li><a href="#" id="editprofileid">Edit Emp Profile</a></li>';
							}
							break;
						case "leaveapproval" :
							if ($row['leaveapproval'] == 1) {
								echo '<li><a href="#" id="leaveapprovalid">Leave Approval</a></li>';
							}
							break;
						case "managersection" :
							if (($row['managersection'] == 1) && ($_SESSION['user_dept'] != "HR")) {
								echo '<li><a href="#" id="managersectionid">Manager Section</a></li>';
							}
							break;
						case "hrsection" :
							if (($row['hrsection'] == 1) || ($_SESSION['user_dept'] == "HR")) {
								echo '<li><a href="#" id="hrsectionid">HR Section</a></li>';
							}
							break;
						case "applyteammemberleave" :
							if ($row['applyteammemberleave'] == 1) {
								echo '<li><a href="#" id="applyteammemberleaveid">Apply Leave for Team</a></li>';
							}
							break;
						case "optionalleave" :
							if ($row['optionalleave'] == 1) {
								echo '<li><a href="#" id="optionalLeaveStatus">Optional Holidays Applied</a></li>';
							}
							break;
							
						/*case "addWFHhr" :
							if ($row['addWFHhr'] == 1) {
								echo '<li><a href="#" id="addWFHhrStatus">Add WFH Hour</a></li>';
							}
							break;
						case "viewWFHhr" :
							if ($row['viewWFHhr'] == 1) {
								echo '<li><a href="#" id="viewWFHhrStatus">View WFH Hour</a></li>';
							}
								break;*/
						
							
							default:
							break;
					}
				} 
									</table> -->
  								</div>
							</div>
				</div>
				<div class="col-sm-4">
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

							� 2017, All rights reserved

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
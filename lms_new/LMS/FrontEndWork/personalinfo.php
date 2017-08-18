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
		<link rel="stylesheet" type="text/css" media="screen" href="css/frontend.css" />
		<script src="public/js/jquery/jquery-1.10.2.min.js"></script>
		<script src="public/js/countdown/countdown.js"></script>
			<script>
		$(document).ready(function() {
			$('body').bind('mousedown keydown', function(event) {
				$('#counter').countdown('option', {
					until : +1200
				});
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
				<h6 class="text-center" style="color:white; font-size:14px; font-family:Times New Roman, Georgia, Serif;"><?php echo $_SESSION['u_fullname']?></h6>
				
					 <center><span class="text-size-small" style="color:white;">
					 <?php 
						$fullname = $_SESSION['u_fullname'];
						$empid=$_SESSION['u_empid'];
						$empinfo=$db->query("select * from emp where empname='".$fullname."'");
						$emprow=$db->fetchAssoc($empinfo);
						$emplocation=$emprow['location'];
						echo $emplocation.", India";
						$managername=$emprow['managername'];
						$department=$emprow['dept'];
						$emailid=$emprow['emp_emailid'];
						$birthdaydate=$emprow['birthdaydate'];
						$empprofile=$db->query("select * from empprofile where empid='".$empid."'");
						$row=$db->fetchAssoc($empprofile);
						$phonenumber=$row['phonenumber'];
						$bloodgroup=$row['bloodgroup'];
						$address=$row['address'];
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
			<div class="col-sm-9" style="margin-left:40px;">
					<div class="panel panel-primary">
								
						<div class="panel-heading text-center">
				
							<strong style="font-size:20px;">Personal Info</strong>
					
						</div>
						<div class="panel-body">
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Emp Name:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="empname" value="<?php echo $fullname; ?>">
								</div>
								<div class="col-sm-2">
									<label>Emp Id:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $empid; ?>">
								</div>
							</div><!--1st row close-->
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Manager Name:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $managername; ?>">
								</div>
								<div class="col-sm-2">
									<label>Company Name:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="ECI">
								</div>
							</div><!--2nd row close-->
							</div>
							
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Department:</label>
								</div>
								<div class="col-sm-4">
									<select class="form-control" value="<?php echo $department; ?>">
										<option>--Department--</option>
										<option>V&V</option>
										<option>ST</option>
										<option>DevTest</option>
										<option>AIBI</option>
										<option>HR</option>
										<option>T3</option>
										<option>LightSoft</option>
										<option>STMS</option>
										<option>ST</option>
										<option>GSC Tier</option>
										<option>GTD-V&V</option>
										<option>IT</option>
									</select>
								</div>
								<div class="col-sm-2">
									<label>Sub Department:</label>
								</div>
								<div class="col-sm-4">
									<select class="form-control">
										<option>--Sub Department--</option>
										<option>V&V</option>
										<option>V&V Automation</option>
										<option>V&V STMS</option>
										<option>Optics V&V Manual</option>
										<option>Optics V&V Manual and Regression</option>
										<option>ST-Apollo Optical App</option>
										<option>ST-GMPLS Infra & Protocols</option>
										<option>ST-MSPP HW</option>
										<option>ST-TND</option>
										<option>HR</option>
										<option>Admin</option>
										<option>HR</option>
										<option>DevTest Data</option>
										<option>AIBI Infrastructure</option>
										<option>LightSoft</option>
										<option>STMS Dev</option>
										<option>STMS Dev1</option>
										<option>STMS Dev2</option>
										<option>ST</option>
										<option>GSC Tier2</option>
										<option>GSC Tier3</option>
										<option>GTD-V&V Access</option>
										<option>GTD-V&V Mng</option>
										<option>IT- System Admin</option>
									</select>
								</div>
							</div><!--3rd row close-->
							</div>
							
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Phone Number:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $phonenumber; ?>">
								</div>
								<div class="col-sm-2">
									<label>Email Id:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $emailid; ?>">
								</div>
							</div><!--4th row close-->
							</div>
							
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
								<label>Years of Exp:</label>
								</div>
								<div class="col-sm-4"><input type="text" class="form-control" value=""></div>
									<div class="col-sm-2">
									<label>Blood Group:</label>
								</div>
								<div class="col-sm-4">
									<select class="form-control" value="<?php echo $bloodgroup; ?>">
										<option>--Blood grp--</option>
										<option>A+</option>
										<option>O+</option>
										<option>B+</option>
										<option>AB+</option>
										<option>A-</option>
										<option>O-</option>
										<option>B-</option>
										<option>AB-</option>
									</select>
								</div>
							</div><!--5th row close-->
							</div>
							
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Date of Birth:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $birthdaydate; ?>">
								</div>
								<div class="col-sm-2">
									<label>Address:</label>
								</div>
								<div class="col-sm-4">
									<textarea class="form-control"><?php echo $address;?></textarea>
								</div>
							</div><!--6th row close-->
							</div>
						</div>
					</div>
				</div>	
					
				<div class="col-sm-1"></div>
			</div>
		</div>
		</div>
		<footer class="footer1">
		<div class="container">
			<div class="row"><!-- row -->
					
						<div class="col-lg-3 col-md-3"><!-- widgets column left -->
						<ul class="list-unstyled clear-margins"><!-- widgets -->
								
									<li class="widget-container widget_nav_menu"><!-- widgets list -->
										<h1 class="title-widget">Email Us</h1>
											
										<p><b>Email id:</b></p>
										<p><b>Anil Kumar Thatavarthi:</b> <a href="mailto:#"></a></p>
										<p><b>Naidile Basvagde :</b> <a href="mailto:#"></a></p>
										<p><b>Sneha Kumari:</b> <a href="mailto:#"></a></p>
										<!--
										<ul>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> About Us</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> Contact Us</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> Success Stories</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> 10th syllabus</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> 12th syllabus</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> Achiever's Batch</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Regular Batch</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Test & Discussion</a></li>
										</ul>
							-->
									</li>
									
								</ul>
								 
							  
						</div><!-- widgets column left end -->
						
						
						
						<div class="col-lg-3 col-md-3"><!-- widgets column left -->
					
						<ul class="list-unstyled clear-margins"><!-- widgets -->
								
									<li class="widget-container widget_nav_menu"><!-- widgets list -->
							
										<h1 class="title-widget">Contact Us</h1>
										<p><b>Helpline Numbers </b>

										<b style="color:#ffc106;">(8AM to 10PM):</b>  +91-9740464882, +91-9945732712  </p>
										
										
										<p><b>Phone Numbers : </b>7042827160, </p>
										<p> 011-2734562, 9745049768</p>
										<!--
										<ul>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Test Series Schedule</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  School Fee</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Postal Coaching</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Books according to class</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Education Details</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Study Centres</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> Extra Curriculum Activities</a></li>
											<li><a  href="#" target="_blank"><i class="fa fa-angle-double-right"></i> Results</a></li>
											
										</ul>-->
							
									</li>
									
								</ul>
								 
							  
						</div><!-- widgets column left end -->
						
						
						
						<div class="col-lg-3 col-md-3"><!-- widgets column left -->
					
						<ul class="list-unstyled clear-margins"><!-- widgets -->
								
									<li class="widget-container widget_nav_menu"><!-- widgets list -->
							
										<h1 class="title-widget">Office Address</h1>
										<p><b>Corp Office / Postal Address</b></p>
									   <!-- <ul>


											<li><a href="#"><i class="fa fa-angle-double-right"></i> Enquiry Form</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i> Online Test Series</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i> Subject Wise Test Series</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i> Smart Book</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i> Test Centres</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i>  Admission Form</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i>  Computer Live Test</a></li>

										</ul>-->
							
									</li>
									
								</ul>
								 
							  
						</div><!-- widgets column left end -->
						
						
						<div class="col-lg-3 col-md-3"><!-- widgets column center -->
						
						   
							
								<ul class="list-unstyled clear-margins"><!-- widgets -->
								
									<li class="widget-container widget_recent_news"><!-- widgets list -->
							
										<h1 class="title-widget">Follow Us On </h1>
										
										<div class="footerp"> 
										
										
										</div>
										
										<div class="social-icons">
										
											<ul class="nomargin">
											
						<a href="#"><i class="fa fa-facebook-square fa-3x social-fb" id="social"></i></a>
						<a href="#"><i class="fa fa-twitter-square fa-3x social-tw" id="social"></i></a>
						<a href="#"><i class="fa fa-google-plus-square fa-3x social-gp" id="social"></i></a>
						<a href="#"><i class="fa fa-envelope-square fa-3x social-em" id="social"></i></a>
											
											</ul>
										</div>
									</li>
								  </ul>
							   </div>
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
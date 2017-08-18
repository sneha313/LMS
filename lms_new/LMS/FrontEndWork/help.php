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
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.min.css">	
		<link rel="stylesheet" type="text/css" media="screen" href="css/frontend.css" />
		<script src="public/js/jquery/jquery-1.10.2.min.js"></script>
		<script src="public/js/countdown/countdown.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script type="text/javascript" src="public/js/bootstrap/js/bootstrap.min.js"></script>

		<script>
		$(document).ready(function() {
			$('body').bind('mousedown keydown', function(event) {
				$('#counter').countdown('option', {
					until : +1200
				});
			});
		});
		</script>
		<style>
			.faqHeader {
				font-size: 27px;
				margin: 20px;
				font-family: monospace;
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
				<div class="col-sm-1"></div>
				<div class="col-sm-8">
					<div class="panel-group" id="accordion">
						<div class="faqHeader">LMS FAQ</div>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">How to login into ECI Leave management system?</a>
								</h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapse in">
								<div class="panel-body"> 
									<strong>The steps involved in login into ECI Leave Management System:</strong>
									<ul>
										<li>One can login into this web app using<strong> windows credentials</strong>.</li>
										<li><strong>ECI_DOMAIN </strong>is not needed in the username field.</li>
										<li><strong>LMS link will work</strong> only when you are in the ECI Domin.</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTen">What an employee can do with LMS?</a>
								</h4>
							</div>
							<div id="collapseTen" class="panel-collapse collapse">
							<div class="panel-body">
								<strong>An employee can:</strong>
								<ul>
									<li>Apply Leave.</li>
									<li>View ECI Holidays.</li>
									<li>View his access details.</li>
									<li>View his team member's PTO.</li>
									<li>View his leave history.</li>
									<li>Modify his Pending leaves before approval.</li>
								</ul>
							</div>
							</div>
						</div>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseEleven">What type of leaves an employee can apply in LMS?</a>
								</h4>
							</div>
							<div id="collapseEleven" class="panel-collapse collapse">
							<div class="panel-body">
								<ol>	
									<li><Strong>Regular Leave:</Strong><br>
										<ul>
											<li>Full Day PTO</li>
											<li>Half Day PTO (First Half or Second Half)</li>
											<li>WFH (First Half or Second Half or FullDay)</li>
											<li>First Half - Half Day PTO and second Half - WFH</li>
											<li>First Half - WFH and Second Half - Half Day PTO</li> 	
										</ul>
									</li>
									<br>
									<li><strong>Special Leave:</strong><br>
										<p>An employee can select special leave as mentioned below:</p>
										<ul>
											<li>The corresponding PTO's for each leave type will not be deducted from the total 
											leave balance of the employee.</li>
											<li>These are the extra leaves provided by the company.</li>
											<li>Once employee uses the special leave and got approval from manager, he can't use 
											the same special leave another time.</li>
											<li>Once the leave is approved, that option will 
											be removed from the selection box.</li>	
										</ul><br>
										<table class="table table-bordered " style="width:70%;">
											<tr>
												<th>Leave Type</th>
												<th>PTO Days Permitted</th>
											</tr>
											<tr>
												<td>Wedding</td>
												<td class="align">4</td>
											</tr>
												<tr>
												<td>Paternity Leave</td>
												<td class="align">2</td>
											</tr>
												<tr>
												<td>Death of spouse or life companion</td>
												<td class="align">4</td>
											</tr>
												<tr>
												<td>Death of immediate family member</td>
												<td class="align">3</td>
											</tr>
											
										</table>
									</li>
								</ol>
							</div>
							</div>
						</div>

						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">How to apply a Regular PTO?</a>
								</h4>
							</div>
							<div id="collapseTwo" class="panel-collapse collapse">
								<div class="panel-body">
									<ol>
										<li>	
											Click on <b>Apply leave</b> in home page. A page appears on <b>right side</b>, where one can select the <b>date and specify reason</b>. Then click on next button.
										</li><br>
										<li>
											In the next page, one can select First Half or second half based on the <b>PTO selected</b> in 
												the previous page. Then click on <b>"Apply and Send Mail"</b> button to apply the selected PTO's 
												and the same will be <b>mailed</b> to the manager and the employee.
										</li>
									</ol>
								</div>
							</div>
						</div>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">How to apply a Special Leave?</a>
								</h4>
							</div>
							<div id="collapseThree" class="panel-collapse collapse">
								<div class="panel-body">
									<ol>
										<li>Click on <b>Apply leave</b> on left plane, and select <b>"special Leave"</b> from right plane. Then 
											select special leave type and click next.
											<img src="HelpImages/splleave1.jpg" alt="ECI Login" height="70%" width="90%">
										</li>
										<li>In next page, the <b>special leave </b>will be populated as shown below:
											<br><img src="HelpImages/splleave2.jpg" alt="ECI Login" height="70%" width="90%">
										</li>
										<li>
											Here <b>"Paternity leave"</b> is selected. So, first <b>two dates</b> are populated with Paternity Leave 
											(as specified in the table above) and the remaining days, we can select <b>any regular leave type </b>
											and click "next" button.
										</li>
									</ol>
								</div>
							</div>
						</div>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">How to see employee access details?</a>
								</h4>
							</div>
							<div id="collapseFive" class="panel-collapse collapse">
								<div class="panel-body">
								   <br><img src="HelpImages/accessdetails1.jpg" alt="ECI Login" height="70%" width="90%"><br>
									One can select the above options and view his access details for the date range selected.

								</div>
							</div>
						</div>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">How to see team member's PTO?</a>
								</h4>
							</div>
							<div id="collapseSix" class="panel-collapse collapse">
								<div class="panel-body">
									One can see team member's PTO by <b>clicking on leave calendar </b>option in the top plane.
								</div>
							</div>
						</div>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseEight">How to see carry forwarded leaves for previous year and balance leaves for present year?</a>
								</h4>
							</div>
							<div id="collapseEight" class="panel-collapse collapse">
							<div class="panel-body">
								One can see his <b>balance leaves</b> and <b>carry forwarded leaves</b> information by clicking on <b>"Balance Leaves"</b>
								option in top right corner.<br>
							</div>
							</div>
						</div>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseNine">Manager Section?</a>
								</h4>
							</div>
							<div id="collapseNine" class="panel-collapse collapse">
								<div class="panel-body">
									<b> A manager can perform the following additional tasks along with the tasks specified above, when he enters into LMS.</b><br>
									<ol>
										<li>View his team member's leave history (By clicking on "Team Leave Report" on top plane).</li>
										<li>View his team member's access details along with his/her access details (in attendance section)</li>
										<li>Approve/reject his team member's PTO request (By clicking on "Leave Approval" in left plane)</li>
										<li>Can modify his/her team member's "Approved PTO's" (By clicking on "Manager Section" in left plane).</li>
										<li>Can apply leave for his team member's on behalf of his team member.(By clicking on "Apply Leave For Team" in left plane)</li>
										<li>Can view his peers PTO's along with his team member's PTO's (By clicking on "Leave Calendar" in top plane)</li>
									</ol>	
								</div>
							</div>
						</div>

						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">HR Section?</a>
								</h4>
							</div>
							<div id="collapseFour" class="panel-collapse collapse">
							<div class="panel-body">
								<b>HR will have access to the following tasks in HR section in left plane.</b><br>
								<ol>
									<li>HR can add/modify/delete/view employee details.</li>
									<li>Apply leave for any employee in the company.</li>
									<li>Modify "Approved Leaves" for any employee in the company.</li>
									<li>Can take print out of PTO report for the whole team.</li>
								</ol>	
							</div>
							</div>
						</div>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSeven">VOE Form?</a>
								</h4>
							</div>
							<div id="collapseSeven" class="panel-collapse collapse">
							<div class="panel-body">
								<b>Procedure for applying VOE:</b><br>
								<ol>
									<li>An employee can check their claim period for previous months by changing the claim period.</li>
									<li>In VOE, claim period field will show only current month and previous two month </li>
									<li>Add Day button will work only for previous month.</li>
									<br><img src="HelpImages/voe.png" alt="ECI Login" height="90%" width="90%">
									<li>Days which are added by user will show with different background colour</li>
									<li>An employee can add a day only when he forget's his id card or when access machine is not functional</li>
									<li>Day which you want add using ADD DAY button should not be  a leave or WFH</li>
									<li>After submission of VOE form we cannot edit details but if we can delete and again we can submit the details.</li>
									<li>After submission only you will get the print,delete options.</li>
									<br><img src="HelpImages/voe1.png" alt="ECI Login" height="90%" width="90%">
								</ol>	
							</div>
							</div>
						</div>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse8">Comp Off Leave?</a>
								</h4>
							</div>
							<div id="collapse8" class="panel-collapse collapse">
							<div class="panel-body">
								<b>Employee can apply for Comp Off leave:</b><br>
								<br><img src="HelpImages/compoff.png" alt="ECI Login" height="60%" width="90%">
								<ol>
									<li>An employee need to mention the worked holiday date to take the compoff leave .</li>
									<li>By clicking on "Add Comp Off leaves" button we can apply more compoff leaves</li>
									<br><img src="HelpImages/compoff1.png" alt="ECI Login" height="60%" width="90%">
									<li>An employee can not modify the applied compoff leave but he can delete it. </li>	
									<br><img src="HelpImages/compoff3.png" alt="ECI Login" height="60%" width="90%">
								</ol>	
							</div>
							</div>
						</div>
			
					</div>
				</div>
				<div class="col-sm-1"></div>
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
<?php
	if (!isset($_SESSION)) {
		session_start();
	}
	if (empty($_SESSION['user_name']))
		header("Location:login.php");
	require_once ("Library.php");
	if(browser_detection("browser")=="msie") {
		echo '<!DOCTYPE html>';
	}
?>
<html>
	<head>
		<title>ECI Leave Management System..</title>
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="public/js/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel='stylesheet' type='text/css' href='public/js/DataTables/media/css/jquery.dataTables.min.css'>
		<link rel='stylesheet' type='text/css' media='screen' href='public/js/jqgrid/jqgridcss/ui.jqgrid.css' />
		<link rel='stylesheet' type='text/css' media='screen' href='public/js/bootstrap3-dialog/bootstrap-dialog.css' />
		<link rel='stylesheet' href='public/js/jqueryui/css/redmond/jquery-ui.css'>
		<link rel="stylesheet" type="text/css" media="screen" href="public/css/frontend.css" />

	</head>
	<body>
		<?php
			$name = $_SESSION['u_fullname'];
			$firstname = strtok($name, ' ');
			$lastname = strstr($name, ' ');
		?>
		<!--navbar inverse start-->
		<nav class="navbar navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<div id="img">
						<img id="image" class="img-responsive" src="img/3.jpg">
					</div>
				</div>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#" id="welcome"><b>  Welcome, <?php echo $firstname; ?></b></a></li>
					<li><a id="help" href="#"><i class="fa fa-question-circle" aria-hidden="true"></i><b> Need Help</b></a></li>
					<li><a id="login" href="login.php"><i class="fa fa-sign-out" aria-hidden="true"></i><b> Logout</b></a></li>
				</ul>
			</div>
		</nav><!--navbar inverse close-->
		<!--navbar default start-->
		<nav class="navbar navbar-default navbar-static-top">
			<div class="container"><!--container div start-->
				<div class="navbar-header"><!--navbar header start-->
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button><!--button close-->
					<a class="navbar-brand" href="#">Leave Management System</a>
					<label style="margin-left:60px; margin-right:5px;margin-top:14px; font-size:16px;">Session Expires in :-</label>
				</div><!--navbar header close-->
				<h4 id="counter" class="countdown"></h4>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right" style="padding-right:10px;">
						<li id="home"><a id="holidays" href="#">Holiday List</a></li>
						<li><a id="attendance" href="#">Attendance</a></li>
						<li><a id="trackattendance" href="#">Track Leaves</a></li>
						<li><a id="calender" href="#">Leave Calender</a></li>
						<li><a id="voe" href="#">Apply VOE</a></li>
					</ul>
				</div>
			</div><!--container div close-->
		</nav><!--navbar default close-->
		
		<!--container fluid div start-->
		<div class="container-fluid well">
			<!--row start-->
			<div class="row">
				<!--2 column start-->
				<div class="col-sm-2">
					<div class="rectangle"><!--rectangle div for employee profile picture and location start-->
						<a href="#"><img src="img/4.jpg" class="img-circle img-responsive" alt="" width="150px;" height="80px;"></a>
						<h4><?php echo $_SESSION['u_fullname']; ?></h4>
						<span class="text-size-small">
						 <?php 
							echo $_SESSION['u_emplocation'].", India";
						?>
						</span>
					</div><!--rectangle div for employee profile picture and location close-->
					<hr>
					<ul class="list-group">
						<li class="list-group-item active"><a href="#" style="color:white; font-size:18px;">My Account</a></li>
						<li class="list-group-item"><a id="myprofile" href="#"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;My Profile<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:50px;"></i></a></li>
						<li class="list-group-item"><a id="personalinfo"  href="#"><i class="fa fa-user-secret" aria-hidden="true"></i>&nbsp;Personal Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:30px;"></i></a></li>
						<li class="list-group-item"><a id="officialinfo" href="#"><i class="fa fa-building" aria-hidden="true"></i>&nbsp;Official Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
						<li class="list-group-item"><a id="applyleaveid" href="#"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;Apply Leave<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
						<?php
						
						if(strtoupper($_SESSION['user_dept'])=="HR") {?>
						<li class="list-group-item"><a id="hrsection" href="#"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;HR Section<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
						<li class="list-group-item"><a id="teamLeavereport" href="#"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Team Leave Report<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:3px;"></i></a></li>
						<?php }elseif(strtoupper($_SESSION['user_desgn'])=="MANAGER") {?>
						<li class="list-group-item"><a id="managersection" href="#"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Manager Section<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:10px;"></i></a></li>
						<li class="list-group-item"><a id="teamLeavereport" href="#"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Team Leave Report<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:3px;"></i></a></li>
						<?php }?>
					</ul>
				</div>	<!--2 column end-->
				<!--10 column start-->
				<div class="col-sm-10">
					<div id="loadpendingstatus"></div>
					<div id="loadempapplyleave"></div>
					<div id="loadempleavestatus"></div>
					<div id="loadempleavehistory"></div>
					<div id="loadempeditprofile"></div>
					<div id="loadholidays"></div>
					<div id="loadteamleaveapproval"></div>
					<div id="loadempleavereport"></div>
					<div id="loadteamleavereport"></div>
					<div id="loadapplyteammemberleave"></div>
					<div id="loadhrsection"></div>
					<div id="loadmanagersection"></div>
					<div id="loadattendance"></div>
					<div id="loadtrackattendance"></div>
					<div id="loadcalender"></div>
					<div id="loadhelp"></div>
					<div id="loadoptionalleave"></div>
					<div id="loadvoeform"></div>
					<div id="loadcompoffleave"></div>
					<div id="loadwfhhr"></div>
					<div id="loadextrawfhhr"></div>
					<div id="loadmyprofile"></div>
					<div id="loadpersonalinfo"></div>
					<div id="loadofficialinfo"></div>
				</div><!--10 column end-->
			</div><!--row end-->
		</div><!--container fluid div end-->
		
		<!--footer 1st section start-->
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
			</div><!--footer container div close-->
		</footer>
		<!--footer 1st section close-->

		<!--footer-bottom start-->
		<div class="footer-bottom">
			<!--footer container div start-->
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
			</div><!--footer container div close-->
		</div><!--footer bottom section close-->
		<script type='text/javascript' src="public/js/jquery/jquery.js" type="text/javascript"></script>
		<script type='text/javascript' src="public/js/jquery/jquery-1.10.2.min.js"></script>
		<script type='text/javascript' src="public/js/countdown/countdown.js"></script>
		<script type='text/javascript' src='public/js/bootstrap/js/bootstrap.min.js'></script>
		<script type="text/javascript" src="public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
		<script type='text/javascript' src='public/js/DataTables/media/js/jquery.dataTables.min.js'></script>
		<script type='text/javascript' src='public/js/jqueryui/js/jquery-ui.js'></script>
		<script type='text/javascript' src='public/js/jqgrid/grid.locale-en.js'></script>
		<script type='text/javascript' src='public/js/bootstrap3-dialog/bootstrap-dialog.js'></script>
		<script type='text/javascript' src='public/js/jqgrid/jquery.jqGrid.min.js'></script>
		<script type='text/javascript' src='public/js/jquery/jquery.validate.min.js'></script>
		<script type="text/javascript" src="projectjs/index.js"></script>
		<script type='text/javascript' src="projectjs/fullcalendar.js"></script>
		<script>
			$(document).ready(function() {
				$('body').bind('mousedown keydown', function(event) {
					$('#counter').countdown('option', {
						until : +1200
					});
				});
			});
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
					window.location = "index.php";
				} else {
					alert("Your session is expired. Logging out");
					window.location = "login.php";
				}
			}
		</script>
	</body>
</html>
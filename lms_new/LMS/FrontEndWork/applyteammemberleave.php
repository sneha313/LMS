<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
require_once 'generalFunctions.php';
?>
<html>
	<head>
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src="public/js/jquery/jquery-1.10.2.min.js"></script>
		<script src="public/js1/jqueryui/js/jquery-ui.js"></script>
		<script type="text/javascript" src="projectjs/index.js"></script>
		<script type="text/javascript">
			function hidealldiv(div) {
				var myCars = new Array("loadempapplyleave", "loadempleavestatus", "loadempleavehistory", "loadempleavereport", "loadempeditprofile", "loadholidays", "loadempleavereport", "loadteamleavereport", "loadteamleaveapproval", "loadattendance", "loadcalender", "loadpendingstatus", "loadhrsection", "loadmanagersection", "loadapplyteammemberleave", "loadtrackattendance", "loadextrawfhhr");
				var hidedivarr = removeByValue(myCars, div);
				hidediv(hidedivarr);
				showdiv(div);
			}

			function hidediv(arr) {
				$("#footer").show();
				for (var i = 0; i < arr.length; i++) {
					$("#" + arr[i]).hide();
					$("#" + arr[i]).html("");
				}
			}

			function showdiv(div) {
				$("#" + div).show();
			}

	
		</script>
		<script>
			<?php 
				if(isset($_REQUEST['leaveform']))
				{
					$empId=getValueFromQuery("select empid from emp where empname='".$_REQUEST['empuser']."' and state='Active'","empid");
					getApplyLeaveJs("teamApplyLeave","teamfromDate","teamtoDate","loadapplyteammemberleave","employeeid","applyteammemberleave.php",$empId);
					getSetOptionsJs("hideteammemsplLeave","teamleaveForm","teamsetOptions");
				}
				if(isset($_REQUEST['getdates']))
				{
					getDisplayDatesJs("loadapplyteammemberleave");
				}
				if (isset($_REQUEST['getShift']))
				{
					getSubmitJs("getShift","loadapplyteammemberleave");
				}
			?>
			
		</script>
		<?php
			$getCalIds = array("teamfromDate", "teamtoDate");
			$calImg=getCalImg($getCalIds);
			echo $calImg;
		?>
		<style>
			.footer1 {
				background: #031432 repeat scroll left top;
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
			<div class="col-sm-7" id="applyleavebody">
				<div id="colTwo">
			<div class="box">
			<div align="center" id="exceededLeaves" style="display: none;">
					<h2><u><font color="red">Exceeded permitted Leaves. Can't apply Leave now.</font></u></h2>
			</div>
				<div id="loadapplyteammemberleave"></div>
			</div>
			<div class="box">
			<?php // require_once 'pendingstatus.php'; ?>
				</div>
				</div>
					<div id="wrapper">
		<div id="form-div">
	<?php
	if(isset($_REQUEST['getEmp']))
	{
		getEmpForm("applyteammemberleave.php",$_SESSION['u_empid'],$_SESSION['user_desgn']);
	}
	if(isset($_REQUEST['leaveform']))
	{	
		if($_REQUEST['empuser']) {
			$childern=getChildren($_SESSION['u_empid']);
			$empId=getValueFromQuery("select empid from emp where empname='".$_REQUEST['empuser']."' and state='Active'","empid");
			if(in_array($empId,$childern)) {
				getLeaveForm("applyteammemberleave.php","teamleaveForm","teamApplyLeave","team_leave_type","hideteammemsplLeave","team_special_leave","teamfromDate","teamtoDate","teamsetOptions");
			} else {
				echo "<script>alert(\"You dont have permissions to apply leave for this employee '".$_REQUEST['empuser']."'\");
						$('#loadapplyteammemberleave').load('applyteammemberleave.php?getEmp=1');
						</script>";
			}
		}
	}
	if(isset($_REQUEST['getdates']))
	{
		getDatesSection("teamfromDate","teamtoDate",$_REQUEST['employeeid'],$_REQUEST['employeeid'],"applyteammemberleave.php");
	}
	if (isset($_REQUEST['getShift']))
	{
		getShiftSection();	
	}
	if (isset($_REQUEST['confirmleave']))
	{
		$empid=$_SESSION['teammem'];
		getConfirmLeaveSection("applyteammemberleave.php",$empid,"fromDate","toDate");
	}
	?>
		</div>
	</div>
		</div>
		<div class="col-sm-2"></div>
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
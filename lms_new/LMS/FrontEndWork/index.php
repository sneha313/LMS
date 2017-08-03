<?php
if (!isset($_SESSION)) {
	session_start();
}
if (empty($_SESSION['user_name']))
	header("Location:userlogin.php");
require_once ("Library.php");
if(browser_detection("browser")=="msie") {
    echo '<!DOCTYPE html>';
}
?>
<html>
	<head>
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<meta http-equiv="X-UA-Compatible" content="IE=9">
		<title>ECI Leave Management System</title>
		
		<?php
		includeJQGrid();
		?>
		<script type="text/javascript" src="projectjs/index.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('body').bind('mousedown keydown', function(event) {
					$('#counter').countdown('option', {
						until : +1200
					});
				});
			});
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

			function removeByIndex(arr, index) {
				arr.splice(index, 1);
			}

			function removeByValue(arr, val) {
				for (var i = 0; i < arr.length; i++) {
					if (arr[i] == val) {
						arr.splice(i, 1);
						break;
					}
				}
				return arr;
			}
			$("#managermodifyempapprovedleaves").click(function() {
				hidealldiv('loadmanagersection');
				$("#loadmanagersection").load('modifyempapprovedleaves.php?role=manager');
			});
			$("#managerApproveEmpLeave").click(function() {
			     hidealldiv('loadmanagersection');
           		     $("#loadmanagersection").load('approveEmpLeave.php?role=manager');
        		});
		</script>
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
		<div id="container">
			<?php 
				$db = connectToDB();	
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
				<h6 class="text-center" style="color:white; font-size:14px; font-family:Times New Roman, Georgia, Serif;"><?php echo $_SESSION['u_fullname']?></h6>
				
					 <center><span class="text-size-small" style="color:white;">
					 <?php 
						$fullname = $_SESSION['u_fullname'];
						$empid=$_SESSION['u_empid'];
						$empinfo=$db->query("select * from emp where empname='".$fullname."'and state='Active'");
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
					
				<?php
				$query = "select * from privileges where role='" . $_SESSION['user_desgn'] . "'";
				$result = $db -> query($query);
				$row = $db -> fetchAssoc($result);
				$keys = array_keys($row);
				//Don't allow user to apply leave when his total leaves crosses -5.
				if ((getTotalLeaves($_SESSION['u_empid'])) < -5) {
					$row['applyleave'] = 0;
					echo '<script>
			    	$("document").ready(function() {
			    	$("#exceededLeaves").show();
			    	});
			    	</script>';
				}
				for ($i = 0; $i < sizeof($keys); $i++) {
					switch ($keys[$i]) {
						case "applyleave" :
							if ($row['applyleave'] == 1) {
								echo '<li class="list-group-item first"><a href="lms.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp; Profile<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:50px;"></i></a></li>';
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
				?>
				</ul>
			</div>
<div class="box mystyle">
	<hr>
		<h4>Your session will expire in </h4>
		<p align="center"><span id="counter" class="countdown"></span></p>
		<pre>
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
						window.location = "index.php";
					} else {
						alert("Your session is expired. Logging out");
						window.location = "logout.php?logout=1";
					}
				}
			</script>
		</pre>
		</div>
		</div>
		<div id="colTwo">
			<div class="box">
			<div align="center" id="exceededLeaves" style="display: none;">
					<h2><u><font color="red">Exceeded permitted Leaves. Can't apply Leave now.</font></u></h2>
			</div>
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
			</div>
			<div class="box">
			<?php // require_once 'pendingstatus.php'; ?>
				</div>
				</div>
			</div>
			<div id='loadingmessage' style='display:none'>
				<img align="middle" src='images/loading.gif'/>
			</div>

		</div>
		<?php
		if (isset($_REQUEST['extrahour'])) {
			echo "<u>Apply Extra WFH for Employee</u>";
			echo "<ul>";
			echo "<li><a href='#' id='addWFHhrStatus'>Add WFH Hour</a></li></br>";
			echo "<li><a href='#' id='viewWFHhrStatus'>View WFH Hour</a></li><br>";
			echo "</ul>";
		}
		?>
		<div id="footer">
			<p>
				<font size="3"><b>ECI TELECOM INDIA PVT. LTD</b> </font>
			</p>
		</div>
		
	</body>
</html>


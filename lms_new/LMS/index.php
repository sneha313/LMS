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
		<meta http-equiv="X-UA-Compatible" content="IE=9">
		<title>ECI Leave Management System</title>
		<link href="css/default.css" rel="stylesheet" type="text/css" />
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
			html {
				font: 90% "Trebuchet MS", sans-serif;
				margin: 20px;
				padding: 0;
				height: 85%;
			}
			body {
				height: 100%;
			}
			#container {
				min-height: 100%;
				height: auto !important;
				position: relative;
			}
			.demoHeaders {
				margin-top: 2em;
			}
			#dialog-link {
				padding: .4em 1em .4em 20px;
				text-decoration: none;
				position: relative;
			}
			#dialog-link span.ui-icon {
				margin: 0 5px 0 0;
				position: absolute;
				left: .2em;
				top: 50%;
				margin-top: -8px;
			}
			#icons {
				margin: 0;
				padding: 0;
			}
			#icons li {
				margin: 2px;
				position: relative;
				padding: 4px 0;
				cursor: pointer;
				float: left;
				list-style: none;
			}
			#icons span.ui-icon {
				float: left;
				margin: 0 4px;
			}
			.fakewindowcontain .ui-widget-overlay {
				position: absolute;
			}
		</style>

	</head>
	<body>
		<div id="container">
			<?php $db = connectToDB(); ?>
			<div id="header">
				<ul id="menu">
					<li>
						<a  id="HomeButton" accesskey="1" title="Home">Home</a>
					</li>
					<?php
					if (strtoupper($_SESSION['user_desgn']) == "MANAGER" || strtoupper($_SESSION['user_dept'])=="HR") {
						echo '<li><a accesskey="2" title="Team Leave Report" id="teamreport">Team Leave Report</a></li>';
					}
					?>
					<li><a href="#" accesskey="4" title="Holiday List" 	id="holidays">Holiday List</a></li>
					<?php 
					$query = "select location from emp where empusername='".$_SESSION['user_name']."' and state='Active'";
					$result = $db -> query($query);
					$row = $db -> fetchAssoc($result);
					/*if(strtoupper($row['location'])=="MUM") {
						echo '';
					}*/
					?>
					<li><a href="#" id="attendance">Attendance</a></li>
					<li><a href="#" id="trackattendance">Track Leaves</a></li>
					<li><a href="#" id="calender">Leave Calender</a></li>
					<li><a href="#" id="voe">APPLY VOE</a></li>
					<li><a href="#" id="help">HELP</a></li>
					<li><a href="logout.php?logout=1" accesskey="5" title="<?php echo $_SESSION['user_name']; ?> logged in">LOGOUT</a></li>
					<li><a href="#" accesskey="7" title="REMAINING LEAVES" id="balanceleavesid"></a></li>
					<li><a href="#" accesskey="6" title="Click to view detailed Leaves" id="detailleaves">BALANCE LEAVES:</a></li>
				</ul>
				<div id="balanceDialog" title="Balance Leaves"></div>
			</div>
			<div id="content">
				<div id="colOne">
				<?php
				$query = "select empname from emp where empusername='".$_SESSION['user_name']."' and state='Active'";
				$result = $db -> query($query);
				$row = $db -> fetchAssoc($result);
				echo "<h3 id='eciempfullname'>" . $row['empname'] . "</h3><hr>";
				?>
				<div class="box mystyle">
				<h3>LINKS</h3>
				<ul>
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


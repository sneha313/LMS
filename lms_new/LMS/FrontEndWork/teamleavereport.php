<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
?>
<html>
<head>
<?php 
$getCalIds = array("fromdate", "todate");
$calImg = getCalImg($getCalIds,-1,0);
echo $calImg;
?>
	<link rel="stylesheet" type="text/css" media="screen" href="css/teamleavereport.css" />
	<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="css/frontend.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="css/teamapproval.css">
	<link href="js/jqueryui/css/redmond/jquery-ui.css" rel="stylesheet">
	<script src="js/jquery/jquery.js" type="text/javascript"></script>
	<script src="js/jqueryui/js/jquery-ui.js"></script>
	<script src="js/jqgrid/grid.locale-en.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/jquery/jquery.validate.min.js"></script>
	<script src="js/jqgrid/jquery.jqGrid.min.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.searchFilter.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/jqgridcss/ui.jqgrid.css" />
	<script src="js/countdown/countdown.js" type="text/javascript"></script>
	<script src="projectjs/fullcalendar.js"></script>		
	<script type="text/javascript" src="projectjs/index.js"></script>
	<script type="text/javascript">
		$("document").ready(function() {

		$('#teamleavereportId').submit(function() {
		if($("#empid").val()=="Choose") {
			alert("Please select an employee");
			return false;
		} else {
			$.ajax({
				data : $(this).serialize(),
				type : $(this).attr('method'),
				url : $(this).attr('action'),
				success : function(response) {
					$("#loadteamleavereport").html(response);
				}
			});
			return false;
		}
	});
});

$("document").ready(function(){
    $(".table-1 tr:odd").addClass("odd");
    $(".table-1 tr:not(.odd)").hide();
    $(".table-1 tr:first-child").show();
    $(".table-1 tr.odd").click(function(){
        $(this).next("tr").toggle();
        $(this).find(".arrow").toggleClass("up");
    });
  });

</script>
<style type="text/css">
#teambalance {
	color: black;
	left: 1000px;
	float:right;
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
			<div class="col-sm-2"></div>
			<div class="col-sm-6">
				
				<?php
				function empHistory($empid,$query){
					global $db;
					global $_REQUEST;
					$leaveTypeCount=0;
					$allCount=0;
					if($_REQUEST['leaveType']=="ALL")
					{
						echo "<table class='table table-hover'>
								<tr class='info'>
									<th width='20%'>Start Date</th>
									<th width='20%'>End Date</th>
									<th>PTO's Taken</th>
									<th width='40%'>Reason</th>
									<th width='40%'>Status</th>
									<th width='40%'>Comments</th>
									<th></th>
								</tr><tr></tr>";
					} else {
						echo "<table class='table table-hover'>
								<tr class='info'>
									<th width='20%'>Date</th>
									<th width='40%'>LeaveType</th>
									<th width='40%'>Shift</th>
								</tr><tr></tr>";
					}
					$sql=$db->query($query);
					$splLeave = "";
					
					for($i=0;$i<$db->countRows($sql);$i++)
					{
						$row=$db->fetchArray($sql);
						if($_REQUEST['leaveType']=="ALL") 
						{
							$allCount=$allCount+$row['count'];
							echo '<tr></tr><tr>';
							echo '<td>'.$row['startdate'].'</td>';
							echo '<td>'.$row['enddate'].'</td>';
							echo '<td>'.$row['count'].'</td>';
							echo '<td>'.$row['reason'].'</td>';
							echo '<td>'.$row['approvalstatus'].'</td>';
							echo '<td>'.$row['approvalcomments'].'</td>';
							echo '<td><div class="arrow"></div></td></tr>';
						}
						$tid=$row['transactionid'];
						$sql1=$db->query("select * from perdaytransactions where transactionid='".$tid."'");
						if($_REQUEST['leaveType']=="ALL")
						{
									echo '<tr>
										<td colspan="6">
										<table>
											<tr>
											<th>Date</th>
											<th>Leave Type</th>
											<th>Shift</th>
											</tr>';
						}
						while($row1=$db->fetchArray($sql1))
						{
							if($_REQUEST['leaveType']=="ALL") 
							{
								$leavetype = $row1['leavetype'];
								$Day = $row1['date'];
								echo '<tr></tr><tr><td>'.$row1['date'].'</td>';
								echo '<td>'.$row1['leavetype'].'</td>';
								echo '<td>'.$row1['shift'].'</td>';
								echo '</tr>';
							} else {
								if($_REQUEST['leaveType']==$row1['leavetype'])
								{
									$leaveTypeCount=$leaveTypeCount+1;
									$leavetype = $row1['leavetype'];
									$Day = $row1['date'];
									echo '<tr></tr><tr><td>'.$row1['date'].'</td>';
									echo '<td>'.$row1['leavetype'].'</td>';
									echo '<td>'.$row1['shift'].'</td>';
									echo '</tr>';
								}	
							}
						}
						if($_REQUEST['leaveType']=="ALL")
						{
							echo '</table>';
							echo '</td></tr>';
						} 
					}
					if($_REQUEST['leaveType']!="ALL")
					{
						echo "<tr></tr><tr><td colspan=3 align='right'><b>Total Count = ".$leaveTypeCount."</b></td></tr>";
					}
					if($_REQUEST['leaveType']=="ALL")
					{
						echo '<tr></tr><tr><td colspan=7><b style="float:right">Total Approved leaves = '.$allCount.'</b></td></tr>';
					}
					echo "</table>";
				
				}
				
				if(isset($_REQUEST['empid']) && isset($_REQUEST['leaveType']) )
				{
					getEmpSelectionBox($_SESSION['u_empid'],$_REQUEST['empid']);
					echo "<br><br>";
					if($_REQUEST['empid']!="All")
					{
						echo "<table class='table table-hover'>
					    <tbody>";
						$result1=$db->query("SELECT empname FROM `emp` WHERE empid=".$_REQUEST['empid']);
						$row1=$db->fetchAssoc($result1);
						$result3=$db->query("SELECT balanceleaves,carryforwarded FROM `emptotalleaves` WHERE empid=".$_REQUEST['empid']);
						$row3=$db->fetchAssoc($result3);
						echo "<tr><th>".$row1['empname']."(".$_REQUEST['empid'].")
						<a id='teambalance'>Balance Leaves: ".($row3['balanceleaves']+$row3['carryforwarded'])."</a></th></tr></tbody></table>";
						$query="SELECT * FROM empleavetransactions where empid=".$_REQUEST['empid']." and startdate between '".$_REQUEST['fromdate']."' and '".$_REQUEST['todate']."' and 
											approvalstatus!='Pending' and approvalstatus!='Deleted' and approvalstatus!='Cancelled' order by startdate";
						empHistory($_REQUEST['empid'],$query);
					} else {
						$emplist=getemp($_SESSION['u_empid']);
						foreach ($emplist as $empid)
						{
							$result1=$db->query("SELECT empname FROM `emp` WHERE empid=".$empid);
							$row1=$db->fetchAssoc($result1);
							$result3=$db->query("SELECT balanceleaves,carryforwarded FROM `emptotalleaves` WHERE empid=".$empid);
							$row3=$db->fetchAssoc($result3);
							echo "<table id='table-2'>
					 		<tbody>";
							echo "<tr><th>".$row1['empname']."(".$empid.")
						    <a id='teambalance'>Balance Leaves: ".($row3['balanceleaves']+$row3['carryforwarded'])."</a></th></tr></tbody></table>";
							$query="SELECT * FROM empleavetransactions where empid=".$empid." and startdate between '".$_REQUEST['fromdate']."' and '".$_REQUEST['todate']."' and
											approvalstatus!='Pending' and approvalstatus!='Deleted' and approvalstatus!='Cancelled' order by startdate";
							empHistory($empid,$query);
							
						}
					}
				}
				else {
					getEmpSelectionBox($_SESSION['u_empid'],"");
				}
				
				?>
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

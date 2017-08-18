<?php
session_start();
require_once '../Library.php';
require_once '../attendenceFunctions.php';
error_reporting("E_ALL");
$db=connectToDB();
?>
<html>
<head>
	<title>Extra WFH hour Approval</title>
	<link rel="stylesheet" type="text/css" media="screen" href="../css/teamapproval.css" />
	<link rel="stylesheet" href="../public/js/bootstrap/css/bootstrap.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="../css/frontend.css" />
	<link href="../js/jqueryui/css/redmond/jquery-ui.css" rel="stylesheet">
	<script src="../js/jquery/jquery.js" type="text/javascript"></script>
	<script src="../js/jqueryui/js/jquery-ui.js"></script>
	<script src="../js/jqgrid/grid.locale-en.js" type="text/javascript"></script>
	<script type="text/javascript" src="../js/jquery/jquery.validate.min.js"></script>
	<script src="../js/jqgrid/jquery.jqGrid.min.js" type="text/javascript"></script>
	<script src="../js/jquery/jquery.searchFilter.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="../js/jqgrid/jqgridcss/ui.jqgrid.css" />
	<script src="../js/countdown/countdown.js" type="text/javascript"></script>
	<script src="../projectjs/fullcalendar.js"></script>		
	<script type="text/javascript" src="../projectjs/index.js"></script>
<?php
if(isset($_REQUEST['role']))
{
	$_SESSION['roleofemp']=$_REQUEST['role'];
	if($_REQUEST['role']=="manager") {$divid="loadmanagersection";echo "<script>var divid=\"loadmanagersection\";</script>"; }
	if($_REQUEST['role']=="hr") { $divid="loadhrsection";echo "<script>var divid=\"loadhrsection\";</script>";}
}
?>
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
	$("#loadingmessage").show();
    $("document").ready(function(){
    	$("#loadingmessage").hide();
        $(".table-1 tr:odd").addClass("odd");
        $(".table-1 tr:not(.odd)").hide();
        $(".table-1 tr:first-child").show();
        $(".table-1 tr.odd").click(function(){
    	    $(this).next("tr").toggle();
    	    $(this).find(".arrow").toggleClass("up");
        }); 
    	
		$('#getempExtraWFHhr').submit(function() {
			var empUser=$("#empuser").val();
             if($("#empuser").val()=="")
                {
                 	alert("Please Enter Employee Name");
                    return false;
                }
                $.ajax({ 
                data: "empuser="+empUser, 
                type: "GET",
                url: "wfhhours/approveEmpExtrawfhhour.php?approveview=1", 
                success: function(response) { 
					alert("Extra WFH Hour Approved");
		    		$('#'+divid).html(response);
                }
                });
                return false; 
         });
		$("#comments").submit(function() {
   			$.ajax({
       	 	data: $(this).serialize(),
       		 type: $(this).attr("method"),
       		 url: $(this).attr("action"),
        	success: function(response) {
				 if(response.match(/success/)) {
					var eid=$("#empid").val();
					var date = $("#fromdate").val();
					var tid = $("#tid").val();
					alert("Extra WFH Hour deleted successsfully");
					$('#'+divid).html(response);
					$('#'+divid).load("wfhhours/approveEmpExtrawfhhour.php?comment=1&tid="+tid);
		        } else {
							alert("not successs");
					  }
			 }
			});
			return false; // cancel original event to prevent form submitting
	});
	   jQuery(function() {
          jQuery('#empuser').autocomplete({
          minLength: 1,
          source: function(request, response) {
	           jQuery.getJSON('autocomplete/Users_JSON.php', {
        	   term: request.term
          }, response)
       },
	   focus: function() {
        	// prevent value inserted on focus
            return false;
       },
	   select: function(event, ui) {
        	   this.value = ui.item.value;
                return false;
      }
   });
});
});

	function hidealldiv(div) {
		var myCars = new Array("loadviewwfhhrcontent","loadempapplyleave", "loadempleavestatus", "loadempleavehistory", "loadempleavereport", "loadempeditprofile", "loadholidays", "loadempleavereport", "loadteamleavereport", "loadteamleaveapproval", "loadattendance", "loadcalender", "loadpendingstatus", "loadhrsection", "loadmanagersection", "loadapplyteammemberleave", "loadtrackattendance", "loadwfhhr");
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
        function approveExtrawfh(tid) {

	    	$('#'+divid).load("wfhhours/approveEmpExtrawfhhour.php?approve=1&tid="+tid);
		}
		function notapproveExtrawfh(tid) {

			$('#'+divid).load("wfhhours/approveEmpExtrawfhhour.php?notapprove=1&tid="+tid);
		}       
    </script>
    <style>
    	#approve {
			cursor: pointer;
		}

		#notApprove {
			cursor: pointer;
		}
    </style>
</head>
<body>
	<div id="loadingmessage" style="display:none">
	     <img align="middle" src="images/loading.gif"/>
	</div>
	<?php
	$name = $_SESSION['u_fullname'];
	$firstname = strtok($name, ' ');
	$lastname = strstr($name, ' ');
	?>
	
		<nav class="navbar navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<div id="img">
						<img class="img-responsive" src="../img/3.jpg" style="height:50px;">
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
						window.location = "../lms.php";
					} else {
						alert("Your session is expired. Logging out");
						window.location = "../login.php";
					}
				}
			</script>
		<div id="navbar" class="navbar-collapse collapse">
		<ul class="nav navbar-nav navbar-right" style="padding-right:10px;">
		<li id="home"><a href="../Holidays.php">Holiday List</a></li>
		<li><a href="../attendance.php">Attendance</a></li>
		<li><a href="../trackLeaves.php">Track Leaves</a></li>
		<li><a href="../leavecalender.php">Leave Calender</a></li>
		<li><a href="../ApplyVOE.php">Apply VOE</a></li>
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
					<a href="#"><img src="../img/4.jpg" class="img-circle img-responsive" alt="" width="150px;" height="80px;"></a>
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
					<li class="list-group-item"><a href="../lms.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;My Profile<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:50px;"></i></a></li>
					<li class="list-group-item"><a href="../personalinfo.php"><i class="fa fa-user-secret" aria-hidden="true"></i>&nbsp;Personal Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:30px;"></i></a></li>
					<li class="list-group-item"><a href="../officialinfo.php"><i class="fa fa-building" aria-hidden="true"></i>&nbsp;Official Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<li class="list-group-item"><a href="../applyLeave.php"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;Apply Leave<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<?php
					if(strtoupper($_SESSION['user_dept'])=="HR") {?>
					<li class="list-group-item"><a href="../hr.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;HR Section<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<?php }elseif(strtoupper($_SESSION['user_desgn'])=="MANAGER") {?>
					<li class="list-group-item"><a href="../manager.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Manager Section<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:10px;"></i></a></li>
					<?php }?>
					<!--  <li class="list-group-item"><a href="leaveinfo.php"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;My Leave Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:20px;"></i></a></li>-->
				</ul>
			</div><!--2 column end-->
			<div class="col-sm-1"></div>
			<div class="col-sm-8">
				<div id="loadmanagersection"></div>
				<?php
				if(isset($_REQUEST['approveview']))
				{
					$childern=getChildren($_SESSION['u_empid']);
					if($_SESSION['roleofemp']=="hr") {
						$join1="select a.empname, a.empid, b.wfhHrs, b.status, b.date, b.reason, b.tid from emp a, extrawfh b where b.status='pending' and a.empid=b.eid and a.empname in(SELECT empname FROM emp where state='Active') order by a.empname desc";
					} else {
						$join1="select a.empname, a.empid, b.wfhHrs, b.status, b.date, b.reason, b.tid from emp a, extrawfh b where b.status='pending' and a.empid=b.eid and a.empname in(SELECT empname FROM emp where state='Active' and managerid='".$_SESSION['u_empid']."')order by a.empname";
					}
					$sql1=$db->query($join1);
						echo "<div id='showtable'><table class='table table-hover'>
							<form method='POST' action='' id='WFH' name='ExtraWFHHour'>
							<tr class='info'>
								<th>Emp Name</th>
								<th>Date</th>
								<th>WFH Hours</th>
								<th>Reason</th>
								<th>Approval Status</th>
								<th colspan=2>Actions</th>
							</tr>";
			
						while($row=$db->fetchassoc($sql1)) {
							//get employee name
							echo getempName($row['$empid']);
							echo  '<tr>
										<td>'.$row['empname'].'</td>
										<td>'.$row['date'].'</td>
										<td>'.$row['wfhHrs'].'</td>
										<td>'.$row['reason'].'</td>
										<td>'.$row['status'].'</td>
										<td><div id="approve" title="'.$row['tid'].'" onclick=approveExtrawfh("'.$row['tid'].'") class="'.$row['empid'].'"><font color="red"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></font></div>
					 				<div id="notApprove" title="'.$row['tid'].'" onclick=notapproveExtrawfh("'.$row['tid'].'") class="'.$row['empid'].'"><font color="red"><i class="fa fa-trash" aria-hidden="true"></i></font></div></td>
							</tr>';
						}
						echo "</table>";
					}
					if(isset($_REQUEST['approve']))
					{
						$tid=$_REQUEST['tid'];
						$childern=getChildren($_SESSION['u_empid']);
						$join="UPDATE `extrawfh` SET `status`='Approved' where `tid`='$tid'";
						$sql=$db->query($join);
						if($_SESSION['roleofemp']=="hr") {
							$showapproved="select a.empname, a.empid, b.wfhHrs, b.status, b.date, b.reason, b.tid from emp a, extrawfh b where b.status='Approved' and a.empid=b.eid and a.empname in(SELECT empname FROM emp where state='Active') order by a.empname desc";
						} else {
							$showapproved="select a.empname, a.empid, b.wfhHrs, b.status, b.date, b.reason, b.tid from emp a, extrawfh b where b.status='Approved' and a.empid=b.eid and a.empname in(SELECT empname FROM emp where state='Active' and managerid='".$_SESSION['u_empid']."')order by a.empname";
						}
						$sqlapproved=$db->query($showapproved);
						
						echo "<div id='showtable'><table class='table table-hover'>
							<form method='POST' action='' id='WFH' name='ExtraWFHHour'>
							<tr class='info'>
								<th>Emp Name</th>
								<th>Date</th>
								<th>WFH Hours</th>
								<th>Reason</th>
								<th>Approval Status</th>
							</tr>";
						
						while($row=$db->fetchassoc($sqlapproved)) {
							//get employee name
							echo getempName($row['$empid']);
							echo  '<tr>
										<td>'.$row['empname'].'</td>
										<td>'.$row['date'].'</td>
										<td>'.$row['wfhHrs'].'</td>
										<td>'.$row['reason'].'</td>
										<td>'.$row['status'].'</td>
									</tr>';
						}
						echo "</table>";
					}
				if(isset($_REQUEST['notapprove']))
				{
					$tid=$_REQUEST['tid'];
					## query database if row exists
					$tquery="select wfhHrs, date,eid from extrawfh where `tid`='$tid'";
					$tresult=mysql_query($tquery);
					$tresult=mysql_fetch_array($tresult);
					## if exists, get number of hrs and date
					$noh=$tresult['wfhHrs'];
					$date=$tresult['date'];
					$empid=$tresult['eid'];
				?>
	<form method="post" action="wfhhours/approveEmpExtrawfhhour.php?comment=1" id="comments" name="comments">
		<div class='panel panel-primary'>
			<div class='panel-heading text-center'>
				<strong style='font-size:20px;'>Not Approve Extra WFH Hour by Manager</strong>
			</div>
			<div class='panel-body'>			
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4"><label>Employee Id</label></div>
						<div class="col-sm-8"><input type="text" class="form-control" name="empid" id="empid" value="<?php echo $empid; ?>" readonly></div>
					</div></div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4"><label>Number of Hour</label></div>
						<div class="col-sm-8"><input type="text" class="form-control" name="wfhHrs" id="wfhHrs" value="<?php echo $noh;?>" ></div>
				</div></div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4"><label>Date</label></div>
						<div class="col-sm-8"><input type="text" class="form-control" name="fromdate" id="fromdate" value="<?php echo $date; ?>" readonly></div>
				</div></div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4"><label>comments</label></div>
						<div class="col-sm-8"><textarea class="form-control" name="commentsform" id="commentsform" value=""></textarea></div>
				</div></div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-12 text-center">
						<input type="submit" id="notapprove" class="btn btn-danger" name="notapprove" value="notapprove">
				 		<input type="submit" id="cancel" class="btn btn-primary" name="cancel" value="Cancel">
				</div></div></div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-12"> <input type="hidden" id="tid" name="tid" value="<?= $tid ?>" ></div>
					</div></div>
		   </div></div>
	</form>
	<?php 
	}
	if(isset($_REQUEST['comment']))
	{
		$date = isset($_POST['fromdate']) ? $_POST['fromdate'] : '';
		$noh = isset($_POST['wfhHrs']) ? $_POST['wfhHrs'] : '';
		$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
		$comment = isset($_POST['commentsform']) ? $_POST['commentsform'] : '';
		$updatedAt = date('Y-m-d H:i:s');
		$result=$db->query("UPDATE  extrawfh SET  `status` ='Cancelled', `comments`='$comment' WHERE `tid` ='$tid'");
		if($_SESSION['roleofemp']=="hr") {
			$showcancelled="select a.empname, a.empid, b.wfhHrs, b.status, b.date, b.reason, b.comments, b.tid from emp a, extrawfh b where b.status='Cancelled'and b.date='$date' and a.empid=b.eid and a.empname in(SELECT empname FROM emp where state='Active')order by a.empname";
		} else {
			$showcancelled="select a.empname, a.empid, b.wfhHrs, b.status, b.date, b.reason, b.comments, b.tid from emp a, extrawfh b where b.status='Cancelled' and a.empid=b.eid and a.empname in(SELECT empname FROM emp where state='Active' and managerid='".$_SESSION['u_empid']."')order by a.empname";
		}
		$sqlnotapprove=$db->query($showcancelled);
			
		echo "<div id='showtable'><table class='table table-hover'>
				<form method='POST' action='' id='WFH' name='ExtraWFHHour'>
				<tr class='info'>
					<th>Emp Name</th>
					<th>Date</th>
					<th>WFH Hours</th>
					<th>Reason</th>
					<th>Approval Status</th>
					<th>Comments</th>
				</tr>";
			
		while($row=$db->fetchassoc($sqlnotapprove)) {
			//get employee name
			echo getempName($row['$empid']);
			echo  '<tr>
							<td>'.$row['empname'].'</td>
							<td>'.$row['date'].'</td>
							<td>'.$row['wfhHrs'].'</td>
							<td>'.$row['reason'].'</td>
							<td>'.$row['status'].'</td>
							<td>'.$row['comments'].'</td>
						</tr>';
		}
		echo "</table>";
		if($sqlnotapprove)
		{
			//echo "<script>alert(\"Not Approved\");</script>";
			//send mail for Not approved status to emp and manager to whom manager not approved leave
			$cmd = '/usr/bin/php -f sendmail.php '.$transactionid.' '.$row1['empid'].'  notApproveLeave >> /dev/null &';
			exec($cmd);
		}
	}
	
	if(isset($_REQUEST['viewapprovalform']))
	{
	echo '<form action="approveEmpExtrawfhhour.php" method="POST" id="getempExtraWFHhr">
	           <div class="col-sm-1"></div>
				 <div class="col-sm-9">
				 <div class="row"> 
                   <div class="col-sm-5">
						<label style="font-size:16px;">Enter Employee Name:</label>
					</div>
	                <div class="col-sm-5"><input id="empuser" type="text" class="form-control" name="empuser" value="'.$empName.'"/></div>';
	                echo '<div class="col-sm-2"><input class="submit btn btn-primary" type="submit" name="submit" value="SUBMIT"/></div>
	               </div>   
	           </div>
	      </form>';
	}
	?>
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
</body></html>	 


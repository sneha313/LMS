<?php
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
$db=connectToDB();
?>
<html>
<head>
	<?php
		echo '	<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
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
				<script type="text/javascript" src="projectjs/index.js"></script>';
	?>
	<script>
		$(document).ready(function() {
			$('body').bind('mousedown keydown', function(event) {
				$('#counter').countdown('option', {
					until : +1200
				});
			});
		$("#addextrawfhmanager").click(function() {
		     hidealldiv('loadmanagersection');
     		     $("#"+divid).load('wfhhours/manageraddwfhforemp.php?role=manager');
  		});
		});
		</script>
<?php 
if(isset($_REQUEST['role']))
{
	$_SESSION['roleofemp']=$_REQUEST['role'];
	if($_REQUEST['role']=="manager") {$divid="loadmanagersection";echo "<script>var divid=\"loadmanagersection\";</script>"; }
	if($_REQUEST['role']=="hr") { $divid="loadhrsection";echo "<script>var divid=\"loadhrsection\";</script>";}
}
?>
<script type="text/javascript">
function getdetail(tid) {
	 url='modifyempapprovedleaves.php?change=1&displaytable=1&tid='+tid;
	 $('#'+divid).load(''+url+'');
}
$("document").ready(function() {
	$('#modifyday').submit(function() {
        $.ajax({ 
        data: $(this).serialize(), 
        type: $(this).attr('method'), 
        url: $(this).attr('action'), 
        success: function(response) { 
            $('#'+divid).html(response); 
        }
        });
                return false; 
	});
	$('#deletetid').submit(function() {
        $.ajax({ 
        data: $(this).serialize(), 
        type: $(this).attr('method'), 
        url: $(this).attr('action'), 
        success: function(response) { 
            $('#'+divid).html(response); 
        }
        });
                return false; 
	});
	$('#getemptrans').submit(function() {
		if($("#empuser").val()=="")
		{
			alert("Please Enter Employee Name");
			return false;
		}
		$.ajax({ 
	        data: $(this).serialize(), 
	        type: $(this).attr('method'), 
	        url: $(this).attr('action'), 
	        success: function(response) { 
	            $('#'+divid).html(response); 
	        }
		});
			return false; 
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
		
		$("#deltid").click(function(){
		var r=confirm("Delete Transaction!");
		if (r==true)
		{
			var tid=$("#deltid").attr("title");
			var empid=$("#deltid").attr("class");
			$('#'+divid).load("modifyempapprovedleaves.php?change=1&getDelteComments=1&tid="+tid+"&empid="+empid);
			
  		}
		else
  		{
  			alert("You pressed Cancel!");
  			var tid=$("#deltid").attr("title");
  			$('#'+divid).load("modifyempapprovedleaves.php?change=1&displaytable=1&tid="+tid);
  		}
	});
		$("#modifytid").click(function(){
			var tid=$("#modifytid").attr("title");
			var empid=$("#modifytid").attr("class");
			$('#'+divid).load("modifyempapprovedleaves.php?change=1&modify=1&tid="+tid+"&empid="+empid);
		
		});
		<?php
			getDynamicSelectOptions();
		?>
});
</script>
<style type="text/css">
#modifytid {
	cursor: pointer;
}

#deltid {
	cursor: pointer;
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
				
						<?php
								function displaytable($transactionid) {
									global $db;
									$sql=$db->query("select * from empleavetransactions where transactionid='".$transactionid."'");
									$row=$db->fetchassoc($sql);
									$childern=getChildren($_SESSION['u_empid']);
									$empnametresult=$db->query("select empname from emp where empid='".$row['empid']."' and state='Active'");
									$empnamerow=$db->fetchAssoc($empnametresult);
									if(in_array($row['empid'],$childern) || ($_SESSION['user_dept']=="HR")) {
										echo "<div class='panel panel-primary'>
												<div class='panel-heading text-center'>
													<strong style='font-size:20px;'>Modify Employee Approved Leaves</strong>
												</div>
												<div class='panel-body'>
													<table class='table table-hover'>
														<tr>
															<th>Transaction ID</th>
															<th>Emp Name</th>
															<th>Start Date</th>
															<th>End Date</th>
															<th>Count</th>
															<th>Reason</th>
															<th>approval Status</th>
															<th>Actions</th>
														</tr>
														<tr>";
														echo  '<td>'.$row['transactionid'].'</td>
												      	<td>'.$empnamerow['empname'].'</td>
														<td>'.$row['startdate'].'</td>
														<td>'.$row['enddate'].'</td>
														<td>'.$row['count'].'</td>
														<td>'.$row['reason'].'</td>
														<td>'.$row['approvalstatus'].'</td>';
														if (!preg_match('/CompOff Leave/', $row['reason'])) {
															echo '<td><div id="modifytid" title="'.$row['transactionid'].'" class="'.$row['empid'].'"><font color="red"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></font></div></td>';
														}
												  		echo '<td><div id="deltid" title="'.$row['transactionid'].'" class="'.$row['empid'].'"><font color="red"><i class="fa fa-trash" aria-hidden="true"></i></font></div></td>
														</tr>
													</table>';
									}
									else {
										echo "<script>alert(\"You dont have permissions to change '".$empnamerow['empname']." ' transaction\");</script>";
									}

									echo '</div></div>';
								}
								function displayRecentTrans($emp)
								{
									global $db;
									global $divid;
									$empnametresult=$db->query("select empid,empname from emp where empname='".$emp."' and state='Active'");
									$empnamerow=$db->fetchAssoc($empnametresult);
									$sql=$db->query("select * from empleavetransactions where approvalstatus='Approved' and empid='".$empnamerow['empid']."'");
									$childern=getChildren($_SESSION['u_empid']);
									if(in_array($empnamerow['empid'],$childern) || ($_SESSION['user_dept']=="HR")) {
										echo "<table class='table table-hover'>
											  	<caption> Click on tranasaction Id to modify approved Leaves.</caption>
												<tr>
													<th>Transaction ID</th>
													<th>Emp Name</th>
													<th>Start Date</th>
													<th>End Date</th>
													<th>Count</th>
													<th>Reason</th>
													<th>approval Status</th>
												</tr>";
										while($row=$db->fetchassoc($sql)) {
											echo  '<tr><td><a href="javascript:getdetail(\''.$row['transactionid'].'\')">'.$row['transactionid'].'</a></td>
									      		<td>'.$empnamerow['empname'].'</td>
												<td>'.$row['startdate'].'</td>
												<td>'.$row['enddate'].'</td>
												<td>'.$row['count'].'</td>
												<td>'.$row['reason'].'</td>
												<td>'.$row['approvalstatus'].'</td>
												</tr>';
										}
										echo "</table>";
									}
									else {
										echo "<script>alert(\"You dont have permissions to change '".$empnamerow['empname']." ' transaction\");</script>";
									}
								}
								if(isset($_REQUEST['change']))
								{
									if(isset($_REQUEST['del']))
									{
										getDelSection("modifyempapprovedleaves.php",$_REQUEST['tid'],$_REQUEST['empid'],$_SESSION['roleofemp']);
									}
									if(isset($_REQUEST['getDelteComments']))
									{
										echo '<form id="deletetid" method="POST" action="modifyempapprovedleaves.php?change=1&del=1&tid='.$_REQUEST['tid'].'&empid='.$_REQUEST['empid'].'">';
										echo '<div class="row">
												  <div class="col-sm-4">
  													 <label>Transcation ID</label>
  												  </div>
												  <div class="col-sm-8">
  													 <input type="text" class="form-control" value='.$_REQUEST['tid'].' />
												  </div>
												  
											  </div>
											  <div class="row">
												  <div class="col-sm-4">
														<label>Employee Id</label>
												  </div>
												  <div class="col-sm-8">
													  <input type="text" class="form-control"'.$_REQUEST['empid'].' />
												  </div>
											  </div>
											  <div class="row">
												  <div class="col-sm-4">
													  <label>Comments</label>
												  </div>
												  <div class="col-sm-8">
														<textarea name="txtMessage" class="form-control" rows="2" cols="20"></textarea>
												  </div>
											  </div>
											  <div class="row">
  												  <div class="col-sm-12 text-center">
														<input  type="submit" class="btn btn-primary" name="submit" value="Submit"/>
												  </div>
											  </div>
											</form>';
									}
									if(isset($_REQUEST['modify']))
									{
										$transactionid=$_REQUEST['tid'];
										$empid=$_REQUEST['empid'];
										displaytable($transactionid);
										getSubmitSection($transactionid,"modifyempapprovedleaves.php","modifyday","modifyempapprovedleaves.php?change=1&submitmodifyday=1&tid=$transactionid","");
										echo "<tr><td>Comments</td><td><textarea name='txtMessage' class='form-control' rows='2'' cols='20'></textarea></td></tr>";
										echo "<tr><td colspan=\"2\" align='center'><input class='submit' type='submit' name='submit' value='Submit'/></td></tr>
									</tbody></table></form>";
									}
									if(isset($_REQUEST['submitmodifyday']))
									{
										getModifySection("modifyempapprovedleaves.php",$_SESSION['roleofemp']);
									}
									if(isset($_REQUEST['displaytable']))
									{
										displaytable($_REQUEST['tid']);
									}
									if(isset($_REQUEST['displayrecentrtans']))
									{
										displayRecentTrans($_REQUEST['empuser']);
									}
								}
								else
								{
									echo '<form action="modifyempapprovedleaves.php?change=1&displayrecentrtans=1" method="POST" id="getemptrans">
											
											<div class="row"> 
												<div class="col-sm-1"></div>
							                   <div class="col-sm-4"><label style="font-size:16px;">Enter Employee Name:</label></div>
							         		   <div class="col-sm-5"><input type="text" id="empuser" class="form-control ui-autocomplete-input" autocomplete="off" name="empuser"/></div>';
									echo '<div class="col-sm-2"><input class="submit btn btn-primary" type="submit" name="submit" value="SUBMIT"/></div>	
									</div>
									</div>
									</form>';
								}
							?>
	</div>
	<div class="col-sm-1"></div>
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
	</body>
</html>

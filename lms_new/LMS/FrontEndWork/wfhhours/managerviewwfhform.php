<?php
session_start();
require_once '../Library.php';
require_once '../attendenceFunctions.php';
error_reporting("E_ALL");
$db=connectToDB();
?>
<?php
echo '<html>
<head>';
echo '<link rel="stylesheet" type="text/css" media="screen" href="css/selfleavehistory.css" />
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
	  <script type="text/javascript">  
		 $("#loadingmessage").show();
         $("document").ready(function(){
			$("#wfhHrs").spinner(
               { min: 1 },
               { max: 18 },
			   { step: 0.25 }
        	);
    		$("#loadingmessage").hide();
			$( "#tabs" ).tabs();
		
			$("#editbymanager").submit(function() {
		   			$.ajax({
		       	 	data: $(this).serialize(),
		       		 type: $(this).attr("method"),
		       		 url: $(this).attr("action"),
		        	success: function(response) {
						 if(response.match(/success/)) {
							alert("WFH edited successfully");
							var eid=$("#empid").val();
							var date = $(".workeddaydynamic").val();
							$("#loadmanagersection").html(response);
							hidealldiv("loadmanagersection");
							$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&eid="+eid+"&date="+date);
				        } else {
									alert("not successs");
							  }
					 }
					});
					return false; // cancel original event to prevent form submitting
			});
		
				$("#deletebymanager").submit(function() {
		   			$.ajax({  
		       	 	data: $(this).serialize(),
		       		type: $(this).attr("method"),
		       		url: $(this).attr("action"),
		        	success: function(response) {
						var r=confirm("Delete Leave!");
						var eid=$("#empid").val();
						var date = $(".workeddaydynamic").val();
								if (r==true)
								{
									var dellink=$("#deleteFormbymanager").attr("href");
									$("#loadmanagersection").html(response);
									hidealldiv("loadmanagersection");
									$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&&eid="+eid+"&date="+date);
								}
								else
						  		{
						  			alert("You pressed Cancel!");
						  			$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&delcancel=1&eid="+eid+"&date="+date);
								}
							  } 
					});
					return false; // cancel original event to prevent form submitting
			});

			$("#viewrecordbymanager").submit(function() {
					if($("#empuser").val()=="")
					{
						alert("Please Enter Employee Name");
						return false;
					}
		   			$.ajax({
			       	 	 data: $(this).serialize(),
			       		 type: $(this).attr("method"),
			       		 url: $(this).attr("action"),
			        	 success: function(response) {
							var eid=$("#empid").val();
							var date=$(".workeddaydynamic").val();
							$("#loadmanagersection").html(response);
							$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&eid="+eid+"&date="+date);
			        	}
					});
					return false; // cancel original event to prevent form submitting
			});
		
			$("#viewEmpWFHbymanager").submit(function() {
					if($("#empuser").val()=="")
					{
						alert("Please Enter Employee Name");
						return false;
					}
		   			$.ajax({
			       	 	 data: $(this).serialize(),
			       		 type: $(this).attr("method"),
			       		 url: $(this).attr("action"),
			        	 success: function(response) {
							hidealldiv("loadmanagersection");
							$("#loadmanagersection").html(response);
			        	}
					});
					return false; // cancel original event to prevent form submitting
			});
			jQuery(function() {
       			jQuery("#empuser").autocomplete({
	            minLength: 1,
	            source: function(request, response) {
	                jQuery.getJSON("../autocomplete/Users_JSON.php", {
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
			var myCars = new Array("loadviewwfhhrcontent","loadempapplyleave", "loadempleavestatus", "loadempleavehistory", "loadempleavereport", "loadempeditprofile", "loadholidays", "loadempleavereport", "loadteamleavereport", "loadteamleaveapproval", "loadattendance", "loadcalender", "loadpendingstatus", "loadhrsection", "loadmanagersection", "loadapplyteammemberleave", "loadtrackattendance", "loadwfhhr", "loadmanagersection");
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
			
		function editExtrawfh(tid) {
			hidealldiv("loadmanagersection");
			$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?editExtrawfh=1&tid="+tid);
		}
		function deleteExtrawfh(tid) {
		
			hidealldiv("loadmanagersection");
			$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?deleteExtrahour=1&tid="+tid);
		}
   </script>
</head>
<body>

<div id="loadingmessage" style="display:none">
     <img align="middle" src="images/loading.gif"/>
</div>';?>
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
<?php 
if(isset($_REQUEST['deleteFormbymanager'])){
	$tmpdate= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
	$date=$tmpdate[0];	
	$noh = isset($_POST['wfhHrs']) ? $_POST['wfhHrs'] : '';
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$updatedAt = date('Y-m-d H:i:s');
	//if(isset($_REQUEST['delete'])){
	$queryDel="UPDATE extrawfh set status='Deleted' WHERE `tid`= '$tid'";
	$sql2=$db->query($queryDel);
	//}
	/*elseif(isset($_REQUEST['delcancel'])){
		$queryDel="select * from extrawfh  WHERE `tid`= '$tid'";
		$sql2=$db->query($queryDel);
	}*/
	if($sql2){
		//send mail that record is deleted
		$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$eid.'  deleteExtraWFH >> /dev/null &';
		exec($cmd);
	} else {
		echo "<center><h3>Record not deleted</h3></center>";
	}
} if(isset($_REQUEST['deleteExtrahour'])){
	//edit form here employee can edit extra work from home hour and date
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
	<form method="POST" action="managerviewwfhform.php?deleteFormbymanager=1" id="deletebymanager" name="deletebymanager">
			<div class='panel panel-primary'>
				<div class='panel-heading text-center'>
					<strong style='font-size:20px;'>Delete Extra WFH Hour</strong>
				</div>
				<div class='panel-body'>	
					<div class="form-group">
					<div class="row">
						<div class="col-sm-4"><label>Employee Id</label></div>
						<div class="col-sm-8"><input type="text" class="form-control" name="empid" id="empid" value="<?php echo $empid; ?>" readonly></div>
					</div></div>
					<div class="form-group">
					<div class="row">
						<div class="col-sm-4">
							<label>Number of Hour</label>
						</div>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="wfhHrs" id="wfhHrs" value="<?php echo $noh;?>" >
						</div>
					</div></div>
					
					<div class="form-group">
					<div class="row">
						<div class="col-sm-4">
							<label>Date</label>
						</div>
						<div class="col-sm-8">
						<div class="input-group">
							<input type="text" id="datetimepicker" class="workeddaydynamic form-control open-datetimepicker" name="dynamicworked_day" value="<?php echo $date;?>" readonly />
							<label class="input-group-addon btn" for="date">
								<span class="fa fa-calendar open-datetimepicker"></span>
							</label>
					  </div></div>
					</div></div>
					<div class="form-group">
					<div class="row">
						<div class="col-sm-12 text-center">
						 	<input type="submit" class="btn btn-danger" id="delete" name="delete" value="delete">
						 	<input type="submit" id="delcancel" class="btn btn-primary" name="delcancel" value="cancel">
						 </div>
					</div></div>
					<div class="form-group">
					<div class="row">
						<div class="col-sm-12"><input type="hidden" id="tid" name="tid" value="<?= $tid ?>" ></div>
					</div></div>
		   </div></div>
	     </form>
	<?php 
} 
if(isset($_REQUEST['editFormbymanager'])){
	$date= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
	$noh = isset($_POST['wfhHrs']) ? $_POST['wfhHrs'] : '';
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$updatedAt = date('Y-m-d H:i:s');
	$queryEdit="UPDATE extrawfh SET `wfhHrs`='$noh', `date`='$date', `updatedAt`='$updatedAt', `updatedBy`='".$_SESSION['user_name']."'  WHERE `tid`='$tid'";
	$sql3=$db->query($queryEdit);
	if($sql3){
		//send mail that record is updated 
		$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$eid.'  editExtraWFH >> /dev/null &';
		exec($cmd);
	} else {
		echo "<center><h3>Record not updated</h3></center>";
	}
} if(isset($_REQUEST['editExtrawfh'])){
	//edit form here employee can edit extra work from home hour and date
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
	<form method="POST" action="managerviewwfhform.php?editFormbymanager=1" id="editbymanager" name="editbymanager">
			<div class='panel panel-primary'>
				<div class='panel-heading text-center'>
					<strong style='font-size:20px;'>Edit Extra WFH Hour</strong>
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
					<div class="col-sm-8">
					<div class="input-group">
						<input type="text" id="datetimepicker" class="workeddaydynamic form-control open-datetimepicker" name="dynamicworked_day" readonly />
						<label class="input-group-addon btn" for="date">
							<span class="fa fa-calendar open-datetimepicker"></span>
						</label>
				  </div></div>
				</div></div>
				
				<div class="form-group">
				<div class="row">
					<div class="col-sm-12 text-center">
				 	<input type="submit" id="cancel" class="btn btn-primary" name="cancel" value="cancel">
				 	<input type="submit" id="submit" class="btn btn-danger" name="submit" value="Edit"></div>
				</div></div>
				
				<div class="form-group">
				<div class="row">
					<div class="col-sm-12"> <input type="hidden" id="tid" name="tid" value="<?= $tid ?>" ></div>
				</div></div>
		   </div>
		   </div>
	     </form>
	<?php 
} 
if(isset($_REQUEST['viewrecordbymanager']) || isset($_REQUEST['viewEmpWFHbymanager'])) { 
	
	
	if (isset($_REQUEST['displayAll'])) {
		$empQuery="select empid,empname from emp where empname='".$_REQUEST['empuser']."' and state='Active'";
		$empnametresult=$db->query($empQuery);
		$empnamerow=$db->fetchAssoc($empnametresult);
		$empid=$empnamerow['empid'];
		//show record based on employee id where status is not equal to deleted
		$query="select * from extrawfh where status!='Deleted' and eid='".$empid."' order by date";
	} else {
		$date=$_REQUEST['date'];
		$empid=$_REQUEST['eid'];
		$empQuery="select empid,empname from emp where empid='".$empid."' and state='Active'";
		$empnametresult=$db->query($empQuery);
		$empnamerow=$db->fetchAssoc($empnametresult);
		$query="select * from extrawfh where status!='Deleted' and eid='".$empid."' order by date";
	}
	
	
	$sql=$db->query("SELECT DISTINCT YEAR(date) as year FROM extrawfh where eid='".$empid."' order by year desc");
	$distinctYears=array();
	$leaveCount=$db->countRows($sql);

	echo '<h3 align=\"center\"><u>View Extra WFH Details</u></h3><br><br>';
	if($leaveCount == 0) {
		echo "<div id='tabs'><ul><div id='Info'><tr><td>No Data Available</td></tr></div></ul></div>";
	} else {
		echo '<div id="tabs">
                <ul>';
	}
	for($i=0;$i<$db->countRows($sql);$i++)
	{
		$row=$db->fetchArray($sql);
		echo "<li><a href='#".$row['year']."'>".$row['year']."</a></li>";
		array_push($distinctYears,$row['year']);
	}
	echo "</ul>";
	
	foreach ($distinctYears as $year) {
		echo "<div id='".$year."'>";
		
	
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
		$sql1=$db->query($query);
		
		while($getDetailedrow=$db->fetchassoc($sql1)) {
			echo  '<tr>
		      		<td>'.$empnamerow['empname'].'</td>
					<td>'.$getDetailedrow['date'].'</td>
					<td>'.$getDetailedrow['wfhHrs'].'</td>
					<td>'.$getDetailedrow['reason'].'</td>
					<td>'.$getDetailedrow['status'].'</td>
		 			<td><div id="modify" title="'.$getDetailedrow['tid'].'" onclick=editExtrawfh("'.$getDetailedrow['tid'].'") class="'.$getDetailedrow['eid'].'"><font color="red"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></font></div>
	 				<div id="delete" title="'.$getDetailedrow['tid'].'" onclick=deleteExtrawfh("'.$getDetailedrow['tid'].'") class="'.$getDetailedrow['eid'].'"><font color="red"><i class="fa fa-trash" aria-hidden="true"></i></font></div></td>
			</tr>';
		}
		echo "</form></table></div></div>";
	}
}
if(isset($_REQUEST['viewform'])){
	echo '<form action="managerviewwfhform.php?viewEmpWFHbymanager=1&displayAll=1" method="POST" id="viewEmpWFHbymanager">
			<div class="row"> 
				<div class="col-sm-1"></div>
				<div class="col-sm-4"><label style="font-size:16px;">Enter Employee Name:</label></div>
         		<div class="col-sm-5"><input id="empuser" type="text" class="form-control" name="empuser"/></div>
				<div class="col-sm-2"><input class="submit btn btn-primary" type="submit" name="submit" value="SUBMIT"/></div>
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

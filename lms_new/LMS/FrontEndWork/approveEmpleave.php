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
		<!-- <link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/teamapproval.css">
		<script src="public/js/jquery/jquery-1.10.2.min.js"></script>
		<script src="public/js1/jqueryui/js/jquery-ui.js"></script>
		<script type="text/javascript" src="projectjs/index.js"></script>
		<script src="js/jqgrid/grid.locale-en.js" type="text/javascript"></script>
		<script type="text/javascript" src="js/jquery/jquery.validate.min.js"></script>
		<script src="js/jqgrid/jquery.jqGrid.min.js" type="text/javascript"></script>
		<script src="js/jquery/jquery.searchFilter.js" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/jqgridcss/ui.jqgrid.css" />
		<script src="js/countdown/countdown.js" type="text/javascript"></script>-->
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
        $("document").ready(function(){
        	$("#teamapprove tr:odd").addClass("odd");
            $("#teamapprove tr:not(.odd)").hide();
            $("#teamapprove tr:first-child").show();
            $("#teamapprove tr.odd").click(function(){
                $(this).next("tr").toggle();
                $(this).find(".arrow").toggleClass("up");
            });
		
	    $('#getemptrans').submit(function() {
		var empUser=$("#empuser").val();
                if($("#empuser").val()=="")
                {
                        alert("Please Enter Employee Name");
                        return false;
                }
                $.ajax({ 
                data: "empuser="+empUser, 
                type: "GET",
                url: "approveEmpleave.php", 
                success: function(response) { 
		   		$('loadmanagersection').html(response);
		   		//$('#'+divid).html(response);
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
	});
        function approve(empName,tid)
        {
	    empName=encodeURIComponent(empName);
            $('#'+divid).load('approveEmpleave.php?approve=1&tid='+tid, function() {
  		$('#'+divid).load('approveEmpleave.php?empuser='+empName);
	    });
	    
        }
        
        function submitcomments(empName,tid,x)
        {
           var comments = $("#txtMessage"+x).val();
	   empName=encodeURIComponent(empName);
           $('#'+divid).load('approveEmpleave.php?notapprove=1&tid='+tid+'&comments='+encodeURIComponent(comments),function() {
                $('#'+divid).load('approveEmpleave.php?empuser='+empName);
            });
        }        
        
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
			
			<div class="col-sm-10">
				<div id="loadmanagersection">
				<?php
					if(isset($_REQUEST['approve']))
					{
						$transactionid=$_REQUEST['tid'];
						//Selecting the empid and count based on transactionid
						$getleavesquery = $db->query("SELECT  `empid` ,  `count`,`startdate`,`enddate` FROM empleavetransactions WHERE transactionid ='".$transactionid."'");
						$row1=$db->fetchAssoc($getleavesquery);
						$balancequery = $db->query("SELECT  balanceleaves FROM emptotalleaves WHERE empid =".$row1['empid']);
						$row2=$db->fetchAssoc($balancequery);
						$reducedleaves=($row2['balanceleaves']-$row1['count']);
						//Check if balance leaves is exceeding permitted leaves per year
						if(($reducedleaves+(getCarryForwardedLeaves($row1['empid']))) < -6) {
							echo "<script>alert('Leaves cant be approved as emp leaves are exceeding permitted leaves per year after approval. So Leaves are not approved.')</script>";
						}
						else {
						//Selecting leavetype from perdaytransactionstable
						$leavetypequery=$db->query("SELECT leavetype FROM  `perdaytransactions` WHERE transactionid ='".$transactionid."' AND leavetype !=  'FullDay' AND leavetype !=  'HalfDay' AND leavetype !=  'WFH'");
						$leavetyperow=$db->fetchAssoc($leavetypequery);
						//Get leave type id from special leaves
						$leavetypeidquery=$db->query("select specialleaveid from specialleaves where specialleave LIKE '".$leavetyperow['leavetype']."%'");
						if($leavetypeidquery) {
							$leavetypeidrow=$db->fetchAssoc($leavetypeidquery);
							//get splleavetaken from empsplleavetaken
							if($leavetypeidrow) {
								$splleavetakenquery=$db->query("select splleavetaken from  empsplleavetaken where empid='".$row1['empid']."'");
								if($db->hasRows($splleavetakenquery) && $leavetypeidrow)
								{
									$splleavetakenrow=$db->fetchAssoc($splleavetakenquery);
									$updatedspl=str_replace("".$leavetypeidrow['specialleaveid']."P","".$leavetypeidrow['specialleaveid']."A","".$splleavetakenrow['splleavetaken']."");
									$updatesplleavetakenquery=$db->query("UPDATE  empsplleavetaken SET  `splleavetaken` =  '".$updatedspl."' where empid='".$row1['empid']."'");
								}
							}
						}
						//Updating the balance leaves
						$reduceleavesquery=$db->query("UPDATE  `emptotalleaves` SET  `balanceleaves` =  '".$reducedleaves."' WHERE  `empid` ='".$row1['empid']."'");
						//Updating the approval status to "Approved"
						$updateapprovalstatus=$db->query("UPDATE  empleavetransactions SET  `approvalstatus` =  'Approved',approvalcomments='Approved By (".$_SESSION['u_fullname'].")' WHERE  `transactionid` ='".$transactionid."'");
						
						
						$optionalLeaveQuery="select * from empoptionalleavetaken where empid='".$row1['empid']."' and state='Pending'";
						$optionalLeaveResult=$db->query($optionalLeaveQuery);
						if($db->hasRows($optionalLeaveResult)) {
							while($optionalLeaveRow = $db->fetchAssoc($optionalLeaveResult)){
								$datesRange=getDatesFromRange($row1['startdate'],$row1['enddate']);
								if(in_array($optionalLeaveRow['date'],$datesRange)) {
									$updateOptionalLeave="update empoptionalleavetaken set state='Approved',approvalcomments='Approved By (".$_SESSION['u_fullname'].")' where empid='".$row1['empid']."' and date='".$optionalLeaveRow['date']."'";
									$optionalLeave=$db->query($updateOptionalLeave);
								}
						   }
						}
						
						if($updateapprovalstatus)
						{
							//send mail for Approval status to emp and manager to whom manager approved leave
							$cmd = '/usr/bin/php -f sendmail.php '.$transactionid.' '.$row1['empid'].'  ApproveLeave >> /dev/null &';
							exec($cmd);
							$empname=$db->query("select empname from emp where state='Active' and empid=".$row1['empid']);
							$empnamerow=$db->fetchAssoc($empname);
							echo "<script>alert(\"Leave Approved and sending mail\");</script>";
						}
						}
					}
					if(isset($_REQUEST['notapprove']))
					{
						$transactionid=$_REQUEST['tid'];
						$leavetypequery=$db->query("SELECT leavetype,empid FROM  `perdaytransactions` WHERE transactionid ='".$transactionid."' AND leavety:qpe !=  'FullDay' AND leavetype !=  'HalfDay' AND leavetype !=  'WFH'");
						if($leavetypequery) {
							$leavetyperow=$db->fetchAssoc($leavetypequery);
							//Get leave type id from special leaves
							if($leavetyperow) {
								$leavetypeidquery=$db->query("select specialleaveid from specialleaves where specialleave LIKE '".$leavetyperow['leavetype']."%'");
								$leavetypeidrow=$db->fetchAssoc($leavetypeidquery);
								$splleavetakenquery=$db->query("select splleavetaken from  empsplleavetaken where empid='".$leavetyperow['empid']."'");
								$spldelete=$db->fetchAssoc($splleavetakenquery);
								//Removing the pending leave when deleted the transaction
								$delspl=str_replace("".$leavetypeidrow['specialleaveid']."P:","","".$spldelete['splleavetaken']."");
								$updatesplleavetakenquery=$db->query("UPDATE  empsplleavetaken SET  `splleavetaken` =  '".$delspl."' where empid='".$leavetyperow['empid']."'");
							}
						}
						$getleavesquery = $db->query("SELECT  * FROM empleavetransactions WHERE transactionid ='".$transactionid."'");
						$row1=$db->fetchAssoc($getleavesquery);
						if (preg_match('/CompOff Leave/', $row1['reason'])) {
							$getinoutCompOff=$db->query("select Date from  `inout` where compofftakenday ='".$row1['startdate']."' and empid='".$row1['empid']."'");
							$getinoutCompOffRow=$db->fetchAssoc($getinoutCompOff);
							$updateCompOff=$db->query("UPDATE `inout` SET compofftakenday='0000-00-00' WHERE empid='".$row1['empid']."' and Date='".$getinoutCompOffRow['Date']."'");
						}
						$comments= mysql_real_escape_string($_REQUEST['comments']);	
						if(empty($comments)) {
							$comments="Cancelled by (".$_SESSION['u_fullname'].")";
						} else {
							$comments=$comments." (Cancelled by ".$_SESSION['u_fullname'].")";
						}
						$result=$db->query("UPDATE  empleavetransactions SET  `approvalstatus` =  'Cancelled',`approvalcomments` = '".$comments."'  WHERE  `transactionid` ='".$transactionid."'");
						if($result)
						{
							echo "<script>alert(\"Not Approved\");</script>";
							//send mail for Not approved status to emp and manager to whom manager not approved leave
							$cmd = '/usr/bin/php -f sendmail.php '.$transactionid.' '.$row1['empid'].'  notApproveLeave >> /dev/null &';
							exec($cmd);
						}
					}
					
					
					if(isset($_REQUEST['empuser'])) {
					        $empName=$_REQUEST['empuser'];
					} else {
					        $empName="";
					}
					
					echo '<form action="approveEmpleave.php" method="POST" id="getemptrans">
					      <div class="col-sm-1"></div>
									<div class="col-sm-9">
									<div class="row"> 
					                   <div class="col-sm-5"><label style="font-size:16px;">Enter Employee Name:</label></div>
					                   <div class="col-sm-5"><input id="empuser" type="text" class="form-control ui-autocomplete-input" name="empuser" value="" autocomplete="off"/>
										
										</div>';
					                echo '<div class="col-sm-2"><input class="submit btn btn-primary" type="submit" name="submit" value="SUBMIT"/></div>
					                        </div>   
					                        </div>
					                </form>';
					if(!empty($empName)) {
					?>
					<table id="teamapprove">
					<thead>
						<tr>
							<th>Employee Name</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Count</th>
							<th>Reason</th>
							<th>Approval Status</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<?php
						// Check whether the given employee is a manager or user
						
						//$emparray=getemp($_SESSION['u_empid']);
						$empIdList=array();
						$emparrayquery=$db->query("SELECT empid,empname,role FROM  `emp` WHERE state='Active' and empname='".$empName."'");
						$emparray=$db->fetchAssoc($emparrayquery);
						$childern=getemp($_SESSION['u_empid']);
				        if(in_array($emparray['empid'],$childern) || ($_SESSION['user_dept']=="HR")) {	
							if (strtoupper($emparray['role']) == "MANAGER") {
								array_push($empIdList,$emparray['empid']);
								$subOrdinateQuery=$db->query("select empid from emp where state='Active' and managerid='".$emparray['empid']."'");
								while($subOrdinateResult = $db->fetchAssoc($subOrdinateQuery)) {
									$val=$subOrdinateResult['empid'];
									array_push($empIdList,$val);
								}
							} else {
								array_push($empIdList,$emparray['empid']);
							}
							$count=0;
							$pendingLeaves=0;	
							foreach ($empIdList as $empID) {
								$sql=$db->query("select id,transactionid,startdate,enddate,count,reason,approvalstatus,approvalcomments from empleavetransactions where empid='".$empID."' and approvalstatus='Pending'");
					                        $empnamequery=$db->query("select empname from emp where state='Active' and empid=".$empID);
				        	                $emprow=$db->fetchAssoc($empnamequery);
				                	        for($x=0;$x<$db->countRows($sql);$x++)
				                        	{
				                        	        $row=$db->fetchArray($sql);
				                        		echo '<tr><td>'.$emprow['empname'].'</td>';
					                                echo '<td>'.$row['startdate'].'</td>';
				        	                        echo '<td>'.$row['enddate'].'</td>';
				                	                echo '<td>'.$row['count'].'</td>';
				                        	        echo '<td>'.$row['reason'].'</td>';
				                        	        echo '<td>'.$row['approvalstatus'].'</td>';
				                        		echo '<td><div class="arrow"></div></td></tr>';
					                                $sql1=$db->query("select * from perdaytransactions where transactionid='".$row['transactionid']."'");
				        	                        echo '<tr>
				                	                        <td colspan="6">
				                        	                <table>
				                        	                        <tr>
				                        		                <th>Date</th>
				                        	        	        <th>Leave Type</th>
					                                                <th>Shift</th>
				        	                                </tr>';
				                	                for($j=0;$j<$db->countRows($sql1);$j++)
				                        	        {
				                        	                $row1=$db->fetchArray($sql1);
				                        		        echo '<tr></tr><tr><td>'.$row1['date'].'</td>';
				                        	        	echo '<td>'.$row1['leavetype'].'</td>';
					                                        echo '<td>'.$row1['shift'].'</td>';
				        	                        }
				                	                echo '<tr></tr><tr><td><button onclick="notapprove'.$count.'()">Not Approve</button></td>';
				                        	        echo '<td><button onclick="approve(\''.$empName.'\',\''.$row['transactionid'].'\')">Approve</button></td>';
				                        	        echo '</tr>';
				                        		echo '</table><div id="comments'.$count.'">
									        <textarea id=txtMessage'.$count.' rows="2" cols="20" placeholder="Write Comments"></textarea>
									        <button onclick="submitcomments(\''.$empName.'\',\''.$row['transactionid'].'\','.$count.')">OK</button>
									</div></td></tr><tr></tr>';             
				                        		
									echo '<script type="text/javascript">';
				        	        		echo '$("#comments'.$count.'").hide();';
							                echo 'function notapprove'.$count.'(tid)
								                          {
				                                				$("#comments'.$count.'").toggle();
									                  }';
								        echo "</script>";
									$count=$count+1;
									$pendingLeaves=1;
				                	        }
							}
							if ($pendingLeaves == 0) {
								echo "<tr><td colsapn='6'>No Pending Leaves Available</td></tr>";
							}
						} else {
							echo "<script>alert(\"You dont have permissions to approve '".$emparray['empname']." ' Leaves\");</script>"; 
						}
				}	 
			?>
		</tbody>
		</table>
		</div>
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
<?php
session_start();
require_once ("librarycopy1.php");
//require_once 'Library.php';
require_once 'LMSConfig.php';
$db=connectToDB();
?>
<html>
<head>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<title>Team Approval</title>
<link rel="stylesheet" type="text/css" media="screen" href="css/teamapproval.css" />
<?php
if(isset($_REQUEST['role']))
{
        $_SESSION['roleofemp']=$_REQUEST['role'];
        if($_REQUEST['role']=="manager") {$divid="loadmanagersection";echo "<script>var divid=\"loadmanagersection\";</script>"; }
        if($_REQUEST['role']=="hr") { $divid="loadhrsection";echo "<script>var divid=\"loadhrsection\";</script>";}
}
?>
<script type="text/javascript">  
        $("document").ready(function() {
           	$(".dispalyRow").show();
            $(".hideRow").hide();
            $(".displayRow").click(function(){
                $(this).next("tr").toggle();
                $(this).find(".arrow").toggleClass("up");
            });
		
			$('#teamLeaveApprovalId').submit(function() {
				if ( $("#hideDept").length!=0 && $("#hideDept").val().toUpperCase() == "NONE" ) {
					BootstrapDialog.alert("Please select the Department");
					return false;
				}
				
				if ( $("#hideDept").val()=="ALL" && $("#getEmpName").val()=="ALL") {
					BootstrapDialog.alert("Please wait for few minutes to get the results for all ECI Employeees.It will take more than a minute.");
				}
				$('#loadingmessage').show();
				$.ajax({
					data : $(this).serialize(),
					type : $(this).attr('method'),
					url : $(this).attr('action'),
					success : function(response) {
						$('#'+divid).html(response);
						if($("#balanceDialog")) {
					              $("#balanceDialog").hide();
					        }
					}
				});
				return false;
			});

			 $( "#accordion-new" ).accordion({
				 heightStyle: "content",
				 collapsible: true
			 });
	
			 $.each( $( "#accordion-new h3"), function( i, val ) {
					var first=$($(val)).next().find(".teamPendingLeave").text();
					if ( first > 10 ) { 
						var firstString = "<font color=red>"+$(val).next().find(".teamPendingLeave").text();
					}  else { 
						var firstString =$(val).next().find(".teamPendingLeave").text();
					}
					$(val).html("<table class='table table-hover' id='table-2'><tr><td width='30%'><b>"+$(val).text()+"</b></td><td width='70%'><table class='table' id='table-2'><tr><td>Total Leave Applications: "+
							firstString+"</td></tr></table>");
					
			});

			$('#location').change(function() {
				var location=$(this).val();
				$.post( 'getSplLeaveOptions.php?location='+location, function(data) {
					$('#hideDept').empty();
					$('#hideDept').append(data);
				});
			});
       });

        function approve(empName,tid)
        {
	    	empName=encodeURIComponent(empName);
            $('#'+divid).load('approveEmpLeave.php?approve=1&tid='+tid, function() {
  				$('#'+divid).load('approveEmpLeave.php?empuser='+empName);
	    	});
	    
        }
        
        function submitcomments(empName,tid,x)
        {
           var comments = $("#txtMessage"+x).val();
	   	   empName=encodeURIComponent(empName);
           $('#'+divid).load('approveEmpLeave.php?notapprove=1&tid='+tid+'&comments='+encodeURIComponent(comments),function() {
                $('#'+divid).load('approveEmpLeave.php?empuser='+empName);
            });
        }        
    </script>
    
    <?php
		echo '
		<script>
		$("#hideDept").change(function() {
			var dept=$("#hideDept").val();
			var empid="' . $_SESSION['u_empid'] . '";
			if(empid==dept || dept=="none") {
				$("#hideName").hide();
			} else {
				$("#hideName").show();
				$.post("getSplLeaveOptions.php?dept="+escape(dept),function(data) {
					$("#getEmpName").empty();
					$("#getEmpName").append(data);
				});
			}
		});
		</script>';
	?>
    
	<style>
		.arrow {
			background: transparent url(public/images/arrows.png) no-repeat scroll 0px
				-16px;
			width: 16px;
			height: 16px;
			display: block;
		}
		#loadingmessage{
			align:center;
		}
	</style>
</head>
<body>
<?php
if(isset($_REQUEST['approve']))
{
	$transactionid=$_REQUEST['tid'];
	//Selecting the empid and count based on transactionid
	$getleaves="SELECT  `empid` ,  `count`,`startdate`,`enddate` FROM empleavetransactions WHERE transactionid ='".$transactionid."'";
	$getleavesquery = $db->pdoQuery($getleaves);
	$rows1=$db->pdoQuery($getleaves)->results();
	foreach($rows1 as $row1)
	$balanceleavequery="SELECT  balanceleaves FROM emptotalleaves WHERE empid =".$row1['empid'];
	$balancequery = $db->pdoQuery($balanceleavequery);
	$rows2=$db->pdoQuery($balanceleavequery)->results();
	foreach($rows2 as $row2)
	$reducedleaves=($row2['balanceleaves']-$row1['count']);
	//Check if balance leaves is exceeding permitted leaves per year
	if(($reducedleaves+(getCarryForwardedLeaves($row1['empid']))) < -6) {
		echo "<script>BootstrapDialog.alert('Leaves cant be approved as emp leaves are exceeding permitted leaves per year after approval. So Leaves are not approved.')</script>";
	}
	else {
	//Selecting leavetype from perdaytransactionstable
	$leavetype=	"SELECT leavetype FROM  `perdaytransactions` WHERE transactionid ='".$transactionid."' AND leavetype !=  'FullDay' AND leavetype !=  'HalfDay' AND leavetype !=  'WFH'";
	$leavetypequery=$db->pdoQuery($leavetype)->results();
	foreach($leavetypequery as $leavetyperow)
	//$leavetyperow=$leavetypequery->results();
	//Get leave type id from special leaves
		$leavetypeid="select specialleaveid from specialleaves where specialleave LIKE '".$leavetyperow['leavetype']."%'";
	$leavetypeidquery=$db->pdoQuery($leavetypeid);
	if($leavetypeidquery) {
		$leavetypeidrows=$db->pdoQuery($leavetypeid)->results();
		foreach($leavetypeidrows as $leavetypeidrow)
		//get splleavetaken from empsplleavetaken
		if($leavetypeidrow) {
			$splleavetaken="select splleavetaken from  empsplleavetaken where empid='".$row1['empid']."'";
			$splleavetakenquery=$db->pdoQuery($splleavetaken);
			$splleavetakenqueryrow=$splleavetakenquery -> count($sTable = 'empsplleavetaken', $sWhere = 'empid = "'.$row1['empid'].'"' );
			
			if($splleavetakenqueryrow>0 && $leavetypeidrow)
			{
				$splleavetakenrows=$db->pdoQuery($splleavetaken)->results();
				foreach($splleavetakenrows as $splleavetakenrow)
				$updatedspl=str_replace("".$leavetypeidrow['specialleaveid']."P","".$leavetypeidrow['specialleaveid']."A","".$splleavetakenrow['splleavetaken']."");
				//$updatesplleavetakenquery=$db->pdoQuery("UPDATE  empsplleavetaken SET  `splleavetaken` =  '".$updatedspl."' where empid='".$row1['empid']."'");
				$updatesplleavetaken = array('splleavetaken'=>$updatedspl);
				// two where condition array
				$aWhere = array('empid'=>$row1['empid']);
				// call update function
				$updatesplleavetakenquery = $db->update('empsplleavetaken', $reduceleaves, $aWhere)->affectedRows();
			}
		}
	}
	//Updating the balance leaves
	//$reduceleavesquery=$db->query("UPDATE  `emptotalleaves` SET  `balanceleaves` =  '".$reducedleaves."' WHERE  `empid` ='".$row1['empid']."'");
	$reduceleaves = array('balanceleaves'=>$reducedleaves);
	// two where condition array
	$aWhere = array('empid'=>$row1['empid']);
	// call update function
	$reduceleavesquery = $db->update('emptotalleaves', $reduceleaves, $aWhere)->affectedRows();
	
	//Updating the approval status to "Approved"
	//$updateapprovalstatus=$db->query("UPDATE  empleavetransactions SET  `approvalstatus` =  'Approved',approvalcomments='Approved By (".$_SESSION['u_fullname'].")' WHERE  `transactionid` ='".$transactionid."'");
	$updateOptionalLeave = array('approvalstatus'=>'Approved','approvalcomments'=>'Approved By (".$_SESSION["u_fullname"].")');
	// two where condition array
	$aWhere1 = array('transactionid'=>$transactionid);
	// call update function
	$optionalLeave = $db->update('empleavetransactions', $updateOptionalLeave, $aWhere1)->affectedRows();
		
	$optionalLeaveQuery="select * from empoptionalleavetaken where empid='".$row1['empid']."' and state='Pending'";
	$optionalLeaveResult=$db->pdoQuery($optionalLeaveQuery);
	$optionalLeaveResultrow=$optionalLeaveResult -> count($sTable = 'empoptionalleavetaken', $sWhere = 'empid = "'.$row1['empid'].'" and state = "Pending"' );
	
	if($optionalLeaveResultrow>0) {
		$optionalLeaveRows = $db->pdoQuery($optionalLeaveQuery)->results();
		foreach($optionalLeaveRows as $optionalLeaveRow){
		//while($optionalLeaveRow = $optionalLeaveResult->results()){
			$datesRange=getDatesFromRange($row1['startdate'],$row1['enddate']);
			if(in_array($optionalLeaveRow['date'],$datesRange)) {
				//$updateOptionalLeave="update empoptionalleavetaken set state='Approved',approvalcomments='Approved By (".$_SESSION['u_fullname'].")' where empid='".$row1['empid']."' and date='".$optionalLeaveRow['date']."'";
				//$optionalLeave=$db->query($updateOptionalLeave);

				$updateOptionalLeave = array('splleavetaken'=>$delspl);
				// two where condition array
				$aWhere = array('empid'=>$leavetyperow['empid']);
				// call update function
				$optionalLeave = $db->update('empoptionalleavetaken', $updateOptionalLeave, $aWhere)->affectedRows();
			}
	   }
	}
	
	if($updateapprovalstatus)
	{
		//send mail for Approval status to emp and manager to whom manager approved leave
		$cmd = '/usr/bin/php -f sendmail.php '.$transactionid.' '.$row1['empid'].'  ApproveLeave >> /dev/null &';
		exec($cmd);
		$empnamequery="select empname from emp where state='Active' and empid=".$row1['empid'];
		$empname=$db->pdoQuery($empnamequery);
		$empnamerows=$db->pdoQuery($empnamequery)->results();
		foreach($empnamerows as $empnamerow)
		echo "<script>BootstrapDialog.alert(\"Leave Approved and sending mail\");</script>";
	}
	}
}
if(isset($_REQUEST['notapprove']))
{
	$transactionid=$_REQUEST['tid'];
	$leavetype="SELECT leavetype,empid FROM  `perdaytransactions` WHERE transactionid ='".$transactionid."' AND leavety:qpe !=  'FullDay' AND leavetype !=  'HalfDay' AND leavetype !=  'WFH'";
	$leavetypequery=$db->pdoQuery($leavetype);
	if($leavetypequery) {
		$leavetyperows=$db->pdoQuery($leavetype)->results();
		foreach($leavetyperows as $leavetyperow)
		//Get leave type id from special leaves
		if($leavetyperow) {
			$leavetypeid="select specialleaveid from specialleaves where specialleave LIKE '".$leavetyperow['leavetype']."%'";
			$leavetypeidquery=$db->pdoQuery($leavetypeid)->results();
			foreach($leavetypeidquery as $leavetypeidrow )
			//$leavetypeidrow=$leavetypeidquery->results();
			$splleavetaken="select splleavetaken from  empsplleavetaken where empid='".$leavetyperow['empid']."'";
			$splleavetakenquery=$db->pdoQuery($splleavetaken)->results();
			//$spldelete=$splleavetakenquery->results();
			foreach($splleavetakenquery as $spldelete)
			//Removing the pending leave when deleted the transaction
			$delspl=str_replace("".$leavetypeidrow['specialleaveid']."P:","","".$spldelete['splleavetaken']."");
			//$updatesplleavetakenquery=$db->query("UPDATE  empsplleavetaken SET  `splleavetaken` =  '".$delspl."' where empid='".$leavetyperow['empid']."'");
			$dataArray = array('splleavetaken'=>$delspl);
			// two where condition array
			$aWhere = array('empid'=>$leavetyperow['empid']);
			// call update function
			$updateCompOff = $db->update('empsplleavetaken', $dataArray, $aWhere)->affectedRows();
		}
	}
	$getleaves="SELECT  * FROM empleavetransactions WHERE transactionid ='".$transactionid."'";
	$getleavesquery = $db->pdoQuery($getleaves)->results();
	//$row1=$getleavesquery->results();
	foreach ($getleavesquery as $row1)
	if (preg_match('/CompOff Leave/', $row1['reason'])) {
		$getinoutCompOffquery="select Date from  `inout` where compofftakenday ='".$row1['startdate']."' and empid='".$row1['empid']."'";
		$getinoutCompOff=$db->pdoQuery($getinoutCompOffquery)->results();
		//$getinoutCompOffRow=$getinoutCompOff->results();
		foreach ($getinoutCompOff as $getinoutCompOffRow)
		//$updateCompOff=$db->query("UPDATE `inout` SET compofftakenday='0000-00-00' WHERE empid='".$row1['empid']."' and Date='".$getinoutCompOffRow['Date']."'");

		// update array data
		$dataArray = array('compofftakenday'=>'0000-00-00');
		// two where condition array
		$aWhere = array('empid'=>$row1['empid'],'Date'=>$getinoutCompOffRow['Date']);
		// call update function
		$updateCompOff = $db->update('inout', $dataArray, $aWhere)->affectedRows();
	}
	$comments= mysql_real_escape_string($_REQUEST['comments']);	
	if(empty($comments)) {
		$comments="Cancelled by (".$_SESSION['u_fullname'].")";
	} else {
		$comments=$comments." (Cancelled by ".$_SESSION['u_fullname'].")";
	}
	//$result=$db->query("UPDATE  empleavetransactions SET  `approvalstatus` =  'Cancelled',`approvalcomments` = '".$comments."'  WHERE  `transactionid` ='".$transactionid."'");

	// update array data
	$dataArray = array('approvalstatus'=>'Cancelled','approvalcomments'=>$comments);
	// two where condition array
	$aWhere = array('transactionid'=>$transactionid);
	// call update function
	$result = $db->update('empleavetransactions', $dataArray, $aWhere)->affectedRows();
	if($result)
	{
		echo "<script>BootstrapDialog.alert(\"Not Approved\");</script>";
		//send mail for Not approved status to emp and manager to whom manager not approved leave
		$cmd = '/usr/bin/php -f sendmail.php '.$transactionid.' '.$row1['empid'].'  notApproveLeave >> /dev/null &';
		exec($cmd);
	}
}



$untrackedLeaves=0;

# Generate departments based on the level of the employee
$deps = "<option selected value=\"ALL\">ALL</option>";

if(isset($_SESSION['u_emplocation'])) {
	$defaultLocation=$_SESSION['u_emplocation'];
}

# Departments for HR
if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
	$querydept = "SELECT distinct(dept) FROM `emp`  where location='".$defaultLocation."' ORDER BY dept ASC";
	$resultdept = $db -> pdoQuery($querydept);
	$rows=$db -> pdoQuery($querydept)->results();
} else if (strtoupper($_SESSION['user_desgn']) == 'MANAGER') {
	# Departments for manager
	$deps = "";
	if ($_SESSION['u_managerlevel'] != 'level1') {
		$query = "SELECT distinct(dept) FROM `emp` WHERE managerid='" . $_SESSION['u_empid'] . "' and state='Active' ORDER BY empname ASC";
		//$result = $db -> pdoQuery($query);
		$deps = " <option selected value=\"none\">NONE</option>";
		$deps = $deps." <option value=\"ALL\">ALL</option>";
	} else {
		$query = "SELECT * FROM `emp` WHERE managerid='".$_SESSION['u_empid']."' and state='Active' ORDER BY empname ASC";
		//$result = $db -> pdoQuery($query);
		$deps = " <option selected value=\"ALL\">ALL</option>";
	}
		$rows=$db -> pdoQuery($query)->results();
	if ($_SESSION['u_managerlevel'] != 'level1') {
		//while ($row = $result->results()) {
		foreach($rows as $row){
			$deps = $deps . '<option value="' . $row["dept"] . '">';
			$deps = $deps . $row["dept"];
			$deps = $deps . '</option>';
		}
	} else {
		foreach($rows as $row){
		//while ($row = $result->results()) {
			$deps = $deps . '<option value="' . $row["empid"] . '">';
			$deps = $deps . $row["empname"];
			$deps = $deps . '</option>';
		}
	}
} else {
	# Name of Individual employee
	$query = "SELECT * FROM `emp` WHERE empusername='".$_SESSION['user_name']."' and state='Active'";
	$result = $db -> pdoQuery($query);
	$deps = "";
	$deps = $deps . '<option value="'.$_SESSION["u_empid"].'">';
	$deps = $deps . $_SESSION['u_fullname'];
	$deps = $deps . '</option>';
}

$typeofday = "";
//Department name
$department = '<option value="none">';
$department = $department . "None";
$department = $department . '</option>';
if(isset($resultdept) && $resultdept) {
	$department = $department . '<option value="ALL">';
	$department = $department . "ALL";
	$department = $department . '</option>';
	//while ($row = $resultdept->results()) {
	foreach($rows as $row){
		$department = $department . '<option value="' . $row["dept"] . '">';
		$department = $department . $row["dept"];
		$department = $department . '</option>';
	}
}

?>
		<form id="teamLeaveApprovalId" name="teamLeaveApprovalForm" method="post" action="approveEmpLeave.php?approveEmpLeave=1">
					<div class="panel panel-primary">
						<div class="panel-heading text-center">
							<strong style="font-size:20px;">Pending Leave Information</strong>
						</div>
						<div class="panel-body">
							<?php
							if($_SESSION['user_dept']=="HR") {
								#  Get the distinct locations
								$queryLocation = "SELECT distinct(location) FROM `emp` where location != '' ORDER BY location ASC";
								$resultLocation = $db -> pdoQuery($queryLocation);
								$rows= $db -> pdoQuery($queryLocation)->results();
								$resultLocationrowcount=$resultLocation -> count($sTable = 'emp', $sWhere = 'location != ""' );
				
								# Location selection Box Options
								$locationSelect='';
								if($resultLocationrowcount>0) {
									foreach($rows as $row){
									//while ($row = $resultLocation->results()) {
										if($_SESSION['u_emplocation']==$row['location']) {
											$locationSelect = $locationSelect . '<option value="' . $row["location"] . '" selected>';
											$locationSelect = $locationSelect . $row["location"];
											$locationSelect = $locationSelect . '</option>';
										} else {
											$locationSelect = $locationSelect . '<option value="' . $row["location"] . '">';
											$locationSelect = $locationSelect . $row["location"];
											$locationSelect = $locationSelect . '</option>';
										}
									}
								} 
								echo "<div class='form-group'>
										<div class='row'>
											<div class='col-sm-2'></div>
											<div class='col-sm-3'>
												<label>Select Location:</label>
											</div>
											<div class='col-sm-5'>
												<select class='form-control' id='location' name='location'>
													$locationSelect.'
												</select>
											</div>
											<div class='col-sm-2'></div>
										</div>
									</div>";
							} 
							if (($_SESSION['u_managerlevel'] != 'level1') || ($_SESSION['user_dept'] == 'HR')) {
								if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
									echo '<div class="form-group">
										<div class="row">
											<div class="col-sm-2"></div>
											<div class="col-sm-3">
												<label>Department:</label>
											</div>
											<div class="col-sm-5">
												<select class="form-control" id="hideDept" size="0" name="UDept">
													' . $department . '
												</select>
											</div>
			 								<div class="col-sm-2"></div>
										</div>
			 						</div>';
									echo '<div class="form-group" id="hideName" style="display:none">
										<div class="row">
											<div class="col-sm-2"></div>
											<div class="col-sm-3">
												<label>Employee Name:</label>
											</div>
											<div class="col-sm-5">
												<select class="form-control" id="getEmpName" size="0" name="getDeptemp"></select>
											</div>
											<div class="col-sm-2"></div>
										</div>
									</div>';
								} else {
									echo '<div class="form-group">
										<div class="row">
											<div class="col-sm-2"></div>
											<div class="col-sm-3">
												<label>Department:</label>
											</div>
											<div class="col-sm-5">
												<select class="form-control" id="hideDept" size="0" name="UDept">
													' . $deps . '
												</select>
											</div>
            								<div class="col-sm-2"></div>
										</div>
            						</div>';
									echo '<div class="form-group" id="hideName" style="display:none">
										<div class="row">
											<div class="col-sm-2"></div>
											<div class="col-sm-3">
												<label>Employee Name:</label>
        									</div>
											<div class="col-sm-5">
												<select class="form-control" id="getEmpName" size="0" name="getDeptemp"></select>
											</div>
        									<div class="col-sm-2"></div>
										</div>
        							</div>';
								}
							} else {
								echo '<div class="form-group" id="hideEmpName">
										<div class="row">
											<div class="col-sm-2"></div>
											<div class="col-sm-3">
												<label>Employee Name:</label>
											</div>
											<div class="col-sm-5">
												<select class="form-control" size="0" name="UGroup" id="UGroup">
													'.$deps.'
												</select>
											</div>
											<div class="col-sm-2"></div>
										</div>
									</div>';
							}
							?>
							<div class="form-group">
										<div class="row">
											<div class="col-sm-12 text-center">
												<input type="submit" class="btn btn-success submitBtn" value="Submit" name="TrackAttInd">
											</div>
										</div>
									</div>
							</div>
							</div>
				</form>
				<div id='loadingmessage' style='display:none'>
					<img src='public/images/spinload.jpg'/>
				</div>
<?php

function getEmployeePendingLeaves($dept,$location,$empId) {
	global $db;
	$query="select * from emp where dept='".$dept."' and state='Active'";
	if($location!="") {
		$query=$query." and location = '".$location."'";
	}
	if($empId!="ALL") {
		$query=$query." and empid='".$empId."'";
	}
	$getEmployeesQueryResult=$db -> pdoQuery($query);
	$getEmployeesQueryRow=$getEmployeesQueryResult -> count($sTable = 'emp', $sWhere = 'dept = "'.$dept.'" and state = "Active"' );
	
	$childern=array();
	$results = $db -> pdoQuery($query)->results();
	foreach($results as $result)
	//for ($i=0;$i<$getEmployeesQueryRow;$i++)
	{
		
		array_push($childern,$result['empid']);
	}
	$pendingLeaveCount=0;
	$count=0;
	$pendingLeaves=0;
	
	echo "<h3>".$dept."</h3>";					//	Accordian Heading
	echo "<div>";								// Accordian Body
	echo '<table class="table table-hover teamapprove">
             <thead>';
         	echo '<tr class="info displayRow">
                    <th>Employee Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Count</th>
                    <th>Reason</th>
                    <th>Approval Status</th>
                    <th></th>
                 </tr>
               </thead>
               <tbody>';
	foreach ($childern as $empID) {
		if($empID==$_SESSION['u_empid']) {
			continue;
		}
		$query="select id,transactionid,startdate,enddate,count,reason,approvalstatus,approvalcomments from empleavetransactions where empid='".$empID."' and approvalstatus='Pending'";
		$sql=$db->pdoQuery($query);
		$empquery="select empname from emp where state='Active' and empid=".$empID;
		$empnamequery =$db->pdoQuery($empquery)->results();
		foreach($empnamequery as $emprow)
			$rows=$db->pdoQuery($query)->results();
		//$emprow=$empnamequery->results();
		//for($x=0;$x<$sql->rowCount();$x++)
			foreach($rows as $row)
		{
			$pendingLeaveCount=$pendingLeaveCount+1;
			echo '<tr class="displayRow"><td>'.$emprow['empname'].'</td>';
			echo '<td>'.$row['startdate'].'</td>';
			echo '<td>'.$row['enddate'].'</td>';
			echo '<td>'.$row['count'].'</td>';
			echo '<td>'.$row['reason'].'</td>';
			echo '<td>'.$row['approvalstatus'].'</td>';
			echo '<td><div class="arrow"></div></td></tr>';
			$query1="select * from perdaytransactions where transactionid='".$row['transactionid']."'";
			$sql1=$db->pdoQuery($query1)->results();
			//$row1=$sql1->results();
			echo '<tr class=hideRow>
                	  <td colspan="7">
                           <table class="table table-hover">
                                <tr class="info">
                                   <th>Date</th>
                         	       <th>Leave Type</th>
	                               <th>Shift</th>
        	                    </tr>';
									//for($j=0;$j<$sql1->rowCount();$j++)
										foreach($sql1 as $row1)
									{
										//$row1=$sql1->results();
										echo '<tr></tr><tr><td>'.$row1['date'].'</td>';
										echo '<td>'.$row1['leavetype'].'</td>';
										echo '<td>'.$row1['shift'].'</td>';
									}
								echo '<tr></tr>';
								echo '<tr>
										<td><button class="btn btn-danger" onclick="notapprove'.$count.'()">Not Approve</button></td>
										<td><button class="btn btn-success" onclick="approve(\''.$emprow['empname'].'\',\''.$row['transactionid'].'\')">Approve</button></td>
									</tr>';
							echo '</table>';
							echo '<div class="form-group">
								<div class="row"><div id="comments'.$count.'">
									 <div class="col-sm-5">
           								<textarea class="form-control" id=txtMessage'.$count.' placeholder="Write Comments for not approving"></textarea>
									</div>	
           							<div class="col-sm-2">				  
           								<button class="btn btn-primary" onclick="submitcomments(\''.$emprow['empname'].'\',\''.$row['transactionid'].'\','.$count.')">OK</button>
									</div>
									<div class="col-sm-5"></div>
								</div>
								</div>	
								</div>';
						echo '</td>';
			echo '</tr>';
			echo '<tr></tr>';
	
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
	echo "</tbody></table>";
	echo "<table class='table' id='table-2'>
			<tr style='text-align:right;'>
				<td><b>Total Pending Leave Applications</b></td>
				<td class='teamPendingLeave'><b>".$pendingLeaveCount."</b></td>
			</tr></table>";
	echo "</div>";
}

if (isset($_REQUEST['approveEmpLeave'])) {
		// Gather information
		if(isset($_REQUEST['UGroup'])) {
                $grp = $_REQUEST['UGroup'];
        }
        if (isset($_REQUEST['getDeptemp'])) {
                $getDeptemp = $_REQUEST['getDeptemp'];
        } else {
                $getDeptemp = "";
        }
        if (isset($_REQUEST['UDept'])) {
                $getDept = $_REQUEST['UDept'];
        } else {
                $getDept = "";
        }
        if (!isset($_REQUEST['location'])) {
        	$_REQUEST['location']="";
        } 
        echo '<script>
                 $(".ui-dialog").remove();
              </script>';
echo "<div id='untrackedLeaveData'>";
        echo "<div class='panel panel-primary'>
						<div class='panel-heading text-center'>
							<strong style='font-size:20px;'>Pending Leave Information details for location: ".$_REQUEST['location']." </strong>";
        
        if (isset($_REQUEST['getDeptemp'])) {
                echo getempName($_REQUEST['getDeptemp']);
        }
        if (isset($_REQUEST['UDept'])) {
                echo " (" . $_REQUEST['UDept'] . ")</div>";
        } else {
		echo "</div>";
	}
	echo "<div class='panel-body'>";
	
        
		# Gather employees based on employee leavel
		
		# Gather employees if employee is HR
        if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
                if ($getDept == 'ALL') {
                        $query = "SELECT distinct(dept) FROM `emp`  where location = '".$_REQUEST['location']."' ORDER BY `dept` ASC";
                        $self = 1;
                } elseif ($getDeptemp == 'ALL') {
                        $query = "SELECT * FROM emp WHERE dept='" . $getDept . "'  and location = '".$_REQUEST['location']."' and state='Active'";
                        $self = 1;
                } elseif ($getDept == $_SESSION['u_empid']) {
                        $query = "SELECT * FROM emp WHERE `empid` = '" .$_SESSION['u_empid']. "' and state='Active'  and location = '".$_REQUEST['location']."' ORDER BY `emp`.`empname` ASC";
                } else {
                        $query = "SELECT * FROM `emp` WHERE `empid` = '".$_REQUEST['getDeptemp']."' and state='Active'  and location = '".$_REQUEST['location']."' ORDER BY `emp`.`empname` ASC";
                }
                $result = $db -> pdoQuery($query);
                $rows=$db -> pdoQuery($query)->results();
                if ($getDept == 'ALL') {
				echo '<div id="accordion-new">';			        
                      //  while ($row = $result->results()) {    
                      foreach($rows as $row){                     
                                getEmployeePendingLeaves($row['dept'],$_REQUEST['location'],$_REQUEST['getDeptemp']);
                        }
				echo "</div>";
                } else {
                        if ($getDeptemp == 'ALL') {
                        	echo '<div id="accordion-new">';
                        	getEmployeePendingLeaves($getDept,$_REQUEST['location'],$_REQUEST['getDeptemp']);
                        	echo "</div>";
                        } else {
                        	echo '<div id="accordion-new">';
                        	getEmployeePendingLeaves($getDept,$_REQUEST['location'],$_REQUEST['getDeptemp']);
                        	echo "</div>";
                        }
                }
    	} else if (strtoupper($_SESSION['user_desgn']) == 'MANAGER') {
		# Gather employee list if employee is a manager
                if ($getDept == 'ALL') {
                        $query = "SELECT distinct(dept) FROM emp WHERE managerid='".$_SESSION['u_empid']."' and state='Active'";
                        $self = 1;
                } elseif ($getDeptemp == 'ALL') {
                        $query = "SELECT distinct(dept) FROM emp WHERE dept='".$getDept."' and state='Active'";
                } elseif ($getDept == $_SESSION['u_empid']) {
                        $query = "SELECT distinct(dept) FROM emp WHERE `empid` = '".$_SESSION['u_empid']."' and state='Active' ORDER BY `emp`.`empname` ASC";
                } else {
                        if ($_SESSION['u_managerlevel'] != 'level1') {
                                $query = "SELECT distinct(dept) FROM emp WHERE `empid` = '".$_REQUEST['getDeptemp']."' and state='Active' ORDER BY `emp`.`empname` ASC";
                        } else {
                                if($grp=="ALL") {
                                        $query = "SELECT distinct(dept) FROM emp WHERE managerid='".$_SESSION['u_empid']."' and state='Active'";
                                } else {
                                        $query = "SELECT distinct(dept) FROM emp WHERE `empid` = '".$grp."' and state='Active'  ORDER BY `emp`.`empname` ASC";
                                }
                        }
                }
                $result = $db -> pdoQuery($query);
                $rows= $db -> pdoQuery($query)->results();
                if ($getDept == 'ALL') {
                	echo '<div id="accordion-new">';			        
                   // while ($row = $result->results()) { 
                   foreach($rows as $row){                        
                    	getEmployeePendingLeaves($row['dept'],$_REQUEST['location'],$_REQUEST['getDeptemp']);
                    }
					echo "</div>";
                } elseif ($getDeptemp == 'ALL') {
                	echo '<div id="accordion-new">';
                	// while ($row = $result->results()) {  
                	foreach($rows as $row){
                    	getEmployeePendingLeaves($row['dept'],$_REQUEST['location'],$_REQUEST['getDeptemp']);
                    }
                	echo "</div>";
                } elseif ($getDept == $_SESSION['u_empid']) { 
                	echo '<div id="accordion-new">';
                	//while ($row = $result->results()) {
                	foreach($rows as $row){
                		getEmployeePendingLeaves($row['dept'],$_REQUEST['location'],$_SESSION['u_empid']);
                	}
                	echo "</div>";
                } else {
                	if ($_SESSION['u_managerlevel'] != 'level1') {
                		echo '<div id="accordion-new">';
                		//while ($row = $result->results()) {
                		foreach($rows as $row){
                			getEmployeePendingLeaves($row['dept'],$_REQUEST['location'],$_REQUEST['getDeptemp']);
                		}
                		echo "</div>";
                	} else {
                		if($grp=="ALL") {
                			echo '<div id="accordion-new">';
                			//while ($row = $result->results()) {
                			foreach($rows as $row){
                				getEmployeePendingLeaves($row['dept'],$_REQUEST['location'],$grp);
                			}
                			echo "</div>";
                		} else {
                			echo '<div id="accordion-new">';
                			//while ($row = $result->results()) {
                			foreach($rows as $row){
                				getEmployeePendingLeaves($row['dept'],$_REQUEST['location'],$grp);
                			}
                			
                		}
                	}
                }
       } 
       echo "</div>";
       echo "</div></div>";
      // $db -> closeConnection();
}
?>

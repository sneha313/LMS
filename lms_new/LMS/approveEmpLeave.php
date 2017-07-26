<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
?>
<html>
<head>
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
                url: "approveEmpLeave.php", 
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
</head>
<body>
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

echo '<form action="approveEmpLeave.php" method="POST" id="getemptrans">
                        <table id="table-2"> 
                        <tr>    
                                <td><p><label>Enter Employee Name:</label></p></td>
                                <td><p><input id="empuser" type="text" name="empuser" value="'.$empName.'"/></p></td>';
                echo '<td><input class="submit" type="submit" name="submit" value="SUBMIT"/></td>
                        </tr>   
                        </table>
                </form></body></html>';
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
		echo "</tbody></table>";
		echo "</body></html>";
}	 
?>

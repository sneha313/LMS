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
	if(isset($_REQUEST['approveview']))
	{
		$childern=getChildren($_SESSION['u_empid']);
		if($_SESSION['roleofemp']=="hr") {
			$join1="select a.empname, a.empid, b.wfhHrs, b.status, b.date, b.reason, b.tid from emp a, extrawfh b where b.status='pending' and a.empid=b.eid and a.empname in(SELECT empname FROM emp where state='Active') order by a.empname desc";
		} else {
			$join1="select a.empname, a.empid, b.wfhHrs, b.status, b.date, b.reason, b.tid from emp a, extrawfh b where b.status='pending' and a.empid=b.eid and a.empname in(SELECT empname FROM emp where state='Active' and managerid='".$_SESSION['u_empid']."')order by a.empname";
		}
		$sql1=$db->query($join1);
			echo "<div id='showtable'><table id=\"table-2\" width='70%'>
				<form method='POST' action='' id='WFH' name='ExtraWFHHour'>
				<tr>
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
							<td><div id="approve" title="'.$row['tid'].'" onclick=approveExtrawfh("'.$row['tid'].'") class="'.$row['empid'].'"><font color="blue">Approve</font></div>
		 				<div id="notApprove" title="'.$row['tid'].'" onclick=notapproveExtrawfh("'.$row['tid'].'") class="'.$row['empid'].'"><font color="blue">Not Approve</font></div></td>
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
			
			echo "<div id='showtable'><table id=\"table-2\" width='70%'>
				<form method='POST' action='' id='WFH' name='ExtraWFHHour'>
				<tr>
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
		<table id="table-2" width="50%">
			<caption>Not Approve Extra WFH Hour by Manager</caption>			
				<tr><td>
					<label>Employee Id&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
					<input type="text" name="empid" id="empid" value="<?php echo $empid; ?>" readonly>
				</td></tr>
				<tr><td>
					<label>Number of Hour&nbsp;&nbsp;</label>
					<input type="text" name="wfhHrs" id="wfhHrs" value="<?php echo $noh;?>" >
				</td></tr>
				<tr><td>
					<label>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
					<input type="text" name="fromdate" id="fromdate" value="<?php echo $date; ?>" readonly>
				</td></tr>
				<tr><td>
					<label>comments&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
					<textarea name="commentsform" id="commentsform" value=""></textarea>
				</td></tr>
				<tr><td>
				 	<input type="submit" id="notapprove" name="notapprove" value="notapprove">
				 	
				</tr>
				<tr><td> <input type="hidden" id="tid" name="tid" value="<?= $tid ?>" ></td></tr>
		   </table>
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
			
		echo "<div id='showtable'><table id=\"table-2\" width='70%'>
				<form method='POST' action='' id='WFH' name='ExtraWFHHour'>
				<tr>
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
	           <table id="table-2"> 
	               <tr>    
	                   <td><p><label>Enter Employee Name:</label></p></td>
	                   <td><p><input id="empuser" type="text" name="empuser" value="'.$empName.'"/></p></td>';
	                echo '<td><input class="submit" type="submit" name="submit" value="SUBMIT"/></td>
	               </tr>   
	           </table>
	      </form>';
	}

			echo "</body></html>";	 

?>
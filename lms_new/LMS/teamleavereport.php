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
function empHistory($empid,$query){
	global $db;
	global $_REQUEST;
	$leaveTypeCount=0;
	$allCount=0;
	if($_REQUEST['leaveType']=="ALL")
	{
		echo "<table class=\"table-1\" width='70%'>
				<tr>
					<th width='20%'>Start Date</th>
					<th width='20%'>End Date</th>
					<th>PTO's Taken</th>
					<th width='40%'>Reason</th>
					<th width='40%'>Status</th>
					<th width='40%'>Comments</th>
					<th></th>
				</tr><tr></tr>";
	} else {
		echo "<table class=\"table-1\" width='70%'>
				<tr>
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
		echo "<table id='table-2'>
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
</body>
</html>

<?php
	session_start();
	//include 'Library.php';
	include 'librarycopy1.php';
	$db=connectToDB();
?>
<html>
<head>
	<style>
		td{
			font-size:15px;
		}
	</style>
</head>
<body>
<div class="col-sm-12">
<div class="panel panel-primary">
			<div class="panel-heading text-center">
				<strong style="font-size:20px;">Balance Leaves</strong>
			</div>
			<div class="panel-body">
<?php 
	if(isset($_REQUEST['getdetailedleaves']))
	{
		$query="select * from emptotalleaves where empid='".$_SESSION['u_empid']."'";
		//$result=$db->query($query);
		//$row=$db->fetchAssoc($result);
		$rows= $db->pdoQuery($query)->results();
		foreach($rows as $row)
		// Get balance leaves for present year
		$actualBalanceLeaves=$row['balanceleaves'];
		$thismonth = date("m");
		$thisday = date("d");
		$noOfLeavesUptoPreviousMonth=($thismonth-1)*2.08;
		if($thisday < 15 ) {
			$noOfLeavesUptoPreviousMonth=ceil($noOfLeavesUptoPreviousMonth);
			$noOfLeavesUptocurrentMonth=$noOfLeavesUptoPreviousMonth+1;
		} else {
			$noOfLeavesUptocurrentMonth=$noOfLeavesUptoPreviousMonth+2.08;
			$noOfLeavesUptocurrentMonth=ceil($noOfLeavesUptocurrentMonth);
		}
	echo '<table class="table table-hover table-bordered" class="display">
			<thead>
				<tr class="info">
					<th>Status of Leave Balance</th>
					<th>No.of Leaves</th>
				</tr>
			</thead>
			<tbody>
				 <tr>
				  	<td>Carry Forwarded '.date("Y",strtotime("-1 year")).':</td>
					<td>'.$row['carryforwarded'].'</td>
				</tr>
				<tr>
				  	<td>Remaining Leaves '.date("Y").':</td>
					<td>'.$row['balanceleaves'].'</td>
				</tr>
				 <tr>
				  	<td>Total Leaves for '.date("Y").':</td>
				  	<td>'.($row['carryforwarded']+$row['balanceleaves']).'</td>
				 </tr>';

	echo "</tbody></table>";
}
?>
</div></div>
<?php 
if(isset($_REQUEST['getleaves']))
{
	$query="select * from emptotalleaves where empid='".$_SESSION['u_empid']."'";
	//$result=$db->query($query);
	//$row=$db->fetchAssoc($result);
	$rows= $db->pdoQuery($query)->results();
	foreach($rows as $row)
	echo ($row['carryforwarded']+$row['balanceleaves']);
}
?>
</div>
</body></html>
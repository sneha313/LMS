<?php
session_start();
include 'Library.php';
$db=connectToDB();
if(isset($_REQUEST['getdetailedleaves']))
{
	$query="select * from emptotalleaves where empid='".$_SESSION['u_empid']."'";
	$result=$db->query($query);
	$row=$db->fetchAssoc($result);
	
	
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
	echo '<table  id="table-2"  style="font-size: 2px" cellpadding="0" cellspacing="0" border="1"  class="display">
			<thead>
				<tr>
					<th><font size="2">Status of Leave Balance</font></th>
					<th><font size="2">No.of Leaves</font></th>
				</tr>
			</thead>
			<tbody>
				 <tr>
				  	<td>Carry Forwarded 2016:</td>
					<td>'.$row['carryforwarded'].'</td>
				</tr>
				<tr>
				  	<td>Current Year 2017:</td>
					<td>'.$row['balanceleaves'].'</td>
				</tr>
				 <tr>
				  	<td>Total Leaves for '.date("Y").':</td>
				  	<td>'.($row['carryforwarded']+$row['balanceleaves']).'</td>
				 </tr>';

	echo "</tbody></table>";
}
if(isset($_REQUEST['getleaves']))
{
	$query="select * from emptotalleaves where empid='".$_SESSION['u_empid']."'";
	$result=$db->query($query);
	$row=$db->fetchAssoc($result);
	echo ($row['carryforwarded']+$row['balanceleaves']);
}
?>

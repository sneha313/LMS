<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
$query=$_REQUEST['query'];
if(isset($_REQUEST['dept'])) {
	$team=$_REQUEST['dept'];
}
if(isset($_REQUEST['export2']))
{
	$startdate=$_REQUEST['startdate'];
	$todate=$_REQUEST['enddate'];
	$table="";
	header("Content-type: application/x-msdownload");
	# replace excelfile.xls with whatever you want the filename to default to
	header('Content-Disposition: attachment; filename="LeaveReport_Detailed (From_'.$startdate.'_and_'.$todate.').xls"');
	header("Pragma: no-cache");
	header("Expires: 0");
	$result2=$db->query($query);
	$table .="<h3><u>Leave data from ".$startdate." to ".$todate." for ".$team."</u></h3>";
	$table .="<br>";
	$table .= '<table class="table"><tbody>';
	for($x=0;$x<$db->countRows($result2);$x++)
	{
		$row2=$db->fetchAssoc($result2);
		$query3="SELECT empname FROM `emp` WHERE empid=".$row2['empid'];
		$result3=$db->query($query3);
		$row3=$db->fetchAssoc($result3);
		$result6=$db->query("SELECT balanceleaves,carryforwarded FROM `emptotalleaves` WHERE empid=".$row2['empid']);
		$row6=$db->fetchAssoc($result6);
		$table.='<tr style="background-color:#F0D8D8;color:#000000;font:bold;"><td>'.$row3['empname'].'('.$row2['empid'].')
				  Balance Leaves: '.($row6['balanceleaves']+$row6['carryforwarded']).'</td></tr>';
		$query4="SELECT empleavetransactions.empid,  empleavetransactions.startdate, empleavetransactions.enddate, empleavetransactions.count,
			empleavetransactions.reason FROM  empleavetransactions WHERE empleavetransactions.empid =".$row2['empid']." and approvalstatus = 'Approved' 
			AND startdate BETWEEN '".$startdate."' AND '".$todate."'";
		$table.='<tr><td><table class="table table-bordered table-striped">
	    <tbody>
	    <tr class="info">
	  	<th>Start Date</td>
	  	<th>End Date</td>
	  	<th>Days Taken</td>
	  	<th>Reason</th>
	    </tr>';
		$result4=$db->query($query4);
		$allCount=0;
		for($i=0;$i<$db->countRows($result4);$i++)
		{
			$row4=$db->fetchAssoc($result4);
			$allCount=$allCount+$row4['count'];
			$table.='<tr>';
			$table.='<td class="warning">'.$row4['startdate'].'</td>';
			$table.='<td class="danger">'.$row4['enddate'].'</td>';
			$table.='<td class="success">'.$row4['count'].'</td>';
			$table.='<td class="warning">'.$row4['reason'].'</td></tr>';
		}
		$table.="<tr style='background-color:#FF8000;color:#000000;'><td colspan=4><b style='float:right;'>Total Approved leaves = ".$allCount."</b></td></tr>";
		$table.="<tr></tr>";
		$table.='</tbody></table></td></tr>';

	}
	echo $table;
}
if(isset($_REQUEST['export1']))
{
	$table="";
	header("Content-type: application/x-msdownload");
	# replace excelfile.xls with whatever you want the filename to default to
	header("Content-Disposition: attachment; filename=LeaveReport_Brief.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	$result=$db->query($query);
	$table .= '<table class="table table-bordered"><tbody>
	  <tr class="info">
	  	<th>Emp Name</th>
	  	<th>Balance Leaves</th>
	  </tr>';
	for($i=0;$i<$db->countRows($result);$i++)
	{
		$row=$db->fetchAssoc($result);
		$table.='<tr>';
		$table.='<td class="warning">'.$row['name'].'</td>';
		$table.='<td class="danger">'.($row['balanceleaves']+$row['carryforwarded']).'</td></tr>';
	}
	$table.='</tbody></table>';
	echo $table;
}

<?php
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
$db = connectToDB();

function removeEmpfromList($empList,$empid){
	$x['324122']=array("420064");
	$x['123456']=array("420064");
	$x['420064']=array("324122","123456");
	$anil=$x[$empid];
	$empList=explode(",", $empList);
	$returnArray=array_diff($empList,$x[$empid]);
	return $returnArray;
}
if (isset($_REQUEST['getEmpData'])) {
	if (isset($_SESSION['start']) && isset($_SESSION['end'])) {
		$arr = array();
		$startDate = $_SESSION['start'];
		$endDate = $_SESSION['end'];
		if (strtoupper($_SESSION['user_desgn']) == "MANAGER") {
			$managerid = $_SESSION['u_empid'];
			$empquery = "select empid from emp where managerid='".$managerid."' and state='Active' union select empid from emp where managerid='".$_SESSION['u_managerid']."' and state='Active'";
			$employeeListString = getempListString($empquery);
			if($_SESSION['u_empid']=="420064") {
				$employeeListArray=removeEmpfromList($employeeListString,$_SESSION['u_empid']);
				$employeeListString=join(",", $employeeListArray);
			}
			$query = "SELECT a.empid,a.leavetype,a.date FROM perdaytransactions a,empleavetransactions b  
						where a.date between '$startDate' and '$endDate'  
						and a.empid in ($employeeListString) and a.transactionid=b.transactionid and b.approvalstatus='Approved' group by a.date,a.empid";
		}
		if (strtoupper($_SESSION['user_desgn']) == "USER") {
			$managerid = $_SESSION['u_managerid'];
			$empquery = "select empid from emp where managerid='".$managerid."' and state='Active'";
			$employeeListString = getempListString($empquery);
			if($_SESSION['u_empid']=="324122" || $_SESSION['u_empid']=="123456") {
				$employeeListArray=removeEmpfromList($employeeListString,$_SESSION['u_empid']);
				$employeeListString=join(",", $employeeListArray);
			}
			$query = "SELECT a.empid,a.leavetype,a.date FROM perdaytransactions a,empleavetransactions b 
					where a.date between '$startDate' and '$endDate' and a.empid in ($employeeListString) 
					and a.transactionid=b.transactionid and b.approvalstatus='Approved' group by a.date,a.empid";
		}
		$sql = $db -> query($query);
		while ($row = $db -> fetchArray($sql)) {
			$empName = getempName($row['empid']);
			$bus = array('name' => $empName, 'leavetype' => $row['leavetype'], 'date' => $row['date']);
			array_push($arr, $bus);
		}
		echo json_encode($arr);
	}
} else {
	if (isset($_REQUEST['start']) && isset($_REQUEST['end'])) {
		$startDate = date("Y-m-d", $_REQUEST['start']);
		$endDate = date("Y-m-d", $_REQUEST['end']);
		$_SESSION['start']=$startDate;
		$_SESSION['end']=$endDate;
		$parallelEmpids = array();
		if (strtoupper($_SESSION['user_desgn']) == "MANAGER") {
			$managerid = $_SESSION['u_empid'];
			$empquery = "select empid from emp where managerid='".$managerid."' and state='Active' union select empid from emp where managerid='".$_SESSION['u_managerid']."' and state='Active'";
			$employeeListString = getempListString($empquery);
			if($_SESSION['u_empid']=="420064") {
				$employeeListArray=removeEmpfromList($employeeListString,$_SESSION['u_empid']);
				$employeeListString=join(",", $employeeListArray);
			}
			$query = "SELECT a.empid,a.date FROM perdaytransactions a, empleavetransactions b where a.date between 
				'$startDate' and '$endDate' and a.empid in ($employeeListString) and a.transactionid=b.transactionid 
				and b.approvalstatus='Approved' group by a.date,a.empid";
			$sql = $db -> query($query);
			$colorQuery = $db -> query("select empid from emp where managerid='".$_SESSION['u_managerid']."' and state='Active'");
			while ($row = $db -> fetchArray($colorQuery)) {
				array_push($parallelEmpids, $row['empid']);
			}

		}
		if (strtoupper($_SESSION['user_desgn']) == "USER") {
			$managerid = $_SESSION['u_managerid'];
			$empquery = "select empid from emp where managerid='".$managerid."' and state='Active'";
			$employeeListString = getempListString($empquery);
			if($_SESSION['u_empid']=="324122" || $_SESSION['u_empid']=="123456") {
				$employeeListArray=removeEmpfromList($employeeListString,$_SESSION['u_empid']);
				$employeeListString=join(",", $employeeListArray);
				
			}
			$query = "SELECT a.empid,a.date FROM perdaytransactions a,empleavetransactions b where a.date between 
				'$startDate' and '$endDate' and a.empid in ($employeeListString) 
				 and a.transactionid=b.transactionid and b.approvalstatus='Approved' group by a.date,a.empid";
			$sql = $db -> query($query);
		}
		$count = 1;
		$arr = array();
		while ($row = $db -> fetchArray($sql)) {
			if (in_array($row['empid'], $parallelEmpids)) {
				$color = "#FE642E";
			} else {
				$color = "#045FB4";
			}

			$empName = getempName($row['empid']);
			$start = strtotime($row['date']);
			$end = strtotime($row['date']); ;
			$bus = array('id' => $count, 'title' => $empName, 'start' => $start, 'end' => $end, 'color' => $color);
			array_push($arr, $bus);
			$count = $count + 1;
		}
		echo json_encode($arr);
	}

}
?>

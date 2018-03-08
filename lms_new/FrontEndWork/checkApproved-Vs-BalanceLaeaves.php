<?php

# Connect to database
/*$con = mysql_connect("localhost","root","eci12Telecom");
if (!$con)
{
  die('Could not connect: ' . mysql_error());
}
$db_selected =  mysql_select_db("lms",$con);
if ($db_selected) {
        echo "Connected to database\n";
} else {
        echo "Not Connected to database\n";
}*/

session_start();
//require_once 'Library.php';
//require_once 'generalFunctions.php';
require_once 'librarycopy1.php';
require_once 'generalcopy.php';
$db = connectToDB();

# Get all the bangalore employees
$empQuery="select * from emp where  state='Active' and location='BLR'";
$empQueryResult=$db->pdoQuery($empQuery)->results();
$year="2017";
$startOfYear="$year-01-01";
$totalLeavesGivenToEmpPerYear="25";
foreach($empQueryResult as $empQueryRow) {
	# Get the total Approved leaves
	$tidQuery="select * from empleavetransactions where empid='".$empQueryRow['empid']."' and startDate between '".$year."-01-01' and '".$year."-12-31' and (approvalstatus='Approved') order by startDate";
	$tidQueryResult=$db->pdoQuery($tidQuery)->results();
	$totalApprovedLeaves=0;
       // for($j=0;$j<count($tidQueryResult);$j++)
       	foreach($tidQueryResult as $row)
        {
                $totalApprovedLeaves=$totalApprovedLeaves+$row['count'];
        }

	# Get the balance leaves for present year
	$perQuery="select * from emptotalleaves where empid='".$empQueryRow['empid']."' and `present year`='".$year."'";
	$perQueryResult=$db->pdoQuery($perQuery);
	$perQueryRows=$db->pdoQuery($perQuery)->results();
	foreach($perQueryRows as $perQueryRow)
	$balanceLeaves=$perQueryRow['balanceleaves'];
	$usedLeaves=$totalLeavesGivenToEmpPerYear-$balanceLeaves;	

	# Get the actual leave count based on joining date for employees who joined in this year. 
	# This is not equal to $totalLeavesGivenToEmpPerYear i.e 25, it is calculated based on joining date
	$leaveCountForNewEmployees=0;
	if($empQueryRow['joiningdate'] >= $startOfYear) {
    	$joiningmonth = date("m",strtotime($empQueryRow['joiningdate']));
       	$joiningday = date("d",strtotime($empQueryRow['joiningdate']));
      	$joiningyear = date("Y",strtotime($empQueryRow['joiningdate']));
    	$remainingmonths = 12 - $joiningmonth;
      	$leaveCountForNewEmployees=$remainingmonths * (2.0);
        # Add 2 Leaves to the employee, if the employee joined day is less than 15th of that month
        if($joiningday<15) {
         	$leaveCountForNewEmployees=$leaveCountForNewEmployees+2.0;
       	} else {
        	# Else add one leave only
            $leaveCountForNewEmployees=$leaveCountForNewEmployees+1;
       	}
        #  Since the user will get 25 leaves, so add one more leave to the balance leave
                $leaveCountForNewEmployees=$leaveCountForNewEmployees+1;
                $leaveCountForNewEmployees=ceil($leaveCountForNewEmployees);	
	}
	
	if($leaveCountForNewEmployees>0) {
		# Get used leaves for new employees who joined in this year
		$usedLeaves=$leaveCountForNewEmployees-$balanceLeaves;
	//	$updateBalanceLeavesQuery="UPDATE  `lms`.`emptotalleaves` SET  `totalLeavesAtStarting` =  '".$leaveCountForNewEmployees."' ".
      //          "WHERE  `emptotalleaves`.`empid` ='".$empQueryRow['empid']."'";
		$dataArray = array('totalLeavesAtStarting'=>$leaveCountForNewEmployees);
		// two where condition array
		$aWhere = array('empid'=>$empQueryRow['empid']);
		// call update function
		$updateBalanceLeavesQuery = $db->update('emptotalleaves', $dataArray, $aWhere)->affectedRows();
		
                echo $updateBalanceLeavesQuery."\n";
#                $updateBalanceLeavesResult=mysql_query($updateBalanceLeavesQuery,$con);
	} else {

		$dataArray = array('totalLeavesAtStarting'=>$totalLeavesGivenToEmpPerYear);
		// two where condition array
		$aWhere = array('empid'=>$empQueryRow['empid']);
		// call update function
		$updateBalanceLeavesQuery = $db->update('emptotalleaves', $dataArray, $aWhere)->affectedRows();
		//$updateBalanceLeavesQuery="UPDATE  `lms`.`emptotalleaves` ".
		//"SET `totalLeavesAtStarting`='".$totalLeavesGivenToEmpPerYear."' WHERE  ".
		//"`emptotalleaves`.`empid` ='".$empQueryRow['empid']."'";
                echo $updateBalanceLeavesQuery."\n";
#		$updateBalanceLeavesResult=mysql_query($updateBalanceLeavesQuery,$con);
	}
	
	# If usedLeaves is not matching the totalApprovedLeaves, then update the emptotalleaves table accordingly
	if($usedLeaves!=$totalApprovedLeaves) {
		echo "\n---------------------------------------------------------------------------------------------\n";
		echo "\nFor Empid: ".$empQueryRow['empid']." UserName: ".$empQueryRow['empusername']." Employee: ".$empQueryRow['empname']." not matched\n";	
		if($leaveCountForNewEmployees>0) {
			# This is for new employee who joined in this year
			echo "Total LeaveCount For New Employees: ".$leaveCountForNewEmployees."\n";
			echo "Joining Date: ".$empQueryRow['joiningdate']."\n";
			$correctLeaves=$leaveCountForNewEmployees-$totalApprovedLeaves;
		} else {
			# This is for old employees
			$correctLeaves=$totalLeavesGivenToEmpPerYear-$totalApprovedLeaves;
		}
		echo "Current Leaves Count: ".$balanceLeaves."\n";
		echo "Correct Leaves Count: ".$correctLeaves."\n";
		//$updateBalanceLeavesQuery="UPDATE  `lms`.`emptotalleaves` SET  `balanceleaves` =  '".$correctLeaves."' ".
		//"WHERE  `emptotalleaves`.`empid` ='".$empQueryRow['empid']."'";

		$dataArray = array('balanceleaves'=>$correctLeaves);
		// two where condition array
		$aWhere = array('empid'=>$empQueryRow['empid']);
		// call update function
		$updateBalanceLeavesQuery = $db->update('emptotalleaves', $dataArray, $aWhere)->showQuery()->affectedRows();
		echo $updateBalanceLeavesQuery."\n";
#		$updateBalanceLeavesResult=mysql_query($updateBalanceLeavesQuery,$con);			
#		if($updateBalanceLeavesResult) {
#			echo "Updated successfully\n";
#		}
	}
}
?>

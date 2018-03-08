<?php
//require_once ("db.class.php");
//require_once ("Library.php");
//require_once("LMSConfig.php");

require_once 'librarycopy1.php';
require_once 'generalcopy.php';
#############################################################################################################
# Database Connection
##############################################################################################################
/*try {
	$pdo = new PDO("mysql:host=$DBHostIP;dbname=$DBName", $DBUser, $DBPassword, array(
                         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                         PDO::ATTR_EMULATE_PREPARES => false ));
	if($pdo) {
		echo "Connection is successful";
	}
} catch(Exception $e) {
	 echo("Can't open the database.->". $e->getMessage());
}*/

$db=connectToDB();
###############################################################################################################
# Automatic Leave Deduction Script
# 	--> Deduct leaves for a given Emp ID (--empId)
#	--> Deduct leaves for all Employees of given sub-department		   (--subDept)
#	--> Deduct leaves for all Employees of given Main-department		   (--mainDept)
# 	--> Deduct leaves for all Employees of Bangalore location	  	   (--All) 
#	--> Deduct leaves for all Employees of Mumbai location             	   (--All --location=MUM)
#	--> To date information, By default it takes present day		   (--toDate=2017-09-01)
#	--> From Date information, By default it takes start of the year           (--fromDate=2017-01-01)
#	--> Notifying the deducted leaves through mail.	
###############################################################################################################


function helpText() {
	echo "Script Usage:\n";
        echo "\t\t empId: Deduct leaves for given Emp ID\n\n";
        echo "\t\t subDept: Deduct leaves for all Employees of given sub-department\n\n";
        echo "\t\t mainDept: Deduct leaves for all Employees of given Main-department\n\n";
        echo "\t\t All: Deduct leaves for all Employees (By default it takes Bangalore location)\n\n";
        echo "\t\t mailList: To send automatic leave deduction information for given Employee mailID, otherwise mail will be send to respective employee\n\n";
        echo "\t\t toDate: To date information, By default it takes present day\n\n";
        echo "\t\t fromDate: From Date information, By default it takes start of the year\n\n";
        echo "\t\t location: location name, By default BLR\n\n";
        echo "\t\t Example:\n\t\t----------------------------------------------------------------------------------------------------------\n\n";
	echo "\t\t php FindDanglingEmpLeaveTransaction.php --empId=323156 --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t php FindDanglingEmpLeaveTransaction.php --subDept=Infrastructure --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t php FindDanglingEmpLeaveTransaction.php --mainDept=AIBI --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t php FindDanglingEmpLeaveTransaction.php --All --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
        exit;
}
function getempMailId($empid)
{
        global $db;
	$empEmailQuery="select emp_emailid from emp where empid=$empid and state='Active'";
	$empEmailStmt=$db->pdoQuery($empEmailQuery);
	$row=$empEmailStmt->results();
	//$empEmailStmt=$pdo->prepare($empEmailQuery);
      //  $empEmailStmt->execute(array($empid));
	//$row=$empEmailStmt->fetch(PDO::FETCH_ASSOC);
        return $row['emp_emailid'];
}

$phpVersion=phpversion();
if(phpversion() < 5.3) {
	echo "This script requires php version >= 5.3\n";
	exit;
}
$shortOptions="i::s::d::t::f::m::l::rah";
$longoptions  = array(
    "empId::",
    "All",
    "subDept::",
    "mainDept::",
    "toDate::",
    "fromDate::",
    "mailList::",
    "location::",
    "help"
);
$options = getopt($shortOptions,$longoptions);
### Get emp id info
if (isset($options['empId']))
        $empId=$options['empId'];

### Get emp location information
if (isset($options['location']))
        $location=$options['location'];
else 
	$location='BLR';

### Get all info
if (isset($options['All'])) {
        $All=1;
} else {
        $All=0;
}
### Get to date info
if (isset($options['toDate'])) {
        $toDate=$options['toDate'];
} else {
        # If toDate is not provided, then take current date
        $toDate=date("Y-m-d");
}
### Get from date info
if (isset($options['fromDate'])) {
        $fromDate=$options['fromDate'];
} else {
        # IF fromDate is not provided, then take starting month of the year
        $fromDate=date('Y-01-01');
}
### Get  sub deptarment info
if (isset($options['subDept']))
        $subDept=$options['subDept'];

### Get main department info
if (isset($options['mainDept']))
        $mainDept=$options['mainDept'];

### Get to mail list 
if (isset($options['mailList'])) {
        $mailList=$options['mailList'];
}
if (!isset($empId) && ! isset($subDept) && !isset($mainDept) && $All == 0) {
	helpText();	
}
### Display help message
if (isset($options['help'])) {
	helpText();
}

### Collect Information for a particular employee (manager)
if (isset($empId)) {
	$queryEmpList="SELECT * FROM `emp` where `state`='Active' and `empid`='".$empId."' and `location`='".$location."'";
}

### Collect Information for Sub department Employees
if (isset($subDept)) {
        $queryEmpList="SELECT * FROM `emp` where `dept`='".$subDept."' and `state`='Active'  and `location`='".$location."'";
}

### Collect Information for main department Employees
if (isset($mainDept)) {
        echo "\nCollecting Information for main department Employees: $mainDept\n";
	$getSubDept="SELECT * FROM `departments` where `mainDept`=$mainDept";
	$subDeptStmt=$db->pdoQuery($getSubDept);
	//$subDeptStmt=$pdo->prepare($getSubDept);
	//$subDeptStmt->execute(array($mainDept));
        $deptList="";
        while ($subDeptRows=$subDeptStmt->results()) {
                $deptList=$deptList.",'".$subDeptRows['subDept']."'";
        }
        $deptList = ltrim($deptList, ',');
        $queryEmpList="SELECT * FROM `emp` where `dept` IN ($deptList) and `state`='Active'  and `location`='".$location."'";
}

### Collect Information for all Employees
if (isset($All) && $All==1) {
	echo "\n------------------------------------------------------------------------------------------------------\n";
        echo "\nCollecting Information for all Employees\n";
	echo "\n------------------------------------------------------------------------------------------------------\n";
        $queryEmpList="SELECT * FROM `emp` where `state`='Active' and `location`='$location'";
}
$empIdList=array();
### For each employee
$empListStmt=$db->pdoQuery($queryEmpList);
if($empListStmt) {
         while ($empRow = $empListStmt->results()) {
		if ($empRow['track'] == 1) {
			array_push($empIdList,$empRow['empid']);
		}
	}
}
$correctFromDate=$fromDate;
foreach ($empIdList as $empVal) {
	echo "Collecting dangling transaction for an Employee: ". getempName($empVal)."\n";
	### Get Emp Role
	$getEmpQuery="SELECT * FROM `emp` where `EmpID`=$empVal and `state`='Active'";
	$getEmpStmt=$db->pdoQuery($getEmpQuery);
	$getEmpRow=$getEmpStmt->results();
	//$getEmpStmt=$pdo->prepare($getEmpQuery);
      //  $getEmpStmt->execute(array($empVal));
	//$getEmpRow = $getEmpStmt->fetch(PDO::FETCH_ASSOC);

	echo "\nFor UserName: ".$getEmpRow['empusername']." Employee: ".$getEmpRow['empname']."\n";
	
	### If the employee is Mumbai empployee, then fromDate should be May-01
	if(strtoupper($empRoleRow['location']) == "MUM") {
		if($fromDate<=$MumDataInLMS) {
			$fromDate=$MumDataInLMS;
		}	
	}

	$empJoiningDate=$getEmpRow['joiningdate'];
	### If Employee joined after $fromDate, then set fromDate to Employee joining date
        if (strtotime($empJoiningDate) > strtotime($fromDate)) {
                $fromDate=$empJoiningDate;
        } else {
                $fromDate=$correctFromDate;
        }
	# Get the distinct transactionId for that emp
	$tidQuery="select distinct(transactionid) from empleavetransactions where empid=$empVal and startdate>=$fromDate";
	$tidStmt=$db->pdoQuery($tidQuery);
	//$tidStmt=$pdo->prepare($tidQuery);
	/*$tidStmt->execute(array(
                                $empVal,
                                $fromDate
                            )
                     );*/
	while($tidQueryRow=$tidStmt->results()) {
		echo "Transaction Id: ".$tidQueryRow['transactionid']."\n";
		$perdaytransactionsQuery="select * from perdaytransactions where transactionid='".$tidQueryRow['transactionid']."'";
		/*$perdaytransactionsStmt=$pdo->prepare($perdaytransactionsQuery);
		$perdaytransactionsStmt->execute(array(
                                $tidQueryRow['transactionid']
                            )
                     );*/
		$perdaytransactionsStmt=$db->pdoQuery($perdaytransactionsQuery);
		$noOfPerDayTransaction=$perdaytransactionsStmt->rowCount();
		if($noOfPerDayTransaction==0) {
			echo "\nFor UserName: ".$getEmpRow['empusername']." Employee: ".$empQueryRow['empname'];
                        echo " TransactionId: ".$tidQueryRow['transactionid']." is not there\n";
		} else {
			echo "Count is : ".$noOfPerDayTransaction." is present";
		}
		
	}	
}
?>

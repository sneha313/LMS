<?php
require_once 'librarycopy1.php';
require_once("LMSConfig.php");
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
	echo "\t\t php resetEmpLeaves.php --empId=323156 --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t php resetEmpLeaves.php --subDept=Infrastructure --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t php resetEmpLeaves.php --mainDept=AIBI --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t php resetEmpLeaves.php --All --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
        exit;
}
function getempMailId($empid)
{
        global $db;
	$empEmailQuery="select emp_emailid from emp where empid='".$empid."' and state='Active'";
	$empEmailStmt=$db->pdoQuery($empEmailQuery)->results();
	foreach ($empEmailStmt as $row)
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
	$getSubDept="SELECT * FROM `departments` where `mainDept`='".$mainDept."'";
	$subDeptStmt=$db->pdoQuery($getSubDept)->results();
        $deptList="";
        foreach($subDeptStmt as $subDeptRows){
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
$empListStmt=$db->pdoQuery($queryEmpList)->results();
if($empListStmt) {
	foreach ($empListStmt as $empRow){
		if ($empRow['track'] == 1) {
			array_push($empIdList,$empRow['empid']);
		}
	}
}

$skipEmpList = array("321771","320630","327855","323219","540032","326198","327754");
$previousYear="2017";
$presentYear="2018";
$totalLeavesAtStarting=25;
$count=0;
foreach ($empIdList as $empId) {
	$empName=getempName($empId);
	$carryForwardLeaves=getTotalLeaves($empId);
	$count=$count+1;
	if(in_array($empId, $skipEmpList)) {
		continue;
	}
	echo "Collecting balance leave information for an Employee: ".$empName."\n";
	### Get Emp Role
	$getEmpQuery="SELECT * FROM `emp` where `EmpID`=$empId and `state`='Active'";
	$getEmpStmt=$db->pdoQuery($getEmpQuery);
	$getEmpRows = $db->pdoQuery($getEmpQuery)->results();
	foreach ($getEmpRows as $getEmpRow)
	$emptotalleavesQuery="select * from `emptotalleaves` where `empid`=$empId";
	 $emptotalleavesStmt=$db->pdoQuery($emptotalleavesQuery)->results();
	 foreach ($emptotalleavesStmt as $emptotalleavesRow)
	$resetStatus="No";
	if($carryForwardLeaves>30){
                $carryForwardLeaves = 30;
                $resetStatus="Yes";
		echo "\nResetting Value to 30 as the carryfarward is > 30 for emp: ".$empId." and EmpName: ".$empName."\n";
        }	
// update array data
$resetEmpLeaves = array('carryforwarded'=>$carryForwardLeaves,'previous year'=>$previousYear,'present year'=>$presentYear,'totalLeavesAtStarting'=>$totalLeavesAtStarting,'balanceleaves'=>$totalLeavesAtStarting);
// where condition array
$aWhere = array('empid'=>$empId);
// call update function
$resetEmpLeavesstmt = $db->update('emptotalleaves', $resetEmpLeaves, $aWhere)->affectedRows();


                $BackUpLeaves = array('id'=>$count,'empid'=>$empId,'empname'=>$empName,'PY-CR'=>$emptotalleavesRow['carryforwarded'],'PY-BL'=>$emptotalleavesRow['balanceleaves'],'total-BL'=>$emptotalleavesRow['carryforwarded']+$emptotalleavesRow['balanceleaves'],'resetStatus'=>$resetStatus,'CY-CR'=>$carryForwardLeaves,'CY-BL'=>$totalLeavesAtStarting,'CY-Used'=>'');
                // use insert function
                $BackUpLeavesstmt = $db->insert('resetleaves',$dataArray)->getLastInsertId();
	
}
?>

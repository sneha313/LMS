<?php
require_once 'Library.php';
require_once 'attendenceFunctions.php';
error_reporting("E_ALL");

$options = array();

foreach ($argv as $arg){
	preg_match('/\-\-(\w*)\=?(.+)?/', $arg, $value);
        if ($value && isset($value[1]) && $value[1]) {
            $options[$value[1]] = isset($value[2]) ? $value[2] : null;
    	}
}

### Get emp id info
if (isset($options['empId']))
	$empId=$options['empId'];

### Get all info
if (isset($options['All']))
        $All=1;

### Get  sub deptarment info
if (isset($options['subDept']))
        $subDept=$options['subDept'];

### Get main department info
if (isset($options['mainDept']))
        $mainDept=$options['mainDept'];

### Get to date info
if (isset($options['toDate'])) {
        $toDate=$options['toDate'];
} else {
	$toDate=date("Y-m-d");
}
### Get from date info
if (isset($options['fromDate'])) {
        $fromDate=$options['fromDate'];
} else {
	$numDays=strtotime("-30 day", strtotime($toDate));
	$fromDate=date('Y-m-d', $numDays);
}

### By default, update to all employees
if (!isset($options['empId']) and !isset($options['All']) and !isset($options['subDept']) and !isset($options['mainDept'])) {
	$All=1;
}

echo "\nempId: $empId\n All: $All\n subDept: $subDept\n mainDept: $mainDept\n toDate: $toDate\n fromDate: $fromDate\n";


########################### Main program starts here ######################################
$db=connectToDB();

### Update for a given EmpID
if (isset($empId)) {
	echo "\nUpdating information for an employee: $empId,  to the inout table\n";
	$getEmpQuery="SELECT * FROM `emp` where `empid`='".$empId."' and `state`='Active'";
}

### Update for given sub Deptarment
if (isset($subDept)) {
	echo "\nUpdating information for a subDept:$subDept, to the inout table\n";
        $getEmpQuery="SELECT * FROM `emp` where `dept`='".$subDept."' and `state`='Active'";
}

### Update for given main Department
if (isset($mainDept)) {
        echo "\nUpdating information for a mainDept:$mainDept, to the inout table\n";
	$getSubDept="SELECT * FROM `departments` where `mainDept`='$mainDept'";
	$subDeptResult=$db->query($getSubDept);
	while ($subDeptRow=mysql_fetch_assoc($subDeptResult)) {
		### Update information for each sub department
		$subDept=$subDeptRow['subDept'];
		echo "\nFor a Sub-Department: $subDept\n";
		$getEmpQuery="SELECT * FROM `emp` where `dept`='".$subDept."' and `state`='Active'";
		echo "\nExecuting a Query: $getEmpQuery\n";
		$getEmpQueryResult=$db->query($getEmpQuery);
		$numRows=mysql_num_rows($getEmpQueryResult);
		echo "rows=".$numRows;
		if($numRows) {
		        weeklyTrackAttendence($getEmpQueryResult,$fromDate,$toDate, $db);
		} else {
		        echo "\nResult is empty for a given Query: $getEmpQuery\n";
		}
		echo "\nEnd of Sub-Department: $subDept\n";
	}
	exit(0);
}

### Update all Employee details
if (isset($All)) {
	echo "\nUpdating information for all employees, to the inout table\n";
        $getEmpQuery="SELECT * FROM `emp` where `state`='Active'";
}

echo "\nExecuting a Query: $getEmpQuery\n";
$getEmpQueryResult=$db->query($getEmpQuery);
$numRows=mysql_num_rows($getEmpQueryResult);
if($numRows) {
	weeklyTrackAttendence($getEmpQueryResult,$fromDate,$toDate, $db);
} else {
	echo "\nResult is empty for a given Query: $getEmpQuery\n";
}

$db->closeConnection();
?>


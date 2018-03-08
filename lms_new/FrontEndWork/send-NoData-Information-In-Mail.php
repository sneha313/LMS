<?php
//require_once ("db.class.php");
//require_once ("Library.php");
require_once 'librarycopy1.php';
###############################################################################################################
# Script to Send Employee Leave Details Waiting for Approval via Email.
# 	--> Send No Data information for given Emp ID (--empId)
#	--> Send No Data information for all Employees of given sub-department		   (--subDept)
#	--> Send No Data information for all Employees of given Main-department		   (--mainDept)
# 	--> Send No Data information for all Employees of Bangalore location	  	   (--All) 
#	--> Send No Data information for all Employees of Mumbai location             	   (--All --location=MUM)
# 	--> By default, Script will send information for all Direct Employees.		   
#	--> To collect infomtion of all sub-ordinates of Given manager			   (--indirect)
#	--> By default location is set to "BLR" , You can explicitly specify location 	   (--location=MUM)
#	--> To date information, By default it takes present day		           (--toDate=2017-09-01)
#	--> From Date information, By default it takes start of the year            	   (--fromDate=2017-05-01)
#	--> Mail will not trigger, if Employee has No data information in IN/OUT table
###############################################################################################################


function helpText() {
	echo "Script Usage:\n";
        echo "\t\t empId: Send No Data information for given Emp ID\n\n";
        echo "\t\t subDept: Send No Data information for all Employees of given sub-department\n\n";
        echo "\t\t mainDept: Send No Data information for all Employees of given Main-department\n\n";
        echo "\t\t All: Send No Data information for all Employees of Bangalore location\n\n";
        echo "\t\t mailList: To send Leave pending information for given Employee mailID, otherwise mail will be send to respective managers\n\n";
        echo "\t\t toDate: To date information, By default it takes end of the year\n\n";
        echo "\t\t fromDate: From Date information, By default it takes start of the year\n\n";
        echo "\t\t location: location name, By default BLR\n\n";
        echo "\t\t indirect: Send Leave pending information of direct and indirect employees\n\n";
        echo "\t\t Example:\n\t\t----------------------------------------------------------------------------------------------------------\n\n";
	echo "\t\t To Collect No data information all employees: \n\t\t\tphp sendNoData.php --empId=323156 --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t To Collect No Data information for particular employee: \n\t\t\tphp sendNoData.php --empId=323156 --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t To Collect No Data information mail to all Employees of given Sub-department:\n\t\t\tphp sendNoData.php --subDept=Infrastructure --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t To Collect No Data information mail to all Employees of given Main-department:\n\t\t\tphp sendNoData.php --mainDept=AIBI --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t To Collect No Data information mail to all Employees:\n\t\t\tphp sendNoData.php --All --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
        exit;
}
function getempMailId($empid)
{
        global $db;
       // $result=$db->query("select emp_emailid from emp where empid='$empid' and state='Active'");
        //$row=$db->fetchAssoc($result);
        $result=$db->pdoQuery("select emp_emailid from emp where (empid=? and state=?);",array($empid,'Active'));
        $row=$result->results();
        return $row['emp_emailid'];
}

function getSubordinate($empval) {
	global $db,$empTree,$indirect;
	$empTree["$empval"]=array();
	array_push($empTree["$empval"], $empval);
        $directEmpList=getChildren($empval);
	foreach ($directEmpList as $empid) {
		if ($empid == "321771") {
			continue;
		}
		array_push($empTree["$empval"], $empid);
	}
	if ($indirect == 1) {
		if ($empval != "420064" && $empval != "325020") {
			foreach ($directEmpList as $empid) {    
        	        	$getEmpQuery="select * from `emp` where `empid`='$empid' and state='Active'";
	        	        //$getEmpQueryResult=$db->query($getEmpQuery);
        	        	//$empRow = mysql_fetch_assoc($getEmpQueryResult);
        	        	$getEmpQueryResult=$db->pdoQuery($getEmpQuery);
        	        	$empRow = $getEmpQueryResult->results();
	                	if ($empRow['role'] == 'manager') {
					getSubordinate($empid);
	        	        }
			}
        	} else {
			foreach ($directEmpList as $empid) {
				$empTree["$empid"]=array();
                                $getEmpQuery="select * from `emp` where `managerid`='$empid' and state='Active'";
                               // $getEmpQueryResult=$db->query($getEmpQuery);
                                $getEmpQueryResult=$db->pdoQuery($getEmpQuery);
				//while($empRow = mysql_fetch_assoc($getEmpQueryResult)) {
				while($empRow = $getEmpQueryResult->results()) {
					array_push($empTree["$empid"], $empRow['empid']);
				}
			}
		}
	}
}

$db=connectToDB();

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
    "indirect::",
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

### Flag to get infomation of all direct/indirect employees
if (isset($options['indirect']))
        $indirect=1;
else
	$indirect=0;

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
	$queryEmpList="SELECT * FROM `emp` where `state`='Active' and `empid`='".$empId."'";
}

### Collect Information for Sub department Employees
if (isset($subDept)) {
        $queryEmpList="SELECT * FROM `emp` where `dept`='".$subDept."' and `state`='Active'";
}

### Collect Information for main department Employees
if (isset($mainDept)) {
        echo "\nCollecting Information for main department Employees: $mainDept\n";
        $getSubDept="SELECT * FROM `departments` where `mainDept`='$mainDept'";
        //$subDeptResult=$db->query($getSubDept);
        $subDeptResult=$db->pdoQuery($getSubDept);
        $deptList="";
        //while ($subDeptRows=mysql_fetch_assoc($subDeptResult)) {
        while ($subDeptRows=$subDeptResult->results()) {
                $deptList=$deptList.",'".$subDeptRows['subDept']."'";
        }
        $deptList = ltrim($deptList, ',');
        $queryEmpList="SELECT * FROM `emp` where `dept` IN ($deptList) and `state`='Active'";
}

### Collect Information for all Employees
if (isset($All) && $All==1) {
        echo "\nCollecting Information for all Employees\n";
        $queryEmpList="SELECT * FROM `emp` where `state`='Active' and `location`='$location'";
}
$empIdList=array();
### For each employee
//$empListResult=$db -> query($queryEmpList);
$empListResult=$db -> pdoQuery($queryEmpList);
if($empListResult) {
        // while ($empRow = mysql_fetch_assoc($empListResult)) {
         	while ($empRow =$empListResult->results()) {
         	if ($empRow['track'] == 1) {
			array_push($empIdList,$empRow['empid']);
		}
	}
}
$htmlHeader="<html><head><style>
table {
    font-family: arial;
    font-size:12px;
    border-collapse: collapse;
    width: 75%;
}
th {
    background-color: #e6eeff;
    border: 1px solid #dddddd;
    text-align: left;   
    padding: 8px;
}
td {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
</style></head><body>";
foreach ($empIdList as $empVal) {
	echo "Collecting Untracked leave information for an Employee: ". getempName($empVal)."\n";
	### Get Emp Role
	$getRoleQuery="SELECT * FROM `emp` where `EmpID`='$empVal' and `state`='Active'";
	//$getRoleResult=$db -> query($getRoleQuery);
	//$empRoleRow = mysql_fetch_assoc($getRoleResult);
	$getRoleResult=$db -> pdoQuery($getRoleQuery);
	$empRoleRow = $getRoleResult->results();
	### If the employee is Mumbai empployee, then fromDate should be May-01
	if(strtoupper($empRoleRow['location']) == "MUM") {
		if($fromDate<="2017-05-01") {
			$fromDate="2017-05-01";
		}	
	}
	if (strtoupper($empRoleRow['role']) == "MANAGER") {
		$string=$htmlHeader."Hi ".getempName($empVal).",<br><br>Below is the list of Employees who din't apply Leaves in LMS portal.<br><br>";
	} else {
		$string=$htmlHeader."Hi ".getempName($empVal).",<br><br>Below is the list of untracked leave information.<br><br>";
	}
	$sendmail=0;
	$empTree=array();
	getSubordinate($empVal);
	foreach (array_keys($empTree) as $eId) {
		$leaveEntry=0;
		$flag=0;
		$flag1=0;
		$tableBody="";
		foreach ($empTree["$eId"] as $empId) {
			$tableHeader="<table border=1>
                                <caption align='left'><font color='green'><b>Employee: ".getempName($empId)." has to apply leave in LMS portal for following days</b></font></caption>
                                <thead>
                                        <tr>
                                                <th>Emp Id</th>
                                                <th>Emp Name</th>
                                                <th>Date</th>
                                                <th>IN Time</th>
                                                <th>OUT Time</th>
                                                <th>State</th>
                                        </tr>
                                </thead><tbody>";
			$subTableBody="";
			$pendingQuery="select * from `inout` where `empid`='$empId' and `state` != 'Data Exists' and `state`!='Leave is pending for approval' and `Date` between '".$fromDate."' and '".$toDate."'";
			//$pendingResult=$db->query($pendingQuery);
			$pendingResult=$db->pdoQuery($pendingQuery);
			$noDataCount=0;
			//while($empRow = mysql_fetch_assoc($pendingResult)) {
	                while($empRow = $pendingResult->results()) {
				$noDataCount=$noDataCount+1;
	                        $subTableBody=$subTableBody. "<tr>
								<td>".$empId."</td>
								<td>".getempName($empId)."</td>
								<td>".$empRow['Date']."</td>
								<td>".$empRow['First']."</td>
	                                        		<td>".$empRow['Last']."</td>
								<td>".$empRow['state']."</td>
							</tr>";
	                }
			if ($subTableBody != "") {
				$tableBody=$tableBody.$tableHeader.$subTableBody."</tbody><tr align='center'><td colspan='7'><b>Total Number of untracked days: $noDataCount</b></td></tr></table><br><br>";
				$flag=1;
			}
		}
		if ($flag == 1) {
			$string=$string.$tableBody;
			$sendmail=1;
			$leaveEntry=1;
		}
	
		if ($leaveEntry == 0) {
			$string=$string."<br>";	
		} else {
		        $string=$string."<br>";
		}
	}
	if ($sendmail==1) {
		if (strtoupper($empRoleRow['role']) == "MANAGER") {
			$string=$string."Please make sure the above list of employees apply leave in <a href='https://blrtools.ecitele.com/lms'>LMS application.</a><br><br>Thanks,<br>DevOps Team</body></html>";
		} else {
			$string=$string."Please apply leaves for the above days, by logging in <a href='https://blrtools.ecitele.com/lms'>LMS application.</a><br><br>Thanks,<br>DevOps Team</body></html>";
		}
		if (isset($mailList)) {
	                $to=$mailList;
        	} else {
                	$to=getempMailId($empVal);
	        }
		sendMail($to,$string,"Untracked Leave Information in LMS Portal");
		echo "Sending message for : $empVal   ". getempName($empVal) . "\n";
	} else {
		echo "Not sending message for : $empVal   ". getempName($empVal) . "\n";
	}
}
?>
<?php
require_once 'librarycopy1.php';
###############################################################################################################
# Script to Send Employee Leave Details Waiting for Approval via Email.
# 	--> Send Leave pending information for given Emp ID, Only If Employee is a manager (--empId)
#	--> Send Leave pending information for all managers of given sub-department	   (--subDept)
#	--> Send Leave pending information for all managers of given Main-department	   (--mainDept)
# 	--> Send Leave pending information for all managers of Bangalore location	   (--All) 
#	--> Send Leave pending information for all managers of Mumbai location             (--All --location=MUM)
# 	--> By default, Script will send information for all Direct Employees.		   
#	--> To collect infomtion of all sub-ordinates of Given manager			   (--indirect)
#	--> By default location is set to "BLR" , You can explicitly specify location 	   (--location=MUM)
#	--> To date information, By default it takes end of the year		           (--toDate=2017-09-01)
#	--> From Date information, By default it takes start of the year            	   (--fromDate=2017-05-01)
#	--> Mail will not trigger, if Manager has no pending leaves to approve
###############################################################################################################


function helpText() {
	echo "Script Usage:\n";
        echo "\t\t empId: Send Leave pending information for given Emp ID, Only If Employee is a manager\n\n";
        echo "\t\t subDept: Send Leave pending information for all managers of given sub-department\n\n";
        echo "\t\t mainDept: Send Leave pending information for all managers of given Main-department\n\n";
        echo "\t\t All: Send Leave pending information for all managers\n\n";
        echo "\t\t mailList: To send Leave pending information for given Employee mailID, otherwise mail will be send to respective managers\n\n";
        echo "\t\t toDate: To date information, By default it takes end of the year\n\n";
        echo "\t\t fromDate: From Date information, By default it takes start of the year\n\n";
        echo "\t\t location: location name, By default BLR\n\n";
        echo "\t\t indirect: Send Leave pending information of direct and indirect employees\n\n";
        echo "\t\t Example:\n\t\t----------------------------------------------------------------------------------------------------------\n\n";
        echo "\t\t To Collect leave pending information of direct employees of given Manager: \n\t\t\tphp sendPendingLeave.php --empId=323156 --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
        echo "\t\t To Collect leave pending information all employees of given Manager: \n\t\t\tphp sendPendingLeave.php --empId=323156 --toDate=2017-07-24 --fromDate=2017-06-01 --indirect\n\n";
        echo "\t\t To send mail to all managers of given Sub-department:\n\t\t\tphp sendPendingLeave.php --subDept=Infrastructure -toDate=2017-07-24 --fromDate=2017-06-01\n\n";
        echo "\t\t To send mail to all managers of given Main-department:\n\t\t\tphp sendPendingLeave.php --mainDept=AIBI -toDate=2017-07-24 --fromDate=2017-06-01\n\n";
        exit;
}
function getempMailId($empid)
{
        global $db;
        //$result=$db->query();
        //$row=$db->fetchAssoc($result);
       $query= "select emp_emailid from emp where empid='$empid' and state='Active'";
        $result=$db->pdoQuery($query)->results();
       // $row=$result;
       foreach ($result as $row)
        return $row['emp_emailid'];
}

function getSubordinate($empval) {
	global $db,$empTree,$indirect;
        $directEmpList=getChildren($empval);
	$empTree["$empval"]=array();
	foreach ($directEmpList as $empid) {
		array_push($empTree["$empval"], $empid);
	}
	if ($indirect == 1) {
		if ($empval != "420064" && $empval != "325020") {
			foreach ($directEmpList as $empid) {    
        	        	$getEmpQuery="select * from `emp` where `empid`='$empid'";
        	        	$getEmpQueryResult=$db->pdoQuery($getEmpQuery)->results();
        	        	foreach ($getEmpQueryResult as $empRow)
	                	if ($empRow['role'] == 'manager') {
					getSubordinate($empid);
	        	        }
			}
        	} else {
			foreach ($directEmpList as $empid) {
				$empTree["$empid"]=array();
                                $getEmpQuery="select * from `emp` where `managerid`='$empid'";
                                $getEmpQueryResult=$db->pdoQuery($getEmpQuery)->results();
				//while($empRow = $getEmpQueryResult->results()) {
				foreach ($getEmpQueryResult as $empRow){
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
        $toDate=date("Y-12-31");
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
	$queryEmpList="SELECT * FROM `emp` where `state`='Active' and `empid`='".$empId."' and `role`='Manager'";
}

### Collect Information for Sub department Employees
if (isset($subDept)) {
        $queryEmpList="SELECT * FROM `emp` where `dept`='".$subDept."' and `state`='Active' and `role`='Manager'";
}

### Collect Information for main department Employees
if (isset($mainDept)) {
        echo "\nCollecting Information for main department Employees: $mainDept\n";
        $getSubDept="SELECT * FROM `departments` where `mainDept`='$mainDept'";
        //$subDeptResult=$db->query($getSubDept);
        $subDeptResult=$db->pdoQuery($getSubDept)->results();
        $deptList="";
        //while ($subDeptRows=mysql_fetch_assoc($subDeptResult)) {
        foreach ($subDeptResult as $subDeptRows){
                $deptList=$deptList.",'".$subDeptRows['subDept']."'";
        }
        $deptList = ltrim($deptList, ',');
        $queryEmpList="SELECT * FROM `emp` where `dept` IN ($deptList) and `state`='Active' and `role`='Manager'";
}

### Collect Information for all Employees
if (isset($All) && $All==1) {
        echo "\nCollecting Information for all Employees\n";
        $queryEmpList="SELECT * FROM `emp` where `state`='Active' and `location`='$location' and `role`='Manager'";
}

$empIdList=array();
### For each employee
//$empListResult=$db -> query($queryEmpList);
$empListResult=$db -> pdoQuery($queryEmpList);
if($empListResult) {
	$empRows=$db -> pdoQuery($queryEmpList)->results();
        // while ($empRow = mysql_fetch_assoc($empListResult)) {
       //  while ($empRow = $empListResult->results()) {
       foreach ($empRows as $empRow){
		array_push($empIdList,$empRow['empid']);
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
	$string=$htmlHeader."Hi ".getempName($empVal).",<br><br> Please find the below details for Employee leaves waiting for approval.<br><br>";
	$sendmail=0;
	$empTree=array();
	getSubordinate($empVal);
	foreach (array_keys($empTree) as $managerId) {
		$leaveEntry=0;
		$mailMsg="<pre color='blue'><u>Employee Leaves waiting for approval under: ".getempName($managerId)."</u></pre><br>";
		$flag=0;
		$flag1=0;
		$tableHeader="<table class='table table-bordered'>
				<caption align='left'><font color='green'><b>Pending Leaves Details</b></font></caption>
				<thead>
					<tr class='info'>
						<th>Emp Id</th>
						<th>Emp Name</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Count</th>
						<th>Status</th>
						<th>Reason</th>
					</tr>
				</thead><tbody>";
		$tableBody="";
		foreach ($empTree["$managerId"] as $empId) {
			$pendingQuery="select * from `empleavetransactions` where `empid`='$empId' and `approvalstatus`='Pending' and (`startdate` between '".$fromDate."' and '".$toDate."' or `enddate` between '".$fromDate."' and '".$toDate."')";
			$pendingResult=$db->pdoQuery($pendingQuery)->results();
	                foreach ($pendingResult as $empRow){
	                        $tableBody=$tableBody. "<tr>
								<td>".$empId."</td>
								<td>".getempName($empId)."</td>
								<td>".$empRow['startdate']."</td>
								<td>".$empRow['enddate']."</td>
	                            <td>".$empRow['count']."</td>
								<td>".$empRow['approvalstatus']."</td>
								<td>".$empRow['reason']."</td>
							</tr>";
				$flag=1;
	                }
		}
		
		if ($flag == 1) {
			$string=$string.$mailMsg.$tableHeader.$tableBody. "</tbody></table><br><br>";
			$sendmail=1;
			$leaveEntry=1;
		}
	
		$tableHeader1="<table class='table table-bordered'><caption><font color='green'><b>Pending IN/OUT Details</b></font></caption>
	                        <thead>
	                                <tr class='info'>
	                                        <th>Emp Id</th>
	                                        <th>Emp Name</th>
	                                        <th>In Time</th>
											<th>Out Time</th>
											<th>Date</th>
											<th>Status</th>
											<th>Reason</th>
	                                </tr>
	                        </thead><tbody>";
	        $tableBody1="";
		foreach ($empTree["$managerId"] as $empId) {
			$empinoutQuery="select * from `empinoutapproval` where `empid`='$empId' and `status`='Pending' and `date` between '".$fromDate."' and '".$toDate."'";
			$empinoutResult=$db->pdoQuery($empinoutQuery)->results();
	                foreach ($empinoutResult as $inoutRow){
				$tableBody1=$tableBody1. "<tr>
                                                        <td>".$empId."</td>
                                                        <td>".getempName($empId)."</td>
                                                        <td>".$inoutRow['intime']."</td>
                                                        <td>".$inoutRow['outtime']."</td>
                                                        <td>".$inoutRow['date']."</td>
														<td>".$inoutRow['status']."</td>
                                                        <td>".$inoutRow['reason']."</td>
	                                                </tr>";
	                        $flag1=1;
	                }
		}
		if ($flag1 == 1) {
			if ($leaveEntry == 0) {
				$string=$string.$mailMsg.$tableHeader1.$tableBody1. "</tbody></table><br><hr><br>";	
			} else {
		                $string=$string.$tableHeader1.$tableBody1. "</tbody></table><br><hr><br>";
			}
			$sendmail=1;
	        }	
	}
	if ($sendmail==1) {
		$string=$string."<br>Please Approve/Not Approve Employee leaves by logging in  <a href='https://blrtools.ecitele.com/lms'>LMS application.</a><br><br>Thanks,<br>DevOps Team</body></html>";
		if (isset($mailList)) {
	                $to=$mailList;
        	} else {
                	$to=getempMailId($empVal);
	        }
		sendMail($to,$string,"LMS - Employee Leave Details Waiting for Approval");
		echo "Sending message for : $empVal   ". getempName($empVal) . "\n";
	} else {
		echo "Not sending message for : $empVal   ". getempName($empVal) . "\n";
	}
}
?>



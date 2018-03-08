<?php
//require_once ("db.class.php");
//require_once ("Library.php");
require_once ("librarycopy1.php");
//require_once ("class.pdowrapper.php");
require_once("LMSConfig.php");

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
	echo "\t\t php deductLeaves.php --empId=323156 --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t php deductLeaves.php --subDept=Infrastructure --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t php deductLeaves.php --mainDept=AIBI --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
	echo "\t\t php deductLeaves.php --All --toDate=2017-07-24 --fromDate=2017-06-01\n\n";
        exit;
}
function getempMailId($empid)
{
        global $db;
	$empEmailQuery="select emp_emailid from emp where empid=? and state='Active'";
	$row=$db->pdoQuery($empEmailQuery)->results();
	//$empEmailStmt=$pdo->prepare($empEmailQuery);
       // $empEmailStmt->execute(array($empid));
	//$row=$empEmailStmt->fetch(PDO::FETCH_ASSOC);
        return $row['emp_emailid'];
}

function deductLeaves($empId,$type,$date,$count) {
	global $db;
	### Generate Transaction ID 	
	$transaction_id = generate_transaction_id();
	echo "\ntransaction_id: $transaction_id\n";
	
	//$pdo->beginTransaction();

	//try {
		echo "\nUpdating empleavetransactions\n";
		### Update in empleavetransactions table
		$reason = "Auto Deducting Employee Leaves";
        	/*$query = "INSERT INTO`empleavetransactions` (`transactionid` ,`empid` ,`startdate` ,`enddate` ,`count`,`reason`,`approvalstatus`,`approvalcomments`) 
			  VALUES (?,?,?,?,?,?,?,?)";
		$stmt = $pdo->prepare($query);
		$stmt->execute(array(
        			    $transaction_id, 
			            $empId,
				    $date,
				    $date,
				    $count,
				    $reason,
        	                    'Approved',
        	                    'Applied by LMS Auto Deduction'
        			   )
		
    			      );*/

		$query = array('transactionid'=>$transaction_id,'empid'=>$empId,'startdate'=>$date,'enddate'=>$date,'count'=>$count,'reason'=>$reason,'approvalstatus'=>'Approved','approvalcomments'=>'Applied by LMS Auto Deduction');
		// use insert function
		$stmt = $db->insert('empleavetransactions',$dataArray)->getLastInsertId();
		
		### Update in perdaytransactions table
		echo "\nUpdating perdaytransactions\n";

		/*$perdayquery = "Insert into `perdaytransactions` (`transactionid` ,`empid` ,`date` ,`leavetype`,`shift`,`status`,`count`)
        	                                                  values(?,?,?,?,?,?,?)";
		$stmt = $pdo->prepare($perdayquery);
		$stmt->execute(array(
        	                    $transaction_id,
        	                    $empId,
        	                    $date,
        	                    $type,
				    "",
				    "Approved",
        	                    $count
        	                   )
        	              );
*/

		$perdayquery = array('transactionid'=>$transaction_id,'empid'=>$empId,'date'=>$date,'leavetype'=>$type,'shift'=>'','status'=>'Approved','count'=>$count);
		// use insert function
		$stmt = $db->insert('perdaytransactions',$dataArray)->getLastInsertId();
		### Update Leave Count
		echo "\nUpdating balanceleaves\n";
		$balanceLeaves = getValueFromQuery("SELECT  balanceleaves FROM emptotalleaves WHERE empid ='" . $empId . "'","balanceleaves");
		$reducedleaves = ($balanceLeaves - $count);

		/*$reduceleavesquery="UPDATE  `emptotalleaves` SET  `balanceleaves` = ? WHERE  `empid` = ?";
		$stmt = $pdo->prepare($reduceleavesquery);
		$stmt->execute(array(
				    $reducedleaves,
        	                    $empId
        	                   )
        	              );
		*/

		$reduceleavesquery = array('balanceleaves'=>$reducedleaves);
		// where condition array
		$Whereupdate = array('empid'=>$empId);
		// call update function
		$stmt = $db->update('emptotalleaves', $reduceleavesquery, $Whereupdate)->affectedRows();
		echo "\nUpdating inout\n";
		if ($type == "HalfDay") {
			# Get the empid, fromdate and todate from the transactionid and execute the script which updates the inout table.
                       /* $updateRowInOut="UPDATE `inout` SET state= ? WHERE `EmpID`=? and `Date`= ?";
                        $stmt = $pdo->prepare($updateRowInOut);
                        $stmt->execute(array(
				    "Data Exists",
                                    $empId,
                                    $date
                                   )
                              );*/
			$updateRowInOut = array('state'=>'Data Exists');
			// where condition array
			$Whereupdate = array('EmpID'=>$empId,'Date'=>$date);
			// call update function
			$stmt = $db->update('inout', $updateRowInOut, $Whereupdate)->affectedRows();
			echo "For a date: $date and empid: $empId---> Updating row from inout table - (Half Day)\n";
		} else {
	        	# Get the empid, fromdate and todate from the transactionid and execute the script which updates the inout table.
			/*$deleteRowInOut="DELETE FROM `inout` WHERE `EmpID`=? and `Date`= ?";
			$stmt = $pdo->prepare($deleteRowInOut);
			$stmt->execute(array(
        		            $empId,
				    $date
        	                   )
        	              );*/

			$deleteRowInOut = array('EmpID'=>$empId, 'Date'=> $date);
			// call update function
			$stmt = $db->delete('inout', $deleteRowInOut)->affectedRows();
			echo "For a date: $date and empid: $empId---> Deleting row from inout table - (Full Day)\n";
        	}
		//$pdo->commit();
/*	}
	catch(Exception $e) {
            ### Rollback the transaction.
            $pdo->rollBack();

 	    ### An exception has occured, which means that one of our database queries failed.Print out the error message.
	    $error=$e->getMessage();
	    $mailBody="Automatic Leave Detection script is failed with error\n\n$error";
	    $toEmailList="anilkumar.thatavarthi@ecitele.com";
	    sendMail($toEmailList,$mailBody,"Automatic Leave Deduction");
	    exit;
	}*/
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

### Mail Header
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
tfoot td {
    background-color: #e6eeff;
    border: 1px solid #dddddd;
    text-align: right;
    padding: 8px;
}
tr:nth-child(even) {
    background-color: #dddddd;
}
</style></head><body>";

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
	//$subDeptStmt=$pdo->prepare($getSubDept);
	//$subDeptStmt->execute(array($mainDept));
        $deptList="";
        foreach($subDeptStmt as $subDeptRows){
        //while ($subDeptRows=$subDeptStmt->results()) {
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
         //while ($empRow = $empListStmt->results()) {
         foreach($empListStmt as $empRow){
		if ($empRow['track'] == 1) {
			array_push($empIdList,$empRow['empid']);
		}
	}
}
$empLeaveCount=array();
$totalCount=array();
$skipEmpList = array("321771","320630","327855","323219","540032","326198","327754");

$correctFromDate=$fromDate;
foreach ($empIdList as $empVal) {
	if(in_array($empVal, $skipEmpList)) {
		continue;
	}
	echo "Collecting Untracked leave information for an Employee: ". getempName($empVal)."\n";
	### Get Emp Role
	$getRoleQuery="SELECT * FROM `emp` where `EmpID`=$empVal and `state`='Active'";
	/*$getRoleStmt=$pdo->prepare($getRoleQuery);
        $getRoleStmt->execute(array($empVal));
	$empRoleRow = $getRoleStmt->fetch(PDO::FETCH_ASSOC);*/
	$getRoleStmt=$db->pdoQuery($getRoleQuery);
	$empRoleRows=$db->pdoQuery($getRoleQuery)->results();
	foreach($empRoleRows as $empRoleRow)
	### If the employee is Mumbai empployee, then fromDate should be May-01
	if(strtoupper($empRoleRow['location']) == "MUM") {
		if($fromDate<=$MumDataInLMS) {
			$fromDate=$MumDataInLMS;
		}	
	}
	$empJoiningDate=$empRoleRow['joiningdate'];
	### If Employee joined after $fromDate, then set fromDate to Employee joining date
        if (strtotime($empJoiningDate) > strtotime($fromDate)) {
        	$fromDate=$empJoiningDate;
        } else {
		$fromDate=$correctFromDate;
	}	

	### Update perdaytransaction table before collecting untracked information
	echo "\nUpdate perdaytransaction table before collecting untracked leave information\n";
	$perdaytransactioncmd = '/usr/bin/php fillPerDayTransactions.php --empId='.$empVal.' --fromDate='.$fromDate.' --toDate='.$toDate.' >> /dev/null ';
        echo "\nUpdate script command is: $perdaytransactioncmd\n";
       	exec($perdaytransactioncmd, $out,$return);
	if (!$return) {
 	   echo "Successfully Executed fillPerDayTransactions.php script\n";
	} else {
	   echo "Error Occured while Executing fillPerDayTransactions.php Script, Error: $out\n";
	}
	
	### Update INOUT table before collecting untracked leave information
	echo "\nUpdate INOUT table before collecting untracked leave information\n";
        $cmd = '/usr/bin/php trackWeeklyAttendance.php --empId='.$empVal.' --fromDate='.$fromDate.' --toDate='.$toDate.' >> /dev/null ';
	echo "\nUpdate script command is: $cmd\n";
        exec($cmd,$cmdOut,$cmdReturn);
	if (!$cmdReturn) {
           echo "Successfully Executed trackWeeklyAttendance.php Script\n";
        } else {
           echo "Error Occured while Executing trackWeeklyAttendance.php Script, Error: $cmdOut\n";
        }

	$totalLeaveCount=0;

	### Get Number of NoData Days
	$noDataQuery="select * from `inout` where `empid`=$empVal and `state` = 'No Data' and `Date` between $fromDate and $toDate";
        /*$noDataStmt=$pdo->prepare($noDataQuery);
        $noDataStmt->execute(array(
				$empVal,
				$fromDate,
				$toDate
			    )
		     );*/
	$noDataStmt=$db->pdoQuery($noDataQuery);
	//$noDataLeaves=$noDataStmt->rowCount();
	$noDataLeaves=$noDataStmt -> count($sTable = 'inout', $sWhere = 'empid = "'.$empVal.'" and state = No Data and `Date` between "'.$fromDate.'" and "'.$toDate.'"' );
	
	$empLeaveCount[$empVal]['leaveInfo']=array();
	### Save all details
	$empRows=$db->pdoQuery($noDataQuery)->results();
	//while($empRow =  $noDataStmt->results()) {
	foreach($empRows as $empRow){
		$row=$empRow['Date'].",".$empRow['state'].",1";
		array_push($empLeaveCount[$empVal]['leaveInfo'],$row);
	}
	
	### Get Number of Half day not applied days
	$halfDayQuery="select * from `inout` where `empid`=$empVal and `state` = 'Half Day PTO not applied' and `Date` between $fromDate and $toDate";
	/*$halfDayStmt=$pdo->prepare($halfDayQuery);
        $halfDayStmt->execute(array(
                                $empVal,
                                $fromDate,
                                $toDate
                            )
                     );*/
	$halfDayStmt=$db->pdoQuery($halfDayQuery);
        //$halfDayLeaves=$halfDayStmt->rowCount()/2;
	$halfDayLeave=$halfDayStmt -> count($sTable = 'inout', $sWhere = 'empid = "'.$empVal.'" and state = "Half Day PTO not applied and `Date` between '.$fromDate.' and '.$toDate.'"' );
	$halfDayLeaves=$halfDayLeave/2;
	$empRows=$db->pdoQuery($halfDayQuery)->results();
	 ### Save all details
       // while($empRow = $halfDayStmt->results()) {
       foreach($empRows as $empRow){
		$row=$empRow['Date'].",".$empRow['state'].",0.5";
                array_push($empLeaveCount[$empVal]['leaveInfo'],$row);
        } 
	
	$totalLeaveCount=$noDataLeaves+$halfDayLeaves;
	$empLeaveCount[$empVal]['TotalCount']=$totalLeaveCount;
	$totalCount[$empVal]=$totalLeaveCount;
}

# Sort the NoData information in descending order
arsort($totalCount);

############################################################################################################
# In Tabular format
############################################################################################################
echo "\n####################################################################################################\n";
echo "\nIn Tabular format\n";
echo "\n-------------\n";
echo "\n####################################################################################################\n";
$tableFormat = "|%5.5s |%-29.30s |%-22.30s | %-20.30s| %-26.30s| %-25.30s |\n";
$currentYear=date('Y', strtotime($fromDate));
$nextYear=$currentYear+1;
printf($tableFormat, 'EmpId','Name',"Balance Leaves of $currentYear", "Used Leaves in $currentYear","Not Applied Leaves in $currentYear","CarryForwarded for $nextYear");
$csvArray=array();
foreach($totalCount as $empId => $countOfLeaves)
{
	# Get the total Approved leaves
	$tidQuery="select * from empleavetransactions where empid=$empId and startDate between $fromDate and $toDate and (approvalstatus='Approved') order by `startDate`";
	/*$tidStmt=$pdo->prepare($tidQuery);
	$tidStmt->execute(array(
                                $empId,
                                $fromDate,
                                $toDate
                            )
                     );*/
	$tidStmt=$db->pdoQuery($tidQuery);
	$tidRowCount=$tidStmt -> count($sTable = 'empleavetransactions', $sWhere = 'empid = "'.$empId.'" and startDate between "'.$fromDate.'" and "'.$toDate.'" and (approvalstatus="Approved")' );
	$rows=$db->pdoQuery($tidQuery)->results();
        $totalApprovedLeaves=0;
        foreach($rows as $row)
       // for($j=0;$j<$tidRowCount;$j++)
        {
                
                $totalApprovedLeaves=$totalApprovedLeaves+$row['count'];
        }
	$balanceLeavesForThisYear=getTotalLeaves($empId);
	$employeeName=getempName($empId);
	$carryForwardForNextYear=$balanceLeavesForThisYear-$countOfLeaves;
	printf($tableFormat,$empId,$employeeName,$balanceLeavesForThisYear,$totalApprovedLeaves,$countOfLeaves,$carryForwardForNextYear);
	$string="$empId,$employeeName,$balanceLeavesForThisYear,$totalApprovedLeaves,$countOfLeaves,$carryForwardForNextYear";
	array_push($csvArray,$string);
}

#####################################
# In CSV Format
#####################################
echo "\n####################################################################################################\n";
echo "\nIn csv format\n";
echo "\n-------------\n";
echo "\n####################################################################################################\n";
echo "EmpId,EmpName,Balance Leaves In $currentYear,Used Leaves In $currentYear,Not Applied Leaves In $currentYear,CarryForward Leaves For $nextYear\n";
foreach ($csvArray as $str) {
	echo $str."\n";	
}
echo "\n####################################################################################################\n";

#### Create a Mail format for each employee
echo "\nDeducting Employee Leaves - START\n";
echo "\n--------------------------\n";
foreach (array_keys($empLeaveCount) as $empId) {
	$empName=getempName($empId);
	$empManagerName=getManagerName($empId);
	if ($empLeaveCount[$empId]['TotalCount'] != 0) {
		echo "\nTotal Leave count for an employee ".$empName." is:  ".$empLeaveCount[$empId]['TotalCount']."\n";
		$string=$htmlHeader."Hi ".$empName.",<br><br>Below is the list of days which are automatically deducted by LMS.<br><br>";
		$tableHeader="<table class='table table-bordered'>
	        	<caption align='left'><font color='green'><b>For an Employee: ".$empName." Balance leaves <b>before</b> deduction: ".getTotalLeaves($empId)."</b></font></caption>
	        	<thead>
		        	<tr>
						<th>sl.no</th>
		               	<th>Emp Id</th>
			           	<th>Emp Name</th>
						<th>Manager Name</th>
		               	<th>Date</th>
		               	<th>Type of day</th>
			         	<th>Count</th>
		          	</tr>
        		</thead><tbody>";
		$subTableBody=$string.$tableHeader;
		$count=1;
		foreach ($empLeaveCount[$empId]['leaveInfo'] as $leaveInfo) {
			$con = split (",", $leaveInfo);	
			$date=$con[0];
			$dayCount=$con[2];
			if ($con[1] == "No Data") {
				$type="FullDay";
			} else {
				$type="HalfDay";
			}
			
			deductLeaves($empId,$type,$date,$dayCount);
			$subTableBody=$subTableBody. "<tr>
                				      	<td>".$count."</td>
						      			<td>".$empId."</td>
                                      	<td>".$empName."</td>
						      			<td>".$empManagerName."</td>
                                      	<td>".$date."</td>
                                        <td>".$type."</td>
                                        <td>".$dayCount."</td>
                                  	</tr>";
			$count++;
		}
		
		$subTableBody=$subTableBody."</tbody>
		   <tfoot align=\"right\">
		   	<tr>
				<td colspan='6'>
					<b>Total Number of Leaves deducted</b></td><td><b>".$empLeaveCount[$empId]['TotalCount']."</b>
				</td>
			   </tr>
			   <tr>
				<td colspan='6'>
					<b>Balance Leaves <b>After</b> Deduction</b></td><td><b>".getTotalLeaves($empId)."</b>
				</td>
			   </tr>
		   </tfoot>
	        </table>
		<br>
		<br>
		<br>Thanks,
		<br>DevOps Team
		</body>
		</html>";
		if (isset($mailList)) {
                        $toEmailList=$mailList;
                } else {
                        $toEmailList=getempMailId($empId);
			$toEmailList=$toEmailList.",neha.bhardwaj@ecitele.com";
                }
		sendMail($toEmailList,$subTableBody,"Automatic Leave Deduction");
		echo "Sending mail for : $toEmailList\n";
	} else {
		echo "\nTotal Leave count for an employee ".$empName." is:  ".$empLeaveCount[$empId]['TotalCount']."\n";
		echo "Not Sending mail for : $toEmailList\n";
	}
}
echo "\nDeducting Employee Leaves - END\n";
?>

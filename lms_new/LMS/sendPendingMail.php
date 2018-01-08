<html>
<body>
<?php
require_once 'Library.php';
$db=connectToDB();


function mailBody($query)
{
	$mailbody="";
	global $db;
	$result=$db->query($query);
	while ($res=$db->fetchAssoc($result))
	{
		$mailbody=$mailbody."<tr>
			<td>".getEmpName($res['empid'])."</td>
			<td>".$res['startdate']."</td>
			<td>".$res['enddate']."</td>
			<td>".$res['count']."</td>
			<td align=left>".$res['reason']."</td>
			</tr>";
	}
	return $mailbody;
}
function getDetail($tableName,$fieldName,$wherefield,$whereOperator,$whereValue) 
{
	global $db;
	$query="select $fieldName from $tableName where $wherefield $whereOperator '$whereValue'";
	$result= $db->query($query);
	$row = $db->fetchArray($result);
	return $row[$fieldName];

}
function printChildern($pendingChildern,$managerId)
{
        global $db;
	$mailbody="";
	$mailbody=$mailbody."<h3>Please Approve/Reject below pending Leaves for your team members</h3>";
	$mailbody=$mailbody."<h5>You can perform respective action by logging into http://blrtools/lms</h5>";
	if(count($pendingChildern)!=0) {
	   $mailbody=$mailbody."<table class='table table-bordered table-hover'>";
           $mailbody=$mailbody."<tr class='info'>
                                <th width=130>EmpName</th>
                                <th width=100>StartDate</th>
                                <th width=100>EndDate</th>
                                <th width=70>Count</th>
                                <th width=400>Reason</th>
                        </tr>";
	   for($i=0;$i<count($pendingChildern);$i++)
           {
		
                $query="SELECT empid,empname FROM  `emp` WHERE empid = '$pendingChildern[$i]'";
                $getNameQuery = $db->query($query);
                $getName = $db->fetchArray($getNameQuery);
		$query="SELECT empid,startdate,enddate,count,reason FROM `empleavetransactions` WHERE empid='".$getName['empid']."' and approvalstatus='Pending'";
		$mailbody=$mailbody.mailBody($query);
           }
       $mailbody=$mailbody."</table><br>";
       $managerEmailId=getDetail("emp","emp_emailid","empid","=",$managerId);
       #Send Mail to managers
       $to=$managerEmailId;
       $sub="Approval action pending for your team members";
       sendMail($to,$mailbody,$sub);
       }
}
$pendingEmp=array();
$pendingEmpName=array();
$pendingChildernofManager=array();
$query = $db->query("SELECT empid FROM  `empleavetransactions` WHERE approvalstatus =  'Pending'");
while($res = $db->fetchArray($query))
{
    array_push($pendingEmp,$res['empid']);
}
$getManagers=$db->query("SELECT empid,empname from emp where role='manager' and state='Active'");
while($getManagersRow=$db->fetchArray($getManagers))
{
	$childern=getChildren($getManagersRow['empid']);
	for ($j=0;$j<count($childern);$j++)
	{
		if(in_array($childern[$j],$pendingEmp))
			array_push($pendingChildernofManager,$childern[$j]);
	}
	printChildern($pendingChildernofManager,$getManagersRow['empid']);
	$pendingChildernofManager=array();
}
?>
</body>
</html>


<html>
<body>
<?php
require_once 'librarycopy1.php';
//require_once 'Library.php';
$db=connectToDB();


function mailBody($query)
{
	$mailbody="";
	global $db;
	//$result=$db->query($query);
	$result=$db->pdoQuery($query);
	//while ($res=$db->fetchAssoc($result))
	while ($res=$result->results())
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
	//$result= $db->query($query);
	$result= $db->pdoQuery($query);
	$row = $result->results();
	//$row = $db->fetchArray($result);
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
                                <th>EmpName</th>
                                <th>StartDate</th>
                                <th>EndDate</th>
                                <th>Count</th>
                                <th>Reason</th>
                        </tr>";
	  // for($i=0;$i<count($pendingChildern);$i++)
	   for($i=0;$i<$pendingChildern->rowCount();$i++)
           {
		
                $query="SELECT empid,empname FROM  `emp` WHERE empid = '$pendingChildern[$i]'";
               // $getNameQuery = $db->query($query);
                //$getName = $db->fetchArray($getNameQuery);
                $getNameQuery = $db->pdoQuery($query);
                $getName = $getNameQuery->results();
		$query="SELECT empid,startdate,enddate,count,reason FROM `empleavetransactions` WHERE empid='".$getName['empid']."' and approvalstatus='Pending'";
		$mailbody=$mailbody.mailBody($query);
           }
       $mailbody=$mailbody."</table><br>";
       $managerEmailId=getDetail("emp","emp_emailid","empid","=",$managerId);
       #Send Mail to managers
#       $to=$managerEmailId;
	$to="anilkumar.thatavarthi@ecitele.com,anilkumar.thatavarthi@ecitele.com";
       $sub="Approval action pending for your team members";
       sendMail($to,$mailbody,$sub);
       }
}
$pendingEmp=array();
$pendingEmpName=array();
$pendingChildernofManager=array();
//$query = $db->query("SELECT empid FROM  `empleavetransactions` WHERE approvalstatus =  'Pending'");
//while($res = $db->fetchArray($query))
$query = $db->pdoQuery("SELECT empid FROM  `empleavetransactions` WHERE (approvalstatus =?);",array('Pending'));
while($res = $query->results())
{
    array_push($pendingEmp,$res['empid']);
}
//$getManagers=$db->query("SELECT empid,empname from emp where role='manager' and state='Active'");
$getManagers=$db->pdoQuery("SELECT empid,empname from emp where (role=? and state=?);",array('manager','Active'));
//while($getManagersRow=$db->fetchArray($getManagers))
while($getManagersRow=$getManagers->results())
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
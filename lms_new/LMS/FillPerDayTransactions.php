<?php
$con = mysql_connect("localhost","root","eci12Telecom");
if (!$con)
{
  die('Could not connect: ' . mysql_error());
}
$db_selected =  mysql_select_db("lms",$con);
if ($db_selected) {
        echo "Connected to database\n";
} else {
        echo "Not Connected to database\n";
}

$startDate="2017-01-01";
$endDate=date('Y-m-d');

$userQuery="select * from perdaytransactions where date between '".$startDate."' and '".$endDate."' order by date desc";
$userQueryResult=mysql_query($userQuery,$con);

# Fill the count table in perdaytransaction table
$ValidLeaveTypes=array("FullDay"=>1,"WFH"=>0,"HalfDay"=>0.5,"First Half-HalfDay & second Half-WFH"=>0.5,"First Half-WFH & Second Half-HalfDay"=>0.5);
while($userQueryRow= mysql_fetch_assoc($userQueryResult)) {
		if(isset($ValidLeaveTypes[$userQueryRow['leavetype']])) {
        	$innerQuery="UPDATE `lms`.`perdaytransactions` SET `count` = '".$ValidLeaveTypes[$userQueryRow['leavetype']]."' WHERE `perdaytransactions`.`id` ='".$userQueryRow['id']."'";
        	echo "$innerQuery\n";
        	$innerQueryResult=mysql_query($innerQuery,$con);
		}
}

# Fill the status table in perdaytransaction table
$userQuery="select * from perdaytransactions where date between '".$startDate."' and '".$endDate."' order by date desc";
$userQueryResult=mysql_query($userQuery,$con);
while($userQueryRow= mysql_fetch_assoc($userQueryResult)) {
                $getStateQuery="select approvalstatus from empleavetransactions where transactionid='".$userQueryRow['transactionid']."'";
                $getStateResult=mysql_query($getStateQuery,$con);
                $getStateRow= mysql_fetch_assoc($getStateResult);
                if(isset($getStateRow['approvalstatus'])) {
                $innerQuery="UPDATE `lms`.`perdaytransactions` SET `status` = '".$getStateRow['approvalstatus']."' WHERE `perdaytransactions`.`transactionid` ='".$userQueryRow['transactionid']."'";
                echo "$innerQuery\n";
                $innerQueryResult=mysql_query($innerQuery,$con);
                }
}

?>

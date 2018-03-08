<?php
require_once ("librarycopy1.php");
$db=connectToDB();
$query = "SELECT id,date,day,holidayname,leavetype FROM holidaylist_blr where `date` like '%2016%' ORDER BY Date asc";

$result = $db->query($query);

$res=$result->columnCount();
echo $res;
?>
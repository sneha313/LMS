<?php
include "Library.php";
$db=connectToDb();
if(isset($_REQUEST['empid']))
{
			$splSelectionBox="";
			$splleaveTaken=array();
			$result = $db->query("SELECT birthdaydate FROM emp WHERE empid = '".$_REQUEST['empid']."' and state='Active'");
			$dob="";
			while($res = $db->fetchAssoc($result))
			{
				$dob = $res['birthdaydate'];
			}
			list($year,$month,$day) = explode('-', $dob);
			$thismonth = date("m");$thisday = date("d");
			$sql = $db->query("select id, specialleave from specialleaves");
			$sql1 = $db->query("select splleavetaken from empsplleavetaken where empid = '".$_REQUEST['empid']."'");
			if($sql1)
			{
				while($row1 = $db->fetchAssoc($sql1))
				{
					$splleavesString = $row1['splleavetaken'];
					$splleaveTaken = explode(':', $splleavesString);
				}
			}
			$countRows=$db->countRows($sql);
			$teamEventFullTaken=0;
			$teamEventHalfTaken=0;
			while ($row=$db->fetchAssoc($sql))
			{
				$splPending=$row['id']."P";
				$splApproved=$row['id']."A";
				if ((!in_array($splPending,$splleaveTaken)) && (!in_array($splApproved,$splleaveTaken)))
				{
					$a=$row['specialleave'];
					
					if(!preg_match("/Employee Birthday/",$a) && !preg_match("/Team Event: 1 day/",$a) && !preg_match("/Team Event: 0.5 day/",$a))
					{
						$splSelectionBox.='<option value='.$row['id'].'>'.$row['specialleave'].'</option>';
					}
				} else {
					if (preg_match("/Team Event: 1 day/",$row['specialleave']))
					{
						$teamEventFullTaken=1;
					}
					if (preg_match("/Team Event: 0.5 day/",$row['specialleave']))
					{
						$teamEventHalfTaken=1;
					}
			    }
			}
			$sql = $db->query("select id, specialleave from specialleaves");
			while ($row=$db->fetchAssoc($sql))
			{
				if (preg_match("/Team Event: 1 day/",$row['specialleave']) && $teamEventFullTaken == 0 && $teamEventHalfTaken == 0) {
					$splSelectionBox.='<option value='.$row['id'].'>'.$row['specialleave'].'</option>';
				}
				if (preg_match("/Team Event: 0.5 day/",$row['specialleave']) && $teamEventFullTaken ==0) {
		                    $splSelectionBox.='<option value='.$row['id'].'>'.$row['specialleave'].'</option>';
                		}
			}
			echo $splSelectionBox;
}
if(isset($_REQUEST['dept']))
{
	if ($_REQUEST['dept']=="ALL") {
		$empName=$empName.'<option value="ALL">ALL</option>';
		echo $empName;
	} else {
		$query="SELECT empid,empname FROM emp WHERE state='Active' and dept LIKE '".$_REQUEST['dept']."' ORDER BY `emp`.`empname` ASC";
		$result = $db->query($query);
		$empName=$empName.'<option value="ALL">ALL</option>';
		while($row = $db->fetchAssoc($result))  
		{
			$empName=$empName. '<option value="'.$row["empid"].'">';
	        $empName=$empName. $row["empname"];
	        $empName=$empName. '</option>';
		}
		echo $empName;
	}
}
?>

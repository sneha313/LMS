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

if(isset($_REQUEST['empLoc'])) {
	$querydept=$db->query("select distinct subDept, ID from departments where deptStatus='Active' and deptLocation = '".$_REQUEST['empLoc']."' ORDER BY subDept ASC");
	if($db->countRows($querydept)>0)
	{
		## Department name
		$department = '<option value="ALL">';
		$department = $department . "ALL";
		$department = $department . '</option>';
		while($deptrow = $db->fetchAssoc($querydept)){
			$department = $department . '<option value="'.$deptrow['ID'].'">';
			$department = $department . $deptrow["subDept"];
			$department = $department . '</option>';
		}
		echo $department;
	}
	else {
		echo "<script>BootstrapDialog.alert('No departments present in this location')</script>";
	}
}

if(isset($_REQUEST['subDeptLoc'])) {
	$querydept=$db->query("select distinct subDept, ID from departments where deptStatus='Active' and deptLocation = '".$_REQUEST['subDeptLoc']."' ORDER BY subDept ASC ");
	if($db->countRows($querydept)>0)
	{
		## Department name
		$department = '<option value="ALL">';
		$department = $department . "ALL";
		$department = $department . '</option>';
		while($deptrow = $db->fetchAssoc($querydept)){
			$department = $department . '<option value="'.$deptrow['ID'].'">';
			$department = $department . $deptrow["subDept"];
			$department = $department . '</option>';
		}
		echo $department;
	}
	else {
		echo "<script>BootstrapDialog.alert('No departments present in this location')</script>";
	}
}

if(isset($_REQUEST['mainDeptLoc'])) {
	$querydept=$db->query("select distinct mainDept from departments where deptStatus='Active' and deptLocation = '".$_REQUEST['mainDeptLoc']."' ORDER BY mainDept ASC ");
	if($db->countRows($querydept)>0)
	{
		## Department name
		$department = '<option value="ALL">';
		$department = $department . "ALL";
		$department = $department . '</option>';
		while($deptrow = $db->fetchAssoc($querydept)){
			$department = $department . '<option value="'.$deptrow['mainDept'].'">';
			$department = $department . $deptrow["mainDept"];
			$department = $department . '</option>';
		}
		echo $department;
	}
	else {
		echo "<script>BootstrapDialog.alert('No departments present in this location')</script>";
	}
}

/*if(isset($_REQUEST['subDepartment'])) {
	$querydept=$db->query("select distinct emp_emailid, empid from emp where dept='".$_REQUEST['subDepartment']."' ");
	if($db->countRows($querydept)>0)
	{
		## Department name
		$email = '<option value="ALL">';
		$email = $email . "ALL";
		$email = $email . '</option>';
		while($deptrow = $db->fetchAssoc($querydept)){
			$email = $email . '<option value="'.$deptrow['empid'].'">';
			$email = $email . $deptrow["emp_emailid"];
			$email = $email . '</option>';
		}
		echo $email;
	}
	else {
		echo "<script>BootstrapDialog.alert('No employee present in this department')</script>";
	}
}*/
// AJAX View Employee department wise FROM JQUERY
if ( isset($_GET['view']) && 0 < intval($_GET['view'])) {
	//to check employee department wise
	//$empname[];
	$depquery=$db->query("select * from departments where ID=". intval($_GET['view']));
	$emp="<table class='table table-hover' id='empList'>";
	if($db->countRows($depquery)>0)
	{
		while($deptrow = $db->fetchAssoc($depquery)){
			//$depres=$db->fetchAssoc($depquery);
			$maindept=$deptrow['mainDept'];
			$subdept=$deptrow['subDept'];
			$deptloc=$deptrow['deptLocation'];
		$empquery = $db->query("select * from emp WHERE dept='".$subdept."' and state='Active' and location='".$deptloc."'");
		if($db->countRows($empquery)>0)
		{
			for ( $i = 0 ; $i < $db->countRows($empquery); $i++ ) {
				$deptres = $db->fetchArray($empquery);
				$empname[]=$deptres['empname'];
			}
			foreach($empname as $emplist){
				$emp=$emp.'<tr><td>'.$emplist.'</td></tr>';
			}
			$emp=$emp."</table>";
			echo $emp;
		}
	}
}
}
?>

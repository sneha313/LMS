<?php
//include "Library.php";
include "librarycopy1.php";
$db=connectToDb();
if(isset($_REQUEST['empid']))
{
			$splSelectionBox="";
			$splleaveTaken=array();
			//$result = $db->query("SELECT birthdaydate FROM emp WHERE empid = '".$_REQUEST['empid']."' and state='Active'");
			$query="SELECT birthdaydate FROM emp WHERE empid = '".$_REQUEST['empid']."' and state='Active'";
			$result = $db->pdoQuery($query)->results();
			$dob="";
			//while($res = $result->results())
			//while($res = $db->fetchAssoc($result))
				foreach($result as $res)
			{
				$dob = $res['birthdaydate'];
			}
			list($year,$month,$day) = explode('-', $dob);
			$thismonth = date("m");$thisday = date("d");
			//$sql = $db->query("select id, specialleave from specialleaves");
			$queryrow="select id, specialleave from specialleaves";
			$sql = $db->pdoQuery($queryrow)->results();
			
			//$sql1 = $db->query("select splleavetaken from empsplleavetaken where empid = '".$_REQUEST['empid']."'");
			$splquery="select splleavetaken from empsplleavetaken where empid = '".$_REQUEST['empid']."'";
			$sql1 = $db->pdoQuery($splquery);
			if($sql1)
			{
				$splrow=$db->pdoQuery($splquery)->results();
				//while($row1 = $sql1->results())
				//while($row1 = $db->fetchAssoc($sql1))
					foreach($splrow as $row1)
				{
					$splleavesString = $row1['splleavetaken'];
					$splleaveTaken = explode(':', $splleavesString);
				}
			}
			//$countRows=$sql->rowCount();
			$countRows=$db->countRows($sql);
			$teamEventFullTaken=0;
			$teamEventHalfTaken=0;
			//while ($row=$sql->results())
			//while ($row=$db->fetchAssoc($sql))
				foreach($sql as $row)
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
			//$sql = $db->query("select id, specialleave from specialleaves");
			$queryrecord="select id, specialleave from specialleaves";
			$sql = $db->pdoQuery($queryrecord)->results();
			//while ($row=$sql->results())
			//while ($row=$db->fetchAssoc($sql))
				foreach($sql as $row)
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


if(isset($_REQUEST['empLoc'])) {
	$query="select distinct subDept, ID from departments where deptStatus='Active' and deptLocation = '".$_REQUEST['empLoc']."' ORDER BY subDept ASC";
	$querydept=$db->pdoQuery($query);
	$p=$querydept -> count($sTable = 'departments', $sWhere = 'deptStatus = "Active" and deptLocation = "'.$_REQUEST['empLoc'].'"' );
	if($p>0)
	//$querydept=$db->query("select distinct subDept, ID from departments where deptStatus='Active' and deptLocation = '".$_REQUEST['empLoc']."' ORDER BY subDept ASC");
	//if($db->countRows($querydept)>0)
	{
		$deptrows=$db->pdoQuery($query)->results();
		## Department name
		$department = '<option value="ALL">';
		$department = $department . "ALL";
		$department = $department . '</option>';
		//while($deptrow = $querydept->results()){
		//while($deptrow = $db->fetchAssoc($querydept)){
		foreach($deptrows as $deptrow){
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
	$queryrow="select distinct subDept, ID from departments where deptStatus='Active' and deptLocation = '".$_REQUEST['subDeptLoc']."' ORDER BY subDept ASC ";
	$querydept=$db->pdoQuery($queryrow);
	//if($querydept->rowCount()>0)
		$p=$querydept -> count($sTable = 'departments', $sWhere = 'deptStatus = "Active" and deptLocation = "'.$_REQUEST['subDeptLoc'].'"' );
				
	//$querydept=$db->query("select distinct subDept, ID from departments where deptStatus='Active' and deptLocation = '".$_REQUEST['subDeptLoc']."' ORDER BY subDept ASC ");
	if($p>0)
	{
		## Department name
		$department = '<option value="ALL">';
		$department = $department . "ALL";
		$department = $department . '</option>';
		//while($deptrow = $querydept->results()){
		$deptrows=$db->pdoQuery($queryrow)->results();
		//while($deptrow = $db->fetchAssoc($querydept)){
		foreach($deptrows as $deptrow){
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
	$query="select distinct mainDept from departments where deptStatus='Active' and deptLocation = '".$_REQUEST['mainDeptLoc']."' ORDER BY mainDept ASC ";
	$querydept=$db->pdoQuery($query);
	$p=$querydept -> count($sTable = 'departments', $sWhere = 'deptStatus = "Active" and deptLocation = "'.$_REQUEST['mainDeptLoc'].'"' );
	
	if($p>0)
	//$querydept=$db->query("select distinct mainDept from departments where deptStatus='Active' and deptLocation = '".$_REQUEST['mainDeptLoc']."' ORDER BY mainDept ASC ");
	//if($db->countRows($querydept)>0)
	{
		$deptrows=$db->pdoQuery($query)->results();
		## Department name
		$department = '<option value="ALL">';
		$department = $department . "ALL";
		$department = $department . '</option>';
		///while($deptrow = $querydept->results()){
		//while($deptrow = $db->fetchAssoc($querydept)){
		foreach($deptrows as $deptrow){
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
	//$depquery=$db->query("select * from departments where ID=". intval($_GET['view']));
	$query="select * from departments where ID=". intval($_GET['view']);
	$depquery=$db->pdoQuery($query);
	$depcount=$depquery -> count($sTable = 'departments', $sWhere = 'ID = "'.intval($_GET['view']).'"' );
	
	$emp="<table class='table table-hover' id='empList'>";
	//if($depquery->rowCount()>0)
	if($depcount>0)
	{
		$deptrows=$db->pdoQuery($query)->results();
		//while($deptrow = $depquery->results()){
		//while($deptrow = $db->fetchAssoc($depquery)){
			//$depres=$db->fetchAssoc($depquery);
			
		foreach($deptrows as $deptrow){
			$maindept=$deptrow['mainDept'];
			$subdept=$deptrow['subDept'];
			$deptloc=$deptrow['deptLocation'];
		//$empquery = $db->pdoQuery("select * from emp WHERE dept='".$subdept."' and state='Active' and location='".$deptloc."'");
		//if($empquery->rowCount()>0)
		$emprow="select * from emp WHERE dept='".$subdept."' and state='Active' and location='".$deptloc."'";
		$empquery = $db->pdoQuery($emprow);
		$p=$empquery -> count($sTable = 'emp', $sWhere = 'dept = "'.$subdept.'" and state = "Active" and location="'.$deptloc.'"' );
				
		if($p>0)
		{
			//for ( $i = 0 ; $i < $db->countRows($empquery); $i++ ) {
				$deptresult = $db->pdoQuery($emprow)->results();
			foreach($deptresult as $deptres){
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
# Get the employees from a department
if(isset($_REQUEST['dept']))
{
	if ($_REQUEST['dept']=="ALL") {
		$empName=$empName.'<option value="ALL">ALL</option>';
		echo $empName;
	} else {
		$query="SELECT empid,empname FROM emp WHERE state='Active' and dept LIKE '".$_REQUEST['dept']."' ORDER BY `emp`.`empname` ASC";
		//$result = $db->query($query);
		$result = $db->pdoQuery($query)->results();
		$empName=$empName.'<option value="ALL">ALL</option>';
		//while($row = $result->results())
		//while($row = $db->fetchAssoc($result))
			foreach($result as $row)
		{
			$empName=$empName. '<option value="'.$row["empid"].'">';
			$empName=$empName. $row["empname"];
			$empName=$empName. '</option>';
		}
		echo $empName;
	}
}

# Get the department based on the location
if(isset($_REQUEST['location']))
{
	if ($_REQUEST['location']=="ALL") {
		$query="SELECT distinct(dept) FROM `emp` ORDER BY dept ASC";
	} else {
		$query="SELECT distinct(dept) FROM `emp` where location='".$_REQUEST['location']."' ORDER BY dept ASC";
	}
	//$result = $db->query($query);
	$result = $db->pdoQuery($query)->results();
	$empName='<option value="None">None</option>';
	$empName=$empName.'<option value="ALL">ALL</option>';
	//while($row = $result->results())
	//while($row = $db->fetchAssoc($result))
		foreach($result as $row)
	{
		$empName=$empName. '<option value="'.$row["dept"].'">';
		$empName=$empName. $row["dept"];
		$empName=$empName. '</option>';
	}
	echo $empName;
}
/*
# Get employee based on the location
if(isset($_REQUEST['Employeelocation']))
{
	$query="SELECT e.id, e.empname, e.empid, e.empusername, et.carryforwarded, et.balanceleaves FROM emp e,emptotalleaves et where e.empid=et.empid and e.location='".$_REQUEST['Employeelocation']."' and (et.carryforwarded + et.balanceleaves)< -5 order by e.empname asc";
	$result = $db->query($query);
	//$result = $db->pdoQuery($query);
	$empName="
			<br><br><table class='table table-bordered' id='leavedeductiontable' name='leavedeductiontable' style='width:95%;' align='center'>
			<tr class='info' id='leavedeductiontablerow'>
							<th style='display:none;'>Id</th>
	                    	<th>Emp Id</th>
	                    	<th>Emp userName</th>
	                    	<th>Emp Name</th>
	                    	<th>Carry Forward Leave</th>
	                    	<th>Balance Leave</th>
	                    	<th>Total leave</th>
	                    	<th>Action</th>
	                    </tr>";
	//while($row = $result->results())
	while($row = $db->fetchAssoc($result))
	{
		$empName=$empName. '<tr id="record"><td style="display:none;">';
		$empName=$empName. $row["id"];
		$empName=$empName. '</td><td>';
		$empName=$empName. $row["empid"];
		$empName=$empName. '</td>';
		$empName=$empName. '<td>';
		$empName=$empName. $row["empusername"];
		$empName=$empName. '</td>';
		$empName=$empName. '<td>';
		$empName=$empName. $row["empname"];
		$empName=$empName. '</td>';
		$empName=$empName. '<td>';
		$empName=$empName. $row["carryforwarded"];
		$empName=$empName. '</td>';
		$empName=$empName. '<td>';
		$empName=$empName. $row["balanceleaves"];
		$empName=$empName. '</td>';
		$empName=$empName. '<td>';
		$empName=$empName. ($row['carryforwarded']+$row['balanceleaves']);
		$empName=$empName. '</td>';
		//$empName=$empName. '<td><a data-id="row-'.$row['empid'].'" href="javascript:editLeave(' . $row['id'] . ');" class="btn btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
		$empName=$empName. '<td><button class="btn btn-primary" id="hrleavededuction"><a data-id="'.$row['empid'].'" href="leavedeductionbyhr.php?empid='. $row['empid'].'" class="btn btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></button>
		</td></tr>';
	}
	$empName=$empName. '</table>';
	echo $empName;
	//echo json_encode($empName);
	echo "<script>
	$('document').ready(function() {
		$('#hrleavededuction').click(function(){
				    alert('hello');
		}); 
	});";
}*/
?>
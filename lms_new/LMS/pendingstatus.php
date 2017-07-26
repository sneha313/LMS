<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
?>
<center>
	<?php
	function emppendingleaves($empid)
	{
		global $db;
		echo "<center><h3><u>Employee Pending Leaves</u></h3></center><br><br>";
		$query="select transactionid,startdate,enddate,reason,approvalstatus,approvalcomments from empleavetransactions where empid='".$empid."' and approvalstatus='Pending'";
		$result=$db->query($query);
		echo '<table id="table-2" cellpadding="0" cellspacing="0" border="3"  class="display">
					  <thead>
					  <tr>
					  <th>Start Date</th>
					  <th>End Date</th>
					  <th>Reason</th>
					  <th>Approval Status</th>
					  <th>Comments</th>
					  </tr></thead>
					  <tbody>';
		for($i=0;$i<$db->countRows($result);$i++)
		{
			$row=$db->fetchArray($result);
			echo '<td>'.$row['startdate'].'</td>';
			echo '<td>'.$row['enddate'].'</td>';
			echo '<td>'.$row['reason'].'</td>';
			echo '<td>'.$row['approvalstatus'].'</td>';
			echo '<td>'.$row['approvalcomments'].'</td></tr>';
		}
		echo "<tbody></table>";
	}
	
	$rolequery=$db->query("select role from emp where state='Active' and empid=".$_SESSION['u_empid']);
	$rolerow=$db->fetchAssoc($rolequery);
	if($rolerow['role']=="manager")
	{
		echo "<center><h3><u>Team Pending Leaves</u></h3></center><br><br>";
		echo '<table id="table-2" cellpadding="0" cellspacing="0" border="3"  class="display">
					  <thead>
					  <tr>
					  <th>Employee Name</th>
					  <th>Start Date</th>
					  <th>End Date</th>
					  <th>Reason</th>
					  <th>Approval Status</th>
					  </tr></thead>
					  <tbody>';
		$emparray=getemp($_SESSION['u_empid']);
		for($i=0;$i<sizeof($emparray);$i++)
		{
			$result=$db->query("select * from empleavetransactions where empid='".$emparray[$i]."' and approvalstatus='Pending'");
			$empnamequery=$db->query("select empname from emp where state='Active' and empid=".$emparray[$i]);
			$emprow=$db->fetchAssoc($empnamequery);
			for($j=0;$j<$db->countRows($result);$j++)
			{
				$row=$db->fetchAssoc($result);
				echo '<tr><td>'.$emprow['empname'].'</td>';
				echo '<td>'.$row['startdate'].'</td>';
				echo '<td>'.$row['enddate'].'</td>';
				echo '<td>'.$row['reason'].'</td>';
				echo '<td>'.$row['approvalstatus'].'</td></tr>';
			}
		}
		echo "<tbody></table><br><br><br>";
		echo '<div>';
		emppendingleaves($_SESSION['u_empid']);
		echo '</div>';
	}

	if($rolerow['role']=="user")
	{
		emppendingleaves($_SESSION['u_empid']);
	}
	?>

</center>

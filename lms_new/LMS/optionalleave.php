<?php
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
$db = connectToDB();


$query="select * from empoptionalleavetaken where empid='".$_SESSION['u_empid']."'";
$result = $db->query($query);
echo "<table id=\"table-2\">
		<thead>
		<th>Date</th>
		<th>Optional Leave</th>
		<th>Status</th>
		</thead>
		<tbody>";
if($db->hasRows($result)) {
	while($row=$db->fetchAssoc($result)) {
		echo "<tr>
				<td>".$row['date']."</td>
				<td>".$row['leave']."</td>
				<td>".$row['state']."</td>
			</tr>";
	}
} else {
	echo "<tr><td colspan=\"3\">No Optional Holdays applied</td></tr>";
}
echo "<tbody><table>";
?>
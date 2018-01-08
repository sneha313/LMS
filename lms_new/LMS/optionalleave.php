<?php
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
$db = connectToDB();


$query="select * from empoptionalleavetaken where empid='".$_SESSION['u_empid']."'";
$result = $db->query($query);
echo "<div class='panel panel-primary'>
		<div class='panel-heading text-center'>
			<strong style='font-size:20px;'>Optional Leave Applied</strong>
		</div>
		<div class='panel-body'>
		<table class='table table-hover table-bordered'>
		<thead>
		<tr class='info'>
		<th>Date</th>
		<th>Optional Leave</th>
		<th>Status</th>
		</thead>
		</tr>
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
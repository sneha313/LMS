<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
?>
<html>
<head>
<?php
$sql=$db->query("select * from emptotalleaves");
$count =0;
$leaveCountPerYear=25;
echo "<table border=1>
			<tr>
				<th>No.</th>
				<th>Empid</th>
				<th>Emp Name</th>
				<th>2014-2015 (Carry forwarded)</th>
				<th>2015 (Balance leaves on 31st of Dec out of ".$leaveCountPerYear." PTO's)</th>
				<th>Tatal balance leaves on 31st in 2015 (Additon of carry forwarded +balance leaves)</th>
				<th>Reset Status</th>
				<th>2015-2016 (Carry forwarded)</th>
				<th>2016 (Balance leaves)</th>
				<th>Total in 2016</th>
			</tr>";
while($row1 = $db->fetchArray($sql)){
	echo "<tr>";
	$count=$count+1;	
	echo "<td align='center'>".$count."</td>";
	echo "<td align='center'>".$row1['empid']."</td>";
	echo "<td align='center'>".getempName($row1['empid'])."</td>";
	echo "<td align='center'>".$row1['carryforwarded']."</td>";
	echo "<td align='center'>".$row1['balanceleaves']."</td>";
	echo "<td align='center'>".($row1['balanceleaves']+ $row1['carryforwarded'])."</td>";
	$leaves = $row1['balanceleaves']+ $row1['carryforwarded'];
	
	$reset="No";
	if($leaves>30){
		$leaves = 30;
		$reset="Yes";
	}
	$query123="UPDATE emptotalleaves SET `carryforwarded` = '".$leaves."',`previous year`='2015',`present year`='2016',`balanceleaves` = '".$leaveCountPerYear."' WHERE empid = '".$row1['empid']."'";
	$sql1=$db->query($query123);
	if($sql1) {
		echo "<td align='center'>".$reset."</td>";
		echo "<td align='center'>".$leaves."</td>";
		echo "<td align='center'>".$leaveCountPerYear."</td>";
		echo "<td align='center'>".($leaves+$leaveCountPerYear)."</td>";
		echo "</tr>";
	}
	$insertRow="INSERT INTO `lms`.`resetleaves` (`id`, `empid`, `empname`, `PY-CR`, `PY-BL`, `total-BL`, `resetStatus`, `CY-CR`, `CY-BL`, `CY-Used`) 
					VALUES ('".$count."', '".$row1['empid']."', '".getempName($row1['empid'])."', '".$row1['carryforwarded']."', '".$row1['balanceleaves']."',
							 '".($row1['balanceleaves']+ $row1['carryforwarded'])."', '".$reset."', '".$leaves."', '".$leaveCountPerYear."', '');";
	$db->query($insertRow);
}
echo "</table>";
?>
</body>
</html>


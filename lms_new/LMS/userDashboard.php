<?php
session_start();
require_once 'Library.php';
require_once 'attendenceFunctions.php';
require_once 'generalFunctions.php';
error_reporting("E_ALL");
$db=connectToDB();
?>
<html>
<body>
<div class="container-fluid">
	<div class="row">
	<?php include 'selfleavestatus.php';?>
		<div class="col-sm-12">
			<div class="panel panel-primary">
				<div class="panel-heading text-center">
					<strong style="font-size:20px;">Team Member's on Leave</strong>
				</div>
				<div class="panel-body">
					<table class='table table-hover'>
						<thead>
							<tr class='success'>
								<th>Emp Name</th>
								<th>Date</th>
								<th>Leave Type</th>
							</tr>
						</thead>
						<?php 
							$query="select empid from emp where managerid = (SELECT managerid from emp where empid='".$_SESSION['u_empid']."')";
							$sql = $db -> query($query);
						?>
				 		<tbody>
					 		<?php 
							 	if($db->countRows($sql) > 0){
							 		$query1="select p.date, p.leavetype, e.empname from perdaytransactions p, emp e where e.empid=p.empid and p.date=CURDATE() and e.dept=(SELECT dept from emp where empid='".$_SESSION['u_empid']."') and (p.status='Pending') group by p.empid order by p.date" ;
							 		//echo $query1;
							 		$sql1 = $db -> query($query1);
							 		if($db->countRows($sql1)>0){
							 		for($i=0;$i<$db->countRows($sql1);$i++)
							 		{
							 		$row=$db->fetchAssoc($sql1);
							 		echo '<tr>
							 		<td class="info">'.$row['empname'].'</td>
							 		<td class="warning">'.$row['date'].'</td>
							 		<td class="danger">'.$row['leavetype'].'</td>
							 		</tr>';	 
							 		}
							 		}
							 		else
							 		{
							 			echo '<tr>
							 				<td colspan="3" class="info text-center">All Team members are present</td>
							 			</tr>';
							 		}
							 	}
							 	?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="panel panel-primary">
				<div class="panel-heading text-center">
					<strong style="font-size:20px;">Employee Birthday this week</strong>
				</div>
				<div class="panel-body">
					<table class="table">
						<thead>
						  <tr class="success">
							<th>Emp Name</th>
							<th>Birthday Date</th>
						  </tr>
						</thead>
						<tbody>
							<?php
								 $result = $db->query("SELECT empname, birthdaydate FROM emp WHERE WEEK(birthdaydate) = WEEK(CURDATE()) AND MONTH(birthdaydate) = MONTH(CURDATE())ORDER BY DAYOFYEAR(`birthdaydate`) ASC");
								  for($i=0;$i<$db->countRows($result);$i++)
									{
										$row=$db->fetchArray($result);
									  	$empname=$row['empname'];
									  	$birthdaydate=$row['birthdaydate'];
									  	//only month and date extract from birthdaydate
									  	$birthday=date("d F", strtotime($birthdaydate));
								  	echo '<tr>';
									echo '<td class="info">'.$row['empname'].'</td>';
									echo '<td class="warning">'.$birthday.'</td>';
								  	echo '</tr>';
								  }
								
								?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div class="col-sm-6">
			<div class="panel panel-primary">
				<div class="panel-heading text-center">
					<strong style="font-size:20px;">Holidays in Current Month</strong>
				</div>
				<div class="panel-body">
					<table class="table">
						<thead>
						  <tr class="success">
							<th>Date</th>
							<th>Holiday Name</th>
							<th>Leave Type</th>
						  </tr>
						</thead>
						<tbody>
							<?php
								 $curdate = date('Y-m-d', time());
								 $res = $db->query("SELECT * FROM `holidaylist` WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())");
								 for($i=0;$i<$db->countRows($res);$i++)
								{
									$row=$db->fetchArray($res);
									$date=$row['date'];
									$holidayname=$row['holidayname'];
									echo '<tr>';
										echo '<td class="info">'.$row['date'].'</td>';
										echo '<td class="warning">'.$row['holidayname'].'</td>';
										echo '<td class="danger">'.$row['leavetype'].'</td>';
									echo '</tr>';
								}
							?>
						</tbody>
					</table>
				</div><!-- panel body div close -->
			</div><!-- panel div close -->
		</div><!-- 6 column div close -->
	</div><!-- row div close -->
</div>
</body>
</html>
	
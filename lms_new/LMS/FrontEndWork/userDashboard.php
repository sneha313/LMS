<?php
session_start();
require_once 'Library.php';
require_once 'attendenceFunctions.php';
require_once 'generalFunctions.php';
error_reporting("E_ALL");
$db=connectToDB();
?>
	
<div class="container-fluid">
	<div class="row">
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
							$managerid = $_SESSION['u_managerid'];
							$empquery = "select empid,empname from emp where managerid='".$managerid."' and state='Active'";
							$empresult=$db->query($empquery);
							$emprow=$db->fetchAssoc($empresult);
							$employeeListString = getempListString($empquery);
							//$date='2013-03-06';
							$query = "SELECT a.empid, a.date, a.leavetype FROM perdaytransactions a,empleavetransactions b where a.date > DATE_SUB(NOW(), INTERVAL 1 MONTH) and a.date < DATE_SUB(NOW(), INTERVAL -1 MONTH ) and a.empid in ($employeeListString) and a.status='Approved'";
							$sql = $db -> query($query);
						?>
				 		<tbody>
					 			<?php 
							 		if($db->countRows($sql) > 0){
							 			$row=$db->fetchAssoc($sql);
							 			echo '<tr>
							 			<td class="info">'.$emprow['empname'].'</td>
							 			<td class="danger">'.$row['date'].'</td>
							 			<td class="warning">'.$row['leavetype'].'</td>
							 			</tr>';
							 			 
							 		}
							 		else 
							 		{
							 			echo '<tr>
							 				<td colspan="3" class="info text-center">All Team members are present</td>
							 			</tr>';
							 			
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
					<strong style="font-size:20px;">Birthday's List this week</strong>
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
								  $result = $db->query("SELECT empname, birthdaydate FROM emp WHERE DATE_ADD(birthdaydate, INTERVAL YEAR(CURDATE())-YEAR(birthdaydate) + IF(DAYOFYEAR(CURDATE()) >= DAYOFYEAR(birthdaydate),1,0) YEAR) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
								 
								  for($i=0;$i<$db->countRows($result);$i++)
									{
										$row=$db->fetchArray($result);
									  	$empname=$row['empname'];
									  	$birthdaydate=$row['birthdaydate'];
								  	echo '<tr>';
									echo '<td class="info">'.$row['empname'].'</td>';
									echo '<td class="warning">'.$row['birthdaydate'].'</td>';
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
					<strong style="font-size:20px;">Holiday in Current Month</strong>
				</div>
				<div class="panel-body">
					<table class="table">
						<thead>
						  <tr class="success">
							<th>Date</th>
							<th>Holiday Name</th>
						  </tr>
						</thead>
						<tbody>
							<?php
								 $curdate = date('Y-m-d', time());
								 $res = $db->query("SELECT date, holidayname FROM holidaylist WHERE date > DATE_SUB(NOW(), INTERVAL 1 MONTH) and date < DATE_SUB(NOW(), INTERVAL -1 MONTH )");
								 for($i=0;$i<$db->countRows($res);$i++)
								{
									$row=$db->fetchArray($res);
									$date=$row['date'];
									$holidayname=$row['holidayname'];
									echo '<tr>';
										echo '<td class="info">'.$row['date'].'</td>';
										echo '<td class="warning">'.$row['holidayname'].'</td>';
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

	
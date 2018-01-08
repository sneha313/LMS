<?php
	session_start();
	require_once 'Library.php';
	$db=connectToDB();
?>
<?php
echo '<html>
	<head>
	<style>
		div.arrow {
			background: transparent url(public/images/arrows.png) no-repeat scroll 0px
				-16px;
			width: 16px;
			height: 16px;
			display: block;
		}
		</style>
		<script type="text/javascript">	
        	$("document").ready(function(){		
            	$(".leavehistory tr:odd").addClass("odd");
            	$(".leavehistory tr:not(.odd)").hide();
            	$(".leavehistory tr:first-child").show();
            	$(".leavehistory tr.odd").click(function(){
	           		$(this).next("tr").toggle();
	            	$(this).find(".arrow").toggleClass("up");
            	}); 
				$( "#tabs" ).tabs();
			});
   		</script>
	</head>
	<body>';
	echo '<div class="panel panel-primary">
		<div class="panel-heading text-center">
			<strong style="font-size:20px;">Approved/Cancelled Leave Details</strong>
		</div>
		<div class="panel-body">';
			$sql=$db->query("SELECT DISTINCT YEAR(startdate) as year FROM empleavetransactions where empid='".$_SESSION['u_empid']."' order by year desc");
			$distinctYears=array(); 
			$leaveCount=$db->countRows($sql);
			if($leaveCount == 0) {
				echo "<div id='tabs'><ul><div id='Info'><tr><td>No Data Available</td></tr></div></ul></div>";
			} else {
				echo '<div id="tabs">
                <ul>';
			}
			for($i=0;$i<$db->countRows($sql);$i++)
			{
				$row=$db->fetchArray($sql);
				echo "<li><a href='#".$row['year']."'>".$row['year']."</a></li>";
				array_push($distinctYears,$row['year']);
			}
			echo "</ul>";
			foreach ($distinctYears as $year) {
				echo "<div id='".$year."'>";
					# Display Approved leaves based on month wise
					$months = array("01"=>"Jan", "02"=>"Feb", "03"=>"Mar", "04"=>"Apr", "05"=>"May", "06"=>"June", "07"=> "July", "08"=>"Aug", "09"=>"Sept","10"=>"Oct","11"=>"Nov", "12"=>"Dec");
					echo "<table class='table table-hover ' ><tr>";
					foreach ($months as $monthID => $monthName) {
						echo "<th>$monthName</th>";
					}
					echo "</tr><tr>";
					foreach ($months as $monthID => $monthName) {
						$startDayInMonth="$year-$monthID-01";
						$EndDayInMonth="$year-$monthID-31";
						$query="select SUM(count) as noDays from perdaytransactions where date between '".$startDayInMonth."' and '".$EndDayInMonth."' and empid='".$_SESSION['u_empid']."' and status='Approved'";
						$getApprovedLeavesInMonth=$db->query($query);
						$getApprovedLeavesInMonthRow=$db->fetchArray($getApprovedLeavesInMonth);
						$numOfDays=(float)$getApprovedLeavesInMonthRow['noDays'];
						if ($numOfDays <= 0) {
							echo "<td>0</td>";
						} else {
							echo "<td>".$numOfDays."</td>";
						}
					}
					echo "</tr></table>";	
					$sql=$db->query("select * from empleavetransactions where empid='".$_SESSION['u_empid']."' and startDate between '".$year."-01-01' and '".$year."-12-31' and
					(approvalstatus='Approved' or approvalstatus='Cancelled' or approvalstatus='Deleted') order by startDate");
	
					$onlyApprovedLeaves=$db->query("select * from empleavetransactions where empid='".$_SESSION['u_empid']."' and startDate between '".$year."-01-01' and '".$year."-12-31' and
					(approvalstatus='Approved') order by startDate");
					$totalCount=0;
						for($j=0;$j<$db->countRows($onlyApprovedLeaves);$j++)
						{
							$row=$db->fetchArray($onlyApprovedLeaves);
							$totalCount=$totalCount+$row['count'];
						}
						$splLeave = "";
						echo "<table class='table table-hover table-bordered leavehistory'>
								<tr class='info'>
									<th>Start Date</th>
									<th>End Date</th>
									<th>Count</th>
									<th>Reason</th>
									<th>Status</th>
									<th>Comments</th>
									<th></th>
								</tr>";
	
						for($i=0;$i<$db->countRows($sql);$i++)
						{
							$row=$db->fetchArray($sql);
							echo '<tr>';
							echo '<td>'.$row['startdate'].'</td>';
							echo '<td>'.$row['enddate'].'</td>';
							echo '<td>'.$row['count'].'</td>';
							echo '<td>'.$row['reason'].'</td>';
							echo '<td>'.$row['approvalstatus'].'</td>';
							echo '<td>'.$row['approvalcomments'].'</td>';
							echo '<td><div class="arrow"></div></td></tr>';
							$tid=$row['transactionid'];
							$sql1=$db->query("select * from perdaytransactions where transactionid='".$tid."'");
							echo '<tr>
								<td colspan="6">
									<table class="table table-hover table-bordered">
									<tr class="info" width="40">
										<th>Date</th>
										<th>Leave Type</th>
										<th>Shift</th>
									</tr>';
									while($row1=$db->fetchArray($sql1)) 
									{
										$leavetype = $row1['leavetype'];
										$Day = $row1['date'];
										echo '<tr></tr><tr><td>'.$row1['date'].'</td>';
										echo '<td>'.$row1['leavetype'].'</td>';
										echo '<td>'.$row1['shift'].'</td>';
										echo '</tr>';
									}
									echo "<tr></tr><tr></tr>";
									echo '</table>';
									echo '</td></tr><tr></tr>';
		
								}
								echo "<tr></tr>";
								echo "<tr></tr><tr>
										<td colspan=7><b style='float:right'>
											Total Approved leaves in ".$year." = ".$totalCount."
										<b></td>
									 </tr>";
											
								echo "</table>";
							echo "</div>";
						}
					echo "</div>
				</div>";
		echo "</body>
		</html>";
?>




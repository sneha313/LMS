<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
?>
<html>
	<head>
		<?php 
			$getCalIds = array("fromdate", "todate");
			$calImg = getCalImg($getCalIds,-1,0);
			echo $calImg;
		?>
		<script>
			$("document").ready(function() {
				$('#teamleavereportId').submit(function() {
					if($("#empid").val()=="Choose") {
						BootstrapDialog.alert("Please select an employee");
						return false;
					} else {
						 $(this).find(":input[type=submit]").replaceWith("<center><img src=\'public/img/loader.gif\' class=\'img-responsive\' alt=\'processing\'/></center>");
						  $.ajax({
							data : $(this).serialize(),
							type : $(this).attr('method'),
							url : $(this).attr('action'),
							success : function(response) {
								$("#loadteamleavereport").html(response);
							}
						});
						return false;
					}
				});
		  	});
			$("document").ready(function(){
			    $(".table-1 tr:odd").addClass("odd");
			    $(".table-1 tr:not(.odd)").hide();
			    $(".table-1 tr:first-child").show();
			    $(".table-1 tr.odd").click(function(){
			        $(this).next("tr").toggle();
			        $(this).find(".arrow").toggleClass("up");
			    });
			  });
		</script>
		<style type="text/css">
			#teambalance {
				color: black;
				left: 1000px;
				float:right;
			}
			.table-1 div.arrow {
				background: transparent url(public/images/arrows.png) no-repeat scroll 0px
					-16px;
				width: 16px;
				height: 16px;
				display: block;
			}
		</style>
	</head>
	<body>
		<div class="col-sm-12">

			<?php
				function empHistory($empid,$query){
					global $db;
					global $_REQUEST;
					$leaveTypeCount=0;
					$allCount=0;
					if($_REQUEST['leaveType']=="ALL")
					{
						echo "<table class='table table-bordered table-hover table-1'>
								<tr class='info'>
									<th>Start Date</th>
									<th>End Date</th>
									<th>PTO's Taken</th>
									<th>Reason</th>
									<th>Status</th>
									<th>Comments</th>
									<th></th>
								</tr><tr></tr>";
					} else {
						echo "<table class='table table-bordered table-hover table-1'>
								<tr class='info'>
									<th>Date</th>
									<th>LeaveType</th>
									<th>Shift</th>
								</tr><tr></tr>";
					}
					$sql=$db->query($query);
					$splLeave = "";
					
					for($i=0;$i<$db->countRows($sql);$i++)
					{
						$row=$db->fetchArray($sql);
						if($_REQUEST['leaveType']=="ALL") 
						{
							$allCount=$allCount+$row['count'];
							echo '<tr></tr><tr>';
							echo '<td>'.$row['startdate'].'</td>';
							echo '<td>'.$row['enddate'].'</td>';
							echo '<td>'.$row['count'].'</td>';
							echo '<td>'.$row['reason'].'</td>';
							echo '<td>'.$row['approvalstatus'].'</td>';
							echo '<td>'.$row['approvalcomments'].'</td>';
							echo '<td><div class="arrow"></div></td></tr>';
						}
						$tid=$row['transactionid'];
						$sql1=$db->query("select * from perdaytransactions where transactionid='".$tid."'");
						if($_REQUEST['leaveType']=="ALL")
						{
							echo '<tr>
									<td colspan="6">
										<table class="table table-hover">
											<tr class="info">
												<th>Date</th>
												<th>Leave Type</th>
												<th>Shift</th>
											</tr>';
						}
						while($row1=$db->fetchArray($sql1))
						{
							if($_REQUEST['leaveType']=="ALL") 
							{
								$leavetype = $row1['leavetype'];
								$Day = $row1['date'];
								echo '<tr></tr><tr><td>'.$row1['date'].'</td>';
								echo '<td>'.$row1['leavetype'].'</td>';
								echo '<td>'.$row1['shift'].'</td>';
								echo '</tr>';
							} else {
								if($_REQUEST['leaveType']==$row1['leavetype'])
								{
									$leaveTypeCount=$leaveTypeCount+1;
									$leavetype = $row1['leavetype'];
									$Day = $row1['date'];
									echo '<tr></tr><tr><td>'.$row1['date'].'</td>';
									echo '<td>'.$row1['leavetype'].'</td>';
									echo '<td>'.$row1['shift'].'</td>';
									echo '</tr>';
								}	
							}
						}
						if($_REQUEST['leaveType']=="ALL")
						{
							echo '</table>';//table close
							echo '</td></tr>';
						} 
					}
					if($_REQUEST['leaveType']!="ALL")
					{
						echo "<tr></tr><tr><td colspan=3 align='right'><b>Total Count = ".$leaveTypeCount."</b></td></tr>";
					}
					if($_REQUEST['leaveType']=="ALL")
					{
						echo '<tr></tr><tr><td colspan=7><b style="float:right">Total Approved leaves = '.$allCount.'</b></td></tr>';
					}
					echo "</table>";//main table close
				
				}
				
				if(isset($_REQUEST['empid']) && isset($_REQUEST['leaveType']) )
				{
					
					getEmpSelectionBox($_SESSION['u_empid'],$_REQUEST['empid']);
					echo "<br><br>";
					echo "<div class='panel panel-primary'>
					<div class='panel-heading text-center'>
					<strong style='font-size:20px;'>Team Leave Report Detail</strong>
					</div>
					<div class='panel-body'>";
					if($_REQUEST['empid']!="All")
					{
						echo "<table class='table table-hover'>
					    <tbody>";
						$result1=$db->query("SELECT empname FROM `emp` WHERE empid=".$_REQUEST['empid']);
						$row1=$db->fetchAssoc($result1);
						$result3=$db->query("SELECT balanceleaves,carryforwarded FROM `emptotalleaves` WHERE empid=".$_REQUEST['empid']);
						$row3=$db->fetchAssoc($result3);
						echo "<tr><th>".$row1['empname']."(".$_REQUEST['empid'].")
						<a id='teambalance'>Balance Leaves: ".($row3['balanceleaves']+$row3['carryforwarded'])."</a></th></tr></tbody></table>";
						$query="SELECT * FROM empleavetransactions where empid=".$_REQUEST['empid']." and startdate between '".$_REQUEST['fromdate']."' and '".$_REQUEST['todate']."' and 
											approvalstatus!='Pending' and approvalstatus!='Deleted' and approvalstatus!='Cancelled' order by startdate";
						empHistory($_REQUEST['empid'],$query);
						
					} else {
						$emplist=getemp($_SESSION['u_empid']);
						foreach ($emplist as $empid)
						{
							$result1=$db->query("SELECT empname FROM `emp` WHERE empid=".$empid);
							$row1=$db->fetchAssoc($result1);
							$result3=$db->query("SELECT balanceleaves,carryforwarded FROM `emptotalleaves` WHERE empid=".$empid);
							$row3=$db->fetchAssoc($result3);
							echo "<table class='table table-hover'>
					 		<tbody>";
							echo "<tr><th>".$row1['empname']."(".$empid.")
						    <a id='teambalance'>Balance Leaves: ".($row3['balanceleaves']+$row3['carryforwarded'])."</a></th></tr></tbody></table>";
							$query="SELECT * FROM empleavetransactions where empid=".$empid." and startdate between '".$_REQUEST['fromdate']."' and '".$_REQUEST['todate']."' and
											approvalstatus!='Pending' and approvalstatus!='Deleted' and approvalstatus!='Cancelled' order by startdate";
							empHistory($empid,$query);
							
						}
					}
					echo "</div></div>";
				}
				else {
					getEmpSelectionBox($_SESSION['u_empid'],"");
				}
				
				?>
		</div><!-- 12 column div close -->
	</body>
</html>

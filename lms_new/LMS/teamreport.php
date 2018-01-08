<?php
	session_start();
	require_once 'Library.php';
	require_once 'attendenceFunctions.php';
	require_once 'generalFunctions.php';
	error_reporting("E_ALL");
	$db=connectToDB();
?>
<html>
	<head>
		<script type="text/javascript">
		function hidealldiv(div) {
			var myCars=new Array("loadleaveinfo","loadDepartment","loadmyprofile","loadpersonalinfo","loadofficialinfo","loadempapplyleave","loadempleavestatus","loadempleavehistory",
								 "loadempleavereport","loadempeditprofile","loadholidays",
								 "loadempleavereport","loadteamleavereport","loadhelp",
								 "loadteamleaveapproval","loadattendance","loadcalender","loadoptionalleave","loadvoeform",
								 "loadpendingstatus","loadhrsection","loadmanagersection","loadapplyteammemberleave",
								 "loadcompoffleave","loadtrackattendance", "loadAttd", "loadwfhhr", "loadextrawfhhr");
			var hidedivarr=removeByValue(myCars,div);
			hidediv(hidedivarr);
			showdiv(div);
		}
		
		function hidediv(arr) {
			$("#footer").show();
			for(var i=0; i<arr.length; i++) {
					$("#"+arr[i]).hide();
					$("#"+arr[i]).html("");
		        }
		}
		function showdiv(div) {
			$("#"+div).show();
		}
		function removeByIndex(arr, index) {
		    arr.splice(index, 1);
		}
		
		function removeByValue(arr, val) {
		    for(var i=0; i<arr.length; i++) {
		        if(arr[i] == val) {
		            arr.splice(i, 1);
		            break;
		        }
		    }
		    return arr;
		}
			$("document").ready(function() {
				$(".radio").css("width","20px");
				$('#detailed_form').submit(function() { 
					 $(this).find(":input[type=submit]").replaceWith("<center><img src=\'public/img/loader.gif\' class=\'img-responsive\' alt=\'processing\'/></center>");
				    $.ajax({ 
				        data: $(this).serialize(),
				        type: $(this).attr('method'), 
				        url: $(this).attr('action'), 
				        success: function(response) { 
				            $('#loadteamleavereport').html(response);
				        }
						});
						return false; 
				});


				$("#leaveapprovalid12").click(function(){
					hidealldiv('loadteamleavereport');
					$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
					$("#loadteamleavereport").load('teamleavereport.php');
				});
				
				$("#brief").click(function(){
					hidealldiv('loadteamleavereport');
					$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
					$("#loadteamleavereport").load('teamreport.php?briefreport=1');
				});
				$("#detailed").click(function(){
					hidealldiv('loadteamleavereport');
					$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
					$("#loadteamleavereport").load('teamreport.php?detailedreport=1');
				});
				
			 });
			$(function() {
				$('#fromDate').datetimepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat: 'yy-mm-dd',
					showButtonPanel: true,
					showOn: 'both',
					yearRange: '-100:+0',
					buttonImageOnly: true
					
					});
			});
			</script>
			<style type="text/css">
				#brief, #detailed{
					cursor: pointer;
				}
			</style>
			<?php 
				$getCalIds = array("detailfromDate","detailtoDate","applyforteamfromDate","applyforteamtoDate");
				$calImg=getCalImg($getCalIds);
				echo $calImg;
			?>
		</head>
		<body>
		<div id="loadingmessage"></div>
		<!--12 column start-->
		<div class="col-sm-12">
		<?php 
		if(isset($_REQUEST['report']))
		{?>
			<div class="panel panel-primary">
	  			<div class="panel-heading text-center">
	  				<strong style="font-size:20px;">User Reports</strong>
	  			</div>
				<!--hr report panel body start-->
	  			<div class="panel-body">
					<table class="table table-bordered table-hover">
						<?php
						
						if(strtoupper($_SESSION['user_dept'])=="HR") {?>
						<tr>
							<td><a id='brief' href="#">Employee Leaves [Brief Report]</a></td>
							<td>HR can view employee brief report</td>
						</tr>
						<tr>
							<td><a id='detailed' href="#">Employee Leaves [Detailed Report]</a></td>
							<td>HR can view employee detailed report</td>
						</tr>
						<?php }?>
						<tr>
							<td><a id='leaveapprovalid12' href="#">Team Leave Report</a></td>
							<td>view All Team Leave report</td>
						</tr>
					</table>
				</div><!--hr report panel body close-->
			</div><!--hr report panel close-->
								
           <?php 
				}
				if(isset($_REQUEST['briefreport']))
				{
					$query="SELECT emp.empname as name,emptotalleaves.balanceleaves,emptotalleaves.carryforwarded FROM emp, emptotalleaves WHERE emp.empid = emptotalleaves.empid";
					// Add link: Export to Excel
					echo "<div class='panel panel-primary'>
							<div class='panel-heading text-center'>
								<strong style='font-size:20px;'>Employee Balance Leave Brief report</strong>
							</div>
							<div class='panel-body'>";
					echo("<div style='font-weight:bold'>Export:&nbsp;".
							"<a href = 'csv.php?export1=1&query=".urlencode($query).
							"' title = 'Export as CSV'>".
							"<img src='images/excel.gif' alt='Export as CSV'/></a></div><br>");
					$result=$db->query($query);
					echo "<table class='table table-hover table-bordered'>
							  <tbody>
							  <tr class='info'>
							  	<th>Emp Name</th>
							  	<th>Balance Leaves</th>
							  </tr>";
						for($i=0;$i<$db->countRows($result);$i++)
						{
							$row=$db->fetchAssoc($result);
							echo "<tr><td>".$row['name']."</td>";
							echo "<td>".($row['balanceleaves']+$row['carryforwarded'])."</td></tr>";
						}
						echo "</tbody></table>
						</div>
					</div>";
					echo "<br><br>";
				}
				if(isset($_REQUEST['detailedreport']))
				{
					echo "<form id='detailed_form' method='POST' action='teamreport.php?detailedreport=1&response=1'>
						  <div class='panel panel-primary'>
							<div class='panel-heading text-center'>
								<strong style='font-size:20px;'>Employee Balance Leave Detailed report</strong>
							</div>
							<div class='panel-body'>
		    					<div class='form-group'>
									<div class='row'>
										<div class='col-sm-2'></div>
		    							<div class='col-sm-3'>
		    								<label for='fromDate'>From Date:</label>
		    							</div>
				    	  				<div class='col-sm-5'>
		    								<div class='input-group'>
												<input type='text' class='form-control open-datetimepicker' name='fromDate' id='detailfromDate'>
												<label class='input-group-addon btn' for='date'>
												   <span class='fa fa-calendar open-datetimepicker'></span>
												</label>
											</div>
		    							</div>
		    							<div class='col-sm-2'></div>
		    						</div>
		    					</div>
						  		<div class='form-group'>
									<div class='row'>
										<div class='col-sm-2'></div>
		    							<div class='col-sm-3'>
		    								<label for='toDate'>To Date:</label>
		    							</div>
				    	  				<div class='col-sm-5'>
		    								<div class='input-group'>
												<input type='text' class='form-control open-datetimepicker1' name='toDate' id='detailtoDate'>
												<label class='input-group-addon btn' for='date'>
												   <span class='fa fa-calendar open-datetimepicker1'></span>
												</label>
											</div>
		    							</div>
		    							<div class='col-sm-2'></div>
		    						</div>
		    					</div>
						  		<div class='form-group'>
									<div class='row'>
										<div class='col-sm-2'></div>
		    							<div class='col-sm-3'>
		    								<label>Select Team:</label>
		    							</div>
				    	  				<div class='col-sm-5'>
		    								<SELECT class='form-control' name='team_select' id='teamName'>
											  	<option>choose</option>";
												$teamresult=$db->query("select distinct(dept) from emp");
												for($y=0;$y<$db->countRows($teamresult);$y++)
												{
													$teamrow=$db->fetchAssoc($teamresult);
													echo '<option>'.$teamrow['dept'].'</option>';
												}
												echo "<option>All</option>
											</select>
										</div>
										<div class='col-sm-2'></div>
									</div>
								</div>
						  		<div class='form-group'>
									<div class='row'>
										<div class='col-sm-12 text-center'>
											<input class='submit btn btn-primary' type='submit' name='submit' value='Submit'/>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>";
					
					if(isset($_REQUEST['response']))
					{
						$startdate=$_REQUEST['fromDate'];
						$enddate=$_REQUEST['toDate'];
						$team=$_REQUEST['team_select'];
						echo '<script>
				                $("#detailfromDate").val("'.$startdate.'");
				                $("#detailtoDate").val("'.$enddate.'");
								$("#teamName").val("'.$team.'");
				        	</script>';
						echo "<div class='panel panel-primary'>
							<div class='panel-heading text-center'>
								<strong style='font-size:20px;'>Detailed Report from $startdate to $enddate for $team</strong>
							</div>
							<div class='panel-body'>";
							if($team=="All")
							{
								$query2="SELECT * FROM emp WHERE state='Active' and empid IN (SELECT DISTINCT (empid) AS empid
								FROM `empleavetransactions` WHERE approvalstatus = 'Approved' AND startdate BETWEEN '".$startdate."'
								AND '".$enddate."')";
							}
							else {
								$query2="SELECT * FROM emp WHERE dept = '".$team."' and state='Active'";
							}
							// Add link: Export to Excel
							echo("<div style='font-weight:bold'>Export:&nbsp;".
									"<a href = 'csv.php?export2=1&dept=".$team."&startdate=".$_REQUEST['fromDate']."&enddate=".$_REQUEST['toDate']."&query=".urlencode($query2).
									"' title = 'Export as CSV'>".
									"<img src='public/images/excel.gif' alt='Export as CSV'/></a></div>");
							$result2=$db->query($query2);
							echo "<table class='table table-hover'>
		 						 <tbody>";
							for($x=0;$x<$db->countRows($result2);$x++)
							{
								$row2=$db->fetchAssoc($result2);
								$query3="SELECT empname FROM `emp` WHERE empid=".$row2['empid'];
								$result3=$db->query($query3);
								$row3=$db->fetchAssoc($result3);
								$result6=$db->query("SELECT balanceleaves,carryforwarded FROM `emptotalleaves` WHERE empid=".$row2['empid']);
								$row6=$db->fetchAssoc($result6);
								echo "<tr><th>".$row3['empname']."(".$row2['empid'].")
			    						<a id='teambalance'>Balance Leaves: ".($row6['balanceleaves']+$row6['carryforwarded'])."</a></th></tr>";
										$query4="SELECT empleavetransactions.empid,  empleavetransactions.startdate, empleavetransactions.enddate, empleavetransactions.count,
									    empleavetransactions.reason FROM  empleavetransactions WHERE empleavetransactions.empid =".$row2['empid']." and approvalstatus = 'Approved'
										AND startdate BETWEEN '".$startdate."' AND '".$enddate."'";
								echo "<tr><td><table class='table table-hover'>
									    <tbody>
										    <tr class='info'>
											  	<th>Start Date</th>
											  	<th>End Date</th>
											  	<th>Days Taken</th>
											  	<th>Reason</th>
										    </tr>";
										$result4=$db->query($query4);
										$allCount=0;
										for($i=0;$i<$db->countRows($result4);$i++)
										{
											$row4=$db->fetchAssoc($result4);
											$allCount=$allCount+$row4['count'];
											echo "<tr><td>".$row4['startdate']."</td>";
											echo "<td>".$row4['enddate']."</td>";
											echo "<td>".$row4['count']."</td>";
											echo "<td>".$row4['reason']."</td></tr>";
										}
										echo"<tr><td colspan=4><b style='float:right'>Total Approved leaves = ".$allCount."</b></td></tr>";
										echo "</tbody></table></td></tr>";
									}
								echo "</tbody>
								</table>
							</div>
						</div>";
					}
				}

?>
</div><!--12 column end-->
</body>
</html>
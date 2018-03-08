<?php
	session_start();
	require_once 'librarycopy1.php';
	require_once 'attendenceFunctions.php';
	require_once 'generalcopy.php';
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
				$('#viewbalance_form').submit(function() { 
		            $.ajax({ 
		                data: $(this).serialize(),
		                type: $(this).attr('method'), 
		                url: $(this).attr('action'), 
		                success: function(response) { 
		                    $('#loadteamleavereport').html(response);
				    $('#viewbalance_form').hide();
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
				$("#viewbalanceleaves").click(function(){
					hidealldiv('loadteamleavereport');
					$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
					$("#loadteamleavereport").load('teamreport.php?viewbalanceleaves=1');
				}); 
			 });
			</script>
			<style type="text/css">
				#brief, #detailed, #viewbalanceleaves{
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
							<td><a id='viewbalanceleaves' href="#">View Balance Leaves for Employee</a></td>
							<td>HR can view list of employees, who will be having less than selected number. </td>
						</tr>
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
					$result=$db->pdoQuery($query)->results();
					echo "<table class='table table-hover table-bordered'>
							  <tbody>
							  <tr class='info'>
							  	<th>Emp Name</th>
							  	<th>Balance Leaves</th>
							  </tr>";
							foreach($result as $row)
						{
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
												$teamresult=$db->pdoQuery("select distinct(dept) from emp")->results();
												
													foreach($teamresult as $teamrow)
												{
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
									"<img src='public /images/excel.gif' alt='Export as CSV'/></a></div>");
							$result2=$db->pdoQuery($query2)->results();
							echo "<table class='table table-hover'>
		 						 <tbody>";
								foreach($result2 as $row2)
							{
								$query3="SELECT empname FROM `emp` WHERE empid='".$row2['empid']."'";
								$result3=$db->pdoquery($query3)->results();
								foreach($result3 as $row3)
								$result6=$db->pdoquery("SELECT balanceleaves,carryforwarded FROM `emptotalleaves` WHERE (empid=?);",array($row2['empid']));
								$row6=$result6->results();
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
										$result4=$db->pdoQuery($query4)->results();
										$allCount=0;
										foreach($result4 as $row4)
										{
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
				
				if(isset($_REQUEST['viewbalanceleaves']))
				{
					echo "<form id='viewbalance_form' method='POST' action='teamreport.php?viewbalanceleaves=1&response=1'>
		                  <div class='col-sm-1'></div>
							<div class='col-sm-3'>
								<label style='font-size:16px;' for='count'>Balance Leaves less than: </label>
				    		</div>
						
		          		  <div class='col-sm-4'>
				  			<input type='number' class='form-control' min='0' max='55' value='0' name='count' id='count' size='20' />
				  		</div>
		        	 	<div class='col-sm-3'>
				    		<input class='btn btn-primary submit' type='submit' name='submit' value='Submit'/>
				    	</div>
				    	<div class='col-sm-1'></div>
					</form>";
					if (isset($_REQUEST['response'])) {
						$count=$_POST['count'];
						echo "<div class='panel panel-primary'>
							<div class='panel-heading text-center'>
								<strong style='font-size:20px;'>Employee Leave information (Balanceleaves + carryforwarded <= $count)</strong>
							</div>
							<div class='panel-body'>";
						echo "<table class='table table-bordered table-hover'>";
						echo "<tr class='info'>";
						echo "<th>Sr. No.</th>";
						echo "<th>Employee Name</th>";
						echo "<th>Emp ID</th>";
						echo "<th>Carry Forwarded Leaves</th>";
						echo "<th>Balance Leaves in present year</th>";
						echo "<th>Used Leaves in present year</th>";
						echo "<th>Carry forwarded + Balance Leaves in present year</th>";
						echo "</tr>";
						global $db;
						$query="SELECT empname, emp.empid, carryforwarded, balanceleaves FROM emp,emptotalleaves where (emp.empid=emptotalleaves.empid and (balanceleaves+carryforwarded) <= '".$count."') order by empname asc";
						$sql = $db->pdoQuery($query);
						$sqlcountquery="SELECT COUNT(*) AS numrows FROM `emptotalleaves`,`emp` WHERE emp.empid=emptotalleaves.empid and (balanceleaves+carryforwarded) <= '".$count."'";
						$sqlcount=$db->showQuery($sqlcountquery)->results();
						if ($sqlcount == 0) {
							echo "<tr><td colspan='7'>No Employees whose (Balance Leaves + Carry Forwarded Leaves) is less than ".$count."</td></tr>";
						} else {
							$results=$db->pdoQuery($query)->results();
							$serialno=1;
							foreach($results as $result)
							{
								echo "<tr>";
								echo "<td>".$serialno."</td>";
								echo "<td>".$result['empname']."</td>";
								echo "<td align=center>".$result['empid']."</td>";
								echo "<td align=center>".$result['carryforwarded']."</td>";
								echo "<td align=center>".$result['balanceleaves']."</td>";
								echo "<td align=center>".(25-$result['balanceleaves'])."</td>";
								echo "<td align=center>".($result['carryforwarded']+$result['balanceleaves'])."</td>";
								echo "</tr>";
								$serialno++;
							}
						}
						
						echo "</table></div></div>";
					}
				}
?>
</div><!--12 column end-->
</body>
</html>
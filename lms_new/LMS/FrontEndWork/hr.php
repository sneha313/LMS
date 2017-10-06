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
				    $.ajax({ 
				        data: $(this).serialize(),
				        type: $(this).attr('method'), 
				        url: $(this).attr('action'), 
				        success: function(response) { 
				            $('#loadhrsection').html(response);
				        }
						});
						return false; 
				});
				$('#applyLeaveForTeam').submit(function() { 
				    $.ajax({ 
				        data: $(this).serialize(),
				        type: $(this).attr('method'), 
				        url: $(this).attr('action'), 
				        success: function(response) { 
				            $('#loadhrsection').html(response);
				        }
						});
						return false; 
				});
				$('#applyLeaveForTeamConfirmation').submit(function() { 
					$('#loadingmessage').show();
					$("#applyLeaveForTeamConfirmationSubmit").hide();
						$.ajax({ 
					 		data: $(this).serialize(),
					       	type: $(this).attr('method'), 
					       	url: $(this).attr('action'), 
					       	success: function(response) { 
					       		$('#loadingmessage').hide();
				     	   		$('#loadhrsection').html(response);
					       }
						});
						return false; 
			    });
				$("#inoutdetails").click(function(){
					$("#loadhrsection").load('Employeeinoutdetails.php?inoutdetails=1');
				});
				$("#Allinoutdetails").click(function(){
			    	$("#loadhrsection").load('Employeeinoutdetails.php?Allinoutdetails=1');
			    });
				$("#brief").click(function(){
					$("#loadhrsection").load('hr.php?briefreport=1');
				});
				$("#detailed").click(function(){
					$("#loadhrsection").load('hr.php?detailedreport=1');
				});
				
				$("#new_emp").click(function(){
					$("#loadhrsection").load('newuser.php?userlinks=1');
				});
				$("#hrmodifyempapprovedleaves").click(function(){
					$("#loadhrsection").load('modifyempapprovedleaves.php?role=hr');
				});
				$("#hronbehalfempapply").click(function(){
					$("#loadhrsection").load('hrapplyleaveforall.php?getEmp=1');
				});
				$("#addextrawfh").click(function(){
					$("#loadhrsection").load('wfhhours/manageraddwfhforemp.php?role=hr');
				});
				$("#viewextrawfh").click(function(){
					$("#loadhrsection").load('wfhhours/managerviewwfhform.php?role=hr&viewform=1');
				});
				$("#approveextrawfhHR").click(function() {
				  	hidealldiv('loadhrsection');
					$("#loadhrsection").load('wfhhours/approveEmpExtrawfhhour.php?role=hr&approveview=1');
				});
				$("#hrApproveEmpLeave").click(function() {
					$("#loadhrsection").load('approveEmpLeave.php?role=hr');
				});
				$("#applyspl").click(function(){
					$("#loadhrsection").load('hr.php?applyspl=1');
				});
				$("#applyforteam").click(function(){
					$("#loadhrsection").load('hr.php?applyforteam=1');
				});
				$("#reApply").click(function(){
					$("#loadhrsection").load('hr.php?applyspl=1');
				});
				$("#viewbalanceleaves").click(function(){
					$("#loadhrsection").load('hr.php?viewbalanceleaves=1');
				});
				$("#department").click(function(){
					hidealldiv('loadDepartment');
					$("#loadDepartment").load("DepartmentActionbyHR.php");
				});
				$("#empList").change(function(){
					if($("#empList").val()=="Choose") {
						$("#empidRow").hide();
						$("#trfromdate").hide();
						$("#trtodate").hide();
						$("#trleavetype").hide();
						$("#option").hide();
						$("#defaultdays").hide();
						$("#reason").hide();
						$("#splsubmit").hide();
					} else {
						$("#employeeid").val($("#empList").val());
						$("#empidRow").show();
						$("#trfromdate").show();
						$("#trtodate").show();
						$("#trleavetype").show();
					}
				});
				$("#noofdays").spinner(
					{ min: 0 },
					{ max: 100 }
				);
				$("#splleave").change(function(){
					if($("#splleave").val()=="Choose") {
						$("#defaultdays").hide();
						$("#option").hide();
						$("#reason").hide();
						$("#splsubmit").hide();
					} else {
						$("#option").show();
						$("#option input").width(25)
						$("#defaultdays").show();
						$("#reason").show();
						$("#splsubmit").show();
						$("#splleavename").val($("#splleave option:selected").html());
						val=$("#splleave").val().split(" ")[0];
						sel=$("#splleave").val().split(" ")[1];
						if (sel.match(/DAY/gi)) {
							$("#noofdays").spinner("value", val);
							$("#days").prop("checked",true);
							$("#labeloption").html(sel);
						}
						if (sel.match(/WEEK/gi)) {
							$("#noofdays").spinner("value", val);
							$("#weeks").prop("checked",true)
							$("#labeloption").html(sel);
						}
					}
				});
				$('input[name=selectoption]:radio').change(function(){
					$("#labeloption").html($('input[name=selectoption]:radio:checked').val());
				});
			
			  	$("#applysplLeave").validate({
			  	    rules: {
			  	    	employeeid: "required",
			  	    	fromDate: "required"
			  	    },
			  	    messages: {
			  	    	employeeid: "Pleasse choose Employee",
			  	    	fromDate: "Please specify From Date"
			  	    },
			  	    submitHandler: function() {
			  			$.ajax({ 
				        data: $('#applysplLeave').serialize(), 
				        type: $('#applysplLeave').attr('method'), 
				        url:  $('#applysplLeave').attr('action'), 
				        success: function(response) { 
				            $('#loadhrsection').html(response); 
				        }
						});
						return false;
			  	  }
			  	  });
			  	$("#confirmSplLeave").submit(function() {
				    $.ajax({
				        data: $(this).serialize(),
				        type: $(this).attr('method'), 
				        url: $(this).attr('action'), 
				        success: function(response) { 
				            $("#loadhrsection").html(response);
				        }
						});
						return false; 
				});
			 });
			</script>
			<style type="text/css">
				#applyspl,#applyforteam {
					cursor: pointer;
				}
				#inoutdetails, #Allinoutdetails ,#viewbalanceleaves {
					cursor: pointer;
				}
				#addextrawfh{
					cursor: pointer;
				}
				#brief, #detailed{
					cursor: pointer;
				}
				#viewextrawfh{
					cursor: pointer;
				}
				#approveextrawfhHR{
					cursor: pointer;
				}
				#new_emp, #hronbehalfempapply, #hrApproveEmpLeave, #hrmodifyempapprovedleaves{
					cursor: pointer;
				}
			</style>
		</head>
		<body>
			<?php
			function confirmSplLeave($transactionid,$empid,$fromDate,$toDate,$leavetype,$daysList,$totalDays,$reason){
				global $db;
				$query="insert into `empleavetransactions` (`transactionid` ,`empid` ,`startdate` ,
						`enddate` ,`count`,`reason`,`approvalstatus`,`approvalcomments`)VALUES ('" . $transactionid . "','".$empid."',
						'" .$fromDate. "', '" . $toDate . "',0,
						'" .$reason. "','Approved','Approved by HR(".$_SESSION['u_fullname'].")')"; 
				$result = $db -> query($query);
				for($i=0;$i<sizeof($daysList);$i++) {
					$perdayquery="Insert into `perdaytransactions` (`transactionid` ,`empid` ,`date` ,`leavetype`,`shift`)
								values('" . $transactionid . "','" .$empid. "','".$daysList[$i]."','".$leavetype."','')";
					$perdayresult = $db -> query($perdayquery);
				}
				if($leavetype=="Compensation Leave") {
					$getBalanceLeaves="select balanceleaves from `emptotalleaves` where empid='".$empid."'";
					$balanceLeavesresult = $db -> query($getBalanceLeaves);
					$row=$db->fetchAssoc($balanceLeavesresult);
					$newBalanceLeaves=$totalDays+$row['balanceleaves'];
					$updateBalanceLeavesQuery="update `emptotalleaves` set balanceleaves=$newBalanceLeaves where empid=".$empid;
					$updateResult = $db -> query($updateBalanceLeavesQuery);
				}	
					
				if($result) {
					echo "<div class='panel panel-primary'>
						<div class='panel-heading text-center'>
							<strong style='font-size:20px;'>Approved Employee Special Leave</strong>
						</div>
						<div class='panel-body'>
		    				<table class='table table-hover table-bordered'>
								<tr class='info'>
									<td>Employee Name</td>
									<td>".getempName($empid)."</td>
								</tr>";
						foreach (array_keys($daysList) as $key) {
							echo "<tr>
									<td>$daysList[$key]</td>
									<td>$leavetype</td>
								</tr>";
						}
							echo "<tr>
								<td>Status</td>
								<td>Approved by ".$_SESSION['u_fullname']."</td>
							</tr>
						</table>
					</div>
				</div>";
				echo "<script>BootstrapDialog.alert('".$leavetype." is approved for ".getempName($empid)."')</script>";
			}
		}
	?>
		<!--12 column start-->
		<div class="col-sm-12">
			<?php 
			if(isset($_REQUEST['hrlinks']))
			{?>
			<!--hr job panel start-->
			<div class="panel panel-primary">
				<div class="panel-heading text-center">	
					<strong style="font-size:20px;">HR Section</strong>
				</div>
				<!-- hr job panel body start-->
				<div class="panel-body table-responsive" id="hrjob">
					<table class="table table-bordered">
						<tr>
							<td width="50%">
								<!--hr job panel start-->
								<div class="panel panel-info">
									<div class="panel-heading">HR Jobs</div>
									<!--hr job panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
											   <td width="40%"><a id='new_emp'>Add/Edit Employee Details </a></td>                                                                                       
												<td>HR can add, edit or view employee details</td>
											</tr>
											<tr>
	                                            <td width="40%"><a id='hrApproveEmpLeave'>Approve Employee Leaves</a></td>
	                                            <td>HR can approve employee pending leaves.</td>
	                                        </tr>
											<tr>
	                                            <td width="40%"><a id='hrmodifyempapprovedleaves'>Modify Employee Approved Leaves</a></td>
	                                            <td>HR can modify employee approved leaves.</td>
											</tr>
											<tr>
	                                            <td width="40%"><a id='viewbalanceleaves'>View Balance Leaves for Employee</a></td>
	                                            <td>HR can view balance leaves for any employee</td>
	                                        </tr>
	                                        <tr>
												<td width="40%"><a id="department" href="#">Department List</a></td>
												<td>HR can add subdepartment, member in a department and delete subdepartment, if any team members are not present</td>
											</tr>
	                                    </table>
									</div><!--hr job panel close-->
								</div><!--hr job panel end-->
								<!--Approve/Delete Extra WFH Hour by hr panel start-->
								  <div class="panel panel-info">
									<div class="panel-heading">Approve/Delete Extra WFH Hour for Employee by HR</div>
									<!-- apply leave by hr panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id='approveextrawfhHR'>Approve/Cancel Extra WFH Hour</a></td>
												<td>HR can Approve/Cancel Extra WFH Hour Applied by Employee.</td>
											</tr>
											<tr>
												<td><a id='addextrawfh'>Add Extra WFH Hour</a></td>
												<td>HR can add Extra WFH Hour for any Employee</td>
											</tr>
											<tr>
												<td><a id='viewextrawfh'>View/Modify Extra WFH Hour</a></td>
												<td>HR can View/Modify Extra WFH Hour Applied by Employee.</td>
											</tr>
										</table>
									</div><!-- Approve/Delete Extra WFH Hour by hr panel body close-->
								</div><!-- Approve/Delete Extra WFH Hour by hr panel close -->
							</td>
							
	                        <td>
								
								<!--apply leave by hr panel start-->
								<div class="panel panel-info">
									<div class="panel-heading">Apply Leave for Employee by HR</div>
									<!-- apply leave by hr panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id='hronbehalfempapply'>Apply Leave on behalf of Employee</a></td>                                                                                       
												<td>HR can apply leave on behalf of employee.</td>
											</tr>
											<tr>
												<td><a id='applyspl'>Apply special Leave</a></td>
												<td>HR can apply special leave for any particular employee</td>
											</tr>
											<tr>
												<td><a id='applyforteam'>Apply Leave for Team</a></td>
												<td>HR can apply leave for any particular team</td>
											</tr>
											<tr>
	                                            <td><a id='inoutdetails'>Apply Employee Inout Details</a></td>
	                                            <td>HR can add employee inout details</td>
	                                        </tr>
	                                        <tr>
	                                            <td><a id='Allinoutdetails'>Apply Inout Details for All Employees</a></td>
	                                            <td>HR can add all employees inout details </td>
	                                        </tr>
										</table>
									</div><!-- apply leave by hr panel body close-->
								</div><!-- apply leave by hr panel close -->
								
                        	</td>
						</tr>
					</table>
				</div><!--hr job panel body close-->
			</div><!--hr job panel close-->
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
							"<img src='images/excel.gif' alt='Export as CSV'/></a></div>");
					$result=$db->query($query);
					echo "<table class='table table-hover table-bordered'>
							  <tbody>
							  <tr class='info'>
							  	<td>Emp Name</td>
							  	<td>Balance Leaves</td>
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
					echo "<form id='detailed_form' method='POST' action='hr.php?detailedreport=1&response=1'>
						  <div class='panel panel-primary'>
							<div class='panel-heading text-center'>
								<strong style='font-size:20px;'>Employee Balance Leave Brief report</strong>
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
					</form>
					 <script>
				   $(document).ready(function(){
					$('.open-datetimepicker').datetimepicker({
						format: 'yy-mm-dd'
					});
					$('.open-datetimepicker1').datetimepicker({
						format: 'yy-mm-dd'
					});
				   });
			   </script>";
					
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
									"<img src='images/excel.gif' alt='Export as CSV'/></a></div>");
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
											  	<td>Start Date</td>
											  	<td>End Date</td>
											  	<td>Days Taken</td>
											  	<td>Reason</td>
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
				
				if(isset($_REQUEST['applyspl']))
				{
					if(isset($_REQUEST['update'])) {
						if(isset($_REQUEST['confirm'])){
							$daysList=array();
							$leavetype="";
							foreach (array_keys($_REQUEST) as $key) {
								if(substr($key, 0, 4 ) === "Date") {
									list($x,$date) = explode('/',$key);
									array_push($daysList,$date);
									$leavetypeTmp=$_REQUEST[$key];
									if (is_array($leavetypeTmp)) {
										$key=key($leavetypeTmp);
										$leavetype=$leavetypeTmp[$key];
									} else {
										$leavetype=$leavetypeTmp;
									}
								}
							}
							confirmSplLeave($_REQUEST['tid'],$_REQUEST['empid'],$_REQUEST['fromDate'],$_REQUEST['toDate'],$leavetype,$daysList,$_REQUEST['count'],urldecode($_REQUEST['reason']));
						} else {
							$tid=generate_transaction_id();
							$fromDate=$_REQUEST['fromDate'];
							if(strtoupper($_REQUEST['selectoption'])=="DAYS") {
								$toDate=date('Y-m-d', strtotime($fromDate. ' +'.($_REQUEST['noofdays']-1).'  days'));
								$totalDays=$_REQUEST['noofdays'];
							}
							if(strtoupper($_REQUEST['selectoption'])=="WEEKS") {
								$toDate=date('Y-m-d', strtotime($fromDate. ' +'.$_REQUEST['noofdays'].'  week'));
								$toDate=date('Y-m-d', strtotime($toDate. ' -1  days'));
								$totalDays=($_REQUEST['noofdays']*7);
									
							}
							$reason=str_replace('\'','\\\'',$_REQUEST['reason']);
							$numOfDays=RegleavesCal($fromDate,$toDate,$_REQUEST['employeeid']);
							$count=0;
							echo "<form name='confirmSplLeave' id='confirmSplLeave' method='POST' action='hr.php?applyspl=1&update=1&confirm=1'>";
							echo"<div class='panel panel-primary'>
								<div class='panel-heading text-center'>
									<strong style='font-size:20px;'>Confirm Special Leave</strong>
								</div>
								<div class='panel-body'>";
							for($j=0;$j<sizeof($numOfDays);$j++)
							{
								if(substr( $numOfDays[$j] , 0, 2 ) === "20" )
								{
									echo "<div class='form-group'>
										<div class='row'>
											<div class='col-sm-2'></div>
											<div class='col-sm-4'>
												<label> $numOfDays[$j]  </label>
											</div>
											<div class='col-sm-4'>
												<input type ='text' class='form-control' name='Date".$count."/".$numOfDays[$j]."' value ='".$_REQUEST['splleavename']."'  readonly=true/>
											</div>
											<div class='col-sm-2'></div>
										</div>
									</div>";
									$count++;
				
								} else {
								echo "<div class='form-group'>
										<div class='row'>
											<div class='col-sm-12 text-center'><label> $numOfDays[$j]  </label></div>";
								echo '</div></div>';
								}
						}
						if($count==0) {
							echo "<div class='form-group'>
								<div class='row'>
									<div class='col-sm-12 text-center'>
										<label><font color='red'>Leave is already approved/Pending on selected days.
												Please reapply leave.</label>
									</div>
								</div>
							</div>";
							echo "<div class='form-group'>
								<div class='row'>
									<div class='col-sm-12 text-center'>
										<input type='button' class='btn btn-primary' name='reApply' id='reApply' value='Re-Apply' />
									</div>
								</div>
							</div>";
						}
						elseif($count<$totalDays) {
						echo "<div class='form-group'>
								<div class='row'>
									<div class='col-sm-12 text-center'>
										<label><font color='red'>You have selected $totalDays days of ".$_REQUEST['splleavename'].".
											Out of which ".($totalDays-$count)." days are already in approved/Pending state or weekends/holidays.
											So, remaining $count days you are applying for (".$_REQUEST['splleavename'].") leave.
										</font></label>
									</div>
								</div>
							</div>";
				
				echo "<div class='form-group'>
						<div class='row'>
							<div class='col-sm-4'> Do you still want to confirm the leave?</div>
							<div class='col-sm-8 text-center'>
								<input type='submit' class='btn btn-primary' name='submit' value='Yes' />
								<input type='button' class='btn btn-primary' name='reApply' id='reApply' value='No' />
							</div>
						</div>
					</div>";
		} else {
				echo "<div class='form-group'>
						<div class='row'>
							<div class='col-sm-12 text-center'>
								<input type='button' class='btn btn-warning' name='reApply' id='reApply' value='Cancel'/>
								<input type='submit' class='btn btn-info' name='submit' value='Apply'/>
							</div>
						</div>
					</div>";
			}
			echo "<input type = hidden name ='tid' value = '$tid'/> ";
			echo "<input type = hidden name ='count' value = '$count'/> ";
			echo "<input type = hidden name ='fromDate' value ='$fromDate'/> ";
			echo "<input type = hidden name ='toDate' value ='$toDate'/> ";
			echo "<input type = hidden name ='empid' value ='".$_REQUEST['employeeid']."'/> ";
			echo "<input type = hidden name ='reason' value ='".urlencode($reason)."'/> ";
			echo "</div></div></form>";
			}
		} else {
			echo "<form name='applysplLeave' id='applysplLeave' method='POST' action='hr.php?applyspl=1&update=1' accept-charset='UTF8'>
				<div class='panel panel-primary'>
					<div class='panel-heading text-center'>
						<strong style='font-size:20px;'>Apply Special Leave For Employee</strong>
					</div>
					<div class='panel-body'>
						<div class='form-group'>
							<div class='row'>
								<div class='col-sm-2'></div>
								<div class='col-sm-3'>
									<label for='empList'>Select Employee:</label></div>
								<div class='col-sm-5'>
									<SELECT class='form-control' id= 'empList' name='empList'>
										<option value='Choose' selected> Choose Employee </option>";
											global $db;
											$sql = $db->query("SELECT distinct(empname),empid FROM emp where state='Active' order by empname asc");
											for ($i=0;$i<$db->countRows($sql);$i++)
											{
												$result = $db->fetchArray($sql);
												echo "<option value='".$result['empid']."'>".$result['empname']."</option>";
											}
									echo "</SELECT></div>
	                             <div class='col-sm-2'></div>
							</div>
	                    </div>
						<div class='form-group'>
							<div class='row' style='display:none' id='empidRow'>
								<div class='col-sm-2'></div>
								<div class='col-sm-3'>
									<label for='empid' class='empid'>Emp Id:</label>
	                  	        </div>
         						<div class='col-sm-5' class='empid'>
	                               	<input type='text' class='form-control' id='employeeid' name='employeeid' readonly/>
	                           	</div>
	                          	<div class='col-sm-2'></div>
        					</div>
	                   	</div>
	                        
						<div class='form-group'>
							<div class='row' style='display:none' id='trfromdate'>
								<div class='col-sm-2'></div>
								<div class='col-sm-3'>
									<label for='fromDate'>From Date:</label>
	                          	</div>
    							<div class='col-sm-5'>
	                               	<div class='input-group'>
										<input type='text' id='fromDate' class='form-control open-datetimepicker' name='fromdate' size='20' class='required' readonly='true'>
										<label class='input-group-addon btn' for='date'>
											<span class='fa fa-calendar open-datetimepicker'></span>
										</label>
									</div>
	                          	</div>
	                         	<div class='col-sm-2'></div>
    						</div>
	                   	</div>
    					<div class='form-group'>
							<div class='row' style='display:none' id='trleavetype'>
								<div class='col-sm-2'></div>
								<div class='col-sm-3'>
									<label for='splleave'>Select Leave Type:</label>
	                          	</div>
								<div class='col-sm-5'>
	                               	<SELECT class='form-control' id= 'splleave' name='splleave'>
										<option value='Choose' selected> Choose Leave Type </option>";
										global $db;
										$sql = $db->query("SELECT * FROM hrsplleaves order by leavetype asc");
										for ($i=0;$i<$db->countRows($sql);$i++)
										{
											$result = $db->fetchArray($sql);
											echo "<option value='".$result['default']."'>".$result['leavetype']."</option>";
										}
									echo "</SELECT>
	  							</div>
								<div class='col-sm-2'></div>
							</div>
	  					</div>
	  					<div class='form-group'>
							<div class='row' style='display:none' id='option'>
								<div class='col-sm-2'></div>
								<div class='col-sm-8'>
									<input type='radio' name='selectoption' value='days' checked id='days'>days
									<input type='radio' name='selectoption' value='weeks' id='weeks'>Weeks
								</div>
	  							<div class='col-sm-2'></div>
							</div>
	  					</div>
	  					<div class='form-group'>
							<div class='row' style='display:none' id='defaultdays'>
								<div class='col-sm-2'></div>
								<div class='col-sm-3'>
									<label for='defaultdays'>Number of Days:</label>
	  							</div>
    							<div class='col-sm-5'>
					    			<input id='noofdays' readonly='true' name='noofdays'/>
					    			<span id='labeloption'>days</span>
    							</div>
	  							<div class='col-sm-2'></div>
     						</div>
	  					</div>
	  					<div class='form-group'>
							<div class='row' style='display:none' id='reason'>
								<div class='col-sm-2'></div>
								<div class='col-sm-3'>
									<label for='reason'>Reason:</label>
	  							</div>
	  							<div class='col-sm-5'>
						   			<textarea class='form-control' id='reason' rows='7' cols='30' name='reason' required></textarea>
	  							</div>
	  							<div class='col-sm-2'></div>
	  						</div>
	  					</div>
							
	  					<div class='form-group'>
							<div class='row' style='display:none' id='splsubmit'>
								<div class='col-sm-12 text-center'>
				  					<input class='btn btn-primary submit' type='submit' name='submit' value='Submit' />
				   				</div>
							</div>
						</div>
				   		<div class='form-group'>
							<div class='row' style='display:none'>
								<div class='col-sm-12'>
				   					<input type='text'  class='form-control' name='splleavename' id='splleavename' size='20' readonly='true'/>
				   				</div>
							</div>
						</div>
       				</div>
				</div>
    		</form>
    	   <script>
			   $(document).ready(function(){
					$('.open-datetimepicker').datetimepicker({
						format: 'yyyy-mm-dd',
                        minView : 2,
                        autoclose: true     
					});
			   });
		</script>";
										
	}
}
				
if(isset($_REQUEST['applyforteam']))
{
	### Get Departments
	$querydept = "SELECT distinct(dept) FROM `emp` ORDER BY dept ASC";
	$resultdept = $db -> query($querydept);
	## Department name
	if($resultdept) {
		while ($row = mysql_fetch_assoc($resultdept)) {
			$department = $department . '<option value="' . $row["dept"] . '">';
			$department = $department . $row["dept"];
			$department = $department . '</option>';
		}
		$department = $department . '<option value="ALL">';
		$department = $department . "ALL";
		$department = $department . '</option>';
	}
				
	echo "<form id='applyLeaveForTeam' method='POST' action='hr.php?applyLeaveForTeam=1'>";
	echo "<div class='panel panel-primary'>
		<div class='panel-heading text-center'>
			<strong style='font-size:20px;'>Apply Leave For Team</strong>
		</div>
		<div class='panel-body'>";
			echo "<div class='form-group'>
				<div class='row'>
					<div class='col-sm-2'></div>
					<div class='col-sm-3'>
						<label for='selectTeam'>Apply Leave For: </label>
					</div>
					<div class='col-sm-5'>
						<select class='form-control' name='applyforteamselectedoption'>
							<option value='allFemale'>All Female</option>
							<option value='allMale'>All Male</option>
							$department
						</select>
					</div>
					<div class='col-sm-2'></div>
				</div>
			</div>";
			echo "<div class='form-group'>
				<div class='row'>
					<div class='col-sm-2'></div>
					<div class='col-sm-3'>
						<label for='applyforteamfromDate'>Date:</label>
					</div>
					<div class='col-sm-5'>
						<div class='input-group'>
							<input type='text' id='applyforteamfromDate' class='form-control open-datetimepicker' name='applyforteamfromDate' size='20'>
							<label class='input-group-addon btn' for='date'>
								<span class='fa fa-calendar open-datetimepicker'></span>
							</label>
						</div>
					</div>
					<div class='col-sm-2'></div>
				</div>
			</div>";
			echo "<div class='form-group'>
				<div class='row'>
					<div class='col-sm-2'></div>
					<div class='col-sm-3'>
						<label for='applyforteamDay'>Select Day:</label>
					</div>
					<div class='col-sm-5'>
						<label class='radio-inline'><input type='radio' class='radio' name='applyforteamRadio' value='applyforteamFullDay' checked> Full Day</label>
						<label class='radio-inline'><input type='radio' class='radio' name='applyforteamRadio' value='applyforteamHalfDay'> Half Day</label>
						<label class='radio-inline'><input type='radio' class='radio' name='applyforteamRadio' value='applyforteamWFH'> WFH</label>
					</div>
					<div class='col-sm-2'></div>
				</div>
			</div>";
			echo "<div class='form-group'>
				<div class='row'>
					<div class='col-sm-2'></div>
					<div class='col-sm-3'>
						<label for='applyforteamreason'>Reason:</label>
		    	  	</div>
					<div class='col-sm-5'>
		    	  		<textarea class='form-control required' name='applyforteamreason'></textarea>
		    	  	</div>
		    	  	<div class='col-sm-2'></div>
		   		</div>
		    </div>";
		    echo "<div class='form-group'>
				<div class='row'>
					<div class='col-sm-12 text-center'>
						<input type='submit' class='btn btn-primary' name='applyforteamsubmit' value='Apply Leave' />
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
 <script>
	$(document).ready(function(){
		$('.open-datetimepicker').datetimepicker({
			format: 'yyyy-mm-dd',
		  	minView : 2,
		   	autoclose: true     
		});
	});
</script>";
		
}
				
if(isset($_REQUEST['applyLeaveForTeam'])) {
	echo "<form id='applyLeaveForTeamConfirmation' method='POST' action='hr.php?applyLeaveForTeamConfirmation=1'>";
	echo "<div class='panel panel-primary'>
		<div class='panel-heading text-center'>
			<strong style='font-size:20px;'>Apply Leave For Team Confirmation</strong>
		</div>
		<div class='panel-body'>";
			echo "<div class='form-group'>
				<div class='row'>
					<div class='col-sm-2'></div>
					<div class='col-sm-3'>
						<label>From Date:</label>
					</div>
					<div class='col-sm-5'>
						<input type='text' class='form-control' name='applyLeaveForTeamConfirmationFromDate' value='".$_REQUEST['applyforteamfromDate']."' size='20' readonly/>
					</div>
					<div class='col-sm-2'></div>
				</div>
			</div>";
	echo "<div class='form-group'>
		<div class='row'>
			<div class='col-sm-2'></div>
			<div class='col-sm-3'>
				<label>Reason:</label>
			</div>
			<div class='col-sm-5'>
				<textarea class='form-control required' name='applyLeaveForTeamConfirmationReason' readonly>".$_REQUEST['applyforteamreason']."</textarea>
			</div>
			<div class='col-sm-2'></div>
		</div>
	</div>";
	if(isset($_REQUEST['applyforteamRadio']) &&  $_REQUEST['applyforteamRadio']=="applyforteamFullDay") {
		echo "<div class='form-group'>
			<div class='row'>
				<div class='col-sm-2'></div>
				<div class='col-sm-3'>
					<label>Selected Day:</label>
				</div>
				<div class='col-sm-5'>
					<input type='text' class='form-control' name='applyLeaveForTeamConfirmationDay' value='FullDay' size='20' readonly/>
				</div>
				<div class='col-sm-2'></div>
			</div>
		</div>";
	}
	if(isset($_REQUEST['applyforteamRadio']) &&  $_REQUEST['applyforteamRadio']=="applyforteamHalfDay") {
		echo "<div class='form-group'>
			<div class='row'>
				<div class='col-sm-2'></div>
				<div class='col-sm-3'>
					<label>Selected Day:</label>
				</div>
				<div class='col-sm-5'>
					<input type='text' class='form-control' name='applyLeaveForTeamConfirmationDay' value='HalfDay' size='20' readonly/><br>
					<label class='radio-inline'><input type='radio' class='radio' name='applyLeaveForTeamConfirmationRadioDay' value='firstHalf' checked> First Half</label>
					<label class='radio-inline'><input type='radio' class='radio' name='applyLeaveForTeamConfirmationRadioDay' value='secondHalf'> Second Half</label>
				</div>
				<div class='col-sm-2'></div>
			</div>
		</div>";
	}
	if(isset($_REQUEST['applyforteamRadio']) &&  $_REQUEST['applyforteamRadio']=="applyforteamWFH") {
		echo "<div class='form-group'>
			<div class='row'>
				<div class='col-sm-2'></div>
				<div class='col-sm-3'>
					<input type='text' class='form-control' name='applyLeaveForTeamConfirmationDay' value='WFH' size='20' readonly/><br>
					<label class='radio-inline'><input type='radio' class='radio' name='applyLeaveForTeamConfirmationRadioDay' value='FullDay' checked> Full day WFH</label>
					<label class='radio-inline'><input type='radio' class='radio' name='applyLeaveForTeamConfirmationRadioDay' value='firstHalf' checked> First Half</label>
					<label class='radio-inline'><input type='radio' class='radio' name='applyLeaveForTeamConfirmationRadioDay' value='secondHalf'> Second Half</label>
				</div>
			<div>
		</div>";
	}
	echo "<div class='form-group'>
			<div class='row'>
				<div class='col-sm-2'></div>
				<div class='col-sm-3'>
					<label>Select Mailing Option:</label>
				</div>
				<div class='col-sm-5'>
					<label class='radio-inline'>
						<input type='radio' class='radio' name='applyLeaveForTeamConfirmationOption' value='sendMailSingle' checked> Send Mail Individually
					</label>			
				</div>
				<div class='col-sm-2'></div>
			</div>
		</div>";
	echo "<div class='form-group'>
			<div class='row'>
				<div class='col-sm-2'></div>
				<div class='col-sm-8'>
					<label>Apply Leave for following employees. Unselect employee if he/she is not included in below list.</label>
				</div>
				<div class='col-sm-2'></div>
			</div>
		</div>";
	if($_REQUEST['applyforteamselectedoption']=='allFemale') {
		$sql = $db->query("SELECT emp.empname, emp.empid as empempid, empprofile.empid as empprofileempid, empprofile.gender FROM emp,empprofile where emp.empid=empprofile.empid and empprofile.gender='F' order by empname asc");
	} elseif($_REQUEST['applyforteamselectedoption']=='allMale') {
		$sql = $db->query("SELECT emp.empname, emp.empid as empempid, empprofile.empid as empprofileempid, empprofile.gender FROM emp,empprofile where emp.empid=empprofile.empid and empprofile.gender='M' order by empname asc");
	} elseif ($_REQUEST['applyforteamselectedoption']=='ALL')  {
        $sqlQuery="SELECT emp.empname, emp.empid as empempid FROM emp where emp.state='Active' order by empname asc";
		$sql = $db->query($sqlQuery);
    } else {
    	$dept=$_REQUEST['applyforteamselectedoption'];
		$sqlQuery="SELECT emp.empname, emp.empid as empempid FROM emp where  emp.dept=\"$dept\"  and emp.state='Active' order by empname asc";
		$sql = $db->query($sqlQuery);
	}
	if($db->countRows($sql)) {
		while($row=$db->fetchArray($sql)) {
			echo "<div class='form-group'>
				<div class='row'>
					<div class='col-sm-2'></div>
					<div class='col-sm-8'>
						<input type='checkbox' name='applyforteamempConfirmationCheck[]' value='".$row['empempid']."' checked></td><td>".$row['empname']."<br>
					</div>
					<div class='col-sm-2'></div>
				</div>
			</div>";
		}
	} else {
		echo "No employees are present with selected criteria.";
	}
	echo "<div class='form-group'>
				<div class='row'>
					<div class='col-sm-12 text-center'>
						<input type='submit' class='btn btn-primary' id='applyLeaveForTeamConfirmationSubmit' name='applyLeaveForTeamConfirmationSubmit' value='Confirm Leave' />
					</div>
				</div>
			</div>
		</div>
	</div>
</form>";
echo "<div id='loadingmessage' style='display:none'>
	<img align='middle' src='images/loading.gif'/>
</div>";
}				
if(isset($_REQUEST['applyLeaveForTeamConfirmation'])) {
	if($_REQUEST['applyforteamempConfirmationCheck']) {
		$successfulEmps = array();
		$notSuccessfulEmpsMsg= array();
		$successfulEmpsMsg= array();
		foreach ($_REQUEST['applyforteamempConfirmationCheck'] as $empid) {
			$alreadyPresent=0;
			$query1="SELECT * from `perdaytransactions` where `date`='".$_REQUEST['applyLeaveForTeamConfirmationFromDate']."' and `empid`='".$empid."'";
			$sql = $db->query($query1);
			if($db->countRows($sql) > 0) {
				$row = $db->fetchArray($sql);
				$query2="SELECT * from `empleavetransactions` where `transactionid`='".$row['transactionid']."' and (`approvalstatus`='Approved' or `approvalstatus`='Pending')";
				$queryEmpLeaveTransactionTable=$db->query($query2);
				if($db->countRows($queryEmpLeaveTransactionTable) > 0) {
					$alreadyPresent=1;
				}
			}
			if($alreadyPresent) {
				# Get the transaction id and check in empleavetransaction table, if the transaction is pending or apoorved
				array_push($notSuccessfulEmpsMsg,"<td>".getempName($empid)." has already applied leave on ".$_REQUEST['applyLeaveForTeamConfirmationFromDate']."</td>");
			} else {
				# Insert the transaction for each employee
				$transaction_id = generate_transaction_id();
				if($_REQUEST['applyLeaveForTeamConfirmationDay']=="FullDay") {
					$leavetype="FullDay";
					$shift="";
				} elseif($_REQUEST['applyLeaveForTeamConfirmationDay']=="HalfDay") {
					$leavetype="HalfDay";
					if($_REQUEST['applyLeaveForTeamConfirmationRadioDay']=="firstHalf") {
						$shift="firstHalf";
					} else {
						$shift="secondHalf";
					}
				} elseif($_REQUEST['applyLeaveForTeamConfirmationDay']=="WFH") {
					$leavetype="WFH";
					if($_REQUEST['applyLeaveForTeamConfirmationRadioDay']=="firstHalf") {
						$shift="firstHalf";
					} else {
						$shift="secondHalf";
					}
				}
				$query = "INSERT INTO`empleavetransactions` (`transactionid` ,`empid` ,`startdate` ,`enddate` ,`count`,`reason`,`approvalstatus`,`approvalcomments`) ".
						"VALUES ('".$transaction_id."','".$empid."','".$_REQUEST['applyLeaveForTeamConfirmationFromDate']."',".
						"'".$_REQUEST['applyLeaveForTeamConfirmationFromDate']."', '0', '".addslashes($_REQUEST['applyLeaveForTeamConfirmationReason'])."',".
						"'Approved','Approved By HR (".$_SESSION['u_fullname'].")')";
				$result1=$db->query($query);
				$perdayquery = "Insert into `perdaytransactions` (`transactionid` ,`empid` ,`date` ,`leavetype`,`shift`,`compoffreason`)
							  values('".$transaction_id."','".$empid."','".$_REQUEST['applyLeaveForTeamConfirmationFromDate']."','".$leavetype."','".$shift."','".addslashes($_REQUEST['applyLeaveForTeamConfirmationReason'])."')";
				$result2=$db->query($perdayquery);
				if($result1 && $result2) {
					array_push($successfulEmps, $empid);
					array_push($successfulEmpsMsg,"<td>".getempName($empid)."</td>");
					if($_REQUEST['applyLeaveForTeamConfirmationOption'] == "sendMailSingle") {
						# Send Mail
						$cmd = '/usr/bin/php -f sendmail.php ' . $transaction_id . ' ' . $empid . ' ApproveLeave hr '.$_SESSION['u_empid'].'>> /dev/null &';
						exec($cmd);
					}
				}
			}
		}
		if(isset($notSuccessfulEmpsMsg) && sizeof($notSuccessfulEmpsMsg)>0) {
			echo "<table class='table table-bordered'>";
			echo "<caption>Leave is not applied for the following list of employees</caption>";
			foreach($notSuccessfulEmpsMsg as $msg) {
			echo "<tr>".$msg."</tr>";
		}
		echo "</table>";
	}
				
	if(isset($successfulEmpsMsg) && sizeof($successfulEmpsMsg)>0) {
		echo "<hr>";
		echo "<table class='table table-bordered'>";
		echo "<caption>Leave applied for the following list of employees successfully</caption>";
		foreach($successfulEmpsMsg as $msg) {
			echo "<tr>".$msg."</tr>";
		}
		echo "</table>";
	}
	//	if(isset($successfulEmps) && sizeof($successfulEmps)>0) {
	//		if($_REQUEST['applyLeaveForTeamConfirmationOption'] == "sendMailWhole") {
	//			# Send mail as a whole
	//			$cmd = '/usr/bin/php -f sendmail.php ' . $transaction_id . ' ' . $empid . ' ApproveLeave hr '.$_SESSION['u_empid'].' >> /dev/null &';
	//			exec($cmd);
	//		}
	///	}
	}
}
if(isset($_REQUEST['viewbalanceleaves']))
{
	echo "<div class='panel panel-primary'>
		<div class='panel-heading text-center'>
			<strong style='font-size:20px;'>View Balance Leave For All Employee</strong>
		</div>
		<div class='panel-body'>
			<table class='table table-striped table-bordered'>
				<tr class='info'>
					<th>Sr. No.</th>
					<th>Employee Name</th>
					<th>Emp ID</th>
					<th>Carry Forwarded Leaves</th>
					<th>Balance Leaves in present year</th>
					<th>Used Leaves in present year</th>
					<th>Carry forwarded + Balance Leaves in present year</th>
				</tr>";
				global $db;
				$sql = $db->query("SELECT empname, emp.empid, carryforwarded, balanceleaves FROM emp,emptotalleaves where emp.empid=emptotalleaves.empid order by empname asc");
				for ($i=0;$i<$db->countRows($sql);$i++)
				{
					$result = $db->fetchArray($sql);
					echo "<tr>
						<td>".($i+1)."</td>
		                <td>".$result['empname']."</td>
						<td align=center>".$result['empid']."</td>
						<td align=center>".$result['carryforwarded']."</td>
						<td align=center>".$result['balanceleaves']."</td>
						<td align=center>".(25-$result['balanceleaves'])."</td>
						<td align=center>".($result['carryforwarded']+$result['balanceleaves'])."</td>
					</tr>";
				}
			echo "</table>
		</div>
	</div>";
}
?>
</div><!--12 column end-->
</body>
</html>
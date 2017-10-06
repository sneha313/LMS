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
		<script>
			function removeByIndex(arr, index) {
				arr.splice(index, 1);
			}

			function removeByValue(arr, val) {
				for (var i = 0; i < arr.length; i++) {
					if (arr[i] == val) {
						arr.splice(i, 1);
						break;
					}
				}
				return arr;
			}
			$("#applyteammemberleaveid").click(function(){
				hidealldiv('loadmanagersection');
				$("#loadmanagersection").load('applyteammemberleave.php?getEmp=1');
			});
			$("#managermodifyempapprovedleaves").click(function() {
				hidealldiv('loadmanagersection');
				$("#loadmanagersection").load('modifyempapprovedleaves.php?role=manager');
			});
			$("#managerApproveEmpLeave").click(function() {
			     hidealldiv('loadmanagersection');
           		     $("#loadmanagersection").load('approveEmpleave.php?role=manager');
        	});
			$("#addextrawfhmanager").click(function() {
			     hidealldiv('loadmanagersection');
          		     $("#loadmanagersection").load('wfhhours/manageraddwfhforemp.php?role=manager');
       		});
			$("#viewextrawfhmanager").click(function() {
			     hidealldiv('loadmanagersection');
          		     $("#loadmanagersection").load('wfhhours/managerviewwfhform.php?role=manager&viewform=1');
       		});
			$("#approveextrawfhmanager").click(function() {
			     hidealldiv('loadmanagersection');
         		 $("#loadmanagersection").load('wfhhours/approveEmpExtrawfhhour.php?role=manager&approveview=1');
      		});
		</script>
		<style type="text/css">
			#addextrawfhmanager{
				cursor: pointer;
			}
			#viewextrawfhmanager{
				cursor: pointer;
			}
			#managerApproveEmpLeave{
				cursor: pointer;
			}	
			#managermodifyempapprovedleaves{
				cursor: pointer;
			}
			#modifyextrawfhmanager{
				cursor: pointer;
			}
			#approveextrawfhmanager{
				cursor: pointer;
			}
		</style>
	</head>
	<body>
		<div class="col-sm-12" id="test">
			<div class="panel panel-primary">
  				<div class="panel-heading text-center">
					<strong style="font-size:20px;">Manager Section</strong>
				</div>
  				<div class="panel-body table-responsive">
					<!-- <table class="table table-bordered table-hover">
						 <tr>
                            <td><a id="applyteammemberleaveid" href="#" >Apply Leave For Team</a></td>
                            <td>Manager can apply leaves for their team members.</td>
                        </tr>
						<tr>
                            <td><a id='addextrawfhmanager' href="#">Add Extra WFH Hour</a></td>
                            <td>Manager can apply extra WFH hour for their team member.</td>
                        </tr>
						<tr>
							<td><a id="managermodifyempapprovedleaves" href="#">Modify Employee Approved Leaves</a></td>
							<td>Manager can delete/edit/modify employee approved leaves.</td>
						</tr>
						<tr>
                            <td><a id="viewextrawfhmanager" href="#">View/Modify Extra WFH Hour</a></td>
                            <td>Manager can view or modify extra WFH hour for their team member.</td>
                        </tr>
						<tr>
                            <td><a id="managerApproveEmpLeave" href="#">Approve Employee Leaves</a></td>
                            <td>Manager can approved employee pending leaves.</td>
                        </tr>
                        <tr>
                            <td><a id="approveextrawfhmanager" href="#">Approve/Cancel Extra WFH Hour</a></td>
                            <td>Manager can approve/delete extra WFH hour applied by their team member.</td>
                        </tr>
					</table>-->
					<!--Approve/Delete Extra WFH Hour by hr panel start-->
					
						
					<table class="table table-bordered">
						<tr>
							<td>
								<div class="panel panel-info">
									<div class="panel-heading">Apply Leave For Team</div>
									<!-- apply leave for team by manager panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id="applyteammemberleaveid" href="#" >Apply Leave For Team</a></td>
												<td>Manager can apply leaves for their team members.</td>
											</tr>
										</table>
									</div><!-- apply leave for team by manager panel body close-->
								</div><!-- apply leave for team by manager panel close -->
								<div class="panel panel-info">
									<div class="panel-heading">Add Extra WFH Hour</div>
									<!-- Add Extra WFH Hour by manager panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id='addextrawfhmanager' href="#">Add Extra WFH Hour</a></td>
												<td>Manager can apply leaves for their team members.</td>
											</tr>
										</table>
									</div><!-- Add Extra WFH Hour by manager panel body close-->
								</div><!-- Add Extra WFH Hour by manager panel close -->
								<!--Approve/Delete Extra WFH Hour by hr panel start-->
								<div class="panel panel-info">
									<div class="panel-heading">Approve/Cancel Extra WFH Hour</div>
									<!-- apply leave by hr panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id="approveextrawfhmanager" href="#">Approve/Cancel Extra WFH Hour</a></td>
												<td>Manager can approve/delete extra WFH hour applied by their team member.</td>
											</tr>
										</table>
									</div><!-- Approve/Delete Extra WFH Hour by hr panel body close-->
								</div><!-- Approve/Delete Extra WFH Hour by hr panel close -->
							</td>
							
	                        <td>
								<!--hr report panel start-->
								<div class="panel panel-info">
	  								<div class="panel-heading">Modify Employee Approved Leaves</div>
									<!--hr report panel body start-->
	  								<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id="managermodifyempapprovedleaves" href="#">Modify Employee Approved Leaves</a></td>
												<td>Manager can edit/delete employee approved leaves.</td>
											</tr>
										</table>
									</div><!--hr report panel body close-->
								</div><!--hr report panel close-->
								
								<!--apply leave by hr panel start-->
								<div class="panel panel-info">
									<div class="panel-heading">View/Modify Extra WFH Hour</div>
									<!-- apply leave by hr panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id="viewextrawfhmanager" href="#">View/Modify Extra WFH Hour</a></td>
												<td>Manager can view or modify extra WFH hour for their team member.</td>
											</tr>
										</table>
									</div><!-- apply leave by hr panel body close-->
								</div><!-- apply leave by hr panel close -->
								<!--Approve/Delete Extra WFH Hour by hr panel start-->
								<div class="panel panel-info">
									<div class="panel-heading">Approve Employee Leaves</div>
									<!-- apply leave by hr panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id="managerApproveEmpLeave" href="#">Approve Employee Leaves</a></td>
												<td>Manager can approved employee pending leaves.</td>
											</tr>
										</table>
									</div><!-- Approve/Delete Extra WFH Hour by hr panel body close-->
								</div><!-- Approve/Delete Extra WFH Hour by hr panel close -->
                        	</td>
						</tr>
					</table>
  				</div><!-- panel-body div close -->
			</div><!-- panel div close -->
		</div><!-- 12 column div close -->
		<script>
			function removeByValue(arr, val) {
				for(var i=0; i<arr.length; i++) {
					if(arr[i] == val) {
						arr.splice(i, 1);
							break;
					}
				}
				return arr;
			}
			
			function hidealldiv(div) {
				var myCars = new Array("loadempapplyleave", "loadempleavestatus", "loadempleavehistory", "loadempleavereport", "loadempeditprofile", "loadholidays", "loadempleavereport", "loadteamleavereport", "loadteamleaveapproval", "loadattendance", "loadcalender", "loadpendingstatus", "loadhrsection", "loadmanagersection", "loadapplyteammemberleave", "loadtrackattendance", "loadextrawfhhr","test");
				var hidedivarr = removeByValue(myCars, div);
				hidediv(hidedivarr);
				showdiv(div);
			}
			
			function hidediv(arr) {
				$("#footer").show();
				for (var i = 0; i < arr.length; i++) {
					$("#" + arr[i]).hide();
					$("#" + arr[i]).html("");
				}
			}
			
			function showdiv(div) {
				$("#" + div).show();
			}
				
			$(document).ready(function() {
				$(".open-datetimepicker").datetimepicker({
					format: 'yyyy-mm-dd',
                    minView : 2,
                    autoclose: true  
				});
				
				$(".open-datetimepicker1").datetimepicker({
					format: 'yyyy-mm-dd',
                    minView : 2,
                    autoclose: true  
				});
				
				/*$("#addextrawfhmanager").click(function() {
					//hidealldiv('loadmanagersection');
					$.ajax({
							data : $(this).serialize(),
							type : $(this).attr('method'),
							url: "wfhhours/manageraddwfhforemp.php?role=manager&approveview=1",
							success : function(response) {
								$("#"+divid).html(response);
							}
						});
						return false;
				});*/
				
				/*$("#viewextrawfhmanager").click(function() {
					//hidealldiv('loadmanagersection');
					$.ajax({
							data : $(this).serialize(),
							type : $(this).attr('method'),
							url: "wfhhours/managerviewwfhform.php?viewform=1",
							success : function(response) {
								$("#"+divid).html(response);
							}
						});
						return false;
				});*/
			});
		</script>
	</body>
</html>
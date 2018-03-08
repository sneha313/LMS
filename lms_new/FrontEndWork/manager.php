<?php
	session_start();
	//require_once 'Library.php';
	require_once 'attendenceFunctions.php';
	//require_once 'generalFunctions.php';
	require_once ("librarycopy1.php");
	require_once ("generalcopy.php");
	error_reporting("E_ALL");
	$db=connectToDB();
?>
<html>
	<head>
		<script>
		function hidealldiv(div) {
			var myCars=new Array("loadinout","loadbalanceleavesid","loadleaveinfo","loadDepartment","loadmyprofile","loadpersonalinfo","loadofficialinfo","loadempapplyleave","loadempleavestatus","loadempleavehistory",
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
			for (var i = 0; i < arr.length; i++) {
				$("#" + arr[i]).hide();
				$("#" + arr[i]).html("");
			}
		}

		function showdiv(div) {
			$("#" + div).show();
		}

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
				$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
				$("#loadmanagersection").load('applyteammemberleave.php?getEmp=1');
			});
			$("#managermodifyempapprovedleaves").click(function() {
				hidealldiv('loadmanagersection');
				$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
				$("#loadmanagersection").load('modifyempapprovedleaves.php?role=manager');
			});
			$("#managerApproveEmpLeave").click(function() {
			     hidealldiv('loadmanagersection');
			     $('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
           		 $("#loadmanagersection").load('approveEmpleave.php?role=manager');
        	});
			$("#addextrawfhmanager").click(function() {
			     hidealldiv('loadmanagersection');
			     $('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
          		 $("#loadmanagersection").load('wfhhours/manageraddwfhforemp.php?role=manager');
       		});
			$("#viewextrawfhmanager").click(function() {
			     hidealldiv('loadmanagersection');$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
          		     $("#loadmanagersection").load('wfhhours/managerviewwfhform.php?role=manager&viewform=1');
       		});
			$("#approveextrawfhmanager").click(function() {
			     hidealldiv('loadmanagersection');$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
         		 $("#loadmanagersection").load('wfhhours/approveEmpExtrawfhhour.php?role=manager&approveview=1');
      		});
			$("#approveInOut").click(function() {
			  	hidealldiv('loadinout');
			  	$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
          		$("#loadinout").load('applyinout.php?role=manager&viewInOutForManager=1');
       		});
			$("#addInOut").click(function() {
			  	hidealldiv('loadinout');
			  	$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
          		$("#loadinout").load('manageraddapplyinout.php?role=manager&managerempinoutform=1');
       		});
			$("#viewInOut").click(function() {
			  	hidealldiv('loadinout');
			  	$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
          		$("#loadinout").load('managerviewinout.php?role=manager&viewInOutForManager=1');
       		});
		</script>
		<style type="text/css">
			#addextrawfhmanager,#viewextrawfhmanager,#managerApproveEmpLeave,#managermodifyempapprovedleaves,#modifyextrawfhmanager,#approveextrawfhmanager{
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
								<!-- <div class="panel panel-info">
									<div class="panel-heading">Approve Employee Leaves</div>
									<!-- apply leave by hr panel body start-->
									<!--  <div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id="managerApproveEmpLeave" href="#">Approve Employee Leaves</a></td>
												<td>Manager can approved employee pending leaves.</td>
											</tr>
										</table>
									</div><!-- Approve/Delete Extra WFH Hour by hr panel body close-->
								<!--  </div><!-- Approve/Delete Extra WFH Hour by hr panel close -->
								
								<div class="panel panel-info">
									<div class="panel-heading">Apply Leave For Team</div>
									<!-- apply leave for team by manager panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id="applyteammemberleaveid" href="#" >Apply Leave For Team Member</a></td>
												<td>Manager can apply leaves for their team members.</td>
											</tr>
										</table>
									</div><!-- apply leave for team by manager panel body close-->
								</div><!-- apply leave for team by manager panel close -->
								
								<div class="panel panel-info">
									<div class="panel-heading">Apply Extra WFH Hour for Employee</div>
									<!-- Add Extra WFH Hour by manager panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id='addextrawfhmanager' href="#">Add Extra WFH Hour</a></td>
												<td>Manager can apply leaves for their team members.</td>
											</tr>
											<tr>
												<td><a id="viewextrawfhmanager" href="#">View/Modify Extra WFH Hour</a></td>
												<td>Manager can view or modify extra WFH hour for their team member.</td>
											</tr>
											<tr>
												<td><a id="approveextrawfhmanager" href="#">Approve/Cancel Extra WFH Hour</a></td>
												<td>Manager can approve/cancel extra WFH hour applied by their team member.</td>
											</tr>
										</table>
									</div><!-- Add Extra WFH Hour by manager panel body close-->
								</div><!-- Add Extra WFH Hour by manager panel close -->
							</td>
							
	                        <td>
	                        <!--Modify Employee Approved Leaves panel start-->
								<div class="panel panel-info">
	  								<div class="panel-heading">Modify / Approve Employee Leaves</div>
									<!--hr report panel body start-->
	  								<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id="managermodifyempapprovedleaves" href="#">Modify Employee Approved Leaves</a></td>
												<td>Manager can edit/delete employee approved leaves.</td>
											</tr>
											<tr>
												<td><a id="managerApproveEmpLeave" href="#">Approve Employee Leaves</a></td>
												<td>Manager can approved employee pending leaves.</td>
											</tr>
										</table>
									</div><!--Modify Employee Approved Leaves panel body close-->
								</div><!--Modify Employee Approved Leaves panel close-->
								
								<div class="panel panel-info">
									<div class="panel-heading">Apply In/Out Detail for Employee</div>
									<!-- Add Extra WFH Hour by manager panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id='addInOut' href="#">Add In/Out Detail</a></td>
												<td>Manager can add In/Out detail for their team members.</td>
											</tr>
											<tr>
												<td><a id="viewInOut" href="#">View/Modify In/Out Detail</a></td>
												<td>Manager can view or modify In/Out detail for their team member.</td>
											</tr>
											<tr>
												<td><a id="approveInOut" href="#">Approve/Not Approve In/Out Details</a></td>
												<td>Manager can approve/Cancel extra In/Out details applied by their team member.</td>
											</tr>
										</table>
									</div><!-- Add Extra WFH Hour by manager panel body close-->
								</div><!-- Add Extra WFH Hour by manager panel close -->
                        	</td>
						</tr>
					</table>
  				</div><!-- panel-body div close -->
			</div><!-- panel div close -->
		</div><!-- 12 column div close -->
	</body>
</html>
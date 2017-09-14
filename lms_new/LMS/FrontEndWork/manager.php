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
			//$("#modifyextrawfhmanager").click(function() {
			   //  hidealldiv('loadmanagersection');
         		// $("#loadmanagersection").load('wfhhours/modifyExtrawfhhour.php?role=manager');
      		//});
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
					<table class="table table-bordered table-hover">
						<tr>
                            <td><a id="applyteammemberleaveid" href="applyteammemberleave.php?getEmp=1" >Apply Leave For Team</a></td>
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
					</table>
  				</div>
			</div>
		</div>
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
					format: "dd-mm-yy"
				});
				
				$(".open-datetimepicker1").datetimepicker({
					format: "dd-mm-yy"
				});
				
				$("#addextrawfhmanager").click(function() {
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
				});
				
				$("#viewextrawfhmanager").click(function() {
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
				});
			});
		</script>
	</body>
</html>
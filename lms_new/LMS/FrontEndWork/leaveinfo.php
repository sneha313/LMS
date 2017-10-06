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
		<!-- Javascript code for this view -->
		<script type="text/javascript" src="projectjs/jsCommon.js"></script>
		<script>
		function hidealldiv(div) {
			var myCars=new Array("loadinout","loadleaveinfo","loadmyprofile","loadpersonalinfo","loadofficialinfo","loadempapplyleave","loadempleavestatus","loadempleavehistory",
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
		
			$("#applyleaveid").click(function(){
				hidealldiv('loadempapplyleave');
				$("#loadempapplyleave").load('applyleave.php?leaveform=1');
			});
	
			$("#compoffleaveid").click(function(){
				hidealldiv('loadcompoffleave');
				$("#loadcompoffleave").load('ApplyCompoffLeave.php?compoffleave=1');
			});
	
			$("#applyOnSiteLeave").click(function(){
				hidealldiv('loadempapplyleave');
				$("#loadempapplyleave").load('applyOnSite.php?leaveform=1');
			});
	
			$("#selfleavehistoryid").click(function(){
				hidealldiv('loadempleavehistory');
				$("#loadempleavehistory").load('selfleavehistory.php');
			});
	
			$("#inoutForm").click(function() {
				hidealldiv('loadinout');
				$("#loadinout").load('applyinout.php?inoutForm=1');
			});
	
			$("#viewInOutPendingForEmployee").click(function() {
			     hidealldiv('loadinout');
	       		 $("#loadinout").load('applyinout.php?viewInOutPendingForEmployee=1');
	    	});
			
			$("#viewInOutDetailsHistory").click(function() {
			    hidealldiv('loadinout');
	       	    $("#loadinout").load('applyinout.php?viewInOutDetailsHistory=1');
	    	});

			$("#optionalLeaveStatus").click(function(){
				hidealldiv('loadoptionalleave');
				$("#loadoptionalleave").load('optionalleave.php');
			});
		</script>
		<!-- CSS for this view -->
		<style type="text/css">
			#inoutForm,#viewInOutPendingForEmployee,#viewInOutDetailsHistory {
				cursor: pointer;
			}
		</style>
	</head>
	<body>
		<!--12 column start-->
		<div class="col-sm-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<strong style="font-size:20px;">My Leave Details</strong>
				</div>
				<div class="panel-body table-responsive" style="background:	#FFF5EE;">
					<table class="table table-bordered">
						<tr>
			            	<td width="50%">
			                	<div class="panel panel-info">
			  						<div class="panel-heading text-center">
			  							<strong style="font-size:20px;">Apply Leave</strong>
			  						</div>
			  						<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td width="90"><a id="applyleaveid" href="#">Apply Leave</a></td>
												<td>User can apply Regular/Special Leave, Full/Half day Leave, and Full/Half day WFH</strong>. </td>
											</tr>
										</table>
  									</div>
								</div>
								<div class="panel panel-info">
			  						<div class="panel-heading text-center">
			  							<strong style="font-size:20px;">Apply ONSITE</strong>
			  						</div>
			  						<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id="applyOnSiteLeave" href="#">Apply ONSITE</a></td>
												<td>Full day leave</td>
											</tr>
										</table>
  									</div>
								</div>
								<div class="panel panel-info">
			  						<div class="panel-heading text-center">
			  							<strong style="font-size:20px;">User Leave History</strong>
			  						</div>
			  						<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id='selfleavehistoryid' href='#'>Emp Leave History</a></td>
												<td>Show all the leave which is taken from january till december.</td>
											</tr>
											<tr>
												<td><a id='optionalLeaveStatus' href='#'>Optional Holidays Applied</a></td>
												<td>Show all the Optional Holidays Applied.</td>
											</tr>
										</table>
  									</div>
								</div>	
                        	</td>
                        
                       	 	<td>
								<div class="panel panel-info">
			  						<div class="panel-heading text-center">
			  							<strong style="font-size:20px;">Apply Comp Off</strong>
			  						</div>
			  						<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td width="150"><a id="compoffleaveid" href="#">Comp Off Leave</a></td>
												<td>Need Worked Holiday date, Comp Off Leave date to apply Comp Off Leave</td>
											</tr>
										</table>
  									</div>
								</div>
								<div class="panel panel-info">
			  						<div class="panel-heading text-center">
			  							<strong style="font-size:20px;">Apply In/Out</strong>
			  						</div>
			  						<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id='inoutForm' href='#'>Apply In/Out Details</a></td>
												<td>Add/Modify In/Out Timings</td>
											</tr>
											<tr>
												<td><a id='viewInOutPendingForEmployee' href='#'>View Pending In/Out Details</a></td>
												<td>View All Pending In/Out Detail</td>
											</tr>
											<tr>
												<td><a id='viewInOutDetailsHistory' href='#'>View In/Out Details History</a></td>
												<td>View All Approved In/Out Detail</td>
											</tr>
										</table>
  									</div>
								</div>							
                        	</td>
                    	</tr>
					</table>
				</div>
			</div>
		</div><!--12 column div end-->
	</body>
</html>
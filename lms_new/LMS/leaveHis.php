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
			var myCars=new Array("loadinout","loadallleavehis","loadleaveinfo","loadmyprofile","loadpersonalinfo","loadofficialinfo","loadempapplyleave","loadempleavestatus","loadempleavehistory",
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
		$("#selfleavehistoryid").click(function(){
			hidealldiv('loadempleavehistory');
			$('#loadempleavehistory').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
			$("#loadempleavehistory").load('selfleavehistory.php');
		});
		$("#optionalLeaveStatus").click(function(){
			hidealldiv('loadoptionalleave');
			$('#loadoptionalleave').html("<div><img src='public/images/spinload.jpg' class='img-responsive'/></div>");
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
				<div class="panel-heading text-center">
					<strong style="font-size:20px;">My Leave Details</strong>
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
		</div><!--12 column div end-->
	</body>
</html>
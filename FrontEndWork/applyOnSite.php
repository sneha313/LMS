<?php
session_start();
//require_once 'Library.php';
//require_once 'generalFunctions.php';
require_once ("librarycopy1.php");
require_once ("generalcopy.php");
$db=connectToDB();
?>
<html>
<head>
<script>
<?php 
	getApplyLeaveJs("onsiteapply","fromDate","toDate","loadempapplyleave","employeeid","applyOnSite.php");
	getDisplayDatesJs("loadempapplyleave");
	getSubmitJs("getShift","loadempapplyleave");
	getSetOptionsJs("hidesplLeave","leaveForm","setOptions");
?>
$("#leave_type").change(function() {
	var empid=$("#empid").val();
	$.post("getSplLeaveOptions.php?empid="+empid,function(data)
	{
		$("#special_leave").empty();
		$("#special_leave").append(data);
	});	
});
</script>
<?php
	$getCalIds = array("fromDate", "toDate");
	$calImg=getCalImg($getCalIds);
	echo $calImg;
?>
</head>
<body id="applyleavebody">
	<div id="wrapper">
		<div id="form-div">
	<?php
	if(isset($_REQUEST['leaveform']))
	{	
		getLeaveForm("applyOnSite.php","leaveForm","onsiteapply","leave_type","hidesplLeave","special_leave","fromDate","toDate","setOptions");
	}
	if(isset($_REQUEST['getdates']))
	{
		getDatesSection("fromDate","toDate",$_POST['empid'],$_SESSION['u_empid'],"applyOnSite.php");
	}
	if (isset($_REQUEST['getShift']))
	{
		getShiftSection();	
	}
	if (isset($_REQUEST['confirmleave']))
	{
		$empid=$_SESSION['u_empid'];
		getConfirmLeaveSection("applyOnSite.php",$empid,"fromDate","toDate");
	}
	?>
		</div>
	</div>
</body>
</html>


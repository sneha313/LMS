<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
require_once 'generalFunctions.php';
?>
<html>
<head>
<link rel="stylesheet" type="text/css" media="screen" href="css/table.css" />
<script>
<?php 
	if(isset($_REQUEST['leaveform']))
	{
		$empId=getValueFromQuery("select empid from emp where empname='".$_REQUEST['empuser']."' and state='Active'","empid");
		getApplyLeaveJs("teamApplyLeave","teamfromDate","teamtoDate","loadapplyteammemberleave","employeeid","applyteammemberleave.php",$empId);
		getSetOptionsJs("hideteammemsplLeave","teamleaveForm","teamsetOptions");
	}
	if(isset($_REQUEST['getdates']))
	{
		getDisplayDatesJs("loadapplyteammemberleave");
	}
	if (isset($_REQUEST['getShift']))
	{
		getSubmitJs("getShift","loadapplyteammemberleave");
	}
?>

</script>
<?php
$getCalIds = array("teamfromDate", "teamtoDate");
$calImg=getCalImg($getCalIds);
echo $calImg;
?>
</head>
<body id="applyleavebody">
	<div id="wrapper">
		<div id="form-div">
	<?php
	if(isset($_REQUEST['getEmp']))
	{
		getEmpForm("applyteammemberleave.php",$_SESSION['u_empid'],$_SESSION['user_desgn']);
	}
	if(isset($_REQUEST['leaveform']))
	{	
		if($_REQUEST['empuser']) {
			$childern=getChildren($_SESSION['u_empid']);
			$empId=getValueFromQuery("select empid from emp where empname='".$_REQUEST['empuser']."' and state='Active'","empid");
			if(in_array($empId,$childern)) {
				getLeaveForm("applyteammemberleave.php","teamleaveForm","teamApplyLeave","team_leave_type","hideteammemsplLeave","team_special_leave","teamfromDate","teamtoDate","teamsetOptions");
			} else {
				echo "<script>alert(\"You dont have permissions to apply leave for this employee '".$_REQUEST['empuser']."'\");
						$('#loadapplyteammemberleave').load('applyteammemberleave.php?getEmp=1');
						</script>";
			}
		}
		
	}
	if(isset($_REQUEST['getdates']))
	{
		getDatesSection("teamfromDate","teamtoDate",$_REQUEST['employeeid'],$_REQUEST['employeeid'],"applyteammemberleave.php");
	}
	if (isset($_REQUEST['getShift']))
	{
		getShiftSection();	
	}
	if (isset($_REQUEST['confirmleave']))
	{
		$empid=$_SESSION['teammem'];
		getConfirmLeaveSection("applyteammemberleave.php",$empid,"fromDate","toDate");
	}
	?>
		</div>
	</div>
</body>
</html>


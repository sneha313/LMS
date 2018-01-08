<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
require_once 'generalFunctions.php';
?>
<html>
<head>
<script>
<?php 
if(isset($_REQUEST['leaveform']))
{
	$empId=getValueFromQuery("select empid from emp where empname='".$_REQUEST['empuser']."' and state='Active'","empid");
	getApplyLeaveJs("hrApplyLeave","hrfromDate","hrtoDate","loadhrsection","employeeid","hrapplyleaveforall.php",$empId);
	getSetOptionsJs("hideteammemsplLeave","hrleaveForm","hrsetOptions");
}
if(isset($_REQUEST['getdates']))
{
	getDisplayDatesJs("loadhrsection");
}
if (isset($_REQUEST['getShift']))
{
	getSubmitJs("getShift","loadhrsection");
}
 
?>
</script>
<body id="applyleavebody">
	<div id="wrapper">
		<div id="form-div">
		<?php
		if(isset($_REQUEST['getEmp']))
		{
			getEmpForm("hrapplyleaveforall.php",$_SESSION['u_empid'],$_SESSION['user_dept']);
		}	
		if(isset($_REQUEST['addExtrawfh']))
		{
			addExtrawfhForm("hrapplyleaveforall.php",$_SESSION['u_empid'],$_SESSION['user_dept']);
		}	
		if(isset($_REQUEST['leaveform']))
		{	
			if($_REQUEST['empuser']) {
				if(($_SESSION['user_dept']=="HR")) {
					getLeaveForm("hrapplyleaveforall.php","hrleaveForm","hrApplyLeave","team_leave_type","hideteammemsplLeave","team_special_leave","hrfromDate","hrtoDate","hrsetOptions");
				} else {
					echo "<script>BootstrapDialog.alert(\"You dont have permissions to apply leave for this employee '".$_REQUEST['empuser']."'\");
						$('#loadapplyteammemberleave').load('applyteammemberleave.php?getEmp=1');
						</script>";
				}
			}
		}
		if(isset($_REQUEST['getdates']))
		{
			getDatesSection("hrfromDate","hrtoDate",$_REQUEST['employeeid'],$_REQUEST['employeeid'],"hrapplyleaveforall.php");
		}
		if (isset($_REQUEST['getShift']))
		{
			getShiftSection();	
		}
		if (isset($_REQUEST['confirmleave']))
		{
			$empid=$_SESSION['teammem'];
			getConfirmLeaveSection("hrapplyleaveforall.php",$empid,"fromDate","toDate");
		}
		?>
		</div>
	</div>
</body>
</html>


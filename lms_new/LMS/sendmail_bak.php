<?php
session_start();
$transactionid=$argv[1];
$empid=$argv[2];
$action=$argv[3];
if(isset($argv[4]) && $argv[4]=="compoffdate") {
	$transactionid=explode(",",$transactionid);
}
if((isset($argv[4]) || isset($argv[5]) && $argv[4]!="compoffdate"))  {
	$role=$argv[4];
	$hrid=$argv[5];
}

require_once 'Library.php';
$db=connectToDB();
if(isset($action) && isset($transactionid) && isset($empid))
{
	if ($action=='PendingLeave')
	{
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$reasonquery=$db->query("SELECT * FROM `empleavetransactions` WHERE transactionid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
		$phone_num=$db->query("select phonenumber,address from empprofile where empid=".$empid);
                $phone_row=$db->fetchAssoc($phone_num);
                $phonenumber=$phone_row['phonenumber'];
		$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		$sub="Leave Applied By ".$emprow['empname']." from ".$reasonsrow['startdate']." to ".$reasonsrow['enddate'].""; //subject of mail
		//body of mail
		$mailBody='
					Employee Name:	<b>'.$emprow['empname'].'</b><br>
					Manager Name:	<b>'.$emprow['managername'].'</b><br>
					Applied Leave from '.$reasonsrow['startdate'].' and '.$reasonsrow['enddate'].'<br><br>
					Reason:	'.$reasonsrow['reason'].'
					<table class="table table-bordered table-striped">
						<caption><h3>Leave for Approval</h3></caption>
						<tr align=justify class="info">
						<th>Date</th>
						<th>Leave Type</th>
					</tr>
					<tr>';
		while($daysrow=$db->fetchAssoc($daysquery)) {
		$mailBody=$mailBody. '<td>'.$daysrow['date'].'</td>';
		$mailBody=$mailBody. '<td>'.$daysrow['leavetype'].'</td>';
		$mailBody=$mailBody.'</tr><tr>';
		}
		$mailBody=$mailBody. '</tr></table>';
		$mailBody=$mailBody."You can reach ".$emprow['empname']." by ".$phonenumber;
		sendMail($to,$mailBody,$sub);
	}

	if ($action=='ModifiedLeave')
	{
		$empquery=$db->query("select * from emp where empid='".$empid."'  and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$reasonquery=$db->query("SELECT * FROM `empleavetransactions` WHERE transactionid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
		$phone_num=$db->query("select phonenumber,address from empprofile where empid=".$empid);
                $phone_row=$db->fetchAssoc($phone_num);
                $phonenumber=$phone_row['phonenumber'];
		$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		$sub="Leave modified By ".$emprow['empname']." from ".$reasonsrow['startdate']." to ".$reasonsrow['enddate'].""; //subject of mail
		//body of mail
		$mailBody='
					Employee Name:	<b>'.$emprow['empname'].'</b><br>
					Manager Name:	<b>'.$emprow['managername'].'</b><br>
					Applied Leave from '.$reasonsrow['startdate'].' and '.$reasonsrow['enddate'].'<br><br>
					Reason:	'.$reasonsrow['reason'].'
					<table class="table table-bordered table-striped">
						<caption><h3>Leave for Approval (Leave Modified by Employee)</h3></caption>
						<tr align=justify class="info">
						<th>Date</th>
						<th>Leave Type</th>
					</tr>
					<tr>';
		while($daysrow=$db->fetchAssoc($daysquery)) {
		$mailBody=$mailBody. '<td>'.$daysrow['date'].'</td>';
		$mailBody=$mailBody. '<td>'.$daysrow['leavetype'].'</td>';
		$mailBody=$mailBody.'</tr><tr>';
		}
		$mailBody=$mailBody. '</tr></table>';
		$mailBody=$mailBody."You can reach ".$emprow['empname']." by ".$phonenumber;
		sendMail($to,$mailBody,$sub);
	}
	if ($action=='ApproveLeave')
	{
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$phone_num=$db->query("select phonenumber,address from empprofile where empid=".$empid);
		$phone_row=$db->fetchAssoc($phone_num);
		$phonenumber=$phone_row['phonenumber'];
		$reasonquery=$db->query("SELECT * FROM `empleavetransactions` WHERE transactionid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
		$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		$sub="Leave is Approved from ".$reasonsrow['startdate']." to ".$reasonsrow['enddate']."";  //subject of mail
		//body of mail
		
		if(isset($role) || isset($hrid)) {
			$hrname="";
			if(isset($hrid)) {
				$query=$db->query("select empname from emp where empid='".$hrid."' and state='Active'");
				$row=$db->fetchAssoc($query);
				$hrname=$row['empname'];
			}
		}
		//body of mail
		$mailBody='Employee Name:	<b>'.$emprow['empname'].'</b><br>';
		if($role=="manager") {
			$mailBody=$mailBody.'Manager Name:	<b>'.$emprow['managername'].'</b><br>';
		}
		if($role=="hr") {
			$mailBody=$mailBody.'HR Name:	<b>'.$hrname.'</b><br>';
		}
		$mailBody=$mailBody.'Reason: '.$reasonsrow['reason'].'</b><br>
					Approved Leave from '.$reasonsrow['startdate'].' and '.$reasonsrow['enddate'].'<br><br>
					<table class="table table-bordered table-striped">
						<caption><h3>Approved Leaves</h3></caption>
						<tr align=justify class="info">
						<th>Date</th>
						<th>Leave Type</th>
					</tr>
					<tr>';
		while($daysrow=$db->fetchAssoc($daysquery)) {
		$mailBody=$mailBody. '<td>'.$daysrow['date'].'</td>';
		$mailBody=$mailBody. '<td>'.$daysrow['leavetype'].'</td>';
		$mailBody=$mailBody.'</tr><tr>';
		}
		$mailBody=$mailBody. '</tr></table>';
		$mailBody=$mailBody."You can reach ".$emprow['empname']." by ".$phonenumber; 
		sendMail($to,$mailBody,$sub);
	}
	
	if ($action=='DeletedByUser')
	{
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$phone_num=$db->query("select phonenumber,address from empprofile where empid=".$empid);
		$phone_row=$db->fetchAssoc($phone_num);
		$phonenumber=$phone_row['phonenumber'];
		$reasonquery=$db->query("SELECT * FROM `empleavetransactions` WHERE transactionid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
		$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		$sub="Leave is Deleted by Employee from ".$reasonsrow['startdate']." to ".$reasonsrow['enddate']."";  //subject of mail
		//body of mail
		$mailBody='
					Employee Name:	<b>'.$emprow['empname'].'</b><br>
					Manager Name:	<b>'.$emprow['managername'].'</b><br>
					Deleted Leaves from '.$reasonsrow['startdate'].' and '.$reasonsrow['enddate'].'<br><br>
					<table class="table table-bordered table-striped">
						<caption><h3>Deleted Leaves</h3></caption>
						<tr align=justify class="info">
						<th>Date</th>
						<th>Leave Type</th>
					</tr>
					<tr>';
		while($daysrow=$db->fetchAssoc($daysquery)) {
		$mailBody=$mailBody. '<td>'.$daysrow['date'].'</td>';
		$mailBody=$mailBody. '<td>'.$daysrow['leavetype'].'</td>';
		$mailBody=$mailBody.'</tr><tr>';
		}
		$mailBody=$mailBody. '</tr></table>';
		$mailBody=$mailBody."You can reach ".$emprow['empname']." by ".$phonenumber; 
		sendMail($to,$mailBody,$sub);
	}
	
	if ($action=='notApproveLeave')
	{
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$reasonquery=$db->query("SELECT * FROM `empleavetransactions` WHERE transactionid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		$phone_num=$db->query("select phonenumber,address from empprofile where empid=".$empid);
                $phone_row=$db->fetchAssoc($phone_num);
                $phonenumber=$phone_row['phonenumber'];
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
		$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		$sub="Leave is not approved from ".$reasonsrow['startdate']." to ".$reasonsrow['enddate']."";  //subject of mail
		//body of mail
		$mailBody='
					Employee Name:	<b>'.$emprow['empname'].'</b><br>
					Manager Name:	<b>'.$emprow['managername'].'</b><br>
					Leave Not Approved from '.$reasonsrow['startdate'].' and '.$reasonsrow['enddate'].'<br><br>
					Comments:	'.$reasonsrow['approvalcomments'].'
					<table class="table table-bordered table-striped">
						<caption><h3>Not Approved Leaves</h3></caption>
						<tr align=justify class="info">
						<th>Date</th>
						<th>Leave Type</th>
					</tr>
					<tr>';
		while($daysrow=$db->fetchAssoc($daysquery)) {
		$mailBody=$mailBody. '<td>'.$daysrow['date'].'</td>';
		$mailBody=$mailBody. '<td>'.$daysrow['leavetype'].'</td>';
		$mailBody=$mailBody.'</tr><tr>';
		}
		$mailBody=$mailBody. '</tr></table>';
		$mailBody=$mailBody."You can reach ".$emprow['empname']." by ".$phonenumber;
		sendMail($to,$mailBody,$sub);
	}
	if ($action=='cancelledApprovedLeaves')
	{
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$reasonquery=$db->query("SELECT * FROM `empleavetransactions` WHERE transactionid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
		$phone_num=$db->query("select phonenumber,address from empprofile where empid=".$empid);
                $phone_row=$db->fetchAssoc($phone_num);
                $phonenumber=$phone_row['phonenumber'];
		$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		if(isset($role) || isset($hrid)) {
			$hrname="";
			if(isset($hrid)) {
				$query=$db->query("select empname from emp where empid='".$hrid."' and state='Active'");
				$row=$db->fetchAssoc($query);
				$hrname=$row['empname'];
			}
		}
		$sub="Leave is cancelled from ".$reasonsrow['startdate']." to ".$reasonsrow['enddate']."";  //subject of mail
		//body of mail
		$mailBody='
					Employee Name:	<b>'.$emprow['empname'].'</b><br>';
		if($role=="manager") {
			$mailBody=$mailBody.'Manager Name:	<b>'.$emprow['managername'].'</b><br>';
		}
		if($role=="hr") {
			$mailBody=$mailBody.'HR Name:	<b>'.$hrname.'</b><br>';
		}
		$mailBody=$mailBody.'Leave Cancelled from '.$reasonsrow['startdate'].' and '.$reasonsrow['enddate'].'<br><br>
					Comments:	'.$reasonsrow['approvalcomments'].'
					<table class="table table-bordered table-striped">
						<caption><h3>Cancelled Approved Leaves</h3></caption>
						<tr align=justify class="info">
						<th>Date</th>
						<th>Leave Type</th>
					</tr>
					<tr>';
		while($daysrow=$db->fetchAssoc($daysquery)) {
		$mailBody=$mailBody. '<td>'.$daysrow['date'].'</td>';
		$mailBody=$mailBody. '<td>'.$daysrow['leavetype'].'</td>';
		$mailBody=$mailBody.'</tr><tr>';
		}
		$mailBody=$mailBody. '</tr></table>';
		$mailBody=$mailBody."You can reach ".$emprow['empname']." by ".$phonenumber;
		sendMail($to,$mailBody,$sub);
	}
	if ($action=='modifyApprovedLeaves')
	{
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$reasonquery=$db->query("SELECT * FROM `empleavetransactions` WHERE transactionid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
		$phone_num=$db->query("select phonenumber,address from empprofile where empid=".$empid);
                $phone_row=$db->fetchAssoc($phone_num);
                $phonenumber=$phone_row['phonenumber'];
		$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		if(isset($role) || isset($hrid)) {
			$hrname="";
			if(isset($hrid)) {
				$query=$db->query("select empname from emp where empid='".$hrid."' and state='Active'");
				$row=$db->fetchAssoc($query);
				$hrname=$row['empname'];
			}
		}
		$sub="Approved Leaves are modified from ".$reasonsrow['startdate']." to ".$reasonsrow['enddate']."";  //subject of mail
		//body of mail
		$mailBody='Employee Name:	<b>'.$emprow['empname'].'</b><br>';
		if($role=="manager") {
			$mailBody=$mailBody.'Manager Name:	<b>'.$emprow['managername'].'</b><br>';
		}
		if($role=="hr") {
			$mailBody=$mailBody.'HR Name:	<b>'.$hrname.'</b><br>';
		}	
	    $mailBody=$mailBody.'Approved Leaves are modified from '.$reasonsrow['startdate'].' and '.$reasonsrow['enddate'].'<br><br>
					Comments:	'.$reasonsrow['approvalcomments'].'
					<table class="table table-bordered table-striped">
						<caption><h3>Modified Approved Leaves</h3></caption>
						<tr align=justify class="info">
						<th>Date</th>
						<th>Leave Type</th>
					</tr>
					<tr>';
		while($daysrow=$db->fetchAssoc($daysquery)) {
		$mailBody=$mailBody. '<td>'.$daysrow['date'].'</td>';
		$mailBody=$mailBody. '<td>'.$daysrow['leavetype'].'</td>';
		$mailBody=$mailBody.'</tr><tr>';
		}
		$mailBody=$mailBody. '</tr></table>';
		$mailBody=$mailBody."You can reach ".$emprow['empname']." by ".$phonenumber;
		sendMail($to,$mailBody,$sub);
	}
	
	if ($action=='HRApproveLeave')
	{
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$phone_num=$db->query("select phonenumber,address from empprofile where empid=".$empid);
		$phone_row=$db->fetchAssoc($phone_num);
		$phonenumber=$phone_row['phonenumber'];
		$reasonquery=$db->query("SELECT * FROM `empleavetransactions` WHERE transactionid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		if(isset($role) || isset($hrid)) {
			$hrname="";
			if(isset($hrid)) {
				$query=$db->query("select empname,emp_emailid from emp where empid='".$hrid."' and state='Active'");
				$row=$db->fetchAssoc($query);
				$hrname=$row['empname'];
			}
		}
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
		$hremailid=$row['emp_emailid'];
		$to=$empemail.",".$manageremailid.",".$hremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		
		$sub="Leave is Approved from ".$reasonsrow['startdate']." to ".$reasonsrow['enddate']." by HR";  //subject of mail
		//body of mail
		$mailBody='
					Employee Name:	<b>'.$emprow['empname'].'</b><br>
					HR Name:	<b>'.$hrname.'</b><br>
					Approved Leave from '.$reasonsrow['startdate'].' and '.$reasonsrow['enddate'].'<br><br>
					<table class="table table-bordered table-striped">
						<caption><h3>Modified Approved Leaves</h3></caption>
						<tr align=justify class="info">
						<th>Date</th>
						<th>Leave Type</th>
					</tr>
					<tr>';
		while($daysrow=$db->fetchAssoc($daysquery)) {
		$mailBody=$mailBody. '<td>'.$daysrow['date'].'</td>';
		$mailBody=$mailBody. '<td>'.$daysrow['leavetype'].'</td>';
		$mailBody=$mailBody.'</tr><tr>';
		}
		$mailBody=$mailBody. '</tr></table>';
		$mailBody=$mailBody."You can reach ".$emprow['empname']." by ".$phonenumber; 
		sendMail($to,$mailBody,$sub);
	}
	
	if ($action=='DeleteCompOff')
	{
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$phone_num=$db->query("select phonenumber,address from empprofile where empid=".$empid);
		$phone_row=$db->fetchAssoc($phone_num);
		$phonenumber=$phone_row['phonenumber'];
		$reasonquery=$db->query("SELECT * FROM `empleavetransactions` WHERE transactionid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
		$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		$sub="Compoff Leave is Deleted by Employee from ".$reasonsrow['startdate']." to ".$reasonsrow['enddate']."";  							  //subject of mail
		//body of mail
		$mailBody='
		Employee Name:	<b>'.$emprow['empname'].'</b><br>
		Manager Name:	<b>'.$emprow['managername'].'</b><br>
		Deleted Leaves from '.$reasonsrow['startdate'].' and '.$reasonsrow['enddate'].'<br><br>
			<table class="table table-bordered table-striped">
		<caption><h3>Deleted Leaves</h3></caption>
		<tr align=justify class="info">
		<th>Date</th>
		<th>Leave Type</th>
		<th>Leave Type</th>
		<th>Reason</th>
		</tr>
		<tr>';
		while($daysrow=$db->fetchAssoc($daysquery)) {
			$mailBody=$mailBody. '<td>'.$daysrow['date'].'</td>';
			$mailBody=$mailBody. '<td>'.$daysrow['leavetype'].'</td>';
			$mailBody=$mailBody. '<td>'.$daysrow['shift'].'</td>';
			$mailBody=$mailBody. '<td>'.$daysrow['compoffreason'].'</td>';
		    $mailBody=$mailBody.'</tr><tr>';
		}
		$mailBody=$mailBody. '</tr></table>';
		$mailBody=$mailBody."You can reach ".$emprow['empname']." by ".$phonenumber;
		sendMail($to,$mailBody,$sub);
	}
	
	if ($action=='ApplyCompOff')
	{
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
		$phone_num=$db->query("select phonenumber,address from empprofile where empid=".$empid);
		$phone_row=$db->fetchAssoc($phone_num);
		$phonenumber=$phone_row['phonenumber'];
		$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		$sub="Leave Applied By ".$emprow['empname']." (Compoff Leave)"; 																		  //subject of mail
		//body of mail
		$mailBody='
		Employee Name:	<b>'.$emprow['empname'].'</b><br>
		Manager Name:	<b>'.$emprow['managername'].'</b><br>
	
		<table class="table table-bordered table-hover table-striped">
			<caption><h3>Leave for Approval</h3></caption>
			<tr align=justify class="info">
				<th>Date</th>
				<th>Leave Type</th>
				<th>Leave Type</th>
				<th>Reason</th>
			</tr>';
		for($j=0; $j<count($transactionid); $j++)
		{
			$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid[$j]."'");
			$daysrow=$db->fetchAssoc($daysquery);
			$mailBody=$mailBody.'<tr>';
			$mailBody=$mailBody. '<td>'.$daysrow['date'].'</td>';
			$mailBody=$mailBody. '<td>'.$daysrow['leavetype'].'</td>';
			$mailBody=$mailBody. '<td>'.$daysrow['shift'].'</td>';
			$mailBody=$mailBody. '<td>'.$daysrow['compoffreason'].'</td>';
			$mailBody=$mailBody.'</tr>';
		}
		$mailBody=$mailBody. '</table>';
		$mailBody=$mailBody."You can reach ".$emprow['empname']." by ".$phonenumber;
		sendMail($to,$mailBody,$sub);
	}
	else
	{
		echo "not defined";
	}
	
	if ($action=='PendingWFHhours')
	{
		//echo "testing PendingWFHhours";
		
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		//$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$reasonquery=$db->query("SELECT * FROM `extrawfh` WHERE tid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
		
		//$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		$to=$empemail;  //to address
		
		$sub="Extra work from home hour Applied By ".$emprow['empname']." for ".$reasonsrow['date'].""; //subject of mail
		//body of mail
		$mailBody='
                                        Employee Name:  <b>'.$emprow['empname'].'</b><br>
                                        Manager Name:   <b>'.$emprow['managername'].'</b><br>
                                        Applied Extra WFH for: '.$reasonsrow['date'].'<br><br>
                                        Applied Hours: '.$reasonsrow['wfhHrs'].'<br><br>
                                        Reason: '.$reasonsrow['reason'].'<br><br>
                                        Status: '.$reasonrow['status'].'<br><br>
                                       ';
		
		$mailBody=$mailBody;
		
		sendMail($to,$mailBody,$sub);
		
	}
	if ($action=='editExtraWFH')
	{
		//echo "testing PendingWFHhours";
	
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		//$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$reasonquery=$db->query("SELECT * FROM `extrawfh` WHERE tid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
	
		//$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		$to=$empemail;  //to address
	
		$sub="Extra work from home hour updated By ".$emprow['empname']." for ".$reasonsrow['date'].""; //subject of mail
		//body of mail
		$mailBody='
                   	Employee Name:  <b>'.$emprow['empname'].'</b><br>
                   	Manager Name:   <b>'.$emprow['managername'].'</b><br>
                 	Applied Extra WFH for: '.$reasonsrow['date'].'<br><br>
                    Applied Hours: '.$reasonsrow['wfhHrs'].'<br><br>
                   	Reason: '.$reasonsrow['reason'].'<br><br>
                   	Status: '.$reasonrow['status'].'<br><br>';
		$mailBody=$mailBody;
		sendMail($to,$mailBody,$sub);
	
	}
	if ($action=='deleteExtraWFH')
	{
		//echo "testing PendingWFHhours";
	
		$empquery=$db->query("select * from emp where empid='".$empid."' and state='Active'");
		$emprow=$db->fetchAssoc($empquery);
		//$daysquery=$db->query("SELECT * FROM `perdaytransactions` WHERE transactionid='".$transactionid."'");
		$reasonquery=$db->query("SELECT * FROM `extrawfh` WHERE tid='".$transactionid."'");
		$reasonsrow=$db->fetchAssoc($reasonquery);
		$empemail=$emprow['emp_emailid'];
		$manageremailid=$emprow['manager_emailid'];
	
		//$to=$empemail.",".$manageremailid.",Dhanalakshmi.Shanbhag@ecitele.com,neha.bhardwaj@ecitele.com,sheela.naveen@ecitele.com";                 //to address
		$to=$empemail;  //to address
	
		$sub="Extra work from home hour deleted By ".$emprow['empname']." for ".$reasonsrow['date'].""; //subject of mail
		//body of mail
		$mailBody='
                 	Employee Name:  <b>'.$emprow['empname'].'</b><br>
                   	Manager Name:   <b>'.$emprow['managername'].'</b><br>
                  	Applied Extra WFH for: '.$reasonsrow['date'].'<br><br>
                   	Applied Hours: '.$reasonsrow['wfhHrs'].'<br><br>
                   	Reason: '.$reasonsrow['reason'].'<br><br>
               		Status: '.$reasonrow['status'].'<br><br>';
	
		$mailBody=$mailBody;
		sendMail($to,$mailBody,$sub);
	}
	
}

?>
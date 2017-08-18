<?php
session_start();
require_once '../Library.php';
require_once '../attendenceFunctions.php';
require_once '../generalFunctions.php';
error_reporting("E_ALL");
$db=connectToDB();
if(isset($_REQUEST['addWFHhrForm']))
{
	echo '<html>
		<head>
			<link rel="stylesheet" type="text/css" media="screen" href="css/table.css" />';	
			echo '<script type=""text/javascript"">
			$("document").ready(function(){
					$("#noh").spinner(
                        { min: 1 },
                        { max: 18 },
						{ step: 0.25 }
        			);
					$("#addwfh").submit(function() {
						$("#loadingmessage").show()
		   				$.ajax({
		       	 		data: $(this).serialize(),
		       		 	type: $(this).attr("method"),
		       		 	url: $(this).attr("action"),
		        	 	success: function(response) {
						  $("#loadingmessage").hide()
						  if(response == "success") {
								alert("WFH inserted successfully");
						  } else {
								alert("not successs");
						  }
						  $("#loadextrawfhhr").load("wfhhours/viewwfh.php");
		        		}
					});
					return false; // cancel original event to prevent form submitting
				});
					$("#Extrawfhhours").change(function() {
					date=$("#Extrawfhhours").val();
					eid= $("#empid").val();
		   			$.ajax({
			       	 	 data: { date: date, eid: eid },
			       		 type: "GET",
			       		 url: "wfhhours/addwfh.php?getwfh=1",
			        	 success: function(response) {
							arr=response.split("-");
		        	      	$("#noh").val(arr[0]);
							$("#reason").val(arr[1]);
		        		 }
					});
				});
				$.validator.addClassRules({
					workeddaydynamic: {
					     required: true
						}
				   });	
				$("body").on("click",".workeddaynoh",function() {
					$(this).datepicker ({
						changeMonth: true,
				        changeYear: true,
				        showButtonPanel: true,
				        dateFormat: "yy-mm-dd",
				        yearRange: "-1:+0",
				        maxDate: "+0D",
				        buttonImage: "js/datepicker/datepickerImages/calendar.gif",
						showOn: "both",
						buttonImageOnly: true,	 
		    	});
			});
		});
		</script>
		</head>
		<body>
		<center>
		<form method="post" action="wfhhours/addwfh.php?addwfhSubmit=1" id="addwfh">
		<table id="table-2" width="400" border="0" cellspacing="1" cellpadding="2">
		<tr>
			<td width="140">Employee Name</td>
			<td><input name="emp_name" type="text" id="emp_name" value="'.$_SESSION['u_fullname'].'" readonly required></td>
		</tr>
		<tr>
			<td width="100">Employee Id</td>
			<td><input name="empid" type="text" id="empid" value="'.$_SESSION['u_empid'].'" required></td>
		</tr>
		<tr>
			<td width="100">Date</td>
			<td><input class="workeddaynoh" id="Extrawfhhours"type="text" name="dynamicworked_day" readonly/></td>
		</tr>
		<tr>
			<td width="100">No. of Hrs</td>
			<td><input name="noh" type="text" id="noh" readonly>
			</td>
		</tr>
		<tr>
			<td width="100">Reason</td>
			<td><textarea name="reason" id="reason" required></textarea>
			</td>
		</tr>
		<tr>
			<td width="100"></td>
					<td><input name="submit" type="submit" id="submit" value="Submit">
						<input name="close" type="submit" id="close" value="Close">
			</td>
		</tr>
	</table>
</form>
</center>
<div id="loadingmessage" style="display:none">
	<img align="middle" src="images/loading.gif"/>
</div> 
</body></html>';
}
elseif(isset($_REQUEST['addwfhSubmit'])) {
		$transactionid = generate_transaction_id();
		$empid= isset($_POST['empid']) ? $_POST['empid'] : '';
		$name = isset($_POST['emp_name']) ? $_POST['emp_name'] : '';
		$date= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
		$noh = isset($_POST['noh']) ? $_POST['noh'] : '';
		
		$reason = isset($_POST['reason']) ? $_POST['reason'] : '';
		## ROW EXISTS
		$checkquery= "select * from extrawfh where eid='$empid' and date='$date'";
		$result=$db->query($checkquery);
		if($db->countRows($result) > 0){
			# IF ROW EXISTS, UPDATE QUERY
			$reason=mysql_escape_string($reason);
			$sqlQuery=$db->query("UPDATE `extrawfh` SET wfhHrs='$noh', reason='$reason', status='Pending',comments='' WHERE date='$date' and eid='$empid'");
		}
		else {
			# ELSE , INSERT
			$sqlQuery = $db->query("INSERT INTO extrawfh (createdAt, createdBy, updatedAt, updatedBy, eid, tid, date, wfhHrs, reason, comments, status)
				VALUES (CURTIME(), '$name','', '', '$empid', '$transactionid','$date', '$noh', '$reason', '', 'Pending')");	
		}
		if($sqlQuery){
			$cmd = '/usr/bin/php -f sendmail.php ' . $transactionid . ' ' . $empid . ' PendingWFHhours >> /dev/null &';
			exec($cmd);
			echo "success";
		} else {
			echo "unsuccessfull";
		}
} elseif(isset($_REQUEST['getwfh'])) {
	$empid=$_REQUEST['eid'];
	$date=$_REQUEST['dynamicworked_day'];
	#query to check row exists
	$getquery= "select * from extrawfh where eid='$empid' and date='$date'";
	$result=$db->query($getquery);
	if($db->countRows($result) > 0){
		# if exists, auto fill number of hours and reason
		$row= $db->fetchAssoc($result);
		//$noh=$row['wfhHrs'];
	//convert hh:mm:ss into hours
		//$t = explode(':', $noh);
		//$wfhhours= $t[0] + $t[1]/60 + $t[2]/3600;
		echo $row['wfhHrs']."-".$row['reason'];
	}
}
?>
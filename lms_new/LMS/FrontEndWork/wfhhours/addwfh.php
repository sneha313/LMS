<?php
	session_start();
	require_once '../Library.php';
	require_once '../attendenceFunctions.php';
	require_once '../generalFunctions.php';
	error_reporting("E_ALL");
	$db=connectToDB();
	//add extra WFH hour form
	if(isset($_REQUEST['addWFHhrForm']))
	{
		echo '<html>
			<body>
				<form method="post" action="wfhhours/addwfh.php?addwfhSubmit=1" id="addwfh">
					<div class="panel panel-primary">
						<div class="panel-heading text-center">
							<strong style="font-size:20px;">Add Extra WFH Hours</strong>
						</div>
						<div class="panel-body">
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Employee Name</label>
								</div>
								<div class="col-sm-4">
									<input name="emp_name" type="text" class="form-control" id="emp_name" value="'.$_SESSION['u_fullname'].'" readonly required>
								</div>
							</div>
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Employee Id</label>
								</div>
								<div class="col-sm-4">
									<input name="empid" type="text" class="form-control" id="empid" value="'.$_SESSION['u_empid'].'" required>
								</div>
							</div>
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">	
									<label>Date</label>
								</div>
								<div class="col-sm-4">		
									<div class="input-group">
										<input type="text" id="Extrawfhhours" class="form-control open-datetimepicker" name="dynamicworked_day" readonly />
											<label class="input-group-addon btn" for="date">
												<span class="fa fa-calendar open-datetimepicker"></span>
											</label>
									</div>
								</div>
							</div>
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">	
									<label>No. of Hrs</label>
								</div>
								<div class="col-sm-4">
									<input name="noh" type="text" class="form-control" id="noh" readonly>
								</div>
							</div>
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">	
									<label>Reason</label>
								</div>
								<div class="col-sm-4">
									<textarea name="reason" class="form-control" id="reason" required></textarea>
								</div>
							</div>
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-12 text-center">
									<input name="submit" type="submit" class="btn btn-success" id="submit" value="Submit">
									<input name="close" type="submit" class="btn btn-danger" id="close" value="Close">
								</div>
							</div>
							</div>
						</div>
					</div>
				</form>
				<div id="loadingmessage" style="display:none">
					<img align="middle" src="images/loading.gif"/>
				</div> 
				<script>
					$(document).ready(function(){
						$(".open-datetimepicker").datetimepicker({
							changeMonth: true,
							changeYear: true,
							showButtonPanel: true,
							dateFormat: "yy-mm-dd",
							yearRange: "-1:+0",
							maxDate: "+0D",
							showOn: "both",
							buttonImageOnly: true,
						});
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
										BootstrapDialog.alert("WFH inserted successfully");
									} else {
										BootstrapDialog.alert("not successs");
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
					});
				</script>	
			</body>
		</html>';
	}
	
	//to get value of all input field from add extra WFH hour form
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
	}
	
	//if employee applied extra WFH hour and want to edit 
	elseif(isset($_REQUEST['getwfh'])) {
		$empid=$_REQUEST['eid'];
		$date=$_REQUEST['dynamicworked_day'];
		#query to check row exists
		$getquery= "select * from extrawfh where eid='$empid' and date='$date'";
		$result=$db->query($getquery);
		if($db->countRows($result) > 0){
			# if exists, auto fill number of hours and reason
			$row= $db->fetchAssoc($result);
			echo $row['wfhHrs']."-".$row['reason'];
		}
	}
?>
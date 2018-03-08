<?php
	session_start();
	require_once '../librarycopy1.php';
	require_once '../generalcopy.php';
	require_once '../attendenceFunctions.php';
	error_reporting("E_ALL");
	$db=connectToDB();
	//add extra WFH hour form
	if(isset($_REQUEST['addWFHhrForm']))
	{
		echo '<html>
				<head>
					<script>
					$(document).ready(function(){
						$("#noh").spinner(
							{ min: 1 },
							{ max: 18 },
							{ step: 0.25 }
						);
						$("#addwfh").submit(function() {
							$(this).find(":input[type=submit]").replaceWith("<center><img src=\'public/img/loader.gif\' class=\'img-responsive\' alt=\'processing\'/></center>");
				
							$.ajax({
								data: $(this).serialize(),
								type: $(this).attr("method"),
								url: $(this).attr("action"),
								success: function(response) {
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
						$("body").on("click",".workeddaynoh",function() {
							$(this).datepicker ({
								changeMonth: true,
						        changeYear: true,
						        showButtonPanel: true,
						        dateFormat: "yy-mm-dd",
						        yearRange: "-1:+0",
						        maxDate: "+0D",
								showOn: "both",
								buttonImageOnly: true,	 
		    				});
						});	
					});
				</script>	
				</head>
			<body>
				<form method="post" action="wfhhours/addwfh.php?addwfhSubmit=1" id="addwfh">
					<div class="panel panel-primary">
						<div class="panel-heading text-center">
							<strong style="font-size:20px;">Add Extra WFH Hours</strong>
						</div>
						<div class="panel-body">
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2"></div>
								<div class="col-sm-3">
									<label>Employee Name</label>
								</div>
								<div class="col-sm-5">
									<input name="emp_name" type="text" class="form-control" id="emp_name" value="'.$_SESSION['u_fullname'].'" readonly required>
								</div>
								<div class="col-sm-2"></div>
							</div>
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2"></div>
								<div class="col-sm-3">
									<label>Employee Id</label>
								</div>
								<div class="col-sm-5">
									<input name="empid" type="text" class="form-control" id="empid" value="'.$_SESSION['u_empid'].'" required>
								</div>
								<div class="col-sm-2"></div>
							</div>
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2"></div>
								<div class="col-sm-3">	
									<label>Date</label>
								</div>
								<div class="col-sm-5">		
									<div class="input-group">
										<input type="text" id="Extrawfhhours" class="form-control workeddaynoh" name="dynamicworked_day" readonly />
											<label class="input-group-addon btn" for="date">
												<span class="fa fa-calendar"></span>
											</label>
									</div>
								</div>
								<div class="col-sm-2"></div>
							</div>
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2"></div>
								<div class="col-sm-3">	
									<label>No. of Hrs</label>
								</div>
								<div class="col-sm-5">
									<input name="noh" type="text" class="form-control" id="noh" readonly>
								</div>
								<div class="col-sm-2"></div>
							</div>
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2"></div>
								<div class="col-sm-3">	
									<label>Reason</label>
								</div>
								<div class="col-sm-5">
									<textarea name="reason" class="form-control" id="reason" required></textarea>
								</div>
								<div class="col-sm-2"></div>
							</div>
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-12 text-center">
									<input name="submit" type="submit" class="btn btn-success" id="submit" value="Submit">
									<input name="close" type="reset" class="btn btn-danger" id="close" value="Reset">
								</div>
							</div>
							</div>
						</div>
					</div>
				</form>
				
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
		$result=$db->pdoQuery($checkquery);
		$rowcount=$result -> count($sTable = 'extrawfh', $sWhere = 'eid = "'.$empid.'" and date = "'.$date.'"' );
				
		if($rowcount > 0){
			# IF ROW EXISTS, UPDATE QUERY
			$reason=mysql_escape_string($reason);

			$dataArray = array('wfhHrs'=>$noh,'reason'=>$reason,'status'=>'Pending','comments'=>'');
			// two where condition array
			$aWhere = array('date'=>$date, 'eid'=>$empid);
			// call update function
			$sqlQuery = $db->update('extrawfh', $dataArray, $aWhere)->affectedRows();
				}
		else {
				$time='CURTIME()';
			$dataArray = array('createdAt'=>$time,'createdBy'=>$name,'updatedAt'=>'','updatedBy'=>'','eid'=>$empid,'tid'=>$transactionid,'date'=>$date,'wfhHrs'=>$noh,'reason'=>$reason,'comments'=>'','status'=>'Pending');
			// use insert function
			$sqlQuery = $db->insert('extrawfh',$dataArray)->getLastInsertId();
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
		$result=$db->pdoQuery($getquery);
		$rowcount=$result -> count($sTable = 'extrawfh', $sWhere = 'eid = "'.$empid.'" and date = "'.$date.'"' );
				
		if($rowcount > 0){
			# if exists, auto fill number of hours and reason
			$rows= $db->pdoQuery($getquery)->results();
			foreach($rows as $row)
			echo $row['wfhHrs']."-".$row['reason'];
		}
	}
?>
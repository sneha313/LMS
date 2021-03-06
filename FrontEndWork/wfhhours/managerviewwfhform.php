<?php
	session_start();
	require_once '../librarycopy1.php';
	require_once '../generalcopy.php';
	error_reporting("E_ALL");
	$db=connectToDB();

	echo '<html>
			<head>
				<script type="text/javascript">
				$("document").ready(function(){
					$("#wfhHrs").spinner(
					   { min: 1 },
					   { max: 18 },
					   { step: 0.25 }
					);
					$( "#tabs" ).tabs();
			
					$("#editbymanager").submit(function() {
						$.ajax({
							data: $(this).serialize(),
							type: $(this).attr("method"),
							url: $(this).attr("action"),
							success: function(response) {
								if(response.match(/success/)) {
									BootstrapDialog.alert("WFH Edited Successfully!");
									var eid=$("#empid").val();
									var date = $(".open-datetimepicker").val();
									$("#loadmanagersection").html(response);
									hidealldiv("loadmanagersection");
									$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&eid="+eid+"&date="+date);
								} else {
									BootstrapDialog.alert("not successs");
								}
							}
						});
						return false; // cancel original event to prevent form submitting
					});
			
					$("#deletebymanager").submit(function() {
						$.ajax({  
							data: $(this).serialize(),
							type: $(this).attr("method"),
							url: $(this).attr("action"),
							success: function(response) {
								var r=BootstrapDialog.confirm("Delete Leave!");
								var eid=$("#empid").val();
								var date = $(".open-datetimepicker").val();
								if (r==true)
								{
									var dellink=$("#deleteFormbymanager").attr("href");
									$("#loadmanagersection").html(response);
									hidealldiv("loadmanagersection");
									$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&&eid="+eid+"&date="+date);
								}
								else
								{
									BootstrapDialog.alert("You pressed Cancel!");
									$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&delcancel=1&eid="+eid+"&date="+date);
								}
							} 
						});
						return false; // cancel original event to prevent form submitting
					});

					$("#viewrecordbymanager").submit(function() {
						if($("#empuser").val()=="")
						{
							BootstrapDialog.alert("Please Enter Employee Name");
							return false;
						}
						$.ajax({
							data: $(this).serialize(),
							type: $(this).attr("method"),
							url: $(this).attr("action"),
							success: function(response) {
								var eid=$("#empid").val();
								var date=$(".open-datetimepicker").val();
								$("#loadmanagersection").html(response);
								$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&eid="+eid+"&date="+date);
							}
						});
						return false; // cancel original event to prevent form submitting
					});
			
					$("#viewEmpWFHbymanager").submit(function() {
						if($("#empuser").val()=="")
						{
							BootstrapDialog.alert("Please Enter Employee Name");
							return false;
						}
						$.ajax({
							data: $(this).serialize(),
							type: $(this).attr("method"),
							url: $(this).attr("action"),
							success: function(response) {
								hidealldiv("loadmanagersection");
								$("#loadmanagersection").html(response);
							}
						});
						return false; // cancel original event to prevent form submitting
					});
					jQuery(function() {
						jQuery("#empuser").autocomplete({
							minLength: 1,
							source: function(request, response) {
								jQuery.getJSON("../autocomplete/Users_JSON.php", {
									term: request.term
								}, response)
							},
							focus: function() {
								// prevent value inserted on focus
								return false;
							},
							select: function(event, ui) {
								this.value = ui.item.value;
								return false;
							}
						});
					});
				});
				
				function editExtrawfh(tid) {
					hidealldiv("loadmanagersection");
					$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?editExtrawfh=1&tid="+tid);
				}
				function deleteExtrawfh(tid) {
				
					hidealldiv("loadmanagersection");
					$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?deleteExtrahour=1&tid="+tid);
				}
			</script>
		</head>
		<body>
			<div class="col-sm-12">';
				if(isset($_REQUEST['deleteFormbymanager'])){
					$tmpdate= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
					$date=$tmpdate[0];	
					$noh = isset($_POST['wfhHrs']) ? $_POST['wfhHrs'] : '';
					$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
					$updatedAt = date('Y-m-d H:i:s');
					//$queryDel="UPDATE extrawfh set status='Deleted' WHERE `tid`= '$tid'";
					//$sql2=$db->query($queryDel);

					$dataArray = array('status'=>'Deleted');
					// where condition array
					$aWhere = array('tid'=>$tid);
					// call update function
					$sql2 = $db->update('extrawfh', $dataArray, $aWhere)->affectedRows();
					if($sql2){
						//send mail that record is deleted
						$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$eid.'  deleteExtraWFH >> /dev/null &';
						exec($cmd);
						echo "deleted";
					} else {
						echo "<center><h3>Record not deleted</h3></center>";
					}
				}
				if(isset($_REQUEST['deleteExtrahour'])){
					//delete form here employee can delete extra work from home hour and date
					$tid=$_REQUEST['tid'];
					## query database if row exists
					$tquery="select wfhHrs, date,eid from extrawfh where `tid`='$tid'";
					$tresults=$db->pdoQuery($tquery)->results();
					//$tresult=$db->fetchArray($tresult);
					foreach($tresults as $tresult)
					## if exists, get number of hrs and date
					$noh=$tresult['wfhHrs'];
					$date=$tresult['date'];
					$empid=$tresult['eid'];
				?>
				<form method="POST" action="wfhhours/managerviewwfhform.php?deleteFormbymanager=1" id="deletebymanager" name="deletebymanager">
					<div class='panel panel-primary'>
						<div class='panel-heading text-center'>
							<strong style='font-size:20px;'>Delete Extra WFH Hour</strong>
						</div>
						<div class='panel-body'>	
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2"></div>
								<div class="col-sm-3">
									<label>Employee Id</label>
								</div>
								<div class="col-sm-5">
									<input type="text" class="form-control" name="empid" id="empid" value="<?php echo $empid; ?>" readonly>
								</div>
								<div class="col-sm-2"></div>
							</div>
							</div>
							
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2"></div>
								<div class="col-sm-3">
									<label>Number of Hour</label>
								</div>
								<div class="col-sm-5">
									<input type="text" class="form-control" name="wfhHrs" id="wfhHrs" value="<?php echo $noh;?>" >
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
										<input type="text" id="datetimepicker" class="workeddaydynamic form-control open-datetimepicker" name="dynamicworked_day" value="<?php echo $date;?>" readonly />
										<label class="input-group-addon btn" for="date">
											<span class="fa fa-calendar open-datetimepicker"></span>
										</label>
									</div>
								</div>
								<div class="col-sm-2"></div>
							</div>
							</div>
							
							<div class="form-group">
							<div class="row">
								<div class="col-sm-12 text-center">
									<input type="submit" class="btn btn-danger" id="delete" name="delete" value="delete">
									<input type="submit" id="delcancel" class="btn btn-success" name="delcancel" value="cancel">
								</div>
							</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-sm-12"><input type="hidden" id="tid" name="tid" value="<?= $tid ?>" ></div>
								</div>
							</div>
						</div><!-- panel body div close -->
					</div><!-- panel div close -->
				</form><!-- form close -->
				<?php 
				} 
				#edit extra WFH hour form by manager
				if(isset($_REQUEST['editFormbymanager'])){
					$date= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
					$noh = isset($_POST['wfhHrs']) ? $_POST['wfhHrs'] : '';
					$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
					$updatedAt = date('Y-m-d H:i:s');
					//$queryEdit="UPDATE extrawfh SET `wfhHrs`='$noh', `date`='$date', `updatedAt`='$updatedAt', `updatedBy`='".$_SESSION['user_name']."'  WHERE `tid`='$tid'";
					//$sql3=$db->query($queryEdit);

					$dataArray = array('wfhHrs'=>$noh,'date'=>$date,'updatedAt'=>$updatedAt,'updatedBy'=>$_SESSION['user_name']);
					// where condition array
					$aWhere = array('tid'=>$tid);
					// call update function
					$sql3 = $db->update('extrawfh', $dataArray, $aWhere)->affectedRows();
					if($sql3){
						//send mail that record is updated 
						$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$eid.'  editExtraWFH >> /dev/null &';
						exec($cmd);
						echo "success";
					} else {
						echo "<center><h3>Record not updated</h3></center>";
					}
				} 
				
				#edit form, here employee can edit extra work from home hour and date
				if(isset($_REQUEST['editExtrawfh'])){
					$tid=$_REQUEST['tid'];
					# query database if row exists
					$tquery="select wfhHrs, date,eid from extrawfh where `tid`='$tid'";
					$tresults=$db->pdoQuery($tquery)->results();
					//$tresult=$db->fetchAssoc($tresult);
					foreach($tresults as $tresult)
					# if exists, get number of hrs and date
					$noh=$tresult['wfhHrs'];
					$date=$tresult['date'];
					$empid=$tresult['eid'];
					?>
					<form method="POST" action="wfhhours/managerviewwfhform.php?editFormbymanager=1" id="editbymanager" name="editbymanager">
						<div class='panel panel-primary'>
							<div class='panel-heading text-center'>
								<strong style='font-size:20px;'>Edit Extra WFH Hour</strong>
							</div>
							<div class='panel-body'>
								<div class="form-group">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-3">
										<label>Employee Id</label>
									</div>
									<div class="col-sm-5">
										<input type="text" class="form-control" name="empid" id="empid" value="<?php echo $empid; ?>" readonly>
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
											<input type="text" id="datetimepicker" class="workeddaydynamic form-control open-datetimepicker" name="dynamicworked_day"  value="<?php echo $date;?>" readonly />
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
										<label>Number of Hour</label>
									</div>
									<div class="col-sm-5">
										<input type="text" class="form-control" name="wfhHrs" id="wfhHrs" value="<?php echo $noh;?>" >
									</div>
									<div class="col-sm-2"></div>
								</div>
								</div>
								
								<div class="form-group">
								<div class="row">
									<div class="col-sm-12 text-center">
										<input type="submit" id="cancel" class="btn btn-danger" name="cancel" value="cancel">
										<input type="submit" id="submit" class="btn btn-success" name="submit" value="Edit">
									</div>
								</div>
								</div>
								
								<div class="form-group">
								<div class="row">
									<div class="col-sm-12">
										<input type="hidden" id="tid" name="tid" value="<?= $tid ?>" >
									</div>
								</div>
								</div>
							</div><!-- panel body div close -->
						</div><!-- panel div close -->
					</form><!-- form close -->
					<script>
						$(document).ready(function(){
							$(".open-datetimepicker").datepicker({
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
					</script>
					<?php 
					} 
					#view extra WFH hour record by manager
					if(isset($_REQUEST['viewrecordbymanager'])) 
					{ 
						if (isset($_REQUEST['displayAll'])) {
							$empQuery="select empid,empname from emp where empname='".$_REQUEST['empuser']."' and state='Active'";
							$empnametresult=$db->pdoQuery($empQuery)->results();
							//$empnamerow=$db->fetchAssoc($empnametresult);
							foreach($empnametresult as $empnamerow)
							$empid=$empnamerow['empid'];
							//show record based on employee id where status is not equal to deleted
							$query="select * from extrawfh where status!='Deleted' and eid='".$empid."' order by date desc";
						} else {
							$date=$_REQUEST['date'];
							$empid=$_REQUEST['eid'];
							$empQuery="select empid,empname from emp where empid='".$empid."' and state='Active'";
							$empnametresult=$db->pdoQuery($empQuery)->results();
							foreach($empnametresult as $empnamerow)
							//$empnamerow=$db->fetchAssoc($empnametresult);
							$query="select * from extrawfh where status!='Deleted' and eid='".$empid."' order by date desc";
						}
						$query1="SELECT DISTINCT YEAR(date) as year FROM extrawfh where eid='".$empid."' order by year desc";
						$sql=$db->pdoQuery($query1);
						$rows=$db->pdoQuery($query1)->results();
						$leaveCount=$sql -> count($sTable = 'extrawfh', $sWhere = 'eid = "'.$empid.'"' );
						
						$distinctYears=array();
						//$leaveCount=$db->countRows($sql);
						echo '<div class="panel panel-primary">
								<div class="panel-heading text-center">
									<strong style="font-size:20px;">View Extra WFH Details</strong>
								</div>
								<div class="panel-body">';
						if($leaveCount == 0) {
							echo "<div id='tabs'><ul><div id='Info'><tr><td>No Data Available</td></tr></div></ul></div>";
						} else {
							echo '<div id="tabs">
									<ul>';
						}
						foreach($rows as $row)
						//for($i=0;$i<$db->countRows($sql);$i++)
						{
							//$row=$db->fetchArray($sql);
							echo "<li><a href='#".$row['year']."'>".$row['year']."</a></li>";
							array_push($distinctYears,$row['year']);
						}
						echo "</ul>";
						
						foreach ($distinctYears as $year) {
							echo "<div id='".$year."'>";
								echo "<div id='showtable'>
									<table class='table table-hover table-bordered'>
										<form method='POST' action='' id='WFH' name='ExtraWFHHour'>
											<tr class='info'>
												<th>Emp Name</th>
												<th>Date</th>
												<th>WFH Hours</th>
												<th>Reason</th>
												<th>Approval Status</th>
												<th colspan=2>Actions</th>
											</tr>";
											$sql1=$db->pdoQuery($query)->results();
											foreach($sql1 as $getDetailedrow){
											//while($getDetailedrow=$db->fetchassoc($sql1)) {
												echo  '<tr>
														<td>'.$empnamerow['empname'].'</td>
														<td>'.$getDetailedrow['date'].'</td>
														<td>'.$getDetailedrow['wfhHrs'].'</td>
														<td>'.$getDetailedrow['reason'].'</td>
														<td>'.$getDetailedrow['status'].'</td>
														<td>
															<button  id="modify" title="'.$getDetailedrow['tid'].'" onclick=editExtrawfh("'.$getDetailedrow['tid'].'") class="btn btn-success '.$getDetailedrow['eid'].'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
															<button id="delete" title="'.$getDetailedrow['tid'].'" onclick=deleteExtrawfh("'.$getDetailedrow['tid'].'") class="btn btn-danger '.$getDetailedrow['eid'].'"><i class="fa fa-trash" aria-hidden="true"></i></button>
														</td>
													</tr>';
											}
										echo "</form>
									</table>
								</div>
							</div>";
						}
						echo "</div>
							</div>";
					}
					
					if(isset($_REQUEST['viewEmpWFHbymanager']))
					{
						$emp=isset($_POST['empuser']) ? $_POST['empuser'] : '';
						$empquery="select empid,empname from emp where empname='".$emp."' and state='Active'";
						$empnametresult=$db->pdoQuery($empquery)->results();
						foreach($empnametresult as $empnamerow)
						//$empnamerow=$db->fetchAssoc($empnametresult);
						$childern=getChildren($_SESSION['u_empid']);
						if((in_array($empnamerow['empid'],$childern) && (strtoupper($_SESSION['user_desgn'])=="MANAGER")) || strtoupper($_SESSION['user_dept'])=="HR") {
					
						if (isset($_REQUEST['displayAll'])) {
							$empQuery="select empid,empname from emp where empname='".$_REQUEST['empuser']."' and state='Active'";
							$empnametresult=$db->pdoQuery($empQuery)->results();
							foreach($empnametresult as $empnamerow)
							//$empnamerow=$db->fetchAssoc($empnametresult);
							$empid=$empnamerow['empid'];
							//show record based on employee id where status is not equal to deleted
							$query="select * from extrawfh where status!='Deleted' and eid='".$empid."' order by date desc";
						} else {
							$date=$_REQUEST['date'];
							$empid=$_REQUEST['eid'];
							$empQuery="select empid,empname from emp where empid='".$empid."' and state='Active'";
							$empnametresult=$db->pdoQuery($empQuery)->results();
							foreach($empnametresult as $empnamerow)
							//$empnamerow=$db->fetchAssoc($empnametresult);
							$query="select * from extrawfh where status!='Deleted' and eid='".$empid."' order by date desc";
						}
						$query1="SELECT DISTINCT YEAR(date) as year FROM extrawfh where eid='".$empid."' order by year desc";
						$sql=$db->pdoQuery($query1);
						$rows=$db->pdoQuery($query1)->results();
						$distinctYears=array();
						$leaveCount=$sql -> count($sTable = 'extrawfh', $sWhere = 'eid = "'.$empid.'"' );
						
						//$leaveCount=$db->countRows($sql);
						echo '<div class="panel panel-primary">
								<div class="panel-heading text-center">
									<strong style="font-size:20px;">View Extra WFH Details</strong>
								</div>
								<div class="panel-body">';
						if($leaveCount == 0) {
							echo "<div id='tabs'><ul><div id='Info'><tr><td>No Data Available</td></tr></div></ul></div>";
						} else {
							echo '<div id="tabs">
									<ul>';
						}
						//for($i=0;$i<$db->countRows($sql);$i++)
							foreach($rows as $row)
						{
						//$row=$db->fetchAssoc($sql);
						echo "<li><a href='#".$row['year']."'>".$row['year']."</a></li>";
								array_push($distinctYears,$row['year']);
						}
						echo "</ul>";
					
						foreach ($distinctYears as $year) {
						echo "<div id='".$year."'>";
						echo "<div id='showtable'>
						<table class='table table-hover'>
							<form method='POST' action='' id='WFH' name='ExtraWFHHour'>
								<tr class='info'>
									<th>Emp Name</th>
									<th>Date</th>
									<th>WFH Hours</th>
									<th>Reason</th>
									<th>Approval Status</th>
									<th colspan=2>Actions</th>
								</tr>";
								$sql1=$db->pdoQuery($query)->results();
								foreach($sql1 as $getDetailedrow){
								//while($getDetailedrow=$db->fetchassoc($sql1)) {
									echo  '<tr>
										<td>'.$empnamerow['empname'].'</td>
										<td>'.$getDetailedrow['date'].'</td>
										<td>'.$getDetailedrow['wfhHrs'].'</td>
										<td>'.$getDetailedrow['reason'].'</td>
										<td>'.$getDetailedrow['status'].'</td>
										<td>
											<button  id="modify" title="'.$getDetailedrow['tid'].'" onclick=editExtrawfh("'.$getDetailedrow['tid'].'") class="btn btn-success '.$getDetailedrow['eid'].'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
											<button id="delete" title="'.$getDetailedrow['tid'].'" onclick=deleteExtrawfh("'.$getDetailedrow['tid'].'") class="btn btn-danger '.$getDetailedrow['eid'].'"><i class="fa fa-trash" aria-hidden="true"></i></button>
										</td>
									</tr>';
								}
							echo "</form>
						</table>
					</div>
					</div>";
				}
				echo "</div>
					</div>";
				}
				else {
					echo "<script>BootstrapDialog.alert(\"You dont have permissions to view/modify Extra WFH Hour for '".$_REQUEST['empuser']."'\");
							$('#loadmanagersection').load('wfhhours/managerviewwfhform.php?viewform=1');
						</script>";
					}
				}
					if(isset($_REQUEST['viewform'])){
						echo '<form action="wfhhours/managerviewwfhform.php?viewEmpWFHbymanager=1&displayAll=1" method="POST" id="viewEmpWFHbymanager">
								<div class="row"> 
									<div class="col-sm-1"></div>
									<div class="col-sm-3">
										<label style="font-size:16px;">Enter Employee Name:</label>
									</div>
									<div class="col-sm-4">
										<input id="empuser" type="text" class="form-control" name="empuser"/>
									</div>
									<div class="col-sm-3">
										<input class="submit btn btn-primary" type="submit" name="submit" value="SUBMIT"/>
									</div>
									<div class="col-sm-1"></div>
								</div> 
							</form>';
					}
				echo '</div>
			</div>
		</body>
	</html>';	
?>
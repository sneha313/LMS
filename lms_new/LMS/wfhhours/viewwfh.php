<?php
	session_start();
	require_once '../Library.php';
	require_once '../attendenceFunctions.php';
	error_reporting("E_ALL");
	$db=connectToDB();
?>
<?php
	echo '<html>
		<head>
			<link rel="stylesheet" type="text/css" media="screen" href="public/css/selfleavehistory.css" />
			<script type="text/javascript">  
				$("document").ready(function(){
					$("#wfhHrs").spinner(
						{ min: 1 },
						{ max: 18 },
						{ step:0.25 }
					);
					$( "#tabs" ).tabs();
					$("#editwfhform").submit(function() {
						$(this).find(":input[type=submit]").replaceWith("<center><img src=\'public/img/loader.gif\' class=\'img-responsive\' alt=\'processing\'/></center>");
				
						$.ajax({
							data: $(this).serialize(),
							type: $(this).attr("method"),
							url: $(this).attr("action"),
							success: function(response) {
								BootstrapDialog.alert("Extra WFH Edited Successfully!");
								$("#loadextrawfhhr").html(response);
								hidealldiv("loadextrawfhhr");
								$("#loadextrawfhhr").load("wfhhours/viewwfh.php");
							}
						});
						return false; // cancel original event to prevent form submitting
					});
					$("#delete").click(function(){
						BootstrapDialog.confirm("Delete Leave!", function(result){
							if (result)
							{
								var dellink=$("#delete").attr("href");
								$("#loadextrawfhhr").load("wfhhours/viewwfh.php");
								
							}
							else
							{
								BootstrapDialog.alert("You pressed Cancel!");
								$("#loadextrawfhhr").load("wfhhours/viewwfh.php");
							}
						});
					});
				});
				function hidealldiv(div) {
					var myCars = new Array("loadviewwfhhrcontent","loadempapplyleave", "loadempleavestatus", "loadempleavehistory", "loadempleavereport", "loadempeditprofile", "loadholidays", "loadempleavereport", "loadteamleavereport", "loadteamleaveapproval", "loadattendance", "loadcalender", "loadpendingstatus", "loadhrsection", "loadmanagersection", "loadapplyteammemberleave", "loadtrackattendance", "loadwfhhr");
					var hidedivarr = removeByValue(myCars, div);
					hidediv(hidedivarr);
					showdiv(div);
				}

				function hidediv(arr) {
					$("#footer").show();
					for (var i = 0; i < arr.length; i++) {
						$("#" + arr[i]).hide();
						$("#" + arr[i]).html("");
					}
				}

				function showdiv(div) {
					$("#" + div).show();
				}

				function removeByIndex(arr, index) {
					arr.splice(index, 1);
				}

				function removeByValue(arr, val) {
					for (var i = 0; i < arr.length; i++) {
						if (arr[i] == val) {
							arr.splice(i, 1);
							break;
						}
					}
					return arr;
				}
					
				function editwfh(tid) {
					hidealldiv("loadextrawfhhr");
					$("#loadextrawfhhr").load("wfhhours/viewwfh.php?editwfh=1&tid="+tid);
				}
				function deletewfh(tid) {
					hidealldiv("loadextrawfhhr");
					$("#loadextrawfhhr").load("wfhhours/viewwfh.php?delete=1&tid="+tid);
				}
			</script>
		</head>
		<body>';
			//delete extra work from home hour request
			if(isset($_REQUEST['delete'])){
				$tid=$_GET['tid'];
				$eid=$_SESSION['u_empid'];
				$queryDel="UPDATE extrawfh set status='Deleted' WHERE `tid`= '$tid'";
				$sql2=$db->query($queryDel);
				if($sql2){
					//send mail that record is deleted
					$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$eid.'  deleteExtraWFH >> /dev/null &';
					exec($cmd);
					echo "success";
				} else {
					echo "<center><h3>Record not deleted</h3></center>";
				}
			} 
			
			//edit extra work from home hour request
			elseif(isset($_REQUEST['editForm'])){
				$eid = isset($_POST['eid']) ? $_POST['eid'] : '';
				$date= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
				$noh = isset($_POST['wfhHrs']) ? $_POST['wfhHrs'] : '';
				$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
				$updatedAt = date('Y-m-d H:i:s');
				$queryEdit="UPDATE extrawfh SET `wfhHrs`='$noh', `date`='$date', `updatedAt`='$updatedAt', `updatedBy`='".$_SESSION['user_name']."'  WHERE `eid`='$eid' and `tid`='$tid'";
				$sql3=$db->query($queryEdit);
				if($sql3){
					//send mail that record is updated 
					$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$eid.'  editExtraWFH >> /dev/null &';
					exec($cmd);
				} else {
					echo "<center><h3>Record not updated</h3></center>";
				}
			}
			
			//edit form here employee can edit extra work from home hour and date
			elseif(isset($_REQUEST['editwfh'])){
				$tid=$_REQUEST['tid'];
				$eid=$_SESSION['u_empid'];
				## query database if row exists
				$tquery="select wfhHrs, date from extrawfh where `tid`='$tid'";
				$tresult=mysql_query($tquery);
				$tresult=mysql_fetch_array($tresult);
				## if exists, get number of hrs and date
				$noh=$tresult['wfhHrs'];
				$date=$tresult['date'];
			?>
	
			<form method="POST" action="wfhhours/viewwfh.php?editForm=1" id="editwfhform" name="editwfhform">
				<div class="panel panel-primary">
					<div class="panel-heading text-center">
						<strong style="font-size:20px;">Edit Extra WFH Hour</strong>
					</div>
					<div class="panel-body">
						<div class="form-group">
						<div class="row">
							<div class="col-sm-2"></div>
							<div class="col-sm-3">
								<label>Number of Hour</label>
							</div>
							<div class="col-sm-5">
								<input type="text" name="wfhHrs" id="wfhHrs" value="<?php echo $noh;?>" readonly>
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
							<div class="col-sm-3">		
								<div class="input-group">
									<input type="text" class="form-control open-datetimepicker" name="dynamicworked_day" value="<?php echo $date; ?>" readonly/>
										<label class="input-group-addon btn" for="date">
											<span class="fa fa-calendar"></span>
										</label>
								</div>
							</div>
							<div class="col-sm-4"></div>
						</div>
						</div>
						
						<div class="form-group">
						<div class="row">
							<div class="col-sm-12 text-center">
								<input type="submit" class="btn btn-info" id="submit" name="Edit WFH" value="Edit WFH">
								<input type="reset" class="btn btn-danger" id="cancel" name="cancel" value="Reset">
							</div>
						</div>
						</div>
						
						<div class="form-group">
						<div class="row">
							<div class="col-sm-12">
								<input type="hidden" name="eid" value="<?= $eid ?>" >
							</div>
						</div>
						</div>
						
						<div class="form-group">
						<div class="row">
							<div class="col-sm-12">
								<input type="hidden" name="tid" value="<?= $tid ?>" >
							</div>
						</div>
						</div>
					</div>
				</div>
			</form>
			<?php 
			} 
			else {
				echo '
				<div class="panel panel-primary">
					<div class="panel-heading text-center">
						<strong style="font-size:20px;">View Extra WFH Details</strong>
					</div>
					<div class="panel-body">';
					$distinctYears=array("2017");
					echo '<div id="tabs">
								<ul>';
					foreach ($distinctYears as $year) {
						echo "<li><a href='#".$year."'>".$year."</a></li>";
					}
					echo "</ul></div>";
		
					foreach ($distinctYears as $year) {
						echo "<div id='".$year."'>";
		
						echo "<div id='showtable'>
							<table class='table table-hover table-bordered'>
								<form method='POST' action='' id='WFH' name='ExtraWFHHour'>
									<tr class='info'>
										<th>Date</th>
										<th>WFH Hours</th>
										<th>Reason</th>
										<th>Comments</th>
										<th>Status</th>
										<th>Action</th>
									</tr>";
						$sql1=$db->query("select * from extrawfh where eid='".$_SESSION['u_empid']."' order by date");
						for($i=0;$i<$db->countRows($sql1);$i++)
						{
							$row=$db->fetchArray($sql1);
							echo '<tr>';
							echo '<center><td class="tid" style="display:none">'.$row['tid'].'</td>';
							echo '<td>'.$row['date'].'</td>';
							echo '<td>'.$row['wfhHrs'].'</td>';
							echo '<td>'.$row['reason'].'</td>';
							echo '<td>'.$row['comments'].'</td>';
							echo '<td>'.$row['status'].'</td>';
							//if status is pending then action will be visible edit/delete
							if(strtoupper($row['status'])=='PENDING'){
								echo '<td>
									<button  id="modify" onclick=editwfh("'.$row['tid'].'") class="btn btn-success" name="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
									<button id="delete" onclick=deletewfh("'.$row['tid'].'") class="btn btn-danger" name="Delete"><i class="fa fa-trash" aria-hidden="true"></i></button>
								</td>';
							}
							else 
							{
								echo '<td></td>';
							}
							echo '</tr>';
						}
		
						echo "</center></form></table>"; //table close
						echo "</div>";//showtable div close
						echo "</div>"; // distinctYears div close
					}
					echo "</div>";//panel-body div close
					echo "</div>";//panel div close
			}
		echo "</body>
	</html>";	
?>
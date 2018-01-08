<?php
	session_start();
	require_once 'Library.php';
	require_once 'generalFunctions.php';
	$db=connectToDB();
?>
<html>
	<head>
		<?php
			echo '<link rel="stylesheet" type="text/css" media="screen" href="public/css/teamapproval.css">';
	
			if(isset($_REQUEST['role']))
			{
				$_SESSION['roleofemp']=$_REQUEST['role'];
				if($_REQUEST['role']=="manager") {$divid="loadmanagersection";echo "<script>var divid=\"loadmanagersection\";</script>"; }
				if($_REQUEST['role']=="hr") { $divid="loadhrsection";echo "<script>var divid=\"loadhrsection\";</script>";}
			}
		?>
	
		<style type="text/css">
		#modifytid {
			cursor: pointer;
		}
		
		#deltid {
			cursor: pointer;
		}
		</style>
	</head>

	<body>
		<div class="col-sm-12"><!-- 12 column start -->
			<?php
				function displaytable($transactionid) {
					global $db;
					$sql=$db->query("select * from empleavetransactions where transactionid='".$transactionid."'");
					$row=$db->fetchassoc($sql);
					$childern=getChildren($_SESSION['u_empid']);
					$empnametresult=$db->query("select empname from emp where empid='".$row['empid']."' and state='Active'");
					$empnamerow=$db->fetchAssoc($empnametresult);
					if(in_array($row['empid'],$childern) || ($_SESSION['user_dept']=="HR")) {
						echo "<div class='panel panel-primary'>
									<div class='panel-heading text-center'>
										<strong style='font-size:20px;'>Modify Employee Approved Leaves</strong>
									</div>
									<div class='panel-body'>
										<table class='table table-hover'>
											<tr class='info'>
												<th>Transaction ID</th>
												<th>Emp Name</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Count</th>
												<th>Reason</th>
												<th>approval Status</th>
												<th>Actions</th>
											</tr>
											<tr>";
												echo  '<td>'.$row['transactionid'].'</td>
											      	<td>'.$empnamerow['empname'].'</td>
													<td>'.$row['startdate'].'</td>
													<td>'.$row['enddate'].'</td>
													<td>'.$row['count'].'</td>
													<td>'.$row['reason'].'</td>
													<td>'.$row['approvalstatus'].'</td><td>';
													if (!preg_match('/CompOff Leave/', $row['reason'])) {
														echo '<button id="modifytid" title="'.$row['transactionid'].'" class="'.$row['empid'].'"><font color="green"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></font></button>';
													}
											  		echo '<button id="deltid" title="'.$row['transactionid'].'" class="'.$row['empid'].'"><font color="red"><i class="fa fa-trash" aria-hidden="true"></i></font></button>
													</td>
												</tr>
											</table>';
							}
							else {
								echo "<script>BootstrapDialog.alert(\"You dont have permissions to change '".$empnamerow['empname']." ' transaction\");
																		
								</script>";
							}
					}
					
					function displayRecentTrans($emp)
					{
						global $db;
						global $divid;
						$empnametresult=$db->query("select empid,empname from emp where empname='".$emp."' and state='Active'");
						$empnamerow=$db->fetchAssoc($empnametresult);
						$sql=$db->query("select * from empleavetransactions where approvalstatus='Approved' and empid='".$empnamerow['empid']."'");
						$childern=getChildren($_SESSION['u_empid']);
						
						if(in_array($empnamerow['empid'],$childern) || ($_SESSION['user_dept']=="HR")) {
							echo "<div class='panel panel-primary'>
						<div class='panel-heading text-center'>
						<strong style='font-size:20px;'>Modify Employee Approved Leaves</strong>
						</div>
						<div class='panel-body'>";
							echo "<table class='table table-hover table-bordered'>
								  	<caption> Click on tranasaction Id to modify approved Leaves.</caption>
									<tr class='info'>
										<th>Transaction ID</th>
										<th>Emp Name</th>
										<th>Start Date</th>
										<th>End Date</th>
										<th>Count</th>
										<th>Reason</th>
										<th>approval Status</th>
									</tr>";
									while($row=$db->fetchassoc($sql)) {
										echo  '<tr><td><a href="javascript:getdetail(\''.$row['transactionid'].'\')">'.$row['transactionid'].'</a></td>
									      		<td>'.$empnamerow['empname'].'</td>
												<td>'.$row['startdate'].'</td>
												<td>'.$row['enddate'].'</td>
												<td>'.$row['count'].'</td>
												<td>'.$row['reason'].'</td>
												<td>'.$row['approvalstatus'].'</td>
												</tr>';
									}
										echo "</table>";
								  
							}
							else {
								echo "<script>BootstrapDialog.alert(\"You dont have permissions to change '".$empnamerow['empname']." ' transaction\");
									$('#loadmanagersection').load('modifyempapprovedleaves.php');
								</script>";
							}
							echo "</div>
							</div>";
						}
						if(isset($_REQUEST['change']))
						{
							if(isset($_REQUEST['del']))
							{
								getDelSection("modifyempapprovedleaves.php",$_REQUEST['tid'],$_REQUEST['empid'],$_SESSION['roleofemp']);
							}
							if(isset($_REQUEST['getDelteComments']))
							{
								echo '<div class="panel panel-primary">
						<div class="panel-heading text-center">
							<strong style="font-size:20px;">Delete Comp Off</strong>
						</div>
						<div class="panel-body">
									<form id="deletetid" method="POST" action="modifyempapprovedleaves.php?change=1&del=1&tid='.$_REQUEST['tid'].'&empid='.$_REQUEST['empid'].'">';
									echo '<div class="form-group">
										<div class="row">
											<div class="col-sm-2"></div>
											  <div class="col-sm-3">
  												 <label>Transcation ID</label>
  											  </div>
											  <div class="col-sm-5">
  												 <input type="text" class="form-control" value='.$_REQUEST['tid'].' readonly />
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
												  <input type="text" class="form-control" value='.$_REQUEST['empid'].' readonly />
											  </div>
											<div class="col-sm-2"></div>
										  </div>
										</div>
										<div class="form-group">
										  <div class="row">
											<div class="col-sm-2"></div>
											  <div class="col-sm-3">
												  <label>Comments</label>
											  </div>
											  <div class="col-sm-5">
													<textarea name="txtMessage" class="form-control" rows="2" cols="20"></textarea>
											  </div>
											<div class="col-sm-2"></div>
										  </div>
										</div>
										<div class="form-group">
										  <div class="row">
  											  <div class="col-sm-12 text-center">
													<input  type="submit" class="btn btn-primary" name="submit" value="Submit"/>
											  </div>
										  </div>
										</div>
										</form></div></div>';
								}
								if(isset($_REQUEST['modify']))
								{
									$transactionid=$_REQUEST['tid'];
									$empid=$_REQUEST['empid'];
									displaytable($transactionid);
									getSubmitSection($transactionid,"modifyempapprovedleaves.php","modifyday","modifyempapprovedleaves.php?change=1&submitmodifyday=1&tid=$transactionid","");
									echo "<tr><td>Comments</td><td><textarea name='txtMessage' class='form-control' rows='2'' cols='20'></textarea></td></tr>";
									echo "<tr><td colspan=\"2\" align='center'><input class='submit btn btn-primary' type='submit' name='submit' value='Submit'/></td></tr>
									</tbody></table></form>";
								}
								if(isset($_REQUEST['submitmodifyday']))
								{
									getModifySection("modifyempapprovedleaves.php",$_SESSION['roleofemp']);
								}
								if(isset($_REQUEST['displaytable']))
								{
									displaytable($_REQUEST['tid']);
								}
								if(isset($_REQUEST['displayrecentrtans']))
								{
									displayRecentTrans($_REQUEST['empuser']);
								}
							}
							else
							{
								echo '<form action="modifyempapprovedleaves.php?change=1&displayrecentrtans=1" method="POST" id="getemptrans">
										<div class="row"> 
								   		  <div class="col-sm-1"></div>
							              <div class="col-sm-3"><label style="font-size:16px;">Enter Employee Name:</label></div>
							        	  <div class="col-sm-4"><input type="text" id="empuser" class="form-control ui-autocomplete-input" autocomplete="off" name="empuser"/></div>
										  <div class="col-sm-3"><input class="submit btn btn-primary" type="submit" name="submit" value="SUBMIT"/></div>	
										<div class="col-sm-1"></div>
									</div>
								</form>';
							}
				?>
		</div>
		<script type="text/javascript">
			function getdetail(tid) {
				 url='modifyempapprovedleaves.php?change=1&displaytable=1&tid='+tid;
				 $('#'+divid).load(''+url+'');
			}
			$("document").ready(function() {
				$('#modifyday').submit(function() {
					$.ajax({ 
					data: $(this).serialize(), 
					type: $(this).attr('method'), 
					url: $(this).attr('action'), 
					success: function(response) { 
						$('#'+divid).html(response); 
					}
					});
					return false; 
				});
				
				$('#deletetid').submit(function() {
					$.ajax({ 
					data: $(this).serialize(), 
					type: $(this).attr('method'), 
					url: $(this).attr('action'), 
					success: function(response) { 
						$('#'+divid).html(response); 
					}
					});
							return false; 
				});
				
				$('#getemptrans').submit(function() {
					if($("#empuser").val()=="")
					{
						BootstrapDialog.alert("Please Enter Employee Name");
						return false;
					}
					$.ajax({ 
						data: $(this).serialize(), 
						type: $(this).attr('method'), 
						url: $(this).attr('action'), 
						success: function(response) { 
						    $('#'+divid).html(response); 
						}
					});
						return false; 
				});
		
				jQuery(function() {
					jQuery('#empuser').autocomplete({
						minLength: 1,
						source: function(request, response) {
							jQuery.getJSON('autocomplete/Users_JSON.php', {
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
			
				$("#deltid").click(function(){
					BootstrapDialog.confirm("Delete Transaction!", function(result){
				    	if(result) {
						var tid=$("#deltid").attr("title");
						var empid=$("#deltid").attr("class");
						$('#'+divid).load("modifyempapprovedleaves.php?change=1&getDelteComments=1&tid="+tid+"&empid="+empid);
						
					}
					else
					{
						BootstrapDialog.alert("You pressed Cancel!");
						var tid=$("#deltid").attr("title");
						$('#'+divid).load("modifyempapprovedleaves.php?change=1&displaytable=1&tid="+tid);
					}
				});
				});
				$("#modifytid").click(function(){
					var tid=$("#modifytid").attr("title");
					var empid=$("#modifytid").attr("class");
					$('#'+divid).load("modifyempapprovedleaves.php?change=1&modify=1&tid="+tid+"&empid="+empid);
				
				});
				<?php
					getDynamicSelectOptions();
				?>
			});
		</script>
	</body>
</html>

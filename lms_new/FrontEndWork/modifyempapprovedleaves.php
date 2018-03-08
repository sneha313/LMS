<?php
	session_start();
	require_once 'librarycopy1.php';
	require_once 'generalcopy.php';
	$db=connectToDB();
?>
<html>
<head>
<?php
//echo '<link rel="stylesheet" type="text/css" media="screen" href="public/css/applyleave.css" />';
if(isset($_REQUEST['role']))
{
	$_SESSION['roleofemp']=$_REQUEST['role'];
	if(strtolower($_REQUEST['role'])=="hr") { $divid="loadhrsection";echo "<script>var divid=\"loadhrsection\";</script>";}
	if(strtolower($_REQUEST['role'])=="manager") {$divid="loadmanagersection";echo "<script>var divid=\"loadmanagersection\";</script>"; }
	}
?>
<script type="text/javascript">
function getdetail(tid) {
	 url='modifyempapprovedleaves.php?change=1&displaytable=1&tid='+tid;
	 $('#'+divid).load(''+url+'');
}
$("document").ready(function() {
	$( "#tabs" ).tabs();
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
});
	</script>
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
					$query="select * from empleavetransactions where transactionid='".$transactionid."'";
					$sql=$db->pdoQuery($query);
					$rows=$db->pdoQuery($query)->results();
					foreach($rows as $row)
					$childern=getChildren($_SESSION['u_empid']);
					$empnametquery="select empname from emp where empid='".$row['empid']."' and state='Active'";
					$empnametresult=$db->pdoQuery($empnametquery);
					$empnamerows=$db->pdoQuery($empnametquery)->results();
					foreach($empnamerows as $empnamerow)
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
						$empnamequery="select empid,empname from emp where empname='".$emp."' and state='Active'";
						$empnametresult=$db->pdoQuery($empnamequery);
						$empnamerows=$db->pdoQuery($empnamequery)->results();
						foreach($empnamerows as $empnamerow)
						$sqlquery="select * from empleavetransactions where approvalstatus='Approved' and empid='".$empnamerow['empid']."'";
						$sql=$db->pdoQuery($sqlquery);
						$sqlresults=$db->pdoQuery($sqlquery)->results();
						foreach($sqlresults as $sqlresult)
						$childern=getChildren($_SESSION['u_empid']);
						if(in_array($empnamerow['empid'],$childern) || ($_SESSION['user_dept']=="HR")) {
							$query1="SELECT DISTINCT YEAR(startdate) as year FROM empleavetransactions where empid='".getEmpIdByName($emp)."' order by year desc";
							$sql1=$db->pdoQuery($query1);
							//$sql1=$db->query("SELECT DISTINCT YEAR(startdate) as year FROM empleavetransactions where empid='".getEmpIdByName($emp)."' order by year desc");
							$distinctYears=array();
							$leaveCount=$sql1 -> count($sTable = 'empleavetransactions', $sWhere = 'empid = "'.getEmpIdByName($emp).'"' );
							
							echo "<div class='panel panel-primary'>
						<div class='panel-heading text-center'>
						<strong style='font-size:20px;'>Modify Employee Approved Leaves</strong>
						</div>
						<div class='panel-body'>";
							if($leaveCount == 0) {
								echo "<div id='tabs'><ul><div id='Info'><tr><td>No Data Available</td></tr></div></ul></div>";
							} else {
								echo '<div id="tabs">
										<ul>';

								$rows=$db->pdoQuery($query1)->results();
								foreach($rows as $row)
								{
									echo "<li><a href='#".$row['year']."'>".$row['year']."</a></li>";
										array_push($distinctYears,$row['year']);
								}
							echo "</ul>";
							foreach ($distinctYears as $year) {
								echo "<div id='".$year."'>";
								echo '<h4 align=\"center\"><b>Click on tranasaction Id to modify approved Leaves</b></h4><br><br>';
							
								# Display Approved leaves based on month wise
								$months = array("01"=>"Jan", "02"=>"Feb", "03"=>"Mar", "04"=>"Apr", "05"=>"May", "06"=>"June", "07"=> "July", "08"=>"Aug", "09"=>"Sept","10"=>"Oct","11"=>"Nov", "12"=>"Dec");
								echo "<table class='table table-bordered' ><tr class='info'>";
								foreach ($months as $monthID => $monthName) {
									echo "<th>$monthName</th>";
								}
								echo "</tr><tr>";
										foreach ($months as $monthID => $monthName) {
											$startDayInMonth="$year-$monthID-01";
											$EndDayInMonth="$year-$monthID-31";
											$query="select SUM(count) as noDays from perdaytransactions where date between '".$startDayInMonth."' and '".$EndDayInMonth."' and empid='".$_SESSION['u_empid']."' and status='Approved'";
											$getApprovedLeavesInMonth=$db->pdoQuery($query);
											$getApprovedLeavesInMonthRows=$db->pdoQuery($query)->results();
											foreach($getApprovedLeavesInMonthRows as $getApprovedLeavesInMonthRow)
												$numOfDays=(float)$getApprovedLeavesInMonthRow['noDays'];
											if ($numOfDays <= 0) {
												echo "<td>0</td>";
											} else {
											echo "<td>".$numOfDays."</td>";
											}
										}
										echo "</tr></table>";

							echo "<table class='table table-hover table-bordered'>
									<tr class='info'>
										<th>Transaction ID</th>
										<th>Emp Name</th>
										<th>Start Date</th>
										<th>End Date</th>
										<th>Count</th>
										<th>Reason</th>
										<th>approval Status</th>
									</tr>";
									foreach($sqlresults as $queryrow){
										echo  '<tr><td><a href="javascript:getdetail(\''.$queryrow['transactionid'].'\')">'.$queryrow['transactionid'].'</a></td>
									      		<td>'.$empnamerow['empname'].'</td>
												<td>'.$queryrow['startdate'].'</td>
												<td>'.$queryrow['enddate'].'</td>
												<td>'.$queryrow['count'].'</td>
												<td>'.$queryrow['reason'].'</td>
												<td>'.$queryrow['approvalstatus'].'</td>
												</tr>';
									}
										echo "</table>";
								  
							}
							echo "</div></div>";
						}
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
													<textarea name="txtMessage" class="form-control"></textarea>
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
									echo "<tr><td>Comments</td><td><textarea name='txtMessage' class='form-control'></textarea></td></tr>";
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

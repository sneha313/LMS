<?php
session_start();
require_once 'librarycopy1.php';
require_once 'generalcopy.php';
//require_once 'Library.php';
//require_once 'generalFunctions.php';
$db = connectToDB();
//$aColumns = array('id','empid', 'empname', 'empusername', 'ModifiedbyHRname','noOfDaysDeducted','reason','modified_at');
$sIndexColumn = "empid";
# Get employee based on the location
if(isset($_REQUEST['Employeelocation']))
{
	$query="SELECT e.id, e.empname, e.empid, e.empusername, et.carryforwarded, et.balanceleaves FROM emp e,emptotalleaves et where e.empid=et.empid and e.location='".$_REQUEST['Employeelocation']."' and (et.carryforwarded + et.balanceleaves)< 1 order by e.empname asc";
	//$result = $db->query($query);
	$result = $db->pdoQuery($query)->results();
	$empName="
			<br><br><table class='table table-bordered' id='leavedeductiontable' name='leavedeductiontable' style='width:95%;' align='center'>
			<tr class='info' id='leavedeductiontablerow'>
							<th style='display:none;'>Id</th>
	                    	<th>Emp Id</th>
	                    	<th>Emp userName</th>
	                    	<th>Emp Name</th>
	                    	<th>Carry Forward Leave</th>
	                    	<th>Balance Leave</th>
	                    	<th>Total leave</th>
	                    	<th>Action</th>
	                    </tr>";
	//while($row = $result->results())
	//while($row = $db->fetchAssoc($result))
		foreach ($result as $row)
	{
		$empName=$empName. '<tr id="record"><td style="display:none;">';
		$empName=$empName. $row["id"];
		$empName=$empName. '</td><td>';
		$empName=$empName. $row["empid"];
		$empName=$empName. '</td>';
		$empName=$empName. '<td>';
		$empName=$empName. $row["empusername"];
		$empName=$empName. '</td>';
		$empName=$empName. '<td>';
		$empName=$empName. $row["empname"];
		$empName=$empName. '</td>';
		$empName=$empName. '<td>';
		$empName=$empName. $row["carryforwarded"];
		$empName=$empName. '</td>';
		$empName=$empName. '<td>';
		$empName=$empName. $row["balanceleaves"];
		$empName=$empName. '</td>';
		$empName=$empName. '<td>';
		$empName=$empName. ($row['carryforwarded']+$row['balanceleaves']);
		$empName=$empName. '</td>';
		$empName=$empName. '<td><a data-id="row-'.$row['empid'].'" href="javascript:editLeave(' . $row['empid'] . ');" class="btn btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
		//$empName=$empName. '<td><button class="btn btn-primary" id="hrleavededuction"><a data-id="'.$row['empid'].'" href="leavedeductionbyhr.php?empid='. $row['empid'].'" class="btn btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></button>
		$empName=$empName. '</td></tr>';
	}
	$empName=$empName. '</table>';
	echo $empName;
}
if ( isset($_GET['edit']) && 0 < intval($_GET['edit'])) {
	global $db;
	global $sIndexColumn;
	$queryres="select * from emptotalleavesmodified WHERE $sIndexColumn = " . intval($_GET['edit']);
	$query=$db->pdoQuery($queryres)->results();
	foreach ($query as $row)
	//$row=$query->results();
	//$query=$db->query("select * from emptotalleavesmodified WHERE $sIndexColumn = " . intval($_GET['edit']));
	//$query=$db->query("select et.*,e.* from emptotalleaves et, emp e WHERE e.empid=et.empid and et.$sIndexColumn = " . intval($_GET['edit']));
	//$row=$db->fetchAssoc($query);
	$empid=$row['empid'];
	$empusername=$row['empusername'];
	$empname=$row['empname'];
	$hr_empid=$_SESSION['u_empid'];
	$hr_username=$_SESSION['user_name'];
	$hr_name=$_SESSION['u_fullname'];
	$carryfroward_year=$row['previous year'];
	$balanceleave_year=$row['present year'];
	$balanceleave=$row['balanceleaves'];
	// SAVE DATA
	if ( isset($_POST) ) {
		$p = $_POST;
		foreach ( $p as &$val ) $val = mysql_real_escape_string($val);

		//condition to check edit model form will not be empty then insert the row
		if ( !empty($p['empid']) && !empty($p['empusername']) && !empty($p['empname'])){

			$resetbalanceleave=$balanceleave+$p['leaveadded'];
			/*$res=$db->query(" UPDATE emptotalleavesmodified SET added_in_balanceLeaves_year = '" . $balanceleave_year . "',
					added_in_carryforwarded_year = '" . $carryfroward_year. "', 
					hr_name = '" . $_SESSION['u_fullname'] . "', hr_empid = '" . $_SESSION['u_empid'] . "',
					 hr_username='".$_SESSION['user_name']."', reason='".$p['leaveDeductionReason']."',
					count_of_leaves_added='".$p['totalLeave']."',added_at= '".NOW()."'  
					WHERE $sIndexColumn = " . intval($_GET['edit']));*/
			//$leaveupdate="insert into emptotalleavesmodified (empid,empusername,empname,hr_empid,hr_username,hr_name,count_of_leaves_added,added_at,reason,added_in,added_in_carryforwarded_year,added_in_balanceLeaves_year) values ('".$p['empid']."','".$p['empusername']."','".$p['empname']."','".$_SESSION['u_empid']."','".$_SESSION['user_name']."','".$_SESSION['u_fullname']."','".$p['leaveadded']."',NOW(),'".$p['leaveDeductionReason']."','balanceLeave','".$carryfroward_year."','".$balanceleave_year."')";
			//$res=$db->query($leaveupdate);
			$date='NOW()';
			$dataArray = array('empid'=>$p['empid'],'empusername'=>$p['empusername'],'empname'=>$p['empname'],'hr_empid'=>$_SESSION['u_empid'],'hr_username'=>$_SESSION['user_name'],'hr_name'=>$_SESSION['u_fullname'],'count_of_leaves_added'=>$p['leaveadded'],'added_at'=>$date,'reason'=>$p['leaveDeductionReason'],'added_in'=>'balanceLeave','added_in_carryforwarded_year'=>$carryfroward_year,'added_in_balanceLeaves_year'=>$balanceleave_year);
			// use insert function
			$res = $db->insert('emptotalleavesmodified',$dataArray)->getLastInsertId();
			//$balanceleave=$db->query("UPDATE emptotalleaves SET balanceleaves='".$resetbalanceleave."' where $sIndexColumn = " . $p['empid']);

			$updatedataArray = array('balanceleaves'=>$resetbalanceleave);
			// where condition array
			$updateWhere = array('$sIndexColumn'=>$p['empid']);
			// call update function
			$balanceleave = $db->update('emptotalleavesmodified', $updatedataArray, $updateWhere)->affectedRows();
		}
	}
	// GET DATA
	$res="select et.*,e.* from emptotalleaves et, emp e WHERE e.empid=et.empid and et.$sIndexColumn = " . intval($_GET['edit']);
	$query = $db->pdoQuery($res)->results();
	foreach ($query as $row)
	//$row=$db->fetchArray($query);
	//$query = $db->pdoQuery(" SELECT * FROM emptotalleavesmodified WHERE ($sIndexColumn = ?);", array(intval($_GET['edit'])));
	//$row=$query->results();
	die(json_encode($row));
}
echo "<html>
<body>
	<form name='LeaveDeduction' id='LeaveDeduction' method='POST' action='leavedeductionbyhr.php?Employeelocation=1' accept-charset='UTF8'>
				<div class='panel panel-primary'>
					<div class='panel-heading text-center'>
						<strong style='font-size:20px;'>Leave Deduction by HR</strong>
					</div>
					<div class='panel-body'>";
							#  Get the distinct locations
							$queryLocation = "SELECT distinct(location) FROM `emp` where location != '' ORDER BY location ASC";
							//$resultLocation = $db -> pdoQuery($queryLocation);
							$resultLocation = $db -> pdoQuery($queryLocation);
							$rows=$db -> pdoQuery($queryLocation)->results();
							$resultcount=$resultLocation -> count($sTable = 'emp', $sWhere = 'location != ""' );
							
							$empName='';
							# Location selection Box Options
							$locationSelect='';
							//if($resultLocation->rowCount()>0) {
							//while ($row = $resultLocation->results()) {
							//if($db->hasRows($resultLocation)) {
							//while ($row = mysql_fetch_assoc($resultLocation)) {
							if($resultcount > 0){
							foreach ($rows as $row){
							if($_SESSION['u_emplocation']==$row['location']) {
							$locationSelect = $locationSelect . '<option value="' . $row["location"] . '">';
							$locationSelect = $locationSelect . $row["location"];
							$locationSelect = $locationSelect . '</option>';
							} else {
							$locationSelect = $locationSelect . '<option value="' . $row["location"] . '">';
								$locationSelect = $locationSelect . $row["location"];
								$locationSelect = $locationSelect . '</option>';
							}
							}
							}
						echo "<div class='form-group'>
							<div class='row' id='selectLoc'>
								<div class='col-sm-2'></div>
								<div class='col-sm-3'>
									<label for='empLoc'>Select Location:</label></div>
								<div class='col-sm-5'>
									<SELECT class='form-control' id= 'empLoc' name='empLoc'>
									<option vlaue='choose'>Choose Location</option>'.$locationSelect.'
									</SELECT>
								</div>
	                             <div class='col-sm-2></div>
							</div>
	                    </div>
						<div class='form-group' id='empleavetable'>
							<div class='row' style='display:none;'>'.$empName.'";
			                 echo "</div></div>
       				</div>
				</div>
    		</form>";?>
    		<!-- modal for editing a leave deduction for an employee whose leave is less than -5 form -->
	<div class="modal fade" id="edit-leaveduction-modal" tabindex="-1" data-role="dialog" data-aria-labelledby="edit-leaveductionmodal-label">
	<div class="modal-dialog" data-role="document">
	<div class="modal-content">
		<form class="form-horizontal" id="edit-leaveduction-form" method="POST">
			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" data-aria-label="Close"><span data-aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="edit-leaveduction-modal-label">Edit Employee Total Leave</h4>
			</div><!-- modal header end here -->
			<!-- modal body start here -->
			<div class="modal-body">
				<input type="hidden" id="edit-id" value="" class="hidden">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="empid">Employee Id:</label>
						</div>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="empid" name="empid" readonly>
						</div>
						<div class="col-sm-2"></div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="empusername">Employee UserName:</label>
						</div>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="empusername" name="empusername" readonly>
						</div>
						<div class="col-sm-2"></div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="empname">Employee Name:</label>
						</div>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="empname" name="empname" readonly>
					    </div>
					    <div class="col-sm-2"></div>
					 </div>
				</div>
				<!-- <div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="carryforwarded">Carry Forwarded:</label>
						</div>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="carryforwarded" name="carryforwarded" readonly>
					    </div>
					    <div class="col-sm-2"></div>
					</div> 
				</div>-->
				 <div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="carryforwarded">Number of Leaves Added:</label>
						</div>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="leaveadded" name="leaveadded" readonly>
					    </div>
					    <div class="col-sm-2"></div>
					</div> 
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="balanceLeave">Balance Leave:</label>
						</div>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="balanceLeave" name="balanceLeave" readonly>
					    </div>
					    <div class="col-sm-2"></div>
					</div> 
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="balanceLeave">Total Leave:</label>
						</div>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="totalLeave" name="totalLeave" readonly>
					    </div>
					    <div class="col-sm-2"></div>
					</div> 
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="leaveDeductionReason">Reason:</label>
						</div>
						<div class="col-sm-5">
							<textarea class="form-control" id="leaveDeductionReason" name="leaveDeductionReason"></textarea>
					    </div>
					    <div class="col-sm-2"></div>
					</div> 
				</div>
			</div><!-- modal body end here -->
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save changes</button>
			</div>
		</form>
		<script>
		$("document").ready(function() {
			$("#leaveadded").spinner(
					{ min: 1 },
					{ max: 30 },
					{ step: 0.5 },
					{spin: function(event, ui) {
				       var val= $(this).change();
				    }}
				);
				$("#leaveadded").change(function(){
				     var val1 = parseFloat($("#leaveadded").val()),
				         val2 = parseFloat($("#balanceLeave").val());
				     $("#totalLeave").val(val1 + val2);
				});
			});
		</script>
	</div><!-- modal content div close -->
	</div><!-- modal dialog div close -->
	</div><!-- modal dialog div close -->
	<script>
			$("document").ready(function() {
				
				
				$("#empLoc").change(function(){

					//$("#empleavetable").hide();
					var location=$("#empLoc").val();
					if(location=="Choose") {
						$("#empleavetable").hide();
					} else {
						$("#empleavetable").show();
						$.post( "leavedeductionbyhr.php?Employeelocation="+location, function(data) {
							
							$('#empleavetable').empty();
							$("#empleavetable").append(data);
							
						});
						
					}
				});	
				
				// Save edited row
				$("#edit-leaveduction-form").on("submit", function(event) {
					//alert("hi");
					event.preventDefault();
					dataType:'JSON',
					$.post("leavedeductionbyhr.php?edit=" + $('#empid').val(), $(this).serialize(), function(data) {
						var obj=jQuery.parseJSON(data);
						var tr = 
						//$('a[data-id="row-' + $('#edit-id').val() + '"]').parent().parent();
						$('td:eq(0)', tr).html(obj.empid);
						$('td:eq(1)', tr).html(obj.empusername);
						$('td:eq(2)', tr).html(obj.empname);
						$('td:eq(3)', tr).html(obj.carryforwarded);
						$('td:eq(4)', tr).html(obj.balanceleaves);
						$('td:eq(5)', tr).html(parseFloat(obj.leaveadded) + parseFloat(obj.balanceleaves));
						$('td:eq(6)', tr).html(obj.reason);
						BootstrapDialog.alert(obj.empname + " leave modified successfully");
						$('#edit-leaveduction-modal').modal('hide');
						$('#leavedeductiontable').load("leavedeductionbyhr.php #leavedeductiontable");
					}).fail(function() { BootstrapDialog.alert('Unable to save data, please try again later.'); });
				});
				/*$("#hrleavededuction").click(function(){
				    alert("hello");
				}); */
       		});
			function editLeave(empid) {
				if ( 'undefined' != typeof empid ) {
					$.getJSON('leavedeductionbyhr.php?edit=' + empid, function(obj) {
						//edit form input box value
						$('#edit-id').val(obj.id);
						$('#empid').val(obj.empid);
						$('#empusername').val(obj.empusername);
						$('#empname').val(obj.empname);
						$('#leaveadded').val(obj.leaveadded);
						$('#balanceLeave').val(obj.balanceleaves);
						$('#totalLeave').val(parseFloat(obj.leaveadded) + parseFloat(obj.balanceleaves));
						$('#leaveDeductionReason').val(obj.reason);
						$('#edit-leaveduction-modal').modal('show');
					}).fail(function() { BootstrapDialog.alert('Unable to fetch data, please try again later.') });
				} else BootstrapDialog.alert('Unknown row id.');
			}
			</script>
			<?php 
	echo "</body>
</html>";

?>

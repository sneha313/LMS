<?php
	session_start();
	require_once 'Library.php';
	//require_once 'attendenceFunctions.php';
	require_once 'generalFunctions.php';
	error_reporting("E_ALL");
	$db=connectToDB();
	$sTable = "emp";
	$aColumns = array('id','empid', 'empusername', 'empname', 'dept', 'joiningdate', 'role', 'managername','location','state');
	$sIndexColumn = "id";
	
	function getSelectBoxOptions($query,$value,$option,$id,$default)
	{
		global $db;
		$selectstr="";
		$selectstr.="<select class='form-control' name='$option' id=$id'>";
		$result=$db->query($query);
		while($row=$db->fetchAssoc($result))
		{
			if($row[$option]==$default)
			{
				$selectstr.="<option value='".$row[$value]."' selected>".$row[$option]."</option>";
			}
			else {
				$selectstr.="<option value='".$row[$value]."'>".$row[$option]."</option>";
			}
		}
		$selectstr.="</select>";
		return $selectstr;
	}
	//to show departments
	function displayDepartmentTable() {
		// FETCH
		global $db;
		global $sTable;
		global $aColumns;
		global $sIndexColumn;
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . " FROM $sTable where state='Active'";
		$rResult=$db->query($sQuery);
		while ( $aRow = $db->fetchArray($rResult) ) {
			$row = array();
			for ( $i = 0 ; $i < count($aColumns); $i++ ) {
				$row[] = $aRow[$aColumns[$i]];
			}
			//merge action(edit/delete) column into datatable
			$output['aaData'][] = array_merge($row, array('<a data-id="row-'.$row[0].'" href="javascript:editRow(' . $row[0] . ');" class="btn btn-success" style="margin-bottom:4px;"><i class="fa fa-pencil" aria-hidden="true"></i>
						</a>&nbsp;<a href="javascript:removeRow('.$row[0].');" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'));
			
		}
		// RETURN IN JSON
		die(json_encode($output));
	}
	// AJAX FROM JQUERY
	if ( isset($_GET['ajax']) ) {
		//call displaydepartmentTable function to show all the records into datatable
		displayDepartmentTable(); 
	}
	// AJAX ADD FROM JQUERY
	if ( isset($_GET['add']) && isset($_POST) ) {
		global $db;
		global $sTable;
		global $aColumns;
		global $sIndexColumn;
		$p = $_POST;
		foreach ( $p as &$val ) $val = mysql_real_escape_string($val);
		//condition to check add form will not be empty then insert the row
		if ( !empty($p['newempid']) && !empty($p['newempusername']) && !empty($p['newempname']) && !empty($p['joiningdate'])) {
			//to generate the id by last insert query
			$id = mysql_insert_id();
			//to check department already exist or not
			$query =$db->query("SELECT distinct empid FROM $sTable where empid='".$p['newempid']."'");
			if($db->countRows($query) >0)
			{
				echo "This Employee already exist";
			}
			else{
				$manager=$db->query("select empid,empusername,empname,emp_emailid from emp where empid='".$p['managername']."' and state='Active'");
				$managerrow=$db->fetchAssoc($manager);
				
				//$res=$db->query("INSERT INTO $sTable(empid, empusername, empname, joiningdate, birthdaydate, dept, managerid,managerusername, managername, role, managerlevel, group, emp_emailid,manager_emailid, location) 
						//VALUES ('" . $p['newempid'] . "', '" . $p['newempusername'] . "', '". $p['newempname'] ."', '".$p['joiningdate']."','".$p['birthdate']."','".$p['dept']."','".$managerrow['empid']."','".$managerrow['empusername']."','".$managerrow['empname']."','".$p['newrole']."'".$p['hideaddmanagerlevel']."', '".$p['newgroup']."','".$p['newemail']."','".$managerrow['emp_emailid']."','".$p['location']."')");
				$query=$db->query("INSERT INTO`emp` (`empid`,`empusername`,`empname`,`joiningdate`,
	    			`birthdaydate`,`dept`,`managerid`,`managerusername`,`managername`,
	    			`role`,`managerlevel`,`group`,`emp_emailid`,`manager_emailid`,`location`)
		 			VALUES ('".$_POST['newempid']."','".$_POST['newempusername']."', 
		 			'".$_POST['newempname']."','".$_POST['joiningdate']."','".$_POST['birthdate']."',
		 			'".$_POST['dept']."','".$managerrow['empid']."','".$managerrow['empusername']."',
		 			'".$managerrow['empname']."','".$_POST['newrole']."','".$_POST['hideaddmanagerlevel']."',
					'".$_POST['newgroup']."',
		 			'".$_POST['newemail']."','".$managerrow['emp_emailid']."','".$_POST['location']."')");
				$res=$db->fetchAssoc($query);
			}
			$query1 = $db->query(" SELECT * FROM $sTable where empid='".$p['newempid']."'");
			$row=$db->fetchAssoc($query1);
			die(json_encode($row));
		}
	}
	
	// AJAX REMOVE FROM JQUERY
	if ( isset($_GET['remove']) && 0 < intval($_GET['remove']) ) {
		global $db;
		global $sTable;
		global $aColumns;
		global $sIndexColumn;
		// Set Emp state as inactive
		$setEmpState=$db->query("UPDATE `emp` SET `state` = 'InActive' WHERE $sIndexColumn = " . intval($_GET['remove']));
		$result=$db->fetchArray($setEmpState);
	}
	
	// AJAX EDIT FROM JQUERY
	if ( isset($_GET['edit']) && intval($_GET['edit'])>0) {
		global $db;
		global $sTable;
		global $aColumns;
		global $sIndexColumn;
		$query=$db->query("select * from $sTable WHERE $sIndexColumn = " . intval($_GET['edit']));
		$row=$db->fetchAssoc($query);
		$dept=$row['dept'];
		//$subdept=$row['subDept'];
		//$deptloc=$row['deptLocation'];
		//$deptmail=$row['deptMailId'];
		// SAVE DATA
		if ( isset($_POST) ) {
			$p = $_POST;
			foreach ( $p as &$val ) $val = mysql_real_escape_string($val);
			//condition to check add form will not be empty then insert the row
			if ( !empty($p['editempid']) && !empty($p['editempusername']) && !empty($p['editempname']) && !empty($p['editgroup']) && !empty($p['editemail']) && !empty($p['editjoiningdate']) && !empty($p['hideeditmanagerlevel']) && !empty($p['dept']) && !empty($p['managername']) && !empty($p['editbirthdate']))
				$res=$db->query(" UPDATE $sTable SET empid = '" . $p['editempid'] . "',
						 empusername = '" . $p['editempusername'] . "', empname = '" . $p['editempname'] . "',
						 joiningdate='".$p['editjoiningdate']."', birthdaydate='".$p['editbirthdate']."', 
						dept='".$p['dept']."',`managerid`='".$p['managername']."',
				`managerusername`='".$row['empusername']."',`managername`='".$row['empname']."',
				`role`='".$p['editrole']."',`managerlevel`='".$p['hideeditmanagerlevel']."',
				`group`='".$p['editgroup']."',
				`emp_emailid`='".$p['editemail']."',`manager_emailid`='".$row['emp_emailid']."',
				`location`='".$p['location']."'WHERE state='Active' and empid='".$p['editempid']."'and $sIndexColumn = " . intval($_GET['edit']));
		}
		// GET DATA
		$query = $db->query(" SELECT * FROM $sTable WHERE $sIndexColumn = " . intval($_GET['edit']));
		$row=$db->fetchArray($query);
		die(json_encode($row));
	}
	
?>
<html>
	<head>
		<style>
			.input-append {
			    display: inline-table;
			    vertical-align: middle;
			  }
		</style>
	</head>
	<body>
		
		<div class="panel panel-primary">
			<div class="panel-heading text-center">
				<strong style="font-size:20px;">ECI Employee List</strong>
			</div>
			<div class="panel-body">
				<button type="button" style="padding:10px; margin:0 50px 15px 0;" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#add-modal"><b><i class="fa fa-plus" data-aria-hidden="true" style="padding-right:10px;"></i>Add New Employee</b></button>
				<div class="col-sm-12">
				<div class="table-responsive demo-x content">
					<table id="example" class="table display">
						<thead>
							<tr>
								<th>#</th>
								<th>Emp Id</th>
								<th>Emp User Name</th>
								<th>Emp Full Name</th>
								<th>Department</th>
								<th>Joining Date</th>
								<th>Role</th>
								<!--  <th>Manager Id</th>-->
								<th>Manager Name</th>
								<th>Emp Location</th>
								<th>State</th>
								<th>Action</th>
							</tr>
						</thead>
					</table><!-- table end here -->
				</div>
			</div>
		</div>
	</div><!-- 12 column div end here -->
	
	<!-- modal for editing an employee -->
		<div class="modal fade" id="edit-modal" tabindex="-1" data-role="dialog" data-aria-labelledby="edit-modal-label">
		<div class="modal-dialog" data-role="document">
	    <div class="modal-content">
		    <div class="modal-header text-center">
		       	<button type="button" class="close" data-dismiss="modal" data-aria-label="Close"><span data-aria-hidden="true">&times;</span></button>
		       	<h4 class="modal-title" id="edit-modal-label">Edit Employee</h4>
		    </div><!-- modal header div end here -->
		    <?php 
		    
	      echo "<div class='modal-body'>
			<form id='editempform2' method='POST'>
				<input type='hidden' id='edit-id' value='' class='hidden'>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
                			<label for='editempid'>Employee Id:</label>
                		</div>
    	  				<div class='col-sm-5'>
                			<input type='text' class='form-control' name='editempid' id='editempid' size='20'/>
    	  				</div>
    	  				<div class='col-sm-2'></div>
    	  			</div>
    	  		</div>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
    	  					<label for='editempusername'>Employee User Name:</label>
    	  				</div>
    	  				<div class='col-sm-5'>
    	  					<input type='text' class='form-control' name='editempusername' id='editempusername' size='20'/>
                		</div>
                		<div class='col-sm-2'></div>
                	</div>
                </div>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
                			<label for='editempname'>Employee Name:</label>
                		</div>
    	  				<div class='col-sm-5'>
                			<input type='text' class='form-control' name='editempname' id='editempname' size='20'/>
						</div>
						<div class='col-sm-2'></div>
					</div>
				</div>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='joiningdate'>Joining Date:</label>
						</div>
    	 				<div class='col-sm-5'>
							<div class='input-append date'>
                                <input type='text' name='editjoiningdate' id='editjoiningdate' class='form-control open-datetimepicker2'>
								 <span class='input-group-addon'>
									<i class='fa fa-calendar'></i></span>
                            </div>
    	  					
						</div>
						<div class='col-sm-2'></div>
					</div>
				</div>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='birthdate'>Birth Date:</label>
						</div>
    	  				<div class='col-sm-5'>
    	  					<div class='input-append date'>
								<input type='text' class='form-control open-datetimepicker3' name='editbirthdate' id='editbirthdate' size='20'/>
								<label class='input-group-addon btn' for='date'>
									<span class='fa fa-calendar'></span>
								</label>
							</div>
							</div>
						<div class='col-sm-2'></div>
					</div>
				</div>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='editdept'>Department:</label>
						</div>
						<div class='col-sm-5' id='dept'>
							".getSelectBoxOptions("select distinct(dept) from emp","dept","dept","editdept",$row['dept'])."
						</div>
						<div class='col-sm-2'></div>
					</div>
				</div>
				<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='editmanager'>Manager:</label>
						</div>
						<div class='col-sm-5' id='getmanager'>
							".getSelectBoxOptions("select distinct(empname) as managername,empid from emp where state='Active' and (role='manager' or role='Manager')","empid","managername","editmanager",$row['managername'])."
				    	</div>
				     	<div class='col-sm-2'></div>
				  	</div>
				</div>
				<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
				        	<label for='editrole'>Role:</label>
				        </div>
    	  				<div class='col-sm-5'>
				        	<SELECT class='form-control' name='editrole' id='editrole'>
								<option></option>
				    	  		<option>user</option>
								<option>manager</option>
								<option>hr</option>    	  
		  					</select>
				    	</div>
				     	<div class='col-sm-2'></div>	
				   	</div>
				</div>
	  			<div class='form-group'>
					<div class='row' id='editmanagerlevel' style='display:none'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
				        	<label for='hideeditmanagerlevel'>Manager Level:</label>
				    	</div>
				        <div class='col-sm-5'>
				       		<SELECT class='form-control' name='hideeditmanagerlevel' id='hideeditmanagerlevel'>
		       					<option></option>
		                       	<option>level1</option>
		                       	<option>level2</option>
		                       	<option>level3</option>       
				       			<option>level4</option> 
                  			</select>
				      	</div>
				     	<div class='col-sm-2'></div>
				  	</div>	
				</div>		
    	  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
				        	<label for='editgroup'>Group:</label>
				       	</div>
    	  				<div class='col-sm-5'>
				       		<input type='text' class='form-control' name='editgroup' id='editgroup' size='20' />
						</div>
						<div class='col-sm-2'></div>
					</div>
				</div>
    	  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='editemail'>Employee Email:</label>
						</div>
    	  				<div class='col-sm-5'>
							<input type='text' class='form-control' name='editemail' id='editemail' size='20'/>
    	  				</div>
    	  				<div class='col-sm-2'></div>
    	  			</div>
    	  		</div>
		    	<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
    	  					<label for='location'>Location:</label>
    	  				</div>
    	  				<div class='col-sm-5'>
    	  					<SELECT name='location' class='form-control' id='location'>
		    	  				<option>BLR</option>
		    	  				<option>MUM</option>
    	  					</select>
    	  				</div>
    	  				<div class='col-sm-2'></div>
    	  			</div>
    	  		</div>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-12 text-center'>
    	  					<input class='submit btn btn-info' type='submit' name='submit' value='Edit'/>
    	  				</div>
		  			 </div>
    	  		</div>
  	</form>";
  	echo "<script>
		$(document).ready(function(){
			$('#editjoiningdate').datepicker({
				dateFormat: 'yy-mm-dd',
				minView : 2,
				autoclose: true    
			});
			$('.open-datetimepicker3').datepicker({
				dateFormat: 'yy-mm-dd',
				minView : 2,
				autoclose: true 
			});
		});
	</script>
  	</div>";
  	?>
  	</div>
  	</div>
  	</div>
	<!-- close modal for editing an employee -->
	
	<!-- modal for adding a new employee -->
	<div class="modal fade" id="add-modal" tabindex="-1" data-role="dialog" data-aria-labelledby="add-modal-label">
		<div class="modal-dialog" data-role="document">
	    <div class="modal-content">
		    	<div class="modal-header text-center">
		        	<button type="button" class="close" data-dismiss="modal" data-aria-label="Close"><span data-aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title" id="add-modal-label">Add new Employee</h4>
		      	</div><!-- modal header div end here -->
		      	<?php 
	      		echo "<div class='modal-body'>
				
	    	<form class='form-horizontal' id='add-form' method='POST'>
		      	<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'><label for='newempid'>Employee Id:</label></div>
    	  				<div class='col-sm-5'><input type='text' class='form-control' name='newempid' id='newempid' size='20' /></div>
		  				<div class='col-sm-2'></div>
                	</div>
                </div>
                <div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'><label for='newempusername'>Employee User Name:</label></div>
    	  				<div class='col-sm-5'><input type='text' class='form-control' name='newempusername' id='newempusername' size='20' /></div>
                		<div class='col-sm-2'></div>
                	</div>
                </div>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'><label for='newempname'>Employee Name:</label></div>
    	  				<div class='col-sm-5'><input type='text' class='form-control' name='newempname' id='newempname' size='20' /></div>
                		<div class='col-sm-2'></div>
                	</div>
                </div>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='joiningdate'>Joining Date:</label>
						</div>
    	  				<div class='col-sm-5'>
							<div class='input-append date'>
								<input type='text' class='form-control joiningdate' name='joiningdate' id='joiningdate' size='20' />
								<label class='input-group-addon btn' for='date'>
									<span class='fa fa-calendar'></span>
								</label>
							</div>
						</div>
                		<div class='col-sm-2'></div>
                	</div>
                </div>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'><label for='birthdate'>Birth Date:</label></div>
    	  				<div class='col-sm-5'>
							<div class='input-append date'>
								<input type='text' class='form-control birthdate' name='birthdate' id='birthdate' size='20' />
								<label class='input-group-addon btn' for='date'>
									<span class='fa fa-calendar'></span>
								</label>
							</div>
						</div>
                		<div class='col-sm-2'></div>
                	</div>
                </div>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'><label for='newdept'>Department:</label></div>
    	  				<div class='col-sm-5' id='dept' name='dept'>".getSelectBoxOptions("select distinct(dept) from emp","dept","dept","newdept","")."</div>
						<div class='col-sm-2'></div>
					</div>
				</div>";
		  echo "<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'><label for='newmanager'>Manager:</label></div>
    	  				<div class='col-sm-5' id='getmanager' name='addmanagername'>".getSelectBoxOptions("select distinct(empname) as managername,empid from emp where state='Active' and (role='manager' or role='Manager')","empid","managername","newmanager","")."</div>
						<div class='col-sm-2'></div>	
					</div>
				</div>";
		  echo "<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'><label for='newrole'>Role:</label></div>
    	  				<div class='col-sm-5'>
							<SELECT class='form-control' name='newrole' id='newrole'>
								<option></option>
								<option>user</option>
				    	  		<option>manager</option>
								<option>hr</option>    	  
					  		</select>
						</div>
						<div class='col-sm-2'></div>
					</div>
				</div>
				<div class='form-group' style='display:none'>
					<div class='row' id='addmanagerlevel'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='hideaddmanagerlevel'>Manager Level:</label>
						</div>
          				<div class='col-sm-5'>
							<SELECT class='form-control' name='hideaddmanagerlevel'>
						    	<option></option>
				                <option>level1</option>
				                <option>level2</option>
				             	<option>level3</option>       
						       	<option>level4</option>	
                  			</select>
						</div>
						<div class='col-sm-2'></div>
					</div>		
				</div>			
    	  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'><label for='newgroup'>Group:</label></div>
    	  				<div class='col-sm-5'>
							<input type='text' class='form-control' name='newgroup' id='newgroup' size='20' />
						</div>
						<div class='col-sm-2'></div>
					</div>
				</div>
    	  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='newemail'>Employee Email:</label>
						</div>
    	  				<div class='col-sm-5'>
							<input type='text' class='form-control' name='newemail' id='newemail' size='20' />
						</div>
						<div class='col-sm-2'></div>
					</div>
				</div>
				<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='location'>Location:</label>
						</div>
						<div class='col-sm-5'>
							<SELECT class='form-control' name='location' id='location'>
								<option id='BLR'>BLR</option>
								<option id='MUM'>MUM</option>
							</select>
						</div>
						<div class='col-sm-2'></div>
					</div>
				</div>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-12 text-center'>
							<input class='submit btn btn-primary' type='submit' size='10px' name='submit' value='Add'/>
						</div>
		   			</div>
				</div>
		  		
			</form>";//form end here
			 	echo "</div>";// modal body div end here -->
				echo "<script>
					$(document).ready(function(){
						$('.joiningdate').datepicker({
							dateFormat: 'yy-mm-dd',
							minView : 2,
							autoclose: true
						});
						$('.birthdate').datepicker({
							dateFormat: 'yy-mm-dd',
							minView : 2,
							autoclose: true
						});
					});
				</script>";
			 	?>
		</div><!-- modal content div end here -->
		
		</div><!-- modal dialog div end here -->
	</div><!-- modal fade div end here -->
	
	<script type="text/javascript" class="init">
		$(document).ready(function() {	
			//return the location of the topmost window in the window hierarchy
			if ( top.location.href != location.href ) top.location.href = location.href;
			// Initialize datatable
			$('#example').dataTable({
				"aoColumnDefs": [
			                        { "bSearchable": false, "bVisible": false, "aTargets": [ 0 ] }
			                    ],
				"aProcessing": true,
				"aServerSide": true,
				"ajax": "eciemp.php?ajax"
			});
			$("#addempform").validate({
		  	    rules: {
		  	    	newempid: "required",
		  	        newempusername: "required",
		  	        newempname: "required",
		  	        joiningdate: "required",
		  	        birthdate: "required",
		  	        dept: "required",
		  	        getmanager: "required",
		  	        newrole: "required",
					hideaddmanagerlevel : "required",
		  	        newgroup: "required",
		  	        newemail: {
		  	            required: true,
		  	            email: true
		  	        },
		  	        location: "required"
		  	    },
		  	    messages: {
		  	    	newempid: "Please enter empid",
		  	    	newempusername: "Please enter Emp User Name",
		  	    	newempname: "Please enter Emp Name",
		  	    	joiningdate: "Please select JoiningDate",
		  	    	birthdate: "Please select BirthDate",
		  	    	dept: "Please select Department",
		  	    	getmanager: "Please select Manager",
		  	    	newrole: "Please select role",
					hideaddmanagerlevel: "Please select managerlevel",
		  	    	newgroup: "please select group",
		  	        newemail: "Please enter a valid email address",
		  	        location: "please select location"   
		  	    }
		  	  });
			$("#editempform2").validate({
	            rules: {
	                editempid: "required",
	                editempusername: "required",
	                editempname: "required",
	                joiningdate: "required",
	                birthdate: "required",
	                dept: "required",
	                getmanager: "required",
	                editrole: "required",
					hideeditmanagerlevel: "required",
	                editgroup: "required",
	                editemail: {
	                    required: true,
	                    email: true
	                },
	                location: "required"
	            },
	            messages: {
	                editempid: "Please enter empid",
	                editempusername: "Please enter Emp User Name",
	                editempname: "Please enter Emp Name",
	                joiningdate: "Please select JoiningDate",
	                birthdate: "Please select BirthDate",
	                dept: "Please select Department",
	                getmanager: "Please select Manager",
	                editrole: "Please select role",
					hideeditmanagerlevel: "Please select manager level",
	                editgroup: "please select group",
	                editemail: "Please enter a valid email address",
	                location: "please select location"   
	            }
	          });
			$("#newrole").change(function(){
				if($("#newrole").val()=="manager") {
					$("#addmanagerlevel").show();
				} else {
					$("#addmanagerlevel").hide();
				} 
			});
			$("#editrole").change(function(){
		    	if($("#editrole").val()=="manager") {
		       		$("#editmanagerlevel").show();
		     	} else {
		        	$("#editmanagerlevel").hide();
		        } 
			});
			// Save edited row
			$("#editempform2").on("submit", function(event) {
				//alert($('#edit-id').val());
				event.preventDefault();
				dataType:'JSON',
				$.post("eciemp.php?edit=" + $('#edit-id').val(), $(this).serialize(), function(data) {
					var obj=jQuery.parseJSON(data);
					var tr = 
					//$('a[data-id="row-' + $('#edit-id').val() + '"]').parent().parent();
					$('td:eq(0)', tr).html(obj.empid);
					$('td:eq(1)', tr).html(obj.empusername);
					$('td:eq(2)', tr).html(obj.empname);
					$('td:eq(3)', tr).html(obj.dept);
					$('td:eq(4)', tr).html(obj.joiningdate);
					$('td:eq(5)', tr).html(obj.role);
					$('td:eq(6)', tr).html(obj.managername);
					$('td:eq(7)', tr).html(obj.location);
					$('td:eq(8)', tr).html(obj.state);
					BootstrapDialog.alert(obj.empname + " employee data edited successfully");
					$('#edit-modal').modal('hide');
					$('#example').DataTable().ajax.reload(null, false);
				}).fail(function() { BootstrapDialog.alert('Unable to save data, please try again later.'); });
			});
			
			// Add new row
			$("#add-form").on("submit", function(event) {
				event.preventDefault();
				dataType:'JSON',
				$.post("eciemp.php?add", $(this).serialize(), function(data) {
					if (data.includes("Employee already exist")) {
						BootstrapDialog.alert("Employee Already exists! Please try again");
						$('#add-modal').modal('hide');
					} else {
						var obj = jQuery.parseJSON(data);
						$('#example tbody tr:last').after('<tr role="row"><td class="sorting_1" style="display:none;">' + obj.id + '</td><td>' + obj.empid + '</td><td>' + obj.empusername + '</td><td>' + obj.empname + '</td><td>' + obj.dept + '</td><td>' + obj.joiningdate + '</td><td>' + obj.role + '</td><td>' + obj.managername + '</td><td>' + obj.location + '</td><td>' + obj.state + '</td><td><a data-id="row-' + obj.id + '" href="javascript:editRow(' + obj.id + ');" class="btn btn-success"><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;<a href="javascript:removeRow(' + obj.id + ');" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td></tr>');
						$('#add-modal').modal('hide');
						BootstrapDialog.alert('successfully added employee '+obj.empname+' and department ' + obj.dept);
					}
				}).fail(function() { BootstrapDialog.alert('Unable to save data, please try again later.'); });
				$('#example').DataTable().ajax.reload();
				});
		});
		// Edit row
		function editRow(id) {
			if ( 'undefined' != typeof id ) {
				$.getJSON('eciemp.php?edit=' + id, function(obj) {
					//edit form input box value
					$('#edit-id').val(obj.id);
					$('#editempid').val(obj.empid);
					$('#editempusername').val(obj.empusername);
					$('#editempname').val(obj.empname);
					$('#editjoiningdate').val(obj.joiningdate);
					$('#editbirthdate').val(obj.birthdaydate);
					$('#dept').val(obj.dept);
					$('#getmanager').val(obj.managername);
					$('#editrole').val(obj.role);
					$('#hideeditmanagerlevel').val(obj.managerlevel);
					$('#editgroup').val(obj.group);
					$('#editemail').val(obj.emp_emailid);
					$('#location').val(obj.location);
					$('#edit-modal').modal('show');
				}).fail(function() { BootstrapDialog.alert('Unable to fetch data, please try again later.') });
			} else BootstrapDialog.alert('Unknown row id.');
		}

		// Remove row
		function removeRow(id) {
			if ( 'undefined' != typeof id ) {
				 BootstrapDialog.confirm('Are you sure delete this employee?', function(result){
			     	if(result) {
			        	$.get('eciemp.php?remove=' + id, function() {
				        	//alert(id);
				      		//remove row from datatable
							$('a[data-id="row-' + id + '"]').parent().parent().remove();
							//refresh datatable after deleting record from database and datatable
							$('#example').DataTable().ajax.reload(null, false);
							BootstrapDialog.alert('Employee is set to InActive state.');
						}).fail(function() { BootstrapDialog.alert('Unable to fetch data, please try again later.') });
			      	}
			     	else
			     	{
			     		BootstrapDialog.alert('Employee is not set to InActive state.');
			     	}
				});
			} else BootstrapDialog.alert('Unknown row id.');
		}
		
	</script>
	</body>
</html>
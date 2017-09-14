<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
$sTable = "departments";
$aColumns = array('ID','mainDept', 'subDept', 'deptLocation', 'deptMailId');
$sIndexColumn = "ID";
//to show departments
function displayDepartmentTable() {
	// FETCH
	global $db;
	global $sTable;
	global $aColumns;
	global $sIndexColumn;
	
	$sQuery = " SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . " FROM $sTable";
	$rResult=$db->query($sQuery);
	while ( $aRow = $db->fetchArray($rResult) ) {
		$row = array();
		for ( $i = 0 ; $i < count($aColumns); $i++ ) {
			$row[] = $aRow[$aColumns[$i]];
		}
		//merge action(edit/delete) column into datatable
		$output['aaData'][] = array_merge($row, array('<a data-id="row-'.$row[0].'" href="javascript:editRow(' . $row[0] . ');" class="btn btn-success"><i class="fa fa-pencil" aria-hidden="true"></i>
					</a>&nbsp;<a href="javascript:removeRow('.$row[0].');" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'));
	}
	// RETURN IN JSON
	die(json_encode($output));
}

// AJAX EDIT FROM JQUERY
if ( isset($_GET['edit']) && 0 < intval($_GET['edit'])) {
	global $db;
	global $sTable;
	global $aColumns;
	global $sIndexColumn;
	$query=$db->query("select * from $sTable WHERE $sIndexColumn = " . intval($_GET['edit']));
	$row=$db->fetchAssoc($query);
	$maindept=$row['mainDept'];
	$subdept=$row['subDept'];
	$deptloc=$row['deptLocation'];
	$deptmail=$row['deptMailId'];
	// SAVE DATA
	if ( isset($_POST) ) {
		$p = $_POST;
		foreach ( $p as &$val ) $val = mysql_real_escape_string($val);
			//condition to check add form will not be empty then insert the row
			if ( !empty($p['maindept']) && !empty($p['subdept']) && !empty($p['deptloc']) )
				$res=$db->query(" UPDATE $sTable SET mainDept = '" . $p['maindept'] . "', subDept = '" . $p['subdept'] . "', deptLocation = '" . $p['deptloc'] . "', deptMailId='".$p['deptmail']."' WHERE $sIndexColumn = " . intval($_GET['edit']));	
		}
		//displayDepartmentTable();

		// GET DATA
		$query = $db->query(" SELECT * FROM $sTable WHERE $sIndexColumn = " . intval($_GET['edit']));
		$row=$db->fetchArray($query);
		die(json_encode($row));
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
	if ( !empty($p['add_maindept']) && !empty($p['add_subdept']) && !empty($p['add_deptloc']) && !empty($p['add_deptmail']) ) {
		//to generate the id by last insert query
		$id = mysql_insert_id();
		//to check department already exist or not
		$query =$db->query("SELECT * FROM $sTable WHERE mainDept='".$p['add_maindept']."' and subDept='".$p['add_subdept']."'");
		
		if($db->countRows($query) >0)
		{
			echo "This department already exist";
		}
		else{
			$res=$db->query(" INSERT INTO $sTable (mainDept, subDept, deptLocation, deptStatus, deptMailId) VALUES ('" . $p['add_maindept'] . "', '" . $p['add_subdept'] . "', '". $p['add_deptloc'] ."', 'Active', '".$p['add_deptmail']."')");
			$res=$db->fetchAssoc($query);
		}
		$query1 = $db->query(" SELECT * FROM $sTable WHERE mainDept='".$p['add_maindept']."' and subDept='".$p['add_subdept']."'");
		$row=$db->fetchAssoc($query1);
		die(json_encode($row));	
		//displayDepartmentTable();
	}
}
// AJAX REMOVE FROM JQUERY
if ( isset($_GET['remove']) && 0 < intval($_GET['remove']) ) {
	global $db;
	global $sTable;
	global $aColumns;
	global $sIndexColumn;
	$query="select e.empid,d.mainDept from emp e join departments d where e.dept=d.mainDept";
	$result = $db->query($query);
	$row = $db->fetchArray($result);
	$empid=$row['empid'];
	$maindept=$row['mainDept'];
	$subdept=$row['subDept'];
	//count number of records present in a particular department 
	//if number of rows greater than 0 then HR can't delete that department
	if($db->countRows($result) >0){
		echo "<span color=red>You cannot remove this department</span>";
	}
	//if number of rows is zero  then HR can delete that department
	else{
		// REMOVE DATA
		$res=$db->query(" DELETE FROM $sTable WHERE $sIndexColumn = " . intval($_GET['remove']));

		$result=$db->fetchArray($res);
	}			
}
// AJAX FROM JQUERY
if ( isset($_GET['ajax']) ) {
	//call displaydepartmentTable function to show all the records into datatable
	displayDepartmentTable(); 
}
?>
<html>
	<head>
		<title>Department List</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.10/css/jquery.dataTables.css">
		<link rel="stylesheet" type="text/css" href="public/js/bootstrap3-dialog/bootstrap-dialog.css">
	</head>
	<body>
		<h1>
			Department List
		</h1>
		<br>

	<div class="container-fluid">
		<button type="button" style="padding:10px; margin:0 50px 15px 0;" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#add-modal"><b><i class="fa fa-plus" aria-hidden="true" style="padding-right:10px;"></i>Add New Department</b></button>
		<div class="row">
			<div class="col-sm-10">
				<div class="table-responsive demo-x content">
					<table id="example" class="display">
						<thead>
							<tr>
								<th>#</th>
								<th>Main Dept</th>
								<th>Sub Dept</th>
								<th>Dept Location</th>
								<th>Dept MailID</th>
								<th style="background-image: none">Action</th>
							</tr>
						</thead>
					</table><!-- table end here -->
				</div>
			</div><!-- 10 column div end here -->
		</div><!-- row div end herer -->
	</div><!-- container-fluid div end here -->
	<!-- modal for editing a department form -->
	<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-label">
	<div class="modal-dialog" role="document">
	<div class="modal-content">
		<form class="form-horizontal" id="edit-form" method="POST">
			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="edit-modal-label">Edit selected row</h4>
			</div><!-- modal header end here -->
			<!-- modal body start here -->
			<div class="modal-body">
				<input type="hidden" id="edit-id" value="" class="hidden">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-1"></div>
						<div class="col-sm-3">
							<label for="maindept">Main Department:</label>
						</div>
						<div class="col-sm-7">
							<input type="text" class="form-control" id="maindept" name="maindept" placeholder="Enter Main Department" required>
						</div>
						<div class="col-sm-1"></div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-1"></div>
						<div class="col-sm-3">
							<label for="subdept">Sub Department:</label>
						</div>
						<div class="col-sm-7">
							<input type="text" class="form-control" id="subdept" name="subdept" placeholder="Enter Sub Department" required>
						</div>
						<div class="col-sm-1"></div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-1"></div>
						<div class="col-sm-3">
							<label for="deptloc">Dept Location:</label>
						</div>
						<div class="col-sm-7">
							<select class="form-control" id="deptloc" name="deptloc">
								<option>BLR</option>
								<option>MUM</option>
							</select>
					    </div>
					    <div class="col-sm-1"></div>
					 </div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-1"></div>
						<div class="col-sm-3">
							<label for="deptmail">Dept MailID:</label>
						</div>
						<div class="col-sm-7">
							<input type="email" class="form-control" id="deptmail" name="deptmail" placeholder="Enter Department MailID">
					    </div>
					    <div class="col-sm-1"></div>
					</div> 
				</div>
			</div><!-- modal body end here -->
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save changes</button>
			</div>
		</form>
	</div><!-- modal content div close -->
	</div><!-- modal dialog div close -->
	</div><!-- modal dialog div close -->
	
	<!-- modal for adding a new department -->
	<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="add-modal-label">
		<div class="modal-dialog" role="document">
	    <div class="modal-content">
	    	<form class="form-horizontal" id="add-form" method="POST">
		    	<div class="modal-header text-center">
		        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title" id="add-modal-label">Add new row</h4>
		      	</div><!-- modal header div end here -->
		      	<div class="modal-body">
	      			<div class="form-group">
	      				<div class="row">
	      					<div class="col-sm-1"></div>
	      					<div class="col-sm-3 control-label">
				 				<label for="add-maindept">Main Department:</label>
				 			</div>
					   		<div class="col-sm-7">
								<input type="text" class="form-control" id="add_maindept" name="add_maindept" placeholder="Enter Main Department" required>
					    	</div>
					    	<div class="col-sm-1"></div>
						</div>
					</div>
					<div class="form-group">
	      				<div class="row">
	      					<div class="col-sm-1"></div>
	      					<div class="col-sm-3 control-label">
								<label for="add-subdept">Sub Department:</label>
							</div>
					    	<div class="col-sm-7">
					   			<input type="text" class="form-control" id="add_subdept" name="add_subdept" placeholder="Enter Sub Department" required>
					   		</div>
					   		<div class="col-sm-1"></div>
						</div>
					</div>
					<div class="form-group">
	      				<div class="row">
	      					<div class="col-sm-1"></div>
	      					<div class="col-sm-3 control-label">
					    		<label for="add-deptloc">Dept Location:</label>
					   	 	</div>
					    	<div class="col-sm-7">
					   			<select class="form-control" id="add_deptloc" name="add_deptloc">
					   				<option>BLR</option>
					   				<option>MUM</option>
					   			</select>
					   		</div>
					   		<div class="col-sm-1"></div>
						</div>
					</div>
					<div class="form-group">
	      				<div class="row">
	      					<div class="col-sm-1"></div>
	      					<div class="col-sm-3 control-label">
					    		<label for="add-deptmail">Dept MailID:</label>
					    	</div>
					   		<div class="col-sm-7">
					   			<input type="email" class="form-control" id="add_deptmail" name="add_deptmail" placeholder="Enter Department mail id" required>
					    	</div>
					    	<div class="col-sm-1"></div>
						</div>
					</div>
			 	</div><!-- modal body div end here -->
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			    	<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form><!--form end here-->
		</div><!-- modal content div end here -->
		</div><!-- modal dialog div end here -->
	</div><!-- modal fade div end here -->

	<script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.js"></script>
	<script type="text/javascript" src="public/js/bootstrap3-dialog/bootstrap-dialog.js"></script>
	<script type="text/javascript" class="init">
		$(document).ready(function() {		
			//return the location of the topmost window in the window hierarchy
			if ( top.location.href != location.href ) top.location.href = location.href;
			// Initialize datatable
			$('#example').dataTable({
				"aProcessing": true,
				"aServerSide": true,
				"ajax": "test1.php?ajax"
			});
			
			// Save edited row
			$("#edit-form").on("submit", function(event) {
				event.preventDefault();
				dataType:'JSON',
				$.post("test1.php?edit=" + $('#edit-id').val(), $(this).serialize(), function(data) {
					var obj=jQuery.parseJSON(data);
					//alert(data);
					var tr = $('a[data-id="row-' + $('#edit-id').val() + '"]').parent().parent();
					$('td:eq(1)', tr).html(obj.mainDept);
					$('td:eq(2)', tr).html(obj.subDept);
					$('td:eq(3)', tr).html(obj.deptLocation);
					$('td:eq(4)', tr).html(obj.deptMailId);
					$('#edit-modal').modal('hide');
				}).fail(function() { BootstrapDialog.alert('Unable to save data, please try again later.'); });
			});
			
			// Add new row
			$("#add-form").on("submit", function(event) {
				event.preventDefault();
				dataType:'JSON',
				$.post("test1.php?add", $(this).serialize(), function(data) {
					if (data.includes("department already exist")) {
						alert("Department Already exists");
						$('#add-modal').modal('hide');
					} else {
						alert(data);
						var obj = jQuery.parseJSON(data);
						$('#example tbody tr:last').after('<tr role="row"><td class="sorting_1">' + obj.ID + '</td><td>' + obj.mainDept + '</td><td>' + obj.subDept + '</td><td>' + obj.deptLocation + '</td><td>' + obj.deptMailId + '</td><td><a data-id="row-' + obj.ID + '" href="javascript:editRow(' + obj.ID + ');" class="btn btn-success"><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;<a href="javascript:removeRow(' + obj.ID + ');" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td></tr>');
						$('#add-modal').modal('hide');
						BootstrapDialog.alert('successfully added ' + obj.subDept);
					}
				}).fail(function() { BootstrapDialog.alert('Unable to save data, please try again later.'); });
			});
		});
		// Edit row
		function editRow(id) {
			if ( 'undefined' != typeof id ) {
				$.getJSON('test1.php?edit=' + id, function(obj) {
					//edit form input box value
					$('#edit-id').val(obj.ID);
					$('#maindept').val(obj.mainDept);
					$('#subdept').val(obj.subDept);
					$('#deptloc').val(obj.deptLocation);
					$('#deptmail').val(obj.deptMailID);
					$('#edit-modal').modal('show')
				}).fail(function() { BootstrapDialog.alert('Unable to fetch data, please try again later.') });
			} else BootstrapDialog.alert('Unknown row id.');
		}

		// Remove row
		function removeRow(id) {
			if ( 'undefined' != typeof id ) {
				 BootstrapDialog.confirm('Are you sure delete this department?', function(result){
			            if(result) {
			            	$.get('test1.php?remove=' + id, function() {
				            	//remove row from datatable
								$('a[data-id="row-' + id + '"]').parent().parent().remove();
							}).fail(function() { BootstrapDialog.alert('Unable to fetch data, please try again later.') });
			            }else {
			            	BootstrapDialog.alert("Row didn't get deleted.");
			            }
			        });
			} else BootstrapDialog.alert('Unknown row id.');
		}
	</script>
	</body>
</html>
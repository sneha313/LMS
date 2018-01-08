<?php
//session_start();
//require_once 'Library.php';
//require_once 'generalFunctions.php';
//$db=connectToDB();

//if($_SESSION['user_dept'] == "HR") {
//	$edit_options="true";
//} else {
	//$edit_options="false";
//}

	// VARIABLES
	$aColumns = array('ID','mainDept', 'subDept', 'deptlocation');
	$sIndexColumn = "ID";
	$sTable = "departments";
	$gaSql['user'] = "root";
	$gaSql['password'] = "Manor441";
	$gaSql['db'] = "lms";
	$gaSql['server'] = "localhost";


	// DATABASE CONNECTION
	function dbinit(&$gaSql) {
		// ERROR HANDLING
		function fatal_error($sErrorMessage = '') {
			header($_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error');
			die($sErrorMessage);
		}

		// MYSQL CONNECT
		if ( !$gaSql['link'] = @mysql_connect($gaSql['server'], $gaSql['user'], $gaSql['password']) ) {
			fatal_error('Could not open connection to server');
		}

		// MYSQL DATABASE SELECT
		if ( !mysql_select_db($gaSql['db'], $gaSql['link']) ) {
			fatal_error('Could not select database');
		}
	}

	// AJAX EDIT FROM JQUERY
	if ( isset($_GET['edit']) && 0 < intval($_GET['edit'])) {
		dbinit($gaSql);

		// SAVE DATA
		if ( isset($_POST) ) {
			$p = $_POST;
			foreach ( $p as &$val ) $val = mysql_real_escape_string($val);
			if ( !empty($p['mainDept']) && !empty($p['subDept']) && !empty($p['deptLocation']) )
				@mysql_query(" UPDATE $sTable SET mainDept = '" . $p['firstname'] . "', subDept = '" . $p['email'] . "', deptLocation = '" . $p['mobile'] . "' WHERE $sIndexColumn = " . intval($_GET['edit']));
			@mysql_query(" UPDATE e SET e.empid = '" . $p['empid'] . "', e.empname = '" . $p['empname'] . "', e.dept = '" . $p['maindept'] . "', d.subDept='".$p['subdept']."' from emp e join departments d on d.mainDept='".$p['maindept']."' where $sIndexColumn = " . intval($_GET['edit']));
			
		}

		// GET DATA
		$query = mysql_query(" SELECT * FROM $sTable WHERE $sIndexColumn = " . intval($_GET['edit']), $gaSql['link']);
		$row=mysql_fetch_assoc($query);
		//die(json_encode(mysql_fetch_assoc($query)));

		$maindept=$row['mainDept'];
		$subdept=$row['subDept'];
		$fp = fopen('jsondata.json', 'w');
		fwrite($fp, json_encode($row));
		fclose($fp);
	}

	// AJAX ADD FROM JQUERY
	if ( isset($_GET['add']) && isset($_POST) ) {
		dbinit($gaSql);

		$p = $_POST;
		foreach ( $p as &$val ) $val = mysql_real_escape_string($val);
		if ( !empty($p['maindept']) && !empty($p['subdept']) && !empty($p['deptLoc']) ) {
			@mysql_query(" INSERT INTO $sTable (ID,mainDept, subDept, deptLocation) VALUES ('".$p['ID']."','" . $p['maindept'] . "', '" . $p['subdept'] . "', '" . $p['deptLoc'] . "')");
			$id = mysql_insert_id();
			
			$query = mysql_query(" SELECT * FROM $sTable WHERE $sIndexColumn = " .$id, $gaSql['link']);
			$row=mysql_fetch_assoc($query);
			//die(json_encode(mysql_fetch_assoc($query)));
			$fp = fopen('jsondata.json', 'w');
			fwrite($fp, json_encode($row));
			fclose($fp);
		}
	}

	// AJAX REMOVE FROM JQUERY
	if ( isset($_GET['remove']) && 0 < intval($_GET['remove']) ) {
		dbinit($gaSql);
		$query="select e.empid from emp e join departments d where e.dept=d.mainDept";
		$result = mysql_query($query, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());
		$row = mysql_fetch_array($result);
		$empid=$row['empid'];
		$maindept=$row['mainDept'];
		$subdept=$row['subDept'];
		//count number of records present in a particular department
		$totemp=COUNT(".$empid.");
		
		//if number of rows greater than 0 then HR can't delete that department
		if($totemp >0)
		{
			alert("you cannot remove this department");
		}
		//if number of rows is zero  then HR can delete that department
		else{
			// REMOVE DATA
			alert("hi");
			//@mysql_query(" DELETE FROM $sTable WHERE $sIndexColumn = " . intval($_GET['remove']));
		}			
	}


	// AJAX FROM JQUERY
	if ( isset($_GET['ajax']) ) {
		dbinit($gaSql);

		// QUERY LIMIT
		$sLimit = "";
		if ( isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1' ) {
			$sLimit = "LIMIT " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);
		}

		// QUERY ORDER
		$sOrder = "";
		if ( isset($_GET['iSortCol_0']) ) {
			$sOrder = "ORDER BY ";
			for ( $i = 0; $i < intval($_GET['iSortingCols']); $i++ ) {
				if ( $_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true" ) {
					$sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . ( $_GET['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc' ) . ", ";
				}
			}
			$sOrder = substr_replace($sOrder, "", -2);
			if ( $sOrder == "ORDER BY" ) $sOrder = "";
		}

		// QUERY SEARCH
		$sWhere = "";
		if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
			$sWhere = "WHERE (";
			for ( $i = 0; $i < count($aColumns); $i++ ) {
				if ( isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" ) {
					$sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
				}
			}
			$sWhere = substr_replace($sWhere, "", -3);
			$sWhere .= ')';
		}

		// BUILD QUERY
		for ( $i = 0; $i < count($aColumns); $i++ ) {
			if ( isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '' ) {
				if ( $sWhere == "" ) $sWhere = "WHERE ";
				else $sWhere .= " AND ";
				$sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
			}
		}

		// FETCH
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . " FROM $sTable $sWhere $sOrder $sLimit ";
		$rResult = mysql_query($sQuery, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());

		while ( $aRow = mysql_fetch_array($rResult) ) {
			$row = array();
			for ( $i = 0 ; $i < count($aColumns); $i++ ) {
				if ( $aColumns[$i] == "version" ) $row[] = ( $aRow[$aColumns[$i]] == "0" ) ? '-' : $aRow[$aColumns[$i]];
				else if ( $aColumns[$i] != ' ' ) $row[] = $aRow[$aColumns[$i]];
			}
			$output['aaData'][] = array_merge($row, array('<a data-id="row-' . $row[0] . '" href="javascript:editRow(' . $row[0] . ');" class="btn btn-md btn-success"><i class="fa fa-pencil" aria-hidden="true"></i>
					</a>&nbsp;<a href="javascript:removeRow(' . $row[0] . ');" class="btn btn-danger btn-md"><i class="fa fa-trash" aria-hidden="true"></i>
					
			</a>'));
		}

		// RETURN IN JSON
		die(json_encode($output));
	}

?>
<html>
	<head>
		<title>Department List</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.10/css/jquery.dataTables.css">
		<link rel="stylesheet" type="text/css" href="public/js/bootstrap3-dialog/bootstrap-dialog.css">
<!-- Start: Google analytics code-->

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38304687-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<!-- End: Google analytics code-->

	</head>
	<body>
		<h1>
			Department List
		</h1>
		<br>

		<div class="container-fluid">
		<button type="button" style="padding:10px; margin:0 50px 15px 0;" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#add-modal"><b><i class="fa fa-plus" aria-hidden="true" style="padding-right:10px;"></i>Add New Department</b></button>
		<div class="row">
<div class="col-md-12 marginT20">

		<div class="table-responsive demo-x content">
		<table id="example" class="display">
			<thead>
				<tr>
					<th>ID</th>
					<th>Main Dept</th>
					<th>Sub Dept</th>
					<th>Dept Location</th>
					<th style="background-image: none">Action</th>
				</tr>
			</thead>
		</table>
		</div>

		</div>
		</div>
		</div>

		<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-label">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		    	<form class="form-horizontal" id="edit-form">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="edit-modal-label">Edit selected row</h4>
			      </div>
			      <div class="modal-body">
			      		<input type="hidden" id="edit-id" value="" class="hidden">
			      		<div class="form-group">
					    	<label for="firstname" class="col-sm-4 control-label">Emp ID</label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control" id="empid" name="empid" placeholder="Enter Employee ID" required>
					    	</div>
					  	</div>
			        	<div class="form-group">
					    	<label for="firstname" class="col-sm-4 control-label">Emp Name</label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control" id="empname" name="empname" placeholder="Enter Employee Name" required>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="mobile" class="col-sm-4 control-label">Department Name</label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control" id="mainDept" name="mainDept" placeholder="Enter Main Department Name">
					    	</div>
					  	</div>
					  		<div class="form-group">
					    	<label for="mobile" class="col-sm-4 control-label">Sub Department</label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control" id="subDept" name="subDept" placeholder="Enter Sub Department Name">
					    	</div>
					  	</div>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        <button type="submit" class="btn btn-primary">Save changes</button>
			      </div>
		      	</form>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="add-modal-label">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		    	<form class="form-horizontal" id="add-form">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="add-modal-label">Add new row</h4>
			      </div>
			      <div class="modal-body">
			        	<div class="form-group">
					    	<label for="add-maindept" class="col-sm-4 control-label">Main Department</label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control" id="maindept" name="maindept" placeholder="enter main department" required>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="add-email" class="col-sm-4 control-label">Sub Department</label>
					    	<div class="col-sm-8">
					      		<input type="email" class="form-control" id="subdept" name="subdept" placeholder="enter sub department" required>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="add-mobile" class="col-sm-4 control-label">Dept Location</label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control" id="deptLoc" name="deptLoc" placeholder="enter department Location" required>
					    	</div>
					  	</div>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        <button type="submit" class="btn btn-primary">Save changes</button>
			      </div>
		      	</form>
		    </div>
		  </div>
		</div>

		<script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
		<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.js"></script>
		<script type="text/javascript" src="public/js/bootstrap3-dialog/bootstrap-dialog.js"></script>
		<script type="text/javascript" language="javascript" class="init">
			$(document).ready(function() {

				// ATW
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
					$.post("test1.php?edit=" + $('#edit-id').val(), $(this).serialize(), function(data) {
						//var obj = $.parseJSON(data);
						 //var obj = jQuery.parseJSON(data);
						var obj=data;
						var tr = $('a[data-id="row-' + $('#edit-id').val() + '"]').parent().parent();
						$('td:eq(1)', tr).html(obj.empid);
						$('td:eq(2)', tr).html(obj.empname);
						$('td:eq(3)', tr).html(obj.mainDept);
						$('td:eq(4)', tr).html(obj.subDept);
						$('#edit-modal').modal('hide');
					}).fail(function() { BootstrapDialog.alert('Unable to save data, please try again later.'); });
				});

				// Add new row
				$("#add-form").on("submit", function(event) {
					event.preventDefault();
					$.post("test1.php?add", $(this).serialize(), function(data) {
						//var obj = $.parseJSON(data);
						//var obj=jQuery.parseJSON(data);
						var obj=data;
						alert(data.mainDept);
						$('#example tbody tr:last').after('<tr role="row"><td class="sorting_1">' + obj.ID + '</td><td>' + obj.mainDept + '</td><td>' + obj.subDept + '</td><td>' + obj.deptLocation + '</td><td><a data-id="row-' + obj.ID + '" href="javascript:editRow(' + obj.ID + ');" class="btn btn-default btn-sm">Edit</a>&nbsp;<a href="javascript:removeRow(' + obj.ID + ');" class="btn btn-default btn-sm">Remove</a></td></tr>');
						$('#add-modal').modal('hide');
					}).fail(function() { BootstrapDialog.alert('Unable to save data, please try again later.'); });
				});

			});

			// Edit row
			function editRow(ID) {
				if ( 'undefined' != typeof ID ) {
					$.getJSON('jsondata.json?edit=' + ID, function(obj) {
						$('#edit-id').val(obj.ID);
						$('#empid').val(obj.empid);
						$('#empname').val(obj.empname);
						$('#mainDept').val(obj.mainDept);
						$('#subDept').val(obj.subDept);
						$('#edit-modal').modal('show')
					}).fail(function() { BootstrapDialog.alert('Unable to fetch data, please try again later.') });
				} else alert('Unknown row id.');
			}

			// Remove row
			function removeRow(ID) {
				if ( 'undefined' != typeof ID ) {
					 BootstrapDialog.confirm('Are you sure delete this department?', function(result){
				            if(result) {
				            	$.get('test1.php?remove=' + ID, function() {
									$('a[data-id="row-' + ID + '"]').parent().parent().remove();
									//alert("sneha");
								}).fail(function() { BootstrapDialog.alert('Unable to fetch data, please try again later.') });
				            }else {
				                alert("Row didn't get deleted.");
				            }
				        });
					
					
				} else BootstrapDialog.alert('Unknown row id.');
			}
		</script>
	</body>
</html>
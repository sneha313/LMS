<?php
	session_start();
	require_once 'librarycopy1.php';
	require_once 'generalcopy.php';
	require_once 'attendenceFunctions.php';
	error_reporting("E_ALL");
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
		//$rResult=$db->query($sQuery);
		$rResult=$db->pdoQuery($sQuery);
		$aRows=$db->pdoQuery($sQuery)->results();
		foreach($aRows as $aRow){
		//while ( $aRow = $db->fetchArray($rResult) ) {
			$row = array();
			for ( $i = 0 ; $i < count($aColumns); $i++ ) {
				$row[] = $aRow[$aColumns[$i]];
			}
			//merge action(edit/delete) column into datatable
			$output['aaData'][] = array_merge($row, array('<a data-id="row-'.$row[0].'" href="javascript:viewEmpDeptBased(' . $row[0] . ');" class="btn btn-warning" style="margin-bottom:4px;"><i class="fa fa-user" aria-hidden="true"></i></a>&nbsp;
					<a data-id="row-'.$row[0].'" href="javascript:editRow(' . $row[0] . ');" class="btn btn-success"><i class="fa fa-pencil" aria-hidden="true"></i>
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
		$editQuery="select * from $sTable WHERE $sIndexColumn = '" . intval($_GET['edit'])."'";
		$query=$db->pdoQuery($editQuery);
		$rows=$db->pdoQuery($editQuery)->results();
		foreach($rows as $row)
		// SAVE DATA
		if ( isset($_POST) ) {
			$p = $_POST;
			foreach ( $p as &$val ) $val = mysql_real_escape_string($val);
				//condition to check add form will not be empty then insert the row
				if ( !empty($p['maindept']) && !empty($p['subdept']) && !empty($p['deptloc']) ){
					//$res=$db->query(" UPDATE $sTable SET mainDept = '" . $p['maindept'] . "', subDept = '" . $p['subdept'] . "', deptLocation = '" . $p['deptloc'] . "', deptMailId='".$p['deptmail']."' WHERE $sIndexColumn = " . intval($_GET['edit']));	

					$dataArray = array('mainDept'=>$p['maindept'],'subDept'=>$p['subdept'],'deptLocation'=>$p['deptloc'],'deptMailId'=> $p['deptmail']);
					// where condition array
					$aWhere = array($sIndexColumn=>intval($_GET['edit']));
					// call update function
					$res = $db->update($sTable, $dataArray, $aWhere)->affectedRows();
				}
		}
			// GET DATA
			$recordQuery=" SELECT * FROM $sTable WHERE $sIndexColumn = '" . intval($_GET['edit'])."'";
			$query = $db->pdoQuery($recordQuery);
			$rows = $db->pdoQuery($recordQuery)->results();
			foreach($rows as $row)
			die(json_encode($row));
		}
	
	// AJAX ADD FROM JQUERY
	if ( isset($_GET['add']) && isset($_POST) ) {
		global $db;
		global $sTable;
		global $aColumns;
		global $sIndexColumn;
		//$p = $_POST;
		//foreach ( $p as &$val ) $val = mysql_real_escape_string($val);
		//condition to check add form will not be empty then insert the row
		if ( !empty($_POST['add_maindept']) && !empty($_POST['add_subdept']) && !empty($_POST['add_deptloc']) && !empty($_POST['add_deptmail']) ) {
			//to generate the id by last insert query
			//$id = mysql_insert_id();
			//to check department already exist or not
			$queryrecord="SELECT distinct mainDept FROM $sTable WHERE subDept='".$_POST['add_subdept']."'";
			$query =$db->pdoQuery($queryrecord);
			$p=$query -> count($sTable = 'departments', $sWhere = 'subDept = "'.$_POST['add_subdept'].'"' );
				
			if($p >0)
			{
				echo "This department already exist";
			}
			else{
				$res = array('mainDept'=>$_POST['add_maindept'],'subDept'=>$_POST['add_subdept'],'deptLocation'=>$_POST['add_deptloc'] ,'deptStatus'=>'Active','deptMailId'=>$_POST['add_deptmail']);
				// use insert function
				$result = $db->insert('departments',$res)->getLastInsertId();
			}
			$queryrow1=" SELECT * FROM $sTable WHERE mainDept='".$_POST['add_maindept']."' and subDept='".$_POST['add_subdept']."'";
			$query1 = $db->pdoQuery($queryrow1);
			$rows=$db->pdoQuery($queryrow1)->results();
			foreach($rows as $row)
			die(json_encode($row));	
		}
	}
	// AJAX REMOVE FROM JQUERY
	if ( isset($_GET['remove']) && 0 < intval($_GET['remove']) ) {
		global $db;
		global $sTable;
		global $aColumns;
		global $sIndexColumn;
		//$query="select e.empid,d.mainDept from emp e join departments d where e.dept=d.mainDept";
		$query="select * from $sTable where $sIndexColumn='".intval($_GET['remove'])."'";
		$result = $db->pdoQuery($query);
		$rows = $db->pdoQuery($query)->results();
		foreach($rows as $row)
		$empid=$row['empid'];
		$maindept=$row['mainDept'];
		$subdept=$row['subDept'];
		//$group=$row['group'];
		//count number of records present in a particular department 
		$deptcountquery="SELECT COUNT(empname) AS numrows FROM emp WHERE dept='".$subdept."' and group='".$maindept."' and state='Active'";
		$sqlcounts=$db->showQuery($deptcountquery);
		foreach($sqlcounts as $sqlcount)
		if($sqlcount >0){
			echo "<script>BootstrapDialog.alert('You cannot remove this department')</script>";
		}
		//if number of rows is zero  then HR can delete that department
		else{
			// REMOVE DATA

			$res = array($sIndexColumn=>intval($_GET['remove']));
			// call update function
			$result = $db->delete('departments', $res)->affectedRows();
			//$res=$db->query(" DELETE FROM $sTable WHERE $sIndexColumn = " . intval($_GET['remove']));
			//$result=$db->fetchArray($res);
		}			
	}
	// AJAX FROM JQUERY
	if ( isset($_GET['ajax']) ) {
		//call displaydepartmentTable function to show all the records into datatable
		displayDepartmentTable(); 
	}
?>
<html>
	<body>
		<div id="employeeList" style="display:none;">
			<table class="table table-hover" id="empList">
				<tr>
					<th>Employee Name</th>
				</tr>
			</table>
		</div>
		<div class="panel panel-primary">
			<div class="panel-heading text-center">
				<strong style="font-size:20px;">Department List</strong>
			</div>
			<div class="panel-body">
				<button type="button" style="padding:10px; margin:0 50px 15px 0;" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#add-modal"><b><i class="fa fa-plus" data-aria-hidden="true" style="padding-right:10px;"></i>Add New Department</b></button>
				<div class="col-sm-12">
				<div class="table-responsive demo-x content">
					<table id="example" class="table display">
						<thead>
							<tr>
								<th>#</th>
								<th>Main Department</th>
								<th>Sub Department</th>
								<th>Department Location</th>
								<th>Department MailID</th>
								<th style="background-image: none">Action</th>
							</tr>
						</thead>
					</table><!-- table end here -->
				</div>
			</div>
		</div>
	</div><!-- 12 column div end here -->
	<!-- modal for editing a department form -->
	<div class="modal fade" id="edit-modal" tabindex="-1" data-role="dialog" data-aria-labelledby="edit-modal-label">
	<div class="modal-dialog" data-role="document">
	<div class="modal-content">
			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" data-aria-label="Close"><span data-aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="edit-modal-label">Edit selected row</h4>
			</div><!-- modal header end here -->
			<!-- modal body start here -->
			<div class="modal-body">
			
		<form class="form-horizontal" id="edit-form" method="POST">
				<input type="hidden" id="edit-id" value="" class="hidden">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="maindept">Main Department:</label>
						</div>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="maindept" name="maindept" placeholder="Enter Main Department" required>
						</div>
						<div class="col-sm-2"></div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="subdept">Sub Depepartment:</label>
						</div>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="subdept" name="subdept" placeholder="Enter Sub Department" required>
						</div>
						<div class="col-sm-2"></div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="deptloc">Department Location:</label>
						</div>
						<div class="col-sm-5">
							<select class="form-control" id="deptloc" name="deptloc">
								<option>BLR</option>
								<option>MUM</option>
							</select>
					    </div>
					    <div class="col-sm-2"></div>
					 </div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="deptmail">Department MailID:</label>
						</div>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="deptmail" name="deptmail" placeholder="Enter Department Mail ID">
					    </div>
					    <div class="col-sm-2"></div>
					</div> 
				</div>
				<div class='form-group'>
					<div class='row'>
						<div class='col-sm-12 text-center'>
    	  					<input class='submit btn btn-info' type='submit' name='submit' value='Edit'/>
    	  				</div>
		  			 </div>
    	  		</div>
				</form>
			</div><!-- modal body end here -->
			<!--  <div class="modal-footer">
				 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    	  					 <input class='submit btn btn-info' type='submit' name='submit' value='Edit'/>
				 <button type="submit" class="btn btn-primary">Edit Department</button>
			</div>-->
		
	</div><!-- modal content div close -->
	</div><!-- modal dialog div close -->
	</div><!-- modal dialog div close -->
	
	<!-- modal for adding a new department -->
	<div class="modal fade" id="add-modal" tabindex="-1" data-role="dialog" data-aria-labelledby="add-modal-label">
		<div class="modal-dialog" data-role="document">
	    <div class="modal-content">
	    	<form class="form-horizontal" id="add-form" method="POST">
		    	<div class="modal-header text-center">
		        	<button type="button" class="close" data-dismiss="modal" data-aria-label="Close"><span data-aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title" id="add-modal-label">Add new Department</h4>
		      	</div><!-- modal header div end here -->
		      	<div class="modal-body">
	      			<div class="form-group">
	      				<div class="row">
	      					<div class="col-sm-2"></div>
	      					<div class="col-sm-3">
				 				<label for="add-maindept">Main Department:</label>
				 			</div>
					   		<div class="col-sm-5">
								<input type="text" class="form-control" id="add_maindept" name="add_maindept" placeholder="Enter Main Department" required>
					    	</div>
					    	<div class="col-sm-2"></div>
						</div>
					</div>
					<div class="form-group">
	      				<div class="row">
	      					<div class="col-sm-2"></div>
	      					<div class="col-sm-3">
								<label for="add-subdept">Sub Department:</label>
							</div>
					    	<div class="col-sm-5">
					   			<input type="text" class="form-control" id="add_subdept" name="add_subdept" placeholder="Enter Sub Department" required>
					   		</div>
					   		<div class="col-sm-2"></div>
						</div>
					</div>
					<div class="form-group">
	      				<div class="row">
	      					<div class="col-sm-2"></div>
	      					<div class="col-sm-3">
					    		<label for="add-deptloc">Department Location:</label>
					   	 	</div>
					    	<div class="col-sm-5">
					    		<select class="form-control" id="add_deptloc" name="add_deptloc">
					    			<option>BLR</option>
					    			<option>MUM</option>
					    		</select>
					   		</div>
					   		<div class="col-sm-2"></div>
						</div>
					</div>
					<div class="form-group">
	      				<div class="row">
	      					<div class="col-sm-2"></div>
	      					<div class="col-sm-3">
					    		<label for="add-deptmail">Department MailID:</label>
					    	</div>
					   		<div class="col-sm-5">
					   			<input type="email" class="form-control" id="add_deptmail" name="add_deptmail" placeholder="Enter Department mail id" required>
					    	</div>
					    	<div class="col-sm-2"></div>
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
	
	<!-- modal for view employee in a particular department -->
	<div class="modal fade" id="viewemp-modal" tabindex="-1" data-role="dialog" data-aria-labelledby="viewemp-modal-label">
		<div class="modal-dialog" data-role="document">
	    <div class="modal-content">
	    	<form class="form-horizontal" id="add-form" method="POST">
		    	<div class="modal-header text-center">
		        	<button type="button" class="close" data-dismiss="modal" data-aria-label="Close"><span data-aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title" id="viewemp-modal-label">Employee List</h4>
		      	</div><!-- modal header div end here -->
		      	<div class="modal-body">
					<table class="table table-table-bordered table-hover" id="employeeList">
						<tr>
							<th>Employee Name</th>
						</tr>
					</table>
			 	</div><!-- modal body div end here -->
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			    	<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form><!--form end here-->
		</div><!-- modal content div end here -->
		</div><!-- modal dialog div end here -->
	</div><!-- modal fade div end here -->
	<!--  <table class="table table-bordered" id="employeeList">
	    <tr>
	        <th>Employee Name</th>
	    </tr>
	</table>-->
	<script>

/*var service = 'http://localhost/DistributedDataSystem/Service.svc/';

$(document).ready(function(){

    jQuery.support.cors = true;

    $.ajax(
    {
        type: "GET",
        url: 'DepartmentActionbyHR.php?view=' +id,
        data: "{}",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        cache: false,
        success: function (data) {
          	var trHTML = '';
       		$.each(data.empname, function (i, item) {
		    	trHTML += '<tr><td>' + data.empname[i] + '</td></tr>';
		    });
		    $('#employeeList').append(trHTML);
		}
    });
});*/

</script>
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
				"ajax": "DepartmentActionbyHR.php?ajax"
			});
			
			// Save edited row
			$("#edit-form").on("submit", function(event) {
				event.preventDefault();
				dataType:'JSON',
				$.post("DepartmentActionbyHR.php?edit=" + $('#edit-id').val(), $(this).serialize(), function(data) {
					var obj=jQuery.parseJSON(data);
					var tr = 
					//$('a[data-id="row-' + $('#edit-id').val() + '"]').parent().parent();
					$('td:eq(0)', tr).html(obj.mainDept);
					$('td:eq(1)', tr).html(obj.subDept);
					$('td:eq(2)', tr).html(obj.deptLocation);
					$('td:eq(3)', tr).html(obj.deptMailId);
					BootstrapDialog.alert(obj.mainDept + " department data edited successfully");
					$('#edit-modal').modal('hide');
					$('#example').DataTable().ajax.reload(null, false);
				}).fail(function() { BootstrapDialog.alert('Unable to save data, please try again later.'); });
			});
			
			// Add new row
			$("#add-form").on("submit", function(event) {
				event.preventDefault();
				dataType:'JSON',
				$.post("DepartmentActionbyHR.php?add", $(this).serialize(), function(data) {
					if (data.includes("department already exist")) {
						BootstrapDialog.alert("Department Already exists! Please enter unique main department or sub department");
						$('#add-modal').modal('hide');
					} else {
						var obj = jQuery.parseJSON(data);
						$('#example tbody tr:last').after('<tr role="row"><td class="sorting_1" style="display:none;">' + obj.ID + '</td><td>' + obj.mainDept + '</td><td>' + obj.subDept + '</td><td>' + obj.deptLocation + '</td><td>' + obj.deptMailId + '</td><td><a href="javascript:viewEmpDeptBased(' + obj.ID + ');" class="btn btn-warning"><i class="fa fa-user" aria-hidden="true"></i></a>&nbsp;<a data-id="row-' + obj.ID + '" href="javascript:editRow(' + obj.ID + ');" class="btn btn-success"><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;<a href="javascript:removeRow(' + obj.ID + ');" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td></tr>');
						$('#add-modal').modal('hide');
						BootstrapDialog.alert('successfully added mainDept '+obj.mainDept+' and subDept ' + obj.subDept);
					}
				}).fail(function() { BootstrapDialog.alert('Unable to save data, please try again later.'); });
			});
		});
		// Edit row
		function editRow(id) {
			if ( 'undefined' != typeof id ) {
				$.getJSON('DepartmentActionbyHR.php?edit=' + id, function(obj) {
					//edit form input box value
					$('#edit-id').val(obj.ID);
					$('#maindept').val(obj.mainDept);
					$('#subdept').val(obj.subDept);
					$('#deptloc').val(obj.deptLocation);
					$('#deptmail').val(obj.deptMailID);
					$('#edit-modal').modal('show');
				}).fail(function() { BootstrapDialog.alert('Unable to fetch data, please try again later.') });
			} else BootstrapDialog.alert('Unknown row id.');
		}

		// Remove row
		function removeRow(id) {
			if ( 'undefined' != typeof id ) {
				 BootstrapDialog.confirm('Are you sure delete this department?', function(result){
			     	if(result) {
			        	$.get('DepartmentActionbyHR.php?remove=' + id, function() {
				      		//remove row from datatable
							$('a[data-id="row-' + id + '"]').parent().parent().remove();
							//refresh datatable after deleting record from database and datatable
							$('#example').DataTable().ajax.reload(null, false);
						}).fail(function() { BootstrapDialog.alert('Unable to fetch data, please try again later.') });
			      	}
				});
			} else BootstrapDialog.alert('Unknown row id.');
		}

		//to view department wise employee
		function viewEmpDeptBased(id){
			if ( 'undefined' != typeof id ) {
				/*$.getJSON('DepartmentActionbyHR.php?view=' + id, function(obj) {
					//var res = String(obj).split(",");
					//var message="<table class='table table-hover'><tr><td>" +res +"</td></tr></table>";
					//alert(res);
					//alert(res[0]);
					//view employee based on sub department row from datatable
					var res;
		      		//BootstrapDialog.alert("<strong>Employees List</strong> \n"+ res);
					/*var trHTML = '<table class="table table-hover" id="employeeList"><tr><th>Employee Name</th></tr>';
		       		$.each(obj, function (i, item) {
		       			//alert(obj[i]);
		       			
				    	trHTML += '<tr><td>' + obj[i] + '</td></tr>';
				    	trHTML +='</table>';
					    res = $('#employeeList').html(trHTML);
					   
				    });
		       	 BootstrapDialog.alert(res);*/
				/*}).fail(function() { BootstrapDialog.alert('Unable to view data, please try again later.') });*/
				$.get( "getSplLeaveOptions.php?view="+escape(id), function( data ) {
					 // var res=$( "#employeeList" ).html( data );
					 BootstrapDialog.alert(data);
				});
			}else BootstrapDialog.alert('Unknown row id.');
		}
		
	</script>
	</body>
</html>
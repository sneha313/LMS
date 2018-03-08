<?php
session_start();
require_once 'librarycopy1.php';
$db=connectToDB();
$emplocation=$_SESSION['u_emplocation'];
$fullname = $_SESSION['u_fullname'];
$empid=$_SESSION['u_empid'];
$empinfoquery='select * from emp where (empname = "'.$fullname.'");';
$emprows = $db->pdoQuery($empinfoquery)->results();
foreach($emprows as $emprow){
$emplocation=$emprow['location'];
$managername=$emprow['managername'];
$department=$emprow['dept'];
$role=$emprow['role'];
$managerlevel=$emprow['managerlevel'];
$joiningdate=$emprow['joiningdate'];
$emailid=$emprow['emp_emailid'];
$birthdaydate=$emprow['birthdaydate'];
}
$empprofile = 'select * from empprofile where (empid = "'.$empid.'");';
$rows = $db->pdoQuery($empprofile)->results();
foreach($rows as $row){
$fathername=$row['fathername'];
$phonenumber=$row['phonenumber'];
$bloodgroup=$row['bloodgroup'];
$address=$row['address'];
$currentdate=date("Y-m-d");
$date_diff = abs(strtotime($currentdate) - strtotime($joiningdate));
$years = floor($date_diff / (365*60*60*24));
$months = floor(($date_diff - $years * 365*60*60*24) / (30*60*60*24));
$days = floor(($date_diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
}
$subdepartmentquery="select d.subDept from departments d join emp e on e.dept=d.mainDept where e.empid=$empid";
$subdepartment = $db->pdoQuery($subdepartmentquery);
$managerid = $_SESSION['u_managerid'];

$teamquery="select * from emp where managerid = (SELECT managerid from emp where empid='".$_SESSION['u_empid']."')";
$teamres=$db->pdoQuery($teamquery)->results();
if(isset($_REQUEST['update']))
{
	$address = urldecode($_POST['personaladdress']);
	$address = addslashes($address);
	$phoneno = $_POST['phonenum'];
	$bloodgrp = $_POST['blg'];
	$father = $_POST['fathername'];
	$query='select * from empprofile where (empid = "'.$_SESSION['u_empid'].'");';
	$selectemp = $db->pdoQuery($query);
	$p=$selectemp -> count($sTable = 'empprofile', $sWhere = 'empid = "'.$_SESSION['u_empid'].'"' );
	if($p>0)
	{
		$dataArray = array('address'=>$address,'phonenumber'=>$phoneno,'bloodgroup'=>$bloodgrp,'fathername'=>$father);
		// where condition array
		$aWhere = array('empid'=>$_SESSION['u_empid']);
		// call update function
		$sql = $db->update('empprofile', $dataArray, $aWhere)->affectedRows();
		if($sql){
			echo "<script>BootstrapDialog.alert(' Record Updated Successfully')
					$('#loadempeditprofile').load('personalinfo.php?personalinfo=1'); 	
					</script>";
				
		} else {
			echo "<script>BootstrapDialog.alert('Record not Updated')
					$('#loadempeditprofile').load('personalinfo.php?personalinfo=1');
					</script>";
		}
	}
	else {
			$dataArray = array('empid'=>$_SESSION['u_empid'],'address'=>$address,'phonenumber'=>$phoneno,'fathername'=>$father,'bloodgroup'=>$bloodgrp);
			// use insert function
			$sql = $db->insert('empprofile',$dataArray)->getLastInsertId();
			if($sql){
			echo "<script>BootstrapDialog.alert('Record Inserted Successfully')
					$('#loadempeditprofile').load('personalinfo.php?personalinfo=1');
					</script>";
		} else {
			echo "<script>BootstrapDialog.alert('Record not Inserted')
					$('#loadempeditprofile').load('personalinfo.php?personalinfo=1');
					</script>";
		}
	}

}
else if(isset($_REQUEST['personalinfo'])) {
	$query='select * from empprofile where (empid = "'.$_SESSION['u_empid'].'");';
	$rs= $db->pdoQuery($query)->results();
	//$row1=$db->fetchArray($rs);
?>
<html>
	<head>
		
		<style>
			.form-control {
			    border: 0;
			  }
			  .form-control:focus {
			    border: 1px solid blue;
			  }
		</style>
	</head>
	<body>
		
		<div class="col-sm-12">
			<div class="panel panel-primary">			
				<div class="panel-heading text-center">
					<strong style="font-size:20px;">Personal Info</strong>
				</div>
				<!--panel body start-->
				<div class="panel-body">
					<form method="post" action="personalinfo.php?update=1" id="edit_emp_profile">
					<div class="form-group">
					<div class="row"><!--1st row start-->
						<div class="col-sm-2">
							<label>Emp Name:</label>
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="empname" value="<?php echo $fullname; ?>" readonly>
						</div>
						
						<div class="col-sm-2">
							<label>Father Name:</label>
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="fathername" id="fathername" value="<?php echo $fathername; ?>">
						</div>
					</div><!--1st row close-->
					</div>
						
					<div class="form-group">
					<div class="row"><!--2nd row start-->
						<div class="col-sm-2">
							<label>Email Id:</label>
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control" value="<?php echo $emailid; ?>" readonly>
						</div>
						<div class="col-sm-2">
							<label>Phone Number:</label>
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control"  name="phonenum" id='phonenum' value="<?php echo $phonenumber; ?>"required>
						</div>
						
					</div><!--2nd row close-->
					</div>
						
					<div class="form-group">
					<div class="row"><!--3rd row start-->
						<div class="col-sm-2">
							<label>Date of Birth:</label>
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control" value="<?php echo $birthdaydate; ?>" readonly>
						</div>
						<div class="col-sm-2">
							<label>Blood Group:</label>
						</div>
						<div class="col-sm-4">
						<!--  <input type="text" class="form-control" value="<?php echo $bloodgroup; ?>" readonly>-->
						<select class="form-control" name="blg" id="blg">
							<option>O+</option>
							<option>O-</option>
							<option>A+</option>
							<option>A-</option>
							<option>B+</option>
							<option>B-</option>
							<option>AB+</option>
							<option>AB-</option>
						</select>
						</div>
					</div><!--3rd row close-->
					</div>
							
					<div class="form-group">
					<div class="row"><!--4th row start-->
						<div class="col-sm-2">
							<label>Personal Address:</label>
						</div>
						<div class="col-sm-4">
							<textarea class="form-control" name="personaladdress" id="personaladdress" ><?php echo $address;?></textarea>
						</div>
					</div><!--4th row close-->
					</div>
					<div class="form-group">
					<div class="row"><!--6th row start-->
						<div class="col-sm-12 text-center">
							<input type='submit' class='btn btn-info' value='update' id='update_emp_profile' name='update'>
						</div>
					</div><!--7th row close-->
					</div>
				</form>
				</div><!--panel body div close-->
			</div><!--panel div close-->
			<script type="text/javascript">
				$("document").ready(function() {
				$("#edit_emp_profile").submit(function() { 
	   			$.ajax({ 
	       	 	data: $(this).serialize(), 
	       		 type: $(this).attr("method"), 
	       		 url: $(this).attr("action"), 
	        	success: function(response) {
	        	      $("#loadempeditprofile").html(response); 	
	        	}
			});
			return false; // cancel original event to prevent form submitting
			});
				//phone number validation
				$("#edit_emp_profile").validate({
			        rules: {
			        	phonenum: 'customphone'
			        }
			    });
				$.validator.addMethod('customphone', function (value, element) {
				    return this.optional(element) || /^(\+91-|\+91|0)?\d{10}$/.test(value);
				}, "Please enter a valid phone number");
			});
		</script>
			<?php }?>
			<!--panel div start-->
					<div class="panel panel-primary">
						<div class="panel-heading text-center">
							<strong style="font-size:20px;">Official Info</strong>
						</div>
						<!--panel body div start-->
						<div class="panel-body">
							<div class="form-group">
							<div class="row"><!--1st row start-->
								<div class="col-sm-2">
									<label>Emp Name:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $fullname; ?>" readonly>
								</div>
								<div class="col-sm-2">
									<label>Emp Id:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $empid; ?>" readonly>
								</div>
							</div><!--1st row close-->
							</div>
							
							<div class="form-group">
							<div class="row"><!--2nd row start-->
								<div class="col-sm-2">
									<label>Manager Name:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $managername; ?>" readonly>
								</div>
								<div class="col-sm-2">
									<label>Designation:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $role;?>" readonly>
								</div>
							</div><!--2nd row close-->
							</div>
							<div class="form-group">
							<div class="row"><!--4th row start-->
								<div class="col-sm-2">
									<label>Official Number:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $phonenumber; ?>" readonly>
								</div>
								<div class="col-sm-2">
									<label>Email Id:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $emailid; ?>" readonly>
								</div>
							</div><!--4th row close-->
							</div>
								
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Years In ECI:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php printf("%d Years, %d Months, %d Days", $years, $months, $days); ?>" readonly>
								</div>
								<div class="col-sm-2">
									<label>Department:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" readonly class="form-control" value="<?php echo $department;?>">
								</div>
								
							</div><!--5th row close-->
							</div>
							<div class="form-group">
							<div class="row"><!--3rd row start-->
								<div class="col-sm-2">
									<label>Team Member:</label>
								</div>
								<div class="col-sm-4">
									<table class="table table-bordered table-hover table-striped" style="margin-left:-20px">
										<tr class="success">
											<th>Name</th>
											<th>Location</th>
											<th>Group</th>
										</tr>
										<?php
										foreach($teamres as $team)
										// for($i=0;$i<$db->countRows($teamres);$i++)
							 				{
											//$team=$db->fetchAssoc($teamres);
											$teamname=$team['empname'];
											$teamempid=$team['empid'];
											
											echo '<tr><td>'.$team['empname'].'</td>
												<td>'.$team['location'].'</td>
												<td>'.$team['group'].'</td>
											</tr>';
										}?>
										
									</table>
								</div>
									<div class="col-sm-2">
									<label>Office Address:</label>
								</div>
								<div class="col-sm-4">
								<?php if ($_SESSION['u_emplocation']=="BLR") {?>
							
									<textarea class="form-control" id="address" readonly><?php echo "5th Floor ,Innovator Building, International Tech Park, Pattandur Agrahara Road, Whitefield, Bengaluru, Karnataka 560066" ?></textarea>
									
									<?php }?>
									<?php if ($_SESSION['u_emplocation']=="MUM") {?>
							
									<textarea class="form-control" id="address" readonly><?php echo "9, Rn Houe, 1, Azad Road, Gandhivali Andheri -, E, Gandhivali Andheri-, Mumbai, Maharashtra 400069" ?></textarea>
									
									<?php }?>
								</div>
							</div><!--3rd row close-->
							</div>
						 
						</div><!--panel body div close-->
					</div><!--panel div close-->
		</div>	<!--12 column close-->
					
		<!-- script is for, user will not be able to modify dropdown boxes -->
		<script>
			/*$(document).ready(function(){
				$('select option:not(:selected)').attr('disabled',true);
				//var loc = $_SESSION['u_emplocation'];
				//var k = $.session.get(‘u_emplocation’); 
				/*var k = $_REQUEST['emploc'];
				alert(k);
				 if (k == 'BLR') { */
					//$("#address").val("5th Floor ,Innovator Building, International Tech Park, Pattandur Agrahara Road, Whitefield, Bengaluru, Karnataka 560066");
				/* }
				 if(k=='MUM'){
					 $("#address").val("9, Rn Houe, 1, Azad Road, Gandhivali Andheri -, E, Gandhivali Andheri-, Mumbai, Maharashtra 400069");
				 }
				 else{
					 $("#address").val("no office address is updated here");
				 }
			});*/
		</script>
		
	</body>
</html>
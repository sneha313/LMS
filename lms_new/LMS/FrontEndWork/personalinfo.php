<?php
session_start();
require_once '../Library.php';
require_once '../attendenceFunctions.php';
error_reporting("E_ALL");
$db=connectToDB();
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
		<?php 
			$fullname = $_SESSION['u_fullname'];
			$empid=$_SESSION['u_empid'];
			$empinfo=$db->query("select * from emp where empname='".$fullname."'");
			$emprow=$db->fetchAssoc($empinfo);
			$emplocation=$emprow['location'];
			$managername=$emprow['managername'];
			$department=$emprow['dept'];
			$joiningdate=$emprow['joiningdate'];
			$emailid=$emprow['emp_emailid'];
			$birthdaydate=$emprow['birthdaydate'];
			$empprofile=$db->query("select * from empprofile where empid='".$empid."'");
			$row=$db->fetchAssoc($empprofile);
			$phonenumber=$row['phonenumber'];
			$bloodgroup=$row['bloodgroup'];
			$address=$row['address'];
			$yoe=date("Y-m-d")-$joiningdate;
			$subdepartment=$db->query("select d.subDept from departments d join emp e on e.dept=d.mainDept where e.empid=$empid");
		?>
		<div class="col-sm-11" style="margin-left:40px;">
			<div class="panel panel-primary">			
				<div class="panel-heading text-center">
					<strong style="font-size:20px;">Personal Info</strong>
				</div>
				<!--panel body start-->
				<div class="panel-body">
					<div class="form-group">
					<div class="row"><!--1st row start-->
						<div class="col-sm-2">
							<label>Emp Name:</label>
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="empname" value="<?php echo $fullname; ?>" readonly>
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
							<label>Company Name:</label>
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control" value="ECI" readonly>
						</div>
					</div><!--2nd row close-->
					</div>
							
					<div class="form-group">
					<div class="row"><!--3rd row start-->
						<div class="col-sm-2">
							<label>Department:</label>
						</div>
						<div class="col-sm-4">
							<select class="form-control">
								<option value=<?php echo $_SESSION['u_empid'];?>><?php echo $emprow['dept'];?></option>
								<option>--Choose Department--</option>
								<option>V&V</option>
								<option>ST</option>
								<option>DevTest</option>
								<option>AIBI</option>
								<option>HR</option>
								<option>T3</option>
								<option>LightSoft</option>
								<option>STMS</option>
								<option>ST</option>
								<option>GSC Tier</option>
								<option>GTD-V&V</option>
								<option>IT</option>
							</select>
						</div>
						<div class="col-sm-2">
							<label>Sub Department:</label>
						</div>
						<div class="col-sm-4">
							<select class="form-control">
								<option>--Sub Department--</option>
								<option>V&V</option>
								<option>V&V Automation</option>
								<option>V&V STMS</option>
								<option>Optics V&V Manual</option>
								<option>Optics V&V Manual and Regression</option>
								<option>ST-Apollo Optical App</option>
								<option>ST-GMPLS Infra & Protocols</option>
								<option>ST-MSPP HW</option>
								<option>ST-TND</option>
								<option>HR</option>
								<option>Admin</option>
								<option>HR</option>
								<option>DevTest Data</option>
								<option>AIBI Infrastructure</option>
								<option>LightSoft</option>
								<option>STMS Dev</option>
								<option>STMS Dev1</option>
								<option>STMS Dev2</option>
								<option>ST</option>
								<option>GSC Tier2</option>
								<option>GSC Tier3</option>
								<option>GTD-V&V Access</option>
								<option>GTD-V&V Mng</option>
								<option>IT- System Admin</option>
							</select>
						</div>
						</div><!--3rd row close-->
					</div>
						
					<div class="form-group">
					<div class="row"><!--4th row start-->
						<div class="col-sm-2">
							<label>Phone Number:</label>
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control" value="<?php echo $phonenumber; ?>">
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
					<div class="row"><!--5th row start-->
						<div class="col-sm-2">								
							<label>Years of Exp:</label>
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control" value="<?php echo $yoe; ?>" readonly>
						</div>
						<div class="col-sm-2">
							<label>Blood Group:</label>
						</div>
						<div class="col-sm-4">
							<select class="form-control" readonly value="<?php echo $bloodgroup; ?>">
								<option>--Blood grp--</option>
								<option>A+</option>
								<option>O+</option>
								<option>B+</option>
								<option>AB+</option>
								<option>A-</option>
								<option>O-</option>
								<option>B-</option>
								<option>AB-</option>
							</select>
						</div>
					</div><!--5th row close-->
					</div>
							
					<div class="form-group">
					<div class="row"><!--6th row start-->
						<div class="col-sm-2">
							<label>Date of Birth:</label>
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control" value="<?php echo $birthdaydate; ?>" readonly>
						</div>
						<div class="col-sm-2">
							<label>Address:</label>
						</div>
						<div class="col-sm-4">
							<textarea class="form-control"><?php echo $address;?></textarea>
						</div>
					</div><!--6th row close-->
					</div>
				</div><!--panel body div close-->
			</div><!--panel div close-->
		</div>	<!--11 column close-->
					
		<div class="col-sm-1"></div>
		<!-- script is for, user will not be able to modify dropdown boxes -->
		<script>
			$(document).ready(function(){
				$('select option:not(:selected)').attr('disabled',true);
			});
		</script>
	</body>
</html>
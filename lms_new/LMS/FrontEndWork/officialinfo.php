<?php
session_start();
require_once 'Library.php';
require_once 'attendenceFunctions.php';
require_once 'generalFunctions.php';
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
		<div class="container-fluid" style="margin-top:-20px;">
			<!--row start-->
			<div class="row">
				<!--11 column start-->
				<div class="col-sm-11" style="margin-left:40px; margin-top:20px;">
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
									<input type="text" class="form-control" value="<?php echo $fullname; ?>" >
								</div>
								<div class="col-sm-2">
									<label>Emp Id:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $empid; ?>">
								</div>
							</div><!--1st row close-->
							</div>
							
							<div class="form-group">
							<div class="row"><!--2nd row start-->
								<div class="col-sm-2">
									<label>Manager Name:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $managername; ?>">
								</div>
								<div class="col-sm-2">
									<label>Designation:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="">
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
									<label>Team Member:</label>
								</div>
								<div class="col-sm-4">
									<select class="form-control">
										<option>--Team Members--</option>
										<option>Giridhar Naga</option>
										<option>Anil Kumar Thatavarthi</option>
										<option>Anil Thippeswamy</option>
										<option>Apporva Shankar</option>
										<option>Harish Kumar</option>
										<option>Kavya Devraja</option>
										<option>Kumar Lachannagari</option>
										<option>Mamtha P V</option>
										<option>Naidile Basavegowda</option>
										<option>Navin Kumar</option>
										<option>Nidhin Manakkal Meethal</option>
										<option>Prabhakaran Senthamizhselvan</option>
										<option>Prathap Billava</option>
										<option>Roshin Kalariparambath</option>
										<option>Sneha Kumari</option>
										<option>Sumanth Saligram Venkatesh</option>
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
									<input type="text" class="form-control" value="<?php echo $emailid; ?>">
								</div>
							</div><!--4th row close-->
							</div>
								
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Years of Exp:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="<?php echo $yoe; ?>">
								</div>
								<div class="col-sm-2">
									<label>Office Address:</label>
								</div>
								<div class="col-sm-4">
									<textarea class="form-control" id="address"></textarea>
								</div>
							</div><!--5th row close-->
							</div>
						</div><!--panel body div close-->
					</div><!--panel div close-->
				</div><!--11 column close div-->	
			</div><!--row close-->
		</div><!--container-fluid div close-->
		<!-- script is for, user will not be able to modify dropdown boxes -->
		<script>
			$(document).ready(function(){
				$('select option:not(:selected)').attr('disabled',true);
				$("#address").val("5th Floor ,Innovator Building, International Tech Park, Pattandur Agrahara Road, Whitefield, Bengaluru, Karnataka 560066");
			});
		</script>
	</body>
</html>
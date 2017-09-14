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
			#hrjob{
				background:	#FFF5EE;
			}
		</style>
	</head>
	<body>
		<!--12 column start-->
		<div class="col-sm-12">
			<!--hr job panel start-->
			<div class="panel panel-success">
				<div class="panel-heading text-center">	
					<strong style="font-size:20px;">HR Section</strong>
				</div>
				<!-- hr job panel body start-->
				<div class="panel-body table-responsive" id="hrjob">
					<table class="table table-bordered">
						<tr>
							<td>
								<!--hr job panel start-->
								<div class="panel panel-primary">
									<div class="panel-heading">HR Jobs</div>
									<!--hr job panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
											   <td><a href="#">Add/Edit Employee Details </a></td>                                                                                       
												<td>HR can add or edit employee details</td>
											</tr>
											<tr>
												<td><a href="#">Apply Leave on behalf of Employee</a></td>                                                                                       
												<td>HR can apply leave on behalf of employee.</td>
											</tr>
											<tr>
	                                            <td><a href="#">Approve Employee Leaves</a></td>
	                                            <td>HR can approve employee pending leaves.</td>
	                                        </tr>
											<tr>
	                                            <td><a href="#">Modify Employee Approved Leaves</a></td>
	                                            <td>HR can modify employee approved leaves.</td>
											</tr>
											<tr>
	                                            <td><a href="#">Add Employee Inout Details</a></td>
	                                            <td>HR can add employee inout details</td>
	                                        </tr>
	                                        <tr>
	                                            <td><a href="#">Add Inout Details for All Employees</a></td>
	                                            <td>HR can add all employees inout details </td>
	                                        </tr>
											<tr>
	                                            <td><a href="#">View Balance Leaves for Employee</a></td>
	                                            <td>HR can view balance leaves for any employee</td>
	                                        </tr>
	                                    </table>
									</div><!--hr job panel close-->
								</div><!--hr job panel end-->
							</td>
							
	                        <td>
								<!--hr report panel start-->
								<div class="panel panel-danger">
	  								<div class="panel-heading">HR Reports</div>
									<!--hr report panel body start-->
	  								<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a href="#">Employee Leaves [Brief Report]</a></td>
												<td>HR can view employee brief report</td>
											</tr>
											<tr>
												<td><a href="#">Employee Leaves [Detailed Report]</a></td>
												<td>HR can view employee detailed report</td>
											</tr>
											<tr>
												<td><a href="#">Team Leave Report</a></td>
												<td>HR can view every team report</td>
											</tr>
											
										</table>
									</div><!--hr report panel body close-->
								</div><!--hr report panel close-->
								
								<!--apply leave by hr panel start-->
								<div class="panel panel-info">
									<div class="panel-heading">Apply Leave by HR</div>
									<!-- apply leave by hr panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a href="#">Apply special Leave</a></td>
												<td>HR can apply special leave for any particular employee</td>
											</tr>
											<tr>
												<td><a href="#">Apply Leave for Team</a></td>
												<td>HR can apply leave for any particular team</td>
											</tr>
										</table>
									</div><!-- apply leave by hr panel body close-->
								</div><!-- apply leave by hr panel close -->
										<!--apply leave by hr panel start-->
								<div class="panel panel-info">
									<div class="panel-heading">Update Departments by HR</div>
									<!-- apply leave by hr panel body start-->
									<div class="panel-body table-responsive">
										<table class="table table-bordered table-hover">
											<tr>
												<td><a id="department" href="DepartmentActionbyHR.php">Department List</a></td>
												<td>HR can add subdepartment, member in a department and delete subdepartment, if any team members are not present</td>
											</tr>
										</table>
									</div><!-- apply leave by hr panel body close-->
								</div><!-- apply leave by hr panel close -->
                        	</td>
						</tr>
					</table>
				</div><!--hr job panel body close-->
			</div><!--hr job panel close-->
                        	
		</div><!--12 column end-->
	</body>
</html>
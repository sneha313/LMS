<?php
	session_start();
	require_once 'Library.php';
	$db=connectToDB();
	require_once 'attendenceFunctions.php';
	require_once 'generalFunctions.php';
	
?>
<html>
	<head>
		<link rel="stylesheet" href="public/css/generateReport.css">
	</head>
	<body>
		<div id="loadingmessage"></div>
		<div class="panel panel-primary">
			<div class="panel-heading text-center">
				<strong style="font-size:20px;">Generate Attendance Report</strong>
			</div>
			<div class="panel-body">		
			  <form method="POST" action="" id="generateforHR" class="text-center">
			    <?php 
					if(strtoupper($_SESSION['user_dept'])=="HR"){?>
						<label class="radio-inline">
					      <input type="radio" name="HRreport" value="empid">Empid
					    </label>
					    <label class="radio-inline">
					      <input type="radio" name="HRreport" value="subdept">SubDept
					    </label>
					    <label class="radio-inline">
					      <input type="radio" name="HRreport" value="Maindept">MainDept
					    </label>
					    <label class="radio-inline">
					      <input type="radio" name="HRreport" value="allempreport">All
					    </label>
					<?php }
						$query=$db->query("select * from emp where empid='".$_SESSION['u_empid']."'");
						$res=$db->fetchAssoc($query);
						$role=$res['role'];
					?>
				</form>
				
				<?php 
					if(isset($_REQUEST['managerUser'])){
						echo '<form id="managerAttndReport">
								<div class="Managerempid">
								<div class="form-group">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-3">
										<label>From Date</label>
									</div>
									<div class="col-sm-5">
										<div class="input-group">
											<input type="text" id="datetimepicker" class="form-control open-datetimepicker" name="fromdate" value="'.add_day(-30, 'Y-m-d').'">
											<label class="input-group-addon btn" for="date">
												<span class="fa fa-calendar"></span>
											</label>.
										</div>
									</div>
									<div class="col-sm-2"></div>
								</div>
								</div>
								
								<div class="form-group">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-3">
										<label>To Date</label>
									</div>
									<div class="col-sm-5">
										<div class="input-group">
											<input type="text" id="Empidtodatepicker1" class="form-control open-datetimepicker1" name="todate" value="'.date('Y-m-d').'">
											<label class="input-group-addon btn" for="date">
												<span class="fa fa-calendar"></span>
											</label>
										</div>
									</div>
									<div class="col-sm-2"></div>
								</div>
								</div>
								<div class="form-group">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-3">
										<label>Mail List</label>
									</div>
									<div class="col-sm-5">
										<input type="text" class="form-control" name="mailList" id="mailList" value="'.$res['emp_emailid'].'" readonly>
									</div>
									<div class="col-sm-2"></div>
								</div>
								</div>
								
								<div class="form-group">
								<div class="row">
									<div class="col-sm-12 text-center">
										<input type="submit" class="btn btn-primary" value="submit" name="empsubmit" id="empsubmit">
									</div>
								</div>
								</div>
							</div>
						</form>';
					}
				?>
				<form id="empidAttndReport">
				<div class="empid Selected" style="display:none;">
					<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label>From Date</label>
						</div>
						<div class="col-sm-5">
							<div class="input-group">
								<input type="text" id="empidDatepicker" class="form-control open-datetimepicker" name="fromdate" value='<?php echo add_day(-30, 'Y-m-d') ?>'>
								<label class="input-group-addon btn" for="date">
									<span class="fa fa-calendar"></span>
								</label>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label>To Date</label>
						</div>
						<div class="col-sm-5">
							<div class="input-group">
								<input type="text" id="empidDatepicker1" class="form-control open-datetimepicker1" name="todate" value='<?php echo date('Y-m-d')  ?>'>
								<label class="input-group-addon btn" for="date">
									<span class="fa fa-calendar"></span>
								</label>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label>Mail List</label>
						</div>
						<div class="col-sm-5">
							<select class="form-control" id="empMail" name="empMail" multiple="multiple">
								<?php
									$sql = $db->query("select distinct emp_emailid, empid from emp where state='Active'");
									for ($i=0;$i<$db->countRows($sql);$i++)
									{
										$result = $db->fetchArray($sql);
										echo "<option value='".$result['empid']."'>".$result['emp_emailid']."</option>";	
									}
								?>
							</select>
						</div>
						<div class="col-sm-2 EmpidMailhelp">
							<a id="EmpAttndReporthelp"><i class="fa fa-question-circle" data-aria-hidden="true"></i><b> Help</b></a>
							<span class="EmpidMailhelpText">All ECI Employee's mail are in drop down list, you can select multiple employee at a time.</span>
						</div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row">
						<div class="col-sm-12 text-center">
							<input type="submit" class="btn btn-primary" value="submit" name="empsubmit" id="empsubmit">
						</div>
					</div>
					</div>
				</div>
				</form>
				
				<form id="SubDept" method="POST">
				<div class="subdept Selected" style="display:none;">
					<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="subDeptLoc">Select Location</label>
						</div>
						<div class="col-sm-5">
							<select class="form-control" name="subDeptLoc" id="subDeptLoc">
							<option value='Choose' selected>Choose Location</option>
							<?php 
								$sql = $db->query("SELECT DISTINCT e.location FROM emp e, departments d where d.deptStatus='Active'");
								for ($i=0;$i<$db->countRows($sql);$i++)
								{
									$result = $db->fetchArray($sql);
									echo "<option value='".$result['location']."'>".$result['location']."</option>";	
								}
							?>
							</select>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row" id="subDeptId" style="display:none;">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="subDepartment">Sub Department</label>
						</div>
						<div class="col-sm-5">
							<?php echo '<select class="form-control" name="subDepartment" id="subDepartment" size="0">'. $department .'</select>';?>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					<?php 
						//$query=$db->query("select * from emp where ")
					?>
					<div class="form-group">
					<div class="row" id="fromdateid" style="display:none;">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label>From Date</label>
						</div>
						<div class="col-sm-5">
							<div class="input-group">
								<input type="text" id="SubDeptDatepicker" class="form-control open-datetimepicker" name="fromdate" value='<?php echo add_day(-30, 'Y-m-d') ?>'>
								<label class="input-group-addon btn" for="date">
									<span class="fa fa-calendar"></span>
								</label>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row" id="todateid" style="display:none;">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label>To Date</label>
						</div>
						<div class="col-sm-5">
							<div class="input-group">
								<input type="text" id="SubDeptDatepicker1" class="form-control open-datetimepicker1" name="todate" value='<?php echo date('Y-m-d')  ?>'>
								<label class="input-group-addon btn" for="date">
									<span class="fa fa-calendar"></span>
								</label>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row" id="maillistid" style="display: none;">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label>Mail List</label>
						</div>
						<div class="col-sm-5">
							<select class="form-control" id="subDeptMail" name="subDeptMail" multiple="multiple">
								<?php
									$sql = $db->query("select distinct emp_emailid, empid from emp where state='Active'");
									for ($i=0;$i<$db->countRows($sql);$i++)
									{
										$result = $db->fetchArray($sql);
										echo "<option value='".$result['empid']."'>".$result['emp_emailid']."</option>";	
									}
								?>
							</select>
						</div>
						<div class="col-sm-2">
							<div class="SubDeptHelptooltip"><a id="subDepthelp"><i class="fa fa-question-circle" data-aria-hidden="true"></i><b> Need Help</b></a>
								<Span class="SubDeptHelptooltiptext">All ECI Employee's mail are available in dropdown, you can select multiple employee at a time. </Span>
							</div>
						</div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row">
						<div class="col-sm-12 text-center">
							<input type="submit" class="btn btn-primary" value="submit" name="subdeptSubmit" id="subdeptSubmit">
						</div>
					</div>
					</div>
				</div>
				</form>
				<form id="MainDept" method="POST">
				<div class="Maindept Selected" style="display:none;">
					
					<div class="form-group">
					<div class="row" id="mainDepartmentLoc">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="mainDeptLoc">Select Location</label>
						</div>
						<div class="col-sm-5">
							<select class="form-control" id="mainDeptLoc" name="mainDeptLoc">
							<option value='Choose' selected>Choose Location</option>
							<?php 
								$sql = $db->query("SELECT DISTINCT e.location FROM emp e, departments d where d.deptStatus='Active'");
								for ($i=0;$i<$db->countRows($sql);$i++)
								{
									$result = $db->fetchArray($sql);
									echo "<option value='".$result['location']."'>".$result['location']."</option>";	
								}
							?>
							</select>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row" id="maindepartment" style="display:none;">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="mainDept">Main Dept</label>
						</div>
						<div class="col-sm-5">
							<select class="form-control" id="mainDepartment" name="mainDepartment">
								<option value="">--Select Main Department--</option>
							<?php 
								$query=$db->query("SELECT DISTINCT mainDept FROM `departments` ");
								//$row=$db->fetchAssoc($query);
								//$subdept=$row['subDept'];
								while($row=$db->fetchAssoc($query))
								{
							?>
							<option value="<?php echo $row['mainDept']?>"><?php echo $row['mainDept'] ?></option>
							<?php }?>
							</select>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					<div class="form-group">
					<div class="row" id="mainDeptfromdate" style="display:none;">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="mainfromdate">From Date</label>
						</div>
						<div class="col-sm-5">
							<div class="input-group">
								<input type="text" id="AllEmpDatepicker" class="form-control open-datetimepicker" name="fromdate" value='<?php echo add_day(-30, 'Y-m-d') ?>'>
								<label class="input-group-addon btn" for="date">
									<span class="fa fa-calendar"></span>
								</label>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row" style="display:none;" id="mainDepttodate">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="maintodate">To Date</label>
						</div>
						<div class="col-sm-5">
							<div class="input-group">
								<input type="text" id="AllEmpDatepicker1" class="form-control open-datetimepicker1" name="todate" value='<?php echo date('Y-m-d')  ?>'>
								<label class="input-group-addon btn" for="date">
									<span class="fa fa-calendar"></span>
								</label>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row" id="mainDeptMail" style="display:none;">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="mainDeptMailList">Mail List</label>
						</div>
						<div class="col-sm-5">
							<select class="form-control" id="MaindeptmailList" name="MaindeptmailList" multiple="multiple">
								<?php
									$sql = $db->query("select distinct emp_emailid, empid from emp where state='Active'");
									for ($i=0;$i<$db->countRows($sql);$i++)
									{
										$result = $db->fetchArray($sql);
										echo "<option value='".$result['empid']."'>".$result['emp_emailid']."</option>";	
									}
								?>
							</select>
							
						</div>
						<div class="col-sm-2 MainDeptMailhelp">
							<a id="mainDeptMailListhelp"><i class="fa fa-question-circle" data-aria-hidden="true"></i><b> Need Help</b></a>
							<span class="MainDeptMailhelpText">All ECI Employee's Email are in drop down list, you can select multiple employee at a time.</span>
						</div>
					</div>
					</div>
					<div class="form-group">
					<div class="row">
						<div class="col-sm-12 text-center">
							<input type="submit" class="btn btn-primary" value="submit" name="maindeptSubmit" id="maindeptSubmit">
						</div>
					</div>
					</div>
				</div>
				</form>
				
				<form id="AllEmpAttndReport" method="POST">
				<div class="allempreport Selected" style="display:none;">
					<div class="form-group">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label for="locationforAllEmp"> Select Location</label>
						</div>
						<div class="col-sm-5">
							<select class="form-control" id="locationforAllEmp" name="locationforAllEmp">
								<option value='Choose' selected>Choose Location</option>
							<?php 
								$sql = $db->query("SELECT DISTINCT e.location FROM emp e, departments d where d.deptStatus='Active'");
								for ($i=0;$i<$db->countRows($sql);$i++)
								{
									$result = $db->fetchArray($sql);
									echo "<option value='".$result['location']."'>".$result['location']."</option>";	
								}
							?>
							</select>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row" style="display:none;" id="fromdateAllEmp">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label>From Date</label>
						</div>
						<div class="col-sm-5">
							<div class="input-group">
								<input type="text" id="AllEmpfromdatepicker" class="form-control open-datetimepicker1" name="fromdate" value='<?php echo add_day(-30, 'Y-m-d') ?>'>
								<label class="input-group-addon btn" for="date">
									<span class="fa fa-calendar"></span>
								</label>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row" id="todateAllEmp" style="display:none;">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label>To date</label>
						</div>
						<div class="col-sm-5">
							<div class="input-group">
								<input type="text" id="AllEmptodatepicker1" class="form-control open-datetimepicker1" name="todate" value='<?php echo date('Y-m-d')  ?>'>
								<label class="input-group-addon btn" for="date">
									<span class="fa fa-calendar"></span>
								</label>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row" id="AllEmpMailList" style="display:none;">
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<label>Mail List</label>
						</div>
						<div class="col-sm-5">
							<select class="form-control" id="AllMailList" name="AllMailList" multiple="multiple">
							<?php 
								$query=$db->query("select emp_emailid, empid from emp where state='Active'");
								for($i=0;$i<$db->countRows($query);$i++)
								{
									$res=$db->fetchAssoc($query);
									$mail=$res['emp_emailid'];
								
							?>
							
								<option value="<?php echo $res['empid']?>"><?php echo $res['emp_emailid'] ?></option>
							
							<?php }?>
							</select>
						</div>
						<div class="col-sm-2 AllEmpMailhelp">
							<a id="AllEmpMailListhelp"><i class="fa fa-question-circle" data-aria-hidden="true"></i><b> Help</b></a>
							<span class="AllEmpMailhelpText">All ECI Employee's Mail are in Dropdown, you can select multiple employee at a time. </span>
						</div>
					</div>
					</div>
					
					<div class="form-group">
					<div class="row">
						<div class="col-sm-12 text-center">
							<input type="submit" value="submit" class="btn btn-primary">
						</div>
					</div>
					</div>
				</div>
				</form>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
			    $('input[type="radio"]').click(function(){
			    	//$('.radio-inline').hide();
			        var inputValue = $(this).attr("value");
			        var targetBox = $("." + inputValue);
			        $(".Selected").not(targetBox).hide();
			        $(targetBox).show();
			    });
			    
		        $('#subDeptMail').multiselect({
		            enableFiltering: true,
		            includeSelectAllOption: true,
		            filterPlaceholder: 'Search for something...'
		        });
		        $('#MaindeptmailList').multiselect({
		            enableFiltering: true,
		            includeSelectAllOption: true,
		            filterPlaceholder: 'Search for something...'
		        });
		        $('#AllMailList').multiselect({
		        	enableFiltering: true,
		            includeSelectAllOption: true,
		            filterPlaceholder: 'Search for something...'
				});
				$('#empMail').multiselect({
		        	enableFiltering: true,
		            includeSelectAllOption: true,
		            filterPlaceholder: 'Search for something...'
				});
			});
			$(".open-datetimepicker").datepicker({
				dateFormat: 'yy-mm-dd',
				minView : 2,
               	buttonImageOnly: true,
               	orientation: "auto",
              	autoclose: true
			}); 
		    $(".open-datetimepicker1").datepicker({
			    dateFormat: 'yy-mm-dd',
			    minView : 2,
               	buttonImageOnly: true,
               	orientation: "auto",
              	autoclose: true
			});   
		    $("#subDeptLocation").change(function() {
				 var location = $("#subDeptLocation").val();
				 $.post("getSplLeaveOptions.php?subDeptLocation="+escape(location),function(data) {
					$("#subDeptMail").empty();
					$("#subDeptMail").append(data);
				 });
			});     
		    $("#subDeptLoc").change(function() {
				 var location = $("#subDeptLoc").val();
				 $.post("getSplLeaveOptions.php?subDeptLoc="+escape(location),function(data) {
					$("#subDepartment").empty();
					$("#subDepartment").append(data);
				 });
			});
		    $("#subDeptLoc").change(function(){
				if($("#subDeptLoc").val()=="Choose") {
					$("#subDeptId").hide();
					$("#fromdateid").hide();
					$("#todateid").hide();
					$("#maillistid").hide();
				} else {
					$("#subDepartment").val($("#subDeptLoc").val());
					$("#subDeptId").show();
					$("#fromdateid").show();
					$("#todateid").show();
					$("#maillistid").show();
				}
			});

		    $("#mainDeptLoc").change(function() {
				 var location = $("#mainDeptLoc").val();
				 $.post("getSplLeaveOptions.php?mainDeptLoc="+escape(location),function(data) {
					$("#mainDepartment").empty();
					$("#mainDepartment").append(data);
				 });
			});
			
			$("#mainDeptLoc").change(function(){
				if($("#mainDeptLoc").val()=="Choose") {
					$("#maindepartment").hide();
					$("#mainDeptfromdate").hide();
					$("#mainDepttodate").hide();
					$("#mainDeptMail").hide();
				} else {
					$("#mainDepartment").val($("#mainDeptLoc").val());
					$("#maindepartment").show();
					$("#mainDeptfromdate").show();
					$("#mainDepttodate").show();
					$("#mainDeptMail").show();
				}
			});

			$("#locationforAllEmp").change(function(){
				if($("#locationforAllEmp").val()=="Choose") {
					$("#fromdateAllEmp").hide();
					$("#todateAllEmp").hide();
					$("#AllEmpMailList").hide();
				} else {
					$("#subDepartment").val($("#locationforAllEmp").val());
					$("#fromdateAllEmp").show();
					$("#todateAllEmp").show();
					$("#AllEmpMailList").show();
				}
			});
		</script>
	</body>
</html>
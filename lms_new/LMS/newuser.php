<?php
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
$db=connectToDB();
?>
<html>
<head>
<script type="text/javascript">
$("document").ready( function () {
	$('#addempform').submit(function() {
		$(this).find(':input[type=submit]').replaceWith('<center><img src="public/img/loader.gif" class="img-responsive" alt="processing"/></center>');
	});

	$('#editempform2').submit(function() {
		$(this).find(':input[type=submit]').replaceWith('<center><img src="public/img/loader.gif" class="img-responsive" alt="processing"/></center>');
	});

	jQuery(function() {
        jQuery('#empusername').autocomplete({
            minLength: 1,
	    autoFocus: true,
            source: function(request, response) {
                jQuery.getJSON('autocomplete/Users_JSON.php', {
                    term: request.term
                }, response)
            },
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            select: function(event, ui) {
                this.value = ui.item.value;
                return false;
            }
        });
    });
  	$('#delempform').submit(function() {
  		var r=BootstrapDialog.confirm("Delete Employee!");
		if (r==true)
		{
  				$.ajax({ 
  				        data: $(this).serialize(), 
  				        type: $(this).attr('method'), 
  				        url: $(this).attr('action'), 
  				        success: function(response) { 
  				            $('#loadhrsection').html(response); 
  				        }
  						});
  				return false;
 		}
  		else {
  			BootstrapDialog.alert("You pressed Cancel!");
  			return false;
		}
  	});
  	
  //form validation rules
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
  	    },
  	    submitHandler: function() {
  			$.ajax({ 
	        data: $('#addempform').serialize(), 
	        type: $('#addempform').attr('method'), 
	        url:  $('#addempform').attr('action'), 
	        success: function(response) { 
	            $('#loadhrsection').html(response); 
	        }
			});
			return false;
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
            },
            submitHandler: function() {
              $.ajax({ 
                data: $('#editempform2').serialize(), 
                type: $('#editempform2').attr('method'), 
                url:  $('#editempform2').attr('action'), 
                success: function(response) { 
                    $('#loadhrsection').html(response); 
                }
              });
                        return false;
          }
          });
 	$("#add_emp").click(function(){
 		$("#loadhrsection").load('newuser.php?add_emp=1');
	});
	<?php 
	getSubmitJs("editempform1","loadhrsection");
	?>
	$("#edit_emp").click(function(){
		$("#loadhrsection").load('newuser.php?edit_emp=1');
	});
	$("#del_emp").click(function(){
		$("#loadhrsection").load('newuser.php?del_emp=1');
	});
	$("#view_emp").click(function(){
		$("#loadhrsection").load("eciemp.php");
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
});
</script>
<style type="text/css">
#add_emp {
cursor: pointer;
}
#edit_emp{
cursor: pointer;
}
#del_emp {
cursor: pointer;
}
#view_emp {
cursor: pointer;
}
</style>
</head>
<body>
<?php 
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

if(isset($_REQUEST['userlinks']))
{
	echo "<div class='panel panel-primary'>
			<div class='panel-heading text-center'>
				<strong style='font-size:20px;'>Employee Details</strong>
			</div>
			<div class='panel-body'>
				<table class='table table-bordered table-hover'>
					<tr>
						<td><a id='add_emp'>Add Employee </a></td>                                                                                       
						<td>HR can add new employee details.</td>
					</tr>
					<tr>
						<td><a id='edit_emp'>Edit Employee </a></td>                                                                                       
						<td>HR can edit existing employee details.</td>
					</tr>
					<tr>
	               		<td><a id='del_emp'>Delete Employee </a></td>
	                	<td>HR can delete employee, if employee no longer belongs to ECI.</td>
	             	</tr>
					<tr>
	               		<td><a id='view_emp'>View Employee Details</a></td>
	                	<td>HR can view all the employee details.</td>
					</tr>
	          	</table>
			</div>
         </div>";
}
if(isset($_REQUEST['showleaves']))
{
		$isEmpPresentquery="SELECT *FROM  `emp` WHERE empid =  '".$_POST['newempid']."'";
		$isEmpPresent=$db->query($isEmpPresentquery);
		if($db->hasRows($isEmpPresent)) {
			echo "<script>BootstrapDialog.alert('".$_POST['newempname']." (".$_POST['newempid'].") is already presnt in database.')</script>";
		}
		else {
		$manager=$db->query("select empid,empusername,empname,emp_emailid from emp where empid='".$_POST['managername']."' and state='Active'");
		$managerrow=$db->fetchAssoc($manager);
		//Insert emp details in emp table
	    $insertemp="INSERT INTO`emp` (`empid`,`empusername`,`empname`,`joiningdate`,
	    			`birthdaydate`,`dept`,`managerid`,`managerusername`,`managername`,
	    			`role`,`managerlevel`,`group`,`emp_emailid`,`manager_emailid`,`location`)
		 			VALUES ('".$_POST['newempid']."','".$_POST['newempusername']."', 
		 			'".$_POST['newempname']."','".$_POST['joiningdate']."','".$_POST['birthdate']."',
		 			'".$_POST['dept']."','".$managerrow['empid']."','".$managerrow['empusername']."',
		 			'".$managerrow['empname']."','".$_POST['newrole']."','".$_POST['hideaddmanagerlevel']."',
					'".$_POST['newgroup']."',
		 			'".$_POST['newemail']."','".$managerrow['emp_emailid']."','".$_POST['location']."')";
	    //Insert emp totalleaves
		$joiningmonth = date("m",strtotime($_POST['joiningdate']));
		$joiningday = date("d",strtotime($_POST['joiningdate']));
		$joiningyear = date("Y",strtotime($_POST['joiningdate']));
		$remainingmonths = 12 -	$joiningmonth;
		$balanceLeaves=$remainingmonths * (1.9);
		if($joiningday<15) { $balanceLeaves=$balanceLeaves+1.9; }
		else {$balanceLeaves=$balanceLeaves+1; }
		$balanceLeaves=ceil($balanceLeaves);
		echo "<div class='panel panel-primary'>
				<div class='panel-heading text-center'>
					<strong style='font-size:20px;'>Employee Leaves Balance</strong>
				</div>
				<div class='panel-body'>
				  <div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
	  	        			<label for='showempid'>Employee Id:</label>
	  	        		</div>
		    	  		<div class='col-sm-5'>
	  	        			<input type='text' class='form-control' size='20' readonly='true' value='".$_POST['newempid']."'/>
	                	</div>
	                	<div class='col-sm-2'></div>
	                </div>
	          	</div>
		    	 <div class='panel-body'>
				  <div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
	                		<label for='showcf'>Employee User Name:</label>
	                	</div>
		    	  		<div class='col-sm-5'>
	                		<input type='text' class='form-control' size='20' readonly='true' value='".$_POST['newempusername']."'/>
	                	</div>
	                	<div class='col-sm-2'></div>
	                </div>
	            </div>
		    	<div class='panel-body'>
				  <div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
	                		<label for='showcf'>Carry Forwarded:</label>
	                	</div>
		    	  		<div class='col-sm-5'>
	                		<input type='text' class='form-control' size='20' readonly='true' value='0'/>
	                	</div>
	                	<div class='col-sm-2'></div>
	               	</div>
	          	</div>
		    	<div class='panel-body'>
				  <div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
	                		<label for='showbl'>Balance Leaves:</label>
	                	</div>
		    	  		<div class='col-sm-5'>
	                		<input type='text' class='form-control' size='20' readonly='true' value='".$balanceLeaves."'/>
	                    </div>
	                    <div class='col-sm-2'></div>
				 	 </div>
	        	</div>
			</div>
  		</div>";
		$insertquery="INSERT INTO`emptotalleaves` (`empid` ,`carryforwarded` ,`previous year` ,`balanceleaves` ,`present year`)
		 VALUES ('".$_POST['newempid']."', 0, '".(($joiningyear)-1)."', '".$balanceLeaves."', '".$joiningyear."')";
		$insert=$db->query($insertquery);
		$indertedemp=$db->query($insertemp);
		if($insert) {
		  echo "<script>BootstrapDialog.alert('".$_POST['newempname']." (".$_POST['newempid'].") added successfully in database.')</script>";
		}else {
		 	echo "<script>BootstrapDialog.alert('".$_POST['newempname']." (".$_POST['newempid'].") is not added in database.')</script>";		  	
		}
	}
}

if(isset($_REQUEST['add_emp']))
{
	echo "<form id='addempform' method='POST' action='newuser.php?showleaves=1'>
		<div class='panel panel-primary'>
			<div class='panel-heading text-center'>
				<strong style='font-size:20px;'>Add New Employee</strong>
			</div>
			<div class='panel-body'>
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
							<div class='input-group'>
								<input type='text' class='form-control open-datetimepicker' name='joiningdate' id='joiningdate' size='20' />
								<label class='input-group-addon btn' for='date'>
									<span class='fa fa-calendar open-datetimepicker'></span>
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
							<div class='input-group'>
								<input type='text' class='form-control open-datetimepicker1' name='birthdate' id='birthdate' size='20' />
								<label class='input-group-addon btn' for='date'>
									<span class='fa fa-calendar open-datetimepicker1'></span>
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
    	  				<div class='col-sm-5' id='dept'>".getSelectBoxOptions("select distinct(dept) from emp","dept","dept","newdept","")."</div>
						<div class='col-sm-2'></div>
					</div>
				</div>";
		  echo "<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'><label for='newmanager'>Manager:</label></div>
    	  				<div class='col-sm-5' id='getmanager'>".getSelectBoxOptions("select distinct(empname) as managername,empid from emp where state='Active' and (role='manager' or role='Manager')","empid","managername","newmanager","")."</div>
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
				<div class='form-group'>
					<div class='row' id='addmanagerlevel' style='display:none'>
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
			</div>
		</div>
	</form>
         <script>
			$(document).ready(function(){
				$('.open-datetimepicker').datetimepicker({
					format: 'yyyy-mm-dd',
		           	minView : 2,
		          	autoclose: true     
				});
				$('.open-datetimepicker1').datetimepicker({
					format: 'yyyy-mm-dd',
                   	minView : 2,
                  	autoclose: true  
				});
			});
		</script>";
}
if(isset($_REQUEST['edit_emp']))
{
	echo "<form id='editempform1' method='POST' action='newuser.php?editempdetails=1'>
		<div class='row'> 
			<div class='col-sm-1'></div>
			<div class='col-sm-3'><label for='empusername'>Enter Employee User Name:</label></div>
    	  	<div class='col-sm-4'><input type='text' class='form-control' name='empname' id='empusername' size='20' /></div>
    	  	<div class='col-sm-3'><input class='submit btn btn-primary' type='submit' name='submit' value='Edit'/></div>
    	  	<div class='col-sm-1'></div>
		  </div>
	</form>";
	
}
if(isset($_REQUEST['editempdetails']))
{
	$getEmpDetails=$db->query("select * from emp where empname='".$_POST['empname']."' and state='Active'");
	$row=$db->fetchAssoc($getEmpDetails);
	echo "<form id='editempform2' method='POST' action='newuser.php?submitdetails=1'>
		<div class='panel panel-primary'>
			<div class='panel-heading text-center'>
				<strong style='font-size:20px;'>Edit Employee Details</strong>
			</div>
			<div class='panel-body'>
		  		<div class='form-group'>
					<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
                			<label for='editempid'>Employee Id:</label>
                		</div>
    	  				<div class='col-sm-5'>
                			<input type='text' class='form-control' name='editempid' id='editempid' size='20' value='".$row['empid']."' readonly='true' />
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
    	  					<input type='text' class='form-control' name='editempusername' id='editempusername' size='20' value='".$row['empusername']."'/>
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
                			<input type='text' class='form-control' name='editempname' id='editempname' size='20' value='".$row['empname']."'/>
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
    	  					<div class='input-group'>
								<input type='text' class='form-control open-datetimepicker2' name='joiningdate' id='joiningdate' size='20' value='".$row['joiningdate']."'/>
								<label class='input-group-addon btn' for='date'>
									<span class='fa fa-calendar open-datetimepicker2'></span>
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
							<label for='birthdate'>Birth Date:</label>
						</div>
    	  				<div class='col-sm-5'>
    	  					<div class='input-group'>
								<input type='text' class='form-control open-datetimepicker3' name='birthdate' id='birthdate' size='20' value='".$row['birthdaydate']."'/>
								<label class='input-group-addon btn' for='date'>
									<span class='fa fa-calendar open-datetimepicker3'></span>
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
				       		<SELECT class='form-control' name='hideeditmanagerlevel'>
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
				       		<input type='text' class='form-control' name='editgroup' id='editgroup' size='20' value='".$row['group']."' />
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
							<input type='text' class='form-control' name='editemail' id='editemail' size='20' value='".$row['emp_emailid']."'/>
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
			</div>
   		</div>
  	</form>
	<script>
		$(document).ready(function(){
			$('.open-datetimepicker2').datetimepicker({
				format: 'yyyy-mm-dd',
		       	minView : 2,
		       	autoclose: true     
			});
			$('.open-datetimepicker3').datetimepicker({
				format: 'yyyy-mm-dd',
              	minView : 2,
               	autoclose: true  
			});
		});
	</script>";
}
if(isset($_REQUEST['submitdetails']))
{
	$result=$db->query("select empid,empusername,empname,emp_emailid from emp where empid='".$_POST['managername']."' and state='Active'");
	$row=$db->fetchAssoc($result);	
	$updateemp="UPDATE `emp` SET `empusername`='".$_POST['editempusername']."',`empname`='".$_POST['editempname']."',
				`joiningdate`='".$_POST['joiningdate']."',`birthdaydate`='".$_POST['birthdate']."',
				`dept`='".$_POST['dept']."',`managerid`='".$_POST['managername']."',
				`managerusername`='".$row['empusername']."',`managername`='".$row['empname']."',
				`role`='".$_POST['editrole']."',`managerlevel`='".$_POST['hideeditmanagerlevel']."',
				`group`='".$_POST['editgroup']."',
				`emp_emailid`='".$_POST['editemail']."',`manager_emailid`='".$row['emp_emailid']."',
				`location`='".$_POST['location']."'
				 where `empid`='".$_POST['editempid']."'";
	
	$update=$db->query($updateemp);
	if($update) {
		  	echo "<script>BootstrapDialog.alert('".$_POST['editempname']." (".$_POST['editempid'].") Updated successfully in database.')</script>";
	}
}
if(isset($_REQUEST['del_emp']))
{
		echo "<form id='delempform' method='POST' action='newuser.php?delempdetails=1'>
		  	<div class='row'> 
				<div class='col-sm-1'></div>
				<div class='col-sm-3'>
					<label for='empusername'>Enter Employee User Name:</label>
				</div>
    	  		<div class='col-sm-4'>
					<input type='text' class='form-control' name='empusername' id='empusername' size='20' />
				</div>
    	  		<div class='col-sm-3'>
					<input class='submit btn btn-primary' type='submit' name='submit' id='delemp' value='Delete'/>
				</div>
				<div class='col-sm-1'></div>
    	  	</div>
		</div>
	</form>";	
}
if(isset($_REQUEST['delempdetails']))
{
	$sql=$db->query("SELECT empid from emp where empname='".$_POST['empusername']."' and state='Active'");
	$row=$db->fetchassoc($sql);
	// Set Emp state as inactive
	$setEmpState=$db->query("UPDATE `emp` SET `state` = 'InActive' WHERE `empid` = '".$row['empid']."'");
#	//Delete entry in empleavetransactions
#	$empleavetransactions=$db->query("DELETE FROM `empleavetransactions` WHERE `empid` = '".$row['empid']."'");
#	//Delete entry in empprofile
#	$empprofile=$db->query("DELETE FROM `empprofile` WHERE `empid` = '".$row['empid']."'");
#	//Delete entry in empsplleavetaken
#	$empsplleavetaken=$db->query("DELETE FROM `empsplleavetaken` WHERE `empid` = '".$row['empid']."'");
#	//Delete entry in  emptotalleaves
#	$emptotalleaves=$db->query("DELETE FROM `emptotalleaves` WHERE `empid` = '".$row['empid']."'");
	//Delete entry in perdaytransactions
#	$perdaytransactions=$db->query("DELETE FROM `perdaytransactions` WHERE `empid` = '".$row['empid']."'");
	//Delete entry in emp
#	$delemp=$db->query("DELETE FROM `emp` WHERE `empid` = '".$row['empid']."'");
	if($setEmpState)
	{
  		echo "<script>BootstrapDialog.alert('Employee is set to InActive state.')</script>";
	}
	else {
		echo "<script>BootstrapDialog.alert('Employee is not set to InActive state.')</script>";
	}	
}


?>
</body>
</html>

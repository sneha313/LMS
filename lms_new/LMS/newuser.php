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
	$("#view_emp").click(function(){
		$("#loadhrsection").show("fast",function() {	
			window.location="eciemp.php";
		});
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
  		var r=confirm("Delete Employee!");
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
  			alert("You pressed Cancel!");
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

#edit_emp {
cursor: pointer;
}

#del_emp {
cursor: pointer;
}

#view_emp {
cursor: pointer;
}

</style>
<?php 
$getCalIds = array("joiningdate","birthdate");
$calImg=getCalImg($getCalIds,"-2","0");
echo $calImg;
?>
</head>
<body>
<?php 
function getSelectBoxOptions($query,$value,$option,$id,$default)
{
	global $db;
	$selectstr="";
	$selectstr.="<select name='$option' id=$id'>";
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
	echo "<ul>";
	echo "<li><a id='add_emp''>Add Employee </a></li><br>";
	echo "<li><a id='edit_emp''>Edit Employee </a></li><br>";
	echo "<li><a id='del_emp''>Delete Employee </a></li><br>";
	echo "<li><a id='view_emp''>View Employee Details</a></li><br>";
	echo "</ul>";
}
if(isset($_REQUEST['showleaves']))
{
		$isEmpPresentquery="SELECT *FROM  `emp` WHERE empid =  '".$_POST['newempid']."'";
		$isEmpPresent=$db->query($isEmpPresentquery);
		if($db->hasRows($isEmpPresent)) {
			echo "<script>alert('".$_POST['newempname']." (".$_POST['newempid'].") is already presnt in database.')</script>";
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
		echo "<h2><center><u>Employee Leaves Balance</u></center></h2><br>";
		echo "<table id='table-2'>
		  <tbody>
		  <tr>
		  <td><label for='showempid'>Emp Id:</label></td>
    	  <td><input type='text' size='20' readonly='true' value='".$_POST['newempid']."'/></td></tr>
    	  <td><label for='showcf'>Employee User Name</label></td>
    	  <td><input type='text' size='20' readonly='true' value='".$_POST['newempusername']."'/></td></tr>
    	  <td><label for='showcf'>Carry Forwarded</label></td>
    	  <td><input type='text' size='20' readonly='true' value='0'/></td></tr>
    	  <td><label for='showbl'>Balance Leaves</label></td>
    	  <td><input type='text' size='20' readonly='true' value='".$balanceLeaves."'/></td></tr>
		  </tbody>
		  </table>";
		  $insertquery="INSERT INTO`emptotalleaves` (`empid` ,`carryforwarded` ,`previous year` ,`balanceleaves` ,`present year`)
		  VALUES ('".$_POST['newempid']."', 0, '".(($joiningyear)-1)."', '".$balanceLeaves."', '".$joiningyear."')";
		  $insert=$db->query($insertquery);
		  $indertedemp=$db->query($insertemp);
		  if($insert) {
		  	echo "<script>alert('".$_POST['newempname']." (".$_POST['newempid'].") added successfully in database.')</script>";
		  }else {
		  	echo "<script>alert('".$_POST['newempname']." (".$_POST['newempid'].") is not added in database.')</script>";		  	
		  }
		}
}

if(isset($_REQUEST['add_emp']))
{
	echo "<h2><center><u>Add Employee</u></center></h2><br>";
	echo "<form id='addempform' method='POST' action='newuser.php?showleaves=1'>
		  <table id='table-2'>
		  <tbody>
		  <tr>
		  <td><label for='newempid'>Emp Id:</label></td>
    	  <td><input type='text' name='newempid' id='newempid' size='20' /></td></tr>
		  <td><label for='newempusername'>Employee User Name</label></td>
    	  <td><input type='text' name='newempusername' id='newempusername' size='20' /></td></tr>
		  <td><label for='newempname'>Employee Name</label></td>
    	  <td><input type='text' name='newempname' id='newempname' size='20' /></td></tr>
		  <td><label for='joiningdate'>Joining Date</label></td>
    	  <td><input type='text' name='joiningdate' id='joiningdate' size='20' /></td></tr>
		  <td><label for='birthdate'>Birth Date</label></td>
    	  <td><input type='text' name='birthdate' id='birthdate' size='20' /></td></tr>
		  <td><label for='newdept'>Department</label></td>";
    	  echo "<td id='dept'>".getSelectBoxOptions("select distinct(dept) from emp","dept","dept","newdept","")."</td></tr>";
		  echo "<tr><td><label for='newmanager'>Manager</label></td>";
    	  echo "<td id='getmanager'>".getSelectBoxOptions("select distinct(empname) as managername,empid from emp where state='Active' and (role='manager' or role='Manager')","empid","managername","newmanager","")."</td></tr>";
		  echo "<tr><td><label for='newrole'>Role</label></td>
    	  <td><SELECT name='newrole' id='newrole'>
					<option></option>
					<option>user</option>
	    	  			<option>manager</option>
					<option>hr</option>    	  
		  </select></td></tr>
	  <tr id='addmanagerlevel' style='display:none'><td><label for='hideaddmanagerlevel'>Manager Level</label></td>
          <td><SELECT name='hideaddmanagerlevel'>
		       <option></option>
                       <option>level1</option>
                       <option>level2</option>
                       <option>level3</option>       
		       <option>level4</option>	
                  </select></td></tr>					
    	  <tr><td><label for='newgroup'>Group</label></td>
    	  <td><input type='text' name='newgroup' id='newgroup' size='20' /></td></tr>
    	  <tr><td><label for='newemail'>Employee Email</label></td>
    	  <td><input type='text' name='newemail' id='newemail' size='20' /></td></tr>
		<tr><td><label for='location'>Location</label></td>
			<td><SELECT name='location' id='location'>
					
					<option id='BLR'>BLR</option>
					<option id='MUM'>MUM</option>
		</select>
		</td>
		</tr>
		  <tr><td colspan=\"3\" align='center'><input class='submit' type='submit' name='submit' value='Add'/></td>
		   </tr></tbody>
	</table></form>";
}
if(isset($_REQUEST['edit_emp']))
{
	echo "<form id='editempform1' method='POST' action='newuser.php?editempdetails=1'>
		  <table id='table-2'>
		  <tbody>
		  <tr>
	  	  <td><label for='empusername'>Enter Employee User Name:</label></td>
    	  <td><input type='text' name='empname' id='empusername' size='20' /></td>
    	  <td colspan=\"3\" align='center'><input class='submit' type='submit' name='submit' value='Edit'/></td>
    	  </tr></tbody>
	</table></form>";
	
}
if(isset($_REQUEST['editempdetails']))
{
	$getEmpDetails=$db->query("select * from emp where empname='".$_POST['empname']."' and state='Active'");
	$row=$db->fetchAssoc($getEmpDetails);
	echo "<h2><center><u>Edit Employee Details</u></center></h2><br>";
	echo "<form id='editempform2' method='POST' action='newuser.php?submitdetails=1'>
		  <table id='table-2'>
		  <tbody>
		  <tr>
		  <td><label for='editempid'>Emp Id:</label></td>
    	  <td><input type='text' name='editempid' id='editempid' size='20' value='".$row['empid']."' readonly='true' /></td></tr>
		  <td><label for='editempusername'>Employee User Name</label></td>
    	  <td><input type='text' name='editempusername' id='editempusername' size='20' value='".$row['empusername']."'/></td></tr>
		  <td><label for='editempname'>Employee Name</label></td>
    	  <td><input type='text' name='editempname' id='editempname' size='20' value='".$row['empname']."'/></td></tr>
		  <td><label for='joiningdate'>Joining Date</label></td>
    	  <td><input type='text' name='joiningdate' id='joiningdate' size='20' value='".$row['joiningdate']."'/></td></tr>
		  <td><label for='birthdate'>Birth Date</label></td>
    	  <td><input type='text' name='birthdate' id='birthdate' size='20' value='".$row['birthdaydate']."'/></td></tr>
		  <td><label for='editdept'>Department</label></td>";
    	  echo "<td id='dept'>".getSelectBoxOptions("select distinct(dept) from emp","dept","dept","editdept",$row['dept'])."</td></tr>";
		  echo "<tr><td><label for='editmanager'>Manager</label></td>";
    	  echo "<td id='getmanager'>".getSelectBoxOptions("select distinct(empname) as managername,empid from emp where state='Active' and (role='manager' or role='Manager')","empid","managername","editmanager",$row['managername'])."</td></tr>";
		  echo "<tr><td><label for='editrole'>Role</label></td>
    	  <td><SELECT name='editrole' id='editrole'>
				<option></option>
    	  			<option>user</option>
				<option>manager</option>
				<option>hr</option>    	  
		  </select></td></tr>
	  <tr id='editmanagerlevel' style='display:none'><td><label for='hideeditmanagerlevel'>Manager Level</label></td>
          <td><SELECT name='hideeditmanagerlevel'>
		       <option></option>
                       <option>level1</option>
                       <option>level2</option>
                       <option>level3</option>       
		       <option>level4</option> 
                  </select></td></tr>			
    	  <tr><td><label for='editgroup'>Group</label></td>
    	  <td><input type='text' name='editgroup' id='editgroup' size='20' value='".$row['group']."' /></td></tr>
    	  <tr><td><label for='editemail'>Employee Email</label></td>
    	  <td><input type='text' name='editemail' id='editemail' size='20' value='".$row['emp_emailid']."'/></td></tr>
    	  <tr>
    	  		<td><label for='location'>Location</label></td>
    	  		<td>
    	  			<SELECT name='location' id='location'>
    	  				
    	  				<option>BLR</option>
    	  				<option>MUM</option>
    	  			</select>
    	  		</td>
    	  		
    	  </tr>
		  <tr><td colspan=\"3\" align='center'><input class='submit' type='submit' name='submit' value='Edit'/></td>
		   </tr></tbody>
	</table></form>";
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
		  	echo "<script>alert('".$_POST['editempname']." (".$_POST['editempid'].") Updated successfully in database.')</script>";
	}
}
if(isset($_REQUEST['del_emp']))
{
		echo "<form id='delempform' method='POST' action='newuser.php?delempdetails=1'>
		  <table id='table-2'>
		  <tbody>
		  <tr>
	  	  <td><label for='empusername'>Enter Employee User Name:</label></td>
    	  <td><input type='text' name='empusername' id='empusername' size='20' /></td>
    	  <td colspan=\"3\" align='center'><input class='submit' type='submit' name='submit' id='delemp' value='Delete'/></td>
    	  </tr></tbody>
		  </table></form>";	
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
  		echo "<script>alert('Employee is set to InActive state.')</script>";
	}
	else {
		echo "<script>alert('Employee is not set to InActive state.')</script>";
	}	
}


?>
</body>
</html>

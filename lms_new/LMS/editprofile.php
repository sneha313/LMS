<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
if(isset($_REQUEST['update']))
{
	$address = urldecode($_POST['address']);
	$address = addslashes($address);
	$phoneno = $_POST['phnenum'];
	$selectemp=$db->query("select * from empprofile where empid='".$_SESSION['u_empid']."'");
	if($db->hasRows($selectemp))
	{
		$sql = $db->query("UPDATE empprofile SET address ='".$address."',phonenumber = '".$phoneno."' WHERE empid='".$_SESSION['u_empid']."'");
		if($sql){
			echo "<center><h3>Updated Successfully</h3></center>";
			
		} else {
			echo "<center><h3>Record not Updated</h3></center>";
		}
	}
	else {
		$sql = $db->query("INSERT INTO empprofile (empid ,address ,phonenumber)
				VALUES ('".$_SESSION['u_empid']."', '".$address."', '".$phoneno."')");
		
		if($sql){
			echo "<center><h3>Inserted Successfully</h3></center>";
		} else {
			echo "<center><h3>Record not Inserted</h3></center>";
		}
	}

}
else {
	$rs = $db->query("SELECT address,phonenumber FROM empprofile where empid='".$_SESSION['u_empid']."'");
$row1=$db->fetchArray($rs);
echo '<html>
		<head>
			<link rel="stylesheet" type="text/css" media="screen" href="css/table.css" />
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
		});
		</script>
		</head>
		<body>
		<center>
		<form method="post" action="editprofile.php?update=1" id="edit_emp_profile">
		<table id="table-2" width="400" border="0" cellspacing="1" cellpadding="2">
		<tr>
			<td width="100">Employee ID</td>
			<td><input name="emp_id" type="text" id="emp_id"
				value='.$_SESSION['u_empid'].' disabled="disabled"></td>
		</tr>
		<tr>
			<td width="100">Address</td>
			<td><textarea id="address" rows="7" cols="30"  name="address">'.$row1['address'].'</textarea></td>
		</tr>
		<tr>
			<td width="100">Phone number</td>
			<td><input name="phnenum" type="text" id="phnenum"
				value='.$row1['phonenumber'].'>
			</td>
		</tr>
		<tr>
			<td width="100"></td>
			<td><input name="update" type="submit" id="update_emp_profile" value="Update">
			</td>
		</tr>
	</table>
</form>
</center></body></html>';
}

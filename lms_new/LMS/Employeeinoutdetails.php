<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
require_once 'generalFunctions.php';
require_once 'attendenceFunctions.php';

if(isset($_REQUEST['inoutdetails']))
{


	echo "<form name='details' id='details' method='POST' action='Employeeinoutdetails.php?add=1'>";
	echo "<table id='table-2'>";
	echo "<tr>
	<td>1.Employee Name:</td>
	<td><input type='text' id='empname' name='empname'/></td>
	</tr>";
	echo "<tr style='display:none'>
	<td>1.Employee ID:</td>
	<td><input type='text' class='input' id='empid' name='empid' /></td>
	</tr>";
	echo "<tr style='display:none'>
	<td>2.Date:</td>
	<td><input type='text' id='department' name='department'/></td>
	</tr>";
	echo "<tr style='display:none' id='currDayRow'>
	<td>2.Date:</td>
	<td><input type='text' id='currentday' name='currentday' /></td>
	</tr>";
	echo "<tr style='display:none'>
	<td>2.HR Name:</td>
	<td><input type='text' id='hrname' name='hrname' value='".$_SESSION['u_fullname']."' /></td>
	</tr>";
    echo "<tr>
	<td>3.Intime:(HH:MM:SS)</td>
	<td><input type='text' class='input' id='intime' name='intime' value='10:00:00' /></td>
	</tr>";
	echo "<tr>
	<td>4.Outtime:(HH:MM:SS)</td>
	<td><input type='text'  class='input' id='outtime' name='outtime' value='16:00:00' /></td>
	</tr>";
	echo "<tr>
	<td>5.Reason For Adding Manually</td>
	<td><textarea id='reason' name='reason'  /></td>
	</tr>";
	echo "<tr><td id='add'><input type='submit' name='submit' value='ADD' /></td>
	<td style='display:none' id='edit'><input type='button' name='edit' value='EDIT' onclick='editDetails();'/></td>
	<td style='display:none' id='delete'><input type='button' name='delete' value='DELETE' onclick='deleteDetails();' /></td>
	</tr></table></form>";
	echo "<div id='div' style='display:none'><p style='text-align:center'><font color='red'>Employee details are saved for that day... You can not change the details manually</font></p></div>";
}

if(isset($_REQUEST['Allinoutdetails'])) {
	### Get Departments
	$querydept = "SELECT distinct(dept) FROM `emp` ORDER BY dept ASC";
	$resultdept = $db -> query($querydept);

	## Department name
	$department = $department . '<option value="none">';
        $department = $department . "None";
        $department = $department . '</option>';
        if($resultdept) {
                while ($row = mysql_fetch_assoc($resultdept)) {
                        $department = $department . '<option value="' . $row["dept"] . '">';
                        $department = $department . $row["dept"];
                        $department = $department . '</option>';
                }
                $department = $department . '<option value="ALL">';
                $department = $department . "ALL";
                $department = $department . '</option>';
        }

        echo "<form name='InOutAlldetails' id='InOutAlldetails' method='POST' action='Employeeinoutdetails.php?addAllInOut=1'>";
        echo "<table id='table-2'>";
        echo "<tr>
		<td>Department:</td>
                <td><select id='hideDept' size='0' name='UDept'>
	               ' . $department . '
                    </select>
                </td>
              </tr>";
        echo "<tr style='display:none'>
	        <td>1.Employee ID:</td>
        	<td><input type='text' class='input' id='empid' name='empid' /></td>
	      </tr>";
        echo "<tr  id='currDayRow'>
	        <td>Date:</td>
        	<td><input type='text' id='currentday' name='currentday' /></td>
	        </tr>";
        echo "<tr style='display:none'>
        	<td>HR Name:</td>
	        <td><input type='text' id='hrname' name='hrname' value='".$_SESSION['u_fullname']."' /></td>
              </tr>";
	echo "<tr>
        	<td>Intime:(HH:MM:SS)</td>
	        <td><input type='text' class='input' id='intime' name='intime' value='10:00:00' /></td>
              </tr>";
        echo "<tr>
	        <td>Outtime:(HH:MM:SS)</td>
        	<td><input type='text'  class='input' id='outtime' name='outtime' value='16:00:00' /></td>
	     </tr>";
        echo "<tr>
        	<td>Reason For Adding Manually</td>
	        <td><textarea id='reason' name='reason'  /></td>
              </tr>";
        echo "<tr><td id='addAllInOut'><input type='submit' name='submit' value='ADD' /></td>
              </tr></table></form>";
	echo "<div id='loadingmessage' style='display:none'>
                        <img align='middle' src='images/loading.gif'/>
                </div>";
}

if (isset($_REQUEST['addAllInOut'])) {
	$onSuccess=1;
	$getDeptQuery="SELECT * FROM `emp` WHERE dept='".$_REQUEST['UDept']."'";
	$getDeptResult= $db->query($getDeptQuery);
	$currDay=$_REQUEST['currentday'];
	if(($db->countRows($getDeptResult)!=0)) {
		while ($row = mysql_fetch_assoc($getDeptResult)) {
			$empId=$row['empid'];
			$empName=$row['empname'];
			$empDept=$row['dept'];
			if (! (isSpecialLeave($currDay,$empId) || isFullDayPTO($currDay,$empId) || isFulldayWFH($currDay,$empId) || isOptionalHolidayApplied($currDay,$empId)  ||
                              (getDay($empId,$currDay)=="First Half-WFH & Second Half-HalfDay" && getShiftforDay("First Half-WFH & Second Half-HalfDay",$empId,$currDay)!="") ||
                              (getDay($empId,$currDay)=="First Half-HalfDay & second Half-WFH" && getShiftforDay("First Half-HalfDay & second Half-WFH",$empId,$currDay)!=""))) {
			
				$checkEntry=$db->query("SELECT * FROM `inout` WHERE empid='$empId' and Date='$currDay'") or die(mysql_error());
				if ($db->countRows($checkEntry)== 0) {
					$sqlq="insert into `inout`(EmpID,EmpName,Department,Type,Date,First,Last,TypeOfDay,reason,added_hrname) values('".$empId."','".$empName."','".$empDept."','".$empDept."','".$_REQUEST['currentday']."','".$_REQUEST['intime']."','".$_REQUEST['outtime']."','','".$_REQUEST['reason']."','".$_REQUEST['hrname']."')";
				} else {
					$sqlq="UPDATE `inout` SET First = '".$_REQUEST['intime']."',Last = '".$_REQUEST['outtime']."',reason = '".$_REQUEST['reason']."',added_hrname = '".$_REQUEST['hrname']."' WHERE EmpId='".$empId."'and date='".$_REQUEST['currentday']."'";
				}
				$res=$db->query($sqlq);
				 if (!$res) {
					$onSuccess=0;
				}
			}
		}
		if ($onSuccess) {			
			echo '<script>
                		alert("Access Detail is added for '.$_REQUEST['UDept'].' on day '.$_REQUEST['currentday'].'");
	                        $("#loadhrsection").load("Employeeinoutdetails.php?Allinoutdetails=1");
        	     	</script>';
		} else {
                	echo '<script>alert("Access Detail not added.");<script>';
		}
	}
}
if(isset($_REQUEST['add']))
{
	$sql= $db->query("SELECT * FROM `inout` WHERE empid='".$_REQUEST['empid']."' and Date='".$_REQUEST['currentday']."'") or die(mysql_error());
	$query = $db->query("SELECT * FROM `perdaytransactions` WHERE empid='".$_REQUEST['empid']."' and date='".$_REQUEST['currentday']."'and leavetype in('WFH','halfDay') AND shift in('firsthalf', 'secondhalf')") or die(mysql_error());
	$queries = $db->query("SELECT * FROM `perdaytransactions` WHERE empid='".$_REQUEST['empid']."' and date='".$_REQUEST['currentday']."'") or die(mysql_error());
	
	if(($db->countRows($sql)==0)&&($db->countRows($query)!=0)){
		$sqlq="insert into `inout`(EmpID,EmpName,Department,Type,Date,First,Last,TypeOfDay,reason,added_hrname) values('".$_REQUEST['empid']."','".$_REQUEST['empname']."','".$_REQUEST['department']."','".$_REQUEST['department']."','".$_REQUEST['currentday']."','".$_REQUEST['intime']."','".$_REQUEST['outtime']."','','".$_REQUEST['reason']."','".$_REQUEST['hrname']."')";
		$res=$db->query($sqlq);
		if ($res) {
			echo '<script>
					alert("Access Detail is added for '.$_REQUEST['empid'].' on day '.$_REQUEST['currentday'].'");
					$("#loadhrsection").load("Employeeinoutdetails.php?inoutdetails=1");
				</script>';
		} else {
			echo '<script>alert("Access Detail not added.");<script>';
		}
	}
	else if(($db->countRows($sql)==0)&&($db->countRows($queries)==0)){
		$sqlq="insert into `inout`(EmpID,EmpName,Department,Type,Date,First,Last,TypeOfDay,reason,added_hrname) values('".$_REQUEST['empid']."','".$_REQUEST['empname']."','".$_REQUEST['department']."','".$_REQUEST['department']."','".$_REQUEST['currentday']."','".$_REQUEST['intime']."','".$_REQUEST['outtime']."','','".$_REQUEST['reason']."','".$_REQUEST['hrname']."')";
		$res=$db->query($sqlq);
		if ($res) {
		echo '<script>
					alert("Access Detail is added for '.$_REQUEST['empid'].' on day '.$_REQUEST['currentday'].'");
					$("#loadhrsection").load("Employeeinoutdetails.php?inoutdetails=1");
			  </script>';
		} else {
			echo '<script>alert("Access Detail not added.");<script>';
		}
	
	}
	else {
		echo '<script>
				alert("Employee details are already present in LMS for day '.$_REQUEST['currentday'].'");
				$("#loadhrsection").load("Employeeinoutdetails.php?inoutdetails=1");
			</script>';
	}
} elseif(isset($_REQUEST['table'])) {

	$empname=urldecode($_REQUEST['empname']) ;
	$sqlquery=  "SELECT * FROM emp WHERE empname='$empname' and state='Active'";
	$resultset = $db->query($sqlquery);
	$row=$db->fetchArray($resultset);
	if($db->countRows($resultset)!= 0)
	{
		$empid=$row['empid'];
		$department=$row['dept'];

	}
	echo $empid."-".$department;
} else if(isset($_REQUEST['data'])) {

	$date=urldecode($_REQUEST['date']) ;
	$empid=urldecode($_REQUEST['empid']) ;
	$query= $db->query("SELECT * FROM `perdaytransactions` WHERE empid='$empid' and date='$date'and leavetype in('WFH','fullday') AND shift in('','fullday')") or die(mysql_error());
	$sqlquery= $db->query("SELECT * FROM `perdaytransactions` WHERE empid='$empid' and date='$date'and leavetype in('WFH','HalfDay') AND shift='firsthalf'") or die(mysql_error());
	$sqlquery1=$db->query("SELECT * FROM `perdaytransactions` WHERE empid='$empid' and date='$date'and leavetype in('WFH','HalfDay') AND shift='secondhalf'") or die(mysql_error());
	$sqlquery2=$db->query("SELECT * FROM `inout` WHERE empid='$empid' and Date='$date'") or die(mysql_error());
#	$sqlquery3=$db->query("SELECT * FROM `inout` WHERE empid='$empid' and Date='$date' and reason=''") or die(mysql_error());
#	if($db->countRows($sqlquery3)!= 0)
#	{
#	
#		$row=$db->fetchArray($sqlquery3);
#	
#		$empname=$row['EmpName'];
#		$intime=$row['First'];
#		$outtime=$row['Last'];
#		echo $empname."*".$intime."*".$outtime."*".$date;
#	
#	
#	}
	if($db->countRows($query)!= 0)
	{
		echo "Employee is on WFH (or) on leave. Please select another day";
	}

	else if($db->countRows($sqlquery)!= 0)
	{
		$intime='2:00:00';
		$outtime='18:00:00';
		echo $intime."-".$outtime;
	}
	else if($db->countRows($sqlquery1)!= 0)
	{
		$intime='9:00:00';
		$outtime='13:00:00';
		echo $intime."-".$outtime;
	}
   	else if($db->countRows($sqlquery2)!= 0)
	{
		echo 'data'."%".'data';
	}
	
	else {
		$intime='10:00:00';
		$outtime='16:00:00';
		echo $intime."-".$outtime;
	}
	
} elseif(isset($_REQUEST['edit'])) {

	$empid=urldecode($_REQUEST['empid']) ;
	$currentday=urldecode($_REQUEST['currentday']) ;
	$sqlquery=  "SELECT * FROM `inout` WHERE EmpID='$empid' and Date='$currentday'";
	$resultset = $db->query($sqlquery);
	$row=$db->fetchArray($resultset);
	if($db->countRows($resultset)!= 0)
	{
		$empname=$row['EmpName'];
		$department=$row['Department'];
		$intime=$row['First'];
		$outtime=$row['Last'];
		$reason=$row['reason'];
		$hrname=$_SESSION['u_fullname'];
		

	}
	echo $empname."$".$department."$".$intime."$".$outtime."$".$reason."$".$hrname."$".$empid."$".$currentday;
}elseif (isset($_REQUEST['update']))
  {
       if($_REQUEST['empname']!=''&&$_REQUEST['intime']!=''&&$_REQUEST['outtime']!=''&&$_REQUEST['currentday']!=''&&$_REQUEST['reason']!=''&&$_REQUEST['intime']<$_REQUEST['outtime']){
       	$sql="UPDATE `inout` SET Department='".$_REQUEST['department']."',Type = '".$_REQUEST['department']."',First = '".$_REQUEST['intime']."',Last = '".$_REQUEST['outtime']."',TypeOfDay = '',reason = '".$_REQUEST['reason']."',added_hrname = '".$_REQUEST['hrname']."' WHERE EmpId='".$_REQUEST['empid']."'and date='".$_REQUEST['currentday']."'";
	    $res=$db->query($sql);
	    echo "data";
       }
       else{
       	echo "-";
       }
      
	   
  }elseif (isset($_REQUEST['delete']))
  {
       $db->query("DELETE FROM `inout` WHERE EmpId='".$_REQUEST['empid']."'and date='".$_REQUEST['currentday']."'and reason!=''");
       echo "data";
	   
  }else {
		?>
		<html>
		<head>
		<link href="js/jqueryPlugins/jquery.timepicker.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="js/jqueryPlugins/jquery.timepicker.min.js"></script>
		<script>
		$("document").ready(function() {
			
			$("#intime,#outtime").timepicker({ 
				'timeFormat': 'H:i:s', 
				'step' : 15
			});
			$.validator.addMethod("greaterThan", function (value, element) {
		        return value > $("#intime").val();

		    }, 'OutTime should be grater than Intime');
			
		    jQuery('#empname').autocomplete({
		        minLength: 1,
		        source: function(request, response) {
		            jQuery.getJSON('autocomplete/Users_JSON.php', {
		                term: request.term
		            }, response);
		        },
		        focus: function() {
		            // prevent value inserted on focus
		            return false;
		        },
		        select: function(event, ui) {
		            this.value = ui.item.value;
		            var empname=this.value;
		            $.ajax
		        	({
		        		type: "POST",
		        		url: "Employeeinoutdetails.php?table=1&empname="+empname,
		        	    success: function(data)
		        	    {
		        	    	$("#currDayRow").show(); 
		        	    	$("#currentday").val("");   
		        		    var split= data.split("-");
		        	      	$("#empid").val(split[0]);
		        	 	    $("#department").val(split[1]);
		        	 	    
		        	    }
		        	});
		            return false;
		        }
		    });
		    
		$("#details").validate({
			errorClass: "errormsg",
		   rules: {
			   empname: "required",
			   currentday: "required",
		       intime: "required",
		       outtime: {required:true,greaterThan:true},
		       reason:"required"
		   
		   },
		   messages: {
			   empname: "Please specify Employee Name",
			   currentday: "Please specify date",
			   intime: "Please specify Intime ",
			   outtime: {required:"Please specify outtime",greaterThan:"OutTime should greater than Intime"},
			   reason: "Please specify Reason"
		   	
		   },
		  submitHandler: function() {
			  if (confirm("Do you want to add this access detail?"))
			  {
		  		$.ajax({ 
				  data: $('#details').serialize(), 
				  type: $('#details').attr('method'), 
				  url:  $('#details').attr('action'), 
				  success: function(response) { 
					  $('#loadhrsection').html(response);
				  }
		   		});
			  }  else {
		  			$("#loadhrsection").load("Employeeinoutdetails.php?inoutdetails=1");
		  	  }
		  return false;
		 }
		 });

		// To Add Inout details for all employees
		$("#InOutAlldetails").validate({
                        errorClass: "errormsg",
                   rules: {
                           empname: "required",
                           currentday: "required",
                       intime: "required",
                       outtime: {required:true,greaterThan:true},
                       reason:"required"
                   
                   },
                   messages: {
                           empname: "Please specify Employee Name",
                           currentday: "Please specify date",
                           intime: "Please specify Intime ",
                           outtime: {required:"Please specify outtime",greaterThan:"OutTime should greater than Intime"},
                           reason: "Please specify Reason"
                        
                   },
                  submitHandler: function() {
			  $('#loadingmessage').show();
                          if (confirm("Do you want to add this access detail?"))
                          {
                                $.ajax({ 
                                  data: $('#InOutAlldetails').serialize(), 
                                  type: $('#InOutAlldetails').attr('method'), 
                                  url:  $('#InOutAlldetails').attr('action'), 
                                  success: function(response) { 
                                          $('#loadhrsection').html(response);
					  $('#loadingmessage').hide();
                                  }
                                });
                          }  else {
                                        $("#loadhrsection").load("Employeeinoutdetails.php?Allinoutdetails=1");
                          }
                  return false;
                 }
                 });
	
		$( "#currentday" ).datepicker({
			changeMonth: true,
			changeYear: true,
			buttonImage: 'js/datepicker/datepickerImages/calendar.gif',
			dateFormat: 'yy-mm-dd',
			showButtonPanel: true,
			showOn: 'both',
			maxDate:new Date(),
			yearRange: '-1',
			onSelect: function(dateText, inst) {
		        var date = $(this).val();
		        var empid= $("#empid").val();
		       
		        $.ajax
		    	({
		    		type: "POST",
		        	url: "Employeeinoutdetails.php?data=1&date="+date+"&empid="+empid,
		    	    success: function(data)
		    	    {

		    	    	if (($.inArray('-', data)< 0)&&($.inArray('%', data)< 0)&&($.inArray('*', data)< 0))
		   		         { 
		    	    		alert(data);
		    	    		$("#currentday").val("");
		    	    		$("#add").show();
		    	    		$("#edit").hide();
		    	    		$("#delete").hide();
		    	    		 $("#div").hide();
		    	    	 }
		    	    	else if (($.inArray('%', data)> 0)&&($.inArray('*', data)< 0))
		   		         { 
		    	    		$("#add").hide();
		    	    		$("#edit").show();
		    	    		$("#delete").show();
		    	    		 $("#div").hide();
		    	    		var currentday=$('#currentday').val();
		    	    		  $.ajax
		  		        	({
		  		        		type: "POST",
		  		        		url: "Employeeinoutdetails.php?edit=1&empid="+empid+"&currentday="+currentday,
		  		        	    success: function(data)
		  		        	    {
			  		        	      var split= data.split("$");
			  		        	      
			  		        	      $("#empname").val(split[0]);
			  		        	      $("#department").val(split[1]);
			 		                  $("#intime").val(split[2]);
			 		         	      $("#outtime").val(split[3]);
			 		         	      $("#reason").val(split[4]);
			 		         	      $("#hrname").val(split[5]);
			 		         	      $("#empid").val(split[6]);
			  		        	      $("#currentday").val(split[7]);
			 		         	  
		  		        	    }
		  		        	});
		    	    		
		    	    	 }
		    	    	else if ($.inArray('*', data)> 0)
		    	    	{
		    	    		  var split= data.split("*");
	  		        	      
	  		        	      $("#empname").val(split[0]);
	  		        	      $("#intime").val(split[1]);
	 		         	      $("#outtime").val(split[2]);
	 		         	      $("#currentday").val(split[3]);
	 		         	      $("#div").show();
	 		         	      $("#add").hide();
				         	  $("#reason").val("");
			    	    	  $("#edit").hide();
			    	    	  $("#delete").hide();
		    	    	}
		    	    	else{
		    	    		
			    		     var split= data.split("-");
			                 $("#intime").val(split[0]);
			         	     $("#outtime").val(split[1]);
			         	     $("#add").show();
			         	     $("#reason").val("");
		    	    		 $("#edit").hide();
		    	    		 $("#delete").hide();
		    	    		 $("#div").hide();
		    	    	}
		    	    }
		    	});
			},
			buttonImageOnly: true
		});
	});
function editDetails()
{
	 var empid= $("#empid").val();
	 var currentday=$('#currentday').val();
	 $.ajax
   	({
   		type: "POST",
   		url: "Employeeinoutdetails.php",
   		data:jQuery("#details").serialize()+"&update=1",
   	    success: function(data)
   	    {
   	     if ($.inArray('-', data)< 0){
   	    	alert("Access Detail is Modified for "+empid+" on day "+currentday);
			$("#loadhrsection").load("Employeeinoutdetails.php?inoutdetails=1"); 
   	     }
   	     else
   	   	     alert("Please enter missing data");
   	    }
  	});
	
}
function deleteDetails()
{
	 var empid= $("#empid").val();
	 var currentday=$('#currentday').val();
	 $.ajax
   	({
   		type: "POST",
   		url:"Employeeinoutdetails.php?delete=1&empid="+empid+"&currentday="+currentday,
   	    success: function(data)
   	    {
   	   	    
               alert("Access Detail is Deleted for "+empid+" on day "+currentday);
   	   	   		$("#loadhrsection").load("Employeeinoutdetails.php?inoutdetails=1"); 
   	    }
  	});
	
}

$("#hideDept").change(function() {
           var dept=$("#hideDept").val();
           var empid="' . $_SESSION['u_empid'] . '";
           if(empid==dept || dept=="none") {
                   $("#hideName").hide();
           } else {
                   $("#hideName").show();
                   $.post("getSplLeaveOptions.php?dept="+escape(dept),function(data) {
                           $("#getEmpName").empty();
                           $("#getEmpName").append(data);
                   });
           }
});

		</script>
		</head>
		<body id="applyleavebody">
		</body>
		</html>
<?php 
}
?> 


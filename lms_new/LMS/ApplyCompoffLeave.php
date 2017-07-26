<?php
session_start();
require_once 'Library.php';
$db=connectToDB();
require_once 'generalFunctions.php';
if(isset($_REQUEST['compoffleave']))
{
	echo "<form name='compoff' id='compoff' method='POST' action='ApplyCompoffLeave.php?add=1'>";
	echo "<table id='table-2'>";
	echo "<tr>
			<td>Employee ID</td>
			<td><input type='text' readonly id='empid' name='empid' value='".$_SESSION['u_empid']."'></td>
		</tr>
		<tr>
			<td colspan='2'>
				<table border='1' id='compofftable' class='dynamitableccompoffinput'>
					<tr>
					    <td>Worked Holiday Date</td>
					    <td>Compoff Leave Date</td>
						<td>Compoff Leave Type</td>
						<td>Comments</td>
					</tr>
					<tr>
						
						<td><input class='workeddaydynamic' type='text' name='dynamicworked_day[0]' readonly/></td>
						<td><input class='dynamiccompoffinput' type='text' name='dynamiccompoff_date[0]' readonly/></td>
					    <td><select name='day_type[]' id='day_type'>
						         <option value=fullday>Full Day</option>
						         <option value=firsthalf>First Half</option>
						         <option value=secondhalf>Second Half</option>
						 </td>
						 <td><textarea name='comments[0]' class='comment'/></td>
						</tr>
				</table>
			 </td>
		</tr>
		<tr>
				<td id='add'><input type='button' value='Add Compoff Leaves' onclick='generateLeaves()'/></td>
				<td><input type='submit' value='Apply and Send Mail' name='submit'></td>
		</tr>
		</table>
		</form>";
	
}
if(isset($_REQUEST['add']))
{
	
	$transactionidArray=array();
	$status=true;
	for($j=0; $j<count($_REQUEST['dynamiccompoff_date']); $j++)
	{
		if($_REQUEST['dynamicworked_day'][$j]!="" && $_REQUEST['dynamiccompoff_date'][$j]!="") 
		{
			$workedday= $_REQUEST['dynamicworked_day'][$j];
			$compoffday= $_REQUEST['dynamiccompoff_date'][$j];
			$transactionid = generate_transaction_id();
			array_push($transactionidArray,$transactionid);
			$sqlq="UPDATE `inout` SET compofftakenday='".$compoffday."' WHERE empid='".$_REQUEST['empid']."' and date='".$workedday."'";
			$res=$db->query($sqlq);
			if(mysql_affected_rows()==0) {
				$Insertsqlq="INSERT into `inout` (EmpID,EmpName,Department,Date,First,Last,compofftakenday) values('".$_SESSION['u_empid']."','".$_SESSION['u_fullname']."','".$_SESSION['user_dept']."','".$workedday."','10:00:00','18:30:00','".$compoffday."')";
				$db->query($Insertsqlq);
			}
			$sqlq1="insert into `perdaytransactions`(empid,transactionid,date,leavetype,shift,compoffreason) values('".$_REQUEST['empid']."','$transactionid','$compoffday','CompOff Leave','".$_REQUEST['day_type'][$j]."','".$_REQUEST['comments'][$j]."')";
			$res1=$db->query($sqlq1);
			$sqlq2="insert into `empleavetransactions`(transactionid,empid,startdate,enddate,count,reason,approvalstatus,approvalcomments,leave_type) values('$transactionid','".$_REQUEST['empid']."','$compoffday','$compoffday','0','CompOff Leave (on behalf of $workedday)','Pending','','compoff')";
			$res1=$db->query($sqlq2);
    	}
    	
	}
	
	if (count($transactionidArray)==1) {
		$tid=$transactionidArray[0];		
	} else {
		$tid=implode(",",$transactionidArray);
	}
	$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$_SESSION['u_empid'].' ApplyCompOff compoffdate >> /dev/null &';
	exec($cmd);
	echo '<script>
				alert("Compoff Leave Applied");
				$("#loadcompoffleave").load("selfleavestatus.php");
		  </script>';
	
	
}
else if(isset($_REQUEST['workedday']))
{
	$date=$_REQUEST['date'];
	$empid=$_REQUEST['empid'];
	$dateArray=explode("-", $date);
	$day = date('D', strtotime($date));
	$sqlquery=$db->query("SELECT * FROM `holidaylist` WHERE date='$date'and leavetype='Fixed'") or die(mysql_error());
	$sqlquery1=$db->query("SELECT * FROM `inout` WHERE empid='$empid' and Date='$date'") or die(mysql_error());
	$sqlquery2 = $db->query("SELECT * FROM `emp` WHERE day(birthdaydate)='".$dateArray[2]."' and month(birthdaydate)='".$dateArray[1]."' and empid='$empid'") or die(mysql_error());
	$sqlquery3=$db->query("SELECT * FROM `inout` WHERE empid='$empid' and date='$date' and compofftakenday!='0000-00-00'") or die(mysql_error());
	if($day=='Sat'||$day=='Sun'||$db->countRows($sqlquery)!= 0||$db->countRows($sqlquery2)!= 0)
	{
		if($db->countRows($sqlquery1)== 0)
		{
			
		#	echo 'You didn\'t worked in office on selected Date( '.$date.')';
		#	return;
		}
		if($db->countRows($sqlquery3)!= 0)
		
			echo 'You have already taken the Comp Off on Day( '.$date.')';
		else 
			echo $date;
	}
	else 
		echo 'Selected Date( '.$date.') is not a week end (or) Holiday (or) your BirthDay';
	
}
else if(isset($_REQUEST['compoffday']))
{
	$date=$_REQUEST['date'];
	$empid=$_REQUEST['empid'];
	$dateArray=explode("-", $date);
	$day = date('D', strtotime($date));
	$sqlquery1=$db->query("SELECT * FROM `empleavetransactions` WHERE '$date'>=startdate and '$date'<=enddate and empid='$empid'and approvalstatus in('Pending','Approved')") or die(mysql_error());
	$sqlquery2=$db->query("SELECT * FROM `holidaylist` WHERE date='$date'and leavetype='Fixed'") or die(mysql_error());
	$sqlquery3 = $db->query("SELECT * FROM `emp` WHERE day(birthdaydate)='".$dateArray[2]."' and month(birthdaydate)='".$dateArray[1]."' and empid='$empid'") or die(mysql_error());
	if($day=='Sat'||$day=='Sun' || $db->countRows($sqlquery2) > 0 || $db->countRows($sqlquery3) > 0) {
		echo "You selected (".$date.").\n\nYou can't avail compoff on a holiday/weekend/on your birthday";
	} 
	elseif($db->countRows($sqlquery1)!= 0)
	{
		echo 'Leave is already applied for selected date ( '.$date.' ). Check in Leave Pending Status/Employee Leave History';
	}
	
}	
else { 
?>		
		<html>
		<head>
		<script>
		
		$("document").ready(function() {
			
			$("#compoff").validate({
			  
			  submitHandler: function() {
				  if (confirm("Do you want to apply Compoff leave?"))
				  {
			  		$.ajax({ 
					  data: $('#compoff').serialize(), 
					  type: $('#compoff').attr('method'), 
					  url:  $('#compoff').attr('action'), 
					  success: function(response) { 
						  $('#loadhrsection').html(response);
					  }
			   		});
				  }  else {
			  			$("#loadhrsection").load("ApplyCompoffLeave.php?compoffleave=1");
			  	  }
			  return false;
			 }
			 });
			 
			$.validator.addClassRules({
				workeddaydynamic: {
		            required: true
				           },
				  dynamiccompoffinput: {
			            required: true
		           },
		           comment: {
			            required: true
		           }
				
				});	
			$('body').on('click','.dynamiccompoffinput',function(){
				$(this).datepicker ({
		        	changeMonth: true,
			        changeYear: true,
			        dateFormat: 'yy-mm-dd',
			        yearRange: '-1:+0',
			        buttonImage: 'js/datepicker/datepickerImages/calendar.gif',
			        showButtonPanel: true,
					showOn: 'both',
					buttonImageOnly: true,	
					onSelect: function(dateText, inst) {
						 $(this).siblings('.error').remove()
						 var date = $(this).val();
						 var workeddate=$(this).closest('tr').children('td:first').find('input').val()
						 var empid= $("#empid").val();
						 if(date < workeddate)
						 {
							 alert("Applied Compoff leave date should be greater than Worked Holiday date");
							 $(this).val("");
						 }
						 else{
						 $.ajax
					    	({
					    		context: this,
					    		type: "POST",
					        	url: "ApplyCompoffLeave.php?compoffday=1&date="+date+"&empid="+empid,
					    	    success: function(data)
					    	    {
					    	    	if($.inArray('(', data)> 0)
						    	    {
					    	    		alert(data);
					    	    		$(this).val("");
					    	    	}
					    	    	else
					    	    	{
										var selectedElement=$(this);
					    	    		$(".dynamiccompoffinput").not(this).each(function() {
					    	    			if($(this).val()==selectedElement.val())
					    		    		{
				                           		alert("Selected date is already present in table");
				                           		selectedElement.val("");
				                           	}

					    	    		});
									}	  
					    	    }
					    	});
					  }
					},	
					onClose: function() { this.focus(); }
		    	});
			});
			
			$('body').on('click','.workeddaydynamic',function() {
				$(this).datepicker ({
					changeMonth: true,
			        changeYear: true,
			        showButtonPanel: true,
			        dateFormat: 'yy-mm-dd',
			        yearRange: '-1:+0',
			        maxDate: '+0D',
			        buttonImage: 'js/datepicker/datepickerImages/calendar.gif',
					showOn: 'both',
					buttonImageOnly: true,	 
					onSelect: function(dateText, inst) {
						   $(this).siblings('.error').remove()
				           var date = $(this).val();
				           var empid= $("#empid").val();
				           var compoffdate=$(this).closest('td').next().find('input').val();
				          
				           $.ajax
					    	({
					    		context: this,
					    		type: "POST",
					        	url: "ApplyCompoffLeave.php?workedday=1&date="+date+"&empid="+empid,
					    	    success: function(data)
					    	    {
					    	    	
					    	    	if($.inArray('(', data)> 0){
					    	    		alert(data);
					    	    		$(this).val("");
					    	    	  
					    	    	}
					    	    	else
					    	    	{
					    	    		var selectedElement=$(this);
					    	    		$(".workeddaydynamic").not(this).each(function() {
					    	    			if($(this).val()==selectedElement.val())
					    		    		{
				                           		alert("Selected date is already present in table");
				                           		selectedElement.val("");
				                           	}

					    	    		});
					    	    	}
					    	    }
					    	});
				           
				      },
				      onClose: function() { this.focus(); }
		    	});
			});

			
		});
		var count=1;
		function generateLeaves()
		{
			var append='<tr><td><input class="workeddaydynamic" type="text" name="dynamicworked_day['+count+']" /></td><td><input class="dynamiccompoffinput" type="text" readonly name="dynamiccompoff_date['+count+']" /></td><td><select name="day_type[]" id="day_type" class="required"><option value=fullday>Full Day</option><option value=firsthalf>First Half</option><option value=secondhalf>Second Half</option></select></td><td><textarea name="comments['+count+']" class="comment"/></td><td class="cancel"><img src="images/cancel.png" name="cancel" onclick="deleteDay();"/></td></tr>';
			$('#compofftable').last().append(append);
			count++;
		
		}
		function deleteDay() {
			  $(".cancel").click(function() {
			  	$(this).parent().remove();
			  	
		  	  });
		}
		</script>
		</head>
		<body id="applyleavebody">
		</body>
		</html>
<?php 
}
?> 



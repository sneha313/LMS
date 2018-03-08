<?php
session_start();
//require_once 'Library.php';
require_once ("librarycopy1.php");
require_once ("generalcopy.php");
$db=connectToDB();
//require_once 'generalFunctions.php';
if(isset($_REQUEST['compoffleave']))
{
	echo "<form name='compoff' id='compoff' method='POST' action='ApplyCompoffLeave.php?add=1'>";
	echo "<div class='panel panel-primary'>
		<div class='panel-heading text-center'>
			<strong style='font-size:20px;'>Apply Comp Off Leave</strong>
		</div>
		<div class='panel-body'>
			<div class='form-group'>
				<div class='row'>
					<div class='col-sm-3'></div>
					<div class='col-sm-2'>
						<label>Employee ID:</label>
					</div>
					<div class='col-sm-4'>
						<input type='text' class='form-control' readonly id='empid' name='empid' value='".$_SESSION['u_empid']."'>
					</div>
					<div class='col-sm-3'></div>
				</div>
			</div>
			<div class='form-group'>
				<div class='row'>
					<div class='col-sm-1'></div>
					<div class='col-sm-10'>
						<table id='compofftable' class='table table-bordered dynamitableccompoffinput'>
							<tr>
								<td>Worked Holiday Date</td>
								<td>Compoff Leave Date</td>
								<td>Compoff Leave Type</td>
								<td>Comments</td>
							</tr>
							<tr>
								<td>
									<div class='input-group'>
										<input class='workeddaydynamic form-control open-datetimepicker' type='text' name='dynamicworked_day[0]' readonly/>
										<label class='input-group-addon btn' for='date'>
											<span class='fa fa-calendar'></span>
										</label>
									</div>
								</td>
								<td>
									<div class='input-group'>
										<input class='dynamiccompoffinput form-control open-datetimepicker1' type='text' name='dynamiccompoff_date[0]' readonly/>
										<label class='input-group-addon btn' for='date'>
											<span class='fa fa-calendar'></span>
										</label>
									</div>
								</td>
								<td>
									<select class='form-control' name='day_type[]' id='day_type'>
										<option value=fullday>Full Day</option>
										<option value=firsthalf>First Half</option>
										<option value=secondhalf>Second Half</option>
									</select>
								</td>
								<td><textarea class='form-control' name='comments[0]' class='comment'/></td>
							</tr>
						</table>
					</div>
					<div class='col-sm-1'></div>
					</div>
					</div>
					<div class='form-group'>
						<div class='row'>
							<div class='col-sm-12 text-center'>
								<input type='button' class='btn btn-info' id='add' value='Add Compoff Leaves' onclick='generateLeaves()'/>
								<input type='submit' class='btn btn-success' value='Apply and Send Mail' name='submit'>
							</div>
						</div>
					</div>
				</div>
			</div>
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
			//$sqlq="UPDATE `inout` SET compofftakenday='".$compoffday."' WHERE empid='".$_REQUEST['empid']."' and date='".$workedday."'";
			$sqlq = array('compofftakenday'=>$compoffday);
			$sqlqWhere = array('empid'=>$_REQUEST['empid'],'date'=>$workedday);
			$res = $db->update('inout', $sqlq, $sqlqWhere)->affectedRows();
			//$res=$db->query($sqlq);
			//$res=$db->pdoQuery($sqlq);
			if(mysql_affected_rows()==0) {
				//$Insertsqlq="INSERT into `inout` (EmpID,EmpName,Department,Date,First,Last,compofftakenday) values('".$_SESSION['u_empid']."','".$_SESSION['u_fullname']."','".$_SESSION['user_dept']."','".$workedday."','10:00:00','18:30:00','".$compoffday."')";
				//$db->pdoQuery($Insertsqlq);
				$Insertsql = array('EmpID'=>$_SESSION['u_empid'],'EmpName'=>$_SESSION['u_fullname'],'Department'=>$_SESSION['user_dept'],'Date'=>'$workedday','First'=>'10:00:00','Last'=>'18:30:00','compofftakenday'=>'$compoffday');
				$Insertsqlq = $db->insert('inout',$Insertsql)->getLastInsertId();
			}
			//$sqlq1="insert into `perdaytransactions`(empid,transactionid,date,leavetype,shift,compoffreason) values('".$_REQUEST['empid']."','$transactionid','$compoffday','CompOff Leave','".$_REQUEST['day_type'][$j]."','".$_REQUEST['comments'][$j]."')";
			//$res1=$db->pdoQuery($sqlq1);
			$sqlq1 = array('empid'=>$_REQUEST['empid'],'transactionid'=>'$transactionid','date'=>'$compoffday','leavetype'=>'CompOff Leave','shift'=>$_REQUEST['day_type'][$j],'compoffreason'=>$_REQUEST['comments'][$j]);
			$res1 = $db->insert('perdaytransactions',$sqlq1)->getLastInsertId();
			//$sqlq2="insert into `empleavetransactions`(transactionid,empid,startdate,enddate,count,reason,approvalstatus,approvalcomments,leave_type) values('$transactionid','".$_REQUEST['empid']."','$compoffday','$compoffday','0','CompOff Leave (on behalf of $workedday)','Pending','','compoff')";
			//$res1=$db->pdoQuery($sqlq2);
			$sqlq2 = array('transactionid'=>'$transactionid','startdate'=>$_REQUEST['empid'],'enddate'=>'$compoffday','count'=>'0','reason'=>'CompOff Leave (on behalf of $workedday)','approvalstatus'=>'Pending','approvalcomments'=>'','leave_type'=>'compoff');
			$res1 = $db->insert('empleavetransactions',$sqlq2)->getLastInsertId();
    	}
    	
	}
	
	if (count($transactionidArray)==1) {
		$tid=$transactionidArray[0];		
	} else {
		$tid=implode(",",$transactionidArray);
	}
	$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$_SESSION['u_empid'].' ApplyCompOff compoffdate >> /dev/null &';
	exec($cmd);
	echo "success";
}
else if(isset($_REQUEST['workedday']))
{
	$date=$_REQUEST['date'];
	$empid=$_REQUEST['empid'];
	$dateArray=explode("-", $date);
	$day = date('D', strtotime($date));
	$sqlquery=$db->pdoQuery("SELECT * FROM `holidaylist` WHERE date='$date'and leavetype='Fixed'");
	$sqlquery1=$db->pdoQuery("SELECT * FROM `inout` WHERE empid='$empid' and Date='$date'");
	$sqlquery2 = $db->pdoQuery("SELECT * FROM `emp` WHERE day(birthdaydate)='".$dateArray[2]."' and month(birthdaydate)='".$dateArray[1]."' and empid='$empid'");
	$sqlquery3=$db->pdoQuery("SELECT * FROM `inout` WHERE empid='$empid' and date='$date' and compofftakenday!='0000-00-00'");
	if($day=='Sat'||$day=='Sun'||$sqlquery->rowCount()!= 0||$sqlquery2->rowCount()!= 0)
	{
		if($sqlquery1->rowCount()== 0)
		{
			#	echo 'You didn\'t worked in office on selected Date( '.$date.')';
			#	return;
		}
		if($sqlquery3->rowCount()!= 0)
		{
			//$sqlquery4=$db->query("select * from perdaytransactions where date='$date' and leavetype='FullDay' and empid='$empid'");
			//if($db->countRows($sqlquery4)!= 0)
			echo 'You have already taken the Comp Off on Day( '.$date.')';
		}
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
	$sqlquery1=$db->pdoQuery("SELECT * FROM `empleavetransactions` WHERE '$date'>=startdate and '$date'<=enddate and empid='$empid'and approvalstatus in('Pending','Approved')");
	$sqlquery2=$db->pdoQuery("SELECT * FROM ".$_SESSION['u_holidayListTable']." WHERE date='$date'and leavetype='Fixed'");
	$sqlquery3 = $db->pdoQuery("SELECT * FROM `emp` WHERE day(birthdaydate)='".$dateArray[2]."' and month(birthdaydate)='".$dateArray[1]."' and empid='$empid'");
	if($day=='Sat'||$day=='Sun' || $sqlquery2->rowCount() > 0 || $sqlquery3->rowCount() > 0) {
		echo "holiday";
	} 
	elseif($sqlquery1->rowCount()!= 0)
	{
		//$countRow=$db->fetchAssoc($sqlquery1);
		$countRow=$sqlquery1->results();
		$count=$countRow['count'];
		if($count==1) {
			echo "FullDay Applied";
		} elseif($count==0.5) {
			echo "HalfDay Applied";
		}
	}
	
}	
else { 
?>		
	<html>
		<head>
			<script>
				$("document").ready(function() {
					$("#compoff").validate({
						submitHandler: function(form) {
							$(form).find(':input[type=submit]').replaceWith('<center><img src="public/img/loader.gif" class="img-responsive" alt="processing"/></center>');
							setTimeout(function () {
						    }, 5000);
							BootstrapDialog.confirm("Do you want to apply Compoff leave?", function(result){
					            if(result) {
					            	$.ajax({ 
										  data: $('#compoff').serialize(), 
										  type: $('#compoff').attr('method'), 
										  url:  $('#compoff').attr('action'), 
										  success: function(response) {
											  if(response.trim() == "success") {
												  BootstrapDialog.alert("Compoff Leave Applied", function(){
													 $("#loadcompoffleave").load("selfleavestatus.php");
											     });
											  }
										  }
								   	}); 
					            } else {
					            	$("#loadhrsection").load("ApplyCompoffLeave.php?compoffleave=1");
					            }
					        });
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
									 BootstrapDialog.alert("Applied Compoff leave date should be greater than Worked Holiday date");
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
							    	    	
									shownAlert=0;
									var selectedElement=$(this);
									$(this).closest('tr').children('td:nth-child(3)').find("select").empty();
	                                $(this).closest('tr').children('td:nth-child(3)').find("select").append('<option value=fullday>Full Day</option><option value=firsthalf>First Half</option><option value=secondhalf>Second Half</option>');
	                                                       
				    	    		$(".dynamiccompoffinput").not(this).each(function() {
				    	    			if($(this).val()==selectedElement.val())
				    		    		{
			                           			BootstrapDialog.alert("Selected date is already present in table");
									shownAlert=1;
			                           			selectedElement.val("");
			                           	}
				    	    		});
								if(shownAlert==0) {
									if(data.toUpperCase().indexOf("HOLIDAY") !== -1) {
										BootstrapDialog.alert("You selected ("+date+").\nYou can't avail compoff on a holiday/weekend/on your birthday");
										$(this).val("");
									} else if(data.toUpperCase().indexOf("FULLDAY APPLIED") !== -1) {
										BootstrapDialog.alert("Leave is already applied for selected date ("+date+"). Check in Leave Pending Status/Employee Leave History");
										$(this).val("");
									} else if(data.toUpperCase().indexOf("HALFDAY APPLIED") !== -1) {
										BootstrapDialog.alert("Already Half day is applied on that day. You can apply compoff only for half day");
										$(this).closest('tr').children('td:nth-child(3)').find("select").empty();
										$(this).closest('tr').children('td:nth-child(3)').find("select").append('<option value=firsthalf>First Half</option><option value=secondhalf>Second Half</option>');
									} 
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
					        dateFormat: 'yy-mm-dd',
					        yearRange: '-1:+0',
					        showButtonPanel: true,
							showOn: 'both',
							buttonImageOnly: true, 
					        maxDate: '+0D',
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
							    	    	BootstrapDialog.alert(data);
							    	    	$(this).val(""); 
							    	    }
							    	    else
							    	    {
							    	    	var selectedElement=$(this);
							    	    	$(".workeddaydynamic").not(this).each(function() {
							    	    		if($(this).val()==selectedElement.val())
							    		   		{
						                       		BootstrapDialog.alert("Selected date is already present in table");
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
					var append='<tr><td><div class="input-group"><input class="workeddaydynamic form-control" readonly type="text" name="dynamicworked_day['+count+']" /><label class="input-group-addon btn" for="date"><span class="fa fa-calendar"></span></label></div></td><td><div class="input-group"><input class="dynamiccompoffinput form-control" type="text" readonly name="dynamiccompoff_date['+count+']" /><label class="input-group-addon btn" for="date"><span class="fa fa-calendar"></span></label></div></td><td><select name="day_type[]" id="day_type" class="form-control required"><option value=fullday>Full Day</option><option value=firsthalf>First Half</option><option value=secondhalf>Second Half</option></select></td><td><textarea name="comments['+count+']" class="comment form-control"/></td><td class="cancel"><img src="public/images1/cancel.png" name="cancel" onclick="deleteDay();"/></td></tr>';
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
		<body id="applyleavebody"></body>
	</html>
<?php 
}
?> 
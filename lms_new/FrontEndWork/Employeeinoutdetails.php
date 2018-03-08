<?php
session_start();
require_once 'librarycopy1.php';
require_once 'generalcopy.php';
//require_once 'Library.php';
$db=connectToDB();
//require_once 'generalFunctions.php';
require_once 'attendenceFunctions.php';
require_once 'LMSConfig.php';

if(isset($_REQUEST['Allinoutdetails'])) {
	
        echo "<form name='InOutAlldetails' id='InOutAlldetails' method='POST' action='Employeeinoutdetails.php?addAllInOut=1'>";
        echo "<div class='panel panel-primary'>
		<div class='panel-heading text-center'>
			<strong style='font-size:20px;'>Add IN/OUT Details for all Employee</strong>
		</div>
		<div class='panel-body'>
       		<div class='form-group'>
				<div class='row'>
					<div class='col-sm-2'></div>
					<div class='col-sm-3'><label for='empLoc'>Select Location:</label></div>
                	<div class='col-sm-5'>
                		<SELECT class='form-control' id= 'empLoc' name='empLoc'>
							<option value='Choose' selected>Choose Location</option>";
       						$query= "SELECT DISTINCT e.location FROM emp e, departments d where d.deptStatus='Active'";
        					$sql = $db->pdoQuery($query)->results();
							//for ($i=0;$i<$sql->rowCount();$i++)
								foreach($sql as $result)
							{
								//$result = $sql;
								echo "<option value='".$result['location']."'>".$result['location']."</option>";	
							}
							
							
						echo "</SELECT>
					</div>
            	 </div>
        	</div>";
						
        echo "<div class='form-group'>
			<div class='row' id='deptidRow' style='display:none;'>
				<div class='col-sm-2'></div>
				<div class='col-sm-3'><label for='hideDept'>Department:</label></div>
                <div class='col-sm-5'>
                	<select class='form-control' id='hideDept' size='0' name='hideDept'>' . $department . '</select>
                </div>
             </div>
        </div>";
        echo "<div class='form-group'>
			<div class='row' style='display:none;' id='empidRow'>
				<div class='col-sm-2'></div>
				<div class='col-sm-3'><label for='empid'>Employee ID:</td>
        		<div class='col-sm-5'>
        			<input type='text' class='input form-control' id='empid' name='empid' />
        		</div>
        		<div class='col-sm-2'></div>
	      	</div>
        </div>";
        echo "<div class='form-group'>
			<div class='row' id='currDayRow' style='display:none;'>
				<div class='col-sm-2'></div>
				<div class='col-sm-3'>
        			<label for='currentday'>Date:</label>
        		</div>
        		<div class='col-sm-5'>
        			<div class='input-group'>
						<input type='text' id='currentday' class='form-control open-datetimepicker' name='currentday'>
							<label class='input-group-addon btn' for='date'>
								<span class='fa fa-calendar'></span>
							</label>
					</div>
        		</div>
        		<div class='col-sm-2'></div>
	        </div>
        </div>";
        echo "<div class='form-group'>
			<div class='row' style='display:none;' id='hrnamerow'>
				<div class='col-sm-2'></div>
				<div class='col-sm-3'><label for='hrname'>HR Name:</label></div>
	        	<div class='col-sm-5'><input type='text' class='form-control' id='hrname' name='hrname' value='".$_SESSION['u_fullname']."' /></div>
              	<div class='col-sm-2'></div>
	        </div>
	   	</div>";
	echo "<div class='form-group'>
			<div class='row' style='display:none;' id='intimerow'>
				<div class='col-sm-2'></div>
				<div class='col-sm-3'><label for='intime'>Intime(HH:MM:SS):</label></div>
	        	<div class='col-sm-5'><input type='text' class='form-control input' id='intime' name='intime' value='10:00:00' /></div>
            	<div class='col-sm-2'></div>
			</div>
		</div>";
        echo "<div class='form-group'>
			<div class='row' style='display:none;' id='outtimerow'>
				<div class='col-sm-2'></div>
				<div class='col-sm-3'><label for='outtime'>Outtime(HH:MM:SS):</label></div>
        		<div class='col-sm-5'><input type='text'  class='form-control input' id='outtime' name='outtime' value='16:00:00' /></div>
        		<div class='col-sm-2'></div>
	    	 </div>
        </div>";
        echo "<div class='form-group'>
			<div class='row' style='display:none;' id='reasonrow'>
				<div class='col-sm-2'></div>
				<div class='col-sm-3'><label for='reason'>Reason For Adding Manually:</label></div>
	        	<div class='col-sm-5'><textarea id='reason' class='form-control' name='reason' /></textarea</div>
        		<div class='col-sm-2'></div>
             </div>
        </div>";
        echo "<div class='form-group'>
			<div class='row' id='addAllInOut' style='display:none;'>
				<div class='col-sm-12 text-center'>
        			<input type='submit' class='btn btn-primary' name='submit' value='ADD' />
        		</div>
            </div>
        </div>
     </div></div></form>";
}

if (isset($_REQUEST['addAllInOut'])) {
	$onSuccess=1;
	$getDeptQuery="SELECT * FROM `emp` WHERE dept='".$_REQUEST['hideDept']."'";
	$getDeptResult= $db->pdoQuery($getDeptQuery);
	$getDeptResultcount=$getDeptResult -> count($sTable = 'emp', $sWhere = 'dept = "'.$_REQUEST['hideDept'].'"' );
	$rows = $db->pdoQuery($getDeptQuery)->results();
	$currDay=$_REQUEST['currentday'];
	if(($getDeptResultcount!=0)) {
		foreach($rows as $row){
		//while ($row = $getDeptResult->results()) {
			$empId=$row['empid'];
			$empName=$row['empname'];
			$empDept=$row['dept'];
			if (! (isSpecialLeave($currDay,$empId) || isFullDayPTO($currDay,$empId) || isFulldayWFH($currDay,$empId) || isOptionalHolidayApplied($currDay,$empId)  ||
            	(getDay($empId,$currDay)=="First Half-WFH & Second Half-HalfDay" && getShiftforDay("First Half-WFH & Second Half-HalfDay",$empId,$currDay)!="") ||
                (getDay($empId,$currDay)=="First Half-HalfDay & second Half-WFH" && getShiftforDay("First Half-HalfDay & second Half-WFH",$empId,$currDay)!=""))) {
				$checkEntry=$db->pdoQuery("SELECT * FROM `inout` WHERE empid='$empId' and Date='$currDay'");
				if ($checkEntry->rowCount()== 0) {
					$sqlq="insert into `inout`(EmpID,EmpName,Department,Type,Date,First,Last,TypeOfDay,reason,added_hrname) values('".$empId."','".$empName."','".$empDept."','".$empDept."','".$_REQUEST['currentday']."','".$_REQUEST['intime']."','".$_REQUEST['outtime']."','','".$_REQUEST['reason']."','".$_REQUEST['hrname']."')";
				} else {
					$sqlq="UPDATE `inout` SET First = '".$_REQUEST['intime']."',Last = '".$_REQUEST['outtime']."',reason = '".$_REQUEST['reason']."',added_hrname = '".$_REQUEST['hrname']."' WHERE EmpId='".$empId."'and date='".$_REQUEST['currentday']."'";
				}
				$res=$db->pdoQuery($sqlq);
				 if (!$res) {
					$onSuccess=0;
				}
			}
		}
		if ($onSuccess) {			
			echo 'success';
		} else {
        	echo '<script>BootstrapDialog.alert("Access Detail not added.");<script>';
		}
	}
}if(isset($_REQUEST['table'])) {

	$empname=urldecode($_REQUEST['empname']) ;
	$sqlquery=  "SELECT * FROM emp WHERE empname='$empname' and state='Active'";
	$resultset = $db->pdoQuery($sqlquery);
	$rowcount=$resultset -> count($sTable = 'emp', $sWhere = 'empname = "'.$empname.'" and state = "Active"' );
				
	$rows=$db->pdoQuery($sqlquery)->results();
	foreach($rows as $row)
	if($rowcount!= 0)
	{
		$empid=$row['empid'];
		$department=$row['dept'];

	}
	echo $empid."-".$department;
} else if(isset($_REQUEST['data'])) {

	$date=urldecode($_REQUEST['date']) ;
	$empid=urldecode($_REQUEST['empid']) ;
	$queryres="SELECT * FROM `perdaytransactions` WHERE empid='$empid' and date='$date'and leavetype in('WFH','fullday') AND shift in('','fullday')";
	$query= $db->pdoQuery($queryres);
	$queryrowcount=$query -> count($sTable = 'perdaytransactions', $sWhere = 'empid = "'.$empid.'" and date = "'.$date.'" and leavetype in("WFH","fullday") AND shift in("","fullday")' );
				
	$sqlqueryres="SELECT * FROM `perdaytransactions` WHERE empid='$empid' and date='$date'and leavetype in('WFH','HalfDay') AND shift='firsthalf'";
	$sqlquery= $db->pdoQuery($sqlqueryres);
	$sqlqueryrowcount=$sqlquery -> count($sTable = 'perdaytransactions', $sWhere = 'empid = "'.$empid.'" and date = "'.$date.'" and leavetype in("WFH","HalfDay") AND shift ="firsthalf"' );
				
	$sqlqueryres1="SELECT * FROM `perdaytransactions` WHERE empid='$empid' and date='$date'and leavetype in('WFH','HalfDay') AND shift='secondhalf'";
	$sqlquery1=$db->pdoQuery($sqlqueryres1);
	$queryrowcount1=$sqlquery1 -> count($sTable = 'perdaytransactions', $sWhere = 'empid = "'.$empid.'" and date = "'.$date.'" and leavetype in("WFH","HalfDay") AND shift ="secondhalf"' );
				
	$sqlqueryres2="SELECT * FROM `inout` WHERE empid='$empid' and Date='$date'";
	$sqlquery2=$db->pdoQuery($sqlqueryres2);
	$sqlqueryrowcount2=$sqlquery2 -> count($sTable = 'inout', $sWhere = 'empid = "'.$empid.'" and date = "'.$date.'"' );
				
	if($queryrowcount!= 0)
	{
		echo "Employee is on WFH (or) on leave. Please select another day";
	}

	else if($sqlqueryrowcount != 0)
	{
	# If emp is on half day PTO/WFH half day on first shift
		$intime='14:15:00';
		$outtime='18:30:00';
		echo $intime."-".$outtime;
	}
	else if($queryrowcount1!= 0)
	{
	
		$intime='9:00:00';
		$outtime='14:15:00';
		echo $intime."-".$outtime;
	}
   	else if($sqlqueryrowcount2!= 0)
	{
		echo 'data'."%".'data';
	}
	
	else {
		$intime='10:00:00';
		$outtime='16:00:00';
		echo $intime."-".$outtime;
	}
	
}else {
		?>
		<html>
		<head>
			<link rel="stylesheet" type="text/css" href="public/js/jqueryPlugins/jquery.timepicker.css"/>
			<script type="text/javascript" src="public/js/jqueryPlugins/jquery.timepicker.min.js"></script>
			<script>
				$("document").ready(function() {
					$("#intime,#outtime").timepicker({ 
						'timeFormat': 'H:i:s', 
						'step' : 15
					});
					$.validator.addMethod("greaterThan", function (value, element) {
				    	return value > $("#intime").val();
				    }, 'OutTime should be grater than Intime');
					
				   /* jQuery('#empname').autocomplete({
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
				            $.ajax({
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
				    });*/
				    
				// To Add Inout details for all employees
				$("#empLoc").change(function(){
					if($("#empLoc").val()=="Choose") {
						$("#deptidRow").hide();
						$("#empidRow").hide();
						$("#currDayRow").hide();
						$("#hrnamerow").hide();
						$("#intimerow").hide();
						$("#outtimerow").hide();
						$("#reasonrow").hide();
						$("#addAllInOut").hide();
					} else {
						$("#hideDept").val($("#empLoc").val());
						$("#deptidRow").show();
						$("#currDayRow").show();
						$("#intimerow").show();
						$("#outtimerow").show();
						$("#reasonrow").show();
						$("#addAllInOut").show();
					}
				});
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
		            submitHandler: function(form) {
		            	$(form).find(':input[type=submit]').replaceWith('<center><img src="public/img/loader.gif" class="img-responsive" alt="processing"/></center>');
						setTimeout(function () {
					    }, 5000);
			        	BootstrapDialog.confirm("Do you want to add this access detail?", function(result){
			            	if(result) {
			            	$.ajax({ 
			                	data: $('#InOutAlldetails').serialize(), 
			                    type: $('#InOutAlldetails').attr('method'), 
			                    url:  $('#InOutAlldetails').attr('action'), 
			                    success: function(response) { 
			                    	if(response) {
			                    		BootstrapDialog.alert("Access Detail is added", function(){
			    	                        $("#loadinout").load("Employeeinoutdetails.php?Allinoutdetails=1");
			                    		});
			                		}
			            		}
			             	});
			         		} else {
			            		$("#loadinout").load("Employeeinoutdetails.php?Allinoutdetails=1");
			           		}
			        	});
			        	return false;
		            }
                });
		        	
				$( "#currentday" ).datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat: 'yy-mm-dd',
					showButtonPanel: true,
					showOn: 'both',
					maxDate:new Date(),
					yearRange: '-1',
					onSelect: function(dateText, inst) {
				        var date = $(this).val();
				        var empid= $("#empid").val();
				        $.ajax({
				    		type: "POST",
				        	url: "Employeeinoutdetails.php?data=1&date="+date+"&empid="+empid,
				    	    success: function(data)
				    	    {
								if (($.inArray('-', data)< 0)&&($.inArray('%', data)< 0)&&($.inArray('*', data)< 0))
				   		        { 
				    	    		BootstrapDialog.alert(data);
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
				    	    		$.ajax({
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
			$("#hideDept").change(function() {
				var dept=$("#hideDept").val();
			    var empid="'.$_SESSION['u_empid'].'";
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
			
			$("#empLoc").change(function() {
				 var location = $("#empLoc").val();
				 $.post("getSplLeaveOptions.php?empLoc="+escape(location),function(data) {
					$("#hideDept").empty();
					$("#hideDept").append(data);
				 });
			});
	</script>
</head>
<body id="applyleavebody"></body>
</html>
<?php 
}
?> 
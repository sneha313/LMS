<?php
	session_start();
	require_once ("librarycopy1.php");
	require_once ("generalcopy.php");
	$db=connectToDB();

#allow to view manager/hr to view in/out details which is applied by manager
if(isset($_REQUEST['viewInOutDetailsHistoryManager']))
{ 
	$date=$_REQUEST['date'];
	$empid=$_REQUEST['empid'];
	$empQuery="select empid,empname from emp where empid='".$empid."' and state='Active'";
	$empnametresult=$db->pdoQuery($empQuery)->results();
	foreach($empnametresult as $empnamerow)
	//$empnamerow=$db->pdoQuery($empQuery)->results();
	//$empnamerow=$db->fetchAssoc($empnametresult);
	$query="select * from empinoutapproval where status!='Cancelled' and empid='".$empid."' order by date desc";
	$sqlquery="SELECT DISTINCT YEAR(date) as year FROM empinoutapproval where empid='".$empid."' order by year desc";
	$sql=$db->pdoQuery($sqlquery);
	$distinctYears=array();
	$InOutHistory=$sql -> count($sTable = 'empinoutapproval', $sWhere = 'empid = "'.$empid.'" ' );
	$rows=$db->pdoQuery($sqlquery)->results();
	//$InOutHistory=$sql->rowCount();
	echo '<div class="panel panel-primary">
		<div class="panel-heading text-center">
			<strong style="font-size:20px;">View In/Out Detail History</strong>
		</div>
		<div class="panel-body">';
	if($InOutHistory == 0) {
		echo "<div id='tabs'><ul><div id='Info'><tr><td>No Data Available</td></tr></div></ul></div>";
	} else {
		echo '<div id="tabs">
			<ul>';
	}
	//for($i=0;$i<$sql->rowCount();$i++)
		foreach($rows as $row)
	{
	//$row=$db->fetchArray($sql);
		
	echo "<li><a href='#".$row['year']."'>".$row['year']."</a></li>";
			array_push($distinctYears,$row['year']);
	}
	echo "</ul>";
	foreach ($distinctYears as $year) {
	echo "<div id='".$year."'>";
	echo "<div id='showtable'>
	<table class='table table-hover'>
		<form method='POST' action='' id='InOut' name='viewInOut'>
			<tr class='info'>
				<th>Sr. No.</th>
				<th>Emp Name</th>
				<th>Date</th>
				<th>In Time</th>
				<th>Out Time</th>
				<th>Reason</th>
				<th>Status</th>
			</tr>";
			$sql1=$db->pdoQuery($query);
			$rowcount1=$sql1 -> count($sTable = 'empinoutapproval', $sWhere = 'empid = "'.$empid.'"' );
			$getDetailedrows=$db->pdoQuery($query)->results();
			if($rowcount1 > 0) {
				$count = 0;
			//while($getDetailedrow=$sql1->results()) {
			foreach($getDetailedrows as $getDetailedrow){
			echo  '<tr>
				<td>'.$count++.'</td>
				<td>'.$empnamerow['empname'].'</td>
				<td>'.$getDetailedrow['date'].'</td>
				<td>'.$getDetailedrow['intime'].'</td>
				<td>'.$getDetailedrow['outtime'].'</td>
				<td>'.$getDetailedrow['reason'].'</td>
				<td>'.$getDetailedrow['status'].'</td>
				
			</tr>';
		}
		}
		echo "</form>
	</table>
	</div>
	</div>";
	}
	echo "</div>
	</div>";
	echo '<script>
			$("document").ready(function(){
				$( "#tabs" ).tabs();
			});
			</script>';
	
}

#view to allow manager/hr to apply In/Out details for employee

if(isset($_REQUEST['managerinoutForm']))
{
	$emp=isset($_POST['empuser']) ? $_POST['empuser'] : '';
	$empnamequery="select empid,empname from emp where empname='".$emp."' and state='Active'";
	$empnametresult=$db->pdoQuery($empnamequery)->results();
	//$empnamerow=$empnametresult->results();
	foreach($empnametresult as $empnamerow)
	$childern=getChildren($_SESSION['u_empid']);
	if((in_array($empnamerow['empid'],$childern) && (strtoupper($_SESSION['user_desgn'])=="MANAGER")) || strtoupper($_SESSION['user_dept'])=="HR") {
	
		echo "<div class='panel panel-primary'>
				<div class='panel-heading text-center'>
					<strong style='font-size:20px;'>Apply In/Out Details for Team Member</strong>
				</div>
				<div class='panel-body table-responsive'>";
		echo "<form name='managerapplyEmpInOut' id='managerapplyEmpInOut' method='POST' action='manageraddapplyinout.php?addInOutToTablebymanager=1'>";
		echo "<div class='form-group'>
				<div class='row'>
					<div class='col-sm-3'></div>
					<div class='col-sm-3'><label>Employee ID</label></div>
					<div class='col-sm-3'>
						<input type='text' class='form-control' readonly id='empid' name='empid' value='".$empnamerow['empid']."'>
					</div>
					<div class='col-sm-3'></div>
				</div>
			</div>
			<div class='form-group'>
				<div class='row'>
					<div class='col-sm-1'></div>
					<div class='col-sm-10'>
						<table class='table table-bordered' id='applyInOutTable'>
							<tr>
							    <td>Date</td>
							    <td>In Time</td>
								<td>Out Time</td>
								<td>Reason</td>
							</tr>
							<tr>
								<td>
									<div class='input-group' id='datetimepicker'>
										<input class='dateInOutemp form-control open-datetimepicker' type='text' name='inout_day[0]' readonly/>
										<label class='input-group-addon btn' for='date'>
											<span class='fa fa-calendar'></span>
										</label>
									</div>
								</td>
								<td><input type='text' class='form-control intime' name='intime[0]' value='09:00:00'/></td>
							    <td><input type='text' class='form-control outtime' name='outtime[0]' value='16:30:00'/></td>
		                        <td><textarea class='form-control reason' name='reason[0]'/></td>
							</tr>
						</table>
				 	</div>
					<div class='col-sm-1'></div>
				</div>
			</div>
			<div class='form-group'>
				<div class='row'>
					<div class='col-sm-12 text-center'>
						<input type='button' class='btn btn-info' value='Add Row'  id='add' onclick='generateInOutDays()'/>
						<input type='submit' class='btn btn-success' value='Apply and Send Mail' name='submit'>
					</div>
				</div>
			</div>
		</form></div>
		</div>";
		
		
	} else {
		echo "<script>
			BootstrapDialog.alert(\"You dont have permissions to apply In/Out Detail for '".$_REQUEST['empuser']."'\");
			$('#loadinout').load('manageraddapplyinout.php?managerempinoutform=1');
		</script>";
	}
	?>
	
    <link rel="stylesheet" type="text/css" href="public/js/jqueryPlugins/jquery.timepicker.css"/> 
    <script type="text/javascript" src="public/js/jqueryPlugins/jquery.timepicker.min.js"></script>
	<script>
		$("document").ready(function() {
            $(".intime,.outtime").timepicker({ 
				'timeFormat': 'H:i:s', 
				'step' : 15
			});

			$("#managerapplyEmpInOut").validate({
				
                submitHandler: function(form) {
                	$(form).find(':input[type=submit]').replaceWith('<center><img src="public/img/loader.gif" class="img-responsive" alt="processing"/></center>');
					setTimeout(function () {
				    }, 5000);
					
				    BootstrapDialog.confirm("Do you want to apply InOut Information?", function(result){
				    	if(result) {
				    		 $.ajax({ 
		                            data: $('#managerapplyEmpInOut').serialize(), 
		                            type: $('#managerapplyEmpInOut').attr('method'), 
		                            url:  $('#managerapplyEmpInOut').attr('action'), 
		                            success: function(response) {
		                            	if(response.trim() == "success") {
		                            		var empid=$("#empid").val();
		        							var date = $(".dateInOutemp").val();
			                            	BootstrapDialog.alert("In/Out Details is submitted", function(){
	                         					$("#loadinout").load("manageraddapplyinout.php?viewInOutDetailsHistoryManager=1&empid="+empid+"&date="+date);
	                         				});
		                            	}
		                            }
		                        });
				    	} else {
                            $("#loadinout").load("manageraddapplyinout.php?managerempinoutform=1");
                    }
				    });
                    return false;
                }
			 });
			 
			$.validator.addClassRules({
				dateInOutemp: {
		            required: true
				           },
		           reason: {
			            required: true
		           }
			});

			$('body').on('click','.dateInOutemp',function(){
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
						 var empid= $("#empid").val();
						 $.ajax
					    	({
					    		context: this,
					    		type: "POST",
					        	url: "manageraddapplyinout.php?checkDayExistInInOutTableforemp=1&date="+date+"&empid="+empid,
					    	    success: function(data)
					    	    {
                                    var obj = JSON.parse(data);
                                    
                                    if(typeof obj.inoutdata != "undefined" && obj.First != "00:00:00" && obj.Last != "00:00:00"){
                                        BootstrapDialog.alert('Already intime and outtime is present for: \n\n'+
                                                'Date : '+obj.Date+'\n\n'+
                                                'The In Time and Out Time values are: \n'+
                                                'In Time : '+obj.First+'\n'+
                                                'Out Time : '+obj.Last, function(){
                                        	BootstrapDialog.confirm("Do you still want to modify In Time and Out Time for Date: "+obj.Date+"?")
	                                            $(this).closest('td').next().find('.intime').val(obj.First);
	                                            $(this).closest('td').next().next().find('.outtime').val(obj.Last); 
	                                            if(typeof obj.reason != "undefined") {
	                                                $(this).closest('td').next().next().next().find('.reason').val(obj.reason); 
	                                            }
                                        });
                                    }
                                    if(typeof obj.perdaytransactionData != "undefined"){
                                        if(obj.fullday == 1) {
                                            BootstrapDialog.alert('Already leave is applied on :'+obj.date+'\n'+
                                                    'Leave Type: '+obj.leavetype+'\n\n'+
                                                    'You can`t apply for In/Out Details. Please select another date');
                                             $(this).val('');
                                        } else {
                                            $(this).closest('td').next().find('.intime').val(obj.First);
                                            $(this).closest('td').next().next().find('.outtime').val(obj.Last);
                                        }
                                    }
					    	    }
					    	});
					},	
					onClose: function() { this.focus(); }
		    	});
			});
		});

		var count=1;
		function generateInOutDays()
		{
			var append='<tr>'+
                            '<td><div class="input-group"><input class="dateInOutemp form-control open-datetimepicker" type="text" name="inout_day['+count+']" readonly/><label class="input-group-addon btn" for="date"><span class="fa fa-calendar open-datetimepicker"></span></label></div>'+
                            '</td><td><input type="text" class="form-control intime" name="intime['+count+']" value="09:00:00"/>'+
                            '</td><td><input type="text" class="form-control outtime" name="outtime['+count+']" value="16:30:00"/>'+
                            '</td><td><textarea name="reason['+count+']" class="form-control reason"/>'+
                            '</td><td class="cancel">'+
                                '<img src="public/images/cancel.png" name="cancel" onclick="deleteDay();"/>'+
                            '</td>'+
                        '</tr>';
			$('#applyInOutTable').last().append(append);
            $(".intime,.outtime").timepicker({ 
				'timeFormat': 'H:i:s', 
				'step' : 15
			});
			count++;
		}

		function deleteDay() {
			  $(".cancel").click(function() {
			  	$(this).parent().remove();
			  	
		  	  });
		}
      	</script>
<?php
} 
#  View to check whether the selected day is already present in inout table or not
if(isset($_REQUEST['checkDayExistInInOutTableforemp'])) {
    $date=$_REQUEST['date'];
    $empid=$_REQUEST['empid'];
    $query = "select * from `inout` where Date='".$date."' and EmpId='".$empid."'";
    $result = $db->pdoQuery($query);
    $rowcount=$result -> count($sTable = 'inout', $sWhere = 'Date = "'.$date.'" and EmpId = "'.$empid.'"' );
				
    $checkInPerDayTransaction = "select * from perdaytransactions where ".
                                "date='".$date."' and empId='".$empid."' and status in ('Approved','Pending')";
    $checkInPerDayTransactionResult=$db->pdoQuery($checkInPerDayTransaction);
    $count=$checkInPerDayTransactionResult -> count($sTable = 'perdaytransactions', $sWhere = 'date = "'.$date.'" and empId = "'.$empid.'" and status in ("Approved","Pending")' );
				
    $checkInPerDayTransactionRows = $db->pdoQuery($checkInPerDayTransaction)->results();
    foreach($checkInPerDayTransactionRows as $checkInPerDayTransactionRow)
    $inoutPresent=0;
    if($rowcount > 0) {
        $rows = $db->pdoQuery($query)->results();
        foreach($rows as $row)
        $row['inoutdata']=1;
        $returnResult = json_encode($row);
        $inoutPresent = 1;
        echo $returnResult;
    }
   // $count = $db->countRows($checkInPerDayTransactionResult);
    if( $inoutPresent == 0 && $count > 0 ) {
        $row['perdaytransactionData']=1;
        if(strtoupper($checkInPerDayTransactionRow['leavetype']) == "HALFDAY" && strtoupper($checkInPerDayTransactionRow['shift']) == "FIRSTHALF") {
            $row['First'] = '14:15:00';    
            $row['Last'] = '18:30:00';
        } 
        if(strtoupper($checkInPerDayTransactionRow['leavetype']) == "HALFDAY" && strtoupper($checkInPerDayTransactionRow['shift']) == "SECONDOFF") {
            $row['First'] = '09:00:00';    
            $row['Last'] = '14:15:00';    
        }
        if(strtoupper($checkInPerDayTransactionRow['leavetype']) == "WFH" && strtoupper($checkInPerDayTransactionRow['shift']) == "FIRSTHALF") {
            $row['First'] = '14:15:00';    
            $row['Last'] = '18:30:00';
        } 
        if(strtoupper($checkInPerDayTransactionRow['leavetype']) == "WFH" && strtoupper($checkInPerDayTransactionRow['shift']) == "SECONDOFF") {
            $row['First'] = '09:00:00';    
            $row['Last'] = '14:15:00';    
        }
        
        if(strtoupper($checkInPerDayTransactionRow['leavetype']) != "HALFDAY" || strtoupper($checkInPerDayTransactionRow['leavetype']) != "WFH") {
            $row['fullday']=1;
        }
        $row['leavetype']=$checkInPerDayTransactionRow['leavetype'];
        $row['date'] = $date;
        $returnResult = json_encode($row);
        echo $returnResult;
    }  elseif ($inoutPresent == 0 && $checkInPerDayTransactionResultcount == 0 )  {
        $returnResult = json_encode(array());
        echo $returnResult;
    }
}
# View for modifying In/Out Details which are in pending state for employee
if(isset($_REQUEST['editInOutForEmployee'])) 
{
    $tid=$_REQUEST['tid'];
    $query="SELECT * FROM `empinoutapproval` WHERE transactionId='$tid' and status='Pending'";
	$result=$db->pdoQuery($query);
	$rowcount=$result -> count($sTable = 'empinoutapproval', $sWhere = 'transactionId = "'.$tid.'" and status = "Pending"' );
	$rows = $db->pdoQuery($query)->results();
    if($rowcount > 0 ) {
        echo "<div class='panel panel-primary'>
				<div class='panel-heading text-center'>
					<strong style='font-size:20px;'>Modify In/Out Details</strong>
				</div>
				<div class='panel-body'>";
        echo "<form name='modifyEmpInOut' id='modifyEmpInOut' method='POST' action='manageraddapplyinout.php?modifyInOutToTable=1'>";
        echo "<div class='form-group'>
				<div class='row'>
					<div class='col-sm-3'></div>									
        			<div class='col-sm-3'><label>Employee ID</label></div>
					<div class='col-sm-3'>
        				<input type='text'class='form-control' readonly id='empid' name='empid' value='".$_SESSION['u_empid']."'>
        			</div>
        			<div class='col-sm-3'></div>									
            </div>
        </div>
         <div class='form-group'>
				<div class='row'>
					<div class='col-sm-1'></div>
        			<div class='col-sm-10'>
                    <table class='table table-bordered' id='applyInOutTable'>
                        <tr>
                            <td>Date</td>
                            <td>In Time</td>
                            <td>Out Time</td>
                            <td>Reason</td>
                        </tr>
                        <tr style='display:none'>
                            <td>
                                <input type='text' class='form-control' name='tid' value='".$tid."'/>
                            </td>
                        </tr>";
                $count = 0;
                foreach($rows as $row){
                //while($row = $result->results()) {
                    echo "<tr><td><div class='input-group'>
					        		<input type='text' id='datetimepicker' class='form-control open-datetimepicker' readonly  name='inout_day[".$count."]' value='".$row['date']."'/>
									<label class='input-group-addon btn' for='date'>
										<span class='fa fa-calendar open-datetimepicker'></span>
									</label>
								</div>
        					</td>
                            <td><input type='text' class='form-control intime' name='intime[".$count."]' value='".$row['intime']."'/></td>
                            <td><input type='text' class='form-control outtime' name='outtime[".$count."]' value='".$row['outtime']."'/></td>
                            <td><textarea class='form-control reason' name='reason[".$count."]'>".$row['reason']."</textarea></td>
                     	</tr>";
                    $count++;
                }
        echo "</table></div>
              <div class='col-sm-1'></div>	
             </div>
            </div>
            <div class='form-group'>
				<div class='row'>
					<div class='col-sm-12 text-center'>
                    	<input type='submit' class='btn btn-success' value='Apply and Send Mail' name='submit'>
                    </div>
               	</div>
          	</div>	
            </form>
            </div>
            </div>";
    }
?>
    <link href="public/js/jqueryPlugins/jquery.timepicker.css" rel="stylesheet" type="text/css" /> 
    <script type="text/javascript" src="public/js/jqueryPlugins/jquery.timepicker.min.js"></script>
	<script>
		$("document").ready(function() {
	            
            $(".intime,.outtime").timepicker({ 
				'timeFormat': 'H:i:s', 
				'step' : 15
			});

			$("#modifyEmpInOut").validate({
                submitHandler: function(form) {
                	$(form).find(':input[type=submit]').replaceWith('<center><img src="public/img/loader.gif" class="img-responsive" alt="processing"/></center>');
					setTimeout(function () {
				    }, 5000);
					BootstrapDialog.confirm("Do you want to modify InOut Information?", function(result){
			            if(result) {
                        $.ajax({ 
                            data: $('#modifyEmpInOut').serialize(), 
                            type: $('#modifyEmpInOut').attr('method'), 
                            url:  $('#modifyEmpInOut').attr('action'), 
                            success: function(response) { 
                            	if(response.trim() == "success") { 
                                	BootstrapDialog.alert('Modified In/Out details Successfully', function(){
                                		$('#loadinout').load('manageraddapplyinout.php?viewInOutPendingForEmployee=1');
								     });
								  }
                            	else if(response.trim() == "notsuccess"){
                                    BootstrapDialog.alert('Not Modified In/Out details. Please Contact AIBI', function(){
                                    	$('#loadinout').load('manageraddapplyinout.php?viewInOutPendingForEmployee=1');
                                    });
                                }
                            }
                        });
                    }  else {
                            $("#loadinout").load("manageraddapplyinout.php?viewInOutPendingForEmployee=1");
                    }
					});
                    return false;
                }
			 });
			 
			$.validator.addClassRules({
				dateInOutemp: {
		            required: true
				           },
		           reason: {
			            required: true
		           }
			});

			$('body').on('click','.dateInOutemp',function(){
				$(this).datepicker ({
		        	changeMonth: true,
			        changeYear: true,
			        dateFormat: 'yy-mm-dd',
                    onSelect: function(dateText, inst) {
						   $(this).siblings('.error').remove()	
                    },
					onClose: function() { this.focus(); }
		    	});
			});
		});

      	</script>
<?php
}

# View for deleting In/Out Details which are in pending state for employee
if(isset($_REQUEST['deleteInOutForEmployee'])) 
{
    $tid=$_REQUEST['tid'];
    $query="SELECT * FROM `empinoutapproval` WHERE transactionId='$tid' and status='Pending'";
	$result=$db->pdoQuery($query);
	$rowcount=$result -> count($sTable = 'empinoutapproval', $sWhere = 'transactionId = "'.$tid.'" and status = "Pending"' );
				
    if($rowcount > 0 ) {
        //$deleteTransaction = "UPDATE `empinoutapproval` SET status='Cancelled', comments='Cancelled By Employee' where transactionId='".$tid."'";
        //$deleteResult = $db->pdoQuery($deleteTransaction);

    	$deleteTransaction = array('status'=>'Cancelled','comments'=>'Cancelled By Employee');
    	// where condition array
    	$deleteTransactionWhere = array('transactionId'=>$tid);
    	// call update function
    	$deleteResult = $db->update('empinoutapproval', $deleteTransaction, $deleteTransactionWhere)->affectedRows();
        if($deleteResult) {
            echo "<script>
                BootstrapDialog.alert('Deleted Successfully');
                $('#loadinout').load('manageraddapplyinout.php?viewInOutPendingForEmployee=1');
            </script>";            
        }
    }
}

# View for modifying  In/Out details applied by employee
if(isset($_REQUEST['modifyInOutToTable'])) {
    $tid=$_REQUEST['tid'];
    $count = count($_REQUEST['inout_day']);
    for($i=0;$i<$count;$i++) {
        //$query="UPDATE  `empinoutapproval` SET intime='".$_REQUEST['intime'][$i]."',outtime='".$_REQUEST['outtime'][$i]."',".
              //          "reason='".$_REQUEST['reason'][$i]."' where date='".$_REQUEST['inout_day'][$i]."' and transactionid='".$tid."'";
        //$result=$db->query($query);
    	$query = array('intime'=>$_REQUEST['intime'][$i],'outtime'=>$_REQUEST['outtime'][$i],'reason'=>$_REQUEST['reason'][$i]);
    	// where condition array
    	$Wherequery = array('date'=>$_REQUEST['inout_day'][$i],'transactionid'=>$tid);
    	// call update function
    	$result = $db->update('empinoutapproval', $query, $Wherequery)->affectedRows();
    	
    }
    if($result) {
            $cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$_SESSION['u_empid'].' modifyinout >> /dev/null &';
	        exec($cmd);
	        echo "success";
    }  else {
    		echo "notsuccess";
    }
}

# View for adding the In/Out Details to table added by employee
if(isset($_REQUEST['addInOutToTablebymanager'])) 
{
	$tid=generate_transaction_id();
	$transactionidArray=array();
	for($j=0; $j<count($_REQUEST['inout_day']); $j++)
	{
		if($_REQUEST['inout_day'][$j]!="") 
		{
			$empid=isset($_POST['empid']) ? $_POST['empid'] : '';
			//$insertInOut="INSERT into `empinoutapproval` (createdAt,createdBy,transactionId,empid,date,intime,outtime,reason,status)".
             //"values('".date("Y-m-d H:i:s")."','".$_SESSION['u_empid']."','".$tid."','".$empid."',".
             //"'".$_REQUEST['inout_day'][$j]."','".$_REQUEST['intime'][$j]."','".$_REQUEST['outtime'][$j]."','".$_REQUEST['reason'][$j]."','Approved')";
			
			//$result=$db->pdoQuery($insertInOut);
			

			$insertInOut = array('createdAt'=>date("Y-m-d H:i:s"),'createdBy'=>$_SESSION['u_empid'],'transactionId'=>$tid,'empid'=>$empid,'date'=>$_REQUEST['inout_day'][$j],'intime'=>$_REQUEST['intime'][$j],'outtime'=>$_REQUEST['outtime'][$j],'reason'=>$_REQUEST['reason'][$j],'status'=>'Approved');
			// use insert function
			$result = $db->insert('empinoutapproval',$insertInOut)->getLastInsertId();
			
			# insert into inout table
			//$tid=$_REQUEST['transactionid'];
			//$date=$_REQUEST['date'];
			//$empid=$_REQUEST['empid'];
			$query="SELECT * FROM `empinoutapproval` WHERE transactionId='$tid' and status='Approved'";
			//$query="SELECT * FROM `empinoutapproval` WHERE date='$date' and empid='$empid' and status='Approved'";
			$result=$db->pdoQuery($query);
			$resultcount=$result -> count($sTable = 'empinoutapproval', $sWhere = 'transactionId = "'.$tid.'" and status = "Approved"' );
			$empinoutapprovalRows=$db->pdoQuery($query)->results();
			if($resultcount > 0 ) {
				//while($empinoutapprovalRow=$result->results()) {
				foreach($empinoutapprovalRows as $empinoutapprovalRow){
					$inoutquery="SELECT * FROM `inout` WHERE empid='".$empinoutapprovalRow['empid']."' and Date='".$empinoutapprovalRow['date']."'";
					$inoutResult=$db->pdoQuery($inoutquery);
					$inoutcount=$inoutResult -> count($sTable = 'inout', $sWhere = 'empid = "'.$empinoutapprovalRow['empid'].'" and Date = "'.$empinoutapprovalRow['date'].'"' );
				
					if($inoutcount > 0) {
						# Already information is avaialable for that emp and for that date, so update the inout row
						//$updateInOutQuery="UPDATE `inout` SET First='".$empinoutapprovalRow['intime']."',Last = '".$empinoutapprovalRow['outtime']."',".
							//	"reason='".$empinoutapprovalRow['reason']."',added_hrname='".$_SESSION['u_fullname']."',state='Data Exists' WHERE ".
								//"EmpId='".$empinoutapprovalRow['empid']."'and Date='".$empinoutapprovalRow['date']."'";

						$updateInOutQuery = array('First'=>$empinoutapprovalRow['intime'],'Last'=>$empinoutapprovalRow['outtime'],'reason'=>$empinoutapprovalRow['reason'],'added_hrname'=>$_SESSION['u_fullname'],'state'=>'Data Exists');
						// where condition array
						$aWhere = array('EmpId'=>$empinoutapprovalRow['empid'],'Date'=>$empinoutapprovalRow['date']);
						// call update function
						$updateResult = $db->update('inout', $updateInOutQuery, $aWhere)->affectedRows();
						//$updateResult=$db->pdoQuery($updateInOutQuery);
						# Update the empinoutapproval status as Approved.
						//$updateempinoutapproval=$db->query("UPDATE `empinoutapproval` SET status='Approved' where transactionId='".$tid."'");
					} else {
						# No Information is present in inout table for that emp for that date. So insert into inout table
						//$insertInOutQuery="INSERT into `inout` (EmpID,EmpName,Department,Date,First,Last,reason,added_hrname,state)".
							//	"values('".$empinoutapprovalRow['empid']."','".getempName($empinoutapprovalRow['empid'])."',
                //'".getDeptByEmpid($empinoutapprovalRow['empid'])."','".$empinoutapprovalRow['date']."',".
			      //          "'".$empinoutapprovalRow['intime']."','".$empinoutapprovalRow['outtime']."','".$empinoutapprovalRow['reason']."',
            //    '".$_SESSION['u_fullname']."','Data Exists')";
						//$insertResult=$db->pdoQuery($insertInOutQuery);

						$insertInOutQuery = array('EmpID'=>$empinoutapprovalRow['empid'],'EmpName'=>getempName($empinoutapprovalRow['empid']),'Department'=>getDeptByEmpid($empinoutapprovalRow['empid']),'Date'=>$empinoutapprovalRow['date'],'First'=>$empinoutapprovalRow['intime'],'Last'=>$empinoutapprovalRow['outtime'],'reason'=>$empinoutapprovalRow['reason'],'added_hrname'=>$_SESSION['u_fullname'],'state'=>'Data Exists');
						// use insert function
						$updateempinoutapproval = $db->insert('inout',$insertInOutQuery)->getLastInsertId();
			
						# Update the empinoutapproval status as Approved.
						//$updateempinoutapproval=$db->query("UPDATE `empinoutapproval` SET status='Approved' where transactionId='".$tid."'");
					}
				}
				$query="SELECT * FROM `empinoutapproval` WHERE transactionId='$tid'";
				//$query="SELECT * FROM `empinoutapproval` WHERE date='$date' and empid='$empid'";
				$getResult=$db->pdoQuery($query);
				$empinoutapprovalRows=$db->pdoQuery($query)->results();
				foreach($empinoutapprovalRows as $empinoutapprovalRow)
				$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$empinoutapprovalRow['empid'].' approveinout >> /dev/null &';
				exec($cmd);
			
				/*echo "<script>
				 BootstrapDialog.alert('Approved In/Out details for ".getempName($empinoutapprovalRow['empid'])."');
				 $('#loadinout').load('manageraddapplyinout.php?viewInOutForManager=1');
				</script>";*/
			}
    	}	
	}	
	$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$_SESSION['u_empid'].' manageraddapplyinout >> /dev/null &';
	exec($cmd);
	echo "success";
} 
if(isset($_REQUEST['managerempinoutform']))
	{
		echo '<form action="manageraddapplyinout.php?managerinoutForm=1" method="POST" id="managerinoutempForm">
				<div class="row"> 
					<div class="col-sm-1"></div>
					<div class="col-sm-3">
						<label style="font-size:16px;">Enter Employee Name:</label>
					</div>
					<div class="col-sm-4">
						<input id="empuser" type="text" class="form-control" name="empuser"/>
					</div>
					<div class="col-sm-3">
						<input class="submit btn btn-primary" type="submit" name="submit" value="SUBMIT"/>
					</div>
       				<div class="col-sm-1"></div>
				</div>
			</form>';
		?>
		
		<script type="text/javascript">
		
		$("document").ready(function() {
			
			$('#managerinoutempForm').submit(function() {
				if($("#empuser").val()=="")
				{
					BootstrapDialog.alert("Please Enter Employee Name");
					return false;
				}
				$.ajax({ 
					data: $(this).serialize(), 
			        type: $(this).attr('method'), 
			        url: $(this).attr('action'), 
			        success: function(response) { 
			        	$('#loadinout').html(response); 
			            }
				});
					return false; 
			});
			
			jQuery(function() {
		        jQuery('#empuser').autocomplete({
		            minLength: 1,
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
		});
		</script>
		<?php 
	}
	?>
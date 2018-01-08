<?php
	session_start();
	require_once 'Library.php';
	$db=connectToDB();
	require_once 'generalFunctions.php';
?>
<?php
# View to allow employee to apply In/Out details
if(isset($_REQUEST['inoutForm'])) 
{
     echo "<div class='panel panel-primary'>
			<div class='panel-heading text-center'>
				<strong style='font-size:20px;'>Apply In/Out Details</strong>
			</div>
			<div class='panel-body table-responsive'>";
	echo "<form name='applyEmpInOut' id='applyEmpInOut' method='POST' action='applyinout.php?addInOutToTable=1'>";
	echo "<div class='form-group'>
			<div class='row'>
				<div class='col-sm-3'></div>
				<div class='col-sm-3'><label>Employee ID:</label></div>
				<div class='col-sm-3'>
					<input type='text' class='form-control' readonly id='empid' name='empid' value='".$_SESSION['u_empid']."'>
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
								<div class='input-group'>
									<input class='dateInOut form-control open-datetimepicker' type='text' name='inout_day[0]' readonly/>
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
?>
    <link rel="stylesheet" type="text/css" href="public/js/jqueryPlugins/jquery.timepicker.css"/> 
    <script type="text/javascript" src="public/js/jqueryPlugins/jquery.timepicker.min.js"></script>
	<script>
		$("document").ready(function() {
	            
            $(".intime,.outtime").timepicker({ 
				'timeFormat': 'H:i:s', 
				'step' : 15
			});

			$("#applyEmpInOut").validate({
                submitHandler: function(form) {
                	$(form).find(':input[type=submit]').replaceWith('<center><img src="public/img/loader.gif" class="img-responsive" alt="processing"/></center>');
					setTimeout(function () {
				    }, 5000);
				    BootstrapDialog.confirm("Do you want to apply InOut Information?", function(result){
				    	if(result) {
				    		 $.ajax({ 
		                            data: $('#applyEmpInOut').serialize(), 
		                            type: $('#applyEmpInOut').attr('method'), 
		                            url:  $('#applyEmpInOut').attr('action'), 
		                            success: function(response) {
		                            	if(response.trim() == "success") {
			                            	BootstrapDialog.alert("In/Out Details is submitted. Approval is pending", function(){
	                         					$("#loadinout").load("applyinout.php?viewInOutPendingForEmployee=1");
	                         				});
		                            	}
		                            }
		                        });
				    	} else {
                            $("#loadinout").load("applyinout.php?viewInOutPendingForEmployee=1");
                    }
				    });
                    return false;
                }
			 });
			 
			$.validator.addClassRules({
				dateInOut: {
		            required: true
				           },
		           reason: {
			            required: true
		           }
			});

			$('body').on('click','.dateInOut',function(){
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
					        	url: "applyinout.php?checkDayExistInInOutTable=1&date="+date+"&empid="+empid,
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
                            '<td><div class="input-group"><input class="dateInOut form-control open-datetimepicker" type="text" name="inout_day['+count+']" readonly/><label class="input-group-addon btn" for="date"><span class="fa fa-calendar open-datetimepicker"></span></label></div>'+
                            '</td><td><input type="text" class="form-control intime" name="intime['+count+']" value="09:00:00"/>'+
                            '</td><td><input type="text" class="form-control outtime" name="outtime['+count+']" value="16:30:00"/>'+
                            '</td><td><textarea name="reason['+count+']" class="form-control reason"/>'+
                            '</td><td class="cancel">'+
                                '<img src="images/cancel.png" name="cancel" onclick="deleteDay();"/>'+
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
if(isset($_REQUEST['checkDayExistInInOutTable'])) {
    $date=$_REQUEST['date'];
    $empid=$_REQUEST['empid'];
    $query = "select * from `inout` where Date='".$date."' and EmpId='".$empid."'";
    $result = $db->query($query);
    $checkInPerDayTransaction = "select * from perdaytransactions where ".
                                "date='".$date."' and empId='".$empid."' and status in ('Approved','Pending')";
    $checkInPerDayTransactionResult=$db->query($checkInPerDayTransaction);
    $checkInPerDayTransactionRow = $db->fetchAssoc($checkInPerDayTransactionResult);
    $inoutPresent=0;
    if($db->countRows($result) > 0) {
        $row = $db->fetchAssoc($result);
        $row['inoutdata']=1;
        $returnResult = json_encode($row);
        $inoutPresent = 1;
        echo $returnResult;
    }
    $count = $db->countRows($checkInPerDayTransactionResult);
    if( $inoutPresent == 0 && $db->countRows($checkInPerDayTransactionResult) > 0 ) {
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
    }  elseif ($inoutPresent == 0 && $db->countRows($checkInPerDayTransactionResult) == 0 )  {
        $returnResult = json_encode(array());
        echo $returnResult;
    }
}

# View for displaying the pending in/out details for an employee
if(isset($_REQUEST['viewInOutPendingForEmployee'])) 
{
    echo "<div class='panel panel-primary'>
			<div class='panel-heading text-center'>
				<strong style='font-size:20px;'>Pending In/Out Details</strong>
			</div>
			<div class='panel-body'>";
			    $empList=array($_SESSION['u_empid']);
			    echo "<table class='table table-hover inouttableclass'>
			            <tbody>
			                <tr class='info'>
			                    <th>Sr. No.</th>
			                    <th>Emp Name</th>
			                    <th>Actions</th>
			                    <th></th>
						</tr>";
			    $countRows = 0;
			    foreach ($empList as $emp) {
			        $query = "select distinct(transactionid) from empinoutapproval where empid='".$emp."' and status='Pending'";
			        $result = $db->query($query);
			        $count=1;   
			        while($row = $db->fetchAssoc($result)) {
			            $countRows++;
			            $getinoutfortransactionQuery="select * from empinoutapproval where transactionid='".$row['transactionid']."'";
			            $getinoutfortransactionResult=$db->query($getinoutfortransactionQuery);
			            echo '<tr><td>'.$count++.'</td>';
			            echo '<td>'.$_SESSION['u_fullname'].'</td>';
			            echo '<td>Click on the row to Edit/Modify</td>';
			            echo '<td><div class="arrow"></div></td></tr>';
			            echo '<tr>
								<td>
								<table class="table table-hover">
									<tr class="info">
			                            <th>Date</th>
			                            <th>In Time</th>
			                            <th>Out Time</th>
			                            <th>Reason</th>
								    </tr>';
			            $x=0;
			            while($getinoutfortransactionRow=$db->fetchAssoc($getinoutfortransactionResult)) {
			                echo '<tr></tr>';
			                echo '<tr><td>' . $getinoutfortransactionRow['date'] . '</td>';
			                echo '<td>' . $getinoutfortransactionRow['intime'] . '</td>';
			                echo '<td>' . $getinoutfortransactionRow['outtime'] . '</td>';
			                echo '<td>' . $getinoutfortransactionRow['reason'] . '</td></tr>';
			            }  
			            echo '<tr></tr>
			            	<tr><td>
			               		<button onclick="editInOutForEmployee(\''.$row['transactionid'].'\')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
								<button onclick="deleteInOutForEmployee(\''.$row['transactionid'].'\')"><i class="fa fa-trash" aria-hidden="true"></i></button>
			                 </td></tr>
                       	</table>
                 	</td>
                 </tr>
               	<tr></tr>';
            $x++;
        }
    }
    if($countRows == 0) {
         echo "<tr><td colspan='3'><center><b>No Data Available</b></center></td></tr>";
    }
    echo "</tbody></table>";
    echo '<script>
                $("document").ready(function() {
                    $(".inouttableclass tr:odd").addClass("odd");
                    $(".inouttableclass tr:not(.odd)").hide();
                    $(".inouttableclass tr:first-child").show();
                    $(".inouttableclass tr.odd").click(function() {
                        $(this).next("tr").toggle();
                        $(this).find(".arrow").toggleClass("up");
                    });
                });

                function editInOutForEmployee(tid)
                {
                    $("#loadinout").load("applyinout.php?editInOutForEmployee=1&tid="+tid);
                }

                function deleteInOutForEmployee(tid)
                {
                    $("#loadinout").load("applyinout.php?deleteInOutForEmployee=1&tid="+tid);
                }
            </script>';
    echo '<style>
            #inout div.arrow {
                background: transparent url("public/images/arrows.png") no-repeat scroll 0 -16px;
                display: block;
                height: 16px;
                width: 16px;
            }
        </style>';
}

# TView for displaying the Approved in/out details for an employee
if(isset($_REQUEST['viewInOutDetailsHistory'])) 
{
    echo "<div class='panel panel-primary'>
				<div class='panel-heading text-center'>
					<strong style='font-size:20px;'> In/Out Detail History</strong>
				</div>
				<div class='panel-body'>";
    $empList=array($_SESSION['u_empid']);
    echo "<table class='table table-hover inout'>
            <tbody>
                <tr class='info'>
                    <th>Sr. No.</th>
                    <th>Emp Name</th>
                    <th>Date</th>
                    <th>In Time</th>
                    <th>Out Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Comments</th>
                </tr>";
    foreach ($empList as $emp) {
        $query = "select * from empinoutapproval where empid='".$emp."' order by date asc";
        $result = $db->query($query);
        if($db->countRows($result) > 0) {
            $count = 0;
            while($row=$db->fetchAssoc($result)) 
            {
                echo "<tr>
                        <td>".$count++."</td>
                        <td>".getempName($row['empid'])."</td>
                        <td>".$row['date']."</td>
                        <td>".$row['intime']."</td>
                        <td>".$row['outtime']."</td>
                        <td>".$row['reason']."</td>
                        <td>".$row['status']."</td>
                        <td>".$row['comments']."</td>
                      </tr>";      
            }
        } else {
            echo "<tr>
                    <td colspan='8'><b><center>No Data available</b></center></td>
                 </tr>";
        }
    }
    echo "</table></div></div>";
}

# View for modifying In/Out Details which are in pending state for employee
if(isset($_REQUEST['editInOutForEmployee'])) 
{
    $tid=$_REQUEST['tid'];
    $query="SELECT * FROM `empinoutapproval` WHERE transactionId='$tid' and status='Pending'";
	$result=$db->query($query);
    if($db->countRows($result) > 0 ) {
        echo "<div class='panel panel-primary'>
				<div class='panel-heading text-center'>
					<strong style='font-size:20px;'>Modify In/Out Details</strong>
				</div>
				<div class='panel-body'>";
        echo "<form name='modifyEmpInOut' id='modifyEmpInOut' method='POST' action='applyinout.php?modifyInOutToTable=1'>";
        echo "<div class='form-group'>
				<div class='row'>
					<div class='col-sm-3'></div>									
        			<div class='col-sm-3'><label>Employee ID:</label></div>
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
                while($row = $db->fetchAssoc($result)) {
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
            </div>
			<script>
				   $(document).ready(function(){
							$('.open-datetimepicker').datepicker({
								dateFormat: 'yy-mm-dd',
		                        minView : 2,
		                        autoclose: true     
							});
			    		});
			   </script>";
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
                                		$('#loadinout').load('applyinout.php?viewInOutPendingForEmployee=1');
								     });
								  }
                            	else if(response.trim() == "notsuccess"){
                                    BootstrapDialog.alert('Not Modified In/Out details. Please Contact AIBI', function(){
                                    	$('#loadinout').load('applyinout.php?viewInOutPendingForEmployee=1');
                                    });
                                }
                            }
                        });
                    }  else {
                            $("#loadinout").load("applyinout.php?viewInOutPendingForEmployee=1");
                    }
					});
                    return false;
                }
			 });
			 
			$.validator.addClassRules({
				dateInOut: {
		            required: true
				           },
		           reason: {
			            required: true
		           }
			});

			$('body').on('click','.dateInOut',function(){
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
	$result=$db->query($query);
    if($db->countRows($result) > 0 ) {
        $deleteTransaction = "UPDATE `empinoutapproval` SET status='Cancelled', comments='Cancelled By Employee' where transactionId='".$tid."'";
        $deleteResult = $db->query($deleteTransaction);
        if($deleteResult) {
            echo "<script>
                BootstrapDialog.alert('Deleted Successfully');
                $('#loadinout').load('applyinout.php?viewInOutPendingForEmployee=1');
            </script>";            
        }
    }
}

# View for modifying  In/Out details applied by employee
if(isset($_REQUEST['modifyInOutToTable'])) {
    $tid=$_REQUEST['tid'];
    $count = count($_REQUEST['inout_day']);
    for($i=0;$i<$count;$i++) {
        $query="UPDATE  `empinoutapproval` SET intime='".$_REQUEST['intime'][$i]."',outtime='".$_REQUEST['outtime'][$i]."',".
                        "reason='".$_REQUEST['reason'][$i]."' where date='".$_REQUEST['inout_day'][$i]."' and transactionid='".$tid."'";
        $result=$db->query($query);
    }
    if($result) {
            $cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$_SESSION['u_empid'].' modifyinout >> /dev/null &';
	        exec($cmd);
	        echo "success";
            /*echo "<script>
                BootstrapDialog.alert('Modified In/Out details Successfully');
                $('#loadinout').load('applyinout.php?viewInOutPendingForEmployee=1');
            </script>";*/
    }  else {
    		echo "notsuccess";
       /* echo "<script>
                BootstrapDialog.alert('Not Modified In/Out details. Please Contact AIBI');
                $('#loadinout').load('applyinout.php?viewInOutPendingForEmployee=1');
            </script>";*/
    }
}

# View for adding the In/Out Details to table added by employee
if(isset($_REQUEST['addInOutToTable'])) 
{
	$tid=generate_transaction_id();
	$transactionidArray=array();
	for($j=0; $j<count($_REQUEST['inout_day']); $j++)
	{
		if($_REQUEST['inout_day'][$j]!="") 
		{
			$insertInOut="INSERT into `empinoutapproval` (createdAt,createdBy,transactionId,empid,date,intime,outtime,reason,status)".
             "values('".date("Y-m-d H:i:s")."','".$_SESSION['u_empid']."','".$tid."','".$_SESSION['u_empid']."',".
             "'".$_REQUEST['inout_day'][$j]."','".$_REQUEST['intime'][$j]."','".$_REQUEST['outtime'][$j]."','".$_REQUEST['reason'][$j]."','Pending')";
			$result=$db->query($insertInOut);
    	}	
	}	
	$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$_SESSION['u_empid'].' applyinout >> /dev/null &';
	exec($cmd);
	echo "success";
} 


# Below conditions are for Manager section

# View for approving In/Out Details 
if(isset($_REQUEST['approveInOut'])) 
{
    $tid=$_REQUEST['tid'];
    $query="SELECT * FROM `empinoutapproval` WHERE transactionId='$tid' and status='Pending'";
	$result=$db->query($query);
    if($db->countRows($result) > 0 ) {
        while($empinoutapprovalRow=$db->fetchAssoc($result)) {
            $inoutResult=$db->query("SELECT * FROM `inout` WHERE empid='".$empinoutapprovalRow['empid']."' and Date='".$empinoutapprovalRow['date']."'");        
            if($db->countRows($inoutResult) > 0) {
                # Already information is avaialable for that emp and for that date, so update the inout row
                $updateInOutQuery="UPDATE `inout` SET First='".$empinoutapprovalRow['intime']."',Last = '".$empinoutapprovalRow['outtime']."',".
                    "reason='".$empinoutapprovalRow['reason']."',added_hrname='".$_SESSION['u_fullname']."',state='Data Exists' WHERE ".
                    "EmpId='".$empinoutapprovalRow['empid']."'and Date='".$empinoutapprovalRow['date']."'";

                $updateResult=$db->query($updateInOutQuery);
                # Update the empinoutapproval status as Approved.
                $updateempinoutapproval=$db->query("UPDATE `empinoutapproval` SET status='Approved' where transactionId='".$tid."'");
	        } else {
                # No Information is present in inout table for that emp for that date. So insert into inout table
                $insertInOutQuery="INSERT into `inout` (EmpID,EmpName,Department,Date,First,Last,reason,added_hrname,state)".
                "values('".$empinoutapprovalRow['empid']."','".getempName($empinoutapprovalRow['empid'])."',
                '".getDeptByEmpid($empinoutapprovalRow['empid'])."','".$empinoutapprovalRow['date']."',".
                "'".$empinoutapprovalRow['intime']."','".$empinoutapprovalRow['outtime']."','".$empinoutapprovalRow['reason']."',
                '".$_SESSION['u_fullname']."','Data Exists')";
			    $insertResult=$db->query($insertInOutQuery);

                # Update the empinoutapproval status as Approved.
                $updateempinoutapproval=$db->query("UPDATE `empinoutapproval` SET status='Approved' where transactionId='".$tid."'");
            }
        }
        $query="SELECT * FROM `empinoutapproval` WHERE transactionId='$tid'";
        $getResult=$db->query($query);
        $empinoutapprovalRow=$db->fetchAssoc($getResult);

        $cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$empinoutapprovalRow['empid'].' approveinout >> /dev/null &';
	    exec($cmd);
        
        echo "<script>
                BootstrapDialog.alert('Approved In/Out details for ".getempName($empinoutapprovalRow['empid'])."');
                $('#loadinout').load('applyinout.php?viewInOutForManager=1');
             </script>";
    } 
} 

# View to display form for not approval of In/Out Details
if(isset($_REQUEST['notapproveInOut'])) 
{
        $tid=$_REQUEST['tid'];
        $result=$db->query("SELECT * FROM `empinoutapproval` WHERE transactionId='$tid' and status='Pending'");
        $row=$db->fetchAssoc($result);

        echo '<form id="notapproveInOutForm" method="POST" action="applyinout.php?AddNotApproveInformation=1&tid='.$tid.'">';
		echo '<div class="panel panel-primary">
				<div class="panel-heading text-center">
					<strong style="font-size:20px;">Not Approve In/Out Details</strong>
				</div>
				<div class="panel-body table-responsive">
				    <div class="form-group">
						<div class="row">
							<div class="col-sm-2"></div>
							<div class="col-sm-4"><label>Employee Id:</label></div>
							<div class="col-sm-4"> '.$row['empid'].'</div>
                   			<div class="col-sm-2"></div>
			        	</div>
            		</div>
                     
				    <div class="form-group">
						<div class="row">
							<div class="col-sm-2"></div>
							<div class="col-sm-4"><label>Comments for Not Approving:</label></div></td>
			            	<div class="col-sm-4">
                            	<textarea class="form-control" name="comments"></textarea>
                        	</div>
                   			<div class="col-sm-2"></div>
			        	</div>
            		</div>
                    <div class="form-group">
						<div class="row">
							<div class="col-sm-12 text-center">
                            	<input class="btn btn-primary" type="submit" name="submit" value="Submit"/>
                        	</div>
                    	</div>
            		</div>
			  </div>
            </div>';
        echo '</form>';
        echo "<script>
                $('#notapproveInOutForm').submit(function() {
					$.ajax({
                        data : $(this).serialize(),
                        type : $(this).attr('method'),
                        url : $(this).attr('action'),
                        success : function(response) {
                            if(response.trim() ==='success') {
                                BootstrapDialog.alert('Comments Submitted Successfully.');
                                $('#loadinout').load('applyinout.php?viewInOutForManager=1');
                            } else {
                                BootstrapDialog.alert('Comments Not Submitted. Please Contact AIBI Team');
                                $('#loadinout').load('applyinout.php?viewInOutForManager=1');
                            }
                        }
				});
				return false;
			});
             </script>";       
} 
# View to update the not approval information to database
if(isset($_REQUEST['AddNotApproveInformation']))
{
	$tid=$_REQUEST['tid'];
	$result=$db->query("SELECT * FROM `empinoutapproval` WHERE transactionId='$tid'and status='Pending'");
	if($db->countRows($result) > 0 ) {
		$successVal=1;
    	while($empinoutapprovalRow=$db->fetchAssoc($result)) {
        	# Update the empinoutapproval status as Approved.
        	$updateempinoutapproval=$db->query("UPDATE `empinoutapproval` SET status='Not Approved', comments='".$_REQUEST['comments']."' where transactionId='".$tid."'");
        	if($updateempinoutapproval) {
            	$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$empinoutapprovalRow['empid'].' notapproveinout >> /dev/null &';
	        	exec($cmd);
	        	//echo "success";
           	} else {
		    	$successVal=0;
            }
      	}
	    if ($successVal == 1) {
			echo "success";
	    } else {
			echo "not success";
        }
   	}
} 


function getEmployeePendingInOut($dept,$location,$empId) {
	global $db;
	$query="select * from emp where dept='".$dept."' and state='Active'";
	if($location!="") {
		$query=$query." and location = '".$location."'";
	}
	if($empId!="ALL") {
		$query=$query." and empid='".$empId."'";
	}
	$getEmployeesQueryResult=$db -> query($query);
	$childern=array();
	for ($i=0;$i<$db->countRows($getEmployeesQueryResult);$i++)
	{
		$result = $db->fetchArray($getEmployeesQueryResult);
		array_push($childern,$result['empid']);
	}
	$pendingInOutCount=0;
	$count=0;
	$pendingLeaves=0;

	echo "<h3>".$dept."</h3>";					//	Accordian Heading
	echo "<div>";								// Accordian Body
	echo "<table class='table table-bordered inouttableclass'>
            <thead>
                <tr class='displayRow'>
                    <th>Sr. No.</th>
                    <th>Emp Name</th>
                    <th>Actions</th>
                    <th></th>
			</tr>
        	</thead>
            <tbody>";
	$rowCount = 0 ;
	foreach ($childern as $emp) {
		if($emp==$_SESSION['u_empid']) {
			continue;
		}
		$query = "select distinct(transactionid) from empinoutapproval where empid='".$emp."' and status='Pending'";
		$result = $db->query($query);
		$count=1;
		while($row = $db->fetchAssoc($result)) {
			$rowCount++;
			$getinoutfortransactionQuery="select * from empinoutapproval where transactionid='".$row['transactionid']."'";
			$getinoutfortransactionResult=$db->query($getinoutfortransactionQuery);
			echo '<tr class="displayRow"><td>'.$count++.'</td>';
			echo '<td>'.getempname($emp).'</td>';
			echo '<td>Click on the row to Approve/notapprove</td>';
			echo '<td><div class="arrow"></div></td></tr>';
			echo '<tr class=hideRow>
					<td colspan="6">
					<table class="table table-hover">
						<tr class="info">
                            <th>Date</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Reason</th>
					    </tr>';
            $x=0;
            while($getinoutfortransactionRow=$db->fetchAssoc($getinoutfortransactionResult)) {
                echo '<tr></tr>';
                echo '<tr><td>' . $getinoutfortransactionRow['date'] . '</td>';
                echo '<td>' . $getinoutfortransactionRow['intime'] . '</td>';
                echo '<td>' . $getinoutfortransactionRow['outtime'] . '</td>';
                echo '<td>' . $getinoutfortransactionRow['reason'] . '</td></tr>';
            }  
            echo '<tr></tr><tr><td><button class="btn btn-danger" onclick="notapprove(\''.$row['transactionid'].'\')">Not Approve</button></td>';
			echo '<td><button class="btn btn-success" onclick="approve(\''.$row['transactionid'].'\')">Approve</button></td>';
			echo '</tr>';
			echo '</table></td></tr><tr></tr>';
			$x++;
			$pendingInOutCount++;
		}
	}
	if($rowCount == 0) {
		echo "<tr><td colspan='3'><center><b>No Data Available</b></center></td></tr>";
	}
	echo "</tbody></table>";
	echo "<table id='table-2'>
			<tr>
				<td><b>Total Pending InOut Applications</b>
				<td class='teamPendingInOut'><b>".$pendingInOutCount."</b></td>
			</tr></table>";
	echo "</div>";
}


# View for display pending In/Out Details for all childern coming under the manager
if(isset($_REQUEST['viewInOutForManager'])) {
	// style information of the page
	echo '
    <style>            		
    .inouttableclass div.arrow {
                background: transparent url("images/arrows.png") no-repeat scroll 0 -16px;
                display: block;
                height: 16px;
                width: 16px;
    }
            		
	.inouttableclass {
		background-color: #f2f2f2;
		width: 100%;
		-webkit-border-radius: 6px;
		-moz-border-radius: 6px;
	}
	
	.inouttableclass th {
	
		color: #fff;
		padding: 7px 15px;
		padding: 5px;
		color: #333;
		font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size: 15px;
		line-height: 20px;
		font-style: normal;
		font-weight: normal;
		text-shadow: white 1px 1px 1px;
		background: #F5D0A9;
	}
	
	.inouttableclass td {
	
		color: #000;
		padding: 7px 15px;
		padding: 5px;
		color: #333;
		line-height: 20px;
		font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size: 14px;
		border-bottom: 1px solid #fff;
		border-top: 1px solid #fff;
	}
	
	.inouttableclass thead {
		font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
		padding: .2em 0 .2em .5em;
		text-align: left;
		color: #4B4B4B;
		background-color: #C8C8C8;
		background-image: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2),
				to(#e3e3e3), color-stop(.6, #B3B3B3) );
						background-image: -moz-linear-gradient(top, #D6D6D6, #B0B0B0, #B3B3B3 90%);
								border-bottom: solid 1px #999;
	}
	
	.inouttableclass tr.odd td {
		cursor: pointer;
	}
	
	.inouttableclass div.arrow {
		background: transparent url(../images/arrows.png) no-repeat scroll 0px
		-16px;
		width: 16px;
		height: 16px;
		display: block;
	}
	
	.inouttableclass div.up {
		background-position: 0px 0px;
	}
	.inouttableclass td:hover {
		background-color: #fff;
	}
    </style>';
	
	
	// Gather information
	if(isset($_REQUEST['UGroup'])) {
		$grp = $_REQUEST['UGroup'];
	}
	if (isset($_REQUEST['getDeptemp'])) {
		$getDeptemp = $_REQUEST['getDeptemp'];
	} else {
		$getDeptemp = "";
	}
	if (isset($_REQUEST['UDept'])) {
		$getDept = $_REQUEST['UDept'];
	} else {
		$getDept = "";
	}
	if (!isset($_REQUEST['location'])) {
		$_REQUEST['location']="";
	}
	echo "<div id='untrackedLeaveData'>";
	echo "<br><u><h2><center>Pending InOut Information details for location: ".$_REQUEST['location']." ";
	
	if (isset($_REQUEST['getDeptemp'])) {
		echo getempName($_REQUEST['getDeptemp']);
	}
	if (isset($_REQUEST['UDept'])) {
		echo " (" . $_REQUEST['UDept'] . ")</center></h2></u><br>";
	} else {
		echo "</center></h2></u><br>";
	}
	
	
	# Gather employees based on employee leavel
	
	# Gather employees if employee is HR
	if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
		if ($getDept == 'ALL') {
			$query = "SELECT distinct(dept) FROM `emp`  where location = '".$_REQUEST['location']."' ORDER BY `dept` ASC";
			$self = 1;
		} elseif ($getDeptemp == 'ALL') {
			$query = "SELECT * FROM emp WHERE dept='" . $getDept . "'  and location = '".$_REQUEST['location']."' and state='Active'";
			$self = 1;
		} elseif ($getDept == $_SESSION['u_empid']) {
			$query = "SELECT * FROM emp WHERE `empid` = '" .$_SESSION['u_empid']. "' and state='Active'  and location = '".$_REQUEST['location']."' ORDER BY `emp`.`empname` ASC";
		} else {
			$query = "SELECT * FROM `emp` WHERE `empid` = '".$_REQUEST['getDeptemp']."' and state='Active'  and location = '".$_REQUEST['location']."' ORDER BY `emp`.`empname` ASC";
		}
		$result = $db -> query($query);
		if ($getDept == 'ALL') {
			echo '<div id="accordion-new">';
			while ($row = mysql_fetch_assoc($result)) {
				getEmployeePendingInOut($row['dept'],$_REQUEST['location'],$_REQUEST['getDeptemp']);
			}
			echo "</div>";
		} else {
			if ($getDeptemp == 'ALL') {
				echo '<div id="accordion-new">';
				getEmployeePendingInOut($getDept,$_REQUEST['location'],$_REQUEST['getDeptemp']);
				echo "</div>";
			} else {
				echo '<div id="accordion-new">';
				getEmployeePendingInOut($getDept,$_REQUEST['location'],$_REQUEST['getDeptemp']);
				echo "</div>";
			}
		}
	} else if (strtoupper($_SESSION['user_desgn']) == 'MANAGER') {
		# Gather employee list if employee is a manager
		if ($getDept == 'ALL') {
			$query = "SELECT distinct(dept) FROM emp WHERE managerid='".$_SESSION['u_empid']."' and state='Active'";
			$self = 1;
		} elseif ($getDeptemp == 'ALL') {
			$query = "SELECT distinct(dept) FROM emp WHERE dept='".$getDept."' and state='Active'";
		} elseif ($getDept == $_SESSION['u_empid']) {
			$query = "SELECT distinct(dept) FROM emp WHERE `empid` = '".$_SESSION['u_empid']."' and state='Active' ORDER BY `emp`.`empname` ASC";
		} else {
			if ($_SESSION['u_managerlevel'] != 'level1') {
				$query = "SELECT distinct(dept) FROM emp WHERE `empid` = '".$_REQUEST['getDeptemp']."' and state='Active' ORDER BY `emp`.`empname` ASC";
			} else {
				if($grp=="ALL") {
					$query = "SELECT distinct(dept) FROM emp WHERE managerid='".$_SESSION['u_empid']."' and state='Active'";
				} else {
					$query = "SELECT distinct(dept) FROM emp WHERE `empid` = '".$grp."' and state='Active'  ORDER BY `emp`.`empname` ASC";
				}
			}
		}
		$result = $db -> query($query);
		if ($getDept == 'ALL') {
			echo '<div id="accordion-new">';
			while ($row = mysql_fetch_assoc($result)) {
				getEmployeePendingInOut($row['dept'],$_REQUEST['location'],$_REQUEST['getDeptemp']);
			}
			echo "</div>";
		} elseif ($getDeptemp == 'ALL') {
			echo '<div id="accordion-new">';
			while ($row = mysql_fetch_assoc($result)) {
				getEmployeePendingInOut($row['dept'],$_REQUEST['location'],$_REQUEST['getDeptemp']);
			}
			echo "</div>";
		} elseif ($getDept == $_SESSION['u_empid']) {
			echo '<div id="accordion-new">';
			while ($row = mysql_fetch_assoc($result)) {
				getEmployeePendingInOut($row['dept'],$_REQUEST['location'],$_SESSION['u_empid']);
			}
			echo "</div>";
		} else {
			if ($_SESSION['u_managerlevel'] != 'level1') {
				echo '<div id="accordion-new">';
				while ($row = mysql_fetch_assoc($result)) {
					getEmployeePendingInOut($row['dept'],$_REQUEST['location'],$_REQUEST['getDeptemp']);
				}
				echo "</div>";
			} else {
				if($grp=="ALL") {
					echo '<div id="accordion-new">';
					while ($row = mysql_fetch_assoc($result)) {
						getEmployeePendingInOut($row['dept'],$_REQUEST['location'],$grp);
					}
					echo "</div>";
				} else {
					echo '<div id="accordion-new">';
					while ($row = mysql_fetch_assoc($result)) {
						getEmployeePendingInOut($row['dept'],$_REQUEST['location'],$grp);
					}
					echo "</div>";
				}
			}
		}
	}
	
    echo '<script>

            $("document").ready(function() {
                    $( "#accordion-new" ).accordion({
                        heightStyle: "content",
                        collapsible: true
                    });
           
                    $.each( $( "#accordion-new h3"), function( i, val ) {
                           var first=$($(val)).next().find(".teamPendingInOut").text();
                           if ( first > 0 ) { 
                               var firstString = "<font color=red>"+$(val).next().find(".teamPendingInOut").text();
                           }  else { 
                               var firstString =$(val).next().find(".teamPendingInOut").text();
                           }
                           $(val).html("<table id=\"table-2\"><tr><td width=\"30%\"><b>"+$(val).text()+"</b></td><td width=\"70%\"><table id=\"table-2\"><tr><td>Total Pending InOut Applications: "+firstString+"</td></tr></table>");
                   });

					$(".dispalyRow").show();
	            	$(".hideRow").hide();
	            	$(".displayRow").click(function(){
		                $(this).next("tr").toggle();
		                $(this).find(".arrow").toggleClass("up");
            		});
                });

                function approve(tid)
                {
                    $("#loadinout").load("applyinout.php?approveInOut=1&tid="+tid);
                }

                function notapprove(tid)
                {
                    $("#loadinout").load("applyinout.php?notapproveInOut=1&tid="+tid);
                }
            </script>';
    
    $db -> closeConnection();
}

if(isset($_REQUEST['viewFormInOutForManager'])) {
    // Get the role of the user
    if(isset($_SESSION['user_desgn']))
    {
            if(strtoupper($_SESSION['user_desgn'])=="MANAGER") {$divid="loadinout";echo "<script>var divid=\"loadinout\";</script>"; }
            if(strtoupper($_SESSION['user_desgn'])=="HR") { $divid="loadinout";echo "<script>var divid=\"loadinout\";</script>";}
    }
    $untrackedLeaves=0;
    
    # Generate departments based on the level of the employee
    $deps = "<option selected value=\"ALL\">ALL</option>";
    
    if(isset($_SESSION['u_emplocation'])) {
        $defaultLocation=$_SESSION['u_emplocation'];
    }
    
    # Departments for HR
    if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
        $querydept = "SELECT distinct(dept) FROM `emp`  where location='".$defaultLocation."' ORDER BY dept ASC";
        $resultdept = $db -> query($querydept);
    } else if (strtoupper($_SESSION['user_desgn']) == 'MANAGER') {
        # Departments for manager
        $deps = "";
        if ($_SESSION['u_managerlevel'] != 'level1') {
            $query = "SELECT distinct(dept) FROM `emp` WHERE managerid='" . $_SESSION['u_empid'] . "' and state='Active' ORDER BY empname ASC";
            $result = $db -> query($query);
            $deps = " <option selected value=\"none\">NONE</option>";
            $deps = $deps." <option value=\"ALL\">ALL</option>";
        } else {
            $query = "SELECT * FROM `emp` WHERE managerid='".$_SESSION['u_empid']."' and state='Active' ORDER BY empname ASC";
            $result = $db -> query($query);
            $deps = " <option selected value=\"ALL\">ALL</option>";
        }
            
        if ($_SESSION['u_managerlevel'] != 'level1') {
            while ($row = mysql_fetch_assoc($result)) {
                $deps = $deps . '<option value="' . $row["dept"] . '">';
                $deps = $deps . $row["dept"];
                $deps = $deps . '</option>';
            }
        } else {
            while ($row = mysql_fetch_assoc($result)) {
                $deps = $deps . '<option value="' . $row["empid"] . '">';
                $deps = $deps . $row["empname"];
                $deps = $deps . '</option>';
            }
        }
    } else {
        # Name of Individual employee
        $query = "SELECT * FROM `emp` WHERE empusername='".$_SESSION['user_name']."' and state='Active'";
        $result = $db -> query($query);
        $deps = "";
        $deps = $deps . '<option value="'.$_SESSION["u_empid"].'">';
        $deps = $deps . $_SESSION['u_fullname'];
        $deps = $deps . '</option>';
    }
    
    $typeofday = "";
    //Department name
    $department = '<option value="none">';
    $department = $department . "None";
    $department = $department . '</option>';
    if(isset($resultdept) && $resultdept) {
        $department = $department . '<option value="ALL">';
        $department = $department . "ALL";
        $department = $department . '</option>';
        while ($row = mysql_fetch_assoc($resultdept)) {
            $department = $department . '<option value="' . $row["dept"] . '">';
            $department = $department . $row["dept"];
            $department = $department . '</option>';
        }
    }
    ?>
    <table id='table-2'>
    <tr>
        <td>
        <form id="teamInOutApprovalId" name="teamInoutApprovalForm" method="post" action="applyinout.php?viewInOutForManager=1">
            <fieldset>
                <legend>
                    Pending Leave Information
                </legend>
                <table id='table-2'>
                    <?php
                    if($_SESSION['user_dept']=="HR") {
                        #  Get the distinct locations
                        $queryLocation = "SELECT distinct(location) FROM `emp` where location != '' ORDER BY location ASC";
                        $resultLocation = $db -> query($queryLocation);
                        
                        # Location selection Box Options
                        $locationSelect='';
                        if($db->hasRows($resultLocation)) {
                            while ($row = mysql_fetch_assoc($resultLocation)) {
                                if($_SESSION['u_emplocation']==$row['location']) {
                                    $locationSelect = $locationSelect . '<option value="' . $row["location"] . '" selected>';
                                    $locationSelect = $locationSelect . $row["location"];
                                    $locationSelect = $locationSelect . '</option>';
                                } else {
                                    $locationSelect = $locationSelect . '<option value="' . $row["location"] . '">';
                                    $locationSelect = $locationSelect . $row["location"];
                                    $locationSelect = $locationSelect . '</option>';
                                }
                            }
                        } 
                        echo "<tr>
                            <td>Select Location:</td>
                            <td>
                                <select id='location' name='location'>
                                    $locationSelect.'
                                </select>
                            </td>
                        </tr>";
                    } 
                    if (($_SESSION['u_managerlevel'] != 'level1') || ($_SESSION['user_dept'] == 'HR')) {
                        if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
                            echo '<tr>
                                    <td>Department:</td>
                                    <td><select id="hideDept" size="0" name="UDept">
                                    ' . $department . '
                                    </select>
                                    </td>
                                    </tr>';
                            echo '<tr id="hideName" style="display:none">
                                    <td>Name:</td>
                                    <td>
                                    <select id="getEmpName" size="0" name="getDeptemp"></select>
                                    </td>
                                    </tr>';
                        } else {
                            echo '<tr>
                                    <td>Department:</td>
                                    <td><select id="hideDept" size="0" name="UDept">
                                    ' . $deps . '
                                    </select>
                                    </td>
                                    </tr>';
                            echo '<tr id="hideName" style="display:none">
                                    <td>Name:</td>
                                    <td>
                                    <select id="getEmpName" size="0" name="getDeptemp"></select>
                                    </td>
                                    </tr>';
                        }
                    } else {
                        echo '<tr>
                            <td id="hideEmpName">Name:</td>
                            <td><select size="0" name="UGroup" id="UGroup">
                            '.$deps.'
                            </select>
                            </td>
                            </tr>';
                    }
                    ?>
                    <tr>
                    <td rowspan="3" align="center">
                    <input type="submit" class="submitBtn" value="Submit" name="TrackAttInd">
                    </td>
                    </tr>
                </table>
            </fieldset>
        </form></td>
    </tr>
</table>
<div id='loadingmessage' style='display:none'>
    <img align="middle" src='images/loading.gif'/>
</div> 

<!-- Javascript for this view-->
<script>
    $("document").ready(function() {
        $('#location').change(function() {
                    var location=$(this).val();
                    $.post( 'getSplLeaveOptions.php?location='+location, function(data) {
                        $('#hideDept').empty();
                        $('#hideDept').append(data);
                    });
        });
		
        $('#teamInOutApprovalId').submit(function() {
            if ( $("#hideDept").length!=0 && $("#hideDept").val().toUpperCase() == "NONE" ) {
                alert("Please selct the Department");
                return false;
            }
            
            if ( $("#hideDept").val()=="ALL" && $("#getEmpName").val()=="ALL") {
                alert("Please wait for few minutes to get the results for all ECI Employeees.It will take more than a minute.");
            }
            $('#loadingmessage').show();
            $.ajax({
                data : $(this).serialize(),
                type : $(this).attr('method'),
                url : $(this).attr('action'),
                success : function(response) {
                    $('#'+divid).html(response);
                    if($("#loadingmessage")) {
                        $("#loadingmessage").hide();
                    }
                }
            });
            return false;
        });
    });
	</script>
    <?php
		echo '
		<script>
		$("document").ready(function() {
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
		});
		</script>';
	?>
<?php   
}
?> 



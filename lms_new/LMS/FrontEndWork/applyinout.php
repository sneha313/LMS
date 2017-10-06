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
									<span class='fa fa-calendar open-datetimepicker'></span>
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
				<input type='submit' class='btn btn-info' value='Add Row'  id='add' onclick='generateInOutDays()'/>
				<input type='submit' class='btn btn-success' value='Apply and Send Mail' name='submit'>
			</div>
		</div>
	</div>
</form></div>
</div>
 <script>
	$(document).ready(function(){
		$('.open-datetimepicker').datetimepicker({
			format: 'yyyy-mm-dd',
		    minView : 2,
		   	autoclose: true     
		});
	});
			   </script>";
?>
    <link rel="stylesheet" type="text/css" href="public/js/jqueryPlugins/jquery.timepicker.css"/> 
    <script type="text/javascript" src="public/js/jqueryPlugins/jquery.timepicker.min.js"></script>
	<script>
		$('#applyEmpInOut').submit(function() {
			$(this).find(':input[type=submit]').replaceWith('<center><img src="img/loader.gif" class="img-responsive" alt="processing"/></center>');
		});
		$("document").ready(function() {
	            
            $(".intime,.outtime").timepicker({ 
				'timeFormat': 'H:i:s', 
				'step' : 15
			});

			$("#applyEmpInOut").validate({
                submitHandler: function() {
                    if (BootstrapDialog.confirm("Do you want to apply InOut Information?"))
                    {
                        $.ajax({ 
                            data: $('#applyEmpInOut').serialize(), 
                            type: $('#applyEmpInOut').attr('method'), 
                            url:  $('#applyEmpInOut').attr('action'), 
                            success: function(response) { 
                                $('#loadinout').html(response);
                            }
                        });
                    }  else {
                            $("#loadinout").load("applyinout.php?viewInOutPendingForEmployee=1");
                    }
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
                                                'Out Time : '+obj.Last);
                                        if (BootstrapDialog.confirm("Do you still want to modify In Time and Out Time for Date: "+obj.Date+"?")) {
                                            $(this).closest('td').next().find('.intime').val(obj.First);
                                            $(this).closest('td').next().next().find('.outtime').val(obj.Last); 
                                            if(typeof obj.reason != "undefined") {
                                                $(this).closest('td').next().next().next().find('.reason').val(obj.reason); 
                                            }
                                        }
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
            #table-2 div.arrow {
                background: transparent url("images/arrows.png") no-repeat scroll 0 -16px;
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
					<strong style='font-size:20px;'>Pending In/Out Details</strong>
				</div>
				<div class='panel-body'>";
    $empList=array($_SESSION['u_empid']);
    echo "<table class='table table-hover'>
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
							$('.open-datetimepicker').datetimepicker({
								format: 'yyyy-mm-dd',
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
		$('#modifyEmpInOut').submit(function() {
			$(this).find(':input[type=submit]').replaceWith('<center><img src="img/loader.gif" class="img-responsive" alt="processing"/></center>');
		});
		$("document").ready(function() {
	            
            $(".intime,.outtime").timepicker({ 
				'timeFormat': 'H:i:s', 
				'step' : 15
			});

			$("#modifyEmpInOut").validate({
                submitHandler: function() {
                    if (BootstrapDialog.confirm("Do you want to modify InOut Information?"))
                    {
                        $.ajax({ 
                            data: $('#modifyEmpInOut').serialize(), 
                            type: $('#modifyEmpInOut').attr('method'), 
                            url:  $('#modifyEmpInOut').attr('action'), 
                            success: function(response) { 
                                $('#loadinout').html(response);
                            }
                        });
                    }  else {
                            $("#loadinout").load("applyinout.php?viewInOutPendingForEmployee=1");
                    }
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
            echo "<script>
                BootstrapDialog.alert('Modified In/Out details Successfully');
                $('#loadinout').load('applyinout.php?viewInOutPendingForEmployee=1');
            </script>";
    }  else {
        echo "<script>
                BootstrapDialog.alert('Not Modified In/Out details. Please Contact AIBI');
                $('#loadinout').load('applyinout.php?viewInOutPendingForEmployee=1');
            </script>";
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
	echo '<script>
				BootstrapDialog.alert("In/Out Details is submitted. Approval is pending");
				$("#loadinout").load("applyinout.php?viewInOutPendingForEmployee=1");
		  </script>';
} 

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
							<div class="col-sm-3"></div>
							<div class="col-sm-2"><label>Emp Id:</label></div>
							<div class="col-sm-4"> '.$row['empid'].'</div>
                   			<div class="col-sm-3"></div>
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
					$(this).find(':input[type=submit]').replaceWith('<center><img src='img/loader.gif' class='img-responsive' alt='processing'/></center>');
				});
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
# View for display pending In/Out Details for all childern coming under the manager
if(isset($_REQUEST['viewInOutForManager'])) 
{
    $empList=getChildren($_SESSION['u_empid']);
    echo "<table id='inout' class='table table-hover inouttableclass'>
            <tbody>
                <tr class='info'>
                    <th>Sr. No.</th>
                    <th>Emp Name</th>
                    <th>Actions</th>
                    <th></th>
			</tr>";
    $rowCount = 0 ;
    foreach ($empList as $emp) {
        $query = "select distinct(transactionid) from empinoutapproval where empid='".$emp."' and status='Pending'";
        $result = $db->query($query);
        $count=1;
        while($row = $db->fetchAssoc($result)) {
            $rowCount++;
            $getinoutfortransactionQuery="select * from empinoutapproval where transactionid='".$row['transactionid']."'";
            $getinoutfortransactionResult=$db->query($getinoutfortransactionQuery);
            echo '<tr><td>'.$count++.'</td>';
            echo '<td>'.getempname($emp).'</td>';
            echo '<td>Click on the row to Approve/notapprove</td>';
            echo '<td><div class="arrow"></div></td></tr>';
            echo '<tr>
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
        }
    }
    if($rowCount == 0) {
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

                function approve(tid)
                {
                    $("#loadinout").load("applyinout.php?approveInOut=1&tid="+tid);
                }

                function notapprove(tid)
                {
                    $("#loadinout").load("applyinout.php?notapproveInOut=1&tid="+tid);
                }
            </script>';
    echo '<style>
            #inout div.arrow {
                background: transparent url("images/arrows.png") no-repeat scroll 0 -16px;
                display: block;
                height: 16px;
                width: 16px;
            }
        </style>';
} 
?> 



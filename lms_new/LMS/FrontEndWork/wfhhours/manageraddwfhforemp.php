<?php
	session_start();
	require_once '../Library.php';
	require_once '../generalFunctions.php';
	$db=connectToDB();
?>
<?php
	function insertRecord() {
		echo "<div class='panel panel-primary'>
				<div class='panel-heading text-center'>
					<strong style='font-size:20px;'>View/Update Extra WFH Hours for Employee</strong>
				</div>
				<div class='panel-body'>";
				global $db;
				$name=$_SESSION['u_fullname'];
				$empid=isset($_POST['empid']) ? $_POST['empid'] : '';
				$noh = isset($_POST['noh']) ? $_POST['noh'] : '';
				$reason = isset($_POST['reason']) ? $_POST['reason'] : '';
				$date= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
				
				$transactionid = generate_transaction_id();
				$query="select * from extrawfh where eid='".$empid."' and date='".$date."' order by date";
				$sql=$db->query($query);
				//$createdAt = date('Y-m-d H:i:s');
				if($db->countRows($sql) > 0){
					# IF ROW EXISTS, UPDATE QUERY
					$reason=mysql_escape_string($reason);
					$sqlQuery=$db->query("UPDATE `extrawfh` SET wfhHrs='$noh', reason='$reason',updatedBy='$name' WHERE date='$date' and eid='$empid'");
				}
				else {
					# ELSE , INSERT
					$insertQuery="INSERT INTO extrawfh (createdAt, createdBy, updatedAt, updatedBy, eid, tid, date, wfhHrs, reason, comments, status)
					VALUES (CURTIME(), '$name','', '', '$empid', '$transactionid','$date', '$noh', '$reason', '', 'Approved')";
					$sqlQuery = $db->query($insertQuery);
				}
				
				if($sqlQuery){
					echo "success";
				} else {
					echo "unsuccessfull";
				}
			echo "</div>";//panel-body div close
		echo "</div>";//panel div close
	}
	
	function getWFHForm($empid,$date){
		global $db;
		global $divid;
			#query to check row exists
			$getquery= "select * from extrawfh where eid='$empid' and date='$date'";
			$result=$db->query($getquery);
			if($db->countRows($result) > 0){
			# if exists, auto fill number of hrs and reason
				$row= $db->fetchAssoc($result);
				echo $row['wfhHrs']."-".$row['reason'];
			}
	}
	
	function displayWFHForm($emp)
	{
		echo "<div class='panel panel-primary'>
				<div class='panel-heading text-center'>
					<strong style='font-size:20px;'>Add Extra WFH Hours for Employee</strong>
				</div>";
					global $db;
					global $divid;
					
					$empnametresult=$db->query("select empid,empname from emp where empname='".$emp."' and state='Active'");
					$empnamerow=$db->fetchAssoc($empnametresult);
					$sql=$db->query("select * from extrawfh where status='Approved' and eid='".$empnamerow['empid']."'");
					$childern=getChildren($_SESSION['u_empid']);
					if((in_array($empnamerow['empid'],$childern) && (strtoupper($_SESSION['user_desgn'])=="MANAGER")) || strtoupper($_SESSION['user_dept'])=="HR") {
						
						//$getCalIds = array("fromdate", "todate", "TypeOfDayfromdate", "TypeOfDaytodate");
						//$calImg = getCalImg($getCalIds);
						//echo $calImg;
						echo '<form method="post" action="wfhhours/manageraddwfhforemp.php?change=1&addEmpWFH=1" id="managerAddWFH">
							<div class="panel-body">
								<div class="form-group">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-3">
										<label>Employee Name</label>
									</div>
									<div class="col-sm-5">
										<input name="emp_name" type="text" class="form-control" id="emp_name" value="'.$empnamerow['empname'].'" readonly required>
									</div>
									<div class="col-sm-2"></div>
								</div>
								</div>
								
								<div class="form-group">
								<div class="row" style="display:none">
									<div class="col-sm-12">
										<input name="emp_tid" type="text" id="emp_tid">
									</div>
								</div>
								</div>
								
								<div class="form-group">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-3">
										<label>Employee Id</label>
									</div>
									<div class="col-sm-5">
										<input name="empid" type="text" class="form-control" id="empid" value="'.$empnamerow['empid'].'" required readonly>
									</div>
									<div class="col-sm-2"></div>
								</div>
								</div>
							
								<div class="form-group">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-3">
										<label>Date</label>
									</div>
									<div class="col-sm-5">
										<div class="input-group">
											<input type="text" id="Extrawfhhours" class="form-control open-datetimepicker" name="dynamicworked_day" readonly />
											<label class="input-group-addon btn" for="date">
												<span class="fa fa-calendar open-datetimepicker"></span>
											</label>
										</div>
									</div>
									<div class="col-sm-2"></div>
								</div>
								</div>
								
								<div class="form-group">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-3">
										<label>No. of Hrs</label>
									</div>
									<div class="col-sm-5">
										<input name="noh" type="text" class="form-control" id="noh" readonly required>
									</div>
									<div class="col-sm-2"></div>
								</div>
								</div>
								
								<div class="form-group">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-3">
										<label>Reason</label>
									</div>
									<div class="col-sm-5">
										<textarea name="reason" class="form-control" id="reason" required></textarea>
									</div>
									<div class="col-sm-2"></div>
								</div>
								</div>
								
								<div class="form-group">
								<div class="row">
									<div class="col-sm-12 text-center">
										<input name="submit" class="btn btn-success" type="submit" id="submit" value="Submit">
										<input name="close" class="btn btn-danger" type="submit" id="close" value="Close">
									</div>
								</div>
								</div>
							</div>
						</form>
			</div>
						<script>
							$(document).ready(function(){
								$(".open-datetimepicker").datetimepicker({
									format: "yyyy-mm-dd",
			                        minView : 2,
			                        autoclose: true  
								});
							});
						</script>';
											
					}
				
					else {
						echo "<script>BootstrapDialog.alert(\"You dont have permissions to change '".$empnamerow['empname']." ' transaction\");</script>";
					}
	}
	
	
	if(isset($_REQUEST['change']))
	{
		if(isset($_REQUEST['addEmpWFH']))
		{	
				insertRecord(); //to view or update extra WFH hour for employee
		}
		if(isset($_REQUEST['displayWFHForm']))
		{
			displayWFHForm($_REQUEST['empuser']); //to display Add Extra WFH Hour Form
			?>
			
			<?php 
			//check whether role is MANAGER or HR
				if(isset($_REQUEST['role']))
				{
					$_SESSION['roleofemp']=$_REQUEST['role'];
					if($_REQUEST['role']=="manager")
					{$divid="loadmanagersection";
					echo "<script>var divid=\"loadmanagersection\";</script>";
					}
					if($_REQUEST['role']=="hr")
					{ $divid="loadhrsection";
					echo "<script>var divid=\"loadhrsection\";</script>";
					}
				}
					
			?>
			<?php require_once 'addwfhjs.js';?>
			<script type="text/javascript">
			$("document").ready(function() {
				$("#noh").spinner(
			            { min: 1 },
			            { max: 18 },
			            { step:0.25 }
					);	
			$(".workeddaydynamic").change(function() {
					date=$(".workeddaydynamic").val();
					empid= $("#empid").val();
		   			$.ajax({
			       	 	 data: { date: date, empid: empid },
			       		 type: "GET",
			       		 url: "wfhhours/manageraddwfhforemp.php?getwfhbymanager=1",
			        	 success: function(response) {
							arr=response.split("-");
		        	      	$("#noh").val(arr[0]);
							$("#reason").val(arr[1]);
		        		 }
					});
				});
			
				$("#managerAddWFH").submit(function() {
					$.ajax({
			 		data: $(this).serialize(),
				 	type: $(this).attr("method"),
				 	url: $(this).attr("action"),
			 		success: function(response) {
				 	  if(response.match(/success/)) {
							BootstrapDialog.alert("WFH inserted successfully");
							var eid=$("#empid").val();
							var date = $(".workeddaydynamic").val();
							$('#'+divid).load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&eid="+eid+"&date="+date);
							 } else {
							BootstrapDialog.alert("not successs");
					   }
				    }
				 });
			return false; // cancel original event to prevent form submitting
			});
			});
			</script>
			<?php 
		}
			
	}
	
	
	else if(isset($_REQUEST['getwfhbymanager'])) {
		# to get the details of employee if data is already available
		getWFHForm($_REQUEST['eid'],$_REQUEST['date']);
	}
	else
	{
		echo '<form action="wfhhours/manageraddwfhforemp.php?change=1&displayWFHForm=1" method="POST" id="getemptrans">
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
		
		<?php 
		//check whether role is MANAGER or HR
		if(isset($_REQUEST['role']))
		{
			$_SESSION['roleofemp']=$_REQUEST['role'];
			if($_REQUEST['role']=="manager")
			{$divid="loadmanagersection";
			echo "<script>var divid=\"loadmanagersection\";</script>";
			}
			if($_REQUEST['role']=="hr")
			{ $divid="loadhrsection";
			echo "<script>var divid=\"loadhrsection\";</script>";
			}
		}
		
		?>
		<?php require_once 'addwfhjs.js';?>
		<script type="text/javascript">
		
		$("document").ready(function() {
			
			$('#getemptrans').submit(function() {
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
			        	$('#'+divid).html(response); 
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

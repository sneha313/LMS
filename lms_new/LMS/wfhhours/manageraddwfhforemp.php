<?php
session_start();
require_once '../Library.php';
require_once '../generalFunctions.php';
$db=connectToDB();
?>
	<?php
	function insertRecord() {
		echo "<center>
		<h3>View/Update Extra WFH Hours for Employee</h3>
		</center>";
		global $db;
		$name=$_SESSION['u_fullname'];
		$empid=isset($_POST['empid']) ? $_POST['empid'] : '';
		$noh = isset($_POST['noh']) ? $_POST['noh'] : '';
		//change number of hours format from hour to hour:minute:second format
		//$second=$noh*3600;
		//$hours=floor($second / (60 * 60));
		// extract minutes
		//$divisor_for_minutes = $second % (60 * 60);
		//$minutes = floor($divisor_for_minutes / 60);
		
		// extract the remaining seconds
		//$divisor_for_seconds = $divisor_for_minutes % 60;
		//$seconds = ceil($divisor_for_seconds);
		//$h=(int)$hours;
		//$m=(int)$minutes;
		//$s=(int)$seconds;
		//$wfhhours="$h:$m:$s";
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
		echo "<center>
		<h3>Add Extra WFH Hours for Employee</h3>
		</center>";
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
			echo '<center>
				<form method="post" action="wfhhours/manageraddwfhforemp.php?change=1&addEmpWFH=1" id="managerAddWFH">
				<table id="table-2" width="400" border="0" cellspacing="1" cellpadding="2">
				<tr>
					<td width="140">Employee Name</td>
					<td><input name="emp_name" type="text" id="emp_name" value="'.$empnamerow['empname'].'" readonly required></td>
				</tr>
				<tr style="display:none">
					<td><input name="emp_tid" type="text" id="emp_tid"></td>
				</tr>
				<tr>
					<td width="100">Employee Id</td>
					<td><input name="empid" type="text" id="empid" value="'.$empnamerow['empid'].'" required></td>
				</tr>
				<tr>
					<td width="100">Date</td>
					<td><input id="Extrawfhhours" class="workeddaynoh" type="text" name="dynamicworked_day" readonly/></td>
		
				</tr>
				<tr>
					<td width="100">No. of Hrs</td>
					<td><input name="noh" type="text" id="noh" readonly required></td>
				</tr>
				<tr>
					<td width="100">Reason</td>
					<td><textarea name="reason" id="reason" required></textarea></td>
				</tr>
				<tr>
					<td width="100"></td>
					<td><input name="submit" type="submit" id="submit" value="Submit">
						<input name="close" type="submit" id="close" value="Close"></td>
				</tr>
			</table>
		</form>
	</center>';
								
		}
	
		else {
			echo "<script>alert(\"You dont have permissions to change '".$empnamerow['empname']." ' transaction\");</script>";
		}
	}
	
	
	if(isset($_REQUEST['change']))
	{
		if(isset($_REQUEST['addEmpWFH']))
		{	
				insertRecord();
		}
		if(isset($_REQUEST['displayWFHForm']))
		{
			echo '<link rel="stylesheet" type="text/css" media="screen" href="css/applyleave.css" />
	 			 <link rel="stylesheet" type="text/css" media="screen" href="css/table.css" />';
			displayWFHForm($_REQUEST['empuser']);
			?>
			
			<?php 
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
			
				$("body").on("click",".workeddaynoh",function() {
					$(this).datepicker ({
						changeMonth: true,
				        changeYear: true,
				        showButtonPanel: true,
				        dateFormat: "yy-mm-dd",
				        yearRange: "-1:+0",
				        maxDate: "+0D",
				        buttonImage: "js/datepicker/datepickerImages/calendar.gif",
						showOn: "both",
						buttonImageOnly: true,	 
		    	});
			});
				$("#managerAddWFH").submit(function() {
					$.ajax({
			 		data: $(this).serialize(),
				 	type: $(this).attr("method"),
				 	url: $(this).attr("action"),
			 		success: function(response) {
				 	  if(response.match(/success/)) {
							alert("WFH inserted successfully");
							var eid=$("#empid").val();
							var date = $(".workeddaydynamic").val();
							$('#'+divid).load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&eid="+eid+"&date="+date);
							 } else {
							alert("not successs");
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
				<table id="table-2">
		<tr>
			<td><p><label>Enter Employee Name:</label></p></td>
         	<td><p><input id="empuser" type="text" name="empuser"/></p></td>';
		echo '<td><input class="submit" type="submit" name="submit" value="SUBMIT"/></td>
        </tr> 	
		 </table>
		</form>';
		?>
		
		<?php 
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
					alert("Please Enter Employee Name");
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

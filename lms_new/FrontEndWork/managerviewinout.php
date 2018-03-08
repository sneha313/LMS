<?php
	session_start();
	require_once 'librarycopy1.php';
	require_once 'generalcopy.php';
	$db=connectToDB();
	
	echo '<head>
			<link rel="stylesheet" type="text/css" href="public/js/jqueryPlugins/jquery.timepicker.css"/> 
    		<script type="text/javascript" src="public/js/jqueryPlugins/jquery.timepicker.min.js"></script>
			<script>
			$("document").ready(function(){
			$( "#tabs" ).tabs();
			
            $(".intime,.outtime").timepicker({ 
				"timeFormat": "H:i:s", 
				"step" : 15
			});
			
			$("#editbymanager").submit(function() {
				$.ajax({
					data: $(this).serialize(),
					type: $(this).attr("method"),
					url: $(this).attr("action"),
					success: function(response) {
						if(response.match(/success/)) {
							BootstrapDialog.alert("In/Out Detail Edited Successfully!");
							var empid=$("#empid").val();
							var date = $(".workeddaydynamic").val();
							$("#loadinout").html(response);
							hidealldiv("loadinout");
							$("#loadinout").load("managerviewinout.php?viewrecordbymanager=1&empid="+empid+"&date="+date);
						} else {
							BootstrapDialog.alert("not successs");
						}
					}
				});
				return false; // cancel original event to prevent form submitting
			});
			
			$("#deletebymanager").submit(function() {
				$.ajax({  
					data: $(this).serialize(),
					type: $(this).attr("method"),
					url: $(this).attr("action"),
					success: function(response) {
						var empid=$("#empid").val();
						var date = $(".workeddaydynamic").val();
						BootstrapDialog.confirm("Delete In/Out Detail!", function(result){
						if (result)
						{
							var dellink=$("#deleteFormbymanager").attr("href");
							$("#loadinout").html(response);
							hidealldiv("loadinout");
							$("#loadinout").load("managerviewinout.php?viewrecordbymanager=1&empid="+empid+"&date="+date);
						}
						else
						{
							BootstrapDialog.alert("You pressed Cancel!");
							$("#loadinout").load("managerviewinout.php?viewrecordbymanager=1&delcancel=1&empid="+empid+"&date="+date);
						}
						});
					} 
				});
				return false; // cancel original event to prevent form submitting
			});
			
			$("#viewrecordbymanager").submit(function() {
				if($("#empuser").val()=="")
				{
					BootstrapDialog.alert("Please Enter Employee Name");
					return false;
				}
				$.ajax({
					data: $(this).serialize(),
					type: $(this).attr("method"),
					url: $(this).attr("action"),
					success: function(response) {
						var empid=$("#empid").val();
						var date=$(".workeddaydynamic").val();
						$("#loadinout").html(response);
						$("#loadinout").load("managerviewinout.php?viewrecordbymanager=1&empid="+empid+"&date="+date);
					}
				});
				return false; // cancel original event to prevent form submitting
			});
			
			$("#viewEmpWFHbymanager").submit(function() {
				if($("#empuser").val()=="")
				{
					BootstrapDialog.alert("Please Enter Employee Name");
					return false;
				}
				$.ajax({
					data: $(this).serialize(),
					type: $(this).attr("method"),
					url: $(this).attr("action"),
					success: function(response) {
						hidealldiv("loadmanagersection");
						$("#loadinout").html(response);
					}
				});
				return false; // cancel original event to prevent form submitting
			});
			});
			function editInOut(transactionId) {
				hidealldiv("loadinout");
				$("#loadinout").load("managerviewinout.php?editInOutDetail=1&transactionId="+transactionId);
			}
			function deleteInOut(transactionId) {	
				hidealldiv("loadinout");
				$("#loadinout").load("managerviewinout.php?deleteInOutDetail=1&transactionId="+transactionId);
			}
		</script></head>';
?>
<?php 
	if(isset($_REQUEST['deleteFormbymanager'])){
		$tmpdate= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
		$date=$tmpdate[0];	
		$empid= isset($_POST['empid']) ? $_POST['empid'] : '';
		$intime = isset($_POST['intime']) ? $_POST['intime'] : '';
		$outtime = isset($_POST['outtime']) ? $_POST['outtime'] : '';
		$transactionId = isset($_POST['transactionId']) ? $_POST['transactionId'] : '';
		$updatedAt = date('Y-m-d H:i:s');
		$dataArray = array('status'=>'Deleted');
		// where condition array
		$aWhere = array('transactionId'=>$transactionId);
		// call update function
		$sql2 = $db->update('empinoutapproval', $dataArray, $aWhere)->affectedRows();
		
		//$queryDel="UPDATE empinoutapproval set status='Deleted' WHERE `transactionId`= '$transactionId'";
		//$sql2=$db->query($queryDel);
		if($sql2){
		//send mail that record is deleted
			$cmd = '/usr/bin/php -f sendmail.php '.$transactionId.' '.$empid.'  deleteInOutDetail >> /dev/null &';
			exec($cmd);
			echo "success";
		} else {
			echo "<center><h3>Record not deleted</h3></center>";
		}
	}
	if(isset($_REQUEST['deleteInOutDetail'])){
		//delete form here employee can delete In/Out Detail
		$transactionId=$_REQUEST['transactionId'];
		## query database if row exists
		$tquery="select * from empinoutapproval where `transactionId`='$transactionId'";
		$tresults=$db->pdoQuery($tquery)->results();
		foreach($tresults as $tresult)
		//$tresult=$db->fetchArray($tresult);
		//$tresult=$db->pdoQuery($tquery);
		//$tresult=$tresult->results();
		## if exists, get Intime, Outtime and date
		$intime=$tresult['intime'];
		$outtime=$tresult['outtime'];
		$date=$tresult['date'];
		$empid=$tresult['empid'];
?>
<form method="POST" action="managerviewinout.php?deleteFormbymanager=1" id="deletebymanager" name="deletebymanager">
	<div class='panel panel-primary'>
		<div class='panel-heading text-center'>
			<strong style='font-size:20px;'>Delete In/Out Detail</strong>
		</div>
		<div class='panel-body'>	
			<div class="form-group">
				<div class="row">
					<div class="col-sm-2"></div>
					<div class="col-sm-3">
						<label>Employee Id</label>
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="empid" id="empid" value="<?php echo $empid; ?>" readonly>
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
							<input type="text" id="datetimepicker" class="workeddaydynamic form-control" name="dynamicworked_day" value="<?php echo $date;?>" readonly />
							<label class="input-group-addon btn" for="date">
								<span class="fa fa-calendar"></span>
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
						<label>In Time</label>
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control intime" name="intime" id="intime" value="<?php echo $intime;?>" readonly >
					</div>
					<div class="col-sm-2"></div>
				</div>
			</div>
								
			<div class="form-group">
				<div class="row">
					<div class="col-sm-2"></div>
					<div class="col-sm-3">
						<label>Out Time</label>
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control outtime" name="outtime" id="outtime" value="<?php echo $outtime;?>" readonly >
					</div>
					<div class="col-sm-2"></div>
				</div>
			</div>
							
			<div class="form-group">
				<div class="row">
					<div class="col-sm-12 text-center">
						<input type="submit" class="btn btn-danger" id="delete" name="delete" value="delete">
						<input type="submit" id="delcancel" class="btn btn-success" name="delcancel" value="cancel">
					</div>
				</div>
			</div>
							
			<div class="form-group">
				<div class="row">
					<div class="col-sm-12">
						<input type="hidden" id="transactionId" name="transactionId" value="<?= $transactionId ?>" >
					</div>
				</div>
			</div>
		</div><!-- panel body div close -->
	</div><!-- panel div close -->
</form><!-- form close -->
<?php 
} 

#edit In/Out Detail by manager
if(isset($_REQUEST['editFormbymanager'])){
	$date= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
	$intime = isset($_POST['InTime']) ? $_POST['InTime'] : '';
	$outtime = isset($_POST['OutTime']) ? $_POST['OutTime'] : '';
	$transactionId = isset($_POST['transactionId']) ? $_POST['transactionId'] : '';
	$empid= isset($_POST['empid']) ? $_POST['empid'] : '';
	$updatedAt = date('Y-m-d H:i:s');
	//$queryEdit="UPDATE empinoutapproval SET `intime`='$intime', `outtime`='$outtime' ,`date`='$date', `updatedAt`='$updatedAt', `updatedBy`='".$_SESSION['user_name']."'  WHERE `transactionId`='$transactionId'";
	//$sql3=$db->query($queryEdit);
	$dataArray = array('intime'=>$intime,'outtime'=>$outtime,'date'=>$date,'updatedAt'=>$updatedAt,'updatedBy'=>$_SESSION['user_name']);
	// where condition array
	$aWhere = array('transactionId'=>$transactionId);
	// call update function
	$sql3 = $db->update('empinoutapproval', $dataArray, $aWhere)->affectedRows();
	if($sql3){
		//send mail that record is updated
		$cmd = '/usr/bin/php -f sendmail.php '.$transactionId.' '.$empid.'  editInOutDetail >> /dev/null &';
		exec($cmd);
		echo "success";
	} else {
		echo "<center><h3>Record not updated</h3></center>";
	}
}

#edit form, here employee can edit extra work from home hour and date
if(isset($_REQUEST['editInOutDetail'])){
	$transactionId=$_REQUEST['transactionId'];
	# query database if row exists
	$tquery="select * from empinoutapproval where `transactionId`='$transactionId'";
	//$tresult=$db->query($tquery);
	//$tresult=$db->fetchAssoc($tresult);
	$tresults=$db->pdoQuery($tquery)->results();
	foreach($tresults as $tresult)
	//$tresult=$tresult->results();
	# if exists, get Intime, Outtime and date
	$InTime=$tresult['intime'];
	$OutTime=$tresult['outtime'];
	$date=$tresult['date'];
	$empid=$tresult['empid'];
?>
<form method="POST" action="managerviewinout.php?editFormbymanager=1" id="editbymanager" name="editbymanager">
	<div class='panel panel-primary'>
		<div class='panel-heading text-center'>
			<strong style='font-size:20px;'>Edit In/Out Detail</strong>
		</div>
		<div class='panel-body'>
			<div class="form-group">
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-3">
					<label>Employee Id</label>
				</div>
				<div class="col-sm-5">
					<input type="text" class="form-control" name="empid" id="empid" value="<?php echo $empid; ?>" readonly>
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
						<input type="text" id="datetimepicker" class="workeddaydynamic form-control" name="dynamicworked_day"  value="<?php echo $date;?>" readonly />
						<label class="input-group-addon btn" for="date">
							<span class="fa fa-calendar"></span>
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
					<label>In Time</label>
				</div>
				<div class="col-sm-5">
					<input type="text" class="form-control intime" name="InTime" value="<?php echo $InTime;?>" >
				</div>
				<div class="col-sm-2"></div>
			</div>
			</div>
								
			<div class="form-group">
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-3">
					<label>Out Time</label>
				</div>
				<div class="col-sm-5">
					<input type="text" class="form-control outtime" name="OutTime" value="<?php echo $OutTime;?>" >
				</div>
				<div class="col-sm-2"></div>
			</div>
			</div>
								
			<div class="form-group">
			<div class="row">
				<div class="col-sm-12 text-center">
					<input type="submit" id="cancel" class="btn btn-danger" name="cancel" value="cancel">
					<input type="submit" id="submit" class="btn btn-success" name="submit" value="Edit">
				</div>
			</div>
			</div>
								
			<div class="form-group">
			<div class="row">
				<div class="col-sm-12">
					<input type="hidden" id="transactionId" name="transactionId" value="<?= $transactionId ?>" >
				</div>
			</div>
			</div>
		</div><!-- panel body div close -->
	</div><!-- panel div close -->
</form><!-- form close -->
<?php 
} 
	//view In/Out Detail record by manager
	if(isset($_REQUEST['viewrecordbymanager']))
	{
		if (isset($_REQUEST['displayAll'])) {
			$empQuery="select empid,empname from emp where empname='".$_REQUEST['empuser']."' and state='Active'";
			//$empnametresult=$db->query($empQuery);
			//$empnamerow=$db->fetchAssoc($empnametresult);
			$empnametresult=$db->pdoQuery($empQuery)->results();
			//$empnamerow=$empnametresult;
			foreach ($empnametresult as $empnamerow)
			$empid=$empnamerow['empid'];
			//show record based on employee id where status is not equal to deleted
			$query="select * from empinoutapproval where status!='Deleted' and empid='".$empid."' order by date desc";
		} else {
			$date=$_REQUEST['date'];
			$empid=$_REQUEST['empid'];
			$empQuery="select empid,empname from emp where empid='".$empid."' and state='Active'";
			//$empnametresult=$db->query($empQuery);
			//$empnamerow=$db->fetchAssoc($empnametresult);
			$empnametresult=$db->pdoQuery($empQuery)->results();
			foreach ($empnametresult as $empnamerow)
			//$empnamerow=$empnametresult;
			$query="select * from empinoutapproval where status!='Deleted' and empid='".$empid."' order by date desc";
		}
		//$sql=$db->query("SELECT DISTINCT YEAR(date) as year FROM empinoutapproval where empid='".$empid."' order by year desc");
		$sqlquery="SELECT DISTINCT YEAR(date) as year FROM empinoutapproval where empid='".$empid."' order by year desc";
		$sql=$db->pdoQuery($sqlquery);
		$distinctYears=array();
		//$InOutHistory=$db->countRows($sql);
		$InOutHistory=$sql -> count($sTable = 'empinoutapproval', $sWhere = 'empid = "'.$empid.'" ' );
		$rows=$db->pdoQuery($sqlquery)->results();
		//$InOutHistory=$sql->rowCount();
		echo '<div class="panel panel-primary">
			<div class="panel-heading text-center">
				<strong style="font-size:20px;">View In/Out Details</strong>
			</div>
			<div class="panel-body">';
		if($InOutHistory == 0) {
			echo "<div id='tabs'><ul><div id='Info'><tr><td>No Data Available</td></tr></div></ul></div>";
		} else {
			echo '<div id="tabs">
									<ul>';
		}
		//for($i=0;$i<$sql->rowCount();$i++)
		//for($i=0;$i<$db->countRows($sql);$i++)
			foreach ($rows as $row)
		{
		
		//$row=$sql->results();
		echo "<li><a href='#".$row['year']."'>".$row['year']."</a></li>";
				array_push($distinctYears,$row['year']);
		}
		echo "</ul>";
	
		foreach ($distinctYears as $year) {
		echo "<div id='".$year."'>";
		echo "<div id='showtable'>
		<table class='table table-hover table-bordered'>
			<form method='POST' action='' id='InOut' name='viewInOut'>
				<tr class='info'>
					<th>Emp Name</th>
					<th>Date</th>
					<th>In Time</th>
					<th>Out Time</th>
					<th>Reason</th>
					<th>Approval Status</th>
					<th colspan=2>Actions</th>
				</tr>";
				//$sql1=$db->pdoQuery($query);
				//while($getDetailedrow=$sql1->results()) {
				$sql1=$db->pdoQuery($query)->results();
				//while($getDetailedrow=$db->fetchassoc($sql1)) {
				foreach ($sql1 as $getDetailedrow){
					echo  '<tr>
							<td>'.$empnamerow['empname'].'</td>
							<td>'.$getDetailedrow['date'].'</td>
							<td>'.$getDetailedrow['intime'].'</td>
							<td>'.$getDetailedrow['outtime'].'</td>
							<td>'.$getDetailedrow['reason'].'</td>
							<td>'.$getDetailedrow['status'].'</td>
							<td>
								<button  id="modify" title="'.$getDetailedrow['transactionId'].'" onclick=editInOut("'.$getDetailedrow['transactionId'].'") class="btn btn-success '.$getDetailedrow['empid'].'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
								<button id="delete" title="'.$getDetailedrow['transactionId'].'" onclick=deleteInOut("'.$getDetailedrow['transactionId'].'") class="btn btn-danger '.$getDetailedrow['empid'].'"><i class="fa fa-trash" aria-hidden="true"></i></button>
							</td>
					</tr>';
				}
				echo "</form>
				</table>
			</div>
			</div>";
		}
		echo "</div>
		</div>";
	}
			
	if(isset($_REQUEST['viewEmpInOutbymanager']))
	{
		$emp=isset($_POST['empuser']) ? $_POST['empuser'] : '';
		//$empnametresult=$db->query("select empid,empname from emp where empname='".$emp."' and state='Active'");
		//$empnamerow=$db->fetchAssoc($empnametresult);
		$empnamequery="select empid,empname from emp where empname='".$emp."' and state='Active'";
		$empnametresult=$db->pdoQuery($empnamequery)->results();
		//$empnamerow=$empnametresult->results();
		foreach ($empnametresult as $empnamerow)
		$childern=getChildren($_SESSION['u_empid']);
		if((in_array($empnamerow['empid'],$childern) && (strtoupper($_SESSION['user_desgn'])=="MANAGER")) || strtoupper($_SESSION['user_dept'])=="HR") {		
			if (isset($_REQUEST['displayAll'])) {
				$empQuery="select empid,empname from emp where empname='".$_REQUEST['empuser']."' and state='Active'";
				//$empnametresult=$db->query($empQuery);
				//$empnamerow=$db->fetchAssoc($empnametresult);
				$empnametresult=$db->pdoQuery($empQuery)->results();
				foreach ($empnametresult as $empnamerow)
				$empid=$empnamerow['empid'];
				//show record based on employee id where status is not equal to deleted
				$query="select * from empinoutapproval where status!='Deleted' and empid='".$empid."' order by date desc";
			} else {
				$date=$_REQUEST['date'];
				$empid=$_REQUEST['empid'];
				$empQuery="select empid,empname from emp where empid='".$empid."' and state='Active'";
				//$empnametresult=$db->query($empQuery);
				//$empnamerow=$db->fetchAssoc($empnametresult);
				$empnametresult=$db->pdoQuery($empQuery)->results();
				foreach ($empnametresult as $empnamerow)
				$query="select * from empinoutapproval where status!='Deleted' and empid='".$empid."' order by date desc";
			}
			$query1="SELECT DISTINCT YEAR(date) as year FROM empinoutapproval where empid='".$empid."' order by year desc";
			$sql=$db->pdoQuery($query1);
			$InOutHistory=$sql -> count($sTable = 'empinoutapproval', $sWhere = 'empid = "'.$empid.'" ' );
			
			$rows=$db->pdoQuery($query1)->results();
			//$sql=$db->query("SELECT DISTINCT YEAR(date) as year FROM empinoutapproval where empid='".$empid."' order by year desc");
			$distinctYears=array();
			//$InOutHistory=$sql->rowCount();
			//$InOutHistory=$db->countRows($sql);
			echo '<div class="panel panel-primary">
				<div class="panel-heading text-center">
					<strong style="font-size:20px;">View In/Out Details</strong>
				</div>
				<div class="panel-body">';
					if($InOutHistory == 0) {
						echo "<div id='tabs'><ul><div id='Info'><tr><td>No Data Available</td></tr></div></ul></div>";
					} else {
						echo '<div id="tabs">
							<ul>';
					}
					//for($i=0;$i<$sql->rowCount();$i++)
					//for($i=0;$i<$db->countRows($sql);$i++)
						foreach($rows as $row)
					{
						//$row=$sql->results()
						echo "<li><a href='#".$row['year']."'>".$row['year']."</a></li>";
						array_push($distinctYears,$row['year']);
					}
					echo "</ul>";
								
					foreach ($distinctYears as $year) {
						echo "<div id='".$year."'>";
						echo "<div id='showtable'>
							<table class='table table-hover table-bordered'>
								<form method='POST' action='' id='InOut' name='InOutTable'>
									<tr class='info'>
										<th>Emp Name</th>
										<th>Date</th>
										<th>In Time</th>
										<th>Out Time</th>
										<th>Reason</th>
										<th>Approval Status</th>
										<th colspan=2>Actions</th>
									</tr>";
									//$sql1=$query->query();
									//while($getDetailedrow=$sql1->results()) {
									$sql1=$db->pdoQuery($query)->results();
									foreach ($sql1 as $getDetailedrow){
									//while($getDetailedrow=$db->fetchassoc($sql1)) {
										echo  '<tr>
											<td>'.$empnamerow['empname'].'</td>
											<td>'.$getDetailedrow['date'].'</td>
											<td>'.$getDetailedrow['intime'].'</td>
											<td>'.$getDetailedrow['outtime'].'</td>
											<td>'.$getDetailedrow['reason'].'</td>
											<td>'.$getDetailedrow['status'].'</td>
											<td>
												<button  id="modify" title="'.$getDetailedrow['transactionId'].'" onclick=editInOut("'.$getDetailedrow['transactionId'].'") class="btn btn-success '.$getDetailedrow['empid'].'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
												<button id="delete" title="'.$getDetailedrow['transactionId'].'" onclick=deleteInOut("'.$getDetailedrow['transactionId'].'") class="btn btn-danger '.$getDetailedrow['empid'].'"><i class="fa fa-trash" aria-hidden="true"></i></button>
											</td>
										</tr>';
									}
								echo "</form>
							</table>
						</div>
						</div>";
					}
					echo "
					</div>
					</div>";
				}
				else {
					echo "<script>BootstrapDialog.alert(\"You dont have permissions to view/modify In/Out detail for '".$_REQUEST['empuser']."'\");
						$('#loadmanagersection').load('managerviewinout.php?viewInOutForManager=1');
					</script>";
				}
			}
	
	if(isset($_REQUEST['viewInOutForManager']))
		{
			echo '<form action="managerviewinout.php?viewEmpInOutbymanager=1&displayAll=1" method="POST" id="managerviewinout">
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
			$('#managerviewinout').submit(function() {
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
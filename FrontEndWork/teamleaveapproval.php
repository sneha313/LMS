<?php
	session_start();
	require_once 'librarycopy1.php';
	//require_once 'Library.php';
	$db=connectToDB();
?>
<html>
	<head>
	<script type="text/javascript">  
			$("document").ready(function(){
				$("#teamapprove tr:odd").addClass("odd");
				$("#teamapprove tr:not(.odd)").hide();
				$("#teamapprove tr:first-child").show();
				$("#teamapprove tr.odd").click(function(){
					$(this).next("tr").toggle();
					$(this).find(".arrow").toggleClass("up");
				});
			  });
			function approve(tid)
			{
				$('#loadteamleaveapproval').load('teamleaveapproval.php?approve=1&tid='+tid);
			}
			
			function submitcomments(tid,x)
			{
			   var comments = $("#txtMessage"+x).val();
			   $('#loadteamleaveapproval').load('teamleaveapproval.php?notapprove=1&tid='+tid+'&comments='+encodeURIComponent(comments));
			}
			
		</script>
	</head>
	<body>
		<?php
			if(isset($_REQUEST['approve']))
			{
				$transactionid=$_REQUEST['tid'];
				//Selecting the empid and count based on transactionid
				//$getleavesquery = $db->query("SELECT  `empid` ,  `count`,`startdate`,`enddate` FROM empleavetransactions WHERE transactionid ='".$transactionid."'");
				//$row1=$db->fetchAssoc($getleavesquery);
				$getleaves="SELECT  `empid` ,  `count`,`startdate`,`enddate` FROM empleavetransactions WHERE transactionid ='".$transactionid."'";
				$getleavesquery = $db->pdoQuery($getleaves)->results();
				//$row1=$getleavesquery->results();
				foreach ($getleavesquery as $row1)
				//$balancequery = $db->query("SELECT  balanceleaves FROM emptotalleaves WHERE empid =".$row1['empid']);
				//$row2=$db->fetchAssoc($balancequery);
					$balanceleavequery="SELECT  balanceleaves FROM emptotalleaves WHERE empid =".$row1['empid'];
				$balancequery = $db->pdoQuery($balanceleavequery)->results();
				//$row2=$balancequery->results();
				foreach ($balancequery as $row2)
				$reducedleaves=($row2['balanceleaves']-$row1['count']);
				//Check if balance leaves is exceeding permitted leaves per year
				if(($reducedleaves+(getCarryForwardedLeaves($row1['empid']))) < -50) {
					echo "<script>BootstrapDialog.alert('Leaves cant be approved as emp leaves are exceeding permitted leaves per year after approval. So Leaves are not approved.')</script>";
				}
				else {
					//Selecting leavetype from perdaytransactionstable
					//$leavetypequery=$db->query("SELECT leavetype FROM  `perdaytransactions` WHERE transactionid ='".$transactionid."' AND leavetype !=  'FullDay' AND leavetype !=  'HalfDay' AND leavetype !=  'WFH'");
					//$leavetyperow=$db->fetchAssoc($leavetypequery);
					$leavetype="SELECT leavetype FROM  `perdaytransactions` WHERE transactionid ='".$transactionid."' AND leavetype !=  'FullDay' AND leavetype !=  'HalfDay' AND leavetype !=  'WFH'";
					$leavetypequery=$db->pdoQuery($leavetype)->results();
					foreach ($leavetypequery as $leavetyperow)
					//$leavetyperow=$leavetypequery->results();
					//Get leave type id from special leaves
					//$leavetypeidquery=$db->query("select specialleaveid from specialleaves where specialleave LIKE '".$leavetyperow['leavetype']."%'");
					$leavetypeid="select specialleaveid from specialleaves where specialleave LIKE '".$leavetyperow['leavetype']."%'";
					$leavetypeidquery=$db->pdoQuery($leavetypeid);
					if($leavetypeidquery) {
						//$leavetypeidrow=$db->fetchAssoc($leavetypeidquery);
						$leavetypeidrows=$db->pdoQuery($leavetypeid)->results();
						foreach ($leavetypeidrows as $leavetypeidrow)
						//get splleavetaken from empsplleavetaken
						if($leavetypeidrow) {
							//$splleavetakenquery=$db->query("select splleavetaken from  empsplleavetaken where empid='".$row1['empid']."'");
							//if($db->hasRows($splleavetakenquery) && $leavetypeidrow)
							$splleavetaken="select splleavetaken from  empsplleavetaken where empid='".$row1['empid']."'";
							$splleavetakenquery=$db->pdoQuery($splleavetaken);
							$splleavetakencount=$splleavetakenquery -> count($sTable = 'empsplleavetaken', $sWhere = 'empid = "'.$row1['empid'].'"' );
				
							if($splleavetakencount>0 && $leavetypeidrow)
							{
								//$splleavetakenrow=$db->fetchAssoc($splleavetakenquery);
								$splleavetakenrows=$db->pdoQuery($splleavetaken)->results();
								foreach ($splleavetakenrows as $splleavetakenrow)
								$updatedspl=str_replace("".$leavetypeidrow['specialleaveid']."P","".$leavetypeidrow['specialleaveid']."A","".$splleavetakenrow['splleavetaken']."");
								//$updatesplleavetakenquery=$db->query("UPDATE  empsplleavetaken SET  `splleavetaken` =  '".$updatedspl."' where empid='".$row1['empid']."'");
								$dataArray = array('splleavetaken'=>$updatedspl);
								// where condition array
								$aWhere = array('empid'=>$row1['empid']);
								// call update function
								$updatesplleavetakenquery = $db->update('empsplleavetaken', $dataArray, $aWhere)->affectedRows();
								}
						}
					}
					//Updating the balance leaves
					/*$reduceleavesquery=$db->query("UPDATE  `emptotalleaves` SET  `balanceleaves` =  '".$reducedleaves."' WHERE  `empid` ='".$row1['empid']."'");
					//Updating the approval status to "Approved"
					$updateapprovalstatus=$db->query("UPDATE  empleavetransactions SET  `approvalstatus` =  'Approved',approvalcomments='Approved By Manager(".$_SESSION['u_fullname'].")' WHERE  `transactionid` ='".$transactionid."'");
					$optionalLeaveQuery="select * from empoptionalleavetaken where empid='".$row1['empid']."' and state='Pending'";
					$optionalLeaveResult=$db->query($optionalLeaveQuery);*/
					//$reduceleavesquery=$db->query("UPDATE  `emptotalleaves` SET  `balanceleaves` =  '".$reducedleaves."' WHERE  `empid` ='".$row1['empid']."'");
					//Updating the approval status to "Approved"
					$reduceleavesqueryArray = array('balanceleaves'=>$reducedleaves);
					// where condition array
					$reduceleavesqueryArrayWhere = array('empid'=>$row1['empid']);
					// call update function
					$reduceleavesquery = $db->update('emptotalleaves', $reduceleavesqueryArray, $reduceleavesqueryArrayWhere)->affectedRows();
						
					//$updateapprovalstatus=$db->query("UPDATE  empleavetransactions SET  `approvalstatus` =  'Approved',approvalcomments='Approved By Manager(".$_SESSION['u_fullname'].")' WHERE  `transactionid` ='".$transactionid."'");
					$reduceleavesqueryArray = array('approvalstatus'=>'Approved','approvalcomments'=>'Approved By Manager("'.$_SESSION['u_fullname'].'")');
					// where condition array
					$reduceleavesqueryArrayWhere = array('transactionid' =>'".$transactionid."');
					// call update function
					$reduceleavesquery = $db->update('empleavetransactions', $reduceleavesqueryArray, $reduceleavesqueryArrayWhere)->affectedRows();
					
					$optionalLeaveQuery="select * from empoptionalleavetaken where empid='".$row1['empid']."' and state='Pending'";
					//$optionalLeaveResult=$db->query($optionalLeaveQuery);
					$optionalLeaveResult=$db->pdoQuery($optionalLeaveQuery);
					$optionalLeaveResultcount=$optionalLeaveResult -> count($sTable = 'empoptionalleavetaken', $sWhere = 'empid = "'.$row1['empid'].'" and state = "Pending"' );
				
					//if($db->hasRows($optionalLeaveResult)) {
					if($optionalLeaveResultcount>0) {
						$optionalLeaveRows=$db->pdoQuery($optionalLeaveQuery)->results();
						//while($optionalLeaveRow = $db->fetchAssoc($optionalLeaveResult)){
						//while($optionalLeaveRow = $optionalLeaveResult->results()){
						foreach ($optionalLeaveRows as $optionalLeaveRow){
							$datesRange=getDatesFromRange($row1['startdate'],$row1['enddate']);
							if(in_array($optionalLeaveRow['date'],$datesRange)) {
								//$updateOptionalLeave="update empoptionalleavetaken set state='Approved' where empid='".$row1['empid']."' and date='".$optionalLeaveRow['date']."'";
								//$optionalLeave=$db->query($updateOptionalLeave);

								$updateOptionalLeave = array('state'=>'Approved');
								// where condition array
								$aWhere = array('empid'=>$row1['empid'],'date'=>$optionalLeaveRow['date']);
								// call update function
								$q = $db->update('empoptionalleavetaken', $updateOptionalLeave, $aWhere)->affectedRows();
							}
					   }
					}
					
					if($updateapprovalstatus)
					{
						//send mail for Approval status to emp and manager to whom manager approved leave
						$cmd = '/usr/bin/php -f sendmail.php '.$transactionid.' '.$row1['empid'].'  ApproveLeave >> /dev/null &';
						exec($cmd);
						//$empname=$db->query("select empname from emp where state='Active' and empid=".$row1['empid']);
						//$empnamerow=$db->fetchAssoc($empname);
						$query="select empname from emp where state='Active' and empid=".$row1['empid'];
						$empname=$db->pdoQuery($query)->results();
						//$empnamerow=$empname->results();
						foreach ($empname as $empnamerow)
						echo "<script>BootstrapDialog.alert(\"Leave Approved and sending mail\");</script>";
					}
				}
			}
			if(isset($_REQUEST['notapprove']))
			{
				$transactionid=$_REQUEST['tid'];
				//$leavetypequery=$db->query("SELECT leavetype,empid,date FROM  `perdaytransactions` WHERE transactionid ='".$transactionid."' AND leavetype !=  'FullDay' AND leavetype !=  'HalfDay' AND leavetype !=  'WFH'");
				$leavetype="SELECT leavetype,empid,date FROM  `perdaytransactions` WHERE transactionid ='".$transactionid."' AND leavetype !=  'FullDay' AND leavetype !=  'HalfDay' AND leavetype !=  'WFH'";
				$leavetypequery=$db->pdoQuery($leavetype);
				if($leavetypequery) {
					$leavetyperows=$db->pdoQuery($leavetype)->results();
					//while($leavetyperow=$db->fetchAssoc($leavetypequery)){
					//while($leavetyperow=$leavetypequery->results()){
					foreach ($leavetyperows as $leavetyperow){
					//Get leave type id from special leaves
					
					if($leavetyperow) {
						/*$leavetypeidquery=$db->query("select specialleaveid from specialleaves where specialleave LIKE '".$leavetyperow['leavetype']."%'");
						$leavetypeidrow=$db->fetchAssoc($leavetypeidquery);
						$splleavetakenquery=$db->query("select splleavetaken from  empsplleavetaken where empid='".$leavetyperow['empid']."'");
						$spldelete=$db->fetchAssoc($splleavetakenquery);*/
						$leavetypeid="select specialleaveid from specialleaves where specialleave LIKE '".$leavetyperow['leavetype']."%'";
						$leavetypeidquery=$db->pdoQuery($leavetypeid)->results();
						foreach ($leavetypeidquery as $leavetypeidquery)
							$splleavetaken="select splleavetaken from  empsplleavetaken where empid='".$leavetyperow['empid']."'";
						$splleavetakenquery=$db->pdoQuery($splleavetaken)->results();
						//$spldelete=$splleavetakenquery->results();
						foreach ($splleavetakenquery as $spldelete)
						//Removing the pending leave when deleted the transaction
						$delspl=str_replace("".$leavetypeidrow['specialleaveid']."P:","","".$spldelete['splleavetaken']."");
						//$updatesplleavetakenquery=$db->query("UPDATE  empsplleavetaken SET  `splleavetaken` =  '".$delspl."' where empid='".$leavetyperow['empid']."'");
						
						$updatesplleavetaken = array('splleavetaken'=>$delspl);
						// where condition array
						$updatesplleavetakenWhere = array('empid'=>$leavetyperow['empid']);
						// call update function
						$updatesplleavetakenquery = $db->update('empsplleavetaken', $updatesplleavetaken, $updatesplleavetakenWhere)->affectedRows();
						## Delete Entry from empoptionalleavetaken table
				//$updateOptionalQuery="delete FROM empoptionalleavetaken WHERE  `state` ='Pending' and `empid`='".$leavetyperow['empid']."' and `date`='".$leavetyperow['date']."'";
				//$updateOptionalResult=$db->query($updateOptionalQuery);

						$updateOptionalQuery = array('state'=>'Pending', 'empid'=> $leavetyperow['empid'],'date'=>$leavetyperow['date']);
						// call update function
						$updateOptionalResult = $db->delete('empoptionalleavetaken', $updateOptionalQuery)->affectedRows();
			}
					}
				}
				//$getleavesquery = $db->query("SELECT  * FROM empleavetransactions WHERE transactionid ='".$transactionid."'");
				//$row1=$db->fetchAssoc($getleavesquery);
				$getleaves="SELECT  * FROM empleavetransactions WHERE transactionid ='".$transactionid."'";
				$getleavesquery = $db->pdoQuery($getleaves)->results();
				//$row1=$db->results($getleavesquery);
				foreach ($getleavesquery as $row1)
				if (preg_match('/CompOff Leave/', $row1['reason'])) {
					//$getinoutCompOff=$db->query("select Date from  `inout` where compofftakenday ='".$row1['startdate']."' and empid='".$row1['empid']."'");
					//$getinoutCompOffRow=$db->fetchAssoc($getinoutCompOff);
					//$updateCompOff=$db->query("UPDATE `inout` SET compofftakenday='0000-00-00' WHERE empid='".$row1['empid']."' and Date='".$getinoutCompOffRow['Date']."'");
				
					$getinoutCompOff=$db->pdoQuery("select Date from  `inout` where (compofftakenday =? and empid=?);",array($row1['startdate'],$row1['empid']));
					$getinoutCompOffRow=$db->results($getinoutCompOff);
					$updateCompOffdata = array('compofftakenday'=>'0000-00-00');
					// where condition array
					$updateCompOffWhere = array('empid'=>$row1['empid'],'Date'=>$getinoutCompOffRow['Date']);
					// call update function
					$updateCompOff = $db->update('inout', $updateCompOffdata, $updateCompOffWhere)->affectedRows();
				}
				$comments= mysql_real_escape_string($_REQUEST['comments']);	
				if(empty($comments)) {
					$comments="Deleted by Manager (".$_SESSION['u_fullname'].")";
				}
				//$result=$db->query("UPDATE  empleavetransactions SET  `approvalstatus` =  'Cancelled',`approvalcomments` = '".$comments."'  WHERE  `transactionid` ='".$transactionid."'");
				$resultdata = array('approvalstatus'=>'Cancelled','approvalcomments'=>'$comments');
				// where condition array
				$resWhere = array('transactionid'=>$transactionid);
				// call update function
				$result = $db->update('empleavetransactions', $resultdata, $resWhere)->affectedRows();
				
				if($result)
				{
					echo "<script>BootstrapDialog.alert(\"Not Approved\");</script>";
					//send mail for Not approved status to emp and manager to whom manager not approved leave
					$cmd = '/usr/bin/php -f sendmail.php '.$transactionid.' '.$row1['empid'].'  notApproveLeave >> /dev/null &';
					exec($cmd);
				}
			}
			echo '<div class="panel panel-primary">'; //panel div start
			echo '<div class="panel-heading text-center">
					<strong style="font-size:20px;">Pending Leaves of Team Member</strong>
			</div>';
			echo "<div class='panel-body'>";
				echo '<table class="table table-hover" id="teamapprove">
					<thead>
						<tr class="info">
							<th>Employee Name</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Count</th>
							<th>Reason</th>
							<th>Approval Status</th>
							<th></th>
						</tr>
					</thead>
					<tbody>';
						//$emparray=getemp($_SESSION['u_empid']);
						//$emparrayquery=$db->query("SELECT empid FROM  `emp` WHERE managerid =  '".$_SESSION['u_empid']."' AND empid != '".$_SESSION['u_empid']."'");
						//for($i=0;$i<$db->countRows($emparrayquery);$i++)
						$emparray="SELECT empid FROM  `emp` WHERE managerid =  '".$_SESSION['u_empid']."' AND empid != '".$_SESSION['u_empid']."'";
						$emparrayquery=$db->pdoQuery($emparray)->results();
						//for($i=0;$i<$emparrayquery->rowCount();$i++)
							foreach ($emparrayquery as $emparray)
						{
							/*$emparray=$db->fetchAssoc($emparrayquery);
							$sql=$db->query("select id,transactionid,startdate,enddate,count,reason,approvalstatus,approvalcomments from empleavetransactions where empid='".$emparray['empid']."' and approvalstatus='Pending'");
							$empnamequery=$db->query("select empname from emp where state='Active' and empid=".$emparray['empid']);
							$emprow=$db->fetchAssoc($empnamequery);*/
							//$emparray=$emparrayquery->results();
							$query1="select id,transactionid,startdate,enddate,count,reason,approvalstatus,approvalcomments from empleavetransactions where empid='".$emparray['empid']."' and approvalstatus='Pending'";
							$sql=$db->pdoQuery($query1)->results();
							$query2="select empname from emp where state='Active' and empid=".$emparray['empid'];
							$empnamequery=$db->pdoQuery($query2)->results();
							foreach ($empnamequery as $emprow)
							//$emprow=$empnamequery->results();
							//for($x=0;$x<$db->countRows($sql);$x++)
							//for($x=0;$x<$sql->rowCount();$x++)
								foreach ($sql as $row)
							{
								//$row=$db->fetchArray($sql);
								//$row=$sql->results();
								echo '<tr><td>'.$emprow['empname'].'</td>';
								echo '<td>'.$row['startdate'].'</td>';
								echo '<td>'.$row['enddate'].'</td>';
								echo '<td>'.$row['count'].'</td>';
								echo '<td>'.$row['reason'].'</td>';
								echo '<td>'.$row['approvalstatus'].'</td>';
								echo '<td><div class="arrow"></div></td></tr>';
								//$sql1=$db->query("select * from perdaytransactions where transactionid='".$row['transactionid']."'");
								$sqlquery="select * from perdaytransactions where transactionid='".$row['transactionid']."'";
								$sql1=$db->pdoQuery($sqlquery)->results();
								echo '<tr>
									<td colspan="7">
									<table class="table table-hover">
										<tr class="info">
										<th>Date</th>
										<th>Leave Type</th>
										<th>Shift</th>
									</tr>';
								//for($j=0;$j<$db->countRows($sql1);$j++)
								//for($j=0;$j<$sql1->rowCount();$j++)
									foreach ($sql1 as $row1)
								{
									//$row1=$db->fetchArray($sql1);
									//$row1=$sql1->results();
									echo '<tr></tr><tr><td>'.$row1['date'].'</td>';
									echo '<td>'.$row1['leavetype'].'</td>';
									echo '<td>'.$row1['shift'].'</td>';
								}
								echo '<tr></tr><tr><td><button class="btn btn-danger" onclick="notapprove'.$x.'()">Not Approve</button></td>';
								echo '<td><button class="btn btn-primary" onclick="approve(\''.$row['transactionid'].'\')">Approve</button></td>';
								echo '</tr>';
								echo '</table><div id="comments'.$x.'">
								<textarea id=txtMessage'.$x.' rows="2" class="form-control" cols="20" placeholder="Write Comments for not approving"></textarea>
								<button class="btn btn-primary" onclick="submitcomments(\''.$row['transactionid'].'\','.$x.')">OK</button>
								</div></td></tr><tr></tr>';
		
							}
						}
						echo "</tbody></table>";
					echo "</div></div>";
				echo '<div id="style"><br>
						<script type="text/javascript">';
				//$emparrayquery=$db->query("SELECT empid FROM  `emp` WHERE managerid =  '".$_SESSION['u_empid']."' AND empid != '".$_SESSION['u_empid']."'");
				//for($y=0;$y<$db->countRows($emparrayquery);$y++)
				$emparrayqueryres="SELECT empid FROM  `emp` WHERE managerid =  '".$_SESSION['u_empid']."' AND empid != '".$_SESSION['u_empid']."'";
				$emparrayquery=$db->pdoQuery($emparrayqueryres)->results();
			//	for($y=0;$y<$emparrayquery->rowCount();$y++)
				foreach ($emparrayquery as $emparray)
				{
					//$emparray=$db->fetchAssoc($emparrayquery);
				//	$emparray=$emparrayquery->results();
					//$sql=$db->query("select id,transactionid,startdate,enddate,reason,approvalstatus,approvalcomments from empleavetransactions where empid='".$emparray['empid']."' and approvalstatus='Pending'");
					//$count=$db->countRows($sql);
					$query="select id,transactionid,startdate,enddate,reason,approvalstatus,approvalcomments from empleavetransactions where empid='".$emparray['empid']."' and approvalstatus='Pending'";
					$sql=$db->pdoQuery($query);
					//$count=$sql->rowCount();
					$count=$sql -> count($sTable = 'empleavetransactions', $sWhere = 'empid = "'.$emparray['empid'].'" and approvalstatus = "Pending"' );
					
					for($z=0;$z<$count;$z++)
					{
					   echo '$("#comments'.$z.'").hide();';
					   echo 'function notapprove'.$z.'(tid)
						  {
								$("#comments'.$z.'").toggle();
						  }';
					}
				}
				echo "</script></div></body></html>";
				?>

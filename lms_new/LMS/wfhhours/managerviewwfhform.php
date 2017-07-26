<?php
session_start();
require_once '../Library.php';
require_once '../attendenceFunctions.php';
error_reporting("E_ALL");
$db=connectToDB();
?>
<?php
echo '<html>
<head>';
//calender code start 
//$getCalIds = array("fromdate", "todate", "TypeOfDayfromdate", "TypeOfDaytodate");
//$calImg = getCalImg($getCalIds);
//echo $calImg;
//calender code end here
echo '<link rel="stylesheet" type="text/css" media="screen" href="css/selfleavehistory.css" />
	  <script type="text/javascript">  
		 $("#loadingmessage").show();
         $("document").ready(function(){
			$("#wfhHrs").spinner(
               { min: 1 },
               { max: 18 },
			   { step: 0.25 }
        	);
    		$("#loadingmessage").hide();
			$( "#tabs" ).tabs();
		
			$("#editbymanager").submit(function() {
		   			$.ajax({
		       	 	data: $(this).serialize(),
		       		 type: $(this).attr("method"),
		       		 url: $(this).attr("action"),
		        	success: function(response) {
						 if(response.match(/success/)) {
							alert("WFH edited successfully");
							var eid=$("#empid").val();
							var date = $(".workeddaydynamic").val();
							$("#loadmanagersection").html(response);
							hidealldiv("loadmanagersection");
							$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&eid="+eid+"&date="+date);
				        } else {
									alert("not successs");
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
						var r=confirm("Delete Leave!");
						var eid=$("#empid").val();
						var date = $(".workeddaydynamic").val();
								if (r==true)
								{
									var dellink=$("#deleteFormbymanager").attr("href");
									$("#loadmanagersection").html(response);
									hidealldiv("loadmanagersection");
									$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&&eid="+eid+"&date="+date);
								}
								else
						  		{
						  			alert("You pressed Cancel!");
						  			$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&delcancel=1&eid="+eid+"&date="+date);
								}
							  } 
					});
					return false; // cancel original event to prevent form submitting
			});

			$("#viewrecordbymanager").submit(function() {
					if($("#empuser").val()=="")
					{
						alert("Please Enter Employee Name");
						return false;
					}
		   			$.ajax({
			       	 	 data: $(this).serialize(),
			       		 type: $(this).attr("method"),
			       		 url: $(this).attr("action"),
			        	 success: function(response) {
							var eid=$("#empid").val();
							var date=$(".workeddaydynamic").val();
							$("#loadmanagersection").html(response);
							$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&eid="+eid+"&date="+date);
			        	}
					});
					return false; // cancel original event to prevent form submitting
			});
		
			$("#viewEmpWFHbymanager").submit(function() {
					if($("#empuser").val()=="")
					{
						alert("Please Enter Employee Name");
						return false;
					}
		   			$.ajax({
			       	 	 data: $(this).serialize(),
			       		 type: $(this).attr("method"),
			       		 url: $(this).attr("action"),
			        	 success: function(response) {
							hidealldiv("loadmanagersection");
							$("#loadmanagersection").html(response);
			        	}
					});
					return false; // cancel original event to prevent form submitting
			});
			jQuery(function() {
       			jQuery("#empuser").autocomplete({
	            minLength: 1,
	            source: function(request, response) {
	                jQuery.getJSON("autocomplete/Users_JSON.php", {
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
		
		function hidealldiv(div) {
			var myCars = new Array("loadviewwfhhrcontent","loadempapplyleave", "loadempleavestatus", "loadempleavehistory", "loadempleavereport", "loadempeditprofile", "loadholidays", "loadempleavereport", "loadteamleavereport", "loadteamleaveapproval", "loadattendance", "loadcalender", "loadpendingstatus", "loadhrsection", "loadmanagersection", "loadapplyteammemberleave", "loadtrackattendance", "loadwfhhr", "loadmanagersection");
			var hidedivarr = removeByValue(myCars, div);
			hidediv(hidedivarr);
			showdiv(div);
		}

		function hidediv(arr) {
			$("#footer").show();
			for (var i = 0; i < arr.length; i++) {
				$("#" + arr[i]).hide();
				$("#" + arr[i]).html("");
			}
		}

		function showdiv(div) {
			$("#" + div).show();
		}

		function removeByIndex(arr, index) {
			arr.splice(index, 1);
		}

		function removeByValue(arr, val) {
			for (var i = 0; i < arr.length; i++) {
				if (arr[i] == val) {
					arr.splice(i, 1);
					break;
				}
			}
			return arr;
		}
			
		function editExtrawfh(tid) {
			hidealldiv("loadmanagersection");
			$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?editExtrawfh=1&tid="+tid);
		}
		function deleteExtrawfh(tid) {
		
			hidealldiv("loadmanagersection");
			$("#loadmanagersection").load("wfhhours/managerviewwfhform.php?deleteExtrahour=1&tid="+tid);
		}
   </script>
</head>
<body>

<div id="loadingmessage" style="display:none">
     <img align="middle" src="images/loading.gif"/>
</div>';
if(isset($_REQUEST['deleteFormbymanager'])){
	$tmpdate= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
	$date=$tmpdate[0];	
	$noh = isset($_POST['wfhHrs']) ? $_POST['wfhHrs'] : '';
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
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$updatedAt = date('Y-m-d H:i:s');
	//if(isset($_REQUEST['delete'])){
	$queryDel="UPDATE extrawfh set status='Deleted' WHERE `tid`= '$tid'";
	$sql2=$db->query($queryDel);
	//}
	/*elseif(isset($_REQUEST['delcancel'])){
		$queryDel="select * from extrawfh  WHERE `tid`= '$tid'";
		$sql2=$db->query($queryDel);
	}*/
	if($sql2){
		//send mail that record is deleted
		$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$eid.'  deleteExtraWFH >> /dev/null &';
		exec($cmd);
	} else {
		echo "<center><h3>Record not deleted</h3></center>";
	}
} if(isset($_REQUEST['deleteExtrahour'])){
	//edit form here employee can edit extra work from home hour and date
	$tid=$_REQUEST['tid'];
	## query database if row exists
	$tquery="select wfhHrs, date,eid from extrawfh where `tid`='$tid'";
	$tresult=mysql_query($tquery);
	$tresult=mysql_fetch_array($tresult);
	## if exists, get number of hrs and date
	$noh=$tresult['wfhHrs'];
	$date=$tresult['date'];
	$empid=$tresult['eid'];
	?>
	<form method="POST" action="wfhhours/managerviewwfhform.php?deleteFormbymanager=1" id="deletebymanager" name="deletebymanager">
			<table id="table-2" width="50%">
			<caption>Delete Extra WFH Hour</caption>
				
				<tr><td>
					<label>Employee Id&nbsp;&nbsp;</label>
					<input type="text" name="empid" id="empid" value="<?php echo $empid; ?>" readonly>
				</td></tr>
				<tr><td>
					<label>Number of Hour&nbsp;&nbsp;</label>
					<input type="text" name="wfhHrs" id="wfhHrs" value="<?php echo $noh;?>" >
				</td></tr>
				
				<tr><td>
					<label>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
					<input class="workeddaydynamic" type="text" name="dynamicworked_day" value="<?php echo $date;?>"readonly/>
		</td></tr>
				<tr><td>
				 	<input type="submit" id="delete" name="delete" value="delete">
				 	<input type="submit" id="delcancel" name="delcancel" value="cancel"></td>
				</tr>
				<tr><td> <input type="hidden" id="tid" name="tid" value="<?= $tid ?>" ></td></tr>
		   </table>
	     </form>
	<?php 
} 
if(isset($_REQUEST['editFormbymanager'])){
	$date= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
	$noh = isset($_POST['wfhHrs']) ? $_POST['wfhHrs'] : '';
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
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$updatedAt = date('Y-m-d H:i:s');
	$queryEdit="UPDATE extrawfh SET `wfhHrs`='$noh', `date`='$date', `updatedAt`='$updatedAt', `updatedBy`='".$_SESSION['user_name']."'  WHERE `tid`='$tid'";
	$sql3=$db->query($queryEdit);
	if($sql3){
		//send mail that record is updated 
		$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$eid.'  editExtraWFH >> /dev/null &';
		exec($cmd);
	} else {
		echo "<center><h3>Record not updated</h3></center>";
	}
} if(isset($_REQUEST['editExtrawfh'])){
	//edit form here employee can edit extra work from home hour and date
	$tid=$_REQUEST['tid'];
	## query database if row exists
	$tquery="select wfhHrs, date,eid from extrawfh where `tid`='$tid'";
	$tresult=mysql_query($tquery);
	$tresult=mysql_fetch_array($tresult);
	## if exists, get number of hrs and date
	$noh=$tresult['wfhHrs'];
	$date=$tresult['date'];
	$empid=$tresult['eid'];
	?>
	<form method="POST" action="wfhhours/managerviewwfhform.php?editFormbymanager=1" id="editbymanager" name="editbymanager">
			<table id="table-2" width="50%">
			<caption>Edit Extra WFH Hour</caption>
				
				<tr><td>
					<label>Employee Id&nbsp;&nbsp;</label>
					<input type="text" name="empid" id="empid" value="<?php echo $empid; ?>" readonly>
				</td></tr>
				<tr><td>
					<label>Number of Hour&nbsp;&nbsp;</label>
					<input type="text" name="wfhHrs" id="wfhHrs" value="<?php echo $noh;?>" >
				</td></tr>
				
				<tr><td>
					<label>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
					<input class="workeddaydynamic" type="text" name="dynamicworked_day" value="<?php echo $date;?>" readonly/>
		</td></tr>
				<tr><td>
				 	<input type="submit" id="submit" name="submit" value="Edit">
				 	<input type="submit" id="cancel" name="cancel" value="cancel"></td>
				</tr>
				<tr><td> <input type="hidden" id="tid" name="tid" value="<?= $tid ?>" ></td></tr>
		   </table>
	     </form>
	<?php 
} 
if(isset($_REQUEST['viewrecordbymanager']) || isset($_REQUEST['viewEmpWFHbymanager'])) { 
	
	
	if (isset($_REQUEST['displayAll'])) {
		$empQuery="select empid,empname from emp where empname='".$_REQUEST['empuser']."' and state='Active'";
		$empnametresult=$db->query($empQuery);
		$empnamerow=$db->fetchAssoc($empnametresult);
		$empid=$empnamerow['empid'];
		//show record based on employee id where status is not equal to deleted
		$query="select * from extrawfh where status!='Deleted' and eid='".$empid."' order by date";
	} else {
		$date=$_REQUEST['date'];
		$empid=$_REQUEST['eid'];
		$empQuery="select empid,empname from emp where empid='".$empid."' and state='Active'";
		$empnametresult=$db->query($empQuery);
		$empnamerow=$db->fetchAssoc($empnametresult);
		$query="select * from extrawfh where status!='Deleted' and eid='".$empid."' order by date";
	}
	
	
	$sql=$db->query("SELECT DISTINCT YEAR(date) as year FROM extrawfh where eid='".$empid."' order by year desc");
	$distinctYears=array();
	$leaveCount=$db->countRows($sql);

	echo '<h3 align=\"center\"><u>View Extra WFH Details</u></h3><br><br>';
	if($leaveCount == 0) {
		echo "<div id='tabs'><ul><div id='Info'><tr><td>No Data Available</td></tr></div></ul></div>";
	} else {
		echo '<div id="tabs">
                <ul>';
	}
	for($i=0;$i<$db->countRows($sql);$i++)
	{
		$row=$db->fetchArray($sql);
		echo "<li><a href='#".$row['year']."'>".$row['year']."</a></li>";
		array_push($distinctYears,$row['year']);
	}
	echo "</ul>";
	
	foreach ($distinctYears as $year) {
		echo "<div id='".$year."'>";
		
	
		echo "<div id='showtable'><table class=\"table-1\" width='70%'>
			<form method='POST' action='' id='WFH' name='ExtraWFHHour'>
			
			<tr>
				<th>Emp Name</th>
				<th>Date</th>
				<th>WFH Hours</th>
				<th>Reason</th>
				<th>Approval Status</th>
				<th colspan=2>Actions</th>
			</tr>";
		$sql1=$db->query($query);
		
		while($getDetailedrow=$db->fetchassoc($sql1)) {
			echo  '<tr>
		      		<td>'.$empnamerow['empname'].'</td>
					<td>'.$getDetailedrow['date'].'</td>
					<td>'.$getDetailedrow['wfhHrs'].'</td>
					<td>'.$getDetailedrow['reason'].'</td>
					<td>'.$getDetailedrow['status'].'</td>
		 			<td><div id="modify" title="'.$getDetailedrow['tid'].'" onclick=editExtrawfh("'.$getDetailedrow['tid'].'") class="'.$getDetailedrow['eid'].'"><font color="blue">Modify</font></div>
	 				<div id="delete" title="'.$getDetailedrow['tid'].'" onclick=deleteExtrawfh("'.$getDetailedrow['tid'].'") class="'.$getDetailedrow['eid'].'"><font color="blue">Delete</font></div></td>
			</tr>';
		}
		echo "</form></table></div></div>";
	}
}
if(isset($_REQUEST['viewform'])){
		echo '<form action="wfhhours/managerviewwfhform.php?viewEmpWFHbymanager=1&displayAll=1" method="POST" id="viewEmpWFHbymanager">
	<table id="table-2">
		<tr>
			<td><p><label>Enter Employee Name:</label></p></td>
         	<td><p><input id="empuser" type="text" name="empuser"/></p></td>';
		echo '<td><input class="submit" type="submit" name="submit" value="SUBMIT"/></td>
        </tr> 
 </table>
</form>';
}

echo "</body>
</html>";	
?>
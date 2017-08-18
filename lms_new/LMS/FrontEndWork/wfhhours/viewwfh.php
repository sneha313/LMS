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
echo '<link rel="stylesheet" type="text/css" media="screen" href="css/selfleavehistory.css" />
	  <script type="text/javascript">  
		 $("#loadingmessage").show();
         $("document").ready(function(){
			$("#wfhHrs").spinner(
               { min: 1 },
               { max: 18 },
				{ step:0.25 }
        	);
    		$("#loadingmessage").hide();
			$( "#tabs" ).tabs();
			$("#editwfhform").submit(function() {
		   			$.ajax({
		       	 	data: $(this).serialize(),
		       		 type: $(this).attr("method"),
		       		 url: $(this).attr("action"),
		        	success: function(response) {
						  $("#loadextrawfhhr").html(response);
						  hidealldiv("loadextrawfhhr");
						
						  	$("#loadextrawfhhr").load("wfhhours/viewwfh.php");
		        	}
					});
					return false; // cancel original event to prevent form submitting
			});
			 $("#delete").click(function(){
				var r=confirm("Delete Leave!");
				if (r==true)
				{
					var dellink=$("#delete").attr("href");
					$("#loadextrawfhhr").load("wfhhours/viewwfh.php");
					
		  		}
				else
		  		{
		  			alert("You pressed Cancel!");
		  			$("#loadextrawfhhr").load("wfhhours/viewwfh.php");
		
		  		}
			});
		});
		function hidealldiv(div) {
			var myCars = new Array("loadviewwfhhrcontent","loadempapplyleave", "loadempleavestatus", "loadempleavehistory", "loadempleavereport", "loadempeditprofile", "loadholidays", "loadempleavereport", "loadteamleavereport", "loadteamleaveapproval", "loadattendance", "loadcalender", "loadpendingstatus", "loadhrsection", "loadmanagersection", "loadapplyteammemberleave", "loadtrackattendance", "loadwfhhr");
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
			
		function editwfh(tid) {
			hidealldiv("loadextrawfhhr");
			$("#loadextrawfhhr").load("wfhhours/viewwfh.php?editwfh=1&tid="+tid);
		}
		function deletewfh(tid) {
		
			hidealldiv("loadextrawfhhr");
			$("#loadextrawfhhr").load("wfhhours/viewwfh.php?delete=1&tid="+tid);
		}
   </script>
</head>
<body>

<div id="loadingmessage" style="display:none">
     <img align="middle" src="images/loading.gif"/>
</div>';
if(isset($_REQUEST['delete'])){
//delete extra work from home hour request
	$tid=$_GET['tid'];
	$eid=$_SESSION['u_empid'];
	
	$queryDel="UPDATE extrawfh set status='Deleted' WHERE `tid`= '$tid'";
	$sql2=$db->query($queryDel);
	if($sql2){
		//send mail that record is deleted
		$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$eid.'  deleteExtraWFH >> /dev/null &';
		exec($cmd);
	} else {
		echo "<center><h3>Record not deleted</h3></center>";
	}
} elseif(isset($_REQUEST['editForm'])){
	//edit extra work from home hour request
	$eid = isset($_POST['eid']) ? $_POST['eid'] : '';
	//$date = isset($_POST['fromdate']) ? $_POST['fromdate'] : '';
	$date= isset($_REQUEST['dynamicworked_day'])? $_REQUEST['dynamicworked_day'] : '';
	$noh = isset($_POST['wfhHrs']) ? $_POST['wfhHrs'] : '';
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$updatedAt = date('Y-m-d H:i:s');
	$queryEdit="UPDATE extrawfh SET `wfhHrs`='$noh', `date`='$date', `updatedAt`='$updatedAt', `updatedBy`='".$_SESSION['user_name']."'  WHERE `eid`='$eid' and `tid`='$tid'";
	$sql3=$db->query($queryEdit);
	if($sql3){
		//send mail that record is updated 
		$cmd = '/usr/bin/php -f sendmail.php '.$tid.' '.$eid.'  editExtraWFH >> /dev/null &';
		exec($cmd);
	} else {
		echo "<center><h3>Record not updated</h3></center>";
	}
} elseif(isset($_REQUEST['editwfh'])){
	//edit form here employee can edit extra work from home hour and date
	$tid=$_REQUEST['tid'];
	$eid=$_SESSION['u_empid'];
	## query database if row exists
	$tquery="select wfhHrs, date from extrawfh where `tid`='$tid'";
	$tresult=mysql_query($tquery);
	$tresult=mysql_fetch_array($tresult);
	## if exists, get number of hrs and date
	$noh=$tresult['wfhHrs'];
	$date=$tresult['date'];
	?>
	
	<form method="POST" action="wfhhours/viewwfh.php?editForm=1" id="editwfhform" name="editwfhform">
			<table id="table-2" width="50%">
			<caption>Edit Extra WFH Hour</caption>
				<tr>
					<td>
					<label>Number of Hour&nbsp;&nbsp;</label>
					<input type="text" name="wfhHrs" id="wfhHrs" value="<?php echo $noh;?>" readonly>
				</td></tr>
				
				<tr><td>
					<label>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
					<!-- <input type="text" name="fromdate" id="fromdate" value="<?php echo $date; ?>"> -->
					<input class="workeddaydynamic" type="text" name="dynamicworked_day" value="<?php echo $date; ?>"readonly/>
		
				</td></tr>
				<tr><td>
				 	<input type="submit" id="submit" name="submit" value="Edit">
				 	<input type="submit" id="cancel" name="cancel" value="cancel"></td>
				</tr>
				<tr><td> <input type="hidden" name="eid" value="<?= $eid ?>" ></td></tr>
				<tr><td> <input type="hidden" name="tid" value="<?= $tid ?>" ></td></tr>
		   </table>
	     </form>
	<?php 
} else {

	echo '<h3 align=\"center\"><u>View WFH Details</u></h3><br><br>';
	$distinctYears=array("2017");
	echo '<div id="tabs">
                <ul>';
	foreach ($distinctYears as $year) {
		echo "<li><a href='#".$year."'>".$year."</a></li>";
	}
	echo "</ul></div>";
	
	foreach ($distinctYears as $year) {
		echo "<div id='".$year."'>";
	
		echo "<div id='showtable'><table class=\"table-1\" width='70%'>
			<form method='POST' action='' id='WFH' name='ExtraWFHHour'>
			<tr>
				<th width='20%'>Date</th>
				<th width='20%'>WFH Hours</th>
				<th width='20%'>Reason</th>
				<th width='25%'>Comments</th>
				<th width='20%'>Status</th>
				<th width='30%'>Action</th>
				
			</tr>";
		$sql1=$db->query("select * from extrawfh where eid='".$_SESSION['u_empid']."' order by date");
	
		for($i=0;$i<$db->countRows($sql1);$i++)
		{
			$row=$db->fetchArray($sql1);
			
			echo '<tr>';
			echo '<center><td class="tid" style="display:none">'.$row['tid'].'</td>';
			echo '<td>'.$row['date'].'</td>';
			echo '<td>'.$row['wfhHrs'].'</td>';
			echo '<td>'.$row['reason'].'</td>';
			echo '<td>'.$row['comments'].'</td>';
			echo '<td>'.$row['status'].'</td>';
			if(strtoupper($row['status'])=='PENDING'){
			//if status is pending then action will be visible edit/delete
			echo '<td>';
			echo '<input type="submit" onclick=deletewfh("'.$row['tid'].'") value="Delete" id="delete" name="delete">';
			echo '<input type="button" onclick=editwfh("'.$row['tid'].'") value="Edit" name="Edit"style="padding:1px 15px;">';
			echo '</td>';
			}
			else 
			{
				echo '<td></td>';
			}
			echo '</tr>';
		}
	
		echo "</center></form></table>";
		echo "</div></div>";
	}
}
echo "</body>
</html>";	
?>
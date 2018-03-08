<?php
session_start();
//require_once '../Library.php';
//require_once '../generalFunctions.php';
require_once '../librarycopy1.php';
require_once '../generalcopy.php';
$db=connectToDB();
?>
<html>
<head>
<?php
echo '<link rel="stylesheet" type="text/css" media="screen" href="css/applyleave.css" />';
if(isset($_REQUEST['role']))
{
	$_SESSION['roleofemp']=$_REQUEST['role'];
	if($_REQUEST['role']=="manager") {$divid="loadmanagersection";echo "<script>var divid=\"loadmanagersection\";</script>"; }
	if($_REQUEST['role']=="hr") { $divid="loadhrsection";echo "<script>var divid=\"loadhrsection\";</script>";}
}
?>
<script type="text/javascript">
function getdetail(tid) {
	 url='wfhhours/modifyExtrawfhhour.php?change=1&displaytable=1&tid='+tid;
	 $('#'+divid).load(''+url+'');
}
$("document").ready(function() {
	$('#modifyday').submit(function() {
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
	$('#deletetid').submit(function() {
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
	/*$('#getemptrans').submit(function() {
		$.ajax({ 
	        data: $(this).serialize(), 
	        type: $(this).attr('method'), 
	        url: $(this).attr('action'), 
	        success: function(response) { 
	            $('#loadmanagersection').load("modifyExtrawfhhour.php?change=1&displayrecentrtans=1&tid="+tid+"&empid="+empid); 
	        }
		});
			return false; 
	});*/
	jQuery(function() {
        jQuery('#empuser').autocomplete({
            minLength: 1,
            source: function(request, response) {
                jQuery.getJSON('../autocomplete/Users_JSON.php', {
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
		
		$("#deltid").click(function(){
		var r=confirm("Delete Transaction!");
		if (r==true)
		{
			var tid=$("#deltid").attr("title");
			var empid=$("#deltid").attr("class");
			$('#'+divid).load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&eid="+eid+"&date="+date);
  		}
		else
  		{
  			alert("You pressed Cancel!");
  			var tid=$("#deltid").attr("title");
  			$('#'+divid).load("wfhhours/managerviewwfhform.php?viewrecordbymanager=1&delcancel=1&eid="+eid+"&date="+date);
		}
	});
		$("#modifytid").click(function(){
			var tid=$("#modifytid").attr("title");
			var empid=$("#modifytid").attr("class");
			//$('#'+divid).load("wfhhours/modifyExtrawfhhour.php?change=1&modify=1&tid="+tid+"&empid="+empid);
			$('#'+divid).load("wfhhours/managerviewwfhform.php?editExtrawfh=1&tid="+tid);
		});
		<?php
			getDynamicSelectOptions();
		?>
});
function editExtrawfh(tid) {
	//hidealldiv('#'+divid);
	$('#'+divid).load("wfhhours/managerviewwfhform.php?editExtrawfh=1&tid="+tid);
}
function deleteExtrawfh(tid) {

	//hidealldiv('#'+divid);
	$('#'+divid).load("wfhhours/managerviewwfhform.php?deleteExtrahour=1&tid="+tid);
}
</script>
<style type="text/css">
#modifytid {
	cursor: pointer;
}

#deltid {
	cursor: pointer;
}
</style>
</head>

<body>
<h3 style="align:text-center;">Modify Employee Extra WFH Hour</h3>
	<br>
	<?php
	function displaytable($transactionid) {
		global $db;
		$transactionid=$_REQUEST['tid'];
		$query="select * from extrawfh where tid='".$transactionid."'";
		$sql=$db->pdoQuery($query)->results();
		foreach($sql as $row)
		//$row=$db->fetchassoc($sql);
		$childern=getChildren($_SESSION['u_empid']);
		$empquery="select empname from emp where empid='".$row['eid']."' and state='Active'";
		$empnametresult=$db->pdoQuery($empquery)->results();
		foreach($empnametresult as $empnamerow)
		//$empnamerow=$db->fetchAssoc($empnametresult);
		if(in_array($row['eid'],$childern) || ($_SESSION['user_dept']=="HR")) {
			echo "<table id=\"table-2\" width='70%'>
			<tr>
				<th>Transaction ID</th>
				<th>Emp Name</th>
				<th>Date</th>
				<th>Number of Hour</th>
				<th>Reason</th>
				<th>Approval Status</th>
				<th colspan=2>Actions</th>
			</tr>
			<tr>";
			echo  '<td>'.$row['tid'].'</td>
	      	<td>'.$empnamerow['empname'].'</td>
			<td>'.$row['date'].'</td>
			<td>'.$row['wfhHrs'].'</td>
			<td>'.$row['reason'].'</td>
			<td>'.$row['status'].'</td>';
			 '<td><div id="modifytid" title="'.$row['tid'].'" class="'.$row['eid'].'"><font color="blue">Modify</font></div>
	  		<div id="deltid" title="'.$row['tid'].'" class="'.$row['eid'].'"><font color="blue">Delete</font></div></td></tr>
			</table>';
		}
		else {
			echo "<script>alert(\"You dont have permissions to change '".$empnamerow['empname']." ' transaction\");</script>";
		}
	}
	function displayRecentTrans($emp)
	{
		global $db;
		global $divid;
		$empquery="select empid,empname from emp where empname='".$emp."' and state='Active'";
		$empnametresult=$db->pdoQuery($empquery)->results();
		//$empnamerow=$db->fetchAssoc($empnametresult);
		foreach($empnametresult as $empnamerow)
		$query=	"select * from extrawfh where status='Approved' and eid='".$empnamerow['empid']."'";
		$sql=$db->pdoQuery($query)->results();
		$childern=getChildren($_SESSION['u_empid']);
		if(in_array($empnamerow['empid'],$childern) || ($_SESSION['user_dept']=="HR")) {
			echo "<table class='table table-bordered' id=\"table-2\" width='70%'>
			<tr>
				<th>Emp Name</th>
				<th>Date</th>
				<th>Number of Hour</th>
				<th>Reason</th>
				<th>Approval Status</th>
				<th colspan=2>Actions</th>
			</tr>";
			foreach($sql as $row){
			//while($row=$db->fetchassoc($sql)) {
				//echo  '<td>'.$row['tid'].'</td>
			
				echo '<tr>
	      	<td>'.$empnamerow['empname'].'</td>
			<td>'.$row['date'].'</td>
			<td>'.$row['wfhHrs'].'</td>
			<td>'.$row['reason'].'</td>
			<td>'.$row['status'].'</td>
			<td><div id="modify" title="'.$row['tid'].'" onclick=editExtrawfh("'.$row['tid'].'") class="'.$row['eid'].'"><font color="blue">Modify</font></div>
	 				<div id="delete" title="'.$row['tid'].'" onclick=deleteExtrawfh("'.$row['tid'].'") class="'.$row['eid'].'"><font color="blue">Delete</font></div></td>
			</tr>';
			
			}
			echo "</table>";
		}
		else {
			echo "<script>alert(\"You dont have permissions to change '".$empnamerow['empname']." ' transaction\");</script>";
		}
	}
	if(isset($_REQUEST['change']))
	{
		if(isset($_REQUEST['del']))
		{
			getDelSection("wfhhours/modifyExtrawfhhour.php",$_REQUEST['tid'],$_REQUEST['eid'],$_SESSION['roleofemp']);
		}
		if(isset($_REQUEST['getDelteComments']))
		{
			echo '<form id="deletetid" method="POST" action="wfhhours/modifyExtrawfhhour.php?change=1&del=1&tid='.$_REQUEST['tid'].'&empid='.$_REQUEST['eid'].'">';
			echo '<table id="table-2">
			  <tbody>
			  <tr><td>Transcation ID</th>
			  <td>'.$_REQUEST['tid'].'</td></tr>
			  <tr><td>Emp Id</th>
			  <td>'.$_REQUEST['empid'].'</td></tr>
			  <tr><td>Comments</th>
			  <td><textarea name="txtMessage" rows="2" cols="20"></textarea></td></tr>
			  <tr><td><input  type="submit" name="submit" value="Submit"/></td></tr>
			  </tbody></table></form>';
		}

			
		if(isset($_REQUEST['modify']))
		{
			$transactionid=$_REQUEST['tid'];
			$empid=$_REQUEST['empid'];
			displaytable($transactionid);
			getSubmitSection($transactionid,"wfhhours/modifyExtrawfhhour.php","modifyday","wfhhours/modifyExtrawfhhour.php?change=1&submitmodifyday=1&tid=$transactionid","");
			echo "<tr><td>Comments</td><td><textarea name='txtMessage' rows='2'' cols='20'></textarea></td></tr>";
			echo "<tr><td colspan=\"2\" align='center'><input class='submit' type='submit' name='submit' value='Submit'/></td></tr>
		</tbody></table></form>";
		}
		if(isset($_REQUEST['submitmodifyday']))
		{
			getModifySection("wfhhours/modifyExtrawfhhour.php",$_SESSION['roleofemp']);
		}
		if(isset($_REQUEST['displaytable']))
		{
			displaytable($_REQUEST['tid']);
		}
		if(isset($_REQUEST['displayrecentrtans']))
		{
			displayRecentTrans($_REQUEST['empuser']);
		}
	}
	else
	{
		echo '<form action="wfhhours/modifyExtrawfhhour.php?change=1&displayrecentrtans=1" method="POST" id="getemptrans">
	<table id="table-2">
		<tr>
			<td><p><label>Enter Employee Name:</label></p></td>
         	<td><p><input id="empuser" type="text" name="empuser"/></p></td>';
		echo '<td><input class="submit" type="submit" name="submit" value="SUBMIT"/></td>
        </tr> 	
 </table>
</form>';
	}
	?>
</body>
</html>

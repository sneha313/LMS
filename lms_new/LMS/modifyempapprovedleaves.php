<?php
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
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
	 url='modifyempapprovedleaves.php?change=1&displaytable=1&tid='+tid;
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
		
		$("#deltid").click(function(){
		var r=confirm("Delete Transaction!");
		if (r==true)
		{
			var tid=$("#deltid").attr("title");
			var empid=$("#deltid").attr("class");
			$('#'+divid).load("modifyempapprovedleaves.php?change=1&getDelteComments=1&tid="+tid+"&empid="+empid);
			
  		}
		else
  		{
  			alert("You pressed Cancel!");
  			var tid=$("#deltid").attr("title");
  			$('#'+divid).load("modifyempapprovedleaves.php?change=1&displaytable=1&tid="+tid);
  		}
	});
		$("#modifytid").click(function(){
			var tid=$("#modifytid").attr("title");
			var empid=$("#modifytid").attr("class");
			$('#'+divid).load("modifyempapprovedleaves.php?change=1&modify=1&tid="+tid+"&empid="+empid);
		
		});
		<?php
			getDynamicSelectOptions();
		?>
});
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
	<center>
		<h3>Modify Employee Approved Leaves</h3>
	</center>
	<br>
	<?php
	function displaytable($transactionid) {
		global $db;
		$sql=$db->query("select * from empleavetransactions where transactionid='".$transactionid."'");
		$row=$db->fetchassoc($sql);
		$childern=getChildren($_SESSION['u_empid']);
		$empnametresult=$db->query("select empname from emp where empid='".$row['empid']."' and state='Active'");
		$empnamerow=$db->fetchAssoc($empnametresult);
		if(in_array($row['empid'],$childern) || ($_SESSION['user_dept']=="HR")) {
			echo "<table id=\"table-2\" width='70%'>
			<tr>
				<th>Transaction ID</th>
				<th>Emp Name</th>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Count</th>
				<th>Reason</th>
				<th>approval Status</th>
				<th colspan=2>Actions</th>
			</tr>
			<tr>";
			echo  '<td>'.$row['transactionid'].'</td>
	      	<td>'.$empnamerow['empname'].'</td>
			<td>'.$row['startdate'].'</td>
			<td>'.$row['enddate'].'</td>
			<td>'.$row['count'].'</td>
			<td>'.$row['reason'].'</td>
			<td>'.$row['approvalstatus'].'</td>';
			if (!preg_match('/CompOff Leave/', $row['reason'])) {
				echo '<td><div id="modifytid" title="'.$row['transactionid'].'" class="'.$row['empid'].'"><font color="blue">Modify</font></div></td>';
			}
	  		echo '<td><div id="deltid" title="'.$row['transactionid'].'" class="'.$row['empid'].'"><font color="blue">Delete</font></div></td>
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
		$empnametresult=$db->query("select empid,empname from emp where empname='".$emp."' and state='Active'");
		$empnamerow=$db->fetchAssoc($empnametresult);
		$sql=$db->query("select * from empleavetransactions where approvalstatus='Approved' and empid='".$empnamerow['empid']."'");
		$childern=getChildren($_SESSION['u_empid']);
		if(in_array($empnamerow['empid'],$childern) || ($_SESSION['user_dept']=="HR")) {
			echo "<table id=\"table-2\" width='70%'>
				  <caption> Click on tranasaction Id to modify approved Leaves.</caption>
			<tr>
				<th>Transaction ID</th>
				<th>Emp Name</th>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Count</th>
				<th>Reason</th>
				<th>approval Status</th>
			</tr>";
			while($row=$db->fetchassoc($sql)) {
				echo  '<tr><td><a href="javascript:getdetail(\''.$row['transactionid'].'\')">'.$row['transactionid'].'</a></td>
		      		<td>'.$empnamerow['empname'].'</td>
					<td>'.$row['startdate'].'</td>
					<td>'.$row['enddate'].'</td>
					<td>'.$row['count'].'</td>
					<td>'.$row['reason'].'</td>
					<td>'.$row['approvalstatus'].'</td>
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
			getDelSection("modifyempapprovedleaves.php",$_REQUEST['tid'],$_REQUEST['empid'],$_SESSION['roleofemp']);
		}
		if(isset($_REQUEST['getDelteComments']))
		{
			echo '<form id="deletetid" method="POST" action="modifyempapprovedleaves.php?change=1&del=1&tid='.$_REQUEST['tid'].'&empid='.$_REQUEST['empid'].'">';
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
			getSubmitSection($transactionid,"modifyempapprovedleaves.php","modifyday","modifyempapprovedleaves.php?change=1&submitmodifyday=1&tid=$transactionid","");
			echo "<tr><td>Comments</td><td><textarea name='txtMessage' rows='2'' cols='20'></textarea></td></tr>";
			echo "<tr><td colspan=\"2\" align='center'><input class='submit' type='submit' name='submit' value='Submit'/></td></tr>
		</tbody></table></form>";
		}
		if(isset($_REQUEST['submitmodifyday']))
		{
			getModifySection("modifyempapprovedleaves.php",$_SESSION['roleofemp']);
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
		echo '<form action="modifyempapprovedleaves.php?change=1&displayrecentrtans=1" method="POST" id="getemptrans">
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

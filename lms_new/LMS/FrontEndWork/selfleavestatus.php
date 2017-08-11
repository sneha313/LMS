<?php
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
$db = connectToDB();
?>
<?php
if(isset($_REQUEST['deltid']))
{
	getDelSection("selfleavestatus.php",$_REQUEST['tid'],$_SESSION['u_empid'],"");
}
if(isset($_REQUEST['editleave'])){
	getModifySection("selfleavestatus.php","");
}
else
{


?>
<?php
echo '<html>
<head>
<link rel="stylesheet" type="text/css" media="screen" href="css/table.css" />
<script type="text/javascript">  
        $("document").ready(function(){
            $("#table-2 tr:odd").addClass("odd");
            $("#table-2 tr:not(.odd)").hide();
            $("#table-2 tr:first-child").show();
            $("#table-2 tr.odd").click(function(){
                $(this).next("tr").toggle();
                $(this).find(".arrow").toggleClass("up");
            });
          });
        
        $("#deltid").click(function(){
		var r=confirm("Delete Leave!");
		if (r==true)
		{
			var dellink=$("#deltid").attr("href");
			$("#loadempleavestatus").load(dellink);
			
  		}
		else
  		{
  			alert("You pressed Cancel!");
  			$("#loadempleavestatus").load("index.php");

  		}
		});
    	function show_alert(Leaves, daysPermitted){
    		alert("Number of Days permitted for this Special leave is " + daysPermitted+ ", But You Have Selected " + Leaves );
    		window.location="selfleavestatus.php";
    	}
       </script>';
	echo '<script type="text/javascript">  
        $("document").ready(function(){';
        getDynamicSelectOptions();
		echo '});</script>
</head>
<body>';
echo '<h3 align="center"><u>Pending Leaves</u></h3><br><br>';
echo "<table id=\"table-2\" width='70%'>
			<tr>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Count</th>
				<th width='40%'>Reason</th>
				<th colspan=2>Actions</th>
				<th></th>
			</tr><tr>";
$sql = $db -> query("select * from empleavetransactions where empid='" . $_SESSION['u_empid'] . "' and approvalstatus='Pending'");
$splLeave = "";
for ($i = 0; $i < $db -> countRows($sql); $i++) {
	$row = $db -> fetchArray($sql);
	echo '<td>' . $row['startdate'] . '</td>';
	echo '<td>' . $row['enddate'] . '</td>';
	echo '<td>' . $row['count'] . '</td>';
	echo '<td>' . $row['reason'] . '</td>';

		if($row['leave_type']=='compoff')
		{
			echo '<td><a id="deltid' . $i . '" href=' . $_SERVER['PHP_SELF'] . '?deltid=1&tid=' . $row['transactionid'] . '&compoffdate='. $row['startdate'] .'>Delete</td>';
		}
		else{
		echo '<td><a class="modify" href="#">Modify</a> &nbsp;' . '<a id="deltid' . $i . '" href=' . $_SERVER['PHP_SELF'] . '?deltid=1&tid=' . $row['transactionid'] . '>Delete</td>';
		}

	echo '<td><div class="arrow"></div></td></tr>';
	getSubmitSection($row['transactionid'], "selfleavestatus.php", "editleaveform", "selfleavestatus.php?editleave=1", $i);
	if (date("Y-m-d", strtotime('-1 days')) < $row['startdate']) {
		if($row['leave_type']!='compoff')
		{
		
		echo "<tr></tr><tr><td><p><input  type='submit' name='submit' value='Modify Leave' /></p></td>";
		}
	}
	echo '</form></table>';
	echo '</td></tr><tr></tr>';
}
echo "</table>";
$count = $db -> countRows($sql);
echo '<div id="style"><br>
			<script type="text/javascript">';
for ($x = 0; $x < $count; $x++) {
	echo "$('#editleaveform$x').submit(function() {
    				$.ajax({ 
        			data: $(this).serialize(),
        			type: $(this).attr('method'), 
        			url: $(this).attr('action'), 
        			success: function(response) {
        				if(response)
        				{
        					alert(response);
	            			window.location=\"index.php\";
	            		}
        			}
        		});
        		return false; 
				});
			";
	echo '$("#deltid' . $x . '").click(function(){
		var r=confirm("Delete Leave!");
		if (r==true)
		{
			var dellink=$("#deltid").attr("href");
			$("#loadempleavestatus").load(dellink);
  		}
		else
  		{
  			alert("You pressed Cancel!");
  			var index="index.php";
  			$("#deltid' . $x . '").attr(\'href\',\'index.php\');
  		}
		});
	';
}
echo "</script></div></body>
</html>";
}
?>


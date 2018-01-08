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
		
<script type="text/javascript">  
	$("document").ready(function(){
    	$(".pending tr:odd").addClass("odd");
        $(".pending tr:not(.odd)").hide();
        $(".pending tr:first-child").show();
        $(".pending tr.odd").click(function(){
        	$(this).next("tr").toggle();
            $(this).find(".arrow").toggleClass("up");
         });
     });
        
     $("#deltid").click(function(){
		BootstrapDialog.confirm("Delete Leave!", function(result){
		if(result) {
			var dellink=$("#deltid").attr("href");
			$("#loadempleavestatus").load(dellink);
  		}
		else
  		{
  			BootstrapDialog.alert("You pressed Cancel!");
  			$("#loadempleavestatus").load("index.php");
  		}
		});
		});
    	function show_alert(Leaves, daysPermitted){
    		BootstrapDialog.alert("Number of Days permitted for this Special leave is " + daysPermitted+ ", But You Have Selected " + Leaves );
    		window.location="selfleavestatus.php";
    	}
       </script>';
	echo '<script type="text/javascript">  
        $("document").ready(function(){';
        getDynamicSelectOptions();
		echo '});</script>
</head>
<body>
   <div class="col-sm-12">';
	echo '<div class="panel panel-primary">
	    	<div class="panel-heading text-center">
	    		<strong style="font-size:20px;">Pending Leaves</strong>
	    	</div>
	    	<div class="panel-body">
				<table class="table table-hover pending">
				<tr class="success">
					<th>Start Date</th>
					<th>End Date</th>
					<th>Count</th>
					<th>Reason</th>
					<th>Actions</th>
					<th></th>
				</tr><tr>';
				$sql = $db -> query("select * from empleavetransactions where empid='" . $_SESSION['u_empid'] . "' and approvalstatus='Pending'");
				$splLeave = "";
				if($db -> countRows($sql)>0){
				for ($i = 0; $i < $db -> countRows($sql); $i++) {
					$row = $db -> fetchArray($sql);
					echo '<td>' . $row['startdate'] . '</td>';
					echo '<td>' . $row['enddate'] . '</td>';
					echo '<td>' . $row['count'] . '</td>';
					echo '<td>' . $row['reason'] . '</td>';
					
					if($row['leave_type']=='compoff')
					{
						echo '<td><a id="deltid' . $i . '" href=' . $_SERVER['PHP_SELF'] . '?deltid=1&tid=' . $row['transactionid'] . '&compoffdate='. $row['startdate'] .'><i class="fa fa-trash" aria-hidden="true"></i></td>';
					}
					else{
						if($row['startdate']>date("Y-m-d"))
						{
							echo '<td><a class="modify" href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a> &nbsp;' . '<a id="deltid' . $i . '" href=' . $_SERVER['PHP_SELF'] . '?deltid=1&tid=' . $row['transactionid'] . '><i class="fa fa-trash" aria-hidden="true"></i></td>';
						}
						else
						{
							echo '<td></td>';
						}
						//echo '<td><a class="modify" href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a> &nbsp;' . '<a id="deltid' . $i . '" href=' . $_SERVER['PHP_SELF'] . '?deltid=1&tid=' . $row['transactionid'] . '><i class="fa fa-trash" aria-hidden="true"></i></td>';
					}
				
					echo '<td><div class="arrow"></div></td></tr>';
					getSubmitSection($row['transactionid'], "selfleavestatus.php", "editleaveform", "selfleavestatus.php?editleave=1", $i);
					if (date("Y-m-d", strtotime('-1 days')) < $row['startdate']) {
						if($row['leave_type']!='compoff')
						{
							echo "<tr></tr><tr><td><p><input type='submit' class='btn btn-primary' name='submit' value='Modify Leave' /></p></td>";
						}
					}
					echo '</form></table>';
					echo '</td></tr><tr></tr>';
				}
			}
			else {
				echo '<td class="info" colspan="6" align="center">No Pending Leaves for you</td>';
			}
				echo "</table></div></div></div>";
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
				        					BootstrapDialog.alert(response);
					            			window.location=\"index.php\";
					            		}
				        			}
				        		});
				        		return false; 
								});
							";
		echo '$("#deltid' . $x . '").click(function(){
		        BootstrapDialog.confirm("Delete Leave!", function(result){
		            if(result) {
		                var dellink=$("#deltid").attr("href");
						$("#loadempleavestatus").load(dellink);
		            }else {
		                BootstrapDialog.alert("You pressed Cancel!");
			  			var index="index.php";
			  			$("#deltid' . $x . '").attr(\'href\',\'index.php\');
		            }
		        });
			});
		';
	}
echo "</script></div></body>
</html>";
}
?>
<?php
session_start();
require_once 'Library.php';
?>
<html>
	<head>
		<script>
			function hidealldiv(div) {
				var myCars = new Array("loadempapplyleave", "loadempleavestatus", "loadempleavehistory", "loadempleavereport", "loadempeditprofile", "loadholidays", "loadempleavereport", "loadteamleavereport", "loadteamleaveapproval", "loadattendance", "loadcalender", "loadpendingstatus", "loadhrsection", "loadmanagersection", "loadapplyteammemberleave", "loadtrackattendance", "loadextrawfhhr");
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
			$("#managermodifyempapprovedleaves").click(function() {
				hidealldiv('loadmanagersection');
				$("#loadmanagersection").load('modifyempapprovedleaves.php?role=manager');
			});
			$("#managerApproveEmpLeave").click(function() {
			     hidealldiv('loadmanagersection');
           		     $("#loadmanagersection").load('approveEmpLeave.php?role=manager');
        	});
			$("#addextrawfhmanager").click(function() {
			     hidealldiv('loadmanagersection');
          		     $("#loadmanagersection").load('wfhhours/manageraddwfhforemp.php?role=manager');
       		});
			$("#viewextrawfhmanager").click(function() {
			     hidealldiv('loadmanagersection');
          		     $("#loadmanagersection").load('wfhhours/managerviewwfhform.php?role=manager&viewform=1');
       		});
			//$("#modifyextrawfhmanager").click(function() {
			   //  hidealldiv('loadmanagersection');
         		// $("#loadmanagersection").load('wfhhours/modifyExtrawfhhour.php?role=manager');
      		//});
			$("#approveextrawfhmanager").click(function() {
			     hidealldiv('loadmanagersection');
         		 $("#loadmanagersection").load('wfhhours/approveEmpExtrawfhhour.php?role=manager&approveview=1');
      		});
		</script>
		<style type="text/css">
			#addextrawfhmanager{
				cursor: pointer;
			}
			#viewextrawfhmanager{
				cursor: pointer;
			}
			#managerApproveEmpLeave{
				cursor: pointer;
			}	
			#managermodifyempapprovedleaves{
				cursor: pointer;
			}
			#modifyextrawfhmanager{
				cursor: pointer;
			}
			#approveextrawfhmanager{
				cursor: pointer;
			}
		</style>
		<?php
		$getCalIds = array("fromDate", "toDate");
		$calImg = getCalImg($getCalIds);
		echo $calImg;
		?>
	</head>
	<body>
		<?php
		if (isset($_REQUEST['managerlinks'])) {
			echo "<u>Manager Jobs</u>";
			echo "<ul>";
			echo "<li><a id='managermodifyempapprovedleaves'>Modify Empoloyee Approved Leaves</a></li></br>";
			echo "<li><a id='managerApproveEmpLeave'>Approve Employee Leaves</a></li><br>";
			echo "</ul>";
			echo "<u>Apply Extra WFH for Employee</u>";
			echo "<ul>";
			echo "<li><a id='addextrawfhmanager'>Add Extra WFH Hour</a></li></br>";
			echo "</ul>";
			echo "<u>Approve/Delete Extra WFH for Employee</u>";
			echo "<ul>";
			//echo "<li><a id='modifyextrawfhmanager'>Modify Extra Work from Home Hour</a></li></br>";
			echo "<li><a id='approveextrawfhmanager'>Approve/Cancel Extra WFH Hour</a></li><br>";
			echo "<li><a id='viewextrawfhmanager'>View/Modify Extra WFH Hour</a></li><br>";
			echo "</ul>";
		}
		?>
	</body>
</html>


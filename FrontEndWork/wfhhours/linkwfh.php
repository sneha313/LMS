<?php
	session_start();
	//require_once '../Library.php';
	require_once '../librarycopy1.php';
	$db=connectToDB();
?>
<html>
	<head>
		<script type="text/javascript">
			function hidealldiv(div) {
				var myCars = new Array("loadempapplyleave", "loadempleavestatus", "loadempleavehistory", "loadempleavereport", "loadempeditprofile", "loadholidays", "loadempleavereport", "loadteamleavereport", "loadteamleaveapproval", "loadattendance", "loadcalender", "loadpendingstatus", "loadhrsection", "loadmanagersection", "loadapplyteammemberleave", "loadtrackattendance", "loadextrawfhhr", "loadwfhhr");
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
			$("document").ready(function() {
				$("#addwfh").click(function(){
					$("#loadextrawfhhr").load('wfhhours/addwfh.php?addWFHhrForm=1');
				});
				
				$("#editwfh").click(function(){
					$("#loadextrawfhhr").load('wfhhours/viewwfh.php');
				});
					
			});
		</script>
		 
		<style type="text/css">
			#addwfh,#editwfh {
				cursor: pointer;
			}
		</style>
	 
	</head>
 
	 <body>
		 <?php 
			 echo "<ul>";
			 echo "<li><a id='addwfh'>Add WFH Hours</a></li><br>";
			 echo "<li><a id='editwfh'>View/Edit/Delete WFH Hours</a></li><br>";
			 echo "</ul>";
		 ?>
	</body>
</html>
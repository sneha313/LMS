<?php
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
$db = connectToDB();
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="public/js/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css">
		<link rel="stylesheet" href="public/js/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script type="text/javascript" src="public/js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" href="public/js/jqueryui/css/redmond/jquery-ui.css">
		<script type="text/javascript" src="public/js/jqueryui/js/jquery-ui.js"></script>
		<script type="text/javascript" src="public/js/bootstrap/js/bootstrap.min.js"></script>
  		<script type="text/javascript" src="public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
  		<script type="text/javascript" src="public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
		
		<script>
			$(document).ready(function() {
				function getEventDataList() {
					var view = $('#calendar').fullCalendar('getView');
						data = "title=" + view.title;
						$.get("calenderdata.php?getEmpData=1", data, function(response) {
							var empData = "<fieldset><legend>List of Employees on Leave </legend>" + "<table id='table-2'>" + "<thead>" + "<tr>" + "<th>Name</th>" + "<th>Date</th>" + "<th>Leave Type</th>" + "</tr>" + "</thead>" + "<tbody>";
							$.each(jQuery.parseJSON(response), function(index, value) {
								var obj = value;
								empData = empData.concat("<tr>" + "<td>" + obj.name + "</td>" + "<td>" + obj.date + "</td>" + "<td>" + obj.leavetype + "</td>" + "</tr>");

							});
							empData = empData.concat("</tbody></table>");
							$("#empList").html(empData);
						});
						return false;
				}
				$('#calendar').fullCalendar({
					theme : true,
					weekMode : 'liquid',
					ignoreTimezone : false,
					header : {
						left : 'prev,next today',
						center : 'title',
						right : 'month'
					},
					eventMouseover : function(event) {
						if (event.title) {
							$(this).attr('title', event.start);
							return false;
						}
						$(this).title('hello');
					},
					eventClick : function(event) {
						getEventDataList();
					},
					events : 'calenderdata.php',
					loading : function(bool) {
						if (bool)
							$('#loading').show();
						else
							$('#loading').hide();
					}
				});
				$(".fc-button-next").click(function() {
					getEventDataList();
				});
				$(".fc-button-prev").click(function() {
					getEventDataList();
				});
			});
		</script>
		<style>
			#calendar {
				width: 480px;
				float: left;
				padding-right:10px;
			}
			#empList {
				float: left;
				width: 290px;
				
			}
			#loading {
				position: absolute;
				top: 5px;
				right: 5px;
			}

		</style>
		<link rel="stylesheet" type="text/css" media="screen" href="css/table.css" />
		
	</head>
	<body>
		<div id='calendar'></div>
		<div id="empList"></div>
		<div id='loading' style='display:none'>
			loading...
		</div>
		

	</body>
</html>

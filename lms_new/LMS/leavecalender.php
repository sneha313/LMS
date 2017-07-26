<?php
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
$db = connectToDB();
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel='stylesheet' href='css/theme.css' />
		<link href='css/fullcalendar.css' rel='stylesheet' />
		<link href='css/fullcalendar.print.css' rel='stylesheet' media='print' />
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

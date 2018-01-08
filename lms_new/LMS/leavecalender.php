<?php
	session_start();
	require_once 'Library.php';
	require_once 'generalFunctions.php';
	$db = connectToDB();
?>
<head>
	<link rel='stylesheet' href='public/css/theme.css' />
	<link href='public/css/fullcalendar.css' rel='stylesheet' />
	<link href='public/css/fullcalendar.print.css' rel='stylesheet' media='print' />
	<style>
		#calendar {
			width: 550px;
			float: left;
			padding-right:10px;
			margin-top:20px;
		}
		#empList {
			float: left;
			width: 360px;
			margin-left:65px;
			margin-top:20px;
		}
		#loading {
			margin-top: 450px;
			margin-left:100px;
		}
		tr td{
			font-size:15px;
		}

	</style>
</head>

<!-- container-fluid div start -->
<div class="container-fluid">
	<!--row start-->
	<div class="row">
		<div class="col-sm-10"><!--10 column div start-->
			<div class="row">
				<div class="col-sm-8">
					<div id='calendar'></div>
				</div>
				<div class="col-sm-4">
					<div id="empList"></div>
				</div>
			</div>
			<div id='loading' style='display:none;'>
				<strong>loading...</strong>
			</div>
		</div><!--10 column div close-->
	</div><!--row div close-->
</div><!--container fluid div end-->
		
<script>
	function getEventDataList() {
		var view = $('#calendar').fullCalendar('getView');
			data = "title=" + view.title;
			$.get("calenderdata.php?getEmpData=1", data, function(response) {
				var empData = "<div class='panel panel-primary' style='width:370px;'><div class='panel-heading text-center'><strong>List of Employees on Leave </strong></div>" + "<div class='panel-body'><table class='table'>" + "<thead>" + "<tr class='success'>" + "<th>Name</th>" + "<th>Date</th>" + "<th>Leave Type</th>" + "</tr>" + "</thead>" + "<tbody>";
				$.each(jQuery.parseJSON(response), function(index, value) {
					var obj = value;
					empData = empData.concat("<tr>" + "<td class='info'>" + obj.name + "</td>" + "<td class='warning'>" + obj.date + "</td>" + "<td class='danger'>" + obj.leavetype + "</td>" + "</tr>");
				});
				empData = empData.concat("</tbody></table></div></div>");
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
</script>

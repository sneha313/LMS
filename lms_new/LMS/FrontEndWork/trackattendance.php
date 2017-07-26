<?php
session_start();
require_once 'Library.php';
require_once 'attendenceFunctions.php';
error_reporting("E_ALL");
?>
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
		
		<?php
		$getCalIds = array("fromdate", "todate", "TypeOfDayfromdate", "TypeOfDaytodate");
		$calImg = getCalImg($getCalIds);
		echo $calImg;
		?>
		<script type="text/javascript">
		 	$("document").ready(function(){
	        		$(".trackData tr:odd").addClass("odd");
			        $(".trackData tr:not(.odd)").hide();
	            		$(".trackData tr:first-child").show();
	            		$(".trackData tr.odd").click(function(){
		    	        	$(this).next("tr").toggle();
	            		}); 
	          	});
			 
			function toggle(thisname) {
				tr = document.getElementsByTagName('tr')
				for ( i = 0; i < tr.length; i++) {
					if (tr[i].getAttribute(thisname)) {
						if (tr[i].style.display == 'none') {
							tr[i].style.display = '';
						} else {
							tr[i].style.display = 'none';
						}
					}
				}
			}


			$('#TrackAttInd').submit(function() {
				$('#TrackAccessData').html(" ");
				if ( $("#hideDept").val()=="none" ) {
					alert("Please selct the Department");
					return false;
				}
				if ( $("#hideDept").val()=="ALL" && $("#getEmpName").val()=="ALL") {
					alert("Please wait for few minutes to get the results for all ECI Employeees.It will take more than a minute.");
				}
				$('#loadingmessage').show();
				$.ajax({
					data : $(this).serialize(),
					type : $(this).attr('method'),
					url : $(this).attr('action'),
					success : function(response) {
						$('#loadtrackattendance').html(response);
						if($("#balanceDialog")) {
                                                       $("#balanceDialog").hide();
                                                }
					}
				});
				return false;
			});
			$('.open-datetimepicker').click(function(event){
			    event.preventDefault();
			    $('#datetimepicker').click();
			});
			 $( "#accordion-new" ).accordion({
				 heightStyle: "content",
				 collapsible: true
			 });

			 $.each( $( "#accordion-new h3"), function( i, val ) {
					var first=$($(val)).next().find(".teamUntrackedLeaves").text();
					if ( first > 10 ) { 
						var firstString = "<font color=red>"+$(val).next().find(".teamUntrackedLeaves").text();
					}  else { 
						var firstString =$(val).next().find(".teamUntrackedLeaves").text();
					}
					
					$(val).html("<table class='table'><tr><td width='30%'><b>"+$(val).text()+"</b></td><td width='70%'><table class='table'><tr><td>Total Untracked Leaves: "+
					firstString+"</td></tr></table>");
			});
			 
		</script>
		<style>
			.footer1 {
				background: #031432 url("../images/footer/footer-bg.png") repeat scroll left top;
				padding-top: 40px;
				padding-right: 0;
				padding-bottom: 20px;
				padding-left: 0;/*	border-top-width: 4px;
				border-top-style: solid;
				border-top-color: #003;*/
				margin-top:-20px;
				color:white;
			}

			.title-widget {
				color: #898989;
				font-size: 20px;
				font-weight: 300;
				line-height: 1;
				position: relative;
				text-transform: uppercase;
				font-family: 'Fjalla One', sans-serif;
				margin-top: 0;
				margin-right: 0;
				margin-bottom: 25px;
				margin-left: 0;
				padding-left: 28px;
			}

			.title-widget::before {
				background-color: #ea5644;
				content: "";
				height: 22px;
				left: 0px;
				position: absolute;
				top: -2px;
				width: 5px;
			}



			.widget_nav_menu ul {
				list-style: outside none none;
				padding-left: 0;
			}

			.widget_archive ul li {
				background-color: rgba(0, 0, 0, 0.3);
				content: "";
				height: 3px;
				left: 0;
				position: absolute;
				top: 7px;
				width: 3px;
			}


			.widget_nav_menu ul li {
				font-size: 13px;
				font-weight: 700;
				line-height: 20px;
				position: relative;
				text-transform: uppercase;
				border-bottom: 1px solid rgba(0, 0, 0, 0.05);
				margin-bottom: 7px;
				padding-bottom: 7px;
				width:95%;
			}



			.title-median {
				color: #636363;
				font-size: 20px;
				line-height: 20px;
				margin: 0 0 15px;
				text-transform: uppercase;
				font-family: 'Fjalla One', sans-serif;
			}

			.footerp p {font-family: 'Gudea', sans-serif; }


			#social:hover {
    			-webkit-transform:scale(1.1); 
				-moz-transform:scale(1.1); 
				-o-transform:scale(1.1); 
			}
			#social {
				-webkit-transform:scale(0.8);
                /* Browser Variations: */
				-moz-transform:scale(0.8);
				-o-transform:scale(0.8); 
				-webkit-transition-duration: 0.5s; 
				-moz-transition-duration: 0.5s;
				-o-transition-duration: 0.5s;
			}           
			/* Only Needed in Multi-Coloured Variation  */
			.social-fb:hover {
				color: #3B5998;
			}
			.social-tw:hover {
				color: #4099FF;
			}
			.social-gp:hover {
				color: #d34836;
			}
			.social-em:hover {
				color: #f39c12;
			}
			.nomargin { margin:0px; padding:0px;}

			.footer-bottom {
				background-color: #15224f;
				min-height: 30px;
				width: 100%;
				margin-bottom:3px;
			}
			.copyright {
				color: #fff;
				line-height: 30px;
				min-height: 30px;
				padding: 7px 0;
			}
			.design {
				color: #fff;
				line-height: 30px;
				min-height: 30px;
				padding: 7px 0;
				text-align: right;
			}
			.design a {
				color: #fff;
			}
			#img{
				float:left;
			}
			#text{
				margin-left:7px;
				float:left;
				color:grey;
			}
			.navbar-inverse{
				background-color:#031432;
			}
			.rectangle{
				width:180px;
				height:180px;
				background:#4682B4;
				border-radius:2px;
				padding:10px;
			}
			#teamMemberLeave{
				background:white;
				padding:18px;
				margin:5px;
			}
			#upcomingHoliday{
				background:white;
				padding:18px;
				margin:5px;
			}
			#teamMemberBirthday{
				background:white;
				padding:18px;
				margin:5px;
			}
			.navbar-default{
				background:white;
				margin-top:-20px;
				margin-bottom:20px;
				padding-left:25px;
			}
			.arrow {
				background: transparent url(images/arrows.png) no-repeat scroll 0px
					-16px;
				width: 16px;
				height: 16px;
				display: block;
			}
		</style>
		
		<?php
		echo '
		<script>
		$("#hideDept").change(function() {
			var dept=$("#hideDept").val();
			var empid="' . $_SESSION['u_empid'] . '";
			if(empid==dept || dept=="none") {
				$("#hideName").hide();
			} else {
				$("#hideName").show();
				$.post("getSplLeaveOptions.php?dept="+escape(dept),function(data) {
					$("#getEmpName").empty();
					$("#getEmpName").append(data);
				});
			}
		});
		</script>';
		?>
		<link rel="stylesheet" type="text/css" media="screen" href="css/table.css" />
		<title>Attendence analyze</title>
	</head>
	<body>
		<nav class="navbar navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<div id="img">
						<img class="img-responsive" src="img/3.jpg" style="height:50px;">
					</div>
					
				</div>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#" style="font-size:16px; color:white; padding-top:20px; padding-right:30px; font-family:cursive;"><b> Morning, Victoria !</b></a></li>
					<li><a href="help.php" style="font-size:16px; color:white; padding-top:20px;"><i class="fa fa-question-circle" aria-hidden="true"></i><b> Need Help</b></a></li>
					<li><a href="login.php" style="font-size:16px; color:white; padding-top:20px;"><i class="fa fa-sign-out" aria-hidden="true"></i><b> Logout</b></a></li>
				</ul>
			</div>
		</nav>
		<nav class="navbar navbar-default navbar-static-top">
		<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button><!--button close-->
			<a class="navbar-brand" href="#">Leave Management System</a>
		</div><!--navbar header-->
		<div id="navbar" class="navbar-collapse collapse">
		<ul class="nav navbar-nav navbar-right" style="padding-right:80px;">
		<li id="home"><a href="Holidays.php">Holiday List</a></li>
		<li><a href="attendance.php">Attendance</a></li>
		<li class="active"><a href="trackattendance.php">Track Leaves</a></li>
		<li><a href="#Login">Leave Calender</a></li>
		<li><a href="#PostList">Apply VOE</a></li>
		</ul>
		
		</div>
		</div><!--container div close-->
		</nav><!--nav close-->
		
		<div class="container-fluid well" style="margin-top:-20px;">
		<!--row start-->
		<div class="row">
		<!--2 column start-->
			<div class="col-sm-2">
				<div class="rectangle">
					<a href="#"><img src="img/4.jpg" class="img-circle img-responsive" alt="" width="150px;" height="80px;"></a>
					<center><h6 style="color:white; font-size:14px; font-family:Times New Roman, Georgia, Serif;">Victoria Baker</h6>
					<span class="text-size-small" style="color:white;">Santa Ana, CA</span>
					</center>
				</div>
				<hr>
				<ul class="list-group">
					<li class="list-group-item active"><a href="#" style="color:white; font-size:18px;">My Account</a></li>
					<li class="list-group-item"><a href="lms.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp; Profile<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:50px;"></i></a></li>
					<li class="list-group-item"><a href="personalinfo.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Personal Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:30px;"></i></a></li>
					<li class="list-group-item"><a href="officialinfo.php"><i class="fa fa-building" aria-hidden="true"></i>&nbsp;Official Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<li class="list-group-item"><a href="leaveinfo.php"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;My Leave Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:20px;"></i></a></li>
				</ul>
			</div><!--2 column end-->
			<div class="col-sm-2"></div>
		<?php
		$untrackedLeaves=0;
		$db = connectToDB();
		
		# Generate departments based on the level of the employee
		$deps = "<option selected value=\"ALL\">ALL</option>";
		
		# Departments for HR
		if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
			$query = "SELECT * FROM `emp` ORDER BY empname ASC";
			$querydept = "SELECT distinct(dept) FROM `emp` ORDER BY dept ASC";
			$resultdept = $db -> query($querydept);
		} else if (strtoupper($_SESSION['user_desgn']) == 'MANAGER') {
			# Departments for manager
			$deps = "";
			if ($_SESSION['u_managerlevel'] != 'level1') {
				$query = "SELECT distinct(dept) FROM `emp` WHERE managerid='" . $_SESSION['u_empid'] . "' and state='Active' ORDER BY empname ASC";
				$result = $db -> query($query);
				$deps = " <option selected value=\"none\">NONE</option>";
				$deps = $deps." <option value=\"ALL\">ALL</option>";
			} else {
				$query = "SELECT * FROM `emp` WHERE managerid='" . $_SESSION['u_empid'] . "' and state='Active' ORDER BY empname ASC";
				$result = $db -> query($query);
				$deps = " <option selected value=\"ALL\">ALL</option>";
			}
			
			//Including self name also in the list
			$deps = $deps . '<option value="' . $_SESSION["u_empid"] . '">';
			$deps = $deps . $_SESSION['u_fullname'];
			$deps = $deps . '</option>';
			
			if ($_SESSION['u_managerlevel'] != 'level1') {
				while ($row = mysql_fetch_assoc($result)) {
					$deps = $deps . '<option value="' . $row["dept"] . '">';
					$deps = $deps . $row["dept"];
					$deps = $deps . '</option>';
				}
			} else {
				while ($row = mysql_fetch_assoc($result)) {
					$deps = $deps . '<option value="' . $row["empid"] . '">';
					$deps = $deps . $row["empname"];
					$deps = $deps . '</option>';
				}
			}
		} else {
			# Name of Individual employee
			$query = "SELECT * FROM `emp` WHERE empusername='" . $_SESSION['user_name'] . "' and state='Active'";
			$result = $db -> query($query);
			$deps = "";
			$deps = $deps . '<option value="' . $_SESSION["u_empid"] . '">';
			$deps = $deps . $_SESSION['u_fullname'];
			$deps = $deps . '</option>';
		}

		$typeofday = "";
		//Department name
		$department = '<option value="none">';
		$department = $department . "None";
		$department = $department . '</option>';
		if($resultdept) {
			$department = $department . '<option value="ALL">';
                        $department = $department . "ALL";
                        $department = $department . '</option>';
			while ($row = mysql_fetch_assoc($resultdept)) {
				$department = $department . '<option value="' . $row["dept"] . '">';
				$department = $department . $row["dept"];
				$department = $department . '</option>';
			}
		}
		
		?>
				<form id="TrackAttInd" name="TrackAttInd" method="post" action="trackattendance.php?TrackAttInd=1">
					
				<div class="col-sm-5">
				<div class="panel panel-primary">
					<div class="panel-heading text-center">
						<strong style="font-size:20px;">Untracked Leave Info</strong>
					</div>
					<div class="panel-body">
							<?php
							if (($_SESSION['u_managerlevel'] != 'level1') || ($_SESSION['user_dept'] == 'HR')) {
								if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
									echo '<div class="form-group">
										<div class="row">
										<div class="col-sm-4"><label>Department:</label></div>
										<div class="col-sm-8"><select class="form-control" id="hideDept" size="0" name="UDept">
											' . $department . '
											</select>
											</div>
											</div>
											</div>';
									echo '<div class="form-group" style="display:none">
										<div class="row">
										<div class="col-sm-4"><label>Name:</label></div>
										<div class="col-sm-8">
											<select class="form-control" id="getEmpName" size="0" name="getDeptemp"></select>
											</div>
		 									</div>
											</div>';
								} else {
									echo '<div class="form-group">
										<div class="row">
										<div class="col-sm-4"><label>Department:</label></div>
										<div class="col-sm-8">
											<select class="form-control" id="hideDept" size="0" name="UDept">
												' . $deps . '
											</select>
										</div>
										</div>
										</div>';
									echo '<div class="form-group" style="display:none">
										<div class="row">
										<div class="col-sm-4"><label>Name:</label></div>
										<div class="col-sm-8">
											<select class="form-control" id="getEmpName" size="0" name="getDeptemp"></select>
											</div>
											</div>
											</div>';
								}
							} else {
								echo '<div class="form-group">
										<div class="row">
										<div class="col-sm-4"><label>Name:</label></div>
									<div class="col-sm-8"><select class="form-control" size="0" name="UGroup">
									'.$deps.'
									</select>
									</div>
									</div>
									</div>';
							}
							?>
							<div class="form-group">
							<div class="row">
							<div class="col-sm-4"><label>From:</label></div>
							<div class="col-sm-8">
							<!-- <input class="form-control" type="text" readonly="true" name="fromdate" value='<?php echo add_day(-30, 'Y-m-d'); ?>' id="fromdate" size="8" /> -->
							<div class="input-group">
									    <input type="text" id="datetimepicker" class="form-control" name="fromdate" value='<?php echo add_day(-30, 'Y-m-d'); ?>'>
									    <label class="input-group-addon btn" for="date">
									       <span class="fa fa-calendar open-datetimepicker"></span>
									    </label>
							</div>
							</div>
							</div>
							</div>
							<div class="form-group">
							<div class="row">
							<div class="col-sm-4"><label>To:</label></div>
							<div class="col-sm-8">
							<!-- <input class="form-control" size="8" readonly="true" name="todate" id="todate" value = '<?php echo date('Y-m-d') ?>' type="text" /></td> -->
							<div class="input-group">
									    <input type="text" id="datetimepicker" class="form-control" name="fromdate" value='<?php echo add_day(-30, 'Y-m-d'); ?>'>
									    <label class="input-group-addon btn" for="date">
									       <span class="fa fa-calendar open-datetimepicker"></span>
									    </label>
							</div>
                    		</div>
                    		</div>
                    		</div>
							<div class="form-group">
							<div class="row">
							<div class="col-sm-12 text-center">
							<input type="submit" class="btn btn-primary submitBtn" value="Submit" name="TrackAttInd">
							</div>
							</div>
							</div>
					
						</div>
						</div>
						</div>
					
				</form>
		<div id='loadingmessage' style='display:none'>
			<img align="middle" src='images/loading.gif'/>
		</div>
		</div>
		<div class="row">
		<div class="col-sm-2"></div>
		<div class="col-sm-9">
<?php
if (isset($_REQUEST['TrackAttInd'])) {
		// Gather information
		$fromDate = $_REQUEST['fromdate'];
		$toDate = $_REQUEST['todate'];
		if(isset($_REQUEST['UGroup'])) {
                $grp = $_REQUEST['UGroup'];
        }
        if (isset($_REQUEST['getDeptemp'])) {
                $getDeptemp = $_REQUEST['getDeptemp'];
        } else {
                $getDeptemp = "";
        }
        if (isset($_REQUEST['UDept'])) {
                $getDept = $_REQUEST['UDept'];
        } else {
                $getDept = "";
        }
        echo '<script>
                 $("#fromdate").val("' . $fromDate . '");
                 $("#todate").val("' . $toDate . '");
                 $(".ui-dialog").remove();
              </script>';
        echo "<div id='untrackedLeaveData'>";
        echo "<br><u><h4><center>Untracked Leave Information details from $fromDate to $toDate "; 

        if (isset($_REQUEST['getDeptemp'])) {
                echo getempName($_REQUEST['getDeptemp']);
        }
        if (isset($_REQUEST['UDept'])) {
                echo " (" . $_REQUEST['UDept'] . ")</center></h4></u><br>";
        } else {
		echo "</center></h4></u><br>";
	}
        
        
		# Gather employees based on employee leavel
		
		# Gather employees if employee is HR
        if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
                if ($getDept == 'ALL') {
                        $query = "SELECT distinct(dept) FROM `emp` ORDER BY `dept` ASC";
                        $self = 1;
                } elseif ($getDeptemp == 'ALL') {
                        $query = "SELECT * FROM emp WHERE dept='" . $getDept . "' and state='Active'";
                        $self = 1;
                } elseif ($_REQUEST['UDept'] == $_SESSION['u_empid']) {
                        $query = "SELECT * FROM emp WHERE `empid` = '" . $getDept . "' and state='Active' ORDER BY `emp`.`empname` ASC";
                } else {
                        $query = "SELECT * FROM `emp` WHERE `empid` = '".$_REQUEST['getDeptemp']."' and state='Active' ORDER BY `emp`.`empname` ASC";
                }
                $result = $db -> query($query);
                if ($getDept == 'ALL') {
			echo '<div id="accordion-new">';			        
                        while ($row = mysql_fetch_assoc($result)) {
                                echo "<h3>".$row['dept']."</h3><div>";
				setUntrackedTeamPercentageNull();
                                $getEmployeesQueryResult=$db ->query("select * from emp where dept='".$row['dept']."' and state='Active'");
                                trackAttendence($getEmployeesQueryResult, $fromDate,$toDate, $db);
                                getTeamUntrackedPercentage();
                                echo "</div>";
                        }
			echo "</div>";
                } else {
                        if ($getDeptemp == 'ALL') {
                       		trackAttendence($result, $fromDate,$toDate, $db);
                       		getTeamUntrackedPercentage();
                        } else {
                        	trackAttendence($result, $fromDate,$toDate, $db);
                        }
                }
    	} else if (strtoupper($_SESSION['user_desgn']) == 'MANAGER') {
		# Gather employee list if employee is a manager
                if ($getDept == 'ALL') {
                        $query = "SELECT distinct(dept) FROM emp WHERE managerid='".$_SESSION['u_empid']."' and state='Active'";
                        $self = 1;
                } elseif ($getDeptemp == 'ALL') {
                        $query = "SELECT * FROM emp WHERE dept='".$getDept."' and state='Active'";
                } elseif ($_REQUEST['UDept'] == $_SESSION['u_empid']) {
                        $query = "SELECT * FROM emp WHERE `empid` = '" . $getDept . "' and state='Active' ORDER BY `emp`.`empname` ASC";
                } else {
                        if ($_SESSION['u_managerlevel'] != 'level1') {
                                $query = "SELECT * FROM emp WHERE `empid` = '".$_REQUEST['getDeptemp']."' and state='Active' ORDER BY `emp`.`empname` ASC";
                        } else {
                                if($grp=="ALL") {
                                        $query = "SELECT * FROM emp WHERE managerid='".$_SESSION['u_empid']."' and state='Active' union SELECT * FROM emp WHERE empid='".$_SESSION['u_empid']."' and state='Active' ORDER BY `empname` ASC";
                                } else {
                                        $query = "SELECT * FROM emp WHERE `empid` = '".$grp."' and state='Active' ORDER BY `emp`.`empname` ASC";
                                }
                        }
                }
                $result = $db -> query($query);
                if ($getDept == 'ALL') {
                        echo '<div id="accordion-new">';
                        while ($row = mysql_fetch_assoc($result)) {
                                echo "<h3>".$row['dept']."</h3><div>";
                                $getEmployeesQueryResult=$db -> query("select * from emp where dept='".$row['dept']."' and state='Active'");
				setUntrackedTeamPercentageNull();	
  				trackAttendence($getEmployeesQueryResult, $fromDate,$toDate, $db);
				getTeamUntrackedPercentage();
                                echo "</div>";
                        }
                        echo "</div>";
                } elseif ($getDeptemp == 'ALL') {
					trackAttendence($result, $fromDate,$toDate, $db);
					getTeamUntrackedPercentage();
                } else {
                      	if($grp=="ALL") {
                       		trackAttendence($result, $fromDate,$toDate, $db);
                       		getTeamUntrackedPercentage();
                       	} else {
                       		trackAttendence($result, $fromDate,$toDate, $db);
                        }
                }
       } else {
       		# Here the employee is at last position in hierarchy
	   		trackAttendence($result, $fromDate,$toDate, $db);
       }
       $db -> closeConnection();
}
?>
</div>
<div class="col-sm-1"></div>
	</div>
	</div>
	</div>
	<footer class="footer1">
		<div class="container">
			<div class="row"><!-- row -->
				<div class="col-lg-4 col-md-4"><!-- widgets column left -->
					<ul class="list-unstyled clear-margins"><!-- widgets -->
						<li class="widget-container widget_nav_menu"><!-- widgets list -->
							<h1 class="title-widget">Email Us</h1>
							<p><b>Anil Kumar Thatavarthi:</b> <a href="mailto:#"></a></p>
							<p><b>Naidile Basvagde :</b> <a href="mailto:#"></a></p>
							<p><b>Sneha Kumari:</b> <a href="mailto:#"></a></p>
						</li>
					</ul>
				</div><!-- widgets column left end -->
				
				<div class="col-lg-4 col-md-4"><!-- widgets column left -->
					<ul class="list-unstyled clear-margins"><!-- widgets -->
						<li class="widget-container widget_nav_menu"><!-- widgets list -->
							<h1 class="title-widget">Contact Us</h1>
							<p><b>Helpline Numbers </b> 
								<b style="color:#ffc106;">(8AM to 10PM): </b></p>
							<p>  +91-9740464882, +91-9945732712  </p>
							<p><b>Phone Numbers : </b>7042827160, </p>
							<p> 011-2734562, 9745049768</p>
						</li>
					</ul>
				</div><!-- widgets column left end -->
						
				<div class="col-lg-4 col-md-4"><!-- widgets column left -->
					<ul class="list-unstyled clear-margins"><!-- widgets -->
						<li class="widget-container widget_nav_menu"><!-- widgets list -->
							<h1 class="title-widget">Office Address</h1>
							<p><b>Corp Office / Postal Address</b></p>
							<p>5th Floor ,Innovator Building, International Tech Park, Pattandur Agrahara Road, Whitefield, Bengaluru, Karnataka 560066</p>
						</li>
					</ul>
				</div><!-- widgets column left end -->
			</div>
		</div>
		</footer>
		<!--header-->

		<div class="footer-bottom">

			<div class="container">

				<div class="row">

					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

						<div class="copyright">

							© 2017, All rights reserved

						</div>

					</div>

					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

						<div class="design">

							 <a href="#"><b>ECI TELECOM</b> </a> |  <a href="#">LMS by ECI</a>

						</div>

					</div>

				</div>

			</div>

		</div>
			
			
			<!--footer end-->
	</body>
	</head>
</html>




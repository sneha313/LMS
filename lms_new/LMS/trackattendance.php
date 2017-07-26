<?php
session_start();
require_once 'Library.php';
require_once 'attendenceFunctions.php';
error_reporting("E_ALL");
?>
<html>
	<head>
		<?php
		$getCalIds = array("fromdate", "todate", "TypeOfDayfromdate", "TypeOfDaytodate");
		$calImg = getCalImg($getCalIds);
		echo $calImg;
		?>
		<SCRIPT TYPE="text/javascript">
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
					
					$(val).html("<table id='table-2'><tr><td width='30%'><b>"+$(val).text()+"</b></td><td width='70%'><table id='table-2'><tr><td>Total Untracked Leaves: "+
					firstString+"</td></tr></table>");
			});
			 
		</SCRIPT>
		<style>
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
		<table id='table-2'>
			<tr>
				<td>
				<form id="TrackAttInd" name="TrackAttInd" method="post" action="trackattendance.php?TrackAttInd=1">
					<fieldset>
						<legend>
							Untracked Leave Information
						</legend>
						<table id='table-2'>
							<?php
							if (($_SESSION['u_managerlevel'] != 'level1') || ($_SESSION['user_dept'] == 'HR')) {
								if (($_SESSION['user_dept'] == 'HR') || ($_SESSION['u_empid'] == "420064")) {
									echo '<tr>
											<td>Department:</td>
											<td><select id="hideDept" size="0" name="UDept">
											' . $department . '
											</select>
											</td>
											</tr>';
									echo '<tr id="hideName" style="display:none">
											<td>Name:</td>
											<td>
											<select id="getEmpName" size="0" name="getDeptemp"></select>
											</td>
											</tr>';
								} else {
									echo '<tr>
											<td>Department:</td>
											<td><select id="hideDept" size="0" name="UDept">
											' . $deps . '
											</select>
											</td>
											</tr>';
									echo '<tr id="hideName" style="display:none">
											<td>Name:</td>
											<td>
											<select id="getEmpName" size="0" name="getDeptemp"></select>
											</td>
											</tr>';
								}
							} else {
								echo '<tr>
									<td id="hideEmpName">Name:</td>
									<td><select size="0" name="UGroup">
									'.$deps.'
									</select>
									</td>
									</tr>';
							}
							?>
							<tr>
							<td>From:</td>
							<td><input type="text" readonly="true" name="fromdate" value='<?php echo add_day(-30, 'Y-m-d'); ?>' id="fromdate" size="8" /></td>
							</tr>
							<tr>
							<td>To:</td>
							<td><input size="8" readonly="true" name="todate" id="todate" value = '<?php echo date('Y-m-d') ?>' type="text" /></td>
							</tr>
							<tr>
							<td rowspan="3" align="center">
							<input type="submit" class="submitBtn" value="Submit" name="TrackAttInd">
							</td>
							</tr>
						</table>
					</fieldset>
				</form></td>
			</tr>
		</table>
		<div id='loadingmessage' style='display:none'>
			<img align="middle" src='images/loading.gif'/>
		</div>
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
        echo "<br><u><h2><center>Untracked Leave Information details from $fromDate to $toDate "; 

        if (isset($_REQUEST['getDeptemp'])) {
                echo getempName($_REQUEST['getDeptemp']);
        }
        if (isset($_REQUEST['UDept'])) {
                echo " (" . $_REQUEST['UDept'] . ")</center></h2></u><br>";
        } else {
		echo "</center></h2></u><br>";
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
	</body>
	</head>
</html>




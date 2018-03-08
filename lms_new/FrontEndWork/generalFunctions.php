<?php
if (!isset($_SESSION)) {
	session_start();
}
require_once 'Library.php';
function getApplyLeaveJs($id, $fromDateId, $toDateId, $section, $empId, $page,$employeeId="") {
	if ($page == "applyteammemberleave.php" || $page == "hrapplyleaveforall.php") {
		echo "$('#team_leave_type').change(function() {
					$.post('getSplLeaveOptions.php?empid=".$employeeId."',function(data)
					{
						$('#team_special_leave').empty();
						$('#team_special_leave').append(data);
					});
			});";
	}
	echo '$("#' . $id . '").submit(function() {';
	echo 'a=$("#'.$fromDateId.'").val().split("-");
			fromDate=new Date(a[1]+"/"+a[2]+"/"+a[0]);
			b=$("#'.$toDateId.'").val().split("-");
			toDate=new Date(b[1]+"/"+b[2]+"/"+b[0]);
			fromDateValue=$("#'.$fromDateId.'").val();
			toDateValue=$("#'.$toDateId.'").val();
			var todaydate=new Date();todaydate.setDate(todaydate.getDate() - 365);
			function countWeekendDays( d0, d1 )
			{
				var start = new Date(d0),
				finish = new Date(d1),
				dayMilliseconds = 1000 * 60 * 60 * 24;
				var weekendDays = 0;
				while (start <= finish) {
					var day = start.getDay()
					if (day == 0 || day == 6) {
						weekendDays++;
					}
					start = new Date(+start + dayMilliseconds);
				}
				return weekendDays;
			}
        		
			if(isNaN(fromDate)|| isNaN(toDate))
			{
				if(isNaN(fromDate)) {
					BootstrapDialog.alert("Select From Date");
					$("#' . $fromDateId . '").focus();
					return false;
				}
				if(isNaN(toDate)) {
					BootstrapDialog.alert("Select To Date");
					$("#' . $toDateId . '").focus();
					return false;
				}
			}
						
			if(fromDate>toDate)
			{
				BootstrapDialog.alert("Starting Date must be older date than ending date..... :)");
				$("#' . $fromDateId . '").focus();
				return false;
			}

		if($("#reason").val()=="") {
			BootstrapDialog.alert("Reason for applying leave is compulsory. Please enter the reason");
			$("#reason").focus();
			return false;	
		}';
		
		if ($page != "hrapplyleaveforall.php" && $page != "applyteammemberleave.php") {
			echo 'if (fromDate < todaydate) {
						BootstrapDialog.alert( $("#' . $fromDateId . '").val() +" is before than Today\'s Date. Leave Can\'t be applied for before week");
						$("#' . $fromDateId . '").focus();
						return false;
            	  }';
		}
		
		if ((getTotalLeaves($employeeId)) < -60) {
			if ($page == "applyteammemberleave.php" || $page == "hrapplyleaveforall.php") {
				$divId="team_special_leave";
			} else {
				$divId="special_leave";
			}
			echo 'if($("#'.$divId.'").is(":visible")) {
						splVal=$("#'.$divId.' option:selected" ).text();
						numOfDays=splVal.split(":")[1].trim().split(" ")[0];
						numOfDays=parseInt(numOfDays);
						var timeDiff = Math.abs(toDate.getTime() - fromDate.getTime());
						var diffDays= Math.ceil(timeDiff / (1000 * 3600 * 24))+1;
						diffDays=parseInt(diffDays);
						noOfweekends=countWeekendDays(fromDate,toDate);
						diffDays=diffDays-noOfweekends;
						jQuery.ajaxSetup({async:false});
						$.get("getQueryResult.php?empid='.$employeeId.'&getNoHolidays=1&fromDate="+fromDateValue+"&toDate="+toDateValue+"", function(countHolidays) {
							diffDays=diffDays-parseInt(countHolidays);
						});
						jQuery.ajaxSetup({async:true});
						if(diffDays > numOfDays) {
							BootstrapDialog.alert("You cant apply leave more than "+numOfDays+"");
							return false;  
						}';
			
						 echo 'else {
						    	$.ajax({ 
						        data: $(this).serialize(), 
						        type: $(this).attr(\'method\'), 
						        url: $(this).attr(\'action\'), 
						        success: function(response) { 
						            $("#'.$section.'").html(response); 
						        }
								});
						}
				  }';
		}
			
		echo 'else { ';
		
					echo '$.ajax({ 
						data: $(this).serialize(), 
						type: $(this).attr(\'method\'), 
						url: $(this).attr(\'action\'), 
						success: function(response) { 
							$("#' . $section . '").html(response); 
						}
					});
				}
				return false;
			});';
}
function getDisplayDatesJs($section) {
	echo '$(".applyOptionalLeave").click(function() {
    if($(this).val()=="YES"){
        var a=$(this).parent().next().text();
		var c=a.split(",");
        $(this).parent().prev().text(c[0]);
    } else {
		var nextValue=$(this).parent().next().text();
		var dayValue=nextValue.split(",");
		var newDayName="Day"+dayValue[1];
		if(unescape($("#spl").val()).replace(/\+/g, \' \').replace(/\//g, \'\')=="") {
        	$(this).parent().prev().html("<select class=\"optionSelection\" name="+newDayName+">"+
											 "<option value=\"1\">FullDay</option>"+
											 "<option value=\"2\">HalfDay</option>"+
											 "<option value=\"3\">WFH</option>"+
											 "<option value=\"4\">First Half-HalfDay &amp; second Half-WFH</option>"+
											 "<option value=\"5\">First Half-WFH &amp; Second Half-HalfDay</option>"+
											 "<option value=\"7\">OnSite</option>"+
									"</select>");
		} else {
			var splLeave=unescape($("#spl").val()).replace(/\+/g, \' \').replace(/\//g, \'\');
			$(this).parent().prev().html("<select class=\"optionSelection\" name="+newDayName+">"+
											 "<option value=\"1\">FullDay</option>"+
											 "<option value=\"2\">HalfDay</option>"+
											 "<option value=\"3\">WFH</option>"+
											 "<option value=\"4\">First Half-HalfDay &amp; second Half-WFH</option>"+
											 "<option value=\"5\">First Half-WFH &amp; Second Half-HalfDay</option>"+
											 "<option value=\"6\">"+splLeave+"</option>"+
									"</select>");
		}
    }
	});';
	echo '$("#displayDates").submit(function() {
		
		var optionalCount=$("#optionalleaveempcount").val();
		res=optionalCount.split("_");
		var optionalLeaveRealCount=0;
		if($("#optionalleaveempid").val()==res[0]) {
				optionalLeaveRealCount=res[1];
		}
		var optcount=0;
		$.each($(".applyOptionalLeave"), function( index, value ) {
			if($(".applyOptionalLeave")[index].value=="YES") {
				optcount=optcount+1;
			}
		});
		var totaloptionalleaves=parseInt(optionalLeaveRealCount)+parseInt(optcount);
		if(totaloptionalleaves> 2) {
			BootstrapDialog.alert("You have applied 2 optional leaves already/ Selected more than two optional holidays in the present transaction. Please Change leaves accordingly");
			return false;
		}
			
		var specialLeaveName=$("#spl").val();
		specialLeaveName=unescape(specialLeaveName).replace(/\+/g, \' \').replace(/\//g, \'\');
		var count=0;
		var permittedleaves = $("#permittedleaves").val();
		$(".optionSelection :selected").each(function() {
				if($(this).text()==specialLeaveName) {
					count=count+1;
				}
			});
			
		//Check for optional leaves taken
			
		//Check Whether special leave applied is more than permitted leaves.
		if(count>permittedleaves && permittedleaves!=0)
		{
			BootstrapDialog.alert(specialLeaveName+" can only be applied for "+permittedleaves+" days. Please modify days accordingly");
			return false;
		}
		else
		{
			$.ajax({ 
		        data: $(this).serialize(), 
		        type: $(this).attr(\'method\'), 
		        url: $(this).attr(\'action\'), 
		        success: function(response) { 
		            $("#' . $section . '").html(response); 
		        }
				});
		}
		return false;
		});';
}

function getSubmitJs($id, $section) {
	echo '$("#' . $id . '").submit(function() {
			 $(this).find(":input[type=submit]").replaceWith("<center><img src=\'public/img/loader.gif\' class=\'img-responsive\' alt=\'processing\'/></center>");
		    $.ajax({
		        data: $(this).serialize(),
		        type: $(this).attr(\'method\'), 
		        url: $(this).attr(\'action\'), 
		        success: function(response) { 
		            $("#' . $section . '").html(response);
		        }
			});
			return false; 
		});';
}

function getSetOptionsJs($section, $formName, $functionName) {
	echo "function $functionName(opt) {
		if(opt == \"SL\"){
			$('#" . $section . "').show();
			document.$formName.specialLeaveType.disabled = false;
		}else{
			$('#" . $section . "').hide();
			document.$formName.specialLeaveType.disabled = true;
		} 
	}";
}

function getEmpForm($page,$empid,$role) {
		if(strtolower($role)=="manager") {$divid="loadmanagersection";echo "<script>var divid=\"loadmanagersection\";</script>"; }
		if(strtolower($role)=="hr") { $divid="loadhrsection";echo "<script>var divid=\"loadhrsection\";</script>";}
		echo "<script>
				$('#getemptrans').submit(function() {
				if($('#empuser').val()==\"\")
				{
					BootstrapDialog.alert('Please Enter Employee Name');
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
	    });</script>";
		echo '<form action="'.$page.'?leaveform=1" method="POST" id="getemptrans">
				<div class="col-sm-1"></div>
					<div class="col-sm-3">
						<label style="font-size:16px;">Enter Employee Name:</label>
					</div>
				   	<div class="col-sm-4">
						<input id="empuser" type="text" class="form-control" name="empuser"/>
					</div>
					<div class="col-sm-3">
						<input class="btn btn-primary submit" type="submit" name="submit" value="SUBMIT"/>
					</div>
					<div class="col-sm-1"></div>
			</form>';
}

function getLeaveForm($page, $formName, $formId, $leaveTypeId, $hideSplLeaveId, $specialLeaveId, $fromDateId, $toDateId, $functionName) {
	global $db;
	$options = "";
	if(isset($_REQUEST['empuser'])) {
		$empId=getValueFromQuery("select empid from emp where empname='".$_REQUEST['empuser']."' and state='Active'","empid");
		if ((getTotalLeaves($empId)) < -60) {
                        echo "<font color='red'>".$_REQUEST['empuser']." can't apply regular leave as his/her total leaves are exhaushted. You can apply WFH, special leaves on behalf of him.</font>";
        	}
	} else {
		$empId=$_SESSION['u_empid'];
	}

	echo "<form name='$formName' method='POST' accept-charset='UTF8' action='$page?getdates=1' id='$formId' method='POST'>
	<div class='panel panel-primary'>
			<div class='panel-heading text-center'>
				<strong style='font-size:20px;'>Apply Leave</strong>
			</div>
			<div class='panel-body'>";
	if ($page == "applyteammemberleave.php" || $page == "hrapplyleaveforall.php") {
		echo "<div class='form-group'>
						<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3 empName'><label for='empid'>Employee Name:</label></div>
		         		<div class='col-sm-5 empName'><input type='text' class='form-control' id='empName' readonly name='empName' value= '".$_REQUEST['empuser']."' /></div>
		        	 	<div class='col-sm-2'></div>
		         	</div></div>";
		echo "<div class='form-group'>
						<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3 empid'><label for='empid'>Employee Id:</label></div>
			         	<div class='col-sm-5 empid'><input type='text' class='form-control' id='employeeid' readonly name='employeeid' value= ".$empId." /></div>
			        	<div class='col-sm-2'></div>
			         </div></div>";
	} else {
			echo "<div class='form-group'>
						<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3 empid'>
							<label for='empid'>Employee Id:</label>
						</div>
			         	<div class='col-sm-5 empid'>
							<input type='text' id='empid' class='form-control' readonly name='empid' value= " . $_SESSION['u_empid'] . " />
						</div>
						<div class='col-sm-2'></div>
									</div>
									</div>";
	}

	if ($page == "applyOnSite.php") {
		echo "<div class='form-group'>
						<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='Leave_type'>Leave Type:</label>
						</div>
						<div class='col-sm-5'>
							<input type='text' class='form-control' id='$leaveTypeId' readonly name='leavetype' value='On Site'/>
						</div>
						<div class='col-sm-2'></div>
		</div>
		</div>";
	} else {
		echo "<div class='form-group'>
						<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='Leave_type'>Leave Type:</label>
						</div>
						<div class='col-sm-5'>
							<select class='form-control' id='$leaveTypeId'  name='leavetype' onchange='$functionName(document.$formName.leavetype.options[document.$formName.leavetype.selectedIndex].value);'>";
							if ((getTotalLeaves($empId)) < -5) {
								echo "<option value='SL'>Special Leave</option>";
							} else {
									echo "<option value='RL'>Regular Leave</option>
										<option value='SL'>Special Leave</option>";
							}
							echo "</select></div>
							<div class='col-sm-2'></div>
						</div>
					</div>";
	}
	echo "<div class='form-group'>
						<div class='row' id='$hideSplLeaveId' style='display:none'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
	        				<label for='Special_leave'>Select Special Leave:</label>
	        			</div>
						<div class='col-sm-5'>
							<SELECT class='form-control' id= '$specialLeaveId' name='specialLeaveType' disabled='disabled'>
								$options
							</SELECT>
						</div>
						<div class='col-sm-2'></div>
	    </div>
	</div>";
	echo "<div class='form-group'>
						<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='fromDate'>From Date:</label>
						</div>
						<div class='col-sm-5'>
							<div class='input-group'>
								<input type='text' name='$fromDateId' id='$fromDateId' size='20' class='required open-datetimepicker form-control' readonly='true'/>
	   							<label class='input-group-addon btn' for='date'>
									<span class='fa fa-calendar'></span>
								</label>
							</div>
						</div>
	   					<div class='col-sm-2'></div>
	   				</div>
	   			</div>";
	echo "<div class='form-group'>
						<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='toDate'>To Date</label>
						</div>
						<div class='col-sm-5'>
							<div class='input-group'>
								<input type='text' name='$toDateId' id='$toDateId' size='20' class='form-control open-datetimepicker1 required' readonly='true'/>
								<label class='input-group-addon btn' for='date'>
									<span class='fa fa-calendar'></span>
								</label>
							</div>
						</div>
						<div class='col-sm-2'></div>
	   				</div>
	   			</div>";
	echo "<div class='form-group'>
						<div class='row'>
						<div class='col-sm-2'></div>
						<div class='col-sm-3'>
							<label for='reason'>Reason:</label>
						</div>
						<div class='col-sm-5'>
							<textarea id='reason' class='form-control required' name='reason'></textarea>
						</div>
						<div class='col-sm-2'></div>
					</div>
				</div>";
	echo "<div class='form-group'>
						<div class='row'>
						<div class='col-sm-12 text-center'>
	       					<input class='btn btn-info reset' type='reset' name='reset' value='Reset' />
	       					<input class='btn btn-success submit' id='next' type='submit' name='submit' value='Next' />
	      </div>";
	echo "</div></div>
		</div></div></form>";
}

function getDatesSection($fromDateId, $toDateId, $empId, $emp, $page) {
	global $db;
	$splLeaveDays = 0;
	if (isset($_REQUEST[$fromDateId]) && isset($_REQUEST[$toDateId])) {
		$_SESSION['teammem'] = $emp;
		if (($_POST['leavetype'] == "RL") || ($_POST['leavetype'] == "On Site")) {
			$numOfDays = RegleavesCal($_POST[$fromDateId], $_POST[$toDateId], $emp);
			displayDates($numOfDays, $splLeaveDays, " ", $_REQUEST[$fromDateId], $_REQUEST[$toDateId], $_REQUEST['reason'], "", "$page","$empId");
		} elseif ($_POST['leavetype'] == "SL") {
			if (isset($_POST['specialLeaveType'])) {
				$splLeaveDays = getSpecialLeavesForType($_POST['specialLeaveType'], "dayspermitted");
			} else {
				$splLeaveDays = "";
				$_POST['specialLeaveType'] = "";
			}
			$numOfDays = RegleavesCal($_POST[$fromDateId], $_POST[$toDateId], $emp);
			displayDates($numOfDays, $splLeaveDays, $_POST['specialLeaveType'], $_REQUEST[$fromDateId], $_REQUEST[$toDateId], $_REQUEST['reason'], $_POST['specialLeaveType'], "$page","$empId");
		} 
	}

}

function getShiftSection() {
	$splLeaves = 0;
	$totalLeaves = 0;
	$leaveTypeSelected = "";
	$mailBody = '<form name="getShiftDays" id="getShift" method="POST" action="' . $_SERVER['PHP_SELF'] . '?confirmleave=1">';
	$mailBody .= "<div class='panel panel-primary'>
						<div class='panel-heading text-center'>
							<strong style='font-size:20px;'>Shift Types</strong>
						</div>
						<div class='panel-body'>";
							$Day = explode(":", urldecode($_POST['days']));
							for ($i = 0; $i < $_POST['noOfdays']; $i++) {
								if (!empty($_POST['Day' . $i])) {
									$leaveTypeSelected = "";
									if ($_POST['Day' . $i] == "FullDay") {
										$totalLeaves++;
										$leaveTypeSelected = "FullDay";
										$mailBody = $mailBody . "<div class='form-group' style=\"display:none\">
																		<div class='row'>
																		<div class='col-sm-2'></div>
																		<div class='col-sm-8'>
																			<input type = hidden name ='Day" . $i . "Date' value =" . $Day[$i] . " class='form-control'>
																		</div>
																		<div class='col-sm-2'></div>
																	</div></div>";
										} else if ($_POST['Day' . $i] == "HalfDay") {
										$totalLeaves = $totalLeaves + 0.5;
										$leaveTypeSelected = "HalfDay";
										$mailBody = $mailBody . "<div class='form-group' style=\"display:none\">
																		<div class='row'>
																		<div class='col-sm-2'></div>
																		<div class='col-sm-8'>
																			<input type = hidden name ='Day" . $i . "Date' value =" . $Day[$i] . " class='form-control'>
																		</div>
																		<div class='col-sm-2'></div>
																	</div></div>";
									} else if ($_POST['Day' . $i] == "WFH") {
										$leaveTypeSelected = "WFH";
										$mailBody = $mailBody . "<div class='form-group' style=\"display:none\">
																		<div class='row'>
																		<div class='col-sm-2'></div>
																		<div class='col-sm-8'>
																			<input type = hidden name ='Day" . $i . "Date' value =" . $Day[$i] . " class='form-control'>
																		</div>
																		<div class='col-sm-2'></div>
																	</div></div>";
									} else if ($_POST['Day' . $i] == "First Half-HalfDay & second Half-WFH") {
										$totalLeaves = $totalLeaves + 0.5;
										$leaveTypeSelected = "First Half-HalfDay & second Half-WFH";
										$mailBody = $mailBody . "<div class='form-group' style=\"display:none\">
																		<div class='row'>
																		<div class='col-sm-2'></div>
																		<div class='col-sm-8'>
																			<input type = hidden name ='Day" . $i . "Date' value =" . $Day[$i] . " class='form-control'>
																		</div>
																		<div class='col-sm-2'></div>
																	</div></div>";
									} else if ($_POST['Day' . $i] == "First Half-WFH & Second Half-HalfDay") {
										$totalLeaves = $totalLeaves + 0.5;
										$leaveTypeSelected = "First Half-WFH & Second Half-HalfDay";
										$mailBody = $mailBody . "<div class='form-group' style=\"display:none\">
																		<div class='row'>
																		<div class='col-sm-2'></div>
																		<div class='col-sm-8'>
																			<input type = hidden name ='Day" . $i . "Date' value =" . $Day[$i] . " class='form-control'>
																		</div>
																		<div class='col-sm-2'></div>
																	</div></div>";
									} else if ($_POST['Day' . $i] == urldecode($_POST['splType'])) {
										$splLeaves++;
										$leaveTypeSelected = urldecode($_POST['splType']);
										$mailBody = $mailBody . "<div class='form-group' style=\"display:none\">
																		<div class='row'>
																		<div class='col-sm-2'></div>
																		<div class='col-sm-8'>
																			<input type = hidden name ='Day" . $i . "Date' value =" . $Day[$i] . " class='form-control'>
																		</div>
																		<div class='col-sm-2'></div>
																	</div></div>";
									} else if ($_POST['Day' . $i] == "OnSite") {
										$totalLeaves = $totalLeaves + 0;
										$leaveTypeSelected = "OnSite";
										$mailBody = $mailBody . "<div class='form-group' style=\"display:none\">
																		<div class='row'>
																		<div class='col-sm-2'></div>
																		<div class='col-sm-8'>
																			<input type = hidden name ='Day" . $i . "Date' value =" . $Day[$i] . " class='form-control'>
																		</div>
																		<div class='col-sm-2'></div>
																	</div></div>";
									}
						
									if (preg_match('/optional/i', $Day[$i],$match)) {
										if(preg_match('/(.*)(\[.*)/', $Day[$i],$splMatch)) {
											$Day[$i]= $splMatch[1];
										}
									}
									if ($leaveTypeSelected == "HalfDay") {
										$mailBody = $mailBody ."<div class='form-group'>
																		<div class='row'>
																			<div class='col-sm-2'></div>
																			<div class='col-sm-2'>" . $Day[$i] . "</div>
																			<div class='col-sm-3'>
																				<input type='text' class='form-control' name='Day$i' size='50' value=\"" . $leaveTypeSelected . "\" readonly='true'>
																			</div>
																	 		<div class='col-sm-5'>
																				<input type='radio' name='Day" . $i . "halfDayChoice' value='firstHalf' checked>First Half
																	 			<input type='radio' name='Day" . $i . "halfDayChoice' value='secondHalf'>Second Half
																	 		</div>
																	 		<div class='col-sm-2'></div>
																 		</div>
																	</div>";
									} elseif ($leaveTypeSelected == "WFH") {
										$mailBody = $mailBody . "<div class='form-group'>
																		<div class='row'>
																			<div class='col-sm-2'></div>
																			<div class='col-sm-2'>" . $Day[$i] . "</div>
																			<div class='col-sm-3'>
																				<input type='text' class='form-control' name='Day" . $i . "' size='50' value=\"" . $leaveTypeSelected . "\" readonly='true'>
																			</div>
																			<div class='col-sm-5'>
																				<input type='radio' name='Day" . $i . "WFHChoice' value='fullDay' checked>Full Day
																		 		<input type='radio' name='Day" . $i . "WFHChoice' value='firstHalf'>First Half
																		 		<input type='radio' name='Day" . $i . "WFHChoice' value='secondHalf'>Second Half
																	 		</div>
																		 	<div class='col-sm-2'></div>
																	 	</div>
																	</div>";
									} else {
										$mailBody = $mailBody . "<div class='form-group'>
																		<div class='row'>
																			<div class='col-sm-2'></div>
																			<div class='col-sm-2'>" . $Day[$i] . "</div>
																			<div class='col-sm-6'>
																				<input type='text' class='form-control' name='Day" . $i . "' size='50' value=\"" . $leaveTypeSelected . "\" readonly='true'>
																			</div>
																			<div class='col-sm-2'></div>
																		</div>
																	</div>";
									}
								} else {
									if(preg_match('/(.*)(\[.*)/', $Day[$i],$splMatch)) {
												$mailBody=$mailBody."<div class='form-group'>
																			<div class='row'>
																				<div class='col-sm-2'></div>
																				<div class='col-sm-2'>$splMatch[1]  </div>";
																				$mailBody=$mailBody."<div class='col-sm-6'>
																						<input type='text' class='form-control' readonly='true' name='Day" . $i . "Date' size='50' value=\"" . str_replace('[special]','',$splMatch[2]) . "\">
																				</div>
																				<div class='col-sm-2'></div>
																			</div>
																		</div>";
			}
		}
	}
	$mailBody = $mailBody . "<div class='form-group'><div class='row'><div class='col-sm-2'></div><div class='col-sm-3'><label>Total Number Of Leaves :</label></div><div class='col-sm-5'> " . $totalLeaves . "</div><div class='col-sm-2'></div></div></div>";
							$mailBody = $mailBody . "<div class='form-group' style\"display:none\"><div class='row'><div class='col-sm-2'></div><div class='col-sm-4'><input class='form-control' type = hidden name ='totalDays' value =" . $_POST['noOfdays'] . "></div></div></div> ";
							$mailBody = $mailBody . "<div class='form-group' style\"display:none\"><div class='row'><div class='col-sm-2'></div><div class='col-sm-4'><input class='form-control' type = hidden name = 'reason' value =  " . urlencode(urldecode($_POST['reason'])) . "></div></div></div> ";
							$mailBody = $mailBody . "<div class='form-group' style\"display:none\"><div class='row'><div class='col-sm-2'></div><div class='col-sm-4'><input class='form-control' type = hidden name = fromDate value =  " . $_POST['fromDate'] . "></div></div></div> ";
							$mailBody = $mailBody . "<div class='form-group' style\"display:none\"><div class='row'><div class='col-sm-2'></div><div class='col-sm-4'><input class='form-control' type = hidden name = toDate value =  " . $_POST['toDate'] . "></div></div></div> ";
							$mailBody = $mailBody . "<div class='form-group' style\"display:none\"><div class='row'><div class='col-sm-2'></div><div class='col-sm-4'><input class='form-control' type = hidden name = splType value =  " . urlencode(urldecode($_POST['splType'])) . "></div></div></div>";
							$mailBody .= '<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><input class="btn btn-primary" type="submit" name="submit" value="Apply and Send Mail" /></div></div></div></div></div>';
							$mailBody=$mailBody.'</div></div>';
							echo $mailBody;
}

function getConfirmLeaveSection($page, $empid, $fromDateId, $toDateId) {
	global $db;
	$splLeaves = 0;
	$totalLeaves = 0;
	$leaveTypeSelected = "";
	$executeQueries = array();
	$mailBody = "<div class='panel panel-primary'>
				<div class='panel-heading text-center'>
				<strong style='font-size:20px;'>Confirm Leave Section</strong>
				</div>
				<div class='panel-body'>
				<table class='table table-bordered table-striped'>";
		
		# set the status messages based on the action taken
	if ($page == "applyteammemberleave.php" || $page == "hrapplyleaveforall.php") {
		$mailBody = $mailBody . "<strong>You have applied leave on behalf of " . $empid . " for the following Dates</strong>";
		$approvalStatus = "Approved";
		$approvalSplLeaveStatus = "A";
		$approvalMail = "ApproveLeave";
		if ($page == "applyteammemberleave.php") {$approvalcomments = "Approved By Manager (".$_SESSION['u_fullname'].")";
		}
		if ($page == "hrapplyleaveforall.php") {$approvalcomments = "Approved By HR (".$_SESSION['u_fullname'].")";
		}
	}
	if ($page == "applyleave.php" || $page == "applyOnSite.php") {
		if($page == "applyleave.php") {
			$leaveName = "leave";
		} 
		if($page == "applyOnSite.php") {
			$leaveName = "On Site";
		}
		$mailBody = $mailBody . "<caption><center><strong>" . $_SESSION['u_fullname'] . " has Applied leave for the following Dates</strong></center></caption>";
		$approvalStatus = "Pending";
		$approvalSplLeaveStatus = "P";
		$approvalMail = "PendingLeave";
		$approvalcomments = "";
	}

	$transaction_id = generate_transaction_id();
	for ($i = 0; $i < $_POST['totalDays']; $i++) {
		if (!empty($_POST['Day' . $i])) {
			$leaveTypeSelected = "";
			$perdayquery = "Insert into `perdaytransactions` (`transactionid` ,`empid` ,`date` ,`leavetype`,`shift`,`status`,`count`)
							  values('" . $transaction_id . "','" . $empid . "',";
			
			# Check whetheer the day selected is an optional holiday
			if (preg_match('/optional/i', $_POST['Day' . $i.'Date'],$match)) {
				if(preg_match('/(.*)(\[.*)/', $_POST['Day' . $i.'Date'],$splMatch)) {
					$_POST['Day' . $i.'Date']=$splMatch[1];
				}
			}
			
			# Prepare the perdaytransactions table quiries -- START
			if ($_POST['Day' . $i] == "FullDay") {
				$totalLeaves++;
				$perDayTransactionCount=1;
				$leaveTypeSelected = "FullDay";
				$dateIndex = "Day" . $i . "Date";
				$perdayquery = $perdayquery . "'" . $_POST[$dateIndex] . "','" . $leaveTypeSelected . "','','".$approvalStatus."','".$perDayTransactionCount."')";
			} elseif ($_POST['Day' . $i] == "HalfDay") {
				$totalLeaves = $totalLeaves + 0.5;
				$perDayTransactionCount=0.5;
				$leaveTypeSelected = "HalfDay";
				$index = "Day" . $i . "halfDayChoice";
				$dateIndex = "Day" . $i . "Date";
				$perdayquery = $perdayquery . "'" . $_POST[$dateIndex] . "','" . $leaveTypeSelected . "','" . $_POST[$index] . "','".$approvalStatus."','".$perDayTransactionCount."')";
			} elseif ($_POST['Day' . $i] == "WFH") {
				$leaveTypeSelected = "WFH";
				$perDayTransactionCount=0;
				$index = "Day" . $i . "WFHChoice";
				$dateIndex = "Day" . $i . "Date";
				$WFH = 1;
				$perdayquery = $perdayquery . "'" . $_POST[$dateIndex] . "','" . $leaveTypeSelected . "','" . $_POST[$index] . "','".$approvalStatus."','".$perDayTransactionCount."')";
			} elseif ($_POST['Day' . $i] == "First Half-HalfDay & second Half-WFH") {
				$totalLeaves = $totalLeaves + 0.5;
				$perDayTransactionCount=0.5;
				$leaveTypeSelected = "First Half-HalfDay & second Half-WFH";
				$dateIndex = "Day" . $i . "Date";
				$perdayquery = $perdayquery . "'" . $_POST[$dateIndex] . "','" . $leaveTypeSelected . "','firstHalf','".$approvalStatus."','".$perDayTransactionCount."')";
			} elseif ($_POST['Day' . $i] == "First Half-WFH & Second Half-HalfDay") {
				$totalLeaves = $totalLeaves + 0.5;
				$perDayTransactionCount=0.5;
				$leaveTypeSelected = "First Half-WFH & Second Half-HalfDay";
				$dateIndex = "Day" . $i . "Date";
				$perdayquery = $perdayquery . "'" . $_POST[$dateIndex] . "','" . $leaveTypeSelected . "','secondHalf','".$approvalStatus."','".$perDayTransactionCount."')";
			} elseif (!empty($_POST['splType']) && ((urldecode($_POST['splType']) == $_POST['Day' . $i]))) {
				$splLeaves++;
				$perDayTransactionCount=0;
				$leaveTypeSelected = urldecode($_POST['splType']);
				$dateIndex = "Day" . $i . "Date";
				$perdayquery = $perdayquery . "'" . $_POST[$dateIndex] . "','" . $leaveTypeSelected . "','','".$approvalStatus."','".$perDayTransactionCount."')";
			} if ($_POST['Day' . $i] == "OnSite") {
				$totalLeaves = $totalLeaves+0;;
				$perDayTransactionCount=0;
				$leaveTypeSelected = "OnSite";
				$dateIndex = "Day" . $i . "Date";
				$OnSite = 1;
				$perdayquery = $perdayquery . "'" . $_POST[$dateIndex] . "','" . $leaveTypeSelected . "','','".$approvalStatus."','".$perDayTransactionCount."')";
			}
			# Prepare the perdaytransactions table quiries -- END
			
			if ($leaveTypeSelected == "HalfDay" || $leaveTypeSelected == "WFH") {
				$mailBody = $mailBody . "<tr><td>" . $_POST[$dateIndex] . "</td><td>" . $leaveTypeSelected . " (" . $_POST[$index] . ")</td></tr>";
				} else {
					$mailBody = $mailBody . "<tr><td>" . $_POST[$dateIndex] . "</td><td>" . $leaveTypeSelected . "</td></tr>";
				}
			array_push($executeQueries, $perdayquery);
		} else {
			
			# If the employee selected optional leave, then update the database accordingly -- START
			$dateIndex = "Day" . $i . "Date";
			//$mailBody = $mailBody . "<tr><td></td><td align='center'>" . $_POST[$dateIndex] . "</td><td></td></tr>";
			$mailBody = $mailBody . "<tr><td colspan='2' align='center'>" . $_POST[$dateIndex] . "</td></tr>";
			if (preg_match('/optional/i', $_POST['Day' . $i.'Date'],$match)) {
				if(preg_match('/(.*)(\(Optional\))/', $_POST['Day' . $i.'Date'],$splMatch)) {
					$currentYear=date("Y");
					$getOptionalLeaveCount="select * from empoptionalleavetaken where empid='".$empid."' and date between '".$currentYear."-01-01' and '".$currentYear."-12-31'";
					$getOptionalLeaveResult=$db -> query($getOptionalLeaveCount);
					$optionalLeaveCount=$db->countRows($getOptionalLeaveResult);
					$optionalLeavenames=array();
					while($optionalleaverow1=$db->fetchAssoc($getOptionalLeaveResult)) {
						array_push($optionalLeavenames, $optionalleaverow1['leave']);
					}
					if(!in_array($splMatch[1],$optionalLeavenames)) {
						$optionalLeaveDate=getValueFromQuery("SELECT * FROM ".$_SESSION['u_holidayListTable']." WHERE `holidayname` LIKE '".mysql_real_escape_string($splMatch[1])."'  order by date desc","date");
						$insertOptionalLeave="insert into empoptionalleavetaken (`empid`,`date`,`leave`,`state`) values('".$empid."','".$optionalLeaveDate."','".mysql_real_escape_string($splMatch[1])."','".$approvalStatus."')";
						$optionalLeaveName="$splMatch[1] (Optional)";
						$perDayTransactionCount=0;
						
						$perdayOptionalquery = "Insert into `perdaytransactions` (`transactionid` ,`empid` ,`date` ,`leavetype`,`shift`,`status`,`count`) 
								values('" . $transaction_id . "','" . $empid . "','".$optionalLeaveDate."','".mysql_real_escape_string($optionalLeaveName)."','','".$approvalStatus."','".$perDayTransactionCount."')";
						$optionalResult=$db->query($perdayOptionalquery);  
						$result = $db -> query($insertOptionalLeave);
						$optionalLeaveapplied=1;
					}
				}
			}
		}
		# If the employee selected optional leave, then update the database accordingly -- END
	}
	
	$mailBody = $mailBody . "<tr><td colspan='3' align='right'><strong>Total Number Of Leaves :</strong> " . $totalLeaves . "</td></tr></table>";
	//Check if balance leaves is exceeding permitted leaves per year
	if ((getTotalLeaves($empid) - $totalLeaves) < -60) {
		echo "<script>BootstrapDialog.alert('Selected number of leaves + your present total balance leaves is exceeding permitted leaves per year. So Leaves are not applied. Please reduce number of leaves.')</script>";
	} else {
		echo $mailBody;
		
		# Dont insert into database if the total leaves is zero.
		if ($totalLeaves != 0 || ($totalLeaves == 0 && $_POST['splType'] != "%2F") || ($totalLeaves == 0 && isset($WFH)) || ($totalLeaves == 0 && isset($OnSite)) || $totalLeaves == 0 && isset($optionalLeaveapplied) && $optionalLeaveapplied==1) {
			$reason = mysql_real_escape_string(urldecode($_POST['reason']));
			$query = "INSERT INTO`empleavetransactions` (`transactionid` ,`empid` ,`startdate` ,`enddate` ,`count`,`reason`,`approvalstatus`,`approvalcomments`)VALUES ('" . $transaction_id . "','" . $empid . "', '" . $_POST[$fromDateId] . "', '" . $_POST[$toDateId] . "','" . $totalLeaves . "', '" . $reason . "','$approvalStatus','$approvalcomments')";
			$result = $db -> query($query);
			# Get the empid, fromdate and todate from the transactionid and execute the script which updates the inout table.
			$cmd = '/usr/bin/php trackWeeklyAttendance.php --empId='.$empid.' --fromDate='.$_POST[$fromDateId].' --toDate='.$_POST[$toDateId].' >> /dev/null &';
			exec($cmd);
		}
		
		//Execute perday transaction queries
		foreach ($executeQueries as $query) {
			$db -> query($query);
		}
		
		# Update the special leaves taken into database 
		if (isset($_POST['splType']) && $_POST['splType'] != "%2F") {
			$splleaveidresult = $db -> query("select specialleaveid, specialleave from specialleaves where specialleave like '" . urldecode($_POST['splType']) . "%'");
			$splleaveidrow = $db -> fetchAssoc($splleaveidresult);
			$result = $db -> query("select * from  empsplleavetaken where empid=" . $empid);
			if ($db -> hasRows($result)) {
				$row = $db -> fetchAssoc($result);
				$spltaken = $row['splleavetaken'] . $splleaveidrow['specialleaveid'] . "$approvalSplLeaveStatus:";
				$query = "UPDATE `empsplleavetaken` SET `splleavetaken` = '" . $spltaken . "' WHERE `empid` ='" . $empid . "'";
				$res = $db -> query($query);
			} else {
				$res = $db -> query("INSERT INTO `empsplleavetaken` (`empid` ,`splleavetaken`)
										VALUES ('" . $empid . "', '" . $splleaveidrow['specialleaveid'] . "$approvalSplLeaveStatus:')");
			}
		}
		
		
		if ($totalLeaves != 0 || ($totalLeaves == 0 && isset($_POST['splType']) && $_POST['splType'] != "%2F") || ($totalLeaves == 0 && isset($WFH)) || ($totalLeaves == 0 && isset($OnSite)) || $totalLeaves == 0 && isset($optionalLeaveapplied) && $optionalLeaveapplied==1) {
			//send mail with pending status to emp and manager who applied leave
			if ($page == "applyteammemberleave.php") {
				$getleavesquery = $db -> query("SELECT  `empid` , `count` FROM empleavetransactions WHERE transactionid ='" . $transaction_id . "'");
				$row1 = $db -> fetchAssoc($getleavesquery);
				$balanceLeaves = getValueFromQuery("SELECT  balanceleaves FROM emptotalleaves WHERE empid ='" . $empid . "'", "balanceleaves");
				$reducedleaves = ($balanceLeaves - $row1['count']);
				//Updating the balance leaves
				$reduceleavesquery = $db -> query("UPDATE  `emptotalleaves` SET  `balanceleaves` =  '" . $reducedleaves . "' WHERE  `empid` ='" . $empid . "'");
				$cmd = '/usr/bin/php -f sendmail.php ' . $transaction_id . ' ' . $empid . ' ApproveLeave >> /dev/null &';
				exec($cmd);
				echo '<script>BootstrapDialog.alert("Leave is Approved by manager.")</script>';
			}
			if ($page == "hrapplyleaveforall.php") {
				$getleavesquery = $db -> query("SELECT  `empid` , `count` FROM empleavetransactions WHERE transactionid ='" . $transaction_id . "'");
				$row1 = $db -> fetchAssoc($getleavesquery);
				$balanceLeaves = getValueFromQuery("SELECT  balanceleaves FROM emptotalleaves WHERE empid ='" . $empid . "'","balanceleaves");
				$reducedleaves = ($balanceLeaves - $row1['count']);
				//Updating the balance leaves
				$reduceleavesquery = $db -> query("UPDATE  `emptotalleaves` SET  `balanceleaves` =  '" . $reducedleaves . "' WHERE  `empid` ='" . $empid . "'");

				$cmd = '/usr/bin/php -f sendmail.php ' . $transaction_id . ' ' . $empid . ' ApproveLeave >> /dev/null &';
				exec($cmd);
				echo '<script>BootstrapDialog.alert("Leave is Approved by HR.")</script>';
			}
			if ($page == "applyleave.php") {
				$cmd = '/usr/bin/php -f sendmail.php ' . $transaction_id . ' ' . $empid . ' PendingLeave >> /dev/null &';
				exec($cmd);
				echo '<script>BootstrapDialog.alert("Application Submitted. Waiting for approval")</script>';
			}
			if ($page == "applyOnSite.php") {
				$cmd = '/usr/bin/php -f sendmail.php ' . $transaction_id . ' ' . $empid . ' OnSite >> /dev/null &';
				exec($cmd);
				echo '<script>BootstrapDialog.alert("Application Submitted. Waiting for approval")</script>';
			}
		} else {
			if ($page == "applyteammemberleave.php") {
				echo '<script>BootstrapDialog.alert("Total Leaves selected is zero. Approval mail is not sent.")</script>';
			}
			if ($page == "applyleave.php") {
				echo '<script>BootstrapDialog.alert("Total Leaves selected is zero. Approval mail is not sent to manager.")</script>';
			}
		}
	}

}

function getDelSection($page, $transactionid, $empid, $role) {
	global $db;
	
	# Delete already approved leaves (Only Optioanl holiday taken leaves)
	if ($page == "modifyempapprovedleaves.php") {
		$leaveCount = getValueFromQuery("select count from empleavetransactions where transactionid='" . $transactionid . "'", "count");
		//Increase count in emptotalleaves
		$balanceLeaves = getValueFromQuery("select balanceleaves from emptotalleaves where empid='" . $empid . "'", "balanceleaves");
		$updatedleaves = $balanceLeaves + $leaveCount;
		$updateleaves = $db -> query("UPDATE `emptotalleaves` SET `balanceleaves` = '" . $updatedleaves . "' WHERE empid='" . $empid . "'");
		$splStatus = "A";
		
		// Delete Optional leave -- START
		$optionalQuery="select * from  `empleavetransactions` where transactionid='".$transactionid."'";
		$resultopt=$db->query($optionalQuery);
		$row1=$db->fetchAssoc($resultopt);
		$currentYear=date("Y");
		$optionalLeaveQuery="select * from empoptionalleavetaken where empid='".$empid."' and date between '".$currentYear."-01-01' and '".$currentYear."-12-31' and state='Approved'";
		$optionalLeaveResult=$db->query($optionalLeaveQuery);
		if($db->hasRows($optionalLeaveResult)) {
			while($optionalLeaveRow = $db->fetchAssoc($optionalLeaveResult)){
				$datesRange=getDatesFromRange($row1['startdate'],$row1['enddate']);
				if(in_array($optionalLeaveRow['date'],$datesRange)) {
					$delOptionalLeave="DELETE FROM `lms`.`empoptionalleavetaken` WHERE `empoptionalleavetaken`.`date` ='".$optionalLeaveRow['date']."' and empid='".$empid."'";
					$optionalLeave=$db->query($delOptionalLeave);
				}
			}
		}
		# Delete Optional leave -- END
	}
	
	# Delete the leaves which are in pending state (Only Optioanl holiday taken leaves)
	if ($page == "selfleavestatus.php") {
		$splStatus = "P";
		
		# Delete Optional leave -- START
		$optionalQuery="select * from  `empleavetransactions` where transactionid='".$transactionid."'";
		$resultopt=$db->query($optionalQuery);
		$row1=$db->fetchAssoc($resultopt);
		$currentYear=date("Y");
		$optionalLeaveQuery="select * from empoptionalleavetaken where empid='".$empid."' and date between '".$currentYear."-01-01' and '".$currentYear."-12-31' and state='Pending'";
		$optionalLeaveResult=$db->query($optionalLeaveQuery);
		if($db->hasRows($optionalLeaveResult)) {
			while($optionalLeaveRow = $db->fetchAssoc($optionalLeaveResult)){
				$datesRange=getDatesFromRange($row1['startdate'],$row1['enddate']);
				if(in_array($optionalLeaveRow['date'],$datesRange)) {
					$delOptionalLeave="DELETE FROM `lms`.`empoptionalleavetaken` WHERE `empoptionalleavetaken`.`date` ='".$optionalLeaveRow['date']."' and empid='".$empid."'";
					$optionalLeave=$db->query($delOptionalLeave);
				}
			}
		}
		# Delete Optional leave -- END
	}
	
	# Update the special leave taken information -- START
	$leavetypequery = $db -> query("SELECT leavetype FROM  `perdaytransactions` WHERE transactionid ='" . $transactionid . "' AND leavetype !=  'FullDay' AND leavetype !=  'HalfDay' AND leavetype !=  'WFH'
											AND leavetype !=  'First Half-HalfDay & second Half-WFH' AND leavetype !=  'First Half-WFH & Second Half-HalfDay' AND leavetype NOT LIKE '%Optional%'");
	$leavetyperow = $db -> fetchAssoc($leavetypequery);
	if ($leavetyperow) {
		//Get leave type id from special leaves
		$leavetypeidquery = $db -> query("select specialleaveid from specialleaves where specialleave LIKE '" . $leavetyperow['leavetype'] . "%'");
		$leavetypeidrow = $db -> fetchAssoc($leavetypeidquery);
		$splleavetaken = getValueFromQuery("select splleavetaken from  empsplleavetaken where empid='" . $empid . "'", "splleavetaken");
		//Removing the pending leave when deleted the transaction
		$delspl = str_replace("" . $leavetypeidrow['specialleaveid'] . "$splStatus:", "", "" . $splleavetaken . "");
		$updatesplleavetakenquery = $db -> query("UPDATE  empsplleavetaken SET  `splleavetaken` =  '" . $delspl . "' where empid='" . $empid . "'");
	}
	# Update the special leave taken information -- END
	
	
	
	if ($page == "modifyempapprovedleaves.php") {
		# Send mail to users -- START
		if ($_SESSION['roleofemp'] == "manager") {
			$cmd = '/usr/bin/php -f sendmail.php ' . $transactionid . ' ' . $empid . ' cancelledApprovedLeaves manager>> /dev/null &';
		}
		if ($_SESSION['roleofemp'] == "hr") {
			$hrid = $_SESSION['u_empid'];
			$cmd = '/usr/bin/php -f sendmail.php ' . $transactionid . ' ' . $empid . ' cancelledApprovedLeaves hr $hrid>> /dev/null &';
		}
		exec($cmd);
		# Send mail to users -- END
		
		# Update the trasaction status as "Deleted
		$txtMessage = mysql_real_escape_string($_REQUEST['txtMessage']);
		if ($txtMessage == "") {
			$txtMessage = "Deleted By $role (".$_SESSION['u_fullname'].")";
		}
		
		# Delete the compoff taken leave
		if (preg_match('/CompOff Leave/', $row1['reason'])) {
			$getinoutCompOff=$db->query("select Date from  `inout` where compofftakenday ='".$row1['startdate']."' and empid='".$row1['empid']."'");
			$getinoutCompOffRow=$db->fetchAssoc($getinoutCompOff);
			$updateCompOff=$db->query("UPDATE `inout` SET compofftakenday='0000-00-00' WHERE empid='".$row1['empid']."' and Date='".$getinoutCompOffRow['Date']."'");
		}
		
		# Update empleavetransactions table status as "Deleted"
		$result1 = $db -> query("update `empleavetransactions` set approvalstatus='Deleted',approvalcomments='" . $txtMessage . "' WHERE transactionid ='" . $transactionid . "'");
		
		if ($result1) {
			# Once the empleavetransacton table is updated, update the perdaytransaction table.
			$updatePerDayTransaction = $db -> query("update `perdaytransactions` set status='Deleted' WHERE transactionid ='" . $transactionid . "'");
			
			# Get the empid, fromDate and toDate from transaction id
			$getTransactionIdInformationResult=$db->query("select * from  `empleavetransactions` where transactionid ='".$transactionid."'");
			$getTransactionIdInformationRow=$db->fetchAssoc($getTransactionIdInformationResult);
			
			# Get the empid, fromdate and todate from the transactionid and execute the script which updates the inout table.
			$cmd = '/usr/bin/php trackWeeklyAttendance.php --empId='.$getTransactionIdInformationRow['empid'].' --fromDate='.$getTransactionIdInformationRow['startdate'].' --toDate='.$getTransactionIdInformationRow['enddate'].' >> /dev/null &';
			exec($cmd);
			
			
			echo '<script>
					BootstrapDialog.alert("Leave deleted");
  					$("#loadmanagersection").load("modifyempapprovedleaves.php");
			  	</script>';
		}
		
	}
	
	
	if ($page == "selfleavestatus.php") {
		
		# Update the compoff taken leave
		if(isset($_REQUEST['compoffdate'])) {
			$inouttable = $db -> query("update `inout` set `compofftakenday`='0000-00-00' where empid ='".$_SESSION['u_empid']."'and  compofftakenday='".$_REQUEST['compoffdate']."'");
            $writeComments = $db -> query("update `empleavetransactions` set approvalstatus='Deleted',approvalcomments='Deleted By Employee (".$_SESSION['u_fullname'].")' WHERE transactionid ='" .$transactionid. "'");
            $updatePerDayTransaction = $db -> query("update `perdaytransactions` set status='Deleted' WHERE transactionid ='" . $transactionid . "'");
			if ($inouttable && $writeComments) {
				$cmd = '/usr/bin/php -f sendmail.php '. $transactionid.' '.$_SESSION['u_empid'].' DeleteCompOff >> /dev/null &';
				exec($cmd);
			} else {
				echo '<script>
                     	BootstrapDialog.alert("Compoff Leave not deleted successfully");
                      </script>';
			}
		} else {
			$cmd = '/usr/bin/php -f sendmail.php ' . $transactionid . ' ' . $_SESSION['u_empid'] . ' DeletedByUser >> /dev/null &';
			exec($cmd);
			$writeComments = $db -> query("update `empleavetransactions` set approvalstatus='Deleted',approvalcomments='Deleted By Employee (".$_SESSION['u_fullname'].")' WHERE transactionid ='" . $transactionid . "'");
			if($writeComments) {
				$updatePerDayTransaction = $db -> query("update `perdaytransactions` set status='Deleted' WHERE transactionid ='" . $transactionid . "'");
			}
		}
		echo "<script>window.location='index.php';</script>";
	}
}

function getModifySection($page, $role) {
	global $db;
	$variables = array();
	$keys = array();
	$values = array();
	$val = "";
	$leavecount = 0;
	$trasactionId;
	$count1 = 0;
	$splLeaveCount = 0;
	$variables = $_POST;
	$permittedLeaves = 0;
	$splLeaveCount = 0;
	$count = 0;
	$executeQueries = array();
	unset($variables['txtMessage']);
	$keys = array_keys($variables);
	$values = array_values($variables);
	if (isset($_SESSION['splLeaveType']) && !empty($_SESSION['splLeaveType'])) {
		$query = "select dayspermitted from specialleaves where specialleave = '" . $_SESSION['splLeaveType'] . "'";
		$sql = $db -> query($query);
		if ($sql == true) {
			while ($row1 = mysql_fetch_array($sql)) {
				$permittedLeaves = $row1['dayspermitted'];
			}
		}
	}
	for ($v = 0; $v < sizeof($values); $v++) {
		if ($values[$v] == 7) {
			$splLeaveCount++;
		}
	}
	if ($splLeaveCount > $permittedLeaves) {
		echo "Not Updated. You have selected special leaves more than permited leaves($permittedLeaves days). Please try again";
	} else {
		# Modify the perdaytransaction table count and leavetype
		for ($k = 0; $k < sizeof($keys); $k++) {
			if (preg_match('/Day/', $keys[$k])) {
				$leaveTypeSelected = "";
				$count++;
				$val = $keys[$k];
				$date = explode("/", $val);
				$daydate = $date[1];
				$trasactionId = $date[2];
				$shift = "";
				$modifiedValue = $values[$k];
				if ($modifiedValue == 1) {
					$leaveTypeSelected = "HalfDay";
					$index = "shift/" . $date[1] . "/" . $trasactionId;
					$shift = $_REQUEST[$index];
					$leavecount += 0.5;
					$perDayTransactionCount=0.5;
				} else if ($modifiedValue == 2) {
					$leaveTypeSelected = "FullDay";
					$leavecount += 1;
					$perDayTransactionCount=1;
				} else if ($modifiedValue == 3) {
					$leaveTypeSelected = "WFH";
					$perDayTransactionCount=0;
					$index = "shift/" . $date[1] . "/" . $trasactionId;
					$shift = $_REQUEST[$index];
				} else if ($modifiedValue == 4) {
					$leaveTypeSelected = "First Half-HalfDay & second Half-WFH";
					$leavecount += 0.5;
					$perDayTransactionCount=0.5;
					$shift = "firstHalf";
				} else if ($modifiedValue == 5) {
					$leaveTypeSelected = "First Half-WFH & Second Half-HalfDay";
					$leavecount += 0.5;
					$perDayTransactionCount=0.5;
					$shift = "secondHalf";
				} else if ($modifiedValue == 6) {
					$leaveTypeSelected = "OnSite";
					$leavecount += 0;
					$perDayTransactionCount=0;
				} else if ($modifiedValue == 7) {
					$perDayTransactionCount=0;
					$leaveTypeSelected = $_SESSION['splLeaveType'];
				}
				$query = "update perdaytransactions SET `count`=".$perDayTransactionCount.", leavetype='" . $leaveTypeSelected . "', shift='" . $shift . "' where transactionid='" . $trasactionId . "' AND date = '" . $daydate . "'";
				array_push($executeQueries, $query);
				$count1 += 1;
			}
		}
		
		# Modify already approved leaves	-	update empleavetransaction table
		if ($page == "modifyempapprovedleaves.php") {
			//Get initialcount from empleavetransactions
			$countquery = $db -> query("select count,empid from empleavetransactions where transactionid='" . $trasactionId . "'");
			$countrow = $db -> fetchAssoc($countquery);
			//Get balance leaves from emptotalleaves
			$balanceleaves = $db -> query("select balanceleaves from emptotalleaves where empid='" . $countrow['empid'] . "'");
			$balanceleavesrow = $db -> fetchAssoc($balanceleaves);
			$newbalanceleaves = ($balanceleavesrow['balanceleaves'] + $countrow['count']) - $leavecount;
			//Check if balance leaves is exceeding permitted leaves per year
			if (($newbalanceleaves + (getCarryForwardedLeaves($countrow['empid']))) < -6) {
				echo "<script>BootstrapDialog.alert('After modification, emp leaves exceeded permitted leaves per year. So Leaves are not approved.')</script>";
			} else {
				//Execute perday transaction queries
				foreach ($executeQueries as $query) {
					$db -> query($query);
				}
				$txtMessage = $_REQUEST['txtMessage'];
				if ($txtMessage == "") {$txtMessage = "Modified By $role (".$_SESSION['u_fullname'].")";
				}
				$query = "update empleavetransactions set count = '" . $leavecount . "',approvalcomments='" . $txtMessage . "' where transactionid = '" . $trasactionId . "'";
				$sql = $db -> query($query);
				
				# Get the empid, fromDate and toDate from transaction id
				$getTransactionIdInformationResult=$db->query("select * from  `empleavetransactions` where transactionid ='".$trasactionId."'");
				$getTransactionIdInformationRow=$db->fetchAssoc($getTransactionIdInformationResult);
				
				# Get the empid, fromdate and todate from the transactionid and execute the script which updates the inout table.
				$cmd = '/usr/bin/php trackWeeklyAttendance.php --empId='.$getTransactionIdInformationRow['empid'].' --fromDate='.$getTransactionIdInformationRow['startdate'].' --toDate='.$getTransactionIdInformationRow['enddate'].' >> /dev/null &';
				exec($cmd);
				
				displaytable($_REQUEST['tid']);
				$updatebalanceleaves = $db -> query("update emptotalleaves set balanceleaves = '" . $newbalanceleaves . "' where empid = '" . $countrow['empid'] . "'");
				if ($count == $count1) {
					echo "<br><br>Updated Successfully";
					if ($_SESSION['roleofemp'] == "manager") {
						$cmd = '/usr/bin/php -f sendmail.php ' . $trasactionId . ' ' . $countrow['empid'] . ' modifyApprovedLeaves manager>> /dev/null &';
					}
					if ($_SESSION['roleofemp'] == "hr") {
						$hrid = $_SESSION['u_empid'];
						$cmd = '/usr/bin/php -f sendmail.php ' . $trasactionId . ' ' . $countrow['empid'] . ' modifyApprovedLeaves hr $hrid>> /dev/null &';
					}
					exec($cmd);
				}
			}
		}
		
		# Modify leaves which are in pending state - update empleavetransaction table
		if ($page == "selfleavestatus.php") {
			//Get initialcount from empleavetransactions
			$countquery = $db -> query("select count,empid from empleavetransactions where transactionid='" . $trasactionId . "'");
			$countrow = $db -> fetchAssoc($countquery);
			//Get balance leaves from emptotalleaves
			$balanceleaves = $db -> query("select balanceleaves from emptotalleaves where empid='" . $countrow['empid'] . "'");
			$balanceleavesrow = $db -> fetchAssoc($balanceleaves);
			$newbalanceleaves = ($balanceleavesrow['balanceleaves'] + $countrow['count']) - $leavecount;
			//Check if balance leaves is exceeding permitted leaves per year
			if (($newbalanceleaves + (getCarryForwardedLeaves($countrow['empid']))) < -6) {
				echo "<script>BootstrapDialog.alert('After modification, emp leaves exceeded permitted leaves per year. So Leaves can't be applied')</script>";
			} else {
				foreach ($executeQueries as $query) {
					$result = $db -> query($query);
				}
				$query = "update empleavetransactions set count = '" . $leavecount . "' where transactionid = '" . $trasactionId . "'";
				$sql = $db -> query($query);
				if ($count == $count1) {
					echo "Updated Successfully and sending mail to Manager for approval";
					$cmd = '/usr/bin/php -f sendmail.php ' . $trasactionId . ' ' . $_SESSION['u_empid'] . ' ModifiedLeave >> /dev/null &';
					exec($cmd);
				}
			}
		}
	}
}

function getSubmitSection($transactionid, $page, $id, $url, $i) {
	global $db;
	$LeavesTypes = array("HalfDay", "FullDay", "WFH", "First Half-HalfDay & second Half-WFH", "First Half-WFH & Second Half-HalfDay","OnSite");
	$query = $db -> query("select * from perdaytransactions where transactionid='" . $transactionid . "'");
	for ($j = 0; $j < $db -> countRows($query); $j++) {
		$row = $db -> fetchAssoc($query);
		if (!in_array($row['leavetype'], $LeavesTypes)) {
			$_SESSION['splLeaveType'] = $row['leavetype'];
			array_push($LeavesTypes, $row['leavetype']);
		}
	}
	echo '<tr>
			<td colspan="6">';
	if ($page == "selfleavestatus.php") {
		echo '<form id=' . $id . '' . $i . ' method="POST" action=' . $url . '>';
	}
	if ($page == "modifyempapprovedleaves.php") {
		echo '<form id=' . $id . ' method="POST" action=' . $url . '>';
	}
	echo '<table class="table table-hover">
				  <thead>
				  <tr class="info">
				  <th>Date</th>
				  <th>leavetype</th>
				  <th>Shift</th>
				  </tr>
				  </thead>
				  <tbody>';
			  
	$query = $db -> query("select * from perdaytransactions where transactionid='" . $transactionid . "'");
	for ($i = 0; $i < $db -> countRows($query); $i++) {
		$row = $db -> fetchAssoc($query);
		$options = "";
		$shiftOptions = "";
		$leavetype = $row['leavetype'];
		$shift = $row['shift'];
		$Day = $row['date'];
		for ($k = 1; $k <= sizeof($LeavesTypes); $k++) {
			$id = $k;
			$thing = $LeavesTypes[$k - 1];
			if ($thing == $leavetype) {
				$options .= "<OPTION VALUE=\"$id\" selected>" . $thing;
			} else {
				$options .= "<OPTION VALUE=\"$id\">" . $thing;
			}
		}
		echo '<tr></tr><tr><td>' . $row['date'] . '</td>';
		echo '<td><SELECT class= "form-control modifyspecial_leave" NAME= Day' . "/" . $Day . "/" . $transactionid . '> ' . $options . '</SELECT></td>';
		if ($leavetype == "WFH") {
			$shiftOptions = '
						<SELECT class="form-control" NAME= shift' . "/" . $Day . "/" . $transactionid . '>';
			if ($shift == "firstHalf") {
				$shiftOptions .= '<OPTION VALUE="firstHalf" selected>First Half</OPTION>
							<OPTION VALUE="secondHalf">Second Half</OPTION>
							<OPTION VALUE="fullDay">Full Day</OPTION>
						</SELECT>';
			}
			if ($shift == "secondHalf") {
				$shiftOptions .= '<OPTION VALUE="firstHalf">First Half</OPTION>
							<OPTION VALUE="secondHalf" selected>Second Half</OPTION>
							<OPTION VALUE="fullDay">Full Day</OPTION>
						</SELECT>';
			}
			if ($shift == "fullDay") {
				$shiftOptions .= '<OPTION VALUE="firstHalf">First Half</OPTION>
							<OPTION VALUE="secondHalf">Second Half</OPTION>
							<OPTION VALUE="fullDay" selected>Full Day</OPTION>
						</SELECT>';
			}
			echo "<td class='danger' id='td/" . $Day . "/" . $transactionid . "'>" . $shiftOptions . "</td>";
		}
		if ($leavetype == "HalfDay") {
			$shiftOptions = '
						<SELECT class="form-control" NAME= shift' . "/" . $Day . "/" . $transactionid . '>';
			if ($shift == "firstHalf") {
				$shiftOptions .= '<OPTION VALUE="firstHalf" selected>First Half</OPTION>
							<OPTION VALUE="secondHalf">Second Half</OPTION>
						</SELECT>';
			}
			if ($shift == "secondHalf") {
				$shiftOptions .= '<OPTION VALUE="firstHalf">First Half</OPTION>
							<OPTION VALUE="secondHalf" selected>Second Half</OPTION>
										</SELECT>';
			}
			echo "<td class='danger' id='td/" . $Day . "/" . $transactionid . "'>" . $shiftOptions . "</td>";
		} else {
			echo "<td class='danger' id='td/" . $Day . "/" . $transactionid . "'></td>";
		}
		echo '</tr>';
	}
}

function getDynamicSelectOptions() {
	echo "$('.modifyspecial_leave').change(function(){
			var selectedValue = $(this).find(\":selected\").val();
			if(!(selectedValue==1 || selectedValue==3)) {
				a=$(this)[0].name;
					a=a.replace(\"Day\",\"td\");
					b=document.getElementById(a);
					b.removeChild(b.children[0]);
			} else {
				if(selectedValue==1) {
					a=$(this)[0].name;
					a=a.replace(\"Day\",\"td\");
					b=document.getElementById(a);
					if(b.childern!=undefined){
						b.removeChild(b.children[0]);
					}
					a=a.replace(\"td\",\"shift\");
					c=\"<select class='form-control' name='\";
					c=c.concat(a+\"'><option value='firstHalf'>First Half</option><option value='secondHalf'>Second Half</option></select>\"); 
					b.innerHTML=c;
					$(this)[0].parentNode.nextSibling.children[0].setAttribute('style','');
				}
				if(selectedValue==3) {
					a=$(this)[0].name;
					a=a.replace(\"Day\",\"td\");
					b=document.getElementById(a);
					if(b.childern!=undefined){
						b.removeChild(b.children[0]);
					}
					a=a.replace(\"td\",\"shift\");
					c=\"<select name='\";
					c=c.concat(a+\"'><option value='firstHalf'>First Half</option><option value='secondHalf'>Second Half</option><option value='fullDay'>Full Day</option></select>\"); 
					b.innerHTML=c;
					$(this)[0].parentNode.nextSibling.children[0].setAttribute('style','');
				}	
			}
		});";
	}
?>

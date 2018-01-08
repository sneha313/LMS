<?php
require_once 'Library.php';
error_reporting("E_ALL");

function timediffinHR ($first, $last) {
        if ($last == '00:00:00') {return 0;}
        $seconds= strtotime($last)-strtotime($first);
        $days    = floor($seconds / 86400);
        $hours   = floor(($seconds - ($days * 86400)) / 3600);
        $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
        $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));
        return "$hours:$minutes:$seconds";
}

function timeAdd ($first, $last) {
	$hour=0;$min=0;$sec=0;
	list($hour1,$m1,$s1)=explode(":",$first);
	list($hour2,$m2,$s2)=explode(":",$last);
	$sec=$s1+$s2;
	if($sec>=60) {
	        if($sec==60) {
	                $sec=00;
	        }
        	if($sec>60) {
                	$sec=$sec-60;
	        }
        	$min=1;
	}
	$min=$min+$m1+$m2;
	if ($min >= 60) {
        	if($min==60) {
                	 $min=00;
	        }
        	if($min>60) {
                	 $min=$min-60;
	        }
        	$hour=1;;
	}
	$hour=$hour+$hour1+$hour2;
	return "$hour:$min:$sec";
}

function getDay($empid,$date) {
	global $db;
	$query="select a.transactionid,a.leavetype,a.shift,a.compoffreason,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empid."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
	$result=$db->query($query);
	$row = $db->fetchArray($result);
	$statusQuery="select * from `empleavetransactions` WHERE `transactionid` ='".$row['transactionid']."'";
        $statusresult=$db->query($statusQuery);
	$statusRow = $db->fetchArray($statusresult);
	if($statusRow['approvalstatus']=="Approved") {
		if ($row['leavetype']=='On Site') {
			return "On Site";
		}
		if(isset($row['compoffreason']) && $row['compoffreason']!="") {
			 return $row['compoffreason'];
		} else {
			if ($row['leavetype']=='WFH') {
				if(empty($row['shift'])) {
					return $row['leavetype'];
				} else {
					if($row['shift']=="fullDay") {
						return $row['leavetype'];
					} else {
						return $row['leavetype']." (".$row['shift'].")";
					}
				}
			} 
			if ($row['leavetype']) {
				if ($row['leavetype']=="First Half-WFH & Second Half-HalfDay" || $row['leavetype']=="First Half-HalfDay & second Half-WFH")
				{
					$retunVal=$row['leavetype'];	
				} else {
					$retunVal=$row['leavetype']." PTO";
				} 
				if ($row['leavetype']=="Other Leave Type") {
					return $statusRow['reason'];
				}
				return $retunVal;
			}
		}
	}
	#Check whether holiday is present on that day
	$getHoliday="select holidayname from holidaylist where date='".$date."'";
				
	#Check whether employee birthday is present on that day
	$getBirthday="SELECT birthdaydate FROM emp WHERE empid='".$empid."' and state='Active'";
				
	$holidayResult=$db->query($getHoliday);
	$birthdayResult=$db->query($getBirthday);
						
	if($holidayResult && $birthdayResult) {
		$holidayRow = $db->fetchArray($holidayResult);
		$birthdayRow = $db->fetchArray($birthdayResult);
		if($db->countRows($holidayResult) > 0 && isBirthday($empid,$date)) {
			return "Birthday and ".$holidayRow['holidayname']; 
		} elseif ($db->countRows($holidayResult) > 0) {
			if ($holidayRow['holidayname']) {
				return $holidayRow['holidayname'];
			}  
		} elseif (isBirthday($empid,$date)) {
			return "Birthday";
		} else {
			return "No Data";
		}
	}
}

function isBirthday($empid,$date) {
	global $db;
	#Check whether employee birthday is present on that day
	$getBirthday="SELECT birthdaydate FROM emp WHERE empid='".$empid."' and state='Active'";
	$birthdayResult=$db->query($getBirthday);
	if($birthdayResult) {
		$birthdayRow = $db->fetchArray($birthdayResult);
		if($db->countRows($birthdayResult)>0) {
			list($by,$bm,$bd) = explode('-', $birthdayRow['birthdaydate']);
			list($y,$m,$d) = explode('-', $date);
			if($bm==$m && $bd==$d) {
				return 1;
			} else {
				return 0;
			}
		}
	}
}

function isHoliday($date) {
	global $db;
	#Check whether holiday is present on that day 
        $getHoliday="select holidayname from holidaylist where date='".$date."' and leavetype='Fixed'";
        $holidayResult=$db->query($getHoliday);
        if($holidayResult && $db->hasRows($holidayResult)) {
		return 1;
	} else {
		return 0;
	}
}

function isOptionalHolidayApplied($date,$empid) {
	global $db;
	#Check whether holiday is present on that day and is optional
	$getHoliday="select holidayname from holidaylist where date='".$date."' and leavetype='Optional'";
	$holidayResult=$db->query($getHoliday);
	# check whether employee applied optional holiday
	$getOptionalHolidayResult=$db->query("select * from empoptionalleavetaken where empid='".$empid."' and date='".$date."'");
	if($holidayResult && $db->hasRows($holidayResult) && $db->hasRows($getOptionalHolidayResult)) {
		return 1;
	} else {
		return 0;
	}
}

function isPending($date,$empId) {
	global $db;
	
	$pendingQuery="SELECT count(*) as count from `perdaytransactions` where `empid` ='".$empId."' AND `date`='".$date."' and state='Pending'";
	$pendingResult=$db->query($pendingQuery);
	$pendingRow=mysql_fetch_assoc($pendingResult);
	if ($pendingRow['count'] != 0) {
		return 1;
	} else {
		return 0;
	}
	
}
function isOptionalHoliday($date) {
        global $db;
        #Check whether holiday is present on that day and it is optional
        $getHoliday="select holidayname from holidaylist where date='".$date."' and leavetype='Optional'";
        $holidayResult=$db->query($getHoliday);
        if($holidayResult && $db->hasRows($holidayResult)) {
                return 1;
        } else {
                return 0;
        }
}

function isFullDayPTO($date,$empId) {
        global $db;
        $getHoliday="select a.transactionid,a.leavetype,a.shift,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empId."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
        #Check whether employee is on Full Day PTO on the given day 
     //   $getHoliday="select leavetype from perdaytransactions where date='".$date."' and empid='".$empId."'";
        $holidayResult=$db->query($getHoliday);
        if($holidayResult && $db->hasRows($holidayResult)) {
		$PTORow = $db->fetchArray($holidayResult);
		if ( $PTORow['leavetype']=="FullDay") {
                	return 1;
        	} else {
                	return 0;
        	}
	} else {
		return 0;
	}
}

function isHalfDayPTO($date,$empId) {
        global $db;
        
        #Check whether employee is on Half Day PTO on the given day 
      //  $getHalfDayPTO="select leavetype from perdaytransactions where date='".$date."' and empid='".$empId."'";
        $getHalfDayPTO="select a.transactionid,a.leavetype,a.shift,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empId."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
        $HalfDayPTOResult=$db->query($getHalfDayPTO);
        if($HalfDayPTOResult && $db->hasRows($HalfDayPTOResult)) {
		$PTORow = $db->fetchArray($HalfDayPTOResult);
		if ( strtoupper($PTORow['leavetype'])=="HALFDAY") {
                	return 1;
        	} else {
                	return 0;
        	}
	} else {
		return 0;
	}
}

function isHalfdayWFH($date,$empId) {
        global $db;
        #Check whether employee is on WFH on the given day
        $getWFH="select a.transactionid,a.leavetype,a.shift,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empId."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
        $WFHResult=$db->query($getWFH);
        if($WFHResult && $db->hasRows($WFHResult)) {
                $WFHRow = $db->fetchArray($WFHResult);
                if ( $WFHRow['leavetype']=="WFH" && (strtoupper($WFHRow['shift'])=="FIRSTHALF" || strtoupper($WFHRow['shift'])=="SECONDHALF")) {
                        return 1;
                } else {
                        return 0;
                }
        } else {
                return 0;
        }
}

function isSpecialLeave($date,$empId) {
        global $db;
        #Check whether employee is on WFH on the given day
        $getSpecialLeave="select a.transactionid,a.leavetype,a.shift,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empId."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
        $SplLeaveResult=$db->query($getSpecialLeave);
        if($SplLeaveResult && $db->hasRows($SplLeaveResult)) {
                $SPLRow = $db->fetchArray($SplLeaveResult);
                if (preg_match('/wedding/',$SPLRow['leavetype']) || preg_match('/Paternity Leave/',$SPLRow['leavetype']) || preg_match('/Death of spouse/',$SPLRow['leavetype']) || preg_match('/Death of immediate/',$SPLRow['leavetype']) || preg_match('/Team Event/',$SPLRow['leavetype']) || preg_match('/ECI Fun Day/',$SPLRow['leavetype'])) {
                        return 1;
                } else {
                        return 0;
                }
        } else {
                return 0;
        }
}

function isWFH($date,$empId) {
		global $db;
        #Check whether employee is on WFH on the given day 
   //     $getWFH="select leavetype from perdaytransactions where date='".$date."' and empid='".$empId."'";
        $getWFH="select a.transactionid,a.leavetype,a.shift,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empId."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
        $WFHResult=$db->query($getWFH);
        if($WFHResult && $db->hasRows($WFHResult)) {
                $WFHRow = $db->fetchArray($WFHResult);
                if ( $WFHRow['leavetype']=="WFH") {
                        return 1;
                } else {
                        return 0;
                }
        } else {
	        return 0;
	}
}

function isFulldayWFH($date,$empId) {
	global $db;
	#Check whether employee is on WFH on the given day
	$getWFH="select a.transactionid,a.leavetype,a.shift,b.approvalstatus from `perdaytransactions` a ,empleavetransactions b WHERE a.`empid` ='".$empId."' AND a.`date`='".$date."' and b.approvalstatus='Approved' and a.transactionid=b.transactionid";
	$WFHResult=$db->query($getWFH);
	if($WFHResult && $db->hasRows($WFHResult)) {
		$WFHRow = $db->fetchArray($WFHResult);
		if ( $WFHRow['leavetype']=="WFH" && strtoupper($WFHRow['shift'])=="FULLDAY") {
			return 1;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
	
}

function getShiftforDay($leavetype,$empId,$date) {
		global $db,$defaultIn, $defaultOut,$halfDayDefault;
        #Check whether employee is on WFH on the given day 
        $getShift="select shift from perdaytransactions where date='".$date."' and empid='".$empId."' and leavetype='".$leavetype."'";
        $shiftResult=$db->query($getShift);
        if($shiftResult && $db->hasRows($shiftResult)) {
                $shiftRow = $db->fetchArray($shiftResult);
                return $shiftRow['shift'];
        } else {
	        return "";
	}
	
}
function isWeekend($date) {
	$date1 = strtotime($date);
        $date2 = date("l", $date1);
        $date3 = strtolower($date2);
        if(($date3 == "saturday" )|| ($date3 == "sunday")){
            return 1;
        } else {
            return 0;
        }
}
function getFriday ($givenDate) {
	$off= 5 - date('w', strtotime($givenDate));
	return date('Y-m-d', strtotime("$givenDate $off day"));
}

function getHalfDayShift($row,$empId,$date,$time,$subempinfo,$leaveType) {
		global $defaultIn, $defaultOut,$halfDayDefault;
		if($time=="first") {
			if (strtoupper(getShiftforDay($leaveType,$empId,$date)) =="FIRSTHALF") {
				if (strtotime($row["First"]) > strtotime($halfDayDefault)) {
						$subempinfo=$subempinfo. '<font color=red>'.$row["First"].'</font>';
				} else {
					$subempinfo=$subempinfo. $row["First"];
				}	
			}
			if (strtoupper(getShiftforDay($leaveType,$empId,$date)) =="SECONDHALF") {
				if (strtotime($row["First"]) > strtotime($defaultIn)) {
						$subempinfo=$subempinfo. '<font color=red>'.$row["First"].'</font>';
				} else {
					$subempinfo=$subempinfo. $row["First"];
				}	
					
			}
			
		}
		if($time=="last") {
			if (strtoupper(getShiftforDay($leaveType,$empId,$date))=="FIRSTHALF") {
				if (strtotime($defaultOut) > strtotime($row["Last"])) {
						$subempinfo=$subempinfo. '<font color=red>'.$row["Last"].'</font>';
				} else {
						$subempinfo=$subempinfo. $row["Last"];
				}
			}
			if (strtoupper(getShiftforDay($leaveType,$empId,$date)) =="SECONDHALF") {
				if (strtotime($halfDayDefault) > strtotime($row["Last"])) {
						$subempinfo=$subempinfo. '<font color=red>'.$row["Last"].'</font>';
				} else {
					$subempinfo=$subempinfo. $row["Last"];
				}
			}
			
		}
		return $subempinfo;
}

function getTotalHRDiff($tot,$dayCount) {
	$totalWorkingHRsPerWeek=0;
	$perDayWorkingHrs="8:30:00";
	list($H,$M,$S)=explode(":",$perDayWorkingHrs);
	$H=$H*$dayCount;
	$M=$M*$dayCount;
	$extraHRS=$M/60;
	if ($extraHRS>=1) {
		$val=floor($extraHRS);
		if (is_integer($extraHRS)) {
			$m=00;
			$H=$H+$val;
			$totalWorkingHRsPerWeek="$H:$m:$S";
		} 
		if (is_double($extraHRS)) {
			$m=$M-(60*$val);
			$H=$H+$val;
			$totalWorkingHRsPerWeek="$H:$m:$S";
		}

	} else {
		$totalWorkingHRsPerWeek="$H:$M:$S";
	}
	list($Th,$Tm,$Ts)=explode(":",$totalWorkingHRsPerWeek);
	list($Eh,$Em,$Es)=explode(":",$tot);
	if ($Eh<$Th) {
		return 1;
	} else {
		if ( $Eh==$Th) {
			if($Em<$Tm) {
				return 1;
			} else {
				if ($Es<$Ts) {
					return 1;
				} else {
					return 0;
				}
			}
		} else {
			return 0;
		}
	}
}

function getColorofRow($count,$totalCount) {
	
	if($totalCount==0) { return $count."/".($totalCount); }
	elseif(($count/($totalCount))>0.5) { return "<font color=red>".$count."/".($totalCount); }
	else { return $count."/".($totalCount);}
	
}

function getDataForEMP($empID,$empName,$first,$last,$P10to4,$hr9,$hr45) {
	global $defaultIn, $defaultOut,$db, $defaultIn, $defaultOut,$halfDayDefault;
	$query='select * from `inout` WHERE `EmpID` ='.$empID.' AND 
			`Date` >= \''.getMonday($first).'\' AND `Date` <= \''.getSunday($last).'\';';
	$result=$db->query($query);
	$totInAfter=0;
	$totInAfterV=0;
	$totOutBefore=0;
	$totOutBeforeV=0;
	$totdayHr=0;
	$totdayHrV=0;
	$totweekHr=0;
	$totweekHrV=0;
	$subempinfoComp='<table class="table" style="width:30%;"><tr>';
	$empinfo="";
	$empinfo=$empinfo. '<tr><td><u>';
	$counter=0;
	while ($row = mysql_fetch_assoc($result)) {
		$in = $row["First"];
		$out= $row["Last"];
		$dayHr1=timediffinHR($in,$out);
		if(strtotime($dayHr1) < strtotime("8:30:00")){
			$totdayHrV=$totdayHrV+1;	
		}else {
			$totdayHr=$totdayHr+1;	
		}
		if (strtotime($in) > strtotime($defaultIn)) {
			$totInAfterV=$totInAfterV+1;
		}else {
			$totInAfter=$totInAfter+1;
			
		}
		if (strtotime($defaultOut) > strtotime($out)) {
			$totOutBeforeV=$totOutBeforeV+1;
		} else {
			$totOutBefore=$totOutBefore+1;
			
		}
	}
	$wkst = getMonday($first);
	while ($w1 < date('Y-m-d',strtotime(getFriday($last)))) {
		$subempinfo= '<td><table class="table">';
		$subempinfo=$subempinfo. '<tr>
				<th class="both" >Day</th>
				<th class="both">In</th>
				<th  class="both">Out</th>
				<th  class="both">total Hr</th>
				<th  class="both">Type Of day</th>

			</tr>';
		$w1= date('Y-m-d', strtotime("$wkst 7 day"));
		$queryS='select * from `inout` WHERE `EmpID` ='.$empID.' AND `Date` >= \''.$wkst.'\' AND `Date` < \''.$w1.'\';';
		$result=$db->query($queryS);
		$tempwkst=$wkst;
		$wkst=$w1;
		if (mysql_num_rows($result) == 0) {
	//		continue;
		}
		$tot="00:00:00";
		$flag=1;
		$flag_1=0;
		$skip=0;
		$dayCount=0;
		for ($j=0;$j<7;$j++) {
			if ($flag){
				$row = mysql_fetch_assoc($result);
			}
			$curday=date('Y-m-d', strtotime("$tempwkst $j day"));
			if ($row["Date"] != $curday) {
				//check whether the day is saturday or sunday
				$day=date('D,d M y', strtotime($curday));
				if (preg_match('/sun|sat/i',$day)){
					$flag=0;
					continue;
				}
				$subempinfo=$subempinfo. '<tr><td>'.$day.'</td>';
				// Check whether the day is On Site
				
				// Check whether the leave type is On Site
                                if (preg_match('/On Site/i',getDay($empID,$curday)))
                                {
                                	$subempinfo=$subempinfo. '<td>10:00:00</td>';
	                                $subempinfo=$subempinfo. '<td>18:30:00</td>';
        	                        $subempinfo=$subempinfo. '<td>8:30:00</td>';
                	                $subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
                        	        $flag=0;
                                	$tot=timeAdd($tot,"8:30:00");
	                                $dayCount=$dayCount+1;
        	                        $totInAfter=$totInAfter+1;
                	                $totOutBefore=$totOutBefore+1;
                        	        $totdayHr=$totdayHr+1;
                                	continue;
                                }	
				//check whether the day is full day WFH
				if (preg_match('/WFH/i',getDay($empID,$curday)) && getShiftforDay("WFH",$empID,$curday)!="")  
                		{
                		$subempinfo=$subempinfo. '<td>10:00:00</td>';
                		$subempinfo=$subempinfo. '<td>18:30:00</td>';
                		$subempinfo=$subempinfo. '<td>8:30:00</td>';
                   		$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).' ('.getShiftforDay("WFH",$empID,$curday).')</td></tr>';
                   		$flag=0;
                   		$tot=timeAdd($tot,"8:30:00");
                   		$dayCount=$dayCount+1;
                   		$totInAfter=$totInAfter+1;
                   		$totOutBefore=$totOutBefore+1;
                   		$totdayHr=$totdayHr+1;
                   		continue;
                		}
                // Check whether the day is First Half-WFH & Second Half-HalfDay
				if (getDay($empID,$curday)=="First Half-WFH & Second Half-HalfDay" && getShiftforDay("First Half-WFH & Second Half-HalfDay",$empID,$curday)!="")  
                {
                		$subempinfo=$subempinfo. '<td>10:00:00</td>';
                		$subempinfo=$subempinfo. '<td>14:15:00</td>';
                		$subempinfo=$subempinfo. '<td>4:15:00</td>';
                   		$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
                   		$flag=0;
                   		$tot=timeAdd($tot,"4:15:00");
                   		$dayCount=$dayCount+0.5;
                        $totInAfter=$totInAfter+1;
                   		$totOutBefore=$totOutBefore+1;
                   		$totdayHr=$totdayHr+1;
                   		continue;
                }
                // Check whether the day is "First Half-HalfDay & second Half-WFH"
				if (getDay($empID,$curday)=="First Half-HalfDay & second Half-WFH" && getShiftforDay("First Half-HalfDay & second Half-WFH",$empID,$curday)!="")  
                {
                		$subempinfo=$subempinfo. '<td>14:15:00</td>';
                		$subempinfo=$subempinfo. '<td>18:30:00</td>';
                		$subempinfo=$subempinfo. '<td>4:15:00</td>';
                   		$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
                   		$flag=0;
                   		$tot=timeAdd($tot,"4:15:00");
                   		$dayCount=$dayCount+0.5;
                   		$totInAfter=$totInAfter+1;
                   		$totOutBefore=$totOutBefore+1;
                   		$totdayHr=$totdayHr+1;
                   		continue;
                }
				$subempinfo=$subempinfo. '<td colspan=3>No Data</td>';
                $subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
               	$flag=0;
				continue;
			}
			
			// If the employee comes under the below days, count those days also
			if (isWeekend($row["Date"]) || isFulldayWFH($row["Date"],$empID) || isHoliday($row["Date"]) || isFullDayPTO($row["Date"],$empID)) {
				if (strtotime($row["First"]) > strtotime($defaultIn)) {
					$totInAfterV=$totInAfterV-1;
					$totInAfter=$totInAfter+1;
				}
				if (strtotime($defaultOut) > strtotime($row["Last"])) {
					$totOutBeforeV= $totOutBeforeV-1;
					$totOutBefore=$totOutBefore+1;
				}
				$diffTime=timediffinHR($row["First"],$row["Last"]);
				if(strtotime($diffTime) < strtotime("8:30:00")){
					$totdayHrV=$totdayHrV-1;
					$totdayHr=$totdayHr+1;
				}
				$dayCount=$dayCount-1;
			}
			 
			$dayHr=timediffinHR($row["First"],$row["Last"]);
			$tot=timeAdd($tot,$dayHr);
			$subempinfo=$subempinfo. '<tr><td>'.date('D,d-M-y', strtotime($row["Date"])).'</td>';
			$subempinfo=$subempinfo. '<td>';
			
			// If employee applies WFH, either fisr half / second half and decide what data goes to "FIRST" column
			if(isWFH($row['Date'], $empID) ) {
				$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"first",$subempinfo,"WFH");
				if(strtotime($dayHr)>strtotime("4:15:00")) {
					$totdayHr=$totdayHr+1;
					$totdayHrV=$totdayHrV-1;
				}
				if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="FIRSTHALF") {
					if (strtotime($row["First"]) > strtotime($halfDayDefault)) {
					} else {
						$totInAfterV=$totInAfterV-1;
						$totInAfter=$totInAfter+1;
					}
				}
				if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="SECONDHALF") {
					if (strtotime($row["First"]) > strtotime($defaultIn)) {
						$totInAfterV=$totInAfterV-1;
						$totInAfter=$totInAfter+1;
					}
				}
				$tot=timeAdd($tot,"4:15:00");
			} elseif(isHalfDayPTO($row['Date'], $empID) ) {
				// If employee applies Half Day PTO, either fisr half / second half and decide what data goes to "FIRST" column
				$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"first",$subempinfo,"HalfDay");
				if(strtotime($dayHr)>strtotime("4:15:00")) {
					$totdayHr=$totdayHr+1;
					$totdayHrV=$totdayHrV-1;
				}
				if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="FIRSTHALF") {
						if (strtotime($row["First"]) > strtotime($halfDayDefault)) {
						} else {
							$totInAfterV=$totInAfterV-1;
							$totInAfter=$totInAfter+1;
						}
				}
				if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="SECONDHALF") {
						if (strtotime($row["First"]) > strtotime($defaultIn)) {
							$totInAfterV=$totInAfterV-1;
							$totInAfter=$totInAfter+1;
						} 
				}
			} else {
					if (strtotime($row["First"]) > strtotime($defaultIn)) {
						if (isWeekend($row["Date"]) || isHoliday($row["Date"]) || isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID)) {
							$subempinfo=$subempinfo. $row["First"];
						} else {
	  						$subempinfo=$subempinfo. '<font color=red>'.$row["First"].'</font>';
						}
					} else {
						$subempinfo=$subempinfo. $row["First"];
					}
			}
		 
			$subempinfo=$subempinfo. '</td>';
			$subempinfo=$subempinfo. '<td>';
			
			// If employee applies WFH, either fisr half / second half and decide what data goes to "LAST" column
			if(isWFH($row['Date'], $empID) ) {
				$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"last",$subempinfo,"WFH");
				if (strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="FIRSTHALF") {
					if (strtotime($defaultOut) > strtotime($row["Last"])) {
							$totOutBeforeV=$totOutBeforeV-1;
							$totOutBefore=$totOutBefore+1;
					} 
				}
				if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="SECONDHALF") {
					if (strtotime($halfDayDefault) > strtotime($row["Last"])) {
					} else {
							$totOutBeforeV=$totOutBeforeV-1;
							$totOutBefore=$totOutBefore+1;
					}
				}
			} elseif(isHalfDayPTO($row['Date'], $empID) ) {
				// If employee applies Half Day PTO, either fisr half / second half and decide what data goes to "LAST" column
				$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"last",$subempinfo,"HalfDay");
				if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date']))=="FIRSTHALF") {
					if (strtotime($defaultOut) > strtotime($row["Last"])) {
							$totOutBeforeV=$totOutBeforeV-1;
							$totOutBefore=$totOutBefore+1;
					} 
				}
				if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="SECONDHALF") {
					if (strtotime($halfDayDefault) > strtotime($row["Last"])) {
					} else {
							$totOutBeforeV=$totOutBeforeV-1;
							$totOutBefore=$totOutBefore+1;
					}
				}
			} else {
					if (strtotime($defaultOut) > strtotime($row["Last"])) {
						if (isWeekend($row["Date"]) || isHoliday($row["Date"])|| isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID)) {
							$subempinfo=$subempinfo. $row["Last"];
						} else {
							$subempinfo=$subempinfo. '<font color=red>'.$row["Last"].'</font>';
						}
					} else {
						$subempinfo=$subempinfo. $row["Last"];
					}
			}
			$subempinfo=$subempinfo. '</td>';
			
			// Get the defaultdayHr based on halfday or fullday or First Half or Second Half WFH
			if(isWFH($row['Date'], $empID)) {
				if (strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="FIRSTHALF" || strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="SECONDHALF") {
					$dayHr=timeAdd($dayHr,"4:15:00");
				}
				$defaultdayHr=strtotime("8:30:00");
			} elseif(isHalfDayPTO($row['Date'], $empID) ) {
				$dayCount=$dayCount-0.5;
				$defaultdayHr=strtotime("4:15:00");
			} else {
				$defaultdayHr=strtotime("8:30:00");
			}
			
			// Get the data for the totaldayHr column
			if((strtotime($dayHr) < $defaultdayHr)){
				if (isWeekend($row["Date"]) || isHoliday($row["Date"])|| isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID) ) {
					$subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
				} else {
					$subempinfo=$subempinfo. '<td><font color=red>'.$dayHr.'</font></td>';
				}
			} else {
				$subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
			}
			
			// Get the data for "Leavetype" column
			if(getDay($empID,$curday)=="No Data") {
				$subempinfo=$subempinfo. '<td>'.$row['TypeOfDay'].'</td></tr>';
			} else {
				$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
			}
		 // Check whether the employee came to office on optional holiday	
		 if(isHoliday($row["Date"])) {
			if(isOptionalHoliday($row["Date"])) {
				if(!isOptionalHolidayApplied($row["Date"],$empID)) {
					$dayCount=$dayCount+1;
				}
			}
		}
		
		$dayCount=$dayCount+1;
		$flag=1;
	}
	# Even if emp comes on saturday/sunday dayCount should not be more than 5.
	if($dayCount>=5) {
		$dayCount=5;
	}
	
	// Get the total Hr's emp came to office per week
	if (getTotalHRDiff($tot,$dayCount)) {
		$subempinfo=$subempinfo. '<tr><td colspan=3 align="right"><font color=red >Total Hr</font></td>';
		$subempinfo=$subempinfo. '<td ><font color=red>'.$tot.'</font></td><td></td></tr>';
		$totweekHrV=$totweekHrV+1;	
	} else {
		$subempinfo=$subempinfo. '<tr><td colspan=3 align="right">Total Hr</td>';
		$subempinfo=$subempinfo. '<td >'.$tot.'</td><td></td></tr>';
		$totweekHr=$totweekHr+1;	
	}
	
	$subempinfo=$subempinfo. '</table>';	
	$subempinfoComp=$subempinfoComp. $subempinfo;
	if ($counter < 1) 
	{
		$subempinfoComp=$subempinfoComp. '</td>';
	} else {
		$subempinfoComp=$subempinfoComp. '</td></tr><tr>';
		$counter=-1;
	}
	$counter=$counter+1;
	$subempinfo="";
	}
	
	$empinfo=$empinfo. $empName.'</span></u></td>';
        $empinfo=$empinfo. '<td>';
        if ($P10to4 == "NOTMEET") {
        	    $empinfo=$empinfo.getColorofRow($totInAfterV,($totInAfterV+$totInAfter));
        } else {
                $empinfo=$empinfo.getColorofRow($totInAfter,($totInAfterV+$totInAfter));
        }
        $empinfo=$empinfo. '</td><td>';
        if($P10to4 == "NOTMEET") {
                $empinfo=$empinfo.getColorofRow($totOutBeforeV,($totOutBefore+$totOutBeforeV));
        } else {
                $empinfo=$empinfo.getColorofRow($totOutBefore,($totOutBefore+$totOutBeforeV));
        }
        $empinfo=$empinfo. '</td><td>';
        if ($hr9 == "NOTMEET"){
                $empinfo=$empinfo.getColorofRow($totdayHrV,($totdayHr+$totdayHrV));
        }else {
                $empinfo=$empinfo.getColorofRow($totdayHr,($totdayHr+$totdayHrV));
        }
	$empinfo=$empinfo. '</td><td>';
	if ($hr45 == "NOTMEET") {
		 $empinfo=$empinfo.getColorofRow($totweekHrV,($totweekHr+$totweekHrV));
	} else { 
		$empinfo=$empinfo.getColorofRow($totweekHr,($totweekHr+$totweekHrV));
	}	
	$empinfo=$empinfo. '</td><td>';
	$count=$count+ 1;
	
	$empinfo=$empinfo."<p><a href='javascript:void(null);' 
		onclick='showDialog(\"".$count."\",\"".$empID."\",\"".$first."\",\"".$last."\");'>Open</a></p>
		<div id='dialog-modal".$count."' title='Access Detail Graph' style='display: none;'></div>";
		
	$empinfo=$empinfo. '</td></tr>';
	echo $empinfo;
	
	echo $subempinfoComp.'</table>';
	echo '</td></tr>';
}

function getDataForEMP1($empID,$empName,$first,$last,$P10to4,$hr9,$hr45) {
	global $defaultIn, $defaultOut,$db, $defaultIn, $defaultOut,$halfDayDefault;
	global $sumOfInDays;
	global $sumOfTotalInDays;
	global $sumOfOutDays;
	global $sumOfTotalOutDays;
	global $sumOfDailyhrDays;
	global $sumOfTotalDailyhrDays;
	global $sumOfWeeklyhrDays;
	global $sumOfTotalWeeklyhrDays;
	$query='select * from `inout` WHERE `EmpID` ='.$empID.' AND 
			`Date` >= \''.getMonday($first).'\' AND `Date` <= \''.getSunday($last).'\';';
	$result=$db->query($query);
	$totInAfter=0;
	$totInAfterV=0;
	$totOutBefore=0;
	$totOutBeforeV=0;
	$totdayHr=0;
	$totdayHrV=0;
	$totweekHr=0;
	$totweekHrV=0;
	$subempinfoComp="";
	$empinfo="";
	global $count;
	$empinfo=$empinfo. '<tr><td width=180><u><span onclick="toggle(\'sub-'.$empID.'\');">';
	while ($row = mysql_fetch_assoc($result)) {
		$in = $row["First"];
		$out= $row["Last"];
		$dayHr1=timediffinHR($in,$out);
		if(strtotime($dayHr1) < strtotime("8:30:00")){
			$totdayHrV=$totdayHrV+1;	
		}else {
			$totdayHr=$totdayHr+1;	
		}
		if (strtotime($in) > strtotime($defaultIn)) {
			$totInAfterV=$totInAfterV+1;
		}else {
			$totInAfter=$totInAfter+1;
			
		}
		if (strtotime($defaultOut) > strtotime($out)) {
			$totOutBeforeV=$totOutBeforeV+1;
		} else {
			$totOutBefore=$totOutBefore+1;
			
		}
	}
	$wkst = getMonday($first);
	while ($w1 < date('Y-m-d',strtotime($last))) {
		$subempinfo= '<table class="table">';
		$subempinfo=$subempinfo. '<tr>
				<th class="both" >Day</th>
				<th class="both">In</th>
				<th  class="both">Out</th>
				<th  class="both">total Hr</th>
				<th  class="both">Type Of day</th>

			</tr>';
		$w1= date('Y-m-d', strtotime("$wkst 7 day"));
		$queryS='select * from `inout` WHERE `EmpID` ='.$empID.' AND `Date` >= \''.$wkst.'\' AND `Date` < \''.$w1.'\';';
		$result=$db->query($queryS);
		$tempwkst=$wkst;
		$wkst=$w1;
		if (mysql_num_rows($result) == 0) {
//			continue;
		}
		$tot="00:00:00";
		$flag=1;
		$dayCount=0;
		for ($j=0;$j<7;$j++){
			if ($flag){
				$row = mysql_fetch_assoc($result);
			}
			$curday=date('Y-m-d', strtotime("$tempwkst $j day"));
			if ($row["Date"] != $curday){
				
				//check whether the day is saturday or sunday
				$day=date('D,d M y', strtotime($curday));
				if (preg_match('/sun|sat/i',$day)){
					$flag=0;
					continue;
				}
				$subempinfo=$subempinfo. '<tr><td>'.$day.'</td>';
				
				// Check whether the leave type is On Site
				if (preg_match('/On Site/i',getDay($empID,$curday)))
                                {
                                        $subempinfo=$subempinfo. '<td>10:00:00</td>';
                                        $subempinfo=$subempinfo. '<td>18:30:00</td>';
                                        $subempinfo=$subempinfo. '<td>8:30:00</td>';
                                        $subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).' ('.getShiftforDay("WFH",$empID,$curday).')</td></tr>';
                                        $flag=0;
                                        $tot=timeAdd($tot,"8:30:00");
                                        $dayCount=$dayCount+1;
                                        $totInAfter=$totInAfter+1;
                                        $totOutBefore=$totOutBefore+1;
                                        $totdayHr=$totdayHr+1;
                                        continue;
                                }				


				//check whether the day is full day WFH
				if (preg_match('/WFH/i',getDay($empID,$curday)) && getShiftforDay("WFH",$empID,$curday)!="")  
                {
                		$subempinfo=$subempinfo. '<td>10:00:00</td>';
                		$subempinfo=$subempinfo. '<td>18:30:00</td>';
                		$subempinfo=$subempinfo. '<td>8:30:00</td>';
                   		$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).' ('.getShiftforDay("WFH",$empID,$curday).')</td></tr>';
                   		$flag=0;
                   		$tot=timeAdd($tot,"8:30:00");
                   		$dayCount=$dayCount+1;
                   		$totInAfter=$totInAfter+1;
                   		$totOutBefore=$totOutBefore+1;
                   		$totdayHr=$totdayHr+1;
                   		continue;
                }
                
                // Check whether the day is First Half-WFH & Second Half-HalfDay
				if (getDay($empID,$curday)=="First Half-WFH & Second Half-HalfDay" && getShiftforDay("First Half-WFH & Second Half-HalfDay",$empID,$curday)!="")  
                {
                		$subempinfo=$subempinfo. '<td>10:00:00</td>';
                		$subempinfo=$subempinfo. '<td>14:15:00</td>';
                		$subempinfo=$subempinfo. '<td>4:15:00</td>';
                   		$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
                   		$flag=0;
                   		$tot=timeAdd($tot,"4:15:00");
                   		$dayCount=$dayCount+0.5;
                   		$totInAfter=$totInAfter+1;
                   		$totOutBefore=$totOutBefore+1;
                   		$totdayHr=$totdayHr+1;
                   		continue;
                }
                
                // Check whether the day is "First Half-HalfDay & second Half-WFH"
				if (getDay($empID,$curday)=="First Half-HalfDay & second Half-WFH" && getShiftforDay("First Half-HalfDay & second Half-WFH",$empID,$curday)!="")  
                {
                		$subempinfo=$subempinfo. '<td>14:15:00</td>';
                		$subempinfo=$subempinfo. '<td>18:30:00</td>';
                		$subempinfo=$subempinfo. '<td>4:15:00</td>';
                   		$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
                   		$flag=0;
                   		$tot=timeAdd($tot,"4:15:00");
                   		$dayCount=$dayCount+0.5;
                   		$totInAfter=$totInAfter+1;
                   		$totOutBefore=$totOutBefore+1;
                   		$totdayHr=$totdayHr+1;
                   		continue;
                }
				$subempinfo=$subempinfo. '<td colspan=3>No Data</td>';
                $subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
               	$flag=0;
				continue;
			}
			
			// If the employee comes under the below days, count those days also
			if (isWeekend($row["Date"]) || isFulldayWFH($row["Date"],$empID) || isHoliday($row["Date"])|| isFullDayPTO($row["Date"],$empID)) {
		        	if (strtotime($row["First"]) > strtotime($defaultIn)) {
		                         $totInAfterV=$totInAfterV-1;
                		         $totInAfter=$totInAfter+1;
		                }
                		if (strtotime($defaultOut) > strtotime($row["Last"])) {
		                         $totOutBeforeV= $totOutBeforeV-1;
                		         $totOutBefore=$totOutBefore+1;
		                }
                		$diffTime=timediffinHR($row["First"],$row["Last"]);
                		if(strtotime($diffTime) < strtotime("8:30:00")){
                			$totdayHrV=$totdayHrV-1;
                			$totdayHr=$totdayHr+1;
                		}
				        $dayCount=$dayCount-1;
		    }
		    
			$dayHr=timediffinHR($row["First"],$row["Last"]);
			$tot=timeAdd($tot,$dayHr);
			$subempinfo=$subempinfo. '<tr><td>'.date('D,d-M-y', strtotime($row["Date"])).'</td>';
			$subempinfo=$subempinfo. '<td>';
			
			// If employee applies WFH, either fisr half / second half and decide what data goes to "FIRST" column
			if(isWFH($row['Date'], $empID) ) {
				$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"first",$subempinfo,"WFH");
				if(strtotime($dayHr)>strtotime("4:15:00")) {
					$totdayHr=$totdayHr+1;
					$totdayHrV=$totdayHrV-1;
				}
				if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="FIRSTHALF") {
					if (strtotime($row["First"]) > strtotime($halfDayDefault)) {
					} else {
						$totInAfterV=$totInAfterV-1;
						$totInAfter=$totInAfter+1;
					}
				}
				if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="SECONDHALF") {
					if (strtotime($row["First"]) > strtotime($defaultIn)) {
						$totInAfterV=$totInAfterV-1;
						$totInAfter=$totInAfter+1;
					}
				}
				$tot=timeAdd($tot,"4:15:00");
			} elseif(isHalfDayPTO($row['Date'], $empID) ) {
				// If employee applies Half Day PTO, either fisr half / second half and decide what data goes to "FIRST" column
				$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"first",$subempinfo,"HalfDay");
				if(strtotime($dayHr)>strtotime("4:15:00")) {
					$totdayHr=$totdayHr+1;
					$totdayHrV=$totdayHrV-1;
				}
				if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="FIRSTHALF") {
						if (strtotime($row["First"]) > strtotime($halfDayDefault)) {
						} else {
							$totInAfterV=$totInAfterV-1;
							$totInAfter=$totInAfter+1;
						}
				}
				if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="SECONDHALF") {
						if (strtotime($row["First"]) > strtotime($defaultIn)) {
							$totInAfterV=$totInAfterV-1;
							$totInAfter=$totInAfter+1;
						} 
				}
			} else {
					if (strtotime($row["First"]) > strtotime($defaultIn)) {
						if (isWeekend($row["Date"]) || isHoliday($row["Date"]) || isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID)) {
							$subempinfo=$subempinfo. $row["First"];
						} else {
	  						$subempinfo=$subempinfo. '<font color=red>'.$row["First"].'</font>';
						}
					} else {
						$subempinfo=$subempinfo. $row["First"];
					}
			}
		 
			$subempinfo=$subempinfo. '</td>';
			$subempinfo=$subempinfo. '<td>';
			
			// If employee applies WFH, either fisr half / second half and decide what data goes to "LAST" column
			if(isWFH($row['Date'], $empID) ) {
				$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"last",$subempinfo,"WFH");
				if (strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="FIRSTHALF") {
					if (strtotime($defaultOut) > strtotime($row["Last"])) {
							$totOutBeforeV=$totOutBeforeV-1;
							$totOutBefore=$totOutBefore+1;
					} 
				}
				if (strtoupper(getShiftforDay("WFH",$empID,$row['Date'])) =="SECONDHALF") {
					if (strtotime($halfDayDefault) > strtotime($row["Last"])) {
					} else {
							$totOutBeforeV=$totOutBeforeV-1;
							$totOutBefore=$totOutBefore+1;
					}
				}
			} elseif(isHalfDayPTO($row['Date'], $empID) ) {
				// If employee applies Half Day PTO, either fisr half / second half and decide what data goes to "LAST" column
				$subempinfo=getHalfDayShift($row,$empID,$row['Date'],"last",$subempinfo,"HalfDay");
				if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date']))=="FIRSTHALF") {
					if (strtotime($defaultOut) > strtotime($row["Last"])) {
							$totOutBeforeV=$totOutBeforeV-1;
							$totOutBefore=$totOutBefore+1;
					} 
				}
				if (strtoupper(getShiftforDay("HalfDay",$empID,$row['Date'])) =="SECONDHALF") {
					if (strtotime($halfDayDefault) > strtotime($row["Last"])) {
					} else {
							$totOutBeforeV=$totOutBeforeV-1;
							$totOutBefore=$totOutBefore+1;
					}
				}
			} else {
					if (strtotime($defaultOut) > strtotime($row["Last"])) {
						if (isWeekend($row["Date"]) || isHoliday($row["Date"])|| isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID)) {
							$subempinfo=$subempinfo. $row["Last"];
						} else {
							$subempinfo=$subempinfo. '<font color=red>'.$row["Last"].'</font>';
						}
					} else {
						$subempinfo=$subempinfo. $row["Last"];
					}
			}
			$subempinfo=$subempinfo. '</td>';
			
			// Get the defaultdayHr based on halfday or fullday or First Half or Second Half WFH
			if(isWFH($row['Date'], $empID)) {
				if (strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="FIRSTHALF" || strtoupper(getShiftforDay("WFH",$empID,$row['Date']))=="SECONDHALF") {
					$dayHr=timeAdd($dayHr,"4:15:00");
				}
				$defaultdayHr=strtotime("8:30:00");
			} elseif(isHalfDayPTO($row['Date'], $empID) ) {
				$dayCount=$dayCount-0.5;
				$defaultdayHr=strtotime("4:15:00");
			} else {
				$defaultdayHr=strtotime("8:30:00");
			}
			
			// Get the data for the totaldayHr column
			if(strtotime($dayHr) < $defaultdayHr){
            	if (isWeekend($row["Date"]) || isHoliday($row["Date"])|| isFullDayPTO($row["Date"],$empID) || isFulldayWFH($row["Date"],$empID)) {
                       $subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
                } else {
                       $subempinfo=$subempinfo. '<td><font color=red>'.$dayHr.'</font></td>';
                }
            } else {
                $subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
            }
            
            // Get the data for the LeaveType column
			if(getDay($empID,$curday)=="No Data") {
                  $subempinfo=$subempinfo. '<td>'.$row['TypeOfDay'].'</td></tr>';
            } else {
             	  $subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
            }
            
            // Check whether the employee came to office on optional holiday
			if(isHoliday($row["Date"])) {
				if(isOptionalHoliday($row["Date"])) {
					if(!isOptionalHolidayApplied($row["Date"],$empID)) {
						$dayCount=$dayCount+1;
					}
				}
			}
			$dayCount=$dayCount+1;
			$flag=1;
		}
		
		# Even if emp comes on saturday/sunday dayCount should not be more than 5.
		if($dayCount>=5) {
			$dayCount=5;
		}
		
		// Get the total Hr's emp came to office per week
		if (getTotalHRDiff($tot,$dayCount)) {
			$subempinfo=$subempinfo. '<tr><td colspan=3 align="right"><font color=red >Total Hr</font></td>';
			$subempinfo=$subempinfo. '<td ><font color=red>'.$tot.'</font></td><td></td></tr>';
			$totweekHrV=$totweekHrV+1;	
		} else {
			$subempinfo=$subempinfo. '<tr><td colspan=3 align="right">Total Hr</td>';
			$subempinfo=$subempinfo. '<td >'.$tot.'</td><td></td></tr>';
			$totweekHr=$totweekHr+1;	
		}
		$subempinfo=$subempinfo. '</table>';	
		$subempinfoComp=$subempinfoComp. $subempinfo;
		$subempinfo="";
	}
		$empinfo=$empinfo. $empName.'</span></u></td>';
        $empinfo=$empinfo. '<td>';
        if ($P10to4 == "NOTMEET") {
        		$empinfo=$empinfo.getColorofRow($totInAfterV,($totInAfterV+$totInAfter));
                $sumOfInDays=$sumOfInDays+$totInAfterV;
                $sumOfTotalInDays=$sumOfTotalInDays+$totInAfterV+$totInAfter;
        } else {
        		$empinfo=$empinfo.getColorofRow($totInAfter,($totInAfterV+$totInAfter));
        		$sumOfInDays=$sumOfInDays+$totInAfter;
                $sumOfTotalInDays=$sumOfTotalInDays+$totInAfterV+$totInAfter;
        }
        $empinfo=$empinfo. '</td><td>';
        if($P10to4 == "NOTMEET") {
        		$empinfo=$empinfo.getColorofRow($totOutBeforeV,($totOutBefore+$totOutBeforeV));
        		$sumOfOutDays=$sumOfOutDays+$totOutBeforeV;
                $sumOfTotalOutDays=$sumOfTotalOutDays+$totOutBefore+$totOutBeforeV;
        } else {
        	$empinfo=$empinfo.getColorofRow($totOutBefore,($totOutBefore+$totOutBeforeV));
        		$sumOfOutDays=$sumOfOutDays+$totOutBefore;
                $sumOfTotalOutDays=$sumOfTotalOutDays+$totOutBefore+$totOutBeforeV;
        }
        $empinfo=$empinfo. '</td><td>';
        if ($hr9 == "NOTMEET") {
        		$empinfo=$empinfo.getColorofRow($totdayHrV,($totdayHr+$totdayHrV));
        		$sumOfDailyhrDays=$sumOfDailyhrDays+$totdayHrV;
                $sumOfTotalDailyhrDays=$sumOfTotalDailyhrDays+$totdayHr+$totdayHrV;
        } else {
        		$empinfo=$empinfo.getColorofRow($totdayHr,($totdayHr+$totdayHrV));
        		$sumOfDailyhrDays=$sumOfDailyhrDays+$totdayHr;
                $sumOfTotalDailyhrDays=$sumOfTotalDailyhrDays+$totdayHr+$totdayHrV;
        }
		$empinfo=$empinfo. '</td><td>';
		if ($hr45 == "NOTMEET") { 
				$empinfo=$empinfo.getColorofRow($totweekHrV,($totweekHr+$totweekHrV));
				$sumOfWeeklyhrDays=$sumOfWeeklyhrDays+$totweekHrV;
				$sumOfTotalWeeklyhrDays=$sumOfTotalWeeklyhrDays+$totweekHr+$totweekHrV;
		} else {
				$empinfo=$empinfo.getColorofRow($totweekHr,($totweekHr+$totweekHrV));
				$sumOfWeeklyhrDays=$sumOfWeeklyhrDays+$totweekHr;
				$sumOfTotalWeeklyhrDays=$sumOfTotalWeeklyhrDays+$totweekHr+$totweekHrV;
		}
	$empinfo=$empinfo. '</td><td>';
	$count=$count+ 1;
	$empinfo=$empinfo."<p><a href='javascript:void(null);' 
			onclick='showDialog(\"".$count."\",\"".$empID."\",\"".$first."\",\"".$last."\");'>Open</a></p>
			<div id='dialog-modal".$count."' title='Access Detail Graph' style='display: none;'></div>";	
	$empinfo=$empinfo. '</td></tr>';
	$empinfo=$empinfo. '<tr style="display:none;" sub-'.$empID.'="att" ><td colspan="5">';
	echo $empinfo;
	echo $subempinfoComp;
	echo '</td></tr>';
}

function getMonday ($givenDate) {
	$off= 1 - date('w', strtotime($givenDate));
	return date('Y-m-d', strtotime("$givenDate $off day"));
}

function getSunday($givenDate) {
	$off= 7 - date('w', strtotime($givenDate));
        return date('Y-m-d', strtotime("$givenDate $off day"));

}

function createTableHeader($P10to4, $hr9, $hr45) {
	echo '
				<table class="table">
					<tr>
						<th  class="both">Name</th>
						<th  class="both">No of days';
	if ($P10to4 == "NOTMEET") {
		echo ' after ';
	} else {
		echo ' before ';
	}
	echo '10 AM</th><th  class="both">No of days';
	if ($P10to4 == "NOTMEET") {
		echo ' before ';
	} else {
		echo ' after ';
	}	
	echo '4 PM</th><th  class="both">No of Daily Hr-';
	echo $hr9;
	echo '</th><th  class="both">No of Weekly Hr-';
	echo $hr45;
	echo ' </th><th  class="both"> Graph</th></tr>';
}

function getTeamUntrackedPercentage() {
	global $untrackedLeaves;
	If($untrackedLeaves!=0) {
		echo "<table class='table'><tr>
				<td><b>Total Untracked Leaves</b>
				<td class='teamUntrackedLeaves'><b>".$untrackedLeaves."</b></td>
			</tr></table>";
	}
}

function setUntrackedTeamPercentageNull() {
	global $untrackedLeaves;
	$untrackedLeaves=0;
}

function getInformationForApprovedLeaves($empId,$empName,$presentDay,$db) {
	
	# If information is present in perdaytransaction table, then check whether employee applied for half day i.e count of leaves is 0.5
        # and not half day WFH.
        $checkInPerDayTransacrionTableForHalfDayPTOApplied= $db->query("SELECT * FROM `perdaytransactions` WHERE empid='".$empId."' and date='".$presentDay."' and status = 'Approved' and count='0.5' and leavetype='HalfDay'");
       if($db->countRows($checkInPerDayTransacrionTableForHalfDayPTOApplied)>0) {
          $checkInOutforDataExists= $db->query("SELECT * FROM `inout` WHERE empid='".$empId."' and Date='".$presentDay."'");
          if($db->countRows($checkInOutforDataExists)>0) {
             $updateQuery="update `inout` set `state`='Half Day PTO not applied' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
             $updateQueryResult=$db->query($updateQuery);
             echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Half Day PTO not applied (HalfDay)\n";
          } else {
             $insertQuery="insert into `inout`(EmpID,EmpName,Department,Type,Date,First,Last,added_hrname,state) values('".$empId."','".$empName."','".$empDept."','".$empDept."','".$presentDay."','-','-','trackAttendence script','Half Day PTO not applied')";
             $insertResult=$db->query($insertQuery);
             echo "For a date: $presentDay and empid: $empId and emp: $empName ---> employee applied only half day PTO. Remaining half day he didnt applied\n";
          }
       } else {
	# Check whether the approved leave is of type "WFH", if yes, then check whether the row is present in inout table
	$checkInPerDayTransacrionTableForHalfDayPTOApplied= $db->query("SELECT * FROM `perdaytransactions` WHERE empid='".$empId."' and date='".$presentDay."' and status = 'Approved' and leavetype='WFH' and (shift='FirstHalf' or shift='SecondHalf')");
        if($db->countRows($checkInPerDayTransacrionTableForHalfDayPTOApplied)>0) {
            $checkInOutforDataExists= $db->query("SELECT * FROM `inout` WHERE empid='".$empId."' and Date='".$presentDay."'");
            if($db->countRows($checkInOutforDataExists)>0) {
               $updateQuery="update `inout` set `state`='Half Day PTO not applied' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
               $updateQueryResult=$db->query($updateQuery);
               echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Half Day PTO not applied (WFH)\n";
            } else {
                 $insertQuery="insert into `inout`(EmpID,EmpName,Department,Type,Date,First,Last,added_hrname,state) values('".$empId."','".$empName."','".$empDept."','".$empDept."','".$presentDay."','-','-','trackAttendence script','Half Day PTO not applied')";
                 $insertResult=$db->query($insertQuery);
                 echo "For a date: $presentDay and empid: $empId and emp: $empName ---> employee applied only half day PTO. Remaining half day he didnt applied";
            }
         } else {
		# Delete the row in inout table, as the employee took one of the following:
                # 1. Full Day PTO
                # 2. Optional Holiday Applied	
		# 3. Special Leave Applied
                # 4. On Site
                $checkInOutforDataExists= $db->query("SELECT * FROM `inout` WHERE empid='".$empId."' and Date='".$presentDay."'");
                if($db->countRows($checkInOutforDataExists)>0) {
	                $deleteRowInOut="DELETE FROM `inout` WHERE `EmpID`='".$empId."' and `Date`='".$presentDay."'";
                        $deleteRowInOutResult=$db->query($deleteRowInOut);
                        echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Deleting row from inout table\n";
                }
          }
     }
}

function weeklyTrackAttendence($result,$fromDate, $toDate, $db) {
	while ($row = mysql_fetch_assoc($result)) {
        	$empName=$row['empname'];
        	$empId=$row['empid'];
        	$empDept=$row['dept'];
        	$empManager=$row['managername'];
        	$empJoiningDate=$row['joiningdate'];

		echo "**************************** For an Employee: $empName *******************************\n";	
        	### If Employee joined after $fromDate, then set fromDate to Employee joining date
        	if (strtotime($empJoiningDate) > strtotime($fromDate)) {
        	        $fromDate=$empJoiningDate;
        	}
		
        	### Getting untracked attendence from fromdate to toDate
        	$presentDay=$fromDate;
        	$todayDate=date("Y-m-d");
        	while (strtotime($presentDay) <= strtotime($toDate)) {
        			if(isWeekend($presentDay) || isHoliday($presentDay)) {
        				$presentDay = date ("Y-m-d", strtotime("+1 day", strtotime($presentDay)));
        				continue;
        			}
        		    		
        	         ### Skip  today date
        	         if (strcmp($todayDate,$presentDay)==0) {
        	                break;
        	         }

        	         $checkInOutforDataExists= $db->query("SELECT * FROM `inout` WHERE empid='".$empId."' and Date='".$presentDay."' and state!='No Data'");
        	         if($db->countRows($checkInOutforDataExists)>0) {
        	         	
        	         	# If the day is his birthday, and he came to office, update inout with state as "Data exists"
        	         	if(isBirthday($empId,$presentDay)) {
					 # Update inout table, if no information is presentin perdaytransaction table
                                         $checkInOutforDataExists= $db->query("SELECT * FROM `inout` WHERE empid='".$empId."' and Date='".$presentDay."'");
                                         if($db->countRows($checkInOutforDataExists)>0) {
                                         	$row = $db->fetchArray($checkInOutforDataExists);
                                         	if($row['First']=="00:00:00") {
                                         		$deleteRowInOut="DELETE FROM `inout` WHERE `EmpID`='".$empId."' and `Date`='".$presentDay."'";
                                                	$deleteRowInOutResult=$db->query($deleteRowInOut);
                                    	            echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Deleted the row in inout table (Birthday)\n";
                                        	 } else {
        	         				$updateQuery="update `inout` set `state`='Data Exists' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
        	      		   			$updateQueryResult=$db->query($updateQuery);
        	         				echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Data Exists\n";
						}
					}
        	         		$presentDay = date ("Y-m-d", strtotime("+1 day", strtotime($presentDay)));
        	         		continue;
        	         	}	
        	         	
        	         	# If any data is available for that employee and for that day, check the below conditions.
        	         	
        	         	# Condition 1:  Check whether his total working hour for that day is less than 3:30 hr. 
        	         					# If yes, update inout table, that he didnt applied half day PTO for that day.
        	         					# else check in perdaytransaction table that for that day and for that employee the "status" is updated.
           	
        	         	$row=$db->fetchAssoc($checkInOutforDataExists);
        	         	$in = $row["First"];
        	         	$out= $row["Last"];
        	         	$dayHr1=timediffinHR($in,$out);
        	         	if(strtotime($dayHr1) < strtotime("03:30:00")) {
        	         		# Check in perdaytransaction table, whether the employee applied leave and whether the status is "Approved"
        	         		$checkInPerDayTransacrionTableForStatus= $db->query("SELECT * FROM `perdaytransactions` WHERE empid='".$empId."' and date='".$presentDay."' and status ='Approved'");
        	         		if($db->countRows($checkInPerDayTransacrionTableForStatus)==0) {
        	         			# If there is no information in perdaytransaction table, then employee didnt applied leave in LMS. So, update
        	         			# inout table with state as "Half Day PTO not applied"
        	         			$updateInOutTableWithHalfDayNotApplied="update `inout` set `state`='Half Day PTO not applied' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
        	         			$updateQueryResult=$db->query($updateInOutTableWithHalfDayNotApplied);
        	         			echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Half day pto not applied\n";
        	         		}
        	         	} elseif(strtotime($dayHr1) > strtotime("15:00:00")) {
					# Condition 2:  Check whether his total working hour for that day is more than 15:00 hr.
                                        # If yes, update inout table with state as 'Swipe Issues"
        	         		$updateInOutTableWithSwipeIssues="update `inout` set `state`='Swipe issues' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
        	         		$updateQueryResult=$db->query($updateQuery);
        	         		echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Swipe issues\n";
        	         	} else {
					$checkInOutforDataExists= $db->query("SELECT * FROM `inout` WHERE empid='".$empId."' and Date='".$presentDay."' and state!='Data Exists'");
                                        if($db->countRows($checkInOutforDataExists)>0) {

						$updateInOutTableWithSwipeIssues="update `inout` set `state`='Data Exists' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
                                        	$updateQueryResult=$db->query($updateInOutTableWithSwipeIssues);
                                        	echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Data Exists\n";
					}
				}
		
				# Check in perdaytransaction table, whether the employee applied leave and whetheer the status is "Pending"
				$checkInPerDayTransacrionTableForStatus= $db->query("SELECT * FROM `perdaytransactions` WHERE empid='".$empId."' and date='".$presentDay."' and status = 'Pending'");
				if($db->countRows($checkInPerDayTransacrionTableForStatus)>0) {
					# If there is no information in perdaytransaction table, then employee didnt applied leave in LMS. So, update
					# inout table with state as "Half Day PTO not applied"
					$updateInOutTableWithHalfDayNotApplied="update `inout` set `state`='Leave is pending for approval' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
					$updateQueryResult=$db->query($updateInOutTableWithHalfDayNotApplied);
					echo "For a date: $presentDay and empid: $empId and emp: $empName ---> 'Leave is pending for approval'\n";
				}
						
				# check if leave is approved
				$checkInPerdayTransactionTableForStatus= $db->query("SELECT * FROM `perdaytransactions` WHERE empid='".$empId."' and date='".$presentDay."' and status='Approved'");
				if($db->countRows($checkInPerdayTransactionTableForStatus)>0) {
					$flag=0;
					# Update inout table, if no information is presentin perdaytransaction table
					$checkInOutforDataExists= $db->query("SELECT * FROM `inout` WHERE empid='".$empId."' and Date='".$presentDay."'");
					if($db->countRows($checkInOutforDataExists)>0) {
						$row = $db->fetchArray($checkInOutforDataExists);
						if($row['First']=="00:00:00") {
							$deleteRowInOut="DELETE FROM `inout` WHERE `EmpID`='".$empId."' and `Date`='".$presentDay."'";
                                                           	$deleteRowInOutResult=$db->query($deleteRowInOut);				
									echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Deleted the row in inout table\n";
									$flag=1;
								} else {
								   $updateQuery="update `inout` set `state`='Data Exists' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
								   $updateQueryResult=$db->query($updateQuery);
								   echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Data Exists\n";
								}
							} 
							if($flag=1 && $row['First']=="00:00:00" && $row['state']=="Half Day PTO not applied") {
								getInformationForApprovedLeaves($empId,$empName,$presentDay,$db);
							}
						}
        	         	
        	         } else {
				# Check condition for inout data update
				$checkInOutforDataExist= $db->query("SELECT * FROM `inout` WHERE empid='".$empId."' and Date='".$presentDay."'");
				$dayHr1="00:00:00";
				if($db->countRows($checkInOutforDataExist)>0) {

					$row=$db->fetchAssoc($checkInOutforDataExist);
                                	$in = $row["First"];
                                	$out= $row["Last"];
                                	$dayHr1=timediffinHR($in,$out);
				}
                                if(strtotime($dayHr1) > strtotime("03:30:00") && strtotime($dayHr1) < strtotime("15:00:00")) {
						$updateQuery="update `inout` set `state`='Data Exists' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
                                        	$updateQueryResult=$db->query($updateQuery);	
						echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Data Exists\n";
				} else { 
					
        	         		# If the day is his birthday, and he came to office, Continue to next day
       	 		         	if(isBirthday($empId,$presentDay)) {
						$checkBirthday=$db->query("SELECT * FROM `inout` WHERE empid='".$empId."' and date='".$presentDay."'");
						if($db->countRows($checkBirthday)>0) {
							$updateQuery="update `inout` set `state`='Data Exists' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
							$updateQueryResult=$db->query($updateQuery);
							echo "For a date: $presentDay and empid: $empId and emp: $empName ---> Data Exists\n";
						}
        	         			$presentDay = date ("Y-m-d", strtotime("+1 day", strtotime($presentDay)));
        	         			continue;
        	         		}
        	         	
        	         		# If no data is available for that employee in the inout table, then check the below conditions
        	         		
        	         		# Condition 1:  Check if any information is present in perdaytransaction table 
					# with status = "Approved" for that day for that employee
        	         		# If no information, insert into inout table with state as 'No Data"
        	         	
        	         		$checkInPerdayTransactionTableForStatus= $db->query("SELECT * FROM `perdaytransactions` WHERE empid='".$empId."' and date='".$presentDay."' and status='Approved'");
        	         		if($db->countRows($checkInPerdayTransactionTableForStatus)==0) {
        	         			# Update inout table, if no information is presentin perdaytransaction table
        	         			$checkInOutforDataExists= $db->query("SELECT * FROM `inout` WHERE empid='".$empId."' and Date='".$presentDay."'");
        	         			if($db->countRows($checkInOutforDataExists)>0) {
        	         				$updateQuery="update `inout` set `state`='No Data' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
        	         				$updateQueryResult=$db->query($updateQuery);
        	         				echo "For a date: $presentDay and empid: $empId and emp: $empName ---> No Data\n";
        	         			} else {
        	         				$insertQuery="insert into `inout`(EmpID,EmpName,Department,Type,Date,First,Last,added_hrname,state) values('".$empId."','".$empName."','".$empDept."','".$empDept."','".$presentDay."','-','-','trackAttendence script','No Data')";
        	         				$insertResult=$db->query($insertQuery);
        	         				echo "For a date: $presentDay and empid: $empId and emp: $empName ---> No data\n";
        	         			}
        	         		} else {
						getInformationForApprovedLeaves($empId,$empName,$presentDay,$db);
        	         		}
        	         		
        	         		# Check whether the employee applied leave but the status is in "Pending" state. 
					# In that case, update inout table
        	         		# with state as "Leave is pending for approval"
        	         		$checkInPerdayTransactionTableForStatus= $db->query("SELECT * FROM `perdaytransactions` WHERE empid='".$empId."' and date='".$presentDay."' and status = 'Pending'");
        	         		if($db->countRows($checkInPerdayTransactionTableForStatus) > 0) {
        	         			$updateQuery="update `inout` set `state`='Leave is pending for approval' where `EmpID`='".$empId."' and `Date`='".$presentDay."'";
        	         			$updateQueryResult=$db->query($updateQuery);
        	         			echo "For a date: $presentDay  and empid: $empId and emp: $empName ---> Leave is pending for approval\n";
        	         		}
        	        	}
			}
        	        $presentDay = date ("Y-m-d", strtotime("+1 day", strtotime($presentDay)));
		}
		echo "**************************** End for an Employee: $empName *******************************\n";
	}
}

function trackAttendence($result,$fromDate, $toDate, $db) {
	global $untrackedLeaves;
	### get the information	
	echo "<table class='table table-bordered table-hover trackData'>
                        <tr class='info'>
                                <th>Emp Id</th>
                                <th>Emp Name</th>
                                <th>Department</th>
                                <th>Manager</th>
                                <th>No of Days (Untracked)</th>
                                <th></th>
                        </tr>";
	while ($row = mysql_fetch_assoc($result)) {
		$count=0;
		$empInnerTable="<tr>
        	<td colspan='6'>
            	<table class='table table-hover table-bordered'>
                	<thead>
                    	<tr class='info'>
                        	<th>Date</th>
                          	<th>In Time</th>
                         	<th>Out Time</th>
                          	<th>Time Difference</th>
                          	<th>Reason</th>
                       	</tr>
                    </thead><tbody>";
	 	$empName=$row['empname'];
                $empId=$row['empid'];
                $empDept=$row['dept'];
                $empManager=$row['managername'];	
		$inOutQuery="SELECT * FROM `inout` where `Date` between '$fromDate' and '$toDate' and `EmpID`='$empId' and `state` != 'Data Exists'";
	        $inOutResult=$db -> query($inOutQuery);
		$foundEntry=0;
		while ($inOutRow=mysql_fetch_assoc($inOutResult)) {
			$dateEmp=$inOutRow['Date'];
	                $inTime=$inOutRow['First'];
        	        $outTime=$inOutRow['Last'];
                	$diffTime=timediffinHR($inTime,$outTime);
	                $reason=$inOutRow['state'];
			$empInnerTable=$empInnerTable. "<tr></tr><tr>
                              	<td>$dateEmp</td>
                                <td>$inTime</td>
                                <td>$outTime</td>
                                <td>$diffTime</td>
                                <td>$reason</td>
                             </tr>";
			$count=$count+1;	
		}
		$empInnerTable= $empInnerTable."</tbody></table>
		      </td> </tr><tr></tr>";
		$empOuterTable= "<tr>
                     <td>$empId</td>
                     <td>$empName</td>
                     <td>$empDept</td>
                     <td>$empManager</td>
                     <td>$count days</td>
                     <td><div class='arrow'></div></td>
             	 </tr>";
	 	echo $empOuterTable;
        echo $empInnerTable;
        $untrackedLeaves=$untrackedLeaves+$count;
	}
	echo "<tr></tr></table>";
			
			/*echo "<script>
				$(document).ready(function() {
				    $('.arrow').click(function(){
				    		$('.arrow-down').hide();
				            $(this).find('.arrow-up, .arrow-down').toggle();
				    });
				});
			
			</script>";*/
}
?>

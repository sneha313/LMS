<?php
session_start();
require_once 'Library.php';
error_reporting("E_ALL");
?>
<?php
$dayhours=8;
$db=connectToDB();
$defaultIn='10:00:00';
$defaultOut='16:00:00';
$mailBody="";
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
function getMonday ($givenDate) {
        $off= 1 - date('w', strtotime($givenDate));
        return date('Y-m-d', strtotime("$givenDate $off day"));
}
function getSunday($givenDate) {
        $off= 7 - date('w', strtotime($givenDate));
        return date('Y-m-d', strtotime("$givenDate $off day"));

}
function getDay($empid,$date) {
        global $db;
        $query="select transactionid,leavetype from `perdaytransactions` WHERE `empid` ='".$empid."' AND 
                       `date`='".$date."'";
        $result=$db->query($query);
        $row = $db->fetchArray($result);
        $statusQuery="select approvalstatus from `empleavetransactions` WHERE `transactionid` ='".$row['transactionid']."'";
        $statusresult=$db->query($statusQuery);
        $statusRow = $db->fetchArray($statusresult);
        if($statusRow['approvalstatus']=="Approved") {
                if ($row['leavetype']=='WFH') {
                        return $row['leavetype'];
                }
                if ($row['leavetype']) {
                        $retunVal=$row['leavetype']." PTO";
                        return $retunVal;
                }
        }
        #Check whether holiday is present on that day 
        $getHoliday="select holidayname from holidaylist where date='".$date."'";
        $holidayResult=$db->query($getHoliday);
        if($holidayResult) {
                $holidayRow = $db->fetchArray($holidayResult);
                if ($holidayRow['holidayname']) {
                        return $holidayRow['holidayname'];
                }  else {
                        return "No Data";
                }
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

function getTotalHRDiff($tot,$dayCount) {
	$totalWorkingHRsPerWeek=0;
	$perDayWorkingHrs="8:30:00";
	list($H,$M,$S)=explode(":",$perDayWorkingHrs);
	$H=$H*$dayCount;
	$M=$M*$dayCount;
	$extraHRS=$M/60.0;
	if ($extraHRS>=1) {
		$val=floor($extraHRS);
		if (is_integer($extraHRS)) {
			$m=00;
			$H=$H+$val;
			$totalWorkingHRsPerWeek="$H:$m:$S";
		} 
		if (is_double($extraHRS)) {
			$m=30;
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
function getDataForEMP($empID,$empName,$first,$last,$P10to4,$hr9,$hr45) {
	global $defaultIn, $defaultOut,$db,$mailBody;
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
	$subempinfoComp='<table border="4"><tr>';
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
		$subempinfo= '<td><table border="3">';
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
			continue;
		}
		$tot="00:00:00";
		$flag=1;
		$flag_1=0;
		$dayCount=0;
		for ($j=0;$j<7;$j++) {
			if ($flag){
				$row = mysql_fetch_assoc($result);
			}
			$curday=date('Y-m-d', strtotime("$tempwkst $j day"));
			if ($row["Date"] != $curday){
				$day=date('D,d M y', strtotime($curday));
				if (preg_match('/sun|sat/i',$day)){
					$flag=0;
					continue;
				}
				$subempinfo=$subempinfo. '<tr><td>'.$day.'</td>';
				$subempinfo=$subempinfo. '<td colspan=2>No Data</td>';
				$subempinfo=$subempinfo. '<td colspan=2>'.getDay($empID,$curday).'</td></tr>';
				$flag=0;
				continue;
			}
			if (isWeekend($row["Date"])) {
				if (strtotime($row["First"]) > strtotime($defaultIn)) {
					$totInAfterV=$totInAfterV-1;
					$totInAfter=$totInAfter+1;
				}
				if (strtotime($defaultOut) > strtotime($row["Last"])) {
					$totOutBeforeV= $totOutBeforeV-1;
					$totOutBefore=$totOutBefore+1;
				}
				$diffTime=timediffinHR($row["First"],$row["Last"]);
				list($H,$m,$s)=explode(":",$diffTime);
				if($H < 8) {
					$totdayHrV=$totdayHrV-1;
					$totdayHr=$totdayHr+1;
				}
				$dayCount=$dayCount-1;
			} 
			$dayHr=timediffinHR($row["First"],$row["Last"]);
			$tot=timeAdd($tot,$dayHr);
			$subempinfo=$subempinfo. '<tr><td>'.date('D,d-M-y', strtotime($row["Date"])).'</td>';
			$subempinfo=$subempinfo. '<td>';
			if (strtotime($row["First"]) > strtotime($defaultIn)) {
				if (isWeekend($row["Date"])) {
					$subempinfo=$subempinfo. $row["First"];
				} else {
	  				$subempinfo=$subempinfo. '<font color=red>'.$row["First"].'</font>';
				}
			} else {
				$subempinfo=$subempinfo. $row["First"];
			}
			$subempinfo=$subempinfo. '</td>';
			$subempinfo=$subempinfo. '<td>';
			if (strtotime($defaultOut) > strtotime($row["Last"])) {
				if (isWeekend($row["Date"])) {
					$subempinfo=$subempinfo. $row["Last"];
				} else {
					$subempinfo=$subempinfo. '<font color=red>'.$row["Last"].'</font>';
				}
			} else {
				$subempinfo=$subempinfo. $row["Last"];
			}
			$subempinfo=$subempinfo. '</td>';
			if(strtotime($dayHr) < strtotime("8:30:00")){
				if (isWeekend($row["Date"])) {
					$subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
				} else {
					$subempinfo=$subempinfo. '<td><font color=red>'.$dayHr.'</font></td>';
				}
			} else {
				$subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
			}
			if(getDay($empID,$curday)=="No Data") {
				$subempinfo=$subempinfo. '<td>'.$row['TypeOfDay'].'</td></tr>';
			} else {
				$subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
			}
		$dayCount=$dayCount+1;
		$flag=1;
	}
	#Even if emp comes on saturday/sunday dayCount should not be more than 5.
	if($dayCount>=5) {
		$dayCount=5;
	}
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
	$subempinfoComp=$subempinfoComp. '</td></tr><tr>';
	$subempinfo="";
	}
	$mailBody.=$subempinfoComp.'</table>';
	$mailBody.'</td></tr>';
	return $mailBody;
}

function getDataForEMP1($empID,$empName,$first,$last,$P10to4,$hr9,$hr45) {
	global $defaultIn, $defaultOut,$db,$mailBody;
	$mailBody="";
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
	$empinfo=$empinfo. '<tr><td><u>';
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
		$subempinfo= '<td colspan="5"><table border="3">';
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
			continue;
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
				$day=date('D,d M y', strtotime($curday));
				if (preg_match('/sun|sat/i',$day)){
					$flag=0;
					continue;
				}
				$subempinfo=$subempinfo. '<tr><td>'.$day.'</td>';
				$subempinfo=$subempinfo. '<td colspan=2>No Data</td>';
                                $subempinfo=$subempinfo. '<td colspan=2>'.getDay($empID,$curday).'</td></tr>';
				$flag=0;
				continue;
			}
			if (isWeekend($row["Date"])) {
                                if (strtotime($row["First"]) > strtotime($defaultIn)) {
                                        $totInAfterV=$totInAfterV-1;
                                        $totInAfter=$totInAfter+1;
                                }
                                if (strtotime($defaultOut) > strtotime($row["Last"])) {
                                        $totOutBeforeV= $totOutBeforeV-1;
                                        $totOutBefore=$totOutBefore+1;
                                }
                                $diffTime=timediffinHR($row["First"],$row["Last"]);
                                list($H,$m,$s)=explode(":",$diffTime);
                                if($H < 8) {
                                        $totdayHrV=$totdayHrV-1;
                                        $totdayHr=$totdayHr+1;
                                }
                                $dayCount=$dayCount-1;
                        }
			$dayHr=timediffinHR($row["First"],$row["Last"]);
			$tot=timeAdd($tot,$dayHr);
			$subempinfo=$subempinfo. '<tr><td>'.date('D,d-M-y', strtotime($row["Date"])).'</td>';
			$subempinfo=$subempinfo. '<td>';
			if (strtotime($row["First"]) > strtotime($defaultIn)) {
                                if (isWeekend($row["Date"])) {
                                        $subempinfo=$subempinfo. $row["First"];
                                } else {
                                        $subempinfo=$subempinfo. '<font color=red>'.$row["First"].'</font>';
                                }
                        } else {
                                $subempinfo=$subempinfo. $row["First"];
                        }
			$subempinfo=$subempinfo. '</td>';
			$subempinfo=$subempinfo. '<td>';
			if (strtotime($defaultOut) > strtotime($row["Last"])) {
                                if (isWeekend($row["Date"])) {
                                        $subempinfo=$subempinfo. $row["Last"];
                                } else {
                                        $subempinfo=$subempinfo. '<font color=red>'.$row["Last"].'</font>';
                                }
                        } else {
                                $subempinfo=$subempinfo. $row["Last"];
                        }
			$subempinfo=$subempinfo. '</td>';
			if(strtotime($dayHr) < strtotime("8:30:00")){
                                if (isWeekend($row["Date"])) {
                                        $subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
                                } else {
                                        $subempinfo=$subempinfo. '<td><font color=red>'.$dayHr.'</font></td>';
                                }
                        } else {
                                $subempinfo=$subempinfo. '<td>'.$dayHr.'</td>';
                        }
			if(getDay($empID,$curday)=="No Data") {
                                $subempinfo=$subempinfo. '<td>'.$row['TypeOfDay'].'</td></tr>';
                        } else {
                                $subempinfo=$subempinfo. '<td>'.getDay($empID,$curday).'</td></tr>';
                        }
			$dayCount=$dayCount+1;
			$flag=1;
		}
		if($dayCount>=5) {
			$dayCount=5;
		}
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
		$subempinfoComp=$subempinfoComp. '</td></tr><tr>';
		$subempinfo="";
	}
	$empinfo=$empinfo. $empName.'</span></u></td>';
        $empinfo=$empinfo. '<td>';
        if ($P10to4 == "NOTMEET") {
                $empinfo=$empinfo. $totInAfterV."/".($totInAfterV+$totInAfter);
        } else {
                $empinfo=$empinfo. $totInAfter."/".($totInAfterV+$totInAfter);
        }
        $empinfo=$empinfo. '</td><td>';
        if($P10to4 == "NOTMEET") {
                $empinfo=$empinfo. $totOutBeforeV."/".($totOutBefore+$totOutBeforeV);
        } else {
                $empinfo=$empinfo. $totOutBefore."/".($totOutBefore+$totOutBeforeV);
        }
        $empinfo=$empinfo. '</td><td>';
        if ($hr9 == "NOTMEET") {
                $empinfo=$empinfo. $totdayHrV."/".($totdayHr+$totdayHrV);
        } else {
                $empinfo=$empinfo. $totdayHr."/".($totdayHr+$totdayHrV);
        }
	$empinfo=$empinfo. '</td><td>';
	if ($hr45 == "NOTMEET") { 
		$empinfo=$empinfo. $totweekHrV."/".($totweekHr+$totweekHrV);
	} else { 
		$empinfo=$empinfo. $totweekHr."/".($totweekHr+$totweekHrV);
	}
	$empinfo=$empinfo. '</td></tr>';
	$mailBody.= $empinfo;
	$mailBody.= $subempinfoComp;
	$mailBody.= '</td></tr>';
	return $mailBody;
}

//Main program 
$toDate=date("Y-m-d");
$oneWeek=strtotime("-7 day", strtotime($toDate));	
$fromDate=date('Y-m-d', $oneWeek); 
$Employees=$db->query("select * from emp where empid='323856' and state='Active'");
$managerMailBody="";
while($res=$db->fetchArray($Employees)) {
	$empid=$res['empid'];
	$result=$db->query("select manager_emailid from emp where empid='$empid' and state='Active'");
	$row=$db->fetchAssoc($result);
	$empEmailId=$res['emp_emailid'];
	$manageremailId=$row['manager_emailid'];
	$mailBody="<h3><center><u>Access details from ".$fromDate." to ".$toDate." for ".$res['empname']."</u></center></h3></br>";
	$mailBody=getDataForEMP($res['empid'],$res['empname'],$fromDate,$toDate,"NOTMEET","NOTMEET","NOTMEET");
	$to=$empEmailId;
	$sub="Access details from $fromDate to $toDate for ".$res['empname'];
	$to="anilkumar.thatavarthi@ecitele.com";
	sendMail($to,$mailBody,$sub);
	//Sleeping for 3 seconds before sending mail
	sleep(3);
	if ( strtolower($res['role'])=="manager") {
		$query="SELECT * FROM emp WHERE managerid='".$res['empid']."' and state='Active'";
		$team=$db->query($query);
		while($teamRow=$db->fetchArray($team)) {
			$managerMailBody.='<table border="4">
                        <tr>    
                                <th>Name</th>
                                <th>No of days after 10 AM</th>
                                <th>No of days before 4 PM</th>
                                <th>No of Daily Hr-NotMeet 
                                <th>No of Weekly Hr-NotMeet
                        </tr>';
			$managerMailBody.=getDataForEMP1($teamRow["empid"],$teamRow["empname"],$fromDate,$toDate,"NOTMEET","NOTMEET","NOTMEET");
			$managerMailBody.="</table></br><hr>";
		}
		$to=$res['emp_emailid'];
		$sub="Access Details for team members from ".$fromDate." to ".$toDate;
		$to="anilkumar.thatavarthi@ecitele.com";
		sendMail($to,$managerMailBody,$sub);
		//Sleeping for 3 seconds before sending mail
		sleep(3);
	}
}
$db->closeConnection();
?>




<?php
# This script is used to generate excel report for tracking information
header('Content-Type: text/plain; charset=utf-8');
require_once 'Library.php';
require_once 'attendenceFunctions.php';
require_once '/auto/bausers/kmishra/script/1.7.6/Classes/PHPExcel.php';

### Generate brief report for a Employees under particular manager
function generateManagerBriefReport($tmpSheetIndex, $dept) {
	global $db, $mainHeaderFormatting,$objPHPExcel,$empList,$summaryArray,$summaryCenterBoldFormatting,$fromDate,$toDate;
	$tmpRowIndex=1;
	$objPHPExcel->setActiveSheetIndex($tmpSheetIndex);
        $objPHPExcel->getActiveSheet()->setTitle("BriefReport");
	
	foreach(array_keys($empList) as $deptKey){
		$objPHPExcel->getActiveSheet()->mergeCells("A$tmpRowIndex:P$tmpRowIndex");
	        $objPHPExcel->getActiveSheet()->getStyle("A$tmpRowIndex:P$tmpRowIndex")->applyFromArray($mainHeaderFormatting);
		$string="For a Department: $deptKey, From $fromDate to $toDate";
	        $objPHPExcel->getActiveSheet()->setCellValue("A$tmpRowIndex", "$string");
        	$tmpRowIndex=$tmpRowIndex+3;
                        
        	breifAttHeader($tmpSheetIndex,$tmpRowIndex);
        	$tmpRowIndex++;
		$empCount=1;
		$summaryArray['after10']=0;
	        $summaryArray['before4']=0;
        	$summaryArray['dailyHrs']=0;
	        $summaryArray['weeklyHrs']=0;
		$summaryArray['totalAfter10']=0;
	        $summaryArray['totalBefore4']=0;
        	$summaryArray['totalDailyHrs']=0;
	        $summaryArray['totalWeeklyHrs']=0;


		foreach($empList[$deptKey] as $empId){
			if ($empId == '') {
				continue;
			}
			echo "For a employee: $empId\n";
			### For each employee
        	        $employeeQuery="SELECT * from `emp` where `empid`='$empId' and `state`='Active'";
                	$employeeResult=$db -> query($employeeQuery);
	                $employeeRow=mysql_fetch_assoc($employeeResult);
        	        $ename=$employeeRow['empname'];
                	$euserName=$employeeRow['empusername'];
	                $eid=$employeeRow['empid'];
        	        $emname=$employeeRow['managername'];
                	$etrack=$employeeRow['track'];
	                ### Don't track attendence for few employees
        	        if ($etrack == 0) {
                	        continue;
                	}
	                $tmpRowIndex=updateExcelSheet_breifReport($tmpSheetIndex,$tmpRowIndex,$empCount,$ename,$eid,$euserName,$emname);
			$empCount++;
		}
		### Write summary information
		$objPHPExcel->getActiveSheet()->setCellValue("E$tmpRowIndex", "Total Team Percentage");
		$tmpStr=$summaryArray['after10']."/".$summaryArray['totalAfter10']." (".round(($summaryArray['after10'] / $summaryArray['totalAfter10']) * 100 ,2)."%)";
	        $objPHPExcel->getActiveSheet()->setCellValue("F$tmpRowIndex", "$tmpStr");
		
		$tmpStr=$summaryArray['before4']."/".$summaryArray['totalBefore4']." (".round(($summaryArray['before4'] / $summaryArray['totalBefore4']) * 100, 2) ."%)";	
        	$objPHPExcel->getActiveSheet()->setCellValue("G$tmpRowIndex", "$tmpStr");

		$tmpStr=$summaryArray['dailyHrs']."/".$summaryArray['totalDailyHrs']." (".round(($summaryArray['dailyHrs'] / $summaryArray['totalDailyHrs']) * 100, 2) ."%)";
	        $objPHPExcel->getActiveSheet()->setCellValue("H$tmpRowIndex", "$tmpStr");
		
		$tmpStr=$summaryArray['weeklyHrs']."/".$summaryArray['totalWeeklyHrs']." (".round(($summaryArray['weeklyHrs'] / $summaryArray['totalWeeklyHrs']) * 100, 2) ."%)";
		$objPHPExcel->getActiveSheet()->setCellValue("I$tmpRowIndex", "$tmpStr");
		$objPHPExcel->getActiveSheet()->getStyle("A$tmpRowIndex:P$tmpRowIndex")->applyFromArray($summaryCenterBoldFormatting);
		$tmpRowIndex=$tmpRowIndex+3;
	}
}

### Generate brief report for all Employees and also department wise
function generateAllBreifReport($brSheetIndex) {
        global $db, $mainHeaderFormatting, $headerFormatting,$objPHPExcel,$summaryArray,$summaryCenterBoldFormatting,$fromDate,$toDate, $empHeaderFormatting,$subDeptHeaderFormatting, $location;
        ### Send breif mail to hr and sgoli
        ### Create a Excel sheet for HR
        $deptQuery="SELECT distinct(mainDept) as deptName FROM `departments` WHERE `deptLocation`='$location'";
        $deptResult=$db -> query($deptQuery);

        $brRowIndex=1;
        while ($deptRow=mysql_fetch_assoc($deptResult)) {
                $mainDept=$deptRow['deptName'];
                formatExcelSheet_breifReport($brSheetIndex,$brRowIndex);
                $objPHPExcel->setActiveSheetIndex($brSheetIndex);
                $objPHPExcel->getActiveSheet()->setTitle("Breif Report");
                $string="For Main Department: $mainDept , From $fromDate to $toDate";
                $objPHPExcel->getActiveSheet()->setCellValue("A$brRowIndex", "$string");
                $objPHPExcel->getActiveSheet()->getStyle("A$brRowIndex:P$brRowIndex")->applyFromArray($mainHeaderFormatting);
		$brRowIndex++;
		$mainDeptRowIndex=$brRowIndex;
                $brRowIndex=$brRowIndex+2;
		$groupRowIndex=$brRowIndex;
		
		$summaryArray['mainafter10']=0;
                $summaryArray['mainbefore4']=0;
                $summaryArray['maindailyHrs']=0;
                $summaryArray['mainweeklyHrs']=0;
                $summaryArray['maintotalAfter10']=0;
                $summaryArray['maintotalBefore4']=0;
                $summaryArray['maintotalDailyHrs']=0;
                $summaryArray['maintotalWeeklyHrs']=0;

                ### Get Sub Departments for a Main Department
                $subDeptQuery="SELECT * from `departments` where `mainDept`='$mainDept'";
                $subDeptQueryResult=$db -> query($subDeptQuery);
		$subGroupArray=array();
                while($subDeptRow=mysql_fetch_assoc($subDeptQueryResult)) {
		
			$summaryArray['after10']=0;
	                $summaryArray['before4']=0;
        	        $summaryArray['dailyHrs']=0;
                	$summaryArray['weeklyHrs']=0;
	                $summaryArray['totalAfter10']=0;
        	        $summaryArray['totalBefore4']=0;
                	$summaryArray['totalDailyHrs']=0;
	                $summaryArray['totalWeeklyHrs']=0;
	
                        $subDept=$subDeptRow['subDept'];
                        $objPHPExcel->setActiveSheetIndex($brSheetIndex);
                        $string="For Sub-department: $subDept";
                        $objPHPExcel->getActiveSheet()->mergeCells("A$brRowIndex:P$brRowIndex");
                        $objPHPExcel->getActiveSheet()->getStyle("A$brRowIndex:P$brRowIndex")->applyFromArray($subDeptHeaderFormatting);
                        $objPHPExcel->getActiveSheet()->setCellValue("A$brRowIndex", "$string");
			$subGroupIndex=$brRowIndex;
                        $brRowIndex++;
                        breifAttHeader($brSheetIndex,$brRowIndex);
                        $brRowIndex++;
                        ### Employee details of sub department
                        $deptEmployeeQuery="SELECT * from `emp` where `dept`='$subDept' and `state`='Active'";
                        $empCount=1;
                        echo "\nFor Department: $subDept\n";
                        $deptEmployeeResult=$db -> query($deptEmployeeQuery);
                        while ($deptEmployeeRow=mysql_fetch_assoc($deptEmployeeResult)) {
                                $ename=$deptEmployeeRow['empname'];
                                $euserName=$deptEmployeeRow['empusername'];
                                $eid=$deptEmployeeRow['empid'];
                                $emname=$deptEmployeeRow['managername'];
                                $etrack=$deptEmployeeRow['track'];
                                ### Don't track attendence for few employees
                                if ($etrack == 0) {
                                        continue;
                                }
                                $brRowIndex=updateExcelSheet_breifReport($brSheetIndex,$brRowIndex,$empCount,$ename,$eid,$euserName,$emname);
                                $empCount++;
                        }
			$brRowIndex++;
			### Write summary information

			### Setting Column Width
		        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40.00);
		        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15.00);
		        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15.00);
		        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15.00);
		        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15.00);

			$objPHPExcel->getActiveSheet()->setCellValue("A$brRowIndex",'Overall Sub-team Percentage');
                        $objPHPExcel->getActiveSheet()->setCellValue("B$brRowIndex",'After 10 am');
                        $objPHPExcel->getActiveSheet()->setCellValue("C$brRowIndex",'Before 4 pm');
                        $objPHPExcel->getActiveSheet()->setCellValue("D$brRowIndex",'Not Met daily Hrs');
                        $objPHPExcel->getActiveSheet()->setCellValue("E$brRowIndex",'Not Met Weekly Hrs');
                        $objPHPExcel->getActiveSheet()->getStyle("A$brRowIndex:E$brRowIndex")->applyFromArray($empHeaderFormatting);
                        $brRowIndex++;
                	$objPHPExcel->getActiveSheet()->setCellValue("A$brRowIndex", "$subDept");
                	$tmpStr=$summaryArray['after10']."/".$summaryArray['totalAfter10']." (".round(($summaryArray['after10'] / $summaryArray['totalAfter10']) * 100 ,2)."%)";
                	$objPHPExcel->getActiveSheet()->setCellValue("B$brRowIndex", "$tmpStr");
                	
                	$tmpStr=$summaryArray['before4']."/".$summaryArray['totalBefore4']." (".round(($summaryArray['before4'] / $summaryArray['totalBefore4']) * 100, 2) ."%)";
                	$objPHPExcel->getActiveSheet()->setCellValue("C$brRowIndex", "$tmpStr");
                	
                	$tmpStr=$summaryArray['dailyHrs']."/".$summaryArray['totalDailyHrs']." (".round(($summaryArray['dailyHrs'] / $summaryArray['totalDailyHrs']) * 100, 2) ."%)";
                	$objPHPExcel->getActiveSheet()->setCellValue("D$brRowIndex", "$tmpStr");
                	
                	$tmpStr=$summaryArray['weeklyHrs']."/".$summaryArray['totalWeeklyHrs']." (".round(($summaryArray['weeklyHrs'] / $summaryArray['totalWeeklyHrs']) * 100, 2) ."%)";
                	$objPHPExcel->getActiveSheet()->setCellValue("E$brRowIndex", "$tmpStr");
                	$objPHPExcel->getActiveSheet()->getStyle("A$brRowIndex:E$brRowIndex")->applyFromArray($summaryCenterBoldFormatting);

			### Assign color
			assignCellColor($brRowIndex,"B",$summaryArray['after10'],$summaryArray['totalAfter10'],"inOut", 0.3);			
			assignCellColor($brRowIndex,"C",$summaryArray['before4'],$summaryArray['totalBefore4'],"inOut", 0.3);
			assignCellColor($brRowIndex,"D",$summaryArray['dailyHrs'],$summaryArray['totalDailyHrs'],"inOut", 0.3);
			assignCellColor($brRowIndex,"E",$summaryArray['weeklyHrs'],$summaryArray['totalWeeklyHrs'],"inOut", 0.3);
		
                        $brRowIndex=$brRowIndex+2;
			$tmpStr=($subGroupIndex+1).",".($brRowIndex-4);
			array_push($subGroupArray,"$tmpStr");
			
			$summaryArray['mainafter10']= $summaryArray['mainafter10']+$summaryArray['after10'];
	                $summaryArray['mainbefore4']= $summaryArray['mainbefore4'] + $summaryArray['before4'];
        	        $summaryArray['maindailyHrs']= $summaryArray['maindailyHrs'] + $summaryArray['dailyHrs'];
                	$summaryArray['mainweeklyHrs']= $summaryArray['mainweeklyHrs'] + $summaryArray['weeklyHrs'];
	                $summaryArray['maintotalAfter10']= $summaryArray['maintotalAfter10'] + $summaryArray['totalAfter10'];
        	        $summaryArray['maintotalBefore4']= $summaryArray['maintotalBefore4'] + $summaryArray['totalBefore4'];
                	$summaryArray['maintotalDailyHrs']= $summaryArray['maintotalDailyHrs'] + $summaryArray['totalDailyHrs'];
	                $summaryArray['maintotalWeeklyHrs']= $summaryArray['maintotalWeeklyHrs'] + $summaryArray['totalWeeklyHrs'];
                }

			### Group Main department Rows
			for ($row = $groupRowIndex; $row <= $brRowIndex-2; ++$row) {
				$objPHPExcel->getActiveSheet()->getRowDimension($row)
			            ->setOutlineLevel(1)->setVisible(false)->setCollapsed(true);			
			}
		
			foreach($subGroupArray as $subGrp) {
				$rowGrp=explode(",",$subGrp);	
				for ($row = $rowGrp[0]; $row <= $rowGrp[1]; ++$row) {
                                	$objPHPExcel->getActiveSheet()->getRowDimension($row)
                                    		->setOutlineLevel(2)->setVisible(false)->setCollapsed(true);
                        	}
				
			}	
			$brRowIndex=$brRowIndex+2;
			$objPHPExcel->getActiveSheet()->setCellValue("A$mainDeptRowIndex",'Overall Main-team Percentage');
		        $objPHPExcel->getActiveSheet()->setCellValue("B$mainDeptRowIndex",'After 10 am');
		        $objPHPExcel->getActiveSheet()->setCellValue("C$mainDeptRowIndex",'Before 4 pm');
		        $objPHPExcel->getActiveSheet()->setCellValue("D$mainDeptRowIndex",'Not Met daily Hrs');
		        $objPHPExcel->getActiveSheet()->setCellValue("E$mainDeptRowIndex",'Not Met Weekly Hrs');
			$objPHPExcel->getActiveSheet()->getStyle("A$mainDeptRowIndex:E$mainDeptRowIndex")->applyFromArray($headerFormatting);
			$mainDeptRowIndex++;

                        ### Write summary information
                        $objPHPExcel->getActiveSheet()->setCellValue("A$mainDeptRowIndex", "$mainDept");
                        $tmpStr=$summaryArray['mainafter10']."/".$summaryArray['maintotalAfter10']." (".round(($summaryArray['mainafter10'] / $summaryArray['maintotalAfter10']) * 100 ,2)."%)";
                        $objPHPExcel->getActiveSheet()->setCellValue("B$mainDeptRowIndex", "$tmpStr");

                        $tmpStr=$summaryArray['mainbefore4']."/".$summaryArray['maintotalBefore4']." (".round(($summaryArray['mainbefore4'] / $summaryArray['maintotalBefore4']) * 100, 2) ."%)";
                        $objPHPExcel->getActiveSheet()->setCellValue("C$mainDeptRowIndex", "$tmpStr");

                        $tmpStr=$summaryArray['maindailyHrs']."/".$summaryArray['maintotalDailyHrs']." (".round(($summaryArray['maindailyHrs'] / $summaryArray['maintotalDailyHrs']) * 100, 2) ."%)";
                        $objPHPExcel->getActiveSheet()->setCellValue("D$mainDeptRowIndex", "$tmpStr");

                        $tmpStr=$summaryArray['mainweeklyHrs']."/".$summaryArray['maintotalWeeklyHrs']." (".round(($summaryArray['mainweeklyHrs'] / $summaryArray['maintotalWeeklyHrs']) * 100, 2) ."%)";          
                        $objPHPExcel->getActiveSheet()->setCellValue("E$mainDeptRowIndex", "$tmpStr");
                        $objPHPExcel->getActiveSheet()->getStyle("A$mainDeptRowIndex:E$mainDeptRowIndex")->applyFromArray($summaryCenterBoldFormatting);

			### Assign color
			assignCellColor($mainDeptRowIndex,"B",$summaryArray['mainafter10'],$summaryArray['maintotalAfter10'],"inOut", 0.3); 
                        assignCellColor($mainDeptRowIndex,"C",$summaryArray['mainbefore4'],$summaryArray['maintotalBefore4'],"inOut", 0.3);
                        assignCellColor($mainDeptRowIndex,"D",$summaryArray['maindailyHrs'],$summaryArray['maintotalDailyHrs'],"inOut", 0.3);
                        assignCellColor($mainDeptRowIndex,"E",$summaryArray['mainweeklyHrs'],$summaryArray['maintotalWeeklyHrs'],"inOut", 0.3);

        }
}

### Color a cell
function assignCellColor($rowIndex, $cellName, $value, $total, $flag, $percentage) {
	global $objPHPExcel;
	if ($flag == "inOut") {
	  	if (($value/$total) >= $percentage) {
       			$objPHPExcel->getActiveSheet()->getStyle("$cellName$rowIndex")
                                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f5ee18');
        	}
	}
	if ($flag == "untracked") {
		if ($value > $percentage) {
			$objPHPExcel->getActiveSheet()->getStyle("$cellName$rowIndex")
                                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f5ee18');
		}
	}
}

### Get IN OUT table information
function getInOutInfo($empID,$first,$last,$P10to4,$hr9,$hr45) {
	global $db;
	$defaultIn='10:00:00';
        $defaultOut='16:00:00';
        $halfDayDefault='13:00:00';

	$query='select * from `inout` WHERE `EmpID` ='.$empID.' AND 
			`Date` >= \''.$first.'\' AND `Date` <= \''.$last.'\';';
	$result=$db->query($query);
	$totInAfter=0;
	$totInAfterV=0;
	$totOutBefore=0;
	$totOutBeforeV=0;
	$totdayHr=0;
	$totdayHrV=0;
	$totweekHr=0;
	$totweekHrV=0;
	$empinfo="";
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
				
				// Check whether the leave type is On Site
                                if (preg_match('/On Site/i',getDay($empID,$curday)))
                                {
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
                   		$flag=0;
                   		$tot=timeAdd($tot,"4:15:00");
                   		$dayCount=$dayCount+0.5;
                   		$totInAfter=$totInAfter+1;
                   		$totOutBefore=$totOutBefore+1;
                   		$totdayHr=$totdayHr+1;
                   		continue;
                }
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
			
			// If employee applies WFH, either fisr half / second half and decide what data goes to "FIRST" column
			if(isWFH($row['Date'], $empID) ) {
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
			}
		 
			// If employee applies WFH, either fisr half / second half and decide what data goes to "LAST" column
			if(isWFH($row['Date'], $empID) ) {
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
			} 
			
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
		$totweekHrV=$totweekHrV+1;	
	} else {
		$totweekHr=$totweekHr+1;	
	}
	
	if (!($counter < 1)) {
		$counter=-1;
	}
	$counter=$counter+1;
	}


	$inOutInfoArr=array();
	$inOutInfoArr['totalINAfterV']=$totInAfterV;
	$inOutInfoArr['totalINAfte']=$totInAfterV+$totInAfter;	

	$inOutInfoArr['totOutBeforeV']=$totOutBeforeV;
        $inOutInfoArr['totOutBefore']=$totOutBeforeV+$totOutBefore;

	$inOutInfoArr['totdayHrV']=$totdayHrV;
        $inOutInfoArr['totdayHr']=$totdayHrV+$totdayHr;

	$inOutInfoArr['totweekHrV']=$totweekHrV;
        $inOutInfoArr['totweekHr']=$totweekHrV+$totweekHr;

	return $inOutInfoArr;
	
}

### Get Leave transaction information
function getLeaveDetails ($empID,$toDate,$fromDate) {
	global $db;
	$leaveInfo=array();
	### get Number of FullDays
	$fulldayquery="select count(*) as fullLeave from perdaytransactions where date between '".$fromDate."' and '".$toDate."' and empid='".$empID."' and `leavetype`='FullDay' and status='Approved'";
	$fulldayResult=$db->query($fulldayquery);
        $fulldayRow = mysql_fetch_assoc($fulldayResult);
	$leaveInfo['fullDay']=$fulldayRow['fullLeave'];

	### get NUmber of Half Days
	$halfdayquery="select count(*) as halfLeave from perdaytransactions where date between '".$fromDate."' and '".$toDate."' and empid='".$empID."' and `leavetype`='HalfDay' and status='Approved'";
        $halfdayResult=$db->query($halfdayquery);
        $halfdayRow = mysql_fetch_assoc($halfdayResult);
	$leaveInfo['halfDay']=$halfdayRow['halfLeave'];	

	### get NUmber of WFH
	$wfhdayquery="select count(*) as wfh from perdaytransactions where date between '".$fromDate."' and '".$toDate."' and empid='".$empID."' and `leavetype`='WFH' and status='Approved'";
        $wfhdayResult=$db->query($wfhdayquery);
        $wfhdayRow = mysql_fetch_assoc($wfhdayResult);
        $leaveInfo['wfh']=$wfhdayRow['wfh']; 

	### get Number of half WFH and WFH PTO
	$spldayquery="select count(*) as splLeave from perdaytransactions where date between '".$fromDate."' and '".$toDate."' and empid='".$empID."' and (`leavetype`='First Half-HalfDay & second Half-WFH' or `leavetype`='First Half-WFH & Second Half-HalfDay') and status='Approved'";
        $spldayResult=$db->query($spldayquery);
	while($spldayRow = mysql_fetch_assoc($spldayResult)) {
		if ($spldayRow['splLeave'] != 0) {
			$leaveInfo['wfh']=$leaveInfo['wfh'] + 0.5;
			$leaveInfo['halfDay']=$leaveInfo['halfDay'] + 0.5;
		}
	}

	### NUmber of compoff taken
	$compoffquery="select count(*) as compOff from `inout` where date between '".$fromDate."' and '".$toDate."' and empid='".$empID."' and `compofftakenday`!='0000-00-00'";
        $compoffResult=$db->query($compoffquery);
        $compoffRow = mysql_fetch_assoc($compoffResult);
        $leaveInfo['compoff']=$compoffRow['compOff'];
	
	return $leaveInfo;
}

### get untracked leave applied
function getUntrackedLeaves($empID,$toDate,$fromDate) {
        global $db;
        $unTrackInfo=array();
	
	### Get NO Data issues
	$noDataquery="select count(*) as nodata from `inout` where date between '".$fromDate."' and '".$toDate."' and empid='".$empID."' and `state`='No Data'";
        $noDataResult=$db->query($noDataquery);
        $noDataRow = mysql_fetch_assoc($noDataResult);
        $unTrackInfo['noData']=$noDataRow['nodata'];

	### Get Half day not applied
        $halfDayquery="select count(*) as halfDay from `inout` where date between '".$fromDate."' and '".$toDate."' and empid='".$empID."' and `state`='Half Day PTO not applied'";
        $halfDayResult=$db->query($halfDayquery);
        $halfDayRow = mysql_fetch_assoc($halfDayResult);
        $unTrackInfo['halfDay']=$halfDayRow['halfDay'];

	### Get NO Data issues
        $swipequery="select count(*) as swipeIssue from `inout` where date between '".$fromDate."' and '".$toDate."' and empid='".$empID."' and `state`='Swipe issues'";
        $swipeResult=$db->query($swipequery);
        $swipeRow = mysql_fetch_assoc($swipeResult);
        $unTrackInfo['swipeIssue']=$swipeRow['swipeIssue'];

	return $unTrackInfo;
}
### Send mail with attachment
function sendMailwithAttachment($to,$mailBody,$sub,$Attachment)
{
        require_once 'PHPMailer_v5.1/class.phpmailer.php';
        try {

                $mail = new PHPMailer(true); //New instance, with exceptions enabled
                $mail->IsSMTP();                           // tell the class to use SMTP
                $mail->SMTPAuth   = false;                  // enable SMTP authentication
                $mail->Port       = 25;                    // set the SMTP server port
                $mail->IsSendmail();  // tell the class to use Sendmail
                $mail->From       = "eci_lms@ecitele.com";
                $mail->FromName   = "ECI Leave Management System";
                $a=array();
                $a=explode(',', $to);
                $i=count($a);
                for($i=0;$i<count($a);$i++)
                {
                        $mail->AddAddress($a[$i]);
                }
                $mail->Subject  = $sub;
		$mail->AddAttachment($Attachment);
                $mail->MsgHTML($mailBody);
		$mail->AddBCC("anilkumar.thatavarthi@ecitele.com" , "ECI Leave Management System Reports");
                $mail->AddBCC("naidile.basavegowda@ecitele.com", "ECI Leave Management System Reports");
                $mail->IsHTML(true); // send as HTML
                $mail->Send();
                echo 'Message with attachment has been sent.';
        } catch (phpmailerException $e) {
                echo $e->errorMessage();
        }
}

### Get an sub-ordinates for a given employee
function getSubOrdinates($sheetIndex,$id, $rowIndex) {
        global $db,$empList;
	global $objPHPExcel;
	global $managerFormatting;
        $getDeptEmployeesQuery="SELECT * from `emp` where `managerid`='".$id."' and state='Active'";
	echo "\ngetSubOrdinates Query: $getDeptEmployeesQuery\n";
        $getDeptEmployeesResult=$db -> query($getDeptEmployeesQuery);
	$currSheet=$sheetIndex;
	$empCount=1;
        while ($deptEmpRow = mysql_fetch_assoc($getDeptEmployeesResult)) {
                $depEmpName=$deptEmpRow['empname'];
                $depEmpId=$deptEmpRow['empid'];
                $depName=$deptEmpRow['dept'];
                $depEmpRole=$deptEmpRow['role'];
		$deptmanagerName=$deptEmpRow['managername'];
		$deptEmpTrack=$deptEmpRow['track'];
		$empList[$depName][] = '';
		echo "Pushing to dept inside get sub: $depName\n";
		array_push($empList[$depName],$depEmpId);
		### Dont track attendence for few employees
		if ($deptEmpTrack == 0) {
			continue;
		}
                if (strtoupper($depEmpRole) == "MANAGER" and $depEmpId != "325020") {
			
			### Update in current excel sheet
			$objPHPExcel->setActiveSheetIndex($currSheet);
                        $objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->applyFromArray($managerFormatting);
                        $rowIndex=updateExcelSheet($currSheet,$rowIndex,$empCount,$depEmpName,$depEmpId,$depName);
                        echo "\n\t\t$depEmpName,$depEmpId,$depName,$depEmpRole\n";
			$sheetIndex++;
                      
			$subRowIndex=1;
			formatExcelSheet($sheetIndex, $subRowIndex);
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$objPHPExcel->getActiveSheet()->setTitle("$depEmpName");
			$string="Department: $depName ,  Manager: $depEmpName ";
			$objPHPExcel->getActiveSheet()->setCellValue("A$subRowIndex", "$string");
			$subRowIndex=$subRowIndex+3;
			$sheetIndex=getSubOrdinates($sheetIndex,$depEmpId,$subRowIndex); 
			
                } else {
			$rowIndex=updateExcelSheet($currSheet,$rowIndex,$empCount,$depEmpName,$depEmpId,$depName);
			#echo "\n\t\t$depEmpName,$depEmpId,$depName,$depEmpRole\n";
                }
		$empCount++;
        }
	return $sheetIndex;
}


### Fucntion to  create a sheet and format
function formatExcelSheet($sheetIndex, $rowIndex) {
        global $objPHPExcel;
        global $headerFormatting;
        $nextIndex=$rowIndex+1;
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex($sheetIndex);
        $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);

        ### Setting Column Width
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30.00);

        $objPHPExcel->getActiveSheet()->mergeCells("A$rowIndex:E$rowIndex");
        $objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->applyFromArray($headerFormatting);
        
	for ($j = "A"; $j <= "E"; $j++) {
                $index=$j.$nextIndex;
                $objPHPExcel->getActiveSheet()->getStyle($index)->applyFromArray($headerFormatting);
        }

	$objPHPExcel->getActiveSheet()->setCellValue("A$nextIndex",'SR.No');
        $objPHPExcel->getActiveSheet()->setCellValue("B$nextIndex",'EmpName');
        $objPHPExcel->getActiveSheet()->setCellValue("C$nextIndex",'Emp ID');
	$objPHPExcel->getActiveSheet()->setCellValue("D$nextIndex",'Emp Dept');
        $objPHPExcel->getActiveSheet()->setCellValue("E$nextIndex",'Status');
}

### Function to create a sheet for breif attendence report
function formatExcelSheet_breifReport($sheetIndex, $rowIndex) {
	global $objPHPExcel,$headerFormatting;
	$objPHPExcel->setActiveSheetIndex($sheetIndex);
        $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(70);
        ### Setting Column Width
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15.00);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30.00);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12.00);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20.00);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20.00);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15.00);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15.00);

        $objPHPExcel->getActiveSheet()->mergeCells("A$rowIndex:P$rowIndex");
        $objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:P$rowIndex")->applyFromArray($headerFormatting);
}

### Assign name to column
function breifAttHeader($sheetIndex, $rowIndex) {
	global $objPHPExcel, $headerFormatting;
	$objPHPExcel->setActiveSheetIndex($sheetIndex);
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex",'SR.No');
        $objPHPExcel->getActiveSheet()->setCellValue("B$rowIndex",'Emp ID');
        $objPHPExcel->getActiveSheet()->setCellValue("C$rowIndex",'Emp userName');
        $objPHPExcel->getActiveSheet()->setCellValue("D$rowIndex",'Emp Name');
        $objPHPExcel->getActiveSheet()->setCellValue("E$rowIndex",'Manager Name');
        $objPHPExcel->getActiveSheet()->setCellValue("F$rowIndex",'After 10 am');
        $objPHPExcel->getActiveSheet()->setCellValue("G$rowIndex",'Before 4 pm');
        $objPHPExcel->getActiveSheet()->setCellValue("H$rowIndex",'Not Met daily Hrs');
	$objPHPExcel->getActiveSheet()->setCellValue("I$rowIndex",'Not Met Weekly Hrs');
        $objPHPExcel->getActiveSheet()->setCellValue("J$rowIndex",'WFH');
        $objPHPExcel->getActiveSheet()->setCellValue("K$rowIndex",'FullDay PTO');
        $objPHPExcel->getActiveSheet()->setCellValue("L$rowIndex",'HalfDay PTO');
        $objPHPExcel->getActiveSheet()->setCellValue("M$rowIndex",'CompOff');
        $objPHPExcel->getActiveSheet()->setCellValue("N$rowIndex",'FullDay Not Applied');
        $objPHPExcel->getActiveSheet()->setCellValue("O$rowIndex",'HalfDay Not Applied');
        $objPHPExcel->getActiveSheet()->setCellValue("P$rowIndex",'Swipe Issues');

        $objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:P$rowIndex")->applyFromArray($headerFormatting);
}
### Function to update a excel sheet
function updateExcelSheet($sheetIndex,$rowIndex,$empCount,$empName, $empId,$empDept) {
        global $objPHPExcel,$toDate, $fromDate, $db,$empHeaderFormatting,$innerHeaderFormatting,$centerBoldFormatting,$mergeCenterFormatting;

	### Convert string to time
	$mTime=strtotime("10:00:00");
	$eTime=strtotime("16:00:00");
	$dTime=strtotime("08:30:00");

	echo "\nWriting details to sheet $sheetIndex:  \t rowIndex: $rowIndex \t empCount: $empCount\t empName: $empName \t empId: $empId\t empDept: $empDept\n";
        $objPHPExcel->setActiveSheetIndex($sheetIndex);
	$objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex", "$empCount"); 
        $objPHPExcel->getActiveSheet()->setCellValue("B$rowIndex", "$empName");
        $objPHPExcel->getActiveSheet()->setCellValue("C$rowIndex", "$empId");
        $objPHPExcel->getActiveSheet()->setCellValue("D$rowIndex", "$empDept");
	$objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->applyFromArray($empHeaderFormatting);
	$statusRowIndex=$rowIndex;
	$rowIndex=$rowIndex+2;
	#### Merge cell  for heading
	$objPHPExcel->getActiveSheet()->getRowDimension("$rowIndex")->setRowHeight(1);
	$objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->applyFromArray($innerHeaderFormatting);
	$rowIndex++;
	$objPHPExcel->getActiveSheet()->mergeCells("A$rowIndex:E$rowIndex");
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex", "IN/OUT Details");
	$objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->applyFromArray($centerBoldFormatting);
	$rowIndex++;
	### Heading for In/Out details
	$objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->applyFromArray($innerHeaderFormatting);
	$objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex",'Date');
        $objPHPExcel->getActiveSheet()->setCellValue("B$rowIndex",'In Time');
        $objPHPExcel->getActiveSheet()->setCellValue("C$rowIndex",'Out Time');
        $objPHPExcel->getActiveSheet()->setCellValue("D$rowIndex",'Time Difference');
        $objPHPExcel->getActiveSheet()->setCellValue("E$rowIndex",'Reason');	

	### Write In Out Details of employee
	$inOutQuery="SELECT * FROM `inout` where `Date` between '$fromDate' and '$toDate' and `EmpID`='$empId'";
	$inOutResult=$db -> query($inOutQuery);
	$foundEntry=0;
	$rowsExists=0;
        while ($inOutRow=mysql_fetch_assoc($inOutResult)) {
		$rowsExists=1;
		$rowIndex++;
		$dateEmp=$inOutRow['Date'];
		$inTime=$inOutRow['First'];
		$outTime=$inOutRow['Last'];
		$diffTime=timediffinHR($inTime,$outTime);
		$reason=$inOutRow['state'];
		if($reason == "Data Exists") {
			$reason="-";
		} else {
			$foundEntry=1;
		}
		if ($reason == "No Data") {
			$objPHPExcel->getActiveSheet()->mergeCells("B$rowIndex:E$rowIndex");
			$objPHPExcel->getActiveSheet()->getStyle("B$rowIndex:E$rowIndex")->applyFromArray($mergeCenterFormatting);
			$objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex", "$dateEmp");
			$objPHPExcel->getActiveSheet()->setCellValue("B$rowIndex", "$reason");
			$objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")
				    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f17961');
		} else {
			if ($reason != "-") {
				$objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->getFill()
					    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f17961');
			}
			$objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex", "$dateEmp");
			if (strtotime($inTime) > $mTime) {
				$objPHPExcel->getActiveSheet()->getStyle("B$rowIndex")
                                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f5ee18');
			}
			if (strtotime($outTime) < $eTime) {
                                $objPHPExcel->getActiveSheet()->getStyle("C$rowIndex")
                                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f5ee18');
                        }
			if (strtotime($diffTime) < $dTime) {
                                $objPHPExcel->getActiveSheet()->getStyle("D$rowIndex")
                                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f5ee18');
                        }
			$objPHPExcel->getActiveSheet()->setCellValue("B$rowIndex", "$inTime");
			$objPHPExcel->getActiveSheet()->setCellValue("C$rowIndex", "$outTime");
			$objPHPExcel->getActiveSheet()->setCellValue("D$rowIndex", "$diffTime");
			$objPHPExcel->getActiveSheet()->setCellValue("E$rowIndex", "$reason");
		}
	}

	if ($rowsExists == 0) {
		$rowIndex++;
		$objPHPExcel->getActiveSheet()->mergeCells("A$rowIndex:E$rowIndex");
		$objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->applyFromArray($mergeCenterFormatting);
		$objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex", "No Information present in IN/OUT table from $fromDate to $toDate");
		$rowIndex++;
	}
	if ($foundEntry == 0) {
		$objPHPExcel->getActiveSheet()->setCellValue("E$statusRowIndex", "No Discrepancies");
	} else {
		$objPHPExcel->getActiveSheet()->setCellValue("E$statusRowIndex", "Found Discrepancies");
	}
	$rowIndex=$rowIndex+2;

	### Write Leave Details of Employee
	$objPHPExcel->getActiveSheet()->mergeCells("A$rowIndex:E$rowIndex");
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex", "Approved & Pending  Leaves");
        $objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->applyFromArray($centerBoldFormatting);
	$rowIndex++;

	### Heading for Approved & Pending  Leaves
        $objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:D$rowIndex")->applyFromArray($innerHeaderFormatting);
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex",'Date');
        $objPHPExcel->getActiveSheet()->setCellValue("B$rowIndex",'Leave Type');
        $objPHPExcel->getActiveSheet()->setCellValue("C$rowIndex",'Status');
        $objPHPExcel->getActiveSheet()->setCellValue("D$rowIndex",'Reason');

	### Write leave information to PHP Excel sheet
	$leaveInfoQuery="SELECT p.leavetype, p.date, e.reason, e.approvalstatus FROM empleavetransactions e, perdaytransactions p where e.transactionid=p.transactionid and e.empid='$empId' and p.date between '$fromDate' and '$toDate' and e.approvalstatus != 'Deleted'";
	$leavetResult=$db->query($leaveInfoQuery);
        $leaveInfoExists=0;
        while ($leaveRow=mysql_fetch_assoc($leavetResult)) {
		$rowIndex++;
		$leaveType=$leaveRow['leavetype'];
		$date=$leaveRow['date'];
		$reason=$leaveRow['reason'];
		$approvalstatus=$leaveRow['approvalstatus'];
	 	$objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex", "$date");
                $objPHPExcel->getActiveSheet()->setCellValue("B$rowIndex", "$leaveType");
                $objPHPExcel->getActiveSheet()->setCellValue("C$rowIndex", "$approvalstatus");
                $objPHPExcel->getActiveSheet()->setCellValue("D$rowIndex", "$reason");
		$leaveInfoExists=1;
	}
	$rowIndex++;
	if ($leaveInfoExists == 0) {
		$objPHPExcel->getActiveSheet()->mergeCells("A$rowIndex:D$rowIndex");	
		$objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:D$rowIndex")->applyFromArray($mergeCenterFormatting);
		$objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex", "No Leave information from $fromDate to $toDate");
	}
	$rowIndex++;
	$objPHPExcel->getActiveSheet()->getRowDimension($rowIndex)->setRowHeight(1);
	$objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->applyFromArray($innerHeaderFormatting);
        $rowIndex++;
	$rowIndex=$rowIndex+2;	
        return $rowIndex;
}


### Function to breif attendence excel sheet
function updateExcelSheet_breifReport($sheetIndex,$rowIndex,$empCount,$empName, $empId,$euserName,$emname) {
        global $objPHPExcel,$db,$empHeaderFormatting,$innerHeaderFormatting,$centerBoldFormatting,$mergeCenterFormatting,$fromDate,$toDate,$summaryArray;

        ### Convert string to time
        $mTime=strtotime("10:00:00");
        $eTime=strtotime("16:00:00");
        $dTime=strtotime("08:30:00");

        $objPHPExcel->setActiveSheetIndex($sheetIndex);
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex", "$empCount");
        $objPHPExcel->getActiveSheet()->setCellValue("B$rowIndex", "$empId");
        $objPHPExcel->getActiveSheet()->setCellValue("C$rowIndex", "$euserName");
        $objPHPExcel->getActiveSheet()->setCellValue("D$rowIndex", "$empName");
	$objPHPExcel->getActiveSheet()->setCellValue("E$rowIndex", "$emname");

	$inOutInfo=getInOutInfo($empId,$fromDate,$toDate,"NOTMEET","NOTMEET","NOTMEET");
	$after10 = $inOutInfo['totalINAfterV']."/".$inOutInfo['totalINAfte'];
	$before4 = $inOutInfo['totOutBeforeV']."/".$inOutInfo['totOutBefore'];
	$dailyHrs = $inOutInfo['totdayHrV']."/".$inOutInfo['totdayHr'];
	$weeklyHrs = $inOutInfo['totweekHrV']."/".$inOutInfo['totweekHr'];	

	$summaryArray['after10'] +=$inOutInfo['totalINAfterV'];
        $summaryArray['before4'] +=$inOutInfo['totOutBeforeV'];
        $summaryArray['dailyHrs'] +=$inOutInfo['totdayHrV'];
        $summaryArray['weeklyHrs'] +=$inOutInfo['totweekHrV'];

	$summaryArray['totalAfter10'] +=$inOutInfo['totalINAfte'];
        $summaryArray['totalBefore4'] +=$inOutInfo['totOutBefore'];
        $summaryArray['totalDailyHrs'] +=$inOutInfo['totdayHr'];
        $summaryArray['totalWeeklyHrs'] +=$inOutInfo['totweekHr'];
	
	assignCellColor($rowIndex,"F",$inOutInfo['totalINAfterV'], $inOutInfo['totalINAfte'],"inOut", 0.5);
	assignCellColor($rowIndex,"G",$inOutInfo['totOutBeforeV'], $inOutInfo['totOutBefore'],"inOut", 0.5);
	assignCellColor($rowIndex,"H",$inOutInfo['totdayHrV'], $inOutInfo['totdayHr'],"inOut", 0.5);
	assignCellColor($rowIndex,"I",$inOutInfo['totweekHrV'],$inOutInfo['totweekHr'],"inOut", 0.5);
	
	$objPHPExcel->getActiveSheet()->setCellValue("F$rowIndex", "$after10");
	$objPHPExcel->getActiveSheet()->setCellValue("G$rowIndex", "$before4");
	$objPHPExcel->getActiveSheet()->setCellValue("H$rowIndex", "$dailyHrs");
	$objPHPExcel->getActiveSheet()->setCellValue("I$rowIndex", "$weeklyHrs");

	$leaveInfo=getLeaveDetails ($empId,$toDate,$fromDate);
	$objPHPExcel->getActiveSheet()->setCellValue("J$rowIndex",$leaveInfo['wfh']);
        $objPHPExcel->getActiveSheet()->setCellValue("K$rowIndex",$leaveInfo['fullDay']);
        $objPHPExcel->getActiveSheet()->setCellValue("L$rowIndex",$leaveInfo['halfDay']);
	$objPHPExcel->getActiveSheet()->setCellValue("M$rowIndex",$leaveInfo['compoff']);	

	$untrackInfo=getUntrackedLeaves($empId,$toDate,$fromDate);
	$objPHPExcel->getActiveSheet()->setCellValue("N$rowIndex",$untrackInfo['noData']);
        $objPHPExcel->getActiveSheet()->setCellValue("O$rowIndex",$untrackInfo['halfDay']);
        $objPHPExcel->getActiveSheet()->setCellValue("P$rowIndex",$untrackInfo['swipeIssue']);
	assignCellColor($rowIndex,"N",$untrackInfo['noData'],0,"untracked",0);
	assignCellColor($rowIndex,"O",$untrackInfo['halfDay'],0,"untracked",0);
	assignCellColor($rowIndex,"P",$untrackInfo['swipeIssue'],0,"untracked",0);

	$rowIndex++;
	return $rowIndex;
}


### Main program 
$db=connectToDB();
$delimiter1="##################################################";
$delimiter2="**************************************************";

$shortOptions="i::s::d::t::f::m::l::rah";
$longoptions  = array(
    "empId::",
    "All",
    "subDept::",
    "mainDept::",
    "toDate::",
    "fromDate::",
    "mailList::",
    "location::",
    "hrReport",
    "help"
);
$options = getopt($shortOptions,$longoptions);

### Get emp id info
if (isset($options['empId']))
        $empId=$options['empId'];

### Get all info
if (isset($options['All']))
        $All=1;

### Get  sub deptarment info
if (isset($options['subDept']))
        $subDept=$options['subDept'];

### Get main department info
if (isset($options['mainDept']))
        $mainDept=$options['mainDept'];

### Get to date info
if (isset($options['toDate'])) {
        $toDate=$options['toDate'];
} else {
	# If toDate is not provided, then take current date
        $toDate=date("Y-m-d");
}
### Get from date info
if (isset($options['fromDate'])) {
        $fromDate=$options['fromDate'];
} else {
	# IF fromDate is not provided, then take start of the month
	$fromDate=date('Y-m-01');
}

### Get to mail list 
if (isset($options['mailList'])) {
        $mailList=$options['mailList'];
}

### option to generate HR report
if (isset($options['hrReport'])) {
        $hrReport=1;
} else {
        $hrReport=0;
}

### Option to get location, By default it is bangalore
if (isset($options['location'])) {
	$location=$options['location'];
} else {
	$location='BLR';
}

### Display help message
if (isset($options['help'])) {
	echo "Script Usage:\n";
	echo "\t\t empId: To generate Attendance information for a particular employee, If Employee is a manager then detailed report will be generated including his sub-ordinates\n\n";
	echo "\t\t subDept: To generate Attendance information for a given sub-department\n\n";
	echo "\t\t mainDept: To generate Attendance information for a given main-department, includes all sub department under given main department\n\n";
	echo "\t\t All: To generate Attendance information for all Employees\n\n";
	echo "\t\t mailList: To send generated Excel report for given mail Ids, otherwise mail will be send to respective managers\n\n";
	echo "\t\t hrReport: To generate Brief report of all Employees and also department wise. Sends report to HR and Srini\n\n";
	echo "\t\t toDate: To date information, By default it takes current date\n\n";
	echo "\t\t fromDate: From Date information, By default it takes 30 days from current day\n\n";
	echo "\t\t location: location name, By default BLR\n\n";
	echo "Example:\n\t\tphp weeklyAttendenceReport.php --empId=323856 --toDate=2017-01-31 --fromDate=2017-01-01 --hrReport=1\n\n";
	echo "Example:\n\t\tphp weeklyAttendenceReport.php --mainDept=AIBI\n\n";
	exit;
}

### By default, update to all employees
if (!isset($options['empId']) and !isset($options['All']) and !isset($options['subDept']) and !isset($options['mainDept'])) {
        $All=1;
}
### Excel sheet formatting
echo date('H:i:s') . " Set thin black border outline around column\n";
    $mainHeaderFormatting = array(
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            ),
        ),
        'font' => array(
            'bold' => false,
            'size' => 18,
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap' => true,
            'AutoSize' => true,
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'rotation' => 90,
            'startcolor' => array(
            'rgb' => 'ffc733',
            ),
            'endcolor' => array(
                'rgb' => 'ffc733',
            ),
        ),
    );

echo date('H:i:s') . " Set thin black border outline around column\n";
    $subDeptHeaderFormatting = array(
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            ),
        ),
        'font' => array(
            'bold' => false,
            'size' => 18,
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap' => true,
            'AutoSize' => true,
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'rotation' => 90,
            'startcolor' => array(
            'rgb' => '33E6FF',
            ),
            'endcolor' => array(
                'rgb' => '33E6FF',
            ),
        ),
    );

$headerFormatting = array(
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            ),
        ),
        'font' => array(
            'bold' => true,
            'size' => 10,
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'AutoSize' => true,
	    'wrap' => true,
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'rotation' => 90,
            'startcolor' => array(
            'rgb' => 'F28A8C',
            ),
            'endcolor' => array(
                'rgb' => 'F28A8C',
            ),
        ),
);

$subHeaderFormatting = array(
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            ),
        ),
        'font' => array(
            'bold' => true,
            'size' => 10,
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'AutoSize' => true,
            'wrap' => true,
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'rotation' => 90,
            'startcolor' => array(
            'rgb' => 'F28A8C',
            ),
            'endcolor' => array(
                'rgb' => 'F28A8C',
            ),
        ),
);

$managerFormatting = array(
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            ),
        ),
        'font' => array(
            'bold' => true,
            'size' => 12,
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap' => true,
            'AutoSize' => true,
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'rotation' => 90,
            'startcolor' => array(
            'rgb' => 'b8eef2',
            ),
            'endcolor' => array(
                'rgb' => 'b8eef2',
            ),
        ),
);

$innerHeaderFormatting = array(
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            ),
        ),
        'font' => array(
            'bold' => true,
            'size' => 12,
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap' => true,
            'AutoSize' => true,
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'rotation' => 90,
            'startcolor' => array(
            'rgb' => 'a2adec',
            ),
            'endcolor' => array(
                'rgb' => 'a2adec',
            ),
        ),
);

echo date('H:i:s') . " Set thin black border outline around column\n";
$empHeaderFormatting = array(
        'borders' => array(
                'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => 'FF000000'),
                ),
        ),
        'font' => array(
                'bold' => false,
                'size' => 12,
        ),

        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
                'AutoSize' => true,
        ),
        'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'rotation' => 90,
                'startcolor' => array(
                'rgb' => 'a2f5d3',
                ),
                'endcolor' => array(
                        'rgb' => 'a2f5d3',
                ),
        ),
);

$mergeCenterFormatting = array(
        'borders' => array(
                'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'f0f0f0'),
                ),
        ),
        'font' => array(
                'bold' => false,
                'size' => 12,
        ),

        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'rotation' => 90,
                'startcolor' => array(
                'rgb' => 'FFFFFF',
                ),
                'endcolor' => array(
                        'rgb' => 'FFFFFF',
                ),
        ),
);

$centerBoldFormatting = array(
	'borders' => array(
                'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'f0f0f0'),
                ),
        ),
        'font' => array(
                'bold' => true,
                'size' => 12,
        ),

        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'rotation' => 90,
                'startcolor' => array(
                'rgb' => 'FFFFFF',
                ),
                'endcolor' => array(
                        'rgb' => 'FFFFFF',
                ),
        ),
);

$summaryCenterBoldFormatting = array(
        'borders' => array(
                'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'f0f0f0'),
                ),
        ),
        'font' => array(
                'bold' => true,
                'size' => 12,
        ),

        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'rotation' => 90,
                'startcolor' => array(
                'rgb' => 'faebd7',
                ),
                'endcolor' => array(
                        'rgb' => 'faebd7',
                ),
        ),
);

echo "\n\n$delimiter2$delimiter2 \nGiven Arguments are:\n";
echo "\t\tempId: $empId\n\t\tAll: $All\n\t\tsubDept: $subDept\n\t\tmainDept: $mainDept\n\t\ttoDate: $toDate\n\t\tfromDate: $fromDate\n\t\tmailList: $mailList\n\t\thrReport: $hrReport\n\t\tlocation: $location";
echo "\n$delimiter2$delimiter2\n\n";

### Collect Information for a particular employee
if (isset($empId)) {
	echo "\nCollecting Information for an Emp ID: $empId\n";
	$queryEmpList="SELECT * FROM `emp` where `state`='Active' and `empid`='".$empId."'";
}

### Collect Information for Sub department Employees
if (isset($subDept)) {
        echo "\nCollecting Information for Sub department Employees: $subDept\n";
        $queryEmpList="SELECT * FROM `emp` where `dept`='".$subDept."' and `state`='Active'";
}

### Collect Information for main department Employees
if (isset($mainDept)) {
        echo "\nCollecting Information for main department Employees: $mainDept\n";
	$getSubDept="SELECT * FROM `departments` where `mainDept`='$mainDept'";
        $subDeptResult=$db->query($getSubDept);
	$deptList="";
        while ($subDeptRows=mysql_fetch_assoc($subDeptResult)) {
		$deptList=$deptList.",'".$subDeptRows['subDept']."'";
	}
	$deptList = ltrim($deptList, ',');
        $queryEmpList="SELECT * FROM `emp` where `dept` IN ($deptList) and `state`='Active'";
}

### Collect Information for all Employees
if (isset($All)) {                            
        echo "\nCollecting Information for all Employees\n";
        $queryEmpList="SELECT * FROM `emp` where `state`='Active' and `location`='$location'";
}

### For each employee
$empListResult=$db -> query($queryEmpList);
if($empListResult) {
	 while ($empRow = mysql_fetch_assoc($empListResult)) {
		$empList=array();
		### Get employee details
                $empName=$empRow['empname'];
		echo "*** For a Employee: $empName***";
                $empId=$empRow['empid'];
                $empDept=$empRow['dept'];
                $empRole=$empRow['role'];
		$empTrack=$empRow['track'];
		$empEmailId=$empRow['emp_emailid'];

		### Don't track attendence details for few employees
		if ($empTrack == 0) {
			continue;
		}
		$sheetIndex=1;
                echo "\n$delimiter2 For an Emp: $empName $delimiter2\n";
			
                if (strtoupper($empRole) == "MANAGER" and strtoupper($empDept) != "HR" and $empId != "420064")  {
			### Create new PHPExcel object
			echo date('H:i:s') . " Create new PHPExcel object\n";
			$objPHPExcel = new PHPExcel();

			### Set properties
			echo date('H:i:s') . " Set properties\n";
			$objPHPExcel->getProperties()->setCreator("Testing")
                                                   ->setLastModifiedBy("Testing")
                                                   ->setTitle("Weekly Attendence Report")
                                                   ->setSubject("Weekly Attendence Report of $empDept")
                                                   ->setDescription("This is  Weekly untracked attendence report")
                                                   ->setKeywords("office 2007 openxml php")
                                                   ->setCategory("Report");

                        echo "\nManager Name:$empName, mangerid: $empId\n";
			$rowIndex=1;
			formatExcelSheet($sheetIndex, $rowIndex);
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$objPHPExcel->getActiveSheet()->setTitle("$empName");
			$string="Department: $empDept ,  Manager: $empName ";
			$objPHPExcel->getActiveSheet()->getRowDimension($rowIndex)->setRowHeight(50);
			$objPHPExcel->getActiveSheet()->setCellValue("A$rowIndex", "$string");
			$objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->applyFromArray($mainHeaderFormatting);
			$rowIndex=$rowIndex+3;
			$currSheet=$sheetIndex;
                        $empQuery="SELECT * FROM `emp` where `managerid`='".$empId."' and state='Active'";
			echo "\nQuery: $empQuery\n";
                        $empResult=$db -> query($empQuery);
			$empCount=1;
			while ($eachEmpRow=mysql_fetch_assoc($empResult)) {
				$eName=$eachEmpRow['empname'];
				$eRole=$eachEmpRow['role'];
				$eId=$eachEmpRow['empid'];
				$eDept=$eachEmpRow['dept'];
				$eTrack=$eachEmpRow['track'];
				echo "Pushing to dept: $eDept\n";
				$empList[$eDept][] ='';
				array_push($empList[$eDept],$eId);
				### Don't track attendence for few employees
				if ($eTrack == 0) {
					continue;
				}
				echo "\nEmp Name:$eName and Role: $eRole\n";
				if (strtoupper($eachEmpRow['role']) == "MANAGER" and $eId != "325020") {
					$objPHPExcel->setActiveSheetIndex($currSheet);
                                        $objPHPExcel->getActiveSheet()->getStyle("A$rowIndex:E$rowIndex")->applyFromArray($managerFormatting);
                                        $rowIndex=updateExcelSheet($currSheet,$rowIndex,$empCount,$eName,$eId,$eDept);
					$sheetIndex++;
					$managerRowIndex=1;
					formatExcelSheet($sheetIndex, $managerRowIndex);
					$objPHPExcel->setActiveSheetIndex($sheetIndex);
					$objPHPExcel->getActiveSheet()->setTitle("$eName");
					$string="Department: $eDept ,  Manager: $eName ";
					$objPHPExcel->getActiveSheet()->setCellValue("A$managerRowIndex", "$string");
					$managerRowIndex=$managerRowIndex+2;
					$sheetIndex=getSubOrdinates($sheetIndex,$eachEmpRow['empid'],$managerRowIndex);

				} else {
					$rowIndex=updateExcelSheet($currSheet,$rowIndex,$empCount,$eName,$eId,$eDept);	
				}
				$empCount++;
			}
			array_push($empList[$eDept],$empId);
			echo "Generating report for manager: $empName";
			### Add brief report to the excel sheet
			generateManagerBriefReport(0,$eDept);			
	
			$fileName=$empName."(".$fromDate." to ". $toDate . ")".date('Y-m-d H-m-s').".xls";
                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        $objWriter->save("lmsReport/".$fileName);
			if (isset($mailList)) {
				$to=$mailList;
			} else {
				$to=$empEmailId;
			}
			$empMailBody="<p>Hi $empName,<br><br>Attached LMS report from $fromDate to $toDate for your team. Please find the attachment</p><br>Thanks,<br>DevOps Team";
			$sub="Access details from $fromDate to $toDate";
			echo "Sending Mail with attachment to: $to";
			sendMailwithAttachment($to,$empMailBody,$sub,"lmsReport/".$fileName);
                }
		### Send Individual mail for each employee
                echo "\nSend user details\n";
		if (isset($mailList)) {
                	$to=$mailList;
                } else {
                        $to=$empEmailId;
                }
		$sub="Access details from $fromDate to $toDate for $empName";
		$empMailBody="<p>Hi $empName,<br><br>Following is the information where employee leave data is not updated in LMS from $fromDate to $toDate. Please update it.</p>";
		$empTable="<table border='1'>
                           	<thead>
                                	<tr>
                                        	<th>Date</th>
                                                <th>In Time</th>
                                                <th>Out Time</th>
                                                <th>Time Difference</th>
                                                <th>Reason</th>
                                        </tr>
                                </thead><tbody>";
		$inOutQuery="SELECT * FROM `inout` where `Date` between '$fromDate' and '$toDate' and `EmpID`='$empId' and `state` != 'Data Exists'";
		$inOutResult=$db -> query($inOutQuery);
		$entryExists=0;
        	while ($inOutRow=mysql_fetch_assoc($inOutResult)) {
        	        $dateEmp=$inOutRow['Date'];
	                $inTime=$inOutRow['First'];
        		$outTime=$inOutRow['Last'];
	        	$diffTime=timediffinHR($inTime,$outTime);
		        $reason=$inOutRow['state'];
			$empTable=$empTable."<tr></tr><tr>
                                        <td>$dateEmp</td>
                                        <td>$inTime</td>
                                        <td>$outTime</td>
                                        <td>$diffTime</td>
                                        <td>$reason</td>
                                      </tr>";	
			$entryExists=1;
        	}
		if ($entryExists == 1) {
			$empTable=$empTable."</tbody></table>";
			$empMailBody=$empMailBody.$empTable;
			$empMailBody=$empMailBody."<br>Thanks,<br>DevOps Team";
			sendMail($to,$empMailBody,$sub);
		}
                echo "\n$delimiter2 End for Emp: $empName $delimiter2\n";
        }
}


if ($hrReport == 1) {
	echo "\nGenerating Brief Report\n";
	### Create new PHPExcel object
	echo date('H:i:s') . " Create new PHPExcel object\n";
	$objPHPExcel = new PHPExcel();
	
	### Set properties
	echo date('H:i:s') . " Set properties\n";
	$objPHPExcel->getProperties()->setCreator("Testing")
	                           ->setLastModifiedBy("Testing")
	                           ->setTitle("Weekly Attendence Report")
	                           ->setSubject("Weekly Attendence Report  all Departments")
	                           ->setDescription("This is  Weekly untracked attendence report")
	                           ->setKeywords("office 2007 openxml php")
	                           ->setCategory("Report");
	
	$hrSheetIndex=0;
	
	### Generate a report for Srini and HR
	generateAllBreifReport($hrSheetIndex);
	$hrSheetIndex++;
	
	### Create a Excel sheet for HR
	$deptQuery="SELECT distinct(mainDept) as deptName FROM `departments` WHERE `deptLocation`='$location'";
	$deptResult=$db -> query($deptQuery);
	
	while ($deptRow=mysql_fetch_assoc($deptResult)) {
		$mainDept=$deptRow['deptName'];
		$hrRowIndex=1;
		$objPHPExcel->createSheet();
	        $objPHPExcel->setActiveSheetIndex($hrSheetIndex);
	        $objPHPExcel->getActiveSheet()->setTitle("$mainDept");
	        $objPHPExcel->getActiveSheet()->mergeCells("A$hrRowIndex:E$hrRowIndex");
	        $objPHPExcel->getActiveSheet()->getStyle("A$hrRowIndex:E$hrRowIndex")->applyFromArray($headerFormatting);
	        $string="For a Main Department: $mainDept";
	        $objPHPExcel->getActiveSheet()->setCellValue("A$hrRowIndex", "$string");
	        $hrRowIndex=$hrRowIndex+3;
		### Get Sub Departments for a Main Department
		$subDeptQuery="SELECT * from `departments` where `mainDept`='$mainDept'";
		$subDeptQueryResult=$db -> query($subDeptQuery);
		while($subDeptRow=mysql_fetch_assoc($subDeptQueryResult)) {
			$subDept=$subDeptRow['subDept'];
			formatExcelSheet($hrSheetIndex,$hrRowIndex);
			$objPHPExcel->setActiveSheetIndex($hrSheetIndex);
	       		$string="For a Department: $subDept";
	        	$objPHPExcel->getActiveSheet()->setCellValue("A$hrRowIndex", "$string");
			$hrRowIndex++;	
			### Employee details of sub department
			$deptEmployeeQuery="SELECT * from `emp` where `dept`='$subDept' and `state`='Active'";
			$empCount=1;
			$deptEmployeeResult=$db -> query($deptEmployeeQuery);
			while ($deptEmployeeRow=mysql_fetch_assoc($deptEmployeeResult)) {
				$ename=$deptEmployeeRow['empname'];
				$eid=$deptEmployeeRow['empid'];
				$erole=$deptEmployeeRow['role'];
				$etrack=$deptEmployeeRow['track'];
				### Don't track attendence for few employees
				if ($etrack == 0) {
					continue;
				}
				$hrRowIndex=updateExcelSheet($hrSheetIndex,$hrRowIndex,$empCount,$ename,$eid,$erole);
				$empCount++;
			}
			$hrRowIndex=$hrRowIndex+4;
		}
		$hrSheetIndex++;
	}
	
	$fileName="LMSReport(".$fromDate." to ". $toDate . ")".date('Y-m-d H-m-s').".xls";
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save("lmsReport/".$fileName);

	$locationName='';	
	### Send Details to HR
	if (isset($mailList)) {
        	$to=$mailList;
        } else {
		if ($location == "MUM") {
			$to="harshada.koli@ecitele.com,jilsha.sivaram@ecitele.com,sheela.naveen@ecitele.com";
			$locationName="Mumbai";
		} else {
	        	$to="srinivas.goli@ecitele.com,BLR-HR@ecitele.com";
			$locationName="Bangalore";
		}
        }
	$hrMailBody="<p>Hi All,<br><br>Attached LMS report of $locationName team, from $fromDate to $toDate. Please find the attachment</p><br>Thanks,<br>DevOps Team";
	$subject="Access details from $fromDate to $toDate";
	echo "Sending Mail with attachment to: $to";
	sendMailwithAttachment($to,$hrMailBody,$subject,"lmsReport/".$fileName);
}
$db->closeConnection();
?>

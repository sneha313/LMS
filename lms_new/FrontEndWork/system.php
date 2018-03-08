 <?php
	session_start();
	//require_once 'Library.php';
	//require_once 'generalFunctions.php';
	require_once 'librarycopy1.php';
	require_once 'generalcopy.php';
	$db=connectToDB();
	if (isset($_REQUEST['table'])) 
	{
		echo "<table class='table table-bordered'><tr class='info'><th>Date on which vehicle used</th><th>Day</th><th>To and from Kms</th><th>Fuel cost per Km</th><th>Total cost</th></tr>";
		if (isset ( $_REQUEST['date'])) {
			$date= $_REQUEST['date'];
		} else {
			$date=date("F").",".date("Y");
		}
		$employee_number=$_SESSION['u_empid'];
		//$res = $db->query("SELECT * FROM dynamic_table WHERE claim_period='$date'and employee_number='$employee_number'");
		//while($row1 = $db->fetchArray($res)) {
		$query="SELECT * FROM dynamic_table WHERE claim_period='$date'and employee_number='$employee_number'";
		$res = $db->pdoQuery($query)->results();
		//while($row1 = $res->results()) {
		foreach ($res as $row1){
			$residence_distance=$row1['distance_from_residenceto_office'];
			$fuellitre=$row1['fuelcost_perlitre'];
			$milage_cost=$row1['vehicle_milage'];
		}
	   // $result = $db->query("SELECT * FROM vehicleuseddays WHERE claim_period='$date' and employee_number='$employee_number'");
	    //$sqlQuery = $db->query("SELECT * FROM specificdays WHERE claim_period='$date' and employee_number='$employee_number'");
	    //$count=$db->countRows($result);
	    //$count1=$db->countRows($sqlQuery);
		$queryres="SELECT * FROM vehicleuseddays WHERE claim_period='$date' and employee_number='$employee_number'";
	    $result = $db->pdoQuery($queryres);
	    $queryresult="SELECT * FROM specificdays WHERE claim_period='$date' and employee_number='$employee_number'";
	    $sqlQuery = $db->pdoQuery($queryresult);
	    $count=$result -> count($sTable = 'vehicleuseddays', $sWhere = 'claim_period = "'.$date.'" and employee_number = "'.$employee_number.'"' );
	    $count1=$sqlQuery-> count($sTable = 'specificdays', $sWhere = 'claim_period = "'.$date.'" and employee_number = "'.$employee_number.'"' );
	     
	    //$count=$result->rowCount();
	    //$count1=$sqlQuery->rowCount();
	    $total=$count+$count1;
	    $overall_cost=0;
	    $total_distance=0;
   		if($count != 0)
    	{
	    	$toandfro_kms=round(2*$residence_distance,2);
    		$fuel_cost=round($fuellitre/$milage_cost,2);
    		$total_cost=round($toandfro_kms*$fuel_cost,2);
    		//while($row1 = $db->fetchArray($result))
    			$rows1=$db->pdoQuery($queryres)->results();
    			foreach ($rows1 as $row1)
    		//while($row1 = $result->results())
    		{
	    		$days=$row1['vehicleused_days'];
    			//echo $days;
    			$dates=$row1['vehicleused_dates'];
    			$overall_cost=$overall_cost+$total_cost;
    			$total_distance=$total_distance+$toandfro_kms;
    		 	echo "<tr><td>
	    		<input readonly type='text' class='form-control' value='$dates'/></td><td>
	    		<input readonly type='text' class='form-control' value='$days'/></td><td>
	    		
	    		<input readonly type='text' class='form-control' value='$toandfro_kms'/></td><td>
	    		<input readonly type='text' class='form-control' value='$fuel_cost'/></td><td>
	    		<input readonly type='text' class='form-control' value='$total_cost'/></td></tr>";
    		}
    		$rows=$db->pdoQuery($queryresult)->results();
    		//while($row1 = $db->fetchArray($sqlQuery))
    		//while($row1 = $sqlQuery->results())
    			foreach ($rows as $row1)
    		{
    			$days=$row1['vehicleused_days'];
    			//echo $days;
    			$dates=$row1['vehicleused_dates'];
    			$overall_cost=$overall_cost+$total_cost;
    			$total_distance=$total_distance+$toandfro_kms;
    			echo "<tr bgcolor='grey'><td>
    			<input style='font-weight:bold' readonly type='text' class='form-control' value='$dates'/></td><td>
    			<input style='font-weight:bold' readonly type='text'  class='form-control' value='$days'/></td><td>
    		
    			<input style='font-weight:bold' readonly type='text' class='form-coontrol' value='$toandfro_kms'/></td><td>
    			<input style='font-weight:bold' readonly type='text' class='form-control' value='$fuel_cost'/></td><td>
    			<input style='font-weight:bold' readonly type='text' class='form-control' value='$total_cost'/></td></tr>";
    		}
    		echo "<tr><td>Total Days</td><td><input readonly type='text' class='form-control' id='total_days' value='$total'/></td><td><input readonly type='text' class='form-control' id='totaltoandfrokms' value='$total_distance'/></td><td><input readonly type='text' class='form-control'/></td><td><input readonly type ='text' id='overall_cost'value='$overall_cost' class='form-control'/></td></tr>";
    	} else {
	    	$employee_number=$_SESSION['u_empid'];
	    	//$resultset1 =  $db->query("SELECT * FROM dynamic_table WHERE employee_number='$employee_number'");
	    	//$count = $db->countRows($resultset1);
	    	$queryresultset="SELECT * FROM dynamic_table WHERE employee_number='$employee_number'";
	    	$resultset1 =  $db->pdoQuery($queryresultset);
	    	$count =$resultset1 -> count($sTable = 'dynamic_table', $sWhere = 'employee_number = "'.$employee_number.'" ' );
				
	    	if($count!=0)
	    	{
	    		//mysql_data_seek($resultset1, ($count - 1));

	    		$results=$db->prepare($queryresultset, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	    		$results->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_ABS, ($count - 1));
	    		//while($row1 = $db->fetchArray($resultset1)) 
	    			$rows1=$db->pdoQuery($queryresultset)->results();
	    		//while($row1 = $resultset1->results()) 
	    			foreach ($rows1 as $row1)
	    		{
	    			$residence_distance=$row1['distance_from_residenceto_office'];
	    			$fuellitre=$row1['fuelcost_perlitre'];
	    			$milage_cost=$row1['vehicle_milage'];
	    		}
	    		$toandfro_kms=round(2*$residence_distance,2);
	    		$fuel_cost=round($fuellitre/$milage_cost,2);
	    		$total_cost=round($toandfro_kms*$fuel_cost,2);
	    	}
	    	$monthArray = explode (",", $date);
	    	$month=$monthArray[0];
	    	$nmonth = date('m',strtotime($month));
	    	$year=$monthArray[1];
	    	$days=cal_days_in_month(CAL_GREGORIAN,$nmonth,$year);
	    	//echo $days;
	    	$total_days=0;
    		//echo $work;
    		$dotw=date('l', strtotime("$year-$nmonth-1"));
			for($i=1; $i<=$days; $i++)
			{
				//$res1 = $db->query("SELECT * FROM `inout` WHERE Date='$year-$nmonth-$i' and empid='$employee_number'") or die(mysql_error());
				//echo $year-$nmonth-$i;
				//if($db->countRows($res1)!= 0)
				$query1="SELECT * FROM `inout` WHERE Date='$year-$nmonth-$i' and empid='$employee_number'";
				$res1 = $db->pdoQuery($query1);

				$calender= '$year-$nmonth-$i';
				$rowcount=$res1 -> count($sTable = 'inout', $sWhere = 'Date = "'.$calender.'" and empid = "'.$employee_number.'"' );
				
				if($rowcount!= 0)
				{
			     	if($count!=0)
			     	{
						$jd=cal_to_jd(CAL_GREGORIAN,$nmonth,$i,$year);
						$day=jddayofweek($jd,1);
						$overall_cost=$overall_cost+$total_cost;
						$total_distance=$total_distance+$toandfro_kms;
						echo "<tr>
								<td>
									<input readonly type='text' class='form-control' value='$i/$month/$year'name='dates[$i]'/></td>
								<td>
									<input readonly type='text' class='form-control' id='days$i'value='$day'name='days[$i]'/></td>
								<td>";
					echo "
						
						<input readonly type='text' class='form-control' id='toandfrokms$i'name='toandfrokms' value='$toandfro_kms'/></td><td>
						<input readonly type='text' class='form-control' id='fuel_cost_per_km$i'name='fuelcost' value='$fuel_cost'/></td>
						<td><input readonly type='text' class='form-control' id='total_cost$i'name='totalcost' value='$total_cost'/></td></tr>";  
					$total_days+=1;
			     }	   
	     else {
	     		$jd=cal_to_jd(CAL_GREGORIAN,$nmonth,$i,$year);
				$day=jddayofweek($jd,1);
				echo "<tr>
				    	<td>
				     	<input readonly type='text' class='form-control' value='$i/$month/$year'name='dates[$i]'/></td><td>
				     	<input readonly type='text' class='form-control' id='days$i'value='$day'name='days[$i]'/></td><td>";
				echo "
				     	
				     	<input readonly type='text' class='form-control' id='toandfrokms$i'name='toandfrokms'/></td><td>
				     	<input readonly type='text' class='form-control' id='fuel_cost_per_km$i'name='fuelcost' /></td>
				     	<td><input readonly type='text' class='form-control' id='total_cost$i'name='totalcost'/></td></tr>";
				$total_days+=1;
	     	 }
		}
	}
	echo "<tr><td>Total Days</td><td><input readonly type='text' class='form-control' id='total_days' value='$total_days'/></td><td><input type='text' class='form-control' id='totaltoandfrokms' value='$total_distance'/></td><td><input readonly type='text' class='form-control'/></td><td><input readonly id='overall_cost'value='$overall_cost' name='total_fuelcost' class='form-control'/></td>
	<td><input type='button' class='btn btn-primary' id='addrow' value='ADD DAY' onclick='addRow();'/></td></tr></table>";
	}
	}
	if (isset($_REQUEST['table1']))
	{
		$date= $_REQUEST['specific_date'];
		$number=$_REQUEST['employee_number'];
		$dates=explode("/", $date);
		$nmonth = date('m',strtotime($dates[1]));
		/*$query = $db->query("SELECT * FROM `inout` WHERE Date='$dates[2]-$nmonth-$dates[0]' and empid='$number'") or die(mysql_error());
		//$query1 = $db->query("SELECT * FROM `perdaytransactions` WHERE Date='$dates[2]-$nmonth-$dates[0]' and empid='$number' and leavetype Not in('WFH','HalfDay') AND shift not in('firstHalf' ,'secondHalf')") or die(mysql_error());
		$query2 = $db->query("SELECT * FROM `empoptionalleavetaken` WHERE Date='$dates[2]-$nmonth-$dates[0]' and empid='$number' and state='Approved'") or die(mysql_error());
		$query3 = $db->query("SELECT * FROM `empleavetransactions` WHERE '$dates[2]-$nmonth-$dates[0]'>=startdate and '$dates[2]-$nmonth-$dates[0]'<=enddate and empid='$number' and approvalstatus='Approved' and count>=1") or die(mysql_error());
		$query1 = $db->query("SELECT * FROM `empleavetransactions` WHERE '$dates[2]-$nmonth-$dates[0]'>=startdate and '$dates[2]-$nmonth-$dates[0]'<=enddate and empid='$number' and approvalstatus='Approved' and count=0") or die(mysql_error());
		$query4 = $db->query("SELECT * FROM `emp` WHERE day(birthdaydate)='$dates[0]' and month(birthdaydate)='$nmonth' and empid='$number'") or die(mysql_error());
		$count=$db->countRows($query1);
		if($db->countRows($query)!=0)*/
		$queryres="SELECT * FROM `inout` WHERE Date='$dates[2]-$nmonth-$dates[0]' and empid='$number'";
		$query = $db->pdoQuery($queryres);
		$count=$query -> count($sTable = 'inout', $sWhere = 'Date = "'.$dates[2]-$nmonth-$dates[0].'" and empid = "'.$number.'"' );
				
		//$query1 = $db->query("SELECT * FROM `perdaytransactions` WHERE Date='$dates[2]-$nmonth-$dates[0]' and empid='$number' and leavetype Not in('WFH','HalfDay') AND shift not in('firstHalf' ,'secondHalf')") or die(mysql_error());
		$queryres2="SELECT * FROM `empoptionalleavetaken` WHERE Date='$dates[2]-$nmonth-$dates[0]' and empid='$number' and state='Approved'";
		$query2 = $db->pdoQuery($queryres2);
		$count2=$query2 -> count($sTable = 'empoptionalleavetaken', $sWhere = 'Date = "'.$dates[2]-$nmonth-$dates[0].'" and empid = "'.$number.'" and state="Approved"' );
				
		$queryres3="SELECT * FROM `empleavetransactions` WHERE '$dates[2]-$nmonth-$dates[0]'>=startdate and '$dates[2]-$nmonth-$dates[0]'<=enddate and empid='$number' and approvalstatus='Approved' and count>=1";
		$query3 = $db->pdoQuery($queryres3);
		$count3=$query3 -> count($sTable = 'empleavetransactions', $sWhere = 'startdate <= "'.$dates[2]-$nmonth-$dates[0].'" and enddate>="'.$dates[2]-$nmonth-$dates[0].'" and empid = "'.$number.'" and approvalstatus="Approved" and count >= 1' );
				
		$queryres1="SELECT * FROM `empleavetransactions` WHERE '$dates[2]-$nmonth-$dates[0]'>=startdate and '$dates[2]-$nmonth-$dates[0]'<=enddate and empid='$number' and approvalstatus='Approved' and count=0";
		$query1 = $db->pdoQuery($queryres1);
		$count1=$query1 -> count($sTable = 'empleavetransactions', $sWhere = 'startdate <= "'.$dates[2]-$nmonth-$dates[0].'" and enddate>="'.$dates[2]-$nmonth-$dates[0].'" and empid = "'.$number.'" and approvalstatus="Approved" and count = 0' );
				
		$queryres4="SELECT * FROM `emp` WHERE day(birthdaydate)='$dates[0]' and month(birthdaydate)='$nmonth' and empid='$number'";
		$query4 = $db->pdoQuery($queryres4);
		$count4=$query4 -> count($sTable = 'emp', $sWhere = 'day(birthdaydate) = "'.$dates[0].'" and month(birthdaydate)>="'.$nmonth.'" and empid = "'.$number.'"' );
				
		//$count=$query1->rowCount();
		if($count!=0)
		{
			echo "The day is already present in table";
			
		}
		//elseif($db->countRows($query2)!=0)
		elseif($count2!=0)
		{
			echo "For that date you have taken optional leave";
		}
		elseif($count3!=0)
		{
			echo "For that date you are on leave";
		}
		elseif($count1!=0)
		{
			echo "For that date you are work from home or on special leave";
		}
		elseif($count4 !=0)
		{
			echo "selected day is your Birthday you are on leave on that day";
		}
		else
		{
			$jd=cal_to_jd(CAL_GREGORIAN,$nmonth,$dates[0],$dates[2]);
			$day=jddayofweek($jd,1);
			echo "$date%$day";
	    }
	   
	}
	if (isset($_REQUEST['data'])) {
		$date=$_REQUEST['date'];
		$employee_number=$_REQUEST['employee_number'];
	  /*	$result = $db->query("SELECT * FROM dynamic_table WHERE claim_period='$date'and employee_number='$employee_number'");
	  	$rs=$db->query("SELECT * FROM dynamic_table WHERE employee_number='$employee_number'");
	  	$count = $db->countRows($rs);*/
		$query="SELECT * FROM dynamic_table WHERE claim_period='$date'and employee_number='$employee_number'";
	  	$result = $db->pdoQuery($query)->results();
	  	$query1="SELECT * FROM dynamic_table WHERE employee_number='$employee_number'";
	  	$rs=$db->pdoQuery($query1)->results();
	  	//$count = $rs->rowCount();
	  	$count=$rs -> count($sTable = 'dynamic_table', $sWhere = 'employee_number = "'.$employee_number.'"' );
	  	$count1=$result -> count($sTable = 'dynamic_table', $sWhere = 'claim_period = "'.$date.'" and employee_number="'.$employee_number.'"' );
		//if($db->countRows($result) != 0)
		if($count1 != 0)
		{
			//$sql=$db->query("SELECT * FROM `emp`,`empprofile` WHERE emp.empid='$employee_number' and empprofile.empid='$employee_number'");
			$query="SELECT * FROM `emp`,`empprofile` WHERE emp.empid='$employee_number' and empprofile.empid='$employee_number'";
			$sql=$db->pdoQuery($query)->results();
			//while ($row = $db->fetchArray($sql))
			//while ($row = $sql->results())
				foreach ($sql as $row)
			{
				if(isset($row['empname']))
							$employee_name=$row['empname'];
					else 
						$employee_name="";
					if(isset($row['address']))
						$homeAddress=$row['address'];
					else
						$homeAddress="";
					if(isset($row['fathername']))
						$father_name=$row['fathername'];
					else
						$father_name="";
			
				if ($row['location'] == "BLR") {
					$official_address="ECI Telecom India PVt. Ltd., 5th Floor, Innovator Building, ITPL, Whitefield, Bangalore-560066";
				} elseif ($row['location'] == "MUM") {
					$official_address="ECI Telecom India Pvt. Ltd. Unit No 901,9thFloor,Awing Reliable Tech Park, Airoli Thane Belapur Road MIDC, Navi Mumbai-400708.";
				}	
			}
			//while($row1 = $db->fetchArray($result)) {
			//while($row1 = $result->results()) {
			foreach ($result as $row1){
				$employee_number=$row1['employee_number'];
				$claim_period=$row1['claim_period'];
			    $vehicle_regno=$row1['vehicle_regno'];
				$vehicle_model=$row1['vehicle_model'];
				$nature_offuel=$row1['nature_offuel'];
				$original_cost=$row1['original_cost'];
				$distance=$row1['distance_from_residenceto_office'];
				$milage= $row1['vehicle_milage'];
				$fuelcost=$row1['fuelcost_perlitre'];
				$repairs=$row1['repairs_maintence_expenses'];
				$wear_tearcost=$row1['wear_tear_cost'];
				$driversal=$row1['driver_salary'];
				$totalvoe=$row1['total_voe'];
				echo "$employee_number%$claim_period%$employee_name%$homeAddress%$official_address%$father_name%$vehicle_regno%$vehicle_model%$nature_offuel%$original_cost%$distance%$milage%$fuelcost%$repairs%$wear_tearcost%$driversal%$totalvoe";
			}
		} else{
			$query="SELECT * FROM `emp`,`empprofile` WHERE emp.empid='$employee_number' and empprofile.empid='$employee_number'";
			//$sql=$db->query("SELECT * FROM `emp`,`empprofile` WHERE emp.empid='$employee_number' and empprofile.empid='$employee_number'");
			$sql=$db->pdoQuery($query)->results();	
			//while ($row = $db->fetchArray($sql))
			//while ($row = $sql->results())
				foreach ($sql as $row)
			{
				if(isset($row['empname']))
							$employee_name=$row['empname'];
					else 
						$employee_name="";
					if(isset($row['address']))
						$homeAddress=$row['address'];
					else
						$homeAddress="";
					if(isset($row['fathername']))
						$father_name=$row['fathername'];
					else
						$father_name="";
				if ($row['location'] == "BLR") {
					$official_address="ECI Telecom India PVt. Ltd., 5th Floor, Innovator Building, ITPL, Whitefield, Bangalore-560066";
				} elseif ($row['location'] == "MUM") {
					$official_address="ECI Telecom India Pvt. Ltd. Unit No 901,9thFloor,Awing Reliable Tech Park, Airoli Thane Belapur Road MIDC, Navi Mumbai-400708.";
				}
			}
			if ($count!=0) {
			//mysql_data_seek($rs, ($count - 1));

				$results=$db->prepare($query1, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
				$results->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_ABS, ($count - 1));
				//FETCH_ORI_ABS
		//	while($row1 = $db->fetchArray($rs)) {
			//while($row1 = $rs->results()) {
				foreach ($rs as $row1){
				$claim_period=$row1['claim_period'];
				$vehicle_regno=$row1['vehicle_regno'];
				$vehicle_model=$row1['vehicle_model'];
				$nature_offuel=$row1['nature_offuel'];
				$original_cost=$row1['original_cost'];
				$distance=$row1['distance_from_residenceto_office'];
				$milage= $row1['vehicle_milage'];
				$fuelcost=$row1['fuelcost_perlitre'];
				$repairs=$row1['repairs_maintence_expenses'];
				$wear_tearcost=$row1['wear_tear_cost'];
				$driversal=$row1['driver_salary'];
				echo "$employee_number%$claim_period%$employee_name%$homeAddress%$official_address%$father_name%$vehicle_regno%$vehicle_model%$nature_offuel%$original_cost%$distance%$milage%$fuelcost%$repairs%$wear_tearcost%$driversal";
			}
		}
		 else {
		 	echo "data%data%$employee_name%$homeAddress%$official_address%$father_name";
		 }
		}
	}
	if (isset($_REQUEST['delete'])) {
		$date=$_REQUEST['date'];
		$employee_number=$_REQUEST['employee_number'];
		//$db->query("DELETE FROM dynamic_table WHERE claim_period='$date' and employee_number='$employee_number'");
             //   $db->query("DELETE FROM vehicleuseddays WHERE claim_period='$date' and employee_number='$employee_number'");
             //   $db->query("DELETE FROM specificdays WHERE claim_period='$date' and employee_number='$employee_number'");

                $dynamic_tableWhere = array('claim_period'=>$date,'employee_number'=>$employee_number);
                // call update function
                $db->delete('dynamic_table', $dynamic_tableWhere)->affectedRows();
                $vehicleuseddaysWhere = array('claim_period'=>$date,'employee_number'=>$employee_number);
                $db->delete('vehicleuseddays', $vehicleuseddaysWhere)->affectedRows();
                $specificdayswhere = array('claim_period'=>$date,'employee_number'=>$employee_number);
                $db->delete('specificdays', $specificdayswhere)->affectedRows();
	}
	//$db->closeConnection();
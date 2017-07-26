 <?php
	session_start();
	require_once 'Library.php';
	require_once 'generalFunctions.php';
	$db=connectToDB();
	if (isset($_REQUEST['table'])) 
	{
		echo "<tr><th>Date on which vehicle used</th><th>Day</th><th>To and fro Kms</th><th>Fuel cost per Km</th><th>Total cost</th></tr>";
		if (isset ( $_REQUEST['date'])) {
			$date= $_REQUEST['date'];
		} else {
			$date=date("F").",".date("Y");
		}
		$employee_number=$_SESSION['u_empid'];
		$res = $db->query("SELECT * FROM dynamic_table WHERE claim_period='$date'and employee_number='$employee_number'");
		while($row1 = $db->fetchArray($res)) {
			$residence_distance=$row1['distance_from_residenceto_office'];
			$fuellitre=$row1['fuelcost_perlitre'];
			$milage_cost=$row1['vehicle_milage'];
		}
	    $result = $db->query("SELECT * FROM vehicleuseddays WHERE claim_period='$date' and employee_number='$employee_number'");
	    $sqlQuery = $db->query("SELECT * FROM specificdays WHERE claim_period='$date' and employee_number='$employee_number'");
	    $count=$db->countRows($result);
	    $count1=$db->countRows($sqlQuery);
	    $total=$count+$count1;
	    $overall_cost=0;
	    $total_distance=0;
   		if($count != 0)
    	{
	    	$toandfro_kms=round(2*$residence_distance,2);
    		$fuel_cost=round($fuellitre/$milage_cost,2);
    		$total_cost=round($toandfro_kms*$fuel_cost,2);
    		while($row1 = $db->fetchArray($result))
    		{
	    		$days=$row1['vehicleused_days'];
    			//echo $days;
    			$dates=$row1['vehicleused_dates'];
    			$overall_cost=$overall_cost+$total_cost;
    			$total_distance=$total_distance+$toandfro_kms;
    		 	echo "<tr><td>
	    		<input readonly type='text' value='$dates'/></td><td>
	    		<input readonly type='text' value='$days'/></td><td>
	    		
	    		<input readonly type='text' value='$toandfro_kms'/></td><td>
	    		<input readonly type='text'value='$fuel_cost'/></td><td>
	    		<input readonly type='text'value='$total_cost'/></td></tr>";
    		}
    		while($row1 = $db->fetchArray($sqlQuery))
    		{
    			$days=$row1['vehicleused_days'];
    			//echo $days;
    			$dates=$row1['vehicleused_dates'];
    			$overall_cost=$overall_cost+$total_cost;
    			$total_distance=$total_distance+$toandfro_kms;
    			echo "<tr bgcolor='grey'><td>
    			<input style='font-weight:bold' readonly type='text' value='$dates'/></td><td>
    			<input style='font-weight:bold' readonly type='text' value='$days'/></td><td>
    		
    			<input style='font-weight:bold' readonly type='text' value='$toandfro_kms'/></td><td>
    			<input style='font-weight:bold' readonly type='text'value='$fuel_cost'/></td><td>
    			<input style='font-weight:bold' readonly type='text'value='$total_cost'/></td></tr>";
    		}
    		echo "<tr><td>Total Days</td><td><input readonly type='text'id='total_days' value='$total'/></td><td><input readonly type='text'id='totaltoandfrokms' value='$total_distance'/></td><td><input readonly type='text'/></td><td><input readonly type ='text' id='overall_cost'value='$overall_cost'/></td></tr>";
    	} else {
	    	$employee_number=$_SESSION['u_empid'];
	    	$resultset1 =  $db->query("SELECT * FROM dynamic_table WHERE employee_number='$employee_number'");
	    	$count = $db->countRows($resultset1);
	    	if($count!=0)
	    	{
	    		mysql_data_seek($resultset1, ($count - 1));
	    		while($row1 = $db->fetchArray($resultset1)) 
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
				$res1 = $db->query("SELECT * FROM `inout` WHERE Date='$year-$nmonth-$i' and empid='$employee_number'") or die(mysql_error());
				//echo $year-$nmonth-$i;
				if($db->countRows($res1)!= 0)
				{
			     	if($count!=0)
			     	{
						$jd=cal_to_jd(CAL_GREGORIAN,$nmonth,$i,$year);
						$day=jddayofweek($jd,1);
						$overall_cost=$overall_cost+$total_cost;
						$total_distance=$total_distance+$toandfro_kms;
						echo "<tr>
								<td>
									<input readonly type='text' value='$i/$month/$year'name='dates[$i]'/></td>
								<td>
									<input readonly type='text' id='days$i'value='$day'name='days[$i]'/></td>
								<td>";
					echo "
						
						<input readonly type='text'id='toandfrokms$i'name='toandfrokms' value='$toandfro_kms'/></td><td>
						<input readonly type='text'id='fuel_cost_per_km$i'name='fuelcost' value='$fuel_cost'/></td>
						<td><input readonly type='text'id='total_cost$i'name='totalcost' value='$total_cost'/></td></tr>";  
					$total_days+=1;
			     }	   
	     else {
	     		$jd=cal_to_jd(CAL_GREGORIAN,$nmonth,$i,$year);
				$day=jddayofweek($jd,1);
				echo "<tr>
				    	<td>
				     	<input readonly type='text' value='$i/$month/$year'name='dates[$i]'/></td><td>
				     	<input readonly type='text' id='days$i'value='$day'name='days[$i]'/></td><td>";
				echo "
				     	
				     	<input readonly type='text'id='toandfrokms$i'name='toandfrokms'/></td><td>
				     	<input readonly type='text'id='fuel_cost_per_km$i'name='fuelcost' /></td>
				     	<td><input readonly type='text'id='total_cost$i'name='totalcost'/></td></tr>";
				$total_days+=1;
	     	 }
		}
	}
	echo "<tr><td>Total Days</td><td><input readonly type='text'id='total_days' value='$total_days'/></td><td><input type='text'id='totaltoandfrokms' value='$total_distance'/></td><td><input readonly type='text'/></td><td><input readonly id='overall_cost'value='$overall_cost' name='total_fuelcost'/></td>
	<td><input type='button' id='addrow' value='ADD DAY' onclick='addRow();'/></td></tr>";
	}
	}
	if (isset($_REQUEST['table1']))
	{
		$date= $_REQUEST['specific_date'];
		$number=$_REQUEST['employee_number'];
		$dates=explode("/", $date);
		$nmonth = date('m',strtotime($dates[1]));
		$query = $db->query("SELECT * FROM `inout` WHERE Date='$dates[2]-$nmonth-$dates[0]' and empid='$number'") or die(mysql_error());
		//$query1 = $db->query("SELECT * FROM `perdaytransactions` WHERE Date='$dates[2]-$nmonth-$dates[0]' and empid='$number' and leavetype Not in('WFH','HalfDay') AND shift not in('firstHalf' ,'secondHalf')") or die(mysql_error());
		$query2 = $db->query("SELECT * FROM `empoptionalleavetaken` WHERE Date='$dates[2]-$nmonth-$dates[0]' and empid='$number' and state='Approved'") or die(mysql_error());
		$query3 = $db->query("SELECT * FROM `empleavetransactions` WHERE '$dates[2]-$nmonth-$dates[0]'>=startdate and '$dates[2]-$nmonth-$dates[0]'<=enddate and empid='$number' and approvalstatus='Approved' and count>=1") or die(mysql_error());
		$query1 = $db->query("SELECT * FROM `empleavetransactions` WHERE '$dates[2]-$nmonth-$dates[0]'>=startdate and '$dates[2]-$nmonth-$dates[0]'<=enddate and empid='$number' and approvalstatus='Approved' and count=0") or die(mysql_error());
		$query4 = $db->query("SELECT * FROM `emp` WHERE day(birthdaydate)='$dates[0]' and month(birthdaydate)='$nmonth' and empid='$number'") or die(mysql_error());
		$count=$db->countRows($query1);
		if($db->countRows($query)!=0)
		{
			echo "The day is already present in table";
			
		}
		elseif($db->countRows($query2)!=0)
		{
			echo "For that date you have taken optional leave";
		}
		elseif($db->countRows($query3)!=0)
		{
			echo "For that date you are on leave";
		}
		elseif($db->countRows($query1)!=0)
		{
			echo "For that date you are work from home or on speecial leave";
		}
		elseif($db->countRows($query4)!=0)
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
	  	$result = $db->query("SELECT * FROM dynamic_table WHERE claim_period='$date'and employee_number='$employee_number'");
	  	$rs=$db->query("SELECT * FROM dynamic_table WHERE employee_number='$employee_number'");
	  	$count = $db->countRows($rs);
		if($db->countRows($result) != 0)
		{
			$sql=$db->query("SELECT * FROM `emp`,`empprofile` WHERE emp.empid='$employee_number' and empprofile.empid='$employee_number'");
			
			while ($row = $db->fetchArray($sql))
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
				$official_address="ECI Telecom India PVt. Ltd., 5th Floor, Innovator Building, ITPL, Whitefield, Bangalore:560066";
			}
			while($row1 = $db->fetchArray($result)) {
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
			$sql=$db->query("SELECT * FROM `emp`,`empprofile` WHERE emp.empid='$employee_number' and empprofile.empid='$employee_number'");
			
			while ($row = $db->fetchArray($sql))
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
				$official_address="ECI Telecom India PVt. Ltd., 5th Floor, Innovator Building, ITPL, Whitefield, Bangalore:560066";
			}
			if ($count!=0) {
			mysql_data_seek($rs, ($count - 1));
			while($row1 = $db->fetchArray($rs)) {
				
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
		$db->query("DELETE FROM dynamic_table WHERE claim_period='$date' and employee_number='$employee_number'");
                $db->query("DELETE FROM vehicleuseddays WHERE claim_period='$date' and employee_number='$employee_number'");
                $db->query("DELETE FROM specificdays WHERE claim_period='$date' and employee_number='$employee_number'");
	}
	$db->closeConnection();


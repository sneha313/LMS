<?php
require_once ("class.pdowrapper.php");
$helper = new PDOHelper();
function getChildren($supervisorID)
{
	global $db;
	$childern=array();
	$childern_Name=array();
	$query='select * from emp where (state="Active" and managerid="'.$supervisorID.'")';
	$sqlrow = $db->pdoQuery($query)->results();
	foreach($sqlrow as $sql)
	array_push($childern,$sql['empid']);
	array_push($childern_Name,$sql['empname']);
	return $childern;
}
function getemp($supervisorID)
{
	$combined=array();
	$child=array();
	$child=getChildren($supervisorID);
	$combined=array_merge($combined,$child);
	for($j=0;$j<sizeof($child);$j++)
	{
		$combined=array_merge($combined,getChildren($child[$j]));
	}
	return $combined;
}
function getTotalLeaves($empid)
{
	global $db;
	$query='select carryforwarded,balanceleaves FROM emptotalleaves where (empid="'.$empid.'") ;';
	$sqlrow = $db->pdoQuery($query)->results();
	foreach($sqlrow as $sql)
	return $sql['carryforwarded']+$sql['balanceleaves'];
}

function getCarryForwardedLeaves($empid)
{
	global $db;
	$query='select carryforwarded FROM emptotalleaves where (empid='.$empid.') ;';
	$sqlres = $db->pdoQuery($query)->results();
	foreach($sqlres as $sql)
	return $sql['carryforwarded'];
}

function showWidget($title, $id) {
	$rs = '<div class="column">
                        <div class="portlet">
                <div class="portlet-header">'.$title.'</div>
                <input type="hidden" id='.$id.'URL value="LoadWidgets.php"></input>
                <div class="portlet-content" id='.$id.'>
                        <h4 style=\'text-decoration:blink\'>Loading . . .</h4>
                </div>
                </div>
                </div>';
	echo $rs;
}



function getOptions($empid)
{
	global $db;
	$options="";
			$splleaveTaken=array();
			$sql = $db->pdoQuery('select birthdaydate FROM emp where (state=? and empid=?) ;',array('Active',$empid))->results();
			$dob="";
				$dob = $sql['birthdaydate'];
			list($year,$month,$day) = explode('-', $dob);
			$thismonth = date("m");$thisday = date("d");
			$sql =  $db->pdoQuery('select id, specialleave from specialleaves;')->results();
			$sql1 =  $db->pdoQuery('select splleavetaken from empsplleavetaken where (empid=?) ;',array($empid))->results();
			//$sql1 = $db->query("select splleavetaken from empsplleavetaken where empid = '".$empid."'");
			/*if($sql1)
			{
				while($row1 = $sql1->fetch(PDO::FETCH_ASSOC))
				{*/
					$splleavesString = $sql1['splleavetaken'];
					$splleaveTaken = explode(':', $splleavesString);
				/*}
			}
			while ($row = $sql->fetch(PDO::FETCH_ASSOC))
			{*/
				$splPending=$sql['id']."P";
				$splApproved=$sql['id']."A";
				if ((in_array($splPending,$splleaveTaken))||(in_array($splApproved,$splleaveTaken)))
				{

				}
				else
				{
					if($sql['id'] == 5)
					{
						if($thismonth<=$month && $thisday <=$day)
						{
							$options.='<option value='.$sql['id'].'>'.$sql['specialleave'].'</option>';
						}
					}
					else
					{
						$options.='<option value='.$sql['id'].'>'.$sql['specialleave'].'</option>';
					}

				}
			/*}*/
			while ($row=$db->fetchArray($sql))
			{
				$options.='<option value='.$row['id'].'>'.$row['specialleave'].'</option>';
			}
		return $options;
	
	
}

function getEmpSelectionBox($supervisorID,$selectedEmployee)
{
	global $db;
	//$leavetype=$db->query("select id,regularleaveid,regularleave from regularleaves");
	echo '<form method="POST" name="teamleavereportName" id="teamleavereportId" action="teamleavereport.php">
		 <div class="panel panel-primary">
			<div class="panel-heading text-center">
				<strong style="font-size:20px;">Team Leave Report</strong>
			</div>
			<div class="panel-body">
			<div class="form-group">
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-4"><label>Leave Status Information for Employee</label></div>
		 		<div class="col-sm-4"><select class="form-control" name = "empid" id="empid">
		 <option value="Choose">Choose</option>
		 <option value="All">All</option>';
	$query="select dept from emp where empid='".$supervisorID."' and state='Active'";
	$deptresult= $db->pdoQuery($query)->results();
	foreach($deptresult as $deptRow){
	//$deptresult=$db->query($query);
	//$deptRow=$db->fetchArray($deptresult);
	//$deptRow=$deptresult->fetchAll(PDO::FETCH_ASSOC);
	if($deptRow['dept']=="HR") {
		$getAllEmpQuery="select empid from emp where `state`='Active'";
		//$getAllEmpresult=$db->query($getAllEmpQuery);
		$getAllEmpresults= $db->pdoQuery($getAllEmpQuery)->results();
		
		$emplist=array();
		foreach($getAllEmpresults as $getAllEmpresult){
		//while($getAllEmpRow = $getAllEmpresult->fetch(PDO::FETCH_ASSOC)) {
				array_push($emplist,$getAllEmpresult['empid']);
		}
	} else {
		$emplist=getemp($supervisorID);	
	}
	$userInformation=array();
	for($i=0;$i<sizeof($emplist);$i++)
	{
		$query= $db->pdoQuery('select empname from emp where (state = ? and empid=?) ;',array('Active',$emplist[$i]))->results();
		foreach($query as $sql){
		//$sql = $db->query("SELECT empname FROM emp where `state`='Active' and empid=".$emplist[$i]);
		//$result = $sql->fetchAll(PDO::FETCH_ASSOC);
		//if ($result) {
			$userInformation[$sql['empname']]=$emplist[$i];
		}
		//}
	}
	}
	ksort($userInformation);
	foreach ($userInformation as $key => $value) {
		if(getempName($selectedEmployee)==$key) {
			echo '<option value="'.$value.'" selected>'.$key.'</option>';
		} else {
			echo '<option value="'.$value.'">'.$key.'</option>';
		}
	}
	echo'</select></div>';
			
	$leavetypequery=$db->pdoQuery('select id,regularleaveid,regularleave from regularleaves;');
	$leavetyperow=$db->pdoQuery('select id,regularleaveid,regularleave from regularleaves;')->results();
			echo '<div class="col-sm-2"></div>
			</div></div>
			<div class="form-group">
			<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-4"><label>Select Leave Type</label></div>
			<div class="col-sm-4"><select class="form-control" name="leaveType" id="leaveTypeId">
			<option value="ALL">ALL</option>';
			//if($db->countRows($leavetype) > 0){
				//for($i=0; $i< $leavetype -> rowCount(); $i++)
				//{
					//$row= $leavetype->fetchAll(PDO::FETCH_ASSOC);
					foreach($leavetyperow as $leavetype){
					echo '<option value="'.$leavetype['regularleave'].'">'.$leavetype['regularleave'].'</option>';
				}
			//}
			echo '</select></div>
				<div class="col-sm-2"></div>
			</div></div>';
			
	echo "<div class='form-group'>
			<div class='row'>
			<div class='col-sm-2'></div>
			<div class='col-sm-4'><label>From Date</label></div>
			<div class='col-sm-4'>
			<div class='input-group'>
				<input type='text' id='datetimepicker' class='form-control open-datetimepicker' name='fromdate' value='".date('Y-m-d', strtotime("first day of january " . date('Y')))."' size='8' />
				<label class='input-group-addon btn' for='date'>
					<span class='fa fa-calendar '></span>
				</label>
			</div>
				</div>
			<div class='col-sm-2'></div>
		</div></div>
		<div class='form-group'>
			<div class='row'>
			<div class='col-sm-2'></div>
			<div class='col-sm-4'><label>To Date</label></div>
			<div class='col-sm-4'>
				<div class='input-group'>
				<input type='text' id='datetimepicker1' class='form-control open-datetimepicker1' name='todate' value=".date('Y-m-d')." size='8' />
				<label class='input-group-addon btn' for='date'>
					<span class='fa fa-calendar'></span>
				</label>
			</div>
			</div>
			<div class='col-sm-2'></div>
		</div></div>";
	echo '<div class="form-group">
			<div class="row">
			<div class="col-sm-12 text-center"><input class="submit btn btn-primary" type="submit" name="submit" value="Submit" /></div></div></div>';
	echo "</div></div></form>";
}

function includeJQGrid()
{
	echo '<link href="public/js/jqueryui/css/redmond/jquery-ui.css" rel="stylesheet">';
  	echo '<link rel="stylesheet" type="text/css" media="screen" href="public/js/jqgrid/jqgridcss/ui.jqgrid.css" />';
  	//echo '<link rel="stylesheet" type="text/css" media="screen" href="public/css/table.css" />';
 	//echo '<script type="text/javascript" src="public/js/jquery/jquery.js"></script>';
  	echo '<script type="text/javascript" src="public/js/jqueryui/js/jquery-ui.js"></script>';
 	echo '<script type="text/javascript" src="public/js/jqgrid/grid.locale-en.js"></script>';
  	echo '<script type="text/javascript" src="public/js/jquery/jquery.validate.min.js"></script>';
  	echo '<script type="text/javascript" src="public/js/jqgrid/jquery.jqGrid.min.js"></script>';
  	echo '<script type="text/javascript" src="public/js/jquery/jquery.searchFilter.js"></script>';
	echo '<script type="text/javascript" src="public/js/countdown/countdown.js"></script>';
	//echo '<link rel="stylesheet" type="text/css" media="screen" href="public/js/countdown/countdown.css" />';
	echo '<script src="projectjs/fullcalendar.js"></script>';
	echo '<script src="public/js/countdown/countdown.js" type="text/javascript"></script>';
	echo '<link rel="stylesheet" type="text/css" media="screen" href="public/js/jqgrid/jqgridcss/ui.jqgrid.css" />';
	echo '<link rel="stylesheet" type="text/css" media="screen" href="public/js/countdown/countdown.css" />';
	echo '<link rel="stylesheet" type="text/css" media="screen" href="public/js/bootstrap/css/bootstrap-theme.css" />';
	//echo '<link rel="stylesheet" type="text/css" media="screen" href="public/js/bootstrap/css/bootstrap.css" />';
}

function connectToDB()
{
	//$config = new config("localhost", "root", "Manor441", "lms", "", "mysql");
	//$db = new db($config);
	//$db->openConnection();
	//return $db;
	$config = array("host"=>"localhost", "dbname"=>'lms', "username"=>'root', "password"=>'Manor441');
	$db = new PdoWrapper($config);
	$db->setErrorLog(true);
	return $db;
}
function auth_by_ldap ($login,$password,$nis_domain='eci_domain'){
	 if($password=="")
                return 0;
        #$ldapconn = ldap_connect("CNHZDC02.ecitele.com")
        $ldapconn = ldap_connect("ilptdc02.ecitele.com")
                or die("Could not connect to LDAP server.");
        $ldapbind = ldap_bind($ldapconn,$nis_domain."\\".$login,$password);
        if ($ldapbind) {
                ldap_unbind( $ldapconn );
                return 1;
        } else {
                return 0;
        }
        return 0;

}


function browser_detection( $which_test ) {
     // initialize the variables
     $browser = '';
     $dom_browser = '';
     // set to lower case to avoid errors, check to see if http_user_agent is set
     $navigator_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
     // run through the main browser possibilities, assign them to the main $browser variable
      if (stristr($navigator_user_agent, "opera"))
      {
          $browser = 'opera';
          $dom_browser = true;
      }
      if (stristr($navigator_user_agent, "Chrome"))
      {
          $browser = 'Chrome';
          $dom_browser = true;
      }
      elseif (stristr($navigator_user_agent, "msie 4"))
      {
          $browser = 'msie4';
          $dom_browser = false;
     }
     elseif (stristr($navigator_user_agent, "msie"))
     {
          $browser = 'msie';
          $dom_browser = true;
     }
     elseif ((stristr($navigator_user_agent, "konqueror")) || (stristr($navigator_user_agent, "safari")))
     {
          $browser = 'safari';
          $dom_browser = true;
     }
     elseif (stristr($navigator_user_agent, "gecko"))
     {
          $browser = 'mozilla';
          $dom_browser = true;
     }
     elseif (stristr($navigator_user_agent, "mozilla/4"))
     {
           $browser = 'ns4';
           $dom_browser = false;
     }
     else
     {
          $dom_browser = false;
          $browser = false;
     }
     // return the test result you want
     if ( $which_test == 'browser' )
     {
          return $browser;
     }
}

function sendMail($to,$mailBody,$sub)
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
		$a=explode(',', $to);
		$i=count($a);
		for($i=0;$i<count($a);$i++)
		{
			$mail->AddAddress($a[$i]);
		}
		$mail->Subject  = $sub;
		$mail->MsgHTML($mailBody);
		$mail->AddBCC("anilkumar.thatavarthi@ecitele.com" , "ECI Leave Management System Reports");
                $mail->AddBCC("naidile.basavegowda@ecitele.com", "ECI Leave Management System Reports");
		$mail->IsHTML(true); // send as HTML
		$mail->Send();
		echo 'Message has been sent.';
	} catch (phpmailerException $e) {
		echo $e->errorMessage();
	}
}

function generate_transaction_id() {
	$id=uniqid();
	return $id;
}
function getSpecialLeavesForType($leaveId,$colName){
	global $db;
	//$sql = $db->query("SELECT ". $colName. " from specialleaves where id = ".$leaveId);
	$query='select '. $colName. ' from specialleaves where (id = "'.$leaveId.'") ;';
	$sql = $db->pdoQuery($query)->results();
	//if($sql) {
	foreach($sql as $row1){
		//while($row1 = $sql->fetch(PDO::FETCH_ASSOC)){
			return $row1[$colName];
		//}
	}
}
function getNumberOfHolidays($start,$end){
	global $db;
	$holidays=array();
	$count=0;
	$query="select date from ".$_SESSION['u_holidayListTable']." where date >= "."'". $start ."'" ." AND date <= " . "'". $end. "'";
	$result = $db->pdoQuery($query)->results();
	//while($row1 = $result->fetch(PDO::FETCH_ASSOC)){
	foreach($result as $row){
		$holidays[$count] = $row['date'];
		$count++;
	}
	return $holidays;
}
function getDatesFromRange($startDate, $endDate)
{
    $return = array($startDate);
    $start = $startDate;
    $i=1;
    if (strtotime($startDate) < strtotime($endDate))
    {
       while (strtotime($start) < strtotime($endDate))
        {
            $start = date('Y-m-d', strtotime($startDate.'+'.$i.' days'));
            $return[] = $start;
            $i++;
        }
    }

    return $return;
}

function removePendingLeaves($num_days,$empid)
{
        global $db;
        $Days=array();
        $pendingDays=array();
        $approvedDays=array();
        $leavetype="";
        $query="SELECT startdate,enddate,transactionid from empleavetransactions where approvalstatus='Approved' and empid = '".$empid."'";
        $approvedQuery = $db->pdoQuery($query);
        $approvedTransactions=array();
        $rows = $db->pdoQuery($query)->results();
        //while($row = mysql_fetch_assoc($approvedQuery)){
        foreach ($rows as $row){
        		array_push($approvedTransactions,$row['transactionid']);
                $Days=getDatesFromRange($row['startdate'],$row['enddate']);
                $approvedDays=array_merge($approvedDays,$Days);
        }
        foreach ($approvedDays as $day)
        {
        	if($a=preg_grep('/^'.$day.'.*/', $num_days))
        	{
        		$key = array_search($day,$num_days);
        		if($key!=-1) {
        			$key=key($a);
        			if (preg_match('/optional/i',$a[$key],$match)) {
        				if(preg_match('/(.*)(\[.*)/', $a[$key],$splMatch)) {
        					foreach ($approvedTransactions as $aTransaction) {
        						$query="select date from perdaytransactions where transactionid='".$aTransaction."'";
        						$queryResult= $db->pdoQuery($query);
        						$optional=array();
        						$rows= $db->pdoQuery($query)->results();
        						foreach($rows as $row){
        						//while($row = mysql_fetch_assoc($queryResult)) {
        							array_push($optional,$row['date']);
        						}
        						if(in_array($splMatch[1], $optional)) {
        							$num_days[$key]=$day." [special] Leave already applied. Check in Employee Leave history";
        						}
        					}
        				}
        			} else {
                         	       $num_days[$key]=$day." [special] Leave already applied. Check in Employee Leave history";
                        	}
        		} 
        	}
        }
        $queryrow="SELECT startdate,enddate,transactionid from empleavetransactions where approvalstatus='Pending' and empid = '".$empid."'";
        $pendingQuery = $db->pdoQuery($queryrow);
        $pendingTransactions=array();
        $rows = $db->pdoQuery($queryrow)->results();
        foreach($rows as $row){
       // while($row = mysql_fetch_assoc($pendingQuery)){
        		array_push($pendingTransactions,$row['transactionid']);
        		$Days=getDatesFromRange($row['startdate'],$row['enddate']);
                $pendingDays=array_merge($pendingDays,$Days);
        }
        foreach ($pendingDays as $day)
        {
        	if($a=preg_grep('/^'.$day.'.*/', $num_days))
        	{
        		$key = array_search($day,$num_days);
        		if($key!=-1) {
        			$key=key($a);
        			if (preg_match('/optional/i',$a[$key],$match)) {
        				if(preg_match('/(.*)(\[.*)/', $a[$key],$splMatch)) {
        					foreach ($pendingTransactions as $pTransaction) {
        						$queryrecord="select date from perdaytransactions where transactionid='".$pTransaction."'";
        						$queryResult= $db->pdoQuery($queryrecord);
        						$optional=array();
        						$rows= $db->pdoQuery($queryrecord)->results();
        						foreach($rows as $row){
        						//while($row = mysql_fetch_assoc($queryResult)) {
	        						array_push($optional,$row['date']);
    	    					}
        						if(in_array($splMatch[1], $optional)) {
        							$num_days[$key]=$day." [special] Leave already applied. Check in Leave Pending Status";
        						}
        					}
        				}
        			} else {
                         	       $num_days[$key]=$day." [special] Leave already applied. Check in Leave Pending Status";
                        	}
        		} 
        	}
        }
        return $num_days;
}


function RegleavesCal($s, $e, $empid){
	global $db;
	date_default_timezone_set('Asia/Kolkata');
	$start = new DateTime($s);
	$end = new DateTime($e);
	//adding one day each time for the start day
	$oneday = new DateInterval("P1D");
	$days = array();
	$num_days =array();
	$holidays =array();
	$count = 0;
	$y=" ";
	$m=" ";
	$d=" ";
	//This loop is for exculding the saturday and sundays between start and end dates selected
	foreach(new DatePeriod($start, $oneday, $end->add($oneday)) as $day) {
		$day_num = $day->format("N"); /* 'N' number days 1 (mon) to 7 (sun) */
		if($day_num < 6) { /* weekday */
			//Stroing the weekdays into an array
			$days[$count] =  $day->format("Y-m-d");
			$count++;
		}
		else {
			if ($day_num == 6) {
					$days[$count]=$day->format("Y-m-d")." [special]"."Saturday";
					$count++;
			}
			if ($day_num == 7) {
				$days[$count]=$day->format("Y-m-d")." [special]"."Sunday";
				$count++;
			} 
		}
	}
	$holidays = getNumberOfHolidays($s,$e);
	$count=0;
	$birth='select birthdaydate from emp where (empid = "'.$empid.'" and state="Active") ;';
	$birthday=$db->pdoQuery($birth)->results();
	foreach($birthday as $birthdayquery){
	$birthdaydate=$birthdayquery['birthdaydate'];
	
	list($by,$bm,$bd) = explode('-', $birthdaydate);
	}
	foreach ($days as $day) {
		if($day!="Saturday" && $day!="Sunday") {
			list($y,$m,$d) = explode('-', $day);
		}
		if(in_array($day, $holidays)){
			$result='select holidayname,leavetype from '.$_SESSION['u_holidayListTable'].' where (date = "'.$day.'") ;';
			$row=$db->pdoQuery($result)->results();
			foreach($row as $row1){
			//$result = $db->query("select holidayname,leavetype from ".$_SESSION['u_holidayListTable']." where date ='".$day."'");
			//while($row1 = $result->fetch(PDO::FETCH_ASSOC)){
				if(($m==$bm) && ($d==$bd)) { 
					$num_days[$count] = $day." [special] ".$row1['holidayname']."(".$row1['leavetype'].") and Employee Birthday";
				} else {
					$num_days[$count] = $day."[special]".$row1['holidayname']."(".$row1['leavetype'].")";
				}
			}	 
		}  else{
		if(($m==$bm) && ($d==$bd)) { 
			$num_days[$count] = $day." [special] "."Employee Birthday "; 
		} else {
			$num_days[$count] = $day;
		  }
		}
		$count++;
	}
	//If the emplyoee applied leaves falls under pending leaves then remove those dates 
	//from num_days.
	$num_days=removePendingLeaves($num_days,$empid);
	return $num_days;
}


function SplLeavesCal($s,$e,$splLeavesCount,$empId){

	$days = RegleavesCal($s, $e, $empId);
	$num_workingDays = sizeof($days);
	$num_holidays = getNumberOfHolidays($s,$e);
	/*$query='SELECT managerid FROM emp WHERE (state="Active" and empid ="'.$empId.'") ;';
	$queryres=$db->pdoQuery($query)->results();
	foreach($queryres as $res)*/
	$query="SELECT emp_emailid from emp where state='Active' and empid IN (SELECT managerid FROM emp WHERE state='Active' and empid ="."'".$empId."')";
	$result = $db->pdoQuery($query);
	//$result=$db->pdoQuery('select emp_emailid from emp  where (state = ? and empid IN(?)) ;',array('Active',$res))->results();
	//$row = mysql_fetch_array($result);
	$rows=$db->pdoQuery($query)->results();
	foreach($rows as $row)
	$emailAddress = $row['emp_emailid'];

	//calucating total number of leaves after excluding week ends and number of holidays and special leaves
	$total_Num_Leaves = $num_workingDays - $num_holidays - $splLeavesCount;

//	mysql_close($con); //closing the connection
	$mailBody = "Start Date:  " . $s . "<br />\n".
				"End Date:  " . $e . "<br />\n".
				"Number of Working Days:  " . $num_workingDays . "<br />\n" .
	 			"Number of Holidays :  " . $num_holidays ." <br />\n" .
				"Number of days for Special Leave Applied :  " .$splLeavesCount ." <br />\n" .
				"Total Number of leaves: " . $total_Num_Leaves;
	echo $mailBody;

	if($total_Num_Leaves == 0){
		return 1;
	}else{

	}


	return $total_Num_Leaves;
}
function jqGrid_GetData($query,$request) {

	// we should set the appropriate header information. Do not forget this.
	header("Content-type: text/xml;charset=utf-8");

	// Get the requested page. By default grid sets this to 1.
	$page = $request['page'];

	// get how many rows we want to have into the grid - rowNum parameter in the grid
	$limit = $request['rows'];

	// Connect to the server and select the current database
	$dbConn = connectToDB();

	// execute the sql query
	$resquery=$dbConn->query($query);
	$results = $dbConn->pdoQuery($query);
$queryres='SELECT FOUND_ROWS()';
	// get the number of rows in the result set
	$resultTotal=$dbConn->pdoQuery($queryres);
	$result=$dbConn->pdoQuery($queryres)->results();
	foreach($result as $res)
	//$resultTotal =$dbConn->query("SELECT FOUND_ROWS()");
	//$res=$resultTotal->fetchAll(PDO::FETCH_ASSOC);
	//$numRows =  $res['FOUND_ROWS()'];
	$numRows =  $res['FOUND_ROWS()'];
	// calculate the total pages for the query
	if( $numRows > 0 ) {
		$total_pages = ceil($numRows/$limit);
	} else {
		$total_pages = 1;
	}

	// if for some reasons the requested page is greater than the total
	// set the requested page to total page
	if ($page > $total_pages) $page=$total_pages;

	// calculate the starting position of the rows
	$start = $limit*$page - $limit + 1;

	// if for some reasons start position is negative set it to 0
	// typical case is that the user type 0 for the requested page
	if($start <0) $start = 0;
	//$p=$sql -> count($sTable = 'empleavetransactions', $sWhere = 'empid = "'.$_SESSION['u_empid'].'" and approvalstatus = "Pending"' );
	
	//$numFields = mysql_num_fields($results);
	$numFields =$resquery->columnCount();
	//$numFields=$resultTotal->count($sTable = 'empleavetransactions', $sWhere = 'empid = "'.$_SESSION['u_empid'].'" and approvalstatus = "Pending"' );
	
	for ($index = 0; $index < $numFields; $index++) {
		//$header = mysql_field_name($results, $index);
		$header = $resquery->getColumnMeta($index);
		$columns[$index] = $header;
	}
	// start building the xml document
	$s = "<?xml version='1.0' encoding='utf-8'?>";
	$s .= "<rows>";
	$s .= "<page>".$page."</page>";
	$s .= "<total>".$total_pages."</total>";
	$s .= "<records>".$numRows."</records>";

	// be sure to put text data in CDATA
	$rowCount = $start-1;
	//mysql_data_seek($results,$rowCount);
	$mysqlres=$dbConn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$mysqlres->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_ABS, $rowCount);
		
	$rows=$dbConn->pdoQuery($query)->results();
	//while($row = $results->fetch(PDO::FETCH_ASSOC)) {
	foreach($rows as $row){
		$s .= "<row id='". $row[$columns[0]]."'>";
		for ($i=0;$i < $numFields;$i++){
			$s .= "<cell>".htmlspecialchars($row[$columns[$i]])."</cell>";
			//$s .= "<cell><![CDATA[". $row[note]."]]></cell>";
		}
		$s .= "</row>";

		$rowCount++;
		if ($rowCount >= ($start+$limit)){
			break;
		}
	}
	$s .= "</rows>";

	// Close the Database connection
	//$dbConn->closeConnection();

	return $s;
}
function displayDates($numofDays,$splLeaveDays,$splLeaveType,$fromDate,$toDate,$reason,$specialleaveid,$filename,$empid)
{
	global $db;
	$mailBody="";
	$splLeave="";
	$options="";
	//to check leave type
### Get regular leaves from table
	$LeavesTypes=array();
	$leavetypequery='select regularleaveid, regularleave from regularleaves';
	$leavetypesquery=$db->pdoQuery($leavetypequery);
	$leavetypes=$db->pdoQuery($leavetypequery)->results();
	$leavetypecount=$leavetypesquery -> count($sTable = 'regularleaves', $sWhere = '' );
	if($leavetypecount > 0){
	foreach($leavetypes as $leavetype){
	//$leavetype=$db->query("select regularleaveid, regularleave from regularleaves");
	//if($db->rowCount($leavetype) > 0){
		//while($row= $leavetype->fetch(PDO::FETCH_ASSOC)) {
			array_push($LeavesTypes,$leavetype['regularleave']);
		}
	}
	
	if ((getTotalLeaves($empid)) < -60) {
        //This array will hold all the Leave Types
        $LeavesTypes = array("WFH");
	}
	/*if ((getTotalLeaves($empid)) < -60) {
		//This array will hold all the Leave Types
		$LeavesTypes = array("WFH");
	} else {
		$LeavesTypes = array("FullDay", "HalfDay", "WFH","First Half-HalfDay & second Half-WFH","First Half-WFH & Second Half-HalfDay");
	}*/
	
	if($filename == "applyOnSite.php") {
		$LeavesTypes = array("OnSite");
		//$LeavesTypes = "OnSite";
	}
	$permittedLeaves=$splLeaveDays;
	$splLeave = getSpecialLeavesForType($splLeaveType, "specialleave");
	//Statring of the form.. This form will contain all the dates and dropdwon list with the leave types
	echo '<form name="leaves" id="displayDates" method="POST" action="'.$filename.'?getShift=1">
			<form id="AttInd" name="AttInd" method="post" action="attendance.php?AttInd=1">
				<div class="col-sm-12">
					<div class="panel panel-primary">
						<div class="panel-heading text-center">
							<strong style="font-size:20px;">Applied Leave Date</strong>
						</div>
						<div class="panel-body">
		  	  <table class="table table-striped table-bordered">';
	$count =0;

	$curYear = date('Y');
     $getOptionalLeaveCount="select * from empoptionalleavetaken where empid='".$empid."' and date between '$curYear-01-01' and '$curYear-12-31'";

     $getOptionalLeaveQuery=$db->pdoQuery($getOptionalLeaveCount);
	$getOptionalLeaveResults=$db->pdoQuery($getOptionalLeaveCount)->results();
	$optionalLeaveCount=$getOptionalLeaveQuery -> count($sTable = 'empoptionalleavetaken', $sWhere = '(date>="'.$curYear.'"-01-01 and date<="'.$curYear.'"-12-31) and empid ="'.$empid.'"');
	
	$optionalLeaveDates=array();
	foreach ($getOptionalLeaveResults as $getOptionalLeaveResult){
		array_push($optionalLeaveDates, $getOptionalLeaveResult['date']);
	}
	for($j=0;$j<sizeof($numofDays);$j++) 
	{
		if(!preg_match('/[special]/',$numofDays[$j],$match))
		{
		$options = "";
		//If special leave is selected by the user.. we will add the selected special leave type to the Optiona1 (Later will add it to html element)
		if($splLeaveDays == 0 && $splLeaveType ==" "){
			for ($i=1;$i<=sizeof($LeavesTypes);$i++) {
				if($filename == "applyOnSite.php") {
					$id=7;
				} else {
					$id=$i;
				}
				$thing = $LeavesTypes[$i-1];
				if ($thing == "WFH") {
					$id=3;
				}
				//$options.= "<OPTION VALUE=\"$id\">".$thing;
				$options.= "<OPTION VALUE=\"$thing\">".$thing;
			}
		}else{
			//Getting Special Leave name from the database
			$splLeave = getSpecialLeavesForType($splLeaveType, "specialleave");
			if($splLeaveDays!=0){
				//$options.= "<OPTION VALUE=".((sizeof($LeavesTypes))-1).">".$splLeave ;
				$options.= "<OPTION VALUE=\"$splLeave\">".$splLeave ;
				for ($i=1;$i<=sizeof($LeavesTypes);$i++){
					$id=$i;
					$thing = $LeavesTypes[$i-1];
					$options.= "<OPTION VALUE=\"$thing\">".$thing;
					//$options.= "<OPTION VALUE=\"$i\">".$thing;
				}
				$splLeaveDays--;

			}else{
				for ($i=1;$i<=sizeof($LeavesTypes);$i++){
					$id=$i;
					$thing = $LeavesTypes[$i-1];
					//else//{
					$options.= "<OPTION VALUE=\"$thing\">".$thing;
					//$options.= "<OPTION VALUE=\"$i\">".$thing;
					//}

				}
				//$options.= "<OPTION VALUE=".((sizeof($LeavesTypes))+1).">".$splLeave;
				$options.= "<OPTION VALUE=\"$splLeave\">".$splLeave;
			}
		}
		}
		
		//Adding all the dates to a string which is used to send the mail to a manager
		$mailBody = $mailBody . $numofDays[$j] . ":" ;
		echo "<tr>";
		if(!preg_match('/[special]/',$numofDays[$j],$match))
		{
			echo "<td> $numofDays[$j]  </td>";
			echo '<td>';
			echo '<SELECT class="form-control optionSelection" id="Day'.$count.'" NAME="Day'.$count.'">; '
			. $options .'</SELECT>';
			echo '</td> </tr>';
			$count++;
			
		}
		else {
			if (preg_match('/optional/i', $numofDays[$j],$match)) {
				if(preg_match('/(.*)(\[.*)/', $numofDays[$j],$splMatch)) {
					echo "<td>$splMatch[1]  </td>";
					if(!in_array($splMatch[1],$optionalLeaveDates)) {
						echo "<td>".str_replace('[special]','',$splMatch[2])."</td>";
								echo "<td>Apply optional leave
									<select class='form-control applyOptionalLeave' name='selectOptionalLeave'>
									<option>YES</option>
									<option>NO</option>
									</select>
									</td>";
						echo "<td style='display:none'>".str_replace('[special]','',$splMatch[2]).",".$count."</td>";
					} else {
						echo "<td>Optional leave (".str_replace('[special]','',$splMatch[2])." applied. Check Optional Leave table</td>";
						if(preg_match('/(.*)(\(Optional\))/', $splMatch[2],$actaulmatch)) {
							$numofDays[$j]=str_replace('[special]','',$actaulmatch[1]);
							
						}
						echo "<td></td>";
					}
				}
				} else {
				if(preg_match('/(.*)(\[.*)/', $numofDays[$j],$splMatch)) {
					echo "<td>$splMatch[1]  </td>";
					echo "<td>".str_replace('[special]','',$splMatch[2])."</td>";
				}
			}
			echo '</tr>';
			$count++;
		}
	}
	
	/*
	 * This Method will display the all the dates between selected start and end dates by removing weekends and holidays
	 * The User will get dropdown list for each and every date.. he has to select the type of leave He wants to take on that particular date
	 */
	//This hidden fields ae used for getting the values on submittion of the button
	
	
	echo "<input type = hidden id=noOfdays name = noOfdays value =  $count /> ";
	echo "<input type = hidden id=permittedleaves name = permittedleaves value =  $permittedLeaves />";
	echo "<input type = hidden id=spl name = spl value =".urlencode($splLeave)."/>";
	echo "<input type = hidden name = days value =  ".urlencode($mailBody)." />";
	echo "<input type = hidden name = fromDate value =  $fromDate /> ";
	echo "<input type = hidden name = toDate value =  $toDate /> ";
	echo "<input type = hidden name = reason value =  ".urlencode($reason)." /> ";
	echo "<input type = hidden  name = specialleaveid value =  $specialleaveid/> ";
	echo "<input type = hidden  id ='optionalleaveempid' name = optionalleaveempid value = '".$empid."'/> ";
	echo "<input type = hidden  id ='optionalleaveempcount' name = optionalleaveempcount value ='".$empid."_".$optionalLeaveCount."'/> ";
	echo "<input type = hidden name = splType value =  ".urlencode($splLeave)." /></table>";
	echo '	<br></br>
			<div class="form-group">
			<div class="row">
			<div class="col-sm-12 text-center">
				<input type="submit" class="btn btn-primary" name="submit" value="Next" />
			</div></div></div>
		</div></div></form>';
}

function getCalImg($arr,$minYear='-0',$maxYear='0')
{
	$str="";
	$str.="<script type='text/javascript'>";
	for($i=0;$i<sizeof($arr);$i++)
	{
		$str.="$(function() {
		$('#".$arr[$i]."').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			showButtonPanel: true,
			showOn: 'both',
			yearRange: '".$minYear.":+".$maxYear."',
			buttonImageOnly: true
			});
		});";
	}
	$str.="</script>";
	return $str;
}
function getselectbox($query,$field)
{
	global $db;
	$selectstr="";
	$selectstr.="<select class='form-control' name='$field' id='$field'>";
	//$result=$db->query($query);
	$results=$db->pdoQuery($query)->results();
	foreach($results as $result){
	//for($i=0;$i<$db->rowCount($result);$i++)
	//{
		//$row=$result->fetchAll(PDO::FETCH_ASSOC);
		$selectstr.="<option>".$result[$field]."</option>";
	}
	$selectstr.="</select>";
	return $selectstr;
}
function add_day($days,$format)
{
    $new_time = time() +  ($days * 24 * 60 * 60);
    $new_date=gmdate($format, $new_time);
    return $new_date;
}

function getManagerName($empid) {
        global $db;
        $query='select managername from emp where (empid = "'.$empid.'" and state = "Active") ;';
        //$result=$db->query("select managername from emp where empid='$empid' and state='Active'");
        $results= $db->pdoQuery($query)->results();
        foreach($results as $result)
       // $row=$result->fetchAll(PDO::FETCH_ASSOC);
        return $result['managername'];
}

function getempName($empid)
{
        global $db;
       // $result=$db->query("select empname from emp where empid='$empid' and state='Active'");
       $query='select empname from emp where (empid = "'.$empid.'" and state = "Active")';
        $results=$db->pdoQuery($query)->results();
        foreach($results as $result)
        //$row=$result->fetchAll(PDO::FETCH_ASSOC);
        return $result['empname'];
        
}

function getEmpIdByUserName($empusername)
{
        global $db;
        //$result=$db->query("select empid from emp where empusername='$empusername' and state='Active'");
        $query='select empid from emp where (empusername = "'.$empusername.'" and state = "Active") ;';
        $result=$db->pdoQuery($query)->results();
        //$row=$result->fetchAll(PDO::FETCH_ASSOC);
        return $result['empid'];
}

function getEmpIdByName($empname)
{
        global $db;
        //$result=$db->query("select empid from emp where empname='$empname' and state='Active'");
        $query='select empid from emp where (empname = "'.$empname.'" OR state = "Active") ;';
        $results=$db->pdoQuery($query)->results();
        foreach($results as $result)
        //$row=$result->fetchAll(PDO::FETCH_ASSOC);
        return $result['empid'];
}

function getDeptByEmpid($empid)
{
        global $db;
        //$result=$db->query("select dept from emp where empid='$empid' and state='Active'");
        $query='select dept from emp where (empid = "'.$empid.'" and state = "Active") ;';
        $results=$db->pdoQuery($query)->results();
        foreach($results as $result)
        //$row=$result->fetchAll(PDO::FETCH_ASSOC);
        return $result['dept'];
}

function getValueFromQuery($query, $element) {
	global $db;
	//$result = $db -> query($query);
	$results = $db->pdoQuery($query)->results();
	foreach($results as $result)
	//$res = $result->fetchAll(PDO::FETCH_ASSOC);
	return $result[$element];
}
?>

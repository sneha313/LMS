<?php session_start();
//require_once ("Library1.php");
require_once ("Library1copy.php");
$db=connectToDb();
$user_name=htmlspecialchars($_POST['user_name'],ENT_QUOTES);
$pass=$_POST['password'];
#if (auth_by_nis($user_name,$pass)){
if (auth_by_ldap($user_name,$pass)){
	$_SESSION['user_name']=$user_name; 
	//$sql = $db->query("SELECT `role`,`dept`,`group` FROM `emp` WHERE `empusername`='".$user_name."' and state='Active'"); 
	//$row = $db->fetchArray($sql);
	$query="SELECT `role`,`dept`,`group` FROM `emp` WHERE `empusername`='".$user_name."' and state='Active'";
	$sql = $db->pdoQuery($query);
	$rowcount=$sql -> count($sTable = 'emp', $sWhere = 'empusername = "'.$user_name.'" and state = "Active"' );
				
	$rows=$db->pdoQuery($query)->results();
	//$row = $sql->results($sql);
	foreach($rows as $row)
	if($rowcount > 0){
		$_SESSION['user_desgn']=$row['role'];
		$_SESSION['user_dept']=$row['dept'];
		$_SESSION['user_grp']=$row['group'];
	}
	    //$sql = $db->query("SELECT `group`,`empname`,`managerid`,`managername`,`managerlevel`,`empid`,`manager_emailid`,`location` FROM `emp` WHERE `empusername`='".$user_name."' and state='Active'"); 
        //$row = $db->fetchArray($sql);
        $query="SELECT `group`,`empname`,`managerid`,`managername`,`managerlevel`,`empid`,`manager_emailid`,`location` FROM `emp` WHERE `empusername`='".$user_name."' and state='Active'";
        $sql = $db->pdoQuery($query);
        $rowcount=$sql -> count($sTable = 'emp', $sWhere = 'empusername = "'.$user_name.'" and state = "Active"' );
        
        $rows = $db->pdoQuery($query)->results();
        foreach($rows as $row)
       if($rowcount>0)
     // if(mysql_num_rows($sql)>0)
        {
                $_SESSION['u_group']=$row['group'];
                $_SESSION['u_fullname']=$row['empname'];
                $_SESSION['u_managerid']=$row['managerid'];
                $_SESSION['u_managername']=$row['managername'];
				$_SESSION['u_managerlevel']=$row['managerlevel'];
                $_SESSION['u_empid']=$row['empid'];
                $_SESSION['u_manager_emailid']=$row['manager_emailid'];
				$_SESSION['u_emplocation']=$row['location'];
				if ($row['location'] == "BLR") {
					$_SESSION['u_holidayListTable']='holidaylist_blr';
				} else {
					$_SESSION['u_holidayListTable']='holidaylist_mum';
				}
        }
		//$sql = $db->query("SELECT empid FROM `emp` WHERE `empusername`='".$user_name."' and state='Active'");
		//$noRecord=$db->query("select * from emptotalleaves where empid='".$_SESSION['u_empid']."'");
		$query="SELECT empid FROM `emp` WHERE `empusername`='".$user_name."' and state='Active'";
		$sql = $db->pdoQuery($query);
		$rowcount=$sql -> count($sTable = 'emp', $sWhere = 'empusername = "'.$user_name.'" and state = "Active"' );
		 $norecordsquery="select * from emptotalleaves where empid='".$_SESSION['u_empid']."'";
		$noRecord=$db->pdoQuery($norecordsquery);
		$noecordrowcount=$noRecord -> count($sTable = 'emptotalleaves', $sWhere = 'empid = "'.$_SESSION['u_empid'].'"' );
		
		if($rowcount>0 && $noecordrowcount>0)
       // if($sql->rowCount()>0 && $noRecord->rowCount()>0)
        {	echo "yes"; }
        //elseif($db->countRows($noRecord)==0) {
		elseif($noecordrowcount==0) {
			echo "nodata";
		}
		else
		echo "no access";
} else
	echo "Login Failed"; //Invalid Login

?>

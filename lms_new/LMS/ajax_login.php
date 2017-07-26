<?php session_start();
require_once ("Library1.php");
$db=connectToDb();
$user_name=htmlspecialchars($_POST['user_name'],ENT_QUOTES);
$pass=$_POST['password'];
#if (auth_by_nis($user_name,$pass)){
if (auth_by_ldap($user_name,$pass)){
	$_SESSION['user_name']=$user_name; 
	$sql = $db->query("SELECT `role`,`dept`,`group` FROM `emp` WHERE `empusername`='".$user_name."' and state='Active'"); 
	$row = $db->fetchArray($sql);
	if($db->countRows($sql)>0){
		$_SESSION['user_desgn']=$row['role'];
		$_SESSION['user_dept']=$row['dept'];
		$_SESSION['user_grp']=$row['group'];
	}
	    $sql = $db->query("SELECT `group`,`empname`,`managerid`,`managername`,`managerlevel`,`empid`,`manager_emailid`,`location` FROM `emp` WHERE `empusername`='".$user_name."' and state='Active'"); 
        $row = $db->fetchArray($sql);
        if(mysql_num_rows($sql)>0)
        {
                $_SESSION['u_group']=$row['group'];
                $_SESSION['u_fullname']=$row['empname'];
                $_SESSION['u_managerid']=$row['managerid'];
                $_SESSION['u_managername']=$row['managername'];
				$_SESSION['u_managerlevel']=$row['managerlevel'];
                $_SESSION['u_empid']=$row['empid'];
                $_SESSION['u_manager_emailid']=$row['manager_emailid'];
		$_SESSION['u_emplocation']=$row['location'];
        }
		$sql = $db->query("SELECT empid FROM `emp` WHERE `empusername`='".$user_name."' and state='Active'");
		$noRecord=$db->query("select * from emptotalleaves where empid='".$_SESSION['u_empid']."'");
		
        if($db->countRows($sql)>0 && $db->countRows($noRecord)>0)
        {	echo "yes"; }
		elseif($db->countRows($noRecord)==0) {
			echo "nodata";
		}
		else
		echo "no access";
} else
	echo "Login Failed"; //Invalid Login

?>

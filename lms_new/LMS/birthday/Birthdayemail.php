<?php
	session_start();
	require_once 'Library.php';
	require_once 'Library1.php';
	$db=connectToDB();
	$query="select empname, emp_emailid from emp where birthdaydate=CURRDAY()";
	$result=mysql_query($query);
	$result=mysql_fetch_array($result);
	$empname=$result['empname'];
	$emp_emailid=$result['emp_emailid'];
	$firstname = strtok($empname, ' ');
	$lastname = strstr($empname, ' ');
	if($query){
		$cmd = '/usr/bin/php -f sendmail.php ' . $transactionid . ' ' . $empid . ' BirthdayEmail >> /dev/null &';
		exec($cmd);
		echo "birthday email sent successfully";
	} else {
		echo "some error";
	}
?>
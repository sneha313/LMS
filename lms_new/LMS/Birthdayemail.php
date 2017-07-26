<?php
	session_start();
	require_once 'Library.php';
	require_once 'Library1.php';
	$db=connectToDB();
	?>
<html>
<head>
<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<script type="text/javascript" src="public/js/bootstrap/js/bootstrap.min.js"></script>
  		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		
<style>
body{
background:pink;
}
</style>
</head>
<body>
<?php 
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
<img class=""><img src="img/bday1.jpg" class="img-responsive" alt="">
<footer class="footer1">
		<div class="container">
			<div class="row"><!-- row -->
			<div class="col-sm-4"></div>
			<div class="col-sm-4">
				<h3 class="text-center" style="color:blue;"><b>BEST WISHES</b></h3>
				<h3 class="tet-center" style="color:blue;"><b>TEAM ECI!!</b></h3>
			</div>
			<div class="col-sm-4"></div>
			</div>
		</div>
		</footer>
</body>

</html>
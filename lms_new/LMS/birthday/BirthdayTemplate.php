<html>
<head>
<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<script type="text/javascript" src="public/js/jquery-1.10.2.min.js"></script>
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
	session_start();
	require_once 'Library.php';
	$db=connectToDB();
	$query="select empname, emp_emailid from emp where birthdaydate='1979-08-04'";
	$result=$db->query($query);
	$tresult=$db->fetchAssoc($result);
	$empname=$tresult['empname'];
	$emp_emailid=$tresult['emp_emailid'];
	$firstname = strtok($empname, ' ');
	$lastname = strstr($empname, ' ');
	if (! $result){
		throw new My_Db_Exception('Database error: ' . mysql_error());
	}
	else{
	if($query){
		$cmd = '/usr/bin/php -f sendmail.php ' . $emp_emailid . ' BirthdayEmail >> /dev/null &';
		exec($cmd);
		echo "birthday email sent successfully";
	} else {
		echo "some error";
	}
	}
?>
<div class="container-fluid">
<div class="row">
<div class="col-sm-2"></div>
<div class="col-sm-8">
<?php

$imagesDir = 'Birthdayimage/';

$images = glob($imagesDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

$randomImage = $images[array_rand($images)]; // See comments
echo '<img src="'.$randomImage.'"  width="700" Style="margin:50px;"/>';

	?>
<!--  <img class="img-responsive" src="img/bday4.jpg" width="700px" Style="margin:50px;" >-->
</div>
<div class="col-sm-2"></div>
</div>
</div>
<footer class="footer1">
		<div class="container">
			<div class="row"><!-- row -->
			<div class="col-sm-4"></div>
			<div class="col-sm-4">
				<h3 class="text-center" style="color:blue;"><b>BEST WISHES</b></h3>
				<h3 class="text-center" style="color:blue;"><b>TEAM ECI!!</b></h3>
			</div>
			<div class="col-sm-4"></div>
			</div>
		</div>
		</footer>
		
</body>

</html>
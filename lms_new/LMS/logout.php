<?php session_start();
//  Developed by Roshan Bhattarai 
//  Visit http://roshanbh.com.np for this script and more.
//  This notice MUST stay intact for legal use

// if session is not set redirect the user
if(empty($_SESSION['user_name']))
	header("Location:userlogin.php");	

//if logout then destroy the session and redirect the user
if(isset($_GET['logout']))
{
	session_destroy();
	header("Location:login.php");
}	

?>
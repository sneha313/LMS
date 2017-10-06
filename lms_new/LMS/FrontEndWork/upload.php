<?php
if(!empty($_FILES['picture']['name'])){
    //Include database configuration file
   if (!isset($_SESSION)) {
	session_start();
}
if (empty($_SESSION['user_name']))
	header("Location:login.php");
require_once ("Library.php");
if(browser_detection("browser")=="msie") {
	echo '<!DOCTYPE html>';
}
require_once 'Library.php';
error_reporting("E_ALL");
$db=connectToDB();
    //File uplaod configuration
    $result = 0;
    $uploadDir = "profilepicture/";
    $fileName = basename($_FILES['picture']['name']);
    $targetPath = $uploadDir. $fileName;
    
    //Upload file to server
    if(@move_uploaded_file($_FILES['picture']['tmp_name'], $targetPath)){
        //Get current user ID from session
        $userId = $_SESSION['u_empid'];
        
        //Update picture name in the database
        $update = $db->query("UPDATE profilepicture SET Image = '".$fileName."' WHERE Empid = $userId");
        
        //Update status
        if($update){
            $result = 1;
        }
    }
    
    //Load JavaScript function to show the upload status
    echo '<script type="text/javascript">window.top.window.completeUpload(' . $result . ',\'' . $targetPath . '\');</script>  ';
}
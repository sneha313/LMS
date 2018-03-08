<?php
if(!empty($_FILES['picture']['name'])){
    //Include database configuration file
   if (!isset($_SESSION)) {
	session_start();
}
if (empty($_SESSION['user_name']))
	header("Location:login.php");
//require_once ("Library.php");
require_once ("librarycopy1.php");
if(browser_detection("browser")=="msie") {
	echo '<!DOCTYPE html>';
}
//require_once 'Library.php';
error_reporting("E_ALL");
$db=connectToDB();
    //File uplaod configuration
    $result = 0;
    $uploadDir = "profilepicture/";
    $fileName = basename($_FILES['picture']['name']);
    $targetPath = $uploadDir. $fileName;
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
    	$check = getimagesize($_FILES["picture"]["tmp_name"]);
    	if($check !== false) {
    		echo "File is an image - " . $check["mime"] . ".";
    		$uploadOk = 1;
    	} else {
    		echo "File is not an image.";
    		$uploadOk = 0;
    	}
    }
    // Check file size
    if ($_FILES["picture"]["size"] > 500000) {
    	echo "Sorry, your file is too large.";
    	$uploadOk = 0;
    }
    //Upload file to server
    if(@move_uploaded_file($_FILES['picture']['tmp_name'], $targetPath)){
        //Get current user ID from session
        $userId = $_SESSION['u_empid'];
        
        //Update picture name in the database
       // $update = $db->query("UPDATE profilepicture SET Image = '".$fileName."' WHERE Empid = $userId");

        $dataArray = array('Image'=>$fileName);
        // two where condition array
        $aWhere = array('Empid'=>$userId);
        // call update function
        $update = $db->update('profilepicture', $dataArray, $aWhere)->affectedRows();
        //Update status
        if($update){
            $result = 1;
        }
    }
    
    //Load JavaScript function to show the upload status
    echo '<script type="text/javascript">window.top.window.completeUpload(' . $result . ',\'' . $targetPath . '\');</script>  ';
}
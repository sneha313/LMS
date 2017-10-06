<?php
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
//Get current user ID from session
$userId = $_SESSION['u_empid'];

//Get user data from database
$result = $db->query("SELECT * FROM profilepicture WHERE Empid = $userId");
$row = $db->fetchAssoc($result);

//User profile picture
$userPicture = !empty($row['Image'])?$row['Image']:'no-image.png';
$userPictureURL = 'images/'.$userPicture;
?>
<html>
<head>

		<title>ECI Leave Management System...</title>
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="public/js/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel='stylesheet' type='text/css' href='public/js/DataTables/media/css/jquery.dataTables.min.css'>
		<link rel='stylesheet' type='text/css' media='screen' href='public/js/jqgrid/jqgridcss/ui.jqgrid.css' />
		<link rel='stylesheet' type='text/css' media='screen' href='public/js/bootstrap3-dialog/bootstrap-dialog.css' />
		<link rel='stylesheet' href='public/js/jqueryui/css/redmond/jquery-ui.css'>
		<link rel="stylesheet" type="text/css" media="screen" href="public/css/frontend.css" />
		
		<script type='text/javascript' src="public/js/jquery/jquery.js" type="text/javascript"></script>
		<script type='text/javascript' src="public/js/jquery/jquery-1.10.2.min.js"></script>
</head>
<body>
<div class="container">
    <div class="user-box">
        <div class="img-relative">
            <!-- Loading image -->
            <div class="overlay uploadProcess" style="display: none;">
                <div class="overlay-content"><img src="images/orbit-400px.gif"/></div>
            </div>
            <!-- Hidden upload form -->
            <form method="post" action="upload.php" enctype="multipart/form-data" id="picUploadForm" target="uploadTarget">
                <input type="file" name="picture" id="fileInput"  style="display:none"/>
            </form>
            <iframe id="uploadTarget" name="uploadTarget" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
            <!-- Image update link -->
            <a class="editLink" href="javascript:void(0);"><font color="red"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
            </font></a>
            <!-- Profile image -->
            <img src="<?php echo $userPictureURL; ?>" id="imagePreview">
        </div>
        <div class="name">
            <h3><?php echo $row['EmpName']; ?></h3>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function () {
    //If image edit link is clicked
    $(".editLink").on('click', function(e){
        e.preventDefault();
        $("#fileInput:hidden").trigger('click');
    });

    //On select file to upload
    $("#fileInput").on('change', function(){
        var image = $('#fileInput').val();
        var img_ex = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
        
        //validate file type
        if(!img_ex.exec(image)){
            alert('Please upload only .jpg/.jpeg/.png/.gif file.');
            $('#fileInput').val('');
            return false;
        }else{
            $('.uploadProcess').show();
            $('#uploadForm').hide();
            $( "#picUploadForm" ).submit();
        }
    });
});

//After completion of image upload process
function completeUpload(success, fileName) {
    if(success == 1){
        $('#imagePreview').attr("src", "");
        $('#imagePreview').attr("src", fileName);
        $('#fileInput').attr("value", fileName);
        $('.uploadProcess').hide();
    }else{
        $('.uploadProcess').hide();
        alert('There was an error during file upload!');
    }
    return true;
}
</script>
</body>
</html>
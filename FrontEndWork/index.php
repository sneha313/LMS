<?php
	if (!isset($_SESSION)) {
		session_start();
	}
	if (empty($_SESSION['user_name']))
		header("Location:login.php");
	require_once ("librarycopy1.php");
	if(browser_detection("browser")=="msie") {
		echo '<!DOCTYPE html>';
	}
	error_reporting("E_ALL");
	$db=connectToDB();
	//Get current user ID from session
	$userId = $_SESSION['u_empid'];
	$queryrow='select * from profilepicture where (Empid = "'.$userId.'") ;';
	$query = $db->pdoQuery($queryrow);
	$row=$db->pdoQuery($queryrow)->results();
	$userPicture = !empty($row[0]['Image'])?$row[0]['Image']:'no-image.png';
	$userPictureURL = 'profilepicture/'.$userPicture;
?>
<html>
	<head>
		<title>ECI Leave Management System...</title>
		<meta http-equiv="X-UA-Compatible" content="IE=9">
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="public/js/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel='stylesheet' type='text/css' href='public/js/DataTables/media/css/jquery.dataTables.min.css'>
		<link rel='stylesheet' type='text/css' media='screen' href='public/js/jqgrid/jqgridcss/ui.jqgrid.css' />
		<link rel='stylesheet' type='text/css' media='screen' href='public/js/bootstrap3-dialog/bootstrap-dialog.css' />
		<link rel='stylesheet' href='public/js/jqueryui/css/redmond/jquery-ui.css'>
		<link rel="stylesheet" href="public/js/Bootstrap-multiselect/Bootstrap-multiselect.css">
		<link rel="stylesheet" type="text/css" media="screen" href="public/css/frontend.css" />
	</head>
	<body>
		<?php
			$name = $_SESSION['u_fullname'];
			$firstname = strtok($name, ' ');
			$lastname = strstr($name, ' ');
		?>
		<!--navbar inverse start-->
		<nav class="navbar navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<div id="img">
						<img id="image" class="img-responsive" src="public/img/3.jpg">
					</div>
					<label id="session">Session Expires in :</label>
				</div>
				<h4 id="counter" class="countdown"></h4>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#" id="welcome"><b>  Welcome, <?php echo $firstname; ?></b></a></li>
					<li><a id="help" href="#"><i class="fa fa-question-circle" data-aria-hidden="true"></i><b> Need Help</b></a></li>
					<li><a id="login" href="logout.php?logout=1" accesskey="5" title="<?php echo $_SESSION['user_name']; ?> logged in"><i class="fa fa-sign-out" data-aria-hidden="true"></i><b> Logout</b></a></li>
				</ul>
			</div>
		</nav><!--navbar inverse close-->
		<!--navbar default start-->
		<nav class="navbar navbar-default navbar-static-top">
			<div class="container"><!--container div start-->
				<div class="navbar-header"><!--navbar header start-->
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" data-aria-expanded="false" data-aria-hidden="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button><!--button close-->
					<a class="navbar-brand" href="#">Leave Management System</a>
					</div><!--navbar header close-->
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right" style="padding-right:10px;">
						<li><a id="HomeButton" href="#">Home</a></li>
						<li><a id="holidays" href="#">Holiday List</a></li>
						<li><a id="attendance" href="#">Attendance</a></li>
						<li><a id="trackattendance" href="#">Track Leaves</a></li>
						<li><a id="calender" href="#">Leave Calender</a></li>
						<li><a id="voe" href="#">Apply VOE</a></li>
					</ul>
				</div>
			</div><!--container div close-->
		</nav><!--navbar default close-->
		
		<!--container fluid div start-->
		<div class="container-fluid well">
			<!--row start-->
			<div class="row">
				<!--2 column start-->
				<div class="col-sm-2">
					<div class="rectangle"><!--rectangle div for employee profile picture and location start-->
						<div class="img-relative">
					            <!-- Loading image -->
					            <div class="overlay uploadProcess" style="display: none;">
					                <!--  <div class="overlay-content"><img src="images/orbit-400px.gif"/></div>-->
					                <div class="overlay-content"></div>
					            </div>
					            <!-- Hidden upload form -->
					            <form method="post" action="upload.php" enctype="multipart/form-data" id="picUploadForm" target="uploadTarget">
					                <input type="file" name="picture" id="fileInput"  style="display:none"/>
					            </form>
					            <iframe id="uploadTarget" name="uploadTarget" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
					            <!-- Image update link -->
					            <a class="editLink" href="javascript:void(0);"><i class="fa fa-camera fa-2x" data-aria-hidden="true"></i></a>
					            <div class="middle">
								    <div class="text">Upload Image</div>
								  </div>
					            <!-- Profile image -->
					            <img src="<?php echo $userPictureURL; ?>" class="img-circle img-responsive" id="imagePreview">
					        </div>
						<h4><?php echo $_SESSION['u_fullname']; ?></h4>
						<span class="text-size-small">
						 <?php 
							echo $_SESSION['u_emplocation'].", India";
						?>
						</span>
					</div><!--rectangle div for employee profile picture and location close-->
					<hr>
					<ul class="list-group">
						<li class="list-group-item active"><p style="color:white; font-size:18px;">My Account</p></li>
						<li class="list-group-item"><a id="myprofile" href="#"><i class="fa fa-home" data-aria-hidden="true"></i>&nbsp;My Profile<i class="fa fa-angle-right" style="margin-left:62px;" data-aria-hidden="true"></i></a></li>
						<li class="list-group-item"><a id="editprofileid"  href="#"><i class="fa fa-user-secret" data-aria-hidden="true"></i>&nbsp;Emp Info<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:66px;"></i></a></li>
						<!-- <li class="list-group-item"><a id="officialinfo" href="#"><i class="fa fa-building" data-aria-hidden="true"></i>&nbsp;Official Info<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:52px;"></i></a></li> -->
						<!--  <li class="list-group-item"><a id="applyleaveid" href="#"><i class="fa fa-info-circle" data-aria-hidden="true"></i>&nbsp;Leave Info<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:38px;"></i></a></li>-->
						<li class="list-group-item"><a id="leaveinfo" href="#"><i class="fa fa-info-circle" data-aria-hidden="true"></i>&nbsp;Apply Leave<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:40px;"></i></a></li>
						<li class="list-group-item"><a id="allleavehis" href="#"><i class="fa fa-history" data-aria-hidden="true"></i>&nbsp;Leave History<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:30px;"></i></a></li>
						<?php
						if(strtoupper($_SESSION['user_dept'])=="HR") {?>
						<li class="list-group-item"><a id="hrsection" href="#"><i class="fa fa-user" data-aria-hidden="true"></i>&nbsp;HR Section<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:50px;"></i></a></li>
						<li class="list-group-item"><a id="teamLeavereport" href="#"><i class="fa fa-users" data-aria-hidden="true"></i>&nbsp;Team Report<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:35px;"></i></a></li>
						<li class="list-group-item"><a id="generateReportHR" href="#"><i class="fa fa-file-excel-o" data-aria-hidden="true"></i>&nbsp;Generate Report<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:17px;"></i></a></li>
						<?php 
						}elseif(strtoupper($_SESSION['user_desgn'])=="MANAGER" || strtoupper($_SESSION['user_desgn'])=="USER") {?>
						<li class="list-group-item"><a id="generateReportManager" href="#"><i class="fa fa-file-excel-o" data-aria-hidden="true"></i>&nbsp;Generate Report<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:17px;"></i></a></li>
						<?php if(strtoupper($_SESSION['user_desgn'])!="USER") {?>
						<li class="list-group-item"><a id="leaveapprovalid" href="#"><i class="fa fa-tasks" data-aria-hidden="true"></i>&nbsp;Leave Approval<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:20px;"></i></a></li>
						<li class="list-group-item"><a id="managersection" href="#"><i class="fa fa-user" data-aria-hidden="true"></i>&nbsp;Manager Section<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:10px;"></i></a></li>
						<li class="list-group-item"><a id="teamLeavereport" href="#"><i class="fa fa-users" data-aria-hidden="true"></i>&nbsp;Team Report<i class="fa fa-angle-right" data-aria-hidden="true" style="margin-left:35px;"></i></a></li>
						<?php }}?>
						
					</ul>
				</div>	<!--2 column end-->
				<!--10 column start-->
				<div class="col-sm-10 box">
					<div id="loadinout"></div>
					<div id="loadpendingstatus"></div>
					<div id="loadempapplyleave"></div>
					<div id="loadempleavestatus"></div>
					<div id="loadempleavehistory"></div>
					<div id="loadempeditprofile"></div>
					<div id="loadholidays"></div>
					<div id="loadteamleaveapproval"></div>
					<div id="loadempleavereport"></div>
					<div id="loadteamleavereport"></div>
					<div id="loadapplyteammemberleave"></div>
					<div id="loadhrsection"></div>
					<div id="loadmanagersection"></div>
					<div id="loadattendance"></div>
					<div id="loadtrackattendance"></div>
					<div id="loadcalender"></div>
					<div id="loadhelp"></div>
					<div id="loadoptionalleave"></div>
					<div id="loadvoeform"></div>
					<div id="loadcompoffleave"></div>
					<div id="loadwfhhr"></div>
					<div id="loadextrawfhhr"></div>
					<div id="loadmyprofile"></div>
					<div id="loadpersonalinfo"></div>
					<div id="loadofficialinfo"></div>
					<div id="loadleaveinfo"></div>
					<div id="loadDepartment"></div>
					<div id="loadbalanceleavesid"></div>
					<div id="loadallleavehis"></div>
					<div id="loadingmessage"></div>
					<div id="loadgenerateReport"></div>
					<div id="loadLeaveDeduction"></div>
				</div><!--10 column end-->
			</div><!--row end-->
		</div><!--container fluid div end-->
		
		<!--footer start-->
		<div class="footer-bottom">
			<!--footer container div start-->
			<div class="container">
				<div class="row">
					<!-- <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
						<div class="copyright">
							&copy;  <?php echo date("Y");?>, All rights reserved
						</div>
					</div> -->
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-center">
						<div class="design">
							<a href="#"><b>ECI TELECOM</b> </a> |  <a href="#">LMS by ECI</a>
						</div>
					</div>
				</div>
			</div><!--footer container div close-->
		</div><!--footer bottom section close-->
		<script type='text/javascript' src="public/js/jquery/jquery.js"></script>
		<script type='text/javascript' src="public/js/jquery/jquery-1.10.2.min.js"></script>
		<script type='text/javascript' src="public/js/countdown/countdown.js"></script>
		<script type='text/javascript' src='public/js/bootstrap/js/bootstrap.min.js'></script>
		<script type='text/javascript' src="public/js/Bootstrap-multiselect/Bootstrap-multiselect.js"></script>
		<script type="text/javascript" src="public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
		<script type='text/javascript' src='public/js/DataTables/media/js/jquery.dataTables.min.js'></script>
		<!--  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>-->
		<script type='text/javascript' src='public/js/jqueryui/js/jquery-ui.js'></script>
		<script type='text/javascript' src='public/js/jqgrid/grid.locale-en.js'></script>
		<script type='text/javascript' src='public/js/bootstrap3-dialog/bootstrap-dialog.js'></script>
		<script type='text/javascript' src='public/js/jqgrid/jquery.jqGrid.min.js'></script>
		<script type='text/javascript' src='public/js/jquery/jquery.validate.min.js'></script>
		<script type="text/javascript" src="projectjs/index.js"></script>
		<script type='text/javascript' src="projectjs/fullcalendar.js"></script>
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
			            BootstrapDialog.alert('Please upload only .jpg/.jpeg/.png/.gif file.');
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
			        BootstrapDialog.alert('There was an error during file upload!');
			    }
			    return true;
			}
			$(document).ready(function () {
				$('body').bind('mousedown keydown', function(event) {
					$('#counter').countdown('option', {
						until : +1200
					});
				});
				$('ul.nav li').click(function(){   
					 $(this).addClass('active');
					 $(this).siblings().removeClass('active');
				});
			});
			$('#counter').countdown({
				until : +1200,
				compact : true,
				description : '',
				onExpiry : liftOff,
				format : 'HMS'
			});

			function liftOff() {
				BootstrapDialog.confirm("Your session is expired. Do you want to extend the session?", function(result){
					if(result) {
						window.location = "index.php";
					} else {
						BootstrapDialog.alert("Your session is expired. Logging out");
						window.location = "login.php";
					}
				});
			}
		</script>
	</body>
</html>
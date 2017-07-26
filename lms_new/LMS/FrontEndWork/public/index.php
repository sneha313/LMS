<!DOCTYPE html>
<html>
<head>
	<title>NSI Automation</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=9">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="js/bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="js/DataTables/media/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="js/jqueryui/css/redmond/jquery-ui-1.10.0.custom.min.css">
	<link rel="stylesheet" type="text/css" href="js/bootstrap3-dialog/bootstrap-dialog.css">
	<style type="text/css" media="screen">
		#nsiResults_completed,#nsiResults_running {
 		   font-size: 12px;
		}
		.nav-header {
			font-size: 12px;
		}
		#loadingPage img {
				width: 80px;		
		} 
		.btnClass {
			margin-top: 10px;
			margin-right: 5px
		}
		
	</style>
</head>
<body>
<?php
require_once("../resources/Libraries/include/userDetails.php");

// Class for DB operations 
require_once ("../resources/Libraries/include/db.class.php");
include '../resources/Libraries/include/Library.php';

// Library to exectute shell commands on remote machine
include('../resources/Libraries/phpLib/Net/SSH2.php');

// Add the phpLib to path variable
set_include_path(get_include_path() . PATH_SEPARATOR . '../resources/Libraries/phpLib');

if (!isset($_SESSION)) {
	session_start();
	if(isset($_SESSION['empObj'])) {
		$employeeObj=$_SESSION['empObj'];
	} else {
		$employeeObj = new FGMembersite();
		if(!$employeeObj->Login())
		{
	
		}
	}
}
?>
	<div class="container-fluid">
		<div class="row">
		<nav class="navbar navbar-default navbar-static-top" role="navigation">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header col-lg-6">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
				</button>
				<a class="navbar-brand" href="http://ecinet.ecitele.com">ECI TELECOM</a>
			</div>
			<div class="navbar-header col-lg-6">
			<?php 
				if($employeeObj->CheckUserLogged()) {
					echo '<button type="button" class="btn btn-primary pull-right btnClass" id="logout_id">Logout</button>';
					echo '<button type="button" class="btn btn-primary btn-space pull-right btnClass ">Hi '.$_SESSION['userFullName'].'</button>';
				} else {
					echo '<button type="button" class="btn btn-primary pull-right btnClass" id="login_id">Login</button>';	
				}
			?>
				<div class=" modal fade" id="userId" role="dialog">
				 <div class="modal-dialog modaql-sm">
					<div class="modal-content">
						<div class="modal-body">
							<form id='userLogindetails' class="form-horrizantal" role="form" action="NsiAutomation/formSubmit.php?editLoginDetails=1" method="GET">
							<legend>Login</legend>
								<div class="form-group">
									<label class="control-label col-sm-2" for="username">UserName:</label>
									<div class="col-sm-10">
										<input type='text' name='username' class="form-control" id='username' placeholder="User Name" maxlength="50" required/> <br>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-2" for='password' >Password:   </label>
									<div class="col-sm-10">
										<input type='password' name='password' class="form-control" id='password' placeholder="password" maxlength="50" required /> <br>
									</div>
								</div>
								<div class="form-group">
								<div class="col-sm-5">
									<input type='hidden' class="form-control" name='submitted' id='submitted' value='1'/><br>
								</div>
								<div class="col-sm-5">
									<button type="submit"  class="form-control" class="btn btn-default btn-success btn-block">Login</button>
								</div>
								</div>
							</form>
				    	</div>				    			
						<div class="modal-footer">
							<button type="submit" class="btn btn-default btn-default pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
						</div>
					</div>
					</div>
				</div>
			</div>
		</div>

	<div class="row">
		<div class="col-sm-2">
    		<a href="#">
				<strong>
					<i class="glyphicon glyphicon-wrench"></i>
						Tools
				</strong>
			</a>
			<hr>
			<ul class="nav nav-stacked">
					<!--NSI AUTOMATION - START-->
	                <li class="nav-header"> <a data-target="#nsiAutomationMenu" data-toggle="collapse" href="#">NSI Automation <i class="glyphicon glyphicon-chevron-down"></i></a>
	                    <ul id="nsiAutomationMenu" class="nav nav-stacked collapse out">
	                    	<li id="nsiDetailsId"><a href="#">NSI Details</a></li>
	                    	<?php 
							if($employeeObj->CheckUserLogged()) {
	                    		echo "<li id='addNsiJobId'><a href='#'>ADD NSI Job</a></li>";
							}
							?>
	                    </ul>
	                </li>
					<!--NSI AUTOMATION - COMPLETE-->
					<!--Smoke Log Request - START-->
					<li class="nav-header"> <a data-target="#smokeLogRequstLogMenu" data-toggle="collapse" href="#">Smoke Request Log <i class="glyphicon glyphicon-chevron-down"></i></a>
	                    <ul id="smokeLogRequstLogMenu" class="nav nav-stacked collapse out">
	                    	<li id="smokerequestlogid"><a href="#">Get Details</a></li>
	                    </ul>
	                </li>
	                <!--Smoke Log Request - COMPLETE-->
	        </ul>
			<hr>
  		</div>
  		<div class="col-sm-10">
	    	<div id="mainPageContent">
				<div id="loadNSIStatus">
					<div id='loadingPage'>
	    				<center><img align="middle" src='images/loading.gif'/></center>
					</div>
				</div>
				<div id="loadAddNSIJob"></div>
				<div id="loadSmokeRequestLog"></div>
			</div>
		</div>
	</div>
	<script src="js/DataTables/media/js/jquery.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" src="js/index.js"></script>
	<script src="js/bootstrap/js/bootstrap.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/DataTables/media/js/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jqueryui/js/jquery-ui-1.10.0.custom.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/bootstrap3-dialog/bootstrap-dialog.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
		$('document').ready(function() {
			
			 //	Modal function for login
			 $("#login_id").click(function(){
	    		$("#userId").modal();
	  		  });	//	Complete of Modal function for login

	  		  // Ajax call to logout 
	  		  $("#logout_id").click(function() {
	  			 $.ajax({
			            data : "logout=1",
			            type : "POST",
			            url : "../resources/Web-Tools/NsiAutomation/formSubmit.php",
			            success : function(response) {
				            alert("Logged out successfully");
				            $("#loadNSIStatus").load('../resources/Web-Tools/NsiAutomation/nsiDetails.php?nsiDetails=1');
				            $("#login_id").removeClass("hidden");
		             		    $("#logout_id").addClass("hidden");
		             		    $("#user").remove();
		             		    window.location.href = "index.php";
			            }
			     });
	  		  });	// Complete of Ajax call to logout 

	  		 //	Ajax call to authenticate user
			 $('#userLogindetails').submit(function() {
			        $.ajax({
			            data : $(this).serialize(),
			            type : $(this).attr('method'),
			            url : $(this).attr('action'),
			            success : function(response) {
				           if(response=="success") {
					           	alert("Logged in Successfully");
					           	$('#userId').modal('toggle');
			             		$("#loadAddNSIJob").html("");
			             		$("#loadAddNSIJob").hide();
			             		$("#loadNSIStatus").load('../resources/Web-Tools/NsiAutomation/nsiDetails.php?nsiDetails=1');
			             		$("#loadNSIStatus").show();
			             		$("#login_id").addClass("hidden");
			             		$("#logout_id").removeClass("hidden");
			             		window.location.href = "index.php";
				           } else {
				        	   $('#userId').modal('toggle');
				        	   alert("Log in error");
				           }
			            }
			    	});
			        return false;
			   });	// Complete of Ajax call to authenticate user
		});	//	Complete of ready function
	</script>
</div>
</body>
</html>

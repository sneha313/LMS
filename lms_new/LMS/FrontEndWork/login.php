<html>
	<head>
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<style>
			#heading{
				margin-top:20px;
			}

			.btn-primary{
				width:82%;
			}
			
			#right-part{
				padding-right:10px;
				margin-top:70px;
			}
			#left-part{
				margin-top:70px;
				height:280px;
				border-right: 3px solid #337ab7;
				margin-right:95px;
			}
			body{
				background:
			}
			.inner-addon { 
				position: relative; 
			}

			/* style icon */
			.inner-addon .glyphicon {
				position: absolute;
				padding: 7px;
				pointer-events: none;
				font-size:19px;
			}

			/* align icon */
			.left-addon .glyphicon  {
				 left:  0px;
				 color:	#337ab7;
			 }

			/* add padding  */
			.left-addon input  {
				padding-left:  30px; 
			 }
			 .form-control{
				border-radius:20px;
			 }
			 h3 b{
				 color:#337ab7;
			 }
			 .navbar-inverse{
				background-color:#031432;
			}
			 #img{
				float:left;
				height:80px;
			}
			.messageboxerror{
				position:absolute;
				width:auto;
				margin-left:30px;
				border:1px solid #CC0000;
				background:#F7CBCA;
				padding:3px;
				font-weight:bold;
				color:#CC0000;
			}
			.messagebox{
				position:absolute;
				width:100px;
				margin-left:30px;
				border:1px solid #c93;
				background:#ffc;
				padding:3px;
			}
			.messageboxok{
				position:absolute;
				width:auto;
				margin-left:30px;
				border:1px solid #349534;
				background:#C9FFCA;
				padding:3px;
				font-weight:bold;
				color:#008000;
				
			}
		</style>
	</head>
	<body>
		<!--navbar start-->
		<nav class="navbar navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<div id="img">
						<img class="img-responsive" src="img/3.jpg" style="height:70px;">
					</div>
					
				</div>
				
			</div>
		</nav><!--navbar end-->	
		<!--container div start-->
		<div class="container">
			<!--row div start-->
			<div class="row">
			<!--12 column start-->
				<div class="col-sm-12">
					<div class="row"><!-- row start-->
						<div class="col-sm-5" id="left-part">
							<div id="heading">
								<!--LMS login details-->
								<h3><b>Leave Management System</b></h3> 
								<ul>
								<li>Use <b>windows credential</b> to Login to LMS.</li>
								<li><b>ECI Domain </b>is not needed.</li>
								<li>LMS link will <b>work </b>only when you are in the ECI Domin.</li>
								</ul>
							</div><!--LMS login details closed-->
						</div><!--1st row end-->
						<div class="col-sm-6" id="right-part"><!--right part div section start-->
							<h3><b>Login To LMS</b></h3>
							<br>
							<!--LMS login form-->
							<form method="post" action="login.php" id="login_form">
								<div class="row">
									<div class="col-sm-8">
										<div class="form-group">
											<div class="row">
												<div class="col-sm-10">
													<div class="inner-addon left-addon">
														<i class="glyphicon glyphicon-user"></i>
														 <input type="text" class="form-control" name="name" id="username" placeholder="Enter User Name" size="40"/>
													</div>
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<div class="row">
												<div class="col-sm-10">
													<div class="inner-addon left-addon">
														<i class="glyphicon glyphicon-glyphicon glyphicon-lock"></i>
														 <input type="password" class="form-control" name="name" id="password" placeholder="Enter Password" size="40"/>
													</div>
												</div>
											</div>
										</div>
										
										<div class="row">
											<div class="col-sm-12">
												<input type="submit" class="btn btn-primary" id="submit" value="Login">
												<span id="msgbox" style="display:none"></span>
											</div>
										</div>
										<br>
										<div class="row">
											<div class="col-sm-3"></div>
											<div class="col-sm-6">
												<a href="needhelp.php"><i class="fa fa-question-circle" aria-hidden="true"></i><b> Need Help</b></a>
											</div>
											<div class="col-sm-3"></div>
										</div>
									</div>
								</div>
							</form><!--LMS login form close-->
						</div><!--right part div section close-->
					</div><!-- row div end-->
				</div><!--12 column div end-->
			</div><!--row div end-->
		</div><!--container div end-->
		
		<script src="public/js/jquery/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script>
			$(document).ready(function()
			{
				$("#login_form").submit(function()
				{
					//remove all the class add the messagebox classes and start fading
					$("#msgbox").removeClass().addClass('messagebox').text('Validating....').fadeIn(1000);
					//check the username exists or not from ajax
					$.post("ajax_login.php",{ user_name:$('#username').val(),password:$('#password').val(),rand:Math.random() } ,function(data)
					{
					   if(data=='yes') //if correct login detail
						{
							$("#msgbox").fadeTo(200,0.1,function()  //start fading the messagebox
							{ 
							  //add message and change the class of the box and start fading
							  $(this).html('Logging in.....').addClass('messageboxok').fadeTo(900,1,
							  function()
							  { 
								 document.location='index.php';
							  });
							});
						}
						else if(data=='nodata') 
						{
							$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
							{ 
							  //add message and change the class of the box and start fading
							  $(this).html('Leave details is not available. Please contact HR').fadeTo(900,1);
							});		
						}
						else 
						{
							$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
							{ 
							  //add message and change the class of the box and start fading
							  $(this).html('Your login detail wrong...'+data).addClass('messageboxerror').fadeTo(900,1);
							});		
						}
						
					});
					return false; //not to post the  form physically
				});
			});
		</script>
	</body>
</html>
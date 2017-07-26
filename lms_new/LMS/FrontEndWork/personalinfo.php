<html>
	<head>
		<link rel="stylesheet" href="public/js/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<style>
			.footer1 {
				background: #031432 url("../images/footer/footer-bg.png") repeat scroll left top;
				padding-top: 40px;
				padding-right: 0;
				padding-bottom: 20px;
				padding-left: 0;/*	border-top-width: 4px;
				border-top-style: solid;
				border-top-color: #003;*/
				margin-top:-20px;
				color:white;
			}

			.title-widget {
				color: #898989;
				font-size: 20px;
				font-weight: 300;
				line-height: 1;
				position: relative;
				text-transform: uppercase;
				font-family: 'Fjalla One', sans-serif;
				margin-top: 0;
				margin-right: 0;
				margin-bottom: 25px;
				margin-left: 0;
				padding-left: 28px;
			}

			.title-widget::before {
				background-color: #ea5644;
				content: "";
				height: 22px;
				left: 0px;
				position: absolute;
				top: -2px;
				width: 5px;
			}



			.widget_nav_menu ul {
				list-style: outside none none;
				padding-left: 0;
			}

			.widget_archive ul li {
				background-color: rgba(0, 0, 0, 0.3);
				content: "";
				height: 3px;
				left: 0;
				position: absolute;
				top: 7px;
				width: 3px;
			}


			.widget_nav_menu ul li {
				font-size: 13px;
				font-weight: 700;
				line-height: 20px;
				position: relative;
				text-transform: uppercase;
				border-bottom: 1px solid rgba(0, 0, 0, 0.05);
				margin-bottom: 7px;
				padding-bottom: 7px;
				width:95%;
			}



			.title-median {
				color: #636363;
				font-size: 20px;
				line-height: 20px;
				margin: 0 0 15px;
				text-transform: uppercase;
				font-family: 'Fjalla One', sans-serif;
			}

			.footerp p {font-family: 'Gudea', sans-serif; }


			#social:hover {
    			-webkit-transform:scale(1.1); 
				-moz-transform:scale(1.1); 
				-o-transform:scale(1.1); 
			}
			#social {
				-webkit-transform:scale(0.8);
                /* Browser Variations: */
				-moz-transform:scale(0.8);
				-o-transform:scale(0.8); 
				-webkit-transition-duration: 0.5s; 
				-moz-transition-duration: 0.5s;
				-o-transition-duration: 0.5s;
			}           
			/* Only Needed in Multi-Coloured Variation  */
			.social-fb:hover {
				color: #3B5998;
			}
			.social-tw:hover {
				color: #4099FF;
			}
			.social-gp:hover {
				color: #d34836;
			}
			.social-em:hover {
				color: #f39c12;
			}
			.nomargin { margin:0px; padding:0px;}

			.footer-bottom {
				background-color: #15224f;
				min-height: 30px;
				width: 100%;
				margin-bottom:3px;
			}
			.copyright {
				color: #fff;
				line-height: 30px;
				min-height: 30px;
				padding: 7px 0;
			}
			.design {
				color: #fff;
				line-height: 30px;
				min-height: 30px;
				padding: 7px 0;
				text-align: right;
			}
			.design a {
				color: #fff;
			}
			#img{
				float:left;
				height:30px;
			}
			#text{
				margin-left:7px;
				float:left;
				color:grey;
			}
			.navbar-inverse{
				background-color:#031432;
			}
			.rectangle{
				width:180px;
				height:180px;
				background:#4682B4;
				border-radius:2px;
				padding:10px;
			}
		
			.navbar-default{
				background:white;
				margin-top:-20px;
				margin-bottom:20px;
				padding-left:25px;
			}
		</style>
		
	</head>
	<body>
		<nav class="navbar navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<div id="img">
						<img class="img-responsive" src="img/3.jpg" style="height:50px;">
					</div>
					
				</div>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#" style="font-size:16px; color:white; padding-top:20px; padding-right:30px; font-family:cursive;"><b> Morning, Victoria !</b></a></li>
					<li><a href="help.php" style="font-size:16px; color:white; padding-top:20px;"><i class="fa fa-question-circle" aria-hidden="true"></i><b> Need Help</b></a></li>
					<li><a href="login.php" style="font-size:16px; color:white; padding-top:20px;"><i class="fa fa-sign-out" aria-hidden="true"></i><b> Logout</b></a></li>
				</ul>
			</div>
		</nav>
		<nav class="navbar navbar-default navbar-static-top">
		<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button><!--button close-->
			<a class="navbar-brand" href="#">Leave Management System</a>
		</div><!--navbar header-->
		<div id="navbar" class="navbar-collapse collapse">
		<ul class="nav navbar-nav navbar-right" style="padding-right:80px;">
		<li class="active" id="home"><a href="#home">Holiday List</a></li>
		<li><a href="attendance.php">Attendance</a></li>
		<li><a href="trackattendance.php">Track Leaves</a></li>
		<li><a href="#Login">Leave Calender</a></li>
		<li><a href="#PostList">Apply VOE</a></li>
		</ul>
		
		</div>
		</div><!--container div close-->
		</nav><!--nav close-->
		
		<div class="container-fluid well" style="margin-top:-20px;">
		<!--row start-->
		<div class="row">
		<!--2 column start-->
			<div class="col-sm-2">
				<div class="rectangle">
					<a href="#"><img src="img/4.jpg" class="img-circle img-responsive" alt="" width="150px;" height="80px;"></a>
					<center><h6 style="color:blue;">Victoria Baker</h6>
					<span class="text-size-small" style="color:white;">Santa Ana, CA</span>
					</center>
				</div>
							
			
				<hr>
				<ul class="list-group">
					<li class="list-group-item active" style="color:white; font-size:18px;">My Account</li>
					<li class="list-group-item"><a href="lms.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;My Profile<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:50px;"></i></a></li>
					<li class="list-group-item"><a href="personalinfo.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Personal Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:30px;"></i></a></li>
					<li class="list-group-item"><a href="officialinfo.php"><i class="fa fa-building" aria-hidden="true"></i>&nbsp;Official Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<li class="list-group-item"><a href="leaveinfo.php"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;My Leave Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:20px;"></i></a></li>
				</ul>
			</div><!--2 column end-->
			<div class="col-sm-9" style="margin-left:40px;">
					<div class="panel panel-primary">
								
						<div class="panel-heading text-center">
				
							<strong style="font-size:20px;">Personal Info</strong>
					
						</div>
						<div class="panel-body">
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Emp Name:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="" >
								</div>
								<div class="col-sm-2">
									<label>Emp Id:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="">
								</div>
							</div><!--1st row close-->
							</div>
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Manager Name:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="">
								</div>
								<div class="col-sm-2">
									<label>Company Name:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="">
								</div>
							</div><!--2nd row close-->
							</div>
							
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Department:</label>
								</div>
								<div class="col-sm-4">
									<select class="form-control">
										<option>--Department--</option>
										<option></option>
										<option></option>
										<option></option>
									</select>
								</div>
								<div class="col-sm-2">
									<label>Blood Group:</label>
								</div>
								<div class="col-sm-4">
									<select class="form-control">
										<option>--Blood grp--</option>
										<option></option>
										<option></option>
										<option></option>
									</select>
								</div>
							</div><!--3rd row close-->
							</div>
							
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Phone Number:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="">
								</div>
								<div class="col-sm-2">
									<label>Email Id:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="">
								</div>
							</div><!--4th row close-->
							</div>
							
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
								<label>Years of Exp:</label>
								</div>
								<div class="col-sm-4"><input type="text" class="form-control" value=""></div>
								<div class="col-sm-2"><label>State:</label></div>
								<div class="col-sm-4"><input type="text" class="form-control" value=""></div>
							</div><!--5th row close-->
							</div>
							
							<div class="form-group">
							<div class="row">
								<div class="col-sm-2">
									<label>Date of Birth:</label>
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" value="">
								</div>
								<div class="col-sm-2">
									<label>Address:</label>
								</div>
								<div class="col-sm-4">
									<textarea class="form-control" value=""></textarea>
								</div>
							</div><!--6th row close-->
							</div>
						</div>
					</div>
				</div>	
					
				<div class="col-sm-1"></div>
			</div>
		</div>
		</div>
		<footer class="footer1">
		<div class="container">
			<div class="row"><!-- row -->
					
						<div class="col-lg-3 col-md-3"><!-- widgets column left -->
						<ul class="list-unstyled clear-margins"><!-- widgets -->
								
									<li class="widget-container widget_nav_menu"><!-- widgets list -->
										<h1 class="title-widget">Email Us</h1>
											
										<p><b>Email id:</b></p>
										<p><b>Anil Kumar Thatavarthi:</b> <a href="mailto:#"></a></p>
										<p><b>Naidile Basvagde :</b> <a href="mailto:#"></a></p>
										<p><b>Sneha Kumari:</b> <a href="mailto:#"></a></p>
										<!--
										<ul>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> About Us</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> Contact Us</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> Success Stories</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> 10th syllabus</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> 12th syllabus</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> Achiever's Batch</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Regular Batch</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Test & Discussion</a></li>
										</ul>
							-->
									</li>
									
								</ul>
								 
							  
						</div><!-- widgets column left end -->
						
						
						
						<div class="col-lg-3 col-md-3"><!-- widgets column left -->
					
						<ul class="list-unstyled clear-margins"><!-- widgets -->
								
									<li class="widget-container widget_nav_menu"><!-- widgets list -->
							
										<h1 class="title-widget">Contact Us</h1>
										<p><b>Helpline Numbers </b>

										<b style="color:#ffc106;">(8AM to 10PM):</b>  +91-9740464882, +91-9945732712  </p>
										
										
										<p><b>Phone Numbers : </b>7042827160, </p>
										<p> 011-2734562, 9745049768</p>
										<!--
										<ul>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Test Series Schedule</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  School Fee</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Postal Coaching</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Books according to class</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Education Details</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Study Centres</a></li>
											<li><a  href="#"><i class="fa fa-angle-double-right"></i> Extra Curriculum Activities</a></li>
											<li><a  href="#" target="_blank"><i class="fa fa-angle-double-right"></i> Results</a></li>
											
										</ul>-->
							
									</li>
									
								</ul>
								 
							  
						</div><!-- widgets column left end -->
						
						
						
						<div class="col-lg-3 col-md-3"><!-- widgets column left -->
					
						<ul class="list-unstyled clear-margins"><!-- widgets -->
								
									<li class="widget-container widget_nav_menu"><!-- widgets list -->
							
										<h1 class="title-widget">Office Address</h1>
										<p><b>Corp Office / Postal Address</b></p>
									   <!-- <ul>


											<li><a href="#"><i class="fa fa-angle-double-right"></i> Enquiry Form</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i> Online Test Series</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i> Subject Wise Test Series</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i> Smart Book</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i> Test Centres</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i>  Admission Form</a></li>
											<li><a href="#"><i class="fa fa-angle-double-right"></i>  Computer Live Test</a></li>

										</ul>-->
							
									</li>
									
								</ul>
								 
							  
						</div><!-- widgets column left end -->
						
						
						<div class="col-lg-3 col-md-3"><!-- widgets column center -->
						
						   
							
								<ul class="list-unstyled clear-margins"><!-- widgets -->
								
									<li class="widget-container widget_recent_news"><!-- widgets list -->
							
										<h1 class="title-widget">Follow Us On </h1>
										
										<div class="footerp"> 
										
										
										</div>
										
										<div class="social-icons">
										
											<ul class="nomargin">
											
						<a href="#"><i class="fa fa-facebook-square fa-3x social-fb" id="social"></i></a>
						<a href="#"><i class="fa fa-twitter-square fa-3x social-tw" id="social"></i></a>
						<a href="#"><i class="fa fa-google-plus-square fa-3x social-gp" id="social"></i></a>
						<a href="#"><i class="fa fa-envelope-square fa-3x social-em" id="social"></i></a>
											
											</ul>
										</div>
									</li>
								  </ul>
							   </div>
			</div>
		</div>
		</footer>
		<!--header-->

		<div class="footer-bottom">

			<div class="container">

				<div class="row">

					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

						<div class="copyright">

							© 2017, All rights reserved

						</div>

					</div>

					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

						<div class="design">

							 <a href="#"><b>ECI TELECOM</b> </a> |  <a href="#">LMS by ECI</a>

						</div>

					</div>

				</div>

			</div>

		</div>
			
			
			<!--footer end-->
	</body>
</html>
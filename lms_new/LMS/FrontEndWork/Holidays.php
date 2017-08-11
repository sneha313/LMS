<?php
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
$db=connectToDB();

if($_SESSION['user_dept'] == "HR") {
	$edit_options="true";
} else {
	$edit_options="false";
}

if (array_key_exists('oper', $_REQUEST)) {
	if (array_key_exists('id', $_REQUEST)) {
		$id = $_REQUEST['id'];
	}
	if (array_key_exists('Date', $_REQUEST)) {
		$date = $_REQUEST['Date'];
	}
	if (array_key_exists('Occasion', $_REQUEST)) {
		$occasion = $_REQUEST['Occasion'];
	}
	$dbConn = connectToDB();
	$sql = $dbConn->query("select * from holidaylist");
	switch($_REQUEST['oper']){
		case 'edit':
			$sql = $dbConn->query("UPDATE holidaylist SET date='".$date."', holidayname='".$occasion."' WHERE id='".$id."'");
			break;
		case 'del':
			$sql = $dbConn->query("DELETE FROM holidaylist WHERE id='".$id."'");
			break;
		case 'add':
			$sql = $dbConn->query("INSERT INTO holidaylist (date,holidayname) VALUES ('".$date."','".$occasion."')");
			break;
		default:
			break;
	}
	$dbConn->closeConnection();
} elseif (array_key_exists('getData', $_REQUEST)) {
	$sidx = $_REQUEST['sidx'];
	$sord = $_REQUEST['sord'];
	if(!$sidx) $sidx =1;
	$query="";
	$query.= "SELECT id,date,day,holidayname,leavetype FROM holidaylist where `date` like '%".$_REQUEST['year']."%'  ";
	if (array_key_exists('searchField', $_REQUEST)) {
		if($_REQUEST['searchField']=='Date') {
			$query.="where date='".$_REQUEST['searchString']."'";
		}
		if($_REQUEST['searchField']=='Occasion') {
			$query.="where holidayname='".$_REQUEST['searchString']."'";
		}
	}
	$query.="ORDER BY ".$sidx." ".$sord."";
	echo jqGrid_GetData($query,$_REQUEST);
	
} else {
	$query="select date from holidaylist";
	$result=$db->query($query);
	$dates=array();
	while($row=$db->fetchAssoc($result)) 
	{
		$year=explode("-",$row['date']);
		array_push($dates,$year[0]);
	}
	$years=array_unique($dates);
	echo("<!DOCTYPE html PUBLIC \" -//w3c//DTD XHTML 1.0 Strict//EN\" 
 		\"http://www.w3c.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
  		<html>
			<head>
				<title>Holiday List</title>
				<link rel='stylesheet' href='public/js/bootstrap/css/bootstrap.css'>
				<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
				<link rel='stylesheet' type='text/css' href='public/js/DataTables/media/css/jquery.dataTables.min.css'>
				<link rel='stylesheet' type='text/css' media='screen' href='js/jqgrid/jqgridcss/ui.jqgrid.css' />	
				<link href='js/jqueryui/css/redmond/jquery-ui.css' rel='stylesheet'>
				<link href='js/jqueryui/css/redmond/theme.css' rel='stylesheet'>
				<script src='public/js/jquery/jquery-1.7.2.min.js' type='text/javascript'></script>
				<script src='js/jqueryui/js/jquery-ui.js'></script>
				<script src='js/jqgrid/grid.locale-en.js' type='text/javascript'></script>
				<script type='text/javascript' src='public/js/jquery/jquery.validate.min.js'></script>
				<script src='public/js/jqgrid/jquery.jqGrid.min.js' type='text/javascript'></script>
				<script src='public/js/jquery/jquery.searchFilter.js' type='text/javascript'></script>
				<script type='text/javascript' src='public/js/DataTables/media/js/jquery.dataTables.js'></script>
				<script src='https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js' type='text/javascript'></script>	
				<script type='text/javascript' src='https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js'></script>
				<script type='text/javascript' src='https://cdn.datatables.net/buttons/1.3.1/js/buttons.bootstrap.min.js'></script>
				<script type='text/javascript' src='https://cdn.datatables.net/select/1.2.2/js/dataTables.select.min.js'> </script>
				<script type='text/javascript' src='projectjs/index.js'></script>
				<style>
					.footer1 {
						background: #031432 repeat scroll left top;
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
						content: '';
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
						content: '';
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
						padding:15px;
					}
					#teamMemberLeave{
						background:white;
						padding:18px;
						margin:5px;
					}
					#upcomingHoliday{
						background:white;
						padding:18px;
						margin:5px;
					}
					#teamMemberBirthday{
						background:white;
						padding:18px;
						margin:5px;
					}
					.navbar-default{
						background:white;
						margin-top:-20px;
						margin-bottom:20px;
						padding-left:25px;
					}
				</style>
			</head>
			<body>");
				echo "<script type='text/javascript'>
						jQuery(document).ready(function(){
							 $(function() {
									$( '#tabs').tabs();
							});";
				foreach ($years as $val) {
							echo("	jQuery('#list".$val."').jqGrid({
									url:'Holidays.php?getData=1&year=".$val."',
									datatype: 'xml',
									mtype: 'GET',
									colNames:['Id','Date','Day','Occasion','Leave Type'],
									colModel :[
										{name:'Id', index:'Id', width:10, editable:false,hidden:true},
										{name:'Date',index:'Date',width:100, sorttype:'date' , editable:true},
										{name:'Day',index:'Day',width:100, editable:true},
										{name:'Occasion', index:'Occasion',width:250, editable:true,editoptions:{size:10}, align:'left'},
										{name:'LeaveType',index:'LeaveType',width:100, editable:true}
									],
									pager: '#pagernav".$val."',
									pginput: false,
									pgbuttons: false,
									rowNum:100,
									rowList:[10,20,30,50,100],
									sortname: 'Date',
									sortorder: 'asc',
									viewrecords: true,
									caption: 'Yearly Holiday List',
									height: 'auto',
									width: 730,
									toppager: true,
									editurl: 'Holidays.php'
							}).navGrid('#pagernav".$val."', {edit:$edit_options,del:$edit_options,add:$edit_options},
							{search:false,cloneToTop:true}, //options
							{   height:280,
									reloadAfterSubmit:true,
							
									checkOnSubmit: true,
									afterSubmit: function(response, postdata){
									success=true;
									message='';
									new_id=0;
									if(response.responseText.indexOf('ERROR')>=0){
									success=false;
									message=response.responseText;
							}
									return [success,message,new_id]
							},
									closeAfterEdit:true
							}, // edit options
							{height:280,reloadAfterSubmit:true,
									afterSubmit: function(response, postdata){
									success=true;
									message='';
									new_id=0;
									if(response.responseText.indexOf('ERROR')>=0){
									success=false;
									message=response.responseText;
							}
									return [success,message,new_id]
							}
							}, // add options
							{reloadAfterSubmit:true,
									afterSubmit: function(response, postdata){
									success=true;
									message='';
									new_id=0;
									if(response.responseText.indexOf('ERROR')>=0){
									success=false;
									message=response.responseText;
							}
									return [success,message,new_id]
							}
							}, // del options
							{} // search options
							);
									
							");
				}
				
				echo "});
					</script>";
			?>
				<?php
				$name = $_SESSION['u_fullname'];
				$firstname = strtok($name, ' ');
				$lastname = strstr($name, ' ');
				?>
				
				<nav class='navbar navbar-inverse'>
						<div class='container'>
							<div class='navbar-header'>
								<div id='img'>
									<img class='img-responsive' src='img/3.jpg' style='height:50px;'>
								</div>
								
							</div>
							<ul class='nav navbar-nav navbar-right'>
							<li><a href='#' style='font-size:16px; color:white; padding-top:20px; padding-right:30px; font-family:cursive;'><b>  Welcome, <?php echo $firstname; ?></b></a></li>
								<li><a href='help.php' style='font-size:16px; color:white; padding-top:20px;'><i class='fa fa-question-circle' aria-hidden='true'></i><b> Need Help</b></a></li>
								<li><a href='login.php' style='font-size:16px; color:white; padding-top:20px;'><i class='fa fa-sign-out' aria-hidden='true'></i><b> Logout</b></a></li>
							</ul>
						</div>
					</nav>
					<nav class='navbar navbar-default navbar-static-top'>
					<div class='container'>
					<div class='navbar-header'>
						<button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#navbar' aria-expanded='false' aria-controls='navbar'>
							<span class='sr-only'>Toggle navigation</span>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
						</button><!--button close-->
						<a class='navbar-brand' href='#'>Leave Management System</a>
					</div><!--navbar header-->
					<div id='navbar' class='navbar-collapse collapse'>
					<ul class='nav navbar-nav navbar-right' style='padding-right:80px;'>
					<li class='active'  id='home'><a href='#home'>Holiday List</a></li>
					<li><a href='attendance.php'>Attendance</a></li>
					<li><a href='trackLeaves.php'>Track Leaves</a></li>
					<li><a href='leavecalender.php'>Leave Calender</a></li>
					<li><a href='ApplyVOE.php'>Apply VOE</a></li>
					</ul>
					
					</div>
					</div>
					</nav>
				
					<div class='container-fluid well' style='margin-top:-20px;'>
			
					<div class='row'>
					<!--2 column start-->
						<div class='col-sm-2'>
							<div class="rectangle">
									<a href="#"><img src="img/4.jpg" class="img-circle img-responsive" alt="" width="150px;" height="80px;"></a>
								<h6 class="text-center" style="color:white; font-size:14px; font-family:Times New Roman, Georgia, Serif;"><?php echo $_SESSION['u_fullname']?></h6>
								
									 <center><span class="text-size-small" style="color:white;">
									 <?php 
										echo $_SESSION['u_emplocation'].", India";
									?>
									</span>
									</center>
						</div>
										
						
							<hr>
								<ul class="list-group">
					<li class="list-group-item active"><a href="#" style="color:white; font-size:18px;">My Account</a></li>
					<li class="list-group-item"><a href="lms.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;My Profile<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:50px;"></i></a></li>
					<li class="list-group-item"><a href="personalinfo.php"><i class="fa fa-user-secret" aria-hidden="true"></i>&nbsp;Personal Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:30px;"></i></a></li>
					<li class="list-group-item"><a href="officialinfo.php"><i class="fa fa-building" aria-hidden="true"></i>&nbsp;Official Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<li class="list-group-item"><a href="applyLeave.php"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;Apply Leave<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<?php
					if(strtoupper($_SESSION['user_dept'])=="HR") {?>
					<li class="list-group-item"><a href="hr.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;HR Section<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:38px;"></i></a></li>
					<?php }elseif(strtoupper($_SESSION['user_desgn'])=="MANAGER") {?>
					<li class="list-group-item"><a href="manager.php"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Manager Section<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:10px;"></i></a></li>
					<?php }?>
					<!--  <li class="list-group-item"><a href="leaveinfo.php"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;My Leave Info<i class="fa fa-angle-right" aria-hidden="true" style="margin-left:20px;"></i></a></li>-->
				</ul>
						</div><!--2 column end-->
						<div class='col-sm-1'></div>
						<div class='col-sm-8'>
							<div class='panel panel-primary'>
								<div class='panel-heading text-center'>
									<strong style='font-size:20px;'>Holidays List</strong>
								</div>
								<div class='panel-body'>
								<?php 
				echo "<div id='tabs'>
						<ul>";
				arsort($years);
				foreach ($years as $val) {
					echo "<li><a href='#".$val."'>".$val."</a></li>";
				}
				echo "</ul>";
				
				foreach ($years as $val1) {
					echo "<div id='".$val1."'>
								<table class='table' id='list".$val1."'></table>
						  		<div id='pagernav".$val1."'></div>
						  </div>";
				}?>
					</div>
					</div>
					</div>
					<div class='col-sm-1'></div>
					</div>
					
			</div></div>
					<footer class='footer1'>
					<div class='container'>
						<div class='row'>
							<div class='col-lg-4 col-md-4'>
								<ul class='list-unstyled clear-margins'>
									<li class='widget-container widget_nav_menu'>
										<h1 class='title-widget'>Email Us</h1>
										<p><b>Anil Kumar Thatavarthi:</b> <a href='mailto:#'></a></p>
										<p><b>Naidile Basvagde :</b> <a href='mailto:#'></a></p>
										<p><b>Sneha Kumari:</b> <a href='mailto:#'></a></p>
									</li>
								</ul>
							</div><!-- widgets column left end -->
							
							<div class='col-lg-4 col-md-4'><!-- widgets column left -->
								<ul class='list-unstyled clear-margins'><!-- widgets -->
									<li class='widget-container widget_nav_menu'>
										<h1 class='title-widget'>Contact Us</h1>
										<p><b>Helpline Numbers </b> 
											<b style='color:#ffc106;'>(8AM to 10PM): </b></p>
										<p>  +91-9740464882, +91-9945732712  </p>
										<p><b>Phone Numbers : </b>7042827160, </p>
										<p> 011-2734562, 9745049768</p>
									</li>
								</ul>
							</div>
									
							<div class='col-lg-4 col-md-4'>
								<ul class='list-unstyled clear-margins'>
									<li class='widget-container widget_nav_menu'>
										<h1 class='title-widget'>Office Address</h1>
										<p><b>Corp Office / Postal Address</b></p>
										<p>5th Floor ,Innovator Building, International Tech Park, Pattandur Agrahara Road, Whitefield, Bengaluru, Karnataka 560066</p>
									</li>
								</ul>
							</div>
						</div>
					</div>
					</footer>
			
					<div class='footer-bottom'>
			
						<div class='container'>
			
							<div class='row'>
			
								<div class='col-xs-12 col-sm-6 col-md-6 col-lg-6'>
			
									<div class='copyright'>
			
										© 2017, All rights reserved
			
									</div>
			
								</div>
			
								<div class='col-xs-12 col-sm-6 col-md-6 col-lg-6'>
			
									<div class='design'>
			
										 <a href='#'><b>ECI TELECOM</b> </a> |  <a href='#'>LMS by ECI</a>
			
									</div>
			
								</div>
			
							</div>
			
						</div>
			
					</div>
						
						</body></html>
			<?php 
}
?>

		
	</body>
</html>
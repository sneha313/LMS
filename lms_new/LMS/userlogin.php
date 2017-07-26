<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Leave Management System</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script src="js/jquery/jquery-1.7.2.min.js" type="text/javascript"></script>
<script language="javascript">
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
	//now call the ajax also focus move from 
	//$("#password").blur(function()
	//{
	//	$("#login_form").trigger('submit');
	//});
});
</script>
<style type="text/css">
body {
margin: 10;
	padding: 10;
	border: 0;
	height: 100%;
	max-height: 100%;
	
	background: #E6EAE9;
	line-height: 1;
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:11px;
}
.top {
margin-bottom: 15px;
}
.buttondiv {
margin-top: 10px;
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
form {
	    width: 250px;
	    padding: 20px;
	    border: 1px solid #270644;
	 
	    /*** Adding in CSS3 ***/
	 
	    /*** Rounded Corners ***/
	    -moz-border-radius: 20px;
    -webkit-border-radius: 20px;
 
    /*** Background Gradient - 2 declarations one for Firefox and one for Webkit ***/
	    background:  -moz-linear-gradient(19% 75% 90deg,#4E0085, #963AD6);
	    background:-webkit-gradient(linear, 0% 0%, 0% 100%, from(#963AD6), to(#4E0085));
	 
	    /*** Shadow behind the box ***/
	    -moz-box-shadow:0px -5px 300px #270644;
	    -webkit-box-shadow:0px -5px 300px #270644;
	}
</style>
</head>
<body>
<br>
<center><h1>ECI Leave Management System</h1></center>
<br>
 <hr> 
<br>
<center><h2><a href="EciLmsHelp.php" title="Click on the link to open FAQ about ECI LMS Web App">HELP</a></h2></center>
<br>
<div align="center">
<form  method="post" action="userlogin.php" id="login_form">


<div style="margin-top:5px">
  <b> User Name : </b><input name="username" type="text" id="username" value="" maxlength="20" />

</div>
<div style="margin-top:5px" >
 <b> &nbsp; Password :  </b> <input name="password" type="password" id="password" value="" maxlength="50" />
   
</div>
<div class="buttondiv">
	
    <input name="Submit" type="submit" id="submit" value="Login" style="margin-left:-10px; height:23px"  />
     <span id="msgbox" style="display:none"></span>
   
</div>

</div>
</form>

<p align='center'> Use windows credientals to login into the App. <br> "ECI_DOMAIN" is <b>not</b> needed </p>
</body>
</html>

<?php
function sendMail($to,$mailBody,$sub,$imageLocation,$BouquetLocation)
{
        require_once 'PHPMailer_v5.1/class.phpmailer.php';
        try {

                $mail = new PHPMailer(true); //New instance, with exceptions enabled
                $mail->IsSMTP();                           // tell the class to use SMTP
                $mail->SMTPAuth   = false;                  // enable SMTP authentication
                $mail->Port       = 25;                    // set the SMTP server port
                $mail->IsSendmail();  // tell the class to use Sendmail
                $mail->From       = "NoticeBoard.FromECIIndia@ecitele.com";
                $mail->FromName   = "Notice Board From ECI India ";
                $a=explode(',', $to);
                $i=count($a);
                for($i=0;$i<count($a);$i++)
                {
                    $mail->AddAddress($a[$i]);
                }
                $mail->Subject  = $sub;
				$mail->AddEmbeddedImage($imageLocation, 'mainImage', $imageLocation);
				$mail->AddEmbeddedImage($BouquetLocation, "BouquetImage", $BouquetLocation);
                $mail->MsgHTML($mailBody);
                $mail->IsHTML(true); // send as HTML
                $mail->Send();
                echo 'Message has been sent.';
        } catch (phpmailerException $e) {
                echo $e->errorMessage();
        }
}

$background_colors = array('#ffe6f3','#ffccff', '#ff9999', '#fedd67', 'white');
$rand_background = $background_colors[array_rand($background_colors)];

//random font color
$font_colors = array('blue','#00FFFF','#3399ff','#ff1a94','#b3b300','#e60073','#CF3897','#20b2aa');
$rand_fontcolor = $font_colors[array_rand($font_colors)];

//random font family
$fonts = array("Brush Script Std, Brush Script MT, cursive", "Coronetscript","FreeMono","OCR A Std, monospace", "Andale Mono, monospace","Jazz LET, fantasy","Bookman, serif","Avantgarde, sans-serif");
shuffle($fonts);
$randomFont = array_shift($fonts);

//random font size
$fontsize = array("40", "36", "32", "38", "36");
shuffle($fontsize);
$randomFontSize = array_shift($fontsize);

//random bouquet
$bouquetDir = 'bouquet/';
$bouquets = glob($bouquetDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
$randomBouquet = $bouquets[array_rand($bouquets)]; 

//random image
$imagesDir = 'Birthdayimage/';
$images = glob($imagesDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
$randomImage = $images[array_rand($images)]; 

require_once 'Library.php';
$db=connectToDB();
$query="select empname, emp_emailid,location from emp where  DATE_FORMAT(birthdaydate, '%m-%d') = DATE_FORMAT('1994-12-06', '%m-%d')";
$result=$db->query($query);
if($db->countRows($result) > 0){
	$row=$db->fetchAssoc($result);
	$empname=$row['empname'];
	$emp_emailid=$row['emp_emailid'];
	$location=$row['location'];
	$firstname = strtok($empname, ' ');
	$lastname = strstr($empname, ' ');
	//subject of mail
	$sub="***Happy Birthday ".$firstname."***";
	if($location=='BLR')
	{
		$blremplist="Sneha.Kumari@ecitele.com";
		$to=$blremplist;
	}
	else{
		$mumemplist="Sneha.Kumari@ecitele.com";
		$to=$mumemplist;
	}
	$mailbody='<body style="background: '.$rand_background.';">';
	$mailbody=$mailbody.'<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<h3 style="color:'.$rand_fontcolor.'; font-size:'.$randomFontSize.'; font-family: '.$randomFont.';">
					Dear '.$firstname.',</h3>
				<center>
					<img src="cid:mainImage">
					<br><br>
					<img src="cid:BouquetImage">
					
				</center>
			</div>
			<div class="col-sm-2"></div>
		</div>
		</div>
		<footer class="footer1">
			<div class="container">
				<div class="row">
					<div class="col-sm-4"></div>
					<div class="col-sm-4">
						<center><h3 style="color:'.$rand_fontcolor.'; font-size:'.$randomFontSize.'; font-family: '.$randomFont.';"><b>BEST WISHES</b>
				<br><b>TEAM ECI!!</b></h3></center>
					</div>
					<div class="col-sm-4"></div>
					</div>
				</div>
				</footer></body>';
				
//echo $mailbody;
	#echo $to;
	$imageLocation= $randomImage;
	$BouquetLocation= $randomBouquet;
	sendMail($to, $mailbody, $sub, $imageLocation,$BouquetLocation);
}
?>

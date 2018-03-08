<?php 
//require_once 'Library.php';
require_once "librarycopy1.php";
$db=connectToDB();
$tickcount=1;
$xticksFortyCurve="";
$numTicks=0;

function timediffinHR ($first, $last) {
	if ($last == '00:00:00') {return 0;}
	$tm= strtotime($last)-strtotime($first);
	return sprintf("%.2f", $tm/3600);
}

function getMonday ($givenDate) {
        $off= 1 - date('w', strtotime($givenDate));
        return date('Y-m-d', strtotime("$givenDate $off day"));
}
function getFriday ($givenDate) {
        $off= 5 - date('w', strtotime($givenDate));
        return date('Y-m-d', strtotime("$givenDate $off day"));
}
function getNextMonday ($givenDate) {
        $off= 8 - date('w', strtotime($givenDate));
        return date('Y-m-d', strtotime("$givenDate $off day"));
}
function getFourtyMark($empid,$from,$to) 
{
	global $db,$tickcount,$numTicks,$xticksFortyCurve;
	$start=getMonday($from);
	$end=getFriday($from);
	$curve="";
	$curve=$curve."[[1,8.5],";
	$avg=0;
	$xcount=1;
	$numTicks=0;
	$xticksFortyCurve=$xticksFortyCurve."[";
	$xticksFortyCurve=$xticksFortyCurve."[$tickcount,\"(0.0)\"],";
	$tickcount=$tickcount+7;
	while($end<=getFriday($to)) 
	{
		$sum=0;
		$numTicks=$numTicks+1;
		$day=date('D,d M y', strtotime($end));
		$xticksFortyCurve=$xticksFortyCurve."[$tickcount,\"$day\"],";
		$tickcount=$tickcount+7;
		$query="select * from `inout` where empid=".$empid." and Date between '$start' and '$end'  order by Date";
		$result=$db->pdoQuery($query);
		$noRows=$result -> count($sTable = 'inout', $sWhere = 'empid = "'.$empid.'" and Date >= "'.$start.'" and Date<="'.$end.'"' );
		$rows=$db->pdoQuery($query)->results();
		foreach($rows as $row)
		{
			$diff=timediffinHR($row['First'],$row['Last']);
			$sum=$sum+$diff;
		}
		if($noRows!=0) {
			$avg=($sum/$noRows);
			$xcount=$xcount+7;
			$curve=$curve."[".$xcount.",".$avg."],";
		} else {
			$xcount=$xcount+7;
			$curve=$curve."[$xcount,null],";
		}
		$start=getNextMonday($start);
		$end=getFriday($start);
	}
	$curve=rtrim($curve,",");
	$curve=$curve."]";
	$xticksFortyCurve=rtrim($xticksFortyCurve,",");
	$xticksFortyCurve=$xticksFortyCurve."]";
	return $curve;
}


if (isset($_REQUEST['empid'])  && isset($_REQUEST['month']) && isset($_REQUEST['first'])) {
$empid=$_REQUEST['empid'];
$to=$_REQUEST['month'];	 
$from=$_REQUEST['first'];
list($year,$month,$day) = explode('-', $to);
//Get Name of employee
$getempNameQuery="select empname from emp where state='Active' and empid='".$empid."'";
$getName=$db->pdoQuery($getempNameQuery)->results();
foreach($getName as $getempNameRow)
//Get the average of 40 hour curve
$graph=getFourtyMark($empid,$from,$to);

//Get avg 8 mark curve
$foutyHourCurve="";
$foutyHourCurve=$foutyHourCurve."[";
for($i=1;$i<=($numTicks*7);$i++)
{
	$foutyHourCurve=$foutyHourCurve."[".$i.",8.5],";
}
$foutyHourCurve=rtrim($foutyHourCurve,",");
$foutyHourCurve=$foutyHourCurve."]";

//<div> id's
$contain_three="contain_three_$empid";

echo "<table class='table table-hover'>
	  <tr>
    <td>
	  <div id='".$contain_three."' style='width:1200px;height:384px;margin:8px;'></div>
	  
	  <script type='text/javascript'>
	  var
     	container = document.getElementById('".$contain_three."'),
     	xticks = ".$xticksFortyCurve.", // Ticks for the X-Axis
     	d1 = ".$graph.",
        d2 = ".$foutyHourCurve.",
        graph, i;

    graph = Flotr.draw(
    container, [d1,d2], {
    	title: 'Average of 42.5 Hour Mark per week',
    	subtitle: '(From $from to $to)',
    	xaxis: {
        	 noTicks : $numTicks,  
     		 minorTickFreq: 6,
     		 title :'DATE',
     		 titleAlign:'center',
     		 labelsAngle: 45,
     		 ticks:$xticksFortyCurve
   		 },
   		 yaxis: {
   		 	 title :'AVG',
     		 titleAlign:'center'     		 
   		 }, 
   		 grid: {
    		  minorVerticalLines: true,
    		  minorHorizontalLines:false
   		 },
   		 mouse : {
        	track           : true, // Enable mouse tracking
        	lineColor       : 'purple',
        	relative        : true,
        	position        : 'ne',
        	sensibility     : 1,
        	trackDecimals   : 2,
	        trackFormatter  : function (o) { return 'Avg = ' + o.y; }
      	},
      	crosshair : {
	        mode : 'xy'
      	},
   		 HtmlText: false,
      	 legend: {
         	position: 'nw'
      }
    });
    </script>
    </td>
    </tr>
    </table>";
}
?>

<?php
session_start();
require_once 'librarycopy1.php';
require_once 'generalcopy.php';
$db=connectToDB();
$holidayListTable=$_SESSION['u_holidayListTable'];
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
	//$sql = $dbConn->query("select * from ".$holidayListTable);
	$sql = $dbConn->pdoQuery('select * from "'.$holidayListTable.'";');
	switch($_REQUEST['oper']){
		case 'edit':
			$dataArray = array('date'=>$date,'holidayname'=>$occasion);
			// two where condition array
			$aWhere = array('id'=>$id);
			// call update function
			$sql = $db->update($holidayListTable, $dataArray, $aWhere)->affectedRows();
			//$sql = $dbConn->query("UPDATE ".$holidayListTable." SET date='".$date."', holidayname='".$occasion."' WHERE id='".$id."'");
			break;
		case 'del':
			// where condition array
			$aWhere = array('id'=>$id);
			// call update function
			$sql = $dbConn->delete($holidayListTable, $aWhere)->affectedRows();
			//$sql = $dbConn->query("DELETE FROM ".$holidayListTable."  WHERE id='".$id."'");
			break;
		case 'add':
			$dataArray = array('date'=>$date,'holidayname'=>$occasion);
			// use insert function
			$sql = $dbConn->insert($holidayListTable,$dataArray)->getLastInsertId();
				
			//$sql = $dbConn->query("INSERT INTO ".$holidayListTable." (date,holidayname) VALUES ('".$date."','".$occasion."')");
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
	$query.= "SELECT id,date,day,holidayname,leavetype FROM ".$holidayListTable."  where `date` like '%".$_REQUEST['year']."%'  ";
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
	$query="select date from ".$holidayListTable;
	//$result=$dbConn->pdoQuery($query);
	$dates=array();
	$rows=$db->pdoQuery($query)->results();
	//while($row=$result->results()) 
		foreach($rows as $row)
	{
		$year=explode("-",$row['date']);
		array_push($dates,$year[0]);
	}
	$years=array_unique($dates);
	echo("<!DOCTYPE html PUBLIC \" -//w3c//DTD XHTML 1.0 Strict//EN\" 
 		\"http://www.w3c.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
  		<html>
			<body>");
	echo "<script type='text/javascript'>";
			
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
						height: 'auto',
						width: 900,
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
					{
						height:280,reloadAfterSubmit:true,
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
					{
						reloadAfterSubmit:true,
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
		echo "jQuery(document).ready(function(){
			$(function() {
				$( '#tabs').tabs();
			});";
	echo "});
	</script>";
?>
<div class='container-fluid'><!--container fluid div start-->
	<div class='row'><!--row start-->
		<div class='col-sm-12'>
			<div class='panel panel-primary'>
				<div class='panel-heading text-center'>
					<strong style='font-size:20px;'>Yearly Holidays List</strong>
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
							}
							//echo $dom->saveXML();
							echo "</div>";//tab div end
							
					?>
					
				</div><!--panel body end-->
			</div><!--panel end-->
		</div><!--12 column end--> 	
	</div><!--row end-->
</div><!--container-fluid div end-->
<?php 
	}
?>
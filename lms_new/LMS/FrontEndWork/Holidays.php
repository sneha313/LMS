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
	echo "});
	</script>";
?>
<div class='container-fluid'><!--container fluid div start-->
	<div class='row'><!--row start-->
		<div class='col-sm-12'>
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
							}
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

	
<?php 
session_start();
require_once 'Library.php';
require_once 'generalFunctions.php';
$db=connectToDB();
function Strip($value)
{
        if(get_magic_quotes_gpc() != 0)
        {
                if(is_array($value))
                        if ( array_is_associative($value) )
                        {
                                foreach( $value as $k=>$v)
                                        $tmp_val[$k] = stripslashes($v);
                                $value = $tmp_val;
                        }
                        else
                                for($j = 0; $j < sizeof($value); $j++)
                                        $value[$j] = stripslashes($value[$j]);
                else
                        $value = stripslashes($value);
        }
        return $value;
}
if (array_key_exists('getData',$_REQUEST)) 
	{
		$sidx = $_REQUEST['sidx'];
		$sord = $_REQUEST['sord'];
		if(!$sidx) $sidx =1;
		$wh = " 1 ";
		if(isset($_REQUEST['_search']))
		{
		$searchOn=Strip($_REQUEST['_search']);
		if($searchOn == 'true') {
                $abc=(json_decode($_REQUEST['filters'],TRUE));
                $sarr = $abc['rules'];
                for ($i = 0; $i < count($sarr); $i++) {
                        $opts[$sarr[$i]['field']]=$sarr[$i]['data'];
                }
                foreach( $opts as $k=>$v) {
                        $wh .= " AND ".$k." LIKE '%".$v."%'";
                }
        }
		}
        $query = "SELECT id,empid,empusername,empname,emp_emailid,dept,joiningdate,birthdaydate,role,
        		 managerid,managername,location,state FROM emp where $wh and state='Active' ORDER BY ".$sidx." ".$sord.";";
		echo jqGrid_GetData($query,$_REQUEST);
	} 
	else 
	{
		echo("
		<!DOCTYPE html PUBLIC \" -//w3c//DTD XHTML 1.0 Strict//EN\"
 		\"http://www.w3c.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
  		<html>
		<head>
	");
	includeJQGrid();
		echo("
    	<script type='text/javascript'>
    	jQuery(document).ready(function(){ 
      	jQuery('#list').jqGrid({
        url:'eciemp.php?getData=1',
        datatype: 'xml',
        mtype: 'GET',
        colNames:['id','Emp Id', 'Emp User Name','Emp Full Name','Emp Email Id','Department','Joining Date','Birthday Date','Role','Manager Id','Manager Name','Emp Location','State'],
        colModel :[ 
          {name:'id', index:'id', width:40,editable:false,hidden:true}, 
          {name:'empid', index:'empid', width:100,editable:true,editoptions:{size:10},editrules:{required:true}}, 
          {name:'empusername', index:'empusername', width:130,editable:true, align:'left'}, 
          {name:'empname', index:'empname', width:200,editable:true,align:'center'}, 
          {name:'emp_emailid', index:'emp_emailid', width:100,editable:true,align:'center',hidden:true},
          {name:'dept', index:'dept', width:170,editable:true,edittype:'select',editoptions:{dataUrl:'eciemp.php?deptSelect=1'},align:'center'},
          {name:'joiningdate', index:'joiningdate', width:170,editable:true,align:'center'},
          {name:'birthdaydate', index:'birthdaydate', width:130,editable:true,align:'center',hidden:true}, 
          {name:'role', index:'role', width:90,editable:true,edittype:'select',editoptions:{value:'user:user;Manager:Manager;HR:HR'},align:'center'},
          {name:'managerid', index:'managerid',editable:true,edittype:'select',editoptions:{dataUrl:'eciemp.php?manageridSelect=1'}, width:80,align:'center'},
		  {name:'managername', index:'managername',align:'center'},
		  {name:'location', index:'location',align:'center',width:90},
		  {name:'State', index:'State',align:'center', width:80} 
        ],
        pager: '#pagernav',
        rowNum:10,
        rowList:[10,20,30,50,100],
        sortname: 'empid',
        sortorder: 'asc',
        viewrecords: true,
        caption: 'Eci Employees',
        height: 'auto',
        width: '950',
        toppager: true,
        editurl: 'eciemp.php'
      })
    jQuery('#gbox_list').css('margin','auto'); //center the table
    jQuery('#list').jqGrid('filterToolbar',{stringResult: true,searchOnEnter : true});
    }); 
    </script>

   </head>
   <body>
	<div class='col-sm-12'>
		<div class='panel panel-primary'>
			<div class='panel-heading text-center'>
				<strong style='font-size:20px;'>V&V Group Information</strong>
			</div>
			<div class='panel-body'>
				<a href='index.php' TARGET = '_top'>Home</a> &nbsp
    			<a href='javascript: history.go(-1)'>Back</a>
			    <table class='table table-bordered' id='list'></table>
			    <div id='pagernav'></div>
			</div>
		</div>
	</div>
   </body>
 </html>
 ");
}
?>

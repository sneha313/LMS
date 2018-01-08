<?php
require_once ("db.class.php");
function includeJQGrid()
{
	echo '<link href="js/jqueryui/css/redmond/jquery-ui.css" rel="stylesheet">';
        echo '<script src="js/jquery/jquery.js" type="text/javascript"></script>';
        echo '<script src="js/jqueryui/js/jquery-ui.js"></script>';
        echo '<script src="js/jqgrid/grid.locale-en.js" type="text/javascript"></script>';
        echo '<script type="text/javascript" src="js/jquery/jquery.validate.min.js"></script>';
        echo '<script src="js/jqgrid/jquery.jqGrid.min.js" type="text/javascript"></script>';
        echo '<script src="js/jquery/jquery.searchFilter.js" type="text/javascript"></script>';
        echo '<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/jqgridcss/ui.jqgrid.css" />';
        echo '<link rel="stylesheet" type="text/css" media="screen" href="css/table.css" />';
}
function connectToDB()
{
	$config = new config("localhost", "lms", "eciTele!", "lmsng", "", "mysql");
	$db = new db($config);
	$db->openConnection();
	return $db;
}

function auth_by_ldap ($login,$password,$nis_domain='eci_domain'){
	return 1;
         if($password=="")
                return 0;
        #$ldapconn = ldap_connect("CNHZDC02.ecitele.com")
        $ldapconn = ldap_connect("inbawpvdc01.ecitele.com")
                or die("Could not connect to LDAP server.");
        $ldapbind = ldap_bind($ldapconn,$nis_domain."\\".$login,$password);
        if ($ldapbind) {
                ldap_unbind( $ldapconn );
                return 1;
        } else {
                return 0;;
        }
        return 0;

}
function jqGrid_GetData($query,$request) {

	// we should set the appropriate header information. Do not forget this.
	header("Content-type: text/xml;charset=utf-8");

	// Get the requested page. By default grid sets this to 1.
	$page = $request['page'];

	// get how many rows we want to have into the grid - rowNum parameter in the grid
	$limit = $request['rows'];

	// Connect to the server and select the current database
	$dbConn = connectToDB();

	// execute the sql query
	$results = $dbConn->query($query);

	// get the number of rows in the result set
	$resultTotal =$dbConn->query("SELECT FOUND_ROWS()");
	$res=$dbConn->fetchArray($resultTotal);
	$numRows =  $res['FOUND_ROWS()'];

	// calculate the total pages for the query
	if( $numRows > 0 ) {
		$total_pages = ceil($numRows/$limit);
	} else {
		$total_pages = 1;
	}

	// if for some reasons the requested page is greater than the total
	// set the requested page to total page
	if ($page > $total_pages) $page=$total_pages;

	// calculate the starting position of the rows
	$start = $limit*$page - $limit + 1;

	// if for some reasons start position is negative set it to 0
	// typical case is that the user type 0 for the requested page
	if($start <0) $start = 0;

	$numFields = mysql_num_fields($results);
	for ($index = 0; $index < $numFields; $index++) {
		$header = mysql_field_name($results, $index);
		$columns[$index] = $header;
	}
	// start building the xml document
	$s = "<?xml version='1.0' encoding='utf-8'?>";
	$s .= "<rows>";
	$s .= "<page>".$page."</page>";
	$s .= "<total>".$total_pages."</total>";
	$s .= "<records>".$numRows."</records>";

	// be sure to put text data in CDATA
	$rowCount = $start-1;
	mysql_data_seek($results,$rowCount);
	while($row = $dbConn->fetchArray($results)) {
		$s .= "<row id='". $row[$columns[0]]."'>";
		for ($i=0;$i<$numFields;$i++){
			$s .= "<cell>".htmlspecialchars($row[$columns[$i]])."</cell>";
			//$s .= "<cell><![CDATA[". $row[note]."]]></cell>";
		}
		$s .= "</row>";

		$rowCount++;
		if ($rowCount >= ($start+$limit)){
			break;
		}
	}
	$s .= "</rows>";

	// Close the Database connection
	$dbConn->closeConnection();

	return $s;
}


?>

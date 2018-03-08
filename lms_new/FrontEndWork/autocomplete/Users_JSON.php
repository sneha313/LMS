<?php
//require_once '../Library.php';
require_once '../librarycopy1.php';
$db=connectToDB();

if (array_key_exists('term',$_REQUEST)) {
    $q = strtolower($_REQUEST["term"]);
} elseif (array_key_exists('q',$_REQUEST)) {
    $q = strtolower($_REQUEST["q"]);
}
if (!$q) return;

$data = array();

$query="SELECT distinct(empname) from emp WHERE empname LIKE '$q%' and state='Active' ORDER BY empname ASC";
$sql=$db->pdoQuery($query)->results();
//while ($row=$db->fetchassoc($sql)) {
foreach($sql as $row){
$item = $row['empname'];
    $data[]=$item;
}
$query="SELECT distinct(empname) from emp WHERE state='Active' and empname LIKE '%$q%' AND empname NOT LIKE '$q%' ORDER BY empname ASC";
$sql=$db->pdoQuery($query)->results();
//while ($row=$db->fetchassoc($sql)) {
foreach($sql as $row){
$item = $row['empname'];
    $data[]=$item;
}
echo json_encode($data);
?>

<?php
require_once '../Library.php';
$db=connectToDB();

if (array_key_exists('term',$_REQUEST)) {
    $q = strtolower($_REQUEST["term"]);
} elseif (array_key_exists('q',$_REQUEST)) {
    $q = strtolower($_REQUEST["q"]);
}
if (!$q) return;

$data = array();

$sql=$db->query("SELECT distinct(empname) from emp WHERE empname LIKE '$q%' and state='Active' ORDER BY empname ASC");
while ($row=$db->fetchassoc($sql)) {
$item = $row['empname'];
    $data[]=$item;
}

$sql=$db->query("SELECT distinct(empname) from emp WHERE state='Active' and empname LIKE '%$q%' AND empname NOT LIKE '$q%' ORDER BY empname ASC");
while ($row=$db->fetchassoc($sql)) {
$item = $row['empname'];
    $data[]=$item;
}
echo json_encode($data);
?>

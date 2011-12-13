<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include("db_setup.php");

include_once("suggest_mesh.php");

//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
//Select Database
mysql_select_db($dbname) or die(mysql_error());
// Retrieve data from Query String
$term = $_GET['term'];
$qlimit = $_GET['qlimit'];
$tree_filter=$_GET['tfilter'];
$id=$_GET['id'];

if ($term=='') {
   echo "Enter a MeSH term<br>";
   echo "For example: <a href=\"javascript:document.getElementById('" . $id . "').value='Alzheimer Disease'; void(0);\">Alzheimer Disease</a><br>";
   return;
}

suggest_mesh($term, $qlimit, $tree_filter, $id);
?>
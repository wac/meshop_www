<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include("db_setup.php");

include_once("suggest_name.php");

//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
//Select Database
mysql_select_db($dbname) or die(mysql_error());
// Retrieve data from Query String
$name = $_GET['name'];
$qlimit = $_GET['qlimit'];
$db = $_GET['db'];
$fieldname = $_GET['fieldname'];
$fieldtxt = $_GET['fieldtxt'];
$exampletxt = $_GET['exampletxt'];
$id = $_GET['id'];

if ($name=='') {
   echo "Enter a $fieldtxt<br>";
   echo "For example: <a href=\"javascript:document.browseForm.$fieldname.value='$exampletxt'; void(0);\">$exampletxt</a><br>";
   return;
}

suggest_name($name, $qlimit, $db, $fieldname, $fieldtxt, $id);
?>
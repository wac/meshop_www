<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include("db_setup.php");

include_once("suggest_gene.php");

//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
//Select Database
mysql_select_db($dbname) or die(mysql_error());
// Retrieve data from Query String
$gene = $_GET['gene'];
$qlimit = $_GET['qlimit'];

if ($gene=='') {
   echo "Enter a human gene<br>";
   echo "For example: <a href=\"javascript:document.browseForm.gene.value='NFE2L2'; void(0);\">NFE2L2</a><br>";
   return;
}


suggest_gene($gene, $qlimit);
?>
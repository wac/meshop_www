<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include_once("overrep_pubmed_lib.php");

// Retrieve data from Query String
$limit = $_GET['limit'];

$db = $_GET['db'];
$pubmedlist = $_GET['pubmedlist'];
$pubmedlist = preg_split("/[\s,\\\\\|\-\/]+/", $pubmedlist);

overrep_pubmed($pubmedlist, $db, $limit);
?>
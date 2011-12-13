<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include_once("overrep_pubmed_lib.php");

// Retrieve data from Query String
$limit = $_GET['limit'];

$db = $_GET['db'];

$maxdate = $_GET['maxdate'];

$pmquery = $_GET['pmquery'];

$query = 'http://www.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=' . urlencode($pmquery) . '&retmax=50&mindate=1900&maxdate=' . urlencode($maxdate);

echo '<b>Query:</b>'.$query.'<br>';

$pmxml = simplexml_load_file($query);

$pubmedlist = $pmxml->xpath('//Id');

overrep_pubmed($pubmedlist, $db, $limit);

?>
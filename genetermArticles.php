<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include("db_setup.php");

include_once("suggest_gene.php");
include_once("suggest_mesh.php");

?>

<html>
<head>
  <title>Browse Gene-Keyword Literature Results</title>
  <link rel="stylesheet" type="text/css" href="browse-form.css">
  <script language="javascript" src="simpleAjax.js"> </script>
  <script language="javascript" src="suggest.js"> </script>
  <script language="javascript" src="clipboard.js"> </script>
</head>
<body>

<?php

//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
//Select Database
mysql_select_db($dbname) or die(mysql_error());
// Retrieve data from Query String
$gene = $_GET['gene'];
$term = $_GET['term'];

echo "<b>Gene:</b> $gene<br> <b>Term:</b> $term<br>";

$db = "gene2pubmed";

//build query

$query = "SELECT pubmed.pmid, title FROM gene2pubmed, pubmed, pubmed_mesh_parent WHERE pubmed_mesh_parent.pmid=pubmed.pmid AND pubmed.pmid=gene2pubmed.pmid AND gene_id=$gene AND mesh_parent=\"$term\"";

//Execute query
$qry_result = mysql_query($query) or die(mysql_error());

echo "<br>Returning " . mysql_num_rows($qry_result) . " results";

//Build Result String
$display_string = "<table class=\"result-table\">";
$display_string .= "<tr>";
$display_string .= "<th>PMID</th>";
$display_string .= "<th>Title</th>";
$display_string .= "</tr>";

// Insert a new row in the table for each person returned
while($row = mysql_fetch_array($qry_result)){
  $display_string .= "<tr>";
  $display_string .= "<td><a href=\"http://www.ncbi.nlm.nih.gov/pubmed/$row[pmid]\">$row[pmid]</a></td><td>$row[title]</td>";
  $display_string .= "</tr>";  
}

$display_string .= "</table>";

echo $display_string;

echo "<div class=\"query-box\"><b>SQL Query:</b> " . $query . "<br /> </div>";

?>

</body>

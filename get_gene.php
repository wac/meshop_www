<?php
//$dbhost = "vm2.cmmt.ubc.ca";
//$dbuser = "wcheung";
//$dbpass = "wcheung243";
//$dbname = "warrendb";

include("db_setup.php");

//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
//Select Database
mysql_select_db($dbname) or die(mysql_error());
// Retrieve data from Query String
$gene = $_GET['gene'];
// Escape User Input to help prevent SQL Injection
$gene = mysql_real_escape_string($gene);
//build query
if(is_numeric($gene)) {
  $query = "SELECT * FROM gene WHERE gene_id=$gene LIMIT 10";
} else {
  $query = "SELECT * FROM gene WHERE locus LIKE '$gene%' LIMIT 10";
}
//Execute query
$qry_result = mysql_query($query) or die(mysql_error());

echo mysql_num_rows($qry_result);
echo " results found.\n";

//Build Result String
$display_string .= "<table>";
$display_string .= "<tr>";
$display_string .= "<th>Gene ID</th>";
$display_string .= "<th>Locus</th>";
$display_string .= "<th>Taxon</th>";
$display_string .= "</tr>";

// Insert a new row in the table for each person returned
while($row = mysql_fetch_array($qry_result)){
  $display_string .= "<tr>";
  $display_string .= "<td><a href='#' onclick=\"document.geneForm.gene.value='$row[gene_id]'; ajaxFunction()\">$row[gene_id]</a></td>";
  $display_string .= "<td>$row[locus]</td>";
  $display_string .= "<td>$row[taxon_id]</td>";
  $display_string .= "</tr>";  
}
echo "Query: " . $query . "<br />";
$display_string .= "</table>";
echo $display_string;
?>

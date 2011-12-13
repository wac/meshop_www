<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include("db_setup.php");

include_once("suggest_gene.php");
include_once("suggest_mesh.php");

//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
//Select Database
mysql_select_db($dbname) or die(mysql_error());
// Retrieve data from Query String
$term = $_POST['term'];
$db = $_POST['db'];
$db = mysql_real_escape_string($db);
$genelist = preg_split("/[\s,\\\\\|\-\/]+/", $_POST['genelist']);

if (($term == '') || (!is_mesh($term, ''))) {
   echo "Please specify a valid MeSH term<br>'$term' was provided.";
   return;
}

header("Content-type: text/plain");

$i = 0;
$j = 0;

echo "# DB: $db\n";

echo "# Term: $term\n# locus\tGeneID\tMeSHTerm\tpval\n";

foreach ($genelist as $gene) {
  if ($gene == '') {
    continue;
  }

  
  $i++;

  $query = "SELECT gene.locus, $db.gene_id, $db.term, $db.p_val FROM $db, gene, mesh_child WHERE gene.gene_id=$db.gene_id AND $db.gene_id=$gene AND mesh_child.term='$term' AND mesh_child.child=$db.term";

  //Execute query
  $qry_result = mysql_query($query) or die(mysql_error());
  while($row = mysql_fetch_array($qry_result)){
  	echo "$row[locus]\t$row[gene_id]\t$row[term]\t$row[p_val]\n";
   	$j++;
  }
  flush();
}

echo "# Processed $i genes\n";
echo "# Found $j gene-disease direct associations\n";



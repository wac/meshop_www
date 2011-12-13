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

$genestats = FALSE;

if ($term == '') {
   $genestats = TRUE;   
} else if (!is_mesh($term, '')) {
   echo "Please specify a valid MeSH term<br>'$term' was provided.";
   return;
}

header("Content-type: text/plain");

$i = 0;
$j = 0;

echo "# DB: $db\n";

if ($genestats) {
  echo "# Global Stats\n# locus\tGeneID\tPubMedArticles\n";
} else {
  echo "# Term: $term\n# locus\tGeneID\tPMID\tPubMedTitle\n";
}


foreach ($genelist as $gene) {
  if ($gene == '') {
    continue;
  }

  
  $i++;

  if ($genestats) {
    $query = "SELECT gene.locus,$db.gene_id, COUNT($db.pmid) AS pmcount FROM $db, pubmed, gene WHERE gene.gene_id=$db.gene_id AND $db.pmid=pubmed.pmid AND $db.gene_id=$gene GROUP BY $db.gene_id";
    // echo "$query\n";

    //Execute query
    $qry_result = mysql_query($query) or die(mysql_error());
    while($row = mysql_fetch_array($qry_result)){
    	echo "$row[locus]\t$row[gene_id]\t$row[pmcount]\n";
   	$j++;
    }
  } else {
    $query = "SELECT gene.locus,$db.gene_id, $db.pmid, pubmed.title FROM $db, pubmed_mesh_parent, pubmed, gene WHERE gene.gene_id=$db.gene_id AND pubmed.pmid=$db.pmid AND $db.pmid=pubmed_mesh_parent.pmid AND pubmed_mesh_parent.mesh_parent='$term' AND $db.gene_id=$gene";

    //Execute query
    $qry_result = mysql_query($query) or die(mysql_error());
    while($row = mysql_fetch_array($qry_result)){
    	echo "$row[locus]\t$row[gene_id]\t$row[pmid]\t$row[title]\n";
   	$j++;
    }
  }
  flush();
}

echo "# Processed $i genes\n";
echo "# Found $j gene-disease direct associations\n";



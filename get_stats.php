<?php
function format_result($query, $display_string) {
  echo "<p>Query: " . $query . "<br />";

  echo $display_string . ": ";
  
  //Execute query
  $qry_result = mysql_query($query) or die(mysql_error());
  
  $row = mysql_fetch_array($qry_result);
  echo $row[result];
  echo "</p>";  
}

$dbhost = "vm2.cmmt.ubc.ca";
$dbuser = "wcheung";
$dbpass = "wcheung243";
$dbname = "warrendb";
//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
//Select Database
mysql_select_db($dbname) or die(mysql_error());

format_result("SELECT COUNT(*) AS result FROM gene","Number of Genes");
format_result("SELECT COUNT(*) AS result FROM gene WHERE taxon_id=9606",
	      "Number of Human Genes");
format_result("SELECT COUNT(DISTINCT gene.gene_id) AS result FROM gene,generif WHERE taxon_id=9606 AND gene.gene_id=generif.gene_id",
	      "Number of Human Genes with GeneRIF");
format_result("SELECT COUNT(DISTINCT pmid) AS result FROM gene,generif WHERE taxon_id=9606 AND gene.gene_id=generif.gene_id",
	      "Number of Human Gene PubMed articles via GeneRIF");
format_result("SELECT COUNT(DISTINCT gene.gene_id) AS result FROM gene,gene2pubmed WHERE taxon_id=9606 AND gene.gene_id=gene2pubmed.gene_id",
	      "Number of Human Genes with Gene2Pubmed");
format_result("SELECT COUNT(DISTINCT pmid) AS result FROM gene,gene2pubmed WHERE taxon_id=9606 AND gene.gene_id=gene2pubmed.gene_id",
	      "Number of Human Gene PubMed articles via Gene2PubMed");
?>

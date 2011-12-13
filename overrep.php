<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include("db_setup.php");

include_once("suggest_gene.php");
include_once("suggest_mesh.php");

require_once 'PDL/ChiSqrDistribution.php';

//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
//Select Database
mysql_select_db($dbname) or die(mysql_error());
// Retrieve data from Query String
$limit = $_GET['limit'];
$db = $_GET['db'];
$db = mysql_real_escape_string($db);
$genelist = preg_split("/[\s,\\\\\|\-\/]+/", $_GET['genelist']);
$format =  $_GET['format'];


$i = 0;
$j = 0;

$raw_profile=array();
$count_profile=array();
$article_profile=array();

foreach ($genelist as $gene) {
  if ($gene == '') {
    continue;
  }
  
  $query = "SELECT $db.term, $db.term_refs, $db.p_val FROM $db WHERE $db.gene_id='$gene'";

  //Execute query
  $qry_result = mysql_query($query) or die(mysql_error());

  // FIXME don't count if no tuples

  if (mysql_num_rows($qry_result) < 1) {
     // Try to match locus
     $query = "SELECT $db.term, $db.term_refs, $db.p_val FROM gene, $db WHERE $db.gene_id=gene.gene_id AND gene.locus like '$gene'";

     //Execute query
     $qry_result = mysql_query($query) or die(mysql_error());
     if (mysql_num_rows($qry_result) < 1) {
          echo "No literature for gene $gene<br>";
     	  continue;
     }
  }

  $i++;

  if ($format == "raw") {
    echo "# Gene $gene - ";
    echo mysql_num_rows($qry_result);
    echo " terms processed\n";
  } else {
    echo "Gene $gene - ";
    echo mysql_num_rows($qry_result);
    echo " terms processed<br>";
  }
  while($row = mysql_fetch_array($qry_result)){
   $score=0; 
   if ($row[p_val] == 0) {
      // ln (2^127) ~= 88
      $score=88;
   } else {
      $score=2*(-log($row[p_val]));
   }

   $raw_profile[$row[term]]=$raw_profile[$row[term]]+$score;
   $count_profile[$row[term]]=$count_profile[$row[term]]+1;
   $article_profile[$row[term]]=$article_profile[$row[term]]+$row[term_refs];
   $j++;
  }
  flush();
}

if ($format == "raw") {
   echo "# Processed $i genes (Chi Square df=" . ($i * 2) . ")\n";
   echo "# $j gene-profile terms processed\n";
} else {
   echo "<hr>Processed $i genes (Chi Square df=" . ($i * 2) . ")<br>";
   echo "$j gene-profile terms processed<br>";
}

$num_terms = sizeof($raw_profile);

if ($format == "raw") {
   echo "# $num_terms MeSH Terms analysed\n";
} else {
  echo "$num_terms MeSH Terms analysed<hr>";
}

// (Optional) Filter end terms
//$filter_profile=array();
//if (1) {
//   foreach ($raw_profile as $key => $val) {
//      $query = "SELECT * FROM mesh_child WHERE term=\"$key\"";
//      //Execute query
//      $qry_result = mysql_query($query) or die(mysql_error());
//      while($row = mysql_fetch_array($qry_result)){
//          if (in_array($row['child'], $raw_profile) && ($raw_profile[$key] < $raw_profile[$row['child']])) {
//       	     unset($raw_profile[$key]);
//	     continue;
//      	  }
//      }
//   }
//}

// Output

if ($format == "raw") {
   echo "\n\nMeSH Term\tGenes\tArticles\tRaw Score\tFisher Combined p-value\tChi Score\n";
} else {
  echo  "<table class=\"result-table\">";
  echo "<tr><th>MeSH Term</th><th>Genes</th><th>Articles</th><th>Raw Score</th><th>Fisher Combined p-value (Chi Square &alpha)</th></tr>";
} 

arsort($raw_profile);

foreach ($raw_profile as $key => $val) {
  $raw_score = sprintf("%.2e", $val);
  $chi = new ChiSqrDistribution(2*$i);
// Probability of x > X^2
  $chi_score = 1.0-$chi->CDF($val);
  if ($format == "raw") {
    echo "$key\t$count_profile[$key]\t$article_profile[$key]\t$raw_score\t$chi_score\n";
  } else {
    echo "<tr><td> $key</td><td> $count_profile[$key] </td><td> $article_profile[$key] </td><td> $raw_score </td> <td> $chi_score </td> </tr>";
  }
}

echo "</table>";


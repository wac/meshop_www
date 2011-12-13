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
$gene = $_GET['gene'];
$term = $_GET['term'];
$sortid = $_GET['sortid'];

$db = $_GET['db'];
$db = mysql_real_escape_string($db);
//build query

$limit = $_GET['limit'];
$limit = mysql_real_escape_string($limit);

$sortid = $_GET['sortid'];
$sortid = mysql_real_escape_string($sortid);

// Check for multiple genes, if so, offer selection and return

$gene_info = get_gene_info($gene);

if ($gene_info == FALSE) {
  if(is_numeric($gene)) {
    echo "No human gene with ID $gene found.";
    return;
  }

  suggest_gene($gene, 20);
  return;
}

echo "<b>Gene:</b> $gene_info[locus] (Gene ID: $gene_info[gene_id], ";

$query = "SELECT term, term_refs, gene_refs FROM gene,nr_hum_gene2pubmed_gene_mesh_p WHERE gene.gene_id=nr_hum_gene2pubmed_gene_mesh_p.gene_id AND ";
if(is_numeric($gene)) {
  $query .= "gene.gene_id=$gene ";
} else if ($gene != "") {
  $gene = mysql_real_escape_string($gene);// Escape User Input to help prevent SQL Injection
  $query .= "locus='$gene' ";
} 

//Execute query
$qry_result = mysql_query($query) or die(mysql_error());

echo mysql_num_rows($qry_result) . " gene MeSH terms, ";

// Build gene profile
$gene_profile = array();
$gene_refs = -1;

while($row = mysql_fetch_array($qry_result)){
    $gene_profile[ $row[term] ] = $row[term_refs];
    $gene_refs = $row[gene_refs];
}

echo $gene_refs . " PubMed refs)<br> "; 

if (($term == "") || (is_mesh($term, 'C') == FALSE)) {
      suggest_mesh($term, 20, 'C', 'term');
      return;
}
echo "<b>MeSH term:</b> $term (";

$query = "SELECT term, term_refs, disease_refs FROM disease_comesh_p WHERE disease=\"$term\"";

//Execute query
$qry_result = mysql_query($query) or die(mysql_error());

echo  mysql_num_rows($qry_result) . " disease MeSH results, ";

// Build disease profile
$mesh_profile = array();
$mesh_refs = -1;

while($row = mysql_fetch_array($qry_result)){
    $mesh_profile[ $row[term] ] = $row[term_refs];
    $mesh_refs = $row[disease_refs];
}

echo $mesh_refs . " PubMed refs)<br> "; 

if (count($mesh_profile) < count($gene_profile)) {
  $key_profile = $mesh_profile;
} else {
  $key_profile = $gene_profile;
}

$term_scores= array();

foreach ($key_profile as $key => $val) {
  $term_scores[$key] = abs(((float) $gene_profile[$key]/$gene_refs) - ((float) $mesh_profile[$key]/$mesh_refs));
}

// Compare
echo "<br>" . count($term_scores) .  " Common MeSH Term Scores <br>";

asort($term_scores);

echo  "<table class=\"result-table\">";
echo "<tr><th>Common MeSH Term</th><th>Gene Term Refs</th><th>Disease Term Refs</th><th>Score</th></tr>";

foreach ($term_scores as $key => $val) {
  $float_score = sprintf("%.2e", $val);
  echo "<tr><td> $key</td> <td>$gene_profile[$key]</td> <td>$mesh_profile[$key]</td><td> $float_score </td></tr>";
}

echo "</table>";

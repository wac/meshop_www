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

$db = $_GET['db'];
$db = mysql_real_escape_string($db);
//build query

$limit = $_GET['limit'];
$limit = mysql_real_escape_string($limit);

$sortid = $_GET['sortid'];
$sortid = mysql_real_escape_string($sortid);

$show_gene=true;
$show_disease=true;

$show_articles=false;
if ($_GET['showArticles'] == "Y") {
   $show_articles=true;
}

// Check for multiple genes, if so, offer selection and return
if ($gene != "") {

$gene_info=get_gene_info($gene);
if ($gene_info == FALSE) {
  if(is_numeric($gene)) {
    echo "No human gene with ID $gene found.";
    return;
  }

  suggest_gene($gene, 20);
  return;
}

echo "<b>Gene:</b> $gene_info[locus] (Gene ID: $gene_info[gene_id])<br>";

$gene = $gene_info[gene_id];

$show_gene=false;
}

if ($term != "") {
//   if (is_mesh($term, 'C') == FALSE) {
   if (is_mesh($term, '') == FALSE) {
      suggest_mesh($term, 20, 'C', 'term');
      return;
   }
   echo "<b>MeSH term:</b> $term<br>";
   $show_disease=false;
}

if ($show_gene==true && $show_disease==true) {
   echo "Please type in a disease or a gene to lookup.";
   return;
}

$show_score=true;

if ((substr($sortid, 0, 5) != "score") && (substr($sortid, 0, 5) != "p_val")) {
  $show_score = false;
}

if (!$show_score && !$show_gene && !$show_disease ) {
   // Nothing to do
   return;
}

$wordle_string="";
$show_wordle=false;
if (($show_gene || $show_disease) && $show_score) {
   $show_wordle=true;
}


$query = "SELECT ";
if ($show_gene) {
   $query .= "gene.gene_id,gene.locus ";
}
if ($show_disease) {
   if ($show_gene) {
      $query .=",";
   }
   $query .= "$db.term ";
}

$order = $_GET['order'];
$order = mysql_real_escape_string($order);

if ($show_score) {
  if ($show_gene || $show_disease) {
     $query .=","; 
  }
  $query .= "$sortid";
}

if ($show_articles) {
  $query .= ", term_refs";
}

$query .= " FROM $db, gene WHERE gene.gene_id=$db.gene_id AND ";

if(is_numeric($gene)) {
  $query .= "gene.gene_id=$gene ";
} else if ($gene != "") {
  $gene = mysql_real_escape_string($gene);// Escape User Input to help prevent SQL Injection
  $query .= "locus='$gene' ";
} 


if ($term) {
  $term = mysql_real_escape_string($term);
  if ($gene != "") {
     $query .= "AND ";
  }
  $query .= " term=\"$term\"";
}

if ($sortid) {
   if ($show_articles) {
      $query .= " ORDER BY $sortid $order, term_refs DESC";
   } else {
      $query .= " ORDER BY $sortid $order";
   }
}

$query .= " LIMIT $limit";

//Execute query
$qry_result = mysql_query($query) or die(mysql_error());

echo "<br>Returning " . mysql_num_rows($qry_result) . " results";

//Build Result String
$display_string = "<table class=\"result-table\">";
$display_string .= "<tr>";
if ($show_gene) {
   $display_string .= "<th>Gene ID</th>";
   $display_string .= "<th>Locus</th>";
}
if ($show_disease) {
   $display_string .= "<th>MeSH Term</th>";
}
if ($show_score) {
  $display_string .= "<th>Score</th>";
}
if ($show_articles) {
  $display_string .= "<th>Articles</th>";
}

$display_string .= "</tr>";

// Insert a new row in the table for each person returned
while($row = mysql_fetch_array($qry_result)){
  $display_string .= "<tr>";
  if ($show_gene) {
     $display_string .= "<td><a href=\"http://view.ncbi.nlm.nih.gov/gene/$row[gene_id]\" target=\"_blank\">$row[gene_id]</a></td>";
     $display_string .= "<td><a href=\"http://view.ncbi.nlm.nih.gov/gene/$row[gene_id]\" target=\"_blank\">$row[locus]</a></td>";

     if ($show_wordle) {
     	$wordle_string .= $row[locus] . ":";
     }
     $gene = $row[gene_id];
  }
  if ($show_disease) {
     $display_string .= "<td>$row[term]</td>";

     if ($show_wordle) {
     	$wordle_string .= $row[term] . ":";
     }
     $term = $row[term];
  }
  if ($show_score) {
     $float_score = sprintf("%.2e", $row[$sortid]);
     $display_string .= "<td>$float_score</td>";

     if ($show_wordle) {
     	if (($sortid != "score3" ) && ($sortid != "score4")) {
	  if ($row[$sortid] == 0) {
	     $wordle_string .= "30\n";
	  } else {
	    $wordle_string .= -log($row[$sortid],10) . "\n";
	  }
	} else {
     	  $wordle_string .= $row[$sortid] . "\n";
     	}
     }
  }
  if ($show_articles) {
     $display_string .="<td align='right'><a href='#' onclick='articleClick( $gene ,\"$term\")'>$row[term_refs]</a></td>";
  }
  $display_string .= "</tr>";  
}

$display_string .= "</table>";

echo $display_string;

// Wordle output - copy to clipboard?
if ($show_wordle) {
  echo "<h2>Wordle Cloud Format</h2>";
  echo '<form action="http://www.wordle.net/compose" method="post"><fieldset>';
//  echo "<input type=\"button\" onclick=\"clipboardTextarea(document.getElementById('wordle-text'));\" value=\"Copy to Clipboard\" />";
  echo "<textarea rows='5' cols='80' id='wordcounts' name='wordcounts' onclick='this.select()' readonly>" . $wordle_string . "</textarea>";
  echo "<p>Copy (click above and type CTRL-C) and paste (CTRL-V) into <a href='http://www.wordle.net/advanced' target=\"_blank\">Wordle Advanced</a> to generate a tag cloud. Word cloud weights are the negative log of p-values, with terms with zero p-value assigned weight 30.</p>";
  echo '<input type="submit" class="submit" value="Load Wordle Java Applet"/></fieldset></form>';

}

echo "<div class=\"query-box\"><b>SQL Query:</b> " . $query . "<br /> </div>";

?>

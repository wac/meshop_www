<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include("db_setup.php");

include_once("suggest_name.php");
include_once("suggest_mesh.php");

// TODO add a sortid2 that parses sortid and lets you sort by the extra cols if it's score12
// do the computation of the score_given_lit inside MySQL - probably need to pull the chem_term_score join
// in if we are doing a score12 compute


//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
//Select Database
mysql_select_db($dbname) or die(mysql_error());
// Retrieve data from Query String
$chem = $_GET['chem'];
$term = $_GET['term'];

$db = $_GET['db'];
$db = mysql_real_escape_string($db);
//build query

$limit = $_GET['limit'];
$limit = mysql_real_escape_string($limit);

$sortid = $_GET['sortid'];
$sortid = mysql_real_escape_string($sortid);

$show_chem=true;
$show_disease=true;

$show_articles=false;
if ($_GET['showArticles'] == "Y") {
   $show_articles=true;
}

$chem_term_p = 0.0;
$disease_term_p = 0.0;


// Check for multiple drugs, if so, offer selection and return
if ($chem != "") {

 if (!is_name($chem, $db, "chem")) {
      suggest_name($chem, 20, $db, 'chem', 'drug', 'chem');
      return;
 }

 $query = "SELECT p_int FROM chem_term_p WHERE chem='$chem'";
 $qry_result = mysql_query($query) or die(mysql_error());
 $row = mysql_fetch_array($qry_result);


 $chem_term_p = (1.01 - ($row[p_int]/100.0));
 echo "<b>Drug/Chemical:</b><a href='http://www.ncbi.nlm.nih.gov/pubmed?term=$chem" . "[nm]' target='_blank'>$chem</a><br><b>Literature p &#8804</b> " . $chem_term_p . "<br>"; 

 $show_chem=false;
}

if ($term != "") {
//   if (is_mesh($term, 'C') == FALSE) {
   if (is_mesh($term, '') == FALSE) {
      suggest_mesh($term, 20, 'C', 'term');
      return;
   }


   $query = "SELECT p_int FROM disease_term_p WHERE disease='$term'";
   $qry_result = mysql_query($query) or die(mysql_error());
   $row = mysql_fetch_array($qry_result);

   $disease_term_p =  (1.01 - ($row[p_int]/100.0));
   echo "<b>MeSH term:</b><a href='http://www.ncbi.nlm.nih.gov/pubmed?term=$term" . "[mh]' target='_blank'>$term</a><br><b>Literature p &#8804</b> "  . $disease_term_p . "<br>"; 
   $show_disease=false;
}

if ($show_chem==true && $show_disease==true) {
   echo "Please type in a disease or a drug to lookup.";
   return;
}

$show_score=true;

if ((substr($sortid, 0, 5) != "score") && (substr($sortid, 0, 5) != "p_val")) {
  $show_score = false;
}

if (!$show_score && !$show_chem && !$show_disease ) {
   // Nothing to do
   return;
}

$wordle_string="";
$show_wordle=false;
if (($show_chem || $show_disease) && $show_score) {
   $show_wordle=true;
}


$query = "SELECT ";
if ($show_chem) {
   $query .= "$db.chem, 1.01 - (chem_term_p.p_int/100.0) AS chem_pscore";
}
if ($show_disease) {
   if ($show_chem) {
      $query .=",";
   }
   $query .= "$db.disease, 1.01 - (disease_term_p.p_int/100.0) AS disease_pscore";
}

$order = $_GET['order'];
$order = mysql_real_escape_string($order);

if ($show_score) {
  if ($show_chem || $show_disease) {
     $query .=","; 
  }
  $query .= "$db.$sortid";
  if ($sortid == "score12") {
     $query .= ", (disease_chem_score_p.score12_p_int/100.0) AS score_p_int, pscore";
  }
}

if ($show_articles) {
  $query .= ", term_refs";
}

$query .= " FROM $db, disease_term_p, chem_term_p";
if ($show_score && $sortid == "score12") {
    $query .= ", disease_chem_score_p, disease_pharma_chem_litp_score_table";
}
$query .= " WHERE $db.chem=chem_term_p.chem AND $db.disease=disease_term_p.disease AND ";
if ($show_score && $sortid == "score12") {
   $query .= "disease_chem_score_p.chem=$db.chem AND disease_chem_score_p.disease=$db.disease AND ";
   $query .= "disease_pharma_chem_litp_score_table.chem_p_int=chem_term_p.p_int AND disease_pharma_chem_litp_score_table.disease_p_int=disease_term_p.p_int AND disease_pharma_chem_litp_score_table.score_p_int=disease_chem_score_p.score12_p_int AND ";
}

if ($chem != "") {
  $chem = mysql_real_escape_string($chem);// Escape User Input to help prevent SQL Injection
  $query .= "$db.chem='$chem' ";
} 


if ($term) {
  $term = mysql_real_escape_string($term);
  if ($chem != "") {
     $query .= "AND ";
  }
  $query .= " $db.disease=\"$term\"";
}

if ($sortid) {
   if ($show_articles) {
      $query .= " ORDER BY $db.$sortid $order, term_refs DESC";
   } else {
      $query .= " ORDER BY $db.$sortid $order";
   }
}

$query .= " LIMIT $limit";

//Execute query
$qry_result = mysql_query($query) or die(mysql_error());

echo "<br>Returning " . mysql_num_rows($qry_result) . " results";

//Build Result String
$display_string = "<table class=\"result-table\">";
$display_string .= "<tr>";
if ($show_chem) {
   $display_string .= "<th>Drug/Chem.</th>";
   $display_string .= "<th>Drug/Chem. Lit. p &#8804</th>";
}
if ($show_disease) {
   $display_string .= "<th>MeSH Term</th>";
   $display_string .= "<th>MeSH Term Lit. p &#8804</th>";
}
if ($show_score) {
  $display_string .= "<th>Score</th>";
  if ($sortid == "score12") {
    $display_string .= "<th>Score p &#8804</th><th>Score (Lit p Corrected) &#8804</th>";
  }
}
if ($show_articles) {
  $display_string .= "<th>Articles</th>";
}

$display_string .= "</tr>";

// Insert a new row in the table for each person returned
while($row = mysql_fetch_array($qry_result)){
  $display_string .= "<tr>";
  if ($show_chem) {
     $display_string .= "<td><a href=\"http://www.ncbi.nlm.nih.gov/pubmed?term=$row[chem]" . "[nm]\" target=\"_blank\">$row[chem]</a></td>";
     $chem_term_p = $row[chem_pscore];
     $display_string .= "<td>$chem_term_p</td>";

     if ($show_wordle) {
     	$wordle_string .= $row[chem] . ":";
     }
     $chem = $row[chem];
  }
  if ($show_disease) {
     $display_string .= "<td><a href='http://www.ncbi.nlm.nih.gov/mesh?term=$row[disease]' target='_blank'>$row[disease]</a></td>";
     $disease_term_p = $row[disease_pscore];
     $display_string .= sprintf("<td>%.2f</td>", $disease_term_p);
     if ($show_wordle) {
     	$wordle_string .= $row[disease] . ":";
     }
     $term = $row[disease];
  }
  if ($show_score) {
     $lit_score = $disease_term_p * $chem_term_p;

//     $display_string .= "<td>$lit_score</td>";     

//     $float_score = sprintf("%.2e", $row[$sortid]);
     $display_string .= sprintf("<td>%.2e</td>", $row[$sortid]);     

     if ($sortid ==  "score12") {
//       $display_string .= "<td>$row[score_p_int]</td><td>$row[pscore]</td>";
       $display_string .= sprintf("<td>%.2f</td><td>%.2f</td>", $row[score_p_int], $row[pscore]);
//       $score_given_lit_p = $row[pscore] / $lit_score;
//       $display_string .= "<td>$score_given_lit_p</td>";
     }
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
     $display_string .="<td align='right'><a href='#' onclick='articleClick( $chem ,\"$term\")'>$row[term_refs]</a></td>";
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

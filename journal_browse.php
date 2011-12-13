<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include("db_setup.php");
include_once("suggest_name.php");

//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
//Select Database
mysql_select_db($dbname) or die(mysql_error());
// Retrieve data from Query String
$journaltitle = $_GET['journaltitle'];

$term = $_GET['term'];

$db = $_GET['db'];
$db = mysql_real_escape_string($db);
//build query

$limit = $_GET['limit'];
$limit = mysql_real_escape_string($limit);

$sortid = $_GET['sortid'];
$sortid = mysql_real_escape_string($sortid);

$query = "SELECT journaltitle, $db.term, $db.p_val";

$show_score=true;

$show_journal=true;
$show_term=true;

if ($db == "CTD_validation" || $sortid == "gene_id" ) {
  $show_score = false;
}

$order = $_GET['order'];
$order = mysql_real_escape_string($order);

$journaltitle = mysql_real_escape_string($journaltitle);// Escape User Input to help prevent SQL Injection
$query .= ", term_refs FROM $db WHERE ";

if ($journaltitle) {
   $query .= "journaltitle='$journaltitle' ";
   $show_journal=false;
   if (!is_name($journaltitle, $db, "journaltitle")) {
      suggest_name($journaltitle, 20, $db, 'journaltitle', 'Journal', 'journaltitle');
      return;
   }
   echo "<b>Journal</b>: " . $journaltitle . "<br>";
}

if ($term) {
  $term = mysql_real_escape_string($term);
  if (!$show_journal) {
    $query .= "AND ";
  }
  $show_term=false;
  $query .= "term=\"$term\"";
  if (!is_mesh($term, '')) {
     suggest_mesh($term, 20, '', 'term');
     return;
  }
  echo "<b>Profile Keyword Term</b>: " . $term . "<br>";
}

if ($show_term && $show_journal) {
   echo "Please type in a journal title or a keyword to lookup.";
   return;
}


if ($sortid) {
  $query .= " ORDER BY $sortid $order, term_refs DESC";
}

$query .= " LIMIT $limit";

//Execute query
$qry_result = mysql_query($query) or die(mysql_error());

echo "<br>Returning " . mysql_num_rows($qry_result) . " results";

$wordle_string="";
$show_wordle=false;

if (($show_term || $show_journal) && $show_score) {
   $show_wordle=true;
}


//Build Result String
$display_string = "<table class='result-table'>";
$display_string .= "<tr>";

if ($show_journal) {
   $display_string .= "<th>Journal</th>";
}

if ($show_term) {
$display_string .= "<th>MeSH Term</th>";
}

$display_string .= "<th>Score</th><th>Articles</th>";

$display_string .= "</tr>";

// Insert a new row in the table for each person returned
while($row = mysql_fetch_array($qry_result)){
  $display_string .= "<tr>";
  if ($show_journal) {
    $display_string .= "<td>$row[journaltitle]</td>";
    $journaltitle = $row[journaltitle];
    if ($show_wordle) {
       $wordle_string .= $row[journaltitle] . ":";
    }
  }
  if ($show_term) {
    $display_string .= "<td>$row[term]</td>";
    $term = $row[term];
    if ($show_wordle) {
       $wordle_string .= $row[term] . ":";
    }
  }
  $display_string .= "<td>$row[p_val]</td>";
  if ($show_wordle) {
     if ($row[p_val] == 0) {
     	$wordle_string .= "300\n";
     } else {
        $wordle_string .= -log($row[p_val],10) . "\n";
     }
  }

  $display_string .= "<td><a href='#' onclick='articleClick(\"$journaltitle\", \"$term\")'>$row[term_refs]</a></td>";

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
  echo "<p>Copy (click above and type CTRL-C) and paste (CTRL-V) into <a href='http://www.wordle.net/advanced' target=\"_blank\">Wordle Advanced</a> to generate a tag cloud.</p>";
  echo '<input type="submit" class="submit" value="Load Wordle Java Applet"/></fieldset></form>';
}


echo "<div class='query-box'><b>SQL Query:</b> " . $query . "</div>";

?>

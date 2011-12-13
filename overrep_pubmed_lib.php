<?php
require_once 'PDL/HyperGeometricDistribution.php';
require_once 'PDL/ChiSqrDistribution.php';


include_once("suggest_gene.php");
include_once("suggest_mesh.php");

function overrep_pubmed($pubmedlist, $db, $limit) {

include("db_setup.php");

//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
//Select Database
mysql_select_db($dbname) or die(mysql_error());

$limit = mysql_real_escape_string($limit);
$db = mysql_real_escape_string($db);

$show_wordle = True;

$i = 0;
$j = 0;

$min_score=1;

$count_profile=array();
$score_profile=array();
$score_comment=array();

foreach ($pubmedlist as $pmid) {
  if ($pmid == '') {
    continue;
  }
  $pmid = mysql_real_escape_string($pmid);
  
  $query = "SELECT $db.mesh_parent FROM $db WHERE $db.pmid='$pmid'";

  //Execute query
  $qry_result = mysql_query($query) or die(mysql_error());

  if (mysql_num_rows($qry_result) < 1) {
          echo "No record for PMID $pmid<br>";
     	  continue;
  }

  $i++;

  echo "PMID $pmid - ";
  echo mysql_num_rows($qry_result);
  echo " terms processed<br>";

  while($row = mysql_fetch_array($qry_result)){
   $count_profile[$row[mesh_parent]]=$count_profile[$row[mesh_parent]]+1;
   $j++;
  }
  flush();
}

echo "<hr>Processed $i PMIDs<br>";
echo "$j PubMed MeSH terms processed<br>";

$num_terms = sizeof($count_profile);

echo "$num_terms MeSH Terms<hr>";


// Sort?

// Output


foreach ($count_profile as $key => $val) {
  $query = "SELECT background_refs, pubmed_non_refs, term_refs FROM disease_comesh_p WHERE term=\"$key\" LIMIT 1;";

  //Execute query
  $qry_result = mysql_query($query) or die(mysql_error());

  if (mysql_num_rows($qry_result) < 1) {
          echo "No record for term $key ($val refs)<br>";
	  $score_profile[$key]=1.0;
          continue;
  }
  $row = mysql_fetch_array($qry_result);

  $m=$row[background_refs];  // white marbles in urn (successes)
  $n=$row[pubmed_non_refs]+$row[term_refs];  // black marbles in urn
  $k=$i; // number of draws
//  echo "m=$m,n=$n,k=$k";
// Probability of x > count
//  $fisher = new HyperGeometricDistribution($m, $n, $k);
//  $fisher_score = 1.0-($fisher->CDF($val));

// Reverse the Fisher Test - # of black marbles drawns <= count
  $fisher = new HyperGeometricDistribution($n, $m, $k);
  $fisher_score = $fisher->CDF($i-$val);
  if ($fisher_score <= 0) {
     $fisher_score = exec("echo 'phyper($i-$val, $n, $m, $k)' | R --vanilla --slave | cut -f 2 -d ' '"); 
     $score_comment[$key] = "R statistics";
  } else {
     $score_comment[$key] = "PDL";
  }

//  $score_comment[$key] = "phyper($i-$val, $n, $m, $k) = $fisher_score";
//  echo "$key: [Fisher] phyper($i-$val, $n, $m, $k) = $fisher_score<br>";
//  if ($fisher_score < 0) {
//     $fisher_score = 0;
//  }

  // TEST
  //$fisher_score=acos(8);

  if (is_nan($fisher_score)) {

//    $a = $val;  // white pulled out
//    $b = $m-$val; // rest of the white
//    $c = $i-$val; // blacks pulled out
//    $d = $n-$c; // rest of the blacks
//    $rawscore=1.0*(($a*$d)-($b*$c));
//    $rawscore=$rawscore/($a+$b);
//    $rawscore=$rawscore/($c+$d);
//    $rawscore=$rawscore*(($a*$d)-($b*$c))/(($b+$d)*($a+$c));
//    $rawscore=$rawscore*($a+$b+$c+$d);
//    $chi = new ChiSqrDistribution(1);
//    $score_profile[$key]=1.0-$chi->CDF($rawscore);
//    echo "=> [Chi] ($rawscore) $score_profile[$key]<br>";
    $score_profile[$key]=1.0;
  } else {
    $score_profile[$key]=$fisher_score;
  }
  
  if (($score_profile[$key] > 0) and ($score_profile[$key] < $min_score)) {
     $min_score = $score_profile[$key];
  }
}

array_multisort($score_profile, SORT_ASC, SORT_NUMERIC, $count_profile, SORT_DESC, SORT_NUMERIC);

echo  "<table class=\"result-table\">";
echo "<tr><th>MeSH Term</th><th>Articles</th><th>p-value</th><th>R code</th></tr>";

$logzero = -log($min_score, 10) * 1.2;


foreach ($score_profile as $key => $val) {

  echo "<tr><td> $key</td><td> $count_profile[$key] </td><td>$score_profile[$key]</td><td>$score_comment[$key]</td<</tr>";

  if ($show_wordle) {
     $wordle_string .= $key . ":";
     if ($score_profile[$key] == 0) {
     	$wordle_string .= "30\n";
     } else {
        $wordle_string .= -log($score_profile[$key],10) . "\n";
     }
  }
}

echo "</table>";

// Wordle output - copy to clipboard?
if ($show_wordle) {
  echo "<h2>Wordle Cloud Format</h2>";
  echo '<form action="http://www.wordle.net/compose" method="post"><fieldset>';
//  echo "<input type=\"button\" onclick=\"clipboardTextarea(document.getElementById('wordle-text'));\" value=\"Copy to Clipboard\" />";
  echo "<textarea rows='5' cols='80' id='wordcounts' name='wordcounts' onclick='this.select()' readonly>" . $wordle_string . "</textarea>";
  echo "<p>Copy (click above and type CTRL-C) and paste (CTRL-V) into <a href='http://www.wordle.net/advanced' target=\"_blank\">Wordle Advanced</a> to generate a tag cloud. Word cloud weights are the negative log of p-values, with terms with zero p-value assigned weight 30.</p>";
  echo '<input type="submit" class="submit" value="Load Wordle Java Applet"/></fieldset></form>';
}

}
?>
<?php

function is_mesh($term, $tree_filter) {
  $query = "SELECT DISTINCT term from mesh_tree WHERE ";
  if ($tree_filter != "") {
     // Filter by a tree i.e. C% == Disease
     $query .= " tree_num LIKE \"$tree_filter%\" AND ";
  }
  $query .= "term=\"$term\"";
  $qry_result = mysql_query($query) or die(mysql_error());
  if (mysql_num_rows($qry_result) != 1) {
     return FALSE;
  }
  return mysql_fetch_array($qry_result);
}

function suggest_mesh($term, $qlimit, $tree_filter, $id){
  $query = "SELECT DISTINCT term from mesh_tree WHERE ";
  if ($tree_filter != "") {
     // Filter by a tree i.e. C% == Disease
     $query .= " tree_num LIKE \"$tree_filter%\" AND ";
  }
  $query .= "term LIKE \"$term%\" LIMIT $qlimit";
  $qry_result = mysql_query($query) or die(mysql_error());
  $num_results=mysql_num_rows($qry_result);
  if ($num_results == 0) {
    echo "No MeSH term similar to '$term'";
    return;
  } if (($num_results == 1) && is_mesh($term,$tree_filter)) {
    return;
  }

  echo "Did you mean the MeSH term:<br>";
  while($row = mysql_fetch_array($qry_result)){
//      $rowtxt = "<a href=\"javascript:document.browseForm.term.value='$row[term]'; void(0);\">$row[term]</a><br>";
      $rowtxt = "<a href=\"javascript:document.getElementById('" . $id . "').value='$row[term]'; void(0);\">$row[term]</a><br>";
      echo $rowtxt;
  }
}
?>
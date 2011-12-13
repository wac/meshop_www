<?php

function is_name($name, $db, $fieldname) {
  $query = "SELECT $fieldname from $db WHERE ";
  $query .= "$fieldname=\"$name\" LIMIT 1";
  $qry_result = mysql_query($query) or die(mysql_error());
  if (mysql_num_rows($qry_result) != 1) {
     return FALSE;
  }
  return mysql_fetch_array($qry_result);
}

function suggest_name($name, $qlimit, $db, $fieldname, $fieldtxt, $id){
  $query = "SELECT DISTINCT $fieldname from $db WHERE ";
  $query .= "$fieldname LIKE \"$name%\" LIMIT $qlimit";
  $qry_result = mysql_query($query) or die(mysql_error());
  $num_results=mysql_num_rows($qry_result);
  if ($num_results == 0) {
    echo "No $fieldtxt similar to '$term'";
    return;
  } if (($num_results == 1) && is_name($name,$db, $fieldname)) {
    return;
  }

  echo "Did you mean the $fieldtxt:<br>";
  while($row = mysql_fetch_array($qry_result)){
      $rowtxt = "<a href=\"javascript:document.getElementById('" . $id . "').value='" . $row[$fieldname] . "'; void(0);\">" . $row[$fieldname] . "</a><br>";
      echo $rowtxt;
  }
}
?>

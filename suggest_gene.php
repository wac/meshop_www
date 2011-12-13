<?php

function get_gene_info($gene){
$query = "SELECT gene_id, locus from gene WHERE taxon_id=9606 AND ";
if(is_numeric($gene)) {
    $query .= "gene_id=$gene";
} else {
    $query .= "locus='$gene'";
}
$qry_result = mysql_query($query) or die(mysql_error());

if (mysql_num_rows($qry_result) != 1) {
   return FALSE;
}
return mysql_fetch_array($qry_result);
}

function suggest_gene($gene, $qlimit){
  $query = "SELECT gene_id, locus from gene WHERE taxon_id=9606 AND ";
  $query .= "locus LIKE \"$gene%\" LIMIT $qlimit";
  $qry_result = mysql_query($query) or die(mysql_error());
  $num_results=mysql_num_rows($qry_result);
  if ($num_results == 0) {
    echo "No human genes similar to your query '$gene'";
    return;
  } else if (($num_results == 1) && (get_gene_info($gene) != FALSE)) {
    return;
  }

  echo "Did you mean the gene:<br>";
  while($row = mysql_fetch_array($qry_result)){
      $rowtxt = "<a href=\"javascript:document.browseForm.gene.value='$row[locus]';void(0) \">$row[locus]</a><br>";
      echo $rowtxt;
  }
}
?>
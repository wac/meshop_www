<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Content-type: text/plain");


// Retrieve data from Query String
$limit = $_GET['limit'];

$maptermlist = $_POST['mtermlist'];
$maptermlist = preg_split("/[\n\r,\\\\\|\-\/]+/", $maptermlist);

echo "# Mapping terms to MeSH\n";
echo "# term\tMeSH_term\n";

foreach ($maptermlist as $term) {
  $term = trim($term);
  if ($term == '') {
    continue;
  }

  $query = 'http://www.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=mesh&term=' . urlencode($term);
  $exml = simplexml_load_file($query);

  $idlist = '';
  foreach ($exml->xpath('//Id') as $id) {
  	  if ($idlist == '') {
	     $idlist = $id;
	  } else {
	     $idlist = $idlist . ',' . $id;
	  }
  }

  $resultlist = file_get_contents("http://www.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=mesh&mode=text&report=brief&id=" . $idlist);
  $resultlist = preg_split("/[\n\r]+/", $resultlist);

  foreach ($resultlist as $result) {
      $result = trim($result);
      if ($result == '') {
      	 continue;
      }
      $result = preg_replace('/(\d)+:/','',$result);
      echo $term . "\t" . $result . "\n";
  }
}

?>
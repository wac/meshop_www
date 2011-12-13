#SQL_CMD="mysql-dbrc warrendb"
SQL_CMD="mysql-dbrc digenei3"

echo -n "Start - "
date

cat drop-tables.sql | $SQL_CMD
cat tables.sql | $SQL_CMD

echo "Reload gene"
cd DATA;  echo "LOAD DATA LOCAL INFILE 'gene_info' INTO TABLE gene IGNORE 1 LINES (taxon_id, gene_id, locus);" | $SQL_CMD ; cd ..


echo "Reload CTD_validation"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'curr-CTD-validation-tuples.txt' INTO TABLE CTD_validation FIELDS TERMINATED by '|' IGNORE 1 LINES" | $SQL_CMD ; cd ..

echo "Reload new_gene2pubmed_hum_disease"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'new-gene2pubmed-hum-disease-validation-tuples-pred-p.txt' INTO TABLE new_gene2pubmed_hum_disease FIELDS TERMINATED by '|' IGNORE 1 LINES" | $SQL_CMD ; cd ..

echo "Reload hum_disease_gene2pubmed_profiles"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'hum-disease-gene2pubmed-profiles.txt' INTO TABLE hum_disease_gene2pubmed_profiles FIELDS TERMINATED by '|' IGNORE 1 LINES" | $SQL_CMD ; cd ..

echo "Reload nr-hum-gene2pubmed-gene-mesh-p"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'nr-hum-gene2pubmed-gene-mesh-p.txt' INTO TABLE nr_hum_gene2pubmed_gene_mesh_p FIELDS TERMINATED by '|' " | $SQL_CMD ; cd ..

echo "Reload nr_gene2pubmedBG_jaspar-gene2pubmed-gene-mesh-p"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'nr-gene2pubmedBG-jaspar-gene2pubmed-gene-mesh-p.txt' INTO TABLE nr_gene2pubmedBG_jaspar_gene2pubmed_gene_mesh_p FIELDS TERMINATED by '|' " | $SQL_CMD ; cd ..


echo "Reload hum-gene2pubmed-gene-mesh-p"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'hum-gene2pubmed-gene-mesh-p.txt' INTO TABLE hum_gene2pubmed_gene_mesh_p FIELDS TERMINATED by '|' " | $SQL_CMD ; cd ..

echo "Reload disease-comesh-p"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'disease-comesh-p.txt' INTO TABLE disease_comesh_p FIELDS TERMINATED by '|' " | $SQL_CMD ; cd ..

echo "Reload nr-gene2pubmedBG-hum-gene2pubmed-gene-mesh-p"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'nr-gene2pubmedBG-hum-gene2pubmed-gene-mesh-p.txt' INTO TABLE nr_gene2pubmedBG_hum_gene2pubmed_gene_mesh_p FIELDS TERMINATED by '|' " | $SQL_CMD ; cd ..

echo "Reload gene2pubmedBG-hum-gene2pubmed-gene-mesh-p"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'gene2pubmedBG-hum-gene2pubmed-gene-mesh-p.txt' INTO TABLE gene2pubmedBG_hum_gene2pubmed_gene_mesh_p FIELDS TERMINATED by '|' " | $SQL_CMD ; cd ..

echo "Reload nr-disease-comesh-p"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'nr-disease-comesh-p.txt' INTO TABLE nr_disease_comesh_p FIELDS TERMINATED by '|' " | $SQL_CMD ; cd ..

echo "Reload nr-diseaseBG-disease-comesh-p"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'nr-diseaseBG-disease-comesh-p.txt' INTO TABLE nr_diseaseBG_disease_comesh_p FIELDS TERMINATED by '|' " | $SQL_CMD ; cd ..

echo "Reload mesh-tree"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'mesh_tree.txt' INTO TABLE mesh_tree FIELDS TERMINATED BY '|' (term, tree_num);" | $SQL_CMD ; cd ..

echo "Reload gene2pubmed"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'gene2pubmed' INTO TABLE gene2pubmed IGNORE 1 lines (@dummy, gene_id, pmid);" | $SQL_CMD ; cd ..

echo "Reload generif"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'parsed_basic_rif.txt' INTO TABLE generif IGNORE 1 lines (gene_id, pmid, description);" | $SQL_CMD ; cd ..

echo "Load pubmed_mesh_parent"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'mesh-parent.txt' INTO TABLE pubmed_mesh_parent FIELDS TERMINATED BY '|' (mesh_parent, pmid)" | $SQL_CMD ; cd ..

echo "Load pubmed"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'pubmed-titles' INTO TABLE pubmed FIELDS TERMINATED by '|'" | $SQL_CMD ; cd ..

echo "Load mesh child"
cd DATA; echo "LOAD DATA LOCAL INFILE 'mesh-child.txt' INTO TABLE mesh_child FIELDS TERMINATED BY '|'" | $SQL_CMD ; cd .. 

echo "Load all chem mesh p"
cd DATA; echo "LOAD DATA LOCAL INFILE 'all-chem-mesh-p.txt' INTO TABLE all_chem_mesh_p FIELDS TERMINATED BY '|'" | $SQL_CMD ; cd ..

echo "Load all pubmed-chem"
cd DATA; echo "LOAD DATA LOCAL INFILE 'pubmed-chem.txt' INTO TABLE pubmed_chem FIELDS TERMINATED BY '|'" | $SQL_CMD ; cd ..

echo "Load journal-mesh-p"
cd DATA; echo "LOAD DATA LOCAL INFILE 'journal-mesh-p.txt' INTO TABLE journal_mesh_p FIELDS TERMINATED BY '|'" | $SQL_CMD ; cd ..

echo "Load nr-journal-mesh-p"
cd DATA; echo "LOAD DATA LOCAL INFILE 'nr-journal-mesh-p.txt' INTO TABLE nr_journal_mesh_p FIELDS TERMINATED BY '|'" | $SQL_CMD ; cd ..

echo "Load journal-min2005-mesh-p"
cd DATA; echo "LOAD DATA LOCAL INFILE 'journal-min2005-mesh-p.txt' INTO TABLE journal_min2005_mesh_p FIELDS TERMINATED BY '|'" | $SQL_CMD ; cd ..

echo "Load nr-min2005-journal-mesh-p"
cd DATA; echo "LOAD DATA LOCAL INFILE 'nr-journal-min2005-mesh-p.txt' INTO TABLE nr_journal_min2005_mesh_p FIELDS TERMINATED BY '|'" | $SQL_CMD ; cd ..

echo "Reload disease_pharma_chem_profiles"
cd DATA ; echo "LOAD DATA LOCAL INFILE 'disease-pharma-chem-profiles.txt' INTO TABLE disease_pharma_chem_profiles FIELDS TERMINATED by '|' IGNORE 1 LINES" | $SQL_CMD ; cd ..

echo "Load nr_gene2pubmedBG_jaspar_gene2pubmed_gene_mesh_p"
cd DATA; echo "LOAD DATA LOCAL INFILE 'nr-gene2pubmedBG-jaspar-gene2pubmed-gene-mesh-p.txt' INTO TABLE nr_gene2pubmedBG_jaspar_gene2pubmed_gene_mesh_p  FIELDS TERMINATED BY '|'" | $SQL_CMD ; cd ..

sh load-disease-chem-tables.sh

echo -n "Done load-tables.sh - "
date


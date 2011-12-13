#SQL_CMD="mysql-dbrc warrendb"
SQL_CMD="mysql-dbrc digenei3"

echo -n "Start - "
date

echo "DROP TABLE disease_pharma_chem_pscores" | $SQL_CMD

#cat drop-tables.sql | $SQL_CMD
cat tables.sql | $SQL_CMD

echo "Load disease_pharma_chem_pscores"
cd DATA; echo "LOAD DATA LOCAL INFILE 'disease-pharma-score12-chem-pscores.txt' INTO TABLE disease_pharma_chem_pscores  FIELDS TERMINATED BY '|'" | $SQL_CMD ; cd ..

cat disease_chem_score.sql | $SQL_CMD

echo "Load disease_pharma_chem_litp_score_table"
cd DATA; echo "LOAD DATA LOCAL INFILE 'disease-pharma-chem-litp-score12-table.txt' INTO TABLE disease_pharma_chem_litp_score_table FIELDS TERMINATED BY '|'" | $SQL_CMD; cd ..

echo -n "Done - "
date


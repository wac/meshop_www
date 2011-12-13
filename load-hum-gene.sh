SQL_CMD="mysql-dbrc digenei3" 

date

echo "all chem mesh p"
cd DATA; echo "LOAD DATA LOCAL INFILE 'all-chem-mesh-p.txt' INTO TABLE all_chem_mesh_p FIELDS TERMINATED BY '|'" | $SQL_CMD ; cd ..
date

date
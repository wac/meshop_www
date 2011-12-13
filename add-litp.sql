--ALTER TABLE disease_pharma_chem_profiles ADD COLUMN lit_p float;
CREATE TEMPORARY TABLE dpcp AS SELECT chem, disease, score3, score4, score6 FROM disease_pharma_chem_profiles;
ALTER TABLE dpcp ADD COLUMN lit_p float;

UPDATE dpcp SET lit_p = (SELECT COUNT(*) FROM disease_pharma_chem_profiles WHERE dpcp.score3 < disease_pharma_chem_profiles.score3 AND dpcp.score4 < disease_pharma_chem_profiles.score4);

UPDATE disease_pharma_chem_profiles SET lit_p = (SELECT lit_p FROM dpcp WHERE dpcp.chem = disease_pharma_chem_profiles.chem AND dpcp.disease=disease_pharma_chem_profiles.disease)

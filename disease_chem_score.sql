DROP TABLE IF EXISTS chem_term_p;
CREATE TABLE chem_term_p AS SELECT DISTINCT chem FROM disease_pharma_chem_profiles;
ALTER TABLE chem_term_p ADD PRIMARY KEY (chem);
ALTER TABLE chem_term_p ADD COLUMN chem_terms int;
UPDATE chem_term_p SET chem_terms = (SELECT chem_terms from disease_pharma_chem_profiles WHERE disease_pharma_chem_profiles.chem=chem_term_p.chem LIMIT 1);
ALTER TABLE chem_term_p ADD COLUMN p_int int;
UPDATE chem_term_p SET p_int = ( SELECT MIN(p_int) FROM disease_pharma_chem_pscores WHERE chem_term_p.chem_terms < disease_pharma_chem_pscores .chem_terms);
UPDATE chem_term_p SET p_int = 100 WHERE p_int IS NULL;

DROP TABLE IF EXISTS disease_term_p;
CREATE TABLE disease_term_p AS SELECT DISTINCT disease FROM disease_pharma_chem_profiles;
ALTER TABLE disease_term_p ADD PRIMARY KEY (disease);
ALTER TABLE disease_term_p ADD COLUMN disease_terms int;
UPDATE disease_term_p SET disease_terms = (SELECT disease_terms from disease_pharma_chem_profiles WHERE disease_pharma_chem_profiles.disease=disease_term_p.disease LIMIT 1);
ALTER TABLE disease_term_p ADD COLUMN p_int int;
UPDATE disease_term_p SET p_int = ( SELECT MIN(p_int) FROM disease_pharma_chem_pscores WHERE disease_term_p.disease_terms < disease_pharma_chem_pscores.disease_terms);
UPDATE disease_term_p SET p_int = 100 WHERE p_int IS NULL;

DROP TABLE IF EXISTS disease_chem_score_p;
CREATE TABLE disease_chem_score_p AS SELECT disease, chem, score12 FROM disease_pharma_chem_profiles;
ALTER TABLE disease_chem_score_p ADD COLUMN score12_p_int int;
UPDATE disease_chem_score_p SET score12_p_int = (SELECT MIN(p_int) FROM disease_pharma_chem_pscores WHERE disease_chem_score_p.score12 < disease_pharma_chem_pscores.score12);
UPDATE disease_chem_score_p SET score12_p_int = 100 WHERE score12_p_int IS NULL;
ALTER TABLE disease_chem_score_p ADD PRIMARY KEY (disease, chem);
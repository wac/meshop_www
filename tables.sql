CREATE TABLE IF NOT EXISTS gene
(
        gene_id INT PRIMARY KEY,
        locus VARCHAR(64),
        taxon_id INT,
	INDEX (locus)
);

CREATE TABLE IF NOT EXISTS CTD_validation
(
term VARCHAR(256),
gene_id int,
PRIMARY KEY (term, gene_id),
INDEX (term),
INDEX (gene_id)
);

CREATE TABLE IF NOT EXISTS new_gene2pubmed_hum_disease
(
term VARCHAR(256),
gene_id int,
score3 float,
score4 float,
score5 float,
score6 float,
score7 float,
score8 float,
score9 float,
score10 float,
score11 float,
score12 float,
PRIMARY KEY (term, gene_id),
INDEX (term),
INDEX (gene_id)
);

CREATE TABLE IF NOT EXISTS hum_disease_gene2pubmed_profiles
(
term VARCHAR(256),
gene_id int,
score3 float,
score4 float,
score5 float,
score6 float,
score7 float,
score8 float,
score9 float,
score10 float,
score11 float,
score12 float,
PRIMARY KEY (term, gene_id),
INDEX (term),
INDEX (gene_id)
);

CREATE TABLE IF NOT EXISTS nr_hum_gene2pubmed_gene_mesh_p
(
gene_id int,
term VARCHAR(256),
term_refs int,
gene_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(gene_id, term),
INDEX (term),
INDEX (gene_id)
);

CREATE TABLE IF NOT EXISTS nr_gene2pubmedBG_jaspar_gene2pubmed_gene_mesh_p
(
gene_id int,
term VARCHAR(256),
term_refs int,
gene_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(gene_id, term),
INDEX (term),
INDEX (gene_id)
);

CREATE TABLE IF NOT EXISTS hum_gene2pubmed_gene_mesh_p
(
gene_id int,
term VARCHAR(256),
term_refs int,
gene_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(gene_id, term),
INDEX (term),
INDEX (gene_id)
);

CREATE TABLE IF NOT EXISTS nr_gene2pubmedBG_hum_gene2pubmed_gene_mesh_p
(
gene_id int,
term VARCHAR(256),
term_refs int,
gene_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(gene_id, term),
INDEX (term),
INDEX (gene_id)
);

CREATE TABLE IF NOT EXISTS gene2pubmedBG_hum_gene2pubmed_gene_mesh_p
(
gene_id int,
term VARCHAR(256),
term_refs int,
gene_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(gene_id, term),
INDEX (term),
INDEX (gene_id)
);

CREATE TABLE IF NOT EXISTS disease_comesh_p
(
disease VARCHAR(256),
term VARCHAR(256),
term_refs int,
disease_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(disease, term),
INDEX (disease),
INDEX (term)
);

CREATE TABLE IF NOT EXISTS nr_disease_comesh_p
(
disease VARCHAR(256),
term VARCHAR(256),
term_refs int,
disease_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(disease, term),
INDEX (disease),
INDEX (term)
);

CREATE TABLE IF NOT EXISTS nr_diseaseBG_disease_comesh_p
(
disease VARCHAR(256),
term VARCHAR(256),
term_refs int,
disease_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(disease, term),
INDEX (disease),
INDEX (term)
);

CREATE TABLE IF NOT EXISTS mesh_tree
(
term varchar(256),
tree_num varchar(256),
PRIMARY KEY(term, tree_num),
INDEX(term),
INDEX(tree_num)
);

CREATE TABLE IF NOT EXISTS generif
(
        gene_id int,
        pmid int,
        heading varchar(30),
        description varchar(512),
        PRIMARY KEY (gene_id, pmid, heading),
        FOREIGN KEY (gene_id) REFERENCES gene,
        FOREIGN KEY (pmid) REFERENCES pubmed
);

CREATE TABLE IF NOT EXISTS gene2pubmed
(
        gene_id INT,
        pmid INT,
	INDEX(pmid),	
        PRIMARY KEY(gene_id,  pmid)
);

CREATE TABLE IF NOT EXISTS pubmed_mesh_parent
(
pmid int,
mesh_parent VARCHAR(256),
PRIMARY KEY (pmid, mesh_parent),
INDEX (mesh_parent)
) max_rows = 200000000000;

CREATE TABLE IF NOT EXISTS pubmed
(
        pmid int PRIMARY KEY,
        title varchar(512),
        journaltitle varchar(256),
        journalisoabbrev varchar(256),
        pubyear int,
        affiliation varchar(512),
        INDEX (pubyear),
        INDEX (journaltitle)
);

CREATE TABLE IF NOT EXISTS nr_braindiseaseBG_disease_comesh_p
(
disease VARCHAR(256),
term VARCHAR(256),
term_refs int,
disease_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(disease, term),
INDEX (disease),
INDEX (term)
);

CREATE TABLE IF NOT EXISTS braindiseaseBG_disease_comesh_p
(
disease VARCHAR(256),
term VARCHAR(256),
term_refs int,
disease_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(disease, term),
INDEX (disease),
INDEX (term)
);

CREATE TABLE IF NOT EXISTS mesh_child
(
term VARCHAR(256),
child VARCHAR(256),
PRIMARY KEY(term, child),
INDEX (term),
INDEX (child)
);

CREATE TABLE IF NOT EXISTS all_chem_mesh_p
(
chem VARCHAR(256),
term VARCHAR(256),
term_refs int,
chem_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
tfidf float,
PRIMARY KEY(chem, term),
INDEX (term),
INDEX (chem)
);

CREATE TABLE IF NOT EXISTS pubmed_chem
(
chem VARCHAR(256),
pmid int
);

CREATE TABLE IF NOT EXISTS journal_mesh_p
(
journaltitle VARCHAR(256),
term VARCHAR(256),
term_refs int,
gene_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(journaltitle, term),
INDEX (term),
INDEX (journaltitle)
);

CREATE TABLE IF NOT EXISTS nr_journal_mesh_p
(
journaltitle VARCHAR(256),
term VARCHAR(256),
term_refs int,
gene_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(journaltitle, term),
INDEX (term),
INDEX (journaltitle)
);

CREATE TABLE IF NOT EXISTS journal_min2005_mesh_p
(
journaltitle VARCHAR(256),
term VARCHAR(256),
term_refs int,
gene_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(journaltitle, term),
INDEX (term),
INDEX (journaltitle)
);

CREATE TABLE IF NOT EXISTS nr_journal_min2005_mesh_p
(
journaltitle VARCHAR(256),
term VARCHAR(256),
term_refs int,
gene_refs int,
background_refs int,
pubmed_non_refs int,
p_val float,
PRIMARY KEY(journaltitle, term),
INDEX (term),
INDEX (journaltitle)
);

CREATE TABLE IF NOT EXISTS disease_pharma_chem_profiles
(
disease VARCHAR(256),
chem VARCHAR(256),
disease_terms int,
chem_terms int,
score3 float,
score4 float,
score5 float,
score6 float,
score7 float,
score8 float,
score9 float,
score10 float,
score11 float,
score12 float,
PRIMARY KEY (chem, disease),
INDEX (chem),
INDEX (disease),
INDEX (chem_terms),
INDEX (disease_terms, chem_terms, score6)
);

CREATE TABLE IF NOT EXISTS disease_pharma_chem_pscores
(
p_int int,
disease_terms int,
chem_terms int,
score12 float,
PRIMARY KEY (p_int),
INDEX (disease_terms),
INDEX (chem_terms),
INDEX (score12)
);

CREATE TABLE IF NOT EXISTS disease_pharma_chem_litp_score_table
(
disease_p_int int,
chem_p_int int,
score_p_int int,
pcount int,
pscore float,
PRIMARY KEY(disease_p_int, chem_p_int, score_p_int)
);
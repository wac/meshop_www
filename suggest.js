function suggestName(term_id, msg_id, db, fieldname, fieldtxt, exampletxt) {
    var term = document.getElementById(term_id).value;
    queryString="get_suggest_name.php?qlimit=6&db="+db+"&fieldname="+fieldname+"&fieldtxt="+fieldtxt+"&name="+term+"&id="+term_id+"&exampletxt="+exampletxt;
    ajaxSubmit(queryString, msg_id, '');
}

function suggestTerm(term_id, msg_id, tfilter) {
    var term = document.getElementById(term_id).value;
    queryString="get_suggest_mesh.php?qlimit=6&tfilter="+tfilter+"&term="+term+"&id="+term_id;
    ajaxSubmit(queryString, msg_id, '');
}

function suggestGene() {
    var gene = document.getElementById('gene').value;
    queryString="get_suggest_gene.php?qlimit=6&gene="+gene;
    ajaxSubmit(queryString, 'suggestDiv', '');
}

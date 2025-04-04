function ocultarBloco(idfornecedor) {
    var fornecedor = document.getElementById(idfornecedor);
    if (fornecedor.style.display === 'none') {
        fornecedor.style.display = 'block';
    } else {
        fornecedor.style.display = 'none';
    }
}
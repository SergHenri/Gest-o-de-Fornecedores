// Função para mostrar ou esconder (alternar visibilidade) de um elemento na página, com base no ID
function ocultarBloco(idfornecedor) {
    // Seleciona o elemento HTML pelo ID passado como parâmetro
    var fornecedor = document.getElementById(idfornecedor);

    // Verifica se o elemento está atualmente escondido (display: none)
    if (fornecedor.style.display === 'block') {
        // Se estiver escondido, mostra o elemento (display: block)
        fornecedor.style.display = 'none';
    } else {
        // Se estiver visível, esconde o elemento (display: none)
        fornecedor.style.display = 'block';
    }
}

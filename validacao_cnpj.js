// Função para validar um CNPJ passado via campo de input
function validarCNPJ(input) {
    // Remove todos os caracteres que não forem dígitos (ex: pontos, barras, traços)
    let cnpj = input.value.replace(/\D/g, '');

    // Verifica se o CNPJ tem exatamente 14 dígitos
    if (cnpj.length !== 14) {
        // Aqui você poderia mostrar uma mensagem de erro (está comentado no momento)
        // document.getElementById('mensagemErroCNPJ').textContent = 'CNPJ inválido';
        return; // Encerra a função se o CNPJ for inválido
    }

    // Se o CNPJ tiver 14 dígitos, considera como válido (não é uma validação matemática completa ainda)
    // A linha abaixo limpa a mensagem de erro, caso exista (também está comentada)
    // document.getElementById('mensagemErroCNPJ').textContent = '';
}

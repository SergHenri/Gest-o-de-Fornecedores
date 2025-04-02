function validarCNPJ(input) {
    // Remove caracteres não numéricos
    let cnpj = input.value.replace(/\D/g, '');

    // Verifica se o CNPJ tem 14 dígitos
    if (cnpj.length !== 14) {
        //document.getElementById('mensagemErroCNPJ').textContent = 'CNPJ inválido';
        return;
    }

    // Limpa a mensagem de erro se o CNPJ for válido
    //document.getElementById('mensagemErroCNPJ').textContent = '';
}
function ocultarBloco(idcliente) {
    var cliente = document.getElementById(idcliente);
    if (cliente.style.display === 'none') {
        cliente.style.display = 'block';
    } else {
        cliente.style.display = 'none';
    }
}
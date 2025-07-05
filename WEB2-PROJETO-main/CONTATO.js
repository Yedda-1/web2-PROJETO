document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');

    form.addEventListener('submit', function(event) {
        // CAMPOS DO FORM.
        const nome = document.getElementById('nome');
        const email = document.getElementById('email');
        const mensagem = document.getElementById('mensagem');

        // VERIFICA SE TEM CAMPO VAZIO
        if (nome.value.trim() === '' || email.value.trim() === '' || mensagem.value.trim() === '') {
            //SOLICITA PREENCHIMENTO
            event.preventDefault();
            alert('Por favor, preencha todos os campos.');
        }
    });
});

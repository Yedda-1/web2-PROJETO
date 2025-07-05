document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const mensagemResposta = document.getElementById('mensagemResposta'); // ID do elemento de mensagem
    const toggleSenha = document.getElementById('toggleSenha'); // ID do ícone do olho
    const senhaInput = document.getElementById('senha'); // ID do campo da senha

    // Lógica para o envio do formulário de login
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Impede o envio padrão do formulário

            const formData = new FormData(loginForm);

            fetch(loginForm.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Espera uma resposta JSON
            .then(data => {
                mensagemResposta.textContent = data.message; // Exibe a mensagem
                if (data.status === 'success') {
                    mensagemResposta.className = 'mensagem sucesso'; // Aplica classe de sucesso
                    // Opcional: Redirecionar após login bem-sucedido
                    setTimeout(() => {
                        window.location.href = 'index.html'; // Redireciona para a página inicial
                     }, 1500); 
                } else {
                    mensagemResposta.className = 'mensagem erro'; // Aplica classe de erro
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mensagemResposta.textContent = 'Ocorreu um erro ao processar sua solicitação.';
                mensagemResposta.className = 'mensagem erro';
            });
        });
    }

    // Lógica para o "olhinho" de alternar a visibilidade da senha
    if (toggleSenha && senhaInput) {
        toggleSenha.addEventListener('click', function() {
            // Alterna o tipo do input entre 'password' e 'text'
            const type = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
            senhaInput.setAttribute('type', type);

            // Alterna o ícone (olho aberto / olho fechado)
            this.classList.toggle('fa-eye'); // Remove/adiciona olho aberto
            this.classList.toggle('fa-eye-slash'); // Adiciona/remove olho fechado
        });
    }
});
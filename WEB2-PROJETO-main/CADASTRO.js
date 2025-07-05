document.addEventListener('DOMContentLoaded', function() {
    const cadastroForm = document.getElementById('cadastroForm');
    const mensagemResposta = document.getElementById('mensagemResposta'); // ID do elemento de mensagem

    if (cadastroForm) {
        cadastroForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Impede o envio padrão do formulário

            const formData = new FormData(cadastroForm);

            fetch(cadastroForm.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Espera uma resposta JSON
            .then(data => {
                mensagemResposta.textContent = data.message;
                if (data.status === 'success') {
                    mensagemResposta.className = 'mensagem sucesso'; // Classes CSS atualizadas
                    cadastroForm.reset(); // Limpa o formulário em caso de sucesso
                    // Opcional: Redirecionar após alguns segundos
                    // setTimeout(() => {
                    //     window.location.href = 'login.html';
                    // }, 3000);
                } else {
                    mensagemResposta.className = 'mensagem erro'; // Classes CSS atualizadas
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mensagemResposta.textContent = 'Ocorreu um erro ao processar sua solicitação.';
                mensagemResposta.className = 'mensagem erro'; // Classes CSS atualizadas
            });
        });
    }
});
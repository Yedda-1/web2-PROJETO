// C:\wamp64\www\WEB2-PROJETO-main\js\redefinir_senha.js
document.addEventListener('DOMContentLoaded', function() {
    console.log("DEBUG JS: redefinir_senha.js carregado.");

    const redefinirSenhaForm = document.getElementById('redefinirSenhaForm');
    const tokenInput = document.getElementById('token');
    const mensagemResposta = document.getElementById('mensagemResposta');

    // Pega o token da URL
    const urlParams = new URLSearchParams(window.location.search);
    const tokenFromUrl = urlParams.get('token');

    if (tokenFromUrl) {
        tokenInput.value = tokenFromUrl; // Preenche o campo oculto com o token
        console.log("DEBUG JS: Token da URL encontrado e preenchido.");
    } else {
        // Se não houver token na URL, provavelmente é um acesso inválido
        mensagemResposta.className = 'mensagem erro';
        mensagemResposta.textContent = 'Token de redefinição de senha não encontrado na URL. Por favor, use o link completo do e-mail.';
        redefinirSenhaForm.querySelector('button[type="submit"]').disabled = true; // Desabilita o botão
        console.error("DEBUG JS ERRO: Token não encontrado na URL.");
        return;
    }

    redefinirSenhaForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Impede o envio padrão do formulário

        console.log("DEBUG JS: Evento de submit de redefinição interceptado.");

        const novaSenha = document.getElementById('nova_senha').value;
        const confirmaSenha = document.getElementById('confirma_senha').value;

        if (novaSenha !== confirmaSenha) {
            mensagemResposta.className = 'mensagem erro';
            mensagemResposta.textContent = 'As senhas não coincidem. Por favor, tente novamente.';
            console.error("DEBUG JS ERRO: Senhas não coincidem.");
            return;
        }

        if (novaSenha.length < 6) { // Exemplo de validação de senha
            mensagemResposta.className = 'mensagem erro';
            mensagemResposta.textContent = 'A senha deve ter pelo menos 6 caracteres.';
            console.error("DEBUG JS ERRO: Senha muito curta.");
            return;
        }

        const formData = new FormData(redefinirSenhaForm); // Coleta todos os campos do formulário (incluindo o token oculto)

        fetch(redefinirSenhaForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log("DEBUG JS: Resposta recebida da requisição de redefinição.");
            const clonedResponse = response.clone();
            clonedResponse.text().then(text => console.log("DEBUG JS: Conteúdo da resposta (texto) de redefinição:", text));

            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return response.json();
            } else {
                throw new TypeError("Resposta do servidor não é JSON: " + text);
            }
        })
        .then(data => {
            console.log("DEBUG JS: Dados JSON de redefinição processados:", data);
            mensagemResposta.className = 'mensagem';
            mensagemResposta.textContent = data.message;
            if (data.status === 'success') {
                mensagemResposta.classList.add('sucesso');
                // Opcional: redirecionar para a página de login após sucesso
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 3000); // Redireciona após 3 segundos
            } else {
                mensagemResposta.classList.add('erro');
            }
        })
        .catch(error => {
            console.error("DEBUG JS ERRO: Erro na requisição ou processamento da resposta de redefinição:", error);
            mensagemResposta.className = 'mensagem erro';
            mensagemResposta.textContent = 'Ocorreu um erro ao redefinir sua senha: ' + error.message;
        });
    });
});
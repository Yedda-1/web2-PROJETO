// C:\wamp64\www\WEB2-PROJETO-main\js\solicitar_redefinicao.js
document.addEventListener('DOMContentLoaded', function() {
    console.log("DEBUG JS: DOMContentLoaded - Script solicitar_redefinicao.js carregado.");

    const form = document.getElementById('solicitarRedefinicaoForm');
    const mensagemResposta = document.getElementById('mensagemResposta');

    if (!form) {
        console.error("DEBUG JS ERRO: Formulário 'solicitarRedefinicaoForm' não encontrado!");
        return;
    }

    console.log("DEBUG JS: Formulário encontrado. Adicionando event listener.");

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Impede o envio padrão do formulário

        console.log("DEBUG JS: Evento de submit interceptado.");
        console.log("DEBUG JS: URL da requisição: " + form.action);

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log("DEBUG JS: Resposta recebida da requisição.");
            // Tenta clonar a resposta para inspecioná-la sem consumir o corpo original
            const clonedResponse = response.clone();
            clonedResponse.text().then(text => console.log("DEBUG JS: Conteúdo da resposta (texto):", text));

            // Verifica se a resposta é JSON antes de tentar o .json()
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return response.json();
            } else {
                // Se não for JSON, trata como erro e lança uma exceção
                throw new TypeError("Resposta do servidor não é JSON: " + text); 
            }
        })
        .then(data => {
            console.log("DEBUG JS: Dados JSON processados:", data);
            mensagemResposta.className = 'mensagem'; // Limpa classes antigas
            mensagemResposta.textContent = data.message;
            if (data.status === 'success') {
                mensagemResposta.classList.add('sucesso');
            } else {
                mensagemResposta.classList.add('erro');
            }
        })
        .catch(error => {
            console.error("DEBUG JS ERRO: Erro na requisição ou processamento da resposta:", error);
            mensagemResposta.className = 'mensagem erro';
            mensagemResposta.textContent = 'Ocorreu um erro ao processar sua solicitação: ' + error.message;
        });
    });
});
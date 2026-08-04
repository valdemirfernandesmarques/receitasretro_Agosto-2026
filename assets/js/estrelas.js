// assets/js/estrelas.js

document.querySelectorAll('.estrela').forEach((estrela, index) => {
    estrela.addEventListener('click', () => {
        const estrelas = index + 1;
        const receitaId = estrela.dataset.receita;

        fetch('../../includes/avaliacao.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `receita_id=${receitaId}&estrelas=${estrelas}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.sucesso) {
                alert("Obrigado pela avaliação!");
            } else {
                alert(data.erro || "Erro ao avaliar.");
            }
        });
    });
});
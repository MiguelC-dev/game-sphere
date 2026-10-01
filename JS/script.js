// =========================
// PESQUISA DE CONTEÚDO
// =========================

function pesquisarNoticias() {

    // Pega o que foi digitado
    let pesquisa = document.getElementById("campoPesquisa").value.toLowerCase();

    // Pega notícias e reviews
    let conteudos = document.querySelectorAll(
        ".noticia-principal, .noticia-menor, .review-card"
    );

    // Verifica cada conteúdo
    conteudos.forEach(function(conteudo) {

        // Pega o texto do conteúdo
        let texto = conteudo.innerText.toLowerCase();

        // Verifica se encontrou o texto pesquisado
        if (texto.includes(pesquisa)) {
            conteudo.style.display = "";
        } else {
            conteudo.style.display = "none";
        }

    });
}
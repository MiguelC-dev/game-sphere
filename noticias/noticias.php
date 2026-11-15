
<?php
require_once "../paineladm/conexao.php";

// Busca todas as notícias publicadas da seção Notícias
$sql = "SELECT * FROM noticias
        WHERE secao = 'noticias'
        AND status = 'publicada'
        ORDER BY data_publicacao DESC, id DESC";

$consulta = $conexao->query($sql);
$noticias = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Notícias — Game Sphere</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/noticias.css">

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>

<body>

<header>
    <h1>
        <span class="gs">GS </span>
        News
    </h1>

    <nav>
        <a href="../index.php" class="nav-btn">Inicio</a>
        <a href="noticias.php" class="nav-btn">Notícias</a>
        <a href="../reviews/reviews.html" class="nav-btn">Reviews</a>
        <a href="../contato/contato.html" class="nav-btn">Contato</a>
    </nav>
</header>

<main class="noticias-menores">

    <section class="noticias-cabecalho">

        <h2 class="pagina-titulo">Notícias</h2>

        <p class="pagina-subtitulo">
            Últimas novidades do mundo dos games
        </p>

        <br>

        <div class="pesquisa-noticias">
            <input
                type="text"
                id="campoPesquisa"
                placeholder="Pesquisar notícia..."
                onkeyup="pesquisarNoticias()"
            >
        </div>

    </section>

    <?php if (count($noticias) > 0): ?>

        <?php foreach ($noticias as $noticia): ?>

            <?php
            $imagem = $noticia["imagem"];

            // Ajusta o caminho da imagem para esta pasta
            if (
                !str_starts_with($imagem, "../") &&
                !str_starts_with($imagem, "/") &&
                !preg_match("~^https?://~i", $imagem)
            ) {
                $imagem = "../" . $imagem;
            }
            ?>

            <article class="noticia-menor">

                <img
                    src="<?php echo htmlspecialchars($imagem); ?>"
                    alt="<?php echo htmlspecialchars($noticia["titulo"]); ?>"
                >

                <h3>
                    <?php echo htmlspecialchars($noticia["titulo"]); ?>
                </h3>

                <p class="data-noticia">
                    <?php echo date("d/m/Y", strtotime($noticia["data_publicacao"])); ?>
                </p>

                <strong>
                    <?php echo htmlspecialchars($noticia["resumo"]); ?>
                </strong>

                <a
                    href="../noticia.php?id=<?php echo $noticia["id"]; ?>"
                    class="botao"
                >
                    Ler mais
                </a>

            </article>

        <?php endforeach; ?>

    <?php else: ?>

        <p>Nenhuma notícia publicada no momento.</p>

    <?php endif; ?>

</main>

<footer>
    <p>© 2026 Game Sphere - Todos os direitos reservados</p>
</footer>

<script src="../JS/script.js"></script>

</body>
</html>

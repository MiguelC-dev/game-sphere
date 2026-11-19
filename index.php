
<?php
require_once "paineladm/conexao.php";

// Busca a notícia principal da página inicial
$sqlPrincipal = "SELECT * FROM noticias
                 WHERE secao = 'inicio'
                 AND status = 'publicada'
                 AND destaque = 1
                 ORDER BY data_publicacao DESC, id DESC
                 LIMIT 1";

$consultaPrincipal = $conexao->query($sqlPrincipal);
$principal = $consultaPrincipal->fetch(PDO::FETCH_ASSOC);

// Busca as notícias menores da página inicial
$sqlMenores = "SELECT * FROM noticias
               WHERE secao = 'inicio'
               AND status = 'publicada'
               AND destaque = 0
               ORDER BY data_publicacao DESC, id DESC
               LIMIT 4";

$consultaMenores = $conexao->query($sqlMenores);
$menores = $consultaMenores->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GS News</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/noticias.css">

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
        <a href="index.php" class="nav-btn">Inicio</a>
        <a href="noticias/noticias.php" class="nav-btn">Notícias</a>
        <a href="reviews/reviews.html" class="nav-btn">Reviews</a>
        <a href="contato/contato.php" class="nav-btn">Contato</a>
    </nav>
</header>

<section class="hero-banner">
    <p class="hero-text">
        O seu portal para as principais notícias, lançamentos e novidades do
        mundo gamer.
    </p>
</section>

<main>

    <section class="principal">

        <h1 class="titulo-secao">Notícias em Alta</h1>

        <div class="pesquisa-noticias">
            <input
                type="text"
                id="campoPesquisa"
                placeholder="Pesquisar notícia..."
                onkeyup="pesquisarNoticias()"
            >
        </div>

        <?php if ($principal): ?>

            <article class="noticia-principal">

                <img
                    src="<?php echo htmlspecialchars($principal['imagem']); ?>"
                    alt="<?php echo htmlspecialchars($principal['titulo']); ?>"
                >

                <h2>
                    <?php echo htmlspecialchars($principal['titulo']); ?>
                </h2>

                <p class="data-noticia">
                    <?php echo date("d/m/Y", strtotime($principal['data_publicacao'])); ?>
                </p>

                <strong>
                    <?php echo htmlspecialchars($principal['resumo']); ?>
                </strong>

                <a href="noticia.php?id=<?php echo $principal['id']; ?>" class="botao">
                    Ler mais
                </a>

            </article>

        <?php else: ?>

            <p>Nenhuma notícia principal publicada no momento.</p>

        <?php endif; ?>


        <div class="noticias-menores">

            <?php foreach ($menores as $noticia): ?>

                <article class="noticia-menor">

                    <img
                        src="<?php echo htmlspecialchars($noticia['imagem']); ?>"
                        alt="<?php echo htmlspecialchars($noticia['titulo']); ?>"
                    >

                    <h3>
                        <?php echo htmlspecialchars($noticia['titulo']); ?>
                    </h3>

                    <p class="data-noticia">
                        <?php echo date("d/m/Y", strtotime($noticia['data_publicacao'])); ?>
                    </p>

                    <p>
                        <?php echo htmlspecialchars($noticia['resumo']); ?>
                    </p>

                    <a href="noticia.php?id=<?php echo $noticia['id']; ?>" class="botao">
                        Ler mais
                    </a>

                </article>

            <?php endforeach; ?>

            <?php if (count($menores) === 0): ?>
                <p>Nenhuma outra notícia publicada no momento.</p>
            <?php endif; ?>

        </div>

    </section>

    <footer>
        <p>© 2026 Game Sphere - Todos os direitos reservados</p>
    </footer>

</main>

<script src="JS/script.js"></script>

</body>
</html>

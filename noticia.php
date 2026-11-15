
<?php
require_once "paineladm/conexao.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM noticias
        WHERE id = ?
        AND status = 'publicada'";

$consulta = $conexao->prepare($sql);
$consulta->execute([$id]);
$noticia = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($noticia["titulo"]); ?> - GS News</title>

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
            <a href="contato/contato.html" class="nav-btn">Contato</a>
        </nav>
    </header>

    <main>
        <section class="principal">

            <article class="noticia-principal">

                <h2><?php echo htmlspecialchars($noticia["titulo"]); ?></h2>

                <p class="data-noticia">
                    <?php echo date("d/m/Y", strtotime($noticia["data_publicacao"])); ?>
                </p>

                <img
                    src="<?php echo htmlspecialchars($noticia["imagem"]); ?>"
                    alt="<?php echo htmlspecialchars($noticia["titulo"]); ?>"
                >

                <strong>
                    <?php echo htmlspecialchars($noticia["resumo"]); ?>
                </strong>

                <p>
                    <?php
                    echo nl2br(htmlspecialchars($noticia["conteudo"]));
                    ?>
                </p>

                <a href="index.php" class="botao">Voltar</a>

            </article>

        </section>
    </main>

    <footer>
        <p>© 2026 Game Sphere - Todos os direitos reservados</p>
    </footer>

</body>
</html>

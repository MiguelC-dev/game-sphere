
<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admGS.php");
    exit;
}

$sql = "SELECT * FROM noticias ORDER BY criado_em DESC";
$consulta = $conexao->query($sql);
$noticias = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Notícias - GS News</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <span>GS</span> NEWS
        </div>

        <p class="admin-text">PAINEL ADMINISTRATIVO</p>

        <nav>
            <a href="noticias.php" class="active">📰 Notícias</a>
            <a href="#">💬 Contato</a>
            <a href="#">⭐ Reviews</a>
        </nav>

        <a href="../index.php" class="back-site">← Voltar ao site</a>
        <a href="logout.php" class="back-site">Sair do painel</a>
    </aside>

    <main class="content">
        <header>
            <h1>Gerenciar Notícias</h1>
            <p>Visualize e gerencie as notícias do GS News.</p>
        </header>

        <a href="cadastrar_noticia.php" class="botao">
            + Cadastrar notícia
        </a>

        <section class="welcome">
            <h2>Notícias cadastradas: <?php echo count($noticias); ?></h2>

            <?php if (count($noticias) > 0): ?>
                <?php foreach ($noticias as $noticia): ?>
                    <div class="card">
                        <h3><?php echo htmlspecialchars($noticia["titulo"]); ?></h3>

                        <p>
                            Seção:
                            <?php echo htmlspecialchars($noticia["secao"]); ?>
                        </p>

                        <p>
                            Status:
                            <?php echo htmlspecialchars($noticia["status"]); ?>
                        </p>

                        <a href="editar_noticia.php?id=<?php echo $noticia["id"]; ?>">
                            Editar
                        </a>

                        <form method="POST" action="excluir_noticia.php"
                            onsubmit="return confirm('Tem certeza que deseja excluir esta notícia?');">
                                <input type="hidden" name="id"
                                value="<?php echo $noticia["id"]; ?>">
                                 <button type="submit">Excluir</button>
                        </form>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhuma notícia cadastrada ainda.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>

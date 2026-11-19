
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admGS.php");
    exit;
}

require_once __DIR__ . "/conexao.php";

$mensagens = [];
$erro = false;

try {
    $sql = "SELECT id, nome, assunto
            FROM mensagens_contato
            ORDER BY data_envio DESC, id DESC";

    $consulta = $conexao->query($sql);
    $mensagens = $consulta->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    error_log("Erro ao buscar mensagens: " . $e->getMessage());
    $erro = true;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mensagens - GS News</title>

    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/mensagens.css">
</head>

<body>

    <aside class="sidebar">

        <div class="logo">
            <span>GS</span> NEWS
        </div>

        <p class="admin-text">PAINEL ADMINISTRATIVO</p>

        <nav>
            <a href="noticias.php">📰 Notícias</a>
            <a href="mensagens.php" class="active">💬 Contato</a>
            <a href="#">⭐ Reviews</a>
        </nav>

        <a href="../index.php" class="back-site">
            ← Voltar ao site
        </a>

        <a href="logout.php" class="back-site">
            Sair do painel
        </a>

    </aside>

    <main class="content mensagens-page">

        <header>
            <h1>Mensagens recebidas</h1>
            <p>Visualize as mensagens enviadas pelos visitantes do GS News.</p>
        </header>

        <section class="mensagens-area">

            <div class="mensagens-topo">
                <h2>Caixa de entrada</h2>

                <span class="contador-mensagens">
                    <?php echo count($mensagens); ?>
                    <?php echo count($mensagens) === 1 ? "mensagem" : "mensagens"; ?>
                </span>
            </div>

            <?php if ($erro): ?>

                <div class="mensagens-vazio">
                    Não foi possível carregar as mensagens.
                </div>

            <?php elseif (count($mensagens) > 0): ?>

                <div class="mensagens-lista">

                    <?php foreach ($mensagens as $msg): ?>

                        <article class="mensagem-card">

                            <div class="mensagem-informacoes">

                                <h3>
                                    <?php echo htmlspecialchars($msg["nome"], ENT_QUOTES, "UTF-8"); ?>
                                </h3>

                                <p>
                                    <?php echo htmlspecialchars($msg["assunto"], ENT_QUOTES, "UTF-8"); ?>
                                </p>

                            </div>

                            <a
                                href="visualizar_mensagem.php?id=<?php echo (int) $msg["id"]; ?>"
                                class="botao-ler"
                            >
                                Ler mais
                            </a>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="mensagens-vazio">
                    <h3>Nenhuma mensagem recebida</h3>
                    <p>Quando alguém enviar uma mensagem pelo site, ela aparecerá aqui.</p>
                </div>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>

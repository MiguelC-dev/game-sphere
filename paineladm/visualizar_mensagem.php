
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admGS.php");
    exit;
}

require_once __DIR__ . "/conexao.php";

$mensagemContato = null;
$erro = false;

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($id !== false && $id !== null && $id > 0) {

    try {
        $sql = "SELECT id, nome, email, assunto, mensagem, data_envio
                FROM mensagens_contato
                WHERE id = :id";

        $consulta = $conexao->prepare($sql);
        $consulta->execute([
            ":id" => $id
        ]);

        $mensagemContato = $consulta->fetch(PDO::FETCH_ASSOC);

    } catch (Throwable $e) {
        error_log("Erro ao abrir mensagem: " . $e->getMessage());
        $erro = true;
    }
}

function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Visualizar mensagem - GS News</title>

    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/visualizar_mensagem.css">
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

    <main class="content visualizar-page">

        <header>
            <h1>Visualizar mensagem</h1>
            <p>Confira os detalhes da mensagem recebida.</p>
        </header>

        <section class="visualizar-area">

            <?php if ($erro): ?>

                <div class="mensagem-alerta">
                    Não foi possível carregar a mensagem.
                </div>

            <?php elseif ($mensagemContato): ?>

                <div class="detalhe-cabecalho">

                    <div>
                        <span class="detalhe-legenda">ASSUNTO DA MENSAGEM</span>

                        <h2>
                            <?php echo escapar($mensagemContato["assunto"]); ?>
                        </h2>
                    </div>

                    <span class="detalhe-data">
                        <?php
                        echo date(
                            "d/m/Y H:i",
                            strtotime($mensagemContato["data_envio"])
                        );
                        ?>
                    </span>

                </div>

                <div class="detalhe-informacoes">

                    <div class="detalhe-campo">
                        <span>Nome do remetente</span>
                        <p><?php echo escapar($mensagemContato["nome"]); ?></p>
                    </div>

                    <div class="detalhe-campo">
                        <span>E-mail</span>
                        <p><?php echo escapar($mensagemContato["email"]); ?></p>
                    </div>

                </div>

                <div class="detalhe-mensagem">

                    <h3>Mensagem</h3>

                    <p><?php
                        echo nl2br(escapar($mensagemContato["mensagem"]));
                    ?></p>

                </div>

            <?php else: ?>

                <div class="mensagem-alerta">
                    Mensagem não encontrada.
                </div>

            <?php endif; ?>

            <div class="detalhe-acoes">
                <a href="mensagens.php" class="botao-voltar-mensagens">
                    ← Voltar para mensagens
                </a>
            </div>

        </section>

    </main>

</body>
</html>

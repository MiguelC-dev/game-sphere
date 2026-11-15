
<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admGS.php");
    exit;
}

$erro = "";

// Verifica o ID recebido pela URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: noticias.php");
    exit;
}

// Busca a notícia no banco
$sql = "SELECT * FROM noticias WHERE id = ?";
$consulta = $conexao->prepare($sql);
$consulta->execute([$id]);
$noticia = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    header("Location: noticias.php");
    exit;
}

// Atualiza os dados quando o formulário é enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $data = $_POST["data_publicacao"] ?? "";
    $imagem = trim($_POST["imagem"] ?? "");
    $resumo = trim($_POST["resumo"] ?? "");
    $conteudo = trim($_POST["conteudo"] ?? "");
    $secao = $_POST["secao"] ?? "";
    $destaque = isset($_POST["destaque"]) ? 1 : 0;
    $status = $_POST["status"] ?? "";

    if ($secao === "noticias") {
        $destaque = 0;
    }

    if (
        $titulo === "" ||
        $data === "" ||
        $imagem === "" ||
        $resumo === "" ||
        $conteudo === "" ||
        !in_array($secao, ["inicio", "noticias"], true) ||
        !in_array($status, ["publicada", "rascunho"], true)
    ) {
        $erro = "Preencha todos os campos corretamente.";
    } else {
        try {
            $sql = "UPDATE noticias SET
                titulo = ?,
                data_publicacao = ?,
                imagem = ?,
                resumo = ?,
                conteudo = ?,
                secao = ?,
                destaque = ?,
                status = ?
                WHERE id = ?";

            $consulta = $conexao->prepare($sql);
            $consulta->execute([
                $titulo,
                $data,
                $imagem,
                $resumo,
                $conteudo,
                $secao,
                $destaque,
                $status,
                $id
            ]);

            header("Location: noticias.php");
            exit;

        } catch (PDOException $e) {
            $erro = "Não foi possível atualizar a notícia.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Notícia - GS News</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

    <main class="content">

        <header>
            <h1>Editar Notícia</h1>
            <p>Altere os dados da notícia cadastrada.</p>
        </header>

        <?php if ($erro !== ""): ?>
            <p><?php echo htmlspecialchars($erro); ?></p>
        <?php endif; ?>

        <section class="welcome">

            <form method="POST">

                <label for="titulo">Título</label>
                <input
                    type="text"
                    id="titulo"
                    name="titulo"
                    value="<?php echo htmlspecialchars($noticia["titulo"]); ?>"
                    required
                >
                <br><br>

                <label for="data_publicacao">Data de publicação</label>
                <input
                    type="date"
                    id="data_publicacao"
                    name="data_publicacao"
                    value="<?php echo htmlspecialchars($noticia["data_publicacao"]); ?>"
                    required
                >
                <br><br>

                <label for="imagem">Caminho da imagem</label>
                <input
                    type="text"
                    id="imagem"
                    name="imagem"
                    value="<?php echo htmlspecialchars($noticia["imagem"]); ?>"
                    required
                >
                <br><br>

                <label for="resumo">Resumo da notícia</label>
                <textarea
                    id="resumo"
                    name="resumo"
                    required
                ><?php echo htmlspecialchars($noticia["resumo"]); ?></textarea>
                <br><br>

                <label for="conteudo">Conteúdo completo</label>
                <textarea
                    id="conteudo"
                    name="conteudo"
                    rows="10"
                    required
                ><?php echo htmlspecialchars($noticia["conteudo"]); ?></textarea>
                <br><br>

                <label for="secao">Onde publicar?</label>
                <select id="secao" name="secao" required>
                    <option value="inicio" <?php echo $noticia["secao"] === "inicio" ? "selected" : ""; ?>>
                        Página inicial
                    </option>
                    <option value="noticias" <?php echo $noticia["secao"] === "noticias" ? "selected" : ""; ?>>
                        Aba Notícias
                    </option>
                </select>
                <br><br>

                <label>
                    <input
                        type="checkbox"
                        name="destaque"
                        value="1"
                        <?php echo $noticia["destaque"] ? "checked" : ""; ?>
                    >
                    Notícia principal da página inicial
                </label>
                <br><br>

                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="publicada" <?php echo $noticia["status"] === "publicada" ? "selected" : ""; ?>>
                        Publicada
                    </option>
                    <option value="rascunho" <?php echo $noticia["status"] === "rascunho" ? "selected" : ""; ?>>
                        Rascunho
                    </option>
                </select>
                <br><br>

                <button type="submit">Salvar alterações</button>

            </form>

            <br>
            <a href="noticias.php">Voltar para notícias</a>

        </section>

    </main>

</body>
</html>



<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admGS.php");
    exit;
}

$erro = "";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: noticias.php");
    exit;
}

$sql = "SELECT * FROM noticias WHERE id = ?";
$consulta = $conexao->prepare($sql);
$consulta->execute([$id]);
$noticia = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    header("Location: noticias.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $data = $_POST["data_publicacao"] ?? "";
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
        $resumo === "" ||
        $conteudo === "" ||
        !in_array($secao, ["inicio", "noticias"], true) ||
        !in_array($status, ["publicada", "rascunho"], true)
    ) {
        $erro = "Preencha todos os campos corretamente.";
    } else {
        $novaImagem = $noticia["imagem"];
        $destino = null;

        if (
            isset($_FILES["imagem"]) &&
            $_FILES["imagem"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {
            if ($_FILES["imagem"]["error"] !== UPLOAD_ERR_OK) {
                $erro = "Não foi possível enviar a imagem.";
            } elseif ($_FILES["imagem"]["size"] > 5 * 1024 * 1024) {
                $erro = "A imagem deve ter no máximo 5 MB.";
            } else {
                $tiposPermitidos = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                $temporario = $_FILES["imagem"]["tmp_name"];
                $mime = mime_content_type($temporario);

                if (!isset($tiposPermitidos[$mime])) {
                    $erro = "Formato inválido. Use JPG, PNG ou WEBP.";
                } else {
                    $extensao = $tiposPermitidos[$mime];
                    $nomeArquivo = bin2hex(random_bytes(16)) . "." . $extensao;

                    $pastaUploads = dirname(__DIR__) . "/uploads/noticias/";
                    $novaImagem = "uploads/noticias/" . $nomeArquivo;
                    $destino = $pastaUploads . $nomeArquivo;

                    if (!is_dir($pastaUploads)) {
                        $erro = "A pasta de imagens não foi encontrada.";
                    } elseif (!move_uploaded_file($temporario, $destino)) {
                        $erro = "Não foi possível salvar a imagem no servidor.";
                    }
                }
            }
        }

        if ($erro === "") {
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
                    $novaImagem,
                    $resumo,
                    $conteudo,
                    $secao,
                    $destaque,
                    $status,
                    $id
                ]);

                // Apaga a imagem anterior somente se ela estiver
                // na pasta de uploads administrada pelo sistema.
                if (
                    $destino !== null &&
                    strpos($noticia["imagem"], "uploads/noticias/") === 0
                ) {
                    $arquivoAntigo = dirname(__DIR__) . "/" . $noticia["imagem"];

                    if (is_file($arquivoAntigo)) {
                        unlink($arquivoAntigo);
                    }
                }

                header("Location: noticias.php");
                exit;

            } catch (PDOException $e) {
                if ($destino !== null && is_file($destino)) {
                    unlink($destino);
                }

                $erro = "Não foi possível atualizar a notícia.";
            }
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

            <form method="POST" enctype="multipart/form-data">

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

                <label>Imagem atual</label>
                <br>
                <img
                    src="../<?php echo htmlspecialchars($noticia["imagem"]); ?>"
                    alt="Imagem atual da notícia"
                    style="max-width: 200px; height: auto;"
                >
                <br><br>

                <label for="imagem">Escolher nova imagem (opcional)</label>
                <input
                    type="file"
                    id="imagem"
                    name="imagem"
                    accept=".jpg,.jpeg,.png,.webp"
                >
                <small>JPG, PNG ou WEBP. Máximo: 5 MB. Deixe vazio para manter a imagem atual.</small>
                <br><br>

                <label for="resumo">Resumo da notícia</label>
                <textarea id="resumo" name="resumo" required><?php echo htmlspecialchars($noticia["resumo"]); ?></textarea>
                <br><br>

                <label for="conteudo">Conteúdo completo</label>
                <textarea id="conteudo" name="conteudo" rows="10" required><?php echo htmlspecialchars($noticia["conteudo"]); ?></textarea>
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

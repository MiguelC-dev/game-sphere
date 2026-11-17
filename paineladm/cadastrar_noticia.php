
<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admGS.php");
    exit;
}

$erro = "";

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
        $conteudo === ""
    ) {
        $erro = "Preencha todos os campos.";
    } elseif (!in_array($secao, ["inicio", "noticias"], true)) {
        $erro = "Seção inválida.";
    } elseif (!in_array($status, ["publicada", "rascunho"], true)) {
        $erro = "Status inválido.";
    } elseif (
        !isset($_FILES["imagem"]) ||
        $_FILES["imagem"]["error"] !== UPLOAD_ERR_OK
    ) {
        $erro = "Selecione uma imagem válida.";
    } elseif ($_FILES["imagem"]["size"] > 5 * 1024 * 1024) {
        $erro = "A imagem deve ter no máximo 5 MB.";
    } else {
        $tiposPermitidos = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];

        $arquivoTemporario = $_FILES["imagem"]["tmp_name"];
        $mime = mime_content_type($arquivoTemporario);

        if (!isset($tiposPermitidos[$mime])) {
            $erro = "Formato inválido. Use JPG, PNG ou WEBP.";
        } else {
            $extensao = $tiposPermitidos[$mime];
            $nomeArquivo = bin2hex(random_bytes(16)) . "." . $extensao;

            $pastaUploads = dirname(__DIR__) . "/uploads/noticias/";
            $caminhoImagem = "uploads/noticias/" . $nomeArquivo;
            $destino = $pastaUploads . $nomeArquivo;

            if (!is_dir($pastaUploads)) {
                $erro = "A pasta de imagens não foi encontrada.";
            } elseif (!move_uploaded_file($arquivoTemporario, $destino)) {
                $erro = "Não foi possível enviar a imagem.";
            } else {
                try {
                    $sql = "INSERT INTO noticias
                    (titulo, data_publicacao, imagem, resumo, conteudo, secao, destaque, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                    $consulta = $conexao->prepare($sql);
                    $consulta->execute([
                        $titulo,
                        $data,
                        $caminhoImagem,
                        $resumo,
                        $conteudo,
                        $secao,
                        $destaque,
                        $status
                    ]);

                    header("Location: noticias.php");
                    exit;

                } catch (PDOException $e) {
                    if (file_exists($destino)) {
                        unlink($destino);
                    }

                    $erro = "Não foi possível cadastrar a notícia.";
                }
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
    <title>Cadastrar Notícia - GS News</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <main class="content">
        <header>
            <h1>Cadastrar Notícia</h1>
            <p>Preencha os dados da nova notícia.</p>
        </header>

        <?php if ($erro !== ""): ?>
            <p><?php echo htmlspecialchars($erro); ?></p>
        <?php endif; ?>

        <section class="welcome">
            <form method="POST" enctype="multipart/form-data">

                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" required>

                <br><br>

                <label for="data_publicacao">Data de publicação</label>
                <input type="date" id="data_publicacao" name="data_publicacao" required>

                <br><br>

                <label for="imagem">Escolher imagem</label>
                <input
                    type="file"
                    id="imagem"
                    name="imagem"
                    accept=".jpg,.jpeg,.png,.webp"
                    required
                >
                <small>Formatos: JPG, PNG ou WEBP. Máximo: 5 MB.</small>

                <br><br>

                <label for="resumo">Resumo da notícia</label>
                <textarea id="resumo" name="resumo" required></textarea>

                <br><br>

                <label for="conteudo">Conteúdo completo</label>
                <textarea id="conteudo" name="conteudo" rows="10" required></textarea>

                <br><br>

                <label for="secao">Onde publicar?</label>
                <select id="secao" name="secao" required>
                    <option value="inicio">Página inicial</option>
                    <option value="noticias">Aba Notícias</option>
                </select>

                <br><br>

                <label>
                    <input type="checkbox" name="destaque" value="1">
                    Notícia principal da página inicial
                </label>

                <br><br>

                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="publicada">Publicada</option>
                    <option value="rascunho">Rascunho</option>
                </select>

                <br><br>

                <button type="submit">Cadastrar notícia</button>
            </form>

            <br>

            <a href="noticias.php">Voltar para notícias</a>
        </section>
    </main>
</body>
</html>



<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admGS.php");
    exit;
}

// Só permite exclusão por POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: noticias.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: noticias.php");
    exit;
}

try {
    // Busca o caminho da imagem antes de excluir a notícia
    $sql = "SELECT imagem FROM noticias WHERE id = ?";
    $consulta = $conexao->prepare($sql);
    $consulta->execute([$id]);

    $noticia = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$noticia) {
        header("Location: noticias.php");
        exit;
    }

    $imagem = $noticia["imagem"];

    // Exclui a notícia do banco de dados
    $sql = "DELETE FROM noticias WHERE id = ?";
    $consulta = $conexao->prepare($sql);
    $consulta->execute([$id]);

    // Apaga somente imagens enviadas pelo painel
    if (strpos($imagem, "uploads/noticias/") === 0) {
        $caminhoImagem = dirname(__DIR__) . "/" . $imagem;

        if (is_file($caminhoImagem)) {
            unlink($caminhoImagem);
        }
    }

    header("Location: noticias.php");
    exit;

} catch (PDOException $e) {
    die("Não foi possível excluir a notícia.");
}
?>

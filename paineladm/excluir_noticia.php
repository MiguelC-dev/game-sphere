
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
    $sql = "DELETE FROM noticias WHERE id = ?";
    $consulta = $conexao->prepare($sql);
    $consulta->execute([$id]);

    header("Location: noticias.php");
    exit;

} catch (PDOException $e) {
    die("Não foi possível excluir a notícia.");
}
?>

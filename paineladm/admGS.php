

<?php
session_start();
require_once "conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM administradores WHERE email = ?";
    $consulta = $conexao->prepare($sql);
    $consulta->execute([$email]);

    $administrador = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($administrador && password_verify($senha, $administrador["senha"])) {
        session_regenerate_id(true);

        $_SESSION["admin_id"] = $administrador["id"];
        $_SESSION["admin_nome"] = $administrador["nome"];
        $_SESSION["admin_email"] = $administrador["email"];

        header("Location: dashboard.php");
        exit;
    } else {
        $erro = "E-mail ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admGS</title>
    <link rel="stylesheet" href="../css/paineladm.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <main class="login-container">
        <section class="login-box">
            <div class="logo">
                <span>GS</span> NEWS
            </div>

            <p class="subtitle">Painel Administrativo</p>
            <h1>Entrar</h1>

            <?php if ($erro !== ""): ?>
                <p class="erro"><?php echo htmlspecialchars($erro); ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
                </div>

                <div class="input-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                </div>

                <button type="submit">Entrar</button>
            </form>

            <a href="../index.php" class="back-link">
                ← Voltar para o site
            </a>
        </section>
    </main>
</body>
</html>

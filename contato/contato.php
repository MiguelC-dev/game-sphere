
<?php
if (session_status() === PHP_SESSION_NONE) {
    $https = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";

    session_set_cookie_params([
        "httponly" => true,
        "secure" => $https,
        "samesite" => "Lax"
    ]);

    session_start();
}

if (empty($_SESSION["csrf_contato"])) {
    $_SESSION["csrf_contato"] = bin2hex(random_bytes(32));
}

$nome = "";
$email = "";
$assunto = "";
$mensagem = "";
$erro = "";

// Processa o formulário
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["csrf_contato"], $token)
    ) {
        $erro = "Não foi possível validar o envio. Atualize a página e tente novamente.";

    } else {

        // Recebe somente valores de texto
        $nome = isset($_POST["nome"]) && is_string($_POST["nome"])
            ? trim($_POST["nome"]) : "";

        $email = isset($_POST["email"]) && is_string($_POST["email"])
            ? trim($_POST["email"]) : "";

        $assunto = isset($_POST["assunto"]) && is_string($_POST["assunto"])
            ? trim($_POST["assunto"]) : "";

        $mensagem = isset($_POST["mensagem"]) && is_string($_POST["mensagem"])
            ? trim($_POST["mensagem"]) : "";

        // Campo invisível para ajudar a filtrar envios automatizados
        $site = isset($_POST["website"]) && is_string($_POST["website"])
            ? trim($_POST["website"]) : "";

        // Conta caracteres corretamente quando mbstring está disponível
        $tamanho = function ($texto) {
            return function_exists("mb_strlen")
                ? mb_strlen($texto, "UTF-8")
                : strlen($texto);
        };

        // Se o campo invisível for preenchido, descarta o envio
        if ($site !== "") {
            $_SESSION["contato_sucesso"] = true;
            header("Location: contato.php");
            exit;
        }

        // Validações no servidor
        if (
            $nome === "" ||
            $email === "" ||
            $assunto === "" ||
            $mensagem === ""
        ) {
            $erro = "Preencha todos os campos.";

        } elseif (
            $tamanho($nome) > 50 ||
            $tamanho($email) > 100 ||
            $tamanho($assunto) > 100 ||
            $tamanho($mensagem) > 500
        ) {
            $erro = "Um ou mais campos ultrapassaram o limite permitido.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = "Informe um endereço de e-mail válido.";

        } else {

            try {
                // Reaproveita a conexão existente
                require_once __DIR__ . "/../paineladm/conexao.php";

                $sql = "INSERT INTO mensagens_contato
                        (nome, email, assunto, mensagem)
                        VALUES
                        (:nome, :email, :assunto, :mensagem)";

                $consulta = $conexao->prepare($sql);

                $consulta->execute([
                    ":nome" => $nome,
                    ":email" => $email,
                    ":assunto" => $assunto,
                    ":mensagem" => $mensagem
                ]);

                // Redireciona para evitar reenvio ao atualizar a página
                $_SESSION["contato_sucesso"] = true;
                $_SESSION["csrf_contato"] = bin2hex(random_bytes(32));

                header("Location: contato.php");
                exit;

            } catch (Throwable $e) {
                error_log("Erro no formulário de contato: " . $e->getMessage());

                $erro = "Não foi possível enviar sua mensagem. Tente novamente mais tarde.";
            }
        }
    }
}

// Mensagem de sucesso após o redirecionamento
$sucesso = !empty($_SESSION["contato_sucesso"]);
unset($_SESSION["contato_sucesso"]);
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contato</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/contato.css">

    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap"
        rel="stylesheet"
    >
</head>

<body>

<header>
    <h1>
        <span class="gs">GS </span>
        News
    </h1>

    <nav>
        <a href="../index.php" class="nav-btn">Inicio</a>
        <a href="../noticias/noticias.php" class="nav-btn">Notícias</a>
        <a href="../reviews/reviews.html" class="nav-btn">Reviews</a>
        <a href="contato.php" class="nav-btn">Contato</a>
    </nav>
</header>

<main>

    <h1 class="logo">
        <span class="game-sphere">Game Sphere </span>
        <span class="news"> News</span>
    </h1>

    <h2 class="sublogo">
        Obrigado por visitar o Game Sphere News, caso tenha alguma dúvida,
        sugestão ou quiser compartilhar algo, não hesite em nos contatar!
    </h2>

    <section class="contato">

        <?php if ($sucesso): ?>
            <p role="status" style="color: #4caf50; margin-bottom: 20px;">
                Sua mensagem foi enviada com sucesso!
            </p>
        <?php endif; ?>

        <?php if ($erro !== ""): ?>
            <p role="alert" style="color: #ff5555; margin-bottom: 20px;">
                <?php echo htmlspecialchars($erro, ENT_QUOTES, "UTF-8"); ?>
            </p>
        <?php endif; ?>

        <form class="form-contato" method="POST" action="contato.php">

            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION["csrf_contato"], ENT_QUOTES, "UTF-8"); ?>"
            >

            <!-- Campo invisível para filtragem básica de bots -->
            <div style="position: absolute; left: -9999px;" aria-hidden="true">
                <label for="website">Não preencha este campo</label>
                <input
                    type="text"
                    id="website"
                    name="website"
                    tabindex="-1"
                    autocomplete="off"
                >
            </div>

            <label for="nome">Nome</label>
            <input
                type="text"
                id="nome"
                name="nome"
                placeholder="Digite seu nome"
                maxlength="50"
                value="<?php echo htmlspecialchars($nome, ENT_QUOTES, "UTF-8"); ?>"
                required
            >

            <label for="email">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Digite seu e-mail"
                maxlength="100"
                value="<?php echo htmlspecialchars($email, ENT_QUOTES, "UTF-8"); ?>"
                required
            >

            <label for="assunto">Assunto</label>
            <input
                type="text"
                id="assunto"
                name="assunto"
                placeholder="Assunto da mensagem"
                maxlength="100"
                value="<?php echo htmlspecialchars($assunto, ENT_QUOTES, "UTF-8"); ?>"
                required
            >

            <label for="mensagem">Mensagem</label>
            <textarea
                id="mensagem"
                name="mensagem"
                rows="6"
                maxlength="500"
                placeholder="Digite sua mensagem"
                required
            ><?php echo htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8"); ?></textarea>

            <button type="submit" class="botao">
                Enviar
            </button>

        </form>

    </section>

    <footer>
        <p>© 2026 Game Sphere - Todos os direitos reservados</p>
    </footer>

</main>

</body>
</html>

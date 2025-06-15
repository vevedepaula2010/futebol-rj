<?php
require_once 'conexao.php';

// Inicializa a variável de erro
$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados do formulário de forma segura
    $nomeUsuario = trim($_POST['usuario'] ?? '');
    $senhaInformada = $_POST['senha'] ?? '';

    try {
        $db = new Database();
        $pdo = $db->getConnection();

        // Busca o usuário pelo nome
        $stmt = $pdo->prepare('SELECT senha FROM usuario WHERE nome = ?');
        $stmt->execute([$nomeUsuario]);
        $usuario = $stmt->fetch();

        // Verifica se o usuário existe e se a senha está correta
        if ($usuario && $senhaInformada === $usuario['senha']) {
            header('Location: page/home.php');
            exit;
        } else {
            $mensagemErro = 'Usuário ou senha inválidos. Por favor, tente novamente.';
        }
    } catch (Exception $e) {
        $mensagemErro = 'Erro ao conectar ao banco de dados. Tente novamente mais tarde.';
        // Opcional: log de erro para administradores
        // error_log($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login | Gestão de Futebol</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/login.css">
</head>
<body>
    <form class="login-container" method="post" action="">
        <img src="img/logo.png" alt="Logo do sistema" class="logo" onerror="this.style.display='none'">
        <h2>Gestão de Futebol</h2>
        <?php if (!empty($mensagemErro)): ?>
            <div class="erro-login" style="color:#c00; margin-bottom:10px; font-weight:bold;">
                <?php echo htmlspecialchars($mensagemErro); ?>
            </div>
        <?php endif; ?>
        <label for="usuario">Usuário</label>
        <input type="text" id="usuario" name="usuario" placeholder="Digite seu usuário" required autofocus>
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
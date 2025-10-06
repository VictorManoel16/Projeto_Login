<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config/conexao.php';

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        $erro = 'Preencha e-mail e senha corretamente.';
    } else {
        
        $sql = "SELECT id, nome, senha_hash FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['senha_hash'])) {
            
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            header('Location: home.php');
            exit;
        } else {
            $erro = 'E-mail ou senha inválidos.';
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login - Projeto Login</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <main class="container">
    <h1>Entrar</h1>

    <?php if ($erro): ?>
      <div class="alert"><?=htmlspecialchars($erro)?></div>
    <?php endif; ?>

    <form method="post" action="">
      <label for="email">E-mail</label>
      <input id="email" name="email" type="email" required>

      <label for="senha">Senha</label>
      <input id="senha" name="senha" type="password" required>

      <button type="submit">Entrar</button>
    </form>

    <p>Não tem conta? <a href="cadastro.php">Crie uma aqui</a>.</p>
  </main>
</body>
</html>

<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config/conexao.php';

    $nome = trim($_POST['nome'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $senha2 = $_POST['senha2'] ?? '';

    if (!$nome || !$email || !$senha || !$senha2) {
        $erro = 'Preencha todos os campos.';
    } elseif ($senha !== $senha2) {
        $erro = 'As senhas não conferem.';
    } elseif (strlen($senha) < 6) {
        $erro = 'Senha deve ter ao menos 6 caracteres.';
    } else {
       
        
        $sql = "SELECT id FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $erro = 'Já existe uma conta com esse e-mail.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nome, email, senha_hash, criado_em) VALUES (:nome, :email, :senha_hash, NOW())";
            $stmt = $pdo->prepare($sql);
            $ok = $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha_hash' => $hash
            ]);
            if ($ok) {
                $sucesso = 'Cadastro realizado com sucesso. Você já pode fazer login.';
            } else {
                $erro = 'Erro ao cadastrar. Tente novamente.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Cadastro - Projeto Login</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <main class="container">
    <h1>Cadastro</h1>

    <?php if ($erro): ?>
      <div class="alert"><?=htmlspecialchars($erro)?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
      <div class="success"><?=htmlspecialchars($sucesso)?></div>
    <?php endif; ?>

    <form method="post" action="">
      <label for="nome">Nome</label>
      <input id="nome" name="nome" type="text" required value="<?=isset($nome) ? htmlspecialchars($nome) : ''?>">

      <label for="email">E-mail</label>
      <input id="email" name="email" type="email" required value="<?=isset($email) ? htmlspecialchars($email) : ''?>">

      <label for="senha">Senha</label>
      <input id="senha" name="senha" type="password" required>

      <label for="senha2">Confirme a senha</label>
      <input id="senha2" name="senha2" type="password" required>

      <button type="submit">Criar conta</button>
    </form>

    <p>Já tem conta? <a href="index.php">Entrar</a>.</p>
  </main>
</body>
</html>

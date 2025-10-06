<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/config/conexao.php';

$stmt = $pdo->prepare("SELECT nome, email, criado_em FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Home - Projeto Login</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <main class="container">
    <h1>Bem-vindo, <?=htmlspecialchars($_SESSION['user_name'] ?? ($user['nome'] ?? 'Usuário'))?>!</h1>

    <section class="card">
      <h2>Seu perfil</h2>
      <?php if ($user): ?>
        <p><strong>Nome:</strong> <?=htmlspecialchars($user['nome'])?></p>
        <p><strong>E-mail:</strong> <?=htmlspecialchars($user['email'])?></p>
        <p><strong>Desde:</strong> <?=htmlspecialchars($user['criado_em'])?></p>
      <?php else: ?>
        <p>Informações não disponíveis.</p>
      <?php endif; ?>
    </section>

    <p><a class="btn-logout" href="logout.php">Sair</a></p>
  </main>
</body>
</html>

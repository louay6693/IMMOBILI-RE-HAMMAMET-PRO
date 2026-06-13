<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'youssef' && $password === 'youssef*0*0') {
        $_SESSION['admin'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Identifiants incorrects !';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin - Immobilière Hammamet</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #0f0c29;
      background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
      font-family: 'Segoe UI', sans-serif;
    }
    .login-box {
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 20px;
      padding: 48px 40px;
      width: 380px;
      text-align: center;
    }
    .logo { font-size: 48px; margin-bottom: 12px; }
    h2 {
      color: #fff;
      font-size: 22px;
      margin-bottom: 6px;
    }
    p.subtitle {
      color: rgba(255,255,255,0.5);
      font-size: 13px;
      margin-bottom: 32px;
    }
    .input-group {
      margin-bottom: 16px;
      text-align: left;
    }
    label {
      color: rgba(255,255,255,0.6);
      font-size: 12px;
      display: block;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    input {
      width: 100%;
      padding: 12px 16px;
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.15);
      border-radius: 10px;
      color: #fff;
      font-size: 15px;
      outline: none;
      transition: border 0.3s;
    }
    input:focus {
      border-color: rgba(255,165,100,0.6);
    }
    .error {
      background: rgba(255,80,80,0.15);
      border: 1px solid rgba(255,80,80,0.3);
      color: #ff8080;
      padding: 10px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 16px;
    }
    button {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #f093fb, #f5576c);
      border: none;
      border-radius: 10px;
      color: #fff;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 8px;
      transition: opacity 0.2s;
    }
    button:hover { opacity: 0.9; }
    .back {
      display: block;
      margin-top: 20px;
      color: rgba(255,255,255,0.4);
      font-size: 13px;
      text-decoration: none;
    }
    .back:hover { color: rgba(255,255,255,0.7); }
  </style>
</head>
<body>
<div class="login-box">
  <div class="logo">🔐</div>
  <h2>Espace Admin</h2>
  <p class="subtitle">Immobilière Hammamet PRO</p>

  <?php if ($error): ?>
    <div class="error">❌ <?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="input-group">
      <label>Nom d'utilisateur</label>
      <input type="text" name="username"  required autofocus>
    </div>
    <div class="input-group">
      <label>Mot de passe</label>
      <input type="password" name="password" placeholder="••••••••" required>
    </div>
    <button type="submit">Se connecter →</button>
  </form>

  <a href="index.php" class="back">← Retour au site</a>
</div>
</body>
</html>
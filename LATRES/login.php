<?php
session_start();
if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
require_once 'config.php';

$error   = '';
$success = '';
if (isset($_GET['registered'])) $success = '✓ Akun berhasil dibuat! Silakan login.';
if (isset($_GET['logout']))     $success = '✓ Anda berhasil logout.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (empty($email) || empty($pass)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $st = $conn->prepare("SELECT id,email,password,role FROM users WHERE email=?");
        $st->bind_param("s", $email);
        $st->execute();
        $user = $st->get_result()->fetch_assoc();

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role']  = $user['role'];
            header("Location: index.php"); exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Violet Restaurant</title>
<link rel="icon" href="LOGOUNGU.png" type="image/png"> 
<link rel="stylesheet" href="style.css">
<script>
  const t=localStorage.getItem('theme')||'dark';
  document.documentElement.setAttribute('data-theme',t);
</script>
</head>
<body>
<div class="auth-bg">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="auth-logo-icon">✦</div>
      <h1>Selamat Datang</h1>
      <p>Masuk ke akun Anda</p>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="form-group">
        <label class="label">Email</label>
        <input class="input" type="email" name="email"
               placeholder="contoh@email.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="label">Password</label>
        <input class="input" type="password" name="password" placeholder="••••••••">
      </div>
      <button type="submit" class="btn btn-primary w-full" style="margin-top:.4rem">Masuk</button>
    </form>

    <div class="auth-footer">
      Belum punya akun? <a href="register.php">Daftar di sini</a>
    </div>

    <div style="text-align:center;margin-top:1.5rem">
      <button onclick="toggleTheme()" class="theme-btn" id="themeBtn">🌙 Mode</button>
    </div>
  </div>
</div>
<script>
function toggleTheme(){
  const cur=document.documentElement.getAttribute('data-theme')||'dark';
  const next=cur==='dark'?'light':'dark';
  document.documentElement.setAttribute('data-theme',next);
  localStorage.setItem('theme',next);
  document.getElementById('themeBtn').textContent=next==='dark'?'🌙 Mode':'☀️ Mode';
}
const stored=localStorage.getItem('theme')||'dark';
document.getElementById('themeBtn').textContent=stored==='dark'?'🌙 Mode':'☀️ Mode';
</script>
</body>
</html>

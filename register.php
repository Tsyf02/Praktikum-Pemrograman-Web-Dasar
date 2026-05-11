<?php
session_start();
if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email   = trim($_POST['email'] ?? '');
    $pass    = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($email))   $errors[] = 'Email wajib diisi.';
    if (empty($pass))    $errors[] = 'Password wajib diisi.';
    if (empty($confirm)) $errors[] = 'Konfirmasi password wajib diisi.';
    if ($pass && strlen($pass) < 6)      $errors[] = 'Password minimal 6 karakter.';
    if ($pass && $confirm && $pass !== $confirm) $errors[] = 'Password dan konfirmasi tidak cocok.';

    if (empty($errors)) {
        $chk = $conn->prepare("SELECT id FROM users WHERE email=?");
        $chk->bind_param("s", $email); $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $errors[] = 'Email sudah terdaftar.';
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (email,password,role) VALUES (?,?,'user')");
            $ins->bind_param("ss", $email, $hashed);
            $ins->execute()
                ? (header("Location: login.php?registered=1") ?: exit())
                : ($errors[] = 'Gagal mendaftar. Coba lagi.');
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
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'dark')</script>
</head>
<body>
<div class="auth-bg">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="auth-logo-icon">✦</div>
      <h1>Buat Akun Baru</h1>
      <p>Bergabung dengan kami</p>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?><div>• <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="form-group">
        <label class="label">Email</label>
        <input class="input" type="email" name="email" placeholder="contoh@email.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="label">Password</label>
        <input class="input" type="password" name="password" placeholder="Min. 6 karakter">
      </div>
      <div class="form-group">
        <label class="label">Konfirmasi Password</label>
        <input class="input" type="password" name="confirm_password" placeholder="••••••••">
      </div>
      <button type="submit" class="btn btn-primary w-full" style="margin-top:.4rem">Daftar Sekarang</button>
    </form>

    <div class="auth-footer">Sudah punya akun? <a href="login.php">Login di sini</a></div>
  </div>
</div>
</body>
</html>

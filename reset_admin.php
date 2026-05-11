<?php
// ============================================
// RESET ADMIN — Jalankan sekali, lalu HAPUS!
// Akses: http://localhost/RESTOUNGU/reset_admin.php
// ============================================
require_once 'config.php';

$email    = 'talithasyifaalfath02@gmail.com';
$password = 'admin123';
$hashed   = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah tabel users ada
$tbl = $conn->query("SHOW TABLES LIKE 'users'");
if ($tbl->num_rows === 0) {
    // Buat semua tabel kalau belum ada
    $conn->multi_query("
        CREATE TABLE IF NOT EXISTS users (
            id       INT(11) AUTO_INCREMENT PRIMARY KEY,
            email    VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role     ENUM('admin','user') DEFAULT 'user'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS menu (
            id_menu   INT(11) AUTO_INCREMENT PRIMARY KEY,
            nama      VARCHAR(100) NOT NULL,
            deskripsi TEXT,
            harga     INT(11) NOT NULL,
            kategori  ENUM('Makanan','Minuman','Dessert') NOT NULL,
            gambar    VARCHAR(255)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS reservasi (
            id           INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id      INT(11) NOT NULL,
            nama         VARCHAR(100) NOT NULL,
            tanggal      DATE NOT NULL,
            jam          TIME NOT NULL,
            jumlah_orang INT(11) NOT NULL DEFAULT 1,
            catatan      TEXT,
            status       ENUM('pending','konfirmasi','batal') DEFAULT 'pending',
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS reservasi_menu (
            id           INT(11) AUTO_INCREMENT PRIMARY KEY,
            reservasi_id INT(11) NOT NULL,
            menu_id      INT(11) NOT NULL,
            jumlah       INT(11) DEFAULT 1,
            FOREIGN KEY (reservasi_id) REFERENCES reservasi(id) ON DELETE CASCADE,
            FOREIGN KEY (menu_id)      REFERENCES menu(id_menu) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    // flush results
    while ($conn->more_results()) $conn->next_result();
    echo "<p style='color:orange'>⚠️ Tabel belum ada — sudah dibuat otomatis!</p>";
}

// Hapus akun lama kalau ada, lalu buat baru
$conn->query("DELETE FROM users WHERE email='$email'");
$st = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'admin')");
$st->bind_param("ss", $email, $hashed);
$ok = $st->execute();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Reset Admin</title>
<style>
  body { font-family: sans-serif; background: #09060f; color: #f3e8ff;
         display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
  .box { background: rgba(139,92,246,.1); border: 1px solid rgba(139,92,246,.4);
         border-radius: 16px; padding: 2.5rem; max-width: 420px; width:90%; text-align:center; }
  h2  { color: #a855f7; margin-bottom: 1rem; }
  .ok { background: rgba(52,211,153,.15); border: 1px solid rgba(52,211,153,.4);
        color: #34d399; border-radius: 8px; padding: 1rem; margin: 1rem 0; }
  .cred { background: rgba(139,92,246,.15); border: 1px solid rgba(139,92,246,.3);
          border-radius: 8px; padding: 1rem; margin: 1rem 0; text-align:left; }
  .cred strong { color: #a855f7; }
  .warn { background: rgba(248,113,113,.15); border: 1px solid rgba(248,113,113,.3);
          color: #f87171; border-radius: 8px; padding: .8rem; margin-top: 1rem;
          font-size: .85rem; }
  a { display:inline-block; margin-top:1.2rem; padding: 10px 24px;
      background: #a855f7; color: #fff; border-radius: 8px; text-decoration:none;
      font-weight: 600; }
  a:hover { background: #9333ea; }
</style>
</head>
<body>
<div class="box">
  <h2>🔧 Reset Admin</h2>

  <?php if ($ok): ?>
    <div class="ok">✅ Akun admin berhasil dibuat / direset!</div>
    <div class="cred">
      <div style="margin-bottom:.5rem"><strong>Email:</strong><br>talithasyifaalfath02@gmail.com</div>
      <div><strong>Password:</strong><br>admin123</div>
    </div>
    <a href="login.php">→ Login Sekarang</a>
    <div class="warn">
      ⚠️ <strong>PENTING:</strong> Setelah berhasil login,<br>
      <strong>HAPUS file reset_admin.php ini!</strong>
    </div>
  <?php else: ?>
    <div class="warn">❌ Gagal membuat akun. Periksa koneksi database di config.php</div>
    <p style="color:rgba(243,232,255,.5);font-size:.85rem">Error: <?= $conn->error ?></p>
  <?php endif; ?>
</div>
</body>
</html>

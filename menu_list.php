<?php
require_once 'auth.php';
require_once 'config.php';

// Handle delete
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $img = $conn->query("SELECT gambar FROM menu WHERE id_menu=$did")->fetch_assoc()['gambar'] ?? '';
    if ($img && file_exists("uploads/$img")) unlink("uploads/$img");
    $conn->query("DELETE FROM menu WHERE id_menu=$did");
    header("Location: menu_list.php?deleted=1"); exit;
}

$menus = $conn->query("SELECT * FROM menu ORDER BY kategori,nama")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Violet Restaurant</title>
<link rel="icon" href="LOGOUNGU.png" type="image/png"> 
<link rel="stylesheet" href="style.css">
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'dark')</script>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page">
  <div class="container">

    <div class="page-header">
      <h1 class="page-title">Daftar Menu</h1>
      <div class="flex gap-3 flex-wrap">
        <a href="index.php" class="btn btn-ghost btn-sm">← Dashboard</a>
        <a href="menu_add.php" class="btn btn-primary btn-sm">＋ Tambah Menu</a>
      </div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="alert alert-success">✓ Menu berhasil dihapus.</div>
    <?php endif; ?>
    <?php if (isset($_GET['saved'])): ?>
      <div class="alert alert-success">✓ Menu berhasil disimpan.</div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body" style="padding:0">
        <div class="tbl-wrap">
          <table>
            <thead>
              <tr>
                <th>No</th><th>Gambar</th><th>Nama</th><th>Deskripsi</th><th>Kategori</th><th>Harga</th><th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($menus)): ?>
                <tr><td colspan="7" style="text-align:center;padding:2.5rem;color:var(--text-muted)">
                  Belum ada menu. <a href="menu_add.php" style="color:var(--primary)">Tambah sekarang</a>.
                </td></tr>
              <?php else: foreach ($menus as $i=>$m): ?>
                <tr>
                  <td style="color:var(--text-muted)"><?= $i+1 ?></td>
                  <td>
                    <div style="width:44px;height:44px;border-radius:8px;overflow:hidden;background:rgba(139,92,246,.1);display:flex;align-items:center;justify-content:center;font-size:1.2rem">
                      <?php if ($m['gambar'] && file_exists('uploads/'.$m['gambar'])): ?>
                        <img src="uploads/<?= htmlspecialchars($m['gambar']) ?>" style="width:100%;height:100%;object-fit:cover">
                      <?php else: echo '🍽'; endif; ?>
                    </div>
                  </td>
                  <td><strong><?= htmlspecialchars($m['nama']) ?></strong></td>
                  <td style="max-width:200px;color:var(--text-muted);font-size:.84rem">
                    <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($m['deskripsi']) ?></div>
                  </td>
                  <td><span class="badge badge-purple"><?= $m['kategori'] ?></span></td>
                  <td style="font-weight:700;color:var(--primary)">Rp <?= number_format($m['harga'],0,',','.') ?></td>
                  <td>
                    <div class="flex gap-2">
                      <a href="menu_edit.php?id=<?= $m['id_menu'] ?>" class="btn btn-ghost btn-xs">✏ Edit</a>
                      <a href="menu_list.php?delete=<?= $m['id_menu'] ?>"
                         onclick="return confirm('Hapus menu <?= htmlspecialchars(addslashes($m['nama'])) ?>?')"
                         class="btn btn-danger btn-xs">🗑 Hapus</a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
</body>
</html>

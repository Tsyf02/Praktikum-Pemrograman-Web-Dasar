<?php
require_once 'auth.php';
require_once 'config.php';

$uid    = $_SESSION['user_id'];
$uemail = $_SESSION['user_email'];
$errors = [];

$menus_raw   = $conn->query("SELECT * FROM menu ORDER BY kategori,nama")->fetch_all(MYSQLI_ASSOC);
$menus_by_cat = [];
foreach ($menus_raw as $m) $menus_by_cat[$m['kategori']][] = $m;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama']        ?? '');
    $tanggal = $_POST['tanggal']           ?? '';
    $jam     = $_POST['jam']               ?? '';
    $jumlah  = (int)($_POST['jumlah_orang']?? 0);
    $catatan = trim($_POST['catatan']      ?? '');
    $mids    = $_POST['menu_ids']          ?? [];
    $mqtys   = $_POST['menu_qty']          ?? [];

    if (!$nama)     $errors[] = 'Nama pemesan wajib diisi.';
    if (!$tanggal)  $errors[] = 'Tanggal wajib diisi.';
    if (!$jam)      $errors[] = 'Jam wajib diisi.';
    if ($jumlah<1)  $errors[] = 'Jumlah orang minimal 1.';

    if (empty($errors)) {
        $st = $conn->prepare("INSERT INTO reservasi (user_id,nama,tanggal,jam,jumlah_orang,catatan,status) VALUES (?,?,?,?,?,?,'pending')");
        $st->bind_param("isssis",$uid,$nama,$tanggal,$jam,$jumlah,$catatan);
        if ($st->execute()) {
            $rid = $conn->insert_id;
            if ($mids) {
                $ins = $conn->prepare("INSERT INTO reservasi_menu (reservasi_id,menu_id,jumlah) VALUES (?,?,?)");
                foreach ($mids as $mid) {
                    $q = max(1,(int)($mqtys[$mid]??1));
                    $ins->bind_param("iii",$rid,$mid,$q); $ins->execute();
                }
            }
            header("Location: index.php#reservasi"); exit;
        } else $errors[] = 'Gagal menyimpan. Coba lagi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Buat Reservasi — Violet Restaurant</title>
<link rel="icon" href="LOGOUNGU.png" type="image/png"> 
<link rel="stylesheet" href="style.css">
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'dark')</script>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page">
  <div class="container" style="max-width:880px">

    <div class="page-header">
      <h1 class="page-title">Buat Reservasi</h1>
      <a href="index.php#reservasi" class="btn btn-ghost btn-sm">← Kembali</a>
    </div>

    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php foreach($errors as $e) echo "<div>• ".htmlspecialchars($e)."</div>"; ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <form method="POST">

          <!-- KEDATANGAN -->
          <div class="section-divider">🛎 Kedatangan</div>
          <div class="form-row">
            <div class="form-group">
              <label class="label">Nama Pemesan</label>
              <input class="input" type="text" name="nama" placeholder="Nama lengkap Anda"
                     value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="label">Jumlah Orang</label>
              <input class="input" type="number" name="jumlah_orang" min="1" max="50"
                     value="<?= htmlspecialchars($_POST['jumlah_orang'] ?? '') ?>"
                     placeholder="1" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="label">Tanggal</label>
              <input class="input" type="date" name="tanggal"
                     value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>"
                     min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
              <label class="label">Jam (AM/PM format)</label>
              <input class="input" type="time" name="jam"
                     value="<?= htmlspecialchars($_POST['jam'] ?? '') ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label class="label">Email Akun (otomatis dari sesi)</label>
            <input class="input" type="email" value="<?= htmlspecialchars($uemail) ?>" readonly>
          </div>

          <div class="form-group">
            <label class="label">Catatan (Opsional)</label>
            <textarea class="input" name="catatan" rows="3"
                      placeholder="Permintaan khusus, alergi, dll…"><?= htmlspecialchars($_POST['catatan'] ?? '') ?></textarea>
          </div>

          <!-- PILIHAN MENU -->
          <?php if ($menus_by_cat): ?>
          <div class="divider"></div>
          <div class="section-divider" style="display:flex;justify-content:space-between;align-items:center">
            <span>🍽 Pilihan Menu</span>
            <span style="font-size:.7rem;color:var(--text-muted);text-transform:none;letter-spacing:0;font-weight:400">Pilih menu untuk pre-order</span>
          </div>

          <?php foreach ($menus_by_cat as $cat => $items): ?>
            <div style="margin-bottom:1.2rem">
              <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);font-weight:600;margin-bottom:.5rem"><?= $cat ?></div>
              <div class="ms-grid">
                <?php foreach ($items as $m):
                  $checked = in_array($m['id_menu'],(array)($_POST['menu_ids']??[]));
                ?>
                <div class="ms-item" style="position:relative">
                  <input type="checkbox" name="menu_ids[]" id="m<?= $m['id_menu'] ?>" value="<?= $m['id_menu'] ?>" <?= $checked?'checked':'' ?>>
                  <label class="ms-label" for="m<?= $m['id_menu'] ?>">
                    <div class="ms-img">
                      <?php if ($m['gambar'] && file_exists('uploads/'.$m['gambar'])): ?>
                        <img src="uploads/<?= htmlspecialchars($m['gambar']) ?>" alt="">
                      <?php else: echo '🍽'; endif; ?>
                    </div>
                    <div class="ms-info">
                      <div class="ms-name"><?= htmlspecialchars($m['nama']) ?></div>
                      <div class="ms-price">Rp <?= number_format($m['harga'],0,',','.') ?></div>
                    </div>
                  </label>
                  <div class="ms-qty" id="q<?= $m['id_menu'] ?>" style="<?= $checked?'display:block':'' ?>">
                    <input type="number" name="menu_qty[<?= $m['id_menu'] ?>]"
                           class="input" value="1" min="1" max="20"
                           style="padding:5px 8px;font-size:.78rem">
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
          <?php endif; ?>

          <div class="divider"></div>
          <div class="flex justify-between items-center flex-wrap gap-3">
            <a href="index.php#reservasi" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Konfirmasi Reservasi →</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
<script>
document.querySelectorAll('.ms-item input[type=checkbox]').forEach(cb=>{
  cb.addEventListener('change',function(){
    const q=document.getElementById('q'+this.value);
    if(q)q.style.display=this.checked?'block':'none';
  });
});
</script>
</body>
</html>

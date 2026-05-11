<?php
require_once 'auth.php';
require_once 'config.php';

$id     = (int)($_GET['id'] ?? 0);
$errors = [];

// ── Load data menu ────────────────────────────────
$st = $conn->prepare("SELECT * FROM menu WHERE id_menu=?");
$st->bind_param("i", $id);
$st->execute();
$menu = $st->get_result()->fetch_assoc();

// Kalau menu tidak ditemukan, kembali ke list
if (!$menu) {
    header("Location: menu_list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama']      ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga     = (int)($_POST['harga']    ?? 0);
    $kategori  = $_POST['kategori']       ?? '';

    // ── Validasi ─────────────────────────────────
    if (empty($nama))  $errors[] = 'Nama menu wajib diisi.';
    if ($harga <= 0)   $errors[] = 'Harga harus lebih dari 0.';
    if (!in_array($kategori, ['Makanan','Minuman','Dessert']))
        $errors[] = 'Kategori wajib dipilih.';

    // ── Proses upload gambar baru (opsional) ─────
    $gambar_name = $menu['gambar']; // default: gambar lama

    if (!empty($_FILES['gambar']['name'])) {
        $allowed = ['jpg','jpeg','png','webp','gif'];
        $ext     = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format gambar tidak didukung. Gunakan jpg/png/webp/gif.';
        } elseif ($_FILES['gambar']['size'] > 3 * 1024 * 1024) {
            $errors[] = 'Ukuran gambar maksimal 3MB.';
        } else {
            $gambar_name = uniqid('menu_') . '.' . $ext;
        }
    }

    if (empty($errors)) {
        // Upload file baru kalau ada
        if (!empty($_FILES['gambar']['name']) && $gambar_name !== $menu['gambar']) {
            if (!is_dir('uploads')) mkdir('uploads', 0755, true);
            // Hapus gambar lama
            if ($menu['gambar'] && file_exists("uploads/{$menu['gambar']}")) {
                unlink("uploads/{$menu['gambar']}");
            }
            move_uploaded_file($_FILES['gambar']['tmp_name'], "uploads/$gambar_name");
        }

        $upd = $conn->prepare("UPDATE menu SET nama=?,deskripsi=?,harga=?,kategori=?,gambar=? WHERE id_menu=?");
        $upd->bind_param("ssissi", $nama, $deskripsi, $harga, $kategori, $gambar_name, $id);

        if ($upd->execute()) {
            header("Location: menu_list.php?saved=1"); exit;
        } else {
            $errors[] = 'Gagal memperbarui menu. Coba lagi.';
        }
    }

    // Update local untuk re-display form
    $menu['nama']      = $nama;
    $menu['deskripsi'] = $deskripsi;
    $menu['harga']     = $harga;
    $menu['kategori']  = $kategori;
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Menu — Violet Restaurant</title>
<link rel="icon" href="LOGOUNGU.png" type="image/png">
<link rel="stylesheet" href="style.css">
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'dark')</script>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page">
  <div class="container" style="max-width:540px">

    <div class="page-header">
      <h1 class="page-title">Edit Menu</h1>
      <a href="menu_list.php" class="btn btn-ghost btn-sm">← Kembali</a>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?>
          <div>• <?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <form method="POST" enctype="multipart/form-data">

          <div class="form-group">
            <label class="label">Nama Menu *</label>
            <input class="input" type="text" name="nama"
                   value="<?= htmlspecialchars($menu['nama']) ?>" required>
          </div>

          <div class="form-group">
            <label class="label">Deskripsi</label>
            <textarea class="input" name="deskripsi" rows="3"><?= htmlspecialchars($menu['deskripsi']) ?></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="label">Harga (Rp) *</label>
              <input class="input" type="number" name="harga"
                     value="<?= $menu['harga'] ?>" min="1" required>
            </div>
            <div class="form-group">
              <label class="label">Kategori *</label>
              <select class="input" name="kategori" required>
                <?php foreach (['Makanan','Minuman','Dessert'] as $k): ?>
                  <option value="<?= $k ?>"
                    <?= ($menu['kategori'] === $k) ? 'selected' : '' ?>>
                    <?= $k ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="label">Gambar Menu (opsional — kosongkan jika tidak ingin ganti)</label>
            <input class="input" type="file" name="gambar"
                   accept="image/*" style="padding:8px"
                   onchange="previewNew(this)">
            <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px">
              Format: JPG, PNG, WEBP, GIF &nbsp;|&nbsp; Maks: 3MB
            </div>

            <!-- Gambar saat ini -->
            <div style="margin-top:10px">
              <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:5px">Gambar saat ini:</div>
              <div id="currentImg">
                <?php if ($menu['gambar'] && file_exists('uploads/'.$menu['gambar'])): ?>
                  <img id="imgPreview"
                       src="uploads/<?= htmlspecialchars($menu['gambar']) ?>"
                       style="max-width:140px;max-height:140px;border-radius:var(--radius);
                              border:1px solid var(--border);object-fit:cover">
                <?php else: ?>
                  <div style="width:80px;height:80px;border-radius:var(--radius);
                              background:rgba(139,92,246,.12);border:1px solid var(--border);
                              display:flex;align-items:center;justify-content:center;font-size:1.8rem">
                    🍽
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="divider"></div>

          <div class="flex justify-between items-center">
            <a href="menu_list.php" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
          </div>

        </form>
      </div>
    </div>

  </div>
</div>

<script>
// Preview gambar baru sebelum upload
function previewNew(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      let img = document.getElementById('imgPreview');
      if (!img) {
        // Kalau belum ada elemen img, buat baru
        img = document.createElement('img');
        img.id = 'imgPreview';
        img.style.cssText = 'max-width:140px;max-height:140px;border-radius:var(--radius);border:1px solid var(--border);object-fit:cover';
        document.getElementById('currentImg').innerHTML = '';
        document.getElementById('currentImg').appendChild(img);
      }
      img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

</body>
</html>

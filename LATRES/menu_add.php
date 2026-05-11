<?php
require_once 'auth.php';
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama']      ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga     = (int)($_POST['harga']    ?? 0);
    $kategori  = $_POST['kategori']       ?? '';

    // ── Validasi wajib ───────────────────────────
    if (empty($nama))     $errors[] = 'Nama menu wajib diisi.';
    if ($harga <= 0)      $errors[] = 'Harga harus lebih dari 0.';
    if (!in_array($kategori, ['Makanan','Minuman','Dessert']))
        $errors[] = 'Kategori wajib dipilih.';

    // ── Upload gambar ────────────────────────────
    $gambar_name = '';
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
        if ($gambar_name) {
            // Buat folder uploads kalau belum ada
            if (!is_dir('uploads')) mkdir('uploads', 0755, true);
            move_uploaded_file($_FILES['gambar']['tmp_name'], "uploads/$gambar_name");
        }

        $st = $conn->prepare("INSERT INTO menu (nama,deskripsi,harga,kategori,gambar) VALUES (?,?,?,?,?)");
        $st->bind_param("ssiss", $nama, $deskripsi, $harga, $kategori, $gambar_name);

        if ($st->execute()) {
            header("Location: menu_list.php?saved=1"); exit;
        } else {
            $errors[] = 'Gagal menyimpan menu. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tambah Menu — Violet Restaurant</title>
<link rel="icon" href="LOGOUNGU.png" type="image/png">
<link rel="stylesheet" href="style.css">
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'dark')</script>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page">
  <div class="container" style="max-width:540px">

    <div class="page-header">
      <h1 class="page-title">Tambah Menu</h1>
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
                   placeholder="Contoh: Nasi Goreng Spesial"
                   value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label class="label">Deskripsi</label>
            <textarea class="input" name="deskripsi" rows="3"
                      placeholder="Deskripsi singkat menu..."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="label">Harga (Rp) *</label>
              <input class="input" type="number" name="harga"
                     placeholder="25000" min="1"
                     value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="label">Kategori *</label>
              <select class="input" name="kategori" required>
                <option value="">— Pilih Kategori —</option>
                <?php foreach (['Makanan','Minuman','Dessert'] as $k): ?>
                  <option value="<?= $k ?>"
                    <?= (($_POST['kategori'] ?? '') === $k) ? 'selected' : '' ?>>
                    <?= $k ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="label">Gambar Menu</label>
            <input class="input" type="file" name="gambar"
                   accept="image/*" style="padding:8px"
                   onchange="previewImg(this)">
            <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px">
              Format: JPG, PNG, WEBP, GIF &nbsp;|&nbsp; Maks: 3MB
            </div>
            <!-- Preview gambar sebelum upload -->
            <div id="previewWrap" style="display:none;margin-top:10px">
              <img id="previewImg"
                   style="max-width:140px;max-height:140px;border-radius:var(--radius);
                          border:1px solid var(--border);object-fit:cover">
            </div>
          </div>

          <div class="divider"></div>

          <div class="flex justify-between items-center">
            <a href="menu_list.php" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Menu</button>
          </div>

        </form>
      </div>
    </div>

  </div>
</div>

<script>
function previewImg(input) {
  const wrap = document.getElementById('previewWrap');
  const img  = document.getElementById('previewImg');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      img.src = e.target.result;
      wrap.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

</body>
</html>

<?php
require_once 'auth.php';
require_once 'config.php';

$id   = (int)($_GET['id'] ?? 0);
$uid  = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$errors = [];

$st = $conn->prepare("SELECT r.*,u.email ue FROM reservasi r JOIN users u ON r.user_id=u.id WHERE r.id=?");
$st->bind_param("i",$id); $st->execute();
$res = $st->get_result()->fetch_assoc();
if (!$res || ($role!=='admin' && $res['user_id']!=$uid)) { header("Location: index.php"); exit; }

$menus_raw = $conn->query("SELECT * FROM menu ORDER BY kategori,nama")->fetch_all(MYSQLI_ASSOC);
$mbc = [];
foreach ($menus_raw as $m) $mbc[$m['kategori']][] = $m;

$curQ = $conn->prepare("SELECT menu_id,jumlah FROM reservasi_menu WHERE reservasi_id=?");
$curQ->bind_param("i",$id); $curQ->execute();
$cur = array_column($curQ->get_result()->fetch_all(MYSQLI_ASSOC),'jumlah','menu_id');

$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama']        ?? '');
    $tanggal = $_POST['tanggal']           ?? '';
    $jam     = $_POST['jam']               ?? '';
    $jumlah  = (int)($_POST['jumlah_orang']?? 0);
    $catatan = trim($_POST['catatan']      ?? '');
    $status  = $_POST['status']            ?? 'pending';
    $mids    = $_POST['menu_ids']          ?? [];
    $mqtys   = $_POST['menu_qty']          ?? [];

    if (!$nama)    $errors[] = 'Nama wajib diisi.';
    if (!$tanggal) $errors[] = 'Tanggal wajib diisi.';
    if (!$jam)     $errors[] = 'Jam wajib diisi.';
    if ($jumlah<1) $errors[] = 'Jumlah orang minimal 1.';
    if (!in_array($status,['pending','konfirmasi','batal'])) $status='pending';

    if (empty($errors)) {
        $upd = $conn->prepare("UPDATE reservasi SET nama=?,tanggal=?,jam=?,jumlah_orang=?,catatan=?,status=? WHERE id=?");
        $upd->bind_param("sssissi",$nama,$tanggal,$jam,$jumlah,$catatan,$status,$id);
        if ($upd->execute()) {
            $del = $conn->prepare("DELETE FROM reservasi_menu WHERE reservasi_id=?");
            $del->bind_param("i",$id); $del->execute();
            if ($mids) {
                $ins = $conn->prepare("INSERT INTO reservasi_menu (reservasi_id,menu_id,jumlah) VALUES (?,?,?)");
                foreach ($mids as $mid) { $q=max(1,(int)($mqtys[$mid]??1)); $ins->bind_param("iii",$id,$mid,$q); $ins->execute(); }
            }
            $saved = true;
            $cur = []; foreach ($mids as $mid) $cur[$mid]=max(1,(int)($mqtys[$mid]??1));
            $res = array_merge($res,compact('nama','tanggal','jam','jumlah','catatan','status'));
            $res['jumlah_orang']=$jumlah;
        } else $errors[]='Gagal menyimpan.';
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Reservasi #<?= $id ?> — Violet Restaurant</title>
<link rel="icon" href="LOGOUNGU.png" type="image/png">
<link rel="stylesheet" href="style.css">
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'dark')</script>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page">
  <div class="container" style="max-width:940px">

    <div class="page-header">
      <div>
        <h1 class="page-title">Edit Reservasi <span style="color:var(--primary)">#<?= $id ?></span></h1>
        <div style="font-size:.8rem;color:var(--text-muted)">Terakhir diperbarui: <?= date('d M Y') ?></div>
      </div>
      <a href="index.php#reservasi" class="btn btn-ghost btn-sm">← Dashboard</a>
    </div>

    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php foreach($errors as $e) echo "<div>• ".htmlspecialchars($e)."</div>"; ?></div>
    <?php endif; ?>
    <?php if ($saved): ?>
      <div class="alert alert-success">✓ Perubahan berhasil disimpan.</div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <form method="POST">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem">

            <!-- LEFT -->
            <div>
              <div class="section-divider">👤 Detail Tamu</div>
              <div class="form-group">
                <label class="label">Nama Lengkap</label>
                <input class="input" type="text" name="nama" value="<?= htmlspecialchars($res['nama']) ?>" required>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="label">Tanggal</label>
                  <input class="input" type="date" name="tanggal" value="<?= $res['tanggal'] ?>" required>
                </div>
                <div class="form-group">
                  <label class="label">Jam</label>
                  <input class="input" type="time" name="jam" value="<?= date('H:i',strtotime($res['jam'])) ?>" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="label">Jumlah Tamu</label>
                  <input class="input" type="number" name="jumlah_orang" value="<?= $res['jumlah_orang'] ?>" min="1" required>
                </div>
                <div class="form-group">
                  <label class="label">Status</label>
                  <select class="input" name="status">
                    <?php foreach(['pending','konfirmasi','batal'] as $s): ?>
                      <option value="<?= $s ?>" <?= $res['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="label">Catatan</label>
                <textarea class="input" name="catatan" rows="4"><?= htmlspecialchars($res['catatan']) ?></textarea>
              </div>
            </div>

            <!-- RIGHT -->
            <div>
              <div class="section-divider">🍽 Menu yang Dipesan</div>
              <?php foreach ($mbc as $cat => $items): ?>
                <div style="margin-bottom:1rem">
                  <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);font-weight:600;margin-bottom:.5rem"><?= $cat ?></div>
                  <div class="ms-grid" style="grid-template-columns:repeat(auto-fill,minmax(110px,1fr))">
                    <?php foreach ($items as $m):
                      $checked = isset($cur[$m['id_menu']]);
                    ?>
                    <div class="ms-item" style="position:relative">
                      <input type="checkbox" name="menu_ids[]" id="em<?= $m['id_menu'] ?>" value="<?= $m['id_menu'] ?>" <?= $checked?'checked':'' ?>>
                      <label class="ms-label" for="em<?= $m['id_menu'] ?>">
                        <div class="ms-img" style="height:60px">
                          <?php if ($m['gambar'] && file_exists('uploads/'.$m['gambar'])): ?>
                           <a class="navbar-brand" href="index.php">
    <img src="assets/images/LOGOUNGU.png" alt="Violet Restaurant Logo">
</a>
                          <?php else: echo '🍽'; endif; ?>
                        </div>
                        <div class="ms-info">
                          <div class="ms-name" style="font-size:.72rem"><?= htmlspecialchars($m['nama']) ?></div>
                          <div class="ms-price" style="font-size:.68rem">Rp <?= number_format($m['harga'],0,',','.') ?></div>
                        </div>
                      </label>
                      <div id="eq<?= $m['id_menu'] ?>" style="margin-top:4px;<?= $checked?'':'display:none' ?>">
                        <input type="number" name="menu_qty[<?= $m['id_menu'] ?>]"
                               value="<?= $cur[$m['id_menu']] ?? 1 ?>"
                               class="input" min="1" max="20"
                               style="padding:4px 6px;font-size:.72rem">
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
              <div style="font-size:.8rem;color:var(--text-muted);margin-top:.5rem">
                Item terpilih: <strong style="color:var(--primary)" id="selNum"><?= count($cur) ?></strong> menu
              </div>
            </div>
          </div>

          <div class="divider"></div>
          <div class="flex justify-between items-center">
            <a href="index.php#reservasi" class="btn btn-ghost">← Kembali</a>
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
<script>
const cbs=document.querySelectorAll('.ms-item input[type=checkbox]');
function upd(){document.getElementById('selNum').textContent=document.querySelectorAll('.ms-item input:checked').length}
cbs.forEach(cb=>cb.addEventListener('change',function(){
  const q=document.getElementById('eq'+this.value);
  if(q)q.style.display=this.checked?'block':'none';
  upd();
}));
</script>
</body>
</html>

<?php
require_once 'auth.php';
require_once 'config.php';

$id   = (int)($_GET['id'] ?? 0);
$uid  = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

$st = $conn->prepare("SELECT r.*,u.email ue FROM reservasi r JOIN users u ON r.user_id=u.id WHERE r.id=?");
$st->bind_param("i",$id); $st->execute();
$res = $st->get_result()->fetch_assoc();
if (!$res || ($role!=='admin' && $res['user_id']!=$uid)) { header("Location: index.php"); exit; }

$mq = $conn->prepare("SELECT m.nama,m.harga,m.kategori,m.gambar,rm.jumlah FROM reservasi_menu rm JOIN menu m ON rm.menu_id=m.id_menu WHERE rm.reservasi_id=?");
$mq->bind_param("i",$id); $mq->execute();
$ordered = $mq->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Detail Reservasi #<?= $id ?> — Violet Restaurant</title>
<link rel="icon" href="LOGOUNGU.png" type="image/png">
<link rel="stylesheet" href="style.css">
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'dark')</script>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page">
  <div class="container" style="max-width:680px">

    <div class="page-header">
      <h1 class="page-title">Detail Reservasi</h1>
      <a href="index.php#reservasi" class="btn btn-ghost btn-sm">← Dashboard</a>
    </div>

    <div class="card">
      <div class="card-body">
        <!-- Header -->
        <div class="flex justify-between items-center flex-wrap gap-3" style="margin-bottom:1.5rem">
          <div>
            <div style="font-family:var(--font-display);font-size:1.4rem;font-weight:600;margin-bottom:3px">
              Detail Reservasi
            </div>
            <div style="font-size:.82rem;color:var(--text-muted)">
              ID #<?= $id ?> &nbsp;•&nbsp; <?= htmlspecialchars($res['ue']) ?>
            </div>
          </div>
          <span class="badge badge-<?= $res['status'] ?>" style="font-size:.8rem;padding:5px 16px">
            <?= strtoupper($res['status']) ?>
          </span>
        </div>

        <!-- Meta -->
        <div class="detail-metas">
          <div class="detail-meta">
            <div class="dm-label">Pemesan</div>
            <div class="dm-val"><?= htmlspecialchars($res['nama']) ?></div>
          </div>
          <div class="detail-meta">
            <div class="dm-label">Tamu</div>
            <div class="dm-val"><?= $res['jumlah_orang'] ?> Orang</div>
          </div>
          <div class="detail-meta">
            <div class="dm-label">Tanggal</div>
            <div class="dm-val"><?= date('d M Y',strtotime($res['tanggal'])) ?></div>
          </div>
          <div class="detail-meta">
            <div class="dm-label">Waktu</div>
            <div class="dm-val"><?= date('H:i',strtotime($res['jam'])) ?> WIB</div>
          </div>
        </div>

        <!-- Catatan -->
        <div style="margin-bottom:1.5rem">
          <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--primary);font-weight:600;margin-bottom:8px">📝 Catatan Khusus</div>
          <div style="background:rgba(139,92,246,.07);border:1px solid var(--border);border-radius:var(--radius);padding:12px;font-size:.9rem;color:<?= $res['catatan']?'var(--text)':'var(--text-muted)' ?>">
            <?= $res['catatan'] ? htmlspecialchars($res['catatan']) : '(tidak ada catatan)' ?>
          </div>
        </div>

        <!-- Menus -->
        <div>
          <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--primary);font-weight:600;margin-bottom:10px">🍽 Pesanan Menu</div>
          <?php if (empty($ordered)): ?>
            <div style="background:rgba(139,92,246,.05);border:1px solid var(--border);border-radius:var(--radius);padding:2rem;text-align:center">
              <div style="font-size:2rem;margin-bottom:.5rem">🛒</div>
              <div style="color:var(--text-muted);font-size:.88rem">Tidak ada menu yang dipesan.</div>
            </div>
          <?php else: ?>
            <div style="border:1px solid var(--border);border-radius:var(--radius);overflow:hidden">
              <?php $total=0; foreach ($ordered as $m): $sub=$m['harga']*$m['jumlah']; $total+=$sub; ?>
                <div style="display:flex;align-items:center;gap:12px;padding:11px 14px;border-bottom:1px solid rgba(139,92,246,.1)">
                  <div style="width:44px;height:44px;border-radius:8px;overflow:hidden;background:rgba(139,92,246,.12);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.2rem">
                    <?php if ($m['gambar'] && file_exists('uploads/'.$m['gambar'])): ?>
                      <a class="navbar-brand" href="index.php">
    <img src="assets/images/LOGOUNGU.png" alt="Violet Restaurant Logo">
</a>
                    <?php else: echo '🍽'; endif; ?>
                  </div>
                  <div style="flex:1">
                    <div style="font-weight:600;font-size:.88rem"><?= htmlspecialchars($m['nama']) ?></div>
                    <div style="font-size:.72rem;color:var(--primary)"><?= $m['kategori'] ?></div>
                  </div>
                  <div style="text-align:right">
                    <div style="font-size:.78rem;color:var(--text-muted)">Rp <?= number_format($m['harga'],0,',','.') ?> × <?= $m['jumlah'] ?></div>
                    <div style="font-weight:700;color:var(--primary);font-size:.9rem">Rp <?= number_format($sub,0,',','.') ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 15px;background:rgba(139,92,246,.08)">
                <div style="font-size:.85rem;color:var(--text-muted)">Total Estimasi</div>
                <div style="font-size:1.05rem;font-weight:700;color:var(--primary)">Rp <?= number_format($total,0,',','.') ?></div>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 flex-wrap" style="margin-top:1.5rem">
          <a href="index.php#reservasi" class="btn btn-ghost">← Kembali</a>
          <a href="reservation_edit.php?id=<?= $id ?>" class="btn btn-primary">✏ Edit Reservasi</a>
        </div>
      </div>
    </div>

  </div>
</div>
</body>
</html>

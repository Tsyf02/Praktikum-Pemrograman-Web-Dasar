<?php
require_once 'auth.php';
require_once 'config.php';

$is_index   = true;
$uid        = $_SESSION['user_id'];
$uemail     = $_SESSION['user_email'];
$role       = $_SESSION['user_role'] ?? 'user';
$msg_del    = $_SESSION['msg_del'] ?? '';
unset($_SESSION['msg_del']);

// ── Filters ──────────────────────────────────────
$f_date = $_GET['tanggal'] ?? '';
$f_name = $_GET['nama']    ?? '';

// ── Reservations ─────────────────────────────────
$where = []; $params = []; $types = '';
if ($role !== 'admin') { $where[]='r.user_id=?'; $params[]=$uid; $types.='i'; }
if ($f_date) { $where[]='r.tanggal=?'; $params[]=$f_date; $types.='s'; }
if ($f_name) { $where[]='r.nama LIKE ?'; $params[]="%$f_name%"; $types.='s'; }
$wh = $where ? 'WHERE '.implode(' AND ',$where) : '';

$st = $conn->prepare("SELECT r.*,u.email ue FROM reservasi r JOIN users u ON r.user_id=u.id $wh ORDER BY r.tanggal DESC,r.jam DESC");
if ($params) $st->bind_param($types,...$params);
$st->execute();
$reservations = $st->get_result()->fetch_all(MYSQLI_ASSOC);

// ── Menus (all) ───────────────────────────────────
$menus_all = $conn->query("SELECT * FROM menu ORDER BY kategori,nama")->fetch_all(MYSQLI_ASSOC);
$cats = ['Makanan','Minuman','Dessert'];

// ── Stats ─────────────────────────────────────────
$stat_menu   = $conn->query("SELECT COUNT(*) c FROM menu")->fetch_assoc()['c'];
$stat_total  = $conn->query("SELECT COUNT(*) c FROM reservasi")->fetch_assoc()['c'];
$stat_pend   = $conn->query("SELECT COUNT(*) c FROM reservasi WHERE status='pending'")->fetch_assoc()['c'];
$stat_users  = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];

// ── Monthly chart ─────────────────────────────────
$chart = [];
for ($i=5;$i>=0;$i--) {
    $ym = date('Y-m',strtotime("-$i months"));
    $cnt = $conn->query("SELECT COUNT(*) c FROM reservasi WHERE DATE_FORMAT(tanggal,'%Y-%m')='$ym'")->fetch_assoc()['c'];
    $chart[] = ['label'=>date('M',strtotime("-$i months")),'n'=>(int)$cnt];
}
$cmax = max(1,...array_column($chart,'n'));
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
<style>
/* index-only micro styles */
.res-grid-menu{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px}
#statistik{padding:5rem 0;background:var(--bg2)}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem}
@media(max-width:900px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:480px){.stats-grid{grid-template-columns:1fr 1fr}}
#menu{background:var(--bg)}
#reservasi{background:var(--bg2)}
#about{padding:5.5rem 0}
.hero-scroll-arrow{
  position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);
  color:var(--text-muted);font-size:1.2rem;
  animation:bounce 1.8s ease infinite;
}
@keyframes bounce{0%,100%{transform:translateX(-50%) translateY(0)}50%{transform:translateX(-50%) translateY(8px)}}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<!--  HERO  -->
<section id="hero">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="container">
    <div class="hero-content">
      <div class="hero-eyebrow">✦ Authentic Indonesian Cuisine ✦</div>
      <h1 class="hero-title">
        Where Every Bite<br><em>Tells a Story</em>
      </h1>
      <p class="hero-sub">Rasa autentik, pengalaman tak terlupakan. Setiap hidangan adalah karya seni yang lahir dari dapur penuh cinta.</p>
      <div class="hero-cta">
        <a href="#reservasi" class="btn btn-primary" style="font-size:1rem;padding:13px 30px;">🗓 Buat Reservasi</a>
        <a href="#menu" class="btn btn-ghost" style="font-size:1rem;padding:13px 24px;">Lihat Menu</a>
      </div>
    </div>
  </div>
  <div class="hero-scroll-arrow">↓</div>
</section>

<!-- RESERVASI  -->
<section id="reservasi" class="section">
  <div class="container">
    <div class="mb-6">
      <div class="s-eyebrow">Manajemen</div>
      <h2 class="s-title">Daftar Reservasi</h2>
    </div>

    <?php if ($msg_del): ?><div class="alert alert-success"><?= htmlspecialchars($msg_del) ?></div><?php endif; ?>

    <!-- Filter -->
    <form method="GET" action="#reservasi" style="margin-bottom:.8rem">
      <div class="filter-row">
        <input type="date" name="tanggal" class="input" style="max-width:180px" value="<?= htmlspecialchars($f_date) ?>">
        <input type="text"  name="nama"    class="input" style="max-width:200px" placeholder="Cari nama…" value="<?= htmlspecialchars($f_name) ?>">
        <button type="submit" class="btn btn-primary btn-sm">🔍 Filter</button>
        <a href="index.php#reservasi" class="btn btn-ghost btn-sm">↺ Reset</a>
        <a href="reservation_add.php" class="btn btn-primary btn-sm" style="margin-left:auto">＋ Tambah Reservasi</a>
      </div>
    </form>

    <div class="card">
      <div class="card-body" style="padding:0">
        <div class="tbl-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th><th>Nama Pemesan</th><th>Email Akun</th>
                <th>Tanggal</th><th>Jam</th><th>Jml. Orang</th><th>Status</th><th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($reservations)): ?>
                <tr><td colspan="8" style="text-align:center;padding:2.5rem;color:var(--text-muted)">
                  <?= ($f_date||$f_name)?'Tidak ada reservasi yang sesuai filter.':'Belum ada reservasi. <a href="reservation_add.php" style="color:var(--primary)">Buat sekarang</a>.' ?>
                </td></tr>
              <?php else: foreach ($reservations as $i=>$r): ?>
                <tr>
                  <td style="color:var(--text-muted);font-size:.8rem"><?= $i+1 ?></td>
                  <td><strong><?= htmlspecialchars($r['nama']) ?></strong></td>
                  <td style="color:var(--text-muted);font-size:.82rem"><?= htmlspecialchars($r['ue']) ?></td>
                  <td><?= date('d M Y',strtotime($r['tanggal'])) ?></td>
                  <td><?= date('H:i',strtotime($r['jam'])) ?> WIB</td>
                  <td><?= $r['jumlah_orang'] ?> orang</td>
                  <td><span class="badge badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                  <td>
                    <div class="flex gap-2 flex-wrap">
                      <a href="reservation_edit.php?id=<?= $r['id'] ?>" class="btn btn-ghost btn-xs">✏ Edit</a>
                      <a href="reservation_detail.php?id=<?= $r['id'] ?>" class="btn btn-xs" style="background:var(--primary-dim);color:var(--primary);border:1px solid var(--border-strong)">👁 Detail</a>
                      <button onclick="openDel(<?= $r['id'] ?>,'<?= htmlspecialchars(addslashes($r['nama'])) ?>')"
                              class="btn btn-danger btn-xs">🗑</button>
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
</section>

<!--  MENU -->
<section id="menu" class="section">
  <div class="container">
    <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
      <div>
        <div class="s-eyebrow">Cuisine</div>
        <h2 class="s-title">Menu Kami</h2>
      </div>
    </div>

    <?php foreach ($cats as $cat):
      $items = array_filter($menus_all, fn($m)=>$m['kategori']===$cat);
      if (empty($items)) continue;
    ?>
    <div style="margin-bottom:2rem">
      <div style="font-size:.78rem;text-transform:uppercase;letter-spacing:.1em;color:var(--primary);font-weight:600;margin-bottom:.8rem;display:flex;align-items:center;gap:8px">
        <span><?= $cat ?></span>
        <span style="flex:1;height:1px;background:var(--border)"></span>
      </div>
      <div class="res-grid-menu">
        <?php foreach ($items as $m): ?>
          <div class="menu-card">
            <div class="menu-card-img">
              <?php if ($m['gambar'] && file_exists('uploads/'.$m['gambar'])): ?>
                <img src="uploads/<?= htmlspecialchars($m['gambar']) ?>" alt="">
              <?php else: echo '🍽'; endif; ?>
            </div>
            <div class="menu-card-body">
              <div class="menu-card-cat"><?= $m['kategori'] ?></div>
              <div class="menu-card-name"><?= htmlspecialchars($m['nama']) ?></div>
              <div class="menu-card-desc"><?= htmlspecialchars($m['deskripsi']) ?></div>
              <div class="menu-card-price">Rp <?= number_format($m['harga'],0,',','.') ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <div style="text-align:center;margin-top:1rem">
      <a href="menu_list.php" class="btn btn-ghost">≡ Kelola Semua Menu →</a>
    </div>
  </div>
</section>

<!-- STATISTIK  -->
<section id="statistik">
  <div class="container">
    <div style="text-align:center;margin-bottom:2.5rem">
      <div class="s-eyebrow">Data & Analitik</div>
      <h2 class="s-title">Statistik Aplikasi</h2>
    </div>

    <div class="stats-grid" style="margin-bottom:2rem">
      <div class="stat-card"><span class="stat-num"><?= $stat_menu ?></span><div class="stat-label">Total Menu</div></div>
      <div class="stat-card"><span class="stat-num"><?= $stat_pend ?></span><div class="stat-label">Reservasi Pending</div></div>
      <div class="stat-card"><span class="stat-num"><?= $stat_total ?></span><div class="stat-label">Total Reservasi</div></div>
      <div class="stat-card"><span class="stat-num"><?= $stat_users ?></span><div class="stat-label">Pengguna Aktif</div></div>
    </div>

    <div class="card">
      <div class="card-body">
        <div style="font-family:var(--font-display);font-size:1.15rem;font-weight:600;margin-bottom:.3rem">Reservasi per Bulan</div>
        <div style="font-size:.78rem;color:var(--text-muted);margin-bottom:1.2rem;display:flex;align-items:center;gap:6px">
          <span style="width:12px;height:12px;background:var(--primary);border-radius:2px;display:inline-block"></span>Jumlah Reservasi
        </div>
        <div class="bar-chart">
          <?php foreach ($chart as $d):
            $pct = $cmax > 0 ? round($d['n']/$cmax*100) : 0;
          ?>
          <div class="bar-col">
            <div class="bar-fill" style="height:<?= max($pct,3) ?>px" data-val="<?= $d['n'] ?>"></div>
            <div class="bar-label"><?= $d['label'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about" class="section">
  <div class="container" style="max-width:720px;text-align:center">
    <div class="s-eyebrow">Tentang Kami</div>
    <h2 class="s-title">About Us</h2>
    <div class="about-quote">"Where joy meets every bite."</div>
    <div class="about-body">
      <p>Elegan dalam rasa, lembut dalam sentuhan. Kami percaya bahwa keindahan bisa dihadirkan dalam bentuk paling sederhana—melalui harmoni rasa, aroma, dan suasana. Setiap elemen kami ciptakan untuk membangkitkan kesan yang tak terlupakan.</p>
      <p>Lebih dari sekadar tempat bersantai, kami ingin menjadi ruang yang membawa ketenangan dan kehangatan. Di sini, setiap momen adalah undangan untuk merasakan kenyamanan dalam bentuk yang paling manis dan autentik.</p>
    </div>
  </div>
</section>

 <!-- DELETE MODAL  -->
<div class="modal" id="delModal">
  <div class="modal-box">
    <div class="modal-icon">🗑</div>
    <div class="modal-title">Hapus Reservasi?</div>
    <div class="modal-desc" id="delDesc">Tindakan ini tidak dapat dibatalkan.</div>
    <form method="POST" action="reservation_delete.php">
      <input type="hidden" name="id" id="delId">
      <div class="modal-btns">
        <button type="button" onclick="closeModal()" class="btn btn-ghost">Batal</button>
        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
function openDel(id,nama){
  document.getElementById('delId').value=id;
  document.getElementById('delDesc').textContent='Hapus reservasi atas nama "'+nama+'"? Tidak dapat dibatalkan.';
  document.getElementById('delModal').classList.add('open');
}
function closeModal(){
  document.getElementById('delModal').classList.remove('open');
}
document.getElementById('delModal').addEventListener('click',function(e){if(e.target===this)closeModal()});
</script>
</body>
</html>

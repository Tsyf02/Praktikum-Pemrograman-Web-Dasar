<?php 
$nama = $_POST['nama'];
$telp = $_POST['telp'];
$umur = $_POST['umur'];
$jenisKelamin = $_POST['jenisKelamin'];
$kelas = $_POST['kelas'];
$durasi = (int)$_POST['durasi'];
$pernahLangganan = $_POST['pernahLangganan'];
// $belumLangganan = $_POST['belumLangganan'];

// Tentukan harga per bulan
if ($kelas == "Minor(250K)") {
    $harga = 250000;
} elseif ($kelas == "Mayor(750K)") {
    $harga = 750000;
} else {
    $harga = 1000000;
}

// Hitung total sebelum diskon
$total = $harga * $durasi;

// Hitung diskon sesuai aturan
$diskon = 0;
if ($durasi < 2) {
    $diskon = 0;
} elseif ($durasi >= 2 && $pernahLangganan == "Tidak") {
    $diskon = 0.10 * $total;
} elseif ($durasi >= 2 && $pernahLangganan == "Ya") {
    $diskon = 0.15 * $total;
}

// Total bayar
$totalBayar = $total - $diskon;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="author" content="Talitha Syifa Al Fath_124250173">
    <meta name="description" content="Website Soundify_ Landing Page">
    <title>Soundify</title>
    <link rel="icon" href="https://plus.unsplash.com/premium_vector-1724342789864-a360104ba42c?q=80&w=1577&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3" type="image/x-icon"> 
    <link rel="stylesheet" href="STYLESHEET.css" type="text/css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
<!-- 1.index.php -->
<body class="page-index">
  <nav>
    <a href="login.php" class="logo">📻 Soundify</a>
    <ul class="nav-links">
      <li><a href="1.index.php">Home</a></li>
      <li><a href="2.form.php">Layanan</a></li>
      <li><a href="1.index.php" class="active">Pemesanan</a></li>
    </ul>
  </nav>
<br>
<!-- STRUK NYA -->
<center><table border="1" cellpadding="8">
  <tr><td>Nama Lengkap</td><td><?= $nama ?></td></tr>
  <tr><td>No. HP</td><td><?= $telp ?></td></tr>
  <tr><td>Umur</td><td><?= $umur ?></td></tr>
  <tr><td>Jenis Kelamin</td><td><?= $jenisKelamin ?></td></tr>
  <tr><td>Kelas</td><td><?= $kelas ?></td></tr>
  <tr><td>Durasi</td><td><?= $durasi ?> Bulan</td></tr>
  <tr><td>Harga per Bulan</td><td>Rp<?= number_format($harga,0,",",".") ?></td></tr>
  <tr><td>Total</td><td>Rp<?= number_format($total,0,",",".") ?></td></tr>
  <tr><td>Diskon</td><td>Rp<?= number_format($diskon,0,",",".") ?></td></tr>
  <tr><td>Total Bayar</td><td><strong>Rp<?= number_format($totalBayar,0,",",".") ?></strong></td></tr>
</center></table>

<br>
<hr>
<!-- INI DAPET DARI TEMPLTE BOOTSTRAP DI GITHUB (https://github.com/mdbootstrap/bootstrap-footer) -->
<!-- Remove the container if you want to extend the Footer to full width. -->
<div class="container my-5">

  <footer class="bg-light text-center">
  <!-- Grid container -->
  <div class="container p-4 pb-0">
    <!-- Section: Form -->
    <section class="">
      <form action="">
        <!--Grid row-->
        <div class="row d-flex justify-content-center">
          <!--Grid column-->
          <div class="col-auto">
            <p class="pt-2">
              <strong>Sign up for our newsletter</strong>
            </p>
          </div>
          <!--Grid column-->
<br>
          <!--Grid column-->
          <div class="col-md-5 col-12">
            <!-- Email input -->
            <div class="form-outline mb-4">
              <input type="email" id="form5Example2" class="form-control" />
              <label class="form-label" for="form5Example2">Email address</label>
            </div>
          </div>
          <!--Grid column-->

          <!--Grid column-->
          <div class="col-auto">
          <br>  <!-- Submit button -->
            <button type="submit" class="btn btn-primary mb-4">
              Subscribe
            </button>
          </div>
          <!--Grid column-->
        </div>
        <!--Grid row-->
      </form>
    </section>
    <!-- Section: Form -->
  </div>
  <!-- Grid container -->
<br>
  <!-- Copyright -->
  <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
    © 2020 Copyright: Soundify
  </div>
  <!-- Copyright -->
</footer>
  
</div>
<!-- End of .container -->

</body>
</html>
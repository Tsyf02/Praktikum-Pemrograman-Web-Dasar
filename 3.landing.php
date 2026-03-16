<?php
$namaDepan        = $_POST['namaDepan'];       
$namaBelakang        = $_POST['namaBelakang'];   
$NomorHP     = $_POST['NomorHP'];
$email       = $_POST['email'];
$tanggalLahir= $_POST['date'];
$jenisKelamin= $_POST['JenisKelamin'];
$alamat      = $_POST['alamat'];
$sepatu      = $_POST['optionSepatu'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Talitha Syifa Al Fath_124250173">
    <meta name="description" content="Website SoleMate Shoe Laundry_ Landing Page">
    <title>Shoe Laundry “SoleMate”</title>
    <link rel="icon" href="SoleMate Logo.png" type="image/x-icon"> 
    <link rel="stylesheet" href="STYLE.css" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
</head>

<!-- 3.landing.php -->
<body class="page-landing">
  <nav>
    <a href="login.php" class="logo">SoleMate</a>
  <ul class="nav-links">
      <li><a href="1.index.php">Layanan</a></li>
      <li><a href="2.form.php">Member</a></li>
      <li><a href="1.index.php" class="active">Daftar Member</a></li>
    </ul>
  </nav>

 <section class="catalog-section">
    <h1>Kartu Member</h1>

<center><div class="book-grid"></center>
 <div style="display: flex; justify-content: center;">
 <div class="card">
      <div class="book-card">
            <div class="card-img-wrapper">
        <img src="SoleMate Logo.png" alt="SoleMate"/> 
            <div class="img-overlay"></div>  <!-- tambah inI BIAR LOGO GA KETERANGAN -->
            </div>
        <!-- diganti warna biru atau aapalah-->
        <div class="book-card-body">
         
        <p><strong>SoleMate MEMBER</strong></p>
            
        <p><strong>Nama Lengkap:</strong> <?php echo $namaDepan; ?> <?php echo $namaBelakang; ?></p>
              <p><strong>Nomor HP:</strong> <?php echo $NomorHP; ?></p>
              <p><strong>Email:</strong> <?php echo $email; ?></p>
              <p><strong>Tanggal Lahir:</strong> <?php echo $tanggalLahir; ?></p>
              <p><strong>Jenis Kelamin:</strong> <?php echo $jenisKelamin; ?></p>
              <p><strong>Alamat:</strong> <?php echo $alamat; ?></p>
              <p><strong>Sepatu Favorit:</strong> <?php echo $sepatu; ?></p>
       
        </div>
          <center> <a href="1.index.php" class="btn-detail">Kembali</a> <br></center>
         </div> <br>
      </div> <br>
  </section> 

    <hr>
<div>
<footer class="text-center py-3 black-bg text-white">
 <div><a href="1.index.php" class="logo">SoleMate</a>
 <p><small>SoleMate adalah layanan laudry sepatu profesional yang membantu menjaga kebersihan dan kualitas sepatu Anda dengan perawatan terbaik </small> </p>
 </div>

 <br>
 <h2>Menu</h2>
 <p><small>Home</small></p>
 <p><small>Register Member</small></p>
 <p><small>Layanan</small></p>
 <p><small>Kontak</small></p>

 <br>
 <h2>Kontak</h2>
 <p><small>Bandung, Indonesia</small></p>
 <p><small>0812-3456-7890</small></p>
 <p><small>solemate@gmail.com</small></p>
 
 <br>
 <div><small>© 2026 SoleMate Laundry Sepatu, All Rights Reserved.</small></div>
</footer>
</div>


</body>
</html>
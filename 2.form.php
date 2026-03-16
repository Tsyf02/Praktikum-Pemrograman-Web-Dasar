<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Talitha Syifa Al Fath_124250173">
    <meta name="description" content="Website SoleMate Shoe Laundry_ Form Page">
    <title>Shoe Laundry “SoleMate”</title>
    <title>Shoe Laundry “SoleMate”</title>
    <link rel="icon" href="SoleMate Logo.png" type="image/x-icon"> 
    <link rel="stylesheet" href="STYLE.css" type="text/css">
    
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
 
</head>

<!-- 2.form.php -->
<body class="page-form">
  <nav>
    <a href="login.php" class="logo">SoleMate</a>
    <ul class="nav-links">
      <li><a href="1.index.php">Layanan</a></li>
      <li><a href="2.form.php">Member</a></li>
      <li><a href="1.index.php" class="active">Daftar Member</a></li>
    </ul>
  </nav>

  <section class="hero">
    <div class="container my-5">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      
  <h1>Daftar Member</h1>
  <p>Isi data di bawah ini untuk membuat kartu member Anda.</p>
      
            <!-- Form pendaftaran -->
  <div class="form-container">
    <form action="3.landing.php" method="POST">
      <div class="row">
         <div class="col">
            <label class="form-label">Nama Depan</label> 
            <input type="text" name="namaDepan" class="form-control" placeholder="Masukkan Nama Depan" required>
         </div>
         <div class="col">
            <label class="form-label">Nama Belakang</label>  
            <input type="text" name="namaBelakang" class="form-control" placeholder="Masukkan Nama Belakang" required>
         </div>
      </div>
<br>
    <div class="mb-3">
      <label class="form-label">Nomor HP</label>
      <input type="text" name="NomorHP" class="form-control" placeholder="Masukkan Nomor HP" required>
    </div>
    
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" placeholder="Masukkan Email" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Tanggal Lahir</label>
      <input type="date" name="date" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Jenis Kelamin</label>
      <select name="JenisKelamin" class="form-select" required>
        <option value="Perempuan">Perempuan</option>
        <option value="Laki-laki">Laki-laki</option>
      </select>
    </div>

     <div class="mb-3">
      <label class="form-label">Alamat</label>
      <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan Alamat" required></textarea>
    </div>

     <div class="mb-3>
      <label class ="form-label">Jenis Sepatu Favorit</label>
      <select name="optionSepatu" class="form-select" required>
        <option value="Option 1 - Sneaker / Kasual">Option 1 - Sneaker / Kasual &amp; Kanvas</option>
        <option value="Option 2 - Sepatu Beludru">Option 2 - Sepatu Beludru &amp; Suede</option>
        <option value="Option 3 - Sepatu Kulit">Option 3 - Sepatu Kulit &amp; Formal / Heels</option>
      </select>
    </div>

    <br>
<div class="d-flex justify-content-between mt-4">
      <button type="submit" class="btn-detail">Buat Kartu Member</button> <br>
      <br><a href="1.index.php" class="btn-detail">Kembali ke Beranda</a>
    </div>
  </form>
</div>
  </section>


   <br>


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
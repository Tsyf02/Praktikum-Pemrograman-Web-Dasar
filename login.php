<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="author" content="Talitha Syifa Al Fath_124250173">
    <meta name="description" content="web PokeMatch">
    <title> PokeMatch </title>
    <link rel="icon" href="data:image/png;base64,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" type="image/x-icon">
    <link rel="stylesheet" href="stylesheet.css" type="text/css">
    <link href="" rel="stylesheet"
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
</head>
<body>
<nav>
    <a href="login.php" class="logo">PokeMatch</a>
    <ul class="nav-links">
        <li><a href="1.index.php">Home</a></li>
        <li><a href="1.index.php">Kategori</a></li>
        <li><a href="2.form.php" class="active">Pendaftaran</a></li>
</ul>
</nav>


<main>
<center> 
<div class="card">
    <h2>Login Akun</h2>
    <div class="input-group">
        <input type="email" placeholder="Email"/>
    </div>
    <div class="input-group">
        <input type="password" placeholder="Password"/>
    </div>
    <a href="1.index.php" class="btn-masuk">Masuk</a>
    </div>
</center> 
</main>
<br>

<center> 
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
            <!-- Submit button -->
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

  <!-- Copyright -->
  <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
    © 2020 Copyright: PokeMatch
  </div>
  <!-- Copyright -->
</footer>
  
</div>
<!-- End of .container -->
</center> 

</body>
</html>

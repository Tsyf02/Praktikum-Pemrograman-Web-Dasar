<?php /* navbar.php — include after auth check */ ?>
<nav class="navbar">
  <a href="index.php" class="nav-brand">
    <div class="nav-logo">✦</div>
    <span>Violet Restaurant</span>
  </a>
  <ul class="nav-links">
    <?php if (!empty($is_index)): ?>
      <li><a href="#reservasi"><span class="nav-text">Reservasi</span></a></li>
      <li><a href="#menu"><span class="nav-text">Menu</span></a></li>
      <li><a href="#statistik"><span class="nav-text">Statistik</span></a></li>
      <li><a href="#about"><span class="nav-text">About</span></a></li>
    <?php else: ?>
      <li><a href="index.php#reservasi"><span class="nav-text">Reservasi</span></a></li>
      <li><a href="menu_list.php"><span class="nav-text">Menu</span></a></li>
      <li><a href="index.php#statistik"><span class="nav-text">Statistik</span></a></li>
      <li><a href="index.php#about"><span class="nav-text">About</span></a></li>
    <?php endif; ?>
    <li>
      <button class="theme-btn" onclick="toggleTheme()" title="Toggle theme" id="themeBtn">🌙</button>
    </li>
    <li><a href="logout.php" class="nav-logout">Logout</a></li>
  </ul>
</nav>
<script>
// Theme persistence
(function(){
  const t=localStorage.getItem('theme')||'dark';
  document.documentElement.setAttribute('data-theme',t);
  const btn=document.getElementById('themeBtn');
  if(btn)btn.textContent=t==='dark'?'🌙':'☀️';
})();
function toggleTheme(){
  const cur=document.documentElement.getAttribute('data-theme')||'dark';
  const next=cur==='dark'?'light':'dark';
  document.documentElement.setAttribute('data-theme',next);
  localStorage.setItem('theme',next);
  const btn=document.getElementById('themeBtn');
  if(btn)btn.textContent=next==='dark'?'🌙':'☀️';
}
</script>

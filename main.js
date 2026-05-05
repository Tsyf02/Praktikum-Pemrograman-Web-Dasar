// main.js – NATURALLE interactive behaviors

(function () {
  'use strict';

  /* ---- State ---- */
  let currentLang = localStorage.getItem('naturalle-lang') || 'id';
  let currentTheme = localStorage.getItem('naturalle-theme') || 'light';

  /* ---- Apply stored prefs immediately ---- */
  applyTheme(currentTheme);
  applyLang(currentLang);

  /* ---- Language Toggle ---- */
  const langBtn = document.getElementById('langToggle');
  if (langBtn) {
    langBtn.addEventListener('click', () => {
      currentLang = currentLang === 'id' ? 'en' : 'id';
      localStorage.setItem('naturalle-lang', currentLang);
      applyLang(currentLang);
    });
  }

  /* ---- Theme Toggle ---- */
  const themeBtn = document.getElementById('themeToggle');
  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      currentTheme = currentTheme === 'light' ? 'dark' : 'light';
      localStorage.setItem('naturalle-theme', currentTheme);
      applyTheme(currentTheme);
    });
  }

  function applyTheme(theme) {
    document.body.classList.toggle('dark', theme === 'dark');
    document.body.classList.toggle('light', theme === 'light');
    const moon = document.getElementById('iconMoon');
    const sun  = document.getElementById('iconSun');
    if (moon && sun) {
      moon.style.display = theme === 'dark' ? 'none' : 'block';
      sun.style.display  = theme === 'dark' ? 'block' : 'none';
    }
  }

  function applyLang(lang) {
    document.body.dataset.lang = lang;
    const t = window.TRANSLATIONS[lang];
    if (!t) return;

    // Update lang-toggle label
    const langBtn = document.getElementById('langToggle');
    if (langBtn) langBtn.textContent = lang === 'id' ? 'EN' : 'ID';

    // Update html lang attribute
    document.documentElement.lang = lang;

    // Update all data-id elements
    document.querySelectorAll('[data-id]').forEach(el => {
      const key = el.dataset.id;
      if (t[key] !== undefined) {
        el.textContent = t[key];
      }
    });

    // Update page title
    if (lang === 'en') {
      if (document.title.includes('Masuk')) {
        document.title = 'Sign In – NATURALLE';
      }
    } else {
      if (document.title.includes('Sign In')) {
        document.title = 'Masuk – NATURALLE';
      }
    }
  }

  /* ---- Scroll Reveal ---- */
  const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        // Stagger siblings in same parent
        const siblings = [...entry.target.parentElement.children].filter(
          c => c.classList.contains('reveal') ||
               c.classList.contains('reveal-left') ||
               c.classList.contains('reveal-right')
        );
        const idx = siblings.indexOf(entry.target);
        entry.target.style.transitionDelay = `${idx * 0.15}s`;
        entry.target.classList.add('revealed');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15, rootMargin: '-60px 0px' });

  revealEls.forEach(el => observer.observe(el));

  /* ---- Navbar scroll effect ---- */
  const navbar = document.getElementById('navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.style.boxShadow = window.scrollY > 10
        ? '0 2px 20px rgba(0,0,0,0.08)'
        : 'none';
    }, { passive: true });
  }

  /* ---- Hero parallax ---- */
  const heroImg = document.querySelector('.hero-img');
  if (heroImg) {
    window.addEventListener('scroll', () => {
      const y = window.scrollY;
      heroImg.style.transform = `translateY(${y * 0.3}px)`;
    }, { passive: true });
  }

  /* ---- Smooth scroll for anchor links ---- */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

})();

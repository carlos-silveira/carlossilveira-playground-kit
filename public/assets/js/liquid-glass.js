/**
 * Carlos Silveira — Liquid Glass Interactive Physics & Theme Toggle
 */
(function () {
  // 1. Two-State Compliant Color Scheme Toggle
  const meta = document.querySelector('meta[name="color-scheme"]');
  const toggleBtn = document.getElementById('theme-toggle-btn');

  function getSystemScheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function getStoredScheme() {
    try {
      const val = localStorage.getItem('color-scheme');
      return val === 'dark' || val === 'light' ? val : null;
    } catch (_) {
      return null;
    }
  }

  function applyScheme(stored) {
    if (meta) {
      meta.content = stored ?? 'light dark';
    }
  }

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      const systemScheme = getSystemScheme();
      const currentRendered = getStoredScheme() ?? systemScheme;
      const targetScheme = currentRendered === 'dark' ? 'light' : 'dark';

      if (targetScheme !== systemScheme) {
        try { localStorage.setItem('color-scheme', targetScheme); } catch (_) {}
        applyScheme(targetScheme);
      } else {
        try { localStorage.removeItem('color-scheme'); } catch (_) {}
        applyScheme(null);
      }
    });
  }

  window.addEventListener('storage', function (e) {
    if (e.key === 'color-scheme') {
      applyScheme(e.newValue === 'dark' || e.newValue === 'light' ? e.newValue : null);
    }
  });

  // 2. Specular Cursor Spotlight Sheen on .lg-card
  document.addEventListener('pointermove', function (e) {
    const cards = document.querySelectorAll('.lg-card');
    cards.forEach(function (card) {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      card.style.setProperty('--mouse-x', x + 'px');
      card.style.setProperty('--mouse-y', y + 'px');
    });
  }, { passive: true });
})();

/**
 * Carlos Silveira — Liquid Glass Interactive Physics, Mobile Drawer & Theme Toggle
 */
(function () {
  'use strict';

  // 1. Two-State Compliant Color Scheme Toggle (supports #cs-theme-toggle, [data-theme-toggle], #theme-toggle-btn)
  const meta = document.querySelector('meta[name="color-scheme"]');
  const themeToggleBtns = document.querySelectorAll('#cs-theme-toggle, [data-theme-toggle], #theme-toggle-btn');

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
      meta.content = stored ?? 'dark';
    }
  }

  function toggleTheme() {
    const currentRendered = getStoredScheme() ?? (meta && meta.content === 'light' ? 'light' : 'dark');
    const targetScheme = currentRendered === 'dark' ? 'light' : 'dark';
    try { localStorage.setItem('color-scheme', targetScheme); } catch (_) {}
    applyScheme(targetScheme);
  }

  themeToggleBtns.forEach((btn) => {
    btn.addEventListener('click', toggleTheme);
  });

  window.addEventListener('storage', function (e) {
    if (e.key === 'color-scheme') {
      applyScheme(e.newValue === 'dark' || e.newValue === 'light' ? e.newValue : null);
    }
  });

  // 2. High-Performance Pointer-Reactive Specular Sheen (Mouse only, no loop)
  document.addEventListener('pointermove', function (e) {
    if (e.pointerType !== 'mouse') return;
    const card = e.target.closest?.('.lg-card');
    if (!card) return;
    const rect = card.getBoundingClientRect();
    card.style.setProperty('--mouse-x', (e.clientX - rect.left) + 'px');
    card.style.setProperty('--mouse-y', (e.clientY - rect.top) + 'px');
  }, { passive: true });

  // 3. Mobile Drawer Toggle
  const mobileToggleBtn = document.getElementById('cs-mobile-toggle') || document.querySelector('[data-mobile-toggle]');
  const navShell = document.getElementById('cs-nav-shell') || document.querySelector('.cs-nav-shell');
  const mobileDrawer = document.getElementById('cs-mobile-drawer') || document.querySelector('.cs-mobile-drawer');

  if (mobileToggleBtn && navShell) {
    mobileToggleBtn.addEventListener('click', function () {
      const isOpen = navShell.classList.toggle('is-menu-open');
      mobileToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      if (mobileDrawer) {
        mobileDrawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
      }
    });

    document.addEventListener('click', function (e) {
      if (navShell.classList.contains('is-menu-open') && !navShell.contains(e.target)) {
        navShell.classList.remove('is-menu-open');
        mobileToggleBtn.setAttribute('aria-expanded', 'false');
        if (mobileDrawer) {
          mobileDrawer.setAttribute('aria-hidden', 'true');
        }
      }
    });
  }
})();

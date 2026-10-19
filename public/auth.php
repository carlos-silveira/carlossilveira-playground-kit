<?php
declare(strict_types=1);

/**
 * Carlos Silveira — Private Lab Authentication Gate (`public/auth.php`)
 * Protects https://carlossilveira.dev/lab/* so only Carlos can access internal micro-apps.
 *
 * Auth is delegated 100% to WordPress (no passwords stored here):
 *   1. An existing WP Admin session (cookie validated server-side by wp_validate_auth_cookie()).
 *   2. The login form below, which calls wp_signon() with your WP Admin credentials.
 * Only users with `manage_options` (administrators) are allowed. Fails closed if WP can't load.
 *
 * Usage in any micro-app under /lab/<slug>/:  require_once __DIR__ . '/../auth.php';
 */

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: private, no-store');

function cs_lab_load_wordpress(): bool {
    if (function_exists('wp_validate_auth_cookie')) {
        return true;
    }
    $wpLoad = dirname(__DIR__) . '/wp-load.php'; // /lab/auth.php -> docroot/wp-load.php
    if (!is_file($wpLoad)) {
        return false;
    }
    require_once $wpLoad;
    return function_exists('wp_validate_auth_cookie');
}

if (!cs_lab_load_wordpress()) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Lab auth unavailable: WordPress could not be loaded.';
    exit;
}

function cs_lab_current_admin(): ?string {
    $userId = wp_validate_auth_cookie('', 'logged_in');
    if (!$userId) {
        return null;
    }
    $user = get_userdata($userId);
    if (!$user || !user_can($user, 'manage_options')) {
        return null;
    }
    return $user->display_name ?: $user->user_login;
}

// 1. Logout (?logout=1) — ends the WordPress session too.
if (isset($_GET['logout'])) {
    wp_logout();
    header('Location: /lab/', true, 303);
    exit;
}

// 2. Login form submission -> WordPress credentials.
$loginError = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['lab_username'], $_POST['lab_password'])) {
    $user = wp_signon([
        'user_login'    => sanitize_user(wp_unslash((string) $_POST['lab_username'])),
        'user_password' => (string) wp_unslash((string) $_POST['lab_password']),
        'remember'      => true,
    ], is_ssl());

    if (!is_wp_error($user) && user_can($user, 'manage_options')) {
        $target = strtok($_SERVER['REQUEST_URI'] ?? '/lab/', '?') ?: '/lab/';
        header('Location: ' . (str_starts_with($target, '/lab/') ? $target : '/lab/'), true, 303);
        exit;
    }
    if (!is_wp_error($user)) {
        wp_logout(); // valid user, but not an administrator
    }
    $loginError = 'Usuario o contraseña incorrectos (usa tus credenciales de WP Admin).';
}

// 3. Determine authenticated user.
$csLabUser = cs_lab_current_admin();

// 4. Render Private Liquid Glass Login Gate if Unauthenticated
if ($csLabUser === null) {
    $wantsJson = (isset($_GET['format']) && $_GET['format'] === 'json')
        || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

    if ($wantsJson) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok' => false,
            'error' => 'Private Lab workspace. Authentication required.',
            'login_url' => '/lab/',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    http_response_code(401);
    $reqUri = $_SERVER['REQUEST_URI'] ?? '/lab/';
    $wpLoginUrl = '/wp-login.php?redirect_to=' . rawurlencode($reqUri);

    // Resolve relative path to /lab/assets
    $assetsPrefix = is_dir(__DIR__ . '/assets') && realpath(dirname($_SERVER['SCRIPT_FILENAME'] ?? __FILE__)) === realpath(__DIR__)
        ? './assets'
        : '../assets';
    ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <meta name="color-scheme" content="light dark">
  <script>
    try {
      document.querySelector('meta[name="color-scheme"]').content = localStorage.getItem("color-scheme") ?? "light dark";
    } catch (_) {}
  </script>
  <title>Acceso Privado — Carlos Silveira Lab</title>

  <!-- Favicon (Uses CS logo-mark ONLY) -->
  <link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars($assetsPrefix) ?>/brand/logo-mark.svg">
  <link rel="apple-touch-icon" href="<?= htmlspecialchars($assetsPrefix) ?>/brand/logo-mark.svg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars($assetsPrefix) ?>/css/liquid-glass.css">
  <style>
    .login-shell {
      min-height: calc(100vh - 110px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 0 64px;
    }
    .login-grid {
      display: grid;
      grid-template-columns: 1.08fr 0.92fr;
      gap: 32px;
      align-items: stretch;
      width: 100%;
    }
    @media (max-width: 920px) {
      .login-grid {
        grid-template-columns: 1fr;
      }
    }
    .login-field {
      margin-bottom: 18px;
    }
    .login-field label {
      display: block;
      font-family: var(--font-display);
      font-size: 13px;
      font-weight: 700;
      margin-bottom: 8px;
      color: var(--text-primary);
    }
    .error-banner {
      padding: 12px 16px;
      border-radius: 14px;
      background: rgba(239, 68, 68, 0.14);
      border: 1px solid rgba(239, 68, 68, 0.35);
      color: var(--danger);
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 18px;
    }
  </style>
</head>
<body>
  <!-- High-Performance Living Ambient Mesh Backdrop -->
  <div class="lg-ambient-canvas" aria-hidden="true"></div>

  <!-- Unified Floating Liquid Glass Navbar -->
  <header class="cs-site-header">
    <div class="cs-container-wide">
      <div class="cs-nav-shell" id="cs-nav-shell">
        <div class="cs-nav-bar">
          <!-- Brand Wordmark -->
          <a href="/lab/" class="cs-brand-link" aria-label="Carlos Silveira — Labs">
            <span class="cs-brand-wordmark">
              <span class="cs-brand-first">Carlos</span>
              <span class="cs-brand-last">Silveira</span>
              <span class="cs-brand-badge">.lab</span>
            </span>
          </a>

          <!-- Desktop Navigation Links -->
          <nav class="cs-nav-desktop" aria-label="Navegación Principal">
            <a href="/" class="cs-nav-link">
              Blog
            </a>
            <a href="/#articulos" class="cs-nav-link">
              Artículos
            </a>
            <a href="/tools/" class="cs-nav-link">
              Herramientas
            </a>
            <a href="/lab/" class="cs-nav-link is-active" aria-current="page">
              Private Lab 🔒
            </a>
          </nav>

          <!-- Actions: Theme Toggle + Mobile Menu Button -->
          <div class="cs-nav-actions">
            <button
              type="button"
              class="cs-theme-toggle"
              id="cs-theme-toggle"
              data-theme-toggle
              aria-label="Cambiar tema claro / oscuro"
              title="Cambiar tema"
            >
              <span class="cs-theme-icon-dark" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
              </span>
              <span class="cs-theme-icon-light" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="5"/>
                  <line x1="12" y1="1" x2="12" y2="3"/>
                  <line x1="12" y1="21" x2="12" y2="23"/>
                  <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                  <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                  <line x1="1" y1="12" x2="3" y2="12"/>
                  <line x1="21" y1="12" x2="23" y2="12"/>
                  <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                  <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
              </span>
            </button>

            <button
              type="button"
              class="cs-mobile-menu-btn"
              id="cs-mobile-toggle"
              data-mobile-toggle
              aria-label="Abrir menú"
              aria-expanded="false"
            >
              <svg class="cs-icon-hamburger" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
              </svg>
              <svg class="cs-icon-close" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Drawer -->
        <div class="cs-mobile-drawer" id="cs-mobile-drawer" aria-hidden="true">
          <nav class="cs-mobile-nav">
            <a href="/" class="cs-mobile-link">
              <span class="cs-mobile-icon">📰</span>
              <span>Blog</span>
            </a>
            <a href="/#articulos" class="cs-mobile-link">
              <span class="cs-mobile-icon">✍️</span>
              <span>Artículos</span>
            </a>
            <a href="/tools/" class="cs-mobile-link">
              <span class="cs-mobile-icon">🛠️</span>
              <span>Herramientas (/tools/)</span>
            </a>
            <a href="/lab/" class="cs-mobile-link is-active" aria-current="page">
              <span class="cs-mobile-icon">🔒</span>
              <span>Private Lab</span>
            </a>
          </nav>
        </div>
      </div>
    </div>
  </header>

  <main class="lg-container login-shell">
    <div class="login-grid">
      <!-- Left Column: Context -->
      <section class="lg-card" style="display:flex; flex-direction:column; justify-content:space-between;">
        <div>
          <span class="lg-badge">&#128274; Espacio Interno de Desarrollo</span>
          <h1 class="lg-hero-title" style="margin: 18px 0 14px;">
            Laboratorio Privado de <span class="lg-gradient-text">Micro-Apps</span>.
          </h1>
          <p style="color: var(--text-secondary); font-size: 17px; margin: 0 0 22px;">
            Este directorio (<code>carlossilveira.dev/lab/</code>) es el entorno privado de Carlos Silveira para prototipar, probar y desplegar micro-aplicaciones reales (tiendas de Shopify en Django, paneles en Next.js, webhooks en Go o servicios en Python/PHP) dentro del mismo dominio.
          </p>
        </div>

        <div style="padding: 16px 20px; border-radius: 18px; background: var(--lg-surface-elevated); border: 1px solid var(--lg-border); box-shadow: var(--lg-specular); font-size: 14px; color: var(--text-secondary);">
          <strong style="color: var(--text-primary); display: block; margin-bottom: 4px;">🚀 Despliegues con un solo comando</strong>
          Usa <code>./scripts/deploy-lab.sh &lt;app-dir&gt;</code> para sincronizar proyectos a Spaceship con configuración automática de Passenger WSGI o CGI.
        </div>
      </section>

      <!-- Right Column: Login Form -->
      <section class="lg-card" style="display:flex; flex-direction:column; justify-content:center;">
        <div style="display:flex; align-items:center; gap:14px; margin-bottom:20px;">
          <div style="width:46px; height:46px; border-radius:14px; background:var(--lg-surface-elevated); border:1px solid var(--lg-border); display:flex; align-items:center; justify-content:center; font-size:22px; box-shadow:var(--lg-specular); flex-shrink:0;">
            🔐
          </div>
          <div>
            <h2 style="font-size:22px; font-weight:800; margin:0;">Iniciar Sesión en Lab</h2>
            <div style="font-size:13.5px; color:var(--text-secondary);">Acceso exclusivo para Carlos Silveira</div>
          </div>
        </div>

        <?php if ($loginError !== ''): ?>
          <div class="error-banner"><?= htmlspecialchars($loginError) ?></div>
        <?php endif; ?>

        <form method="post" action="">
          <div class="login-field">
            <label for="lab_username">Usuario o Correo</label>
            <input class="lg-input" type="text" id="lab_username" name="lab_username" placeholder="tu usuario de WP" required autocomplete="username">
          </div>

          <div class="login-field">
            <label for="lab_password">Contraseña</label>
            <input class="lg-input" type="password" id="lab_password" name="lab_password" placeholder="••••••••••••" required autocomplete="current-password" autofocus>
          </div>

          <div style="display:flex; flex-direction:column; gap:12px; margin-top:24px;">
            <button type="submit" class="lg-btn lg-btn-primary" style="width:100%;">
              Entrar al Laboratorio Privado &rarr;
            </button>
            <a href="<?= htmlspecialchars($wpLoginUrl) ?>" class="lg-btn lg-btn-secondary" style="width:100%; text-align:center;">
              También puedes iniciar sesión en WP Admin
            </a>
          </div>
        </form>
      </section>
    </div>
  </main>

  <script src="<?= htmlspecialchars($assetsPrefix) ?>/js/liquid-glass.js"></script>
</body>
</html>
    <?php
    exit;
}

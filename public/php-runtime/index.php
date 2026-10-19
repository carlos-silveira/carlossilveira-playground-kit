<?php
declare(strict_types=1);

/**
 * Carlos Silveira Private Lab — PHP 8.3 Micro-App Sandbox
 * Live URL: https://carlossilveira.dev/lab/php-runtime/
 */

require_once dirname(__DIR__) . '/auth.php';

$t0 = hrtime(true);
$opcacheStatus = function_exists('opcache_get_status') ? @opcache_get_status(false) : false;
$extensions = get_loaded_extensions();
sort($extensions, SORT_NATURAL | SORT_FLAG_CASE);
$totalMs = round((hrtime(true) - $t0) / 1e6, 3);

$payload = [
    'ok' => true,
    'app' => 'php-runtime',
    'title' => 'PHP 8.3 Micro-App Sandbox',
    'authenticated_user' => $csLabUser ?? 'carlos',
    'runtime' => [
        'php_version' => PHP_VERSION,
        'sapi' => PHP_SAPI,
        'binary_path' => PHP_BINARY ?: '/opt/alt/php83/usr/bin/php',
        'opcache_enabled' => is_array($opcacheStatus) ? (bool) ($opcacheStatus['opcache_enabled'] ?? false) : false,
        'memory_limit' => ini_get('memory_limit'),
        'memory_usage_kb' => round(memory_get_usage(true) / 1024, 2),
    ],
    'extensions_count' => count($extensions),
    'response_time_ms' => $totalMs,
    'timestamp' => gmdate('c'),
];

$wantsJson = (isset($_GET['format']) && $_GET['format'] === 'json')
    || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

if ($wantsJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}
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
  <title>PHP <?= htmlspecialchars(PHP_VERSION) ?> Micro-App — Carlos Silveira Lab</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/liquid-glass.css">
</head>
<body>
  <div class="lg-ambient-canvas" aria-hidden="true">
    <div class="lg-orb lg-orb-sapphire"></div>
    <div class="lg-orb lg-orb-indigo"></div>
    <div class="lg-orb lg-orb-aqua"></div>
    <div class="lg-orb lg-orb-rose"></div>
  </div>

  <header class="lg-navbar">
    <a href="/lab/" class="lg-brand-link">
      <img src="../assets/brand/logo-combined.svg" alt="Carlos Silveira .dev">
    </a>
    <nav class="lg-nav-links">
      <a href="/lab/" class="lg-nav-item">&larr; Volver a Mis Micro-Apps</a>
      <a href="?format=json" class="lg-nav-item">Respuesta JSON</a>
      <button type="button" class="lg-btn lg-btn-secondary" id="theme-toggle-btn">
        <span class="theme-opt-dark">&#9790; Oscuro</span>
        <span class="theme-opt-light">&#9728; Claro</span>
      </button>
      <a href="/lab/?logout=1" class="lg-btn lg-btn-secondary">Cerrar sesión</a>
    </nav>
  </header>

  <main class="lg-container" style="padding: 36px 0 80px;">
    <section class="lg-card" style="margin-bottom: 24px;">
      <span class="lg-badge lg-badge-success">PHP <?= htmlspecialchars(PHP_VERSION) ?> Activo</span>
      <h1 class="lg-hero-title" style="margin: 14px 0 10px;">PHP 8.3 API &amp; Webhook Sandbox.</h1>
      <p style="color:var(--text-secondary); margin:0;">
        Ejecutándose sobre LiteSpeed SAPI (<code><?= htmlspecialchars($payload['runtime']['binary_path']) ?></code>) con <?= count($extensions) ?> extensiones activas.
      </p>
    </section>

    <section class="lg-card">
      <h2 class="lg-section-title" style="font-size:20px; margin-bottom:14px;">Estado del Servicio (JSON)</h2>
      <pre style="margin:0; overflow-x:auto; font-family:var(--font-mono); font-size:13px;"><?= htmlspecialchars((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
    </section>
  </main>

  <script src="../assets/js/liquid-glass.js"></script>
</body>
</html>
